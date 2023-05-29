<?php

if($_SESSION["OBJ_user"]->allow_access("announce")){
  require_once(PHPWS_SOURCE_DIR.'mod/faq/class/FaqManager.php');

  if ($approvalChoice == "yes"){
    PHPWS_FaqManager::approvalApprove($id);
  } else if ($approvalChoice == "no") {
    PHPWS_FaqManager::approvalRefuse($id);
  } else if ($approvalChoice == "view") {
    $_SESSION["SES_FAQ_MANAGER"] = new PHPWS_FaqManager;
    require_once(PHPWS_SOURCE_DIR.'mod/faq/class/Faq.php');
    $_SESSION["SES_FAQ_MANAGER"]->_currentFAQ = new PHPWS_Faq($id);
    $_SESSION["SES_FAQ_MANAGER"]->_currentFAQ->view(0, 0, NULL, TRUE);
  }
}

?> 