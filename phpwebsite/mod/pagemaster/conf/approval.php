<?php

if($_SESSION["OBJ_user"]->allow_access("pagemaster")){
  require_once(PHPWS_SOURCE_DIR.'mod/pagemaster/class/Page.php');
  if ($approvalChoice == "yes"){
    PHPWS_Page::approve($id);
  } else if ($approvalChoice == "no") {
    PHPWS_Page::refuse($id);
  } else if ($approvalChoice == "view") {
    $_SESSION['SES_PM_page'] = new PHPWS_Page($id);
    $_SESSION['SES_PM_page']->view_page();
    echo $_SESSION['OBJ_layout']->popbox($GLOBALS['CNT_pagemaster']['title'], $GLOBALS['CNT_pagemaster']['content']);
  }
}

?>