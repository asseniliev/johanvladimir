<?php

function showUserBox() {
  $sql = "SELECT id FROM ".PHPWS_TBL_PREFIX."mod_poll WHERE active='Yes'";
  $result = $GLOBALS["core"]->getOne($sql, FALSE);
  if(isset($result)) {
    $userbox_poll = new PHPWS_Poll($result);
    $userbox_poll->showUserBox();
  }
}

if($GLOBALS['module'] == "home") {
  require_once(PHPWS_SOURCE_DIR.'mod/poll/class/Poll.php');
  showUserBox();
}

?>