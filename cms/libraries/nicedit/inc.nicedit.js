	bkLib.onDomLoaded(function() {
		
		if (window.document.getElementById("content")) {
			new nicEditor({
				buttonList : ['fontFormat','bold','italic','underline','strikeThrough','left','center','right','justify','ol','ul','subscript','superscript','html','image','link','unlink']
			}).panelInstance('content');
		}
		if (window.document.getElementById("content_html")) {
			new nicEditor({
				buttonList : ['fontFormat','bold','italic','underline','strikeThrough','left','center','right','justify','ol','ul','subscript','superscript','html','image','link','unlink']
			}).panelInstance('content_html');
		}
		if (window.document.getElementById("story_content")) {
			new nicEditor({
				buttonList : ['fontFormat','bold','italic','underline','strikeThrough','left','center','right','justify','ol','ul','subscript','superscript','html','image','link','unlink']
			}).panelInstance('story_content');
		}
		if (window.document.getElementById("story_wide_content")) {
			new nicEditor({
				buttonList : ['fontFormat','bold','italic','underline','strikeThrough','left','center','right','justify','ol','ul','subscript','superscript','html','image','link','unlink']
			}).panelInstance('story_wide_content');
		}

	});
