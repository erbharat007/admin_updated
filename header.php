<?php 
require_once("config.php");
require_once(CLASS_PATH."/class.Authentication.php");
$objAuth = new Authentication;
//echo '<pre>';
//print_r($_SESSION);
//die;
?>
<?php
if(!isset($_SESSION['userId']) || $_SESSION['userId'] == '')
{
	//$_SESSION['msg'] = "Your session has expired. Please login again to continue.";
	header("Location: login.php");
	exit();
}	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">

<script src="<?php echo SITE_URL;?>/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo SITE_URL;?>/js/jquery-ui-1.10.0.custom.min.js"></script>
<link href="<?php echo SITE_URL;?>/css/bootstrap.min.css" rel="stylesheet">
<!--<link href="<?php echo SITE_URL;?>/css/bootstrap-responsive.min.css" rel="stylesheet">-->
<!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">-->
<!--<link href="<?php echo SITE_URL;?>/css/fonts.css" rel="stylesheet">-->
<link href="<?php echo SITE_URL;?>/css/font-awesome.min.css" rel="stylesheet">        
<link href="<?php echo SITE_URL;?>/css/jquery-ui-1.10.0.custom.min.css" rel="stylesheet">
<link href="<?php echo SITE_URL;?>/css/base-admin-3.css" rel="stylesheet">
<link href="<?php echo SITE_URL;?>/css/base-admin-3-responsive.css" rel="stylesheet">
<link href="<?php echo SITE_URL;?>/css/dashboard.css" rel="stylesheet">
<link href="<?php echo SITE_URL;?>/css/jquery.lightbox.css" rel="stylesheet">
<link href="<?php echo SITE_URL;?>/css/jquery.msgbox.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/common.js"></script>
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<style type="text/css">
 <!--
.fancybox-title { position: absolute; top: 0; padding-bottom:10px; }
.fancybox-skin { position: relative; }
-->
</style>
</head>
<body>

<nav class="navbar navbar-inverse" role="navigation">
	<div class="container">
		<div class="navbar-header">
    		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
      		<i class="icon-cog"></i>
    		</button>
    		<a class="navbar-brand" href="index.php"><img src="images/bg-logo-white.png"></a>
  		</div>

	<div class="collapse navbar-collapse navbar-ex1-collapse">
    	<ul class="nav navbar-nav navbar-right">
			<li class="dropdown">
				<a href="javscript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-user"></i> Welcome <?php echo $_SESSION['userName']; ?>
                    <b class="caret"></b>
				</a>
					<ul class="dropdown-menu">
                        <li><a href="index.php?Option=Users&SubOption=ChangePassword">Change Password</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php">Logout</a></li>
					</ul>
			</li>
    	</ul>
   </div><!-- /.navbar-collapse -->
</div> <!-- /.container -->
</nav>

<div class="subnavbar">
	<div class="subnavbar-inner">
		<?php
		$allowedServices = $objAuth->getAllowedServices($_SESSION['roleId']);
		if(!empty($allowedServices))
		{
		?>
			<div class="container">
				<a href="javascript:;" class="subnav-toggle" data-toggle="collapse" data-target=".subnav-collapse">
		      	 <span class="sr-only">Toggle navigation</span>
		         <i class="icon-reorder"></i>
		        </a>
			<div class="collapse subnav-collapse">
				<ul class="mainnav">			
					<li class="active">
						<a href="index.php">
							<i class="fa fa-home"></i>
							<span>Home</span>
						</a>	    				
					</li>
					<?php	
					foreach($allowedServices as $serviceDetails)
					{
					?>
						<li class="dropdown">					
							<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-th"></i>
                                <span><?php echo $serviceDetails['serviceName']; ?></span>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
							<?php
                            foreach($serviceDetails['features'] as $feature)
                            {
                            ?>						
                                <li><a href="<?php echo $feature['featureURL']; ?>"><?php echo $feature['featureName']; ?></a></li>
                                                    
                            <?php	
                            }
							if($serviceDetails['serviceName'] == 'General Set-up' && isset($_SESSION['superAdmin']) && $_SESSION['superAdmin'] == 1)
							{
							?>
								<li><a href="index.php?Option=Setup&SubOption=adminUser">Set up Admin Users</a></li>
							<?php	
							}	
                            ?>	
                            </ul>	
						</li>
					<?php		
					}
				    ?>
			</ul>
		 </div> <!-- /.subnav-collapse -->
	</div> <!-- /container -->
		<?php
		}	
		?> 
  </div> <!-- /subnavbar-inner -->
</div> <!-- /subnavbar -->
