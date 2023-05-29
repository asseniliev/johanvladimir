<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}
require_once (PHPWS_SOURCE_DIR . "core/File.php");

if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/branch/boost/install.sql", 1, 1)){
  if (!is_dir(PHPWS_SOURCE_DIR . "conf/branch/")){
    if (PHPWS_File::makeDir(PHPWS_SOURCE_DIR . "conf/branch/"))
      $content .= "Branch configuration directory created successfully.<br />";
    else
      $content .= "Branch configuration directory WAS NOT created successfully.<br /><b>" . PHPWS_SOURCE_DIR ."/conf/branch/<br />";
  }

}
?>
