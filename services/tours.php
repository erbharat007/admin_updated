<?php
switch ($suboption)
{
	case "createTourRequest":
		include(DOC_ROOT."/includes/tour/tour_application.php");
		break;
		
	case "tourRequests":
		include(DOC_ROOT."/includes/tour/tour_requests.php");
		break;
		
	case "localConveyance":
		include(DOC_ROOT."/includes/tour/local_conveyance_application.php");
		break;
		
	case "localConveyanceRequests":
		include(DOC_ROOT."/includes/tour/local_conveyance_requests.php");
		break;
		
	case "daCalculation":
		include(DOC_ROOT."/includes/tour/da_calculation.php");
		break;

	default:
		include(DOC_ROOT."/includes/tour/tour_application.php");
		break;
}
?>