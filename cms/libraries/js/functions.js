function get_textarea_editor(editor, textarea_id) {

	var arg = null;
	switch (editor) {
		case 'tinymce':
			if (typeof (tinyMCE) != "undefined") {
				arg = tinyMCE.get(textarea_id).getContent();
			}
			break;
		case 'none':
		default:
			arg = $("textarea#" + textarea_id).val();
			break;
	}
	return arg;
}

function isValidPassword(input) {
	var reg1 = /^[^%\s]{8,}$/;
	var reg2 = /[A-Z]/; //upper
	var reg3 = /[a-z]/; //lower
	var reg4 = /[0-9]/; //numeric
	var reg5 = /[\@\'\"\!\&\|\<\>\#\?\:\;\$\*\_\+\-\^\.\,\(\)\{\}\[\]\\\/\=\~]/;

	var a = reg1.test(input);
	var b = reg2.test(input);
	var c = reg3.test(input);
	var d = reg4.test(input);
	var e = reg5.test(input);

	if (b + c + d + e >= 3 && a) {
		return true;
	}
}

function isNumeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

// check length
function get_length(str) {
	return str.length;
}

// check lowercase
function get_lowecase(str) {
	return /[a-z]/.test(str);
}

// check uppercase
function get_uppercase(str) {
	return /[A-Z]/.test(str);
}

// check number
function get_number(str) {
	return /[0-9]/.test(str);
}

// check special character
function get_special_char(str) {
	return /[@#$%&!*)(-+=^~.,]/.test(str);
}

// check 8 characters - lowercase or uppercase
function get_8_letters(str) {
	return /[a-z]{8}/i.test(str);
}

// check space
function get_space(str) {
	return /\s/.test(str);
}

// count numbers
function count_numbers(str) {
	count = str.match(/[0-9]/g);
	if (count) {
		return count.length;
	} else {
		return 0;
	}
}

// count special characters
function count_special_char(str) {
	count = str.match(/[@#$%&!*)(-+=^~.,]/g);
	if (count) {
		return count.length;
	} else {
		return 0;
	}
}

// check unique characters
function get_unique_char(str) {
	var result = "";
	var char_count = new Array();
	var i = str.length;
	var char_code;
	for (var i = 0; i < str.length; i++) {
		char_code = str.charCodeAt(i);
		if (typeof (char_count[char_code]) == 'undefined') {
			char_count[char_code] = 1; // first occurrence
		} else {
			char_count[char_code]++ // increase count
		}
	}
	for (var i = 0; i < char_count.length; i++) {
		if (typeof (char_count[i]) == 'number' && char_count[i] == 1) result += String.fromCharCode(i);
	}
	return result;
}

// check unique characters
function get_duplicate_char(str) {
	var result = "";
	var char_count = new Array();
	var i = str.length;
	var char_code;
	for (var i = 0; i < str.length; i++) {
		char_code = str.charCodeAt(i);
		if (typeof (char_count[char_code]) == 'undefined') {
			char_count[char_code] = 1; // first occurrence
		} else {
			char_count[char_code]++ // increase count
		}
	}
	for (var i = 0; i < char_count.length; i++) {
		if (typeof (char_count[i]) == 'number' && char_count[i] > 1) result += char_count[i];
	}
	return result;
}

// toggle layers
function toggleLayer(whichLayer) {
	if (document.getElementById) {
		// browser std
		var style2 = document.getElementById(whichLayer).style;
		style2.display = style2.display ? "" : "block";
	} else if (document.all) {
		//browser old msie
		var style2 = document.all[whichLayer].style;
		style2.display = style2.display ? "" : "block";
	} else if (document.layers) {
		// browser nn4 
		var style2 = document.layers[whichLayer].style;
		style2.display = style2.display ? "" : "block";
	}
}

// limit text length in textarea
// example onKeyDown="limitText(this,100);" OR onKeyUp="limitText(this,100);"
function limitText(limitField, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	}
}

// autosave - highlight ajax_result span tag
function showAutoSave() {
	document.getElementById("ajax_status_autosave").style.color = "#000000";
	document.getElementById("ajax_status_autosave").style.background = "#ffff00";
	alertTimerId = setTimeout("hideAutoSave()", 10000);
}
// autosave - hide
function hideAutoSave() {
	document.getElementById("ajax_status_autosave").style.color = "#f0f0f0";
	document.getElementById("ajax_status_autosave").style.background = "";
	clearTimeout(alertTimerId);
}


// autosave - highlight ajax_result span tag
function delayscript() {
	setTimeout("hideAutoSave()", 5000);
}

// function ajax_reply - highlight reply -> hide reply
// param message
// param tag_id - ie, span tag
function ajaxReply(message, tag_id) {
	var now = getTime();
	if (message == "!token") {
		$(tag_id).empty().append('<span class="ui-icon ui-icon-circle-close" style="display:inline-block;vertical-align:text-bottom;"></span>Edited by another user!').attr('style', 'border:1px solid #999999;background:#FF3300;padding:2px;color:#FFF;font-size:1.1em;').show();
		$(":button").attr('style', 'background:#FFF;').attr("disabled", true);
		$("#dialog_token_text").text("This page was recently edited by another user. If you want to edit this page again, please close window and click edit link again. Buttons are now disabled.");
		$("#dialog_token").dialog("open");
		$("#dialog_token").dialog({
			buttons: {
				"Confirm": function () {
					$(this).dialog("close");
				},
			}
		});

	} else {
		$(tag_id).empty().append('<span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;"></span>' + message + ' ' + now).addClass('ui-state-highlight').attr('style', 'padding:2px;').show();
		setTimeout('$("' + tag_id + '").hide()', 10000);
	}
}


// function ajax_reply - highlight reply -> hide reply
// param message
// param tag_id - ie, span tag
function ajaxReplyLetItBe(message, tag_id) {
	var now = getTime();
	$(tag_id).empty().append('<span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;"></span>' + message + ' ' + now).attr('style', 'border:1px solid #999999;background:#CCFF66;padding:2px;').show();
}

function ajaxReplyInline(element, result) {
	$("span.edit_inline_reply").each(function() {
		$(this).remove();
	});
	var f = $(element).closest("form");
	var now = getTime();
	var message = result == 1 ? "saved: " : "error: ";
	f.append("<span class=\"ui-icon ui-icon-circle-check edit_inline_reply\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;saved: "+now+"</span>");
}


// function ajax_reply - highlight reply -> hide reply
// param jsonfomat
// param tag_id - ie, span tag
function ajaxReplyUser(json, tag_id) {
	var now = getTime();
	var json = JSON.parse(json);
	console.log(json);
	var message = json['text'];

	if (json['success'] == true) {
		$(tag_id).empty().append('<span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;"></span>' + message + ' ' + now).attr('style', 'border:1px solid #999999;background:#CCFF66;padding:2px;').show();
	} else {
		$(tag_id).empty().append('<span class="ui-icon ui-icon-notice" style="display:inline-block;vertical-align:text-bottom;"></span>' + message + ' ' + now).attr('style', 'border:1px solid #999999;background:#FF0000;padding:2px;color:#FFF;').show();
	}
	setTimeout('$("' + tag_id + '").hide()', 10000);

}


// function ajax_reply - highlight reply -> hide reply
// param message
// param tag_id - ie, span tag
function ajaxReplyHistory(message, tag_id) {
	$(tag_id).empty().append(message).attr('style', 'font-style:italic;border:1px dashed #fff;padding:2px;').show();
	setTimeout('$("' + tag_id + '").hide()', 10000);
}

function getTime() {
	var date = new Date;
	var hour = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	hour = (hour < 10) ? '0' + hour : hour;
	minutes = (minutes < 10) ? '0' + minutes : minutes;
	seconds = (seconds < 10) ? '0' + seconds : seconds;
	return hour + ':' + minutes + ':' + seconds;
}


function setNewDate(d, days_months_years, i) {

	var d = new Date(d);

	switch (days_months_years) {
		case 'd':
			d.setDate(d.getDate() + i);
			break;
		case 'm':
			d.setMonth(d.getMonth() + i);
			break;
		case 'y':
			d.setFullYear(d.getFullYear() + i);
			break;
	}
	return d.toISOString().substring(0, 10);
}


// function, get parameter value from url strings
// name: parameter name 
// stringURL: from jQuery click $(this).attr("href") | window.location.search
function getParameter(name, stringURL) {
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(stringURL);
	if (results == null)
		return "";
	else
		return results[1];
}

// function toggleCheckboxes in a form
function toggleCheckboxes(theForm, cName) {
	for (i = 0, n = theForm.elements.length; i < n; i++) {
		if (theForm.elements[i].className.indexOf(cName) != -1) {
			if (theForm.elements[i].checked == true) {
				theForm.elements[i].checked = false;
			} else {
				theForm.elements[i].checked = true;
			}
		}
	}
}


// add leading zero
function addZ(n) {
	return n < 10 ? '0' + n : '' + n;
}

// output yyyy-mm-dd h:i:s from utc
function format_utc_date(utc) {
	var date = new Date(utc * 1000);
	var y = date.getFullYear()
	var m = date.getMonth() + 1;
	var d = date.getDate();
	var h = date.getHours();
	var mi = date.getMinutes();
	var s = date.getSeconds();
	return y + "-" + addZ(m) + "-" + addZ(d) + " " + addZ(h) + ":" + addZ(mi) + ":" + addZ(s);
}


function ImageExist(url) {
	var img = new Image();
	img.src = url;
	return img.height != 0;
}


// passwordmeter 0-100                                                 
function update_passwordmeter(pw) {

	i_lowercase = 0;
	i_uppercase = 0;
	i_number = 0;
	i_special_char = 0;
	i_sum = 0;
	i_score = 0;

	if (get_length(pw) >= 4) {
		i_score += 3;
	}
	if (get_length(pw) >= 5) {
		i_score += 3;
	}
	if (get_length(pw) >= 6) {
		i_score += 3;
	}
	if (get_length(pw) >= 7) {
		i_score += 3;
	}
	if (get_length(pw) >= 8) {
		i_score += 3;
	}
	if (get_length(pw) >= 9) {
		i_score += 3;
	}
	if (get_length(pw) >= 10) {
		i_score += 3;
	}
	if (get_length(pw) >= 11) {
		i_score += 3;
	}
	if (get_length(pw) >= 12) {
		i_score += 3;
	}
	if (get_length(pw) >= 13) {
		i_score += 3;
	}
	if (get_length(pw) >= 14) {
		i_score += 3;
	}

	if (get_lowecase(pw)) {
		i_score += 5;
		i_lowercase = 1;
	}

	if (get_uppercase(pw)) {
		i_score += 5;
		i_uppercase = 1;
	}

	if (get_number(pw)) {
		i_score += 5;
		i_number = 1;
	}

	if (get_special_char(pw)) {
		i_score += 5;
		i_special_char = 1;
	}

	// combinations matter
	isum = i_lowercase + i_uppercase + i_number + i_special_char;
	if (isum == 2) {
		i_score += 10;
	}

	if (isum == 3) {
		i_score += 20;
	}

	if (isum == 4) {
		i_score += 30;
	}

	// many unique characters are good in passwords (generally), few not good
	var res = get_unique_char(pw);
	switch (res.length) {
		case 0:
			i_score -= -0;
			break;
		case 1:
			i_score -= -0;
			break;
		case 2:
			i_score -= -0;
			break;
		case 3:
			i_score -= -0;
			break;
		case 4:
			i_score += 5;
			break;
		case 5:
			i_score += 10;
			break;
		case 6:
			i_score += 15;
			break;
		case 7:
			i_score += 20;
			break;
		case 8:
			i_score += 25;
			break;
		case 9:
			i_score += 30;
			break;
		case 10:
			i_score += 35;
			break;
		default:
			i_score += 40;
	}

	// prevent duplicated char 
	if (get_duplicate_char(pw) > 8) {
		//i_score -= 10;
	}

	// if space found - reset i_score
	if (get_space(pw)) {
		i_score = 0;
	}

	// if i_score is negatvie - set i_score = 0
	if (i_score < 0) {
		i_score = 0;
	}

	// minimum, must contain a combination of characters
	if (!isValidPassword(pw)) {
		if (i_score > 49) {
			i_score = 49;
		}
	}

	// if i_score is 100 + - set i_score = 100
	if (i_score > 100) {
		i_score = 100;
	}

	var s_status = 'pending';

	// style passwordmeter
	if (i_score <= 9) {
		s_background = 'rgb(255,0,0)';
	}
	if (i_score >= 10 && i_score <= 49) {
		s_background = 'rgb(255,0,0)';
	}
	if (i_score >= 50 && i_score <= 59) {
		s_background = 'rgb(0,153,51)';
		s_status = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
	}
	if (i_score >= 60 && i_score <= 79) {
		s_background = 'rgb(0,153,51)';
		s_status = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
	}
	if (i_score >= 80 && i_score <= 89) {
		s_background = 'rgb(0,153,51)';
		s_status = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
	}
	if (i_score >= 90) {
		s_background = 'rgb(0,153,51)';
		s_status = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
	}

	// css change
	document.getElementById("passmeter").style.width = i_score + '%';
	document.getElementById("passmeter").style.background = s_background;
	document.getElementById("password_score").innerHTML = i_score;

	if (get_length(pw) >= 8 && i_score >= 50) {
		document.getElementById("password_status").innerHTML = s_status;
	} else {
		document.getElementById("password_status").innerHTML = '';
	}
}

function get_prexix_filesize(size) {
	var i = Math.floor(Math.log(size) / Math.log(1024));
	return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

function set_jquery_ui_touch_punch() {

	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		/*!
		 * jQuery UI Touch Punch 0.2.3
		 *
		 * Copyright 2011–2014, Dave Furfero
		 * Dual licensed under the MIT or GPL Version 2 licenses.
		 *
		 * Depends:
		 *  jquery.ui.widget.js
		 *  jquery.ui.mouse.js
		 */
		! function (a) {
			function f(a, b) {
				if (!(a.originalEvent.touches.length > 1)) {
					a.preventDefault();
					var c = a.originalEvent.changedTouches[0],
						d = document.createEvent("MouseEvents");
					d.initMouseEvent(b, !0, !0, window, 1, c.screenX, c.screenY, c.clientX, c.clientY, !1, !1, !1, !1, 0, null), a.target.dispatchEvent(d)
				}
			}
			if (a.support.touch = "ontouchend" in document, a.support.touch) {
				var e, b = a.ui.mouse.prototype,
					c = b._mouseInit,
					d = b._mouseDestroy;
				b._touchStart = function (a) {
					var b = this;
					!e && b._mouseCapture(a.originalEvent.changedTouches[0]) && (e = !0, b._touchMoved = !1, f(a, "mouseover"), f(a, "mousemove"), f(a, "mousedown"))
				}, b._touchMove = function (a) {
					e && (this._touchMoved = !0, f(a, "mousemove"))
				}, b._touchEnd = function (a) {
					e && (f(a, "mouseup"), f(a, "mouseout"), this._touchMoved || f(a, "click"), e = !1)
				}, b._mouseInit = function () {
					var b = this;
					b.element.bind({
						touchstart: a.proxy(b, "_touchStart"),
						touchmove: a.proxy(b, "_touchMove"),
						touchend: a.proxy(b, "_touchEnd")
					}), c.call(b)
				}, b._mouseDestroy = function () {
					var b = this;
					b.element.unbind({
						touchstart: a.proxy(b, "_touchStart"),
						touchmove: a.proxy(b, "_touchMove"),
						touchend: a.proxy(b, "_touchEnd")
					}), d.call(b)
				}
			}
		}(jQuery);
	}

}

function enc(str, number) {
	var encoded = "";
	for (i = 0; i < str.length; i++) {
		var a = str.charCodeAt(i);
		// bitwise XOR
		var b = a ^ number;
		encoded = encoded + String.fromCharCode(b);
	}

	return encoded;
}

/**
 * Gets a random number between min and max value
 *
 * @param min
 * @param max
 * @returns {*}
 */
function getRandomNumber(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
};

/**
 * Set equal height of elements in a row
 * https://codepen.io/micahgodbolt/pen/FgqLc
 * 
 * @param min
 * @param max
 * @returns {*}
 */
equalheight = function (container) {

	var currentTallest = 0,
		currentRowStart = 0,
		rowDivs = new Array(),
		$el,
		topPosition = 0;
	$(container).each(function () {

		$el = $(this);
		$($el).height('auto')
		topPostion = $el.position().top;

		if (currentRowStart != topPostion) {
			for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
				rowDivs[currentDiv].height(currentTallest);
			}
			rowDivs.length = 0; // empty the array
			currentRowStart = topPostion;
			currentTallest = $el.height();
			rowDivs.push($el);
		} else {
			rowDivs.push($el);
			currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
		}
		for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
			rowDivs[currentDiv].height(currentTallest);
		}
	});
	
}

function getSelectNumber(arrayOfNumbers, current, name, id, classes) {
	if (!Array.isArray(arrayOfNumbers)) { return null}
	name = name.length ? " name=\""+name+"\"" : "";
	id = id.length ? " id=\""+id+"\"" : "";
	classes = classes.length ? " class=\""+classes+"\"" : "";

	var html = "<select "+name + id + classes+">"
	var selected = "";
	for (var i = 0; i < arrayOfNumbers.length; i++) {
		selected = current === arrayOfNumbers[i] ? " selected" : ""; 
		html += "<option value=\""+arrayOfNumbers[i]+"\""+selected+">"+arrayOfNumbers[i]+"</option>";
	}
	html += "</select>";
	return html;
}

function getSelectStrings(arrayOfStrings, current, name, id, classes) {
	if (!Array.isArray(arrayOfStrings)) { return null}
	name = name.length ? " name=\""+name+"\"" : "";
	id = id.length ? " id=\""+id+"\"" : "";
	classes = classes.length ? " class=\""+classes+"\"" : "";

	var html = "<select "+name + id + classes+">"
	var selected = "";
	for (var i = 0; i < arrayOfStrings.length; i++) {
		selected = current === arrayOfStrings[i][0] ? " selected" : ""; 
		html += "<option value=\""+arrayOfStrings[i][0]+"\""+selected+">"+arrayOfStrings[i][1]+"</option>";
	}
	html += "</select>";
	return html;
}


/**
 * Function to validate data
 *
 * @param data
 * @param type
 * @returns {boolean}
 */
function validateThis(data, type) {

	var pattern;
	type.toLowerCase();

	switch(type) {
		case "username" : // 4-8 length
			pattern = /^(?=.{4,8}$)[A-Za-z0-9]+$/;
			break;
		case "numbers" :
			pattern = /^[0-9]+$/;
			break;
		case "anynumbers" :
			pattern = /\d+/;
			break;
		case "email" :
			// code from http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
			pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			break;
		case "url" :
			// code from https://stackoverflow.com/questions/161738/what-is-the-best-regular-expression-to-check-if-a-string-is-a-valid-url
			pattern = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$/i;
			break;
		case "heading" : // max 100 length
			pattern = /^(?=.{2,100}$)[\s\wåäöÅÄÖ]+$/;
			break;
	}

	if (pattern === undefined) {
		return false;
	}

	return pattern.test(data);
}
