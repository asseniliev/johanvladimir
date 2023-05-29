<?php

// This is a sample theme include file. Just insert the functions you want to appear
// in your theme and place them into the $THEME['LABEL_NAME'] array. Then just create
// a {EXAMPLE} tag in your theme.tpl file.
// This file will NOT be included if layout's config.php file prohibits it.

// $THEME["EXAMPLE"] = "This is a sample of including php code into your theme. <br />
//                      Uncomment this line then look at the bottom of your Default theme page.";

$THEME['SITE'] = $_SERVER['HTTP_HOST'];

?>