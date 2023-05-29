<?php

if ($_SESSION["OBJ_user"]->allow_access("linkman")){

  require_once(PHPWS_SOURCE_DIR . "mod/linkman/class/Link.php");

  if ($approvalChoice == "yes"){
    PHPWS_Link::approve($id);
  } else if ($approvalChoice == "no") {
    PHPWS_Link::refuse($id);
  } else if ($approvalChoice == "view") {
    $link = new PHPWS_Link($id);
    echo $link->view();
  }
}

?>