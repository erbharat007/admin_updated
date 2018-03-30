<?php
require_once("config.php");

$_SESSION['userEmail'] = "";
$_SESSION['userId']    = "";
$_SESSION['roleId']    = "";
$_SESSION['userName']  = "";
$_SESSION['superAdmin']= "";
$_SESSION['adminUser'] = "";
$_SESSION['userType']  = "";
$_SESSION['deptId']    = "";
session_destroy();
$_SESSION['msg']  = "You have successfully logged out.";
header("Location: login.php");
exit();
?>