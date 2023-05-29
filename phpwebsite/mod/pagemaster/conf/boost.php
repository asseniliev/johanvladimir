<?php
/**
 * This is the Pagemaster boost.php file for Boost
 *
 * @version $Id: boost.php,v 1.36 2005/03/22 19:34:45 steven Exp $
 * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
$mod_title = "pagemaster";
$mod_pname = "Web Pages";
$mod_directory = "pagemaster";
$mod_filename = "index.php";
$allow_view = array("home"=>1, "pagemaster"=>1, "approval"=>1, "search"=>1);
$priority = 50;
$active = "on";
$version = "2.1.9";
$admin_mod = 1;

$mod_class_files = array("PageMaster.php",
			 "Page.php",
			 "Section.php");

$mod_sessions = array("SES_PM_master",
		      "SES_PM_page",
		      "SES_PM_section");


?>