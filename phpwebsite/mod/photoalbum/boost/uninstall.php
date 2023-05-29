<?php

/**
 * @version $Id: uninstall.php,v 1.7 2003/10/30 17:05:30 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

require_once(PHPWS_SOURCE_DIR."core/File.php");

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/photoalbum/boost/uninstall.sql", 1, 1)) {
  $content .= "All photoalbum tables successfully removed.<br />";

  $ok = PHPWS_File::rmdir(PHPWS_HOME_DIR . "images/photoalbum/");
  if($ok) {
    $content .= "The photoalbum images directory was fully removed.<br />";
  } else {
    $content .= "The photoalbum images directory could not be removed.<br />";
  }

  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>