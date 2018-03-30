<?php  
set_time_limit(0);
ini_set('memory_limit', '1024M'); // or you could use 1G

require_once(CLASS_PATH."/class.Employee.inc.php");
$objEmployee = new Employee;

require_once(CLASS_PATH."/class.Holiday.inc.php");
$objHoliday = new Holiday;

require_once(CLASS_PATH."/class.Leaves.inc.php");
$objLeaves = new Leaves;

require_once("phpmailer/class.phpmailer.php");
$objPhpMailer = new PHPMailer();

$rsEmp = $objEmployee->getAllEmployees();

list($leaveTypeId) = $GLOBALS['obj_db']->fetch_array($GLOBALS['obj_db']->execute_query("SELECT leave_type_id FROM leave_types WHERE leave_abbr = 'EL' "));

$currentDateObj = new DateTime();
$message = '';

if($GLOBALS['obj_db']->num_rows($rsEmp) > 0)
{
	$cnt = 0;	
	while($rowEmp = $GLOBALS['obj_db']->fetch_array($rsEmp))
	{
		$cnt++;
		
		$sqlCheck = "SELECT id, earned_type, last_updated, remaining_countable_days FROM leave_balance WHERE emp_id = '".$rowEmp['emp_id']."' AND leave_type_id = '".$leaveTypeId."' AND year = YEAR(CURDATE()) ";
		$rsCheck = $GLOBALS['obj_db']->execute_query($sqlCheck);
		if($GLOBALS['obj_db']->num_rows($rsCheck) > 0)
		{
			$rowCheck = $GLOBALS['obj_db']->fetch_array($rsCheck);
		}
		else
		{
			$message .= "\nEL is not specified for the employee. Please update it once manually.\n";
			$message .= "--------------*********--------------";
			$message .= "\n\n";
			continue;
		}
		
		$message .= $cnt.") ".$rowEmp['first_name']." ".$rowEmp['last_name'].":  \n-------------------------";
		if($rowCheck['last_updated'] == '' || $rowCheck['last_updated'] == '0000-00-00')
		{
			$message .= "\nDate of last updation(EL) is not appropriate for the employee. Please update it once manually.\n";
			$message .= "--------------*********--------------";
			$message .= "\n\n";
			continue;
		}
		if($rowCheck['earned_type'] == 'NA')
		{
			$message .= "\nEmployee is not applicable for EL.\n";
			$message .= "--------------*********--------------";
			$message .= "\n\n";
			continue;
		}
		if($rowEmp['region'] == '')
		{
			$message .= "\nBranch is not set for the employee\n";
			$message .= "--------------*********--------------";
			$message .= "\n\n";
			continue;
		}		
		$lastUpdatedDateObj = new DateTime($rowCheck['last_updated']);
		
		$dateDiffObj = $lastUpdatedDateObj->diff($currentDateObj);
		$dateDiffInDays = (int) $dateDiffObj->format('%a');
		
		$holidaysBetweenDates = $objLeaves->getHolidaysBetweenLeaves($rowCheck['last_updated'], $currentDateObj->format('Y-m-d'), strtolower($rowEmp['region']));
		$holidaysCount = (!empty($holidaysBetweenDates)) ? count($holidaysBetweenDates) : 0;
		
		//$sqlLeaves = "SELECT la.half_day_leave, SUM(la.leaves_required) AS total_leaves FROM leave_applications AS la WHERE la.leave_from_date >= '".$rowCheck['last_updated']."' AND la.leave_from_date < '".$currentDateObj->format('Y-m-d')."' AND la.emp_id = '".$rowEmp['emp_id']."' GROUP BY la.half_day_leave ";
		$sqlLeaves = "SELECT la.half_day_leave, la.leave_from_date, la.leave_to_date, la.leaves_required FROM leave_applications AS la WHERE ((la.leave_from_date >= '".$rowCheck['last_updated']."' AND la.leave_from_date < '".$currentDateObj->format('Y-m-d')."') OR (la.leave_to_date >= '".$rowCheck['last_updated']."' AND la.leave_to_date < '".$currentDateObj->format('Y-m-d')."')) AND la.emp_id = '".$rowEmp['emp_id']."' ";
		$rsLeaves = $GLOBALS['obj_db']->execute_query($sqlLeaves);
		
		$totalLeavesInDays = 0;
		if($GLOBALS['obj_db']->num_rows($rsLeaves) > 0)
		{
			while($rowLeave = $GLOBALS['obj_db']->fetch_array($rsLeaves))
			{
				if($rowLeave['half_day_leave'] == 'YES')
				{
					$leavesIndays = $rowLeave['leaves_required']/2;
				}
				else
				{
					$leaveFromDateObj = new DateTime($rowLeave['leave_from_date']);
					$interval = new DateInterval('P1D');
					$leaveToDateObj = new DateTime($rowLeave['leave_to_date']);
					$leaveToDateObj->add($interval); // To include last date
					
					if($leaveFromDateObj < $currentDateObj && $leaveToDateObj >= $currentDateObj)
					{
						$period = new DatePeriod($leaveFromDateObj, $interval, $currentDateObj);
						// By iterating over the DatePeriod object, all of the recurring dates within that period are printed.
						$leavesIndays = 0;
						foreach($period as $dateObj)
						{
							$leaveDate = $dateObj->format('Y-m-d');
							if(!in_array($leaveDate, $holidaysBetweenDates))
							{
								$leavesIndays++;
							}	
						}
					}
					elseif($leaveFromDateObj < $lastUpdatedDateObj && $leaveToDateObj >= $lastUpdatedDateObj)
					{
						$period = new DatePeriod($lastUpdatedDateObj, $interval, $leaveToDateObj);
						// By iterating over the DatePeriod object, all of the recurring dates within that period are printed.
						$leavesIndays = 0;
						foreach($period as $dateObj)
						{
							$leaveDate = $dateObj->format('Y-m-d');
							if(!in_array($leaveDate, $holidaysBetweenDates))
							{
								$leavesIndays++;
							}	
						}
					}
					else
					{
						$leavesIndays = $rowLeave['leaves_required'];
					}
				}	
				$totalLeavesInDays += $leavesIndays;
			}	
		}	
		
		$totalNonWorkingDaysForEmployee = $totalLeavesInDays+$holidaysCount;
		$totalWorkingDaysForEmployee = $dateDiffInDays-$totalNonWorkingDaysForEmployee;
		$totalWorkingDaysForEmployee += $rowCheck['remaining_countable_days'];
		
		/*
		$noOfElGroups = intval($totalWorkingDaysForEmployee/EL_BASE_DAYS);
		$elToBeAdded = $noOfElGroups*EL_BASE_LEAVE;
		
		$remainingCountableDays = fmod($totalWorkingDaysForEmployee, EL_BASE_DAYS);
		*/
		$elToBeAdded = $totalWorkingDaysForEmployee/EL_BASE_DAYS;
		$elToBeAdded = round($elToBeAdded, 2);
		$remainingCountableDays = 0;
		
		$message .= "\nEL Last Updated - ".$lastUpdatedDateObj->format("d-m-Y");
		$message .= "\nDate Diff (in days) - ".$dateDiffInDays;
		$message .= "\nTotal Holidays - ".$holidaysCount;
		$message .= "\nTotal Leaves - ".$totalLeavesInDays;
		$message .= "\nTotal Non-Working Days - ".$totalNonWorkingDaysForEmployee;
		$message .= "\nPrevious Remaining Countable Days - ".$rowCheck['remaining_countable_days'];
		$message .= "\nTotal Working Days for this period (Date Diff - Total Non-Working Days + Previous Remaining Countable Days) - ".$totalWorkingDaysForEmployee;
		$message .= "\nEL to be added - ".$elToBeAdded;
		//$message .= "\nRemaining Countable Days for Next Updation - ".$remainingCountableDays;
		
		$sqlUpdate = "UPDATE leave_balance SET opening_balance = opening_balance+".$elToBeAdded.", last_updated = '".$currentDateObj->format('Y-m-d')."', remaining_countable_days = ".$remainingCountableDays." WHERE emp_id = '".$rowEmp['emp_id']."' AND leave_type_id = '".$leaveTypeId."' AND year = YEAR(CURDATE()) ";
		$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
		
		$sqlSelectClosing = "SELECT (lb.opening_balance-lb.leaves_availed) AS remaining FROM leave_balance AS lb WHERE emp_id = '".$rowEmp['emp_id']."' AND leave_type_id = '".$leaveTypeId."' AND year = YEAR(CURDATE()) ";
		$rsSelectClosing = $GLOBALS['obj_db']->execute_query($sqlSelectClosing);
		
		if($GLOBALS['obj_db']->num_rows($rsSelectClosing) > 0)
		{
			$rowSelectClosing      = $GLOBALS['obj_db']->fetch_array($rsSelectClosing);
			$updatedClosingBalance = $rowSelectClosing['remaining'];
		}
		
		$message .= "\nUpdated Closing Balance - ".$updatedClosingBalance;
		$message .= "\n\n";
		
		if($rsUpdate)
		{
			$message .= "EL has been successfully updated.\n";
			$message .= "--------------*********--------------";
			$message .= "\n\n";
		}
				
	}	
}
else
{
	$message .= "No data found for employees.";
}	

