<?php
class UserRoles
{
	function add()
	{
		if($this->is_name_exist($_REQUEST['txtRoleName']))
		{
			$msg="Role already exists";
		}
		else
		{
			$table_name="roles";
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('role_name',$_REQUEST['txtRoleName']);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('status',1);
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			 $last_role_id = $GLOBALS['obj_db']->insert_id();
			$this->add_features($last_role_id);
			if($flag)
			{
				$msg="Role added successfully";
				$_SESSION['success'] = 1;
			}				
			else
			{
				$msg="Error while adding role";
			}				
		}
		return $msg;
	}
	
	function add_features($role_id)
	{
		$table_name="role_details";
		$arrFeatureID=explode(";",$_REQUEST['hdnFeatureID']);		
		
		for($j=0;$j<count($arrFeatureID);$j++)
		{
			$fields=array();
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('role_id',$role_id);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('feature_id',$arrFeatureID[$j]);
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			
			$flag=$GLOBALS['obj_db']->execute_query($query);
		}		
	}	
 	
	function search()
	{
	  $tempquery="select r.* from roles r, role_details rd where r.role_name like'%".addslashes(trim($_REQUEST['txtSearchRole']))."%'"."and status=1 and rd.role_id=r.role_id GROUP BY r.role_id order by role_id";
		$rs=$GLOBALS['obj_db']->execute_query($tempquery);
		return $rs;	 
	}
	
	function edit()
	 {
			$query="delete from role_details where role_id='".$_REQUEST['hdnRoleID']."'" ;
			$flag1=$GLOBALS['obj_db']->execute_query($query);
			$table_name="roles";
			
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('role_name',$_REQUEST['txtRoleName']);
			$condition=" where role_id='".$_REQUEST['hdnRoleID']."'";
			$query=$GLOBALS['obj_db']->create_update_query($table_name,$fields,$condition);
			
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
				
			$role_id=$_REQUEST['hdnRoleID'];
			$this->add_features($role_id);
		return $msg;
	 }

	function is_name_existforedit($name,$id)
	{
		$query="select * from roles where role_name='".$name."' and role_id<>'".$id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$num_rows=$GLOBALS['obj_db']->num_rows($rs);
		if($num_rows>0)
			return true;
		else
			return false;
	}
	
	function is_name_exist($role_name)
	{
		$query="select * from roles where role_name='".$role_name."'"." order by role_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$num_rows=$GLOBALS['obj_db']->num_rows($rs);
		if($num_rows>0)
			return true;
		else
			return false;
	}

	function delete()
	{
	   $hdnIDs=explode(",",$_REQUEST['hdnIDs']);
		if(count($hdnIDs)>0)
		{
			$delctr=0;
			for($i=0;$i<count($hdnIDs);$i++)
			{
				$query="delete from roles where role_id='".$hdnIDs[$i]."'";
				$flag=$GLOBALS['obj_db']->execute_query($query);
				if($flag) $delctr++;
			}
			if($flag)
			{
				$msg=$delctr." record deleted";
				$_SESSION['success'] = 1;
			}				
			else
			{
				$msg="Error while deleting";
			}				
		}
		return $msg;
	}		
		
	function getallfeatures()
	{
		$query="select * from features order by service_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
   function getallroles()
   {
       $query="select r.* from roles r, role_details rd where r.role_id=rd.role_id and r.status=1 group by r.role_id order by r.role_id ";
	   $rs=$GLOBALS['obj_db']->execute_query($query);
	   return $rs;
   }

	function getallrolesadmin()
	{
		$query="select * from roles where status=1 order by role_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	function getallroles1()
	{
		$query="SELECT * from roles where status=1 order by role_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	function getrowbyid($id)
	{
		$query="SELECT * from roles where role_id ='".$id."'" ."order by role_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	function getroleid($user_id)
	{
		$query="SELECT * from employees where emp_id='".$user_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	function getRoleIdAdminUser($user_id)
	{
		$query="SELECT * FROM admin_users where user_id='".$user_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	function getrolename($role_id)
    {
        $query="SELECT * from roles where role_id='".$role_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$row=$GLOBALS['obj_db']->fetch_array($rs);
		$name=$row['role_name'];
		return $name; 
    }
	
	function getrolenamebyid($role_id)
    {
        $query="SELECT * from role where role_id='".$role_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs; 
    }
		
	
}
?>
