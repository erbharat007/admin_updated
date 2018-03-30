<?php
class Activity
{
	private $activityStatusTypes = array('approved', 'pending', 'rejected');
	
	public function getDailyActivities($empId, $category = 'originator', $date = '', $requesterId = 0, $start = '', $end = '')
	{
		$search_text = '';
		if($_REQUEST['search_text'] != "")
		{
			$search_text = $_REQUEST['search_text'];
		}
		$search_text_condition = $this->getSearchText($search_text);
		
		$query = "SELECT da.*, e.emp_id, e.first_name, e.last_name, e.HOD_1_id, e.email, ac.id AS category_id, ac.category FROM daily_activities AS da INNER JOIN employees AS e ON da.emp_id = e.emp_id LEFT JOIN activity_categories AS ac ON da.category_id = ac.id WHERE 1=1 ";
		//$query .= $search_text_condition;		
		
		if($date != "" && $category == 'originator')
		{
			$query .= " AND da.start_date_time >= '".$date." 00:00:00' AND da.start_date_time <= '".$date." 23:59:59' AND da.emp_id = '".$empId."'";
		}	
		elseif($date != "" && $category == 'authorizer')
		{
			$query .= " AND da.start_date_time >= '".$date." 00:00:00' AND da.start_date_time <= '".$date." 23:59:59' AND e.HOD_1_id = '".$empId."' AND da.emp_id = '".$requesterId."' ";
		}
		elseif($date != "" && $category == 'superadmin')
		{
			$query .= " AND da.start_date_time >= '".$date." 00:00:00' AND da.start_date_time <= '".$date." 23:59:59' AND da.emp_id = '".$requesterId."' ";
		}
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
			$query .= " GROUP BY da.emp_id, DATE(da.start_date_time) ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " GROUP BY da.emp_id, DATE(da.start_date_time) ";
		}
		else
		{
			$query .= " AND e.emp_id = '".$empId."' ";
			$query .= " GROUP BY da.emp_id, DATE(da.start_date_time) ";
		}		
		
