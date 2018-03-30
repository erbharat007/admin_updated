<?php
require_once("".CLASS_PATH."/class.ConsoleUser.inc.php");
$obj_consoleuser=new ConsoleUser;

switch($action)
{
	case "Add":
		$_SESSION['msg']=$obj_consoleuser->add();
		break;

	case "Update":

		$_SESSION['msg']=$obj_consoleuser->edit();
		$activity = 'Edit';
		$formdata = 'Updated User - '.addslashes($_REQUEST['txtEmail']).' : ID - '.$_REQUEST['hdnUserID'];		
		$ObjActivityLog->AddActivityLog($browser, $username, $pageurl, $formdata, $ip, $section, $activity);

		break;

	case "Edit":

		if($_SESSION['Usertype']=='admin')
		{
			//$rs_consoleuser=$obj_consoleuser->getalladmin();
			$rs_consoleuser=$obj_consoleuser->getrowbyidadmin($_REQUEST['chkSelect']);
		}
		else
		{
			$rs_consoleuser=$obj_consoleuser->getrowbyid($_REQUEST['chkSelect']);
		}
		

		$row_consoleuser1=$GLOBALS['obj_db']->fetch_array($rs_consoleuser);

		break;
	
	case "Reset":
		$_SESSION['msg']=$obj_consoleuser->ResetPassword($_REQUEST['hdnUserID'],$_REQUEST['txtEmail']);
		break;

	case "Delete":

		$_SESSION['msg']=$obj_consoleuser->delete();
		$hdnIDs=explode("&",$_REQUEST['hdnIDs']);
		for($i=0;$i<count($hdnIDs);$i++)
		{
		    $activity = 'Delete';
		    $formdata = 'Deleted User Id - '.$hdnIDs[$i];
		    $ObjActivityLog->AddActivityLog($browser, $username, $pageurl, $formdata, $ip, $section, $activity);
		}

		break;

}

?>

<SCRIPT language=javascript >

function CheckAddValidation()

