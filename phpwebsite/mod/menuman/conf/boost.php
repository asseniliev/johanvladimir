<?php

$mod_title = "menuman";
$mod_pname = "Menu Manager";
$mod_directory = "menuman";
$mod_filename = "index.php";
$allow_view = "all";
$admin_mod = 1;
$priority = 50;
$mod_class_files = array("MenuActions.php", "Menuman.php", "Menu.php", "MenuItem.php");
$mod_sessions = array("OBJ_menuman", "SES_parentlevel_id");
$init_object = array("OBJ_menuman"=>"PHPWS_Menuman");
$active = "on";
$version = "1.3.4";

?>
