<?php
class Leaves
{
	private $leaveStatusTypes = array('approved', 'pending', 'rejected');
	
	public function getLeaveTypes($leaveTypeId = 0)
	{
		$sql = "SELECT * FROM leave_types WHERE 1=1 ";
		if($leaveTypeId > 0)
		{
		    $sql .= " AND leave_type_id = '".$leaveTypeId."' ";		
		}
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		return $rs;
	}
	
	public function getApplicableLeaveTypesForEmployee($empId)
	{
		$sql = "SELECT lt.* FROM leave_types AS lt INNER JOIN leave_balance AS lb ON lt.leave_type_id = lb.leave_type_id WHERE lb.emp_id = '".$empId."' AND lb.year = YEAR(CURDATE()) AND lb.earned_type != 'NA' ";
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		return $rs;
	}
	
	public function getLeaveBalance($empId, $leaveTypeId = 0)
	{
		$sql = "SELECT lb.*, lt.leave_type, lt.max_allowed, lt.leave_abbr, (lb.opening_balance-lb.leaves_availed) AS remaining FROM leave_balance AS lb LEFT JOIN leave_types AS lt ON lb.leave_type_id = lt.leave_type_id WHERE lb.emp_id = '".$empId."' ";
		if($leaveTypeId > 0)
		{
		    $sql .= " AND lb.leave_type_id = '".$leaveTypeId."' ";		
		}
		$sql .= " AND lb.year = YEAR(CURDATE()) GROUP BY lt.leave_type ";
	
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		return $rs;
	}
	
