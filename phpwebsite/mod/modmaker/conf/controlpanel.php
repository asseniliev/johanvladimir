<?php
$image['name'] = "modmaker.png";
$image['alt'] = "Module Maker by Matt McNaney";

$link[] = array("label"=>"Module Maker",
		"module"=>"modmaker",
		"description"=>"Assists developers in the creation and editing of module settings.",
		"url"=>"index.php?module=modmaker&amp;mod_op=mod_admin",
		"image"=>$image,
		"admin"=>TRUE,
		"tab"=>"developer");

?>