<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR."mod/linkman/boost/uninstall.sql", 1, 1)){
  $content .= "All Link Manager tables successfully removed.<br />";

  $status = 1;

} else
  $content .= "There was a problem accessing the database.<br />";

?>