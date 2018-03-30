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

require_once("".CLASS_PATH."/class.Holiday.inc.php");
$objHoliday = new Holiday;

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

if($_REQUEST['ofaction']=="update")
{
    $rs_content = $objLeaves->getLeaveApplications($_SESSION['userId'], 'originator', $_REQUEST['id']);
    $result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "addsave":
		$arrayData = array($_SESSION['userId'], $_REQUEST['leave_from_date'], $_REQUEST['leave_to_date'], $_REQUEST['leave_type'], $_REQUEST['job_assigned_to'], $_REQUEST['reason_of_leave'], $_REQUEST['leave_address'], $_REQUEST['leave_phone'], $_REQUEST['regionOfEmployee'], $_REQUEST['half_day_leave']);
		
		$_SESSION['msg']=$objLeaves->createLeaveApplication($arrayData);
	break;

    case "updatesave":
		$arrayData = array($_REQUEST['id'], $_SESSION['userId'], $_REQUEST['leave_from_date'], $_REQUEST['leave_to_date'], $_REQUEST['leave_type'], $_REQUEST['job_assigned_to'], $_REQUEST['reason_of_leave'], $_REQUEST['leave_address'], $_REQUEST['leave_phone'], $_REQUEST['regionOfEmployee'], $_REQUEST['half_day_leave']);
		
		$_SESSION['msg'] = $objLeaves->updateLeaveApplication($arrayData);
	break;

    case "deleteof":
		$_SESSION['msg'] = $objLeaves->deleteLeaveApplication($_REQUEST['id'], $_SESSION['userId']);
	break;
}
$message = $_SESSION['msg'];

$rsUser = $objEmployee->getEmployeeData($_SESSION['userId']);
$rowUser = $GLOBALS['obj_db']->fetch_array($rsUser);
$regionOfEmployee = strtolower($rowUser['region']);

$rsHolidays = $objHoliday->getHolidaysOfYear('all', $regionOfEmployee);
$holidaysList = array();
if($GLOBALS['obj_db']->num_rows($rsHolidays) > 0)
{
	while($row = $GLOBALS['obj_db']->fetch_array($rsHolidays))
	{
		$holidaysList[] = $row['date'];
	}	
}
?>

<script language=javascript>
/** Days to be disabled as an array */
//var datesToBeDisabled = ["2016-09-25", "2016-09-28"];
var datesToBeDisabled = [<?php echo '"'.implode('","', $holidaysList).'"' ?>];

