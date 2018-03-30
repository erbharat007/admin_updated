<?php
class Tour
{
	private $tourStatusTypes = array('approved', 'pending', 'rejected');
	
	public function getTourRequests($empId, $category = 'originator', $tourId = 0, $start = '', $end = '')
	{
		$search_text_condition = $this->getSearchTextCondition();
		
		$query = "SELECT tr.*, e.first_name, e.last_name, e.HOD_1_id, e.HOD_2_id, e.hr_id, e.email FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		$query .= $search_text_condition;
		
		if($tourId > 0 && $category == 'originator')
		{
			$query .= " AND tr.tour_id = '".$tourId."' AND tr.emp_id = '".$empId."'";
		}	
		elseif($tourId > 0 && $category == 'authorizer')
		{
			$query .= " AND tr.tour_id = '".$tourId."' AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($tourId > 0 && $category == 'superadmin')
		{
			$query .= " AND tr.tour_id = '".$tourId."' ";
		}
		elseif($tourId > 0 && ($category == 'finance' || $category == 'hr'))
		{
			$query .= " AND tr.tour_id = '".$tourId."' ";
		}
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($category == 'hr')
		{
			$query .= " ";
		}
		elseif($category == 'finance')
		{
			$query .= " AND (tr.approve_status_HOD1 = 'approved' OR tr.approve_status_HOD2 = 'approved') ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " ";
		}
		else
		{
			$query .= " AND e.emp_id = '".$empId."' ";
		}
		
		$query .= " ORDER BY tr.tour_id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		//echo $query; 
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
	}
	
	public function createTourRequest($arrayData)
    {
		global $objMail;
		
		list($empId, $tour_start_date, $tour_end_date, $tour_place, $tour_customer, $tour_transport, $tour_purpose, $tickets_purchased_by, $tickets_purchased_by_amount, $hotel_accommodation_by, $hotel_accommodation_by_amount, $local_conveyance_paid_by, $local_conveyance_amount, $requiredDaAmountPerday, $daysDiff, $totalDaAmount, $tourResDetails, $remarks) = $arrayData;
		
		$tour_start_date         = getDbDateFormat(trim($tour_start_date));
		$tour_end_date           = getDbDateFormat(trim($tour_end_date));
		
		$table_name = 'tour_requests';
		$fields=array();

		$fields[]=array('name'=>'emp_id','value'=>$empId);
		$fields[]=array('name'=>'tour_start_date','value'=>$tour_start_date);
		$fields[]=array('name'=>'tour_end_date','value'=>$tour_end_date);
		$fields[]=array('name'=>'tour_place','value'=>$GLOBALS['obj_db']->escape($tour_place));
		$fields[]=array('name'=>'tour_customer','value'=>$GLOBALS['obj_db']->escape($tour_customer));
		$fields[]=array('name'=>'tour_transport','value'=>$tour_transport);
		$fields[]=array('name'=>'tour_purpose','value'=>$GLOBALS['obj_db']->escape($tour_purpose));
		$fields[]=array('name'=>'tickets_purchased_by','value'=>$tickets_purchased_by);
		$fields[]=array('name'=>'tickets_purchased_by_amount','value'=>$tickets_purchased_by_amount);
		$fields[]=array('name'=>'hotel_accommodation_by','value'=>$hotel_accommodation_by);
		$fields[]=array('name'=>'hotel_accommodation_by_amount','value'=>$hotel_accommodation_by_amount);
		$fields[]=array('name'=>'local_conveyance_paid_by','value'=>$local_conveyance_paid_by);
		$fields[]=array('name'=>'local_conveyance_amount','value'=>$local_conveyance_amount);
		$fields[]=array('name'=>'required_da_amount_perday','value'=>$requiredDaAmountPerday);
		$fields[]=array('name'=>'total_days','value'=>$daysDiff);
		$fields[]=array('name'=>'total_da_amount','value'=>$totalDaAmount);
		$fields[]=array('name'=>'remarks','value'=>$GLOBALS['obj_db']->escape($remarks));
		$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
		
		$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
		$flag=$GLOBALS['obj_db']->execute_query($query);
		
		if(!$flag)
		{
			$msg = "Error occured while adding tour request. Please try again.";
		}
		else
		{
			$msg = "Tour request successfully raised.";
			$tourId = $GLOBALS['obj_db']->insert_id();
			if(!empty($tourResDetails))
			{
				$flagResDetails = $this->addTourResDetails($tourId, $tourResDetails);
				if(!$flagResDetails)
				{
					$msg .= "Error in adding tour reservation details.";
				}
			}
			$_SESSION['success'] = 1;
			$objMail->processTourRequestMail($tourId, 'added');
		}
		
		return $msg;
    }

	public function updateTourRequest($arrayData)
    {
		global $objMail;
		
		list($id, $empId, $tour_start_date, $tour_end_date, $tour_place, $tour_customer, $tour_transport, $tour_purpose, $tickets_purchased_by, $tickets_purchased_by_amount, $hotel_accommodation_by, $hotel_accommodation_by_amount, $local_conveyance_paid_by, $local_conveyance_amount, $requiredDaAmountPerday, $daysDiff, $totalDaAmount, $tourResDetails, $tourResDetailsNew, $remarks) = $arrayData;
		
		$tour_start_date         = getDbDateFormat(trim($tour_start_date));
		$tour_end_date           = getDbDateFormat(trim($tour_end_date));
		
		$query = "UPDATE tour_requests SET
				tour_start_date = '".$tour_start_date."',
				tour_end_date = '".$tour_end_date."',
				tour_place = '".$GLOBALS['obj_db']->escape($tour_place)."',
				tour_customer = '".$GLOBALS['obj_db']->escape($tour_customer)."',
				tour_transport = '".$tour_transport."',
				tour_purpose = '".$GLOBALS['obj_db']->escape($tour_purpose)."',
				tickets_purchased_by = '".$tickets_purchased_by."',
				tickets_purchased_by_amount = '".$tickets_purchased_by_amount."',
				hotel_accommodation_by = '".$hotel_accommodation_by."',
				hotel_accommodation_by_amount = '".$hotel_accommodation_by_amount."',
				local_conveyance_paid_by = '".$local_conveyance_paid_by."',
				local_conveyance_amount = '".$local_conveyance_amount."',
				required_da_amount_perday = '".$requiredDaAmountPerday."',
				total_days = '".$daysDiff."',
				total_da_amount = '".$totalDaAmount."',
				remarks = '".$GLOBALS['obj_db']->escape($remarks)."',
				last_updated = '".date('Y-m-d H:i:s')."'
				WHERE 
				tour_id = '".$id."' AND emp_id = '".$empId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
					
			$flag = @$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error occured while updating tour request. Please try again.";
			}
			else
			{
				$msg = "Tour request successfully updated.";
				if(!empty($tourResDetails))
				{
					$flagResDetails = $this->updateTourResDetails($id, $tourResDetails);
					if(!$flagResDetails)
					{
						$msg .= "Error in updating tour reservation details.";
					}
				}
				if(!empty($tourResDetailsNew))
				{
					$flagResDetailsNew = $this->addTourResDetails($id, $tourResDetailsNew);
					if(!$flagResDetailsNew)
					{
						$msg .= "Error in updating new reservation details.";
					}
				}
				
				$_SESSION['success'] = 1;
				$objMail->processTourRequestMail($id, 'updated');
			}		
		return $msg;
	}

