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
<script src="<?php echo SITE_URL;?>/js/moment.min.js"></script>
<link href="<?php echo SITE_URL;?>/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/bootstrap-datetimepicker.min.js"></script>
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
if(!isset($_GET['tour_id']) || $_GET['tour_id'] == '')
{
?>
	<div class="alert alert-danger alert-dismissable"><strong>Something went wrong. Try again.</strong></div>
<?php	
}
else
{	
	$rs_content = $objTour->getTourRequests($_SESSION['userId'], 'originator', $_GET['tour_id']);
	if(!$GLOBALS['obj_db']->num_rows($rs_content))
	{
	?>
		<div class="alert alert-danger alert-dismissable"><strong>You are not a valid person to calculate/update DA for this tour</strong></div>
	<?php	
	}
	else
	{
		$showDaForm = false;
		$rsDailyAllowance = $objDailyAllowance->getCalculatedDa($_SESSION['userId'], 'originator', $_GET['tour_id']);
		if($GLOBALS['obj_db']->num_rows($rsDailyAllowance) > 0)
		{
			$rowDailyAllowance = $GLOBALS['obj_db']->fetch_array($rsDailyAllowance);
			if($rowDailyAllowance['approve_status_HOD1'] == 'pending' && $rowDailyAllowance['approve_status_HOD2'] == 'pending')
			{
				$showDaForm = true;
			}	
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
			$showDaForm = true;
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
		$result_content = $GLOBALS['obj_db']->fetch_array($rs_content);

switch($action)
{
    case "addsave":
		
		$DaResDetails = array();
		for($x=0; $x < count($_POST['da_date_time_from']); $x++)
		{
			if(trim($_POST['da_date_time_from'][$x] != "") && trim($_POST['da_date_time_to'][$x] != ""))
			{
				$resStartDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['da_date_time_from'][$x]));
				$resEndDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['da_date_time_to'][$x]));
				
				$DaResDetails[] = array('resStartDateTime' => $resStartDateTimeObj->format('Y-m-d H:i:s'), 'resEndDateTime' => $resEndDateTimeObj->format('Y-m-d H:i:s'), 'resFrom' => $_POST['da_from'][$x], 'resTo' => $_POST['da_to'][$x], 'totalJourneyHours' => $_POST['da_total_journey_hours'][$x]);
			}		
		}
		
		$arrayData = array($_GET['tour_id'], $_SESSION['userId'], $_POST['total-travel-time-hrs'], $_POST['total-travel-time-days'], $_POST['total-tour-time-hrs'], $_POST['total-tour-time-days'], $_POST['show-tour-start-date'], $_POST['show-tour-end-date'], $_POST['total-balance-hours'], $_POST['total-balance-days'], $_POST['half-da-for-travel'], $_POST['full-da-for-balance-days'], $_POST['show-total-da'], $DaResDetails);
		
		$_SESSION['msg'] = $objDailyAllowance->add($arrayData);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=daCalculation&tour_id=".$_GET['tour_id']."'; </script>";
		exit();
	break;

    case "updatesave":
		
		$DaResDetails = array();
		$DaResDetailsNew = array();
		foreach($_POST['da_date_time_from'] as $id => $dateTimeFrom)
		{
			if(trim($_POST['da_date_time_from'][$id] != "") && trim($_POST['da_date_time_to'][$id] != ""))
			{
				$resStartDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['da_date_time_from'][$id]));
				$resEndDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['da_date_time_to'][$id]));
				
				$DaResDetails[$id] = array('resStartDateTime' => $resStartDateTimeObj->format('Y-m-d H:i:s'), 'resEndDateTime' => $resEndDateTimeObj->format('Y-m-d H:i:s'), 'resFrom' => $_POST['da_from'][$id], 'resTo' => $_POST['da_to'][$id], 'totalJourneyHours' => $_POST['da_total_journey_hours'][$id]);
			}		
		}
		
		for($x=0; $x < count($_POST['da_date_time_from_New']); $x++)
		{
			if(trim($_POST['da_date_time_from_New'][$x] != "") && trim($_POST['da_date_time_to_New'][$x] != ""))
			{
				$resStartDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['da_date_time_from_New'][$x]));
				$resEndDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['da_date_time_to_New'][$x]));
				
				$DaResDetailsNew[] = array('resStartDateTime' => $resStartDateTimeObj->format('Y-m-d H:i:s'), 'resEndDateTime' => $resEndDateTimeObj->format('Y-m-d H:i:s'), 'resFrom' => $_POST['da_from_New'][$x], 'resTo' => $_POST['da_to_New'][$x], 'totalJourneyHours' => $_POST['da_total_journey_hours_New'][$x]);
			}		
		}
		
		$arrayData = array($_GET['tour_id'], $_SESSION['userId'], $_POST['total-travel-time-hrs'], $_POST['total-travel-time-days'], $_POST['total-tour-time-hrs'], $_POST['total-tour-time-days'], $_POST['show-tour-start-date'], $_POST['show-tour-end-date'], $_POST['total-balance-hours'], $_POST['total-balance-days'], $_POST['half-da-for-travel'], $_POST['full-da-for-balance-days'], $_POST['show-total-da'], $DaResDetails, $DaResDetailsNew);
		
		$_SESSION['msg'] = $objDailyAllowance->update($arrayData);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=daCalculation&tour_id=".$_GET['tour_id']."'; </script>";
		exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objDailyAllowance->delete($_GET['tour_id'], $_SESSION['userId']);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=daCalculation'; </script>";
		exit();
	break;
}
$message = $_SESSION['msg'];
?>
<div class="page-header">
	<?php
    if($ofAction=="update")
    {
    ?>
    <h3>Update your Daily Allowance</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Calculate your Daily Allowance</h3>
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
<?php
if($showDaForm)
{	
?>
<div class="col-md-12" style="clear:both;">		
	<form name="da-calculation" id="da-calculation" method="post" enctype="multipart/form-data" action="index.php?Option=Tour&SubOption=daCalculation&ofaction=<?php if($ofAction=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>&tour_id=<?php echo $_GET['tour_id']; ?>" class="form-horizontal col-md-12" role="form">
		<div class="table-responsive">
			<?php
			$rsUser = $objEmployee->getEmployeeData($_SESSION['userId']);
			$rowUser = $GLOBALS['obj_db']->fetch_array($rsUser);
			?>		
			<table class="table table-bordered table-hover table-striped" id="da-details-section">
                <thead>
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
				if($ofAction=="update")
				{
					$daReservationDetails = $objDailyAllowance->getDaResDetails($_SESSION['userId'], 'originator', $_GET['tour_id']);
					if(!empty($daReservationDetails))
					{
						foreach($daReservationDetails as $id => $daDetails)
						{
					?>
						<tr class="da-res-row">
						<?php		
							$daStartDateTimeObj = new DateTime($daDetails['start_date_time']);
							$daEndDateTimeObj = new DateTime($daDetails['end_date_time']);
						?>
							<td>
								<input type="text" name="da_date_time_from[<?php echo $id; ?>]" class="form-control date-picker" readonly="readonly" value="<?php echo ($daDetails['start_date_time'] != '' && $daDetails['start_date_time'] != '0000-00-00') ? $daStartDateTimeObj->format('d/m/Y H:i') : ''; ?>" />															
							</td>
							<td>							
								<input type="text" name="da_date_time_to[<?php echo $id; ?>]" class="form-control date-picker" readonly="readonly" value="<?php echo ($daDetails['end_date_time'] != '' && $daDetails['end_date_time'] != '0000-00-00') ? $daEndDateTimeObj->format('d/m/Y H:i') : ''; ?>" />
							</td>
							<td>
								 <input type="text" name="da_from[<?php echo $id; ?>]" class="form-control" value="<?php echo $daDetails['reservation_from']; ?>" />
							</td>
							<td>
								 <input type="text" name="da_to[<?php echo $id; ?>]" class="form-control" value="<?php echo $daDetails['reservation_to']; ?>" />
							</td>							
							<td colspan="2">
								<span name="da_total_journey_hours[<?php echo $id; ?>]"><?php echo $daDetails['travel_time']; ?></span>
								<input type="hidden" name="da_total_journey_hours[<?php echo $id; ?>]" value="<?php echo $daDetails['travel_time']; ?>" />
							</td>							
						</tr>
					<?php	
						}
					}
					else
					{
					?>
						<tr class="da-res-row">
							<td>
								<input type="text" name="da_date_time_from_New[]" class="form-control date-picker" readonly="readonly" />															
							</td>
							<td>							
								<input type="text" name="da_date_time_to_New[]" class="form-control date-picker" readonly="readonly" />
							</td>
							<td>
								 <input type="text" name="da_from_New[]" class="form-control" />
							</td>
							<td>
								 <input type="text" name="da_to_New[]" class="form-control" />
							</td>							
							<td colspan="2">
								<span name="da_total_journey_hours_New[]"></span>
								<input type="hidden" name="da_total_journey_hours_New[]" value="" />
							</td>							
						</tr>
					<?php	
					}		
				}
				else
				{
				?>
					<tr class="da-res-row">
							<td>
								<input type="text" name="da_date_time_from[]" class="form-control date-picker" readonly="readonly" />															
							</td>
							<td>							
								<input type="text" name="da_date_time_to[]" class="form-control date-picker" readonly="readonly" />
							</td>
							<td>
								 <input type="text" name="da_from[]" class="form-control" />
							</td>
							<td>
								 <input type="text" name="da_to[]" class="form-control" />
							</td>							
							<td colspan="2">
								<span name="da_total_journey_hours[]"></span>
								<input type="hidden" name="da_total_journey_hours[]" value="" />
							</td>							
						</tr>		
				<?php	
				}	
				?>
		       </tbody>
				<tr>
					<td colspan="6">
						<a href="javascript:void(0);" class="add_more_field_da" title="Add field" data-action="<?php echo ($ofAction=="update") ? 'update' : 'add'; ?>">Add more row</a>
					</td>
				</tr>
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
			
			<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th>Name</th>
					<th><?php echo $rowUser['first_name']." ".$rowUser['last_name']; ?></th>
					<th>Tour Start Date</th>
					<th>
						<span id="show-tour-start-date"><?php echo $tour_start_date; ?></span>
						<input type="hidden" name="show-tour-start-date" value="<?php echo $tour_start_date; ?>" />
					</th>
					<th>Tour End Date</th>
					<th>
						<span id="show-tour-end-date"><?php echo $tour_end_date; ?></span>
						<input type="hidden" name="show-tour-end-date" value="<?php echo $tour_end_date; ?>" />
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
						<span id="total-tour-time-hrs"><?php echo $total_tour_time_hrs; ?></span>
						<input type="hidden" name="total-tour-time-hrs" value="<?php echo $total_tour_time_hrs; ?>" />
					</td>
					<td>
						<span id="total-tour-time-days"><?php echo $total_tour_time_days; ?></span>
						<input type="hidden" name="total-tour-time-days" value="<?php echo $total_tour_time_days; ?>" />
					</td>
					<td>
						<span id="approved_da_perday"><?php echo $result_content['required_da_amount_perday']; ?></span>
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Travel Time in Hrs.</td>
					<td>
						<span id="total-travel-time-hrs"><?php echo $total_travel_time_hrs; ?></span>
						<input type="hidden" name="total-travel-time-hrs" value="<?php echo $total_travel_time_hrs; ?>" />
					</td>
					<td>
						<span id="total-travel-time-days"><?php echo $total_travel_time_days; ?></span>
						<input type="hidden" name="total-travel-time-days" value="<?php echo $total_travel_time_days; ?>" />
					</td>
					<td><?php echo $result_content['required_da_amount_perday']; ?></td>
					<td>
						<span id="half-da-for-travel"><?php echo sprintf("%.2f", $half_da_for_travel); ?></span>
						<input type="hidden" name="half-da-for-travel" value="<?php echo $half_da_for_travel; ?>" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6"> </td>
				</tr>
				<tr>
					<td>Balance Hours</td>
					<td>
						<span id="total-balance-hours"><?php echo $total_balance_hours; ?></span>
						<input type="hidden" name="total-balance-hours" value="<?php echo $total_balance_hours; ?>" />
					</td>
					<td>
						<span id="total-balance-days"><?php echo sprintf("%.2f", $total_balance_days); ?></span>
						<input type="hidden" name="total-balance-days" value="<?php echo $total_balance_days; ?>" />
					</td>					
					<td><?php echo $result_content['required_da_amount_perday']; ?></td>
					<td>&nbsp;</td>
					<td>
						<span id="full-da-for-balance-days"><?php echo sprintf("%.2f", $full_da_for_balance_days); ?></span>
						<input type="hidden" name="full-da-for-balance-days" value="<?php echo $full_da_for_balance_days; ?>" />
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
						<span id="show-total-da"><?php echo sprintf("%.2f", $total_da); ?></span>/-
						<input type="hidden" name="show-total-da" value="<?php echo $total_da; ?>" />
					</th>
				</tr>
		    </table>
		</div>
		<div class="form-group">
            <div class="col-md-12">
				<input type="button" name="btn-calculate-da" id="btn-calculate-da" class="btn btn-default" value="Calculate DA" data-action="<?php if($ofAction=="update"){ echo "update" ; } else { echo "add"; } ?>">            	
            </div>
        </div> <!-- /.form-group -->	
	</form>
</div>
<?php
}
else
{
?>
	<div class="alert alert-danger alert-dismissable"><strong>This DA request has already been approved/rejected.</strong></div>
<?php	
}	
?>
	
<script type="text/javascript">
    $(function(){
		$(document).on('focus', 'input[name^="da_date_time_from["], input[name^="da_date_time_to["], input[name="da_date_time_from_New[]"], input[name="da_date_time_to_New[]"]', function(e) 
		{
			$(this).datetimepicker({
									 useCurrent: false,
									 showClose: true,
									 ignoreReadonly: true,
									 format: "DD/MM/YYYY HH:mm"
									 
								});
		});	
		
		/*$("#da-details-section").on("dp.change", "input[name='da_date_time_from[]']", function (e) {
			$(this).parent().siblings().find('input[name="da_date_time_to[]"]').datetimepicker({format: "DD/MM/YYYY HH:mm", ignoreReadonly: true, showClose: true, useCurrent: false});
			$(this).parent().siblings().find('input[name="da_date_time_to[]"]').data("DateTimePicker").minDate(e.date);
        });*/		
    });
</script>
<?php
	}	
}
?>