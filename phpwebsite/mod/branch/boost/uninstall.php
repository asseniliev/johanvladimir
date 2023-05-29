<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlTableExists("branch_sites", TRUE)){
  if($GLOBALS['core']->sqlDropTable("branch_sites")) {
    $content .= "All branch tables successfully removed.<br />";

    if (is_dir(PHPWS_SOURCE_DIR . "conf/branch/")){
      if(PHPWS_File::rmdir(PHPWS_SOURCE_DIR . "conf/branch/")) {
	$content .= "Removed branch configuration directory.<br />";	
      } else {
	$content .= "The branch configuration directory could not be removed.<br />";	
      }
    }

    $status = 1;
  } else {
    $content .= "There was a problem removing the branch tables.<br />";
    $status = 0;
  }
} else {
  $content .= "Branch table does not exist. <br />";
  $status = 0;  
}    


?>