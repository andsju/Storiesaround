<?php
// include file
if(!defined('VALID_INCL')){die('Restricted access');}

if (isset($_SESSION['first_name'])) {
	
	$is_page =  strpos($_SERVER['SCRIPT_NAME'], "pages.php") > 0 ? true : false;
	
	//echo "\n".'<div class="toolbar">';
		echo "\n".'<ul id="user_navigation">';

			echo "\n\t".'<li class="toolbar-icons"><a href="'.$_SESSION['site_domain_url'].'" title="'.translate("Home", "site_home", $languages).'"><i class="fas fa-home"></i></a></li>';
			if(get_role_CMS('contributor') == 1) {
				echo "\n\t".'<li class="toolbar-icons"><a href="'.CMS_DIR.'/cms/admin.php" target="_blank" title="'.translate("Administration", "site_administration", $languages).'"><i class="fas fa-cog"></i></a></li>';
			
				if(get_role_CMS('author') == 1 && $is_page == true) {
					echo "\n\t".'<li class="toolbar-icons"><a href="'.CMS_DIR.'/cms/admin.php?t=pages&amp;tp=add&amp;id='. $id .'" class="colorbox_edit" title="'.translate("Add page", "pages_add_childpage", $languages).'">&nbsp;<i class="far fa-file"></i></a></li>';
					if($id) {
						echo "\n\t".'<li class="toolbar-icons"><a href="'.CMS_DIR.'/cms/pages_edit.php?id='. $id .'" class="colorbox_edit" title="'.translate("Edit", "pages_edit", $languages).'">&nbsp;<i class="fas fa-edit"></i></a></li>';
						echo "\n\t".'<li class="toolbar-icons"><i id="inline_edit" class="fas fa-pencil-alt" title="'.translate("Edit inline", "pages_edit_inline", $languages).'" aria-hidden></i></li>';
					}
				}
			}
			
			echo "\n\t".'<li class="toolbar-icons dropdown" title="'. $_SESSION['first_name'] .' '. $_SESSION['last_name'] .'"><i class="fas fa-user"></i>';	
				echo '<ul>';
					echo "\n\t".'<li>IP adress: '.$_SERVER['REMOTE_ADDR'].'</li>';
					echo "\n\t".'<li><a id="link_user" href="'.CMS_DIR.'/cms/users_edit.php?users_id='. $_SESSION['users_id'] .'">'. $_SESSION['first_name'] .' '. $_SESSION['last_name'] .'&nbsp;<span class="ui-icon ui-icon-person" style="display:inline-block;"></span></a> </li>';
					echo "\n\t".'<li><a href="'.CMS_DIR.'/cms/logout.php">'.translate("Logout", "site_logout", $languages).'</a></li>';
				echo '</ul>';
			echo '</li>';
			echo "\n\t".'<li class="toolbar-icons dropdown"><img src="'.CMS_DIR.'/cms/css/images/storiesaround_logotype_white.png" style="width:100px;" alt="Storiesaround logotype" id="logo-storiesaround">';
			include 'inc.cms_list_links.php';
			echo '</li>';		
			
		echo "\n".'</ul>';
	//echo "\n".'</div>';
}
?>