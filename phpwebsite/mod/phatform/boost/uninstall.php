<?php
/**
 * Uninstall file for PhatForm v2
 *
 * @version $Id: uninstall.php,v 1.10 2004/11/29 18:01:19 darren Exp $
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

/* Remove any dynamic tables */
$sql = "SELECT id, archiveTableName FROM {$GLOBALS['core']->tbl_prefix}mod_phatform_forms WHERE saved='1'";
$result = $GLOBALS["core"]->getAll($sql);
if(sizeof($result) > 0) {
  foreach($result as $form) {
    if($form["archiveTableName"] == NULL) {
      $table = $GLOBALS['core']->tbl_prefix . "mod_phatform_form_" . $form["id"];
      if($GLOBALS["core"]->sqlTableExists($table)) {
	$result = $GLOBALS["core"]->getAll("SELECT * FROM $table");
	$sql = "DROP TABLE $table";
	$GLOBALS["core"]->query($sql);
      
	if(sizeof($result) > 0) {
	  if($GLOBALS["core"]->sqlTableExists($table . "_seq")) {	  
	    $sql = "DROP TABLE {$table}_seq";
	    $GLOBALS["core"]->query($sql);
	  }
	}
      }
    
    } else {
      $table = $GLOBALS['core']->tbl_prefix . $form["archiveTableName"];
      if($GLOBALS["core"]->sqlTableExists($table)) {
	$sql = "DROP TABLE $table";
	$GLOBALS["core"]->query($sql);
      }
    }
  }
  $content .= "Removed all dynamic phatform tables successfully!";
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/phatform/boost/uninstall.sql", 1, 1)) {
  $content .= "All phatform static tables successfully removed.<br />";
  $content .= "Removing images directory " . PHPWS_SOURCE_DIR . "images/phatform<br />";
  system("rm -rf " . PHPWS_HOME_DIR . "images/phatform", $temp);
  $status =1;

  if(isset($_SESSION["OBJ_approval"]))
    $_SESSION["OBJ_approval"]->unregister_module("phatform");

} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>