<?php

// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';

include_once CMS_ABSPATH . '/cms/includes/inc.functions_pages.php';


// include session access 
//--------------------------------------------------
require_once CMS_ABSPATH . '/cms/includes/inc.session_access.php';


// access right, minimum, hierarchy matters
//--------------------------------------------------
if(!get_role_CMS('administrator') == 1) {
	if(!get_role_LMS('administrator') == 1) {
		header('Location: '. $_SESSION['site_domain_url']);	exit;
	}
}


// css files, loaded in inc.header.php 
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/content/plugins/kursbokning/css/style.css' );

//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js', 
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',
	CMS_DIR.'/content/plugins/kursbokning/js/jquery.autosize.js' );

?>
<!DOCTYPE html>
<html lang="sv">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Administration - plugin Kursbokning</title>

	<?php 
	//load css files
	foreach ( $css_files as $css ) {
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	}; 
	echo "\n";
	echo "\n\t".'<script src="'.CMS_DIR.'/cms/libraries/jquery/jquery.min.js"></script>';
	echo "\n\t".'<script src="https://www.google.com/jsapi"></script>';
	echo "\n";
	?>
	
</head>

<body style="width:1200px;background:#fafafa;">


<script>

	// get querystring paramater 
	function getParameterByName(name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(location.search);
		return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

	function addParameter(url, parameterName, parameterValue){
		replaceDuplicates = true;
		if(url.indexOf('#') > 0){
			var cl = url.indexOf('#');
			urlhash = url.substring(url.indexOf('#'),url.length);
		} else {
			urlhash = '';
			cl = url.length;
		}
		sourceUrl = url.substring(0,cl);

		var urlParts = sourceUrl.split("?");
		var newQueryString = "";

		if (urlParts.length > 1) {
			var parameters = urlParts[1].split("&");
			for (var i=0; (i < parameters.length); i++) {
				var parameterParts = parameters[i].split("=");
				if (!(replaceDuplicates && parameterParts[0] == parameterName)) {
					if (newQueryString == "")
						newQueryString = "?";
					else
						newQueryString += "&";
						newQueryString += parameterParts[0] + "=" + (parameterParts[1]?parameterParts[1]:'');
				}
			}
		}
		if (newQueryString == "")
			newQueryString = "?";

			if (newQueryString !== "" && newQueryString != '?')
				newQueryString += "&";
			newQueryString += parameterName + "=" + (parameterValue?parameterValue:'');

		return urlParts[0] + newQueryString + urlhash;
	}
	
	function removeParameter(sourceURL, key) {
		var rtn = sourceURL.split("?")[0],
			param,
			params_arr = [],
			queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
		if (queryString !== "") {
			params_arr = queryString.split("&");
			for (var i = params_arr.length - 1; i >= 0; i -= 1) {
				param = params_arr[i].split("=")[0];
				if (param === key) {
					params_arr.splice(i, 1);
				}
			}
			rtn = rtn + "?" + params_arr.join("&");
		}
		return rtn;
	}
	
	function generateexcel(tableid) {
		var table= document.getElementById(tableid);
		var html = table.outerHTML;
		window.open('data:application/vnd.ms-excel;base64,' + $.base64.encode(html));
	}
	
	var tableToExcel = (function() {
	  var uri = 'data:application/vnd.ms-excel;base64,'
		, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
		, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
		, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
	  return function(table, name) {
		if (!table.nodeType) table = document.getElementById(table)
		var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
		window.location.href = uri + base64(format(template, ctx))
	  }
	})()	
	
	
	function preview(id) {
		w=window.open('kursbokning_preview.php?id='+id,'','width=1024,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}

	function kursbokning(id) {
		w=window.open('kursbokning.php?id='+id,'','width=1024,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}

	function rapport(id) {
		w=window.open('export.php?plugin_kursbokning_kurs_id='+id,'','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}
	
	(function($) {
		$.fn.toggleDisabled = function(){
			return this.each(function(){
				this.disabled = !this.disabled;
			});
		};
	})(jQuery);
	
	
	$(document).ready(function() {
	
		$("#tabs_edit").tabs({
		});
		
		$("#tabs_edit").show(); 

		$("#tabs_edit2").tabs({
		});
		
		$("#tabs_edit2").show(); 

		$('textarea').autosize(); 
	
		var plugin_kursbokning_id = getParameterByName('plugin_kursbokning_id');

		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});

		$( ".toolbar2 button" ).button({
			icons: {
				primary: "ui-icon-locked"
			},
			text: true
		});

		$( ".toolbar_edit button" ).button({
			icons: {
				primary: "ui-icon-grip-dotted-vertical",
				secondary: "ui-icon-pencil"
			},
			text: true
		});

		$('.table_js').dataTable({
			"iDisplayLength": 25,

			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],			
			"sPaginationType": "full_numbers",
			"aaSorting": [[ 4, "desc" ]]
		});
		
		
		
		$.datepicker.setDefaults($.datepicker.regional['sv']);


		$("#date_start").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -2, maxDate: '+1M +10D',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#date_end").datepicker({
			showWeek: true, firstDay: 1,
			minDate: 0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});
		
		$("#utc_date_start").datepicker({
			showWeek: true, firstDay: 1,
			minDate: '-3M', maxDate: '+1Y',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#utc_date_end").datepicker({
			showWeek: true, firstDay: 1,
			minDate: '-3M', maxDate: '+1Y',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#utc_created").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -30,
			maxDate: +0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#utc_admitted").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -10,
			maxDate: +0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#utc_confirmed").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -7,
			maxDate: +0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#utc_canceled").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -7,
			maxDate: +0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});

		$("#utc_date_1").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -720,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});
		
		$("#utc_date_2").datepicker({
			showWeek: true, firstDay: 1,
			maxDate: 0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});
		
		
		
		$('#btn_form_add').click(function(event){
			event.preventDefault();
			var action = "action_form_add";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var title = $("#title").val();
			var type = $("#type").val();
			var activeTabIndex = $( "#tabs_edit" ).tabs( "option", "active" );

			if(title.length && type.length) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_add_form').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_add_form').hide()",700)},
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&type="+type,
					success: function(newdata){
						ajaxReply('','#ajax_status_add_form');
						$('<a href=admin.php?t=form_edit&plugin_kursbokning_id=' +newdata+'> formulär skapat &raquo; redigera <b>'+title+'</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').hide().fadeIn('fast').insertAfter("#ajax_status_add_form");
						$('#title').val('');
					},
				});
			}
		});
			
		$('.btn_form_save').click(function(event){
			event.preventDefault();
			var action = "action_form_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_id = $("#plugin_kursbokning_id").val();
			var title = $("#title").val();
			var terms = $("#terms").val();
			var status = $('input:checkbox[name=status]').is(':checked') ? 1 : 0;
			var web_reservation = $('input:checkbox[name=web_reservation]').is(':checked') ? 1 : 0;
			var email_notify = $("#email_notify").val();
			var type = $("#type").val();
			var adress = $('input:checkbox[name=adress]').is(':checked') ? 1 : 0;
			adress = (adress) ? 'adress:'+$("#adress_option").val()+',' : '';
			var postnummer = $('input:checkbox[name=postnummer]').is(':checked') ? 1 : 0;
			postnummer = (postnummer) ? 'postnummer:'+$("#postnummer_option").val()+',' : '';
			var ort = $('input:checkbox[name=ort]').is(':checked') ? 1 : 0;
			ort = (ort) ? 'ort:'+$("#ort_option").val()+',' : '';
			var mobil = $('input:checkbox[name=mobil]').is(':checked') ? 1 : 0;
			mobil = (mobil) ? 'mobil:'+$("#mobil_option").val()+',' : '';
			var telefon = $('input:checkbox[name=telefon]').is(':checked') ? 1 : 0;
			telefon = (telefon) ? 'telefon:'+$("#telefon_option").val()+',' : '';
			var personnummer = $('input:checkbox[name=personnummer]').is(':checked') ? 1 : 0;
			personnummer = (personnummer) ? 'personnummer:'+$("#personnummer_option").val()+',' : '';
			var lan = $('input:checkbox[name=lan]').is(':checked') ? 1 : 0;
			lan = (lan) ? 'lan:'+$("#lan_option").val()+',' : '';
			var kommun = $('input:checkbox[name=kommun]').is(':checked') ? 1 : 0;
			kommun = (kommun) ? 'kommun:'+$("#kommun_option").val()+',' : '';
			var country = $('input:checkbox[name=country]').is(':checked') ? 1 : 0;
			country = (country) ? 'country:'+$("#country_option").val()+',' : '';
			var organisation = $('input:checkbox[name=organisation]').is(':checked') ? 1 : 0;
			organisation = (organisation) ? 'organisation:'+$("#organisation_option").val()+',' : '';
			var fakturaadress = $('input:checkbox[name=fakturaadress]').is(':checked') ? 1 : 0;
			fakturaadress = (fakturaadress) ? 'fakturaadress:'+$("#fakturaadress_option").val()+',' : '';
			var date_start = $("#date_start").val() ? $("#date_start").val() : null;
			var time_start = $("#time_start").val() ? $("#time_start").val() +':00' : '00:00:00';
			var datetime_start = (date_start==null) ?  null : date_start +' '+ time_start;
			var date_end = $("#date_end").val() ? $("#date_end").val() : null;
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '00:00:00';
			var datetime_end = (date_end==null) ? null : date_end +' '+ time_end;
			var file_upload = $('input:checkbox[name=file_upload]').is(':checked') ? 1 : 0;
			var file_instruction = $("#file_instruction").val();
			var file_extensions = $("#file_extensions").val();
			var file_path = $("#file_path").val();
			var file_attach = $('input:checkbox[name=file_attach]').is(':checked') ? 1 : 0;
			var file_action = $('input:radio[name=file_action]:checked').val();
			var file_move_path = $("#file_move_path").val();
			var fields = adress + postnummer + ort + mobil + telefon + personnummer + lan + kommun + country + organisation + fakturaadress;
		
			//console.log(fields);
			fields = fields.replace(/,(?=[^,]*$)/, '');
				
			var items = {  
				action: action,
				token: token,
				users_id: users_id,
				plugin_kursbokning_id: plugin_kursbokning_id,
				title: title,
				terms: terms,
				status: status,
				web_reservation: web_reservation,
				email_notify: email_notify,
				type: type,
				datetime_start: datetime_start,
				datetime_end: datetime_end,
				file_upload: file_upload,
				file_instruction: file_instruction,
				file_extensions: file_extensions,
				file_path: file_path,
				file_attach: file_attach,
				file_action: file_action,
				file_move_path: file_move_path,
				fields: fields
			}; 
			
			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_form_edit').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_form_edit').hide()",700)},
				type: 'POST',
				url: 'admin_ajax.php',
				data: items,
				success: function(newdata){
					ajaxReply('','.ajax_status_form_edit');
				},
			});
		});


		$('body').delegate('.btn_form_course_save', "click", function () {
			$('.btn_form_course_save').click(function(event){
				event.preventDefault();
				var action = "action_form_course_save";
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var plugin_kursbokning_id = $("#plugin_kursbokning_id").val();
				var plugin_kursbokning_kurs_id = $(this).parent().attr("id");
				var el = $(this);
				$.ajax({
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&plugin_kursbokning_id="+plugin_kursbokning_id+"&plugin_kursbokning_kurs_id="+plugin_kursbokning_kurs_id,
					success: function(newdata){
						ajaxReply('','.ajax_status_form_edit');
						$('#title-'+plugin_kursbokning_kurs_id).text('ok');
						el.remove();
					},
				});
			});
		});

		
		$('.btn_form_course_save').click(function(event){
			event.preventDefault();
			var action = "action_form_course_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_id = $("#plugin_kursbokning_id").val();
			var plugin_kursbokning_kurs_id = $(this).parent().attr("id");
			var el = $(this);
			$.ajax({
				type: 'POST',
				url: 'admin_ajax.php',
				data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&plugin_kursbokning_id="+plugin_kursbokning_id+"&plugin_kursbokning_kurs_id="+plugin_kursbokning_kurs_id,
				success: function(newdata){
					ajaxReply('','.ajax_status_form_edit');
					$('#title-'+plugin_kursbokning_kurs_id).text('ok');
					el.remove();
				},
			});
		});


		$('body').delegate('.btn_form_course_remove', "click", function () {
			$('.btn_form_course_remove').click(function(event){
				event.preventDefault();
				var action = "action_form_course_save";
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var plugin_kursbokning_id = 0;
				var plugin_kursbokning_kurs_id = $(this).parent().attr("id");
				var el = $(this);
				$.ajax({
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&plugin_kursbokning_id="+plugin_kursbokning_id+"&plugin_kursbokning_kurs_id="+plugin_kursbokning_kurs_id,
					success: function(newdata){
						ajaxReply('','.ajax_status_form_edit');
						$('#title-'+plugin_kursbokning_kurs_id).text('');
						el.remove();
					},
				});			
			});
		});
		
		$('.btn_form_course_remove').click(function(event){
			event.preventDefault();
			var action = "action_form_course_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_id = 0;
			var plugin_kursbokning_kurs_id = $(this).parent().attr("id");
			var el = $(this);
			//console.log(el);
			$.ajax({
				type: 'POST',
				url: 'admin_ajax.php',
				data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&plugin_kursbokning_id="+plugin_kursbokning_id+"&plugin_kursbokning_kurs_id="+plugin_kursbokning_kurs_id,
				success: function(newdata){
					ajaxReply('','.ajax_status_form_edit');
					$('#title-'+plugin_kursbokning_kurs_id).text('');
					el.remove();
				},
			});			
		});
		

		$('#btn_form_delete').click(function(event){
			event.preventDefault();
			var action = "action_form_delete";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_id = $("#plugin_kursbokning_id").val();
			
			$.ajax({
				type: 'POST',
				url: 'admin_ajax.php',
				data: $("#custom_form").serialize() +"&action="+action+"&token="+token+"&users_id="+users_id+"&plugin_kursbokning_id="+plugin_kursbokning_id,
				success: function(newdata){				
					var url = window.location.toString();					
					url = removeParameter(url,'form_edit');
					url = addParameter(url,'t','form_find');
					document.location = url;
				},
			});
		});

		$('#btn_course_add').click(function(event){
			event.preventDefault();
			var action = "action_course_add";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var title = $("#title").val();
			var type = $("#type").val();
			var activeTabIndex = $( "#tabs_edit" ).tabs( "option", "active" );
			if(title.length && type.length) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_course_add').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_course_add').hide()",700)},			
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&type="+type,
					success: function(newdata){
						ajaxReply('','#ajax_status_course_add');
						$('<a href=admin.php?t=course_edit&plugin_kursbokning_kurs_id=' +newdata+'> kurs skapad &raquo; redigera <b>'+title+'</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').hide().fadeIn('fast').insertAfter("#ajax_status_course_add");
						$('#title').val('');
					},
				});
			} else {
				$('#ajax_status_add_course').html('Kurstitel och kurstyp är obligatoriska fält').show();
			}
		});

		$('#btn_course_save').click(function(event){
			event.preventDefault();
			var action = "action_course_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_kurs_id = $("#plugin_kursbokning_kurs_id").val();
			var title = $("#title").val();
			var description = $("#description").val();
			var type = $("#type").val();
			var cost = $("#cost").val();
			var location = $("#location").val();
			var participants = $("#participants").val();
			var status = $('input:checkbox[name=status]').is(':checked') ? 1 : 0;
			var web_reservation = $('input:checkbox[name=web_reservation]').is(':checked') ? 1 : 0;		
			var show_participants = $('input:checkbox[name=show_participants]').is(':checked') ? 1 : 0;		
			var notify = $("#notify").val();
			var date_start = $("#utc_date_start").val() ? $("#utc_date_start").val() : null;
			var time_start = $("#time_start").val() ? $("#time_start").val() +':00' : '00:00:00';
			var datetime_start = (date_start==null) ?  null : date_start +' '+ time_start;
			var date_end = $("#utc_date_end").val() ? $("#utc_date_end").val() : null;
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '00:00:00';
			var datetime_end = (date_end==null) ? null : date_end +' '+ time_end;
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_edit').hide()",700)},			
				type: 'POST',
				url: 'admin_ajax.php',
				data: "&action="+action+"&token="+token+"&users_id="+users_id+"&plugin_kursbokning_kurs_id="+plugin_kursbokning_kurs_id+"&title="+title+"&description="+description+"&type="+type+"&status="+status+"&cost="+cost+"&location="+location+"&participants="+participants+"&show_participants="+show_participants+"&datetime_start="+datetime_start+"&datetime_end="+datetime_end+"&web_reservation="+web_reservation+"&notify="+notify,
				success: function(newdata){
					ajaxReply('','#ajax_status_course_edit');
				},
			});
		});

		
		$('.btn_course_delete').click(function(event){
			event.preventDefault();
			var action = "action_course_delete";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_kurs_id = $(this).attr("id");

			var items = {  
				action: action,
				token: token,
				users_id: users_id,
				plugin_kursbokning_kurs_id: plugin_kursbokning_kurs_id
			}; 
			
			$.ajax({
				type: 'POST',
				url: 'admin_ajax.php',
				data: items,
				success: function(newdata){
					var url = window.location.toString();						
					url = removeParameter(url,'t','course_edit');
					url = addParameter(url,'t','course_find');
					document.location = url;
				},
			});
		});
		


		$("#datatable_courses").on("click", ".link_course_reservation_add", function(event) {
			event.preventDefault();
			var action = "action_course_reservation_add";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_kurser_id = this.id;
			var fnamn = 'Förnamn';
			var enamn = 'Efternamn';
			if(fnamn.length && enamn.length) {
				$.ajax({
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&plugin_kursbokning_kurser_id="+plugin_kursbokning_kurser_id+"&fnamn="+fnamn+"&enamn="+enamn,
					success: function(newdata){
						var url = window.location.toString();						
						url = addParameter(url,'t','reservation_edit');
						url = addParameter(url,'plugin_kursbokning_kurs_anmalan_id',newdata);
						document.location = url;
					},
				});
			}
		});
		
		
		$('#btn_course_reservation_save').click(function(event){
			event.preventDefault();
			var action = "action_course_reservation_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_kurs_anmalan_id = $("#plugin_kursbokning_kurs_anmalan_id").val();			
			var fnamn = $("#fnamn").val();
			var enamn = $("#enamn").val();
			var personnummer = $("#personnummer").val();
			var epost = $("#epost").val();
			var mobil = $("#mobil").val();
			var telefon = $("#telefon").val();
			var adress = $("#adress").val();
			var postnummer = $("#postnummer").val();
			var ort = $("#ort").val();
			var lan = $("#lan").val();
			var kommun = $("#kommun").val();
			var country = $("#country").val();
			var organisation = $("#organisation").val();
			var fakturaadress = $("#fakturaadress").val();
			var notes = $("#notes").val();
			var log = $("#log").val();
			var utc_admitted = $("#utc_admitted").val() ? $("#utc_admitted").val() : null;
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '00:00:00';
			var datetime_utc_admitted = (utc_admitted==null) ? null : utc_admitted +' '+ time_end;
			var utc_confirmed = $("#utc_confirmed").val() ? $("#utc_confirmed").val() : null;
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '00:00:00';
			var datetime_utc_confirmed = (utc_confirmed==null) ? null : utc_confirmed +' '+ time_end;
			var utc_canceled = $("#utc_canceled").val() ? $("#utc_canceled").val() : null;
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '00:00:00';
			var datetime_utc_canceled = (utc_canceled==null) ? null : utc_canceled +' '+ time_end;
		
			var items = {  
				action: action,
				token: token,
				users_id: users_id,
				plugin_kursbokning_kurs_anmalan_id: plugin_kursbokning_kurs_anmalan_id,
				fnamn: fnamn,
				enamn: enamn,
				personnummer: personnummer,
				epost: epost,
				mobil: mobil,
				telefon: telefon,
				adress: adress,
				postnummer: postnummer,
				ort: ort,
				lan: lan,
				kommun: kommun,
				country: country,
				organisation: organisation,
				fakturaadress: fakturaadress,
				notes: notes,
				log: log,
				datetime_utc_admitted: datetime_utc_admitted,
				datetime_utc_confirmed: datetime_utc_confirmed,
				datetime_utc_canceled: datetime_utc_canceled
			}; 
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_reservation_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_reservation_edit').hide()",700)},			
				type: 'POST',
				url: 'admin_ajax.php',
				data: items,
				success: function(newdata){
					ajaxReply('','#ajax_status_course_reservation_edit');
				},
			});
		});
				

		$('#btn_course_reservation_change_course_save').click(function(event){
			event.preventDefault();
			var action = "action_course_reservation_change_course_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var log = $("#log").val();
			var plugin_kursbokning_kurs_anmalan_id = $("#plugin_kursbokning_kurs_anmalan_id").val();
			var plugin_kursbokning_kurs_id = $("#change_course").val();
		
			var items = {  
				action: action,
				token: token,
				users_id: users_id,
				log: log,
				plugin_kursbokning_kurs_anmalan_id: plugin_kursbokning_kurs_anmalan_id,
				plugin_kursbokning_kurs_id: plugin_kursbokning_kurs_id
			}; 
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_reservation_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_reservation_edit').hide()",700)},			
				type: 'POST',
				url: 'admin_ajax.php',
				data: items,
				success: function(newdata){
					ajaxReply('','#ajax_status_course_reservation_edit');
				},
			});
		});



		$('#btn_course_confirm_email').click(function(event){
			event.preventDefault();
			var action = "course_confirm";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_kurs_anmalan_id = $("#plugin_kursbokning_kurs_anmalan_id").val();
		
			var items = {  
				action: action,
				token: token,
				users_id: users_id,
				plugin_kursbokning_kurs_anmalan_id: plugin_kursbokning_kurs_anmalan_id
			}; 
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_confirm').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_confirm').hide()",700)},			
				type: 'POST',
				url: 'kursbokning_ajax.php',
				data: items,
				success: function(newdata){
					ajaxReply(newdata,'#ajax_status_course_confirm');
				},
			});
		});		
		
		
		

		$('#btn_course_reservation_delete').click(function(event){
			event.preventDefault();
			var action = "action_course_reservation_delete";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_kurs_anmalan_id = $("#plugin_kursbokning_kurs_anmalan_id").val();
		
			var items = {  
				action: action,
				token: token,
				users_id: users_id,
				plugin_kursbokning_kurs_anmalan_id: plugin_kursbokning_kurs_anmalan_id
			}; 
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_reservation_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_reservation_edit').hide()",700)},
				type: 'POST',
				url: 'admin_ajax.php',
				data: items,
				success: function(newdata){
					window.location.href = window.location.toString();
					location.reload(true);
				},
			});
		});
				


		$('#btn_course_reservation_select').click(function(event){
			event.preventDefault();			
			var url = window.location.toString();
			var d1 = $("#utc_date_1").val();
			var d2 = $("#utc_date_2").val();
			var ca = $('input:checkbox[name=chk_utc_canceled]').is(':checked') ? 1 : 0;
			var url = addParameter(url,'d1',d1);
			url = addParameter(url,'d2',d2);
			url = removeParameter(url,'ch');
			url = addParameter(url,'ca',ca);
			document.location = url;
		});
				
		$('#btn_course_reservation_select_admitted').click(function(event){
			event.preventDefault();			
			var url = window.location.toString();
			var d1 = $("#utc_date_1").val();
			var d2 = $("#utc_date_2").val();
			var ca = $('input:checkbox[name=chk_utc_canceled]').is(':checked') ? 1 : 0;
			var url = removeParameter(url,'ch');
			url = addParameter(url,'d1',d1);
			url = addParameter(url,'d2',d2);
			url = addParameter(url,'ca',ca);
			url = addParameter(url,'ch','ad');
			document.location = url;
		});

		$('#btn_course_reservation_select_confirmed').click(function(event){
			event.preventDefault();			
			var url = window.location.toString();
			var d1 = $("#utc_date_1").val();
			var d2 = $("#utc_date_2").val();
			var ca = $('input:checkbox[name=chk_utc_canceled]').is(':checked') ? 1 : 0;
			var url = removeParameter(url,'ch');
			url = addParameter(url,'d1',d1);
			url = addParameter(url,'d2',d2);
			url = addParameter(url,'ca',ca);
			url = addParameter(url,'ch','co');
			document.location = url;
		});

		
		$('#btn_rapport').click(function(event){
			event.preventDefault();	
			
			// delete tmp folder
			var action = "action_rapport_delete_tmp_folder";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_add').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_add').hide()",700)},			
				type: 'POST',
				url: 'admin_ajax.php',
				data: "action="+action+"&token="+token,
				success: function(){

				},
			});			

			var plugin_kursbokning_kurs_id = $(this).attr('data-plugin_kursbokning_kurs_id');
			var rapport = $('#rapport').val();
			var d1 = $("#utc_date_1").val();
			var d2 = $("#utc_date_2").val();
			var ca = $('input:checkbox[name=chk_utc_canceled]').is(':checked') ? 1 : 0;

			var loccation = window.location.toString();
			var loccation_dir = loccation.substring(0, loccation.lastIndexOf('/'));	
			var url = loccation_dir+rapport;

			url = addParameter(url,'plugin_kursbokning_kurs_id',plugin_kursbokning_kurs_id);
			url = addParameter(url,'d1',d1);
			url = addParameter(url,'d2',d2);

			if(rapport.length > 0) {
				window.open(url, '_blank', 'width=600,height=400,toolbar=no,scrollbars=no,location=no,resizable=yes');
				// new action - delete file in 20 sec
				var action = "action_rapport_delete_tmp_folder_delayed";
				var token = $("#token").val();
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_course_add').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_course_add').hide()",700)},			
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token,
					success: function(){

					},
				});			
				
			} else {
				$('#rapport').css('background','yellow');
			}
			
			
		});
		
		$('#btn_rapport_one').click(function(event){
			event.preventDefault();	
			
			// delete tmp folder
			var action = "action_rapport_delete_tmp_folder";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_course_add').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_course_add').hide()",700)},			
				type: 'POST',
				url: 'admin_ajax.php',
				data: "action="+action+"&token="+token,
				success: function(){

				},
			});			

			//var plugin_kursbokning_kurs_id = $(this).attr('data-plugin_kursbokning_kurs_id');
			var plugin_kursbokning_kurs_anmalan_id = $(this).attr('data-plugin_kursbokning_kurs_anmalan_id');
			
			var rapport = $('#rapport').val();

			var loccation = window.location.toString();
			var loccation_dir = loccation.substring(0, loccation.lastIndexOf('/'));	
			var url = loccation_dir+rapport;

			url = addParameter(url,'plugin_kursbokning_kurs_anmalan_id',plugin_kursbokning_kurs_anmalan_id);


			if(rapport.length > 0) {
				window.open(url, '_blank', 'width=600,height=400,toolbar=no,scrollbars=no,location=no,resizable=yes');
				// new action - delete file in 20 sec
				var action = "action_rapport_delete_tmp_folder_delayed";
				var token = $("#token").val();
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_course_add').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_course_add').hide()",700)},			
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token,
					success: function(){

					},
				});			
				
			} else {
				$('#rapport').css('background','yellow');
			}
			
			
		});
		
		
		$('#btn_course_reservation_select_export_excel').click(function(event){
			event.preventDefault();			
			tableToExcel('reservations_excel', 'Export');
		});
		$('#btn_course_reservation_select_export_excel_avanti').click(function(event){
			event.preventDefault();			
			tableToExcel('reservations_excel_avanti', 'Export Avanti');
		});


		$("div").delegate(".btn_form_field_custom_delete","click",function(event){
			event.preventDefault();
			var action = "action_form_field_custom_delete";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var plugin_kursbokning_fields_id = $(this).closest('li').find('.custom_question').attr('id');
			$.ajax({
				type: 'POST',
				url: 'admin_ajax.php',
				data: "&action="+action+"&token="+token+"&users_id="+users_id+"&plugin_kursbokning_fields_id="+plugin_kursbokning_fields_id,
				success: function(message){	
					ajaxReply('qwe','#ajax_status_add_question');
				},
			});
			$(this).closest('li').remove();
		});

		$("body").delegate(".remove","click",function(){
			$(this).parent('div').remove();
		});
		
		$('body').delegate('.add_options', 'click', function (event) {
			event.preventDefault();
			$(this).before('<div><input type="text" name="field_values[]" /> <button class="remove">-</button></div>');
		});	

		$('body').delegate('.add_options_checkbox', 'click', function (event) {
			event.preventDefault();
			$(this).before('<div><input type="checkbox"> <input type="text" name="field_values[]" /> <button class="remove">-</button></div>');
		});	

		$('body').delegate('.add_options_radio', 'click', function (event) {
			event.preventDefault();
			$(this).before('<div><input type="radio"> <input type="text" name="field_values[]" /> <button class="remove">-</button></div>');
		});	

		$("body").delegate(".link_form_field_custom_edit","click",function(){
			$(this).closest('li').find('textarea.field-label').toggleDisabled();
			$(this).closest('li').find('.field-label-options').toggleDisabled();
			$(this).closest('li').find('button').toggleDisabled();
			$(this).closest('li').find('textarea').toggleClass('field-label-active');
		});

		$("body").delegate(".btn_form_field_custom_save","click",function(event){
			event.preventDefault();
			var action = "action_form_field_custom_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();

			var plugin_kursbokning_fields_id = $(this).closest('li').find('.custom_question').attr('id');
			var datastring = $(this).closest('li').find('.custom_question').serialize();
			//console.log(datastring);
			
			$(this).closest('li').find('textarea.field-label').toggleDisabled();
			$(this).closest('li').find('.field-label-options').toggleDisabled();
			$(this).closest('li').find('button').toggleDisabled();
			$(this).closest('li').find('textarea').toggleClass('field-label-active');
			
			if(plugin_kursbokning_id.length > 0) {
				$.ajax({
					type: 'POST',
					url: 'admin_ajax.php',
					data: datastring+"&action="+action+"&token="+token+"&users_id="+users_id+"&plugin_kursbokning_fields_id="+plugin_kursbokning_fields_id,
					success: function(message){						
						ajaxReply('','#ajax_status_add_question');
					},
				});
			}
		});

		$('.btn_form_field_add').click(function(event){
			event.preventDefault();
			var action = "action_form_field_add";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var field = $("#field option:selected").val();
			var str = '';
			var field_label = '<textarea name="field_label" class="field-label" disabled>...</textarea>';
			switch(field) {
				case 'text':
					str = field_label+'<br /><input type="text" class="field" value="Textruta" />';
				break;
				case 'checkbox':
					str = '<input type="checkbox" class="field" disabled />'+field_label;
				break;
				case 'checkboxes':
					str = field_label+'<br /><button class="add_options_checkbox" disabled>+</button>';
				break;
				case 'radio':
					str = field_label+'<br /><button class="add_options_radio" disabled>+</button>';
				break;
				case 'textarea':
					str = field_label+'<br /><textarea class="field" disabled>Text</textarea>';
				break;
				case 'select':
					str = field_label+'<select class="field"><option value="0" disabled>(välj)</option></select><br /><button class="add_options" disabled>+</button>';
				break;
				case 'description':
					str = field_label;
				break;
			}

			if(str.length > 0) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_add_question').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_add_question').hide()",700)},
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&plugin_kursbokning_id="+plugin_kursbokning_id+"&field="+field,
					success: function(newdata){						
						var s_select = '<span style="margin:0 10px;">obligatorisk: <select name="required"><option value="0">nej</option><option value="1">ja</option></select></span>';
						$('#custom_fields').prepend('<li id="'+newdata+'"><form class="custom_question" id="'+newdata+'">'+str+'<div style="position:absolute;top:5px;right:-400px;"><img src="css/images/arrow_up_down.png" style="margin:0 10px;cursor:pointer;" /><span class="link_form_field_custom_edit">redigera</span><span style="margin:0 10px;">'+s_select+'<button class="btn_form_field_custom_delete" disabled>Radera</button><button class="btn_form_field_custom_save" disabled>Spara</button></div></form></li>');
						ajaxReply('','#ajax_status_add_question');
						$('textarea').autosize(); 
					},
				});
			}
		});

		$( "#custom_fields" ).sortable({
			placeholder: "ui-state-highlight2",
			axis: 'y',
			opacity: 0.6, 
			cursor: 'move', 
			update: function() {
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var data = "positions="+$(this).sortable("toArray") + "&action=action_field_position_update&token=" + token + "&plugin_kursbokning_id="+plugin_kursbokning_id;
					$.post("admin_ajax.php", data, function(message){
					ajaxReply(message,'#ajax_status_add_question');
				});
			}
		});
				
		$("#btn_toggle_fields").click(function(){
			$(this).closest('table').find('input').toggleDisabled();
			$('table').find('textarea').toggleDisabled();
		});
		
		$('.course_title').click(function(){
			$(this).closest('tr').next('tr').fadeToggle();
		});


		/*
		var admitted = $('#utc_admitted').val();
		if(admitted.length > 0) {
			$('#utc_admitted').addClass('admitted');
		}
		var canceled = $('#utc_canceled').val();
		if(canceled.length > 0) {
			$('#utc_canceled').addClass('canceled');
		}
		*/
		
		
	});
		
