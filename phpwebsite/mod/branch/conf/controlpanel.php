<?php
$image['name'] = "branch.png";
$image['alt'] = "Branch by Matt McNaney";

$link[] = array("label"=>"Branch Creator",
		"module"=>"branch",
		"description"=>"Creates branch sites off your hub code.",
		"url"=>"index.php?module=branch&amp;branch_op=firstPage",
		"admin"=>TRUE,
		"image"=>$image,
		"tab"=>"administration");
?>