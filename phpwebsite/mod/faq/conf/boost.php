<?php
$mod_title = "faq";
$mod_pname = "FAQ";
$mod_directory = "faq";
$mod_filename = "index.php";
$admin_mod = 1;
$mod_class_files = array("FaqManager.php","Faq.php","FaqStats.php");
$mod_sessions = array("SES_FAQ_MANAGER", "SES_FAQ_STATS");
$init_object = array("SES_FAQ_MANAGER"=>"PHPWS_FaqManager",
		     "SES_FAQ_STATS"=>"PHPWS_FaqStats");
$allow_view = "all";
$priority = 50;
$active = "on";
$version = "1.1.1";
?>