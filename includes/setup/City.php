<?php
$baseFormAction = "index.php?Option=Setup&SubOption=City";
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.City.inc.php");
$objCity = new City;

require_once("".CLASS_PATH."/class.Country.inc.php");
$objCountry = new Country;

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
    $rs_content = $objCity->getCityData($_REQUEST['id']);
    $result_content = $GLOBALS['obj_db']->fetch_array($rs_content);
}

switch($action)
{
    case "addsave":
		$arrayData = array($_REQUEST['designation']);
		$_SESSION['msg']=$objCity->add($arrayData);
		//header("Location: ".$baseFormAction);
		//exit();
	break;

    case "updatesave":
		$arrayData = array($_REQUEST['id'], $_REQUEST['designation']);
		$_SESSION['msg'] = $objCity->update($arrayData);
		//header("Location: ".$baseFormAction.$additional_link_redirect);
		//exit();
	break;

    case "deleteof":
		$_SESSION['msg'] = $objCity->delete($_REQUEST['id']);
		//header("Location: ".$baseFormAction.$additional_link_redirect);
		//exit();
	break;
}
?>

<script language=javascript>
function checkData() 
{
    var error = "";
  
    if ($.trim($("#city_name").val()) == "") 
    { 
		error = "Please enter city name"; 
		alert(error);
		$("#city_name").val('').focus();
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
    <h3>Edit City</h3>
    <?php
    }
    else
    {
    ?>
    <h3>Add City</h3>
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
	<form name="frm_dept" method="post" enctype="multipart/form-data" action="<?php echo $baseFormAction; ?>&ofaction=<?php if($_REQUEST['ofaction']=="update") echo "updatesave".$additional_link_redirect; else echo "addsave";?>" class="form-horizontal col-md-6" role="form" onsubmit="return checkData();";>
    	<input type="hidden" value="<?php echo $result_content['city_id']; ?>" name="id" id="id">
		<div class="form-group ">
            <label class="col-md-3" for="select1">Country <span style="color:red;">*</span></label>
            <div class="col-md-9">
			<select name="country" id="country" class="form-control">
			<option value="-1">--- Select Country--- </option>
			<?php
			$rsCountry = $objCountry->getAll('', '', false);
			if($GLOBALS['obj_db']->num_rows($rsCountry))
			{
				while($rowCountry = $GLOBALS['obj_db']->fetch_array($rsCountry))
				{
			?>
					<option value="<?php echo $rowCountry['country_id']; ?>" <?php echo ($result_content['country_id'] == $rowCountry['country_id']) ? 'selected="selected"' : ''; ?>><?php echo $rowCountry['country_name']; ?></option>
			<?php	
				}	
			}	
			?>
             </select>	
            </div>
        </div> <!-- /.form-group -->
        <div class="form-group">
            <label class="col-md-4">City Name<span style="color:red;">*</span></label>
            <div class="col-md-8">
            <input name="city_name" type="text" id="city_name" maxlength="100" class="form-control" value="<?php echo $result_content['city_name']; ?>" required autofocus>
            	
            </div>
        </div> <!-- /.form-group -->
		
		<div class="form-group">
            <label class="col-md-4">Is metro city ?</label>
            <div class="col-md-8">
			<input type="radio" name="is_metro_city" id="is_metro_city_y" <?php echo ($result_content['is_metro_city'] == 1) ? 'checked="checked"' : ''; ?> /> Yes &nbsp;
            <input type="radio" name="is_metro_city" id="is_metro_city_n" <?php echo ( ($_REQUEST['ofaction']=="update" && $result_content['is_metro_city'] == 0)) ? 'checked="checked"' : ''; ?> /> No
            	
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
	$dsqle = $objCity->getAll();
	$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	
	<div class="col-md-12" style="clear:both;">
		<form name="search_form" method="post" action="<?php echo $baseFormAction; ?>">
			<input type="search" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>">
			<input type="submit" class="btn btn-info" value="Search">
			<input type="button" onclick="window.location.href='<?php echo $baseFormAction; ?>';" class="btn btn-danger" value="Cancel" />				
		</form>
	</div>
	
	<form action="<?php echo $baseFormAction; ?>" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>Designation</th>
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

				$targetpage = $baseFormAction;

				$contentrow = $objCity->getAll($start, $end);
				if($GLOBALS['obj_db']->num_rows($contentrow))
				{	
					$i = 1;
					while($dsqlerow=$GLOBALS['obj_db']->fetch_array($contentrow))
					{
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $dsqlerow['designation']; ?></td>						
						<td>
							<a href="<?php echo $baseFormAction; ?>&ofaction=update&id=<?php echo $dsqlerow['id'];?><?php echo $additional_link_redirect; ?>" >Edit</a>
							&nbsp;&nbsp;
							<a href="<?php echo $baseFormAction; ?>&ofaction=deleteof&id=<?php echo $dsqlerow['id'];?><?php echo $additional_link_redirect; ?>" onclick="return confirmDeleteAction();" class="confirm-delete">Delete</a>
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
