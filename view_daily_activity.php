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

require_once("".CLASS_PATH."/class.Activity.inc.php");
$objActivity = new Activity;

if(isset($_REQUEST['date']) && $_REQUEST['date'] != "")
{
	if($_REQUEST['userType'] == 'authorizer' || $_REQUEST['userType'] == 'superadmin' || $_REQUEST['userType'] == 'hr')
	{
		$rsDetail = $objActivity->getDailyActivities($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['date'], $_REQUEST['emp_id']);
	}	
	else
	{
		$rsDetail = $objActivity->getDailyActivities($_SESSION['userId'], $_REQUEST['userType'], $_REQUEST['date']);
	}	
	if($GLOBALS['obj_db']->num_rows($rsDetail) > 0)
	{
	?>
		<div style="text-align: center; font-weight: bold; margin-bottom: 20px;">Daily Activities</div>
		<table>
			<tr>
				<th colspan="4"></th>
				<th><button onclick="window.print();">Print</button></th>
			</tr>
		<tr>		
			<th>Activity</th>
			<th>Start Date/Time</th>
			<th>End Date/Time</th>
			<th>Category</th>
			<th>Status</th>
		</tr>
	<?php	
		while($rowDetail = $GLOBALS['obj_db']->fetch_array($rsDetail))
		{
			$fromDateObj = new DateTime($rowDetail['start_date_time']);
			$toDateObj = new DateTime($rowDetail['end_date_time']);
	?>
		<tr>
			<td width="30%"><?php echo $rowDetail['activity']; ?></td>
			<td width="20%">
				<?php echo ($rowDetail['start_date_time'] != "" && $rowDetail['start_date_time'] != "0000-00-00 00:00:00") ? $fromDateObj->format("d-m-Y H:i") : '' ; ?>
			</td>
			<td width="20%">
				<?php echo ($rowDetail['end_date_time'] != "" && $rowDetail['end_date_time'] != "0000-00-00 00:00:00") ? $toDateObj->format("d-m-Y H:i") : '' ; ?>
			</td>
			<td width="15%"><?php echo $rowDetail['category']; ?></td>
			<td width="15%"><?php echo ucfirst($rowDetail['approve_status_HOD1']); ?></td>
		</tr>		
	<?php
		}
	?>
		</table>
	<?php	
	}	
}