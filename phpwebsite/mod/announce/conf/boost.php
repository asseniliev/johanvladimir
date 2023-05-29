<?php
/**
 * @version $Id: boost.php,v 1.36 2005/05/12 13:01:44 darren Exp $
 */
$mod_title = "announce";
$mod_pname = "Announcements";
$mod_directory = "announce";
$mod_filename = "index.php";
$allow_view = "all";
$active = "on";
$version = "2.2.5";
$admin_mod = 1;

$mod_class_files = array("AnnouncementManager.php",
			 "Announcement.php");

$mod_sessions = array("SES_ANN_MANAGER",
		      "SES_ANN");

?>
