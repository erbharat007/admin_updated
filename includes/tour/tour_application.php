<link href="<?php echo SITE_URL;?>/css/jquery.fancybox.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/jquery.fancybox.js"></script>
<script type="text/javascript">
$(document).ready(function () 
{
	$(".iframe_fancybox").fancybox({
		'type': 'iframe', 
        'width' : 800,
        'height' : 470,
        'autoDimensions' : false,
        'autoScale' : false,
		'autoSize': false,
		'fitToView': false
	});
});
</script>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/tour/tour.js"></script>
<style type="text/css">
.fancybox-wrap{
	width: 860px !important;	
}
</style>
<?php
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.Mail.php");
$objMail = new Mail;

require_once("./phpmailer/class.phpmailer.php");
$objPhpMailer = new PHPMailer();

require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.Tour.inc.php");
$objTour = new Tour;

require_once("".CLASS_PATH."/class.DailyAllowance.inc.php");
$objDailyAllowance = new DailyAllowance;

require_once("".CLASS_PATH."/common_function.inc.php");

$additional_link_redirect = "";
if(isset($_REQUEST['page']) && $_REQUEST['page'] > 0)
{
	$additional_link_redirect .= "&page=".$_REQUEST['page'];
}
if(isset($_REQUEST['search_text']) && trim($_REQUEST['search_text']) != "")
{
	$additional_link_redirect .= "&search_text=".trim($_REQUEST['search_text']);
}

