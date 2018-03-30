<?php
class Designation
{
    public function add($arrayData)
    {
		list($designation) = $arrayData;
		if($this->isDuplicate($designation))
		{
			$msg = "Designation already exists.";
		}
		else
		{		
			$table_name = 'designations';
			$fields=array();

			$fields[]=array('name'=>'designation','value'=>$designation);
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding designation.";
			}
			else
			{
				$id=$GLOBALS['obj_db']->last_insert_id();
				$msg = "Designation added successfully";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND designation LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' " : "";
        return $search_text_condition;
    }

	public function getDesignationData($id)
	{
		$que = "SELECT * FROM designations WHERE id= '".$id."'";
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
		
		$query = "SELECT * FROM designations WHERE 1 = 1";
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
		list($id, $designation) = $arrayData;
		
		$query = "UPDATE designations SET designation = '".$designation."' WHERE id	= '".$id."'";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating data.";
		}
		else
		{
			$msg = "Data updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE FROM designations where id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting data.";
        }
		else
		{
            $msg = "Data deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($designation)
	{
		$query = "SELECT id FROM designations WHERE designation = '".$designation."'";
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