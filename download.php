<?php
require_once("config.php");

if(!isset($_SESSION['userId']) || $_SESSION['userId'] == '')
{
	$_SESSION['msg'] = "Your session has expired. Please login again to continue.";
	header("Location: login.php");
	exit();
}

if(!isset($_REQUEST['filename']) || $_REQUEST['filename'] == '')
{
	
}
else
{
	$fileName = base64_decode($_REQUEST['filename']);
	$filePath = "user_uploads/emp_".$_REQUEST['empId']."/".$fileName;
	
	header('Content-Type: "application/octet-stream"');
    header('Content-Disposition: attachment; filename='.$fileName);
	readfile($filePath);
}
?>