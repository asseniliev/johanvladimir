<?php

$image["name"] = "health.gif";
$image["alt"] = "Author: rck <http://www.kiesler.at>";

$link[] = array ("label"=>"Healthcheck",
		 "module"=>"health",
		 "url"=>"index.php?module=health",
		 "image"=>$image,
		 "admin"=>TRUE,
		 "description"=>"Health will give you a quick overview on how ".
		    "healthy your phpwebsite installation is right now.",
		 "tab"=>"administration");

?>
