<?php
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.Leaves.inc.php");
$objLeaves = new Leaves;

require_once("".CLASS_PATH."/class.Department.inc.php");
$objDept = new Department;

require_once("".CLASS_PATH."/class.Designation.inc.php");
$objDesignation = new Designation;

require_once("".CLASS_PATH."/class.AdminUser.inc.php");
$objAdminUsers = new AdminUser;

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
    $rs_content = $objEmployee->getEmployeeData($_REQUEST['id']);
    $result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "updatesave":
		$rsLeaveTypes = $objLeaves->getLeaveTypes();
		if($GLOBALS['obj_db']->num_rows($rsLeaveTypes))
		{
			$leaveBalArray = array();
			while($rowLeaveTypes = $GLOBALS['obj_db']->fetch_array($rsLeaveTypes))
			{
				$leaveBalArray[$rowLeaveTypes['leave_type_id']] = array('openingBalance' => $_REQUEST['OB_'.$rowLeaveTypes['leave_type_id']], 'earnedType' => $_REQUEST['earned_type_'.$rowLeaveTypes['leave_type_id']]);
			}	
		}
		$arrayData = array($_REQUEST['id'], $leaveBalArray, $_REQUEST['update_EL']);
		$_SESSION['msg'] = $objLeaves->updateLeaveBalance($arrayData);
		//header("Location: index.php?Option=Setup&SubOption=empLeaveSetup".$additional_link_redirect);
		//exit();
	break;    
}
$message = $_SESSION['msg'];
?>

