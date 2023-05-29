<?php
/**
 * This is the Pagemaster uninstall file for Boost
 *
 * @version $Id: uninstall.php,v 1.7 2004/03/11 21:15:41 darren Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/pagemaster/boost/uninstall.sql", 1, 1)) {
  $content .= "All tables successfully removed.<br />";
  $content .= "Removing images directory " . PHPWS_HOME_DIR . "images/pagemaster<br />";

  PHPWS_File::rmdir(PHPWS_HOME_DIR . "images/pagemaster/");

  $_SESSION["SES_PM_master"] = NULL;
  $_SESSION["SES_PM_page"] = NULL;
  $_SESSION["SES_PM_section"] = NULL;
  $_SESSION["SES_PM_error"] = NULL;

  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
}

?>