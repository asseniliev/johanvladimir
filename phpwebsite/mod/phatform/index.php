<?php

/**
 * Index file for phatform module
 *
 * @version $Id: index.php,v 1.25 2004/06/02 21:11:37 darren Exp $
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

/* Include the phatform config file */
include(PHPWS_SOURCE_DIR . "mod/phatform/conf/phatform.php");
require_once(PHPWS_SOURCE_DIR . "mod/phatform/class/advViews.php");

if($GLOBALS["module"] == "phatform") {
  $GLOBALS['CNT_phatform'] = array("title"=>NULL,
				   "content"=>NULL);
}

/* Look for the PHAT MAN :) */
if(isset($_REQUEST["PHAT_MAN_OP"])) {
  $_SESSION["PHAT_FormManager"]->managerAction();
  $_SESSION["PHAT_FormManager"]->action();
}

if(isset($_REQUEST["EXPORT_OP"])) {
  $_SESSION["PHAT_advViews"]->exportActions();
} else if(isset($_REQUEST["ARCHIVE_OP"])) {
  $_SESSION["PHAT_advViews"]->archiveActions();
}

/* Check for PHAT_Form operation */
if(isset($_REQUEST["PHAT_FORM_OP"])) {
  check_session();
  $_SESSION["PHAT_FormManager"]->form->action();
}

/* Where's the PHAT EL? */
if(isset($_REQUEST["PHAT_EL_OP"])) {
  check_session();
  $_SESSION["PHAT_FormManager"]->form->element->action();
}

/* Check to see if there is a reprt operation */
if(isset($_REQUEST["PHAT_REPORT_OP"])) {
  check_session();
  $_SESSION["PHAT_FormManager"]->form->report->action();
}

function check_session() {
  if(!isset($_SESSION["PHAT_FormManager"]->form)) {
    header("Location: ./index.php?module=users&norm_user_op=signup&error=timeout");
    exit();
  } 
}

?>