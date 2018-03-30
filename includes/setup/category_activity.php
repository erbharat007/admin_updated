<?php
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.ActivityCategory.inc.php");
$objCategory = new ActivityCategory;
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
    $rs_content = $objCategory->getCatData($_REQUEST['id']);
    $result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "addsave":
		$arrayData = array($_REQUEST['category']);
		$_SESSION['msg']=$objCategory->add($arrayData);
		//header("Location: index.php?Option=Setup&SubOption=activityCategory");
		//exit();
	break;

    case "updatesave":
		$arrayData = array($_REQUEST['id'], $_REQUEST['category']);
		$_SESSION['msg'] = $objCategory->update($arrayData);
		//header("Location: index.php?Option=Setup&SubOption=activityCategory".$additional_link_redirect);
		//exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objCategory->delete($_REQUEST['id']);
		//header("Location: index.php?Option=Setup&SubOption=activityCategory".$additional_link_redirect);
		//exit();
	break;
}
?>

<script language=javascript>
function checkData() 
{
    var error = "";
  
    if ($.trim($("#category").val()) == "") 
    { 
		error = "Please enter activity category."; 
		alert(error);
		$("#category").val('').focus();
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
    <h3>Edit Activity Category</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Add Activity Category</h3>
    <?php
    }
    ?>
</div>
<?php 
if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
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
	<form name="frm_dept" method="post" enctype="multipart/form-data" action="index.php?Option=Setup&SubOption=activityCategory&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-6" role="form" onsubmit="return checkData();";>
    	<input type="hidden" value="<?php echo $result_content['id']; ?>" name="id" id="id">
        <div class="form-group">
            <label class="col-md-4">Category <span style="color:red;">*</span></label>
            <div class="col-md-8">
				<input name="category" type="text" id="category" class="form-control" value="<?php echo $result_content['category']; ?>" required autofocus>            	
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
	$dsqle = $objCategory->getAll();
	$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	
	<div class="col-md-12" style="clear:both;">
		<form name="search_form" method="post" action="index.php?Option=Setup&SubOption=activityCategory">
			<input type="search" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>">
			<input type="submit" class="btn btn-info" value="Search">
			<input type="button" onclick="window.location.href='index.php?Option=Setup&SubOption=activityCategory';" class="btn btn-danger" value="Cancel" />				
		</form>
	</div>	
	
	<form action="index.php?Option=Setup&SubOption=activityCategory" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>Category</th>
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

				$targetpage = "index.php?Option=Setup&SubOption=activityCategory";

				$contentrow = $objCategory->getAll($start, $end);
				if($GLOBALS['obj_db']->num_rows($contentrow))
				{	
					$i = 1;
					while($dsqlerow=$GLOBALS['obj_db']->fetch_array($contentrow))
					{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $dsqlerow['category']; ?></td>
						<td>
							<a href="index.php?Option=Setup&SubOption=activityCategory&ofaction=update&id=<?php echo $dsqlerow['id'];?><?php echo $additional_link_redirect; ?>" >Edit</a>
							&nbsp;&nbsp;
							<a href="index.php?Option=Setup&SubOption=activityCategory&ofaction=deleteof&id=<?php echo $dsqlerow['id'];?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();" class="confirm-delete">Delete</a>
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