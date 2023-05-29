<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR."mod/blockmaker/boost/install.sql", 1, 1)){
  $_SESSION['translate']->registerModule("blockmaker", "mod_blockmaker_data", "block_id", "block_title:block_content:block_footer");
  $content .= "All Blockmaker tables successfully written.<br />";
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
  $status = 0;
}
  

?>
