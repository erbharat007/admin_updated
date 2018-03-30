<?php

require_once("".CLASS_PATH."/class.Session.inc.php");

$GLOBALS['obj_session']=new Session;

require_once("".CLASS_PATH."/class.AssignHotel.inc.php");

$obj_assignhotels=new AssignHotels;

$row_assignhotels=$obj_assignhotels->getall();

$rs_hotels=$obj_assignhotels->getallusers();

require_once("".CLASS_PATH."/class.Hotel.inc.php");

$obj_userhotels=new Hotel;

require_once("".CLASS_PATH."/class.Authenticate.inc.php");

$obj_authenticate=new Authenticate;

$obj_authenticate->is_feature_allowed(4);

switch($action)

{

	case "Add":

		$_SESSION['msg']=$obj_assignhotels->add();

		break;

	case "Update":

		$_SESSION['msg']=$obj_assignhotels->edit();

		break;

	case "Edit":

		$rs_assignhotels=$obj_assignhotels->getrowbyid($_REQUEST['chkSelect']);

		$row_assignhotels=$GLOBALS['obj_db']->fetch_array($rs_assignhotels);

		break;

	case "Delete":

		$_SESSION['msg']=$obj_assignhotels->delete();

		break;

}

?>

<SCRIPT language=javascript>

function CheckAddValidation()

{

	// Validation checks for add 

	document.frmEdit.hdnHotelID.value="";

	for(i=0;i<document.frmEdit.lstSelectedHotels.options.length;i++)

	{

		if(document.frmEdit.hdnHotelID.value=="")

		{

		document.frmEdit.hdnHotelID.value=document.frmEdit.lstSelectedHotels.options[i].value;

		}

		else

		{

		document.frmEdit.hdnHotelID.value=document.frmEdit.hdnHotelID.value+";"+document.frmEdit.lstSelectedHotels.options[i].value;

		//alert(document.frmAdd.hdnFeatureID.value+";"+document.frmAdd.lstScreenNames.options[i].value);

		}

	}

	document.frmEdit.hdnAction.value="Add";

	// Submit the form for add 

//	alert(document.frmEdit.lstSelectedHotels.options.length);

//	alert(document.frmEdit.hdnHotelID.value.length);

	document.frmEdit.submit();

}



function CheckSearchValidation()

{

	// Validation checks for search 

	if(document.frmSearch.lstSearchBy.value==1)

		{

			if(!chkBlank(document.frmSearch.txtSearch,"User Name")) return false;

		}

	else

		{

			if(!chkBlank(document.frmSearch.txtSearch,"User Email")) return false;

		}

	// Submit the form for search 

	document.frmSearch.submit();

}



function CheckEditValidation()

{

	// Validation checks for edit 

	count=fnCheckCount(document.frmUpdate.chkSelect);

	if(count==0) alert("No row selected");

	else if(count>1) alert("Select only one row to edit");

	else

	{

		// Submit the form for edit

		document.frmUpdate.hdnAction.value="Edit";

		document.frmUpdate.submit();

	}

}



function move(fbox,tbox)

{

var id=fbox.selectedIndex;

var roleid=fbox.options[id].value;

for(var i=0; i<tbox.options.length; i++) 

	{

if(tbox.options[i].value==roleid)

	  {

  alert("The hotel already exits");

   return false;

	  }

	}	  

		var no = new Option();

		no.value = fbox.options[id].value;

		no.text = fbox.options[id].text;

		tbox.options[tbox.options.length] = no;

}



function remove(fbox)

 {

for(var i=0; i<fbox.options.length; i++) 

   {

	if(fbox.options[i].selected && fbox.options[i].value != '')

	 {

		//var no = new Option();

		//no.value = fbox.options[i].value;

		//no.text = fbox.options[i].text;

		//tbox.options[tbox.options.length] = no;

		fbox.options[i].value = '';

		fbox.options[i].text = '';

	 }

   }

BumpUp(fbox);

 }