<script language=javascript>
function checkData() {
    var error = "";
  
    return true;
}					
</script>
<div class="page-header">
	<h3>Leave Setup for Employees</h3>
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
if($action == 'update')
{	
?>
<div class="widget-content">
		<div class="form-group">
            <label class="col-md-3">Employee Name </label>
            <div class="col-md-9">
				<p><?php echo $result_content['first_name']." ".$result_content['last_name']; ?></p>            	
            </div>
        </div> <!-- /.form-group -->
       
		<div class="form-group ">
            <label class="col-md-3" for="select1">Employee Code </label>
            <div class="col-md-9">
				<p><?php echo $result_content['emp_code']; ?></p>            	
            </div>
        </div> <!-- /.form-group -->
		
		<div class="form-group ">
            <label class="col-md-3" for="select1">Branch </label>
            <div class="col-md-9">
				<p><?php echo $result_content['city_name']; ?></p>            	
            </div>
        </div> <!-- /.form-group -->      
</div>                               

<div class="col-md-12" style="clear:both;">
		<p>LEAVE ACCOUNT</p>
	</div>	
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Setup&SubOption=empLeaveSetup&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-12" role="form">
		<input type="hidden" value="<?php echo $result_content['emp_id']; ?>" name="id" id="id">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped" width="100%">
                <thead>
                  <tr>
                    <th width="13%">Type of Leave</th>
                    <th width="8%">Opening Balance</th>
					<th width="11%">Leaves Availed</th>
					<th width="12%">Closing Balance</th>
					<th width="10%">Current Year</th>
					<th width="12%">Earned Type</th>
					<th width="16%">Last Updated</th>
					<th width="19%">Updated by</th>
                  </tr>
                </thead>
				<tbody>
				<?php
				$rsLeaveBalance = $objLeaves->getLeaveBalance($_REQUEST['id']);
				if($GLOBALS['obj_db']->num_rows($rsLeaveBalance))
				{	
					$i = 1;
					while($rowLeaveBalance = $GLOBALS['obj_db']->fetch_array($rsLeaveBalance))
					{
					?>
					<tr>
						<td><?php echo $rowLeaveBalance['leave_type']; ?></td>
						<td>
							<input type="text" name="OB_<?php echo $rowLeaveBalance['leave_type_id']; ?>" value="<?php echo $rowLeaveBalance['opening_balance']; ?>" />
						</td>
						<td><?php echo $rowLeaveBalance['leaves_availed']; ?></td>
						<td><?php echo round($rowLeaveBalance['remaining'], 2); ?></td>
						<td><?php echo $rowLeaveBalance['year']; ?></td>
						<td>
							<input type="radio" name="earned_type_<?php echo $rowLeaveBalance['leave_type_id']; ?>" value="automatic" <?php echo ($rowLeaveBalance['earned_type'] == 'automatic') ? 'checked' : ''; ?> /> Automatic <br/>
							<input type="radio" name="earned_type_<?php echo $rowLeaveBalance['leave_type_id']; ?>" value="NA" <?php echo ($rowLeaveBalance['earned_type'] == 'NA') ? 'checked' : ''; ?> /> NA
						</td>
						<td>
						<?php 
							$lastUpdatedTimeObj = new DateTime($rowLeaveBalance['last_updated']);
							echo $lastUpdatedTimeObj->format('d-M-Y h:i A');
						?>
						</td>
						<td>
						<?php						
						if($rowLeaveBalance['updated_by_user_type'] == 'admin_user')
						{
							$rsUpdatedBy        = $objAdminUsers->getUserData($rowLeaveBalance['updated_by']);
							$rowUpdatedBy       = $GLOBALS['obj_db']->fetch_array($rsUpdatedBy);
							$updatedByName      = $rowUpdatedBy['first_name']." ".$rowUpdatedBy['last_name'];
						}
						elseif($rowLeaveBalance['updated_by_user_type'] == 'employee')
						{
							$rsUpdatedBy    = $objEmployee->getEmployeeData($rowLeaveBalance['updated_by']);
							$rowUpdatedBy   = $GLOBALS['obj_db']->fetch_array($rsUpdatedBy);
							$updatedByName  = $rowUpdatedBy['first_name']." ".$rowUpdatedBy['last_name'];
						}
						elseif($rowLeaveBalance['updated_by_user_type'] == 'CRON')
						{
							$updatedByName = 'Automated script';
						}
						else
						{
							$updatedByName = '';
						}	
						echo $updatedByName."<br /> (".$rowLeaveBalance['updated_by_user_type'].")";
						?>
						</td>
					</tr>
					<?php
						$i++;
					}
				}
				else
				{
					$rsTotalLeaveTypes = $objLeaves->getLeaveTypes();
					if($GLOBALS['obj_db']->num_rows($rsTotalLeaveTypes))
					{
						$leaveBalArray = array();
						while($rowLeaveTypes = $GLOBALS['obj_db']->fetch_array($rsTotalLeaveTypes))
						{
						?>
							<tr>
								<td><?php echo $rowLeaveTypes['leave_type']; ?></td>
								<td>
									<input type="text" name="OB_<?php echo $rowLeaveTypes['leave_type_id']; ?>" value="0" />
								</td>
								<td>0</td>
								<td>0</td>
								<td><?php echo date('Y'); ?></td>
								<td>
									<input type="radio" name="earned_type_<?php echo $rowLeaveTypes['leave_type_id']; ?>" value="automatic" /> Automatic <br/>
									<input type="radio" name="earned_type_<?php echo $rowLeaveTypes['leave_type_id']; ?>" value="NA" checked /> NA
								</td>
								<td></td>
								<td></td>
							</tr>
					<?php	
						}	
					}
				}
				?>
				<tr>
					<td colspan="4">
						<input type="checkbox" name="update_EL" id="update_EL" /> Update EL also
					</td>
				</tr>
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

<?php
}
?>

	
	<?php
	$dsqle = $objEmployee->getAllEmployees();
	$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	
	<div class="col-md-12" style="clear:both;">
		<form name="search_form" method="post" action="index.php?Option=Setup&SubOption=empLeaveSetup">
			<input type="search" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>">
			<input type="submit" class="btn btn-info" value="Search">
			<input type="button" onclick="window.location.href='index.php?Option=Setup&SubOption=empLeaveSetup';" class="btn btn-danger" value="Cancel" />				
		</form>
	</div>	
	
	<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>Employee Name</th>
					<th>Department</th>
					<th>Designation</th>					
                    <th>Leave Setup</th>
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

				$targetpage = "index.php?Option=Setup&SubOption=empLeaveSetup";

				$contentrow = $objEmployee->getAllEmployees($start, $end);
				if($GLOBALS['obj_db']->num_rows($contentrow))
				{	
					$i = 1;
					while($dsqlerow=$GLOBALS['obj_db']->fetch_array($contentrow))
					{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $dsqlerow['first_name']." ".$dsqlerow['last_name']; ?></td>
						<td><?php echo $dsqlerow['department']; ?></td>
						<td><?php echo $dsqlerow['designation']; ?></td>
						
						<td>
							<!--<a href="./leave_request_detail.php?applicationId=<?php //echo $rowLeaveApplications['id']; ?>" class="iframe_fancybox" title="Leave Request Details - <?php //echo $rowLeaveApplications['first_name']." ".$rowLeaveApplications['last_name']; ?>"><img src="<?php //echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" /></a>-->
							&nbsp;
							<a href="index.php?Option=Setup&SubOption=empLeaveSetup&ofaction=update&id=<?php echo $dsqlerow['emp_id'];?><?php echo $additional_link_redirect; ?>" ><img src="<?php echo SITE_URL;?>/images/icon-edit.png" style="width:25px; height:20px;" /></a>
						
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
	<table width="100%">
		<tr>
			<td colspan="100%">
				<?php echo display_Admin_Paging_For_Admin($total_pages, $targetpage, $start, $end, $select_list_href); ?>
			</td>
		</tr>
	</table>