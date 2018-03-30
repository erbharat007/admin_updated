<?php
class ActivityCategory
{
    public function add($arrayData)
    {
		list($category) = $arrayData;
		if($this->isDuplicate($category))
		{
			$msg = "Activity Category already exists.";
		}
		else
		{		
			$table_name = 'activity_categories';
			$fields=array();

			$fields[]=array('name'=>'category','value'=>$GLOBALS['obj_db']->escape($category));
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding Activity Category.";
			}
			else
			{
				$msg = "Activity Category added successfully";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND category LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' " : "";
        return $search_text_condition;
    }

	public function getCatData($id)
	{
		$que = "SELECT * FROM activity_categories WHERE id= '".$id."'";
		$exe = $GLOBALS['obj_db']->execute_query($que);
		return $exe;
	}

	public function getAll($start = '', $end = '')
    {
		$search_text = '';
		if($_REQUEST['search_text'] != "")
		{
			$search_text = $_REQUEST['search_text'];
		}
		$search_text_condition = $this->getSearchText($search_text);
		
		$query = "SELECT * FROM activity_categories WHERE 1 = 1";
		$query .= $search_text_condition;
		
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
		list($id, $category) = $arrayData;
		
		$query = "UPDATE activity_categories SET category = '".$GLOBALS['obj_db']->escape($category)."' WHERE id = '".$id."'";
		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating Activity Category.";
		}
		else
		{
			$msg = "Activity Category updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE FROM activity_categories WHERE id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting Activity Category.";
        }
		else
		{
            $msg = "Activity Category deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($category)
	{
		$query = "SELECT id FROM activity_categories WHERE category = '".$GLOBALS['obj_db']->escape($category)."' ";
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