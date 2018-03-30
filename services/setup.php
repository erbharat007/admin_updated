<?php
switch ($suboption)
{
	case "Department":
		include(DOC_ROOT."/includes/setup/department.php");
		break;
		
	case "Designation":
		include(DOC_ROOT."/includes/setup/designation.php");
		break;
	
	case "Country":
		include(DOC_ROOT."/includes/setup/Country.php");
		break;	
		
	case "City":
		include(DOC_ROOT."/includes/setup/City.php");
		break;
		
	case "holidayCalendarNorth":
		include(DOC_ROOT."/includes/setup/holidays_north.php");
		break;
		
	case "holidayCalendarSouth":
		include(DOC_ROOT."/includes/setup/holidays_south.php");
		break;	
		
	case "empLeaveSetup":
		include(DOC_ROOT."/includes/setup/emp_leave_setup.php");
		break;
		
	case "branches":
		include(DOC_ROOT."/includes/setup/company_branches.php");
		break;
	
	case "activityCategory":
		include(DOC_ROOT."/includes/setup/category_activity.php");
		break;
		
	case "adminUser":
		include(DOC_ROOT."/includes/setup/admin_user.php");
		break;	
		
	case "updateEL":
		include(DOC_ROOT."/includes/setup/update_el.php");
		break;	
	
	default:
		include(DOC_ROOT."/includes/setup/department.php");
		break;
}
?>