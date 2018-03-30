<style type="text/css">
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {    
	border: 1px solid #000;
    text-align: left;
    padding: 8px;
	font-size: 14px;
	vertical-align: top;
}

tr:nth-child(even) {
    background-image: linear-gradient(to bottom, #f5f5f5 0%, #e2e2e2 100%);
	background-repeat: repeat-x;
}
</style>
<?php
require_once("config.php");

if(!isset($_SESSION['userId']) || $_SESSION['userId'] == '')
{
	$_SESSION['msg'] = "Your session has expired. Please login again to continue.";
	header("Location: login.php");
	exit();
}

require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.Holiday.inc.php");
$objHoliday = new Holiday;

require_once("".CLASS_PATH."/class.Leaves.inc.php");
$objLeaves = new Leaves;

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
		?>
		<style type="text/css">
			.popupheading{ background: #e2e3de; text-transform: uppercase; font-size: 18px; padding: 12px; }
			td,th{border: none; background: #fff; font-size: 12px; color: #555; border-bottom: 1px solid #ddd; padding: 12px; }
			td:last-child{ color: #939393 }
			.btn2{ float: right; background: #000; color: #fff; outline: none; padding: 8px 15px; border: none; cursor: pointer; text-transform: uppercase; }
		</style>
			<div class="popupheading">Leave Details - <?php echo $rowDetail['first_name']." ".$rowDetail['last_name']; ?></div>
			<table class="table table-bordered table-hover table-striped">
				<tr>
                    <th></th>					
					<th><button onclick="window.print();" class="btn2">Print</button></th>
				  </tr>
			     <tr>
                    <th>Leave Request ID</th>
					<th><?php echo $rowDetail['id']; ?></th>					
				  </tr>
				 <tr>					
                    <td width="200">Leave Requester:</td>
					<td><?php echo $rowDetail['first_name']." ".$rowDetail['last_name']; ?></td>					
				  </tr>
				  <tr>					
                    <td>Designation:</td>
					<td>
					<?php 
					$rsEmpDetail = $objEmployee->getEmployeeData($rowDetail['emp_id']);
					$rowEmpDetail = $GLOBALS['obj_db']->fetch_array($rsEmpDetail);
					echo $rowEmpDetail['designation']; 
					?>
					</td>					
				  </tr>
				  <tr>					
                    <td>Leave Type:</td>
					<td><?php echo $rowDetail['leave_abbr']; ?></td>					
				  </tr>
				  <tr>					
                    <td>Leave Dates:</td>
					<td>
					<?php
					$leaveFromDateObj = new DateTime($rowDetail['leave_from_date']);
					$leaveToDateObj   = new DateTime($rowDetail['leave_to_date']);
					
					echo $leaveFromDateObj->format('d-M-Y').' to '.$leaveToDateObj->format('d-M-Y');
					?>
					</td>
				  </tr>
				  <tr>					
                    <td>Leave required (in days):</td>
					<td><?php echo ($rowDetail['half_day_leave'] == 'YES') ? 'Half Day' : $rowDetail['leaves_required']; ?></td>
				  </tr>
				  <tr>					
                    <td>Holidays inBetween:</td>
					<td>
					<?php 
					$holidaysBetweenLeaves = $objLeaves->getHolidaysBetweenLeaves($rowDetail['leave_from_date'], $rowDetail['leave_to_date'], strtolower($rowEmpDetail['region']));
					if(!empty($holidaysBetweenLeaves))
					{
						foreach($holidaysBetweenLeaves as $holidayDate)
						{
							$holidayDateObj = new DateTime($holidayDate);
							echo $holidayDateObj->format('d-M-Y').'<br/>';
						}
					}
					else
					{
						echo 'No holidays between leaves.';
					}	
					?>
					</td>
				  </tr>
				  <tr>					
                    <td>Reason of Leave:</td>
					<td><?php echo $rowDetail['reason_of_leave']; ?></td>
				  </tr>
				  <tr>					
                    <td>Leave Account:</td>
					<td>
					<?php
					$rsLeaveBal = $objLeaves->getLeaveBalance($rowDetail['emp_id']);
					if($GLOBALS['obj_db']->num_rows($rsLeaveBal) > 0)
					{
						while($rowBal = $GLOBALS['obj_db']->fetch_array($rsLeaveBal))
						{
							echo $rowBal['leave_abbr']." - ".round($rowBal['remaining'], 2)."<br />";
						}	
					}	
					?>
					</td>
				  </tr>
				  <tr>
                    <td>Job Assigned to:</td>
					<td><?php echo $rowDetail['assignee_first_name']." ".$rowDetail['assignee_last_name']; ?></td>
				  </tr>
				  <tr>					
                    <td>Leave Status:</td>
					<td>
					<?php 
					$rsHod = $objEmployee->getEmployeeData($rowDetail['HOD_1_id']);
					$rowHod = $GLOBALS['obj_db']->fetch_array($rsHod);
					
					if($rowDetail['approved_by_HOD1_id'] > 0)
					{
						$rsApproveHod = $objEmployee->getEmployeeData($rowDetail['approved_by_HOD1_id']);
						$rowApproveHod = $GLOBALS['obj_db']->fetch_array($rsApproveHod);
						$approveHodName = $rowApproveHod['first_name']." ".$rowApproveHod['last_name'];
					}
					else
					{
						$approveHodName = $rowHod['first_name']." ".$rowHod['last_name'];
					}	
					
					$hodText = ($rowDetail['approve_status_HOD1'] == 'pending') ? ' at ' : ' by '; 
					$hodText .= $approveHodName;
			
					echo ucfirst($rowDetail['approve_status_HOD1']).$hodText; ?></td>
				  </tr>
				  <tr>
                    <td>Comments:</td>
					<td><?php echo ($rowDetail['HOD1_comment'] != "") ? $rowDetail['HOD1_comment'] : "No Comments Given"; ?></td>
				  </tr>
			</table>					
		<?php
		}
	}	
}