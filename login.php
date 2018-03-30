<?php require_once("config.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Members Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">    
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="<?php echo SITE_URL;?>/css/fonts.css" rel="stylesheet">
<link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">        
<link href="css/jquery-ui-1.10.0.custom.min.css" rel="stylesheet">
<link href="css/base-admin-3.css" rel="stylesheet">
<link href="hcss/base-admin-3-responsive.css" rel="stylesheet">
<link href="css/dashboard.css" rel="stylesheet">   
<link href="css/signin.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	

<div class="account-container stacked">
	<div class="content clearfix loginbox">
		<form action="check_login.php" method="post">
			<h1>Members Login</h1>
			<?php
		 	if(isset($_SESSION['msg']) && $_SESSION['msg'] != "")
		 	{
		 	?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
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
			<div class="login-fields">
				<p><strong>Login using your registered account:</strong></p>
				<div class="field">
					<label for="username">Username:</label>
					<input type="text" name="username" id="username" placeholder="Email ID" class="form-control input-lg username-field" autofocus onchange="try{setCustomValidity('')}catch(e){}" oninvalid="setCustomValidity('Please enter a valid e-mail address')" pattern="[a-zA-Z0-9_\.\-\+]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9]{2,4}$" required="required" />
				</div> <!-- /field -->
				<div class="field">
					<label for="password">Password:</label>
					<input type="password" name="password" id="password" placeholder="Password" class="form-control input-lg password-field" required/>
				</div> <!-- /password -->
				 <!-- /password -->
			</div> <!-- /login-fields -->
			<div class="field">
					<strong>Log in as:</strong>
					<div class="loginradio">
						<ul>
							 <li><input type="radio" name="log_in_as" value="employee"> Employee</li>
							 <li><input type="radio" name="log_in_as" value="finance"> Finance People</li>
							 <li><input type="radio" name="log_in_as" value="hr"> HR</li>
							 <li><input type="radio" name="log_in_as" value="superadmin"> Super Admin</li>
						</ul>
					</div>					
				</div>
			<div class="login-actions">
				<span class="login-checkbox">
				<!--<a href="#">Forgot password</a>-->
				</span>						
				<input type="submit" id="submit" name="submit" value="Log in" class="login-action btn btn-primary" />
				<a class="forgot" href="#">Forgot Password?</a>
			</div> <!-- .actions -->
		</form>
	</div> <!-- /content -->
</div> <!-- /account-container -->

<!-- Placed at the end of the document so the pages load faster -->
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery-ui-1.10.0.custom.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.flot.js"></script>
<script src="js/jquery.flot.pie.js"></script>
<script src="js/jquery.flot.resize.js"></script>
<script src="js/Application.js"></script>
<script src="js/area.js"></script>
<script src="js/donut.js"></script>
<script src="js/signin.js"></script>

</body>
</html>
