<?php

$image['name'] = "block.png";
$image['alt'] = "Block Maker Author: Steven Levin";

$link[0] = array ("label"=>"Block Maker",
		  "module"=>"blockmaker",
		  "url"=>"index.php?module=blockmaker&amp;BLK_block_op=block_menu",
		  "description"=>"The Block Maker allows you to add basic blocks of content to your site.",
		  "image"=>$image,
		  "admin"=>TRUE,
		  "tab"=>"content");

?>