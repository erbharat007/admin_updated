<?php
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.AdminUser.inc.php");
$objAdminUsers = new AdminUser;

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
    $rs_content = $objAdminUsers->getUserData($_REQUEST['id']);
    $result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "addsave":
	    $branchId = ($_REQUEST['user_type'] == 'finance') ? $_REQUEST['branch'] : -1 ;
		$arrayData = array($_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email'], $_REQUEST['password'], $_REQUEST['m_phone'], $_REQUEST['status'], $_REQUEST['user_type'], $_REQUEST['is_centralised'], $branchId);
		
		
		
		$_SESSION['msg']=$objAdminUsers->add($arrayData);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Setup&SubOption=adminUser'; </script>";
		exit();
	break;

    case "updatesave":
		$branchId = ($_REQUEST['user_type'] == 'finance') ? $_REQUEST['branch'] : -1 ;
		$arrayData = array($_REQUEST['id'], $_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email'], $_REQUEST['password'], $_REQUEST['m_phone'], $_REQUEST['status'], $_REQUEST['user_type'], $_REQUEST['is_centralised'], $branchId);
		
		$_SESSION['msg'] = $objAdminUsers->update($arrayData);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Setup&SubOption=adminUser'; </script>";
		exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objAdminUsers->delete($_REQUEST['id']);
		echo "<script type='text/javascript'>window.location.href = 'index.php?Option=Setup&SubOption=adminUser'; </script>";
		exit();
	break;
}
$message = $_SESSION['msg'];
?>

<script language=javascript>
$(function() 
{
	$("#user_type").change(function(){
		if($(this).val() == 'finance')
		{
			$("#branch-row, #central-admin-row").removeClass('hide');
		}
		else
		{
			$("#branch-row, #central-admin-row").addClass('hide');
		}	
	});
});
function checkData() 
{
    var error = "";
	
	if($.trim($('#user_type').val()) == -1)
	{
		error = "Please select type of user";
		alert(error);
		$('#user_type').focus();
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
    <h3>Edit Admin User</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Add Admin User</h3>
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
<div class="widget-content">
	<form name="sample" method="post" enctype="multipart/form-data" action="index.php?Option=Setup&SubOption=adminUser&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-6" role="form" onsubmit="return checkData();">
    	<input type="hidden" value="<?php echo $result_content['user_id']; ?>" name="id" id="id">
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
            <label class="col-md-3" for="select1">E-mail ID <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="email" type="text" id="email" class="form-control" value="<?php echo $result_content['email']; ?>" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid e-mail address')" pattern="[a-zA-Z0-9_\.\-\+]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9]{2,4}$" required="required">
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group ">
            <label class="col-md-3" for="select1">Password <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="password" type="password" id="password" value="<?php echo ($_REQUEST['ofaction']=="update") ? DUMMY_PASS : ''; ?>" class="form-control" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid password. It must contain at least one number, one uppercase, one lowercase letter and at least of 8 or more characters.')" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must contain at least one number, one uppercase, lowercase letter, and at least 8 or more characters" required="required" >
            </div>
        </div> <!-- /.form-group -->
		
        <div class="form-group ">
            <label class="col-md-3" for="select1">Mobile Phone <span style="color:red;">*</span></label>
            <div class="col-md-9">
             	<input name="m_phone" type="text" id="m_phone" class="form-control" value="<?php echo $result_content['phone']; ?>" onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter valid mobile number. Only numeric values are allowed.')" pattern="[0-9]+" required>
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
            <label class="col-md-3" for="select1">User Type</label>
            <div class="col-md-9">
             	<select name="user_type" class="form-control" id="user_type">
					<option value="finance" <?php echo ($result_content['user_type'] == 'finance') ? 'selected="selected"' : ''; ?>>Finance</option>
					<option value="hr" <?php echo ($result_content['user_type'] == 'hr') ? 'selected="selected"' : ''; ?>>HR</option>
					<option value="superadmin" <?php echo ($result_content['user_type'] == 'superadmin') ? 'selected="selected"' : ''; ?>>Super Admin</option>
				</select>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group <?php echo ($_REQUEST['ofaction']=="update" && $result_content['user_type'] != 'finance') ? 'hide' : ''; ?>" id="branch-row">
            <label class="col-md-3" for="select1">Branch</label>
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
							<option value="<?php echo $rowBranch['id']; ?>" <?php echo ($result_content['branch_id'] == $rowBranch['id']) ? 'selected="selected"' : ''; ?>><?php echo $rowBranch['city_name']; ?></optio n>
					<?php	
						}	
					}	
					?>
				 </select>
            </div>
        </div> <!-- /.form-group -->
		<div class="form-group <?php echo ($_REQUEST['ofaction']=="update" && $result_content['user_type'] != 'finance') ? 'hide' : ''; ?>" id="central-admin-row">
            <label class="col-md-3" for="select1"></label>
            <div class="col-md-9">
			<?php
			$checked = '';
			if($result_content['centralised_admin'] == 'YES')
			{
				$checked = 'checked';
			}	
			?>
             	<input type="checkbox" name="is_centralised" id="is_centralised" <?php echo $checked; ?> /> Centralised Account Admin
				<br/>
				Check this if the user is centralised account admin. Remember, it will override the existing centralised account admin (if any).
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
           
   
	<?php
	$dsqle = $objAdminUsers->getAll();
	$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	
	<div class="col-md-12" style="clear:both;">
		<form name="search_form" method="post" action="index.php?Option=Setup&SubOption=adminUser">
			<input type="search" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>">
			<input type="submit" class="btn btn-info" value="Search">
			<input type="button" onclick="window.location.href='index.php?Option=Setup&SubOption=adminUser';" class="btn btn-danger" value="Cancel" />				
		</form>
	</div>	
	<form action="index.php?Option=Setup&SubOption=adminUser" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>User Name</th>
					<th>User Type</th>
					<th>Status</th>
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

				$targetpage = "index.php?Option=Setup&SubOption=adminUser";

				$contentrow = $objAdminUsers->getAll($start, $end);
				if($GLOBALS['obj_db']->num_rows($contentrow))
				{	
					$i = 1;
					while($dsqlerow=$GLOBALS['obj_db']->fetch_array($contentrow))
					{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $dsqlerow['first_name']." ".$dsqlerow['last_name']; ?></td>
						<td><?php echo strtoupper($dsqlerow['user_type']); ?></td>
						<td><?php echo ucwords($dsqlerow['status']); ?></td>
						<td>
							<a href="index.php?Option=Setup&SubOption=adminUser&ofaction=update&id=<?php echo $dsqlerow['user_id'];?><?php echo $additional_link_redirect; ?>" >Edit</a>
							&nbsp;&nbsp;
							<a href="index.php?Option=Setup&SubOption=adminUser&ofaction=deleteof&id=<?php echo $dsqlerow['user_id'];?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();">Delete</a>
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