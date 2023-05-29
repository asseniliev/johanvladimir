<?php
$image['name'] = "guestbook.gif";
$image['alt'] = "Guestbook Admin";
$image2['name'] = "guestbook.gif";
$image2['alt'] = "Guestbook";
$link[] = array("label"=>"Guestbook Admin","module"=>"guestbook","description"=>"Guestbook Administration","url"=>"index.php?module=guestbook&agbook=admin","image"=>$image,"admin"=>TRUE,"tab"=>"administration");
$link[] = array("label"=>"Guestbook","module"=>"guestbook","description"=>"Guestbook","url"=>"index.php?module=guestbook","image"=>$image2,"admin"=>FALSE,"tab"=>"my_modules");
?>