<?php
if(!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR."mod/menuman/boost/uninstall.sql", 1, 1)) {
  $content .= "All Menu Manager tables successfully removed.<br />";
  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
}

$ok = PHPWS_File::rmdir("images/menuman/");
if($ok) {
  $content .= "The menuman images directory was fully removed.<br />";
} else {
  $content .= "The menuman images directory could not be removed.<br />";
}

$_SESSION['OBJ_menuman'] = NULL;
unset($_SESSION['OBJ_menuman']);

?>