	public function getLeaveApplications($empId, $category = 'originator', $applicationId = 0, $start = '', $end = '')
	{
		/*
		$search_text = '';
		if($_REQUEST['search_text'] != "")
		{
			$search_text = $_REQUEST['search_text'];
		}
		$search_text_condition = $this->getSearchText($search_text);
		*/
		$search_text_condition = $this->getSearchTextCondition();
		
		$query = "SELECT la.*, e.first_name, e.last_name, e.HOD_1_id, e.HOD_2_id, e.hr_id, e.email, e_1.emp_id AS assignee_emp_id, e_1.first_name AS assignee_first_name, e_1.last_name AS assignee_last_name, e_1.email AS assignee_email, lt.* FROM leave_applications AS la INNER JOIN employees AS e ON la.emp_id = e.emp_id INNER JOIN employees AS e_1 ON la.job_assignee_id = e_1.emp_id INNER JOIN leave_types AS lt ON la.leave_type_id = lt.leave_type_id WHERE 1=1 ";
		$query .= $search_text_condition;
		
		if($applicationId > 0 && $category == 'originator')
		{
			$query .= " AND la.id = '".$applicationId."' AND la.emp_id = '".$empId."'";
		}	
		elseif($applicationId > 0 && $category == 'authorizer')
		{
			$query .= " AND la.id = '".$applicationId."' AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($applicationId > 0 && $category == 'superadmin')
		{
			$query .= " AND la.id = '".$applicationId."' ";
		}
		elseif($applicationId > 0 && ($category == 'finance' || $category == 'hr'))
		{
			$query .= " AND la.id = '".$applicationId."' ";
		}
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($category == 'finance' || $category == 'hr')
		{
			$query .= " ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " ";
		}
		else
		{
			$query .= " AND e.emp_id = '".$empId."' ";
		}
		
		$query .= " ORDER BY la.id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		//echo $query;
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
	}
	
	public function createLeaveApplication($arrayData)
    {
		global $objMail;
		list($empId, $leave_from_date, $leave_to_date, $leave_type_id, $job_assignee_id, $reason_of_leave, $leave_address, $leave_phone, $regionOfEmployee, $halfDayLeave) = $arrayData;
		
		$leave_from_date = getDbDateFormat($leave_from_date);
		$leave_to_date   = getDbDateFormat($leave_to_date);
		$leaves_required = getDateDifference($leave_from_date, $leave_to_date);
		$leaves_required += 1;
		if(!empty($halfDayLeave))
		{
			$leaves_required = 0.5;
		}	
		$rsBal = $this->getLeaveBalance($empId, $leave_type_id);
		$row = $GLOBALS['obj_db']->fetch_array($rsBal);
		$remainingLeaves = $row['remaining'];
		
		$holidaysBetweenLeaves = $this->getHolidaysBetweenLeaves($leave_from_date, $leave_to_date, $regionOfEmployee);
		$totalHolidaysBetweenLeaves = (!empty($holidaysBetweenLeaves)) ? count($holidaysBetweenLeaves) : 0;
		$leaves_required = $leaves_required-$totalHolidaysBetweenLeaves;
		
		$leaveTypeDetailsResult = $this->getLeaveTypes($leave_type_id);
		$leaveTypeDetails       = $GLOBALS['obj_db']->fetch_array($leaveTypeDetailsResult);
		$leaveAbbr = $leaveTypeDetails['leave_abbr'];
		
		if($leaveAbbr == 'EL' || $leaveAbbr == 'LWP')
		{
			$leaves_required = $leaves_required+$totalHolidaysBetweenLeaves;
		}	
		
		if($row['earned_type'] == 'NA')
		{
			$msg = "You are not allowed to take ".$row['leave_type'].".";
		}	
		elseif($this->isDuplicate($empId, $leave_from_date, $leave_to_date))
		{
			$msg = "Leave application already raised.";
		}
		elseif($remainingLeaves < $leaves_required)
		{
			$msg = "You don't have enough leaves in your account to apply this leave application.";
		}
		elseif( ($leaveAbbr == 'CL' || $leaveAbbr == 'comp. off') && $leaves_required > 2)
		{
			$msg = "You can't take consecutively more than 2 leaves for this leave type. Please contact your HOD or HR";
		}
		else
		{
			$table_name = 'leave_applications';
			$fields=array();

			$fields[]=array('name'=>'emp_id','value'=>$empId);
			$fields[]=array('name'=>'leave_type_id','value'=>$leave_type_id);
			$fields[]=array('name'=>'leave_from_date','value'=>$leave_from_date);
			$fields[]=array('name'=>'leave_to_date','value'=>$leave_to_date);
			$fields[]=array('name'=>'leaves_required','value'=>$leaves_required);
			$fields[]=array('name'=>'job_assignee_id','value'=>$job_assignee_id);
			$fields[]=array('name'=>'reason_of_leave','value'=>$GLOBALS['obj_db']->escape($reason_of_leave));
			$fields[]=array('name'=>'leave_address','value'=>$GLOBALS['obj_db']->escape($leave_address));
			$fields[]=array('name'=>'leave_phone','value'=>$leave_phone);
			$fields[]=array('name'=>'leave_balance','value'=>$remainingLeaves);
			
			if(!empty($halfDayLeave))
			{
			    $fields[]=array('name'=>'half_day_leave','value'=>'YES');
			}	
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error occured while applying leaves. Please try again.";
			}
			else
			{
				$applicationId = $GLOBALS['obj_db']->insert_id();
				$objMail->processLeaveRequestMail($applicationId, 'added');
				
				$msg = "Leaves successfully applied.";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND (first_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR last_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR email = '".$search_text."')" : "";
        return $search_text_condition;
    }
	
	public function getSearchTextCondition()
    {
		$search_text_condition = "";
		if(isset($_REQUEST['txt_search_emp_name']) && $_REQUEST['txt_search_emp_name'] != "")
		{
			$search_text_condition .= " AND (e.first_name LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_emp_name'])."%' OR e.last_name LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_emp_name'])."%') ";
		}
		if(isset($_REQUEST['txt_search_email']) && $_REQUEST['txt_search_email'] != "")
		{
			$search_text_condition .= " AND e.email = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_email'])."' ";
		}
		if(isset($_REQUEST['txt_search_status']) && $_REQUEST['txt_search_status'] != -1)
		{
			$search_text_condition .= " AND approve_status_HOD1 = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_status'])."' ";
		}
		if(isset($_REQUEST['txt_search_leave_type']) && $_REQUEST['txt_search_leave_type'] != -1)
		{
			$search_text_condition .= " AND la.leave_type_id = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_leave_type'])."' ";
		}
		if(isset($_REQUEST['txt_search_date_from']) && $_REQUEST['txt_search_date_from'] != "" && isset($_REQUEST['txt_search_date_to']) && $_REQUEST['txt_search_date_to'] != "" && !empty($_REQUEST['date_included']))
		{
			$txtSearchDateFromDb = getDbDateFormat($_REQUEST['txt_search_date_from']);
			$txtSearchDateToDb = getDbDateFormat($_REQUEST['txt_search_date_to']);
			
			if($_REQUEST['txt_search_date_type'] == 'applied_date')
			{
				$search_text_condition .= " AND la.created_date >= '".$txtSearchDateFromDb." 00:00:00' AND la.created_date <= '".$txtSearchDateToDb." 23:59:59' ";
			}
			elseif($_REQUEST['txt_search_date_type'] == 'leave_from_date')
			{
				$search_text_condition .= " AND leave_from_date >= '".$txtSearchDateFromDb."' AND leave_from_date <= '".$txtSearchDateToDb."' ";
			}
			elseif($_REQUEST['txt_search_date_type'] == 'leave_to_date')
			{
				$search_text_condition .= " AND leave_to_date >= '".$txtSearchDateFromDb."' AND leave_to_date <= '".$txtSearchDateToDb."' ";
			}
		}
		
        return $search_text_condition;
    }

