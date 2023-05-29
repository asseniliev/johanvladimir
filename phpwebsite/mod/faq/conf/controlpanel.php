<?php
$image['name'] = "faq.png";
$image['alt'] = "Author: Darren Greene";

$link[0] = array ("label"=>"Frequently Asked Questions",
		  "module"=>"faq",
		  "url"=>"index.php?module=faq&amp;FAQ_op=menu&amp;FAQ_op=viewFAQs",
		  "image"=>$image,
		  "admin"=>TRUE,
		  "description"=>"Provides an interface to manage a list of frequently asked questions.",
		  "tab"=>"content");


?>