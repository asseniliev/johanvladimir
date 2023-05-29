<?php

$image['name'] = "menu.png";
$image['alt'] = "Menu Manager Author: Steven Levin";

$link[0] = array ("label"=>"Menu Manager",
		  "module"=>"menuman",
		  "url"=>"index.php?module=menuman&amp;MMN_menuman_op=adminMenu",
		  "description"=>"The Menu Manager allows you to add and create dynamic menus. Menus are used to link to stuff from your site and other sites.",
		  "admin"=>TRUE,
		  "image"=>$image,
		  "tab"=>"content");

?>
