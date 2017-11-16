$(document).ready(function() { 

	$( "#datepicker_events" ).datepicker({
		showWeek: true,
		firstDay: 1

	});			

	$(document).on("click", "a.datepicker-day", function(event){
		event.preventDefault();
		var token = $("#token").val();
		var calendar_categories_id = $("#calendar_categories_id").val();
		var calendar_views_id = $("#calendar_views_id").val();
		var period = $("#period option:selected").val();
		var pages_id = $("#pages_id").val();
		var string = $(this).attr('href');
		var date = getParameterByName('date',string);
		if(date.length == 0) {
			// match date width regex
			var rgx = /(\d{4})-(\d{2})-(\d{2})/;
			date = $("#datepicker_events").val();
			if(!date.match(rgx)) {
				date = getToday();
			}
		}
		 
		var action = "calendar_events";
		var cms_dir = $("#cms_dir").val();
		var ajax_dir = cms_dir +'/cms/';
		$.ajax({
			type: 'POST',
			url: ajax_dir+'pages_ajax.php',			
			data: { 
				action: action, token: token, pages_id: pages_id,
				date: date, calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, period: period
			},
			success: function(newdata){	
				$(".calendar-sidebar-events").empty().append(newdata);
			},
		});		
	});

	
	$(document).on("click", "td.calendar-event", function(event){
		event.preventDefault();		
		var action = "calendar_get_events";
		var token = $("#token").val();
		var catcal_id = $(this).attr('id');
		var cms_dir = $("#cms_dir").val();
		var ajax_dir = cms_dir +'/cms/';
		$('#ajax_spinner_events').show();
		
		mouseX = event.pageX; 
		mouseY = event.pageY;

		$( "#dialog_calendar" ).load(ajax_dir+'calendar_edit_ajax.php?token='+token+'&date='+catcal_id+'&action='+action).dialog({
			autoOpen: true,
			width: 400,
			modal: true,
			closeOnEscape: true,
			position: ({ my: "left top", at: "left bottom", of: event, collision: "fit" }),
			
			buttons: {
				"Set event": function() {
					var cal_event = $('textarea[id=cal_event]').val();
					var event_title = $("#event_title").val();
					var event_rss = $('input:checkbox[id=event_rss]').is(':checked') ? 1 : 0;
					var event_link = $("#event_link").val();
					
					$.ajax({
						beforeSend: function() { loading = $('#ajax_spinner_events').show()},
						complete: function(){ loading = setTimeout("$('#ajax_spinner_events').hide()",3000)},
						type: 'POST',
						url: ajax_dir+'calendar_edit_ajax.php',
						data: "action=calendar_set_events" + "&token=" + token + "&catcal_id=" + catcal_id + "&cal_event=" + cal_event + "&event_title=" + event_title + "&event_rss=" + event_rss + "&event_link=" + event_link,
						success: function(message){									
							$("#content_"+catcal_id).empty().append(nl2br(cal_event)).hide().fadeIn('fast');
							$("textarea#cal_event").empty();
						}
					});
					
					$( this ).dialog( "close" );
				
				},
				Cancel: function() {
					$("textarea#cal_event").empty();
					$( this ).dialog( "close" );
				},
								
				"Delete": function() {

					$.ajax({
						beforeSend: function() { loading = $('#ajax_spinner_events').show()},
						complete: function(){ loading = setTimeout("$('#ajax_spinner_events').hide()",1000)},
						type: 'POST',
						url: ajax_dir+'calendar_edit_ajax.php',
						data: "action=calendar_delete_events" + "&token=" + token + "&catcal_id=" + catcal_id,
						success: function(message){	
							$("#content_"+catcal_id).empty();
							$("textarea#cal_event").empty();
						}
					});								
					$( this ).dialog( "close" );
									
				},					
			},
			
			close: function() {
				$("textarea#cal_event").empty();
			}				
		});			
		
	});

	
	$(document).on("change", ".calendar_select", function(event){

		$('.ajax_calendar_load').show();
		var token = $("#token").val();
		var period = $("#period option:selected").val();
		var date = $("#init_date").val();
		var cms_dir = $("#cms_dir").val();
		var calendar_categories_id = $("#calendar_categories_id").val();
		var calendar_views_id = $("#calendar_views_id").val();
		var action = 'calendar_load';
		var pages_id = $("#pages_id").val();
		
		$.ajax({
			type: 'POST',
			url: cms_dir+'/cms/pages_ajax.php',			
			data: { 
				action: action, token: token, pages_id: pages_id,
				date: date, calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, period: period
			},
			success: function(newdata){	
				$("#calendar_include").empty().append(newdata);
				$('.ajax_calendar_load').hide();
				$("#init_date").val(date);
			},
		});		
		
	});		

	
	$( ".btn_previous_period button" ).button({
		icons: { primary: "ui-icon-carat-1-w" }, text: false
	});
	$( ".btn_next_period button" ).button({
		icons: { primary: "ui-icon-carat-1-e" }, text: false
	});

	$(".btn_today").click(function() {

		$('.ajax_calendar_load').show();
		
		var date = getToday();
		var period = $("#period option:selected").val();
		var token = $("#token").val();
		var cms_dir = $("#cms_dir").val();
		var calendar_categories_id = $("#calendar_categories_id").val();
		var calendar_views_id = $("#calendar_views_id").val();
		var action = 'calendar_load';
		var pages_id = $("#pages_id").val();
		
		$.ajax({
			type: 'POST',
			url: cms_dir+'/cms/pages_ajax.php',			
			data: { 
				action: action, token: token, pages_id: pages_id,
				date: date, calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, period: period
			},
			success: function(newdata){	
				$("#calendar_include").empty().append(newdata);
				$('.ajax_calendar_load').hide();
			},
		});		
		
	});	

	$(".btn_previous_period").click(function() {

		$('.ajax_calendar_load').show();
		
		var date = $("#init_date").val();
		var period = $("#period option:selected").val();
		
		switch(period) {
			case 'day':
				date = setNewDate(date,'d',-1);
			break;
			case '4days':
				date = setNewDate(date,'d',-4);
			break;
			case 'week':
				date = setNewDate(date,'d',-7);
			break;
			case '2weeks':
				date = setNewDate(date,'d',-14);
			break;
			case '4weeks':
				date = setNewDate(date,'d',-28);
			break;
			case '8weeks':
				date = setNewDate(date,'d',-56);
			break;
			case 'month':
				date = setNewDate(date,'m',-1);
			break;
			case '2months':
				date = setNewDate(date,'m',-2);
			break;
			case '4months':
				date = setNewDate(date,'m',-4);
			break;
			case '6months':
				date = setNewDate(date,'m',-6);
			break;
			
		}

		var token = $("#token").val();
		var cms_dir = $("#cms_dir").val();
		var calendar_categories_id = $("#calendar_categories_id").val();
		var calendar_views_id = $("#calendar_views_id").val();
		var action = 'calendar_load';
		var pages_id = $("#pages_id").val();
		
		$.ajax({
			type: 'POST',
			url: cms_dir+'/cms/pages_ajax.php',			
			data: { 
				action: action, token: token, pages_id: pages_id,
				date: date, calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, period: period
			},
			success: function(newdata){	
				$("#calendar_include").empty().append(newdata);
				$('.ajax_calendar_load').hide();
				$("#init_date").val(date);
			},
		});		
		
	});	

	$(".btn_next_period").click(function() {

		$('.ajax_calendar_load').show();
		
		var date = $("#init_date").val();
		var period = $("#period option:selected").val();
		
		switch(period) {
			case 'day':
				date = setNewDate(date,'d',1);
			break;
			case '4days':
				date = setNewDate(date,'d',4);
			break;
			case 'week':
				date = setNewDate(date,'d',7);
			break;
			case '2weeks':
				date = setNewDate(date,'d',14);
			break;
			case '4weeks':
				date = setNewDate(date,'d',28);
			break;
			case '8weeks':
				date = setNewDate(date,'d',56);
			break;
			case 'month':
				date = setNewDate(date,'m',1);
			break;
			case '2months':
				date = setNewDate(date,'m',2);
			break;
			case '4months':
				date = setNewDate(date,'m',4);
			break;
			case '6months':
				date = setNewDate(date,'m',6);
			break;
			
		}

		var token = $("#token").val();
		var cms_dir = $("#cms_dir").val();
		var calendar_categories_id = $("#calendar_categories_id").val();
		var calendar_views_id = $("#calendar_views_id").val();
		var action = 'calendar_load';
		var pages_id = $("#pages_id").val();
		
		$.ajax({
			type: 'POST',
			url: cms_dir+'/cms/pages_ajax.php',			
			data: { 
				action: action, token: token, pages_id: pages_id,
				date: date, calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, period: period
			},
			success: function(newdata){	
				$("#calendar_include").empty().append(newdata);
				$('.ajax_calendar_load').hide();
				$("#init_date").val(date);
			},
		});		
		
	});		
	
	
	$("#datepicker_events").datepicker({
		showWeek: true,
		firstDay: 1,
		showButtonPanel: true,
		onSelect: function(dateText) {
			$(this).change();
			}
		})
		.change(function() {

		$('.ajax_calendar_load').show();
		var token = $("#token").val();
		var period = $("#period option:selected").val();
		var date = this.value;
		var cms_dir = $("#cms_dir").val();
		var calendar_categories_id = $("#calendar_categories_id").val();
		var calendar_views_id = $("#calendar_views_id").val();
		var action = 'calendar_load';
		var pages_id = $("#pages_id").val();
		
		$.ajax({
			type: 'POST',
			url: cms_dir+'/cms/pages_ajax.php',			
			data: { 
				action: action, token: token, pages_id: pages_id,
				date: date, calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, period: period
			},
			success: function(newdata){	
				$("#calendar_include").empty().append(newdata);
				$('.ajax_calendar_load').hide();
				$("#init_date").val(date);
			},
		});		
		
	});		


});


function nl2br(str, is_xhtml) {
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}			

function getToday() {
	var d = new Date();
	var month = ('0'+(d.getMonth()+1)).slice(-2);
	var day = ('0'+d.getDate()).slice(-2);
	var year = d.getFullYear();
	return year+'-'+month+'-'+day;
}

function getParameterByName(name, qstring) {
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(qstring);
	if(results == null)
		return "";
	else
		return decodeURIComponent(results[1].replace(/\+/g, " "));
}