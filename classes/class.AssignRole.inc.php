<?php
// Class for Assign Roles
class AssignRoles
{
     function add()
	 {
	     $query= "UPDATE employees SET role_id='".$_REQUEST['lstAllRoles']."' WHERE emp_id='".$_REQUEST['hdnUserID']."'";
		 $flag=$GLOBALS['obj_db']->execute_query($query);
		    
			if($flag)
			{
				$msg="Role edited successfully";
				$_SESSION['success'] = 1;
			}				
			else
			{
				$msg="Error while editing role";
			}				
			return $msg; 
	 }
	 
	function search()
	{
	
		if($_REQUEST['lstSearchBy']==0)
		{
			$query="SELECT * FROM employees WHERE status='active' AND first_name like '%".addslashes(trim($_REQUEST['txtSearch']))."%'";
			  $rs=$GLOBALS['obj_db']->execute_query($query);
			  	
		}
		else
		{
		  $query="SELECT * FROM employees WHERE status='active' and email like '%".addslashes(trim($_REQUEST['txtSearch']))."%'";
			$rs=$GLOBALS['obj_db']->execute_query($query);
			
		}
		return $rs;
	}

	function getrowbyid($id)
	{
		$query="SELECT * FROM employees WHERE emp_id ='".$id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}

	function getalladmin()
	{
	
		$query="SELECT * FROM employees WHERE status='active'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	function getall()
	{
		$query="SELECT * FROM employees WHERE status='active'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}

	function getallusers()
	{
	
		$query="SELECT * FROM employees ORDER BY emp_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
}
?>