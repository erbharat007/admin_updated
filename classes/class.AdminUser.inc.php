<?php
class AdminUser
{
    public function add($arrayData)
    {
		list($first_name, $last_name, $email, $password, $phone, $status, $user_type, $isCentralised, $branchId) = $arrayData;
	
		if($this->isDuplicate($email, $phone))
		{
			$msg = "Either Email, phone number already exists.";
		}
		else
		{
			if(!empty($isCentralised) && $user_type == 'finance')
			{
				$sqlUpdate = "UPDATE admin_users SET centralised_admin = 'NO' WHERE user_type = '".$user_type."' ";
				$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
			}
			
			$table_name = 'admin_users';
			$fields=array();

			$fields[]=array('name'=>'first_name','value'=>$GLOBALS['obj_db']->escape($first_name));
			$fields[]=array('name'=>'last_name','value'=>$GLOBALS['obj_db']->escape($last_name));
			$fields[]=array('name'=>'email','value'=>$email);
			$fields[]=array('name'=>'password','value'=>md5($password));
			$fields[]=array('name'=>'phone','value'=>$phone);
			$fields[]=array('name'=>'status','value'=>$GLOBALS['obj_db']->escape($status));
			$fields[]=array('name'=>'user_type','value'=>$user_type);
			if(!empty($isCentralised) && $user_type == 'finance')
			{
				$fields[]=array('name'=>'centralised_admin','value'=>'YES');
			}
			$fields[]=array('name'=>'branch_id','value'=>$branchId);
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));			
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding user.";
			}
			else
			{
				$msg = "User added successfully";
				$_SESSION['success'] = 1;
			}
		}
		return $msg;
    }

	public function getSearchText($search_text)
    {
		$search_text_condition = ($GLOBALS['obj_db']->escape($search_text) != "") ? " AND (first_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR last_name LIKE '%".$GLOBALS['obj_db']->escape($search_text)."%' OR email = '".$search_text."')" : "";
        return $search_text_condition;
    }

	public function getUserData($id)
	{
		$query = "SELECT au.* FROM admin_users AS au WHERE au.user_id= '".$id."' ";
		$exe = $GLOBALS['obj_db']->execute_query($query);
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
		
		$query = "SELECT au.* FROM admin_users AS au ";
		$query .= " WHERE 1 = 1 ";
		$query .= $search_text_condition;		
		
		$query .= " ORDER BY au.user_id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
    }

	 public function update($arrayData)
    {
		list($id, $first_name, $last_name, $email, $password, $phone, $status, $user_type, $isCentralised, $branchId) = $arrayData;
		
		if(!empty($isCentralised) && $user_type == 'finance')
		{
			$sqlUpdate = "UPDATE admin_users SET centralised_admin = 'NO' WHERE user_type = '".$user_type."' ";
			$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);
			
			$isCentralised = 'YES';
		}
		else
		{
			$isCentralised = 'NO';
		}		
		$query = "UPDATE admin_users SET ";
		$query .= " first_name = '".$GLOBALS['obj_db']->escape($first_name)."',
					last_name = '".$GLOBALS['obj_db']->escape($last_name)."',
					email = '".$email."', ";
					if($password != DUMMY_PASS)
					{
						$query .= " password = '".md5($password)."', ";	
					}	
					
		$query .=	" 
					phone = '".$phone."',					
					status = '".$status."',
					user_type = '".$user_type."',
					centralised_admin = '".$isCentralised."',
					branch_id = '".$branchId."',
					last_updated = '".date('Y-m-d H:i:s')."'
					WHERE 
					user_id	= '".$id."'";

		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating user data.";
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
		$query = "DELETE from admin_users WHERE user_id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting data.";
        }
		else
		{
            $msg = "User deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function isDuplicate($email, $phone)
	{
		$query = "SELECT user_id FROM admin_users WHERE 1=1 AND ((email = '".$email."' AND email != '') OR (phone = '".$phone."' AND phone != '')) ";
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