</script>







<?php

$kursbokning = new Kursbokning();

$html = $kursbokning->getForm();
if($html) {
	echo $html;
}



$t = (isset($_GET['t'])) ? $_GET['t'] : null;

// url
$this_url = $_SERVER['PHP_SELF'] .'?t='. $t;

echo '<img src="'.CMS_DIR.'/cms/css/images/storiesaround_logotype_black.png" alt="Storiesaround logotype" style="width:120px;float:right;"/>';
echo '<h3 class="plugin-text">plugin</h3>';
echo '<div class="admin-heading">';
	echo '<div class="float">';
		echo '<div class="cms-ui-icons cms-ui-plugins"></div>';
		echo '<div class="float">';
			echo '<a href="'.CMS_HOME.'/help/plugins/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Kursbokning</h1></a>';
		echo '</div>';
	echo '</div>';
	echo '<div class="ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
		$icon = "<span class='ui-icon ui-icon-pencil' style='display:inline-block;height:12px;'></span>";
		get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("info","form_add","form_find","form_edit","course_add","course_find","course_edit","reservation_find","reservation_edit"), array("Info","Nytt formulär","Formulär",$icon,"Ny kurs","Kurser",$icon,"Bokningar",$icon), "t", "", null, $ui_ul_add_class="ui-two", $ui_a_add_class="");
	echo '</div>';	
