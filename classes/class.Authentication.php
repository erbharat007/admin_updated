<?php
class Authentication
{
	public function checkUserAuthentication($email, $pass, $logInAs)
	{
		if(!$email || !$pass)
		{
			return false;
		}
		if($logInAs == 'employee')
		{
			$chkSql = "SELECT emp_id, email, role_id, first_name, last_name, department_id, emp_category FROM employees WHERE email = '".$GLOBALS['obj_db']->escape($email)."' AND password = '".md5($GLOBALS['obj_db']->escape($pass))."' AND status = 'active'";
		}
		else
		{
			$chkSql = "SELECT * FROM admin_users WHERE email = '".$GLOBALS['obj_db']->escape($email)."' AND password = '".md5($GLOBALS['obj_db']->escape($pass))."' AND status = 'active' AND user_type = '".$logInAs."'";
		}
		
		//echo $chkSql;die;
		$rsChkSql = $GLOBALS['obj_db']->execute_query($chkSql);
		if($GLOBALS['obj_db']->num_rows($rsChkSql) > 0)
		{
			$rowUser = $GLOBALS['obj_db']->fetch_array($rsChkSql);
			
			$_SESSION['userEmail'] = $rowUser['email'];
			if($logInAs == 'employee')
			{
				$_SESSION['userId']    = $rowUser['emp_id'];
				$_SESSION['roleId']    = $rowUser['role_id'];
				$_SESSION['userName']  = $rowUser['first_name']." ".$rowUser['last_name'];
				$_SESSION['deptId']    = $rowUser['department_id'];
				$_SESSION['userType']  = ($rowUser['emp_category'] == 'authorizer' || $rowUser['emp_category'] == 'both') ? 'authorizer' : 'originator';
				$_SESSION['superAdmin']= 0;
				$_SESSION['adminUser'] = 0;
			}
			else
			{
				$_SESSION['userId']    = $rowUser['user_id'];
				$_SESSION['userName']  = $rowUser['first_name']." ".$rowUser['last_name'];
				$_SESSION['userType']  = $rowUser['user_type'];
				$_SESSION['roleId']    = $rowUser['role_id'];
				$_SESSION['adminUser'] = 1;
				if($rowUser['user_type'] == 'superadmin')
				{
					$_SESSION['superAdmin'] = 1;
				}
			}
			
			return true;
		}
		else
		{
			return false;
		}	
	}

	public function getAllowedServices($roleId)
	{
		if(isset($_SESSION['superAdmin']) && $_SESSION['superAdmin'] == 1)
		{
			$sqlServices = "SELECT fr.service_id, sr.service_name, GROUP_CONCAT(CONCAT(fr.feature_name,'===',fr.feature_url)) AS role_features FROM features AS fr INNER JOIN services AS sr ON (fr.service_id = sr.service_id) GROUP BY fr.service_id ORDER BY sr.display_order ASC";
		}
		else
		{
			$sqlServices = "SELECT fr.service_id, sr.service_name, GROUP_CONCAT(CONCAT(fr.feature_name,'===',fr.feature_url)) AS role_features FROM features AS fr INNER JOIN services AS sr ON (fr.service_id = sr.service_id) WHERE fr.feature_id IN (SELECT feature_id FROM role_details WHERE role_id = '".$roleId."') GROUP BY fr.service_id ORDER BY sr.display_order ASC";	
		}
		
		$rsServices = $GLOBALS['obj_db']->execute_query($sqlServices);
		
		$allowedServices = array();
		if($GLOBALS['obj_db']->num_rows($rsServices) > 0)
		{
			while($rowService = $GLOBALS['obj_db']->fetch_array($rsServices))
			{
				$services = array();
				$roleFeatures = array();
				$serviceName = $rowService['service_name'];
				$features = $rowService['role_features'];
				$featuresArr = explode(',', $features);
				foreach($featuresArr as $featureDetail)
				{
					$featureDetailArr = explode('===',$featureDetail);
					$featureName = $featureDetailArr[0];
					$featureURL = $featureDetailArr[1];
					$roleFeatures[] = array('featureName' => $featureName, 'featureURL' => $featureURL);
				}
				$services['serviceName'] = $serviceName;
				$services['features']    = $roleFeatures;
				$allowedServices[] = $services;
			}	
		}	
		return $allowedServices;
	}
	
	public function getFeatureIdByOption($subOption)
	{
		if(!$subOption)
		{
			return 0;
		}
		$sql = "SELECT feature_id FROM features WHERE feature_url LIKE '%".$subOption."%' ";
		$rs = $GLOBALS['obj_db']->execute_query($sql);
		if($GLOBALS['obj_db']->num_rows($rs) > 0)
		{
			$row = $GLOBALS['obj_db']->fetch_array($rs);
			return $row['feature_id'];
		}
		else
		{
			return 0;
		}
	}
	
	public function isFeatureAllowed($roleId, $featureId)
	{
		if(!$roleId || !$featureId)
		{
			return false;
		}
		$sql = "SELECT id FROM role_details WHERE role_id = '".$roleId."' AND feature_id = '".$featureId."' ";
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
}