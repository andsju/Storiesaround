

$(document).ready(function() {

	var pages_id = $("#pages_id").val();
	var token = $("#token").val();
	var users_id = $("#users_id").val();
	var role_cms = $("#role_cms").val();

	var language = $("#site_language").val();
	var lang = 0;
	switch(language) {
		case "english" :
			lang = 0;
		break;
		case "swedish" :
			lang = 1;
		break;
		default :
			lang = 0;
		break;
	}
	const l = lang;
	var languages = [];
	languages["Search"] = ["Search", "Sök"];
	languages["search_page"] = ["Search page", "Sök sida"];
	languages["search_page_not_found"] = ["No pages found", "Ingen sida hittades"];
	languages["search_page_click_search"] = ["Click Search button to se all matches", "Klicka Sök för att se alla träffar"];

	var limit_page_result = 10;

	var menu = "#menu";
	var position = {my: "left top", at: "left bottom"};
	$(menu).hide().menu({
		position: position,
		blur: function() {
			$(this).menu("option", "position", position);
		},
		focus: function(e, ui) {
			if ($(menu).get(0) !== $(ui).get(0).item.parent().get(0)) {
				console.log("A");
				$(this).menu("option", "position", {my: "left top", at: "right top"});
			}
		}
	});
	
	$(menu).css("visibility", "visible").fadeIn(100).show();
	
	$( ".datepicker" ).datepicker();
	$( ".slider" ).slider({
		value: 40,
		orientation: "horizontal",
		range: "min",
		animate: true
	});
	
	$( ".tabs" ).tabs();
	
	$(".colorbox_edit").colorbox({
		width:"100%", 
		height:"100%",
		reposition: false,
		transition:"none",
		iframe:true, 
		onClosed:function(){ 
			location.reload(true); 
		}
	});

	$(".colorbox_show").colorbox({
		width:"100%", 
		height:"100%",
		reposition: false,
		transition:"none",
		iframe:true, 
		onClosed:function(){ 

		}
	});
	
	$(".colorbox_login").colorbox({
		width:"100%", 
		height:"100%",
		reposition: false,
		iframe:false, 
		transition:"none",
		onClosed:function(){ 
			location.reload(true); 
		}
	});
			
	$('.ajax_history').click(function(event){
		event.preventDefault();
		var action = "pages_history";
		var token = $("#token").val();
		var pages_id = $(this).attr('id'); 
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_history').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_history').hide()",700)},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id,
			success: function(message){
				ajaxReplyHistory(message,'#ajax_status_history');
			},
		});
	});
			
	jQuery("abbr.timeago").timeago();

	$('#btn_pages_search').click(function(event){
		event.preventDefault();
		var action = "pages_search_extended";
		var token = $("#token").val();
		var pages_s = $("#pages_s").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		var again = "false";
		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_pages_search').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_pages_search').hide()",700)},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_s=" + pages_s + "&again=" + again,
			success: function(newdata){
				$("#pages_search_result").empty().append(newdata).hide().fadeIn('fast');
			},
		});
	});

	$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_create: function() {
		  this._super();
		  this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
		},
		_renderMenu: function( ul, items ) {
		  var that = this,
			currentCategory = "";
			var max = 8;
			var count = 0;
		  $.each( items, function( index, item ) {
			var li;
			if ( item.category != currentCategory ) {
			  ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
			  currentCategory = item.category;
			}
			li = that._renderItemData( ul, item );
			if ( item.category ) {
			  li.attr( "aria-label", item.category + " : " + item.label );
			}
			if (count > max) {
				ul.append( "<li class='ui-autocomplete-category'>"+ languages["search_page_click_search"][lang] +" ("+ items.length + ")</li>" );
				return false;
			}
			count++;
		  });
		}
	});	

	$("#search-page").catcomplete({
		minLength: 2,
		delay: 200,
		source: function (request, response) {
			var cms_dir = $("#cms_dir").val();
			var ajax_url = cms_dir +'/cms/pages_ajax.php';
			var limit_tree = $('input:checkbox[name=search-page-limit-tree]').is(':checked') ? 1 : 0;
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_search').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_search').hide()", 500)},
				type: "POST",
				url: ajax_url,
				dataType: "json",
				data: {
					action: "pages_search_extended_summary",
					token: $("#token").val(),
					pages_id: pages_id,
					limit_tree: limit_tree,
					s: request.term
				},
				success: function (data) {
					data = data.length > 0 ? data : [{title: languages["search_page_not_found"][lang], category: "", pages_id: "0"}];
					response($.map(data, function (item, index) {						
						return {
							label: item.title,
							category: item.category,
							id: item.pages_id,
							index: item.length
						}
					}));
				}
			});
		},
		select: function (event, ui) {
			$("input#pid").val(ui.item.id)
		}
	});

	$("#btn-site-search-page").click(function() {
		var pid = $("#pid").val();
		var search = $("#search-page").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		var limit_tree = $('input:checkbox[name=search-page-limit-tree]').is(':checked') ? 1 : 0;
		var pages_id = $("#pages_id").val();

		if (pid > 0) {
			window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/cms/pages.php?id=" + pid;
		} else {

			if (pages_id > 0) {
				var limit_start = 0;
				var action = "pages_search_extended";
				var token = $("#token").val();
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_pages_search').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_pages_search').hide()",700)},
					type: 'POST',
					url: ajax_url,
					dataType: "text",
					data: {
						action: action,
						token: token,
						pages_id: pages_id,
						limit_tree: limit_tree,
						limit_start: limit_start,
						s: search
					},
					success: function(data){
						$("#pages_search_result").empty().show().append(data);
						var total = parseInt($("#search_results_total").val());
						if (total > limit_start + 10) {
							$("#btn-site-search-page-more").show();
						}
						$("#pages_search_result_start").val(limit_start + 10);
					}
				});
			}
		}
	});
	

	$("#btn-site-search-page-more").click(function() {

		var search = $("#search-page").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		var limit_tree = $('input:checkbox[name=search-page-limit-tree]').is(':checked') ? 1 : 0;
		var pages_id = $("#pages_id").val();

		if (pages_id > 0) {
			var limit_start = parseInt($("#pages_search_result_start").val());
			var action = "pages_search_extended";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_pages_search').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_pages_search').hide()",700)},
				type: 'POST',
				url: ajax_url,
				dataType: "text",
				data: {
					action: action,
					token: token,
					pages_id: pages_id,
					limit_tree: limit_tree,
					limit_start: limit_start,
					s: search
				},
				success: function(data){
					$("#pages_search_result").append(data);
					var total = parseInt($("#search_results_total").val());
					if (total < limit_start + 10) {
						$("#btn-site-search-page-more").hide();
					}
					$("#pages_search_result_start").val(limit_start + 10);
				}
			});
		}
	});

	$( ".toolbar button" ).button({
	});
	
	$('#stories_event_previous').click(function(event){
		event.preventDefault();
		var action = "event_stories";
		var token = $("#token").val();
		var pages_id = $("#pages_id").val(); 
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		var period = 'previous';
		var search = $('#stories_event_dates_filter').val();
		var datetrigger = $('#stories_events').children('.story-event').first().attr('id');

		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_stories_event_previous').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_event_previous').hide()",700)},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&datetrigger=" + datetrigger + "&period=" + period + "&search=" + search,
			success: function(newdata){
				$("#stories_events").prepend(newdata).hide().fadeIn('fast');
			},
		});
	});

	$('#stories_event_next').click(function(event){
		event.preventDefault();
		var action = "event_stories";
		var token = $("#token").val();
		var pages_id = $("#pages_id").val(); 
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		var period = 'next';
		var search = $('#stories_event_dates_filter').val();
		var datetrigger = $('#stories_events').children('.story-event').last().attr('id');

		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_stories_event_next').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_event_next').hide()",700)},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&datetrigger=" + datetrigger + "&period=" + period + "&search=" + search,
			success: function(newdata){
				$("#stories_events").append(newdata).hide().fadeIn('fast');
			},
		});
	});

	
	$( "#stories_events" ).delegate( ".story-event-history", "click", function(event) {
		event.preventDefault();
		$(this).attr('style','opacity:1;');
	});
	
	
	$("#navigation-pages-children").change(function(){
		if ($(this).val()!='') {
			window.location.href=$(this).val();
		}
	});
	
	
	if ($('#stories-child-masonry').length > 0) {
		var $container = $('#stories-child-masonry').masonry();
		$container.imagesLoaded( function() {
			$container.masonry({
				itemSelector : '.item',
				columnWidth: '.grid-sizer',
				transitionDuration: '0.3s'
		  });
	  });
	}
	if ($('#stories-promoted-masonry').length > 0) {
		var $container = $('#stories-promoted-masonry').masonry();
		$container.imagesLoaded( function() {
			$container.masonry({
				itemSelector : '.item',
				columnWidth : 20,
				gutter: 20,
				isResizable: false,
				transitionDuration: '0.3s'
		  });
	  });

	}
	
	$('#preview_theme').change(function(event){
		event.preventDefault();
		var action = "preview_theme";
		var token = $("#token").val();
		var theme = $("#preview_theme").val();
		$.ajax({
			type: 'POST',
			url: 'pages_ajax.php',
			data: { action: action, token: token, theme: theme }, 
			success: function(){
				location.reload(true);
			},
		});
	});
	
	$(".dropdown").click(function() {
		$(this).find('ul').fadeToggle(100);
	});

	$("#link_user").colorbox({
		width:"100%", 
		height:"100%",
		reposition: false,
		iframe:true, 
		transition:"none",
		onClosed:function(){ 
		}
	});

	$('#themes_preview').change(function() {
		var theme = $('#themes_preview').find(':selected').text();
		var cms_dir = $("#cms_dir").val();
		var cms_dir = $("#cms_dir").val();
		$('#themes-css').attr('href', cms_dir+'/cms/themes/'+theme+'/style.css');
	});
	
	$( "#browser_size_fixed" ).change(function() {
		var w = $( "#browser_size_fixed option:selected").val();
		if(w < 320) { 
			w = 480;
		}
		console.log(w);
		var h = screen.height-50;
		window.resizeTo(w, h);
		window.focus()
	});
	
	
	
	setInterval(online, 60000);
	
	$(window).resize(addMobileMenu());  	
	
	$("#menutoggle").click(function() {		
		$(".flexnav").flexNav({ 'animationSpeed' : 0});	
		//$("#nav-site-navigation-vertical").show();
		$("#site-navigation-mobile").show();
	});

	$("#search-site-icon").click(function(event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/pages/sok";
	});	
		
	$("#site-login-icon").click(function(event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/cms/login.php";
	});	

	// window resize
	$(window).resize(function() {

		var $videos = $(".grid-video iframe");
		var $fluidEl = $(".grid-video");
		var newWidth = $fluidEl.width();
		$videos.each(function() {
			$(this)
			.width(newWidth)
			.height(newWidth * $(this).data('ratio'));
		});
		equalheight('div.grid-cell');
	}).resize();


	if ($("#stories_equal_height").val() == 1) {
		equalheight('div.stories-cell');
		equalheight('div.stories-wrapper');
	}
	
	if (users_id && isNumeric(role_cms)) {
		$(".editable").each(function() {  
			$(this).wrap( "<form style=\"position:relative\"></form>" );
		});
		$(".editable_row").each(function() {  
			$(this).wrap( "<form></form>" );
		});

		tinymce.init({
			selector: 'h1.editable',
			inline: true,
			toolbar: 'undo redo | save',
			plugins: 'save',
			menubar: false,
			save_enablewhendirty: true,
			save_onsavecallback: function() {		
				var title = $("#content-title").text();
				var action = "update_title_only";
				if (title.length > 0){
					$.ajax({				
						type: 'POST',
						url: 'pages_edit_ajax.php',
						data: { 
							action: action, token: token, pages_id: pages_id, users_id: users_id, title: title
						},
						success: function(result){
							ajaxReplyInline($('#content-title').closest("form"), result);
						}
					});
				}
			}
		});

		tinymce.init({
			selector: 'div.editable_row',
			inline: true,
			toolbar: 'undo redo | save',
			plugins: 'save',
			menubar: false,
			save_enablewhendirty: true,
			save_onsavecallback: function() {		
				var author = $("#content-author").text();
				var action = "update_author_only";
				if (author.length > 0){
					$.ajax({				
						type: 'POST',
						url: 'pages_edit_ajax.php',
						data: { 
							action: action, token: token, pages_id: pages_id, users_id: users_id, author: author
						},
						success: function(result){
							ajaxReplyInline($('#content-author').closest("form"), result);
						}
					});
				}
			}
		});
		  
		tinymce.init({
			selector: 'div.editable',
			inline: true,
			plugins: [
			  'lists link image anchor',
			  'searchreplace visualblocks ',
			  'media contextmenu paste save'
			],
			menubar: false,
			toolbar: 'searchreplace undo redo | styleselect | bold italic | bullist numlist outdent indent | link image | save',
			save_enablewhendirty: true,
			save_onsavecallback: function() {				
				var content = tinyMCE.get('content-html').getContent();
				var action = "update_content_only";
				if (content.length > 0){
					$.ajax({				
						type: 'POST',
						url: 'pages_edit_ajax.php',
						data: { 
							action: action, token: token, pages_id: pages_id, users_id: users_id, content: content
						},
						success: function(result){
							ajaxReplyInline($('#content-html').closest("form"), result);
						}
					});
				}
			},		
			content_css: [
				]
		});
		
	}

	//replace_image_path('/content/', '/somefolder/content/');

});

