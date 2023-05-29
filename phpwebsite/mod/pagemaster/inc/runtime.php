<?php

if($GLOBALS['module'] == "home") {
  require_once(PHPWS_SOURCE_DIR . "mod/pagemaster/class/Page.php");
  $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages", "mainpage", 1);
  if($result) {
    $_SESSION['SES_PM_page'] = new PHPWS_Page($result[0]["id"]);
    $_SESSION['SES_PM_page']->view_page();
  }
}

?>