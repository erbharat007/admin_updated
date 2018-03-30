<?

require_once("8K4AE_config_Df8_.php");
require_once(CLASS_PATH."/class.Database.inc.php");
$GLOBALS['obj_db']=new Database;
require_once("".CLASS_PATH."/class.Session.inc.php");
$GLOBALS['obj_session']=new Session;
require_once("".CLASS_PATH."/class.Hotel.inc.php");
require_once("".CLASS_PATH."/class.HotelGroup.inc.php");
require_once("".CLASS_PATH."/class.RoomType.inc.php");
require_once("".CLASS_PATH."/class.RoomStock.inc.php");
$obj_hotel=new Hotel;
$obj_group=new HotelGroup;
$rs_group=$obj_group->getallrecords();
$obj_hotel=new Hotel;
$obj_roomtype=new RoomType;
//$rs_roomtype=$obj_roomtype->getall();
$obj_roomstock=new RoomStock;
require_once("".CLASS_PATH."/class.Authenticate.inc.php");
$obj_authenticate=new Authenticate;
$obj_authenticate->is_feature_allowed(5);
?>



<table width="545" border="0" align="center"  cellspacing="0" cellpadding="0" class="border">
<tr>

<td align="right" width="545">
<div align="right">

  <table width="100%" border="0" align="center"  cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="6" class="headingTwo">User Log Detail </td>
  </tr>
  <tr>
  <td height="20" colspan="6">
  
  </td>
  </tr>
  <tr class="headingTwo">
    <td width="27%" align="center" class="textNormalOne">Date</td>
    <td width="19%" align="center" class="textNormalOne">Login</td>
    <td width="16%" align="center" class="textNormalOne">User Type </td>
    <td width="13%" align="center" class="textNormalOne">Action</td>
    <td width="25%" align="center" class="textNormalOne">IP</td>
    <!--<td width="9%" align="center" class="textNormalOne">Status</td>-->
  </tr>
  <? 
  $sel_customer="select * from customer where todaydate<>'0' order by todaydate ASC";
  $select_cust=$GLOBALS['obj_db']->execute_query($sel_customer) or die(mysql_error());
  $cnt_cust_login_detail=$GLOBALS['obj_db']->num_rows($select_cust);
  if($cnt_cust_login_detail>'0')
  { 
  while($fetch_customer_login_detail=$GLOBALS['obj_db']->fetch_array($select_cust))
  {
   
?>
	<tr>
    <td align="center" class="textNormalOne"><? echo $fetch_customer_login_detail['todaydate']; ?></td>
    <td align="center" class="textNormalOne"><? echo $fetch_customer_login_detail['first_name']."&nbsp;".$fetch_customer_login_detail['last_name']; ?></td>
	
    <td align="center" class="textNormalOne"><? echo $fetch_customer_login_detail['usertype']; ?></td>
    <td align="center" class="textNormalOne"><? if($fetch_customer_login_detail['flag']=='Y') { echo "Login"; } else { echo "LogOut"; }  ?></td>
    <td align="center" class="textNormalOne"><? echo $fetch_customer_login_detail['ip']; ?></td>
    <!--<td align="center" class="textNormalOne"><? if($fetch_customer_login_detail['flag']=='Y') { echo "Success"; } else { echo "Failure"; } ?></td>-->
	</tr>
 <? 
 }
 }
 else
 {
 ?>
 <tr>
 <td align="center" colspan="5" class="textNormalOne"><? echo "Currently No Data"; ?></td>
 </tr>
 <? 
 }
  ?> 
  </table>
</div>
</td>
</tr>
</table>