if($_REQUEST['ofaction']=="update")
{
    $rs_content = $objTour->getTourRequests($_SESSION['userId'], 'originator', $_REQUEST['id']);
	$result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "addsave":
	
		$requiredDaAmountPerday = (float)$_REQUEST['required_da_amount_perday'];
		
		$tourStartDateDb = getDbDateFormat(trim($_REQUEST['tour_start_date']));
		$tourEndDateDb = getDbDateFormat(trim($_REQUEST['tour_end_date']));
		$daysDiff = (float)getDateDifference($tourStartDateDb, $tourEndDateDb);
		$daysDiff += 1;
		
		$totalDaAmount = $requiredDaAmountPerday*$daysDiff;
		
		$tourResDetails = array();
		for($x=0; $x < count($_POST['reservation_start_date']); $x++)
		{
			if(trim($_POST['reservation_mode'][$x] != -1) && trim($_POST['reservation_start_date'][$x] != "") && trim($_POST['reservation_return_date'][$x] != ""))
			{
				$resStartDateObj = DateTime::createFromFormat('d/m/Y', trim($_POST['reservation_start_date'][$x]));
				$resReturnDateObj = DateTime::createFromFormat('d/m/Y', trim($_POST['reservation_return_date'][$x]));
				
				$tourResDetails[] = array('resStartDate' => $resStartDateObj->format('Y-m-d'), 'resReturnDate' => $resReturnDateObj->format('Y-m-d'), 'resFrom' => $_POST['reservation_from'][$x], 'resTo' => $_POST['reservation_to'][$x], 'resMode' => $_POST['reservation_mode'][$x], 'resModeDetails' => $_POST['reservation_details'][$x]);
			}		
		}
		
		$arrayData = array($_SESSION['userId'], $_REQUEST['tour_start_date'], $_REQUEST['tour_end_date'], $_REQUEST['tour_place'], $_REQUEST['tour_customer'], $_REQUEST['tour_transport'], $_REQUEST['tour_purpose'], $_REQUEST['tickets_purchased_by'], $_REQUEST['tickets_purchased_by_amount'], $_REQUEST['hotel_accommodation_by'], $_REQUEST['hotel_accommodation_by_amount'], $_REQUEST['local_conveyance_paid_by'], $_REQUEST['local_conveyance_amount'], $requiredDaAmountPerday, $daysDiff, $totalDaAmount, $tourResDetails, $_REQUEST['remarks']);
		
		$_SESSION['msg'] = $objTour->createTourRequest($arrayData);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=createTourRequest'; </script>";
		exit();
	break;

    case "updatesave":
		
		$requiredDaAmountPerday = (float)$_REQUEST['required_da_amount_perday'];
		
		$tourStartDateDb = getDbDateFormat(trim($_REQUEST['tour_start_date']));
		$tourEndDateDb = getDbDateFormat(trim($_REQUEST['tour_end_date']));
		$daysDiff = (float)getDateDifference($tourStartDateDb, $tourEndDateDb);
		$daysDiff += 1;
		
		$totalDaAmount = $requiredDaAmountPerday*$daysDiff;
	
		$tourResDetails = array();
		$tourResDetailsNew = array();
		foreach($_POST['reservation_mode'] as $id => $mode)
		{
			if(trim($_POST['reservation_mode'][$id] != -1) && trim($_POST['reservation_start_date'][$id] != "") && trim($_POST['reservation_return_date'][$id] != ""))
			{
				$resStartDateObj = DateTime::createFromFormat('d/m/Y', trim($_POST['reservation_start_date'][$id]));
				$resReturnDateObj = DateTime::createFromFormat('d/m/Y', trim($_POST['reservation_return_date'][$id]));
				
				$tourResDetails[$id] = array('resStartDate' => $resStartDateObj->format('Y-m-d'), 'resReturnDate' => $resReturnDateObj->format('Y-m-d'), 'resFrom' => $_POST['reservation_from'][$id], 'resTo' => $_POST['reservation_to'][$id], 'resMode' => $_POST['reservation_mode'][$id], 'resModeDetails' => $_POST['reservation_details'][$id]);
			}		
		}
		
		for($x=0; $x < count($_POST['reservation_start_date_New']); $x++)
		{
			if(trim($_POST['reservation_mode_New'][$x] != -1) && trim($_POST['reservation_start_date_New'][$x] != "") && trim($_POST['reservation_return_date_New'][$x] != ""))
			{
				$resStartDateObj = DateTime::createFromFormat('d/m/Y', trim($_POST['reservation_start_date_New'][$x]));
				$resReturnDateObj = DateTime::createFromFormat('d/m/Y', trim($_POST['reservation_return_date_New'][$x]));
				
				$tourResDetailsNew[] = array('resStartDate' => $resStartDateObj->format('Y-m-d'), 'resReturnDate' => $resReturnDateObj->format('Y-m-d'), 'resFrom' => $_POST['reservation_from_New'][$x], 'resTo' => $_POST['reservation_to_New'][$x], 'resMode' => $_POST['reservation_mode_New'][$x], 'resModeDetails' => $_POST['reservation_details_New'][$x]);
			}		
		}
		
		$arrayData = array($_REQUEST['id'], $_SESSION['userId'], $_REQUEST['tour_start_date'], $_REQUEST['tour_end_date'], $_REQUEST['tour_place'], $_REQUEST['tour_customer'], $_REQUEST['tour_transport'], $_REQUEST['tour_purpose'], $_REQUEST['tickets_purchased_by'], $_REQUEST['tickets_purchased_by_amount'], $_REQUEST['hotel_accommodation_by'], $_REQUEST['hotel_accommodation_by_amount'], $_REQUEST['local_conveyance_paid_by'], $_REQUEST['local_conveyance_amount'], $requiredDaAmountPerday, $daysDiff, $totalDaAmount, $tourResDetails, $tourResDetailsNew, $_REQUEST['remarks']);
		
		$_SESSION['msg'] = $objTour->updateTourRequest($arrayData);	
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=createTourRequest'; </script>";
		exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objTour->deleteTourRequest($_REQUEST['id'], $_SESSION['userId']);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=createTourRequest'; </script>";
		exit();
	break;
}
$message = $_SESSION['msg'];
?>
<div class="page-header">
	<?php
    if($_REQUEST['ofaction']=="update")
    {
    ?>
    <h3>Edit Tour Request</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Create Tour Request</h3>
    <?php
    }
    ?>
