<?php
/**
 * This is a skeleton version of an uninstall file for boost. Edit it to
 * be used with your module.
 *
 * $Id: uninstall.php,v 1.1.1.1 2004/10/21 17:45:30 steven Exp $
 */

/* Make sure the user is a deity before running this script */
if(!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR."mod/stats/boost/uninstall.sql", 1, 1)) {
  $content .= "All stats tables successfully removed.<br />";

  $status = 1;
}

?>