	public function deleteTourRequest($id = '', $empId)
    {
		global $objMail;
		
		$query = "DELETE FROM tour_requests WHERE tour_id = '".$id."' AND emp_id = '".$empId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting tour request.";
        }
		else
		{
			$objMail->processTourRequestMail($id, 'deleted');
            $msg = "Tour request deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function updateStatus($tourId, $empId, $status, $comments, $userType)
    {
		global $objMail;
		if($userType == 'authorizer')
		{
			$sqlUpdate = "UPDATE tour_requests SET approve_status_HOD1 = '".trim($status)."', HOD1_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE tour_id = '".$tourId."' ";
		}	
		elseif($userType == 'finance')
		{
			$sqlUpdate = "UPDATE tour_requests SET approve_status_finance = '".trim($status)."', finance_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_finance_id = '".$_SESSION['userId']."' WHERE tour_id = '".$tourId."' ";
		}
		
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		if(!$rsUpdate)
        {
	    $msg = "Error while updating the status of tour request.";
        }
		else
		{
			$objMail->processTourRequestMail($tourId, $status);
			
            $msg = "Tour request ".$status." successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
    
    public function updateAmountDeposited($tourId, $empId, $amountDeposited, $userType)
    {
		global $objMail;
		if($userType == 'finance')
		{
			$sqlUpdate = "UPDATE tour_requests SET amount_deposited = '".$amountDeposited."' WHERE tour_id = '".$tourId."' ";
		}	
		
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		if(!$rsUpdate)
        {
	    $msg = "Error while updating the amount of tour request.";
        }
		else
		{
			$msg = "Amount added successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function getSearchTextCondition()
    {
		$search_text_condition = "";
		if(isset($_REQUEST['txt_search_tour_id']) && $_REQUEST['txt_search_tour_id'] != "")
		{
			$search_text_condition .= " AND tour_id = '".$_REQUEST['txt_search_tour_id']."' ";
		}
		if(isset($_REQUEST['txt_search_emp_name']) && $_REQUEST['txt_search_emp_name'] != "")
		{
			$search_text_condition .= " AND (first_name LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_emp_name'])."%' OR last_name LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_emp_name'])."%') ";
		}
		if(isset($_REQUEST['txt_search_email']) && $_REQUEST['txt_search_email'] != "")
		{
			$search_text_condition .= " AND email = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_email'])."' ";
		}
		if(isset($_REQUEST['txt_search_place']) && $_REQUEST['txt_search_place'] != "")
		{
			$search_text_condition .= " AND tour_place LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_place'])."%' ";
		}
		if(isset($_REQUEST['txt_search_status']) && $_REQUEST['txt_search_status'] != -1)
		{
			$search_text_condition .= " AND approve_status_HOD1 = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_status'])."' ";
		}		
		
        return $search_text_condition;
    }
	
	public function getTourStatus($empId, $category = 'originator')
	{
		$tourStatus = array();
		$allTours = array();
		
		$query = "SELECT IF((tr.approve_status_HOD1 = 'pending' && tr.approve_status_HOD2 = 'pending'), 'pending', IF(tr.approve_status_HOD1 != 'pending', tr.approve_status_HOD1, tr.approve_status_HOD2)) AS final_status, COUNT(tr.tour_id) AS total FROM tour_requests AS tr WHERE 1=1 ";
		
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
				$tourStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->tourStatusTypes, array_keys($tourStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$tourStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM tour_requests AS tr WHERE 1=1 ";
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
		
		$allTours['total'] = $total_applications;
		$allTours['tourStatus'] = $tourStatus;
		
        return $allTours;
	}
	
	public function getTourStatusAuthorizer($empId, $category = 'originator')
	{
		$tourStatus = array();
		$allTours = array();
		
		if($category == 'authorizer')
		{
			$query = "SELECT tr.approve_status_HOD1 AS final_status, COUNT(tr.tour_id) AS total FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'finance')
		{
			$query = "SELECT tr.approve_status_finance AS final_status, COUNT(tr.tour_id) AS total FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'hr')
		{
			$query = "SELECT tr.approve_status_HOD1 AS final_status, COUNT(tr.tour_id) AS total FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'superadmin')
		{
			$query = "SELECT tr.approve_status_HOD1 AS final_status, COUNT(tr.tour_id) AS total FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		}
		
		if($category == 'originator')
		{
			$query .= " AND tr.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($category == 'finance')
		{
			$query .= " ";
		}
		$query .= " GROUP BY final_status";
		$query .= " ORDER BY FIELD(`final_status`, 'approved', 'pending', 'rejected')";
		
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);
		if($GLOBALS['obj_db']->num_rows($rsQuery))
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rsQuery))
			{
				$tourStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->tourStatusTypes, array_keys($tourStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$tourStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
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
		
		$allTours['total'] = $total_applications;
		$allTours['tourStatus'] = $tourStatus;
		
        return $allTours;
	}
	
	private function addTourResDetails($tourId, $tourResDetails)
	{
		$sqlResDetails = "INSERT INTO tour_res_details(tour_id, reservation_start_date, reservation_return_date, reservation_from, reservation_to, reservation_mode, reservation_details, created_date) VALUES ";
		$valueStr = '';
		foreach($tourResDetails as $details)
		{
			$makeValues = " ('".$tourId."', '".$details['resStartDate']."', '".$details['resReturnDate']."', '".$GLOBALS['obj_db']->escape($details['resFrom'])."', '".$GLOBALS['obj_db']->escape($details['resTo'])."', '".$GLOBALS['obj_db']->escape($details['resMode'])."', '".$GLOBALS['obj_db']->escape($details['resModeDetails'])."', '".date('Y-m-d H:i:s')."') ";
			
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
	
	private function updateTourResDetails($tourId, $tourResDetails)
	{
		$msgFlag = true;
		foreach($tourResDetails as $id => $details)
		{		
			$query = "UPDATE tour_res_details SET reservation_start_date = '".$details['resStartDate']."', reservation_return_date = '".$details['resReturnDate']."', reservation_from = '".$GLOBALS['obj_db']->escape($details['resFrom'])."', reservation_to = '".$GLOBALS['obj_db']->escape($details['resTo'])."', reservation_mode = '".$GLOBALS['obj_db']->escape($details['resMode'])."', reservation_details = '".$GLOBALS['obj_db']->escape($details['resModeDetails'])."', last_updated = '".date('Y-m-d H:i:s')."' WHERE id = '".$id."' AND tour_id = '".$tourId."' ";
			
			$flag = @$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msgFlag = false;
			}	
		}
		return $msgFlag;
	}
	
	public function getTourResDetails($empId, $category = 'originator', $tourId)
	{
		if(!$tourId)
			return false;
		
		$tourReservationDetails = array();
		$query = "SELECT trd.* FROM tour_res_details AS trd INNER JOIN tour_requests AS tr ON trd.tour_id = tr.tour_id INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE 1=1 ";
		if($category == 'originator')
		{
			$query .= " AND tr.tour_id = '".$tourId."' AND tr.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND tr.tour_id = '".$tourId."' AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " AND tr.tour_id = '".$tourId."' ";
		}
		elseif($category == 'finance' || $category == 'hr')
		{
			$query .= " AND tr.tour_id = '".$tourId."' ";
		}
	
		$query .= " ORDER BY trd.id ASC";
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);
		
		if($GLOBALS['obj_db']->num_rows($rsQuery) > 0)
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rsQuery))
			{
				$tourReservationDetails[$row['id']] = array('reservation_start_date' => $row['reservation_start_date'], 'reservation_return_date' => $row['reservation_return_date'], 'reservation_from' => $row['reservation_from'], 'reservation_to' => $row['reservation_to'], 'reservation_mode' => $row['reservation_mode'], 'reservation_details' => $row['reservation_details']);
			}	
		}	
        return $tourReservationDetails;
	}
}
?>