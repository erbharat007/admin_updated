function CheckAddValidation()
{
	// Validation checks for add 
	
	document.frmEdit.hdnAction.value="Add";
	document.frmEdit.submit();
}


function CheckSearchValidation()
{
	// Validation checks for search 
	if(document.frmSearch.lstSearchBy.value==0)
	{
		if(!chkBlank(document.frmSearch.txtSearch,"User Name")) return false;
		/*if(document.frmSearch.txtSearch.value.search(/^ |@|#|!|~|"|'|\;|\:|`|\$|\||%|\^|&|\*|\+|\=|\?|\>|\<|\\|\]|\[|\}|\{/) != -1)
		{
			alert("Please enter a valid User Name");
			document.frmSearch.txtSearch.value="";
			document.frmSearch.txtSearch.focus();
			return false;
		} */
	}
	else
	{
		if(!chkBlank(document.frmSearch.txtSearch,"User Email")) return false;
		if(document.frmSearch.txtSearch.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)
		{
			alert("Please enter a valid User Email");
			document.frmSearch.txtSearch.value="";
			document.frmSearch.txtSearch.focus();
			return false;
		} 
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
	if(id==-1)
	{
		alert("Please select from User Roles.");
		fbox.focus();
		return false;
	}
	for(var j=0;j<fbox.options.length;j++)
	{
		if(fbox.options[j].selected==true)
		{
			var roleid=fbox.options[j].value;
			for(var i=0; i<tbox.options.length; i++) 
			{
				if(tbox.options[i].value==roleid)
				{
				  alert("The role already exits");
				   return false;
				}
			}	
			var no = new Option();
			no.value = fbox.options[j].value;
			no.text = fbox.options[j].text;
			tbox.options[tbox.options.length] = no;  
		}
	}
	
}



function remove(fbox)
{
	var id=fbox.selectedIndex;
	if(id==-1)
	{
		alert("Please select from 'Selected User Role' to Remove.");
		fbox.focus();
		return false;
	}
	if(confirm('Would you like to proceed'))
	{
		for(var i=0; i<fbox.options.length; i++) 
		{
			if(fbox.options[i].selected && fbox.options[i].value != '')
			{
				fbox.options[i].value = '';
				fbox.options[i].text = '';
			}
		}
		BumpUp(fbox);
	}
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

	document.location.href="index.php?Option=Users&SubOption=AssignRole";

}