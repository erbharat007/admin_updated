<link href="<?php echo SITE_URL;?>/css/jquery.fancybox.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/jquery.fancybox.js"></script>
<script type="text/javascript">
$(document).ready(function () 
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

<script src="<?php echo SITE_URL;?>/js/moment.min.js"></script>
<link href="<?php echo SITE_URL;?>/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="<?php echo SITE_URL;?>/js/bootstrap-datetimepicker.min.js"></script>
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

require_once("".CLASS_PATH."/class.ActivityCategory.inc.php");
$objCategory = new ActivityCategory;

require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

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
    $rs_content = $objActivity->getDailyActivities($_SESSION['userId'], 'originator', $_REQUEST['date'], 0);    
}

switch($action)
{
    case "addsave":
	/*echo '<pre>';
	print_r($_POST);die;*/
	$dailyActs = array();
	for($x=1; $x <= count($_POST['activity']); $x++)
	{
		if(trim($_POST['activity'][$x] != "") && trim($_POST['fromDateTime'][$x] != "") && trim($_POST['toDateTime'][$x] != ""))
		{
		    $stratDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['fromDateTime'][$x]));
		    $endDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['toDateTime'][$x]));
			
			$dailyActs[] = array('activity' => $_POST['activity'][$x], 'startDateTime' => $stratDateTimeObj->format('Y-m-d H:i:s'), 'endDateTime' => $endDateTimeObj->format('Y-m-d H:i:s'), 'categoryId' => $_POST['category'][$x]);
		}		
	}	
	
	$arrayData = array($_SESSION['userId'], $dailyActs);
	$_SESSION['msg'] = $objActivity->createDailyActivity($arrayData);
	echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Activity&SubOption=dailyActivity'; </script>";
	exit();
	break;

    case "updatesave":
		$dailyActs = array();
		foreach($_POST['activity'] as $activityId => $activity)
		{
			if(trim($_POST['activity'][$activityId] != "") && trim($_POST['fromDateTime'][$activityId] != "") && trim($_POST['toDateTime'][$activityId] != ""))
			{
				$stratDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['fromDateTime'][$activityId]));
				$endDateTimeObj = DateTime::createFromFormat('d/m/Y H:i', trim($_POST['toDateTime'][$activityId]));
				
				$dailyActs[$activityId] = array('activity' => $_POST['activity'][$activityId], 'startDateTime' => $stratDateTimeObj->format('Y-m-d H:i:s'), 'endDateTime' => $endDateTimeObj->format('Y-m-d H:i:s'), 'categoryId' => $_POST['category'][$activityId]);
			}		
		}
		$arrayData = array($_SESSION['userId'], $dailyActs);
		$_SESSION['msg'] = $objActivity->updateDailyActivity($arrayData);		
	break;

    case "deleteof":
		$_SESSION['msg'] = $objActivity->deleteDailyActivity($_REQUEST['date'], $_SESSION['userId']);
	break;
}
$message = $_SESSION['msg'];
?>

<script language=javascript>
$(function() 
{
	$("input[class^='txtFromDate_'], input[class^='txtToDate_']").blur(function(){
		alert("test");
		if($(this).val() != "")
		{
			$(this).removeClass("errortag");
		}	
		
	})
});

