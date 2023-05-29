<?php

$image["name"] = "poll.png";
$image["alt"] = "Author: Feng Pan";

$link[] = array ("label"=>"Poll Manager",
		 "module"=>"poll",
		 "url"=>"index.php?module=poll&amp;poll_op=list",
		 "image"=>$image,
		 "admin"=>TRUE,
		 "description"=>"Go here to create and edit your site's polls.",
		 "tab"=>"content");

?>