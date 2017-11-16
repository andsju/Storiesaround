<?php


////////////////////////////////////////////////////////////////////////////////////////////////////
// plugin
////////////////////////////////////////////////////////////////////////////////////////////////////

// prepare id
$plugin_bookitems_category_id = null;
// parse arguments
parse_str($plugin_arguments, $plugin_argument);
// check if we have a value
if($plugin_argument) {
	$plugin_bookitems_category_id = $plugin_argument['plugin_bookitems_category_id'];
}



if(!$plugin_bookitems_category_id) {
	die();
}


$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;

$bookitems = new Bookitems();

echo '<div id="calendar-bookitems">';
echo $bookitems->getBookitemsVertical($date=null, $href=null, $max_width=true, $plugin_bookitems_category_id, $period=null);
echo '</div>';
echo '<input type="hidden" name="token" id="token" value="'.$_SESSION['token'].'" />';
echo '<input type="hidden" name="users_id" id="users_id" value="'.$users_id.'" />';
echo '<input type="hidden" name="cms_dir" id="cms_dir" value="'.CMS_DIR.'" />';
echo '<input type="hidden" name="plugin_bookitems_category_id" id="plugin_bookitems_category_id" value="'.$plugin_bookitems_category_id.'" />';

?>

<script>

	
	$(document).ready(function() { 
		ready_load();
	});
	

	function ready_load() {
	
		$( "#datepicker_events" ).datepicker({
			showWeek: true,
			firstDay: 1
		});			


		$('button.calendar_mybookings').bind("click",function(event){ 
			event.preventDefault();
			var token = $("#token").val();
			var cms_dir = $("#cms_dir").val();
			var ajax_dir = cms_dir +'/content/';
			var dir = $("#cms_dir").val();
			
			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_unit_edit').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_unit_edit').hide()",700)},
				type: 'POST',
				url: dir+'/content/plugins/bookitems/admin_ajax.php',
				data: "action=cheek&token="+token,
				success: function(data){
				
					$('#units_list_bookings').html(data);

					$( "#dialog_bookitems_mybookings" ).dialog({
						modal: true,
						buttons: {
							Ok: function() {
								$( this ).dialog( "close" );
							}
						}
					});
				
				}
			});
			
		});		
		
	
		$('.calendar-bookitems-change-view').click(function(event){			
			event.preventDefault();
			var token = $("#token").val();
			var period = $("#period option:selected").val();
			var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
			// check if we have valid date in id
			var date = $(this).attr('id') ? $(this).attr('id') : '';
			if(date.length == 0) {
				// match date width regex
				var rgx = /(\d{4})-(\d{2})-(\d{2})/;
				date = $("#datepicker_events").val();
				// no match, get today
				if(!date.match(rgx)) {
					date = getToday();
				}
			}
			$('.ajax_calendar_load').show();
			var cms_dir = $("#cms_dir").val();
			var ajax_dir = cms_dir +'/content/plugins/bookitems/';
			var url = ajax_dir+'include_bookitems_load.php?token='+token+'&date='+date+'&plugin_bookitems_category_id='+plugin_bookitems_category_id+'&period='+period;
			$('#calendar-bookitems').load(url, function() {					
				ready_load();
			});				
		
		});

	
		$('a.bookitems').bind("click",function(event){ 
			event.preventDefault();
					
			var token = $("#token").val();
			var cms_dir = $("#cms_dir").val();
			var ajax_dir = cms_dir +'/content/';
			var dir = $("#cms_dir").val();
			var plugin_bookitems_id = $(this).attr('data-edit');
			var action = "action_get_enhet";
			$('.ui-dialog-buttonset button:contains("")').button('disable');
			
			if(plugin_bookitems_id != 0) {
				
				$('#units_list').html('<span class="toolbar"><button id="btn_units_show">Visa alternativ</button></span>').show();
				
				$.ajax({
					beforeSend: function() { loading = $('.ajax_spinner_unit_edit').show()},
					complete: function(){ loading = setTimeout("$('.ajax_spinner_unit_edit').hide()",700)},
					type: 'POST',
					url: dir +'/content/plugins/bookitems/admin_ajax.php',
					data: "action="+action+"&token="+token+"&plugin_bookitems_id="+plugin_bookitems_id,
					success: function(data){
						var data = JSON.parse( data );
						$('#title').val(data.title);
						$('#description').val(data.description);
						$('#utc_start').val(data.utc_start);
						
						var start = parseDateStringToDate(data.utc_start);
						var end = parseDateStringToDate(data.utc_end);
						$('#utc_start').val(start);
						$('#utc_end').val(end);
						
						var start_time = parseDateStringToTime(data.utc_start);
						var end_time = parseDateStringToTime(data.utc_end);						
						
						$('#utc_start_hours').val(start_time.substr(0,2));
						$('#utc_start_minutes').val(start_time.substr(3,2));
						$('#utc_end_hours').val(end_time.substr(0,2));
						$('#utc_end_minutes').val(end_time.substr(3,2));
						
					},
				});
			}
		
			$('.ajax_spinner_unit_edit').hide();
			$('.ajax_status_unit_edit').hide();
			

			var unit_id = $(this).attr('id');
			var date = $(this).closest('tr').attr('id');
			var title = $(this).attr('data-title');
			var image = $(this).attr('data-image');
			var description = $(this).attr('data-description');
			
			// set 'theunit_image'
			$('#theunit_image').attr('src',cms_dir+'/content/uploads/plugins/bookitems/'+image);
			
			// set 'theunit_title'
			$('#theunit_title').text(title);
			$('#theunit_description').text(description);
			
			// set utc_start & utc_end 
			$('#utc_start').val(date);
			$('#utc_end').val(date);
			
			$( "#dialog_bookitems" ).dialog({
				
				autoOpen: true,
				width: 400,
				height: 600,
				modal: true,
				draggable: true,
				resizable: true,				
				closeOnEscape: true,
				buttons: 
				
					{
					
					"Boka": function() {
						var bValid = true;

						if ( bValid ) {
							
							var description = $('textarea[id=description]').val();
							var title = $("#title").val();
							var dir = $("#cms_dir").val();
							
							var utc_start = $("#utc_start").val();
							var utc_start_hours = $('#utc_start_hours').val() ? $('#utc_start_hours').val() : '00';
							var utc_start_minutes = $('#utc_start_minutes').val() ? $('#utc_start_minutes').val() : '00';
							var datetime_start = (utc_start=='') ? '' : utc_start  +' '+  utc_start_hours +':'+ utc_start_minutes +':00';
							
							var utc_end = $("#utc_end").val();
							var utc_end_hours = $('#utc_end_hours').val() ? $('#utc_end_hours').val() : '00';
							var utc_end_minutes = $('#utc_end_minutes').val() ? $('#utc_end_minutes').val() : '00';
							var datetime_end = (utc_end=='') ? '' : utc_end  +' '+  utc_end_hours +':'+ utc_end_minutes +':00';
							
							var repeat = $('input:checkbox[name=repeat]').is(':checked') ? 1 : 0;							
							var interval = $("#interval").val();
							var occations = $("#occations").val();
							var dates_include = $("#dates_include").val();
							var dates_exclude = $("#dates_exclude").val();
							
							// if unit change - use this unit_id (default 0)
							var theunit_switch_id = $('#theunit_switch_id').val();
							
							if (title.length > 0) {
								$.ajax({
									beforeSend: function() { loading = $('.ajax_spinner_unit_edit').show()},
									complete: function(){ loading = setTimeout("$('.ajax_spinner_unit_edit').hide()",3000)},
									type: 'POST',
									url: dir +'/content/plugins/bookitems/admin_ajax.php',
									data: "action=action_unit_booking" + "&token=" + token +"&datetime_start=" + datetime_start +"&datetime_end=" + datetime_end +"&title=" + title +"&description=" + description +"&unit_id=" + unit_id  +"&theunit_switch_id=" + theunit_switch_id +"&plugin_bookitems_id=" + plugin_bookitems_id +"&repeat=" + repeat +"&interval=" + interval +"&occations=" + occations +"&dates_exclude=" + dates_exclude +"&dates_include=" + dates_include,
									success: function(response){	
										ajaxReplyUser(response,'.ajax_status_unit_edit');
										
									}
								});
							}
							
						}
					},
					"Avbryt": function() {						
						$( this ).dialog( "close" );
					},
					
					
					"Radera": function() {
						var bValid = true;
						if ( bValid ) {
							
							$.ajax({
								beforeSend: function() { loading = $('.ajax_spinner_unit_edit').show()},
								complete: function(){ loading = setTimeout("$('.ajax_spinner_unit_edit').hide()",1000)},
								type: 'POST',
								url: dir +'/content/plugins/bookitems/admin_ajax.php',
								data: "action=action_unit_booking_delete" + "&token=" + token + "&plugin_bookitems_id=" + plugin_bookitems_id,
								success: function(message){	
								}
							});								
							$( this ).dialog( "close" );
						}					
					},					
				},
				
								
				close: function() {
					//if(confirm('Ladda om sidan?')){
						//location.reload(true);

						$('textarea[id=description]').val('');
						$("#title").val('');
						$("#units_list").empty();
						
  						
						event.preventDefault();
						var token = $("#token").val();
						var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
						var period = $("#period option:selected").val();
						// check if we have valid date in id
						var date = $(this).attr('id') ? $(this).attr('id') : '';
						var date = $("#utc_start").val();
						if(date.length == 0) {
							// match date width regex
							var rgx = /(\d{4})-(\d{2})-(\d{2})/;
							date = $("#datepicker_events").val();
							// no match, get today
							if(!date.match(rgx)) {
								date = getToday();
							}
						}
						$('.ajax_calendar_load').show();
						var cms_dir = $("#cms_dir").val();
						var ajax_dir = cms_dir +'/content/plugins/bookitems/';
						var url = ajax_dir+'include_bookitems_load.php?token='+token+'&date='+date+'&period='+period+'&plugin_bookitems_category_id='+plugin_bookitems_category_id;
						$('#calendar-bookitems').load(url, function() {							
							ready_load();
						});
						
					//}				
				}
				
			});			
		});
		

		$("div").delegate("#btn_units_show","click",function(event){
			event.preventDefault();
			var action = "action_units_show";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
			var dir = $("#cms_dir").val();

			var utc_start = $("#utc_start").val();
			var utc_start_hours = $('#utc_start_hours').val() ? $('#utc_start_hours').val() : '00';
			var utc_start_minutes = $('#utc_start_minutes').val() ? $('#utc_start_minutes').val() : '00';
			var datetime_start = (utc_start=='') ? '' : utc_start  +' '+  utc_start_hours +':'+ utc_start_minutes +':00';
			
			var utc_end = $("#utc_end").val();
			var utc_end_hours = $('#utc_end_hours').val() ? $('#utc_end_hours').val() : '00';
			var utc_end_minutes = $('#utc_end_minutes').val() ? $('#utc_end_minutes').val() : '00';
			var datetime_end = (utc_end=='') ? '' : utc_end  +' '+  utc_end_hours +':'+ utc_end_minutes +':00';

			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_unit_edit').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_unit_edit').hide()",700)},
				type: 'POST',
				url: dir +'/content/plugins/bookitems/admin_ajax.php',
				data: "action="+action+"&token="+token+"&users_id="+users_id+"&plugin_bookitems_category_id="+plugin_bookitems_category_id+"&datetime_start="+datetime_start+"&datetime_end="+datetime_end,
				success: function(newdata){
					ajaxReply('','.ajax_status_unit_edit');
					$('#units_list').html(newdata);
				},
			});
		});
		
		
		$("#utc_start").datepicker({
			showWeek: true,
			firstDay: 1,
			onClose: function( selectedDate ) {
				$("#utc_end").datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#utc_end" ).datepicker({
			showWeek: true,
			firstDay: 1,
			onClose: function( selectedDate ) {
				$("#utc_start").datepicker( "option", "maxDate", selectedDate );
			}			
		});
		
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});

		
		$('input[name="repeat"]').click(function(){
			$('.repeat_form').toggle();
		});
		
		$("div").delegate(".btn_units_choose","click",function(event){
			event.preventDefault();
			
			var action = "action_unit_choose";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			
			var id = $(this).attr("id");
			var image = $(this).attr("data-imagesrc");
			var title = $(this).attr("data-title");
			var description = $(this).attr("data-description");
			
			$('#theunit_switch_id').val(id);
			$('#theunit_image').attr('src',image);
			$('#theunit_title').text(title);
			$('#theunit_description').text(description);
			//$(this).closest('li').remove();
		});

		
	};	

	
	function getToday() {
		var d = new Date();
		var month = ('0'+(d.getMonth()+1)).slice(-2);
		var day = ('0'+d.getDate()).slice(-2);
		var year = d.getFullYear();
		return year+'-'+month+'-'+day;
	}

	function nl2br(str, is_xhtml) {
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	}
	
	// parse a date in YYYY-mm-dd H:i:s format to YYYY-mm-dd
	function parseDateStringToDate(input) {
		var parts = input.split('-');
		return parts[0]+'-'+parts[1]+'-'+parts[2].substr(0,2);
	}	

	// parse a date in YYYY-mm-dd H:i:s format to H:i:s
	function parseDateStringToTime(input) {
		var parts = input.split('-');
		var timeparts = parts[2].split(':');
		return timeparts[0].substr(3,5)+':'+timeparts[1]+':'+timeparts[2];
	}	
	
