
	$(document).ready(function () {
		var theme = $("#theme").val();
		tinymce.init({
			
			//selector : "textarea.tinymce",
			mode : "specific_textareas",
			selector : "textarea#content, textarea#story_content, textarea#content_html",
			autoresize_max_height: 900,
			content_css : ['css/layout.css','../content/themes/'+theme+'/style.css','css/wysiwyg_editor.css'],
			// plugins : " stories",
			plugins : "advlist anchor autoresize charmap code hr image imagetools link lists media paste searchreplace table template visualblocks wordcount moxiemanager",
			toolbar: "undo redo | styleselect | bold italic | bullist numlist | outdent indent | link | image",
			menubar: "view edit insert tools",
			image_advtab: true,
			style_formats_merge: true,
			style_formats: [
				{title: 'Custom text', items: [		
					{title : 'Horizont line shadowed <p>', block : 'p', classes : 'horizont-line'},
					{title : 'FAQ question <p>', block : 'p', classes : 'faq-question'},
					{title : 'FAQ answer <p>', block : 'p', classes : 'faq-answer'},
					{title : 'Box shadowed<p>', block : 'p', classes : 'box-shadowed'},
					{title : 'Box elevated<p>', block : 'p', classes : 'box-elevated'},
					{title : 'Quote emphasize <p>', block : 'p', classes : 'quote-emphasize'},
					{title : 'Read more <span>', inline : 'span', classes : 'read-more'},
					{title : 'Highlight <span>', inline : 'span', classes : 'highlight-word'},
					{title : 'Bigger <span>', inline : 'span', classes : 'text-bigger'},
					{title : 'Smaller <span>', inline : 'span', classes : 'text-smaller'},
					{title : 'SMALL CAPS <span>', inline : 'span', classes : 'small-caps'},
				]},
				{title: 'Image', items: [
					{title : 'align left 33%', selector : 'img', styles : {'width' : '33%', 'height' : 'auto', 'float' : 'left', 'margin' : '0 10px 0 0'}},
					{title : 'align left 50%', selector : 'img', styles : {'width' : '50%', 'height' : 'auto', 'float' : 'left', 'margin' : '0 10px 0 0'}},
					{title : 'align rigth 33%', selector : 'img', styles : {'width' : '33%', 'float' : 'right', 'height' : 'auto', 'margin' : '0 0 0 10px'}},
					{title : 'align rigth 50%', selector : 'img', styles : {'width' : '50%', 'float' : 'right', 'height' : 'auto', 'margin' : '0 0 0  10px'}},
					{title : '100%', selector : 'img', styles : {'width' : '100%', 'height' : 'auto', 'margin' : '10px 0'}}
				]}
			],
			templates: [ 
				{title: 'Lorem ipsum 1', description: 'Dummy text', content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae suscipit velit, a blandit augue. Quisque eu condimentum tellus. Pellentesque luctus, tortor non consectetur convallis, urna magna suscipit tortor, id commodo elit magna sit amet eros. Quisque eget interdum risus. Vestibulum nec lectus sit amet diam lacinia sollicitudin eget ac magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque purus ipsum, porttitor eget vestibulum et, congue eget ipsum. Curabitur sagittis est mi, et molestie ipsum porttitor ac.'}, 
				{title: 'Lorem ipsum 5', description: 'Dummy text', url: 'libraries/tinymce/template_lorem.html'} 
			],
			image_list: "includes/inc.image_list.php"
		});
	});
