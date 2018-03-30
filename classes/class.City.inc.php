<?php
class City
{
    public function City($arrayData)
    {
		list($countryId, $cityName, $isoCode3, $status) = $arrayData;
				
		$table_name = 'city';
		$fields=array();
		
		$fields[]=array('name'=>'country_id','value'=>$countryId);
		$fields[]=array('name'=>'city_name','value'=>$cityName);
		$fields[]=array('name'=>'is_metro_city','value'=>$isoCode3);			
		$fields[]=array('name'=>'status','value'=>$status);
		$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
		
		$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
		$flag=$GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
		{
			$msg = "Error while adding City.";
		}
		else
		{
			$id=$GLOBALS['obj_db']->last_insert_id();
			$msg = "City added successfully";
			$_SESSION['success'] = 1;
		}
			
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND city_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' " : "";
        return $search_text_condition;
    }

	public function getCityData($id)
	{
		$que = "SELECT * FROM city WHERE city_id= '".$id."'";
		$exe = $GLOBALS['obj_db']->execute_query($que);
		return $exe;
	}

	public function getAll($start = '', $end = '', $includeSearchText = true)
    {
		$search_text = '';
		if($_REQUEST['search_text'] != "")
		{
			$search_text = $_REQUEST['search_text'];
		}
		$search_text_condition = $this->getSearchText($search_text);
		
		$query = "SELECT * FROM city WHERE 1 = 1";
		if($includeSearchText)
		{
			$query .= $search_text_condition;
		}
		
		$query .= " ORDER BY city_id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
    }

	 public function update($arrayData)
    {
		list($countryId, $countryName, $isoCode2, $isoCode3, $status) = $arrayData;
		
		$query = "UPDATE city SET country_name = '".$countryName."', iso_code_2 = '".$isoCode2."', iso_code_3 = '".$isoCode3."', status = '".$status."' WHERE country_id	= '".$countryId."'";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating city.";
		}
		else
		{
			$msg = "City updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE FROM city where city_id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting city.";
        }
		else
		{
            $msg = "city deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($cityName)
	{
		$query = "SELECT city_id FROM city WHERE city_name = '".$cityName."'";
		$rs = $GLOBALS['obj_db']->execute_query($query);
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
}
?>
