<?php

$image['name'] = "bug.png";
$image['alt'] = "Debugger Author: Steven Levin";

$link[0] = array ("label"=>"PhpWebSite Debugger",
		  "module"=>"debug",
		  "url"=>"index.php?module=debug&amp;DBUG_op=admin_settings",
		  "description"=>"PhpWebsite Debugger is a developer tool used for viewing session and request information.",
		  "image"=>$image,
		  "admin"=>TRUE,
		  "tab"=>"developer");

?>