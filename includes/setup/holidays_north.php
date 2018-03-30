<!-- loads mdp -->
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery-ui.multidatespicker.js"></script>	

<!-- loads some utilities (not needed for your developments) -->
<!--<link rel="stylesheet" type="text/css" href="<?php //echo SITE_URL;?>/css/mdp.css">-->
<?php
$action = $_REQUEST["ofaction"];

require_once("".CLASS_PATH."/class.Holiday.inc.php");
$objHoliday = new Holiday;
require_once("".CLASS_PATH."/common_function.inc.php");

$additional_link_redirect = "";
if(isset($_REQUEST['page']) && $_REQUEST['page'] > 0)
{
	$additional_link_redirect .= "&page=".$_REQUEST['page'];
}
if(isset($_REQUEST['search_text']) && trim($_REQUEST['search_text']) != "")
{
	$additional_link_redirect .= "&search_text=".trim($_REQUEST['search_text']);
}

switch($action)
{
	case "updatesave":
		$arrayData = array($_REQUEST['full-year-calendar'], 'north');
		$_SESSION['msg'] = $objHoliday->updateHolidays($arrayData);
	break;
}

$rsHolidays = $objHoliday->getHolidaysOfYear('all', 'north');
$holidaysList = array();
if($GLOBALS['obj_db']->num_rows($rsHolidays) > 0)
{
	while($row = $GLOBALS['obj_db']->fetch_array($rsHolidays))
	{
		$dateObj = new DateTime($row['date']);
		$holidaysList[] = $dateObj->format('m/d/Y');
	}	
}
?>
<script type="text/javascript">
function showEdit(editableObj) 
{
	$(editableObj).css("background","#FFF");
} 
	
function updateReason(editableObj, id) 
{
	$.ajax({
		url: "ajax.php",
		dataType: 'json',
		type: "POST",
		data: {'action': 'updateHolidayReason', 'region': 'north', 'reasonOfHoliday' : editableObj.innerHTML, 'id': id},
		beforeSend: function() 
		{
			 $(editableObj).css("background","#FFF url(images/loaderIcon.gif) no-repeat right");
		},
		success: function(data)
		{
			$(editableObj).css("background","#FDFDFD");
			if(data.successFlag)
			{
				$(editableObj).append("<p id='message-section' class='success'>"+data.message+"</p>");
			}
			else
			{
				$(editableObj).append("<p id='message-section' class='error'>"+data.message+"</p>");
			}	
			setTimeout(function(){
				$("#message-section").remove();
			}, 3000);
		}        
   });
}
$(function() 
{
	var d = new Date();
	var sat = new Array();   //Declaring array for inserting Saturdays
	var sun = new Array(); 
	for(var j=1; j<=12; j++)
	{
		var getTot = daysInMonth(j,d.getFullYear()); //Get total days in a month
		  //Declaring array for inserting Sundays
		
		var m = j;
		if(m < 10)
		{
			var m = '0'+m;
		}
		
		for(var i=1; i<=getTot; i++)
		{    //looping through days in month
			var newDate = new Date(d.getFullYear(),j-1,i)
			
			if(newDate.getDay()==0)
			{   //if Sunday
				sun.push(m+"/"+i+"/"+d.getFullYear());
			}
			if(newDate.getDay()==6)
			{   //if Saturday
				sat.push(m+"/"+i+"/"+d.getFullYear());
			}

		}
	}
	var satSun = sat.concat(sun);
	//console.log(sat);
	//console.log(sun);

	function daysInMonth(month,year) 
	{
		return new Date(year, month, 0).getDate();
	}
	var holidaysList = [<?php echo '"'.implode('","', $holidaysList).'"' ?>];
	var today = new Date();
	var y = today.getFullYear();
	$('#full-year-calendar').multiDatesPicker({
		//addDates: ['10/14/'+y, '02/19/'+y, '01/14/'+y, '11/16/'+y],
		addDates: holidaysList,
		numberOfMonths: [1,5],
		defaultDate: '1/1/'+y
	});
});
</script>
<div class="page-header">
	<h3>ADD/UPDATE HOLIDAYS</h3>