		$query .= " ORDER BY DATE(da.start_date_time) DESC";
		//echo $query;
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
	}
	
	public function createDailyActivity($arrayData)
    {
		global $objMail;
		list($empId, $dailyActivity) = $arrayData;
		
		if(empty($dailyActivity))
		{
			$msg = "Something went wrong. Try again.";
		}	
		else
		{
			$sqlCreate = "INSERT INTO daily_activities(emp_id, activity, start_date_time, end_date_time, category_id, created_date) VALUES ";
			$valueStr = '';
			foreach($dailyActivity as $activityDetail)
			{
			    if($valueStr == '')
				{
					$valueStr = " ('".$empId."', '".$GLOBALS['obj_db']->escape($activityDetail['activity'])."', '".$activityDetail['startDateTime']."', '".$activityDetail['endDateTime']."', '".$activityDetail['categoryId']."', '".date('Y-m-d H:i:s')."') ";
				}
				else
				{
					$valueStr .= ", ('".$empId."', '".$GLOBALS['obj_db']->escape($activityDetail['activity'])."', '".$activityDetail['startDateTime']."', '".$activityDetail['endDateTime']."', '".$activityDetail['categoryId']."', '".date('Y-m-d H:i:s')."') ";
				}	
			}
			$sqlCreate .= $valueStr;
			
			$flag=$GLOBALS['obj_db']->execute_query($sqlCreate);
			if(!$flag)
			{
				$msg = "Error occured creating daily activity. Please try again.";
			}
			else
			{
				$objMail->processDailyActivityMail($dailyActivity, 'added', $empId);
				
				$msg = "Daily activity successfully added.";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;}

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND (first_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR last_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR email = '".$search_text."')" : "";
        return $search_text_condition;
    }

	public function updateDailyActivity($arrayData)
    {
		global $objMail;
		list($empId, $dailyActivity) = $arrayData;
		
		if(empty($dailyActivity))
		{
			$msg = "Something lwent wrong. Try again.";
		}
		else
		{			
			foreach($dailyActivity as $activityId => $activityDetail)
			{		
				$query = "UPDATE daily_activities SET activity = '".$GLOBALS['obj_db']->escape($activityDetail['activity'])."', start_date_time = '".$activityDetail['startDateTime']."', end_date_time = '".$activityDetail['endDateTime']."', category_id = '".$activityDetail['categoryId']."' WHERE id = '".$activityId."' AND emp_id = '".$empId."' ";
				
				$flag = @$GLOBALS['obj_db']->execute_query($query);
			}
			
			$objMail->processDailyActivityMail($dailyActivity, 'updated', $empId);
			
			$msg = "Daily activity successfully updated.";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function deleteDailyActivity($date = '', $empId)
    {
		global $objMail;
		
		$query = "DELETE FROM daily_activities WHERE start_date_time >= '".$date." 00:00:00' AND start_date_time <= '".$date." 23:59:59' AND emp_id = '".$empId."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting daily activity.";
        }
		else
		{
			$objMail->processDailyActivityMail($date, 'deleted', $empId);
			
            $msg = "Daily activity deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($empId, $leave_from_date, $leave_to_date)
	{
		$query = "SELECT id FROM daily_activities WHERE 1=1 AND emp_id = '".$empId."' AND leave_from_date = '".$leave_from_date."' AND leave_to_date = '".$leave_to_date."' ";
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
	
	public function updateActivityStatus($arrayData)
	{
		global $objMail;
		list($empId, $status, $activityStatus) = $arrayData;
		
		if(empty($activityStatus))
		{
		    $msg = "Something went wrong. Try again.";	
		}
		else
		{
			foreach($activityStatus as $activityId => $activityDetail)
			{
				$query = "UPDATE daily_activities AS da INNER JOIN employees AS e ON da.emp_id = e.emp_id SET da.approve_status_HOD1 = '".$status."', da.HOD1_comment = '".$GLOBALS['obj_db']->escape($activityDetail['comments'])."', da.approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE da.id = '".$activityId."' AND e.HOD_1_id = '".$empId."' ";
				
				$flag = @$GLOBALS['obj_db']->execute_query($query);
			}
			
			$objMail->processDailyActivityMail($activityStatus, $status, $empId);
			
		    $msg = "Daily activity(s) ".$status." successfully";
			$_SESSION['success'] = 1;
		}	
		return $msg;
	}
	
	public function getActivityStatus($empId, $category = 'originator')
	{
		$activityStatus = array();
		$allActivities = array();
		
		$query = "SELECT IF((da.approve_status_HOD1 = 'pending' && da.approve_status_HOD2 = 'pending'), 'pending', IF(da.approve_status_HOD1 != 'pending', da.approve_status_HOD1, da.approve_status_HOD2)) AS final_status, COUNT(da.id) AS total FROM daily_activities AS da WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND da.emp_id = '".$empId."'";
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
				$activityStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->activityStatusTypes, array_keys($activityStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$activityStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_activities FROM daily_activities AS da WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND da.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$queryTotal .= " AND e.HOD_1_id = '".$empId."' ";
		}
		$rsTotal = $GLOBALS['obj_db']->execute_query($queryTotal);
		if($GLOBALS['obj_db']->num_rows($rsTotal))
		{
			$rowTotal = $GLOBALS['obj_db']->fetch_array($rsTotal);
			$total_activities = $rowTotal['total_activities'];
		}	
		
		$allActivities['total'] = $total_activities;
		$allActivities['activityStatus'] = $activityStatus;
		
        return $allActivities;
	}
	
	public function getActivityStatusAuthorizer($empId, $category = 'originator')
	{
		$activityStatus = array();
		$allActivities = array();
		
		$query = "SELECT da.approve_status_HOD1 AS final_status, COUNT(da.id) AS total FROM daily_activities AS da INNER JOIN employees AS e ON da.emp_id = e.emp_id WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND da.emp_id = '".$empId."'";
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
				$activityStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->activityStatusTypes, array_keys($activityStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$activityStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_activities FROM daily_activities AS da INNER JOIN employees AS e ON da.emp_id = e.emp_id WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND da.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$queryTotal .= " AND e.HOD_1_id = '".$empId."' ";
		}
		$rsTotal = $GLOBALS['obj_db']->execute_query($queryTotal);
		if($GLOBALS['obj_db']->num_rows($rsTotal))
		{
			$rowTotal = $GLOBALS['obj_db']->fetch_array($rsTotal);
			$total_activities = $rowTotal['total_activities'];
		}	
		
		$allActivities['total'] = $total_activities;
		$allActivities['activityStatus'] = $activityStatus;
		
        return $allActivities;
	}
}
?>