<link href="<?php echo SITE_URL;?>/css/jquery.fancybox.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/jquery.fancybox.js"></script>
<script>
$(document).ready(function (cash) 
{
	$(".iframe_fancybox").fancybox({
		type: 'iframe', 
        width : 500,
        height : -50,
        autoDimensions : false,
        autoScale : false
	});
});
</script>
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

require_once("".CLASS_PATH."/class.ActivityCategory.inc.php");
$objCategory = new ActivityCategory;

require_once("".CLASS_PATH."/class.Activity.inc.php");
$objActivity = new Activity;

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
    $rs_content = $objActivity->getDailyActivities($_SESSION['userId'], $_SESSION['userType'], $_REQUEST['date'], $_REQUEST['emp_id']);    
}

switch($action)
{
    case "updatesave":
		$activityStatus = array();
		foreach($_POST['chkSelect'] as $activityId)
		{
			$activityStatus[$activityId] = array('comments' => $_POST['commentActivity'][$activityId]);			
		}	
		$arrayData = array($_SESSION['userId'], $_POST['status'], $activityStatus);
		$_SESSION['msg'] = $objActivity->updateActivityStatus($arrayData);
	break;
}
?>
<script type="text/javascript">
$(function(){
    
	$("#chkSelectAll").change(function() 
	{
		$(".selectBox").prop("checked", $(this).prop("checked"));
	});
	
	$(".selectBox").change(function() 
	{
		if($(".selectBox").length == $(".selectBox:checked").length)
		{
			$("#chkSelectAll").prop("checked", true);
		}
		else
		{
			$("#chkSelectAll").prop("checked", false);
		}
	});
	
});