{

	// Validation checks for add 

	if(!chkBlank(document.frmAdd.txtUserName,"User Name")) return false;
	if(document.frmAdd.txtUserName.value.search(/^ |^0|@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{/) != -1)
	{
		alert("User Name should be alphanumeric only.");
		document.frmAdd.txtUserName.value="";
		document.frmAdd.txtUserName.focus();
		return false;
	}

	if(!chkBlank(document.frmAdd.txtEmail,"User EmailID")) return false;
	if (document.frmAdd.txtEmail.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)
	{
		alert("Please enter a valid Email.");
		document.frmAdd.txtEmail.focus();
		return false;
	}
	//if(!chkEmail(document.frmAdd.txtEmail,"User EmailID")) return false;
	
	if(!chkBlank(document.frmAdd.txtPassword,"Password")) return false;
	if(document.frmAdd.txtPassword.value.length < 8) {
		alert("Your new password length should have 8 letters");
		return false;
	}
	if(!CheckPassword(document.frmAdd.txtPassword.value)) {
		alert("Your password must contains: \n1. At least one upper case English letter. \n2. At least one lower case English letter. \n3. At least one digit. \n4. At least one special character");
		return false;
	}
	if(document.frmAdd.txtPassword.value.search(/^ | /) != -1)
	{
		alert("Please enter a valid Password");
		document.frmAdd.txtPassword.value="";
		document.frmAdd.txtPassword.focus();
		return false;
	}

	if(!chkBlank(document.frmAdd.txtConfirmPassword,"Confirm Password")) return false;
	if(document.frmAdd.txtPassword.value!=document.frmAdd.txtConfirmPassword.value)
	{
		alert("Password don't match. Please re-enter Passwords.");
		document.frmAdd.txtPassword.value="";
		document.frmAdd.txtConfirmPassword.value="";
		document.frmAdd.txtPassword.focus();
		return false;
	}
	
	//if(!chkPassword(document.frmAdd.txtPassword,document.frmAdd.txtConfirmPassword))return false;
	
  

	// Submit the form for add 

	document.frmAdd.submit();

}

function CheckSearchValidation()

{

	// Validation checks for search 

	if(document.frmSearch.lstSearchBy.value==1)
	{
		if(!chkBlank(document.frmSearch.txtSearch,"User Name")) return false;
		/*if(document.frmSearch.txtSearch.value.search(/^ |@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{| /) != -1)
		{
			alert("User Name should be alphanumeric only.");
			document.frmSearch.txtSearch.value="";
			document.frmSearch.txtSearch.focus();
			return false;
		}*/
	}
	else
	{
		if(!chkBlank(document.frmSearch.txtSearch,"User Email")) return false;
		/*if (document.frmSearch.txtSearch.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)
		{
			alert("Please enter a valid Email.");
			document.frmSearch.txtSearch.value="";
			document.frmSearch.txtSearch.focus();
			return false;
		}*/
	}
	// Submit the form for search 
	document.frmSearch.submit();
}


function CheckDeleteValidation()
{
	count=fnCheckCount(document.frmUpdate.chkSelect);
	if(count==0) alert("No row selected");
	else
	{
		if (confirm('Would you like to proceed'))
		{
			fnFillSelectedIDs(document.frmUpdate.chkSelect,document.frmUpdate.hdnIDs);
			document.frmUpdate.hdnAction.value="Delete";
			document.frmUpdate.submit();
		}
	}
}



function CheckUpdateValidation()

{

	// Validation checks for edit 

	if(!chkBlank(document.frmEdit.txtUserName,"User Name")) return false;
	if(document.frmEdit.txtUserName.value.search(/^ |^0|@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{/) != -1)
	{
		alert("User Name should be alphanumeric only.");
		document.frmEdit.txtUserName.value="";
		document.frmEdit.txtUserName.focus();
		return false;
	}

	if(!chkBlank(document.frmEdit.txtEmail,"User EmailID")) return false;
	if (document.frmEdit.txtEmail.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)
	{
		alert("Please enter a valid Email.");
		document.frmEdit.txtEmail.focus();
		return false;
	}
	//if(!chkEmail(document.frmAdd.txtEmail,"User EmailID")) return false;
	if(document.frmEdit.txtPassword.value!='' || document.frmEdit.txtConfirmPassword.value !=''){
		if(!chkBlank(document.frmEdit.txtPassword,"Password")) return false;
		if(!CheckPassword(document.frmEdit.txtPassword.value)) {
			alert("Your password must contains: \n1. At least one upper case English letter. \n2. At least one lower case English letter. \n3. At least one digit. \n4. At least one special character");
			return false;
		}
		if(document.frmEdit.txtPassword.value.search(/^ | /) != -1)
		{
			alert("Please enter a valid Password");
			document.frmEdit.txtPassword.value="";
			document.frmEdit.txtPassword.focus();
			return false;
		}
	
		if(!chkBlank(document.frmEdit.txtConfirmPassword,"Confirm Password")) return false;
		if(document.frmEdit.txtPassword.value!=document.frmEdit.txtConfirmPassword.value)
		{
			alert("Password don't match. Please re-enter Passwords.");
			document.frmEdit.txtPassword.value="";
			document.frmEdit.txtConfirmPassword.value="";
			document.frmEdit.txtPassword.focus();
			return false;
		}
	}
	//if(!chkPassword(document.frmEdit.txtPassword,document.frmEdit.txtConfirmPassword))return false;

	// Submit the form for edit 

	document.frmEdit.hdnAction.value="Update";

	document.frmEdit.submit();

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



function CheckCancelValidation()

{

	document.frmEdit.hdnAction.value="";

	document.location.href="index.php?Option=Users&SubOption=ConsoleUser";

}
function CheckPassword(value) {
	return /[\@\#\$\%\^\&\*\(\)\_\+\!]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) && /[A-Z]/.test(value);
}
function ResetPassword() {
	if(!chkBlank(document.frmEdit.txtUserName,"User Name")) return false;
	if(document.frmEdit.txtUserName.value.search(/^ |^0|@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{/) != -1)
	{
		alert("User Name should be alphanumeric only.");
		document.frmEdit.txtUserName.value="";
		document.frmEdit.txtUserName.focus();
		return false;
	}

	if(!chkBlank(document.frmEdit.txtEmail,"User EmailID")) return false;
	if (document.frmEdit.txtEmail.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)
	{
		alert("Please enter a valid Email.");
		document.frmEdit.txtEmail.focus();
		return false;
	}
	document.frmEdit.hdnAction.value="Reset";
	document.frmEdit.submit();
}
</SCRIPT>
<table width="100%" border="0" cellpadding="0" cellspacing="0">

	<tr>
		<td>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td align="center">
			<table width="540" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="3" align="center" class="heading-main-red">&nbsp;</td>
        </tr>
	<tr>
          <td colspan="3" align="center">
	<font color=red> 

	<?php 

		      if(isset($_SESSION['msg']) && $_SESSION['msg']!="")

		      {

			      echo $_SESSION['msg'];

			      $_SESSION['msg']="";

		      }

		      ?>

	</font>
	</td>
        </tr>
        <tr>
          <td width="25" height="24" align="left" valign="top"><img src="images/reports_top-left.gif" alt="curve-left" width="25" height="34" /></td>
          <td width="100%" class="heading-reports">Add / Edit / Delete Console User </td>
          <td width="28" height="24" align="right" valign="top"><img src="images/reports_top-right.gif" alt="curve-right" width="28" height="34" /></td>
        </tr>
        <tr>
          <td width="25" align="left" valign="top" background="images/reports_top-lt-middle.gif">&nbsp;</td>
          <td align="left" valign="top" bgcolor="#FCFEEC"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="81" align="center" valign="top">
					<table width="545" border="0" align="center" cellpadding="0" cellspacing="0">

  <?php

if($action=="Edit"): // Display Edit Form
?>
  
  <tr> 

    <td colspan="2" valign="top">

        <table width="100%" border="0" cellpadding="0" cellspacing="0">

        <tr> 

          <td>
		  	<form action="index.php?Option=Users&amp;SubOption=ConsoleUser" method="post" name="frmEdit" id="frmUpdate">

              <table width="545" border="0" align="center" cellpadding="0" cellspacing="0">

                <tr> 

                  <td colspan="4" align="center"> Edit Consol 

                    User </td>
                </tr>

                <tr> 

                  <td colspan="4" align="left" class="textTwo"><font color=red> 

                    <?php 

				  if(isset($_SESSION['msg']) && $_SESSION['msg']!="")

				  {

					  echo $_SESSION['msg'];

					  $_SESSION['msg']="";

				  }

				  ?>

                    </font></td>
                </tr>

                <tr> 

                  <td colspan="4" align="right" class="textTwo">&nbsp;</td>
                </tr>

                <tr class="textTwo"> 

                  <td align="right" class="textNormalOne">Full Name :</td>

                  <td width="30%"><input name="hdnUserID" type="hidden"  id="hdnUserID"  value="<?php echo $row_consoleuser1['user_id']?>"> 

                  <input name="txtUserName" type="text" class="txtBoxOne" id="txtUserName" maxlength="30" value="<?php  echo $row_consoleuser1['full_name']?>"></td>

                  <td width="24%" align="right" class="textNormalOne">Email Address<font color="red" size="2">*</font>&nbsp;:</td>

                  <td width="27%"><input name="txtEmail" type="text" class="txtBoxOne" id="txtEmail" maxlength="50" value="<?php  echo $row_consoleuser1['email']?>"></td>
                </tr>

                <tr class="textTwo"> 

                  <td width="19%" align="right">&nbsp;</td>

                  <td colspan="3">&nbsp;</td>
                </tr>
		
                <tr class="textTwo"> 

                  <td width="19%" align="right" class="textNormalOne">Password :</td>

                  <td><input name="txtPassword" type="password" class="txtBoxOne" id="txtPassword" maxlength="30" /></td>
                  <td align="right" class="textNormalOne">Confirm Password :</td>

                  <td><input name="txtConfirmPassword" type="password" class="txtBoxOne" id="txtConfirmPassword" maxlength="30"></td>
                </tr>
		
		<tr>
                  <td colspan="4" >&nbsp;</td>
                </tr>
				
				<tr class="textTwo"> 
                  <td width="19%" align="right" class="textNormalOne">Blocked :</td>
                  <td><select name="is_blocked">
					<option value="No" <?php echo ($row_consoleuser1['is_blocked'] == 'No') ? "selected" : ""; ?>>No</option>
					<option value="Yes" <?php echo ($row_consoleuser1['is_blocked'] == 'Yes') ? "selected" : ""; ?>>Yes</option>
				  </select></td>
                  <td align="right" class="textNormalOne"></td>
                  <td></td>
                </tr>

                <tr> 

                  <td colspan="4" >&nbsp;</td>
                </tr>
				 <tr> 

                <tr class="textTwo">
                  <td colspan="4"><font color="red" size="2">*</font>&nbsp;<font color="red">Email address will be used as Login Id to this console.</font></td>
                </tr>
			<tr> 

                  <td colspan="4" >&nbsp;</td>
                </tr>

                <tr> 

                  <td colspan="4" class="textTwo"><table width="350" border="0" align="center" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td><input name="hdnAction" type="hidden"  id="hdnAction" value=""> 

                          <input name="cmdUpdate" type="button" class="buttongrey" id="cmdUpdate" value="Update" onClick="CheckUpdateValidation()"></td>

                        <td><input name="cmdCancel" type="button" class="buttongrey" id="cmdCancel" value="Cancel" onClick="CheckCancelValidation()"></td>
                      </tr>

                    </table></td>
                </tr>

                <tr class="textTwo"> 

                  <td colspan="4">&nbsp;</td>
                </tr>
              </table>

            </form>
			</td>
        </tr>

        <tr> 

          <td>&nbsp;</td>
        </tr>
      </table> 
	</td>
  </tr>
<? 
else: // Display Add/Search Form
?>
  <tr> 
    <td colspan="2" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td>
		  <?
		  	if($_REQUEST['cmdaddhd1'] == "addconsole") {
 		  ?>
		  <form action="index.php?Option=Users&amp;SubOption=ConsoleUser" method="post" name="frmAdd" id="frmAdd">
              <table width="545" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr> 
                  <td colspan="4" align="center"> Add Consol 
                    User </td>
                </tr>
                <tr> 

                  <td colspan="4" align="left" class="textTwo"><font color=red> 

                    <?php 

					  if(isset($_SESSION['msg']) && $_SESSION['msg']!="")

					  {

						  echo $_SESSION['msg'];

						  $_SESSION['msg']="";

					   }

				  ?>

                    </font></td>
                </tr>

                <tr> 

                  <td colspan="4" align="right" class="textTwo">&nbsp;</td>
                </tr>

                <tr class="textTwo"> 

                  <td align="right" class="textNormalOne">Full Name :</td>

                  <td width="32%"><input name="txtUserName" type="text" class="txtBoxOne" id="txtUserName" maxlength="30"></td>

                  <td width="24%" align="right" class="textNormalOne">Email Address<font color="red" size="2">*</font>&nbsp;:</td>

                  <td width="28%"><input name="txtEmail" type="text" class="txtBoxOne" id="txtEmail" maxlength="50"></td>
                </tr>

                <tr class="textTwo"> 

                  <td width="16%" align="right">&nbsp;</td>

                  <td colspan="3">&nbsp;</td>
                </tr>
		
                <tr class="textTwo"> 

                  <td width="16%" align="right" class="textNormalOne">Password :</td>

                  <td><input name="txtPassword" type="password" class="txtBoxOne" id="txtPassword" maxlength="30"></td>

                  <td align="right" class="textNormalOne">Confirm Password&nbsp;:</td>

                  <td><input name="txtConfirmPassword" type="password" class="txtBoxOne" id="txtConfirmPassword3" maxlength="30"></td>
                </tr>
		
                <tr> 

                  <td colspan="4" >&nbsp;</td>
                </tr>
                <tr class="textTwo">
                  <td colspan="4"><font color="red" size="2">*</font>&nbsp;<font color="red">Email address will be used as Login Id to this console.</font></td>
                </tr>
				<tr> 

                  <td colspan="4" >&nbsp;</td>
                </tr>
                <tr> 

                  <td colspan="4" class="textTwo"><table width="220" border="0" align="center" cellpadding="0" cellspacing="0">

                      <tr align="center"> 

                        <td><input name="hdnAction" type="hidden"  id="hdnAction" value="Add"> 

                          <input name="cmdAdd" type="button" class="buttongrey" id="cmdAdd" value="Add" onClick="CheckAddValidation()"></td>

                        <td><input name="cmdReset" type="reset" class="buttongrey" id="cmdReset" value="Reset"></td>
                      </tr>

                    </table></td>
                </tr>

               
              </table>

            </form>
		  <? }?>	
		   </td>
        </tr>

        <? if(!isset($_REQUEST['cmdadd11']))
		{
		?> 

        <tr> 

          <td height="20">
		  <form action="index.php?Option=Users&amp;SubOption=ConsoleUser" method="post" name="frmSearch" id="frmSearch">

              <table width="545" border="0" align="center" cellpadding="2" cellspacing="0">

                <tr> 

                  <td colspan="2" align="center">&nbsp;</td>
                </tr>

				<tr class="textTwo"> 

                  <td width="31%" height="30" align="right" class="textNormalOne">Search By :</td>

                  <td width="38%" height="30"> 

                    <select name="lstSearchBy" class="lstBoxOne" id="lstSearchBy">

                      <option value="1">User Name</option>
                      <option value="2">Login ID</option>
                    </select></td>
                </tr>

                <tr class="textTwo"> 

                  <td height="30" align="right" class="textNormalOne">Search:</td>

                  <td height="30"><input name="txtSearch" type="text" class="txtBoxOne" id="txtSearch2" maxlength="100"></td>
                </tr>

                <tr class="textTwo"> 

                  <td height="30" align="right">&nbsp;</td>
                  <td height="30" align="left"><input name="hdnAction" type="hidden"  id="hdnAction" value="Search"><input name="cmdSearch" type="button" class="buttongrey" id="cmdSearch4" value="Search" onClick="CheckSearchValidation()"></td>
                </tr>
              </table>

            </form></td>
        </tr>

  <? } ?>
<?php

//search check

	
	if($action=="Search") : //If-Two
		$rs_consoleuser=$obj_consoleuser->search();
		/*if($_SESSION['Usertype']=='admin')
		{
			$rs_consoleuser=$obj_consoleuser->searchadmin();
		}
		else
		{
			
			$rs_consoleuser=$obj_consoleuser->search();
		}*/
	else:
	$rs_consoleuser=$obj_consoleuser->getall();
		/*if($_SESSION['Usertype']=='admin')
		{
			$rs_consoleuser=$obj_consoleuser->getalladmin();
		}
		else
		{
			?><script>alert("user function call");</script><?
			$rs_consoleuser=$obj_consoleuser->getall();
		}*/
	endif ;//

	
	
		
?>
<?php  ?>

        
<?php   ?>
        
      </table>      </td>
  </tr>
  <?php

 endif

 ?>


</table>
              </td>
            </tr>
          </table>
		  </td>
          <td width="28" align="right" valign="top" background="images/reports_top-rt-middle.jpg">&nbsp;</td>
        </tr>
        
        <tr>
          <td width="25" height="26" align="left" valign="bottom"><img src="images/reports_bottom-left.jpg" alt="curve bot-left" width="25" height="26" /></td>
          <td width="100%" align="left" valign="bottom" background="images/reports_side-bot.jpg">&nbsp;</td>
          <td width="28" height="26" align="right" valign="bottom"><img src="images/reports_bottom-right.gif" alt="curve-bot-right" width="28" height="26" /></td>
        </tr>
      </table>
		</td>
	</tr>
</table>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 

          <td height="10">
		  <form action="index.php?Option=Users&amp;SubOption=ConsoleUser" method="post" name="frmUpdate" id="frmUpdate">

              <table width="100%" border="0" cellspacing="0" cellpadding="0">


                  <tr><td width="38" height="30" class="textNormalTwo"> &nbsp; 

                    <input name="chkSelectAll" type="checkbox" id="chkSelectAll" onClick="fnSelectAll(document.frmUpdate.chkSelect,this)">

                        </td>

                    <td class="textNormalTwo">Select All</td>
                    <td  class="textNormalTwo"><table width="220" border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr align="center">
                        <td>
							<input name="cmdadd11" type="submit" class="buttonOne" id="cmdadd11" value="Add"  />
							<input type="hidden" name="cmdaddhd1" value="addconsole" />
						</td>
                        <td><input name="cmdEdit2" type="button" class="buttonOne" id="cmdEdit2" value="Edit" onclick="CheckEditValidation()" /></td>
                        <td><input name="cmdDelete2" type="button" class="buttonOne" id="cmdDelete2" value="Delete" onclick="CheckDeleteValidation()" /></td>
                      </tr>
                    </table></td>
                </tr>

				   <tr> 

                  <td height="20" colspan="3"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="border"> 

                      <tr class="reports-tbldata-head"> 

                        <td width="63" align="center">Select</td>

                        <td width="300" style="padding-left:4px;">Consol User Name</td>

                        <td width="450" style="padding-left:4px;"> Login ID </td>

                        <td width="150" style="padding-left:4px;"> Blocked </td>
                      </tr>

<?php

while ($row_consoleuser=$GLOBALS['obj_db']->fetch_array($rs_consoleuser)):

 ?>

              

			             <tr class="reports-tbldata"> 

                        <td align="center"><input name="chkSelect" type="checkbox" id="chkSelect" value="<?php echo $row_consoleuser['user_id'] ?>"></td>

                        <td style="padding-left:4px;"><?php echo $row_consoleuser['full_name'] ?></td>

                        <td style="padding-left:4px;"><?php echo $row_consoleuser['user_name'] ?></td>

						<td style="padding-left:4px;"><?php echo ($row_consoleuser['is_blocked'] == 'Yes') ? "Yes <br> " . $row_consoleuser['last_blocked_date'] : "No"; ?></td>
				      </tr>

<?php

		endwhile

?>

                </table></td>
                </tr>

				  <tr> 

                  <td height="2" colspan="3" > </td>
                </tr>




                <tr> 

                  <td colspan="3"><table width="220" border="0" align="right" cellpadding="2" cellspacing="0">

                      <tr align="center">
                        <td><input name="cmdadd11" type="submit" class="buttonOne" id="cmdadd11" value="Add"  />
							<input type="hidden" name="cmdaddhd1" value="addconsole" /></td> 

                        <td><input name="hdnIDs" type="hidden"  id="hdnIDs" value=""><input name="hdnAction" type="hidden"  id="hdnAction" value=""><input name="cmdEdit" type="button" class="buttonOne" id="cmdEdit" value="Edit" onClick="CheckEditValidation()"></td>

                        <td><input name="cmdDelete" type="button" class="buttonOne" id="cmdDelete" value="Delete" onClick="CheckDeleteValidation()"></td>
                      </tr>

                    </table></td>
                </tr>
              </table>

            </form></td>
        </tr>
  
</table>


</table>