<?php
switch ($suboption)
{
	case "dailyActivity":
		include(DOC_ROOT."/includes/activity/daily_activity.php");
		break;
		
	case "dailyActivityRequests":
		include(DOC_ROOT."/includes/activity/daily_activity_requests.php");
		break;

	default:
		include(DOC_ROOT."/includes/activity/daily_activity.php");
		break;
}
?>