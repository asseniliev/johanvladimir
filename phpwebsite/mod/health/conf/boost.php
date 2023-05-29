<?php

	/**
	 * Health module for phpWebSite
	 *
	 * @author rck <http://www.kiesler.at/>
	 */

	$version = "1.1";
	$mod_pname = "Health";
	$mod_title = "health";
	$allow_view = "all";
	$priority = 50;
	$mod_class_files = array("health.php");
	$mod_sessions = array("OBJ_health");
	$init_object = array("OBJ_health"=>"PHPWS_health");
	$active = "on";
	$branch_allow = 1;
?>
