<?php

$image['name'] = "linkman.png";
$image['alt'] = "Link Manager Author: Steven Levin";

$link[0] = array ("label"=>"Link Manager",
		  "module"=>"linkman",
		  "url"=>"index.php?module=linkman&amp;LMN_op=adminMenuAction",
		  "description"=>"The Link Manager allows you to add web links to specific categories for you site.",
		  "admin"=>TRUE,
		  "image"=>$image,
		  "tab"=>"content");

?>