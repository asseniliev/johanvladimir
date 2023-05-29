<?php

$image["name"] = "pagemaster.png";
$image["alt"] = "Author: Adam Morton";

$link[] = array ("label"=>"Web Pages",
		 "module"=>"pagemaster",
		 "url"=>"index.php?module=pagemaster&amp;MASTER_op=main_menu",
		 "image"=>$image,
		 "admin"=>TRUE,
		 "description"=>"Go here to create and edit your site's web pages.",
		 "tab"=>"content");

?>