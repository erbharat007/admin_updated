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

if($_REQUEST['ofaction']=="update")
{
    $rs_content = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], 'originator', $_REQUEST['id']);
	$result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "addsave":
		$reArrangedFilesArray = reArrayFiles($_FILES['userFile']);
		$attachments = array();
		
		foreach($_POST['comments'] as $index => $comments)
		{
			if($comments != '')
			{
				$attachments[] = array('comments' => $comments, 'attachment' => $reArrangedFilesArray[$index], 'imageDeleted' => $_POST['image_deleted'][$index]);
			}
		}
		
		$arrayData = array($_SESSION['userId'], $_REQUEST['type'], $_REQUEST['date'], $_REQUEST['from'], $_REQUEST['to'], $_REQUEST['paid_by'], $_REQUEST['purpose'], $_REQUEST['travel_mode'], $_REQUEST['amount'], $_REQUEST['start_meter_reading'], $_REQUEST['end_meter_reading'], $_REQUEST['total_kms'], $attachments);
		
		$_SESSION['msg'] = $objLocalConveyance->createRequest($arrayData);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=localConveyance'; </script>";
		exit();
	break;

    case "updatesave":
		$reArrangedFilesArray = reArrayFiles($_FILES['userFile']);
		if(!empty($_FILES['userFileNew']))
		{
			$reArrangedFilesArrayNew = reArrayFiles($_FILES['userFileNew']);
		}	
		
		$attachments = array();
		$attachmentsNew = array();
		
		foreach($_POST['comments'] as $index => $comments)
		{
			if($comments != '')
			{
				$attachments[$index] = array('comments' => $comments, 'attachment' => $reArrangedFilesArray[$index], 'imageDeleted' => $_POST['image_deleted'][$index]);
			}
		}
		foreach($_POST['commentsNew'] as $index => $comments)
		{
			if($comments != '')
			{
				$attachmentsNew[] = array('comments' => $comments, 'attachment' => $reArrangedFilesArrayNew[$index], 'imageDeleted' => $_POST['image_deletedNew'][$index]);
			}
		}
		/*
		echo '<pre>';
		print_r($attachments);
		echo '<pre>';
		print_r($attachmentsNew);die;
		*/
		$arrayData = array($_REQUEST['id'], $_SESSION['userId'], $_REQUEST['type'], $_REQUEST['date'], $_REQUEST['from'], $_REQUEST['to'], $_REQUEST['paid_by'], $_REQUEST['purpose'], $_REQUEST['travel_mode'], $_REQUEST['amount'], $_REQUEST['start_meter_reading'], $_REQUEST['end_meter_reading'], $_REQUEST['total_kms'], $attachments, $attachmentsNew);
		
		$_SESSION['msg'] = $objLocalConveyance->updateRequest($arrayData);	
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=localConveyance'; </script>";
		exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objLocalConveyance->deleteRequest($_REQUEST['id'], $_SESSION['userId']);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Tour&SubOption=localConveyance'; </script>";
		exit();
	break;
}
$message = $_SESSION['msg'];
?>
<div class="page-header">
	<?php
    if($_REQUEST['ofaction']=="update")
    {
    ?>
    <h3>Local Conveyance Request/Reimbursement Form</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Local Conveyance Request/Reimbursement Form</h3>
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
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Tour&SubOption=localConveyance&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-12" role="form" onsubmit="return checkDataConveyance();">
		<input type="hidden" value="<?php echo $result_content['id']; ?>" name="id" id="id">
		<div class="table-responsive">
			<?php
			$rsUser = $objEmployee->getEmployeeData($_SESSION['userId']);
			$rowUser = $GLOBALS['obj_db']->fetch_array($rsUser);
			?>
			
			<span>
				<label class="col-md-3">Name</label>
				<label class="col-md-3"><?php echo $rowUser['first_name']." ".$rowUser['last_name']; ?></label>
			</span>
			<span>
				<label class="col-md-3" for="select1">Designation </label>
				<label class="col-md-3"><?php echo $rowUser['designation']; ?></label>
			</span>
			<span>
				<label class="col-md-3" for="select1">Department </label>				
				<label class="col-md-3"><?php echo $rowUser['department']; ?></label>
			</span>
			<span>
				<label class="col-md-3" for="select1">Request Date </label>				
				<label class="col-md-3"><?php echo date("d-m-Y"); ?></label>
			</span>
			<table class="table table-bordered table-hover table-striped">
                <thead>
				  <tr>
                    <th colspan="7">Tour Schedule</th>
                  </tr>
                  <tr>					
                    <th>Type</th>
					<th>Date</th>
					<th>From</th>
                    <th>To</th>
					<th>Paid by</th>					
				  </tr>
                </thead>
				<tbody>
					<tr>							
                       <?php		
							$dateObj = new DateTime($result_content['date']);
						?>
							<td>
								<select name="type" id="type" class="form-control">
									<option value="-1">-- Select -- </option>
									<option value="Advance" <?php echo ($result_content['type'] == 'Advance') ? 'selected="selected"' : ''; ?>>Advance Payment</option>
									<option value="Reimbursement" <?php echo ($result_content['type'] == 'Reimbursement') ? 'selected="selected"' : ''; ?>>Reimbursement</option>									
								</select>
							</td>
							<td>							
								<input name="date" id="date" class="form-control date-picker" readonly="readonly" value="<?php echo ($result_content['date'] != '' && $result_content['date'] != '0000-00-00') ? $dateObj->format('d/m/Y') : ''; ?>" required="required" />
							</td>
							<td>
								 <input type="text" name="from" id="from" class="form-control" value="<?php echo $result_content['from']; ?>" required="required" />
							</td>
							<td>
								 <input type="text" name="to" id="to" class="form-control" value="<?php echo $result_content['to']; ?>" required="required" />
							</td>							
							<td>
								<select name="paid_by" id="paid_by" class="form-control">
									<option value="-1">-- Select -- </option>
									<option value="Company" <?php echo ($result_content['paid_by'] == 'Company') ? 'selected="selected"' : ''; ?>>Company</option>
									<option value="Self" <?php echo ($result_content['paid_by'] == 'Self') ? 'selected="selected"' : ''; ?>>Self</option>									
								</select>								
							</td>                        	
						</tr>						
		       </tbody>
		    </table>
			
			<table class="table table-bordered table-hover table-striped">
                <thead>
				  <tr>					
                    <th width="20%">Meter Reading (Start)</th>
					<th width="20%">Meter Reading (End)</th>
					<th width="20%">Total Kms.</th>
					<th width="40%">Per Kms. Charges</th>
				  </tr>
                </thead>
				<tbody>
					<tr>							
							<td>
								<input type="text" name="start_meter_reading" id="start_meter_reading" class="form-control" value="<?php echo $result_content['start_meter_reading']; ?>" />
							</td>
							<td>							
								<input type="text" name="end_meter_reading" id="end_meter_reading" class="form-control" value="<?php echo $result_content['end_meter_reading']; ?>" />
							</td>
							<td>
								<span id="total_kms"><?php echo $result_content['total_kms']; ?></span>
								 <input type="hidden" name="total_kms" class="form-control" value="<?php echo $result_content['total_kms']; ?>" />
							</td>							
							<td>
							<strong>Car: </strong> Rs. 6.00/- <br />
							<strong>Bike/Scooter: </strong> Rs. 3.00/-
							</td>
						</tr>						
		       </tbody>
		    </table>
			
			<table class="table table-bordered table-hover table-striped">
                <thead>				  
                  <tr>					
                    <th>Purpose</th>					
					<th>Travel Mode</th>
					<th>Amount</th>				    
				  </tr>
                </thead>
				<tbody>
					<tr>                       
							<td>
								<textarea name="purpose" id="purpose" rows="4" cols="40" class="form-control" required="required"><?php echo $result_content['purpose']; ?></textarea>
							</td>
							<td>						
								<input type="text" name="travel_mode" id="travel_mode" class="form-control" value="<?php echo $result_content['travel_mode']; ?>" required="required" />								
							</td>
							<td>
								 <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $result_content['amount']; ?>" required="required" />
							</td>							
						</tr>						
		       </tbody>
		    </table>
			
			<div class="col-md-12 more-field-wrapper">
            	<div class="row remark-section">
					<div class="col-md-4">
						<label>Comments</label>
					</div>
					<div class="col-md-4">
						<label>Attachment</label>
					</div>
					<div class="col-md-2">						
					</div>
					<div class="col-md-2">						
					</div>
				</div>
				<?php
                if($_REQUEST['ofaction']=="update")
                {
					$rsAttachments = $objLocalConveyance->getAttachments($_SESSION['userId'], 'originator', $_REQUEST['id']);
					if($GLOBALS['obj_db']->num_rows($rsAttachments) > 0)
					{
						while($rowAttach = $GLOBALS['obj_db']->fetch_array($rsAttachments))
						{
				?>
                			<div class="row remark-section">
								<div class="col-md-4">
									<textarea name="comments[<?php echo $rowAttach['id']; ?>]" rows="5" cols="60" class="form-control"><?php echo $rowAttach['comments']; ?></textarea>
								</div>
								<div class="col-md-4 imagePreviewSection">
									<p>
									<?php
									$attachmentImage = "";
									if($rowAttach['attachment'] != '')
									{
										$attachmentPath = "user_uploads/emp_".$_SESSION['userId']."/".$rowAttach['attachment'];
										if(file_exists($attachmentPath))
										{
											$attachmentImage = $attachmentPath;
										}	
									}
									?>
									</p>
									<div class="imagePreview" style="background-image: url(<?php echo $attachmentImage; ?>)"></div>
									<input type="file" name="userFile[<?php echo $rowAttach['id']; ?>]" class="uploadFile" />
								</div>
								<div class="col-md-2 delete-img-div <?php echo ($attachmentImage == "") ? "hide" : ""; ?>">
									<a href="javascript:void(0);" class="delete_image" title="Delete Image">Delete Image</a>
									<input type="hidden" name="image_deleted[<?php echo $rowAttach['id']; ?>]" value="0" />
								</div>
								<div class="col-md-2">						
								</div>
							</div>	 
                <?php			
						}				
					}               
                }
                else
                {
                ?>
                   <div class="row remark-section">
                        <div class="col-md-4">
                            <textarea name="comments[]" rows="5" cols="60" class="form-control"></textarea>
                        </div>
                        <div class="col-md-4 imagePreviewSection">
							<div class="imagePreview"></div>
                            <input type="file" name="userFile[]" class="uploadFile" />
                        </div>
						<div class="col-md-2 delete-img-div hide">
							<a href="javascript:void(0);" class="delete_image" title="Delete Image">Delete Image</a>
							<input type="hidden" name="image_deleted[]" value="0" />
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>	 
                <?php	
                }
                ?>
			</div>
			
			<div class="col-md-12" style="clear:both;">
				<div class="row">
					<a href="javascript:void(0);" class="add_more_field" title="Add field" data-action="<?php echo ($_REQUEST['ofaction']=="update") ? 'update' : 'add'; ?>">Add more row</a>
				</div>				
			</div>
			
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
		$dsqle = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], 'originator');
		$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
		?>
		
		<div class="col-md-12" style="clear:both;">
			<p>Previous Conveyance Requests</p>
		</div>	
		
		<form action="index.php?Option=Tour&SubOption=localConveyance" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
				    <th>Sr.No.</th>
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

				$targetpage = "index.php?Option=Tour&SubOption=localConveyance";

				$rsRequest = $objLocalConveyance->getLocalConveyanceRequests($_SESSION['userId'], 'originator', 0, $start, $end);
				if($GLOBALS['obj_db']->num_rows($rsRequest))
				{	
					$i = 1;
					while($rowRequest = $GLOBALS['obj_db']->fetch_array($rsRequest))
					{
						$dateObj = new DateTime($rowRequest['date']);
					?>
					<tr>
						<td><?php echo $i; ?></td>
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
							<a href="./view_conveyance_request.php?conveyanceId=<?php echo $rowRequest['id']; ?>&userType=originator" class="iframe_fancybox" title="Local Conveyance Details">
								<img src="<?php echo SITE_URL;?>/images/icon-search.png" style="width:20px; height:20px;" />
							</a>							
                            &nbsp;
						<?php
						if($rowRequest['approve_status_HOD1'] == 'pending' && $rowRequest['approve_status_HOD2'] == 'pending')
						{
						?>	
                            <a href="index.php?Option=Tour&SubOption=localConveyance&ofaction=update&id=<?php echo $rowRequest['id']; ?><?php echo $additional_link_redirect; ?>" title="Edit"><img src="<?php echo SITE_URL;?>/images/icon-edit.png" style="width:25px; height:20px;" /></a>
                            &nbsp;
                            <a href="index.php?Option=Tour&SubOption=localConveyance&ofaction=deleteof&id=<?php echo $rowRequest['id']; ?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();" title="Delete"><img src="<?php echo SITE_URL;?>/images/icon-delete.png" style="width:23px; height:25px;" /></a>
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