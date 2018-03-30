<?php
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once("".CLASS_PATH."/class.Department.inc.php");
$objDept = new Department;

require_once("".CLASS_PATH."/class.Designation.inc.php");
$objDesignation = new Designation;

require_once("".CLASS_PATH."/class.Branch.inc.php");
$objBranch = new Branch;

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
    case "addsave":
		$arrayData = array($_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['designation'], $_REQUEST['department'], $_REQUEST['email'], $_REQUEST['password'], $_REQUEST['home_address'], $_REQUEST['m_phone'], $_REQUEST['emp_code'], $_REQUEST['status'], $_REQUEST['emp_category'], $_REQUEST['dob'], $_REQUEST['pan_number'], $_REQUEST['passport_number'], $_REQUEST['hod_1_name'], $_REQUEST['hod_2_name'], $_REQUEST['hr_name'], $_REQUEST['branch'], $_REQUEST['bank_name'], $_REQUEST['bank_ac_number'], $_REQUEST['bank_ifsc_Code'], $_REQUEST['bank_branch_address'], $_REQUEST['joining_date']);
		
		$_SESSION['msg']=$objEmployee->add($arrayData);
		//header("Location: index.php?Option=Registration&SubOption=employee");
		//exit();
	break;

    case "updatesave":
		$arrayData = array($_REQUEST['id'], $_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['designation'], $_REQUEST['department'], $_REQUEST['email'], $_REQUEST['password'], $_REQUEST['home_address'], $_REQUEST['m_phone'], $_REQUEST['emp_code'], $_REQUEST['status'], $_REQUEST['emp_category'], $_REQUEST['dob'], $_REQUEST['pan_number'], $_REQUEST['passport_number'], $_REQUEST['hod_1_name'], $_REQUEST['hod_2_name'], $_REQUEST['hr_name'], $_REQUEST['branch'], $_REQUEST['bank_name'], $_REQUEST['bank_ac_number'], $_REQUEST['bank_ifsc_Code'], $_REQUEST['bank_branch_address'], $_REQUEST['joining_date']);
		
		$_SESSION['msg'] = $objEmployee->update($arrayData);
		//header("Location: index.php?Option=Registration&SubOption=employee".$additional_link_redirect);
		//exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objEmployee->delete($_REQUEST['id']);
		//header("Location: index.php?Option=Registration&SubOption=employee".$additional_link_redirect);
		//exit();
	break;
}
$message = $_SESSION['msg'];
?>

