// JavaScript Document
function CheckAddValidation()
{
	flag=0;
	
	if(!chkBlank(document.frmAdd.txtRoleName,"Role Name")) 	return false;
	if(document.frmAdd.txtRoleName.value.search(/^ |^[0-9]|@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{/) != -1)
	{
		alert("Role Name should be alphanumeric only.");
		document.frmAdd.txtRoleName.value="";
		document.frmAdd.txtRoleName.focus();
		return false;
	}
	
	document.frmAdd.hdnFeatureID.value="";
	for(i=0;i<document.frmAdd.lstScreenNames.options.length;i++)
	{
		if(document.frmAdd.lstScreenNames.options[i].selected==true)
		{
			flag=1;
			if(document.frmAdd.hdnFeatureID.value=="")
			{
			document.frmAdd.hdnFeatureID.value=document.frmAdd.lstScreenNames.options[i].value;
			}
			else
			{
			document.frmAdd.hdnFeatureID.value=document.frmAdd.hdnFeatureID.value+";"+document.frmAdd.lstScreenNames.options[i].value;			
			}
		}
	}
	if(flag==0)
	{
		alert("Please select a Screen Name.");
		document.frmAdd.lstScreenNames.focus();
		return false;
	}
	
	document.frmAdd.hdnAction.value="Add";
	document.frmAdd.submit();

}

function CheckSearchValidation()
{

	// Validation checks for search 
	if(!chkBlank(document.frmSearch.txtSearchRole,"Role Name")) return false;
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

function CheckDeleteValidation()
{

	// Validation checks for delete 

	count=fnCheckCount(document.frmUpdate.chkSelect);

	if(count==0) alert("No row selected");

	else

	{

		// Submit the form for delete

		fnFillSelectedIDs(document.frmUpdate.chkSelect,document.frmUpdate.hdnIDs);

		document.frmUpdate.hdnAction.value="Delete";

		document.frmUpdate.submit();

	}

}

function CheckUpdateValidation()
{
	flag=0;
	if(!chkBlank(document.frmEdit.txtRoleName,"Role Name")) return false;
	
	if(document.frmEdit.txtRoleName.value.search(/^ |^[0-9]|@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{/) != -1)
	{
				alert("Role Name should be alphanumeric only.");
				document.frmEdit.txtRoleName.value="";
				document.frmEdit.txtRoleName.focus();
				return false;
	}
	
	document.frmEdit.hdnFeatureID.value="";
	for(i=0;i<document.frmEdit.lstScreenNames.options.length;i++)
	{
		if(document.frmEdit.lstScreenNames.options[i].selected==true)
		{
			flag=1;
			if(document.frmEdit.hdnFeatureID.value=="")
			{
			document.frmEdit.hdnFeatureID.value=document.frmEdit.lstScreenNames.options[i].value;
			}
			else
			{
			document.frmEdit.hdnFeatureID.value=document.frmEdit.hdnFeatureID.value+";"+document.frmEdit.lstScreenNames.options[i].value;
			}
		}
	}		
	
	if(flag==0)
	{
		alert("Please select a Screen Name.");
		document.frmEdit.lstScreenNames.focus();
		return false;
	}
	
	// Submit the form for edit 
	document.frmEdit.hdnAction.value="Update";
	document.frmEdit.submit();

}

function CheckCancelValidation()
{
	document.frmEdit.hdnAction.value="";
	document.location.href="index.php?Option=Users&SubOption=UserRole";

}