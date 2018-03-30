<?php
class DailyAllowance
{
	private $daStatusTypes = array('approved', 'pending', 'rejected');
	
	public function getCalculatedDa($empId, $category = 'originator', $tourId)
	{
		$query = "SELECT cda.*, tr.emp_id FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND cda.tour_id = '".$tourId."' AND tr.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND cda.tour_id = '".$tourId."' ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " AND cda.tour_id = '".$tourId."' ";
		}		
		elseif($category == 'finance' || $category == 'hr')
		{
			$query .= " AND cda.tour_id = '".$tourId."' ";
		}
		$query .= " ORDER BY cda.id DESC";
		
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
	}
	
	public function add($arrayData)
    {
		global $objMail;
		
		list($tourId, $empId, $total_travel_time_hrs, $total_travel_time_days, $total_tour_time_hrs, $total_tour_time_days, $show_tour_start_date, $show_tour_end_date, $total_balance_hours, $total_balance_days, $half_da_for_travel, $full_da_for_balance_days, $show_total_da, $DaResDetails) = $arrayData;
		
		$show_tour_start_dateObj = DateTime::createFromFormat('d-m-Y', $show_tour_start_date);
		$show_tour_end_dateObj = DateTime::createFromFormat('d-m-Y', $show_tour_end_date);
		$show_tour_start_date = $show_tour_start_dateObj->format('Y-m-d');
		$show_tour_end_date = $show_tour_end_dateObj->format('Y-m-d');
		
		$table_name = 'calculated_da';
		$fields=array();

		$fields[]=array('name'=>'tour_id','value'=>$tourId);
		$fields[]=array('name'=>'tour_start_date','value'=>$show_tour_start_date);
		$fields[]=array('name'=>'tour_end_date','value'=>$show_tour_end_date);
		$fields[]=array('name'=>'total_travel_time_hrs','value'=>$total_travel_time_hrs);
		$fields[]=array('name'=>'total_travel_time_days','value'=>$total_travel_time_days);
		$fields[]=array('name'=>'total_tour_time_hrs','value'=>$total_tour_time_hrs);
		$fields[]=array('name'=>'total_tour_time_days','value'=>$total_tour_time_days);
		$fields[]=array('name'=>'total_balance_hours','value'=>$total_balance_hours);
		$fields[]=array('name'=>'total_balance_days','value'=>$total_balance_days);
		$fields[]=array('name'=>'half_da_for_travel','value'=>$half_da_for_travel);
		$fields[]=array('name'=>'full_da_for_balance_days','value'=>$full_da_for_balance_days);
		$fields[]=array('name'=>'total_da','value'=>$show_total_da);
		$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
		
		$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
		$flag=$GLOBALS['obj_db']->execute_query($query);
		
		if(!$flag)
		{
			$msg = "Error occured while adding DA. Please try again.";
		}
		else
		{
			$msg = "DA successfully updated.";
			if(!empty($DaResDetails))
			{
				$flagResDetails = $this->addDaResDetails($tourId, $DaResDetails);
				if(!$flagResDetails)
				{
					$msg .= "Error in adding DA reservation details.";
				}
			}
			$_SESSION['success'] = 1;
			//$objMail->processTourRequestMail($tourId, 'added');
		}
		
		return $msg;
    }

	public function update($arrayData)
    {
		global $objMail;
		
		list($tourId, $empId, $total_travel_time_hrs, $total_travel_time_days, $total_tour_time_hrs, $total_tour_time_days, $show_tour_start_date, $show_tour_end_date, $total_balance_hours, $total_balance_days, $half_da_for_travel, $full_da_for_balance_days, $show_total_da, $DaResDetails, $DaResDetailsNew) = $arrayData;
		
		$show_tour_start_dateObj = DateTime::createFromFormat('d-m-Y', $show_tour_start_date);
		$show_tour_end_dateObj = DateTime::createFromFormat('d-m-Y', $show_tour_end_date);
		$show_tour_start_date = $show_tour_start_dateObj->format('Y-m-d');
		$show_tour_end_date = $show_tour_end_dateObj->format('Y-m-d');
		
		$query = "UPDATE calculated_da SET
				tour_start_date = '".$show_tour_start_date."',
				tour_end_date = '".$show_tour_end_date."',
				total_travel_time_hrs = '".$total_travel_time_hrs."',
				total_travel_time_days = '".$total_travel_time_days."',
				total_tour_time_hrs = '".$total_tour_time_hrs."',
				total_tour_time_days = '".$total_tour_time_days."',
				total_balance_hours = '".$total_balance_hours."',
				total_balance_days = '".$total_balance_days."',
				half_da_for_travel = '".$half_da_for_travel."',
				full_da_for_balance_days = '".$full_da_for_balance_days."',
				total_da = '".$show_total_da."',
				last_updated = '".date('Y-m-d H:i:s')."'
				WHERE 
				tour_id = '".$tourId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
					
			$flag = @$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error occured while updating DA. Please try again.";
			}
			else
			{
				$msg = "DA successfully updated.";
				if(!empty($DaResDetails))
				{
					$flagResDetails = $this->updateDaResDetails($tourId, $DaResDetails);
					if(!$flagResDetails)
					{
						$msg .= "Error in updating tour reservation details.";
					}
				}
				if(!empty($DaResDetailsNew))
				{
					$flagResDetailsNew = $this->addDaResDetails($tourId, $DaResDetailsNew);
					if(!$flagResDetailsNew)
					{
						$msg .= "Error in updating new reservation details.";
					}
				}
				
				$_SESSION['success'] = 1;
				//$objMail->processTourRequestMail($tourId, 'updated');
			}		
		return $msg;
	}

	public function updateStatus($tourId, $empId, $status, $comments, $userType)
    {
		global $objMail;
		if($userType == 'authorizer')
		{
			$sqlUpdate = "UPDATE calculated_da SET approve_status_HOD1 = '".trim($status)."', HOD1_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE tour_id = '".$tourId."' ";
		}	
		elseif($userType == 'finance')
		{
			$sqlUpdate = "UPDATE calculated_da SET approve_status_finance = '".trim($status)."', finance_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_finance_id = '".$_SESSION['userId']."' WHERE tour_id = '".$tourId."' ";
		}
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		if(!$rsUpdate)
        {
			$msg = "Error while updating the status of DA.";
        }
		else
		{
			//$objMail->processTourRequestMail($tourId, $status);
			
            $msg = "DA ".$status." successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND (first_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR last_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR email = '".$GLOBALS['obj_db']->escape($search_text)."' OR tour_place = '".$GLOBALS['obj_db']->escape($search_text)."')" : "";
        return $search_text_condition;
    }
	
	public function getDAStatus($empId, $category = 'originator')
	{
		$daStatus = array();
		$allDa = array();
		
		$query = "SELECT IF((cda.approve_status_HOD1 = 'pending' && cda.approve_status_HOD2 = 'pending'), 'pending', IF(cda.approve_status_HOD1 != 'pending', cda.approve_status_HOD1, cda.approve_status_HOD2)) AS final_status, COUNT(cda.id) AS total FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND tr.emp_id = '".$empId."'";
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
				$daStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->daStatusTypes, array_keys($daStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$daStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND tr.emp_id = '".$empId."'";
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
		
		$allDa['total'] = $total_applications;
		$allDa['daStatus'] = $daStatus;
		
        return $allDa;
	}
	
	public function getDAStatusAuthorizer($empId, $category = 'originator')
	{
		$daStatus = array();
		$allDa = array();
		
		if($category == 'authorizer')
		{
			$query = "SELECT cda.approve_status_HOD1 AS final_status, COUNT(cda.tour_id) AS total FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'finance')
		{
			$query = "SELECT cda.approve_status_finance AS final_status, COUNT(cda.tour_id) AS total FROM calculated_da AS cda INNER JOIN tour_requests AS tr WHERE 1=1 ";
		}
		elseif($category == 'hr')
		{
			$query = "SELECT cda.approve_status_HOD1 AS final_status, COUNT(cda.tour_id) AS total FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id WHERE 1=1 ";
		}
		elseif($category == 'superadmin')
		{
			$query = "SELECT cda.approve_status_HOD1 AS final_status, COUNT(cda.tour_id) AS total FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id WHERE 1=1 ";
		}
		
		if($category == 'originator')
		{
			$query .= " AND tr.emp_id = '".$empId."'";
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
				$daStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->daStatusTypes, array_keys($daStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$daStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND tr.emp_id = '".$empId."'";
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
		
		$allDa['total'] = $total_applications;
		$allDa['daStatus'] = $daStatus;
		
        return $allDa;
	}
	
	private function addDaResDetails($tourId, $DaResDetails)
	{
		$sqlResDetails = "INSERT INTO da_res_details(tour_id, start_date_time, end_date_time, reservation_from, reservation_to, travel_time, created_date) VALUES ";
		$valueStr = '';
		foreach($DaResDetails as $details)
		{
			$makeValues = " ('".$tourId."', '".$details['resStartDateTime']."', '".$details['resEndDateTime']."', '".$GLOBALS['obj_db']->escape($details['resFrom'])."', '".$GLOBALS['obj_db']->escape($details['resTo'])."', '".$GLOBALS['obj_db']->escape($details['totalJourneyHours'])."', '".date('Y-m-d H:i:s')."') ";
			
			if($valueStr == '')
			{
				$valueStr = $makeValues;
			}
			else
			{
				$valueStr .= ", ".$makeValues;
			}	
		}
		$sqlResDetails .= $valueStr;
		$flagResDetails = $GLOBALS['obj_db']->execute_query($sqlResDetails);
		if(!$flagResDetails)
		{
			return false;
		}	
		return true;
	}
	
	private function updateDaResDetails($tourId, $DaResDetails)
	{
		$msgFlag = true;
		foreach($DaResDetails as $id => $details)
		{		
			$query = "UPDATE da_res_details SET start_date_time = '".$details['resStartDateTime']."', end_date_time = '".$details['resEndDateTime']."', reservation_from = '".$GLOBALS['obj_db']->escape($details['resFrom'])."', reservation_to = '".$GLOBALS['obj_db']->escape($details['resTo'])."', travel_time = '".$GLOBALS['obj_db']->escape($details['totalJourneyHours'])."', last_updated = '".date('Y-m-d H:i:s')."' WHERE id = '".$id."' AND tour_id = '".$tourId."' ";
			
			$flag = @$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msgFlag = false;
			}	
		}
		return $msgFlag;
	}
	
	public function getDaResDetails($empId, $category = 'originator', $tourId)
	{
		if(!$tourId)
			return false;
		
		$daReservationDetails = array();
		$query = "SELECT drd.* FROM da_res_details AS drd WHERE 1=1 ";
		if($category == 'originator')
		{
			$query .= " AND drd.tour_id = '".$tourId."' ";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND drd.tour_id = '".$tourId."' ";
		}
		elseif($category == 'finance' || $category == 'hr')
		{
			$query .= " AND drd.tour_id = '".$tourId."' ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " AND drd.tour_id = '".$tourId."' ";
		}		
		
		$query .= " ORDER BY drd.id ASC";
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);
		
		if($GLOBALS['obj_db']->num_rows($rsQuery) > 0)
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rsQuery))
			{
				$daReservationDetails[$row['id']] = array('start_date_time' => $row['start_date_time'], 'end_date_time' => $row['end_date_time'], 'reservation_from' => $row['reservation_from'], 'reservation_to' => $row['reservation_to'], 'travel_time' => $row['travel_time']);
			}	
		}	
        return $daReservationDetails;
	}
}
?>