function BumpUp(box)

 {

	for(var i=0; i<box.options.length; i++)

	 {

	if(box.options[i].value == '')

	   {

	for(var j=i; j<box.options.length-1; j++)

		 {

			box.options[j].value = box.options[j+1].value;

			box.options[j].text = box.options[j+1].text;

		 }

	var ln = i;

	break;

	   }

    }

	if(ln < box.options.length)

    {

	box.options.length -= 1;

	BumpUp(box);

    }

}



function CheckCancelValidation()

{

	document.frmEdit.hdnAction.value="";

	document.location.href="index.php?Option=Users&SubOption=AssignHotel";

}

</SCRIPT>

<table width="550" border="0" align="center" cellpadding="0" cellspacing="0">

<?php

if($action=="Edit"): // Display Edit Form

?>  <tr> 

    <td height="10" colspan="2"><p></p></td>

  </tr>

  <tr> 

    <td height="400" colspan="2" valign="top">

        <table width="100%" border="0" cellpadding="0" cellspacing="0">

        <tr> 

          <td height="20"><form action="index.php?Option=Users&SubOption=AssignHotel" method="post" name="frmEdit" id="frmUpdate">

              <table width="550" border="0" align="center" cellpadding="0" cellspacing="0" class="tableOne">

                <tr> 

                  <td colspan="2" align="center" class="headingTwo">Edit Hotels Assigned to Users </td>

                </tr>

                  <tr> 

                  <td colspan="2" align="left" class="textTwo"><font color=red> 

                 <?php 

					  if(isset($_SESSION['msg']) && $_SESSION['msg']!="")

					  {

						  echo $_SESSION['msg'];

						  $_SESSION['msg']="";

					   }

				  ?>

                    </font></td>

                </tr>

                <tr class="textTwo"> 

                  <td width="50%" align="right" class="textTwo">User Name :</td>

                  <td><input name="hdnUserID" type="hidden"  id="hdnUserID"  value="<?php echo $row_assignhotels['user_id']?>"><?php echo $row_assignhotels['user_name'] ?></td>

                </tr>

                <tr> 

                  <td colspan="2" align="right" class="textTwo">&nbsp;</td>

                </tr>

                <tr align="center"> 

                  <td colspan="2" class="textOne">All Hotel</td>

                </tr>

                <tr align="center"> 

                  <td height="14" colspan="2" class="textTwo">&nbsp;</td>

                </tr>

                <tr align="center"> 

                  <td height="14" colspan="2" class="textTwo"><select name="lstAllHotels" size="4" multiple class="lstBoxTwo" id="lstAllHotels"  style="width:160">

                 <?php

				  $rs_hotels=$obj_userhotels->getallrecords();

				  while($row_hotels=$GLOBALS['obj_db']->fetch_array($rs_hotels)):

					{

				  ?>

                  <option value="<?php echo $row_hotels['hotel_id']; ?>" ><?php echo $row_hotels['hotel_name']; ?></option>

                <?php

					} 

				  endwhile;

				?>

				</select></td>

                </tr>

                <tr align="center"> 

                  <td colspan="2" class="textTwo">&nbsp;</td>

                </tr>

                <tr align="center"> 

                  <td colspan="2" class="textTwo"><table width="220" border="0" align="center" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td><input name="cmdAdd" type="button" class="buttonOne" id="cmdAdd" value="Add&gt;&gt;"  onClick="move(lstAllHotels,lstSelectedHotels)"></td>

                        <td><input name="cmdRemove" type="button" class="buttonOne" id="cmdRemove" value="&lt;&lt;Remove" onClick="if (confirm('Would you like to proceed')) {remove(lstSelectedHotels)}"></td>

                      </tr>

                    </table></td>

                </tr>

                <tr align="center" class="textTwo"> 

                  <td colspan="2">&nbsp;</td>

                </tr>

                <tr align="center"> 

                  <td colspan="2" class="textOne">Selected Hotels<input type="hidden" name="hdnHotelID" id="hdnHotelID" value=""></td>

                </tr>

                <tr align="center" class="textTwo">

                <td colspan="2">&nbsp;</td>

                </tr>

                <tr align="center" class="textTwo"> 

                  <td colspan="2"><select name="lstSelectedHotels" size="4" multiple class="lstBoxTwo" id="lstSelectedHotels"  style="width:160">

				  <?php

				  $rs_hotelid=$obj_userhotels->gethotelid($_REQUEST['chkSelect']);

				  while($row_hotels=$GLOBALS['obj_db']->fetch_array($rs_hotelid)):

				  $hotel_name=$obj_userhotels->gethotelname($row_hotels['hotel_id']);

				  ?>

				  <option value="<?php echo $row_hotels['hotel_id']?>"><?php echo $hotel_name?></option> 

				  <?php

					endwhile

				  ?>

				    </select></td>

                </tr>

                <tr class="textTwo"> 

                  <td colspan="2">&nbsp; </td>

                </tr>

                <tr> 

                  <td colspan="2" class="textTwo"><table width="324" border="0" align="center" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td width="324"><input name="hdnAction" type="hidden"  id="hdnAction" value="">

						<input name="cmdAdd" type="button" class="buttonOne" id="cmdAdd" value="Add Hotel" onClick="CheckAddValidation()">

                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <input name="cmdCancel" type="button" class="buttonOne" id="cmdCancel" value="Cancel" onClick="CheckCancelValidation()">

                          &nbsp;</td>

                      </tr>

                    </table></td>

                </tr>

                <tr class="textTwo"> 

                  <td colspan="2">&nbsp;</td>

                </tr>

              </table>

            </form></td>

        </tr>

        <tr> 

          <td height="10">&nbsp;</td>

        </tr>

