<?php

/**
 * TODO: add printout of sql information from tables hit to queries executed
 */

/**
 * debug.php
 *
 * Main control switch and debug display
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @version $Id: debug.php,v 1.13 2003/10/27 21:13:09 steven Exp $
 */
if (!DEBUG_MODE)
     return;

if(isset($DBUG_op)) {
  $GLOBALS['CNT_debug']['title'] = $_SESSION['translate']->it("phpWebSite Debugger");
  /* Begin Switch */
  switch($DBUG_op) {
  case "admin_settings":
    $_SESSION['PHPWS_Debug']->adminSettings();
    break;
    
  case "save_settings":
    $_SESSION['PHPWS_Debug']->saveSettings();
    $_SESSION['PHPWS_Debug']->adminSettings();
    break;

  case "setActivity":
    $_SESSION['PHPWS_Debug']->setActivity();
    PHPWS_WizardBag::home();
    break;
  }
  /* End Switch */
}

?>
