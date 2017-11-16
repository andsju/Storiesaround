<?php

/**
 * API for class Instagram
 * extends Widgets class
 */

class Instagram extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Instagram';
		$a['description'] = 'Show Instagram feed';
		$a['classname'] = 'Instagram';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"limit": "", "caption": "false", "endpoint": "", "tag": "", "client_id": "", "user_id": "", "username": "", "access_token": "", "timer": "120000", "background": "#fff", "color": "#000"}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"limit": "numerical", "caption": "boolean", "endpoint": "str", "tag": "str", "client_id": "str", "user_id": "numerical", "username": "str", "access_token": "str", "timer": "milliseconds", "background": "str", "color": "str"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"limit": "limit number of images (max 30)", "caption": "show image caption true/false", "endpoint": "set instagram endpoint like tag user_id or search user name", "tag": "set tag", "client_id": "set instagram client_id if tag is set", "user_id": "set user-id", "username": "search username", "access_token": "set instagram access token if user_id is set or search for username", "timer": "set refresh timer in milliseconds (above 10000 ms)", "background": "set background color", "color": "set font color"}';
		return $help;
   }
	
	public function Instagram($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		//$w = ($width==474) ? 222 : 222;
		$limit = isset($objects['limit']) ? $objects['limit'] : $defaults['limit'];
		$limit = filter_var($limit, FILTER_VALIDATE_INT) ? $limit: 30;
		$caption = isset($objects['caption']) ? $objects['caption'] : $defaults['caption'];
		$endpoint = isset($objects['endpoint']) ? $objects['endpoint'] : $defaults['endpoint'];
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		$client_id = isset($objects['client_id']) ? $objects['client_id'] : $defaults['client_id'];
		$user_id = isset($objects['user_id']) ? $objects['user_id'] : $defaults['user_id'];
		$username = isset($objects['username']) ? $objects['username'] : $defaults['username'];
		$access_token = isset($objects['access_token']) ? $objects['access_token'] : $defaults['access_token'];
		$timer = isset($objects['timer']) ? $objects['timer'] : $defaults['timer'];
		$timer = $timer > 10000 ? $timer: 10000;
		$background = isset($objects['background']) ? $objects['background'] : $defaults['background'];
		$color = isset($objects['color']) ? $objects['color'] : $defaults['color'];
		
		?>		
		<script>
		
		
			function addZ(n){return n<10? '0'+n:''+n;}

			function format_utc_date(utc) {
				var date = new Date(utc*1000);
				var y = date.getFullYear()
				var m = date.getMonth()+1;
				var d = date.getDate();
				var h = date.getHours();
				var mi = date.getMinutes();
				var s = date.getSeconds();
				return y+"-"+addZ(m)+"-"+addZ(d)+" "+addZ(h)+":"+addZ(mi)+":"+addZ(s);
			}
			
			var token = "<?php echo $_SESSION['token']; ?>";
			var id = <?php echo $pages_widgets_id; ?>;
			var limit = <?php echo $limit; ?>;
			var endpoint = '<?php echo $endpoint; ?>';
			var caption = <?php echo $caption; ?>;
			var tag = '<?php echo $tag; ?>';
			var client_id = '<?php echo $client_id; ?>';
			var user_id = '<?php echo $user_id; ?>';
			var username = '<?php echo $username; ?>';
			var access_token = '<?php echo $access_token; ?>';
			var timer = <?php echo $timer; ?>;
			var url_endpoint = '';

			if (typeof(endpoint) != "undefined") {
				switch(endpoint) {
					case 'tag':
						url_endpoint = 'https://api.instagram.com/v1/tags/'+tag+'/media/recent/?client_id='+client_id+'';
					break;
					case 'user_id':
						url_endpoint = 'https://api.instagram.com/v1/users/'+user_id+'/media/recent/?access_token='+access_token+'';
					break;
					case 'username':
						url_endpoint = 'https://api.instagram.com/v1/users/search?q='+username+'&access_token='+access_token+'';
					break;
					default:
						url_endpoint = '';
					break;
				}
			}

			
			function showInstagramFeed() {
				$(function() {
					$.ajax({
						type: "GET",
						dataType: "jsonp",
						cache: false,
						url: url_endpoint,						
						success: function(data) {
							var sizelist = data.data.length;
							var size = limit < sizelist ? limit : sizelist;
							var terms = "This product uses the Instagram API but is not endorsed or certified by Instagram";
							for (var i = 0; i < size; i++) {
								
								if(i==0) {
									
									if(endpoint != 'tag') {
										var picture = data.data[i].user.profile_picture;
										<?php
										if ($width == 222) {
											?>
											$("#instagram_user_<?php echo $pages_widgets_id; ?>").append("<a target=\"_blank\" href='" + data.data[i].link +"'><img class=\"instagram-image\" src='" + picture +"' width=\"222px\"  /></a><br />" + data.data[i].user.username +", "+ data.data[i].user.full_name); 
											<?php
										}
										if ($width != 222) {
											?>
											$("#instagram_user_<?php echo $pages_widgets_id; ?>").append("<a target=\"_blank\" href='" + data.data[i].link +"'><img class=\"instagram-image\" src='" + picture +"' width=\"50px\" /></a><span style=\"padding-left:10px;\" >" + data.data[i].user.username +", "+ data.data[i].user.full_name) +"</span>"; 
											<?php
										}
										?>
									}
									if(endpoint == 'tag') {
										$("#instagram_tag_<?php echo $pages_widgets_id; ?>").append("<span style=\"font-style:bold;\">#"+tag+"</span>");   
									}
									$("#instagram_terms_<?php echo $pages_widgets_id; ?>").append("<span style=\"font-style:italic;\">"+terms+"</span>");   
									
								}
								
								var user = data.data[i].user ? data.data[i].user.full_name : "";
								var capt = data.data[i].caption ? data.data[i].caption.text : "";
								var caption = capt +" / "+user;
								var show_caption = (caption == true) ? "<br />"+capt : "";
								var utc = data.data[i].created_time;
								var dt = format_utc_date(utc);
								<?php
								if ($width == 222) {
									?>
									$("#instagram_<?php echo $pages_widgets_id; ?>").append("<div style=\"\"><a target=\"_blank\" href='" + data.data[i].link +"'><img class=\"instagram-image\" src='" + data.data[i].images.low_resolution.url +"' title='"+caption+" "+ dt +"' width=\"222px\"  /></a></div>"); 
									<?php
								}
								if ($width != 222) {
									?>
									$("#instagram_<?php echo $pages_widgets_id; ?>").append("<a class=\"preview\" target=\"_blank\" href='" + data.data[i].link +"'><img class=\"compress\" style=\"padding:5px;\" src='" + data.data[i].images.standard_resolution.url +"' title='"+caption+" "+ dt +"' width=\"132px\"  /></a>"); 
									<?php
								}
								?>			
							}
							
								$("#instagram_<?php echo $pages_widgets_id; ?> a img")
									.mouseover(function(e){
										var im = e.currentTarget.src;
										$("body").append("<p id='preview'><img src="+im+" width='400px' /></p>").fadeIn("fast");
										$("#preview").css("top",(e.pageY - 10) + "px").css("left",(e.pageX + 10) + "px").fadeIn("fast");								
								})
									.mouseout(function(e){
										$("#preview").remove();
							});	
						
						}
					});
				});	
			}
			
			showInstagramFeed();
			
			setInterval(function() {
				$("#instagram_<?php echo $pages_widgets_id; ?>").empty();
				$("#instagram_user_<?php echo $pages_widgets_id; ?>").empty();
				$("#instagram_tag_<?php echo $pages_widgets_id; ?>").empty();
				$("#instagram_terms_<?php echo $pages_widgets_id; ?>").empty();
				showInstagramFeed();
			}, timer);

			
		</script>
		<div class="instagram_wrapper" style="clear:both;width:100%;border:1px solid #000;background:<?php echo $background;?>;color:<?php echo $color;?>;">
			<div id="instagram_<?php echo $pages_widgets_id; ?>" style="width:100%;margin:7px;">
			</div>
			<div id="instagram_user_<?php echo $pages_widgets_id; ?>" style="padding:0 0 0 10px;"></div>
			<div id="instagram_tag_<?php echo $pages_widgets_id; ?>" style="padding:0 0 0 10px;"></div>
			<div id="instagram_terms_<?php echo $pages_widgets_id; ?>" style="padding:0 0 5px 10px;"></div>
		</div>
		<?php	
	}
}
?>