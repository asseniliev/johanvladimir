<?php

$mod_title = "comments";
$mod_pname = "Comment Manager";
$mod_directory = "comments";
$mod_filename = "index.php";
$allow_view = "all";
$admin_mod = 1;
$priority = 50;
$mod_class_files = array("CommentActions.php", "CommentManager.php", "Comment.php");
$mod_sessions = array("PHPWS_CommentManager");
$init_object = array("PHPWS_CommentManager"=>"PHPWS_CommentManager");
$active = "on";
$version = "2.1.3";

?>
