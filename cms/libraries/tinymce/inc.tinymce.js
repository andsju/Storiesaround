	// editor tinymce start

	tinymce.init({
		
		selector : "textarea.tinymce",
		autoresize_max_height: 900,
		content_css : "css/layout_wysiwyg_editor.css?" + new Date().getTime(),
		// emoticons
		//plugins : "advlist anchor autolink charmap code contextmenu fullscreen directionality hr image imagetools insertdatetime link lists media nonbreaking pagebreak paste preview save searchreplace table template textcolor visualblocks visualchars wordcount youtube",
		plugins : "advlist anchor autoresize charmap code hr image imagetools link lists media paste searchreplace table template visualblocks wordcount moxiemanager stories",
		toolbar : "",
		//toolbar: "undo redo | bullist numlist | formats | outdent indent | image | charmap",
		menubar: "view edit insert tools",
		image_advtab: true,
		style_formats: [
			{title: 'Headings', items: [
				{title: 'h1', block: 'h1'},
				{title: 'h2', block: 'h2'},
				{title: 'h3', block: 'h3'},
				{title: 'h4', block: 'h4'},
				{title: 'h5', block: 'h5'},
				{title: 'h6', block: 'h6'}
			]},
			{title: 'Blocks', items: [
				{title: 'p', block: 'p'},
				{title: 'div', block: 'div'},
				{title: 'pre', block: 'pre'}
			]},		
			{title: 'Alignment', items: [
				{title:"Left",icon:"alignleft",format:"alignleft"},
				{title:"Center",icon:"aligncenter",format:"aligncenter"},
				{title:"Right",icon:"alignright",format:"alignright"},
				{title:"Justify",icon:"alignjustify",format:"alignjustify"}
			]},
			{title: 'Custom', items: [		
				{title : 'Horizont line shadowed <p>', block : 'p', classes : 'horizont-line'},
				{title : 'FAQ question <p>', block : 'p', classes : 'faq-question'},
				{title : 'FAQ answer <p>', block : 'p', classes : 'faq-answer'},
				{title : 'Box shadowed<p>', block : 'p', classes : 'box-shadowed'},
				{title : 'Box elevated<p>', block : 'p', classes : 'box-elevated'},
				{title : 'Quote emphasize <p>', block : 'p', classes : 'quote-emphasize'},
				{title : 'Columns: 2 <p>', block : 'p', classes : 'p-columns-2'},
				{title : 'Columns: 3 <p>', block : 'p', classes : 'p-columns-3'},
				{title : 'Drop cap <p>', block : 'p', classes : 'dropcap'},
				{title : 'Read more <span>', inline : 'span', classes : 'read-more'},
				{title : 'Code <span>', inline : 'span', classes : 'code'},
				{title : 'Highlight <span>', inline : 'span', classes : 'highlight-word'},
				{title : 'Bigger <span>', inline : 'span', classes : 'text-bigger'},
				{title : 'Smaller <span>', inline : 'span', classes : 'text-smaller'},
				{title : 'SMALL CAPS <span>', inline : 'span', classes : 'small-caps'},
			]},
		],
		
		templates: [ 
			{title: 'Lorem ipsum 1', description: 'Dummy text', content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae suscipit velit, a blandit augue. Quisque eu condimentum tellus. Pellentesque luctus, tortor non consectetur convallis, urna magna suscipit tortor, id commodo elit magna sit amet eros. Quisque eget interdum risus. Vestibulum nec lectus sit amet diam lacinia sollicitudin eget ac magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque purus ipsum, porttitor eget vestibulum et, congue eget ipsum. Curabitur sagittis est mi, et molestie ipsum porttitor ac.'}, 
			{title: 'Lorem ipsum 5', description: 'Dummy text', url: 'libraries/tinymce/template_lorem.html'} 
		],
		//image_list: "libraries/tinymce/image_list.php"
		image_list: "includes/inc.image_list.php"
	 });


/* 	 tinymce.init({
		
		selector : "textarea.tinymce-grid",
		branding: false,

		menu: {
			edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
			insert: {title: 'Insert', items: 'link media | template hr'},
			format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
			tools: {title: 'Tools', items: 'spellchecker code'}
		  },
		  toolbar: false
	 });

 */
	// editor tinymce end