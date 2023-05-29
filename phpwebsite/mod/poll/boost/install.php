<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

  if($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/poll/boost/install.sql", TRUE)) {
    $content .= "All Poll tables successfully written.<br />";

    $status = 1;
  } else
    $content .= "There was a problem writing to the database.<br />";

?>
