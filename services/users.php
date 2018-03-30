<?php
switch ($suboption){
	case "ConsolUser":
		include(DOC_ROOT."/includes/users/ConsolUser.php");
		break;
	case "UserRole":
		include(DOC_ROOT."/includes/users/UserRoles.php");
		break;	
	case "AssignRole":
		include(DOC_ROOT."/includes/users/AssignRoles.php");
		break;
	case "AssignRoleAdminUser":
		include(DOC_ROOT."/includes/users/AssignRolesAdminUser.php");
		break;	
	case "UserLogs":
		include(DOC_ROOT."/includes/users/userlog.php"); //   /includes/users/UserLogs.php
		break;
	case "AssignHotel":
		include(DOC_ROOT."/includes/users/AssignHotelsToUsers.php"); //   /includes/users/UserLogs.php
		break;
	case "ChangePassword":
		include(DOC_ROOT."/includes/users/ChangePassword.php"); //   /includes/users/UserLogs.php
		break;
	default:
		include(DOC_ROOT."/includes/users/ConsolUser.php");
		break;
}
?>