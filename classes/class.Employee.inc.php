<?php
class Employee
{
    public function add($arrayData)
    {
		list($first_name, $last_name, $designation_id, $department_id, $email, $password, $address, $phone, $emp_code, $status, $emp_category, $birth_date, $pan_number, $passport_number, $HOD_1_id, $HOD_2_id, $hr_id, $branchId, $bankName, $bankAcNumber, $bankIfsCode, $bankBranchAddress, $joiningDate) = $arrayData;
	
		$birth_date = getDbDateFormat($birth_date);
		$joiningDate = getDbDateFormat($joiningDate);
		
		if($this->isDuplicate($email, $phone, $emp_code, $pan_number, $passport_number))
		{
			$msg = "Either Email, phone number, employee code, PAN number, passport number already exists.";
		}
		else
		{
			$table_name = 'employees';
			$fields=array();

			$fields[]=array('name'=>'first_name','value'=>$GLOBALS['obj_db']->escape($first_name));
			$fields[]=array('name'=>'last_name','value'=>$GLOBALS['obj_db']->escape($last_name));
			$fields[]=array('name'=>'designation_id','value'=>$GLOBALS['obj_db']->escape($designation_id));
			$fields[]=array('name'=>'department_id','value'=>$GLOBALS['obj_db']->escape($department_id));
			$fields[]=array('name'=>'email','value'=>$email);
			$fields[]=array('name'=>'password','value'=>md5($password));
			$fields[]=array('name'=>'address','value'=>$GLOBALS['obj_db']->escape($address));
			$fields[]=array('name'=>'phone','value'=>$phone);
			$fields[]=array('name'=>'emp_code','value'=>$emp_code);
			$fields[]=array('name'=>'status','value'=>$GLOBALS['obj_db']->escape($status));
			$fields[]=array('name'=>'emp_category','value'=>$emp_category);
			$fields[]=array('name'=>'birth_date','value'=>$birth_date);
			$fields[]=array('name'=>'joining_date','value'=>$joiningDate);
			$fields[]=array('name'=>'pan_number','value'=>$pan_number);
			$fields[]=array('name'=>'passport_number','value'=>$passport_number);
			$fields[]=array('name'=>'HOD_1_id','value'=>$HOD_1_id);
			$fields[]=array('name'=>'HOD_2_id','value'=>$HOD_2_id);
			$fields[]=array('name'=>'hr_id','value'=>$hr_id);
			$fields[]=array('name'=>'branch_id','value'=>$branchId);
			$fields[]=array('name'=>'bank_name','value'=>$bankName);
			$fields[]=array('name'=>'bank_ac_number','value'=>$bankAcNumber);
			$fields[]=array('name'=>'bank_ifsc_Code','value'=>$bankIfsCode);
			$fields[]=array('name'=>'bank_branch_address','value'=>$GLOBALS['obj_db']->escape($bankBranchAddress));
			$fields[]=array('name'=>'created_date','value'=>date('Y-m-d H:i:s'));
			
			$query=$GLOBALS['obj_db']->create_insert_query($table_name,$fields);
			$flag=$GLOBALS['obj_db']->execute_query($query);
			if(!$flag)
			{
				$msg = "Error while adding employee.";
			}
			else
			{
				$empId = $GLOBALS['obj_db']->insert_id();
				$sqlAddLeaveTypes = "INSERT INTO leave_balance(emp_id, leave_type_id, year, last_updated) SELECT '".$empId."', leave_type_id, YEAR(CURDATE()), '".date('Y-m-d')."' FROM leave_types";
				$rsAddLeaveTypes  = $GLOBALS['obj_db']->execute_query($sqlAddLeaveTypes);
				
				$msg = "Employee added successfully";
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

	public function getEmployeeData($id)
	{
		$query = "SELECT e.*, dept.dept_name AS department, d.designation AS designation, br.city_name, br.region FROM employees AS e ";
		$query .= " LEFT JOIN departments AS dept ON e.department_id = dept.dept_id LEFT JOIN designations AS d ON e.designation_id = d.id LEFT JOIN branches AS br ON e.branch_id = br.id WHERE e.emp_id= '".$id."' ";
		
		$exe = $GLOBALS['obj_db']->execute_query($query);
		return $exe;
	}

	public function getAllEmployees($start = '', $end = '')
    {
		$search_text = '';
		if($_REQUEST['search_text'] != "")
		{
			$search_text = $_REQUEST['search_text'];
		}
		$search_text_condition = $this->getSearchText($search_text);
		
		$query = "SELECT e.*, dept.dept_name AS department, d.designation AS designation, br.city_name, br.region FROM employees AS e ";
		$query .= " LEFT JOIN departments AS dept ON e.department_id = dept.dept_id 
					LEFT JOIN designations AS d ON e.designation_id = d.id 
					LEFT JOIN branches AS br ON e.branch_id = br.id ";
		$query .= " WHERE 1 = 1 ";
		$query .= $search_text_condition;		
		
		$query .= " ORDER BY e.emp_id DESC";
		if($end > 0)
		{
			$query .= " LIMIT $start, $end";
		}
		$rsQuery = $GLOBALS['obj_db']->execute_query($query);	
        return $rsQuery;
    }

	 public function update($arrayData)
    {
		list($id, $first_name, $last_name, $designation_id, $department_id, $email, $password, $address, $phone, $emp_code, $status, $emp_category, $birth_date, $pan_number, $passport_number, $HOD_1_id, $HOD_2_id, $hr_id, $branchId, $bankName, $bankAcNumber, $bankIfsCode, $bankBranchAddress, $joiningDate) = $arrayData;
		
		$birth_date = getDbDateFormat($birth_date);
		$joiningDate = getDbDateFormat($joiningDate);
		
		$query = "UPDATE employees SET ";
		$query .= " first_name = '".$GLOBALS['obj_db']->escape($first_name)."',
					last_name = '".$GLOBALS['obj_db']->escape($last_name)."',
					designation_id = '".$designation_id."',
					department_id = '".$department_id."',
					email = '".$email."', ";
					if($password != DUMMY_PASS)
					{
						$query .= " password = '".md5($password)."', ";	
					}	
					
		$query .=	" address = '".$GLOBALS['obj_db']->escape($address)."',
					phone = '".$phone."',
					emp_code = '".$emp_code."',
					status = '".$status."',
					emp_category = '".$emp_category."',
					birth_date = '".$birth_date."',
					joining_date = '".$joiningDate."',
					pan_number = '".$pan_number."',
					passport_number = '".$passport_number."',
					HOD_1_id = '".$HOD_1_id."',
					HOD_2_id = '".$HOD_2_id."',
					hr_id = '".$hr_id."',
					branch_id = '".$branchId."',
					bank_name = '".$bankName."',
					bank_ac_number = '".$bankAcNumber."',
					bank_ifsc_Code = '".$bankIfsCode."',
					bank_branch_address = '".$GLOBALS['obj_db']->escape($bankBranchAddress)."'
					WHERE 
					emp_id	= '".$id."'";

		$flag = @$GLOBALS['obj_db']->execute_query($query);

		if(!$flag)
		{
			$msg = "Error while updating employee data.";
		}
		else
		{
			$msg = "Employee data updated successfully";
			$_SESSION['success'] = 1;
		}
		return $msg;
	}

	public function delete($id = '')
    {
		$query = "DELETE from employees where emp_id = '".$id."'";
		$flag = $GLOBALS['obj_db']->execute_query($query);
		if(!$flag)
        {
	    $msg = "Error while deleting employee data.";
        }
		else
		{
            $msg = "Employee deleted successfully";
			$_SESSION['success'] = 1;
        }
	
		return $msg;
    }
	
	public function getReportingHeadsList()
	{
		$sqlHod = "SELECT * FROM employees WHERE emp_category IN('authorizer', 'both')";
		$rsHod = $GLOBALS['obj_db']->execute_query($sqlHod);
		return $rsHod;
	}
	
	public function isDuplicate($email, $phone, $emp_code, $pan_number, $passport_number)
	{
		$query = "SELECT emp_id FROM employees WHERE 1=1 AND ((email = '".$email."' AND email != '') OR (phone = '".$phone."' AND phone != '') OR (emp_code = '".$emp_code."' AND emp_code != '') OR (pan_number = '".$pan_number."' AND pan_number != '') OR (passport_number = '".$passport_number."' AND passport_number != '')) ";
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
	
	public function getDeptEmployees($deptId, $userId, $branchId)
	{
		$sql = "SELECT * FROM employees WHERE (department_id = '".$deptId."' OR branch_id = '".$branchId."') AND emp_id != '".$userId."'";
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		return $rs;
	}
	
	public function isValidAuthorizer($applicationId, $empId)
	{
		$sql = "SELECT la.id FROM leave_applications AS la INNER JOIN employees AS e ON la.emp_id = e.emp_id WHERE la.id = '".$applicationId."' AND (e.HOD_1_id = '".$empId."' || e.HOD_2_id = '".$empId."') ";
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function isValidAuthorizerTour($tourId, $empId, $userType)
	{
		$sql = "SELECT tr.tour_id FROM tour_requests AS tr INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE tr.tour_id = '".$tourId."' ";
		
		if($userType == 'authorizer')
		{
			$sql .= " AND (e.HOD_1_id = '".$empId."' || e.HOD_2_id = '".$empId."') ";
		}
		elseif($userType == 'finance')
		{
			
		}
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function isValidAuthorizerConveyance($conveyanceId, $empId, $userType)
	{
		$sql = "SELECT lcr.id FROM local_conveyance_requests AS lcr INNER JOIN employees AS e ON lcr.emp_id = e.emp_id WHERE lcr.id = '".$conveyanceId."' ";
		
		if($userType == 'authorizer')
		{
			$sql .= " AND (e.HOD_1_id = '".$empId."' || e.HOD_2_id = '".$empId."') ";
		}
		elseif($userType == 'finance')
		{
			
		}
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function isValidAuthorizerDa($tourId, $empId, $userType)
	{
		$sql = "SELECT cda.id FROM calculated_da AS cda INNER JOIN tour_requests AS tr ON cda.tour_id = tr.tour_id INNER JOIN employees AS e ON tr.emp_id = e.emp_id WHERE cda.tour_id = '".$tourId."' ";
		
		if($userType == 'authorizer')
		{
			$sql .= " AND (e.HOD_1_id = '".$empId."' || e.HOD_2_id = '".$empId."') ";
		}
		elseif($userType == 'finance')
		{
			
		}
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getEmployeeHeads($empId)
	{
		$sql = "SELECT e1.first_name AS HOD_1_first_name, e1.last_name AS HOD_1_last_name, e1.email AS HOD_1_email, 
				e2.first_name AS HOD_2_first_name, e2.last_name AS HOD_2_last_name, e2.email AS HOD_2_email, 
				e3.first_name AS hr_first_name, e3.last_name AS hr_last_name, e3.email AS hr_email 
				FROM employees AS e 
				LEFT JOIN employees AS e1 ON e.HOD_1_id = e1.emp_id 
				LEFT JOIN employees AS e2 ON e.HOD_2_id = e2.emp_id 
				LEFT JOIN employees AS e3 ON e.hr_id = e3.emp_id 
				WHERE e.emp_id = '".$empId."' ";
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		
		$heads = array();
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			$row = $GLOBALS['obj_db']->fetch_array($rs);
			
			$heads['HOD_1'] = array('fullName' => $row['HOD_1_first_name']." ".$row['HOD_1_last_name'], 'email' => $row['HOD_1_email']);
			$heads['HOD_2'] = array('fullName' => $row['HOD_2_first_name']." ".$row['HOD_2_last_name'], 'email' => $row['HOD_2_email']);
			$heads['hr'] = array('fullName' => $row['hr_first_name']." ".$row['hr_last_name'], 'email' => $row['hr_email']);
		}
		return $heads;
	}
}
?>