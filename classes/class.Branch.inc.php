<?php
class Branch
{
    public function add($arrayData)
    {
		list($cityName, $region) = $arrayData;
		if($this->isDuplicate($cityName, $region))
		{
			$msg = "Branch already exists.";
		}
		else
		{		
			$table_name = 'branches';
			$fields=array();

			$fields[]=array('name'=>'city_name','value'=>$cityName);
			$fields[]=array('name'=>'region','value'=>$region);
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding branch.";
			}
			else
			{
				$id=$GLOBALS['obj_db']->last_insert_id();
				$msg = "Branch added successfully";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND (city_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR region LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%') " : "";
        return $search_text_condition;
    }

	public function getBranchData($id)
	{
		$que = "SELECT * FROM branches WHERE id= '".$id."'";
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
		
		$query = "SELECT * FROM branches WHERE 1 = 1";
		if($includeSearchText)
		{
		   $query .= $search_text_condition; 
		}		
		
		$query .= " ORDER BY id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
    }

	 public function update($arrayData)
    {
		list($id, $cityName, $region) = $arrayData;
		
		$query = "UPDATE branches SET city_name = '".$cityName."', region = '".$region."' WHERE id	= '".$id."'";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating branch.";
		}
		else
		{
			$msg = "Branch updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE FROM branches where id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting branch.";
        }
		else
		{
            $msg = "Branch deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($cityName, $region)
	{
		$query = "SELECT id FROM branches WHERE city_name = '".$cityName."' AND region = '".$region."' ";
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