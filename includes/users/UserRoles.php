<?php
require_once("".CLASS_PATH."/class.UserRoles.inc.php");
$obj_userroles=new UserRoles;
$rs_features=$obj_userroles->getallfeatures();

require_once("".CLASS_PATH."/class.ConsoleUser.inc.php");
$obj_consoleuser=new ConsoleUser;

switch($action)
{
	case "Add":
		$_SESSION['msg']=$obj_userroles->add();
		break;

	case "Update":
		$_SESSION['msg']=$obj_userroles->edit();		
		break;

	case "Edit":
		$rs_userroles=$obj_userroles->getrowbyid($_REQUEST['chkSelect']);
		$row_roles=$GLOBALS['obj_db']->fetch_array($rs_userroles);
		break;

	case "Delete":
		$_SESSION['msg']=$obj_userroles->delete();
		$hdnIDs=explode("&",$_REQUEST['hdnIDs']);
		break;
}
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/users/UserRoles.js"></script>
<div class="widget stacked">
<?php
if($action=="Edit") // Display Edit Form
{
?> 
<div class="page-header">
	<h3>Edit User Roles</h3>	
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
	<form action="index.php?Option=Users&SubOption=UserRole" method="post" name="frmEdit" id="frmEdit" class="form-horizontal col-md-6" role="form">
        <div class="form-group">
            <label class="col-md-4">Role Name</label>
            <div class="col-md-8">
            	<input name="hdnRoleID" type="hidden"  id="hdnRoleID"  value="<?php echo $row_roles['role_id']?>">
<input name="txtRoleName" type="text" class="form-control" id="txtRoleName" maxlength="30" value="<?php echo $row_roles['role_name']; ?>"> 	
            </div>
        </div> <!-- /.form-group -->


        <div class="form-group ">
            <label class="col-md-4" for="select1">Screen Names</label>
            <div class="col-md-8">
             <?php
			  $features		="";
			  $featureSQL	= "select feature_id from features";
			  $rsFeature	= $GLOBALS['obj_db']->execute_query($featureSQL);
			  while($rstFeature	= $GLOBALS['obj_db']->fetch_array($rsFeature))
			  {
				if($features=="")
				{
				  $features	= $rstFeature['feature_id'];
				}
				else
				{
				  $features	.= ";".$rstFeature['feature_id'];
				}
			  }	     
			?>
			<input type="hidden" name="hdnFeatureID" id="hdnFeatureID" value="">
            <select name="lstScreenNames" size="8" multiple class="form-control multiple-select" id="lstScreenNames">
                <option value="<?=$features?>">All Screens</option>
                  <?php
                  while($row_features=$GLOBALS['obj_db']->fetch_array($rs_features))
                  {
                      $sname=$obj_consoleuser->getservicenamebyid($row_features['service_id']);
                      $flag = $obj_consoleuser->isfeatureinrole($row_features['feature_id'],$row_roles['role_id']);
					  if($flag)
					  {	
				 ?>
						  <option selected value="<?php echo $row_features['feature_id']; ?>" ><?php echo $sname."->".$row_features['feature_name']; ?></option>
				<?php
					  }
					  else
					  {	
				?>		
						  <option value="<?php echo $row_features['feature_id']; ?>" ><?php echo $sname."->".$row_features['feature_name']; ?></option>
				<?php
					  }
                }
               ?>
              </select>
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
<?php
}
else
{ 
	// Display Add/Search form
?>

<div class="page-header">
	<h3>Add User Roles</h3>
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
	<form action="index.php?Option=Users&SubOption=UserRole" method="post" name="frmAdd" id="frmAdd" class="form-horizontal col-md-6" role="form" >
        <div class="form-group">
            <label class="col-md-4">Role Name</label>
            <div class="col-md-8">
                <input name="txtRoleName" type="text" class="form-control" id="txtRoleName" maxlength="30">
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group ">
            <label class="col-md-4" for="select1">Screen Names</label>
            <div class="col-md-8">
             <?php
              $features		= "";
              $featureSQL	= "select feature_id from features";
              $rsFeature	= $GLOBALS['obj_db']->execute_query($featureSQL);
              while($rstFeature	= $GLOBALS['obj_db']->fetch_array($rsFeature))
              {
                if($features=="")
                {
                  $features	= $rstFeature['feature_id'];
                }
                else
                {
                  $features	.= ";".$rstFeature['feature_id'];
                }
              }	     
            ?>
            <input type="hidden" name="hdnFeatureID" id="hdnFeatureID" value="" />
            <select name="lstScreenNames" size="8" multiple class="form-control multiple-select" id="lstScreenNames" >
                <option value="<?=$features?>">All Screens</option>
                <?php
                while($row_features=$GLOBALS['obj_db']->fetch_array($rs_features))
                {	
                    $sname = $obj_consoleuser->getservicenamebyid($row_features['service_id']);
                ?>
                    <option value="<?php echo $row_features['feature_id']; ?>" ><?php echo $sname."->".$row_features['feature_name'];?></option>
                <?php
                }
                ?>
            </select>	            
        </div>
      </div> <!-- /.form-group -->

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input name="hdnAction" type="hidden" id="hdnAction" value="Add">
            <input name="cmdAdd" type="button" class="btn btn-success" id="cmdAdd" value="Add" onClick="CheckAddValidation()" >
            <input name="cmdReset" type="reset" class="btn btn-warning" id="cmdReset" value="Reset">
        </div>
    </div> <!-- /.form-group -->
</form>
</div>

<div class="widget-content">
	<form role="search" class="navbar-form navbar-right" action="index.php?Option=Users&SubOption=UserRole" method="post" name="frmSearch" id="frmSearch">
      <div class="form-group">
        <input name="hdnAction" type="hidden"  id="hdnAction" value="Search">
        <input name="txtSearchRole" type="text" id="txtSearchRole" maxlength="100" placeholder="Search Role Name" class="form-control input-sm search-query">
        <input name="cmdSearch" type="button" class="buttonOne" id="cmdSearch" value="Search" onClick="CheckSearchValidation()">
      </div>
	</form>
</div>  
<?php
if($action=="Search")
{
	$rs_roles=$obj_userroles->search();
}
else
{
	$rs_roles=$obj_userroles->getallroles();
}

$num_rows = $GLOBALS['obj_db']->num_rows($rs_roles);

if($num_rows==0)
{
?>
	<div class="col-md-12">No record Matched</div>
<?php	
}
else
{ 
?>
	<form action="index.php?Option=Users&SubOption=UserRole" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<input name="chkSelectAll" type="checkbox" id="chkSelectAll" onClick="fnSelectAll(document.frmUpdate.chkSelect,this)"> Select All
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Select</th>
                    <th>Role Name</th>
                    <th>Screen Assigned</th>
                  </tr>
                </thead>
				<tbody>
				<?php
                while($row_roles=$GLOBALS['obj_db']->fetch_array($rs_roles))
                {	
					$rs_feature = $obj_consoleuser->getfeatureid($row_roles['role_id']);
					$rs_hotel = $obj_consoleuser->gethotelid($row_roles['role_id']);
                ?>
					<tr>
					    <td><input name="chkSelect" type="checkbox" id="chkSelect" value="<?php echo $row_roles['role_id'] ?>"></td>
					    <td><?php echo $row_roles['role_name'] ?></td>
					    <td>
							<?php 
                            $cn=0;
                            $fl=0;
                            $i=1;
                            while($row_feature=$GLOBALS['obj_db']->fetch_array($rs_feature))
                            {
                                
                                    $fid=$row_feature['feature_id'];
                                
                                    $feature_name=$obj_consoleuser->getfeaturename($fid);
                                    echo "<b>".$i."."."</b>"." ".$feature_name."<br>";
                                            
                            $i++;
                                
                            }
                            ?>	
						</td>
					 </tr>
                 <?php
				 }
				 ?>
		       </tbody>
		    </table>
		</div>	
        <div class="col-md-6"> 
            <div class="row">
                <input name="hdnIDs" type="hidden"  id="hdnIDs" value="">
                <input name="hdnAction" type="hidden"  id="hdnAction" value="">
                <input name="cmdEdit" type="button" class="btn btn-info" id="cmdEdit" value="Edit" onClick="CheckEditValidation()">&nbsp;&nbsp;
                <input name="cmdDelete" type="button" class="btn btn-danger" id="cmdDelete" value="Delete" onClick="if (confirm('Would you like to proceed')) {CheckDeleteValidation()}">
            </div>
		</div>
	</form>
<?php 
}
}
?>
</div>