	public function updateLeaveApplication($arrayData)
    {
		global $objMail;
		list($id, $empId, $leave_from_date, $leave_to_date, $leave_type_id, $job_assignee_id, $reason_of_leave, $leave_address, $leave_phone, $regionOfEmployee, $halfDayLeave) = $arrayData;
		
		$leave_from_date = getDbDateFormat($leave_from_date);
		$leave_to_date   = getDbDateFormat($leave_to_date);
		$leaves_required = getDateDifference($leave_from_date, $leave_to_date);
		$leaves_required += 1;
		if(!empty($halfDayLeave))
		{
			$leaves_required = 0.5;
		}
		$rsBal = $this->getLeaveBalance($empId, $leave_type_id);
		$row = $GLOBALS['obj_db']->fetch_array($rsBal);
		$remainingLeaves = $row['remaining'];
		
		$holidaysBetweenLeaves = $this->getHolidaysBetweenLeaves($leave_from_date, $leave_to_date, $regionOfEmployee);
		$totalHolidaysBetweenLeaves = (!empty($holidaysBetweenLeaves)) ? count($holidaysBetweenLeaves) : 0;
		$leaves_required = $leaves_required-$totalHolidaysBetweenLeaves;
		
		if($remainingLeaves < $leaves_required)
		{
			$msg = "You don't have enough leaves in your account to apply this leave application.";
		}
		else
		{
			if(!empty($halfDayLeave))
			{
				$halfDayLeave = 'YES';			    
			}
			else
			{
				$halfDayLeave = 'NO';
			}	
			$query = "UPDATE leave_applications SET
					leave_type_id = '".$leave_type_id."',
					leave_from_date = '".$leave_from_date."',
					leave_to_date = '".$leave_to_date."',
					leaves_required = '".$leaves_required."',
					job_assignee_id = '".$job_assignee_id."',
					reason_of_leave = '".$GLOBALS['obj_db']->escape($reason_of_leave)."',
					leave_address = '".$GLOBALS['obj_db']->escape($leave_address)."',
					half_day_leave = '".$halfDayLeave."',
					leave_phone = '".$leave_phone."'
					WHERE 
					id = '".$id."' AND emp_id = '".$empId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
					
			$flag = @$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error occured while updating leave application. Please try again.";
			}
			else
			{
				$objMail->processLeaveRequestMail($id, 'updated');
				$msg = "Leaves successfully updated.";
				$_SESSION['success'] = 1;
			}
		}		
		return $msg;
	}

	public function deleteLeaveApplication($id = '', $empId)
    {
		global $objMail;
		
		$query = "DELETE FROM leave_applications WHERE id = '".$id."' AND emp_id = '".$empId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting application.";
        }
		else
		{
			$objMail->processLeaveRequestMail($id, 'deleted');
            $msg = "Application deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function getReportingHeadsList()
	{
		$sqlHod = "SELECT * FROM employees WHERE emp_category IN('authorizer', 'both')";
		$rsHod = $GLOBALS['obj_db']->execute_query($sqlHod);
		return $rsHod;
	}
	
	public function isDuplicate($empId, $leave_from_date, $leave_to_date)
	{
		$query = "SELECT id FROM leave_applications WHERE 1=1 AND emp_id = '".$empId."' AND leave_from_date = '".$leave_from_date."' AND leave_to_date = '".$leave_to_date."' ";
		$rs = $GLOBALS['obj_db']->execute_query($query);
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			return true;
		}
		else
		{  
			return false;
		}
	}
	
	public function updateLeaveBalance($arrayData)
	{
		list($empId, $leaveBalArray, $updateEl) = $arrayData;
		if(empty($leaveBalArray))
		{
			$msg = 'Error occured while updating leave balance.';
		}
		else
		{
			list($leaveTypeIdEL) = $GLOBALS['obj_db']->fetch_array($GLOBALS['obj_db']->execute_query("SELECT leave_type_id FROM leave_types WHERE leave_abbr = 'EL' "));
			
			$i = 0;
			foreach($leaveBalArray as $leaveTypeId => $detail)
			{
				$sqlCheck = "SELECT id, opening_balance, earned_type FROM leave_balance WHERE emp_id = ".$empId." AND leave_type_id = ".$leaveTypeId." AND year = YEAR(CURDATE()) ";
				$rsCheck = $GLOBALS['obj_db']->execute_query($sqlCheck);
				if($GLOBALS['obj_db']->num_rows($rsCheck) > 0)
				{
					$rowCheck           = $GLOBALS['obj_db']->fetch_array($rsCheck);
					$prevOpeningBalance = $rowCheck['opening_balance'];
					$prevEarnedType     = $rowCheck['earned_type'];
					
					if($prevOpeningBalance != $detail['openingBalance'] || $prevEarnedType != $detail['earnedType'])
					{					
						if($leaveTypeIdEL == $leaveTypeId)
						{
							if(!empty($updateEl))
							{
								$updatedByUserType = (isset($_SESSION['adminUser']) && $_SESSION['adminUser'] == 1) ? 'admin_user' : 'employee';
								
								$sqlUpdate = "UPDATE leave_balance SET opening_balance = ".$detail['openingBalance'].", earned_type = '".$detail['earnedType']."', last_updated = '".date('Y-m-d H:i:s')."', updated_by = '".$_SESSION['userId']."', updated_by_user_type = '".$updatedByUserType."' WHERE emp_id = ".$empId." AND leave_type_id = ".$leaveTypeId." AND year = YEAR(CURDATE()) ";
								$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
								$i++;
							}
						}
						else
						{
							$updatedByUserType = (isset($_SESSION['adminUser']) && $_SESSION['adminUser'] == 1) ? 'admin_user' : 'employee';
							
							$sqlUpdate = "UPDATE leave_balance SET opening_balance = ".$detail['openingBalance'].", earned_type = '".$detail['earnedType']."', last_updated = '".date('Y-m-d H:i:s')."', updated_by = '".$_SESSION['userId']."', updated_by_user_type = '".$updatedByUserType."' WHERE emp_id = ".$empId." AND leave_type_id = ".$leaveTypeId." AND year = YEAR(CURDATE()) ";
							$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
							$i++;
						}
					}
				}
				else
				{
					$updatedByUserType = (isset($_SESSION['adminUser']) && $_SESSION['adminUser'] == 1) ? 'admin_user' : 'employee';
					
					$sqlInsert = "INSERT INTO leave_balance(emp_id, leave_type_id, year, last_updated, updated_by, updated_by_user_type) VALUES('".$empId."', '".$leaveTypeId."', YEAR(CURDATE()), '".date('Y-m-d H:i:s')."', '".$_SESSION['userId']."', '".$updatedByUserType."')";
					$rsInsert = $GLOBALS['obj_db']->execute_query($sqlInsert);
					$i++;
				}
			}
			if($i > 0)
			{
				$msg = 'Leave Balance successfully updated.';
				$_SESSION['success'] = 1;
			}
			else
			{
				$msg = 'No Changes Made';
			}
		}
		return $msg;
	}
	
	public function getLeavesStatus($empId, $category = 'originator')
	{
		$leaveStatus = array();
		$allLeaves = array();
		
		$query = "SELECT IF((la.approve_status_HOD1 = 'pending' && la.approve_status_HOD2 = 'pending'), 'pending', IF(la.approve_status_HOD1 != 'pending', la.approve_status_HOD1, la.approve_status_HOD2)) AS final_status, COUNT(la.id) AS total FROM leave_applications AS la WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND la.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
		}
		$query .= " GROUP BY final_status";
		$query .= " ORDER BY FIELD(`final_status`, 'approved', 'pending', 'rejected')";
		
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);
		if($GLOBALS['obj_db']->num_rows($rsQuery))
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rsQuery))
			{
				$leaveStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->leaveStatusTypes, array_keys($leaveStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$leaveStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM leave_applications AS la WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND la.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$queryTotal .= " AND e.HOD_1_id = '".$empId."' ";
		}
		$rsTotal = $GLOBALS['obj_db']->execute_query($queryTotal);
		if($GLOBALS['obj_db']->num_rows($rsTotal))
		{
			$rowTotal = $GLOBALS['obj_db']->fetch_array($rsTotal);
			$total_applications = $rowTotal['total_applications'];
		}	
		
		$allLeaves['total'] = $total_applications;
		$allLeaves['leaveStatus'] = $leaveStatus;
		
        return $allLeaves;
	}
	
	public function getLeavesStatusAuthorizer($empId, $category = 'originator')
	{
		$leaveStatus = array();
		$allLeaves = array();
		
		$query = "SELECT la.approve_status_HOD1 AS final_status, COUNT(la.id) AS total FROM leave_applications AS la INNER JOIN employees AS e ON la.emp_id = e.emp_id WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND la.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
		}
		$query .= " GROUP BY final_status";
		$query .= " ORDER BY FIELD(`final_status`, 'approved', 'pending', 'rejected')";
		
		//echo $query;die;
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);
		if($GLOBALS['obj_db']->num_rows($rsQuery))
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rsQuery))
			{
				$leaveStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->leaveStatusTypes, array_keys($leaveStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$leaveStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(la.id) AS total_applications FROM leave_applications AS la INNER JOIN employees AS e ON la.emp_id = e.emp_id WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND la.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$queryTotal .= " AND e.HOD_1_id = '".$empId."' ";
		}
		$rsTotal = $GLOBALS['obj_db']->execute_query($queryTotal);
		if($GLOBALS['obj_db']->num_rows($rsTotal))
		{
			$rowTotal = $GLOBALS['obj_db']->fetch_array($rsTotal);
			$total_applications = $rowTotal['total_applications'];
		}	
		
		$allLeaves['total'] = $total_applications;
		$allLeaves['leaveStatus'] = $leaveStatus;
		
        return $allLeaves;
	}

	public function getHolidaysBetweenLeaves($leaveFromDate, $leaveToDate, $regionOfEmployee)
	{
		global $objHoliday;
		$start = new DateTime($leaveFromDate);
		$interval = new DateInterval('P1D');
		$end = new DateTime($leaveToDate);
		$end->add($interval); // To include last date i.e. $leaveToDate
		
		$period = new DatePeriod($start, $interval, $end);
		// By iterating over the DatePeriod object, all of the recurring dates within that period are printed.
		$leaveDates = array();
		$rsHolidays = $objHoliday->getHolidaysOfYear('all', $regionOfEmployee);
		$holidaysList = array();
		if($GLOBALS['obj_db']->num_rows($rsHolidays) > 0)
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rsHolidays))
			{
				$holidaysList[] = $row['date'];
			}	
		}
		
		$holidaysBetweenLeaves = array();
		foreach ($period as $date) 
		{
			$leaveDates[] = $date->format('Y-m-d');
		}
		$holidaysBetweenLeaves = array_intersect($leaveDates, $holidaysList);
		/*echo '<pre>';
		print_r($holidaysBetweenLeaves);die;*/
		
		return $holidaysBetweenLeaves;
	}
	
	public function updateStatus($leaveId, $empId, $status, $comments)
    {
		global $objMail;
		$rsLeaveDetail = $this->getLeaveApplications($_SESSION['userId'], 'authorizer', $leaveId);
		if($GLOBALS['obj_db']->num_rows($rsLeaveDetail) > 0)
		{
			$rowLeaveDetail = $GLOBALS['obj_db']->fetch_array($rsLeaveDetail);					
		}
		
		if(trim($status) == 'approved')
		{
			$rsLeaveBal = $this->getLeaveBalance($rowLeaveDetail['emp_id'], $rowLeaveDetail['leave_type_id']);
			$rowLeaveBal = $GLOBALS['obj_db']->fetch_array($rsLeaveBal);
			$remainingLeaves = $rowLeaveBal['remaining'];
			$requiredLeaves = $rowLeaveDetail['leaves_required'];
			if($rowLeaveDetail['half_day_leave'] == 'YES')
			{
				$requiredLeaves = 0.5;
			}
			
			if($remainingLeaves < $requiredLeaves)
			{
				$msg = "No enough leaves in employee's account to get it approved.";
			}	
			else
			{
				$sqlUpdate = "UPDATE leave_applications SET approve_status_HOD1 = '".trim($status)."', HOD1_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE id = '".$leaveId."' ";
				$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
						
				$leaveCountToBeUpdated = ($rowLeaveDetail['half_day_leave'] == 'YES') ? 0.5 : $rowLeaveDetail['leaves_required'];
				
				$sqlUpdateLeaveBal = "UPDATE leave_balance SET leaves_availed = leaves_availed+".$leaveCountToBeUpdated." WHERE emp_id = '".$rowLeaveDetail['emp_id']."' AND leave_type_id = '".$rowLeaveDetail['leave_type_id']."' AND year = YEAR('".$rowLeaveDetail['leave_to_date']."') ";
				$rsUpdateLeaveBal = $GLOBALS['obj_db']->execute_query($sqlUpdateLeaveBal);
				
				if($rsUpdate && $rsUpdateLeaveBal)
				{
					$_SESSION['success'] = 1;
					$msg = "You have ".trim($status)." the leave application.";
					
					$objMail->processLeaveRequestMail($leaveId, trim($status));
				}
				else
				{
					$msg = "Error occured. Try again.";
				}
			}
		}	
		elseif(trim($status) == 'rejected')
		{
			if($rowLeaveDetail['approve_status_HOD1'] == 'approved')
			{
				$leaveCountToBeUpdated = ($rowLeaveDetail['half_day_leave'] == 'YES') ? 0.5 : $rowLeaveDetail['leaves_required'];
				
				$sqlUpdateLeaveBal = "UPDATE leave_balance SET leaves_availed = leaves_availed-".$leaveCountToBeUpdated." WHERE emp_id = '".$rowLeaveDetail['emp_id']."' AND leave_type_id = '".$rowLeaveDetail['leave_type_id']."' AND year = YEAR('".$rowLeaveDetail['leave_to_date']."') ";
				$rsUpdateLeaveBal = $GLOBALS['obj_db']->execute_query($sqlUpdateLeaveBal);
			}
			
			$sqlUpdate = "UPDATE leave_applications SET approve_status_HOD1 = '".trim($status)."', HOD1_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE id = '".$leaveId."' ";
			$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
			
			if($rsUpdate)
			{
				$_SESSION['success'] = 1;
				$msg = "You have ".trim($status)." the leave application.";
				
				$objMail->processLeaveRequestMail($leaveId, trim($status));
			}
			else
			{
				$msg = "Error occured. Try again.";
			}
		}
		elseif(trim($status) == 'cancelled')
		{
			if($rowLeaveDetail['approve_status_HOD1'] == 'approved')
			{
				$leaveCountToBeUpdated = ($rowLeaveDetail['half_day_leave'] == 'YES') ? 0.5 : $rowLeaveDetail['leaves_required'];
				
				$sqlUpdateLeaveBal = "UPDATE leave_balance SET leaves_availed = leaves_availed-".$leaveCountToBeUpdated." WHERE emp_id = '".$rowLeaveDetail['emp_id']."' AND leave_type_id = '".$rowLeaveDetail['leave_type_id']."' AND year = YEAR('".$rowLeaveDetail['leave_to_date']."') ";
				$rsUpdateLeaveBal = $GLOBALS['obj_db']->execute_query($sqlUpdateLeaveBal);
			}	
			
			$sqlUpdate = "UPDATE leave_applications SET approve_status_HOD1 = '".trim($status)."', HOD1_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE id = '".$leaveId."' ";
			$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);			
			
			if($rsUpdate)
			{
				$_SESSION['success'] = 1;
				$msg = "You have ".trim($status)." the leave application.";
				
				$objMail->processLeaveRequestMail($leaveId, trim($status));
			}
			else
			{
				$msg = "Error occured. Try again.";
			}
		}
	
		return $msg;
    }
}
?>
