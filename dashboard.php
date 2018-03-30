<?php
if($_SESSION['userType'] == 'finance' || $_SESSION['userType'] == 'hr' || $_SESSION['userType'] == 'superadmin')
{
	echo "<script type='text/javascript'>window.location.href = 'index.php?Option=authorizerDashboard'; </script>";
	exit();
	
}
require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.Leaves.inc.php");
$objLeaves = new Leaves;

require_once("".CLASS_PATH."/class.Tour.inc.php");
$objTour = new Tour;

require_once("".CLASS_PATH."/class.Activity.inc.php");
$objActivity = new Activity;

require_once("".CLASS_PATH."/class.LocalConveyance.inc.php");
$objLocalConveyance = new LocalConveyance;

require_once("".CLASS_PATH."/class.DailyAllowance.inc.php");
$objDailyAllowance = new DailyAllowance;

$isAuthorizer = false;
$rsEmp = $objEmployee->getEmployeeData($_SESSION['userId']);
if($GLOBALS['obj_db']->num_rows($rsEmp))
{
	$rowEmp = $GLOBALS['obj_db']->fetch_array($rsEmp);
	if($rowEmp['emp_category'] != 'originator')
	{
		$isAuthorizer = true;
	}	
}	
?>
<div class="col-md-6 col-xs-12">
	<div class="widget stacked">
		<table class="table table-bordered table-hover table-striped">
				<tr>
                    <th colspan="2" style="text-align:center;">Your Daily Activities</th>
                  </tr>
				<?php
				$activityStatus = $objActivity->getActivityStatus($_SESSION['userId']);
				if(!empty($activityStatus['activityStatus']))
				{
					foreach($activityStatus['activityStatus'] as $status => $count)
					{
				?>
						<tr>		
							<td><?php echo ucwords($status); ?></td>
							<td><?php echo $count; ?></td>
						</tr>	       	
				<?php		
					}
				}	
				?>					
					<tr>
						<th colspan="2" style="text-align:center;"><a href="index.php?Option=Activity&SubOption=dailyActivity">Create New Daily Activity</a></th>
                  </tr>
		    </table>
			<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th colspan="2" style="text-align:center;">Your Leaves</th>
                  </tr>
					<?php
						$leaveStatus = $objLeaves->getLeavesStatus($_SESSION['userId']);
						if(!empty($leaveStatus['leaveStatus']))
						{
							foreach($leaveStatus['leaveStatus'] as $status => $count)
							{
						?>
								<tr>		
									<td><?php echo ucwords($status); ?></td>
									<td><?php echo $count; ?></td>
								</tr>	       	
						<?php		
							}
						?>
								<tr>						
									<td>All Leaves</td>
									<td><?php echo $leaveStatus['total']; ?></td>
								</tr>	
						<?php	
						}
						else
						{
						?>
							<tr>				
								<td colspan="2">No Leave Record Found</td>
							</tr>	
						<?php	
						}		
						?>					
					<tr>
						<th colspan="2" style="text-align:center;"><a href="index.php?Option=Leaves&SubOption=leaveApplication">Create Leave Application</a></th>
                  </tr>
		    </table>
			<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th colspan="2" style="text-align:center;">Your Tour Requests</th>
                  </tr>
				  <?php
						$tourStatus = $objTour->getTourStatus($_SESSION['userId']);
						if(!empty($tourStatus['tourStatus']))
						{
							foreach($tourStatus['tourStatus'] as $status => $count)
							{
						?>
								<tr>		
									<td><?php echo ucwords($status); ?></td>
									<td><?php echo $count; ?></td>
								</tr>	       	
						<?php		
							}
						?>
								<tr>						
									<td>All Tour Requests</td>
									<td><?php echo $tourStatus['total']; ?></td>
								</tr>
						<?php	
						}
						else
						{
						?>
							<tr>						
								<td colspan="2">No Tour Record Found</td>								
							</tr>
						<?php	
						}		
						?>
					
					<tr>
						<th colspan="2" style="text-align:center;"><a href="index.php?Option=Tour&SubOption=createTourRequest">Create Tour Request</a></th>
                  </tr>
		    </table>
	</div>
</div>
<div class="col-md-6 col-xs-12">
	<div class="widget stacked">
		<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th colspan="2" style="text-align:center;">Your Local Conveyance Requests</th>
                  </tr>
				  <?php
						$conveyanceStatus = $objLocalConveyance->getLocalConveyanceStatus($_SESSION['userId']);
						if(!empty($conveyanceStatus['conveyanceStatus']))
						{
							foreach($conveyanceStatus['conveyanceStatus'] as $status => $count)
							{
						?>
								<tr>		
									<td><?php echo ucwords($status); ?></td>
									<td><?php echo $count; ?></td>
								</tr>	       	
						<?php		
							}
						?>
								<tr>						
									<td>All Requests</td>
									<td><?php echo $conveyanceStatus['total']; ?></td>
								</tr>
						<?php	
						}
						else
						{
						?>
							<tr>						
								<td colspan="2">No Local Conveyance Record Found</td>								
							</tr>
						<?php	
						}		
						?>
					
					<tr>
						<th colspan="2" style="text-align:center;"><a href="index.php?Option=Tour&SubOption=localConveyance">Create Local Conveyance Request</a></th>
                  </tr>
		    </table>
			<table class="table table-bordered table-hover table-striped">
				  <tr>
                    <th colspan="2" style="text-align:center;">Your Daily Allowance Requests</th>
                  </tr>
				  <?php
						$daStatus = $objDailyAllowance->getDAStatus($_SESSION['userId']);
						if(!empty($daStatus['daStatus']))
						{
							foreach($daStatus['daStatus'] as $status => $count)
							{
						?>
								<tr>		
									<td><?php echo ucwords($status); ?></td>
									<td><?php echo $count; ?></td>
								</tr>	       	
						<?php		
							}
						?>
								<tr>						
									<td>All Daily Allowance Requests</td>
									<td><?php echo $daStatus['total']; ?></td>
								</tr>
						<?php	
						}
						else
						{
						?>
							<tr>						
								<td colspan="2">No Daily Allowance Record Found</td>								
							</tr>
						<?php	
						}		
						?>
		    </table>
	</div>
	<?php
	if($isAuthorizer)
	{
	?>
		<div class="col-md-12">
			<p><a href="index.php?Option=authorizerDashboard">Switch to Authorizer's Dashboard</a></p>
		</div>
	<?php	
	}	
	?>	
</div>