echo '</div>';


switch($t) {


	case 'form_add':
	
		?>
		<div class="admin-area-outer">
			<div class="admin-area-inner">
			<h3 class="heading">Nytt formulär för kursbokning</h3>
			
			<table>
				<tr>
					<td>
					<label for="title">Formulärets titel</label><br />
					<input type="text" id="title" name="title" style="width:300px;" maxlength="75" />
					</td>
					<td>
					<label for="type">Kurstyp</label><br />
					<select id="type">
					<option value=""></option>
					<?php
					$row = $kursbokning->info();
					$types = explode(",",$row['types']);
					foreach($types as $type) {						
						echo '<option value="'.$type.'"';
						echo '>'.$type.'</option>';						
					}					
					?>
					</select>
					</td>
					<td style="vertical-align:bottom;">
					<span class="toolbar"><button id="btn_form_add">Skapa</button></span>
					<span id="ajax_spinner_add_form" style='display:none'><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_add_form" style='display:none'></span>			
					</td>
				</tr>
			</table>

			</div>
		</div>
		<?php
	
	break;
	
	case 'form_find':

		echo '<div class="admin-area-outer">';
			echo '<div class="admin-area-inner">';

			$html = '';
			$rows = $kursbokning->getKursbokning();
			
			echo '<h3 class="heading">Formulär för kursbokning</h3>';
			if($rows) {
				$html = '<table class="table_js lightgrey">';
					$html .= '<thead>';
						$html .= '<tr>';
							$html .= '<th>Titel</th>';
							$html .= '<th>Kurstyp</th>';
							$html .= '<th style="text-align:center;width:5%;">Aktiv</th>';
							$html .= '<th style="text-align:center;width:5%;">Webb</th>';
							$html .= '<th>Publicerings datum</th>';
							$html .= '<th style="width:5%;text-align:center;">Redigera</th>';
						$html .= '</tr>';
					$html .= '</thead>';					
					$html .= '<tbody>';
						foreach($rows as $row) {
							$html .= '<tr class="">';
								$html .= '<td>'.$row['title'].'</td>';
								$html .= '<td>'.$row['type'].'</td>';
								$q = $row['status'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
								$html .= '<td style="text-align:center;width:5%;">'.$q.'</td>';
								$q = $row['web_reservation'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
								$html .= '<td style="text-align:center;width:5%;">'.$q.'</td>';
								$html .= '<td>'.$row['utc_start_publish'].'</td>';
								$html .= '<td style="text-align:center;"><a class="link_form_edit" href="admin.php?t=form_edit&plugin_kursbokning_id='.$row['plugin_kursbokning_id'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></td>';
							$html .= '</tr>';
						}
					$html .= '</tbody>';
				$html .= '</table>';
			}
			echo $html;

			echo '</div>';
		echo '</div>';
		
	break;
	
	
	case 'info':
	case '';
	
		echo '<div class="admin-area-outer">';
			echo '<div class="admin-area-inner">';

			$row = $kursbokning->info();
			//print_r2($row);
			?>
			
			<?php
			$d1 = date('Y-m-d H:m:s',strtotime("+2 weeks", time()));
			$d2 = date('Y-m-d H:m:s',strtotime("+14 days", time()));
			$d3 = date('Y-m-d H:m:s',strtotime("+15 days", time()));
			
				
				echo '<div class="row">';			
					echo '<div style="width:50%;float:left;height:200px;padding:20px 0;">';
						echo '<h3>Kursbokning är ett CMS-plugin för att:</h3>';
						echo '<ul style="font-size:1.5em;list-style:none;">';
							echo '<li>ansöka till kurs</li>';
							echo '<li>boka kurs</li>';
							echo '<li>boka evenemang</li>';
						echo '</ul>';
					echo '</div>';
					echo '<div style="width:50%;float:right;">';

					
						echo '<h4 style="margin-bottom:10px;">Folkhögskolekurs</h4>';
						
						$kurser = array (
						0 => array(
							"title" => "Lorem ipsum dolor sit amet",
							"utc_start" => $d1,
							"utc_end" => $d1,
							"location" => "Camera visum",
							"cost" => "",
							"description" => "Nulla posuere feugiat mauris. Curabitur facilisis ullamcorper nunc at facilisis. Suspendisse potenti. Ut sit amet magna fringilla felis dignissim bcountryit rhoncus in lorem."),
						1 => array(
							"title" => "Vestibulum ante ipsum primis ",
							"utc_start" => $d2,
							"utc_end" => $d2,
							"location" => "Civitate in civitatem",
							"cost" => "",
							"description" => "Nunc non lacus sit amet quam scelerisque pellentesque. Duis adipiscing ultricies facilisis. Mauris ornare congue ullamcorper.")
						);
					
						$html = '<table class="courses_list kurs"" class="kursval" style="width:500px;">';
							$html .= '<thead><tr">';
								$html .= '<th style="width:5%;"><span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
								$html .= '</th>';
								$html .= '<th>Kurs';
								$html .= '</th>';
								$html .= '<th style="text-align:right;">Datum kursstart';
								$html .= '</th>';
							$html .= '</tr></thead>';
						foreach($kurser as $kurs) {
							$html .= '<tr>';
								$utc_date_start = ($kurs['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_start'], $dtz, 'Y-m-d H:i:s')) : new DateTime(get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
								$utc_date_end = ($kurs['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;
								$html .= '<td><input type="checkbox" id="kurs_id[]" name="kurs_id[]" value="" style="margin-right:10px;"></td>';
								$html .= '<td><span class="course_title">'.$kurs['title'].'</span></td>';
								$dts = '';
								$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
								$html .= '<td style="text-align:right;">'.$dts.'</td>';
							$html .= '</tr>';
							$html .= '<tr style="display:none;">';
								$html .= '<td colspan="5" class="kurs"><div class="course_info"><b>Mer info</b><br /> '.$kurs['description'].'</div></td>';
							$html .= '</tr>';
						}
						$html .= '</table>';
						echo $html;


						
						echo '<h4 style="margin-bottom:10px;">Uppdragsutbildning</h4>';

						$kurser = array (
						0 => array(
							"title" => "Lorem ipsum dolor sit amet",
							"utc_start" => $d1,
							"utc_end" => $d1,
							"location" => "Camera visum",
							"cost" => "700 kr",
							"description" => "Nulla posuere feugiat mauris. Curabitur facilisis ullamcorper nunc at facilisis. Suspendisse potenti. Ut sit amet magna fringilla felis dignissim bcountryit rhoncus in lorem."),
						1 => array(
							"title" => "Vestibulum ante ipsum primis ",
							"utc_start" => $d2,
							"utc_end" => $d3,
							"location" => "Civitate in civitatem",
							"cost" => "2100 kr",
							"description" => "Nunc non lacus sit amet quam scelerisque pellentesque. Duis adipiscing ultricies facilisis. Mauris ornare congue ullamcorper.")
						);

						$html = '<table class="courses_list uppdragsutbildning"" style="width:500px;">';
							$html .= '<thead><tr>';
								$html .= '<th>Kurs';
								$html .= '</th>';
								$html .= '<th>Datum';
								$html .= '</th>';
								$html .= '<th>Plats';
								$html .= '</th>';
								$html .= '<th class="right">Kostnad';
								$html .= '</th>';
								$html .= '<th>Bokning';
								$html .= '</th>';
							$html .= '</tr></thead>';
						foreach($kurser as $kurs) {

							$utc_date_start = ($kurs['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
							$utc_date_end = ($kurs['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;					
							
							$html .= '<tr>';
								$html .= '<td><span class="course_title">'.$kurs['title'].'</span></td>';
								$dts = '';
								if($utc_date_end) {
									if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
										$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
									} else {
										$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
									}
								} else {
									$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
								}
								$html .= '<td>'.$dts.'</td>';
								$html .= '<td>'.$kurs['location'].'</td>';
								$html .= '<td class="right">'.$kurs['cost'].'</td>';						
								$str = '<span class="toolbar"><button value="" class="btn_magic" id="">Boka</button></span>';
								$html .= '<td style="width:10%;">'.$str.'</td>';
							$html .= '</tr>';
							$html .= '<tr style="display:none;">';
								$html .= '<td colspan="5" class="kurs"><div class="course_info"><b>Mer info</b><br /> '.$kurs['description'].'</div></td>';
							$html .= '</tr>';
						}
						$html .= '</table>';
						echo $html;

					
						echo '<h4 style="margin-bottom:10px;">Evenemang</h4>';

						$kurser = array (
						0 => array(
							"title" => "Lorem ipsum dolor sit amet",
							"utc_start" => $d1,
							"utc_end" => $d1,
							"location" => "Camera visum",
							"cost" => "700 kr",
							"description" => "Nulla posuere feugiat mauris. Curabitur facilisis ullamcorper nunc at facilisis. Suspendisse potenti. Ut sit amet magna fringilla felis dignissim bcountryit rhoncus in lorem."),
						1 => array(
							"title" => "Vestibulum ante ipsum primis ",
							"utc_start" => $d2,
							"utc_end" => $d3,
							"location" => "Civitate in civitatem",
							"cost" => "2100 kr",
							"description" => "Nunc non lacus sit amet quam scelerisque pellentesque. Duis adipiscing ultricies facilisis. Mauris ornare congue ullamcorper.")
						);

						
						$html = '';
						foreach($kurser as $kurs) {

							$html .= '<table class="courses_list evenemang" style="width:500px;">';
						
								$utc_date_start = ($kurs['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
								$utc_date_end = ($kurs['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;					

								$dts = '';
								if($utc_date_end) {
									if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
										$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
									} else {
										$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
									}
								} else {
									$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
								}
								
								$html .= '<tr>';
									$html .= '<td>'.$dts.' | '.$kurs['location'].'</td>';
									$str = '<span class="toolbar"><button value="" class="btn_magic" id="">Boka</button></span>';
									$html .= '<td style="width:10%;">'.$str.'</td>';									
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td><span class="course_title events">'.$kurs['title'].'</span></td>';
									$html .= '<td class="right">'.$kurs['cost'].'</td>';						
								$html .= '</tr>';
								$html .= '<tr style="display:none;">';
									$html .= '<td colspan="5" class="kurs"><div class=""><b>Mer info</b><br /> '.$kurs['description'].'</div></td>';
								$html .= '</tr>';
							
							$html .= '</table>';
						}						
						echo $html;

						
					echo '</div>';					
				echo '</div>';
				
				echo '<div style="clear:both;"></div>';


			echo '</div>';
		echo '</div>';

	break;


	case 'form_edit':
	
		$plugin_kursbokning_id = isset($_GET['plugin_kursbokning_id']) && is_numeric($_GET['plugin_kursbokning_id']) ? $_GET['plugin_kursbokning_id'] : null;
		if(!$plugin_kursbokning_id) {die;};		
		?>
		
		<div class="admin-area-outer">
			<div class="admin-area-inner">
			
			<h3 class="heading">Redigering av formulär för kursbokning</h3>
			
			<input type="hidden" id="plugin_kursbokning_id" value="<?php echo $plugin_kursbokning_id;?>">

			<?php
			$result = $kursbokning->getKursbokningId($plugin_kursbokning_id);		
			$date_start = ($result['utc_start_publish']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_start_publish'], $dtz, 'Y-m-d H:i:s')) : new DateTime(get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
			$date_end = ($result['utc_end_publish']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_end_publish'], $dtz, 'Y-m-d H:i:s')) : null;				
			?>

				
				
				Formulär: <span style="font-size:1.5em;"><?php echo $result['title'];?></span>
				
				<div id="tabs_edit" style="display:none;">
					
					<ul>
						<li><a href="#settings">Inställningar</a></li>
						<li><a href="#fields">Grundformulär</a></li>
						<li><a href="#uploads">Ladda upp filer</a></li>
						<li><a href="#fields_new">Lägg till nya fält</a></li>
						<li><a href="#form_courses">Koppla kurs</a></li>
					</ul>
										
					<div id="settings">	

						<div style="float:right;text-align:right;">
							<span class="ajax_spinner_form_edit" style='display:none'><img src="css/images/spinner.gif"></span>
							<span class="ajax_status_form_edit" style='display:none'></span>
							<span class="toolbar"><button class="btn_form_save">Spara</button></span>
							<span class="toolbar"><button type="submit" onclick="preview(<?php echo $plugin_kursbokning_id; ?>)">Förhandsgranska</button></span>
							<span class="toolbar"><button type="submit" onclick="kursbokning(<?php echo $plugin_kursbokning_id; ?>)">Använd formulär</button></span>
							
							<?php
							// check courses using this form
							$row = $kursbokning->getKursbokningCount($plugin_kursbokning_id);
							$disabled = $row['count'] ? 'disabled' : null;
							echo '<span class="toolbar"><button id="btn_form_delete" title="Formulär kan endast raderas om kopplade kurser saknas" '.$disabled.'>Radera formulär</button></span>';
							?>
							
							<br /><br />
							Antal kurser kopplade till formuläret: <?php echo $row['count'];?>		
							
						</div>
			
						<div>
						<h3 class="heading">Inställningar</h3>
						<table class="reservation_edit" style="width:100%;">
							<tr>
								<td colspan="3">
								<label for="title">Titel</label><br />
								<input type="text" name="title" id="title" value="<?php echo $result['title'];?>" style="width:400px;" />
								</td>
							</tr>
							<tr>
								<td style="vertical-align:top;" colspan="2">
									<label for="terms">Villkor (max 10 000 tecken)</label><br />
									<textarea name="terms" id="terms" style="width:600px;min-height:75px;max-height:400px;"><?php echo $result['terms'];?></textarea>
								</td>
								<td>
									<p>
									<input type="checkbox" name="status" id="status" value="1" <?php if($result['status']==1) {echo 'checked';}?>> Aktiv
									</p>
									<p>
									<input type="checkbox" name="web_reservation" id="web_reservation" value="1" <?php if($result['web_reservation']==1) {echo 'checked';}?>> Boka via webben
									</p>
									<p>
									<label for="type">Kurstyp</label><br />
									<select id="type" disabled>
									<option value=""></option>
									<?php
									$row = $kursbokning->info();
									$types = explode(",",$row['types']);
									foreach($types as $type) {
										
										echo '<option value="'.$type.'"';
										if($type==$result['type']) {
											echo ' selected';
										}
										echo '>'.$type.'</option>';
										
									}					
									?>
									</select>
									</p>
								</td>
							</tr>
							<tr>
								<td>
									<label for="date_start">Publicera fr o m:</label><br />
									<input type="text" id="date_start" value="<?php if($date_start) {echo $date_start->format('Y-m-d');} ?>">
									<input type="text" id="time_start" size="5" maxlength="5" title="add hours and minutes hh:mm" value="<?php if($date_end) {echo $date_start->format('H:i');} ?>">
								</td>
								<td>
									<label for="date_end">Publicera t om:</label><br />
									<input type="text" id="date_end" value="<?php if($date_end) {echo $date_end->format('Y-m-d');} ?>">
									<input type="text" id="time_end" size="5" maxlength="5" title="add hours and minutes hh:mm" value="<?php if($date_end) {echo $date_end->format('H:i');} ?>">
								</td>
								<td>
									<label for="email_notify">E-post för bokningar/ansökningar där all information skickas inkl ev uppladdade filer (ex: <?php echo $_SESSION['site_email'];?>)</label><br />
									<input type="text" id="email_notify" style="width:300px;"  value="<?php echo $result['email_notify']; ?>">
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td style="width:45%;"><i>Flera e-postadresser kan läggas till (enklare notifiering) när bokning/ansökan görs till respektive kurs. Den inställningen finns i formuläret för kursredigering.</i></td>
							</tr>
						</table>
						</div>


					</div>
				
					<div id="fields">
					
						<div style="float:right;text-align:right;">
							<span class="ajax_spinner_form_edit" style="display:none;"><img src="css/images/spinner.gif"></span>
							<span class="ajax_status_form_edit" style="display:none;"></span>
							<span class="toolbar"><button class="btn_form_save">Spara</button></span>
							<span class="toolbar"><button type="submit" onclick="preview(<?php echo $plugin_kursbokning_id; ?>)">Förhandsgranska</button></span>
							<span class="toolbar"><button type="submit" onclick="kursbokning(<?php echo $plugin_kursbokning_id; ?>)">Använd formulär</button></span>
						</div>
					
						<h3 class="heading">Använd följande fält i grundformuläret</h3>
						
						<?php
						
						include 'kursbokning_functions.php';
						
						function getOption($name,$required) {
							$html = '<span style="margin:0 10px;position:absolute;left:300px;">obligatorisk: <select name="'.$name.'" id="'.$name.'"><option value="0" ';
							if($required==1) {
								$html .= 'selected';
							}
							$html .= '>nej</option><option value="1" ';
							if($required==2) {
								$html .= 'selected';
							}
							$html .= '>ja</option></select></span>';
							return $html;
						}
						
						$fields = json_decode($result['fields']);
						$adress = get_field_setting("adress",$fields);
						$postnummer = get_field_setting("postnummer",$fields);
						$ort = get_field_setting("ort",$fields);
						$mobil = get_field_setting("mobil",$fields);
						$telefon = get_field_setting("telefon",$fields);
						$personnummer = get_field_setting("personnummer",$fields);
						$lan = get_field_setting("lan",$fields);
						$kommun = get_field_setting("kommun",$fields);
						$country = get_field_setting("country",$fields);
						$organisation = get_field_setting("organisation",$fields);
						$fakturaadress = get_field_setting("fakturaadress",$fields);
						?>
						
						<ul id="fields_std">
							<li><input type="checkbox" checked disabled="disabled"> förnamn</li>
							<li><input type="checkbox" checked disabled="disabled"> efternamn</li>
							<li><input type="checkbox" checked disabled="disabled"> epost</li>							
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="adress" id="adress" value="1" <?php if($adress>0) {echo 'checked';}?>> adress <?php echo getOption($name="adress_option", $required=$adress);?></li>
							<li><input type="checkbox" name="postnummer" id="postnummer" value="1" <?php if($postnummer>0) {echo 'checked';}?>> postnummer <?php echo getOption($name="postnummer_option", $required=$postnummer);?></li>
							<li><input type="checkbox" name="ort" id="ort" value="1" <?php if($ort>0) {echo 'checked';}?>> ort <?php echo getOption($name="ort_option", $required=$ort);?></li>
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="mobil" id="mobil" value="1" <?php if($mobil>0) {echo 'checked';}?>> mobil <?php echo getOption($name="mobil_option", $required=$mobil);?></li>
							<li><input type="checkbox" name="telefon" id="telefon" value="1" <?php if($telefon>0) {echo 'checked';}?>> telefon <?php echo getOption($name="telefon_option", $required=$telefon);?></li>
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="personnummer" id="personnummer" value="1" <?php if($personnummer>0) {echo 'checked';}?>> personnummer <?php echo getOption($name="personnummer_option", $required=$personnummer);?></li>
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="lan" id="lan" value="1" <?php if($lan>0) {echo 'checked';}?>> län<?php echo getOption($name="lan_option", $required=$lan);?></li>
							<li><input type="checkbox" name="kommun" id="kommun" value="1" <?php if($kommun>0) {echo 'checked';}?>> kommun <?php echo getOption($name="kommun_option", $required=$kommun);?></li>
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="country" id="country" value="1" <?php if($country>0) {echo 'checked';}?>> land <?php echo getOption($name="country_option", $required=$country);?></li>
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="organisation" id="organisation" value="1" <?php if($organisation>0) {echo 'checked';}?>> organisation | företag | förening<?php echo getOption($name="organisation_option", $required=$organisation);?></li>
							<hr style="width:500px;" align="left" />
							<li><input type="checkbox" name="fakturaadress" id="fakturaadress" value="1" <?php if($fakturaadress>0) {echo 'checked';}?>> fakturaadress<?php echo getOption($name="fakturaadress_option", $required=$fakturaadress);?></li>
						</ul>
					
					</div>
					


					<div id="uploads">
					
						<div style="float:right;text-align:right;">
							<span class="ajax_spinner_form_edit" style="display:none;"><img src="css/images/spinner.gif"></span>
							<span class="ajax_status_form_edit" style="display:none;"></span>
							<span class="toolbar"><button class="btn_form_save">Spara</button></span>
							<span class="toolbar"><button type="submit" onclick="preview(<?php echo $plugin_kursbokning_id; ?>)">Förhandsgranska</button></span>
							<span class="toolbar"><button type="submit" onclick="kursbokning(<?php echo $plugin_kursbokning_id; ?>)">Använd formulär</button></span>
						</div>
					
						<h3 class="heading">Inställningar för att ladda upp filer</h3>
						
						<?php
						
						
						?>

						<p>
							<input type="checkbox" name="file_upload" id="file_upload" value="1" <?php if($result['file_upload'] == 1) { echo ' checked';}?>> Aktivera funktion för att ladda upp filer
						</p>
						<p>
							<label for="file_instruction">Beskrivning av funktion</label><br />
							<textarea name="file_instruction" id="file_instruction" style="width:600px;min-height:75px;"><?php echo $result['file_instruction'];?></textarea>
						</p>
						<p>
							<label for="file_extensions">Ange tillåtna filtyper</label><br />
							<input type="text" id="file_extensions" style="width:600px;" value="<?php echo $result['file_extensions']; ?>">
						</p>
						<p>
							<label for="file_path">Ange sökväg till filer för att administrera bokningar: URL | PATH | UNC (file://)</label><br />
							<input type="text" id="file_path" style="width:600px;" value="<?php echo $result['file_path']; ?>" title="Sökväg anges med avslutande /">
						</p>
						<p>
							<label for="file_attach">Bestäm åtgärd för filer som laddats upp</label><br />
							<p>
							<input type="checkbox" id="file_attach" name="file_attach" value="1" <?php if($result['file_attach'] == 1) { echo ' checked';}?>> Bifoga i mail - adress sparad under fliken inställningar: <i><?php echo $result['email_notify']; ?></i>
							</p>
							<p>
								<input type="radio" name="file_action" value="1" <?php if($result['file_action'] == 1) { echo ' checked';}?>> Radera
								<br />
								<input type="radio" name="file_action" value="2" <?php if($result['file_action'] == 2) { echo ' checked';}?>> Lämna
								<br />
								<input type="radio" name="file_action" value="3" <?php if($result['file_action'] == 3) { echo ' checked';}?>> Flytta - ange relativ sökväg med inledande och avslutande slash / : <input type="text" id="file_move_path" style="width:400px;" value="<?php echo $result['file_move_path']; ?>">
							</p>
						</p>
						
					</div>
					
					
					<div id="fields_new">

						<div style="float:right;text-align:right;">
							<span class="ajax_spinner_form_edit" style='display:none'><img src="css/images/spinner.gif"></span>
							<span class="ajax_status_form_edit" style='display:none'></span>
							<span class="toolbar"><button type="submit" onclick="preview(<?php echo $plugin_kursbokning_id; ?>)">Förhandsgranska</button></span>
							<span class="toolbar"><button type="submit" onclick="kursbokning(<?php echo $plugin_kursbokning_id; ?>)">Använd formulär</button></span>
						</div>

						<h3 class="heading">Lägg till nya fält</h3>
		
						<select name="field" id="field">
							<option value="">(välj typ)</option>
							<option value="text">textruta</option>
							<option value="textarea">text</option>
							<option value="checkbox">kryssruta</option>
							<option value="checkboxes">kryssrutor</option>
							<option value="radio">radioknappar</option>
							<option value="select">listruta</option>
							<option value=""></option>
							<option value="description">beskrivning</option>
						</select>
						
						<span class="toolbar"><button class="btn_form_field_add">Lägg till</button></span>
						<span id="ajax_spinner_add_question" style="display:none;"><img src="css/images/spinner.gif"></span>
						<span id="ajax_status_add_question" style="display:none;"></span>
							
						<ul id="custom_fields">

						<?php
						$result_fields = $kursbokning->getKursbokningFieldsId($plugin_kursbokning_id);

						function getTypeFields($field, $label, $values) {
							$html = '';
							switch($field) {
								case 'text':
									$html = '<textarea name="field_label" class="field-label" disabled>'.$label.'</textarea><br /><input type="text" class="field" value="Textruta" />';
								break;
								case 'checkbox':
									$html = '<input type="checkbox" class="field" disabled /> <textarea name="field_label" class="field-label" disabled>'.$label.'</textarea>';
								break;
								case 'checkboxes':
									$html = '<textarea name="field_label" class="field-label" disabled>'.$label.'</textarea><br />';
									$values = json_decode($values);
									if(is_array($values)) {
										foreach($values as $key=>$value) {
											$html .= '<div><input type="checkbox"> <input type="text" name="field_values[]" value="'.$value.'" class="field-label-options" disabled /> <button class="remove" disabled>-</button></div>';
										}
									}
									$html .= '<button class="add_options" disabled>+</button>';
								break;
								case 'radio':
									$html = '<textarea name="field_label" class="field-label" disabled>'.$label.'</textarea><br />';
									$values = json_decode($values);
									if(is_array($values)) {
										foreach($values as $key=>$value) {
											$html .= '<div><input type="radio"> <input type="text" name="field_values[]" value="'.$value.'" class="field-label-options" disabled /> <button class="remove" disabled>-</button></div>';
										}
									}
									$html .= '<button class="add_options" disabled>+</button>';
								break;
								case 'textarea':
									$html = '<textarea name="field_label" class="field-label" disabled>'.$label.'</textarea><br /><textarea class="field" disabled>Text</textarea>';
								break;
								case 'select':
									$html = '<textarea name="field_label" class="field-label" disabled>'.$label.'</textarea><br /><select class="field">';
									$html .= '<option value="0" disabled>(välj)</option>';
									$html .= '</select><br />';
									$values = json_decode($values);
									if(is_array($values)) {
										foreach($values as $key=>$value) {
											$html .= '<div><input type="text" name="field_values[]" value="'.$value.'" class="field-label-options" disabled /> <button class="remove" disabled>-</button></div>';
										}
									}
									$html .= '<button class="add_options" disabled>+</button>';
								break;
								case 'description':
									$html = '<textarea name="field_label" class="field-label" disabled>'.$label.'</textarea>';
								break;
							}
							return $html;
						}

						function getTypeRequired($required) {
							$html = '<span style="margin:0 10px;">obligatorisk: <select name="required"><option value="0" ';
							if($required==0) {
								$html .= 'selected';
							}
							$html .= '>nej</option><option value="1" ';
							if($required==1) {
								$html .= 'selected';
							}
							$html .= '>ja</option></select></span>';
							return $html;
						}
						
						if($result_fields) {
							foreach($result_fields as $result_field) {
								$label = $result_field['label'];
								$field = $result_field['field'];
								$required = $result_field['required'];
								$values = $result_field['field_values'];
							
								echo '<li id="'.$result_field['plugin_kursbokning_fields_id'].'"><form class="custom_question" id="'.$result_field['plugin_kursbokning_fields_id'].'">';
								echo getTypeFields($field, $label, $values);
								echo '<div style="position:absolute;top:5px;right:-400px;"><img src="css/images/arrow_up_down.png" style="margin:0 10px;cursor:pointer;" /><span class="link_form_field_custom_edit">redigera</span><span style="margin:0 10px;">';
								echo getTypeRequired($required);
								echo '<button class="btn_form_field_custom_delete" disabled>Radera</button><button class="btn_form_field_custom_save" disabled>Spara</button></div></form></li>';
							}
						}
						?>
						</ul>

					</div>
				
					
					<div id="form_courses">

						<div style="float:right;text-align:right;">
							<span class="ajax_spinner_form_edit" style='display:none'><img src="css/images/spinner.gif"></span>
							<span class="ajax_status_form_edit" style='display:none'></span>
							<span class="toolbar"><button type="submit" onclick="preview(<?php echo $plugin_kursbokning_id; ?>)">Förhandsgranska</button></span>
							<span class="toolbar"><button type="submit" onclick="kursbokning(<?php echo $plugin_kursbokning_id; ?>)">Använd formulär</button></span>
						</div>
												
						<div>
						<h3 class="heading">Koppling av kurs till aktuellt formulär</h3>
						</div>

						<?php
						$kurser = $kursbokning->getKursbokningKurs();
						
						if($kurser) {
						
							$s = '<table class="table_js lightgrey">';
								$s .= '<thead>';
								$s .= '<tr>';
									$s .= '<th>Kurs</th>';
									$s .= '<th>Kurstyp</th>';
									$s .= '<th style="text-align:center;width:5%;">Status</th>';
									$s .= '<th style="text-align:center;width:5%;">Webb</th>';
									$s .= '<th>Start</th>';
									$s .= '<th>Slut</th>';
									$s .= '<th>Formulär</th>';
									$s .= '<th>&nbsp;</th>';
								$s .= '</tr>';
								$s .= '<thead>';
								$s .= '<tbody>';
								foreach($kurser as $kurs) {
									
									$s .= '<tr id="'.$kurs['plugin_kursbokning_kurs_id'].'">';
										$css = $result['type']!=$kurs['type'] ? 'color:grey;' : '';
										$s .= '<td><span style="'.$css.'">'.$kurs['title'].'</span></td>';
										$s .= '<td><span style="'.$css.'">'.$kurs['type'].'</span></td>';
										$q = $kurs['status'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
										$s .= '<td style="text-align:center;width:5%;"><span style="'.$css.'">'.$q.'</span></td>';
										$q = $kurs['web_reservation'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
										$s .= '<td style="text-align:center;width:5%;"><span style="'.$css.'">'.$q.'</span></td>';
										$utc_start = $kurs['utc_start']>'2000-01-01 00:00' ? new DateTime($kurs['utc_start']) : null;
										$utc_start_date = $utc_start? $utc_start->format('Y-m-d') : null;
										$utc_end = $kurs['utc_end']>'2000-01-01 00:00' ? new DateTime($kurs['utc_end']) : null;
										$utc_end_date = $utc_end? $utc_end->format('Y-m-d') : null;
										$s .= '<td><span style="'.$css.'">'.$utc_start_date.'</span></td>';
										$s .= '<td><span style="'.$css.'">'.$utc_end_date.'</span></td>';
										$s .= '<td style="width:20%;" id="title-'.$kurs['plugin_kursbokning_kurs_id'].'"><span style="'.$css.'">'.substr($kurs['title_form'],0,30).'</span></td>';
										$s .= '<td id="'.$kurs['plugin_kursbokning_kurs_id'].'">';
										if($result['type']==$kurs['type']) {
											$s .= $kurs['plugin_kursbokning_id']==$result['plugin_kursbokning_id'] ? '<span class="toolbar" id="'.$kurs['plugin_kursbokning_kurs_id'].'"><button class="btn_form_course_remove">Ta bort koppling</button></span>' : '<span class="toolbar" id="'.$kurs['plugin_kursbokning_kurs_id'].'"><button class="btn_form_course_save">Koppla</button></span>';
										} else {
											$s .= '&nbsp;';
										}
										$s .= '</td>';
										
									$s .= '</tr>';
								
								}
								$s .= '<tbody>';
							$s .= '</table>';	
							echo $s;
						}
						?>
					</div>
				
				
				<!--tabs_edit-->
				</div>	
			<!--inner /outer -->
			</div>
		</div>
		
		
		
		<?php
	
	break;

	case 'course_add':
	
		?>
		<div class="admin-area-outer">
			<div class="admin-area-inner">
			
			<h3 class="heading">Ny kurs</h3>
			
			<table>
				<tr>
					<td>
					<label for="title">Kurstitel</label><br />
					<input type="text" id="title" name="title" style="width:300px;" maxlength="75" />
					</td>
					<td>
					<label for="type">Kurstyp</label><br />
					<select id="type">
					<option value=""></option>
					<?php
					$row = $kursbokning->info();
					$types = explode(",",$row['types']);
					foreach($types as $type) {						
						echo '<option value="'.$type.'"';
						echo '>'.$type.'</option>';
						
					}					
					?>
					</select>
					</td>
					<td style="vertical-align:bottom;">
					<span class="toolbar"><button id="btn_course_add">Skapa</button></span>
					<span id="ajax_spinner_course_add" style='display:none'><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_course_add" style='display:none'></span>
					</td>
				</tr>
			</table>
			
			</div>
		</div>
		<?php
	
	break;


	case 'course_find':

		echo '<div class="admin-area-outer">';
			echo '<div class="admin-area-inner">';

			$html = '';
			$rows = $kursbokning->getKursbokningKurser();
			
			echo '<h3 class="heading">Kurser</h3>';
			
			if($rows) {
				$html = '<table class="table_js lightgrey" id="datatable_courses">';
					$html .= '<thead>';
						$html .= '<tr>';
							$html .= '<th>Title</th>';
							$html .= '<th>Kurstyp</th>';
							$html .= '<th>Aktiv</th>';
							$html .= '<th style="text-align:center;width:5%;">Webb</th>';
							$html .= '<th>Kurs start</th>';
							$html .= '<th>Kurs slut</th>';
							$html .= '<th style="text-align:center;width:5%;">Redigera</th>';							
							$html .= '<th style="text-align:center;width:5%;">Bokningar</th>';
							$html .= '<th style="text-align:center;width:5%;">Boka</th>';
						$html .= '</tr>';
					$html .= '</thead>';
					$html .= '<tbody>';
						foreach($rows as $row) {
							$html .= '<tr class="">';
								$html .= '<td>'.$row['title'].'</td>';
								$html .= '<td>'.$row['type'].'</td>';
								$q = $row['status'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
								$html .= '<td style="text-align:center;width:5%;">'.$q.'</td>';
								$q = $row['web_reservation'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
								$html .= '<td style="text-align:center;width:5%;">'.$q.'</td>';	
								$utc_start = $row['utc_start']>'2000-01-01 00:00' ? new DateTime($row['utc_start']) : null;
								$utc_start_date = $utc_start? $utc_start->format('Y-m-d') : null;
								$utc_end = $row['utc_end']>'2000-01-01 00:00' ? new DateTime($row['utc_end']) : null;
								$utc_end_date = $utc_end? $utc_end->format('Y-m-d') : null;
								$html .= '<td>'.$utc_start_date.'</td>';
								$html .= '<td>'.$utc_end_date.'</td>';
								$html .= '<td style="text-align:center;"><a class="link_form_edit" href="admin.php?t=course_edit&plugin_kursbokning_kurs_id='.$row['plugin_kursbokning_kurs_id'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;" title="Redigera kurs '.$row['title'].'"></span></a></td>';								
								$html .= '<td style="text-align:center;"><a class="link_form_edit" href="admin.php?t=reservation_find&plugin_kursbokning_kurs_id='.$row['plugin_kursbokning_kurs_id'].'"><span class="ui-icon ui-icon-person" style="display:inline-block;" title="Visa bokningar till '.$row['title'].'"></span></a></td>';
								$html .= '<td style="text-align:center;"><a class="link_course_reservation_add" href="admin.php?t=reservation_find&plugin_kursbokning_kurs_id='.$row['plugin_kursbokning_kurs_id'].'" id="'.$row['plugin_kursbokning_kurs_id'].'"><span class="ui-icon ui-icon-document" style="display:inline-block;" title="Ny manuell bokning '.$row['title'].'"></span></a></td>';
							$html .= '</tr>';
						}
					$html .= '</tbody>';
				$html .= '</table>';
			}
			echo $html;

			echo '</div>';
		echo '</div>';
		
	break;


	case 'course_edit':
	
		$plugin_kursbokning_kurs_id = isset($_GET['plugin_kursbokning_kurs_id']) && is_numeric($_GET['plugin_kursbokning_kurs_id']) ? $_GET['plugin_kursbokning_kurs_id'] : null;
		if(!$plugin_kursbokning_kurs_id) {die;};		
		?>
		
		<div class="admin-area-outer">
			<div class="admin-area-inner">
			
			<input type="hidden" id="plugin_kursbokning_kurs_id" value="<?php echo $plugin_kursbokning_kurs_id;?>">

			<?php
			$result = $kursbokning->getKursbokningKursId($plugin_kursbokning_kurs_id);		
			$utc_date_start = ($result['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_start'], $dtz, 'Y-m-d H:i:s')) : new DateTime(get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
			$utc_date_end = ($result['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;				
			?>
			
			<div style="float:right;text-align:right;">
			<span id="ajax_spinner_course_edit" style='display:none'><img src="css/images/spinner.gif"></span>
			<span id="ajax_status_course_edit" style='display:none'></span>
			
			<?php
			// check reservations
			$row = $kursbokning->getKursbokningKurserAnmalanCount($plugin_kursbokning_kurs_id);
			$disabled = $row['count'] ? 'disabled' : null;
			echo '<span class="toolbar"><button class="btn_course_delete" id="'.$plugin_kursbokning_kurs_id.'" title="Kurs kan endast raderas om anmälningar saknas" '.$disabled.'>Radera kurs</button></span>';
			?>
			
			<span class="toolbar"><button id="btn_course_save">Spara</button></span>
			<br /><br />
			<a href="admin.php?t=reservation_find&plugin_kursbokning_kurs_id=<?php echo $plugin_kursbokning_kurs_id;?>">Antal anmälda: <?php echo $row['count'];?></a>
			</div>

			<div>
			<h3 class="heading">Redigera kurs</h3>
			<table id="course_edit" style="width:800px;">
				<tr>
					<td style="vertical-align:top;">
					<p>
					<label for="title">Titel</label><br />
					<input type="text" name="title" id="title" value="<?php echo $result['title'];?>" style="width:400px;" />
					</p>
					</td>					
					<td>
					<p>
					<label for="type">Kurstyp</label><br />
					<select id="type" disabled>
					<option value=""></option>
					<?php
					$row = $kursbokning->info();
					$types = explode(",",$row['types']);
					foreach($types as $type) {						
						echo '<option value="'.$type.'"';
						if($type==$result['type']) {
							echo ' selected';
						}
						echo '>'.$type.'</option>';
					}					
					?>
					</select>
					</p>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;">
					<p>
					<label for="terms">Beskrivning</label><br />
					<textarea name="description" id="description" style="width:400px;min-height:100px;"><?php echo $result['description'];?></textarea>
					</p>
					</td>
					<td>
					<p>
					<input type="checkbox" name="status" id="status" value="1" <?php if($result['status']==1) {echo 'checked';}?>> Aktiv
					</p>
					<p>
					<input type="checkbox" name="web_reservation" id="web_reservation" value="1" <?php if($result['web_reservation']==1) {echo 'checked';}?>> Boka via webben
					</p>
					<p>
					<label for="participants">Antal kursdeltagare</label><br />
					<input type="participants" id="participants" value="<?php echo $result['participants'];?>"">
					</p>
					<p>
					<input type="checkbox" name="show_participants" id="show_participants" value="1" <?php if($result['show_participants']==1) {echo 'checked';}?>> Visa antal deltagare i formulär
					</p>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top;">
					<p>
					<label for="location">Plats</label><br />
					<input type="text" id="location" name="location" value="<?php echo $result['location'];?>"">
					</p>
					</td>
					<?php if($result['type']=='Uppdragsutbildning' || $result['type']=='Evenemang')  { ?>
					<td style="vertical-align:top;">
					<p>
					<label for="cost">Kostnad</label><br />
					<input type="text" id="cost" style="width:200px;" value="<?php echo $result['cost'];?>" maxlength="50">
					</p>
					</td>
					<?php } ?>
				</tr>
				<tr>
					<td>
					<p>
					<label for="date_start">Datum för kursstart:</label><br />
					<input type="text" id="utc_date_start" value="<?php if($utc_date_start) {echo $utc_date_start->format('Y-m-d');} ?>">
					<input type="text" id="utc_time_start" size="5" maxlength="5" title="add hours and minutes hh:mm" value="">
					</p>
					</td>
					<td>
					<p>
					<label for="date_end">Datum för kursslut:</label><br />
					<input type="text" id="utc_date_end" value="<?php if($utc_date_end) {echo $utc_date_end->format('Y-m-d');} ?>">
					<input type="text" id="utc_time_end" size="5" maxlength="5" title="add hours and minutes hh:mm" value="">
					</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<p>
					<label for="notify">Notifiera epostadress(er) vid kursanmälan (separera flera med kommatecken)</label><br />
					<input type="text" id="notify" name="notify" style="width:100%" maxlength="200" value="<?php echo $result['notify'];?>">
					</p>
					</td>
				</tr>
			</table>
			</div>
			
			</div>
		</div>
		
		
		<?php
	
	break;

	
	
	case 'reservation_find':

		echo '<div class="admin-area-outer">';
			echo '<div class="admin-area-inner">';

			$plugin_kursbokning_kurs_id = isset($_GET['plugin_kursbokning_kurs_id']) && is_numeric($_GET['plugin_kursbokning_kurs_id']) ? $_GET['plugin_kursbokning_kurs_id'] : null;

			$result = $kursbokning->getKursbokningKursId($plugin_kursbokning_kurs_id);		
			
			if($result) {
				
				$named = '';
				switch ($result['type']) {
					case 'Uppdragsutbildning':					
						$named = 'bokningar';
					break;
					case 'Evenemang':					
						$named = 'bokningar';
					break;
					case 'Uppdragsutbildning':
						$named = 'ansökningar';
					break;
				}
			
				// check reservations						
				$row = $kursbokning->getKursbokningKurserAnmalanCount($result['plugin_kursbokning_kurs_id']);
				$i = $row ? $row['count'] : 0;

				
				$utc_date_start = ($result['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
				$utc_date_end = ($result['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;
				if($utc_date_end) {
					if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
						$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
					} else {
						$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
					}
				} else {
					$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
				}
				
				echo '<table style="width:100%;">';
					echo '<tr>';
						echo '<td><h3 class="heading">Antal '.$named.' till "'. $result['title'].'"</h3></td>';
						echo '<td style="text-align:right;">Antal '.$named.' | platser: '.$i.' | '.$result['participants'].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td colspan="2"><b>'.$dts.'</b></td>';
					echo '</tr>';
				echo '</table>';
				
			}
			
			$utc_date_1 = isset($_GET['d1']) ? filter_var(trim($_GET['d1']), FILTER_SANITIZE_STRING) : date('Y-m-d',strtotime("-6 months", time()));
			$utc_date_2 = isset($_GET['d2']) ? filter_var(trim($_GET['d2']), FILTER_SANITIZE_STRING) : date('Y-m-d',strtotime("+1 day", time()));
			
			$canceled = isset($_GET['ca']) ? filter_input(INPUT_GET, 'ca', FILTER_VALIDATE_INT) : 0;
			$choosen = isset($_GET['ch']) ? filter_var(trim($_GET['ch']), FILTER_SANITIZE_STRING)  : '';
			
			?>
			<table style="width:100%;margin:10px 0;">
				<tr>
					<td>
					<label for="utc_date_1">Datum från:</label><br />
					<input type="text" id="utc_date_1" value="<?php echo $utc_date_1; ?>" style="background:#FFFFCC;" size="10">
					</td>
					<td>
					<label for="utc_date_2">Datum till:</label><br />
					<input type="text" id="utc_date_2" value="<?php echo $utc_date_2; ?>" size="10">
					</td>
					<td style="vertical-align:bottom;bottom;width:10%;padding-left:10px;">
					<input type="checkbox" id="chk_utc_canceled" name="chk_utc_canceled" value="1" <?php if($canceled) { echo 'checked';} ?>>&nbsp;inkl återbud
					</td>
					<td style="vertical-align:bottom;">
					<span class="toolbar"><button id="btn_course_reservation_select">Kursansökningar</button></span>
					<span class="toolbar"><button id="btn_course_reservation_select_admitted">Antagna</button></span>
					<span class="toolbar"><button id="btn_course_reservation_select_confirmed">Bekräftade</button></span>
					</td>
					<td style="width:40%;text-align:right;vertical-align:bottom;">
					
					<select name="rapport" id="rapport">
					<?php include_once 'inc.rapport.php'; ?>
					</select>
					<span class="toolbar"><button type="submit" id="btn_rapport" data-plugin_kursbokning_kurs_id="<?php echo $plugin_kursbokning_kurs_id; ?>">Exportera</button></span>
					</td>
				</tr>
			</table>
			
			<?php
			
			$rows = $kursbokning->getKursbokningKurserAnmalan($plugin_kursbokning_kurs_id, $utc_date_1, $utc_date_2, $choosen, $canceled);
				
			$html = '<table class="table_js lightgrey" id="reseq">';
				$html .= '<thead>';
					$html .= '<tr>';
						$html .= '<th>Förnamn</th>';
						$html .= '<th>Efternamn</th>';
						$html .= '<th>Epost</th>';
						$html .= '<th>Postnr</th>';
						$html .= '<th>Ort</th>';
						$html .= '<th>Datum</th>';
						$html .= '<th>Sökt kurs</th>';
						$html .= '<th style="text-align:center;"><span class="ui-icon ui-icon-person" style="display:inline-block;"></th>';
					$html .= '</tr>';
				$html .= '</thead>';
				
				if($rows) {
					$html .= '<tbody>';
						foreach($rows as $row) {
							$html .= '<tr class="">';
								$html .= '<td>'.$row['fnamn'].'</td>';
								$html .= '<td>'.$row['enamn'].'</td>';
								$html .= '<td>'.$row['epost'].'</td>';
								$html .= '<td>'.$row['postnummer'].'</td>';
								$html .= '<td>'.$row['ort'].'</td>';
								$html .= '<td>'.date('Y-m-d',strtotime($row['utc_created'], time())).'</td>';
								$html .= '<td>';
								// show reservations
								$ids = explode(',',$row['plugin_kursbokning_kurser_id']);
								foreach($ids as $id) {
									$r = $kursbokning->getKursbokningKursId($id);		
									$html .= '<a href="admin.php?t=reservation_find&plugin_kursbokning_kurs_id='.$r['plugin_kursbokning_kurs_id'].'">'. $r['title'] .'</a> | ';
								}
								$html .= '</td>';
								$html .= '<td style="width:5%;text-align:center;"><a class="link_form_edit" href="admin.php?t=reservation_edit&plugin_kursbokning_kurs_anmalan_id='.$row['plugin_kursbokning_kurs_anmalan_id'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;" title="'.$row['fnamn'].' '.$row['enamn'].'"></span></a></td>';
							$html .= '</tr>';
						}
					$html .= '</tbody>';
				}
			$html .= '</table>';			
			echo $html;
					
			echo '</div>';
		echo '</div>';
		
	break;


	case 'reservation_edit':
	
		$plugin_kursbokning_kurs_anmalan_id = isset($_GET['plugin_kursbokning_kurs_anmalan_id']) && is_numeric($_GET['plugin_kursbokning_kurs_anmalan_id']) ? $_GET['plugin_kursbokning_kurs_anmalan_id'] : null;
		if(!$plugin_kursbokning_kurs_anmalan_id) {die;};		
		
		?>
		
		<div class="admin-area-outer">
			<div class="admin-area-inner">
			
			<input type="hidden" id="plugin_kursbokning_kurs_anmalan_id" value="<?php echo $plugin_kursbokning_kurs_anmalan_id;?>">

			<?php
			$result = $kursbokning->getKursbokningKurserAnmalanId($plugin_kursbokning_kurs_anmalan_id);		
			if(!$result) { die();}
			
			$utc_created = ($result['utc_created']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_created'], $dtz, 'Y-m-d H:i:s')) : null;
			$utc_admitted = ($result['utc_admitted']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_admitted'], $dtz, 'Y-m-d H:i:s')) : null;
			$utc_confirmed = ($result['utc_confirmed']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_confirmed'], $dtz, 'Y-m-d H:i:s')) : null;
			$utc_canceled = ($result['utc_canceled']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_canceled'], $dtz, 'Y-m-d H:i:s')) : null;
			$utc_exported = ($result['utc_exported']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_exported'], $dtz, 'Y-m-d H:i:s')) : null;
			$utc_modified = ($result['utc_modified']>'2000-01-01 00:00') ? new DateTime(utc_dtz($result['utc_modified'], $dtz, 'Y-m-d H:i:s')) : null;
			?>

			<h3 class="heading">Redigera anmälan</h3>
			
			<div style="width:100%;">
				<div style="width:50%;float:left;">
			
					<table class="reservation_edit" style="width:98%;margin-top:20px;">
						<tr>
							<td style="vertical-align:top;">
							<label for="fnamn">Förnamn</label><br />
							<input type="text" id="fnamn" class="text" value="<?php echo $result['fnamn']; ?>" disabled />
							</td>
							<td style="vertical-align:top;">
							<label for="enamn">Efternamn</label><br />
							<input type="text" id="enamn" class="text" value="<?php echo $result['enamn']; ?>" disabled />
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="personnummer">Personnummer</label><br />
							<input type="text" id="personnummer" value="<?php echo $result['personnummer'] ?>" disabled />
							</td>
							<td>
							<label for="kod">Bokningskod</label><br />
							<input type="text" id="kod" value="<?php echo $result['kod'] ?>" disabled />
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;" colspan="2">
							<label for="epost">Epost</label><br />
							<input type="text" id="epost" class="text" value="<?php echo $result['epost']; ?>" disabled />
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="telefon">Telefon</label><br />
							<input type="text" id="telefon" class="text" value="<?php echo $result['telefon']; ?>" disabled />
							</td>
							<td style="vertical-align:top;">
							<label for="mobil">Mobil</label><br />
							<input type="text" id="mobil" class="text" value="<?php echo $result['mobil']; ?>" disabled />
							</td>
						</tr>				
						<tr>
							<td style="vertical-align:top;">
							<label for="adress">Adress</label><br />
							<input type="text" id="adress" class="text" value="<?php echo $result['adress']; ?>" disabled />
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="postnummer">Postnummer</label><br />
							<input type="text" id="postnummer" class="text" value="<?php echo $result['postnummer']; ?>" disabled />
							</td>
							<td style="vertical-align:top;">
							<label for="ort">Ort</label><br />
							<input type="text" id="ort" class="text" value="<?php echo $result['ort']; ?>" disabled />
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="lan">Län</label><br />
							<input type="text" id="lan" class="text" value="<?php echo $result['lan']; ?>" disabled />
							</td>
							<td style="vertical-align:top;">
							<label for="kommun">Kommun</label><br />
							<input type="text" id="kommun" class="text" value="<?php echo $result['kommun']; ?>" disabled />
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="country">Land</label><br />
							<input type="text" id="country" class="text" value="<?php echo $result['country']; ?>" disabled />
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="organisation">Organisation</label><br />
							<input type="text" id="organisation" class="text" value="<?php echo $result['organisation']; ?>" disabled />
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;" colspan="2">
							<label for="fakturaadress">Fakturaadress</label><br />
							<textarea id="fakturaadress" class="field" disabled><?php echo $result['fakturaadress']; ?></textarea>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="utc_created">Datum kursanmälan</label><br />
							<input type="text" id="utc_created" class="text" value="<?php echo $utc_created->format('Y-m-d'); ?>" disabled />
							</td>
							<td style="vertical-align:top;">
							<label for="utc_admitted">Datum antagen</label><br />
							<input type="text" id="utc_admitted" class="text" value="<?php if($utc_admitted) { echo $utc_admitted->format('Y-m-d');} ?>" disabled />
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="utc_confirmed">Datum bekräftad</label><br />
							<input type="text" id="utc_confirmed" class="text" value="<?php if($utc_confirmed) { echo $utc_confirmed->format('Y-m-d');} ?>" disabled />
							</td>
							<td style="vertical-align:bottom;">							
							<label for="utc_canceled">Datum återbud</label><br />
							<input type="text" id="utc_canceled" class="text" value="<?php if($utc_canceled) { echo $utc_canceled->format('Y-m-d');} ?>"  disabled />
							<span class="toolbar" style="float:right;"><button id="btn_toggle_fields"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></button></span>
							</td>
						</tr>
					</table>
									
				</div>
				
				<div style="width:50%;float:right;">
					<table class="reservation_edit" style="width:98%;margin-top:20px;">
						<tr>
							<td style="vertical-align:top;">
							<label for="questions">Frågor</label><br />
							<textarea id="questions" style="width:100%;min-height:300px;max-height:350px;overflow:scroll;padding:10px;" disabled><?php echo $result['questions']; ?></textarea>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<label for="files">Filer</label><br />
							
							<?php
							
							if(strlen($result['files']) > 0) {
							
							
								$row = $kursbokning->getKursbokningId($result['plugin_kursbokning_id']);								
								$path = $row ? $row['file_path'] : '';
						
								//if path is set make sure it ends with slash
								$t = substr($path, -1);
								$path = $t == '/' ? $path : $path . '/';

								if(substr($path, 0, 7) == "file://") {
									
									echo "<i>UNC sökväg(file://) - filer öppnas normalt inte pga säkerhetsskäl. Prova att kopiera länkadressen och öppna i ny flik.</i>";
								}
								
								$files = explode(",",$result['files']);
								
								echo '<ul id="uploaded_files">';
								foreach($files as $file) {
									echo '<li><a href="'.$path.$file.'" target="_blank">'.$file.'</a></li>';
								
								}
								echo '</ul>';
							
							}
							
							?>
							
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;">
							<h3 class="heading">Kursbokning / anmälan till följande kurs</h3>
							<?php 
							$id = 0;
							$ids = explode(',',$result['plugin_kursbokning_kurser_id']);
							foreach($ids as $id) {
								$r = $kursbokning->getKursbokningKursId($id);		
								echo '<p><span class="course_title">'.$r['title'].' ('.date('Y-m-d',strtotime($r['utc_start'])).')<span></p>';
							}							
							?>
							</td>
						</tr>
					</table>
				</div>

			</div>
			
			<div style="clear:both;"></div>
			
			<div style="width:100%;">
			
				<div style="width:50%;float:left;">
					<table class="reservation_edit" style="width:98%;margin-top:20px;">
						<tr>
							<td style="vertical-align:top;">
							<label for="log">Log</label><br />
							<div style="width:100%;min-height:70px;max-height:150px;overflow-y:scroll;background:#FAFAFA;padding:10px;border:1px solid black;"><?php echo $result['log']; ?></div>
							<textarea id="log" style="display:none;" disabled><?php echo $result['log']; ?></textarea>
							</td>
						</tr>
					</table>
				</div>
				<div style="width:50%;float:left;">
					<table class="reservation_edit" style="width:98%;margin-top:20px;">
						<tr>
							<td style="vertical-align:top;">
							<p>
								Anmäld via IP: <?php echo $result['ip']; ?>
							</p>
							<p>
								Browser agent: <?php echo $result['agent']; ?>
							</p>
							<p>
								Senast redigerad: <?php if($utc_modified) { echo $utc_modified->format('Y-m-d');} ?>
							</p>
							<p>
								Exporterad: <?php if($utc_exported) { echo $utc_exported->format('Y-m-d');} ?>
							</p>
							</td>
						</tr>
					</table>
				</div>
			
			</div>

			<div style="clear:both;"></div>
			
			<div style="width:100%;">
			
					<table class="reservation_edit" style="width:98%;margin-top:20px;">
						<tr>
							<td style="vertical-align:top;">
							<label for="notes">Administrativa noteringar</label><br />
							<textarea id="notes" style="width:100%;min-height:80px;max-height:100px;overflow:scroll;"><?php echo $result['notes']; ?></textarea>
							<div style="width:100%;text-align:right;padding:5px 0;">
								<span id="ajax_spinner_course_reservation_edit" style='display:none'><img src="css/images/spinner.gif"></span>
								<span id="ajax_status_course_reservation_edit" style='display:none'></span>
								<span class="toolbar"><button id="btn_course_reservation_save">Spara</button></span>							
							</div>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top;" colspan="2">
							</td>
						</tr>
						<tr>
							<td style="background:#ffff99;">
							
							<div id="tabs_edit2" style="display:none;">
								
								<ul>
									<li><a href="#course_change">Kursbokning</a></li>
									<li><a href="#course_export">Exportera</a></li>
									<li><a href="#course_confirm_email">Skicka bekräftelse</a></li>
									<li><a href="#course_reservation_delete">Radera bokning</a></li>
								</ul>
								
								<div id="course_change">	
									<?php
									
									$form_id = $result['plugin_kursbokning_id'];
									
									// administrative reservation, field plugin_kursbokning_id is set to 0...
									if($form_id == 0) {
										
										// get this course form for reservations
										$form = $kursbokning->getKursbokningFormId($id);
										
										if($form) {
											$form_id = $form['plugin_kursbokning_id'];
										}
									} 
									
									$kurser = $kursbokning->getKursbokningIdKurser($form_id);
									
									if($kurser) {
										echo '<p><b>Ändra kurs:</b><br />';
										echo '<select name="change_course" id="change_course">';
											echo '<option value=""></option>';
											foreach($kurser as $kurs) {
												echo '<option value="'.$kurs['plugin_kursbokning_kurs_id'] .'">'.$kurs['title'].' ('.date('Y-m-d',strtotime($kurs['utc_start'])).')</option>';
											}
										echo '</select><span class="toolbar">&nbsp;<button id="btn_course_reservation_change_course_save">Spara ändring</button></span></p>';
									}
									?>								
								</div>


								

								<div id="course_export">	


									<select name="rapport" id="rapport">
									<?php include_once 'inc.rapport.php'; ?>
									</select>
									<span class="toolbar"><button type="submit" id="btn_rapport_one" data-plugin_kursbokning_kurs_anmalan_id="<?php echo $plugin_kursbokning_kurs_anmalan_id; ?>">Exportera</button></span>


								</div>
								
								
								<div id="course_confirm_email">	

									<span class="toolbar"><button id="btn_course_confirm_email">Skicka bekräftelse</button></span> <i>skickar en ny bekräftelse på bokning till angiven epost</i>
									<span id="ajax_spinner_course_confirm" style='display:none'><img src="css/images/spinner.gif"></span>
									<span id="ajax_status_course_confirm" style='display:none'></span>

								</div>
								
								
								<div id="course_reservation_delete">	
								
									<span class="toolbar"><button id="btn_course_reservation_delete">Radera bokning</button></span> <i>radera aktuell bokning (går ej att ångra)</i>
								
								</div>
								
							</div>
							
							</td>
						</tr>
					</table>
				
			
			</div>

			
			<div style="clear:both;"></div>
			
			
			</div>
		</div>
		
		
		<?php
	
	break;

}
?>

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" id="users_id" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">

<?php
// load javascript files
foreach ( $js_files as $js ) {
	echo "\n".'<script src="'.$js.'"></script>';
}
?>


<script>
	// enable log
	jQuery.fn.log = function (msg) {
	  console.log("%s: %o", msg, this);
	  return this;
	};	
</script>

<?php include_once CMS_ABSPATH . '/cms/includes/inc.footer_cms.php'; ?>

</body>
</html>