<script language=javascript>
$(function() 
{
	// Add minus icon for collapse element which is open by default
        $(".collapse.in").each(function(){
        	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
        });
        
        // Toggle plus minus icon on show hide of collapse element
        $(".collapse").on('show.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hide.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });
	
	$('#dob, #joining_date').datepicker({		
								dateFormat: 'dd/mm/yy',
								buttonImage: 'images/calc.png',
								buttonImageOnly: true,
								//buttonText:'Select your Date of Birth',
								changeMonth: true,
								changeYear: true,
								yearRange:"1920:2017"
							});	
});
function checkData() 
{
    var error = "";
	
	if($.trim($('#designation').val()) == -1)
	{
		error = "Please select designation";
		alert(error);
		$('#designation').focus();
		return false;
	}
	if($.trim($('#department').val()) == -1)
	{
		error = "Please select department";
		alert(error);
		$('#department').focus();
		return false;
	}
	if($.trim($('#branch').val()) == -1)
	{
		error = "Please select branch";
		alert(error);
		$('#branch').focus();
		return false;
	}
	if($.trim($('#dob').val()) == '')
	{
		error = "Please select date of birth";
		alert(error);
		$('#dob').focus();
		return false;
	}
	
	if($.trim($('#joining_date').val()) == '')
	{
		error = "Please select date of joining";
		alert(error);
		$('#joining_date').focus();
		return false;
	}
	if($.trim($('#hod_1_name').val()) == -1)
	{
		error = "Please select reporting head 1 name";
		alert(error);
		$('#hod_1_name').focus();
		return false;
	}
	if($.trim($('#hr_name').val()) == -1)
	{
		error = "Please select reporting HR name";
		alert(error);
		$('#hr_name').focus();
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
    <h3>Edit Registered User</h3>
    <?php
    }
    else
    {
    ?>
    <h3>New User Registration</h3>
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
<div class="bs-example">
    <div class="panel-group" id="accordion">
        <div class="panel panel-default  accordion-color-1">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#employee-data-section"><span class="glyphicon glyphicon-plus"></span> Employee Data</a>
                </h4>
            </div>
            <div id="employee-data-section" class="panel-collapse collapse in">
                <div class="panel-body">
                    <div class="tab-section" id="form-tab-3">
			<div class="formsection">
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>First Name <span>*</span></h5></div>
						        <div class="col-sm-8">
									<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Last Name <span>*</span></h5></div>
						        <div class="col-sm-8">
									<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Reemployed  <span>*</span></h5></div>
						        <div class="col-sm-8">
									<div class="form-group">
										<div class="row">
											<div class="col-xs-3"><input type="radio" name="reemployed"> &nbsp;<span>Yes</span></div>
											<div class="col-xs-6"><input type="radio" class="reemployed" name=""> &nbsp;<span>No</span></div>
										</div>
									</div>
						        </div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Old Emp. Code <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <input name="old_emp_code" type="text" id="old_emp_code" maxlength="50" class="form-control" value="<?php echo $result_content['old_emp_code']; ?>" required>
						        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Designation  <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <select name="designation" id="designation" class="form-control">
										<option value="-1">--- Select Designation--- </option>
										<?php
										$rsDesignation = $objDesignation->getAll('', '', false);
										if($GLOBALS['obj_db']->num_rows($rsDesignation))
										{
											while($rowDesignation = $GLOBALS['obj_db']->fetch_array($rsDesignation))
											{
										?>
												<option value="<?php echo $rowDesignation['id']; ?>" <?php echo ($result_content['designation_id'] == $rowDesignation['id']) ? 'selected="selected"' : ''; ?>><?php echo $rowDesignation['designation']; ?></option>
										<?php	
											}	
										}	
										?>
										 </select>	
						        </div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Department <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <select name="department" id="department" class="form-control">
										<option value="-1">--- Select Department--- </option>
										<?php
										$rsDept = $objDept->getAll('', '', false);
										if($GLOBALS['obj_db']->num_rows($rsDept))
										{
											while($rowDept = $GLOBALS['obj_db']->fetch_array($rsDept))
											{
										?>
												<option value="<?php echo $rowDept['dept_id']; ?>" <?php echo ($result_content['department_id'] == $rowDept['dept_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowDept['dept_name']; ?></option>
										<?php	
											}	
										}	
										?>
										 </select>
						        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Employee Category <span>*</span></h5></div>
						        <div class="col-sm-8">									
									<div class="form-group">
										<div class="row">
											<div class="col-xs-3"><input type="radio" name="emp_category" value="stationary"> &nbsp;<span>Stationary</span></div>
											<div class="col-xs-6"><input type="radio" name="emp_category" value="remote"> &nbsp;<span>Remote</span></div>
										</div>
									</div>									
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Password <span>*</span></h5></div>
						        <div class="col-sm-8">
						        <input name="password" type="password" id="password" value="<?php echo ($_REQUEST['ofaction']=="update") ? DUMMY_PASS : ''; ?>" class="form-control" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid password. It must contain at least one number, one uppercase, one lowercase letter and at least of 8 or more characters.')" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Your password must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required="required" >
						        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Father's Name <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text" name="emp_father_name" id="emp_father_name" class="form-control" /></div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Company Name <span>*</span></h5></div>
						        <div class="col-sm-8">
								<select name="emp_company_name" id="emp_company_name" class="form-control">
									<option value="-1">--- Select Company--- </option>
									<?php
									$rsDept = $objDept->getAll('', '', false);
									if($GLOBALS['obj_db']->num_rows($rsDept))
									{
										while($rowDept = $GLOBALS['obj_db']->fetch_array($rsDept))
										{
									?>
											<option value="<?php echo $rowDept['dept_id']; ?>" <?php echo ($result_content['department_id'] == $rowDept['dept_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowDept['dept_name']; ?></option>
									<?php	
										}	
									}	
									?>
									 </select>									
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Employee Located (Branch) <span>*</span></h5></div>
						        <div class="col-sm-8">
									<select name="branch" id="branch" class="form-control">
									<option value="-1">--- Select Branch--- </option>
									<?php
									$rsBranch = $objBranch->getAll('', '', false);
									if($GLOBALS['obj_db']->num_rows($rsBranch))
									{
										while($rowBranch = $GLOBALS['obj_db']->fetch_array($rsBranch))
										{
									?>
											<option value="<?php echo $rowBranch['id']; ?>" <?php echo ($result_content['branch_id'] == $rowBranch['id']) ? 'selected="selected"' : ''; ?>><?php echo $rowBranch['city_name']; ?></option>
									<?php	
										}	
									}	
									?>
								 </select>									
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Current Status <span>*</span></h5></div>
						        <div class="col-sm-8">
									<div class="form-group">
										<div class="row">
											<div class="col-xs-4">
												<input type="radio" value="active" name="status" <?php echo ($result_content['status'] == 'active') ? 'checked' : ''; ?>> &nbsp;<span>Active</span>
											</div>
											<div class="col-xs-8">
												<input type="radio" value="inactive" name="status" <?php echo ($result_content['status'] == 'inactive') ? 'checked' : ''; ?>>   &nbsp;<span>InActive</span>
											</div>
										</div>
									 </div>
								</div>
									</div>									  
						        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Status <span>*</span></h5></div>
						        <div class="col-sm-8">
								<div class="form-group">
                                <div class="row">
							 	<div class="col-xs-3"><input type="radio" name=""> &nbsp;<span>Yes</span></div>
							 	<div class="col-xs-6"><input type="radio" class="" name=""> &nbsp;<span>No</span></div>
							 	</div>
							 </div>
							</div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Employee Category <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <select name="" class="form-control" data-bv-message="Please select City" required class="form-control">
							  	           <option>Select</option>
							          </select>
						        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Date of Birth <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Date of Joining <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
				</div>
                <div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>PAN Number <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Passport Number <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Reporting Head 1 <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <select name="" class="form-control" data-bv-message="Please select City" required class="form-control">
							  	           <option>Select</option>
							          </select>
						        </div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Reporting Head 2  <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <select name="" class="form-control" data-bv-message="Please select City" required class="form-control">
							  	           <option>Select</option>
							          </select>
						        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>HR Head <span>*</span></h5></div>
						        <div class="col-sm-8">
                                      <select name="" class="form-control" data-bv-message="Please select City" required class="form-control">
							  	           <option>Select</option>
							          </select>
						        </div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12"><h4>Bank Details</h4></div>
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Bank Name <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>A/C Number <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>IFSC Code <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
					<div class="col-sm-5 col-sm-offset-1">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4"><h5>Branch Address <span>*</span></h5></div>
						        <div class="col-sm-8"><input type="text"  name="name" placeholder="Your Name*" class="form-control" /></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" style="text-align: center;">
						<div class="form-group">
							<div class="btnbox">
							 <input type="button" name="" class="btn add" value="Add">
							 <input type="button" name="" class="btn reset" value="Reset">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default accordion-color-2">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#personal-data-section"><span class="glyphicon glyphicon-plus"></span> Personal Data</a>
                </h4>
            </div>
            <div id="personal-data-section" class="panel-collapse collapse">
                <div class="panel-body">
					<div class="formsection">
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Present Address <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Permanent Address <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Personal Email ID <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Passport No. <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Passport Issue Date <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Passport Valid Till <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Passport Issue Place <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Driving License No. <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Driving License Valid Till <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Driving License Issue Place <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Marital Status <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Blood Group <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Nationality <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Emergency Contact Number <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default accordion-color-3">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#salary-data-section"><span class="glyphicon glyphicon-plus"></span> Salary Data</a>
                </h4>
            </div>
            <div id="salary-data-section" class="panel-collapse collapse">
                <div class="panel-body">
					<div class="formsection">
					<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Basic Salary <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>HRA <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Daily Allowance <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Medical Allowance <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Other Allowance <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Statuary Bonus  <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Travel Allowance <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Gross Salary <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Employer Provident Fund <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Employer ESIC <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>CTC <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Employee SIC <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Employee PF <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>LWF <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>NETPAY <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							
						</div>					
						
					
                   </div> 
                </div>
            </div>
        </div>
		
		<div class="panel panel-default accordion-color-4">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#authorization-data-section"><span class="glyphicon glyphicon-plus"></span> Set Up Authorizations </a>
                </h4>
            </div>
            <div id="authorization-data-section" class="panel-collapse collapse">
                <div class="panel-body">
                    <p>CSS stands for Cascading Style Sheet. CSS allows you to specify various style properties for a given HTML element such as colors, backgrounds, fonts etc. <a href="https://www.tutorialrepublic.com/css-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div>
		
		<div class="panel panel-default accordion-color-5">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#file-data-section"><span class="glyphicon glyphicon-plus"></span> Attach Employee File</a>
                </h4>
            </div>
            <div id="file-data-section" class="panel-collapse collapse">
                <div class="panel-body">
					<div class="formsection">
					<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Employment Form <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Resume <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Passport <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Driving License <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Previous Company Relieving Letter <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Current Offer Letter <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Previous Company Salary Sheet <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Aadhar Card <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<div class="row">
										<div class="col-sm-4"><h5>Service Bond <span>*</span></h5></div>
										<div class="col-sm-8">
											<input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>	
										</div>
									</div>
								</div>
							</div>
							
						</div>
						
					</div>	
                </div>
            </div>
        </div>
    </div>

    <div class="datasection">
    	<h4>QUICK SEARCH</h4>
    	<?php
	$dsqle = $objEmployee->getAllEmployees();
	$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	  <form name="search_form" method="post" action="index.php?Option=Registration&SubOption=employee">
        <div class="searchbox">
        	<div class="box">
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Name</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="searchbtn-1">
        				<div class="btnbox">
        				   <input type="submit" class="btn add" value="Search">
			               <input type="button" onclick="window.location.href='index.php?Option=Registration&SubOption=employee';" class="btn reset" value="Cancel" />
			            </div>
        			</div>
        		</div>
        	</div>
        </div>
        <div class="searchbox">
        	<div class="box">
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Name</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Department</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="searchbtn-1">
        				<div class="btnbox">
        				   <input type="submit" class="btn add" value="Search">
			               <input type="button" onclick="window.location.href='index.php?Option=Registration&SubOption=employee';" class="btn reset" value="Cancel" />
			            </div>
        			</div>
        		</div>
        	</div>
        </div>
        <div class="searchbox">
        	<div class="box">
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Name</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Department</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Designation</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        	</div>
        	<div class="box">
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Name</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Department</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Designation</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        	</div>
        	<div class="box">
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Name</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Department</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="form-group">
        			   <div class="row">
        				  <div class="col-sm-4"><h5>Designation</h5></div>
                          <div class="col-sm-8"><input class="form-control" type="" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>"></div>
        			   </div>
        			 </div>
        		</div>
        		<div class="col-sm-4">
        			<div class="searchbtn-2">
        				<div class="btnbox">
        				   <input type="submit" class="btn add" value="Search">
			               <input type="button" onclick="window.location.href='index.php?Option=Registration&SubOption=employee';" class="btn reset" value="Cancel" />
			            </div>
        			</div>
        		</div>
        	</div>
        </div>
     </form>
				<?php
	// $dsqle = $objEmployee->getAllEmployees();
	// $total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	
	<!-- <div class="col-md-12" style="clear:both;">
		<form name="search_form" method="post" action="index.php?Option=Registration&SubOption=employee">
			<input type="search" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>">
			<input type="submit" class="btn btn-info" value="Search">
			<input type="button" onclick="window.location.href='index.php?Option=Registration&SubOption=employee';" class="btn btn-danger" value="Cancel" />				
		</form>
	</div>	 -->
	
	<form action="index.php?Option=Registration&SubOption=employee" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>Employee Name</th>
					<th>Department</th>
					<th>Designation</th>
					<th>Status</th>
                    <th colspan="3">Action</th>
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

				$targetpage = "index.php?Option=Registration&SubOption=employee";

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
						<td><?php echo $dsqlerow['status']; ?></td>
						<td class="aligncenter">
							<a class="table-icon addicon" href="index.php?Option=Registration&SubOption=employee&ofaction=update&id=<?php echo $dsqlerow['emp_id'];?><?php echo $additional_link_redirect; ?>" ><i class="fa fa-pencil-square-o"></i></a>
                        </td>
                        <td  class="aligncenter">
							<a class="table-icon deleteicon" href="index.php?Option=Registration&SubOption=employee&ofaction=deleteof&id=<?php echo $dsqlerow['emp_id'];?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();"><i class="fa fa-remove"></i></a>
						</td>
						<td  class="aligncenter">
							<a class="table-icon viewicon" href="#"><i class="fa fa-search"></i></a>
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
			</div>
	
</div>

<div class="widget-content">
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Registration&SubOption=employee&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-6" role="form" onsubmit="return checkData();">
    	<input type="hidden" value="<?php echo $result_content['emp_id']; ?>" name="id" id="id">
        <div class="form-group">
            <label class="col-md-3">First Name <span style="color:red;">*</span></label>
            <div class="col-md-9">
            <input name="first_name" type="text" id="first_name" maxlength="50" class="form-control" value="<?php echo $result_content['first_name']; ?>" required autofocus>
            	
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Last Name <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="last_name" type="text" id="last_name" maxlength="50" class="form-control" value="<?php echo $result_content['last_name']; ?>" required>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Designation <span style="color:red;">*</span></label>
            <div class="col-md-9">
			<select name="designation" id="designation" class="form-control">
			<option value="-1">--- Select Designation--- </option>
			<?php
			$rsDesignation = $objDesignation->getAll('', '', false);
			if($GLOBALS['obj_db']->num_rows($rsDesignation))
			{
				while($rowDesignation = $GLOBALS['obj_db']->fetch_array($rsDesignation))
				{
			?>
					<option value="<?php echo $rowDesignation['id']; ?>" <?php echo ($result_content['designation_id'] == $rowDesignation['id']) ? 'selected="selected"' : ''; ?>><?php echo $rowDesignation['designation']; ?></option>
			<?php	
				}	
			}	
			?>
             </select>	
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Department <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<select name="department" id="department" class="form-control">
					<option value="-1">--- Select Department--- </option>
				<?php
				$rsDept = $objDept->getAll('', '', false);
				if($GLOBALS['obj_db']->num_rows($rsDept))
				{
					while($rowDept = $GLOBALS['obj_db']->fetch_array($rsDept))
					{
				?>
						<option value="<?php echo $rowDept['dept_id']; ?>" <?php echo ($result_content['department_id'] == $rowDept['dept_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowDept['dept_name']; ?></option>
				<?php	
					}	
				}	
				?>
				 </select>
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">E-mail ID <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="email" type="text" id="email" class="form-control" value="<?php echo $result_content['email']; ?>" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid e-mail address')" pattern="[a-zA-Z0-9_\.\-\+]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9]{2,4}$" required="required">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Password <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="password" type="password" id="password" value="<?php echo ($_REQUEST['ofaction']=="update") ? DUMMY_PASS : ''; ?>" class="form-control" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid password. It must contain at least one number, one uppercase, one lowercase letter and at least of 8 or more characters.')" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Your password must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required="required" >
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Home Address <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<textarea rows="6" class="form-control" name="home_address" id="home_address" required><?php echo $result_content['address']; ?></textarea>				
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Mobile Phone <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="m_phone" type="text" id="m_phone" class="form-control" value="<?php echo $result_content['phone']; ?>" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter valid mobile number. Only numeric values are allowed.')" pattern="[0-9]+" required>
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Employee Code <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="emp_code" type="text" id="emp_code" class="form-control" value="<?php echo $result_content['emp_code']; ?>" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter valid employee code. Only alphabets, digits and hyphen(-) are allowed.')" pattern="[a-zA-Z0-9-]+" required>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Employee Located <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<select name="branch" id="branch" class="form-control">
					<option value="-1">--- Select Branch--- </option>
					<?php
					$rsBranch = $objBranch->getAll('', '', false);
					if($GLOBALS['obj_db']->num_rows($rsBranch))
					{
						while($rowBranch = $GLOBALS['obj_db']->fetch_array($rsBranch))
						{
					?>
							<option value="<?php echo $rowBranch['id']; ?>" <?php echo ($result_content['branch_id'] == $rowBranch['id']) ? 'selected="selected"' : ''; ?>><?php echo $rowBranch['city_name']; ?></option>
					<?php	
						}	
					}	
					?>
				 </select>
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Status </label>
            <div class="col-md-9">
             	<input type="radio" value="active" name="status" <?php echo ($result_content['status'] == 'active') ? 'checked' : ''; ?>> Active &nbsp;&nbsp;&nbsp;
				<input type="radio" value="inactive" name="status" <?php echo ($result_content['status'] == 'inactive') ? 'checked' : ''; ?>> InActive 
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Employee Category</label>
            <div class="col-md-9">
             	<select name="emp_category" class="form-control" id="emp_category">
					<option value="originator" <?php echo ($result_content['emp_category'] == 'originator') ? 'selected="selected"' : ''; ?>>Originator</option>
					<option value="authorizer" <?php echo ($result_content['emp_category'] == 'authorizer') ? 'selected="selected"' : ''; ?>>Authorizer</option>
					<option value="both" <?php echo ($result_content['emp_category'] == 'both') ? 'selected="selected"' : ''; ?>>Both</option>
				</select>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Date of Birth <span style="color:red;">*</span></label>
            <div class="col-md-9">
			  <?php
			  if($result_content['birth_date'] != '' && $result_content['birth_date'] != '0000-00-00')
			  {
				  $dobObj = new DateTime($result_content['birth_date']);				  
			  }
			  ?>			
             	<input name="dob" type="text" id="dob" value="<?php echo ($result_content['birth_date'] != '' && $result_content['birth_date'] != '0000-00-00') ? $dobObj->format('d/m/Y') : ''; ?>" class="form-control date-picker" readonly="readonly">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Date of Joining <span style="color:red;">*</span></label>
            <div class="col-md-9">
			  <?php
			  if($result_content['joining_date'] != '' && $result_content['joining_date'] != '0000-00-00')
			  {
				  $joiningDateObj = new DateTime($result_content['joining_date']);				  
			  }
			  ?>			
             	<input name="joining_date" type="text" id="joining_date" value="<?php echo ($result_content['joining_date'] != '' && $result_content['joining_date'] != '0000-00-00') ? $joiningDateObj->format('d/m/Y') : ''; ?>" class="form-control date-picker" readonly="readonly">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">PAN Number</label>
            <div class="col-md-9">
             	<input name="pan_number" type="text" id="pan_number" class="form-control" value="<?php echo $result_content['pan_number']; ?>">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Passport Number</label>
            <div class="col-md-9">
             	<input name="passport_number" type="text" id="passport_number" class="form-control" value="<?php echo $result_content['passport_number']; ?>">
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-3" for="select1">Reporting Head 1 <span style="color:red;">*</span></label>
            <div class="col-md-9">
				<select name="hod_1_name" id="hod_1_name" class="form-control">
					<option value="-1">--- Select Hod 1--- </option>
					<?php
					$rsHod = $objEmployee->getReportingHeadsList();
					if($GLOBALS['obj_db']->num_rows($rsHod))
					{
						while($rowHod = $GLOBALS['obj_db']->fetch_array($rsHod))
						{
					?>
							<option value="<?php echo $rowHod['emp_id']; ?>" <?php echo ($result_content['HOD_1_id'] == $rowHod['emp_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowHod['first_name']." ".$rowHod['last_name']; ?></option>
					<?php	
						}	
					}	
					?>
				 </select>             	
            </div>
        </div> <!-- /.form-group -->
        <!--<div class="form-group ">
            <label class="col-md-3" for="select1">Reporting Head 1 (Email) <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="hod_1_email" type="text" id="hod_1_email" maxlength="50" value="" class="form-control" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid e-mail address')" pattern="[a-zA-Z0-9_\.\-\+]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9]{2,4}$" required="required">
            </div>
        </div> <!-- /.form-group --> 
		<div class="form-group ">
            <label class="col-md-3" for="select1">Reporting Head 2 </label>
            <div class="col-md-9">
				<select name="hod_2_name" id="hod_2_name" class="form-control">
					<option value="-1">--- Select Hod 2--- </option>
					<?php
					$rsHod = $objEmployee->getReportingHeadsList();
					if($GLOBALS['obj_db']->num_rows($rsHod))
					{
						while($rowHod = $GLOBALS['obj_db']->fetch_array($rsHod))
						{
					?>
							<option value="<?php echo $rowHod['emp_id']; ?>" <?php echo ($result_content['HOD_2_id'] == $rowHod['emp_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowHod['first_name']." ".$rowHod['last_name']; ?></option>
					<?php	
						}	
					}	
					?>
				 </select>             	
            </div>
        </div> <!-- /.form-group -->
		<!--<div class="form-group ">
            <label class="col-md-3" for="select1">Reporting Head 2 (Email)</label>
            <div class="col-md-9">
             	<input name="hod_2_email" type="text" id="hod_2_email" maxlength="50" value="" class="form-control">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">HR Head <span style="color:red;">*</span></label>
            <div class="col-md-9">
				<select name="hr_name" id="hr_name" class="form-control">
					<option value="-1">--- Select HR Head--- </option>
					<?php
					$rsHod = $objEmployee->getReportingHeadsList();
					if($GLOBALS['obj_db']->num_rows($rsHod))
					{
						while($rowHod = $GLOBALS['obj_db']->fetch_array($rsHod))
						{
					?>
							<option value="<?php echo $rowHod['emp_id']; ?>" <?php echo ($result_content['hr_id'] == $rowHod['emp_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowHod['first_name']." ".$rowHod['last_name']; ?></option>
					<?php	
						}	
					}	
					?>
				 </select>              	
            </div>
        </div> <!-- /.form-group -->
		<!--<div class="form-group ">
            <label class="col-md-3" for="select1">HR Head (Email) <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="hr_email" type="text" id="hr_email" maxlength="50" value="" class="form-control" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid e-mail address')" pattern="[a-zA-Z0-9_\.\-\+]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9]{2,4}$" required="required">
            </div>
        </div> <!-- /.form-group -->
		
		<div class="form-group ">
            <div class="col-md-12">
             	<p>Bank Details</p>
            </div>
        </div> <!-- /.form-group -->
		
		<div class="form-group ">
            <label class="col-md-3" for="select1">Bank Name</label>
            <div class="col-md-9">
             	<input name="bank_name" type="text" id="bank_name" class="form-control" value="<?php echo $result_content['bank_name']; ?>">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">A/C Number</label>
            <div class="col-md-9">
             	<input name="bank_ac_number" type="text" id="bank_ac_number" class="form-control" value="<?php echo $result_content['bank_ac_number']; ?>">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">IFSC Code</label>
            <div class="col-md-9">
             	<input name="bank_ifsc_Code" type="text" id="bank_ifsc_Code" class="form-control" value="<?php echo $result_content['bank_ifsc_Code']; ?>">
            </div>
        </div> <!-- /.form-group -->
		
		<div class="form-group ">
            <label class="col-md-3" for="select1">Branch Address</label>
            <div class="col-md-9">
			    <textarea rows="6" class="form-control" name="bank_branch_address" id="bank_branch_address"><?php echo $result_content['bank_branch_address']; ?></textarea>         
			</div>
        </div> <!-- /.form-group -->
		
        <div class="form-group">
            <div class="col-md-offset-4 col-md-8">
            	<input type="submit" name="Submit" class="btn btn-success" value="<?php if($_REQUEST['ofaction']=="update"){ echo "Update" ; } else { echo "Add"; } ?>">
                <input name="cmdReset" type="reset" class="btn btn-warning" id="cmdReset" value="Reset" >
            </div>
        </div> <!-- /.form-group -->
	</form>
</div>                               
           
   
	
