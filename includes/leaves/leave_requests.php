<link href="<?php echo SITE_URL;?>/css/jquery.fancybox.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/jquery.fancybox.js"></script>
<script>
$(document).ready(function (cash) 
{
	$(".iframe_fancybox").fancybox({
		'type': 'iframe', 
        'width' : 800,
        'height' : 400,
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
<style type="text/css">
.fancybox-wrap{
	width: 500px !important;
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

require_once("".CLASS_PATH."/class.Leaves.inc.php");
$objLeaves = new Leaves;

require_once("".CLASS_PATH."/class.Department.inc.php");
$objDept = new Department;

require_once("".CLASS_PATH."/class.Designation.inc.php");
$objDesignation = new Designation;


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

$message = $_SESSION['msg'];

switch($action)
{
	case "statusUpdate":
		if(!$objEmployee->isValidAuthorizer($_REQUEST['leave_id'], $_SESSION['userId']))
		{
			$_SESSION['msg'] = 'You are not authorized to take any action for this request.';
		}
		else
		{
			$status = $_REQUEST['status'];		
			$_SESSION['msg'] = $objLeaves->updateStatus($_REQUEST['leave_id'], $_SESSION['userId'], $status, $_REQUEST['comments']);
			echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Leaves&SubOption=leaveRequests'; </script>";
			exit();
		}
	break;
}
?>
<script type="text/javascript">
$(function() 
{
	$(".approve-reject-icon").click(function(){
		
		var status        = $(this).data('action');
		var applicationId = $(this).data('application-id');
		
		$.ajax({
		url: "ajax.php",
		dataType: 'json',
		type: "POST",
		data: {'action': 'updateLeaveStatus', 'status' : status, 'applicationId': applicationId},
		beforeSend: function() 
		{
			 $("#message-section").html("<img src='images/loaderIcon.gif' />");
		},
		success: function(data)
		{
			if(data.successFlag)
			{
				$("#message-section").removeClass("error").addClass("success").html(data.message);
				$("#actionbox-"+applicationId).html('Action already taken.');
			}
			else
			{
				$("#message-section").removeClass("success").addClass("error").html(data.message);
			}
			$("#status-text-"+applicationId).html(status);
			setTimeout(function(){
				$("#message-section").removeClass("success").removeClass("error").html('');
			}, 4000);
		}        
		});
		
	});
});
</script>
<div class="page-header">
	<h3>Leave Requests</h3>
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
	<form name="search_form" method="post" action="index.php?Option=Leaves&SubOption=leaveRequests">
	<div class="row">
	  <div class="tourform">
		<div class="col-md-4">
			<input type="search" name="txt_search_emp_name" value="<?php echo $_REQUEST['txt_search_emp_name']; ?>" class="form-control" placeholder="Applicant Name">
		</div>
		<div class="col-md-4">
			<input type="search" name="txt_search_email" value="<?php echo $_REQUEST['txt_search_email']; ?>" class="form-control" placeholder="E-mail">
		</div>
		<div class="col-md-4">
			<select name="txt_search_status" class="form-control">
				<option value="-1">Select Status</option>				
				<option value="approved" <?php echo ($_REQUEST['txt_search_status'] == 'approved') ? 'selected="selected"' : ''; ?>>Approved</option>
				<option value="rejected" <?php echo ($_REQUEST['txt_search_status'] == 'rejected') ? 'selected="selected"' : ''; ?>>Rejected</option>
				<option value="pending" <?php echo ($_REQUEST['txt_search_status'] == 'pending') ? 'selected="selected"' : ''; ?>>Pending</option>
				<option value="cancelled" <?php echo ($_REQUEST['txt_search_status'] == 'cancelled') ? 'selected="selected"' : ''; ?>>Cancelled</option>
			</select>
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
			<select name="txt_search_date_type" class="form-control">
				<option value="applied_date" <?php echo ($_REQUEST['txt_search_date_type'] == 'applied_date') ? 'selected="selected"' : ''; ?>>Applied Date</option>
				<option value="leave_from_date" <?php echo ($_REQUEST['txt_search_date_type'] == 'leave_from_date') ? 'selected="selected"' : ''; ?>>Leave From Date</option>
				<option value="leave_to_date" <?php echo ($_REQUEST['txt_search_date_type'] == 'leave_to_date') ? 'selected="selected"' : ''; ?>>Leave To Date</option>
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
			<input type="button" onclick="window.location.href='index.php?Option=Leaves&SubOption=leaveRequests';" class="btn btn-danger" value="Cancel" />
		</div>
		</div>
	</div>
	</form>
</div>
		<p id='message-section'></p>
		<?php
		$dsqle = $objLeaves->getLeaveApplications($_SESSION['userId'], $_SESSION['userType']);
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		<form action="index.php?Option=Leaves&SubOption=leaveRequests" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr.No.</th>
                    <th>Applicant Name</th>
					<th>Leave Applied Date</th>
					<th>Leave From Date</th>
                    <th>Leave To Date</th>
					<th>Status</th>
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

				$targetpage = "index.php?Option=Leaves&SubOption=leaveRequests";

				$rsLeaveApplications = $objLeaves->getLeaveApplications($_SESSION['userId'], $_SESSION['userType'], 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsLeaveApplications))
				{	
					$i = 1;
					while($rowLeaveApplications = $GLOBALS['obj_db']->fetch_array($rsLeaveApplications))
					{
						$createdDateObj = new DateTime($rowLeaveApplications['created_date']);
						$fromDateObj = new DateTime($rowLeaveApplications['leave_from_date']);
						$toDateObj = new DateTime($rowLeaveApplications['leave_to_date']);
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $rowLeaveApplications['first_name']." ".$rowLeaveApplications['last_name']; ?></td>
						<td><?php echo $createdDateObj->format('l, d F Y h:i A'); ?></td>
						<td><?php echo $fromDateObj->format('l, d F Y'); ?></td>
						<td><?php echo $toDateObj->format('l, d F Y'); ?></td>						
						<td id="status-text-<?php echo $rowLeaveApplications['id']; ?>"><?php echo ucfirst($rowLeaveApplications['approve_status_HOD1']); ?></td>					
						<td>
							<a href="./leave_request_detail.php?applicationId=<?php echo $rowLeaveApplications['id']; ?>&userType=<?php echo $_SESSION['userType']; ?>" class="iframe_fancybox" title="Leave Request Details - <?php echo $rowLeaveApplications['first_name']." ".$rowLeaveApplications['last_name']; ?>"><img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" /></a>
						
							<span id="actionbox-<?php echo $rowLeaveApplications['id']; ?>">
                                <?php
								if($rowLeaveApplications['approve_status_HOD1'] != 'approved' && $_SESSION['userType'] == 'authorizer')
								{
								?>
									<a href="./ajax.php?action=showLeaveComments&applicationId=<?php echo $rowLeaveApplications['id']; ?>&userType=<?php echo $_SESSION['userType']; ?>&status=approved" data-original-title="Approve Request" title="" data-placement="bottom" data-toggle="tooltip" class="ui-tooltip iframe_fancybox">
										<img src="<?php echo SITE_URL;?>/images/icon-approve.png" style="width:20px; height:20px;" />
									</a>
								<?php
								}
								?>
								<?php
								if($rowLeaveApplications['approve_status_HOD1'] != 'rejected' && $_SESSION['userType'] == 'authorizer')
								{
								?>
									&nbsp;
									<a href="./ajax.php?action=showLeaveComments&applicationId=<?php echo $rowLeaveApplications['id']; ?>&userType=<?php echo $_SESSION['userType']; ?>&status=rejected" data-original-title="Reject Request" title="" data-placement="bottom" data-toggle="tooltip" class="ui-tooltip iframe_fancybox">
										<img src="<?php echo SITE_URL;?>/images/icon-reject.png" style="width:20px; height:20px;" />
									</a>
								<?php
								}
								?>								
							</span>						
								<?php
								if($rowLeaveApplications['approve_status_HOD1'] != 'cancelled' && $_SESSION['userType'] == 'authorizer')
								{
								?>
									&nbsp;
								<a href="./ajax.php?action=showLeaveComments&applicationId=<?php echo $rowLeaveApplications['id']; ?>&userType=<?php echo $_SESSION['userType']; ?>&status=cancelled" data-original-title="Cancel Request" title="" data-placement="bottom" data-toggle="tooltip" class="ui-tooltip iframe_fancybox">
									Cancel
								</a>		
								<?php	
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
					echo '<tr><td colspan="100%" align="center">You have no leave requests.</td></tr>';
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