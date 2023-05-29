<?php
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

if(isset($_REQUEST["module"]) && $_REQUEST["module"] == "poll") {
  
  $GLOBALS['CNT_POLL'] = array("title"=>$_SESSION['translate']->it("Poll Manager"),
			       "content"=>NULL);

  if(!isset($_SESSION["SES_POLL"])) {
    $_SESSION["SES_POLL"] = new PollManager;
  }

  if(isset($_REQUEST["PHPWS_MAN_OP"])) {
    $_SESSION["SES_POLL"]->managerAction();
  }

  $_SESSION["SES_POLL"]->action();  
}


?>
