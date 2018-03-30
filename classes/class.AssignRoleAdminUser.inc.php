<?php
// Class for Assign Roles to Admin Users
class AssignRoleAdminUser
{
     function add()
	 {
	     $query= "UPDATE admin_users SET role_id='".$_REQUEST['lstAllRoles']."' WHERE user_id='".$_REQUEST['hdnUserID']."'";
		 $flag=$GLOBALS['obj_db']->execute_query($query);
		    
		 if($flag)
		 {
			$msg="Role successfully assigned to user";
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
			$query="SELECT * FROM admin_users WHERE status='active' AND first_name like '%".addslashes(trim($_REQUEST['txtSearch']))."%'";
		}
		else
		{
		    $query="SELECT * FROM admin_users WHERE status='active' and email like '%".addslashes(trim($_REQUEST['txtSearch']))."%'";
		}
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}

	function getrowbyid($id)
	{
		$query="SELECT * FROM admin_users WHERE user_id ='".$id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}

	function getall()
	{
		$query="SELECT * FROM admin_users WHERE status='active'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
}
?>