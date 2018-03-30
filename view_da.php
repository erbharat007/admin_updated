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

require_once("".CLASS_PATH."/class.Tour.inc.php");
$objTour = new Tour;

require_once("".CLASS_PATH."/class.DailyAllowance.inc.php");
$objDailyAllowance = new DailyAllowance;

require_once("".CLASS_PATH."/class.AdminUser.inc.php");
$objAdminUsers = new AdminUser;

require_once("".CLASS_PATH."/class.Mail.php");
$objMail = new Mail;

if(isset($_REQUEST['tourId']) && $_REQUEST['tourId'] > 0)
{
	$rsDetail = $objTour->getTourRequests($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['tourId']);
		
	if($GLOBALS['obj_db']->num_rows($rsDetail) > 0)
	{
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
		$rsDailyAllowance = $objDailyAllowance->getCalculatedDa($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['tourId']);
		if($GLOBALS['obj_db']->num_rows($rsDailyAllowance) > 0)
		{
			$rowDailyAllowance = $GLOBALS['obj_db']->fetch_array($rsDailyAllowance);
			$ofAction                 = 'update';
			$tour_start_date          = $rowDailyAllowance['tour_start_date'];
			$tour_end_date            = $rowDailyAllowance['tour_end_date'];
			$total_travel_time_hrs    = $rowDailyAllowance['total_travel_time_hrs'];
			$total_travel_time_days   = $rowDailyAllowance['total_travel_time_days'];
			$total_tour_time_hrs      = $rowDailyAllowance['total_tour_time_hrs'];
			$total_tour_time_days     = $rowDailyAllowance['total_tour_time_days'];
			$total_balance_hours      = $rowDailyAllowance['total_balance_hours'];
			$total_balance_days       = $rowDailyAllowance['total_balance_days'];
			$half_da_for_travel       = $rowDailyAllowance['half_da_for_travel'];
			$full_da_for_balance_days = $rowDailyAllowance['full_da_for_balance_days'];
			$total_da                 = $rowDailyAllowance['total_da'];
			
			$tour_start_dateObj = new DateTime($tour_start_date);
			$tour_end_dateObj = new DateTime($tour_end_date);
			$tour_start_date = $tour_start_dateObj->format('d-m-Y');
			$tour_end_date = $tour_end_dateObj->format('d-m-Y');
		}
		else
		{
			$ofAction                 = 'add';
			$tour_start_date          = '';
			$tour_end_date            = '';
			$total_travel_time_hrs    = '';
			$total_travel_time_days   = '';
			$total_tour_time_hrs      = '';
			$total_tour_time_days     = '';
			$total_balance_hours      = '';
			$total_balance_days       = '';
			$half_da_for_travel       = '';
			$full_da_for_balance_days = '';
			$total_da                 = '';
		}
	?>
		<div style="text-align: center; font-weight: bold; margin-bottom: 20px;">Daily Allowance Details - <?php echo $rowDetail['first_name']." ".$rowDetail['last_name']; ?></div>
		<table class="table table-bordered table-hover table-striped" id="da-details-section">
                <thead>
				  <tr>
                    <th>DA Request ID</th>
					<th colspan="4"><?php echo $rowDailyAllowance['id']; ?></th>
					<th><button onclick="window.print();">Print</button></th>
				  </tr>
				  <tr>					
                    <th width="18%">From Date Time</th>
					<th width="18%">To Date Time</th>
					<th width="15%">From</th>
                    <th width="15%">To</th>
					<th colspan="2">Total Hours</th>					
				  </tr>
                </thead>
				<tbody class="more-field-wrapper">
				<?php
					$daReservationDetails = $objDailyAllowance->getDaResDetails($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['tourId']);
					if(!empty($daReservationDetails))
					{
						foreach($daReservationDetails as $id => $daDetails)
						{
					?>
						<tr>
						<?php		
							$daStartDateTimeObj = new DateTime($daDetails['start_date_time']);
							$daEndDateTimeObj = new DateTime($daDetails['end_date_time']);
						?>
							<td>
								<?php echo ($daDetails['start_date_time'] != '' && $daDetails['start_date_time'] != '0000-00-00') ? $daStartDateTimeObj->format('d/m/Y H:i') : ''; ?>															
							</td>
							<td>							
								<?php echo ($daDetails['end_date_time'] != '' && $daDetails['end_date_time'] != '0000-00-00') ? $daEndDateTimeObj->format('d/m/Y H:i') : ''; ?>
							</td>
							<td>
								 <?php echo $daDetails['reservation_from']; ?>
							</td>
							<td>
								 <?php echo $daDetails['reservation_to']; ?>
							</td>							
							<td colspan="2">
								<?php echo $daDetails['travel_time']; ?>
							</td>							
						</tr>
					<?php	
						}
					}
				?>
		       </tbody>
				<tr>
					<td colspan="3" style="text-align:center;">
						Total Journey Time(in hours)
					</td>
					<td>
						Total Hours
					</td>
					<td colspan="2">
						<span id="total-travel-time"><?php echo $total_travel_time_hrs; ?></span>
					</td>
				</tr>
		    </table>
			<br/>
			<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th>Name</th>
					<th><?php echo $rowDetail['first_name']." ".$rowDetail['last_name']; ?></th>
					<th>Tour Start Date</th>
					<th>
						<?php echo $tour_start_date; ?>
					</th>
					<th>Tour End Date</th>
					<th>
						<?php echo $tour_end_date; ?>
					</th>
				 </tr>
                  <tr>					
                    <th></th>
					<th>Hours</th>
					<th>Days (hours/24)</th>
					<th>Rate (INR)</th>
					<th>Half DA (INR)</th>
					<th>Full DA (INR)</th>
				  </tr>                
				<tr>						
					<td>Total Time in Hrs.</td>
					<td>
						<?php echo $total_tour_time_hrs; ?>
					</td>
					<td>
						<?php echo $total_tour_time_days; ?>
					</td>
					<td>
						<?php echo $rowDetail['required_da_amount_perday']; ?>
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Travel Time in Hrs.</td>
					<td>
						<?php echo $total_travel_time_hrs; ?>
					</td>
					<td>
						<?php echo $total_travel_time_days; ?>
					</td>
					<td><?php echo $rowDetail['required_da_amount_perday']; ?></td>
					<td>
						<?php echo sprintf("%.2f", $half_da_for_travel); ?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6"> </td>
				</tr>
				<tr>
					<td>Balance Hours</td>
					<td>
						<?php echo $total_balance_hours; ?>
					</td>
					<td>
						<?php echo $total_balance_days; ?>
					</td>					
					<td><?php echo $rowDetail['required_da_amount_perday']; ?></td>
					<td>&nbsp;</td>
					<td>
						<?php echo sprintf("%.2f", $full_da_for_balance_days); ?>
					</td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Total</th>
					<th>
					    <span id="show-total-da-currency">INR </span>
						<?php echo sprintf("%.2f", $total_da); ?>/-
					</th>
				</tr>
		    </table>	
            <br/>
			<?php 
			$heads = $objEmployee->getEmployeeHeads($rowDailyAllowance['emp_id']);
			
			if($rowDailyAllowance['approved_by_HOD1_id'] > 0)
			{
				$rsApproveHod = $objEmployee->getEmployeeData($rowDailyAllowance['approved_by_HOD1_id']);
				$rowApproveHod = $GLOBALS['obj_db']->fetch_array($rsApproveHod);
				$approveHodName = $rowApproveHod['first_name']." ".$rowApproveHod['last_name'];
			}
			else
			{
				$approveHodName = $heads['HOD_1']['fullName'];
			}
			
			$hodText = ($rowDailyAllowance['approve_status_HOD1'] == 'pending') ? ' at ' : ' by '; 
			$hodText .= $approveHodName;
			
			if($rowDailyAllowance['approved_by_finance_id'] > 0)
			{
				$rsApproveFinance = $objAdminUsers->getUserData($rowDailyAllowance['approved_by_finance_id']);
				$rowApproveFinance = $GLOBALS['obj_db']->fetch_array($rsApproveFinance);
				$approveFinanceName = $rowApproveFinance['first_name']." ".$rowApproveFinance['last_name'];
			}
			else
			{
				$approveFinanceName = $objMail->nameUsers['finance_central'];
			}
			
			$financeText = ($rowDailyAllowance['approve_status_finance'] == 'pending') ? ' at ' : ' by ';
			$financeText .= $approveFinanceName;
			?>
			<strong>HOD Approval Status : </strong>&nbsp;&nbsp;<?php echo ucfirst($rowDailyAllowance['approve_status_HOD1']).$hodText; ?><br/><br/>
			<strong>HOD Comments</strong><br/>
			<?php echo ($rowDailyAllowance['HOD1_comment'] != '') ? $rowDailyAllowance['HOD1_comment'] : 'No Comments Given.'; ?>
			<br/>
			<strong>Finance Approval Status : </strong>&nbsp;&nbsp;<?php echo ucfirst($rowDailyAllowance['approve_status_finance']).$financeText; ?><br/><br/>
			<strong>Finance Dept. Comments</strong><br/>
			<?php echo ($rowDailyAllowance['finance_comment'] != '') ? $rowDailyAllowance['finance_comment'] : 'No Comments Given.'; ?>
			<br/><br/>
			<?php
			if($_REQUEST['userType'] == 'authorizer' || $_REQUEST['userType'] == 'finance')
			{
			?>
				<script type="text/javascript">
				function submitForm(status)
				{
					parent.$.fancybox.close();
					document.getElementById("status").value = status;
					document.frmUpateDaStatus.submit();
				}
				</script>
				<br/>
				<form name="frmUpateDaStatus" id="frmUpateDaStatus" target="_top" action="index.php?Option=Tour&SubOption=tourRequests" method="post">
					<input type="hidden" name="tour_id" id="tour_id" value="<?php echo $rowDetail['tour_id']; ?>" />
					<input type="hidden" name="user_type" id="user_type" value="<?php echo $_REQUEST['userType']; ?>" />
					<input type="hidden" name="status" id="status" value="" />
					<input type="hidden" name="ofaction" id="ofaction" value="daStatusUpdate" />
				<?php
				if($rowDailyAllowance['approve_status_HOD1'] == 'pending' && $rowDailyAllowance['approve_status_HOD2'] == 'pending')
				{
					if($_REQUEST['userType'] == 'authorizer')
					{
				?>
						Comments&nbsp;&nbsp;&nbsp;<textarea name="comments" id="comments" rows="4" cols="30"></textarea>
						<br/>
						<br/>
						<input type="button" class="btn btn-success" value="Approve" onclick="submitForm(this.value);">
						<input type="button" class="btn btn-danger" value="Reject" onclick="submitForm(this.value);">	
				<?php	
					}	
				}
				else
				{
					if($rowDailyAllowance['approve_status_HOD1'] == 'approved' || $rowDailyAllowance['approve_status_HOD2'] == 'approved')
					{	
						if($_REQUEST['userType'] == 'finance' && $rowDailyAllowance['approve_status_finance'] == 'pending')
						{
					?>
							Comments&nbsp;&nbsp;&nbsp;<textarea name="comments" id="comments" rows="4" cols="30"></textarea>
							<br/>
							<br/>
							<input type="button" class="btn btn-success" value="Approve" onclick="submitForm(this.value);">
							<input type="button" class="btn btn-danger" value="Reject" onclick="submitForm(this.value);">
					<?php
						}
					}
				}	
				?>
				</form>
			<?php
			}
	}
	else
	{
		echo "No Record Found.";
	}
}
else
{
	echo "Invalid Request.";
}