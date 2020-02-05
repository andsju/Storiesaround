<?php

/**
 * API for class HTMLpage
 * extends Plugins class
 */

class HTMLpage extends Plugins {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'HTMLpage';
		$a['classname'] = 'HTMLpage';
		$a['description'] = 'Show a HTML page and add selections';
		$a['info'] = 'This page uses "HTMLpage - plugin". Just Selections under Setup will show up.';
		$a['css'] = CMS_DIR.'/content/plugins/htmlpage/css/style.css';
		// replace data in area(s) (comma-separated): 'header,left_sidebar,content,right_sidebar,footer,page'  // use blank space pre and post words!
		$a['area'] = ' page ';
		return $a;
    }
	
   // pass parameters to the class file action.php
	public function action($pages_id, $users_id, $token, $areas) {
	
		$info = $this->info();
		$folder = strtolower($info['classname']);
		$file = $folder.'/action.php';
		$plugin_areas = null;
		//if(is_file('')) {}
		include_once $file;
		return $plugin_areas;
	}
	
	public function db_sql() {
		$sqls = array();
	}
}
?>