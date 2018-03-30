<link href="<?php echo SITE_URL;?>/css/jquery.fancybox.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/jquery.fancybox.js"></script>
<script>
$(document).ready(function (cash) 
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
	
	$('#txt_search_date_from').datepicker({
								onSelect: function(dateStr) 
								  {
										var d = $.datepicker.parseDate('dd/mm/yy', dateStr);
										$("#txt_search_date_to").datepicker('option', 'minDate', d);
										$('#txt_search_date_to').datepicker('setDate', d);
										setTimeout(function(){
											$("#txt_search_date_to").datepicker('show');
										}, 16);										
								  },
								dateFormat: 'dd/mm/yy',
								buttonImage: 'images/calc.png',
								buttonImageOnly: true,								
								changeMonth: true,
								changeYear: true,
								yearRange:"-10:+10"
							});
	$('#txt_search_date_to').datepicker({
								dateFormat: 'dd/mm/yy',
								buttonImage: 'images/calc.png',
								buttonImageOnly: true,								
								changeMonth: true,
								changeYear: true,
								yearRange:"-10:+10"								
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

require_once("".CLASS_PATH."/class.LocalConveyance.inc.php");
$objLocalConveyance = new LocalConveyance;

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
switch($action)
{
    case "statusUpdate":
		if(!$objEmployee->isValidAuthorizerConveyance($_REQUEST['conveyance_id'], $_SESSION['userId'], $_REQUEST['user_type']))
		{
			$_SESSION['msg'] = 'You are not authorized to take any action for this request.';
		}
		else
		{
			$status = ($_REQUEST['status'] == 'Approve') ? 'approved' : (($_REQUEST['status'] == 'Reject') ? 'rejected' : 'pending');
		
			$_SESSION['msg'] = $objLocalConveyance->updateStatus($_REQUEST['conveyance_id'], $_SESSION['userId'], $status, $_REQUEST['comments'], $_REQUEST['user_type']);
			echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=localConveyanceRequests'; </script>";
			exit();
		}
	break;
	
	case "updateAmountDeposited":
		if(!$objEmployee->isValidAuthorizerConveyance($_REQUEST['conveyance_id'], $_SESSION['userId'], $_REQUEST['user_type']))
		{
			$_SESSION['msg'] = 'You are not authorized to take any action for this request.';
		}
		else
		{
			$_SESSION['msg'] = $objLocalConveyance->updateAmountDeposited($_REQUEST['conveyance_id'], $_SESSION['userId'], $_REQUEST['amount_deposited'], $_REQUEST['user_type']);
			echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=localConveyanceRequests'; </script>";
			exit();
		}
	break;
}
?>
<div class="page-header">
	<h3>Local Conveyance Requests</h3>
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
<div class="col-md-12" style="clear:both; border: 1px solid grey; border-radius: 5px; padding: 5px; margin-bottom: 20px;">
	<form name="search_form" method="post" action="index.php?Option=Tour&SubOption=localConveyanceRequests">
	<div class="row">
	  <div class="tourform">
		<div class="col-md-4">
			<input type="search" name="txt_search_conveyance_id" value="<?php echo $_REQUEST['txt_search_conveyance_id']; ?>" class="form-control" placeholder="Conveyance ID">
		</div>
		<div class="col-md-4">
			<input type="search" name="txt_search_emp_name" value="<?php echo $_REQUEST['txt_search_emp_name']; ?>" class="form-control" placeholder="Employee Name">			
		</div>
		<div class="col-md-4">
			<input type="search" name="txt_search_email" value="<?php echo $_REQUEST['txt_search_email']; ?>" class="form-control" placeholder="E-mail">
		</div>
	</div>	
	</div>
	<div class="row">
	<div class="tourform">
		<div class="col-md-4">
			<input type="search" name="txt_search_date_from" id="txt_search_date_from" value="<?php echo $_REQUEST['txt_search_date_from']; ?>" class="form-control date-picker" placeholder="From Date" readonly="readonly">
		</div>
		<div class="col-md-4">
			<input type="search" name="txt_search_date_to" id="txt_search_date_to" value="<?php echo $_REQUEST['txt_search_date_to']; ?>" class="form-control date-picker" placeholder="To Date" readonly="readonly">			
		</div>
		<div class="col-md-4">		
			<select name="txt_search_status" class="form-control">
				<option value="-1">Select</option>
				<option value="pending" <?php echo ($_REQUEST['txt_search_status'] == 'pending') ? 'selected="selected"' : ''; ?>>Pending</option>				
				<option value="approved" <?php echo ($_REQUEST['txt_search_status'] == 'approved') ? 'selected="selected"' : ''; ?>>Approved</option>
				<option value="rejected" <?php echo ($_REQUEST['txt_search_status'] == 'rejected') ? 'selected="selected"' : ''; ?>>Rejected</option>				
			</select>			
		</div>
		</div>
     </div>
     <div class="row">
     <div class="tourform">
		<div class="col-md-4" style="text-align: left;">
			<input type="checkbox" name="date_included" id="date_included" <?php echo (!empty($_REQUEST['date_included'])) ? 'checked' : ''; ?> />&nbsp;&nbsp;Include Date in Search
		</div>
		<div class="col-md-8" style="text-align: right;">
			<input type="submit" class="btn btn-info" value="Filter Results">
			<input type="button" onclick="window.location.href='index.php?Option=Tour&SubOption=localConveyanceRequests';" class="btn btn-danger" value="Cancel" />
		</div>
		</div>
	</div>
	</form>
</div>
<?php
		$dsqle = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], $_SESSION['userType']);
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		<form action="index.php?Option=Tour&SubOption=localConveyanceRequests" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr. No.</th>
                    <th>Employee Name</th>
                    <th>Date</th>
					<th>From</th>
					<th>To</th>
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

				$targetpage = "index.php?Option=Tour&SubOption=localConveyanceRequests";

				$rsTourRequest = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], $_SESSION['userType'], 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsTourRequest))
				{	
					$i = 1;
					while($rowRequest = $GLOBALS['obj_db']->fetch_array($rsTourRequest))
					{
						$dateObj = new DateTime($rowRequest['date']);
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $rowRequest['first_name']." ".$rowRequest['last_name']; ?></td>
						<td>
							<?php echo $dateObj->format("d-m-Y"); ?>
						</td>
						<td>
							<?php echo $rowRequest['from']; ?>
						</td>
						<td>
							<?php echo $rowRequest['to']; ?>
						</td>
						<td>
							<?php echo $rowRequest['purpose']; ?>
						</td>
						<td><?php echo ucfirst($rowRequest['approve_status_HOD1']); ?></td>
						<td><?php echo ucfirst($rowRequest['approve_status_finance']); ?></td>
						<td>						
							<a href="./view_conveyance_request.php?conveyanceId=<?php echo $rowRequest['id']; ?>&userType=<?php echo $_SESSION['userType']; ?>" class="iframe_fancybox" title="Local Conveyance Details - <?php echo $rowRequest['first_name']." ".$rowRequest['last_name']; ?>">
								<img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" />
							</a>						
						</td>  					                              
					</tr>
					<?php
						$i++;
					}
				}
				else
				{
					echo '<tr><td colspan="100%" align="center">You have no requests.</td></tr>';
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