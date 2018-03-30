$(document).ready(function() {
	
	//add some elements with animate effect
		$(".box").hover(
			function () {
			$(this).find('span.badge').addClass("animated fadeInLeft");
			$(this).find('.ico').addClass("animated fadeIn");
			},
			function () {
			$(this).find('span.badge').removeClass("animated fadeInLeft");
			$(this).find('.ico').removeClass("animated fadeIn");
			}
		);
		
	(function() {

		var $menu = $('.navigation nav'),
			optionsList = '<option value="" selected>Go to..</option>';

		$menu.find('li').each(function() {
			var $this   = $(this),
				$anchor = $this.children('a'),
				depth   = $this.parents('ul').length - 1,
				indent  = '';

			if( depth ) {
				while( depth > 0 ) {
					indent += ' - ';
					depth--;
				}

			}
			$(".nav li").parent().addClass("bold");

			optionsList += '<option value="' + $anchor.attr('href') + '">' + indent + ' ' + $anchor.text() + '</option>';
		}).end()
		.after('<select class="selectmenu">' + optionsList + '</select>');
		
		$('select.selectmenu').on('change', function() {
			window.location = $(this).val();
		});
		
	})();

		//Navi hover
		$('ul.nav li.dropdown').hover(function () {
			$(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
		}, function () {
			$(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
		});		
		
});

function chkBlank(formelement,text)
{
	if (formelement.value.trim()=='')
	{
		alert('Please enter value for '+text);
		formelement.focus();
		return false;
    }
	else
	{
		return true;
	}
}
function fnCheckCount(formelement)
{
	var intMultiple, typeofCheckBox, intLength;
	intLength = formelement.length;
	typeofCheckBox = typeof intLength;
	if (typeofCheckBox=='undefined')
	{
			if (formelement.checked==true)
				return 1;
			else
				return 0;
	}
	else
	{
		intMultiple=0
		for (i = 1;i<=formelement.length;i++)
		{
			if (formelement[i-1].checked==true)
			{
				intMultiple++;
			}
		}
	}
	return intMultiple;
}

function fnSelectAll(formelement,caller)
{
	var intMultiple, typeofCheckBox, intLength;
	intLength = formelement.length;
	typeofCheckBox = typeof intLength;
	if (typeofCheckBox=='undefined')
	{
		formelement.checked=caller.checked
		return true;
	}
	else
	{
		intMultiple=0
		for (i = 1;i<=formelement.length;i++)
		{
			formelement[i-1].checked=caller.checked
		}
	}
	return true;
}

function deSelectAllCheckbox()
{
	if(document.getElementById("chkSelectAll"))
	{
		document.getElementById("chkSelectAll").checked = false;
	}
}

function fnFillSelectedIDs(formelement,targetformelement)
{
	targetformelement.value="";
	intLength = formelement.length;
	typeofCheckBox = typeof intLength;
	if (typeofCheckBox=='undefined')
	{
		if (formelement.checked==true)
			targetformelement.value=formelement.value;
	}
	else
	{
		for(i=0;i<formelement.length;i++)
		{
			if (formelement[i].checked==true)
			{
				if(targetformelement.value=="")
					targetformelement.value=formelement[i].value;
				else
					targetformelement.value=targetformelement.value+","+formelement[i].value;
			}
		}
	}
}

function confirmDeleteAction()
{
	var ans = confirm("Are you sure you want to delete it ?");
	if(!ans)
	{
		return false;
	}
	//return false;
}

// date1 and date2 are javascript Dates in "DD/MM/YYYY" format
function dateDiffInDays(date1, date2) 
{
	var date1Arr = date1.split("/");
	var date2Arr = date2.split("/");
	
	var objDate1 = new Date(date1Arr[2], date1Arr[1]-1, date1Arr[0]);
	var objDate2 = new Date(date2Arr[2], date2Arr[1]-1, date2Arr[0]);
	
  //Get 1 day in milliseconds
  var one_day=1000*60*60*24;

  // Convert both dates to milliseconds
  var date1_ms = objDate1.getTime();
  var date2_ms = objDate2.getTime();

  // Calculate the difference in milliseconds
  var difference_ms = date2_ms - date1_ms;
  console.log(Math.round(difference_ms/one_day));
  // Convert back to days and return
  return Math.round(difference_ms/one_day); 
}

// date1 and date2 are javascript Dates in "DD/MM/YYYY" format
function dateDiff(date1, date2, interval) 
{
	var divideBy = {week:604800000, days:86400000, hours:3600000, minutes:60000, seconds:1000};
	var date1Arr = date1.split("/");
	var date2Arr = date2.split("/");
	
	var objDate1 = new Date(date1Arr[2], date1Arr[1]-1, date1Arr[0]);
	var objDate2 = new Date(date2Arr[2], date2Arr[1]-1, date2Arr[0]);
	
  //Get 1 day in milliseconds
  var one_day=1000*60*60*24;

  // Convert both dates to milliseconds
  var date1_ms = objDate1.getTime();
  var date2_ms = objDate2.getTime();

  // Calculate the difference in milliseconds
  var difference_ms = date2_ms - date1_ms;
  console.log(Math.round(difference_ms/one_day));
  
  // Convert back to required interval and return
  return Math.floor(difference_ms/divideBy[interval]);
}