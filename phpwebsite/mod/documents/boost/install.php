<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: install.php,v 1.6 2004/06/15 21:04:33 darren Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("Location: ./index.php");
  exit();
}

include_once(PHPWS_SOURCE_DIR.'mod/documents/conf/config.php');
require_once (PHPWS_SOURCE_DIR . "core/File.php");

$sqlalter = array();

require_once(PHPWS_SOURCE_DIR.'mod/documents/conf/form.php');

if($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/documents/boost/install.sql", TRUE)) {
  $content .= "All Documents tables successfully written.<br />";
  
  /* Alter the table to reflect the config file. */
  foreach($databaseColumns as $key=>$value) {
    $sqlalter[$value]=$databaseProperties[$key];
  }

  $GLOBALS['core']->sqlAddColumn("mod_documents_docs", $sqlalter);

  /* Create files directory */
  PHPWS_File::makeDir($GLOBALS['core']->home_dir . JAS_DOCUMENT_DIR);
  if(is_dir($GLOBALS['core']->home_dir . JAS_DOCUMENT_DIR)) {
    $content .= "Documents files directory successfully created!<br />" . $GLOBALS['core']->home_dir . JAS_DOCUMENT_DIR . "<br />";
  } else {
    $content .= "Boost could not create the Documents files directory:<br />" . $GLOBALS['core']->home_dir . JAS_DOCUMENT_DIR . "<br />You will have to do this manually!<br />";
  }

  /* Create images directory */
  PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/documents/");
  if(is_dir("{$GLOBALS['core']->home_dir}images/documents/")) {
    $content .= "Documents images directory successfully created!<br />" . "{$GLOBALS['core']->home_dir}images/documents/<br />";
  } else {
    $content .= "Boost could not create the Documents images directory:<br />" . "{$GLOBALS['core']->home_dir}images/documents/<br />You will have to do this manually!<br />";
  }

  PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "mod/documents/img/icons/button_view.png", $GLOBALS['core']->home_dir . "images/documents/", "button_view.png", false, false);

  PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "mod/documents/img/icons/button_edit.png", $GLOBALS['core']->home_dir . "images/documents/", "button_edit.png", false, false);

  PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "mod/documents/img/icons/button_delete.png", $GLOBALS['core']->home_dir . "images/documents/", "button_delete.png", false, false);

  PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "mod/documents/img/icons/button_download.png", $GLOBALS['core']->home_dir . "images/documents/", "button_download.png", false, false);

  $status = 1;

} else {
  $content .= "There was a problem writing to the database.<br />";
}

?>