$(function() 
{
	$("input#required_da_amount_perday, input#tickets_purchased_by_amount, input#hotel_accommodation_by_amount, input#local_conveyance_amount, input#amount, input#start_meter_reading, input#end_meter_reading").on("keydown", function(event) 
	{
		if(event.shiftKey)
		{
			event.preventDefault();
		}
		if (!(event.keyCode == 8                                // backspace
		|| event.keyCode == 9                               // tab
		|| event.keyCode == 17                              // ctrl
		|| event.keyCode == 46                              // delete
		|| (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
		|| (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
		|| (event.keyCode >= 96 && event.keyCode <= 105)    // number on keypad
		|| event.keyCode == 190)          // ctrl + a, on same control
	) 
		{
			event.preventDefault();     // Prevent character input
		}
		else 
		{
			prevKey = event.keyCode;
			prevControl = event.currentTarget.id;
		}
		
	});	
	
	$('input#required_da_amount_perday').blur(function(){
		
		if($('#tour_start_date').val() != '' && $('#tour_end_date').val() != '')
		{
			var dateDiff = dateDiffInDays($('#tour_start_date').val(), $('#tour_end_date').val());
			dateDiff = parseInt(dateDiff)+parseInt(1);
			$("span#days_count").html(dateDiff);	
		}	
		
		var days = parseFloat(dateDiff);
		var requiredDaAmountPerday = parseFloat($("#required_da_amount_perday").val());
		
		if(days != '' && days > 0 && requiredDaAmountPerday != '' && requiredDaAmountPerday > 0)
		{
			var totalAmount = parseFloat(days*requiredDaAmountPerday);
			totalAmount = totalAmount.toFixed(2);
			$("span#da_total").html(totalAmount);
		}	
		
	});
	
	$('input#start_meter_reading, input#end_meter_reading').blur(function()
	{
		var startMeter = parseFloat($.trim($('#start_meter_reading').val()));
		var endMeter = parseFloat($.trim($('#end_meter_reading').val()));
		
		if(startMeter != '' && endMeter != '')
		{
			if(startMeter > endMeter)
			{
				alert('Start meter reading can\'t be larger than end meter reading');				
				return false;
			}	
			
			var totalKms = endMeter-startMeter;
			totalKms = totalKms.toFixed(2);
			$("span#total_kms").html(totalKms);
			$("input[name='total_kms']").val(totalKms);
		}
	});
	$('form#da-calculation *').focus(function()
	{
		$('input[name="btn-calculate-da"]').attr("id", "btn-calculate-da").attr("type", "button").addClass("btn-default").removeClass("btn-success").val('Calculate DA');
	});
	$('#tour_start_date').datepicker({
								 onSelect: function(dateStr) 
								  {
										var d = $.datepicker.parseDate('dd/mm/yy', dateStr);
										//d.setDate(d.getDate() + 1);
										$("#tour_end_date").datepicker('option', 'minDate', d);
										$('#tour_end_date').datepicker('setDate', d);
										setTimeout(function(){
											$("#tour_end_date").datepicker('show');
										}, 16);
								  },
								dateFormat: 'dd/mm/yy',								
								buttonImageOnly: true,								
								changeMonth: true,
								changeYear: true								
							});	
	$('#tour_end_date, #date').datepicker({
								dateFormat: 'dd/mm/yy',								
								buttonImageOnly: true,								
								changeMonth: true,
								changeYear: true								
							});	

	
							
	$(document).on('focus', 'input[name^="reservation_start_date["]', function(e) 
	{
		$(this).datepicker({
								 onSelect: function(dateStr) 
								  {
									 var d = $.datepicker.parseDate('dd/mm/yy', dateStr);
									 $(this).parent().siblings().find('input[name^="reservation_return_date["]').datepicker({dateFormat: 'dd/mm/yy',buttonImageOnly: true,changeMonth: true,changeYear: true});
									 $(this).parent().siblings().find('input[name^="reservation_return_date["]').datepicker('option', 'minDate', d);
									 $(this).parent().siblings().find('input[name^="reservation_return_date["]').datepicker('setDate', d);
									 //$(this).parent().siblings().find('input[name^="reservation_return_date["]').datepicker('show');
								  },
								  dateFormat: 'dd/mm/yy',								
								  buttonImageOnly: true,								
								  changeMonth: true,
								  changeYear: true
							});	
	});

	$(document).on('focus', 'input[name="reservation_start_date_New[]"]', function(e) 
	{
		$(this).datepicker({
								 onSelect: function(dateStr) 
								  {
									 var d = $.datepicker.parseDate('dd/mm/yy', dateStr);
									 $(this).parent().siblings().find('input[name="reservation_return_date_New[]"]').datepicker({dateFormat: 'dd/mm/yy',buttonImageOnly: true,changeMonth: true,changeYear: true});
									 $(this).parent().siblings().find('input[name="reservation_return_date_New[]"]').datepicker('option', 'minDate', d);
									 $(this).parent().siblings().find('input[name="reservation_return_date_New[]"]').datepicker('setDate', d);
									 //$(this).parent().siblings().find('input[name="reservation_return_date_New[]"]').datepicker('show');
								  },
								  dateFormat: 'dd/mm/yy',								
								  buttonImageOnly: true,								
								  changeMonth: true,
								  changeYear: true
							});	
	});	
	
	$(document).on('focus', 'input[name^="reservation_return_date["], input[name="reservation_return_date_New[]"]', function(e) 
	{
		$(this).datepicker({
								dateFormat: 'dd/mm/yy',								
								buttonImageOnly: true,								
								changeMonth: true,
								changeYear: true								
							});
	});	
	
	var addButton = $('.add_more_field'); //Add button selector
	var addButtonTour = $('.add_more_field_tour'); //Add button selector
	var addButtonDa = $('.add_more_field_da'); //Add button selector
	
	var wrapper = $('.more-field-wrapper'); //Input field wrapper
	var fieldHTML = '<div class="row remark-section"><div class="col-md-4"><textarea name="comments[]" rows="5" cols="60" class="form-control"></textarea></div><div class="col-md-4 imagePreviewSection"><div class="imagePreview"></div><input type="file" name="userFile[]" class="uploadFile" /></div><div class="col-md-2 delete-img-div hide"><a href="javascript:void(0);" class="delete_image" title="Delete Image">Delete Image</a><input type="hidden" name="image_deleted[]" value="0" /></div><a href="javascript:void(0);" class="remove_button" title="Remove field">Remove row</a></div>'; 
	
	var fieldHTMLNEW = '<div class="row remark-section"><div class="col-md-4"><textarea name="commentsNew[]" rows="5" cols="60" class="form-control"></textarea></div><div class="col-md-4 imagePreviewSection"><div class="imagePreview"></div><input type="file" name="userFileNew[]" class="uploadFile" /></div><div class="col-md-2 delete-img-div hide"><a href="javascript:void(0);" class="delete_image" title="Delete Image">Delete Image</a><input type="hidden" name="image_deletedNew[]" value="0" /></div><a href="javascript:void(0);" class="remove_button" title="Remove field">Remove row</a></div>';
	//New input field html 
	
	var fieldHTMLTour = '<tr class="tour-res-row"><td><input name="reservation_start_date[]" class="form-control date-picker" readonly="readonly" /></td><td><input name="reservation_return_date[]" class="form-control date-picker" readonly="readonly" /></td><td><input type="text" name="reservation_from[]" class="form-control" /></td><td><input type="text" name="reservation_to[]" class="form-control" /></td><td><select name="reservation_mode[]" class="form-control"><option value="-1">-- Select -- </option><option value="Bus">Bus</option><option value="Train">Train</option><option value="Flight">Flight</option><option value="Taxi">Taxi</option><option value="Hotel">Hotel</option></select></td><td><textarea name="reservation_details[]" rows="4" cols="40" class="form-control"></textarea></td><td><a href="javascript:void(0);" class="remove_button_tour" title="Remove field">Remove row</a></td></tr>'; 
	
	var fieldHTMLTourNEW = '<tr class="tour-res-row"><td><input name="reservation_start_date_New[]" class="form-control date-picker" readonly="readonly" /></td><td><input name="reservation_return_date_New[]" class="form-control date-picker" readonly="readonly" /></td><td><input type="text" name="reservation_from_New[]" class="form-control" /></td><td><input type="text" name="reservation_to_New[]" class="form-control" /></td><td><select name="reservation_mode_New[]" class="form-control"><option value="-1">-- Select -- </option><option value="Bus">Bus</option><option value="Train">Train</option><option value="Flight">Flight</option><option value="Taxi">Taxi</option><option value="Hotel">Hotel</option></select></td><td><textarea name="reservation_details_New[]" rows="4" cols="40" class="form-control"></textarea></td><td><a href="javascript:void(0);" class="remove_button_tour" title="Remove field">Remove row</a></td></tr>'; 
	
	var fieldHTMLDa = '<tr class="da-res-row"><td><input name="da_date_time_from[]" class="form-control date-picker" readonly="readonly" /></td><td><input name="da_date_time_to[]" class="form-control date-picker" readonly="readonly" /></td><td><input name="da_from[]" class="form-control" /></td><td><input name="da_to[]" class="form-control" /></td><td><span name="da_total_journey_hours[]"></span><input type="hidden" name="da_total_journey_hours[]" value="" /></td><td><a href="javascript:void(0);" class="remove_button_da" title="Remove field">Remove row</a></td></tr>';
	
	var fieldHTMLDaNEW = '<tr class="da-res-row"><td><input name="da_date_time_from_New[]" class="form-control date-picker" readonly="readonly" /></td><td><input name="da_date_time_to_New[]" class="form-control date-picker" readonly="readonly" /></td><td><input name="da_from_New[]" class="form-control" /></td><td><input name="da_to_New[]" class="form-control" /></td><td><span name="da_total_journey_hours_New[]"></span><input type="hidden" name="da_total_journey_hours_New[]" value="" /></td><td><a href="javascript:void(0);" class="remove_button_da" title="Remove field">Remove row</a></td></tr>';
	
	$(addButton).click(function(){ //Once add button is clicked
		var action = $(this).data('action');
		if(action == 'add')
		{
		    $(wrapper).append(fieldHTML); // Add field html
		}
		else
		{
			$(wrapper).append(fieldHTMLNEW); // Add field html
		}
	});
	$(addButtonTour).click(function(){ //Once add button is clicked
		var action = $(this).data('action');
		if(action == 'add')
		{
		    $(wrapper).append(fieldHTMLTour); // Add field html
		}
		else
		{
			$(wrapper).append(fieldHTMLTourNEW); // Add field html
		}
	});
	$(addButtonDa).click(function(){ //Once add button is clicked
		var action = $(this).data('action');
		if(action == 'add')
		{
		    $(wrapper).append(fieldHTMLDa); // Add field html
		}
		else
		{
			$(wrapper).append(fieldHTMLDaNEW); // Add field html
		}
	});
	$(wrapper).on('click', '.remove_button', function(e){ //Once remove button is clicked
		e.preventDefault();
		$(this).parent('div.remark-section').remove(); //Remove field html
	});
	
	$(wrapper).on('click', '.remove_button_tour, .remove_button_da', function(e){ //Once remove button is clicked
		e.preventDefault();
		$(this).parent().siblings().remove(); //Remove field html
		$(this).parent().remove(); //Remove field html
	});
	
	$(wrapper).on('click', '.delete_image', function(e){ // Once delete image button is clicked
		e.preventDefault();
		$(this).parent().siblings(".imagePreviewSection").find(".imagePreview").html("").css("background-image", "");
		$(this).parent().addClass('hide');
		$(this).siblings("input[name^='image_deleted']").val(1);
	});
	
	$(wrapper).on("change", ".uploadFile", function()
    {
		var elem = $(this);
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
        
		var showProgress = function(elem) 
		{
			$(elem).siblings(".imagePreview").html("progress");			
		};
		
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
			reader.onloadstart = showProgress();
			reader.onprogress = showProgress();
			reader.onload = showProgress();	
			
            reader.readAsDataURL(files[0]); // read the local file
            
            reader.onloadend = function(){ // set image data as background of div
				$(elem).siblings(".imagePreview").html("").css("background-image", "url("+this.result+")");
				$(elem).parent().siblings(".delete-img-div").removeClass('hide');
				$(elem).parent().siblings(".delete-img-div").find("input[name^='image_deleted']").val(0);
            }
        }
		else
		{
			$(elem).siblings(".imagePreview").css("background-image", "").html("Can't show preview of file");
		}	
    });
	
	$('form#da-calculation').on('click', 'input[type="submit"]', function(e)
	{
		$('form#da-calculation').submit();
	});
	$('form#da-calculation').on('click', '#btn-calculate-da', function(e)
	{
		var totalTravelTimeInHrs = 0;
		var datesArr = [];
		var dateError = false;
		var dateErrorNew = false;
		$("input[name^='da_date_time_from[']").each(function(elemIndex){
			var txtFromDateTime = $.trim($("input[name^='da_date_time_from[']").eq(elemIndex).val());
			var txtToDateTime = $.trim($("input[name^='da_date_time_to[']").eq(elemIndex).val());
			
			if(txtFromDateTime != '')
					$("input[name^='da_date_time_from[']").eq(elemIndex).removeClass('errortag');
			if(txtToDateTime != '')
					$("input[name^='da_date_time_to[']").eq(elemIndex).removeClass('errortag');
				
			if(txtFromDateTime == '' || txtToDateTime == '')
			{
				dateError = true;
				if(txtFromDateTime == '')
					$("input[name^='da_date_time_from[']").eq(elemIndex).addClass('errortag');
				if(txtToDateTime == '')
					$("input[name^='da_date_time_to[']").eq(elemIndex).addClass('errortag');
				
				return false;
			}	
			
			var txtFromDateTimeArr = txtFromDateTime.split(" ");
			var txtToDateTimeArr   = txtToDateTime.split(" ");
			var txtFromDateArr     = txtFromDateTimeArr[0].split("/");
			var txtToDateArr       = txtToDateTimeArr[0].split("/");
			var txtFromTimeArr     = txtFromDateTimeArr[1].split(":");
			var txtToTimeArr       = txtToDateTimeArr[1].split(":");
			
			var txtFromDateTimeObj = new Date(txtFromDateArr[2], txtFromDateArr[1]-1, txtFromDateArr[0], txtFromTimeArr[0], txtFromTimeArr[1]);
			var txtToDateTimeObj   = new Date(txtToDateArr[2], txtToDateArr[1]-1, txtToDateArr[0], txtToTimeArr[0], txtToTimeArr[1]);
			
			//datesArr.push(new Date(txtFromDateArr[2], txtFromDateArr[1]-1, txtFromDateArr[0]));
			//datesArr.push(new Date(txtToDateArr[2], txtToDateArr[1]-1, txtToDateArr[0]));
			
			datesArr.push(txtFromDateTimeObj);
			datesArr.push(txtToDateTimeObj);
			
			var txtToDateTimeMS   = txtToDateTimeObj.getTime();
			var txtFromDateTimeMS = txtFromDateTimeObj.getTime();
			if(txtToDateTimeMS < txtFromDateTimeMS)
			{
				dateError = true;
				$("input[name^='da_date_time_to[']").eq(elemIndex).addClass('errortag');
				return false;
			}
			
			var dateTimeDiffMS = txtToDateTimeMS - txtFromDateTimeMS;
			var timeDiffInMins = dateTimeDiffMS/60000;
			var timeDiffInHrs = timeDiffInMins/60;
			totalTravelTimeInHrs += timeDiffInHrs;
			console.log(timeDiffInHrs);
			timeDiffInHrs = timeDiffInHrs.toFixed(2);
			$("span[name^='da_total_journey_hours[']").eq(elemIndex).html(timeDiffInHrs);
			$("input[name^='da_total_journey_hours[']").eq(elemIndex).val(timeDiffInHrs);
		});
		$("input[name='da_date_time_from_New[]']").each(function(elemIndex){
			var txtFromDateTime = $.trim($("input[name='da_date_time_from_New[]']").eq(elemIndex).val());
			var txtToDateTime = $.trim($("input[name='da_date_time_to_New[]']").eq(elemIndex).val());
			
			if(txtFromDateTime != '')
					$("input[name='da_date_time_from_New[]']").eq(elemIndex).removeClass('errortag');
			if(txtToDateTime != '')
					$("input[name='da_date_time_to_New[]']").eq(elemIndex).removeClass('errortag');
				
			if(txtFromDateTime == '' || txtToDateTime == '')
			{
				dateErrorNew = true;
				if(txtFromDateTime == '')
					$("input[name='da_date_time_from_New[]']").eq(elemIndex).addClass('errortag');
				if(txtToDateTime == '')
					$("input[name='da_date_time_to_New[]']").eq(elemIndex).addClass('errortag');
				
				return false;
			}	
			
			var txtFromDateTimeArr = txtFromDateTime.split(" ");
			var txtToDateTimeArr   = txtToDateTime.split(" ");
			var txtFromDateArr     = txtFromDateTimeArr[0].split("/");
			var txtToDateArr       = txtToDateTimeArr[0].split("/");
			var txtFromTimeArr     = txtFromDateTimeArr[1].split(":");
			var txtToTimeArr       = txtToDateTimeArr[1].split(":");
			
			var txtFromDateTimeObj = new Date(txtFromDateArr[2], txtFromDateArr[1]-1, txtFromDateArr[0], txtFromTimeArr[0], txtFromTimeArr[1]);
			var txtToDateTimeObj   = new Date(txtToDateArr[2], txtToDateArr[1]-1, txtToDateArr[0], txtToTimeArr[0], txtToTimeArr[1]);
			
			//datesArr.push(new Date(txtFromDateArr[2], txtFromDateArr[1]-1, txtFromDateArr[0]));
			//datesArr.push(new Date(txtToDateArr[2], txtToDateArr[1]-1, txtToDateArr[0]));
			
			datesArr.push(txtFromDateTimeObj);
			datesArr.push(txtToDateTimeObj);
			
			var txtToDateTimeMS   = txtToDateTimeObj.getTime();
			var txtFromDateTimeMS = txtFromDateTimeObj.getTime();
			if(txtToDateTimeMS < txtFromDateTimeMS)
			{
				dateErrorNew = true;
				$("input[name='da_date_time_to_New[]']").eq(elemIndex).addClass('errortag');
				return false;
			}
			
			var dateTimeDiffMS = txtToDateTimeMS - txtFromDateTimeMS;
			var timeDiffInMins = dateTimeDiffMS/60000;
			var timeDiffInHrs = timeDiffInMins/60;
			totalTravelTimeInHrs += timeDiffInHrs;
			console.log(timeDiffInHrs);
			timeDiffInHrs = timeDiffInHrs.toFixed(2);
			$("span[name='da_total_journey_hours_New[]']").eq(elemIndex).html(timeDiffInHrs);
			$("input[name='da_total_journey_hours_New[]']").eq(elemIndex).val(timeDiffInHrs);
		});	
		if(dateError || dateErrorNew)
		{
			alert("Please fill mandatory fields (marked in red) correctly");
			$('html,body').animate({scrollTop: $('#da-details-section').offset().top }, 500);
			return false;
		}	
		var DA = parseFloat($("span#approved_da_perday").html());
		var minDate = new Date(Math.min.apply(null, datesArr));
		var maxDate = new Date(Math.max.apply(null, datesArr));
		var minDateMS = minDate.getTime();
		var maxDateMS = maxDate.getTime();
		var dateDiffMS = maxDateMS - minDateMS;
		var totalTimeDiffInMins = dateDiffMS/60000;
		var totalTimeDiffInHrs = totalTimeDiffInMins/60;
		totalTimeDiffInHrs = totalTimeDiffInHrs.toFixed(2);
		var totalTimeDiffInDays = totalTimeDiffInHrs/24;
		
		totalTimeDiffInDays = totalTimeDiffInDays.toFixed(2);
		
		totalTravelTimeInHrs = totalTravelTimeInHrs.toFixed(2);
		var totalTravelTimeInDays = totalTravelTimeInHrs/24;
		totalTravelTimeInDays = totalTravelTimeInDays.toFixed(2);
		
		var balanceHours = totalTimeDiffInHrs-totalTravelTimeInHrs;
		balanceHours = balanceHours.toFixed(2);
		var balanceDays = balanceHours/24;
		balanceDays = balanceDays.toFixed(2);		
		
		//console.log(minDate+' - '+maxDate);
		var actualMinMonth = parseInt(minDate.getMonth())+parseInt(1);
		actualMinMonth = (actualMinMonth < 10) ? "0"+actualMinMonth : actualMinMonth;
		var actualMaxMonth = parseInt(maxDate.getMonth())+parseInt(1);
		actualMaxMonth = (actualMaxMonth < 10) ? "0"+actualMaxMonth : actualMaxMonth;
		
		var actualMinDate = minDate.getDate();
		actualMinDate = (actualMinDate < 10) ? "0"+actualMinDate : actualMinDate;
		var actualMaxDate = maxDate.getDate();
		actualMaxDate = (actualMaxDate < 10) ? "0"+actualMaxDate : actualMaxDate;
		
		var minDateShow = actualMinDate+"-"+actualMinMonth+"-"+minDate.getFullYear();
		var maxDateShow = actualMaxDate+"-"+actualMaxMonth+"-"+maxDate.getFullYear();
		
		var HalfDaForTravel = parseFloat(DA*totalTravelTimeInDays/2);
		HalfDaForTravel = HalfDaForTravel.toFixed(2);
		var FullDa = parseFloat(DA*balanceDays);
		FullDa = FullDa.toFixed(2);
		var totalDA = parseFloat(HalfDaForTravel)+parseFloat(FullDa);
		totalDA = totalDA.toFixed(2);
		
		
		$("span#half-da-for-travel").html(HalfDaForTravel); $("input[name='half-da-for-travel']").val(HalfDaForTravel);
		$("span#full-da-for-balance-days").html(FullDa); $("input[name='full-da-for-balance-days']").val(FullDa);
		$("span#show-total-da").html(totalDA); $("input[name='show-total-da']").val(totalDA);
		
		$("span#total-balance-hours").html(balanceHours); $("input[name='total-balance-hours']").val(balanceHours);
		$("span#total-balance-days").html(balanceDays); $("input[name='total-balance-days']").val(balanceDays);
		
		$("span#show-tour-start-date").html(minDateShow); $("input[name='show-tour-start-date']").val(minDateShow);
		$("span#show-tour-end-date").html(maxDateShow); $("input[name='show-tour-end-date']").val(maxDateShow);
		
		$("span#total-tour-time-hrs").html(totalTimeDiffInHrs); $("input[name='total-tour-time-hrs']").val(totalTimeDiffInHrs);
		$("span#total-tour-time-days").html(totalTimeDiffInDays); $("input[name='total-tour-time-days']").val(totalTimeDiffInDays);
		
		$("span#total-travel-time, span#total-travel-time-hrs").html(totalTravelTimeInHrs); $("input[name='total-travel-time-hrs']").val(totalTravelTimeInHrs);
		$("span#total-travel-time-days").html(totalTravelTimeInDays); $("input[name='total-travel-time-days']").val(totalTravelTimeInDays);
		var btnValue = ($(this).data("action") == 'add') ? 'Add' : 'Update';
		$('input[name="btn-calculate-da"]').removeAttr("id").attr("type", "submit").removeClass("btn-default").addClass("btn-success").val(btnValue);
		return false;
	});

});

function checkData() {
    var error = "";
	
	if($.trim($('#tour_start_date').val()) == '')
	{
		error = "Please select tour start date";
		alert(error);
		$('#tour_start_date').focus();
		return false;
	}
	
	if($.trim($('#tour_end_date').val()) == '')
	{
		error = "Please select tour end date";
		alert(error);
		$('#tour_end_date').focus();
		return false;
	}
	
	var tourStartDateArr  = $('#tour_start_date').val().split("/");
	var tourEndDateArr = $('#tour_end_date').val().split("/");
	var tourStartDateObj  = new Date(tourStartDateArr[2], tourStartDateArr[1]-1, tourStartDateArr[0]);
	var tourEndDateObj = new Date(tourEndDateArr[2], tourEndDateArr[1]-1, tourEndDateArr[0]);
	
	if(tourEndDateObj.getTime() < tourStartDateObj.getTime())
	{
		error = "Tour end date can't be lesser than tour start date";
		alert(error);
		$('#tour_end_date').focus();
		return false;
	}
	
	if($.trim($('#tour_place').val()) == '')
	{
		error = "Please enter place of tour";
		alert(error);
		$('#tour_place').focus();
		return false;
	}  
	if($.trim($('#tour_customer').val()) == '')
	{
		error = "Please enter customer name";
		alert(error);
		$('#tour_customer').focus();
		return false;
	}
	if($.trim($('#tour_transport').val()) == -1)
	{
		error = "Please select tour transport";
		alert(error);
		$('#tour_transport').focus();
		return false;
	}
	if($.trim($('#tour_purpose').val()) == '')
	{
		error = "Please write purpose of your tour";
		alert(error);
		$('#tour_purpose').focus();
		return false;
	}
	
	var errorFlag = false;
	var errorFlagNew = false;
	$("input[name^='reservation_start_date[']").each(function(elemIndex){
			
			var txtResFrom   = $.trim($("input[name^='reservation_from[']").eq(elemIndex).val());
			var txtResTo     = $.trim($("input[name^='reservation_to[']").eq(elemIndex).val());
			var txtResMode   = $.trim($("select[name^='reservation_mode[']").eq(elemIndex).val());
			var txtResDetail = $.trim($("textarea[name^='reservation_details[']").eq(elemIndex).val());
			var txtResStartDate = $.trim($("input[name^='reservation_start_date[']").eq(elemIndex).val());
			var txtResReturnDate = $.trim($("input[name^='reservation_return_date[']").eq(elemIndex).val());
			
			var txtResStartDateArr  = txtResStartDate.split("/");
			var txtResReturnDateArr = txtResReturnDate.split("/");
			var txtResStartDateObj  = new Date(txtResStartDateArr[2], txtResStartDateArr[1]-1, txtResStartDateArr[0]);
			var txtResReturnDateObj = new Date(txtResReturnDateArr[2], txtResReturnDateArr[1]-1, txtResReturnDateArr[0]);
			
			if(txtResStartDate != "")
			{
				$("input[name^='reservation_start_date[']").eq(elemIndex).removeClass("errortag");
			}	
			if(txtResReturnDate != "")
			{
				$("input[name^='reservation_return_date[']").eq(elemIndex).removeClass("errortag");
			}
			if(txtResReturnDate != -1)
			{
				$("select[name^='reservation_mode[']").eq(elemIndex).removeClass("errortag");
			}	
			
			if(txtResReturnDateObj.getTime() < txtResStartDateObj.getTime())
			{
				errorFlag = true;
				$("input[name^='reservation_return_date[']").eq(elemIndex).addClass("errortag");
			}	
			
			if(txtResFrom != "" || txtResTo != "" || txtResMode != -1 || txtResDetail != "")
			{
				if(txtResMode == -1)
				{
					errorFlag = true;
					$("select[name^='reservation_mode[']").eq(elemIndex).addClass("errortag");
				}	
				if(txtResStartDate == "")
				{
					errorFlag = true;
					$("input[name^='reservation_start_date[']").eq(elemIndex).addClass("errortag");
				}
				if(txtResReturnDate == "")
				{
					errorFlag = true;
					$("input[name^='reservation_return_date[']").eq(elemIndex).addClass("errortag");
				}
			}	
		});
	$("input[name='reservation_start_date_New[]']").each(function(elemIndex){
			
			var txtResFrom   = $.trim($("input[name='reservation_from_New[]']").eq(elemIndex).val());
			var txtResTo     = $.trim($("input[name='reservation_to_New[]']").eq(elemIndex).val());
			var txtResMode   = $.trim($("select[name='reservation_mode_New[]']").eq(elemIndex).val());
			var txtResDetail = $.trim($("textarea[name='reservation_details_New[]']").eq(elemIndex).val());
			var txtResStartDate = $.trim($("input[name='reservation_start_date_New[]']").eq(elemIndex).val());
			var txtResReturnDate = $.trim($("input[name='reservation_return_date_New[]']").eq(elemIndex).val());
			
			if(txtResStartDate != "")
			{
				$("input[name='reservation_start_date_New[]']").eq(elemIndex).removeClass("errortag");
			}	
			if(txtResReturnDate != "")
			{
				$("input[name='reservation_return_date_New[]']").eq(elemIndex).removeClass("errortag");
			}
			if(txtResReturnDate != -1)
			{
				$("select[name='reservation_mode_New[]']").eq(elemIndex).removeClass("errortag");
			}	
			
			if(txtResFrom != "" || txtResTo != "" || txtResMode != -1 || txtResDetail != "")
			{
				if(txtResMode == -1)
				{
					errorFlagNew = true;
					$("select[name='reservation_mode_New[]']").eq(elemIndex).addClass("errortag");
				}	
				if(txtResStartDate == "")
				{
					errorFlagNew = true;
					$("input[name='reservation_start_date_New[]']").eq(elemIndex).addClass("errortag");
				}
				if(txtResReturnDate == "")
				{
					errorFlagNew = true;
					$("input[name='reservation_return_date_New[]']").eq(elemIndex).addClass("errortag");
				}
			}	
		});	
	if(errorFlag || errorFlagNew)
	{
		alert("Please fill mandatory fields marked in red.");
		$('html,body').animate({scrollTop: $('#res-details-section').offset().top }, 1000);
		return false;
	}	
	
	return true;    
}					

function checkDataConveyance() 
{
    var error = "";
	var errorFlag = false;
	
	if($.trim($('#type').val()) == -1)
	{
		error = "Please select tour type";
		alert(error);
		$('#type').focus();
		return false;
	}
	
	if($.trim($('#date').val()) == '')
	{
		error = "Please select date";
		alert(error);
		$('#date').focus();
		return false;
	}
	if($.trim($('#from').val()) == '')
	{
		error = "Please enter from place";
		alert(error);
		$('#from').focus();
		return false;
	}  
	if($.trim($('#to').val()) == '')
	{
		error = "Please enter to place";
		alert(error);
		$('#to').focus();
		return false;
	}
	if($.trim($('#paid_by').val()) == -1)
	{
		error = "Please select paid by option";
		alert(error);
		$('#paid_by').focus();
		return false;
	}
	
	var startMeter = parseFloat($.trim($('#start_meter_reading').val()));
	var endMeter = parseFloat($.trim($('#end_meter_reading').val()));
	
	if(startMeter != '' && endMeter != '')
	{
		if(startMeter > endMeter)
		{
			alert('Start meter reading can\'t be larger than end meter reading');
			$('#start_meter_reading').focus();
			return false;
		}
	}
	if($.trim($('#purpose').val()) == '')
	{
		error = "Please write purpose";
		alert(error);
		$('#purpose').focus();
		return false;
	}
	if($.trim($('#travel_mode').val()) == '')
	{
		error = "Please enter mode of travel";
		alert(error);
		$('#travel_mode').focus();
		return false;
	}
	if($.trim($('#amount').val()) == '')
	{
		error = "Please enter amount";
		alert(error);
		$('#amount').focus();
		return false;
	}
	
	$("input[name^='userFile']").each(function(elemIndex){
		var fileName  = $.trim($(this).val());
		var comments  = $.trim($("textarea[name^='comments']").eq(elemIndex).val());
		var imageDeleted  = $.trim($("input[name^='image_deleted']").eq(elemIndex).val());
		
		if(comments != "")
		{
			$("textarea[name^='comments']").eq(elemIndex).removeClass("errortag");
		}
		
		if(fileName != '' && imageDeleted != 1 && comments == '')
		{
			errorFlag = true;
			$("textarea[name^='comments']").eq(elemIndex).addClass("errortag");
		}
	});		
	if(errorFlag)
	{
		alert("Please fill mandatory fields marked in red.");
		return false;
	}
	
    return true;    
}