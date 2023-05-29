<?php
/**
 * @version $Id: install.php,v 1.4 2004/03/11 21:15:34 darren Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

  if($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/notes/boost/install.sql", TRUE)) {
    $content .= "All Note tables successfully written.<br />";

    $status = 1;
  } else
    $content .= "There was a problem writing to the database.<br />";

?>