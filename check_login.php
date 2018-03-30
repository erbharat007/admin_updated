<?php
require_once("config.php");
require_once(CLASS_PATH."/class.Authentication.php");
$objAuth = new Authentication;

if(isset($_POST['submit']))
{
	$email   = trim($_POST['username']);
	$pass    = trim($_POST['password']);
	$logInAs = trim($_POST['log_in_as']);
	
	if($objAuth->checkUserAuthentication($email, $pass, $logInAs))
	{
		header("Location: index.php");
		exit;
	}
	else
	{
		$_SESSION['msg'] = "Invalid Username/Password";
		header("Location: login.php");
		exit;
	}
}
