<?php
$image['name'] = "calendar.png";
$image['alt'] = "Calendar by Matt McNaney";

$link[] = array("label"=>"Calendar",
		"module"=>"calendar",
		"description"=>"Event calendar for phpWebSite.",
		"url"=>"index.php?module=calendar&amp;calendar[admin]=admin_menu",
		"image"=>$image,
		"admin"=>TRUE,
		"tab"=>"content");

?>