//$objPhpMailer->IsSendmail();                     // telling the class to use SendMail transport
$objPhpMailer->IsMail();                           // telling the class to use PHP mail() function for sending mails
/*
$objPhpMailer->IsSMTP();                           // telling the class to use SMTP
$objPhpMailer->SMTPAuth   = true;                  // enable SMTP authentication
$objPhpMailer->SMTPSecure = SMTP_SECURE;           // sets the prefix to the servier
$objPhpMailer->Host       = SMTP_HOST;             // sets GMAIL as the SMTP server
$objPhpMailer->Port       = SMTP_PORT;             // set the SMTP port for the GMAIL server
$objPhpMailer->Username   = SMTP_USER;             // GMAIL username
$objPhpMailer->Password   = SMTP_PASS;             // GMAIL password	
*/

$objPhpMailer->SetFrom(DEFAULT_FROM_EMAIL, DEFAULT_FROM_NAME);
//$objPhpMailer->AddAddress('erbharat.007@gmail.com', 'Bharat Ojha');
$objPhpMailer->AddAddress('r.mehrotra@bergengroupindia.com', 'Rajat Malhotra');

$objPhpMailer->Subject = "Auto EL Updation Report :: ".$currentDateObj->format("d-m-Y");

$messageMail = str_replace("\n", "<br />", $message);
$objPhpMailer->MsgHTML($messageMail);

if(!$objPhpMailer->Send()) 
{
	$message .= "\n\nReport Couldn't be sent on mail due to the below error : ".$objPhpMailer->ErrorInfo;
} 
else 
{
	$message .= "\n\nReport has been sent on mail.";
}
$objPhpMailer->ClearAllRecipients();

$file = fopen("cron/logs/log_".$currentDateObj->format("d_m_Y").'.txt', "w");
if($file)
{
	$bytesWritten = fwrite($file, $message);
}	
fclose($file);

$messageShow = str_replace("\n", "<br />", $message);
echo $messageShow;
?>
