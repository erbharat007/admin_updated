<?php
// Class for Console User
class ConsoleUser
{
	function add()
	{
		if($this->is_name_exist($_REQUEST['txtEmail']))
		{
		   $msg="Login Id already exists!";
		}
		else
		{
			$table_name="user";
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('user_name',$_REQUEST['txtEmail']);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('full_name',$_REQUEST['txtUserName']);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('password',md5(trim($_REQUEST['txtPassword'])));
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('email',$_REQUEST['txtEmail']);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('status',1);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('site_id',$_SESSION['SiteID']);
			$fields[]=$GLOBALS['obj_db']->get_field_for_query('hotel_id',$_SESSION['sel_hotel']);
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			
			if($flag)
			{
				$msg="User added successfully";
				$UserID = mysql_insert_id();
				$query="update user set sent_password_status = '1' where user_id='".$UserID."'";
				$GLOBALS['obj_db']->execute_query($query);
				$_SESSION['success'] = 1;
			}
			else
			{
				$msg="Error while adding user";			
			}	
		}
		return $msg;
	}
	
 	
	function search()
	{
		if($_REQUEST['lstSearchBy']==1)
		{
			$fetch_hotel="select * from hotel";
			$hotel_exe=$GLOBALS['obj_db']->execute_query($fetch_hotel);			
			
			$query = "select * from user where email like'%".addslashes(trim($_REQUEST['txtSearch']))."%' group by user_id";
			$rs = $GLOBALS['obj_db']->execute_query($query);
		}
		if($_REQUEST['lstSearchBy']==2)
		{
			$fetch_hotel="select * from hotel";
		    $hotel_exe=$GLOBALS['obj_db']->execute_query($fetch_hotel) or die(mysql_error());
			$query="select u.* from user u, user_roles ur, role_details rd where u.full_name like'%".addslashes(trim($_REQUEST['txtSearch']))."%' and ur.role_id=rd.role_id and ur.user_id=u.user_id  and u.hotel_id='".$_SESSION['sel_hotel']."'  group by u.user_id";
			$rs=$GLOBALS['obj_db']->execute_query($query);
		}	
		return $rs;
	}
	
