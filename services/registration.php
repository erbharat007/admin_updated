<?php
switch ($suboption){
	case "employee":
		include(DOC_ROOT."/includes/registration/employee.php");
		break;
	
	default:
		include(DOC_ROOT."/includes/registration/employee.php");
		break;
}
?>