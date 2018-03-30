<?php 
require_once("config.php");

require_once("".CLASS_PATH."/class.Mail.php");
$objMail = new Mail;

require_once("./phpmailer/class.phpmailer.php");
$objPhpMailer = new PHPMailer();

require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.Leaves.inc.php");
$objLeaves = new Leaves;

$action = $_REQUEST['action'];

switch($action)
{
	case "showLeaveComments":
	if(isset($_REQUEST['applicationId']) && $_REQUEST['applicationId'] > 0)
	{	
		if($_REQUEST['userType'] == 'authorizer' && !$objEmployee->isValidAuthorizer($_REQUEST['applicationId'], $_SESSION['userId']))
		{
			echo 'You are not authorized to view this section.';
		}
		else
		{
			$rsDetail = $objLeaves->getLeaveApplications($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['applicationId']);
			if($GLOBALS['obj_db']->num_rows($rsDetail) > 0)
			{
				$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
			}	
		?>
			<form name="frmUpateLeaveStatus" id="frmUpateLeaveStatus" target="_top" action="index.php?Option=Leaves&SubOption=leaveRequests" method="post">
				<input type="hidden" name="leave_id" id="leave_id" value="<?php echo $rowDetail['id']; ?>" />
				<input type="hidden" name="user_type" id="user_type" value="<?php echo $_REQUEST['userType']; ?>" />
				<input type="hidden" name="status" id="status" value="<?php echo $_REQUEST['status']; ?>" />
				<input type="hidden" name="ofaction" id="ofaction" value="statusUpdate" />
			<?php
			//if($rowDetail['approve_status_HOD1'] == 'pending')
			//{
				if($_REQUEST['userType'] == 'authorizer')
				{
			?>
					Comments&nbsp;&nbsp;&nbsp;<textarea name="comments" id="comments" rows="4" cols="30"></textarea>
					<br/>
					<br/>
					<input type="submit" class="btn btn-success" value="Submit" onclick="javascript: parent.$.fancybox.close();">					
			<?php
				}
			//}
			?>
			</form>
		<?php
		}
	}	
	break;
	
	case "updateHolidayReason":
	    $response = array();
		$sqlUpdate = "UPDATE holiday_calendar_".$_POST['region']." SET reason_of_holiday = '".trim(strip_tags($_POST['reasonOfHoliday']))."' WHERE id = '".$_POST['id']."' ";
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		
		if($rsUpdate)
		{
			$response['successFlag'] = 1;
			$response['message'] = "Reason Updated";
		}
		else
		{
			$response['successFlag'] = 0;
			$response['message'] = "Error Occured. Try again.";
		}
		echo json_encode($response);
	break;
	
	case "updateLeaveStatus":
		$response = array();
		if(isset($_POST['applicationId']) && $_POST['applicationId'] > 0)
		{
			if(!$objEmployee->isValidAuthorizer($_REQUEST['applicationId'], $_SESSION['userId']))
			{
				$response['successFlag'] = 0;
				$response['message'] = "You are not authorized to take any action for this request.";
			}
			else
			{
				$rsLeaveDetail = $objLeaves->getLeaveApplications($_SESSION['userId'], 'authorizer', $_POST['applicationId']);
				if($GLOBALS['obj_db']->num_rows($rsLeaveDetail) > 0)
				{
					$rowLeaveDetail = $GLOBALS['obj_db']->fetch_array($rsLeaveDetail);					
				}
				
				if(trim($_POST['status']) == 'approved')
				{
				    $rsLeaveBal = $objLeaves->getLeaveBalance($rowLeaveDetail['emp_id'], $rowLeaveDetail['leave_type_id']);
					$rowLeaveBal = $GLOBALS['obj_db']->fetch_array($rsLeaveBal);
					$remainingLeaves = $rowLeaveBal['remaining'];

					if($remainingLeaves < $rowLeaveDetail['leaves_required'])
					{
						$response['successFlag'] = 0;
						$response['message'] = "No enough leaves in employee's account to get it approved.";
					}	
					else
					{
						$sqlUpdate = "UPDATE leave_applications SET approve_status_HOD1 = '".trim($_POST['status'])."' WHERE id = '".$_POST['applicationId']."' ";
						$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
								
						$leaveCountToBeUpdated = ($rowLeaveDetail['half_day_leave'] == 'YES') ? 0.5 : $rowLeaveDetail['leaves_required'];
						
					    $sqlUpdateLeaveBal = "UPDATE leave_balance SET leaves_availed = leaves_availed+".$leaveCountToBeUpdated." WHERE emp_id = '".$rowLeaveDetail['emp_id']."' AND leave_type_id = '".$rowLeaveDetail['leave_type_id']."' AND year = YEAR('".$rowLeaveDetail['leave_to_date']."') ";
						$rsUpdateLeaveBal = $GLOBALS['obj_db']->execute_query($sqlUpdateLeaveBal);
						
						if($rsUpdate && $rsUpdateLeaveBal)
						{
							$response['successFlag'] = 1;
							$response['message'] = "You have ".trim($_POST['status'])." the leave application.";
							
							$objMail->processLeaveRequestMail($_POST['applicationId'], trim($_POST['status']));
						}
						else
						{
							$response['successFlag'] = 0;
							$response['message'] = "Error occured. Try again.";
						}
					}
				}	
				elseif(trim($_POST['status']) == 'rejected')
				{
				    $sqlUpdate = "UPDATE leave_applications SET approve_status_HOD1 = '".trim($_POST['status'])."' WHERE id = '".$_POST['applicationId']."' ";
					$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
					
					if($rsUpdate)
					{
						$response['successFlag'] = 1;
						$response['message'] = "You have ".trim($_POST['status'])." the leave application.";
						
						$objMail->processLeaveRequestMail($_POST['applicationId'], trim($_POST['status']));
					}
					else
					{
						$response['successFlag'] = 0;
						$response['message'] = "Error occured. Try again.";
					}
				}							
			}	
		}
		else
		{
			$response['successFlag'] = 0;
			$response['message'] = "Invalid Request. Try again.";
		}	
	    echo json_encode($response);
	break;	
}
?>