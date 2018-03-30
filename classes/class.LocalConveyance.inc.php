<?php
class LocalConveyance
{
	private $allowedImageExtensions = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'JPG', 'JPEG', 'PNG');
	private $conveyanceStatusTypes = array('approved', 'pending', 'rejected');
	
	public function getLocalConveyanceRequests($empId, $category = 'originator', $conveyanceId = 0, $start = '', $end = '')
	{
		$search_text_condition = $this->getSearchTextCondition();
		
		$query = "SELECT lcr.*, e.first_name, e.last_name, e.HOD_1_id, e.HOD_2_id, e.hr_id, e.email, e.branch_id FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		$query .= $search_text_condition;
		
		if($conveyanceId > 0 && $category == 'originator')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' AND lcr.emp_id = '".$empId."'";
		}	
		elseif($conveyanceId > 0 && $category == 'authorizer')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($conveyanceId > 0 && $category == 'superadmin')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' ";
		}
		elseif($conveyanceId > 0 && ($category == 'finance' || $category == 'hr'))
		{
			$query .= " AND lcr.id = '".$conveyanceId."' ";
		}
		elseif($category == 'authorizer')
		{
			$query .= " AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " ";
		}
		elseif($category == 'hr')
		{
			$query .= " ";
		}
		elseif($category == 'finance')
		{
			$query .= " AND (lcr.approve_status_HOD1 = 'approved' OR lcr.approve_status_HOD2 = 'approved') AND e.branch_id = (SELECT branch_id FROM admin_users WHERE user_id = '".$empId."') ";
		}
		else
		{
			$query .= " AND e.emp_id = '".$empId."' ";
		}
		
		$query .= " ORDER BY lcr.id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		//echo $query;
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
	}
	
	public function createRequest($arrayData)
    {
		global $objMail;
		list($empId, $type, $date, $from, $to, $paid_by, $purpose, $travel_mode, $amount, $start_meter_reading, $end_meter_reading, $total_kms, $attachments) = $arrayData;
		
		$date = getDbDateFormat(trim($date));
		
		$table_name = 'local_conveyance_requests';
		$fields=array();

		$fields[]=array('name'=>'emp_id','value'=>$empId);
		$fields[]=array('name'=>'type','value'=>$type);
		$fields[]=array('name'=>'date','value'=>$date);
		$fields[]=array('name'=>'from','value'=>$GLOBALS['obj_db']->escape($from));
		$fields[]=array('name'=>'to','value'=>$GLOBALS['obj_db']->escape($to));
		$fields[]=array('name'=>'paid_by','value'=>$paid_by);
		$fields[]=array('name'=>'purpose','value'=>$GLOBALS['obj_db']->escape($purpose));
		$fields[]=array('name'=>'travel_mode','value'=>$travel_mode);
		$fields[]=array('name'=>'amount','value'=>$amount);
		$fields[]=array('name'=>'start_meter_reading','value'=>$start_meter_reading);
		$fields[]=array('name'=>'end_meter_reading','value'=>$end_meter_reading);
		$fields[]=array('name'=>'total_kms','value'=>$total_kms);
		$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
		
		$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
		$flag=$GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
		{
			$msg = "Error occured while adding request. Please try again.";
		}
		else
		{
			$msg = "Local Conveyance Request successfully raised.";
			$conveyanceId = $GLOBALS['obj_db']->insert_id();
			
			if(!empty($attachments))
			{
			    if(!$this->addAttachments($empId, $conveyanceId, $attachments))
				{
					$msg .= "Error in saving attachments";
				}	
			}
			$objMail->processLocalConveyanceMail($conveyanceId, 'added');
			$_SESSION['success'] = 1;
		}
		
		return $msg;
    }

	public function updateRequest($arrayData)
    {
		global $objMail;
		
		list($id, $empId, $type, $date, $from, $to, $paid_by, $purpose, $travel_mode, $amount, $start_meter_reading, $end_meter_reading, $total_kms, $attachments, $attachmentsNew) = $arrayData;
		
		$date = getDbDateFormat(trim($date));
		
		$query = "UPDATE local_conveyance_requests SET
				type = '".$type."',
				date = '".$date."',
				`from` = '".$GLOBALS['obj_db']->escape($from)."',
				`to` = '".$GLOBALS['obj_db']->escape($to)."',
				paid_by = '".$paid_by."',
				purpose = '".$GLOBALS['obj_db']->escape($purpose)."',
				travel_mode = '".$travel_mode."',
				amount = '".$amount."',
				start_meter_reading = '".$start_meter_reading."',
				end_meter_reading = '".$end_meter_reading."',
				total_kms = '".$total_kms."',
				last_updated = '".date('Y-m-d H:i:s')."'
				WHERE 
				id = '".$id."' AND emp_id = '".$empId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
					
			$flag = @$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error occured while updating request. Please try again.";
			}
			else
			{
				if(!empty($attachments))
				{
					if(!$this->updateAttachments($empId, $id, $attachments))
					{
						$msg .= "Error in saving attachments";
					}	
				}
				if(!empty($attachmentsNew))
				{
					if(!$this->addAttachments($empId, $id, $attachmentsNew))
					{
						$msg .= "Error in saving new attachments";
					}	
				}
				
				$objMail->processLocalConveyanceMail($id, 'updated');
				$msg = "Request successfully updated.";
				$_SESSION['success'] = 1;
			}		
		return $msg;
	}

	public function deleteRequest($id = '', $empId)
    {
		global $objMail;
		
		$query = "DELETE FROM local_conveyance_requests WHERE id = '".$id."' AND emp_id = '".$empId."' AND approve_status_HOD1 = 'pending' AND approve_status_HOD2 = 'pending' ";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting request.";
        }
		else
		{
			$objMail->processLocalConveyanceMail($id, 'deleted');
            $msg = "Request deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function updateStatus($conveyanceId, $empId, $status, $comments, $userType)
    {
		global $objMail;
		if($userType == 'authorizer')
		{
			$sqlUpdate = "UPDATE local_conveyance_requests SET approve_status_HOD1 = '".trim($status)."', HOD1_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_HOD1_id = '".$_SESSION['userId']."' WHERE id = '".$conveyanceId."' ";
		}	
		elseif($userType == 'finance')
		{
			$sqlUpdate = "UPDATE local_conveyance_requests SET approve_status_finance = '".trim($status)."', finance_comment = '".$GLOBALS['obj_db']->escape($comments)."', approved_by_finance_id = '".$_SESSION['userId']."' WHERE id = '".$conveyanceId."' ";
		}
		
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		if(!$rsUpdate)
        {
	        $msg = "Error while updating the status of tour request.";
        }
		else
		{
			$objMail->processLocalConveyanceMail($conveyanceId, $status);
			
            $msg = "Tour request ".$status." successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function updateAmountDeposited($conveyanceId, $empId, $amountDeposited, $userType)
    {
		global $objMail;
		if($userType == 'finance')
		{
			$sqlUpdate = "UPDATE local_conveyance_requests SET amount_deposited = '".$amountDeposited."' WHERE id = '".$conveyanceId."' ";
		}	
		
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		if(!$rsUpdate)
        {
	    $msg = "Error while updating the amount.";
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
		if(isset($_REQUEST['txt_search_conveyance_id']) && $_REQUEST['txt_search_conveyance_id'] != "")
		{
			$search_text_condition .= " AND id = '".$_REQUEST['txt_search_conveyance_id']."' ";
		}
		if(isset($_REQUEST['txt_search_emp_name']) && $_REQUEST['txt_search_emp_name'] != "")
		{
			$search_text_condition .= " AND (first_name LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_emp_name'])."%' OR last_name LIKE '%".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_emp_name'])."%') ";
		}
		if(isset($_REQUEST['txt_search_email']) && $_REQUEST['txt_search_email'] != "")
		{
			$search_text_condition .= " AND email = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_email'])."' ";
		}
		if(isset($_REQUEST['txt_search_date_from']) && $_REQUEST['txt_search_date_from'] != "" && isset($_REQUEST['txt_search_date_to']) && $_REQUEST['txt_search_date_to'] != "" && !empty($_REQUEST['date_included']))
		{
			$txtSearchDateFromDb = getDbDateFormat($_REQUEST['txt_search_date_from']);
			$txtSearchDateToDb = getDbDateFormat($_REQUEST['txt_search_date_to']);
			
			$search_text_condition .= " AND date >= '".$txtSearchDateFromDb."' AND date <= '".$txtSearchDateToDb."' ";
		}
		if(isset($_REQUEST['txt_search_status']) && $_REQUEST['txt_search_status'] != -1)
		{
			$search_text_condition .= " AND approve_status_HOD1 = '".$GLOBALS['obj_db']->escape($_REQUEST['txt_search_status'])."' ";
		}
		
        return $search_text_condition;
    }	
	
	private function addAttachments($empId, $conveyanceId, $attachments)
	{
		if(empty($attachments))
			return false;
		
		if(checkCreateFolder("user_uploads/emp_".$empId))
		{
			$sqlAttach = "INSERT INTO local_conveyance_proofs(conveyance_id, comments, attachment) VALUES ";
			$valueStr = '';
			$totalCount = count($attachments);
			$added = 0;
			
			foreach($attachments as $details)
			{
				if($details['imageDeleted'] == 1)
				{
					$imageName = '';
				}
				else
				{
					$imageName = $details['attachment']['name'];
					if(is_uploaded_file($details['attachment']['tmp_name'])) 
					{
						$imgExt = end(explode('.', $imageName));
						$imgUpload = true;
						if(!in_array($imgExt, $this->allowedImageExtensions))
						{
							$imgUpload = false;
						}
						else
						{
							move_uploaded_file($details['attachment']['tmp_name'], "user_uploads/emp_".$empId."/".$imageName);
						}
					}
				}
				if($valueStr == '')
				{
					$valueStr = " ('".$conveyanceId."', '".$GLOBALS['obj_db']->escape($details['comments'])."', '".$imageName."') ";
				}
				else
				{
					$valueStr .= ", ('".$conveyanceId."', '".$GLOBALS['obj_db']->escape($details['comments'])."', '".$imageName."') ";
				}
			}
			$sqlAttach .= $valueStr;
		
			$flag=$GLOBALS['obj_db']->execute_query($sqlAttach);
			if(!$flag)
			{
				$msg = "Error occured while adding attachments. Please try again.";
			}
		}
		else
		{
			$msg = "Error in creating files.";
		}
		if($flag)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	private function updateAttachments($empId, $conveyanceId, $attachments)
	{
		if(empty($attachments))
			return false;
		
		if(checkCreateFolder("user_uploads/emp_".$empId))
		{
			$totalCount = count($attachments);
			$added = 0;
			
			foreach($attachments as $attachmentId => $details)
			{
				if($details['imageDeleted'] == 1)
				{
					$imageName = '';
					$sqlAttach = "UPDATE local_conveyance_proofs SET comments = '".$GLOBALS['obj_db']->escape($details['comments'])."', attachment = '".$imageName."' WHERE id = '".$attachmentId."' ";
				}
				else
				{
					if($details['attachment']['name'] == '')
					{
						$sqlAttach = "UPDATE local_conveyance_proofs SET comments = '".$GLOBALS['obj_db']->escape($details['comments'])."' WHERE id = '".$attachmentId."' ";
					}
					else
					{
						$imageName = $details['attachment']['name'];
						if(is_uploaded_file($details['attachment']['tmp_name'])) 
						{
							$imgExt = end(explode('.', $imageName));
							$imgUpload = true;
							if(!in_array($imgExt, $this->allowedImageExtensions))
							{
								$imgUpload = false;
							}
							else
							{
								move_uploaded_file($details['attachment']['tmp_name'], "user_uploads/emp_".$empId."/".$imageName);
							}
						}
						$sqlAttach = "UPDATE local_conveyance_proofs SET comments = '".$GLOBALS['obj_db']->escape($details['comments'])."', attachment = '".$imageName."' WHERE id = '".$attachmentId."' ";
					}
				}
				
				$flag=$GLOBALS['obj_db']->execute_query($sqlAttach);
				if($flag)
				{
					$added++;
					$msg = "Error occured while adding attachments. Please try again.";
				}
			}
		}
		else
		{
			$msg = "Error in creating files.";
		}
		if($totalCount == $added)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getAttachments($empId, $category = 'originator', $conveyanceId)
	{
		if(!$conveyanceId)
			return false;
		
		$query = "SELECT lcp.* FROM local_conveyance_proofs AS lcp INNER JOIN local_conveyance_requests AS lcr ON lcp.conveyance_id = lcr.id INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' AND lcr.emp_id = '".$empId."'";
		}	
		elseif($category == 'authorizer')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' AND e.HOD_1_id = '".$empId."' ";
		}
		elseif($category == 'superadmin')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' ";
		}
		elseif($category == 'finance' || $category == 'hr')
		{
			$query .= " AND lcr.id = '".$conveyanceId."' ";
		}		
		
		$query .= " ORDER BY lcp.id ASC";	
		
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;		
	}
	
	public function getLocalConveyanceStatus($empId, $category = 'originator')
	{
		$conveyanceStatus = array();
		$allConveyance = array();
		
		$query = "SELECT IF((lcr.approve_status_HOD1 = 'pending' && lcr.approve_status_HOD2 = 'pending'), 'pending', IF(lcr.approve_status_HOD1 != 'pending', lcr.approve_status_HOD1, lcr.approve_status_HOD2)) AS final_status, COUNT(lcr.id) AS total FROM local_conveyance_requests AS lcr WHERE 1=1 ";
		
		if($category == 'originator')
		{
			$query .= " AND lcr.emp_id = '".$empId."'";
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
				$conveyanceStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->conveyanceStatusTypes, array_keys($conveyanceStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$conveyanceStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM local_conveyance_requests AS lcr WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND lcr.emp_id = '".$empId."'";
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
		
		$allConveyance['total'] = $total_applications;
		$allConveyance['conveyanceStatus'] = $conveyanceStatus;
		
        return $allConveyance;
	}
	
	public function getLocalConveyanceStatusAuthorizer($empId, $category = 'originator')
	{
		$conveyanceStatus = array();
		$allConveyance = array();
		
		if($category == 'authorizer')
		{
			$query = "SELECT lcr.approve_status_HOD1 AS final_status, COUNT(lcr.id) AS total FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'finance')
		{
			$query = "SELECT lcr.approve_status_finance AS final_status, COUNT(lcr.id) AS total FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'hr')
		{
			$query = "SELECT lcr.approve_status_HOD1 AS final_status, COUNT(lcr.id) AS total FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		}
		elseif($category == 'superadmin')
		{
			$query = "SELECT lcr.approve_status_HOD1 AS final_status, COUNT(lcr.id) AS total FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		}
		
		
		if($category == 'originator')
		{
			$query .= " AND lcr.emp_id = '".$empId."'";
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
				$conveyanceStatus[$row['final_status']] = $row['total'];
			}
			
			$diffStatus = array_diff($this->conveyanceStatusTypes, array_keys($conveyanceStatus));
			if(!empty($diffStatus))
			{
			    foreach($diffStatus as $status)
				{
					$conveyanceStatus[$status] = 0;
				}
			}	
		}
		
		$queryTotal = "SELECT COUNT(*) AS total_applications FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE 1=1 ";
		if($category == 'originator')
		{
			$queryTotal .= " AND lcr.emp_id = '".$empId."'";
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
		
		$allConveyance['total'] = $total_applications;
		$allConveyance['conveyanceStatus'] = $conveyanceStatus;
		
        return $allConveyance;
	}
}
?>