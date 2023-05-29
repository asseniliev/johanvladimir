<?php
/**
 * This is the Pagemaster install file for Boost
 *
 * @version $Id: install.php,v 1.15 2004/06/14 21:13:11 darren Exp $
 * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

if($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/pagemaster/boost/install.sql", TRUE)) {
  $content .= "All PageMaster tables successfully written.<br />";
  
  if (!is_dir("{$GLOBALS['core']->home_dir}images/pagemaster"))
    PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/pagemaster");

  if(is_dir("{$GLOBALS['core']->home_dir}images/pagemaster"))
    $content .= "PageMaster image directory {$GLOBALS['core']->home_dir}images/pagemaster successfully created!<br />";
  else
    $content .= "PageMaster could not create the image directory: {$GLOBALS['core']->home_dir}images/pagemaster<br />You will have to do this manually!<br />";
  
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
}

?>
