<?php 
set_time_limit(0);
ini_set('memory_limit', '1024M'); // or you could use 1G
require_once("../config.php");

$sqlUpdate = "UPDATE leave_balance SET last_updated = '2016-10-01' WHERE emp_id = '13' AND leave_type_id = '1' AND year = YEAR(CURDATE()) ";
$rsUpdate = $GLOBALS['obj_db']->execute_query($sqlUpdate);

$sqlUpdate1 = "UPDATE leave_balance SET last_updated = '2016-09-15' WHERE emp_id = '12' AND leave_type_id = '1' AND year = YEAR(CURDATE()) ";
$rsUpdate1 = $GLOBALS['obj_db']->execute_query($sqlUpdate1);

echo "Data has been reset.";
?>