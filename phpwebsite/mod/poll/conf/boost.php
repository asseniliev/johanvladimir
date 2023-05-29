<?php
$mod_title = "poll";
$mod_pname = "Poll";
$mod_directory = "poll";
$mod_filename = "index.php";
$admin_op = "&poll_op=list";
$admin_mod = 1;
$mod_icon = "poll.gif";
$active = "on";
$priority = 50;
$version = "1.3.6";
$allow_view = array("poll"=>1, "home"=>1);

$mod_class_files = array("Poll.php", "PollManager.php");

$mod_sessions = array("SES_POLL");

?>
