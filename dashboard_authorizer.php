<?php
require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.AdminUser.inc.php");
$adminUser = new AdminUser;

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

if($_SESSION['userType'] == 'finance' || $_SESSION['userType'] == 'hr')
{
	$rsEmp = $adminUser->getUserData($_SESSION['userId']);
}
else
{
    $rsEmp = $objEmployee->getEmployeeData($_SESSION['userId']);	
}	

if($GLOBALS['obj_db']->num_rows($rsEmp))
{
	$rowEmp = $GLOBALS['obj_db']->fetch_array($rsEmp);
	if($rowEmp['emp_category'] == 'originator')
	{
	?>
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissable"><strong>You are not authorized to view this section.</strong></div>
		</div>
	<?php	
	}
	else
	{
	?>
		<div class="col-md-6 col-xs-12">
			<div class="widget stacked">
				<?php
					if($_SESSION['userType'] != 'finance')
					{
				?>
				<table class="table table-bordered table-hover table-striped">
						<tr>
							<th colspan="2" style="text-align:center;">Daily Activities</th>
						  </tr>
						<?php
						$activityStatus = $objActivity->getActivityStatusAuthorizer($_SESSION['userId'], $_SESSION['userType']);
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
								<th colspan="2" style="text-align:center;"><a href="index.php?Option=Activity&SubOption=dailyActivityRequests">See Daily Activity Requests</a></th>
						  </tr>
					</table>
					<table class="table table-bordered table-hover table-striped">
						  <tr>
							<th colspan="2" style="text-align:center;">Leaves</th>
						  </tr>
							<?php
								$leaveStatus = $objLeaves->getLeavesStatusAuthorizer($_SESSION['userId'], $_SESSION['userType']);
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
										<td colspan="2" style="text-align:center;">No Leave Record Found</td>
									</tr>
								<?php
								}	
								?>	
							
							<tr>
								<th colspan="2" style="text-align:center;"><a href="index.php?Option=Leaves&SubOption=leaveRequests">See Leave Applications</a></th>
						  </tr>
					</table>
					<?php
					}
					?>
					<table class="table table-bordered table-hover table-striped">
						  <tr>
							<th colspan="2" style="text-align:center;">Tour Requests</th>
						  </tr>
						  <?php
								$tourStatus = $objTour->getTourStatusAuthorizer($_SESSION['userId'], $_SESSION['userType']);
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
										<td colspan="2" style="text-align:center;">No Tour Record Found</td>
									</tr>
								<?php	
								}		
								?>							
							<tr>
								<th colspan="2" style="text-align:center;"><a href="index.php?Option=Tour&SubOption=tourRequests">See Tour Requests</a></th>
						  </tr>
					</table>
			</div>
		</div>
		<div class="col-md-6 col-xs-12">
			<div class="widget stacked">
				<table class="table table-bordered table-hover table-striped">
						  <tr>
							<th colspan="2" style="text-align:center;">Local Conveyance Requests</th>
						  </tr>
						  <?php
								$conveyanceStatus = $objLocalConveyance->getLocalConveyanceStatusAuthorizer($_SESSION['userId'], $_SESSION['userType']);
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
										<td colspan="2" style="text-align:center;">No Local Conveyance Record Found</td>								
									</tr>
								<?php	
								}		
								?>							
							<tr>
								<th colspan="2" style="text-align:center;"><a href="index.php?Option=Tour&SubOption=localConveyanceRequests">See Local Conveyance Requests</a></th>
						  </tr>
					</table>
					<table class="table table-bordered table-hover table-striped">
						  <tr>
							<th colspan="2" style="text-align:center;">Daily Allowance Requests</th>
						  </tr>
						  <?php
								$daStatus = $objDailyAllowance->getDAStatusAuthorizer($_SESSION['userId'], $_SESSION['userType']);
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
										<td colspan="2" style="text-align:center;">No DA Record Found</td>
									</tr>
								<?php	
								}		
								?>							
							<tr>
								<th colspan="2" style="text-align:center;"><a href="index.php?Option=Tour&SubOption=tourRequests">See Daily Allowance Requests</a></th>
						  </tr>
					</table>
			</div>
			<?php
			if($_SESSION['userType'] != 'finance' && $_SESSION['userType'] != 'hr' && $_SESSION['userType'] != 'superadmin')
			{
			?>
				<div class="col-md-12">
					<p><a href="index.php">Switch to Own Dashboard</a></p>
				</div>
			<?php	
			}	
			?>
		</div>
	<?php	
	}
}
else
{
?>
	<div class="col-md-12">
			<div class="alert alert-danger alert-dismissable"><strong>No user found.</strong></div>
	</div>
<?php	
}	
?>