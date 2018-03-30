<?php
@session_start();
if($_SERVER['HTTP_HOST'] == 'www.bergengroupindia.com' || $_SERVER['HTTP_HOST'] == 'bergengroupindia.com')
{
    define("DEVELOPMENT_MODE", 0);	
}
else
{
    define("DEVELOPMENT_MODE", 1);		
}	

if(DEVELOPMENT_MODE == 1)
{
	error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR);
    ini_set('display_errors', 1);
	
	/* Database config */
	define("DB_HOST", "localhost");
	define("DB_USER", "root");
	define("DB_PASSWORD", "");
	define("DB_NAME", "emp_system_updated");
	/* End config */
	define("SITE_URL", "http://localhost/admin_updated");
}
else
{
	/* Database config */
	define("DB_HOST", "localhost");
	define("DB_USER", "emp_newuser");
	define("DB_PASSWORD", "emp_newpass");
	define("DB_NAME", "emp_new");
	/* End config */
	define("SITE_URL", "http://www.bergengroupindia.com/emp");	
}
define("DOC_ROOT",".");
define("CLASS_PATH", "classes");
define("DUMMY_PASS", "Dummypass#4");

define("DEFAULT_FROM_EMAIL", "info@bergengroupindia.com");
define("DEFAULT_FROM_NAME", "Admin");

define("SMTP_SECURE", "");
define("SMTP_HOST", "relay-hosting.secureserver.net");
define("SMTP_PORT", 25);
define("SMTP_USER", "info@bergengroupindia.com");
define("SMTP_PASS", "July@2011");

define("EL_BASE_DAYS", 20);
define("EL_BASE_LEAVE", 1);

$validOptions = array('daCalculation', 'adminUser', 'ChangePassword');

require_once(CLASS_PATH . "/class.Database.inc.php");
$GLOBALS['obj_db'] = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

date_default_timezone_set('Asia/Kolkata');