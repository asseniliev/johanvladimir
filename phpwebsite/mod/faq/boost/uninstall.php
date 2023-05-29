<?php

/**
 * Uninstall file for module faq.(used by Boost)
 *
 * @version $Id: uninstall.php,v 1.10 2004/03/11 21:14:58 darren Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 *
 */

/* Check to see if administrator */
if(!$_SESSION["OBJ_user"]->isDeity()) {
  header("location:index.php");
  exit();
}

if($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR. "mod/faq/boost/uninstall.sql", 1, 1)) {
  $content = "Unregistered FAQ from help module.<br />";

  if(isset($_SESSION["OBJ_menuman"])) {
    if($GLOBALS["core"]->sqlDelete("mod_menuman_items", "menu_item_url", "%module=faq%", "LIKE"))
      $content .= "Removed link to FAQ from menu.<br />";
  }

  $content .= "<br />All FAQ tables were successfully removed from the database.<br /><br />";
  $GLOBALS['core']->killSession("SES_FAQ_STATS");
  $GLOBALS['core']->killSession("SES_FAQ_MANAGER");

  $status = 1;
} else {
  $content = "There was a problem accessing the database.<br />";
}

?>