function checkData()
{
	if($(".selectBox:checked").length == 0)
	{
		alert("Please select at least one activity for approve/reject");
		return false;
	}
	return true;
}
</script>
<div class="page-header">
	<h3>Daily Activity Requests</h3>
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
if($_REQUEST['ofaction']=="update")
{
?>
<div class="col-md-12" style="clear:both;">		
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Activity&SubOption=dailyActivityRequests&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-12" role="form" onsubmit="return checkData();">
		<input type="hidden" name="status" id="status" value="<?php if($_REQUEST['action']=="approve"){ echo "approved" ; } else { echo "rejected"; } ?>" />
		<div class="table-responsive">
		<input type="checkbox" name="chkSelectAll" id="chkSelectAll" /> Select/Unselect All
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Select</th>
					<th>Activity</th>
                    <th>Start date/time</th>
					<th>End date/time</th>
					<th>Category</th>
					<th>Status</th>
					<th>Comments</th>
                  </tr>
                </thead>
				<tbody>
				<?php				
				    if($GLOBALS['obj_db']->num_rows($rs_content) > 0)
					{
						$i = 1;
						while($result_content = $GLOBALS['obj_db']->fetch_array($rs_content))
						{							
							$startTimeObj = new DateTime($result_content['start_date_time']);
							$endTimeObj = new DateTime($result_content['end_date_time']);							
						?>
							<tr>
								<td>
								<?php		
								if($result_content['approve_status_HOD1'] == 'pending' && $result_content['approve_status_HOD2'] == 'pending')
								{
								?>
									<input type="checkbox" name="chkSelect[]" id="chkSelect_<?php echo $result_content['id']; ?>" value="<?php echo $result_content['id']; ?>" class="selectBox" />
								<?php		
								}
								else
								{
									echo 'NA';
								}	
								?>
									
								</td>
								<td class="col-md-4">
									<?php echo $result_content['activity']; ?>
								</td>
								<td>							
									<?php echo ($result_content['start_date_time'] != "" && $result_content['start_date_time'] != "0000-00-00 00:00:00") ? $startTimeObj->format("d/m/Y H:i") : ''; ?>		
								</td>
								<td>
									<?php echo ($result_content['end_date_time'] != "" && $result_content['end_date_time'] != "0000-00-00 00:00:00") ? $endTimeObj->format("d/m/Y H:i") : ''; ?>	
								</td>
								<td>
									<?php echo $result_content['category']; ?>
								</td>
								<td>
									<?php echo ucwords($result_content['approve_status_HOD1']); ?>
								</td>
								<td>
									<?php
									if($result_content['approve_status_HOD1'] == 'pending' && $result_content['approve_status_HOD2'] == 'pending')
									{
									?>
										<textarea name="commentActivity[<?php echo $result_content['id']; ?>]" id="activity_<?php echo $result_content['id']; ?>" rows="3" cols="52" data-row-id="<?php echo $result_content['id']; ?>" class="txtActivity_<?php echo $result_content['id']; ?>"><?php echo $result_content['HOD1_comment']; ?></textarea>
									<?php	
									}
									else
									{
										echo ($result_content['HOD1_comment'] != '') ? $result_content['HOD1_comment'] : 'No comments given.';
									}		
									?>									
								</td>
						</tr>
						<?php
						$i++;		
						}	
					}								
				?>
		       </tbody>
		    </table>
		</div>
		<div class="form-group">
            <div class="col-md-offset-4 col-md-8">
            	<input type="submit" name="Submit" class="btn <?php if($_REQUEST['action']=="approve"){ echo "btn-success" ; } else { echo "btn-danger"; } ?>" value="<?php if($_REQUEST['action']=="approve"){ echo "Approve Selected" ; } else { echo "Reject Selected"; } ?>">                
            </div>
        </div> <!-- /.form-group -->	
	</form>
</div>
<?php
}
?>
		<?php
		$dsqle = $objActivity->getDailyActivities($_SESSION['userId'], $_SESSION['userType'], '', 0);
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		<form action="index.php?Option=Activity&SubOption=dailyActivityRequests" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr.No.</th>
                    <th>Employee Name</th>
					<th>Date</th>
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

				$targetpage = "index.php?Option=Activity&SubOption=dailyActivityRequests";

				$rsActivity = $objActivity->getDailyActivities($_SESSION['userId'], $_SESSION['userType'], '', 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsActivity))
				{	
					$i = 1;
					while($rowDailyActivity = $GLOBALS['obj_db']->fetch_array($rsActivity))
					{						
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $rowDailyActivity['first_name']." ".$rowDailyActivity['last_name']; ?></td>
						<td>
						<?php 
						$dateTimeObj = new DateTime($rowDailyActivity['start_date_time']);
						echo $dateTimeObj->format("d-m-Y"); 
						?>
						</td>
						<td>
						<?php
						$rsActivityStatus = $objActivity->getDailyActivities($_SESSION['userId'], $_SESSION['userType'], $dateTimeObj->format("Y-m-d"), $rowDailyActivity['emp_id']);
						$totalActivities = $GLOBALS['obj_db']->num_rows($rsActivityStatus);
						$totalApproved = 0;
						$totalRejected = 0;
						$totalPending  = 0;
						
						while($rowActivity = $GLOBALS['obj_db']->fetch_array($rsActivityStatus))
						{
							if($rowActivity['approve_status_HOD1'] == 'approved')
								$totalApproved++;
							elseif($rowActivity['approve_status_HOD1'] == 'rejected')
								$totalRejected++;
							else
								$totalPending++;
						}
						if($totalApproved == $totalActivities)
						{
							echo 'Approved';
						}
						elseif($totalRejected == $totalActivities)
						{
							echo 'Rejected';
						}
						elseif($totalPending == $totalActivities)
						{
							echo 'Pending';
						}
						elseif($totalApproved > 0)
						{
							echo 'Partially Approved';
						}
						elseif($totalRejected > 0 &&  $totalApproved == 0)
						{
							echo 'Partially Rejected';
						}	
						?>
						</td>
						<td id="actionbox-<?php echo $rowDailyActivity['id']; ?>">						
							<a href="./view_daily_activity.php?date=<?php echo $dateTimeObj->format("Y-m-d");?>&userType=<?php echo $_SESSION['userType']; ?>&emp_id=<?php echo $rowDailyActivity['emp_id']; ?>" class="iframe_fancybox" title="Daily Activity of - <?php echo $rowDailyActivity['first_name']." ".$rowDailyActivity['last_name']; ?> (<?php echo $dateTimeObj->format("d-m-Y"); ?>)"><img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" /></a>
							&nbsp;
							<?php
							if($_SESSION['userType'] == 'authorizer')
							{
							?>							
							<a href="index.php?Option=Activity&SubOption=dailyActivityRequests&ofaction=update&action=approve&date=<?php echo $dateTimeObj->format("Y-m-d");?>&emp_id=<?php echo $rowDailyActivity['emp_id']; ?>" data-original-title="Approve" title="" data-placement="bottom" data-toggle="tooltip" class="ui-tooltip approve-reject-icon"><img src="<?php echo SITE_URL;?>/images/icon-approve.png" style="width:20px; height:20px;" /></a>
							&nbsp;
							<a href="index.php?Option=Activity&SubOption=dailyActivityRequests&ofaction=update&action=reject&date=<?php echo $dateTimeObj->format("Y-m-d");?>&emp_id=<?php echo $rowDailyActivity['emp_id']; ?>" data-original-title="Reject" title="" data-placement="bottom" data-toggle="tooltip" class="ui-tooltip approve-reject-icon"><img src="<?php echo SITE_URL;?>/images/icon-reject.png" style="width:20px; height:20px;" /></a>
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
					echo '<tr><td colspan="100%" align="center">You have no daily activity requests.</td></tr>';
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