	function searchadmin()
	{
		if($_REQUEST['lstSearchBy']==1)
		{
			$query="select * from user where site_id='".$_SESSION['SiteID']."' and user_name like'%".addslashes($_REQUEST['txtSearch'])."%'"."and status=1 order by user_id ";
		}
		else if($_REQUEST['lstSearchBy']==2)
		{
			$query="select * from user where site_id='".$_SESSION['SiteID']."' and email like'%".addslashes($_REQUEST['txtSearch'])."%'"."and status=1 order by user_id" ;
		}
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	function edit()
	{			
		if($this->is_name_existforedit($_REQUEST['txtEmail'], $_REQUEST['hdnUserID']))
		{
		     $msg="Login Id already exists!";
		}
		else
		{	
			$query="select password from user where user_id='".$_REQUEST['hdnUserID']."'";
			$rs=$GLOBALS['obj_db']->execute_query($query);			
			$row=$GLOBALS['obj_db']->fetch_array($rs);
			
			if(trim($_REQUEST['txtPassword'])!='' && ($row["password"] == md5($_REQUEST['txtPassword']))) 
			{
				$msg = "Your new password should be different from previous password.";
				
			}
			else
			{
				$table_name="user";
				$fields[]=$GLOBALS['obj_db']->get_field_for_query('full_name',$_REQUEST['txtUserName']);
				$fields[]=$GLOBALS['obj_db']->get_field_for_query('user_name',$_REQUEST['txtEmail']);
				if(trim($_REQUEST['txtPassword'])!=''){
					$fields[]=$GLOBALS['obj_db']->get_field_for_query('password',md5(trim($_REQUEST['txtPassword'])));
				}
				$fields[]=$GLOBALS['obj_db']->get_field_for_query('email',$_REQUEST['txtEmail']);
				$fields[]=$GLOBALS['obj_db']->get_field_for_query('is_blocked',$_REQUEST['is_blocked']);
				$fields[]=$GLOBALS['obj_db']->get_field_for_query('hotel_id',$_SESSION['sel_hotel']);
				$condition=" where user_id='".$_REQUEST['hdnUserID']."'";
				
				$query=$GLOBALS['obj_db']->create_update_query($table_name,$fields,$condition);
				$flag=$GLOBALS['obj_db']->execute_query($query);
	
				if($_REQUEST['is_blocked'] == 'No') 
				{
					$GLOBALS['obj_db']->execute_query("delete from login_strike where user_id = '".$_REQUEST['hdnUserID']."'");
				}
	
				if($flag)
				{
					$msg="User edited successfully";
					if(trim($_REQUEST['txtPassword'])!='')
					{
						$query="update user set sent_password_status = '1' where user_id='".$_REQUEST['hdnUserID']."'";
						$GLOBALS['obj_db']->execute_query($query);
						$_SESSION['success'] = 1;	
					}
				}
				else
				{
					$msg="Error while editing user";
				}
			}
		}
		return $msg;
	}
	
	function is_name_exist($name)
	{
		$query="select * from user where site_id='".$_SESSION['SiteID']."' and user_name='".$name."' order by user_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$num_rows=$GLOBALS['obj_db']->num_rows($rs);
		if($num_rows>0)
			return true;
		else
			return false;
	}

	function is_name_existforedit($name,$id)
	{
		$query="select * from user where  room_type='".$name."' and room_type_id<>'".$id."'" ;
		$query="select * from user where site_id='".$_SESSION['SiteID']."' and user_name='".$name."' and user_id<>'".$id."' order by user_id";
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
				$query="delete from user where user_id='".$hdnIDs[$i]."'";
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
	
	
	function getall()
	{
		$query="select * from employees  where hotel_id='".$_SESSION['sel_hotel']."'  group by user_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		
		return $rs;
	}
	
	
	function getalladmin()
	{
		$query="select * from user where site_id='".$_SESSION['SiteID']."' and status=1 order by user_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
		
	function getrowbyidadmin($id)
	{
		
		$query="select * from user where site_id='".$_SESSION['SiteID']."' and user_id ='".$id."' order by user_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	
	function getrowbyid($id)
	{
		$query="select * from user where site_id='".$_SESSION['SiteID']."' and user_id ='".$id."' order by user_id";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	
	function getservicenamebyid($service_id)
	{
		$query="select * from services where service_id='".$service_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$row=$GLOBALS['obj_db']->fetch_array($rs);
		$name=$row['service_name'];
		return $name;
	}
	
	function getfeatureid($role_id)
	{
		$query="select distinct feature_id from role_details where role_id='".$role_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}
	
	function gethotelid($role_id)
	{
		$query="select * from role_details where role_id='".$role_id."' ";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		return $rs;
	}		

	function getfeaturename($feature_id)
    {
        $query="select * from features where feature_id='".$feature_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$row=$GLOBALS['obj_db']->fetch_array($rs);
		$name=$row['feature_name'];
		return $name; 
    }

	function isfeatureinrole($feature_id,$role_id)
	{
		$query="SELECT * FROM role_details WHERE role_id='".$role_id."' AND feature_id='".$feature_id."'";
		$rs=$GLOBALS['obj_db']->execute_query($query);
		$num_rows=$GLOBALS['obj_db']->num_rows($rs);
		if($num_rows>0)
			return true;
		else
			return false;
	}
	
	function ChangePassword() 
	{
		$txtOldPassword = trim($_REQUEST['txtOldPassword']);
		$txtNewPassword = trim($_REQUEST['txtNewPassword']);
		$txtConfirmPassword = trim($_REQUEST['txtConfirmPassword']);

		if(isset($_SESSION['adminUser']) && $_SESSION['adminUser'] == 1)
		{
			$query = "SELECT * FROM admin_users WHERE user_id='".$_SESSION['userId']."' AND password='".md5($txtOldPassword)."'";
		}
		else
		{
			$query = "SELECT * FROM employees WHERE emp_id='".$_SESSION['userId']."' AND password='".md5($txtOldPassword)."'";
		}	
		$rs = $GLOBALS['obj_db']->execute_query($query);
		
		$num_rows=$GLOBALS['obj_db']->num_rows($rs);
		if($num_rows == 0) 
		{
			return "You entered wrong Old Password. Please try again.";
		} 
		else 
		{
			if(isset($_SESSION['adminUser']) && $_SESSION['adminUser'] == 1)
			{
				$query="UPDATE admin_users SET password = '".md5($txtNewPassword)."', last_password_change = CURDATE() WHERE user_id='".$_SESSION['userId']."'";
			}
			else
			{
				$query="UPDATE employees SET password = '".md5($txtNewPassword)."', last_password_change = CURDATE() WHERE emp_id='".$_SESSION['userId']."'";
			}
			
			$GLOBALS['obj_db']->execute_query($query);
			$_SESSION['ForceChangePassword'] = "";
			unset($_SESSION['ForceChangePassword']);
			return "Your new password has been set successfully.";
		}
	}

	function ResetPassword($UserID,$Email)
	{
		$password_generated =  generatePassword(8);
		$msg = '<table width="100%" border="0" cellspacing="0" cellpadding="0"  style="background-color:#ffffff;"><tr><td colspan="2">Dear '.$_REQUEST['txtUserName'].',</td></tr>
				<tr><td colspan="2"> Your login details are as follows:</td></tr>
				<tr><td>URL:</td>
				<td><a href="'.SITE_URL.'" target="new">'.SITE_URL.'</a></td></tr>
				<tr>
				<td>Your UserName:&nbsp;</td>
				<td>'.$Email.'</td></tr>
				<tr><td>Your Password:&nbsp;</td>
				<td>'.$password_generated.'</td></tr></table>';
		$mail_subject = "Your Password";
		$headers = "MIME-Version: 1.0\n";
		$headers.= "Content-type: text/html; charset=iso-8859-1\n";
		$headers.="From: ".ADMIN_EMAIL."\n";
		$mail_flag = @mail($Email, $mail_subject, $msg, $headers);
		
		$resetdate = date('Y-m-d H:i:s', strtotime("now -31 days") );
		if($mail_flag)
		{
			$query="UPDATE employees SET password='".md5($password_generated)."', sent_password_status = '1' WHERE emp_id='".$UserID."'";
			$GLOBALS['obj_db']->execute_query($query);
		}
		return "Password has been sent to the user ".ucwords($_REQUEST['txtUserName']);
	}
}
?>