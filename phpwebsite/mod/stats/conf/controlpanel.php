<?php

$image["name"] = "stats.png";
$image["alt"] = "Module Author: Darren Greene";

$link[] = array ("label"=>"Stats",
		 "module"=>"stats",
		 "url"=>"index.php?module=stats&amp;stats[stats]=view",
		 "image"=>$image,
		 "admin"=>TRUE,
		 "description"=>"Includes module statistics, admin counter block, and the ability to track user visits.",
		 "tab"=>"administration");

?>