</script>





<div id="dialog_bookitems" style="display:none;">
<input type="hidden" name="theunit_switch_id" id="theunit_switch_id" />
	<?php


	?>
	<table style="" class="units">
		<tr>
			<td><h3>Bokning</h3></td>
			<td style="float:right;">
			<span id="ajax_log"></span>		
			<span class="ajax_spinner_unit_edit"><img src="<?php echo CMS_DIR; ?>/content/plugins/bookitems/css/images/spinner.gif"></span>
			<span class="ajax_status_unit_edit"></span>
			
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;" colspan="2">
			<div id="units_list" style="display:none;"></div>

			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;" colspan="2">
			<div class="units">

				<img src="" id="theunit_image" style="float:right;padding:0 0 5px 5px;" />
				<h5 id="theunit_title"></h5>
				<p style="font-size:0.9em;" id="theunit_description"></p>

			</div>
			</td>
		</tr>
		<tr>
			<td>
				<table class="utc">
					<tr>
						<td><label for="utc_start">Start</label></td>
						<td><label for="utc_start_hours">kl</label></td>
					</tr>
					<tr>
						<td><input type="text" id="utc_start" maxlength="10" style="width:80px;"></td>
						<td>
						<select id="utc_start_hours">
							<?php
							$h1 = date('G');
							$h2 = $h1+1;
							$m = date('i');			
							for($i=0;$i<=23;$i++) {			
								$s = ($i == $h1) ? ' selected' : '';
								echo '<option value="'.sprintf("%02d",$i).'" '.$s.'>'.sprintf("%02d",$i).'</option>';
							}
							?>
						</select>		
						<select id="utc_start_minutes">
							<?php
							for($i=0;$i<=55;$i+=5) {
								$s = ($i > $m && $i-5 < $m ) ? ' selected' : '';						
								echo '<option value="'.sprintf("%02d",$i).'" '.$s.'>'.sprintf("%02d",$i).'</option>';
							}
							?>
						</select>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table class="utc">
					<tr>
						<td><label for="utc_end">Slut</label></td>
						<td><label for="utc_end_hours">kl</label></td>
					</tr>
					<tr>
						<td><input type="text" id="utc_end" maxlength="10" style="width:80px;"></td>
						<td>
						<select id="utc_end_hours">
							<?php
							for($i=0;$i<=23;$i++) {			
								$s = ($i == $h2) ? ' selected' : '';
								echo '<option value="'.sprintf("%02d",$i).'" '.$s.'>'.sprintf("%02d",$i).'</option>';
							}
							?>
						</select>		
						<select id="utc_end_minutes">
							<?php
							for($i=0;$i<=55;$i+=5) {
								$s = ($i > $m && $i-5 < $m ) ? ' selected' : '';						
								echo '<option value="'.sprintf("%02d",$i).'" '.$s.'>'.sprintf("%02d",$i).'</option>';
							}
							?>
						</select>
						</td>
					</tr>
				</table>		
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;" colspan="2">
			<label for="title">Rubrik</label><br />
			<input type="title" id="title" name="title" style="width:100%;" autofocus />
			<label for="description">Beskrivning</label><br />
			<textarea id="description" name="description" style="width:100%;height:30px;"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<input type="checkbox" name="repeat" id="repeat" value="1" /> Upprepa bokning
			<br />
			<div class="repeat_form" style="display:none;">
				<table style="width:100%;">
					<tr>
						<td colspan="2">
						</td>
					</tr>
					<tr>
						<td style="width:20px;"></td>
						<td>
						<select name="interval" id="interval">
						<?php
						for($i=1;$i<=10;$i++) {
							echo '<option value="'.$i.'">'.$i.'</option>';
						}
						?>
						</select> dagars intervall
						</td>
					</tr>
					<tr>
						<td style="width:20px;"></td>
						<td>
						<select name="occations" id="occations">
						<?php
						for($i=1;$i<=10;$i++) {
							echo '<option value="'.$i.'">'.$i.'</option>';
						}
						?>
						</select> tillfällen
						</td>
					</tr>
					<tr>
						<td style="width:20px;"></td>
						<td>
						<label for="dates_exclude">Exkludera startdatum</label><br />
						<input type="text" style="width:100%" id="dates_exclude" name="dates_exclude" />
						</td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
	</table>


</div>


<div id="dialog_bookitems_mybookings" style="display:none;">
	<table style="" class="units">
		<tr>
			<td><h4>Mina aktuella bokningar</h4></td>
			<td style="float:right;">
			<span id="ajax_log"></span>		
			<span class="ajax_spinner_unit_edit" style='display:none'><img src="<?php echo CMS_DIR; ?>/content/plugins/bookitems/css/images/spinner.gif"></span>
			<span class="ajax_status_unit_edit"></span>		
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;" colspan="2">
			<div id="units_list_bookings" style="max-height:400px;"></div>
			</td>
		</tr>
	</table>
</div>