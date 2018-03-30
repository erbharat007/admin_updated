<?php
class Holiday
{
    public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND (reason_of_holiday LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR date = '".$GLOBALS['obj_db']->escape($search_text)."') " : "";
        return $search_text_condition;
    }

	public function getHolidaysOfYear($year = 'all', $region, $start = '', $end = '')
    {
		$search_text = '';
		if($_REQUEST['search_text'] != "")
		{
			$search_text = $_REQUEST['search_text'];
		}
		$search_text_condition = $this->getSearchText($search_text);
		
		$query = "SELECT * FROM holiday_calendar_".$region." WHERE 1 = 1 ";
		if($year == 'currentYear')
		{
			$query .= " AND year = YEAR(CURDATE()) ";
		}
		$query .= $search_text_condition;
		
		$query .= " ORDER BY date ASC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);
        return $rsQuery;
    }

	 public function updateHolidays($arrayData)
    {
		list($holidayDates, $region) = $arrayData;
		
		$holidayDatesArray = explode(',', $holidayDates);
		/*echo '<pre>';
		print_r($holidayDatesArray);
		echo '</pre>';die;*/
		if(count($holidayDatesArray) > 0)
		{
			$validDates = array();
			foreach($holidayDatesArray as $holidayDate)
			{
				$dateObj = DateTime::createFromFormat('m/d/Y', trim($holidayDate));
				$validDates[] = $dateObj->format('Y-m-d');
			}
		}	
		$query = "DELETE FROM holiday_calendar_".$region." WHERE date NOT IN ('".implode("', '", $validDates)."') ";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error occured while updating holiday calendar. Try again.";
		}
		else
		{
			$rsHolidays = $this->getHolidaysOfYear('all', $region);
			$alreadyAddedDates = array();
			if($GLOBALS['obj_db']->num_rows($rsHolidays) > 0)
			{
				while($row = $GLOBALS['obj_db']->fetch_array($rsHolidays))
				{
					$alreadyAddedDates[] = $row['date'];
				}	
			}

			$datesToBeAdded = array_diff($validDates, $alreadyAddedDates);
			if(count($datesToBeAdded) > 0)
			{
				$sqlAdd = "INSERT INTO holiday_calendar_".$region."(date, year) VALUES ";
				$valueStr = '';
				foreach($datesToBeAdded as $date)
				{
					if($valueStr == '')
					{
						$valueStr = " ('".$date."', YEAR('".$date."')) ";
					}
					else
					{
						$valueStr .= ", ('".$date."', YEAR('".$date."')) ";
					}				    
				}
				$sqlAdd .= $valueStr;
				
				$UpdateFlag = $GLOBALS['obj_db']->execute_query($sqlAdd);
				if($UpdateFlag)
				{
					$msg = " Holiday Calendar updated successfully";
					$_SESSION['success'] = 1;
				}
				else
				{
					$msg = "Error occured while updating holiday calendar. Try again.";
				}
			}
			else
			{
				$msg = " Holiday Calendar updated successfully";
				$_SESSION['success'] = 1;
			}	
		}
		return $msg;
	}
}
?>