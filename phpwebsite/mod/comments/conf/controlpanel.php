<?php

$image['name'] = "comments.png";
$image['alt'] = "Comments Author: Steven Levin";

$link[0] = array ("label"=>"Manage Comment Threads",
		  "module"=>"comments",
		  "url"=>"index.php?module=comments&amp;CM_op=adminMenu",
		  "description"=>"The Comment Manager administration allows you to manage all of the comment threads on your site.",
		  "image"=>$image,
		  "admin"=>TRUE,
		  "tab"=>"content");

$link[1] = array ("label"=>"Personal Comment Settings",
		  "module"=>"comments",
		  "url"=>"index.php?module=comments&amp;CM_op=userSettings",
		  "description"=>"Personal Comment settings allows you to set the default way you view comment threads on the site.",
		  "image"=>$image,
		  "admin"=>FALSE,
		  "tab"=>"my_settings");
?>