<?php

require_once (PHPWS_SOURCE_DIR . "core/File.php");

function archive($formId = NULL) {
  if(!isset($formId)) {
    $message = $_SESSION['translate']->it("No form ID was passed");
    return new PHPWS_Error("phatform", "archive()", $message, "continue", PHAT_DEBUG_MODE);
  }
 
  $archiveDir = "{$GLOBALS['core']->home_dir}files/phatform/archive/";
  $path = $archiveDir;

  clearstatcache();
  if(!is_dir($path)) {
    if(is_writeable($archiveDir)) {
      PHPWS_File::makeDir($path);
    } else {
      $message = $_SESSION['translate']->it("The archive path is not webserver writable.");
      return new PHPWS_Error("phatform", "archive()", $message, "continue", PHAT_DEBUG_MODE);
    }
  } else if(!is_writeable($path)) {
    $message = $_SESSION['translate']->it("The archive path is not webserver writable.");
    return new PHPWS_Error("phatform", "archive()", $message, "continue", PHAT_DEBUG_MODE);    
  }

  include(PHPWS_SOURCE_DIR . $GLOBALS['core']->configFile);

  $table = array();
  $time = time();

  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_forms";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_forms_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_form_" . $formId;
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_form_" . $formId . "_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_multiselect";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_multiselect_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_checkbox";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_checkbox_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_dropbox";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_dropbox_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_options";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_options_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_radiobutton";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_radiobutton_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_textarea";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_textarea_seq";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_textfield";
  $table[] = "{$GLOBALS['core']->tbl_prefix}mod_phatform_textfield_seq";

  for($i=0; $i<sizeof($table); $i++) {
    $pipe = " >> ";
    if($i == 0) {$pipe = " > ";}
    $goCode = "mysqldump -h" . $dbhost . " -u" . $dbuser . " -p" . $dbpass . " " . $dbname . " " . $table[$i]  . $pipe . $path . $formId . "." . $time . ".phat";
    system($goCode);
  }
}

?>