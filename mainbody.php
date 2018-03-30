<?php
if(isset($error) && $error==1)
{
	include(DOC_ROOT."/includes/Error.php");
}
else
{
	switch ($option)
	{
		case "Setup":
			include(DOC_ROOT."/services/setup.php");
			break;
			
		case "Registration":
			include(DOC_ROOT."/services/registration.php");
			break;
			
		case "Users":
			include(DOC_ROOT."/services/users.php");
			break;
			
		case "Leaves":
			include(DOC_ROOT."/services/leaves.php");
			break;	
			
		case "Activity":
			include(DOC_ROOT."/services/activity.php");
			break;
			
		case "Tour":
			include(DOC_ROOT."/services/tours.php");
			break;
			
		case "authorizerDashboard":
			include(DOC_ROOT."/dashboard_authorizer.php");
			break;	
		
		default:
			 include(DOC_ROOT."/dashboard.php");
			break;
	}
}