</div>
<?php 
if(isset($_SESSION['msg']) && $_SESSION['msg']!="")
{
	if(isset($_SESSION['success']) && $_SESSION['success'] == 1)
	{
		$alertClass = "alert-success";
	}
	else
	{
		$alertClass = "alert-danger";
	}
?>
	<div class="alert <?php echo $alertClass; ?> alert-dismissable">           
		<strong>
		<?php
		echo $_SESSION['msg'];
		$_SESSION['msg'] = "";
		$_SESSION['success'] = "";
		?>
        </strong> 
	</div>
<?php
}
?>
<div class="widget-content">
	<form name="frm_dept" method="post" enctype="multipart/form-data" action="index.php?Option=Setup&SubOption=holidayCalendarNorth&ofaction=updatesave<?php echo $additional_link_redirect;?>" class="form-horizontal col-md-6" role="form" onsubmit="return checkData();";>
    	<div class="form-group">
            <label class="col-md-4">Add Holidays</label>
            <div class="col-md-8">
				<input id="full-year-calendar" name="full-year-calendar" type="text" class="form-control" readonly="readonly">
            </div>
        </div> <!-- /.form-group -->
        
		<div class="form-group">
            <div class="col-md-offset-4 col-md-8">
            	<input type="submit" name="Submit" class="btn btn-success" value="Update">                
            </div>
        </div> <!-- /.form-group -->
	</form>
</div>                   
           
   
	<?php
	$dsqle = $objHoliday->getHolidaysOfYear('currentYear', 'north');
	$total_pages = $GLOBALS['obj_db']->num_rows($dsqle);
	?>
	
	<div class="col-md-12" style="clear:both;">
		<form name="search_form" method="post" action="index.php?Option=Setup&SubOption=holidayCalendarNorth">
			<input type="search" required name="search_text" value="<?php echo $_REQUEST['search_text']; ?>">
			<input type="submit" class="btn btn-info" value="Search">
			<input type="button" onclick="window.location.href='index.php?Option=Setup&SubOption=holidayCalendarNorth';" class="btn btn-danger" value="Cancel" />				
		</form>
	</div>	
	
	
	<div class="col-md-12" style="clear:both;">
		<p>List of Holidays of Current Year</p>
	</div>	
	
	<form action="index.php?Option=Setup&SubOption=holidayCalendarNorth" method="post" name="frmUpdate" id="frmUpdate">
		<div class="table-responsive">
			<table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>Date</th>
					<th>Reason of Holiday</th>
				  </tr>
                </thead>
				<tbody>
				<?php
				 $i = 1;
		
				$end = 20;
				$page = $_REQUEST['page'];
				if($page)
					$start = ($page - 1) * $end;
				else
					$start = 0;

				$select_list_href = "&search_text=".$_REQUEST['search_text'];

				$targetpage = "index.php?Option=Setup&SubOption=holidayCalendarNorth";

				$contentrow = $objHoliday->getHolidaysOfYear('currentYear', 'north', $start, $end);
				if($GLOBALS['obj_db']->num_rows($contentrow))
				{	
					$i = 1;
					while($dsqlerow=$GLOBALS['obj_db']->fetch_array($contentrow))
					{
						$dateObj = new DateTime($dsqlerow['date']);
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td>
							<?php 
								echo $dateObj->format('l, d F Y');
							?>
						</td>
						<td contenteditable="true" onBlur="updateReason(this, '<?php echo $dsqlerow['id']; ?>')" onClick="showEdit(this);"><?php echo $dsqlerow['reason_of_holiday']; ?></td>						
					</tr>
					<?php
						$i++;
					}
				}
				else
				{
					echo '<tr><td colspan="100%" align="center">No Record Found</td></tr>';
				}
				?>
		       </tbody>
		    </table>
		</div>        
	</form>
	<table width="100%">
		<tr>
			<td colspan="100%">
				<?php echo display_Admin_Paging_For_Admin($total_pages, $targetpage, $start, $end, $select_list_href); ?>
			</td>
		</tr>
	</table>