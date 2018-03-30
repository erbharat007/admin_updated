<?php
require_once("".CLASS_PATH."/class.ConsoleUser.inc.php");
$obj_consoleuser=new ConsoleUser;

require_once("".CLASS_PATH."/class.UserRoles.inc.php");
$userCardauthentication=new UserRoles;

switch($action)
{
	case "Update":
		$_SESSION['msg']=$obj_consoleuser->ChangePassword();
		break;
}

?>
<SCRIPT language=javascript>
function CheckUpdateValidation()
{
	flag1=0;
	flag=0;
	if(!chkBlank(document.frmEdit.txtOldPassword,"Old Password")) return false;
	else if(!chkBlank(document.frmEdit.txtNewPassword,"New Password")) return false;
	else if(document.frmEdit.txtNewPassword.value.length < 8) 
	{
		alert("Your New Password lenght should have 8 letters");
		return false;
	}
	else if(!CheckPassword(document.frmEdit.txtNewPassword.value)) 
	{
		alert("Your password must contains: \n1. At least one upper case English letter. \n2. At least one lower case English letter. \n3. At least one digit. \n4. At least one special character");
		return false;
	}
	else if(document.frmEdit.txtOldPassword.value == document.frmEdit.txtNewPassword.value) {
		alert("Old Password and New Password should be different");
		return false;
	}
	else if(!chkBlank(document.frmEdit.txtConfirmPassword,"Confirm Password")) return false;
	else if(document.frmEdit.txtNewPassword.value != document.frmEdit.txtConfirmPassword.value) 
	{
		alert("New Password and Confirm Password does not match");
		return false;
	}
	else {
		// Submit the form for edit 
		document.frmEdit.hdnAction.value="Update";
		document.frmEdit.submit();
	}
}

function CheckCancelValidation()
{
	<?php if(!isset($_SESSION['ForceChangePassword']) || empty($_SESSION['ForceChangePassword'])) { ?>
	document.location.href = 'index.php';
	<?php } else { ?>
	document.location.href = 'Logout.php';
	<?php } ?>
}
function CheckPassword(value) {
	return /[\@\#\$\%\^\&\*\(\)\_\+\!]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) && /[A-Z]/.test(value);
}
</SCRIPT>
<div class="page-header">
	<h3>Change Login Password</h3>	
</div>
<?php 
if(isset($_SESSION['msg']) && $_SESSION['msg']!="" && $_SESSION['msg2']=='')
{
?>
	<div class="alert alert-success alert-dismissable">           
		<strong>
		<?php
		echo $_SESSION['msg'];
		if(!isset($_SESSION['ForceChangePassword']) || $_SESSION['ForceChangePassword'] == '')
		{
		    $_SESSION['msg']="";
 		}
		?>
        </strong> 
	</div>
<?php
}
?>
<div class="widget-content">
	<form action="index.php?Option=Users&SubOption=ChangePassword" method="post" name="frmEdit" id="frmEdit" class="form-horizontal col-md-6" role="form">
        <div class="form-group">
            <label class="col-md-4">Old Password</label>
            <div class="col-md-8">
            	<input name="hdnRoleID" type="hidden"  id="hdnRoleID"  value="<?php echo $row_roles['role_id']?>">
                <input name="txtOldPassword" type="password" id="txtOldPassword" maxlength="50" value="" class="form-control">
            	
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-4" for="select1">New Password</label>
            <div class="col-md-8">
             	<input name="txtNewPassword" type="password" id="txtNewPassword" maxlength="50" value="" class="form-control">
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-4" for="select1">Confirm Password</label>
            <div class="col-md-8">
             	<input name="txtConfirmPassword" type="password" id="txtConfirmPassword" maxlength="50" value="" class="form-control">
            </div>
        </div> <!-- /.form-group -->

        <div class="form-group">
            <div class="col-md-offset-4 col-md-8">
            	<input name="hdnAction" type="hidden"  id="hdnAction" value="">
                <input name="cmdUpdate" type="button" class="btn btn-success" id="cmdUpdate" value="Update" onClick="CheckUpdateValidation()">
      			<input name="cmdCancel" type="button" class="btn btn-warning" id="cmdCancel" value="Cancel" onClick="CheckCancelValidation()">
            </div>
        </div> <!-- /.form-group -->
	</form>
</div>