$(function() 
{
	$('#leave_from_date, #leave_to_date, #leave_type').on('blur', function(){	
																			if($("#leave_type").val() != -1 && $("#leave_type option:selected").text() == 'SL')
																			{
																			    var leavesRequired = dateDiffInDays($("#leave_from_date").val(), $("#leave_to_date").val());
																				leavesRequired += 1;
																				if(leavesRequired > 3)
																				{
																					$("#medical_certificate_section").show();
																				}
																				else
																				{
																					$("#medical_certificate_section").hide();
																				}
																			}
																			else																			{
																				$("#medical_certificate_section").hide();
																			}
																				
																		  });
	
	$('#leave_from_date').datepicker({
								 onSelect: function(dateStr) 
								  {
										var d = $.datepicker.parseDate('dd/mm/yy', dateStr);
										//d.setDate(d.getDate() + 1);
										$("#leave_to_date").datepicker('option', 'minDate', d);
										$('#leave_to_date').datepicker('setDate', d);
										setTimeout(function(){
											$("#leave_to_date").datepicker('show');
										}, 16);
										var leaveFromDate = $('#leave_from_date').datepicker("getDate");
										var leaveToDate = $('#leave_to_date').datepicker("getDate");
										if(leaveFromDate.getTime() == leaveToDate.getTime())
										{
											$('#half_day_leave').attr("checked", false).prop("disabled", false);
										}
										else
										{
											$('#half_day_leave').attr("checked", false).prop("disabled", true);
										}
								  },
								dateFormat: 'dd/mm/yy',								
								buttonImageOnly: true,
								buttonText:'Select leave from date',
								changeMonth: true,
								changeYear: true,
								beforeShowDay: function(date){
																var dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);
																return [$.inArray(dateString, datesToBeDisabled) == -1];
															 }
							});	
	$('#leave_to_date').datepicker({		
								dateFormat: 'dd/mm/yy',								
								buttonImageOnly: true,
								buttonText:'Select leave to date',
								changeMonth: true,
								changeYear: true,
								beforeShowDay: function(date){
																var dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);
																return [$.inArray(dateString, datesToBeDisabled) == -1];
															 },
								onSelect: function(dateStr) 
								  {
									var leaveFromDate = $('#leave_from_date').datepicker("getDate");
									var leaveToDate = $('#leave_to_date').datepicker("getDate");
									if(leaveFromDate.getTime() == leaveToDate.getTime())
									{
										$('#half_day_leave').attr("checked", false).prop("disabled", false);
									}
									else
									{
										$('#half_day_leave').attr("checked", false).prop("disabled", true);
									}	
								  }
							});
});
function checkData() {
    var error = "";
	
	if($.trim($('#leave_from_date').val()) == '')
	{
		error = "Please select leave from date";
		alert(error);
		$('#leave_from_date').focus();
		return false;
	}
	
	if($.trim($('#leave_to_date').val()) == '')
	{
		error = "Please select leave to date";
		alert(error);
		$('#leave_to_date').focus();
		return false;
	}
	
	if($.trim($('#leave_type').val()) == -1)
	{
		error = "Please select type of leave";
		alert(error);
		$('#leave_type').focus();
		return false;
	}
	
	if($.trim($('#job_assigned_to').val()) == -1)
	{
		error = "Please select job assignee";
		alert(error);
		$('#job_assigned_to').focus();
		return false;
	}
	
	if($.trim($('#leave_type').val()) == -1 != -1 && $.trim($("#leave_type option:selected").text()) == 'SL')
	{
		var leavesRequired = dateDiffInDays($("#leave_from_date").val(), $("#leave_to_date").val());
		leavesRequired += 1;
		if(leavesRequired > 3)
		{
			if($.trim($('#medical_certificate').val()) == '')
			{
				error = "Medical certificate is required for more than 3 sick leaves";
				alert(error);
				$('#medical_certificate').focus();
				return false;
			}
		}
	}
  
    return true;    
}					
</script>
<div class="page-header">
	<?php
    if($_REQUEST['ofaction']=="update")
    {
    ?>
    <h3>Edit Leave Application</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Create Leave Application</h3>
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
<div class="widget-content">
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Leaves&SubOption=leaveApplication&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-6" role="form" onsubmit="return checkData();">
    	<input type="hidden" value="<?php echo $result_content['id']; ?>" name="id" id="id">
		<!--<input type="hidden" value="<?php //echo htmlentities(serialize($holidaysList)); ?>" name="holidays_list" id="holidays_list">-->
		<input type="hidden" value="<?php echo $regionOfEmployee; ?>" name="regionOfEmployee" id="regionOfEmployee">
		
		<div class="form-group">
            <label class="col-md-3">Name</label>
            <div class="col-md-9">
				<?php echo $rowUser['first_name']." ".$rowUser['last_name']; ?>
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Designation <span style="color:red;">*</span></label>
            <div class="col-md-9">
			<?php echo $rowUser['designation']; ?>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Leaves applied for <span style="color:red;">*</span></label>
            <div class="col-md-9">
			<?php
			  if($result_content['leave_from_date'] != '' && $result_content['leave_from_date'] != '0000-00-00')
			  {
				  $leaveFromDateObj = new DateTime($result_content['leave_from_date']);
			  }
			  if($result_content['leave_to_date'] != '' && $result_content['leave_to_date'] != '0000-00-00')
			  {
				   $leaveToDateObj   = new DateTime($result_content['leave_to_date']);				   
			  }			
			?>
             	<div class="col-md-6">
					<input name="leave_from_date" id="leave_from_date" class="form-control date-picker" readonly="readonly" value="<?php echo ($result_content['leave_from_date'] != '' && $result_content['leave_from_date'] != '0000-00-00') ? $leaveFromDateObj->format('d/m/Y') : ''; ?>" />
				</div>
				<div class="col-md-6">
					<input name="leave_to_date" id="leave_to_date" class="form-control date-picker" readonly="readonly" value="<?php echo ($result_content['leave_to_date'] != '' && $result_content['leave_to_date'] != '0000-00-00') ? $leaveToDateObj->format('d/m/Y') : ''; ?>" />
				</div>
				<span id="leaves_count"></span>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <div class="col-md-3"></div>
            <div class="col-md-9">
			<?php
			$disabled = 'disabled="disabled"';
			$checked = '';
			if($result_content['half_day_leave'] == 'YES')
			{
				$disabled = '';
				$checked = 'checked';
			}	
			?>			
				<input type="checkbox" name="half_day_leave" id="half_day_leave" <?php echo $disabled; ?> <?php echo $checked; ?> /> Half Day Leave	
            </div>
        </div> <!-- /.form-group -->		
		<div class="form-group ">
            <label class="col-md-3" for="select1">Type of Leave <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<select name="leave_type" id="leave_type" class="form-control">
				<option value="-1">--- Select type of leave --- </option>
				<?php
				$rsLeaveTypes = $objLeaves->getApplicableLeaveTypesForEmployee($_SESSION['userId']);
				if($GLOBALS['obj_db']->num_rows($rsLeaveTypes))
				{
					while($rowLeaveType = $GLOBALS['obj_db']->fetch_array($rsLeaveTypes))
					{
				?>
						<option value="<?php echo $rowLeaveType['leave_type_id']; ?>" <?php echo ($result_content['leave_type_id'] == $rowLeaveType['leave_type_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowLeaveType['leave_abbr']; ?></option>
				<?php	
					}	
				}	
				?>
				</select>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Job assigned to <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<select name="job_assigned_to" id="job_assigned_to" class="form-control">
				<option value="-1">--- Select employee --- </option>
				<?php
				$rsDeptemp = $objEmployee->getDeptEmployees($rowUser['department_id'], $_SESSION['userId'], $rowUser['branch_id']);
				if($GLOBALS['obj_db']->num_rows($rsDeptemp))
				{
					while($rowDeptemp = $GLOBALS['obj_db']->fetch_array($rsDeptemp))
					{
				?>
						<option value="<?php echo $rowDeptemp['emp_id']; ?>" <?php echo ($result_content['assignee_emp_id'] == $rowDeptemp['emp_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowDeptemp['first_name']." ".$rowDeptemp['last_name']; ?></option>
				<?php	
					}	
				}	
				?>
				</select>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Reason in brief </label>
            <div class="col-md-9">
             	<textarea rows="6" class="form-control" name="reason_of_leave" id="reason_of_leave"><?php echo $result_content['reason_of_leave']; ?></textarea>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Leave Address </label>
            <div class="col-md-9">
             	<textarea rows="6" class="form-control" name="leave_address" id="leave_address"><?php echo $result_content['leave_address']; ?></textarea>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Phone (While on leave) </label>
            <div class="col-md-9">
             	<input name="leave_phone" id="leave_phone" class="form-control" value="<?php echo $result_content['leave_phone']; ?>" />
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group" style="display:none;" id="medical_certificate_section">
            <label class="col-md-3" for="select1">Medical Certificate <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input type="file" name="medical_certificate" id="medical_certificate" value="<?php echo $result_content['leave_phone']; ?>" />
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group">
            <div class="col-md-offset-3 col-md-9">
            	<input type="submit" name="Submit" class="btn btn-success" value="<?php if($_REQUEST['ofaction']=="update"){ echo "Update" ; } else { echo "Add"; } ?>">
                <input name="cmdReset" type="reset" class="btn btn-warning" id="cmdReset" value="Reset" >
            </div>
        </div> <!-- /.form-group -->
      </form>
</div>                               
           
   <div class="col-md-12" style="clear:both;">
		<p>LEAVE ACCOUNT</p>
	</div>	
	
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Type of Leave</th>
					<th>Current Year</th>
                    <th>Opening Balance</th>
					<th>Availed</th>					
                    <th>Balance (in days)</th>
					<th>Eligibility</th>
                  </tr>
                </thead>
				<tbody>
				<?php
				$rsLeaveBalance = $objLeaves->getLeaveBalance($_SESSION['userId']);
				if($GLOBALS['obj_db']->num_rows($rsLeaveBalance))
				{	
					$i = 1;
					while($rowLeaveBalance = $GLOBALS['obj_db']->fetch_array($rsLeaveBalance))
					{
					?>
					<tr>
						<td><?php echo $rowLeaveBalance['leave_type']; ?></td>
						<td><?php echo $rowLeaveBalance['year']; ?></td>
						<td><?php echo $rowLeaveBalance['opening_balance']; ?></td>
						<td><?php echo $rowLeaveBalance['leaves_availed']; ?></td>
						<td><?php echo round($rowLeaveBalance['remaining'],2); ?></td>
						<td>
						<?php 
						if($rowLeaveBalance['earned_type'] == 'NA')
						{
							echo "Not Eligible";
						}
						else
						{
							echo ucwords($rowLeaveBalance['earned_type']);
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
		
		<?php
		$dsqle = $objLeaves->getLeaveApplications($_SESSION['userId'], 'originator');
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		
		<div class="col-md-12" style="clear:both;">
			<p>LEAVES APPLIED BY YOU</p>
		</div>	
		
		<form action="index.php?Option=Leaves&SubOption=leaveApplication" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr.No.</th>
                    <th>From Date</th>
                    <th>To Date</th>
					<th>Job Assignee Name</th>
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

				$targetpage = "index.php?Option=Leaves&SubOption=leaveApplication";

				$rsLeaveApplications = $objLeaves->getLeaveApplications($_SESSION['userId'], 'originator', 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsLeaveApplications))
				{	
					$i = 1;
					while($rowLeaveApplications = $GLOBALS['obj_db']->fetch_array($rsLeaveApplications))
					{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td>
						<?php 
						$leaveFromDate_Obj = new DateTime($rowLeaveApplications['leave_from_date']);
						echo $leaveFromDate_Obj->format('d-m-Y');
						?>
						</td>
						<td>
						<?php
						$leaveToDate_Obj = new DateTime($rowLeaveApplications['leave_to_date']);
						echo $leaveToDate_Obj->format('d-m-Y');
						?>
						</td>
						<td><?php echo $rowLeaveApplications['assignee_first_name']." ".$rowLeaveApplications['assignee_last_name']; ?></td>
						<td><?php echo ucwords($rowLeaveApplications['approve_status_HOD1']); ?></td>
						<td>
						<a href="./leave_request_detail.php?applicationId=<?php echo $rowLeaveApplications['id']; ?>&userType=originator" class="iframe_fancybox" title="Leave Request Details"><img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" /></a>
						<?php
						if($rowLeaveApplications['approve_status_HOD1'] == 'pending' && $rowLeaveApplications['approve_status_HOD2'] == 'pending')
						{
						?>
							<a href="index.php?Option=Leaves&SubOption=leaveApplication&ofaction=update&id=<?php echo $rowLeaveApplications['id'];?><?php echo $additional_link_redirect; ?>" >Edit</a>
							&nbsp;&nbsp;
							<a href="index.php?Option=Leaves&SubOption=leaveApplication&ofaction=deleteof&id=<?php echo $rowLeaveApplications['id'];?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();">Delete</a>
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