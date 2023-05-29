<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: uninstall.php,v 1.3 2004/03/11 21:14:51 darren Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/documents/boost/uninstall.sql", 1, 1)) {
  $content .= "All Documents tables successfully removed.<br />";

  $ok = PHPWS_File::rmdir(PHPWS_HOME_DIR . "files/documents/");
  if($ok) {
    $content .= "The Documents files directory was fully removed.<br />";
  } else {
    $content .= "The Documents files directory could not be removed.<br />";
  }

  $ok = PHPWS_File::rmdir(PHPWS_HOME_DIR . "images/documents/");
  if($ok) {
    $content .= "The Documents images directory was fully removed.<br />";
  } else {
    $content .= "The Documents images directory could not be removed.<br />";
  }

  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>