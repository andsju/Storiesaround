$(document).ready(function () {

	$("#dialog_edit").dialog({
		autoOpen: false,
		modal: true
	});


	var pages_id = $("#pages_id").val();
	var token = $("#token").val();
	var users_id = $("#users_id").val();
	var role_cms = $("#role_cms").val();
	var language = $("#site_language").val();
	var lang = 0;
	switch (language) {
		case "english":
			lang = 0;
			break;
		case "swedish":
			lang = 1;
			break;
		default:
			lang = 0;
			break;
	}
	const l = lang;
	var languages = [];
	languages["Search"] = ["Search", "Sök"];
	languages["search_page"] = ["Search page", "Sök sida"];
	languages["search_page_not_found"] = ["No pages found", "Ingen sida hittades"];
	languages["search_page_click_search"] = ["Click Search button to se all matches", "Klicka Sök för att se alla träffar"];
	languages["More"] = ["More pages...", "Fler sidor..."];

	var limit_page_result = 10;

	var menu = "#menu";
	var position = {
		my: "left top",
		at: "left bottom"
	};
	$(menu).hide().menu({
		position: position,
		blur: function () {
			$(this).menu("option", "position", position);
		},
		focus: function (e, ui) {
			if ($(menu).get(0) !== $(ui).get(0).item.parent().get(0)) {
				$(this).menu("option", "position", {
					my: "left top",
					at: "right top"
				});
			}
		}
	});

	$(menu).css("visibility", "visible").fadeIn(100).show();

	$(".datepicker").datepicker();
	$(".slider").slider({
		value: 40,
		orientation: "horizontal",
		range: "min",
		animate: true
	});

	$(".tabs").tabs();

	$(".colorbox_edit").colorbox({
		width: "100%",
		height: "100%",
		reposition: false,
		transition: "none",
		iframe: true,
		onClosed: function () {
			location.reload(true);
		}
	});

	$(".colorbox_show").colorbox({
		width: "100%",
		height: "100%",
		reposition: false,
		transition: "none",
		iframe: true,
		onClosed: function () {

		}
	});

	$(".colorbox_login").colorbox({
		width: "100%",
		height: "100%",
		reposition: false,
		iframe: false,
		transition: "none",
		onClosed: function () {
			location.reload(true);
		}
	});

	$('.ajax_history').click(function (event) {
		event.preventDefault();
		var action = "pages_history";
		var token = $("#token").val();
		var pages_id = $(this).attr('id');
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir + '/cms/pages_ajax.php';
		$.ajax({
			beforeSend: function () {
				loading = $('#ajax_spinner_history').show()
			},
			complete: function () {
				loading = setTimeout("$('#ajax_spinner_history').hide()", 700)
			},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id,
			success: function (message) {
				ajaxReplyHistory(message, '#ajax_status_history');
			},
		});
	});

	jQuery("abbr.timeago").timeago();

	$('#btn_pages_search').click(function (event) {
		event.preventDefault();
		var action = "pages_search_extended";
		var token = $("#token").val();
		var pages_s = $("#pages_s").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir + '/cms/pages_ajax.php';
		var again = "false";
		$.ajax({
			beforeSend: function () {
				loading = $('#ajax_spinner_pages_search').show()
			},
			complete: function () {
				loading = setTimeout("$('#ajax_spinner_pages_search').hide()", 700)
			},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_s=" + pages_s + "&again=" + again,
			success: function (newdata) {
				$("#pages_search_result").empty().append(newdata).hide().fadeIn('fast');
			},
		});
	});

	$.widget("custom.catcomplete", $.ui.autocomplete, {
		_create: function () {
			this._super();
			this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
		},
		_renderMenu: function (ul, items) {
			var that = this,
				currentCategory = "";
			var max = 8;
			var count = 0;
			$.each(items, function (index, item) {
				var li;
				var category;
				if (item.category != currentCategory) {
					category = item.category.length > 0 ? item.category : languages["More"][lang];
					ul.append("<li class='ui-autocomplete-category'>" + category + "</li>");
					currentCategory = item.category;
				}
				li = that._renderItemData(ul, item);
				if (item.category) {
					li.attr("aria-label", item.category + " : " + item.label);
				}
				if (count > max) {
					ul.append("<li class='ui-autocomplete-category'>" + languages["search_page_click_search"][lang] + " (" + items.length + ")</li>");
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
			var ajax_url = cms_dir + '/cms/pages_ajax.php';
			var limit_tree = $('input:checkbox[name=search-page-limit-tree]').is(':checked') ? 1 : 0;
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function () {
					loading = $('#ajax_spinner_search').show()
				},
				complete: function () {
					loading = setTimeout("$('#ajax_spinner_search').hide()", 500)
				},
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
					data = data.length > 0 ? data : [{
						title: languages["search_page_not_found"][lang],
						category: "",
						pages_id: "0"
					}];
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
			$("input#pid").val(ui.item.id);
			searchPagesContent();
		}
	});

	$("#btn-site-search-page").click(function () {
		searchPagesContent();
	});

	$("#btn-site-search-page-more").click(function () {

		var search = $("#search-page").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir + '/cms/pages_ajax.php';
		var limit_tree = $('input:checkbox[name=search-page-limit-tree]').is(':checked') ? 1 : 0;
		var pages_id = $("#pages_id").val();

		if (pages_id > 0) {
			var limit_start = parseInt($("#pages_search_result_start").val());
			var action = "pages_search_extended";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function () {
					loading = $('#ajax_spinner_pages_search').show()
				},
				complete: function () {
					loading = setTimeout("$('#ajax_spinner_pages_search').hide()", 700)
				},
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
				success: function (data) {
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

	$(".toolbar button").button({});

	$('#stories_event_previous').click(function (event) {
		event.preventDefault();
		var action = "event_stories";
		var token = $("#token").val();
		var pages_id = $("#pages_id").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir + '/cms/pages_ajax.php';
		var period = 'previous';
		var search = $('#stories_event_dates_filter').val();
		var datetrigger = $('#stories_events').children('.story-event').first().attr('id');

		$.ajax({
			beforeSend: function () {
				loading = $('#ajax_spinner_stories_event_previous').show()
			},
			complete: function () {
				loading = setTimeout("$('#ajax_spinner_stories_event_previous').hide()", 700)
			},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&datetrigger=" + datetrigger + "&period=" + period + "&search=" + search,
			success: function (newdata) {
				$("#stories_events").prepend(newdata).hide().fadeIn('fast');
			},
		});
	});

	$('#stories_event_next').click(function (event) {
		event.preventDefault();
		var action = "event_stories";
		var token = $("#token").val();
		var pages_id = $("#pages_id").val();
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir + '/cms/pages_ajax.php';
		var period = 'next';
		var search = $('#stories_event_dates_filter').val();
		var datetrigger = $('#stories_events').children('.story-event').last().attr('id');

		$.ajax({
			beforeSend: function () {
				loading = $('#ajax_spinner_stories_event_next').show()
			},
			complete: function () {
				loading = setTimeout("$('#ajax_spinner_stories_event_next').hide()", 700)
			},
			type: 'POST',
			url: ajax_url,
			data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&datetrigger=" + datetrigger + "&period=" + period + "&search=" + search,
			success: function (newdata) {
				$("#stories_events").append(newdata).hide().fadeIn('fast');
			},
		});
	});


	$("#stories_events").delegate(".story-event-history", "click", function (event) {
		event.preventDefault();
		$(this).attr('style', 'opacity:1;');
	});


	$("#navigation-pages-children").change(function () {
		if ($(this).val() != '') {
			window.location.href = $(this).val();
		}
	});


	if ($('#stories-child-masonry').length > 0) {
		var $container = $('#stories-child-masonry').masonry();
		$container.imagesLoaded(function () {
			$container.masonry({
				itemSelector: '.item',
				columnWidth: '.grid-sizer',
				transitionDuration: '0.3s'
			});
		});
	}
	if ($('#stories-promoted-masonry').length > 0) {
		var $container = $('#stories-promoted-masonry').masonry();
		$container.imagesLoaded(function () {
			$container.masonry({
				itemSelector: '.item',
				columnWidth: 20,
				gutter: 20,
				isResizable: false,
				transitionDuration: '0.3s'
			});
		});

	}

	$('#preview_theme').change(function (event) {
		event.preventDefault();
		var action = "preview_theme";
		var token = $("#token").val();
		var theme = $("#preview_theme").val();
		$.ajax({
			type: 'POST',
			url: 'pages_ajax.php',
			data: {
				action: action,
				token: token,
				theme: theme
			},
			success: function () {
				location.reload(true);
			},
		});
	});

	$(".dropdown").click(function () {
		$(this).find('ul').fadeToggle(100);
	});

	$("#link_user").colorbox({
		width: "100%",
		height: "100%",
		reposition: false,
		iframe: true,
		transition: "none",
		onClosed: function () {}
	});

	$('#themes_preview').change(function () {
		var theme = $('#themes_preview').find(':selected').text();
		var cms_dir = $("#cms_dir").val();
		var cms_dir = $("#cms_dir").val();
		$('#themes-css').attr('href', cms_dir + '/cms/themes/' + theme + '/style.css');
	});

	$("#browser_size_fixed").change(function () {
		var w = $("#browser_size_fixed option:selected").val();
		if (w < 320) {
			w = 480;
		}
		var h = screen.height - 50;
		window.resizeTo(w, h);
		window.focus()
	});



	setInterval(online, 60000);

	$(window).resize(addMobileMenu());

	$("#menutoggle").click(function () {
		$(".flexnav").flexNav({
			'animationSpeed': 0
		});
		//$("#nav-site-navigation-vertical").show();
		$("#site-navigation-mobile").show();
	});

	$("#search-site-icon").click(function (event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/pages/sok";
	});

	$("#site-login-icon").click(function (event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/cms/login.php";
	});

	$("#site-admin-icon").click(function (event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/cms/admin.php";
	});

	$("#site-edit-icon").click(function (event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		var pages_id = $(this).attr("data-id");
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/cms/pages_edit.php?id=" + pages_id;
	});

	$("#site-user-icon").click(function (event) {
		event.preventDefault();
		var cms_dir = $("#cms_dir").val();
		var users_id = $("#users_id").val();
		window.location.href = location.protocol + "//" + location.hostname + cms_dir + "/cms/users_edit.php?id=" + users_id;
	});

	// window resize
	$(window).resize(function () {

		var $medias = $("#content-html iframe");
		var $fluidEl = $("#content-html");
		var newWidth = $fluidEl.width();
		$medias.each(function () {
			$(this)
				.attr('data-ratio', this.height / this.width)
				.removeAttr('height')
				.removeAttr('width')
				.width(newWidth)
				.height(newWidth * $(this).data('ratio'));
		});

		var $videos = $(".grid-video iframe");
		var $fluidEl = $(".grid-video");
		var newWidth = $fluidEl.width();
		var h = 0;
		$videos.each(function () {
			$(this)
				.width(newWidth)
				.height(newWidth * $(this).data('ratio'));
			h = newWidth * $(this).data('ratio');
		});
		$fluidEl.each(function () {
			$(this)
				//.height(h);
				// setting height crossbrowser
				.css("height", h + "px");
		});

		//equalheight('div.grid-video');
		equalheight('div.grid-cell');

	}).resize();


	if ($("#stories_equal_height").val() == 1) {
		equalheight('div.stories-cell');
		equalheight('div.stories-wrapper');
	}

	$("#user_navigation").delegate("#inline_edit", "click", function () {
		inlineEdit(pages_id, users_id, role_cms, token);
		notifyInlineEdit($(this));
	});

	$("body").delegate("#site-inline-edit-icon", "click", function () {
		inlineEdit(pages_id, users_id, role_cms, token);
		notifyInlineEdit($(this));
	});

	if (!getCookie("cookieAlert")) {
		$("#about-cookies").show();
	}

	$("#btn_about_cookies").click(function () {

		var accepted_cookies = getCookie("cookieAlert");

		if (!accepted_cookies) {
			var action = "accept_cookies";
			var cms_dir = $("#cms_dir").val();
			var ajax_url = cms_dir + '/cms/pages_ajax.php';
			var accept_cookies = "true";
			setCookie("cookieAlert", "true", 180);
			$.ajax({
				type: 'POST',
				url: ajax_url,
				data: {
					action: action,
					token: token,
					accept_cookies: accept_cookies
				},
				success: function (result) {
					if (result) {
						$("#about-cookies").remove();
					}
				}
			});
		}
	});


	//replace_image_path('/content/', '/somefolder/content/');

});

function searchPagesContent() {
	var pid = $("#pid").val();
	var search = $("#search-page").val();
	var cms_dir = $("#cms_dir").val();
	var ajax_url = cms_dir + '/cms/pages_ajax.php';
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
				beforeSend: function () {
					loading = $('#ajax_spinner_pages_search').show()
				},
				complete: function () {
					loading = setTimeout("$('#ajax_spinner_pages_search').hide()", 700)
				},
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
				success: function (data) {
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
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

function notifyInlineEdit(element) {
	element.css("background-color", "lightgreen");
	setTimeout(function () {
		element.css("background-color", "inherit");
	}, 1000);
}

function inlineEdit(pages_id, users_id, role_cms, token) {

	if (users_id > 0 && role_cms > 1) {
		var cms_dir = $("#cms_dir").val();
		var ajax_url = cms_dir + '/cms/pages_edit_ajax.php';

		let checkEditStatus = new Promise((resolve, reject) => {
			var action = "update_inline";
			$.ajax({
				type: 'POST',
				url: ajax_url,
				data: {
					action: action,
					token: token,
					pages_id: pages_id,
					users_id: users_id
				},
				success: function (result) {
					resolve(result);
				}
			});
		});

		checkEditStatus.then((result) => {
			if (!result) {
				$("#dialog_edit").dialog("open");
				$("#dialog_edit").dialog({
					modal: true,
					buttons: {
						Ok: function () {
							$(this).dialog("close");
						}
					}
				});

			} else {

				$(".editable").each(function () {
					$(this).wrap("<form style=\"position:relative\"></form>");
					notifyInlineEdit($(this));
				});
				$(".editable_row").each(function () {
					$(this).wrap("<form></form>");
					notifyInlineEdit($(this));
				});

				tinymce.init({
					selector: 'h1.editable',
					inline: true,
					toolbar: 'undo redo | save',
					plugins: 'save',
					menubar: false,
					save_enablewhendirty: true,
					save_onsavecallback: function () {
						var title = $("#content-title").text();
						var action = "update_title_only";
						if (title.length > 0) {
							$.ajax({
								type: 'POST',
								url: ajax_url,
								data: {
									action: action,
									token: token,
									pages_id: pages_id,
									users_id: users_id,
									title: title
								},
								success: function (result) {
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
					save_onsavecallback: function () {
						var author = $("#content-author").text();
						var action = "update_author_only";
						if (author.length > 0) {
							$.ajax({
								type: 'POST',
								url: ajax_url,
								data: {
									action: action,
									token: token,
									pages_id: pages_id,
									users_id: users_id,
									author: author
								},
								success: function (result) {
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
					style_formats_merge: true,
					style_formats: [{
							title: 'Custom text',
							items: [{
									title: 'Horizont line shadowed <p>',
									block: 'p',
									classes: 'horizont-line'
								},
								{
									title: 'FAQ question <p>',
									block: 'p',
									classes: 'faq-question'
								},
								{
									title: 'FAQ answer <p>',
									block: 'p',
									classes: 'faq-answer'
								},
								{
									title: 'Box shadowed<p>',
									block: 'p',
									classes: 'box-shadowed'
								},
								{
									title: 'Box elevated<p>',
									block: 'p',
									classes: 'box-elevated'
								},
								{
									title: 'Quote emphasize <p>',
									block: 'p',
									classes: 'quote-emphasize'
								},
								{
									title: 'Read more <span>',
									inline: 'span',
									classes: 'read-more'
								},
								{
									title: 'Highlight <span>',
									inline: 'span',
									classes: 'highlight-word'
								},
								{
									title: 'Bigger <span>',
									inline: 'span',
									classes: 'text-bigger'
								},
								{
									title: 'Smaller <span>',
									inline: 'span',
									classes: 'text-smaller'
								},
								{
									title: 'SMALL CAPS <span>',
									inline: 'span',
									classes: 'small-caps'
								},
							]
						},
						{
							title: 'Image',
							items: [{
									title: 'align left 33%',
									selector: 'img',
									styles: {
										'width': '33%',
										'height': 'auto',
										'float': 'left',
										'margin': '0 10px 0 0'
									}
								},
								{
									title: 'align left 50%',
									selector: 'img',
									styles: {
										'width': '50%',
										'height': 'auto',
										'float': 'left',
										'margin': '0 10px 0 0'
									}
								},
								{
									title: 'align rigth 33%',
									selector: 'img',
									styles: {
										'width': '33%',
										'float': 'right',
										'height': 'auto',
										'margin': '0 0 0 10px'
									}
								},
								{
									title: 'align rigth 50%',
									selector: 'img',
									styles: {
										'width': '50%',
										'float': 'right',
										'height': 'auto',
										'margin': '0 0 0  10px'
									}
								},
								{
									title: '100%',
									selector: 'img',
									styles: {
										'width': '100%',
										'height': 'auto',
										'margin': '10px 0'
									}
								}
							]
						},

					],
					save_enablewhendirty: true,
					save_onsavecallback: function () {
						var content = tinyMCE.get('content-html').getContent();
						var action = "update_content_only";
						if (content.length > 0) {
							$.ajax({
								type: 'POST',
								url: ajax_url,
								data: {
									action: action,
									token: token,
									pages_id: pages_id,
									users_id: users_id,
									content: content
								},
								success: function (result) {
									ajaxReplyInline($('#content-html').closest("form"), result);
								}
							});
						}
					},
					content_css: []
				});

			}

		});
	}
}



function addMobileMenu() {
	console.log("mobile menu");
	var w = document.documentElement.clientWidth || document.body.clientWidth;
	var logged_in = document.querySelector("#user-toolbar");
	if (w <= 1024) {
		var cms_dir = $("#cms_dir").val();
		var pages_id = $("#pages_id").val();
		var role_cms = $("#role_cms").val();
		var newdata = '<img class="mobile-menu-icon" src="' + cms_dir + '/content/favicon.png" id="site-icon">';
		// newdata += '<img class="mobile-menu-icon" src="' + cms_dir + '/cms/css/images/icon_search.png" style="" id="search-site-icon">';
		if (!logged_in) {
			newdata += '<img class="mobile-menu-icon" src="' + cms_dir + '/cms/css/images/icon_login.png" id="site-login-icon">';
		} else {
			if (role_cms >= 5) {
				newdata += '<img class="mobile-menu-icon" src="' + cms_dir + '/cms/css/images/icon_cogs.png" id="site-admin-icon">';
			}
			if (role_cms >= 3) {
				newdata += '<img class="mobile-menu-icon" src="' + cms_dir + '/cms/css/images/icon_edit.png" id="site-edit-icon" data-id="' + pages_id + '">';
				newdata += '<img class="mobile-menu-icon" src="' + cms_dir + '/cms/css/images/icon_inline_edit.png" id="site-inline-edit-icon" data-id="' + pages_id + '">';
			}
			newdata += '<img class="mobile-menu-icon" src="' + cms_dir + '/cms/css/images/icon_user.png" id="site-user-icon" data-id="' + pages_id + '">';
		}

		$(".mobile-buttons").append(newdata);
		$("#site-navigation-mobile").addClass("flexnav");
		$("#site-navigation-mobile").show();
		$(".flexnav").flexNav({
			'animationSpeed': 0
		});

		if (logged_in) {
			$("#site-navigation-mobile-wrapper").css("margin-top", "40px");
			$("#search_site").css("margin-top", "30px");
		}
	}
}

function refresh() {
	var w = $("#browser_size").slider("value");
	$("body").css("width", +w + "px");
	$("#browser_size_px").text("Browser width: " + w + "px");
}

function get_random_int(min, max) {
	return Math.floor(Math.random() * (max - min + 1) + min);
}

function calendar_preview(id) {
	var cms_dir = $("#cms_dir").val();
	w = window.open(cms_dir + '/cms/calendar_categories_view.php?id=' + id, '', 'width=500,height=600,menubar=no,location=no,directories=no,toolbar=no');
	w.focus();
}

function online() {
	var action = "online";
	var token = $("#token").val();
	var cms_dir = $("#cms_dir").val();
	$.ajax({
		type: 'POST',
		url: cms_dir + '/cms/online_ajax.php',
		data: "action=" + action + "&token=" + token,
		success: function (newdata) {},
	});
}

// function to temporary replace all matching images src path or part of path to new path
function replace_image_path(replace_pattern, new_pattern) {
	$('img').each(function () {
		var $this = $(this);
		$this.attr('src', $this.attr('src').replace(replace_pattern, new_pattern));
	});
}

function getFileExtension(filename) {
	if (!filename) {
		return
	}
	return filename.split('.').pop();
}

function getFileBaseName(filename) {
	if (!filename) {
		return
	}
	return filename.split('.').shift();
}

// parallax
// root element html
var doc = document.documentElement;
var h = window.screen.height;

let parallax_scroll = document.querySelector("#parallax_scroll").value;
let parallaxImages = document.querySelectorAll(".parallax");

// save initial images heights
let parallaxImagesHeight = [];
parallaxImages.forEach(element => {
	let elementStyle = window.getComputedStyle(element);
	let height = parseInt(elementStyle.getPropertyValue("height"));
	parallaxImagesHeight.push(height);
})

// set image heights
let checkLandingPage = document.querySelector("#landing-page");
let rect = parallaxImages[0].getBoundingClientRect();
var initialHeight = 0;
if (checkLandingPage === null || checkLandingPage === undefined) {
	initialHeight = rect.height;
	parallaxImages[0].style.height = initialHeight + "px";
} else {
	initialHeight = window.innerHeight - rect.y;
	parallaxImages[0].style.height = initialHeight + "px";
 	if (parallaxImagesHeight[0] > initialHeight) {
		parallaxImagesHeight[0] = initialHeight;
	}
}



window.addEventListener('load', (event) => {

	// first image fit height
	let rect = parallaxImages[0].getBoundingClientRect();

	// set parent element container
	var parentElement = parallaxImages[0].parentElement;

	// initial scroll
	var scrolled = false;

	window.addEventListener("scroll", function (event) {
		if (!scrolled) {
			parallaxImages[0].style.height = initialHeight + "px";
			scrolled = true;
		}
		if (parallax_scroll == 1) {
			showParallax(parallaxImages, initialHeight, parentElement);
		}
	});
});

function showParallax(elements, initialHeight, parentElement) {
	if (elements.length === 0) {
		return;
	}
	let i = 0;
	elements.forEach(element => {
		let result = isInView(element);
		if (result.rectangle.height > initialHeight) {
			result.rectangle.height = initialHeight;
		}
		if (result.rectangle.top < -result.rectangle.height || result.rectangle.top > result.rectangle.height) {
			return;
		}
		if (result.rectangle.top <= 0 && result.rectangle.height >= 0) {
			element.style.height = parallaxImagesHeight[i] + (result.rectangle.top * 0.5) + "px";
			parentElement.style.height = parallaxImagesHeight[i] + (result.rectangle.top * 0.5) + "px";
		} else if (result.check === true) {
			element.style.height = parallaxImagesHeight[i] + "px";
			parentElement.style.height = parallaxImagesHeight[i] + "px";
		}
		i++;
	});
}

function isInView(element) {
	let rectangle = element.getBoundingClientRect();
	let html = document.documentElement;
	let result = {}
	result.check =
		rectangle.top >= 0 &&
		rectangle.left >= 0 &&
		rectangle.bottom <= (window.innerHeight || html.clientHeight) &&
		rectangle.right <= (window.innerWidth || html.clientWidth);
	result.rectangle = rectangle;
	return result;
}

// swap settings
let swapSlideshowCycleImagesTime = parseInt(document.querySelector("#header_image_timeout").value);
swapSlideshowCycleImagesTime = swapSlideshowCycleImagesTime < 5000 ? 8000 : swapSlideshowCycleImagesTime;
let swapSlideshowCycleImagesFadeStep = 0.01;
let swapSlideshowCycleImagesFade = document.querySelector("#header_image_fade").value;
switch (swapSlideshowCycleImagesFade) {
	case "super slow":
		swapSlideshowCycleImagesFadeStep = 0.0025;
		break;
	case "slow":
		swapSlideshowCycleImagesFadeStep = 0.005;
		break;
	case "normal":
		swapSlideshowCycleImagesFadeStep = 0.01;
		break;
	case "fast":
		swapSlideshowCycleImagesFadeStep = 0.02;
		break;
	case "super fast":
		swapSlideshowCycleImagesFadeStep = 0.05;
		break;
}

let swapSlideshowCycleImagesStart = true;

// swap images
window.addEventListener('load', (event) => {
	swapSlideshowCycleImageCaption();
	setInterval(function () {
		fadeSlideshowCycleImages();
	}, swapSlideshowCycleImagesTime);
});


// function to fade images
function fadeSlideshowCycleImages() {

	// check if ready
	if (swapSlideshowCycleImagesStart === false) {
		return;
	}

	// get images
	let images = document.querySelectorAll(".slideshow-cycle-wrapper video, .slideshow-cycle-wrapper img.slideshow-cycle-image");
	
	// just one image
	if (images.length <= 1) {
		return;
	}

	// last image
	let image = images[images.length - 1];

	// let second last image
	let second_image = images[images.length - 2];

	// if video - play
	if (second_image.tagName === "VIDEO") {
		second_image.pause();
		second_image.currentTime = 0;
		second_image.play();
	}

	// prepare
	swapSideshowCycleImagesStart = false;
	let currentOpacity = 1;
	let step = swapSlideshowCycleImagesFadeStep;

	// show caption
	let showCaptionValue = document.querySelector("#header_caption_show").value;
	var showCaption = showCaptionValue == 1 ? true : false;
	let pauseStart = false;

	// change opacity
	let swapIntervalId = setInterval(function () {

		// done ?
		if (currentOpacity <= 0) {

			clearInterval(swapIntervalId);

			// DOM manipulation
			let parent = image.parentElement;
			parent.removeChild(image);
			parent.prepend(image);

			// reset opacity
			image.style.opacity = 1;

			// done
			swapSlideshowCycleImagesStart = true;

			// if video rewind and pause
			if (image.tagName === "VIDEO") {
				image.pause();
				image.currentTime = 0;
				startPause = false;
			}

			// caption
			if (showCaption) {
				swapSlideshowCycleImageCaption();
			}

		} else {

			// ändra stegvis opacitet
			currentOpacity -= step;
			image.style.opacity = currentOpacity;
		}
	}, 1000 / 60);
}

// show caption
function swapSlideshowCycleImageCaption() {

	// get cycle images
	let images = document.querySelectorAll(".slideshow-cycle-wrapper video, .slideshow-cycle-wrapper img.slideshow-cycle-image");

	// get last image caption
	let caption = images[images.length - 1].getAttribute("data-caption");
	let captionAlign = images[images.length - 1].getAttribute("data-caption-align");
	let captionVerticalAlign = images[images.length - 1].getAttribute("data-caption-vertical-align");

	if (caption.length === 0) {
		return;
	};

	// get element
	let elementCaption = document.querySelector("#site-header-caption");

	// show caption
	elementCaption.innerHTML = parseMarkdownCode(caption);
	elementCaption.style.textAlign = captionAlign;
	let posY = 10;
	if (captionVerticalAlign == "middle") {
		posY = 30;
	} else if (captionVerticalAlign == "bottom") {
		posY = 50;
	}
	elementCaption.style.top = posY + "%";
	elementCaption.style.filter = "opacity(1)";

	setTimeout(function () {
		elementCaption.style.filter = "opacity(0)";
	}, swapSlideshowCycleImagesTime - 2000);
}

function parseMarkdownCode(text) {
	let rows = text.split("\n");
	let result = "";
	rows.forEach(row => {
		let pattern = row.indexOf("# ");
		if (pattern >= 0) {
			result += "<div><h1>" + row.substring(2, row.length) + "</h1></div>";	
		} else {
			result += "<div><p>" + row + "</p></div>";
		}
		result += "<br>";
	})
	return result;
}

$(window).scroll(function() {
	if($(window).scrollTop() + $(window).height() > $(document).height() - 10) {		
		$("#page-bottom").show();
	}
});