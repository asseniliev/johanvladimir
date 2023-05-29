<?php
/**
 * This is the uninstall file for Notes. It is used by Boost.
 *
 * @version $Id: uninstall.php,v 1.4 2004/03/11 21:15:34 darren Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR."mod/notes/boost/uninstall.sql", 1, 1)) {
  $content .= "All Note tables successfully removed.<br />";

  $status = 1;
} else
$content .= "There was a problem accessing the database.<br />";

?>