function addMobileMenu() {
	var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	var logged_in = document.querySelector("#user-toolbar");
	console.log("addMobileMenu", w);
	if (w <= 767) {	
		console.log("767");
		var cms_dir = $("#cms_dir").val();
		var newdata = '<img class="mobile-menu-icon" src="'+cms_dir+'/content/favicon.png" id="site-icon">';
		newdata += '<img class="mobile-menu-icon" src="'+cms_dir+'/cms/css/images/icon_search.png" style="" id="search-site-icon">';
		newdata += '<img class="mobile-menu-icon" src="'+cms_dir+'/cms/css/images/icon_login.png" id="site-login-icon" >';
				
		$(".mobile-buttons").append(newdata);
		$("#site-navigation-mobile").addClass("flexnav");
		$("#site-navigation-mobile").show();
		$(".flexnav").flexNav({ 'animationSpeed' : 0});

		if (logged_in) {
			$("#site-navigation-mobile-wrapper").css("margin-top", "40px");
			$("#search_site").css("margin-top", "30px");
		}
	}
}

function refresh() {
	var w = $( "#browser_size" ).slider( "value" );
	console.log(w);
	$( "body" ).css( "width", +w+"px");
	$( "#browser_size_px" ).text( "Browser width: " + w + "px");
}

function get_random_int(min,max) {
	return Math.floor(Math.random()*(max-min+1)+min);
}		

function calendar_preview(id) {
	var cms_dir = $("#cms_dir").val();
	w=window.open(cms_dir+'/cms/calendar_categories_view.php?id='+id,'','width=500,height=600,menubar=no,location=no,directories=no,toolbar=no');
	w.focus();
}

function online() {
	var action = "online";
	var token = $("#token").val();
	var cms_dir = $("#cms_dir").val();
	$.ajax({				
		type: 'POST',
		url: cms_dir+'/cms/online_ajax.php',
		data: "action=" + action + "&token=" + token,
		success: function(newdata){	
		},
	});
}	

// function to temporary replace all matching images src path or part of path to new path
function replace_image_path(replace_pattern, new_pattern) {
    $('img').each(function(){
		var $this = $(this);
		$this.attr('src',$this.attr('src').replace(replace_pattern,new_pattern));
    });
}

function getFileExtension(filename) {
	if (!filename) {return}
	return filename.split('.').pop();
}
function getFileBaseName(filename) {
	if (!filename) {return}
	return filename.split('.').shift();
}
