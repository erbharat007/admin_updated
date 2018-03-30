<?php require_once("header.php"); ?>
<?php
if(isset($_REQUEST['Option']))  $option=$_REQUEST['Option'];
if(isset($_REQUEST['op']))  $op=$_REQUEST['op'];
if(isset($_REQUEST['id']))  $id=$_REQUEST['id'];
if(isset($_REQUEST['SubOption']))  $suboption=$_REQUEST['SubOption'];
if(isset($_REQUEST['hdnAction']))  $action=$_REQUEST['hdnAction'];
if(isset($_REQUEST['Error']))  $error=$_REQUEST['Error'];
?>
<div class="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php
				if(isset($_SESSION['superAdmin']) && $_SESSION['superAdmin'] == 1)
				{
					include_once("mainbody.php");
				}	
				elseif($suboption != "" && !in_array($suboption, $validOptions))
				{
					$featureId = $objAuth->getFeatureIdByOption($suboption);
					if(!$featureId)
					{
					?>
						<div class="alert alert-danger alert-dismissable"><strong>Something went wrong. Try again.</strong></div>
					<?php						
					}
					elseif(!$objAuth->isFeatureAllowed($_SESSION['roleId'], $featureId))
					{
					?>
						<div class="alert alert-danger alert-dismissable"><strong>You are not authorized to view this section.</strong></div>
					<?php						
					}
					else
					{
						include_once("mainbody.php");
					}	
				}
				else
				{
					include_once("mainbody.php");
				}	
				?>				
			</div>
		</div>
	</div>	
</div>
<?php require_once("footer.php"); ?>
<?php
if(isset($_REQUEST['search_text']) && trim($_REQUEST['search_text']) != "")
{
?>
<script type="text/javascript">
$('html,body').animate({scrollTop: $('input[name="search_text"]').offset().top }, 1200);
</script>
<?php
}
?>
