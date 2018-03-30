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

require_once("".CLASS_PATH."/class.AdminUser.inc.php");
$objAdminUsers = new AdminUser;

require_once("".CLASS_PATH."/class.Mail.php");
$objMail = new Mail;

if(isset($_REQUEST['tourId']) && $_REQUEST['tourId'] > 0)
{
	if($_REQUEST['userType'] == 'authorizer')
	{
		if(!$objEmployee->isValidAuthorizerTour($_REQUEST['tourId'], $_SESSION['userId']))
		{
			echo 'You are not authorized to view this section.';
			die;
		}
		else
		{
			$rsDetail = $objTour->getTourRequests($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['tourId']);	
		}
	}	
	else
	{
	    $rsDetail = $objTour->getTourRequests($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['tourId']);			
	}
		
	if($GLOBALS['obj_db']->num_rows($rsDetail) > 0)
	{
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
	?>
		<div style="text-align: center; font-weight: bold; margin-bottom: 20px;">Tour Details - <?php echo $rowDetail['first_name']." ".$rowDetail['last_name']; ?></div>
		<table class="table table-bordered table-hover table-striped">
                <thead>
				  <tr>
                    <th>Tour Request ID</th>
					<th colspan="4"><?php echo $rowDetail['tour_id']; ?></th>
					<th><button onclick="window.print();">Print</button></th>
				  </tr>
				  <tr>
                    <th colspan="7">Tour Schedule</th>
                  </tr>
                  <tr>					
                    <th>Tour Start Date</th>
					<th>End Date</th>
					<th>Place</th>
                    <th>Customer</th>
					<th>Transport</th>
					<th>Purpose</th>
				  </tr>
                </thead>
				<tbody>
					<tr>     
                    		<?php
								$tourStartDateObj = new DateTime($rowDetail['tour_start_date']);
								$tourEndDateObj = new DateTime($rowDetail['tour_end_date']);	                 
							?>                    		
							<td>
								<?php echo $tourStartDateObj->format("d-m-Y"); ?>
							</td>
							<td>							
                            	<?php echo $tourEndDateObj->format("d-m-Y"); ?>						
							</td>
							<td>
                            	<?php echo $rowDetail['tour_place']; ?>
							</td>
							<td>
                            	<?php echo $rowDetail['tour_customer']; ?>							
							</td>							
							<td>
                            	<?php echo $rowDetail['tour_transport']; ?>								
							</td>                        
                        	<td>
                            	<?php echo $rowDetail['tour_purpose']; ?>								
							</td>                                
						</tr>						
		       </tbody>
		    </table>
			<br/>
            <table class="table table-bordered table-hover table-striped">
                <thead>
				  <tr>
                    <th colspan="7">Reservation Details</th>
                  </tr>
                  <tr>					
                    <th>Start Date</th>
					<th>Return Date</th>
					<th>From</th>
                    <th>To</th>
					<th>Mode/Stay</th>
					<th>Mode/Stay Details</th>
				  </tr>
                </thead>
				<tbody>
					<?php
					$tourReservationDetails = $objTour->getTourResDetails($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['tourId']);
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
		       </tbody>
		    </table>
            <br/>
			<table class="table table-bordered table-hover table-striped">
                <thead>
				  <tr>
                    <th>Remarks</th>
                  </tr>                  
                </thead>
				<tbody>
					<tr>
						<td>
							<?php echo $rowDetail['remarks']; ?>								
						</td>                                
					</tr>
		       </tbody>
		    </table>
			<br/>
            <table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th colspan="2">Tour Advance Requirements</th>
                  </tr>
                  <tr>					
                    <th>Travel Tickets to be purchased by</th>
					<th>Amount</th>
				  </tr>                
				
					<tr>						
							<td>
                            	<?php echo $rowDetail['tickets_purchased_by']; ?>
																							
							</td>
							<td>							
								<?php echo sprintf("%.2f", $rowDetail['tickets_purchased_by_amount']); ?>
							</td>
					</tr>		
									
                    <th colspan="2">Hotel Accommodation</th>					
				  </tr>                
				
					<tr>						
							<td>
                            	<?php echo $rowDetail['hotel_accommodation_by']; ?>			
							</td>
							<td>	
                            	<?php echo sprintf("%.2f", $rowDetail['hotel_accommodation_by_amount']); ?>
							</td>
					</tr>		
					
						<tr>					
                    <th colspan="2">Local Conveyance</th>					
				  </tr>                
				
					<tr>						
							<td>
                            	<?php echo $rowDetail['local_conveyance_paid_by']; ?>																					
							</td>
							<td>	
                            	<?php echo sprintf("%.2f", $rowDetail['local_conveyance_amount']); ?>
							</td>
					</tr>			
		       
		    </table>
			<br/>
			<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th colspan="3">Daily Allowance Request</th>
                  </tr>
                  <tr>					
                    <th>Rate (per day) INR</th>
					<th>Days</th>
					<th>Total DA = (Rate*Days)</th>
				  </tr>                
				
					<tr>						
						<td>
							<?php echo sprintf("%.2f",$rowDetail['required_da_amount_perday']); ?>
						</td>
						<td>							
							<?php echo $rowDetail['total_days']; ?>
						</td>
						<td>							
							<?php echo sprintf("%.2f",$rowDetail['total_da_amount']); ?>
						</td>
					</tr>	       
		    </table>
			<br/>
			<table class="table table-bordered table-hover table-striped">				  
                  <tr>					
                    <th></th>
					<th>To be paid by company</th>
					<th>To be paid by self (employee)</th>
				  </tr>				
					<tr>						
						<td>
							Total Amount
						</td>
						<td>							
							<?php
							$totalToBePaidByCompany = 0;
							$totalToBePaidBySelf = 0;
							$totalToBePaidByCompany += $rowDetail['total_da_amount'];
							
							if($rowDetail['hotel_accommodation_by'] == 'Company')
							{
								$totalToBePaidByCompany += $rowDetail['hotel_accommodation_by_amount'];
							}								
							elseif($rowDetail['hotel_accommodation_by'] == 'Self')
							{
								$totalToBePaidBySelf += $rowDetail['hotel_accommodation_by_amount'];
							}							
							if($rowDetail['tickets_purchased_by'] == 'Company')
							{
								$totalToBePaidByCompany += $rowDetail['tickets_purchased_by_amount'];
							}
							elseif($rowDetail['tickets_purchased_by'] == 'Self')
							{
								$totalToBePaidBySelf += $rowDetail['tickets_purchased_by_amount'];
							}								
							if($rowDetail['local_conveyance_paid_by'] == 'Company')
							{
								$totalToBePaidByCompany += $rowDetail['local_conveyance_amount'];
							}	
							elseif($rowDetail['local_conveyance_paid_by'] == 'Self')
							{
								$totalToBePaidBySelf += $rowDetail['local_conveyance_amount'];
							}
							
							echo sprintf("%.2f", $totalToBePaidByCompany);
							?>
						</td>
						<td>							
							<?php echo sprintf("%.2f", $totalToBePaidBySelf); ?>
						</td>
					</tr>	       
		    </table>
			<br/>
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
			
			if($rowDetail['approved_by_finance_id'] > 0)
			{
				$rsApproveFinance = $objAdminUsers->getUserData($rowDetail['approved_by_finance_id']);
				$rowApproveFinance = $GLOBALS['obj_db']->fetch_array($rsApproveFinance);
				$approveFinanceName = $rowApproveFinance['first_name']." ".$rowApproveFinance['last_name'];
			}
			else
			{
				$approveFinanceName = $objMail->nameUsers['finance_central'];
			}
			
			$financeText = ($rowDetail['approve_status_finance'] == 'pending') ? ' at ' : ' by '; 
			$financeText .= $approveFinanceName;
			?>
			<table class="table table-bordered table-hover table-striped">				  
                  <tr>					
                    <th></th>
					<th>HOD</th>
					<th>Finance</th>
				  </tr>				
					<tr>						
						<td>
							Status
						</td>
						<td>							
							<?php echo ucfirst($rowDetail['approve_status_HOD1']).$hodText; ?>
						</td>
						<td>							
							<?php echo ucfirst($rowDetail['approve_status_finance']).$financeText; ?>
						</td>
					</tr>
					<tr>						
						<td>
							Comments
						</td>
						<td>							
							<?php echo ($rowDetail['HOD1_comment'] != '') ? $rowDetail['HOD1_comment'] : 'No Comments Given.'; ?>
						</td>
						<td>							
							<?php echo ($rowDetail['finance_comment'] != '') ? $rowDetail['finance_comment'] : 'No Comments Given.'; ?>
						</td>
					</tr>	 					
		    </table>
			<br />
			<table class="table table-bordered table-hover table-striped">				  
                  	<tr>						
						<td>Amount Deposited</td>
						<td>		
							<?php echo sprintf("%.2f", $rowDetail['amount_deposited']); ?>
						</td>						
					</tr>						 					
		    </table>
		    <br/>
			<?php
			if($_REQUEST['userType'] == 'authorizer' || $_REQUEST['userType'] == 'finance')
			{
			?>
				<script type="text/javascript">
				function submitForm(status)
				{
					parent.$.fancybox.close();
					document.getElementById("status").value = status;
					document.frmUpateTourStatus.submit();
				}
				</script>
				<form name="frmUpateTourStatus" id="frmUpateTourStatus" target="_top" action="index.php?Option=Tour&SubOption=tourRequests" method="post">
					<input type="hidden" name="tour_id" id="tour_id" value="<?php echo $rowDetail['tour_id']; ?>" />
					<input type="hidden" name="user_type" id="user_type" value="<?php echo $_REQUEST['userType']; ?>" />
					<input type="hidden" name="status" id="status" value="" />
					<input type="hidden" name="ofaction" id="ofaction" value="statusUpdate" />
				<?php
				if($rowDetail['approve_status_HOD1'] == 'pending' && $rowDetail['approve_status_HOD2'] == 'pending')
				{
					if($_REQUEST['userType'] == 'authorizer')
					{
				?>
						Comments&nbsp;&nbsp;&nbsp;<textarea name="comments" id="comments" rows="4" cols="30"><?php echo $rowDetail['HOD1_comment']; ?></textarea>
						<br/>
						<br/>
						<input type="button" class="btn btn-success" value="Approve" onclick="submitForm(this.value);">
						<input type="button" class="btn btn-danger" value="Reject" onclick="submitForm(this.value);">
				<?php
					}
				}
				else
				{
					if($rowDetail['approve_status_finance'] == 'pending')
					{
						if($_REQUEST['userType'] == 'authorizer')
						{	
				?>
						Comments&nbsp;&nbsp;&nbsp;<textarea name="comments" id="comments" rows="4" cols="30"><?php echo $rowDetail['HOD1_comment']; ?></textarea>
						<br/>
						<br/>
						<input type="button" class="btn btn-success" value="Remain Pending" onclick="submitForm(this.value);">
						<?php $buttonText = ($rowDetail['approve_status_HOD1'] == 'approved') ? "Reject" : "Approve"; ?>						
						<input type="button" class="btn btn-danger" value="<?php echo $buttonText; ?>" onclick="submitForm(this.value);">
				<?php
						}
					}	
					if($rowDetail['approve_status_HOD1'] == 'approved' || $rowDetail['approve_status_HOD2'] == 'approved')
					{
						if($_REQUEST['userType'] == 'finance' && $rowDetail['approve_status_finance'] == 'pending')
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
				?>
					</form>
				<?php	
					if($_REQUEST['userType'] == 'finance' && $rowDetail['approve_status_finance'] == 'approved')
					{
				?>
					<form name="frmUpateAmountDeposited" id="frmUpateAmountDeposited" target="_top" action="index.php?Option=Tour&SubOption=tourRequests" method="post">
						<input type="hidden" name="tour_id" id="tour_id" value="<?php echo $rowDetail['tour_id']; ?>" />
						<input type="hidden" name="user_type" id="user_type" value="<?php echo $_REQUEST['userType']; ?>" />
						<input type="hidden" name="ofaction" id="ofaction" value="updateAmountDeposited" />
						<table class="table table-bordered table-hover table-striped">				  
							<tr>						
								<td>Amount Deposited</td>						
								<td>
									<input type="text" name="amount_deposited" id="amount_deposited" value="<?php echo $rowDetail['amount_deposited']; ?>" />
									<input type="submit" class="btn btn-success" value="OK" onclick="parent.$.fancybox.close();" name="btnAmountDeposited" id="btnAmountDeposited">
								</td>
							</tr>							
						</table>
					</form>
					<br/>
				<?php
					}
				}				
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