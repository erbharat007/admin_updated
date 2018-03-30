<?php
class Mail
{
	public $adminUsers;
	public $mailReceivers;
	public $nameUsers;
	
	public function __construct()
	{
		$sql = "SELECT first_name, last_name, user_type, email, branch_id, centralised_admin FROM admin_users WHERE user_type IN ('finance', 'hr') ";
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		if($GLOBALS['obj_db']->num_rows($rs))
		{
			while($row = $GLOBALS['obj_db']->fetch_array($rs))
			{
				$this->adminUsers[$row['user_type']][] = $row['email'];
				$this->adminUsers[$row['user_type']."_".$row['branch_id']][] = $row['email'];
				
				$this->nameUsers[$row['user_type']][] = $row['first_name'].' '.$row['last_name'];
				$this->nameUsers[$row['user_type']."_".$row['branch_id']][] = $row['first_name'].' '.$row['last_name'];
				
				if($row['centralised_admin'] == 'YES')
				{
					$this->adminUsers[$row['user_type']."_central"] = $row['email'];
					$this->nameUsers[$row['user_type']."_central"] = $row['first_name'].' '.$row['last_name'];
				}	
			}
			/*echo '<pre>';
			print_r($this->adminUsers);
			echo '</pre>';
			die;*/
		}
	}
	
