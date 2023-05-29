<?php
/**
 * @version $Id: install.php,v 1.14 2004/06/14 21:16:28 darren Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 */

/* Make sure user is logged in as administrator */
if(!$_SESSION["OBJ_user"]->isDeity()) {
  header("location:index.php");
  exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

if($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . 
			       "mod/faq/boost/install.sql", TRUE)) { 

  /* get default legend from file and save to database */
  require_once("defaultScores.php");
  $queryData["score_text"] = serialize($ratings);

  if(!$GLOBALS["core"]->sqlInsert($queryData, "mod_faq_settings")) 
    $content .= "Error saving default scoring legend to database.<br />";

  $content .= "<br />All FAQ tables were successfully written to the database.<br /><br />";

  /* Create image directory */
  PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/faq");
  if(is_dir("{$GLOBALS['core']->home_dir}images/faq")) {
    $content .= "FAQ images directory successfully created!<br />{$GLOBALS['core']->home_dir}images/faq<br />";
  } else {
    $content .= "Boost could not create the FAQ image directory:<br />{$GLOBALS['core']->home_dir}images/faq<br />You will have to do this manually!<br />";
  }

  $status = 1;

} else {
  $status = 0;
  $content = "There was a problem with accessing the database.<br />";
}

?>