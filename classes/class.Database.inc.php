<?php 
//Class for database operations
class Database
{
	public $link;
    public $waitTimeout;
	/** Reserved words for array to ( insert / update )
	 * @var array
	 */
	public $reserved = array('null', 'now()', 'current_timestamp', 'curtime()', 'localtime()', 'localtime', 'utc_date()', 'utc_time()', 'utc_timestamp()');
	/** Start of MySQL statement for array to ( insert / update )
	 * @var string
	 */
	public $statementStart = 'sql::';
	/** mysql / mysqli
	 * @var string
	 */
	public $extension = 'mysqli';
	
	private $db_host;
	private $db_name;
	private $db_user;
	private $db_password;
	private $db_port;

	//Constructor
	function __construct($db_host = DB_HOST, $db_user = DB_USER, $db_password = DB_PASSWORD, $db_name = DB_NAME)
	{
		if(!$db_host || !$db_name || !$db_user || !$db_password)
		{
			//return;
		}
		
		$this->db_host = $db_host;
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_password = $db_password;
		
		$this->get_database_link();
		$this->getTimeout();
	}

	//function to get database link
	function get_database_link()
	{
		
		if($this->link)
		{
			return $this->link;
		}
		$this->link = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $this->db_name);
		if (mysqli_connect_errno())
		{
			$error = mysqli_connect_error();
			$this->error_function($error);		    
		}
		else
		{
			return $this->link;
		}
	}
    
	//function to clean up the database link
	function cleanup()
	{
		$close = mysqli_close($this->link);
		$this->link = false;
		return $close;
	}
	
	//function to clean up the database link
	function close()
	{
		return mysqli_close($this->link);
	}
	
    
    public function getTimeout()
    {
        $this->waitTimeout = false;
        $sql = 'SHOW VARIABLES WHERE Variable_name ="wait_timeout"';
        $rs = $this->execute_query($sql);
        if(!$rs)
        {
            return false;
        }
        
        while($row = mysqli_fetch_assoc($rs))
        {
            $this->waitTimeout = $row['Value'];					
        }
    }
    public function autoReconnect()
    {
        mysqli_ping($this->link);
    }
	
	public function reconnect()
    {
        $this->cleanup();
        return $this->get_database_link();
    }
    
	//function to execute an insert query
	function execute_query($query, $file='', $line=0, $func='')
	{
		if(!$this->link)
		{
			$this->reconnect();
		}
		$rs = mysqli_query($this->link, $query) or die($this->error_function(mysqli_error($this->link), $file, $line, $func, $query));
		return $rs;
	}

	//function to create insert query
	function create_insert_query($table_name, $fields)
	{
		if(count($fields) > 0)
		{
			
			$query = "INSERT INTO " . $table_name . " SET ";

			for($i = 0; $i < count($fields); $i++)
			{
				if(strtolower($fields[$i]) != "now()")
				{
					$query .= "`".$fields[$i]['name']."` = '".$fields[$i]['value']."', ";
				}
				else
				{
					$query .= "`".$fields[$i]['name']."` = ".$fields[$i]['value'].", ";
				}
			}

			$query = substr($query, 0, -2);
			return $query;
		}
		else
		{
			return false;
		}
	}

	//function for create update query
	function create_update_query($table_name,$fields,$condition)
	{
		
		if(count($fields)>0)
		{
			$query="update ".$table_name." ";
			$query=$query." set ".$fields[0]['name']."='".$fields[0]['value']."'";
			for($i=1;$i<count($fields);$i++)
			{
				$query=$query.",".$fields[$i]['name']."='".$fields[$i]['value']."'";
			}
			$query=$query."  ".$condition;
			
			return $query;
				
		}
		else
		{
			return false;
		}
	}

	// Function related to create query
	function get_field_for_query($name,$value)
	{
		return array('name'=>$name,'value'=>$value);
	}
	
	/** Creates an sql string from an associate array
	 * @param 	string 		$table 	- Table name
	 * @param 	array 		$data 	- Data array Eg. array('column' => 'val') or multirows array(array('column' => 'val'), array('column' => 'val2'))
	 * @param 	boolean		$ingore	- INSERT IGNORE (row won't actually be inserted if it results in a duplicate key)
	 * @param 	string 		$duplicateupdate 	- ON DUPLICATE KEY UPDATE (The ON DUPLICATE KEY UPDATE clause can contain multiple column assignments, separated by commas.)
	 * @return 	insert id or false
	 */
	public function create_multi_insert_query($table, $data, $ignore = FALSE, $duplicateupdate = NULL) {
		$multirow = is_array(reset($data));
		/*echo '<br/> Building SQL :';*/
		if ($multirow) {
			$c = implode('`, `', array_keys($data[0]));
			$dat = array();
			//echo '<br/> Multi Row Data:';
			//print_r($data);
			foreach ($data as &$val) {
				foreach ($val as &$v) {
					//echo '\n<br/> processing data...';
					if (in_array(strtolower($v), $this->reserved)) 
					{
						/*echo '\n<br/> Reserved word :'.$v;*/						
						$v = strtoupper($v);
					} elseif (preg_match('/^' . preg_quote($this->statementStart) . '/i', $v)) {
						/*echo '\n<br/> SQL: detected';*/						
						$v = preg_replace('/^' . preg_quote($this->statementStart) . '/i', NULL, $v);
					} else {
						/*echo '\n<br/> Normal value :';*/						
						/*var_dump($v);*/
						$v = "'{$this->escape($v)}'";
						//$v = mysql_real_escape_string($v);
						/*echo '\n<br/> After Escape :';
						var_dump($v);*/
					}
				}
				/*echo " Escaped vals : ";
				var_dump($val);*/
				$dat[] = "( " . implode(', ', $val) . " )";
			}
			$v = implode(', ', $dat);
		} else {
			/*echo '<br/> Single Row Data:';*/
			$c = implode('`, `', array_keys($data));
			foreach ($data as &$val) {
				if (in_array(strtolower($val), $this->reserved)) {
					$val = strtoupper($val);
				} elseif (preg_match('/^' . preg_quote($this->statementStart) . '/i', $val)) {
					$val = preg_replace('/^' . preg_quote($this->statementStart) . '/i', NULL, $val);
				} else {
					$val = "'{$this->escape($val)}'";
				}
			}
			$v = "( " . implode(', ', $data) . " )";
		}
		return (!empty($data)) ? "INSERT" . ($ignore ? " IGNORE" : NULL) . " INTO `{$table}` ( `{$c}` ) VALUES {$v}" . ($duplicateupdate ? " ON DUPLICATE KEY UPDATE {$duplicateupdate}" : NULL) . ";" : FALSE;
	}
	
	// customized error handler for die function
	function error_function($error_message, $error_file, $error_line, $error_function,$err_query) 
	{
		$errors = debug_backtrace();
		$error_message = $error_message ? $error_message : mysqli_error($this->link);
		$msg = "<h2>A SQL Error was encountered</h2><br><br>";
		$msg .= "<b>IP</b> : ".$_SERVER['REMOTE_ADDR']."<br><br>";
		$msg .= "<b>URL</b> : ".SITE_URL.$_SERVER['REQUEST_URI']."<br><br>";
		$msg .= "<b>Referer URL</b> : ".$_SERVER['HTTP_REFERER']."<br><br>";
		$msg .= "<b>Query Sring</b> : ".$_SERVER['QUERY_STRING']."<br><br>";
		$msg .= "<b>Message</b>: ".$error_message."<br><br>";
		$msg .= "<b>Query</b>: ".$err_query."<br><br>";
		$msg .= "<b>File Name</b>: ".$errors[1]['file']."<br><br>";
		$msg .= "<b>Line Number</b>: ".$errors[1]['line']."<br><br>";
		$msg .= "<b>Function Name</b>: ".$errors[1]['function']."<br><br>";
		
		$headers = "MIME-Version: 1.0;\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		$headers .= "From: Admin <info@admin.com>\n";
		if(1===DEVELOPMENT_MODE || 2===DEVELOPMENT_MODE)
		{
			echo $msg;
			exit;
		}
		else
		{
			echo $msg;
			exit;
		}
	}


	// customized error handler for die function
	function blank_desc($error_file, $error_line, $error_function) {
	
		$msg = "<h3>Blank description problem</h3><br><br>";
		$msg .= "Filename: $error_file<br><br>";
		$msg .= "Line Number: $error_line<br><br>";
		$msg .= "Function Name: $error_function<br><br>";
		$msg .= "Message: " . date("d-m-Y H:i:s") . "<br><br>";

		$headers = "MIME-Version: 1.0;\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		$headers .= "From: The Montcalm  <reservations@montcalm.co.uk>\n";
		$mail_flag = @mail(HOTEL_ERRORS_EMAIL, "Blank Issue - Montcalm", $msg, $headers);	

	}
	
	/**
	 * This method is depriated. It will be removed on next version
	 */
    public function mysqliRealEscapeString($string)
    {
        return $this->escape($string);
    }
    
    /**
	 * escape is the new function and next time the mysqliRealEscapeString function will be depricated
	 */
    public function escape($string)
    {
        return @mysqli_real_escape_string($this->link, $string);
    }
	
	 /**
	 * fetch_assoc is the function to perform $GLOBALS['obj_db']->fetch_assoc
	 */
    public function fetch_assoc($rs)
    {
        return @mysqli_fetch_assoc($rs);
    }
    
     /**
	 * fetch_array is the function to perform mysqli_fetch_array
	 */
    public function fetch_array($resultSet, $mode = '')
    {
		if(!$mode)
		{
			$mode = MYSQLI_BOTH;
		}
		elseif($mode == 'ASSOC')
		{
			$mode = MYSQLI_ASSOC;
		}
		elseif($mode == 'NUM')
		{
			$mode = MYSQLI_NUM;
		}
		elseif($mode == 'BOTH')
		{
			$mode = MYSQLI_BOTH;
		}
		
        return @mysqli_fetch_array($resultSet, $mode);
    }
	
	 /**
	 * num_rows is the function to perform mysqli_num_rows
	 */
    public function num_rows($resultSet)
    {
        return @mysqli_num_rows($resultSet);
    }
	
	public function insert_id()
	{
		return mysqli_insert_id($this->link);
	}
	
	public function last_insert_id()
	{
		$query = $this->execute_query('SELECT LAST_INSERT_ID() as id');
		$row = mysqli_fetch_row($query);
		$last_insert_id = $row['0'];
		return $last_insert_id;
	}
	
	public function data_seek($result, $offset)
	{
		return mysqli_data_seek($result, $offset);
	}

	public function free_result($result)
	{
		return mysqli_free_result($result);
	}
	
	public function affected_rows()
	{
		return mysqli_affected_rows($this->link);
	}
}