	public function processLeaveRequestMail($applicationId, $action)
	{
		global $objEmployee, $objLeaves;
		if($action == 'approved' || $action == 'rejected')
		{
			$userType = $_SESSION['userType'];
		}
		else
		{
			$userType = 'originator';
		}
		$rsDetail  = $objLeaves->getLeaveApplications($_SESSION['userId'], $userType, $applicationId);
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
		
		$rowHeads = $objEmployee->getEmployeeHeads($rowDetail['emp_id']);
		
		$requesterMsg  = $this->getMailTemplateForLeaveRequester($rowDetail, $action);
		$authorizerMsg = $this->getMailTemplateForLeaveAuthorizer($rowDetail, $rowHeads, $action);
		
		$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
		$this->mailReceivers['to']   = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
		$headsEmails                 = array($rowHeads['HOD_1']['email'], $rowHeads['HOD_2']['email'], $rowHeads['hr']['email']);
		$assigneeEmail               = array($rowDetail['assignee_email']);
		$this->mailReceivers['cc'] = array_filter(array_merge($headsEmails, $assigneeEmail));
		
		if($action == 'added')
		{
			$requesterSub  = 'Leaves Successfully Applied';
			$authorizerSub = 'Leave Request from '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'updated')
		{
			$requesterSub =  'Leaves Successfully updated';
			$authorizerSub = 'Leave Request updated by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'deleted')
		{
			$requesterSub =  'Leaves Successfully deleted';
			$authorizerSub = 'Leave Request deleted by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'approved')
		{
			$leaveFromDate_Obj = new DateTime($rowDetail['leave_from_date']);
			$leaveToDate_Obj = new DateTime($rowDetail['leave_to_date']);
			
			$leaveFromDate_Str = $leaveFromDate_Obj->format('d-m-Y');
			$leaveToDate_Str = $leaveToDate_Obj->format('d-m-Y');
						
			$requesterSub =  'Congratulations !! Your leave for the date '.$leaveFromDate_Str. ' to '. $leaveToDate_Str. ' has been approved';
			$authorizerSub = 'You have approved the leave request of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'rejected')
		{
			$leaveFromDate_Obj = new DateTime($rowDetail['leave_from_date']);
			$leaveToDate_Obj = new DateTime($rowDetail['leave_to_date']);
			
			$leaveFromDate_Str = $leaveFromDate_Obj->format('d-m-Y');
			$leaveToDate_Str = $leaveToDate_Obj->format('d-m-Y');
			
			$requesterSub =  'Your leave for the date '.$leaveFromDate_Str. ' to '. $leaveToDate_Str. ' has been rejected';
			$authorizerSub = 'You have rejected the leave request of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		$this->sendMail($requesterSub,  $requesterMsg);
		if($action == 'approved' || $action == 'rejected')
		{
			$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
			$this->mailReceivers['to']   = array('name' => $_SESSION['userName'], 'email' => $_SESSION['userEmail']);
		}
		else
		{
			$this->mailReceivers['from'] = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
			$this->mailReceivers['to']   = array('name' => $rowHeads['HOD_1']['fullName'], 'email' => $rowHeads['HOD_1']['email']);
		}
		$this->mailReceivers['cc'] = array();
		$this->sendMail($authorizerSub,  $authorizerMsg);
	}
	
	public function processTourRequestMail($tourId, $action)
	{
		global $objEmployee, $objTour;
		if($action == 'approved' || $action == 'rejected')
		{
			$userType = $_SESSION['userType'];
		}
		else
		{
			$userType = 'originator';
		}
		$rsDetail  = $objTour->getTourRequests($_SESSION['userId'], $userType, $tourId);
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
		
		$tourReservationDetails = $objTour->getTourResDetails($_SESSION['userId'], $userType, $tourId);
		
		$rowHeads = $objEmployee->getEmployeeHeads($rowDetail['emp_id']);		
		
		$requesterMsg  = $this->getMailTemplateForTourRequester($rowDetail, $tourReservationDetails, $action);
		$authorizerMsg = $this->getMailTemplateForTourAuthorizer($rowDetail, $tourReservationDetails, $rowHeads, $action);
		
		$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
		$this->mailReceivers['to']   = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
		$headsEmails                 = array($rowHeads['HOD_1']['email'], $rowHeads['HOD_2']['email'], $rowHeads['hr']['email'], $this->adminUsers['finance_central']);
		$this->mailReceivers['cc']   = $headsEmails;
		
		if($action == 'added')
		{
			$requesterSub  = 'Tour Request Successfully Raised';
			$authorizerSub = 'New Tour Request from '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'updated')
		{
			$requesterSub =  'Tour Request Successfully updated';
			$authorizerSub = 'Tour Request updated by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'deleted')
		{
			$requesterSub =  'Tour Request Successfully deleted';
			$authorizerSub = 'Tour Request deleted by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'approved')
		{
			$tourStartDate_Obj = new DateTime($rowDetail['tour_start_date']);
			$tourEndDate_Obj = new DateTime($rowDetail['tour_end_date']);
			
			$tourStartDate_Str = $tourStartDate_Obj->format('d-m-Y');
			$tourEndDate_Str = $tourEndDate_Obj->format('d-m-Y');
				
			$requesterSub =  'Congratulations !! Your tour request for the date '.$tourStartDate_Str. ' to '. $tourEndDate_Str. ' has been approved';
			$authorizerSub = 'You have approved the tour request of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'rejected')
		{
			$tourStartDate_Obj = new DateTime($rowDetail['tour_start_date']);
			$tourEndDate_Obj = new DateTime($rowDetail['tour_end_date']);
			
			$tourStartDate_Str = $tourStartDate_Obj->format('d-m-Y');
			$tourEndDate_Str = $tourEndDate_Obj->format('d-m-Y');
			
			$requesterSub =  'Your tour request for the date '.$tourStartDate_Str. ' to '. $tourEndDate_Str. ' has been rejected';
			$authorizerSub = 'You have rejected the tour request of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		$this->sendMail($requesterSub,  $requesterMsg);
		
		if($action == 'approved' || $action == 'rejected')
		{
			$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
			$this->mailReceivers['to']   = array('name' => $_SESSION['userName'], 'email' => $_SESSION['userEmail']);
		}
		else
		{
			$this->mailReceivers['from'] = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
			$this->mailReceivers['to']   = array('name' => $rowHeads['HOD_1']['fullName'], 'email' => $rowHeads['HOD_1']['email']);
		}		
		$this->mailReceivers['cc'] = array();
		$this->sendMail($authorizerSub,  $authorizerMsg);
	}
	
	public function processDailyActivityMail($dailyActivity, $action, $empId)
	{
		global $objEmployee, $objActivity;
		if($action == 'approved' || $action == 'rejected')
		{
			$rsHeads  = $objEmployee->getEmployeeData($_SESSION['userId']);
			$rowHeads = $GLOBALS['obj_db']->fetch_array($rsHeads);
		}
		else
		{
			$rsDetail  = $objEmployee->getEmployeeData($_SESSION['userId']);
			$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
			
			$rowHeads = $objEmployee->getEmployeeHeads($_SESSION['userId']);
		}
		
		$requesterMsg  = $this->getMailTemplateForDailyActivityRequester($rowDetail, $action, $dailyActivity);
		$authorizerMsg = $this->getMailTemplateForDailyActivityAuthorizer($rowDetail, $rowHeads, $action, $dailyActivity);
		
		$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
		$this->mailReceivers['to']   = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
		$headsEmails                 = array($rowHeads['HOD_1']['email'], $rowHeads['HOD_2']['email']);
		$this->mailReceivers['cc']   = $headsEmails;
		
		if($action == 'added')
		{
			$requesterSub  = 'Daily Activity(s) Successfully Created';
			$authorizerSub = 'New Daily Activity(s) Request from '.$rowDetail['first_name'].' '.$rowDetail['last_name'];
		}
		elseif($action == 'updated')
		{
			$requesterSub =  'Daily Activity(s) Successfully updated';
			$authorizerSub = 'Daily Activity(s) updated by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'deleted')
		{
			$requesterSub =  'Daily Activity(s) Successfully deleted';
			$authorizerSub = 'Daily Activity(s) deleted by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'approved')
		{
			$requesterSub =  'Congratulations !! Some of your daily activity(s) have been approved';
			$authorizerSub = 'You have approved the daily activity(s) of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'rejected')
		{
			$requesterSub =  'Some of your daily activity(s) have been rejected';
			$authorizerSub = 'You have rejected the daily activity(s) of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		$this->sendMail($requesterSub,  $requesterMsg);
		
		if($action == 'approved' || $action == 'rejected')
		{
			$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
			$this->mailReceivers['to']   = array('name' => $_SESSION['userName'], 'email' => $_SESSION['userEmail']);
		}
		else
		{
			$this->mailReceivers['from'] = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
			$this->mailReceivers['to']   = array('name' => $rowHeads['HOD_1']['fullName'], 'email' => $rowHeads['HOD_1']['email']);
		}
		
		$this->sendMail($authorizerSub,  $authorizerMsg);
	}
	
	public function processLocalConveyanceMail($conveyanceId, $action)
	{
		global $objEmployee, $objLocalConveyance;
		if($action == 'approved' || $action == 'rejected')
		{
			$userType = $_SESSION['userType'];
		}
		else
		{
			$userType = 'originator';
		}
		$rsDetail  = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], $userType, $conveyanceId);
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
		
		$rowHeads = $objEmployee->getEmployeeHeads($rowDetail['emp_id']);		
		
		$requesterMsg  = $this->getMailTemplateForLocalConveyanceRequester($rowDetail, $action);
		$authorizerMsg = $this->getMailTemplateForLocalConveyanceAuthorizer($rowDetail, $rowHeads, $action);
		
		$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
		$this->mailReceivers['to']   = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
		$headsEmails                 = array($rowHeads['HOD_1']['email'], $rowHeads['HOD_2']['email'], $rowHeads['hr']['email']);
		$this->mailReceivers['cc']   = array_filter(array_merge($headsEmails, $this->adminUsers['finance_'.$rowDetail['branch_id']]));
		
		if($action == 'added')
		{
			$requesterSub  = 'Local Conveyance Request Successfully Raised';
			$authorizerSub = 'New Local Conveyance Request from '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'updated')
		{
			$requesterSub =  'Local Conveyance Request Successfully updated';
			$authorizerSub = 'Local Conveyance Request updated by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'deleted')
		{
			$requesterSub =  'Local Conveyance Request Successfully deleted';
			$authorizerSub = 'Local Conveyance Request deleted by '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'approved')
		{
			$dateObj = new DateTime($rowDetail['date']);
			$dateStr = $dateObj->format('d-m-Y');
			
			$requesterSub =  'Congratulations !! Your Local Conveyance request for the date '.$dateStr.' has been approved';
			$authorizerSub = 'You have approved the Local Conveyance request of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		elseif($action == 'rejected')
		{
			$dateObj = new DateTime($rowDetail['date']);
			$dateStr = $dateObj->format('d-m-Y');
			
			$requesterSub =  'Your Local Conveyance request for the date '.$dateStr.' has been rejected';
			$authorizerSub = 'You have rejected the Local Conveyance request of '.$rowDetail['first_name'].' '.$rowDetail['last_name']; 
		}
		$this->sendMail($requesterSub,  $requesterMsg);
		
		if($action == 'approved' || $action == 'rejected')
		{
			$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
			$this->mailReceivers['to']   = array('name' => $_SESSION['userName'], 'email' => $_SESSION['userEmail']);
		}
		else
		{
			$this->mailReceivers['from'] = array('name' => $rowDetail['first_name']." ".$rowDetail['last_name'], 'email' => $rowDetail['email']);
			$this->mailReceivers['to']   = array('name' => $rowHeads['HOD_1']['fullName'], 'email' => $rowHeads['HOD_1']['email']);
		}
		
		$this->mailReceivers['cc'] = array();
		$this->sendMail($authorizerSub,  $authorizerMsg);
	}
	
	public function processDailyAllowanceMail($tourId, $action)
	{
		global $objEmployee, $objTour, $objDailyAllowance;
		if($action == 'approved' || $action == 'rejected')
		{
			$userType = $_SESSION['userType'];
		}
		else
		{
			$userType = 'originator';
		}
		
		$rsTour  = $objTour->getTourRequests($_SESSION['userId'], $userType, $tourId);
		$rowTour = $GLOBALS['obj_db']->fetch_array($rsTour);
		
		$rsDetail  = $objDailyAllowance->getCalculatedDa($_SESSION['userId'], $userType, $tourId);
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
		
		$rowHeads = $objEmployee->getEmployeeHeads($rowDetail['emp_id']);		
		
		$requesterMsg  = $this->getMailTemplateForDailyAllowanceRequester($rowDetail, $rowTour, $action);
		$authorizerMsg = $this->getMailTemplateForDailyAllowanceAuthorizer($rowDetail, $rowTour, $rowHeads, $action);
		
		$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
		$this->mailReceivers['to']   = array('name' => $rowTour['first_name']." ".$rowTour['last_name'], 'email' => $rowTour['email']);
		$headsEmails                 = array($rowHeads['HOD_1']['email'], $rowHeads['HOD_2']['email'], $rowHeads['hr']['email'], $this->adminUsers['finance_central']);
		$this->mailReceivers['cc']   = $headsEmails;
		
		if($action == 'added')
		{
			$requesterSub  = 'Daily Allowance Request Successfully Raised';
			$authorizerSub = 'New Daily Allowance Request from '.$rowTour['first_name'].' '.$rowTour['last_name']; 
		}
		elseif($action == 'updated')
		{
			$requesterSub =  'Daily Allowance Request Successfully updated';
			$authorizerSub = 'Daily Allowance Request updated by '.$rowTour['first_name'].' '.$rowTour['last_name']; 
		}
		elseif($action == 'deleted')
		{
			$requesterSub =  'Daily Allowance Request Successfully deleted';
			$authorizerSub = 'Daily Allowance Request deleted by '.$rowTour['first_name'].' '.$rowTour['last_name']; 
		}
		elseif($action == 'approved')
		{
			$requesterSub =  'Congratulations !! Your Daily Allowance request for the tour ID '.$rowTour['tour_id'].' has been approved';
			$authorizerSub = 'You have approved the Daily Allowance request of '.$rowTour['first_name'].' '.$rowTour['last_name']; 
		}
		elseif($action == 'rejected')
		{
			$requesterSub =  'Your Daily Allowance request for the tour ID '.$rowTour['tour_id'].' has been rejected';
			$authorizerSub = 'You have rejected the Daily Allowance request of '.$rowTour['first_name'].' '.$rowTour['last_name']; 
		}
		$this->sendMail($requesterSub,  $requesterMsg);
		
		if($action == 'approved' || $action == 'rejected')
		{
			$this->mailReceivers['from'] = array('name' => DEFAULT_FROM_NAME, 'email' => DEFAULT_FROM_EMAIL);
			$this->mailReceivers['to']   = array('name' => $_SESSION['userName'], 'email' => $_SESSION['userEmail']);
		}
		else
		{
			$this->mailReceivers['from'] = array('name' => $rowTour['first_name']." ".$rowTour['last_name'], 'email' => $rowTour['email']);
			$this->mailReceivers['to']   = array('name' => $rowHeads['HOD_1']['fullName'], 'email' => $rowHeads['HOD_1']['email']);
		}
		
		$this->sendMail($authorizerSub,  $authorizerMsg);
	}
	
	public function getMailTemplateForLeaveRequester($details, $action)
	{
		ob_start();
	?>
		<tr class='tableinner-part'>
			<td vAlign=top align=Justify>
			  <table width="96%" cellpadding="0" cellspacing="0" align="center">
      	  <tr>
      	    <td>
			<font face=arial size=2>
				Dear <?php echo $details['first_name']." ".$details['last_name']; ?><br /><br />
				<?php
				$leaveFromDate_Obj = new DateTime($details['leave_from_date']);
				$leaveToDate_Obj = new DateTime($details['leave_to_date']);
				
				$leaveFromDate_Str = $leaveFromDate_Obj->format('d-m-Y');
				$leaveToDate_Str = $leaveToDate_Obj->format('d-m-Y');
			
				if($action == 'added')
				{
					echo "You have successfully applied a leave from ".$leaveFromDate_Str. " to ".$leaveToDate_Str.". You will receive an automated email once your leave has been approved by your HOD. ";
				}
				elseif($action == 'updated')
				{
					echo "You have successfully updated your leave application for the date ".$leaveFromDate_Str. " to ".$leaveToDate_Str;
				}
				elseif($action == 'deleted')
				{
					echo "You have successfully deleted your leave application for the date ".$leaveFromDate_Str. " to ".$leaveToDate_Str;
				}
				elseif($action == 'approved')
				{
					echo "Congratulations !! Your leave for the date ".$leaveFromDate_Str. " to ". $leaveToDate_Str. " has been approved by your HOD. Please contact him/her personally for more information.";
				}
				elseif($action == 'rejected')
				{
					echo "Unfortunately, Your leave for the date ".$leaveFromDate_Str. " to ". $leaveToDate_Str. " has been rejected by your HOD. Please contact him/her personally for more information.";
				}
				?>				
			</font>
			</td>
			</tr>
			</table>
			</td> 
		</tr>
		<tr>
		 <td>
		  <table width="96%" cellpadding="0" cellspacing="0" align="center">
      	  <tr>
			<td> Leave Dates - </td>
			<td><?php echo $leaveFromDate_Str. " to ".$leaveToDate_Str; ?></td>
			</tr>
			</table>
			</td>
			</tr>
		
		<tr>
		<td>
		   <table width="96%" cellpadding="0" cellspacing="0" align="center">
      	  <tr>
			<td> Job Assignee Name - </td>
			<td><?php echo $details['assignee_first_name']. " ".$details['assignee_last_name']; ?></td>
			</tr></table></tr>
		</tr>
		
	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;
	}
	
	public function getMailTemplateForLeaveAuthorizer($details, $rowHod, $action)
	{
		ob_start();
	?>		
	<tr>
	   
		<td colSpan=3 vAlign=top align=Justify>
		   <table width="96%" cellspacing="0" cellpadding="0" align="center">
	   	  <tr><td>
			<font face=arial size=2>
				Dear <?php echo $rowHod['HOD_1']['fullName']; ?><br /><br />
				<?php
				$leaveFromDate_Obj = new DateTime($details['leave_from_date']);
				$leaveToDate_Obj = new DateTime($details['leave_to_date']);
				
				$leaveFromDate_Str = $leaveFromDate_Obj->format('d-m-Y');
				$leaveToDate_Str = $leaveToDate_Obj->format('d-m-Y');
				
				if($action == 'added')
				{
					echo "You have received a leave application from ".$details['first_name']. " ".$details['last_name'].". Applicant is anticipating hearing from you at the earliest.<br/><br/>Once you have reviewed the application, you can approve the same through your dashboard and the applicant will receive an automated email after approval.";
				}
				elseif($action == 'updated')
				{
					echo $details['first_name']." ".$details['last_name']. " has updated his/her leave application for the date ".$leaveFromDate_Str. " to ".$leaveToDate_Str;
				}
				elseif($action == 'deleted')
				{
					echo $details['first_name']." ".$details['last_name']. " has deleted his/her leave application for the date ".$leaveFromDate_Str. " to ".$leaveToDate_Str;
				}
				elseif($action == 'approved')
				{
					echo "You have approved the leave request of ".$details['first_name']." ".$details['last_name']." for the date ".$leaveFromDate_Str." to ".$leaveToDate_Str;
				}
				elseif($action == 'rejected')
				{
					echo "You have rejected the leave request of ".$details['first_name']." ".$details['last_name']." for the date ".$leaveFromDate_Str." to ".$leaveToDate_Str;
				}
				?>				
			</font>
		</td> 
	</tr>
	<tr>
      <table width="96%" cellpadding="0" cellspacing="0" align="center">
      	  <tr>
      	  	
		<td>Leave Dates - </td>
		<td><?php echo $leaveFromDate_Str. " to ".$leaveToDate_Str; ?></td>
      	  </tr>
      </table>
	</tr>
	<tr>
	  <td>
	   <table width="96%" cellpadding="0" cellspacing="0" align="center">
      	  <tr>
		<td>Job Assignee Name - </td>
		<td><?php echo $details['assignee_first_name']. " ".$details['assignee_last_name']; ?></td>
		</tr>
		</table>
		</td>
	</tr>
		
	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;		
	}
	
	public function getMailHeader()
	{
		ob_start();
	?>
 		<style type='text/css'>
.border-table th, .border-table td{ padding: 5px!important; padding: 5px!important }
</style>
<!--
<style type="text/css">
		.tablecontainer {
			width: 650;
			font-family: arial;
			font-size: 14px;
			background-color: #ffffff;
			color: #000;
			border: 1px solid #aaaaaa;
		}
		.tablecontainer td {
			width: 180px;			
			color: #000;
		}
		a {
			color: #000;
		}
		h3{ padding-left:10px; font-size:16px;}
		h4{ font-size:15px;}
		.tableinner-part td{padding:10px 27px 32px 15px;}
		.copyright{ font-size:12px; text-indent:20px; }
		 .copyright p{ text-indent:20px;} 
		</style>
		<table class=tablecontainer cellSpacing=0 cellPadding=0 width=650 align=center border=0>
			<tbody>
				<tr>
					<td>
						<table cellSpacing=0 cellPadding=0 width=650 align=center border=0>
							<tbody>
								<tr>
									<td align=left colSpan=3 height=1>			
										<table cellspacing=0 cellpadding=0 width='650' border=0>
											<tbody>
												
											</tbody>
										</table>
									</td>
								</tr>
 -->

			<table width="650" cellspacing="0" cellpadding="0" align="center" style="border: 1px solid #aaaaaa; background-color:#ffffff; font-family: arial ">
				<tr>
				   <td>
				       <table width="100%" cellspacing="0" cellpadding="0" align="center" style="border: 1px solid #aaaaaa; background-color:#ffffff ">
							<tr>
								<td align='center' style='background:#000; padding: 10px 0'><img align='center' src='<?php echo SITE_URL;?>/images/logo-bergen-group.png' /> </td>
								</tr>
								<tr>
								<td align='center'><img width="100%" align='center' src='<?php echo SITE_URL;?>/images/email-banner.png' /></td>
								</tr>
						</table>
                   </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
			
	<?php	
		$mailHeader = ob_get_contents();
		ob_end_clean();
		return $mailHeader;
	}
	
	public function getMailFooter()
	{
		ob_start();
	?>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
					<table width="96%" align="center" cellspacing="0" cellpadding="0">
						<tr><td>Best Wishes,<br/><p><b>Admin Team</b></p><br/></td></tr>
					</table>	
					</td>
				</tr>
				<tr>
				  <td>
					<table width="100%" style="background: #000000; color: #ffffff; padding: 10px 0" cellpadding="0" cellspacing="0">
						<tr>
						  <td style="text-align: center; color: #ffffff;  padding: 10px 0">Company Name - <br>
						  Mobile - <br>
						  Email id -
						  </td>
					</tr>
					</table>
				  </td>
				</tr>
			</table>
					
	<?php
		$mailFooter = ob_get_contents();
		ob_end_clean();
		return $mailFooter;
	}
	
	public function sendMail($mailSubject, $mailBody)
	{
		global $objPhpMailer;
		
		//$objPhpMailer->IsSendmail();                     // telling the class to use SendMail transport
		$objPhpMailer->IsMail();                           // telling the class to use PHP mail() function for sending mails
		/*
		$objPhpMailer->IsSMTP();                           // telling the class to use SMTP
		$objPhpMailer->SMTPAuth   = true;                  // enable SMTP authentication
		$objPhpMailer->SMTPSecure = SMTP_SECURE;           // sets the prefix to the servier
		$objPhpMailer->Host       = SMTP_HOST;             // sets GMAIL as the SMTP server
		$objPhpMailer->Port       = SMTP_PORT;             // set the SMTP port for the GMAIL server
		$objPhpMailer->Username   = SMTP_USER;             // GMAIL username
		$objPhpMailer->Password   = SMTP_PASS;             // GMAIL password	
		*/
		
		$objPhpMailer->SetFrom($this->mailReceivers['from']['email'], $this->mailReceivers['from']['name']);
	    $objPhpMailer->AddReplyTo($this->mailReceivers['from']['email'], $this->mailReceivers['from']['name']);
		$objPhpMailer->AddAddress($this->mailReceivers['to']['email'], $this->mailReceivers['to']['name']);
		
		if(!empty($this->mailReceivers['cc']))
		{
			foreach($this->mailReceivers['cc'] as $email)
			{
				$objPhpMailer->AddCC($email);
			}
		}
		
		/*echo '<pre>';
		print_r($this->mailReceivers);
	    print_r($mailBody);
		echo '</pre>';*/
		$objPhpMailer->Subject = $mailSubject;
	    $objPhpMailer->MsgHTML($mailBody);		
	
		if(!$objPhpMailer->Send()) 
		{
			//echo "Mailer Error: " . $objPhpMailer->ErrorInfo;die;
		} 
		else 
		{
			//echo "Message sent!";
		}
		$objPhpMailer->ClearAllRecipients();
	}
	


	public function getMailTemplateForTourRequester($details, $tourReservationDetails, $action)
	{
		ob_start();
	?>
		<tr>
			<td vAlign=top align=Justify>
			<table width="96%" align="center" cellpadding="0" cellspacing="0">
				<tr>
				  <td>
			
			<font face=arial size=2>
				<strong>Dear <?php echo $details['first_name']." ".$details['last_name']; ?>,</strong><br /><br />
				<?php
				$tourStartDate_Obj = new DateTime($details['tour_start_date']);
				$tourEndDate_Obj = new DateTime($details['tour_end_date']);
				
				$tourStartDate_Str = $tourStartDate_Obj->format('d-m-Y');
				$tourEndDate_Str = $tourEndDate_Obj->format('d-m-Y');
				
				if($action == 'added')
				{
					echo "You have successfully raised a tour request. You will receive an email once it is approved by your HOD.";
				}
				elseif($action == 'updated')
				{
					echo "You have successfully updated your tour request.";
				}
				elseif($action == 'deleted')
				{
					echo "You have successfully deleted your tour request.";
				}
				elseif($action == 'approved')
				{
					$approveTxt = ($_SESSION['userType'] == 'finance') ? 'finance department' : 'your HOD';
					echo "Congratulations !! Your tour request for the date ".$tourStartDate_Str. " to ". $tourEndDate_Str. " has been approved by ".$approveTxt.". Please contact him/her personally for more information.";
				}
				elseif($action == 'rejected')
				{
					$approveTxt = ($_SESSION['userType'] == 'finance') ? 'finance department' : 'your HOD';
					echo "Unfortunately, Your tour request for the date ".$tourStartDate_Str. " to ". $tourEndDate_Str. " has been rejected by ".$approveTxt.". Please contact him/her personally for more information.";
				}
				?>
				</font>
				     </td>
				    </tr>
				 </table>
				</td> 
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			  <td>
			     <table width="96%" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td><strong>Tour Details are as follows - </strong></td>
					</tr>
					<tr>
						<td>Dates :: <?php echo 'From - '.$tourStartDate_Str; ?> <?php echo ', To - '.$tourEndDate_Str; ?> <br><br></td>
					</tr>
					<tr>
						<td><strong>Tour Reservation Details - </strong></td>
					</tr>
					<tr>
					  <td>
					    <table width="100%" class="border-table" align="center" cellpadding="0" cellspacing="0">
				<?php
				if(!empty($tourReservationDetails))
				{
				    foreach($tourReservationDetails as $id => $tourDetails)
				    {
				    ?>
				    <tr>							
				    <?php
				    	$reservationStartDateObj = new DateTime($tourDetails['reservation_start_date']);
						$reservationReturnDateObj = new DateTime($tourDetails['reservation_return_date']);
				    ?> 
				    <td>
								<?php echo $reservationStartDateObj->format("d-m-Y"); ?>																													
							</td>
							<td>	
								<?php echo $reservationReturnDateObj->format("d-m-Y"); ?>								
							</td>
							<td>
								<?php echo $tourDetails['reservation_from']; ?>								
							</td>
							<td>
								<?php echo $tourDetails['reservation_to']; ?>									 
							</td>							
							<td>
								<?php echo $tourDetails['reservation_mode']; ?>								 
							</td>
							<td>
								<?php echo $tourDetails['reservation_details']; ?>
							</td>
						</tr>	
					<?php	
					}
				}
				else
				{
				?>

					<tr>
						<td style="text-align:center;">No reservation details found.</td>
					</tr>
				<?php
				}	
				?>
				    </table>
				    </td>
				  </tr>
				  <tr>
					<td><strong>&nbsp;</strong></td>
				  </tr>
				  <?php
					if($action == 'approved' || $action == 'rejected')
					{
						if($details['HOD1_comment'] != "")
						{
					?>
							<tr>
								<td><strong>HOD Comments - </strong></td>
							</tr>
							<tr>
								<td><?php echo $details['HOD1_comment']; ?> <br><br></td>
							</tr>
					<?php	
						}
						if($details['finance_comment'] != "")
						{
					?>
							<tr>
								<td><strong>Finance Dept. Comments - </strong></td>
							</tr>
							<tr>
								<td><?php echo $details['finance_comment']; ?> <br><br></td>
							</tr>
					<?php	
						}	
					}					
					?>
				</table>
				</td>
				</tr>
	<?php	
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;
	}
	
	public function getMailTemplateForTourAuthorizer($details, $tourReservationDetails, $rowHod, $action)
	{
		ob_start();
	?>		
	<tr>
		<td vAlign=top align=Justify>
		   <table width="96%" align="center" cellpadding="0" cellspacing="0">
				<tr>
				  <td>
			<font face=arial size=2>
				Dear <?php echo $rowHod['HOD_1']['fullName']; ?><br /><br />
				<?php
				$tourStartDate_Obj = new DateTime($details['tour_start_date']);
				$tourEndDate_Obj = new DateTime($details['tour_end_date']);
				
				$tourStartDate_Str = $tourStartDate_Obj->format('d-m-Y');
				$tourEndDate_Str = $tourEndDate_Obj->format('d-m-Y');
				
				if($action == 'added')
				{
					echo "You have received a tour request from ".$details['first_name']. " ".$details['last_name'].". Applicant is anticipating hearing from you at the earliest.<br/><br/>Once you have reviewed the request, you can approve/reject the same through your dashboard and the applicant will receive an email after that.<br/>";
				}
				elseif($action == 'updated')
				{
					echo $details['first_name']." ".$details['last_name']. " has updated his/her tour request.";
				}
				elseif($action == 'deleted')
				{
					echo $details['first_name']." ".$details['last_name']. " has deleted his/her tour request.";
				}
				elseif($action == 'approved')
				{
					echo "You have approved the tour request of ".$details['first_name']." ".$details['last_name']; 
				}
				elseif($action == 'rejected')
				{
					echo "You have rejected the tour request of ".$details['first_name']." ".$details['last_name']; 
				}
				?>					 
			</font>
		       </td> 
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="3"><strong>Tour Details are as follows - </strong></td>
			</tr>
			<tr>
				<td><strong>Dates :: <?php echo 'From - '.$tourStartDate_Str; ?> <?php echo ', To - '.$tourEndDate_Str; ?> </strong></td>
			</tr>
			<tr>
				<td><strong>Tour Reservation Details - </strong></td>
			</tr>
			<tr>
			 <td>
			   <table width="100%" cellpadding="0" cellspacing="0" class="border-table">
	<?php
	if(!empty($tourReservationDetails))
	{
		foreach($tourReservationDetails as $id => $tourDetails)
		{
		?>
			<tr>							
		   <?php
					$reservationStartDateObj = new DateTime($tourDetails['reservation_start_date']);
					$reservationReturnDateObj = new DateTime($tourDetails['reservation_return_date']);
			?> 
				<td>
					<?php echo $reservationStartDateObj->format("d-m-Y"); ?>																													
				</td>
				<td>	
					<?php echo $reservationReturnDateObj->format("d-m-Y"); ?>								
				</td>
				<td>
					<?php echo $tourDetails['reservation_from']; ?>								
				</td>
				<td>
					<?php echo $tourDetails['reservation_to']; ?>									 
				</td>							
				<td>
					<?php echo $tourDetails['reservation_mode']; ?>								 
				</td>
				<td>
					<?php echo $tourDetails['reservation_details']; ?>
				</td>
			</tr>	
		<?php	
		}
	}
	else
	{
	?>
		<tr>
			<td colspan="6" style="text-align:center;">No reservation details found.</td>
		</tr>
	<?php
	}	
	?>	

          </table>
       </td>
     </tr>
	 <tr>
		<td><strong>&nbsp;</strong></td>
	  </tr>
	  <?php
		if($action == 'approved' || $action == 'rejected')
		{
			if($details['HOD1_comment'] != "")
			{
		?>
				<tr>
					<td><strong>HOD Comments - </strong></td>
				</tr>
				<tr>
					<td><?php echo $details['HOD1_comment']; ?> <br><br></td>
				</tr>
		<?php	
			}
			if($details['finance_comment'] != "")
			{
		?>
				<tr>
					<td><strong>Finance Dept. Comments - </strong></td>
				</tr>
				<tr>
					<td><?php echo $details['finance_comment']; ?> <br><br></td>
				</tr>
		<?php	
			}	
		}					
		?>
     </table>
     </td>
     </tr>

	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;		
	}
	
	public function getMailTemplateForDailyActivityRequester($details, $action, $dailyActivity)
	{
		ob_start();
	?>
		<tr>
			<td vAlign=top align=Justify>
			<table width="96%" cellpadding="0" cellspacing="0" align="center">
				<tr>
				 <td>
			<font face=arial size=2>
				Dear <?php echo $details['first_name']." ".$details['last_name']; ?><br /><br />
				

				<?php
				if($action == 'added')
				{
					echo "You have successfully created some daily activity(s). You will receive an automated email once these have been approved by your HOD. Below are the details of activities.";
			   ?>
			   
			  </font>
			    </td>
			   </tr>
			   <tr>
			     <td>
			       <table width="95%" cellspacing="0" cellpadding="0" align="center" class="border-table">
					 <tr>
					   <th>Activity</th>
						<th>Start Date Time</th>
						<th>End Date Time</th>
					  </tr>
				<?php
					foreach($dailyActivity as $activityDetail)
					{
						?>
						<tr>
								<td><?php echo $activityDetail['activity']; ?></td>
								<td>
								<?php
									$startDateTimeObj = new DateTime($activityDetail['startDateTime']);
									echo $startDateTimeObj->format("d-m-Y H:i");
								?>
								</td>
								<td>
								<?php 
									$endDateTimeObj = new DateTime($activityDetail['endDateTime']);
									echo $endDateTimeObj->format("d-m-Y H:i");
								?>
								</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
				}
				elseif($action == 'updated')
				{
					echo "You have successfully updated some daily activity(s). Below are the details of them.";
					?>
					</font>
					 </tr>
					   <tr>
					    <td>
					     <table width="95%" cellspacing="0" cellpadding="0" align="center">
					      <tr>
							<th>Activity</th>
							<th>Start Date Time</th>
							<th>End Date Time</th>
						  </tr>
					<?php
					foreach($dailyActivity as $activityDetail)
					{
						?>
						<tr>
								<td><?php echo $activityDetail['activity']; ?></td>
								<td>
								<?php
									$startDateTimeObj = new DateTime($activityDetail['startDateTime']);
									echo $startDateTimeObj->format("d-m-Y H:i");
								?>
								</td>
								<td>
								<?php 
									$endDateTimeObj = new DateTime($activityDetail['endDateTime']);
									echo $endDateTimeObj->format("d-m-Y H:i");
								?>
								</td>
							</tr>
							<?php
					}
					?>
					</table>
					<?php	
				}
				elseif($action == 'deleted')
				{
					echo "You have successfully deleted your daily activities for the date ".$dailyActivity;
					echo '</font></td></tr></table>';
				}
				elseif($action == 'approved')
				{
					echo "Congratulations !! Some of your daily activities have been approved by your HOD. Please check your account for more information.";
					echo '</font></td></tr></table>';
				}
				elseif($action == 'rejected')
				{
					echo "Unfortunately, Some of your daily activities have been rejected by your HOD. Please check your account for more information.";
					echo '</font></td></tr></table>';
				}
				?>				
			
			</td> 
		</tr>	
		    </table>
		    </td>
		    </tr>						   
	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;
	}
	
	public function getMailTemplateForDailyActivityAuthorizer($details, $rowHod, $action, $dailyActivity)
	{
		ob_start();
	?>		
	<tr>
		<td vAlign=top align=Justify>
		   <table width="96%" cellspacing="0" cellpadding="0" align="center">
		   	  <tr>
		   	  	 <td>
			<font face=arial size=2>
				Dear <?php echo $rowHod['HOD_1']['fullName']; ?><br /><br />
				<?php
				if($action == 'added')
				{
					echo "Some daily activities have been added by ".$details['first_name']. " ".$details['last_name'].". You can approve/reject the same through your dashboard and the applicant will receive an automated email after that.<br/>Below are the details of activities.</font>";
					?>
					<table width="100%" cellpadding="0" cellspacing="0" class="border-table">
					<tr>
							<th>Activity</th>
							<th>Start Date Time</th>
							<th>End Date Time</th>
						  </tr>
					<?php
					foreach($dailyActivity as $activityDetail)
					{
						?>
						<tr>
								<td><?php echo $activityDetail['activity']; ?></td>
								<td>
								<?php
									$startDateTimeObj = new DateTime($activityDetail['startDateTime']);
									echo $startDateTimeObj->format("d-m-Y H:i");
								?>
								</td>
								<td>
								<?php 
								$endDateTimeObj = new DateTime($activityDetail['endDateTime']);
								echo $endDateTimeObj->format("d-m-Y H:i");
								?>
								</td>								
							</tr>
					  <?php
					}
					?>
					</table>
					<?php
				}
				elseif($action == 'updated')
				{
					echo $details['first_name']." ".$details['last_name']. " has updated his/her daily activity(s).</font>";
				}
				elseif($action == 'deleted')
				{
					echo $details['first_name']." ".$details['last_name']. " has deleted his/her daily activity(s).</font>";
				}
				elseif($action == 'approved')
				{
					echo "You have approved some daily activities of ".$details['first_name']." ".$details['last_name']."</font>"; 
				}
				elseif($action == 'rejected')
				{
					echo "You have rejected some daily activities of ".$details['first_name']." ".$details['last_name']."</font>"; 
				}
				?>					 
			
			</td>
			</tr></table>
		</td> 
	</tr>							   
	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;		
	}
	
	public function getMailTemplateForLocalConveyanceRequester($details, $action)
	{
		ob_start();
	?>
		<tr>
			<td vAlign=top align=Justify>
			<table width="96%" cellpadding="0" cellspacing="0" align="center">
				<tr>
				  <td>
			<font face=arial size=2>
				Dear <?php echo $details['first_name']." ".$details['last_name']; ?><br /><br />
				<?php
				$dateObj = new DateTime($details['date']);
				$dateStr = $dateObj->format('d-m-Y');
				
				if($action == 'added')
				{
					echo "You have successfully raised a Local Conveyance request. You will receive an email once it is approved by your HOD. Details of the request are below.";
				}
				elseif($action == 'updated')
				{
					echo "You have successfully updated your Local Conveyance request for the date ".$dateStr.". Details of the request are below.";
				}
				elseif($action == 'deleted')
				{
					echo "You have successfully deleted your Local Conveyance request for the date ".$dateStr.". Details of the request are below.";
				}
				elseif($action == 'approved')
				{
					$approveTxt = ($_SESSION['userType'] == 'finance') ? 'finance department' : 'your HOD';
					echo "Congratulations !! Your Local Conveyance request for the date ".$dateStr." has been approved by ".$approveTxt.". Details of the request are below.";
				}
				elseif($action == 'rejected')
				{
					$approveTxt = ($_SESSION['userType'] == 'finance') ? 'finance department' : 'your HOD';
					echo "Unfortunately, Your Local Conveyance request for the date ".$dateStr." has been rejected by ".$approveTxt.". Please contact them personally for more information. Details of the request are below.";
				}
				?>
				</font>
				</td>
				</tr>

				<tr><td>
				<?php
				echo "<table width='95%' cellspacing='0' cellpadding='0' align='center' class='border-table'>";
				echo "<tr>
						<th>Date</th>
						<th>From</th>
						<th>To</th>
						<th>Purpose</th>
						<th>Required Amount</th>
					  </tr>					
					  <tr>
						<td>".$dateStr."</td>
						<td>".$details['from']."</td>
						<td>".$details['to']."</td>
						<td>".$details['purpose']."</td>
						<td>".sprintf("%.2f",$details['amount'])."</td>
					  </tr>
				</table>";
				?>				
			  </td>
			  </tr>
			  </table>
			</td> 
		</tr>
	<?php	
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;	
	}
	
	public function getMailTemplateForLocalConveyanceAuthorizer($details, $rowHod, $action)
	{
		ob_start();
	?>		
	<tr>
		<td vAlign=top align=Justify>
		   <table width="96%" cellpadding="0" cellspacing="0" align="center">
		   	  <tr><td>
			<font face=arial size=2>
				Dear <?php echo $rowHod['HOD_1']['fullName']; ?><br /><br />
				<?php
				$dateObj = new DateTime($details['date']);
				$dateStr = $dateObj->format('d-m-Y');
				
				if($action == 'added')
				{
					echo "You have received a Local Conveyance request from ".$details['first_name']." ".$details['last_name'].". <br/><br/>Once you have reviewed the request, you can approve/reject the same through your dashboard and the applicant will receive an email after that. Below are the details of the request.<br/>";
				}
				elseif($action == 'updated')
				{
					echo $details['first_name']." ".$details['last_name']. " has updated his/her Local Conveyance request. Below are the details of the request.";
				}
				elseif($action == 'deleted')
				{
					echo $details['first_name']." ".$details['last_name']. " has deleted his/her Local Conveyance request. Below are the details of the request.";
				}
				elseif($action == 'approved')
				{
					echo "You have approved the Local Conveyance request of ".$details['first_name']." ".$details['last_name'].". Below are the details of the request."; 
				}
				elseif($action == 'rejected')
				{
					echo "You have rejected the Local Conveyance request of ".$details['first_name']." ".$details['last_name'].". Below are the details of the request."; 
				}
				echo "</font>";

				echo "</td></tr><tr><td><table cellspacing='0' cellpadding='0' class='border-table'>";
				echo "<tr>
						<th>Employee Name</th>
						<th>Date</th>
						<th>From</th>
						<th>To</th>
						<th>Purpose</th>
						<th>Required Amount</th>
					  </tr>					
					  <tr>
						<td>".$details['first_name']." ".$details['last_name']."</td>
						<td>".$dateStr."</td>
						<td>".$details['from']."</td>
						<td>".$details['to']."</td>
						<td>".$details['purpose']."</td>
						<td>".sprintf("%.2f",$details['amount'])."</td>
					  </tr>
				</table>";
				?>					 
			
			</td>
			</tr>
			</table>
		</td> 
	</tr>							   
	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;		
	}
	
	public function getMailTemplateForDailyAllowanceRequester($details, $tourDetails, $action)
	{
		ob_start();
	?>
		<tr>
			<td vAlign=top align=Justify>
			 <table width="96%" cellspacing="0" cellpadding="0" align="center">
			 	 <tr><td>
			<font face=arial size=2>			
				Dear <?php echo $tourDetails['first_name']." ".$tourDetails['last_name']; ?><br /><br />
				<?php
				if($action == 'added')
				{
					echo "You have successfully raised a Daily Allowance request. You will receive an email once it is approved by your HOD.";
				}
				elseif($action == 'updated')
				{
					echo "You have successfully updated your Daily Allowance request for the tour ID ".$tourDetails['tour_id'];
				}
				elseif($action == 'deleted')
				{
					echo "You have successfully deleted your Daily Allowance request for the tour ID ".$tourDetails['tour_id'];
				}
				elseif($action == 'approved')
				{
					$approveTxt = ($_SESSION['userType'] == 'finance') ? 'finance department' : 'your HOD';
					echo "Congratulations !! Your Daily Allowance request for the tour ID ".$tourDetails['tour_id']." has been approved by ".$approveTxt.".";
				}
				elseif($action == 'rejected')
				{
					$approveTxt = ($_SESSION['userType'] == 'finance') ? 'finance department' : 'your HOD';
					echo "Unfortunately, Your Daily Allowance request for the tour ID ".$tourDetails['tour_id']." has been rejected by ".$approveTxt.". Please contact them personally for more information.";
				}
				?>				
			</font>
			</td>
			</tr>
			</table>
			</td> 
		</tr>
		<tr>
		  <td>
		    <table width="96%" align="center" cellpadding="0" cellspacing="0">
		       <tr>
			<td>Total DA Amount :</td>
			<td><?php echo sprintf("%.2f", $details['total_da']); ?></td>
		</tr>
		</table>
		</td>
		</tr>
	<?php	
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;	
	}
	
	public function getMailTemplateForDailyAllowanceAuthorizer($details, $tourDetails, $rowHod, $action)
	{
		ob_start();
	?>		
	<tr>
		<td vAlign=top align=Justify>
		   <table width="96%" cellspacing="0" cellpadding="0" align="center">
		   	  <tr><td>
			<font face=arial size=2>
				Dear <?php echo $rowHod['HOD_1']['fullName']; ?><br /><br />
				<?php
				if($action == 'added')
				{
					echo "You have received a Daily Allowance request from ".$tourDetails['first_name']." ".$tourDetails['last_name'].". <br/><br/>Once you have reviewed the request, you can approve/reject the same through your dashboard and the applicant will receive an email after that.<br/>";
				}
				elseif($action == 'updated')
				{
					echo $tourDetails['first_name']." ".$tourDetails['last_name']. " has updated his/her Daily Allowance request.";
				}
				elseif($action == 'deleted')
				{
					echo $tourDetails['first_name']." ".$tourDetails['last_name']. " has deleted his/her Daily Allowance request.";
				}
				elseif($action == 'approved')
				{
					echo "You have approved the Daily Allowance request of ".$tourDetails['first_name']." ".$tourDetails['last_name']; 
				}
				elseif($action == 'rejected')
				{
					echo "You have rejected the Daily Allowance request of ".$tourDetails['first_name']." ".$tourDetails['last_name']; 
				}
				?>					 
			</font>
			</td>
			</tr>
			</table>
		</td> 
	</tr>
		<tr>
		<td>
		  <table width="96%" cellpadding="0" cellspacing="0" align="center">
		  	  <tr>
		  
			<td>Total DA Amount :</td>
			<td><?php echo sprintf("%.2f", $details['total_da']); ?></td>
		</tr>
		</table>
		</td>
		</tr>

	<?php
		$mailTemplate = $this->getMailHeader();
		$mailTemplate .= ob_get_contents();
		$mailTemplate .= $this->getMailFooter();
		ob_end_clean();
		return $mailTemplate;		
	}
} 