</div>
<?php 
if(isset($_SESSION['msg']) && $_SESSION['msg']!="" && $_SESSION['msg2']=='')
{
	if(isset($_SESSION['success']) && $_SESSION['success'] == 1)
	{
		$alertClass = "alert-success";
	}
	else
	{
		$alertClass = "alert-danger";
	}
?>
	<div class="alert <?php echo $alertClass; ?> alert-dismissable">
		<strong>
		<?php
		echo $_SESSION['msg'];
		$_SESSION['msg'] = "";
		$_SESSION['success'] = "";
		?>
        </strong> 
	</div>
<?php
}
?>
<div class="col-md-12" style="clear:both;">		
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Tour&SubOption=createTourRequest&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-12" role="form" onsubmit="return checkData();">
		<input type="hidden" value="<?php echo $result_content['tour_id']; ?>" name="id" id="id">
		<div class="table-responsive">
			<?php
			$rsUser = $objEmployee->getEmployeeData($_SESSION['userId']);
			$rowUser = $GLOBALS['obj_db']->fetch_array($rsUser);
			?>
			
			<span>
				<label class="col-md-3">Name</label>
				<label class="col-md-3"><?php echo $rowUser['first_name']." ".$rowUser['last_name']; ?></label>
			</span>
			<span>
				<label class="col-md-3" for="select1">Designation </label>
				<label class="col-md-3"><?php echo $rowUser['designation']; ?></label>
			</span>
			<span>
				<label class="col-md-3" for="select1">Department </label>				
				<label class="col-md-3"><?php echo $rowUser['department']; ?></label>
			</span>
			<span>
				<label class="col-md-3" for="select1">Request Date </label>				
				<label class="col-md-3"><?php echo date("d-m-Y"); ?></label>
			</span>
			<table class="table table-bordered table-hover table-striped">
                <thead>
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
							$tourStartDateObj = new DateTime($result_content['tour_start_date']);
							$tourEndDateObj = new DateTime($result_content['tour_end_date']);
						?>
							<td>
								<input name="tour_start_date" id="tour_start_date" class="form-control date-picker" readonly="readonly" value="<?php echo ($result_content['tour_start_date'] != '' && $result_content['tour_start_date'] != '0000-00-00') ? $tourStartDateObj->format('d/m/Y') : ''; ?>" required="required" />															
							</td>
							<td>							
								<input name="tour_end_date" id="tour_end_date" class="form-control date-picker" readonly="readonly" value="<?php echo ($result_content['tour_end_date'] != '' && $result_content['tour_end_date'] != '0000-00-00') ? $tourEndDateObj->format('d/m/Y') : ''; ?>" required="required" />
							</td>
							<td>
								 <input type="text" name="tour_place" id="tour_place" class="form-control" value="<?php echo $result_content['tour_place']; ?>" required="required" />
							</td>
							<td>
								 <input type="text" name="tour_customer" id="tour_customer" class="form-control" value="<?php echo $result_content['tour_customer']; ?>" required="required" />
							</td>							
							<td>
								<select name="tour_transport" id="tour_transport" class="form-control">
									<option value="-1">-- Select -- </option>
									<option value="Bus" <?php echo ($result_content['tour_transport'] == 'Bus') ? 'selected="selected"' : ''; ?>>Bus</option>
									<option value="Train" <?php echo ($result_content['tour_transport'] == 'Train') ? 'selected="selected"' : ''; ?>>Train</option>
									<option value="Flight" <?php echo ($result_content['tour_transport'] == 'Flight') ? 'selected="selected"' : ''; ?>>Flight</option>
									<option value="Taxi" <?php echo ($result_content['tour_transport'] == 'Taxi') ? 'selected="selected"' : ''; ?>>Taxi</option>
								</select>
							</td>                        
                        	<td>
								 <textarea name="tour_purpose" id="tour_purpose" rows="4" cols="40" class="form-control" required="required"><?php echo $result_content['tour_purpose']; ?></textarea>
							</td>                                
						</tr>						
		       </tbody>
		    </table>
			
			<table class="table table-bordered table-hover table-striped" id="res-details-section">
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
				<tbody class="more-field-wrapper">
					<?php
					if($_REQUEST['ofaction']=="update")
					{
						$tourReservationDetails = $objTour->getTourResDetails($_SESSION['userId'], 'originator', $_REQUEST['id']);
						if(!empty($tourReservationDetails))
						{
							foreach($tourReservationDetails as $id => $tourDetails)
							{
							?>
								<tr class="tour-res-row">							
							   <?php		
									$reservationStartDateObj = new DateTime($tourDetails['reservation_start_date']);
									$reservationReturnDateObj = new DateTime($tourDetails['reservation_return_date']);
								?>
									<td>
										<input name="reservation_start_date[<?php echo $id; ?>]" class="form-control date-picker" readonly="readonly" value="<?php echo ($tourDetails['reservation_start_date'] != '' && $tourDetails['reservation_start_date'] != '0000-00-00') ? $reservationStartDateObj->format('d/m/Y') : ''; ?>" />															
									</td>
									<td>							
										<input name="reservation_return_date[<?php echo $id; ?>]" class="form-control date-picker" readonly="readonly" value="<?php echo ($tourDetails['reservation_return_date'] != '' && $tourDetails['reservation_return_date'] != '0000-00-00') ? $reservationReturnDateObj->format('d/m/Y') : ''; ?>" />
									</td>
									<td>
										 <input type="text" name="reservation_from[<?php echo $id; ?>]" class="form-control" value="<?php echo $tourDetails['reservation_from']; ?>" />
									</td>
									<td>
										 <input type="text" name="reservation_to[<?php echo $id; ?>]" class="form-control" value="<?php echo $tourDetails['reservation_to']; ?>" />
									</td>							
									<td>
										 <select name="reservation_mode[<?php echo $id; ?>]" class="form-control">
											<option value="-1">-- Select -- </option>
											<option value="Bus" <?php echo ($tourDetails['reservation_mode'] == 'Bus') ? 'selected="selected"' : ''; ?>>Bus</option>
											<option value="Train" <?php echo ($tourDetails['reservation_mode'] == 'Train') ? 'selected="selected"' : ''; ?>>Train</option>
											<option value="Flight" <?php echo ($tourDetails['reservation_mode'] == 'Flight') ? 'selected="selected"' : ''; ?>>Flight</option>
											<option value="Taxi" <?php echo ($tourDetails['reservation_mode'] == 'Taxi') ? 'selected="selected"' : ''; ?>>Taxi</option>
											<option value="Hotel" <?php echo ($tourDetails['reservation_mode'] == 'Hotel') ? 'selected="selected"' : ''; ?>>Hotel</option>									
										</select>								 
									</td>
									<td colspan="2">
										<textarea name="reservation_details[<?php echo $id; ?>]" rows="4" cols="40" class="form-control"><?php echo $tourDetails['reservation_details']; ?></textarea>
									</td>
								</tr>	
							<?php
							}
						}
						else
						{
						?>
							<tr class="tour-res-row">
								<td>
									<input name="reservation_start_date_New[]" class="form-control date-picker" readonly="readonly" />															
								</td>
								<td>							
									<input name="reservation_return_date_New[]" class="form-control date-picker" readonly="readonly" />
								</td>
								<td>
									 <input type="text" name="reservation_from_New[]" class="form-control" />
								</td>
								<td>
									 <input type="text" name="reservation_to_New[]" class="form-control" />
								</td>							
								<td>
									 <select name="reservation_mode_New[]" class="form-control">
										<option value="-1">-- Select -- </option>
										<option value="Bus">Bus</option>
										<option value="Train">Train</option>
										<option value="Flight">Flight</option>
										<option value="Taxi">Taxi</option>
										<option value="Hotel">Hotel</option>
									</select>								 
								</td>
								<td colspan="2">
									<textarea name="reservation_details_New[]" rows="4" cols="40" class="form-control"></textarea>
								</td>
							</tr>
						<?php
						}	
					}
					else
					{
					?>
					<tr class="tour-res-row">
							<td>
								<input name="reservation_start_date[]" class="form-control date-picker" readonly="readonly" />															
							</td>
							<td>							
								<input name="reservation_return_date[]" class="form-control date-picker" readonly="readonly" />
							</td>
							<td>
								 <input type="text" name="reservation_from[]" class="form-control" />
							</td>
							<td>
								 <input type="text" name="reservation_to[]" class="form-control" />
							</td>							
							<td>
								 <select name="reservation_mode[]" class="form-control">
									<option value="-1">-- Select -- </option>
									<option value="Bus">Bus</option>
									<option value="Train">Train</option>
									<option value="Flight">Flight</option>
									<option value="Taxi">Taxi</option>
									<option value="Hotel">Hotel</option>
								</select>								 
							</td>
							<td colspan="2">
								<textarea name="reservation_details[]" rows="4" cols="40" class="form-control"></textarea>
							</td>
						</tr>
						<?php	
						}	
						?>	
		       </tbody>
				<tr>
					<td colspan="7">
						<a href="javascript:void(0);" class="add_more_field_tour" title="Add field" data-action="<?php echo ($_REQUEST['ofaction']=="update") ? 'update' : 'add'; ?>">Add more row</a>
					</td>
				</tr>
		    </table>
			
			<table class="table table-bordered table-hover table-striped">                
				<tr>					
                    <th>Remarks</th>					
				</tr>                
				<tr>
					<td>
						<textarea name="remarks" id="remarks" rows="4" cols="40"><?php echo $result_content['remarks']; ?></textarea>
					</td>
				</tr>
		    </table>
			
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
								<input type="radio" value="Company" name="tickets_purchased_by" <?php echo ($result_content['tickets_purchased_by'] == 'Company') ? 'checked' : ''; ?>> Company &nbsp;&nbsp;&nbsp;
								<input type="radio" value="Self" name="tickets_purchased_by" <?php echo ($result_content['tickets_purchased_by'] == 'Self') ? 'checked' : ''; ?>> Self 
																							
							</td>
							<td>							
								<input type="text" name="tickets_purchased_by_amount" id="tickets_purchased_by_amount" class="form-control" value="<?php echo $result_content['tickets_purchased_by_amount']; ?>" />
							</td>
					</tr>		
									
                    <th colspan="2">Hotel Accommodation</th>					
				  </tr>                
				
					<tr>						
							<td>
								<input type="radio" value="Company" name="hotel_accommodation_by" <?php echo ($result_content['hotel_accommodation_by'] == 'Company') ? 'checked' : ''; ?>> Company &nbsp;&nbsp;&nbsp;
								<input type="radio" value="Self" name="hotel_accommodation_by" <?php echo ($result_content['hotel_accommodation_by'] == 'Self') ? 'checked' : ''; ?>> Self 													
							</td>
							<td>							
								<input type="text" name="hotel_accommodation_by_amount" id="hotel_accommodation_by_amount" class="form-control" value="<?php echo $result_content['hotel_accommodation_by_amount']; ?>" />
							</td>
					</tr>		
					
						<tr>					
                    <th colspan="2">Local Conveyance</th>					
				  </tr>                
				
					<tr>						
							<td>
								<input type="radio" value="Company" name="local_conveyance_paid_by" <?php echo ($result_content['local_conveyance_paid_by'] == 'Company') ? 'checked' : ''; ?>> Company &nbsp;&nbsp;&nbsp;
								<input type="radio" value="Self" name="local_conveyance_paid_by" <?php echo ($result_content['local_conveyance_paid_by'] == 'Self') ? 'checked' : ''; ?>> Self 															
							</td>
							<td>							
								<input type="text" name="local_conveyance_amount" id="local_conveyance_amount" class="form-control" value="<?php echo $result_content['local_conveyance_amount']; ?>" />
							</td>
					</tr>			
		       
		    </table>
			
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
							<input type="text" name="required_da_amount_perday" id="required_da_amount_perday" value="<?php echo $result_content['required_da_amount_perday']; ?>" >
						</td>
						<td>							
							<span id="days_count"><?php echo $result_content['total_days']; ?></span>
						</td>
						<td>							
							<span id="da_total"><?php echo sprintf("%.2f", $result_content['total_da_amount']); ?></span>
						</td>
					</tr>	       
		    </table>
			
		</div>
		<div class="form-group">
            <div class="col-md-offset-4 col-md-8">
            	<input type="submit" name="Submit" class="btn btn-success" value="<?php if($_REQUEST['ofaction']=="update"){ echo "Update" ; } else { echo "Add"; } ?>">
                <input name="cmdReset" type="reset" class="btn btn-warning" id="cmdReset" value="Reset" >
            </div>
        </div> <!-- /.form-group -->	
	</form>
