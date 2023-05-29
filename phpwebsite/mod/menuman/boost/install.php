<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/menuman/boost/install.sql", 1, 1)){

  $time = time();
  $sql = "UPDATE mod_menuman_menus SET updated='$time'";
  $GLOBALS['core']->query($sql, TRUE);

  $content .= "All Menu Manager tables successfully written.<br />";
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
}

/* Create image directory */
PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/menuman");
if(is_dir("{$GLOBALS['core']->home_dir}images/menuman")) {
  $content .= "Menuman image directory {$GLOBALS['core']->home_dir}images/menuman successfully created!<br />";
} else {
  $content .= "Menuman could not create the image directory: {$GLOBALS['core']->home_dir}images/menuman<br />You will have to do this manually!<br />";
}

?>
