<?php
class Country
{
    public function add($arrayData)
    {
		list($countryName, $isoCode2, $isoCode3, $status) = $arrayData;
		if($this->isDuplicate($countryName))
		{
			$msg = "Country already exists.";
		}
		else
		{		
			$table_name = 'country';
			$fields=array();

			$fields[]=array('name'=>'country_name','value'=>$countryName);
			$fields[]=array('name'=>'iso_code_2','value'=>$isoCode2);
			$fields[]=array('name'=>'iso_code_3','value'=>$isoCode3);
			$fields[]=array('name'=>'status','value'=>$status);
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding Country.";
			}
			else
			{
				$id=$GLOBALS['obj_db']->last_insert_id();
				$msg = "Country added successfully";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND country_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' " : "";
        return $search_text_condition;
    }

	public function getCountryData($id)
	{
		$que = "SELECT * FROM country WHERE country_id= '".$id."'";
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
		
		$query = "SELECT * FROM country WHERE 1 = 1";
		if($includeSearchText)
		{
			$query .= $search_text_condition;
		}
		
		$query .= " ORDER BY country_id DESC";
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
		
		$query = "UPDATE country SET country_name = '".$countryName."', iso_code_2 = '".$isoCode2."', iso_code_3 = '".$isoCode3."', status = '".$status."' WHERE country_id	= '".$countryId."'";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating country.";
		}
		else
		{
			$msg = "Country updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE FROM country where country_id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting country.";
        }
		else
		{
            $msg = "country deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($countryName)
	{
		$query = "SELECT country_id FROM country WHERE country_name = '".$countryName."'";
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