</div>

	    <?php
		$dsqle = $objTour->getTourRequests($_SESSION['userId'], 'originator');
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		
		<div class="col-md-12" style="clear:both;">
			<p>Your Previous Tour Requests</p>
		</div>	
		
		<form action="index.php?Option=Tour&SubOption=createTourRequest" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr.No.</th>
                    <th>Tour Start Date</th>
					<th>Tour End Date</th>
					<th>Place</th>
					<th>Purpose</th>
					<th>Status (HOD)</th>
					<th>Status (Finance)</th>
					<th>Action</th>
                  </tr>
                </thead>
				<tbody>
				<?php
				 $i = 1;
		
				$end = 20;
				$page = $_REQUEST['page'];
				if($page)
					$start = ($page - 1) * $end;
				else
					$start = 0;

				$select_list_href = "&search_text=".$_REQUEST['search_text'];

				$targetpage = "index.php?Option=Tour&SubOption=createTourRequest";

				$rsTourRequest = $objTour->getTourRequests($_SESSION['userId'], 'originator', 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsTourRequest))
				{	
					$i = 1;
					while($rowTourRequest = $GLOBALS['obj_db']->fetch_array($rsTourRequest))
					{
						$tourStartDateObj = new DateTime($rowTourRequest['tour_start_date']);
						$tourEndDateObj = new DateTime($rowTourRequest['tour_end_date']);
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td>
							<?php echo $tourStartDateObj->format("d-m-Y"); ?>
						</td>
						<td>
							<?php echo $tourEndDateObj->format("d-m-Y"); ?>
						</td>
						<td>
							<?php echo $rowTourRequest['tour_place']; ?>
						</td>
						<td>
							<?php echo $rowTourRequest['tour_purpose']; ?>
						</td>
						<td>
							<?php echo ucwords($rowTourRequest['approve_status_HOD1']); ?>
						</td>
						<td>
							<?php echo ucwords($rowTourRequest['approve_status_finance']); ?>
						</td>
						<td>						
							<a href="./view_tour_request.php?tourId=<?php echo $rowTourRequest['tour_id']; ?>&userType=originator" class="iframe_fancybox" title="Tour Details">
								<img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" />
							</a>
                            &nbsp;
						<?php
						if($rowTourRequest['approve_status_HOD1'] == 'pending' && $rowTourRequest['approve_status_HOD2'] == 'pending')
						{
						?>	
                            <a href="index.php?Option=Tour&SubOption=createTourRequest&ofaction=update&id=<?php echo $rowTourRequest['tour_id']; ?><?php echo $additional_link_redirect; ?>" title="Edit"><img src="<?php echo SITE_URL;?>/images/icon-edit.png" style="width:25px; height:20px;" /></a>
                            &nbsp;
                            <a href="index.php?Option=Tour&SubOption=createTourRequest&ofaction=deleteof&id=<?php echo $rowTourRequest['tour_id']; ?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();" title="Delete"><img src="<?php echo SITE_URL;?>/images/icon-delete.png" style="width:23px; height:25px;" /></a>
						<?php		
						}
						else
						{
							if($rowTourRequest['approve_status_HOD1'] == 'approved' || $rowTourRequest['approve_status_HOD2'] == 'approved')
							{	
								$showUpdateDaBtn = false;
								$rsDailyAllowance = $objDailyAllowance->getCalculatedDa($_SESSION['userId'], 'originator', $rowTourRequest['tour_id']);
								if($GLOBALS['obj_db']->num_rows($rsDailyAllowance) > 0)
								{
									$rowDailyAllowance = $GLOBALS['obj_db']->fetch_array($rsDailyAllowance);
									if($rowDailyAllowance['approve_status_HOD1'] == 'pending' && $rowDailyAllowance['approve_status_HOD2'] == 'pending')
									{
										$showUpdateDaBtn = true;
									}	
									$daText = 'Update DA';
								}
								else
								{
									$daText = 'Calculate Your DA';
									$showUpdateDaBtn = true;
								}
								if($daText == 'Update DA')
								{
								?>
									<a href="./view_da.php?tourId=<?php echo $rowTourRequest['tour_id']; ?>&userType=originator" class="iframe_fancybox" title="DA Details">
										View DA
									</a>
								<?php
								}
								if($showUpdateDaBtn)
								{
								?>
								<a href="index.php?Option=Tour&SubOption=daCalculation&tour_id=<?php echo $rowTourRequest['tour_id']; ?>" title="Calculate Your DA"><?php echo $daText; ?></a>				
								<?php	
								}	
							}
						}	
						?>                            
						</td>			                              
					</tr>
					<?php
						$i++;
					}
				}
				else
				{
					echo '<tr><td colspan="100%" align="center">No Record Found</td></tr>';
				}
				?>
		       </tbody>
		    </table>
		</div>        
	</form>	
	
	<table width="100%">
		<tr>
			<td colspan="100%">
				<?php echo display_Admin_Paging_For_Admin($total_pages, $targetpage, $start, $end, $select_list_href); ?>
			</td>
		</tr>
	</table>