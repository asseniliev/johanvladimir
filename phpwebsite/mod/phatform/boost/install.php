<?php
/**
 * Install file for PhatForm v2
 *
 * @version $Id: install.php,v 1.11 2004/06/14 21:12:02 darren Exp $
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/phatform/boost/install.sql", TRUE)){
  $content .= "All PhatForm tables successfully written.<br />";
  
  /* Create image directory */
  PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/phatform");
  if(is_dir("{$GLOBALS['core']->home_dir}images/phatform"))
    $content .= "PhatForm images directory successfully created!<br />{$GLOBALS['core']->home_dir}images/phatform<br />";
  else
    $content .= "Boost could not create the PhatForm image directory:<br />{$GLOBALS['core']->home_dir}images/phatform<br />You will have to do this manually!<br />";

  PHPWS_File::makeDir($GLOBALS['core']->home_dir . "files/phatform");
  if(is_dir("{$GLOBALS['core']->home_dir}files/phatform")) {
    PHPWS_File::makeDir($GLOBALS['core']->home_dir . "files/phatform/export");
    PHPWS_File::makeDir($GLOBALS['core']->home_dir . "files/phatform/archive");
    $content .= "PhatForm files directory successfully created!<br />{$GLOBALS['core']->home_dir}files/phatform<br />";
  } else
    $content .= "Boost could not create the PhatForm files directory:<br />{$GLOBALS['core']->home_dir}files/phatform<br />You will have to do this manually!<br />";

} else
    $content .= "There was a problem writing to the database.<br />";

?>