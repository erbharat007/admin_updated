<?php
require_once(CLASS_PATH."/class.AssignRoleAdminUser.inc.php");
$objAssignRoleAdminUser=new AssignRoleAdminUser;

require_once(CLASS_PATH."/class.UserRoles.inc.php");
$obj_userroles=new UserRoles;

switch($action)
{
	case "Add":
		$_SESSION['msg']=$objAssignRoleAdminUser->add();
		break;

	case "Update":
		$_SESSION['msg']=$objAssignRoleAdminUser->edit();		
		break;

	case "Edit":
		$rs_assignroles=$objAssignRoleAdminUser->getrowbyid($_REQUEST['chkSelect']);
		$row_assignroles=$GLOBALS['obj_db']->fetch_array($rs_assignroles);
		break;

	case "Delete":
		$_SESSION['msg']=$objAssignRoleAdminUser->delete();
		break;
}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/users/AssignRoles.js"></script>
<?php
if($action=="Edit") 
{ 
	// Display Edit Form
?>  
<div class="page-header">
	<h3>Assign Roles to Admin Users</h3>
</div>
<?php 
if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
{
?>
    <div class="alert alert-success alert-dismissable">           
        <strong>
		<?php
        echo $_SESSION['msg'];
        $_SESSION['msg'] = "";
        ?>
        </strong>
	</div>
<?php
}
?>
<div class="widget-content">
	<form action="index.php?Option=Users&SubOption=AssignRoleAdminUser" method="post" name="frmEdit" id="frmUpdate" class="form-horizontal col-md-6" role="form">
        <div class="form-group">
            <label class="col-md-3">User Name : </label>
            <label class="col-md-9"><input name="hdnUserID" type="hidden" id="hdnUserID"  value="<?php echo $row_assignroles['user_id']?>"><?php echo $row_assignroles['first_name']." ".$row_assignroles['last_name'] ?> </label>
    	</div> <!-- /.form-group -->
        <div class="form-group">
            <label class="col-md-3">Assign Role </label>
            <div class="col-md-9">
            <select name="lstAllRoles"  class="form-control" id="lstAllRoles">
                <?php
                $rs_roles = $obj_userroles->getallroles();
                while($row_roles=$GLOBALS['obj_db']->fetch_array($rs_roles))
                { 
              ?>
                	<option <?php echo ($row_assignroles['role_id'] == $row_roles['role_id']) ? 'selected="selected"' : ''; ?> value="<?php echo $row_roles['role_id']; ?>" ><?php echo $row_roles['role_name']; ?></option>
               <?php
                } 
            	?>
            </select>	 
           </div>
        </div> <!-- /.form-group -->
        <div class="form-group">
            <div class="col-md-offset-3 col-md-9">
                <input name="hdnAction" type="hidden"  id="hdnAction" value="">
                <input name="cmdAssign" type="button" class="btn btn-info" id="cmdAssign" value="Assign" onClick="CheckAddValidation()">
                <input name="cmdCancel" type="button" class="btn btn-danger" id="cmdCancel" value="Cancel" onClick="CheckCancelValidation()">
            </div>
        </div> <!-- /.form-group -->
	</form>
</div>
<?php
}
else 
{ 
	// Display Add/Search Form
?>
<div class="page-header">
	<h3>Assign Roles to Admin Users</h3>
</div>
<?php 
if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
{
?>
    <div class="alert alert-success alert-dismissable">           
        <strong>
		<?php
        echo $_SESSION['msg'];
        $_SESSION['msg'] = "";
        ?>
        </strong> 
	</div>
<?php
}
?>
<div class="widget-content">
	<form action="index.php?Option=Users&SubOption=AssignRoleAdminUser" method="post" name="frmSearch" id="frmSearch" class="form-horizontal col-md-6" role="form">
        <div class="form-group">
            <label class="col-md-4">Search By</label>
            <div class="col-md-8">
                <select name="lstSearchBy" class="form-control" id="select">
                	<option value="0" selected="selected">User Name</option>
					<option value="1">Login ID</option>
				</select>
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-4" for="select1">Search</label>
            <div class="col-md-8">
				<input name="txtSearch" type="text" class="form-control" id="txtSearch" maxlength="100">            
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group">
            <div class="col-md-offset-4 col-md-8">
                <input name="hdnAction" type="hidden"  id="hdnAction" value="Search" />
                <input name="cmdSearch" type="button" class="btn btn-info" id="cmdSearch" value="Search" onclick="CheckSearchValidation()" />
            </div>
        </div> <!-- /.form-group -->
	</form>
</div>
	
<?php

//search check

if($action=="Search")
{
	$rs_assignroles=$objAssignRoleAdminUser->search();	
}
else
{
	$rs_assignroles=$objAssignRoleAdminUser->getall();
}

$num_rows=$GLOBALS['obj_db']->num_rows($rs_assignroles);

if($num_rows==0) 
{
?>
	<div class="col-md-12">No record Matched</div>
<?php 
}
else
{
?>
<form action="index.php?Option=Users&SubOption=AssignRoleAdminUser" method="post" name="frmUpdate" id="frmUpdate">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr>
                <th>Select</th>
                <th>User Name</th>
                <th>Login ID</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $cnt=$GLOBALS['obj_db']->num_rows($rs_assignroles);
            while ($row_assignroles=$GLOBALS['obj_db']->fetch_array($rs_assignroles))
            {
                $rs_roles = $obj_userroles->getRoleIdAdminUser($row_assignroles['user_id']);
            ?>	
                <tr>
                    <td><input name="chkSelect" type="checkbox" id="chkSelect" value="<?php echo $row_assignroles['user_id']; ?>"></td>
                    <td><?php echo $row_assignroles['first_name']." ".$row_assignroles['last_name']; ?> </td>
                    <td><?php echo $row_assignroles['email']; ?></td>
                    <td>
                    <?php
                    $i=1;
                    while($row_roles=$GLOBALS['obj_db']->fetch_array($rs_roles))
                    {
                        $rid = $row_roles['role_id'];
                        $role_name=$obj_userroles->getrolename($rid);
                        echo $i++.") ". $role_name."<br>";
                    }
                    ?>	
                   </td>
              </tr>
         <?php
        $cnt--;
        }
        ?>			          
    </tbody>
</table>
</div>	
<div class="col-md-6">
  <div class="row">
    <input name="hdnIDs" type="hidden"  id="hdnIDs" value="">
    <input name="hdnAction" type="hidden"  id="hdnAction" value="">
    <input name="cmdEditRole" type="button" class="btn btn-info" id="cmdEditRole" value="Edit Role" onClick="CheckEditValidation()">
  </div>
</div>  
</form>
<?php  
} 
}
?> 