<? 

else: // Display Add/Search Form

?>

 <tr> 

          <td height="10"><p></p></td>

        </tr>

		

		 <tr> 

          <td height="20"><form action="index.php?Option=Users&SubOption=AssignHotel" method="post" name="frmSearch" id="frmSearch">

              <table width="550" border="0" align="center" cellpadding="0" cellspacing="0" class="tableOne">

               <tr> 

                  <td colspan="2" align="center" class="headingTwo"> Search User</td>

                </tr>

				<tr class="textTwo"> 

				  <tr> 

                  <td colspan="2" align="left" class="textTwo"><font color=red> 

                 <?php 

					  if(isset($_SESSION['msg']) && $_SESSION['msg']!="")

					  {

						  echo $_SESSION['msg'];

						  $_SESSION['msg']="";

					   }

				  ?>

                    </font></td>

                </tr>

				 <tr class="textTwo"> 

 				<td width="31%" height="30" align="right"> Search By:</td>   

				<td width="38%" height="30"><select name="lstSearchBy" class="lstBoxOne" id="select">

                      <option value="username">User Name</option>

                      <option value="email">Email</option>

                    </select></td>

                </tr>

                <tr class="textTwo"> 

                  <td height="30" align="right">Search :</td>

                  <td height="30"> 

                    <input name="txtSearch" type="text" class="txtBoxOne" id="txtSearch" maxlength="100"></td>

                </tr>

                <tr class="textTwo"> 

                  <td height="30" colspan="2" align="right"><table width="110" border="0" align="center" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td> <input name="hdnAction" type="hidden"  id="hdnAction" value="Search">

                          <input name="cmdSearch" type="button" class="buttonOne" id="cmdSearch" value="Search" onClick="CheckSearchValidation()"></td>

                      </tr>

                    </table></td>

                </tr>

              </table>

            </form></td>

        </tr>

   <tr> 

          <td >&nbsp;</td>

        </tr>

<?php

//search check

if($action=="Search") : //If-Two

	$rs_assignhotels=$obj_assignhotels->search();

else:

	$rs_assignhotels=$obj_assignhotels->getall();

endif ;//End of If-Two

$num_rows=$GLOBALS['obj_db']->num_rows($rs_assignhotels);

if($num_rows==0) : //If-Three

?>

  <tr class="textTwo"> 

                        <td colspan="3">No record Matched</td>

                      </tr>

