<?php

/**
 * @version $Id: install.php,v 1.5 2004/06/14 21:10:19 darren Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

if($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/photoalbum/boost/install.sql", TRUE)) {
  $content .= "All PhotoAlbum tables successfully written.<br />";
  
  /* Create image directory */
  PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/photoalbum");
  if(is_dir("{$GLOBALS['core']->home_dir}images/photoalbum")) {
    $content .= "PhotoAlbum images directory successfully created!<br />{$GLOBALS['core']->home_dir}images/photoalbum<br />";
  } else {
    $content .= "Boost could not create the PhotoAlbum image directory:<br />{$GLOBALS['core']->home_dir}images/photoalbum<br />You will have to do this manually!<br />";
  }

} else {
    $content .= "There was a problem writing to the database.<br />";
}

?>