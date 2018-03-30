<?php
class Department
{
    public function add($arrayData)
    {
		list($dept_name) = $arrayData;
		if($this->isDuplicate($dept_name))
		{
			$msg = "Department already exists.";
		}
		else
		{	
			$table_name = 'departments';
			$fields=array();
			$fields[]=array('name'=>'dept_name','value'=>$GLOBALS['obj_db']->escape($dept_name));
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding department.";
			}
			else
			{
				$msg = "Department added successfully";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND dept_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' " : "";
        return $search_text_condition;
    }

	public function getDeptData($id)
	{
		$que = "SELECT * FROM departments WHERE dept_id= '".$id."'";
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
		
		$query = "SELECT * FROM departments WHERE 1 = 1";
		if($includeSearchText)
		{
			$query .= $search_text_condition;
		}
		
		$query .= " ORDER BY dept_id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
    }

	 public function update($arrayData)
    {
		list($id, $dept_name) = $arrayData;
		
		$query = "UPDATE departments SET dept_name = '".$GLOBALS['obj_db']->escape($dept_name)."' WHERE dept_id	= '".$id."'";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating department.".$query;
		}
		else
		{
			$msg = "Department updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE FROM departments where dept_id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting department.";
        }
		else
		{
            $msg = "Department deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($dept_name)
	{
		$query = "SELECT dept_id FROM departments WHERE dept_name = '".$dept_name."'";
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