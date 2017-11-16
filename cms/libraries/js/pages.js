/*
jquery behaviours and functions
*/
$(document).ready(function() {
	
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
	
	$("body").on("click", "#btn_pages_search_again", function(event){
		event.preventDefault();
		var action = "pages_search_extended";
		var token = $("#token").val();
		var pages_s_again = $("#pages_s_again").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir +'/cms/pages_ajax.php';
		var again = "true";
		
		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_pages_search').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_pages_search').hide()",700)},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_s_again=" + pages_s_again + "&again=" + again,
			success: function(newdata){
				$("#pages_search_result").empty().append(newdata).hide().fadeIn('fast');
			},
		});
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
	
	

	
	
	
	setTimeout(delay_swap_site_feed, 3000);
	setInterval(online, 60000);
	
	$(window).resize(addMobileMenu());  	
	
	$("#menutoggle").click(function() {		
		$(".flexnav").flexNav({ 'animationSpeed' : 0});	
		//$("#nav-site-navigation-vertical").show();
		$("#site-navigation-mobile").show();
	});
		
	$("#search-site").click(function(event) {
		event.preventDefault();
		//$("#nav-site-navigation-vertical").toggle();
		$("#site-navigation-mobile").toggle();
		$("#search_site").toggle();
	});	
	
	$("#fb").click(function(event) {
		event.preventDefault();
		window.location.href = "http://sunet.se";
		
	});	
	
	//replace_image_path('/content/', '/somefolder/content/');
	
});


function addMobileMenu() {
	var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	if (w <= 767) {	
		var cms_dir = $("#cms_dir").val();

		var newdata = ""	
		newdata += 		'<img class="mobile-menu-icon" src="'+cms_dir+'/content/favicon.png" id="site-icon" />';
		newdata += 		'<img class="mobile-menu-icon" src="'+cms_dir+'/cms/css/images/search-icon-32.png" style="" id="search-site" />';
		//newdata += 		'<img class="mobile-menu-icon" src="'+cms_dir+'/cms/css/images/FB-f-Logo__white_29.png" id="fb" />';
		
		$(".mobile-buttons").append(newdata);
		$("#site-navigation-mobile").addClass("flexnav");
		$("#site-navigation-mobile").show();
		$(".flexnav").flexNav({ 'animationSpeed' : 0});

		var logged_in = document.querySelector("#user_navigation");
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

function swap_site_feed(newdata) {
	if(newdata.length) {
		var data = jQuery.parseJSON(newdata);		
		$i = get_random_int(0,data.length-1);		
		$('#site-feed').fadeTo('fast', 0.5, function() {
			$(this).html("<div id='site-feed-inner'><a href="+data[$i].link+"><div><h5>"+data[$i].title+"</h5><p>"+data[$i].content+"</p></a></div>");
		}).fadeTo('fast', 1);
	}
}

function delay_swap_site_feed() {
	var action = "site_feed";
	var token = $("#token").val();
	var pages_id = $("#pages_id").val();
	var cms_dir = $("#cms_dir").val();
	var ajax_url = cms_dir +'/cms/pages_ajax.php';
	if (pages_id > 0) {
		$.ajax({				
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id,
			success: function(newdata){	
				swap_site_feed(newdata);
				var ajax_swap_site_feed = 5000;
				setInterval( function() { swap_site_feed(newdata); }, ajax_swap_site_feed);					
			},
		});
		
	}

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
