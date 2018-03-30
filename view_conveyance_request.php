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

require_once("".CLASS_PATH."/class.LocalConveyance.inc.php");
$objLocalConveyance = new LocalConveyance;

require_once("".CLASS_PATH."/class.AdminUser.inc.php");
$objAdminUsers = new AdminUser;

require_once("".CLASS_PATH."/class.Mail.php");
$objMail = new Mail;

if(isset($_REQUEST['conveyanceId']) && $_REQUEST['conveyanceId'] > 0)
{
	if($_REQUEST['userType'] == 'authorizer')
	{
		if(!$objEmployee->isValidAuthorizerConveyance($_REQUEST['conveyanceId'], $_SESSION['userId']))
		{
			echo 'You are not authorized to view this section.';
			die;
		}
		else
		{
			$rsDetail = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['conveyanceId']);	
		}
	}	
	else
	{
	    $rsDetail = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['conveyanceId']);			
	}
		
	if($GLOBALS['obj_db']->num_rows($rsDetail) > 0)
	{
		$rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail);
	?>
		<div style="text-align: center; font-weight: bold; margin-bottom: 20px;">Local Conveyance Details - <?php echo $rowDetail['first_name']." ".$rowDetail['last_name']; ?></div>
		<table class="table table-bordered table-hover table-striped">
                <thead>
				  <tr>
                    <th>Conveyance Request ID</th>
					<th colspan="3"><?php echo $rowDetail['id']; ?></th>
					<th><button onclick="window.print();">Print</button></th>
				  </tr>
                  <tr>					
                    <th>Type</th>
					<th>Date</th>
					<th>From</th>
                    <th>To</th>
					<th>Paid by</th>	
				  </tr>
                </thead>
				<tbody>
					<tr>     
                    		<?php
								$dateObj = new DateTime($rowDetail['date']);								
							?>                    		
							<td>
								<?php echo $rowDetail['type']; ?>
							</td>
							<td>							
                            	<?php echo $dateObj->format("d-m-Y"); ?>						
							</td>
							<td>
                            	<?php echo $rowDetail['from']; ?>								 
							</td>
							<td>
                            	<?php echo $rowDetail['to']; ?>							
							</td>							
							<td>
                            	<?php echo $rowDetail['paid_by']; ?>								
							</td>                        	
						</tr>						
		       </tbody>
		    </table>
			<br/>
			<table class="table table-bordered table-hover table-striped">
                <thead>				  
                  <tr>					
                    <th>Start Meter Reading</th>
					<th>End Meter Reading</th>
					<th>Total Kms.</th>				    
				  </tr>
                </thead>
				<tbody>
					<tr>                      
							<td>
                            	<?php echo $rowDetail['start_meter_reading']; ?>
							</td>
							<td>	
                            	<?php echo $rowDetail['end_meter_reading']; ?>								
							</td>
							<td>
                            	<?php echo sprintf("%.2f",$rowDetail['total_kms']); ?>
							</td>							                    	
						</tr>						
		       </tbody>
		    </table>
			<br/>
            <table class="table table-bordered table-hover table-striped">
                <thead>		  
                  <tr>					
                    <th>Purpose</th>					
					<th>Travel Mode</th>
					<th>Amount</th>				    
				  </tr>
                </thead>
				<tbody>
					<tr>                      
							<td>
                            	<?php echo $rowDetail['purpose']; ?>
							</td>
							<td>	
                            	<?php echo $rowDetail['travel_mode']; ?>								
							</td>
							<td>
                            	<?php echo sprintf("%.2f",$rowDetail['amount']); ?>
							</td>							                    	
						</tr>						
		       </tbody>
		    </table>
			<br/>
			<?php
				$rsAttachments = $objLocalConveyance->getAttachments($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['conveyanceId']);
				if($GLOBALS['obj_db']->num_rows($rsAttachments) > 0)
				{
			?>
					<table class="table table-bordered table-hover table-striped">
						<thead>		  
						  <tr>					
							<th>Comments</th>					
							<th>Attachment</th>							
						  </tr>
						</thead>
						<tbody>					  					
			<?php	
					while($rowAttach = $GLOBALS['obj_db']->fetch_array($rsAttachments))
					{
				?>
						<tr>
							<td><?php echo $rowAttach['comments']; ?></td>
							<td>									
							<?php
							if($rowAttach['attachment'] != '')
							{
								$attachmentPath = "user_uploads/emp_".$rowDetail['emp_id']."/".$rowAttach['attachment'];
								if(file_exists($attachmentPath))
								{
									if(end(explode('.', $rowAttach['attachment'])) == 'jpg' || end(explode('.', $rowAttach['attachment'])) == 'jpeg' || end(explode('.', $rowAttach['attachment'])) == 'png')
									{	
							?>
									<img src="<?php echo $attachmentPath; ?>" width="125" height="100" alt="<?php echo $rowAttach['attachment']; ?>" />
									<br />
									<a href="<?php echo $attachmentPath; ?>" target="_blank">View In Original Size</a>
							<?php
									}
									/*else
									{
										echo 'Can\'t show preview of file';
									}*/	
							?>
										
									
									&nbsp;
									<a href="download.php?empId=<?php echo $rowDetail['emp_id']; ?>&filename=<?php echo base64_encode($rowAttach['attachment']); ?>">Download</a>									
							<?php		
								}
							}
							else
							{
								echo 'No Proof Given';	
							}	
							?>
							</td>
						</tr>
				<?php	
					}
				?>
					 </tbody>
					 </table>
				<?php	
				}
				?>
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
				$approveFinanceName = $objMail->nameUsers['finance_'.$rowDetail['branch_id']][0];
			}
			
			$financeText = ($rowDetail['approve_status_finance'] == 'pending') ? ' at ' : ' by ';
			$financeText .= $approveFinanceName;
			?>	
			<br/>
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
				document.frmUpateConveyanceStatus.submit();
			}
			</script>
				<br/>
				<form name="frmUpateConveyanceStatus" id="frmUpateConveyanceStatus" target="_top" action="index.php?Option=Tour&SubOption=localConveyanceRequests" method="post">
					<input type="hidden" name="conveyance_id" id="conveyance_id" value="<?php echo $rowDetail['id']; ?>" />
					<input type="hidden" name="user_type" id="user_type" value="<?php echo $_REQUEST['userType']; ?>" />
					<input type="hidden" name="status" id="status" value="" />
					<input type="hidden" name="ofaction" id="ofaction" value="statusUpdate" />
				<?php
				if($rowDetail['approve_status_HOD1'] == 'pending' && $rowDetail['approve_status_HOD2'] == 'pending')
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