<?php else: ?>

				       

        <tr> 

          <td height="10"><form action="index.php?Option=Users&SubOption=AssignHotel" method="post" name="frmUpdate" id="frmUpdate">

              <table width="770" border="0" cellspacing="0" cellpadding="0">

                <tr> 

                  <td height="10"><p></p></td>

                </tr>

                <tr> 

                  <td height="10"><table width="110" border="0" align="right" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td><input name="cmdEditHotel" type="button" class="buttonOne" id="cmdEditHotel" value="Edit Hotel" onClick="CheckEditValidation()"></td>

                      </tr>

                    </table></td>

                </tr>

                <tr> 

                  <td height="10">&nbsp;</td>

                </tr>

         <!--       <tr> 

                  <td height="25"><table border="0" align="right" cellpadding="1" cellspacing="0">

                      <tr align="center" valign="middle" class="textNormalOne"> 

                        <td valign="middle">Show&nbsp; </td>

                        <td valign="middle" class="textNormalOne"><select name="lstRecordPerPage" id="lstRecordPerPage">

                            <option value="10">10</option>

                            <option value="20">20</option>

                            <option value="50">50</option>

                            <option value="100">100</option>

                          </select> </td>

                        <td valign="middle"> &nbsp;Records Per Page</td>

                        <td width="20" valign="middle" class="textNormalOne">&nbsp; 

                        </td>

                        <td><a href="javascript: location.reload()">First</a> 

                          | <a href="javascript: location.reload()">Prev</a> | 

                          <a href="javascript: location.reload()">Next</a> | <a href="javascript: location.reload()">Last</a>&nbsp;&nbsp;</td>

                      </tr>

                    </table></td>

                </tr>

              --> 

                <tr> 

                  <td height="10" class="textNormalTwo"> &nbsp; 

                    <input name="chkSelectAll" type="checkbox" id="chkSelectAll" onClick="fnSelectAll(document.frmUpdate.chkSelect,this)">

                        Select All</td>

                </tr>

			   <tr> 

                  <td height="89"><table width="760" border="0" align="center" cellpadding="1" cellspacing="1">

                      <tr class="headingTwo"> 

                        <td width="63">Select</td>

                        <td width="123">Consol User Name</td>

                        <td width="153">Email</td>

						<td width="408">Hotels</td>

                      </tr>

            

<?php

while($row_assignhotels=$GLOBALS['obj_db']->fetch_array($rs_assignhotels)):

$rs_hotels=$obj_userhotels->gethotelid($row_assignhotels['user_id']);		

?>					  

                      <tr class="textTwo"> 

                        <td><input name="chkSelect" type="checkbox" id="chkSelect" value="<?php echo $row_assignhotels['user_id'] ?>"></td>

                        <td><?php echo $row_assignhotels['user_name'] ?>  </td>

                        <td><?php echo $row_assignhotels['email'] ?>  </td>

						<td>

						<?php

						while($row_hotels=$GLOBALS['obj_db']->fetch_array($rs_hotels))

						{

						$hid=$row_hotels['hotel_id'];

						$hotel_name=$obj_userhotels->gethotelname($hid);

						echo $hotel_name.",";

						}

						?>

						  </td>

                      </tr>

<?php					  				    

endwhile

?>                      

                </table></td>

                </tr>

                <tr> 

                  <td  class="textNormalTwo">&nbsp; </td>

                </tr>

<?php  endif ?>

 <tr> 

                  <td >&nbsp;</td>

                </tr>

                <tr> 

                  <td ><table width="110" border="0" align="right" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td><input name="hdnIDs" type="hidden"  id="hdnIDs" value=""><input name="hdnAction" type="hidden"  id="hdnAction" value="">

						<input name="cmdEditHotel" type="button" class="buttonOne" id="cmdEditHotel" value="Edit Hotel" onClick="CheckEditValidation()"></td>

                      </tr>

                    </table></td>

                </tr>

              </table>

            </form></td>

        </tr>

        <tr> 

          <td height="10">&nbsp;</td>

        </tr>

      </table>

      </td>

  </tr>

<?php

endif

?>  
</table>