function checkData() 
{
    var errorFlag = false;
	$("textarea[class^='txtActivity_']").each(function(){
			var rowId = $(this).data("row-id");
			if($.trim($(".txtActivity_"+rowId).val()) != "")
			{
				if($.trim($(".txtFromDate_"+rowId).val()) == "")
				{
					errorFlag = true;
					$(".txtFromDate_"+rowId).addClass("errortag");
				}
				if($.trim($(".txtToDate_"+rowId).val()) == "")
				{
					errorFlag = true;
					$(".txtToDate_"+rowId).addClass("errortag");
				}
			}	
		});
	if(errorFlag)
	{
		alert("Please fill mandatory fields marked in red.");
		return false;
	}	
	return true;
}					
</script>
<div class="page-header">
	<?php
    if($_REQUEST['ofaction']=="update")
    {
    ?>
    <h3>Edit Daily Activity</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Create Daily Activity</h3>
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
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Activity&SubOption=dailyActivity&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-12" role="form" onsubmit="return checkData();">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>S. No.</th>
					<th>Activity</th>
                    <th>Start date/time</th>
					<th>End date/time</th>
					<th>Category</th>
					<?php
					if($_REQUEST['ofaction']=="update")
					{
					?>
						<th>Status</th>	
					<?php
					}	
					?>                    
                  </tr>
                </thead>
				<tbody>
				<?php
				$rsCategory = $objCategory->getAll();
				if($GLOBALS['obj_db']->num_rows($rsCategory))
				{
					$activityCategory = array();
					while($rowCategory = $GLOBALS['obj_db']->fetch_array($rsCategory))
					{
					    $activityCategory[$rowCategory['id']] = $rowCategory['category'];
					}	
				}	
				
				if($_REQUEST['ofaction']=="update")
				{
				    if($GLOBALS['obj_db']->num_rows($rs_content) > 0)
					{
						$i = 1;
						while($result_content = $GLOBALS['obj_db']->fetch_array($rs_content))
						{	
				?>
                			<tr>
							<td><?php echo $i; ?></td>
                       <?php		
							$startTimeObj = new DateTime($result_content['start_date_time']);
							$endTimeObj = new DateTime($result_content['end_date_time']);
								
							if($result_content['approve_status_HOD1'] == 'pending' && $result_content['approve_status_HOD2'] == 'pending')
							{
						?>
                        		<td class="col-md-4">
								<textarea name="activity[<?php echo $result_content['id']; ?>]" id="activity_<?php echo $result_content['id']; ?>" rows="3" cols="45" data-row-id="<?php echo $result_content['id']; ?>" class="txtActivity_<?php echo $result_content['id']; ?>"><?php echo $result_content['activity']; ?></textarea>							
							</td>
							<td>							
								<div class='input-group date' id='startDateTime_<?php echo $result_content['id']; ?>' data-picker-id="<?php echo $result_content['id']; ?>">
									<input type='text' class="form-control txtFromDate_<?php echo $result_content['id']; ?>" name="fromDateTime[<?php echo $result_content['id']; ?>]" id="fromDateTime_<?php echo $result_content['id']; ?>" readonly="readonly" style="background-color: #fff;" value="<?php echo ($result_content['start_date_time'] != "" && $result_content['start_date_time'] != "0000-00-00 00:00:00") ? $startTimeObj->format("d/m/Y H:i") : ''; ?>" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</td>
							<td>
								 <div class='input-group date' id='EndDateTime_<?php echo $result_content['id']; ?>' data-picker-id="<?php echo $result_content['id']; ?>">
									<input type='text' class="form-control txtToDate_<?php echo $result_content['id']; ?>" name="toDateTime[<?php echo $result_content['id']; ?>]" id="toDateTime_<?php echo $result_content['id']; ?>" readonly="readonly" style="background-color: #fff;" value="<?php echo ($result_content['end_date_time'] != "" && $result_content['end_date_time'] != "0000-00-00 00:00:00") ? $endTimeObj->format("d/m/Y H:i") : ''; ?>" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</td>
							<td>
								<select name="category[<?php echo $result_content['id']; ?>]" id="category_<?php echo $result_content['id']; ?>" class="form-control txtCategory_<?php echo $result_content['id']; ?>">
								<option value="-1">--- Select Category --- </option>
								<?php							
									foreach($activityCategory as $categoryId => $category)
									{
								?>
										<option value="<?php echo $categoryId; ?>" <?php echo ($result_content['category_id'] == $categoryId) ? 'selected="selected"' : ''; ?>><?php echo $category; ?></option>
								<?php	
									}								
								?>
								</select>
							</td>	
                        <?php		
							}
							else
							{
						?>
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
                        <?php		
							}
						?>								
							<td>
								<?php echo ucwords($result_content['approve_status_HOD1']); ?>
							</td>                                
						</tr>
						<?php
						$i++;		
						}	
					}	
				}	
				else
				{								
					for($i=1; $i<=10; $i++)
					{
					?>
						<tr>
							<td><?php echo $i; ?></td>
							<td class="col-md-4">
								<textarea name="activity[<?php echo $i; ?>]" id="activity_<?php echo $i; ?>" rows="3" cols="45" data-row-id="<?php echo $i; ?>" class="txtActivity_<?php echo $i; ?>"></textarea>							
							</td>
							<td>
								<div class='input-group date' id='startDateTime_<?php echo $i; ?>' data-picker-id="<?php echo $i; ?>">
									<input type='text' class="form-control txtFromDate_<?php echo $i; ?>" name="fromDateTime[<?php echo $i; ?>]" id="fromDateTime_<?php echo $i; ?>" readonly="readonly" style="background-color: #fff;" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</td>
							<td>
								 <div class='input-group date' id='EndDateTime_<?php echo $i; ?>' data-picker-id="<?php echo $i; ?>">
									<input type='text' class="form-control txtToDate_<?php echo $i; ?>" name="toDateTime[<?php echo $i; ?>]" id="toDateTime_<?php echo $i; ?>" readonly="readonly" style="background-color: #fff;" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</td>
							<td>
								<select name="category[<?php echo $i; ?>]" id="category_<?php echo $i; ?>" class="form-control txtCategory_<?php echo $i; ?>">
								<option value="-1">--- Select Category --- </option>
								<?php							
									foreach($activityCategory as $categoryId => $category)
									{
								?>
										<option value="<?php echo $categoryId; ?>" <?php echo ($result_content['leave_type_id'] == $categoryId) ? 'selected="selected"' : ''; ?>><?php echo $category; ?></option>
								<?php	
									}								
								?>
								</select>
							</td>
						</tr>
					<?php		
					}	
				}	
				?>
		       </tbody>
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
		$dsqle = $objActivity->getDailyActivities($_SESSION['userId'], 'originator', '', 0);
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		
		<div class="col-md-12" style="clear:both;">
			<p>DAILY ACTIVITIES CREATED BY YOU</p>
		</div>	
		
		<form action="index.php?Option=Activity&SubOption=dailyActivity" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr.No.</th>
                    <th>Date</th>					
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

				$targetpage = "index.php?Option=Activity&SubOption=dailyActivity";

				$rsDailyActivity = $objActivity->getDailyActivities($_SESSION['userId'], 'originator', '', 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsDailyActivity))
				{	
					$i = 1;
					while($rowDailyActivity = $GLOBALS['obj_db']->fetch_array($rsDailyActivity))
					{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td>
							<?php 
                            $dateTimeObj = new DateTime($rowDailyActivity['start_date_time']);
                            echo $dateTimeObj->format("d-m-Y"); 
                            ?>
						</td>						
						<td>
                            <a href="./view_daily_activity.php?date=<?php echo $dateTimeObj->format("Y-m-d");?>&userType=originator" class="iframe_fancybox" title="View Daily Activity (<?php echo $dateTimeObj->format("d-m-Y"); ?>)"><img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" /></a>
                            &nbsp;
                            <a href="index.php?Option=Activity&SubOption=dailyActivity&ofaction=update&date=<?php echo $dateTimeObj->format("Y-m-d");?><?php echo $additional_link_redirect; ?>" ><img src="<?php echo SITE_URL;?>/images/icon-edit.png" style="width:25px; height:20px;" /></a>
                            &nbsp;&nbsp;
                            <a href="index.php?Option=Activity&SubOption=dailyActivity&ofaction=deleteof&date=<?php echo $dateTimeObj->format("Y-m-d");?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();">Delete</a>
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
	
<script type="text/javascript">
    $(function () {
        $("div[id^='startDateTime_'], div[id^='EndDateTime_']").datetimepicker({
			useCurrent: false,
			//sideBySide: true,
			showClose: true,
			ignoreReadonly: true,
			format: "DD/MM/YYYY HH:mm"
			//maxDate: new Date()
		});
        
       /* $("div[id^='startDateTime_']").on("dp.change", function (e) {					
			var pickerId = $(this).data("picker-id");
			$("div[id='EndDateTime_"+pickerId+"']").data("DateTimePicker").minDate(e.date);
        });  */      
    });
</script>