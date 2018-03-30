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
$obj_authenticate->is_feature_allowed(36);
?>



<table width="545" border="0" align="center"  cellspacing="0" cellpadding="0" >
<tr>

<td align="right" width="70%">
<div align="right">

  <table width="100%" border="0" align="center"  cellspacing="0" cellpadding="0" class="border">
  <tr>
    <td colspan="6" class="headingTwo">Customer Property Detail </td>
  </tr>
  <tr>
    <td width="27%" align="center" class="textNormalOne">Customer Name </td>
    <td width="19%" align="center" class="textNormalOne">Domain Name </td>
    <td width="16%" align="center" class="textNormalOne">Hotel Name </td>
    <!--<td width="13%" align="center" class="textNormalOne">Action</td>
    <td width="25%" align="center" class="textNormalOne">IP</td>-->
    <!--<td width="9%" align="center" class="textNormalOne">Status</td>-->
  </tr>
  <?
	 $fetch_cust="select * from site_details where status='1'";
	 $f_customer=$GLOBALS['obj_db']->execute_query($fetch_cust) or die(mysql_error());
	 $cnt_num=$GLOBALS['obj_db']->num_rows($f_customer);
	 if($cnt_num=='0')
	 {
	 ?>
	 <tr>
	 <td class="textTwo" align="center" colspan="4">
	 <? echo "No Detail's Found"; ?>
	 </td>
	 </tr>
	 <?
	 }
	 while($select_customer=$GLOBALS['obj_db']->fetch_array($f_customer))
	 {
       $fetch_det_cust="select * from clients where status='1' and site_id='".$select_customer['site_id']."'";
	   $f_cust_detail=$GLOBALS['obj_db']->execute_query($fetch_det_cust) or die(mysql_error());
	   $select_cust_detail=$GLOBALS['obj_db']->fetch_array($f_cust_detail);
	   
	  
    ?>
	<tr>
	
    <td align="center" class="textTwo"><? echo $select_cust_detail['firstname']."&nbsp;".$select_cust_detail['surname']; ?></td>
    <td align="center" class="textTwo"><? echo $select_customer['domain'];?></td>
    
  <td align="left" class="textTwo">
  <? 
  $i=1; 
  $fetch_hot="select site_id,hotel_id from site_hotel where site_id='".$select_customer['site_id']."'";
  $fetch_hotel_detail=$GLOBALS['obj_db']->execute_query($fetch_hot) or die(mysql_error());
  while($sel_hotel_detail=$GLOBALS['obj_db']->fetch_array($fetch_hotel_detail))
  {
    
	$selectquery="select hotel_name from hotel where hotel_id='".$sel_hotel_detail['hotel_id']."'";
	$sel_hotelname=$GLOBALS['obj_db']->execute_query($selectquery) or die(mysql_error());
	$fetch_hotel_name=$GLOBALS['obj_db']->fetch_array($sel_hotelname);
	echo $i++.")"."&nbsp;".$fetch_hotel_name['hotel_name']."<br><br>";
  }	

  ?>
   </td>
	</tr>
 <? } ?>
  <tr>
  <td colspan="5" align="center" class="textNormalOne">
 </td>
  </tr>
 
  </table>
</div>
</td>
</tr>
</table>