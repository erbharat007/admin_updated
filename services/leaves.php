<?php
switch ($suboption)
{
	case "leaveApplication":
		include(DOC_ROOT."/includes/leaves/leave_application.php");
		break;
		
	case "leaveRequests":
		include(DOC_ROOT."/includes/leaves/leave_requests.php");
		break;

	default:
		include(DOC_ROOT."/includes/leaves/leave_application.php");
		break;
}
?>