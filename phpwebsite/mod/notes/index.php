<?php
/**
 * @version $Id: index.php,v 1.18 2004/05/20 19:59:06 darren Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */

if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

/* Check to see if Note session is set and set it if it's not. */
if(!isset($_SESSION["SES_NOTE_MANAGER"])) {
  $_SESSION["SES_NOTE_MANAGER"] = new PHPWS_NoteManager;
}

if($GLOBALS["module"] == "notes") {
  $GLOBALS["CNT_notes"] = array("title"=>$_SESSION["translate"]->it("Notes"),
				"content"=>NULL);
}

/* Check to see if an operation was recieved */
if(!isset($_SESSION["OBJ_user"]->username)) {
  $content = "<span style=\"color:red\">" . $_SESSION["translate"]->it("You must be logged in to use Notes.") . "</span>";
  $GLOBALS["CNT_notes"]["content"] .= $content;

} else if(isset($_REQUEST["NOTE_op"]) && isset($_SESSION["OBJ_user"]->username)){
  if (is_array($_REQUEST["NOTE_op"]))
    list($operation,) = each ($_REQUEST["NOTE_op"]);
  else
    $operation = $_REQUEST["NOTE_op"];
  
  switch($operation) {
  case "menu":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    break;

  case "adminMenu":
    $GLOBALS["CNT_notes"]["title"] = $_SESSION["translate"]->it("Multi-Send Notes");
    $_SESSION["SES_NOTE_MANAGER"]->adminOptions();
    break;

  case "selectGroup":
    $GLOBALS["CNT_notes"]["title"] = $_SESSION["translate"]->it("Multi-Send Notes");
    $_SESSION["SES_NOTE_MANAGER"]->chooseUserGroup(TRUE);
    break;

  case "admin_send":
    $GLOBALS["CNT_notes"]["title"] = $_SESSION["translate"]->it("Multi-Send Notes");
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE_MANAGER"]->adminSend();
    break;

  case "new_note":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE"] = new PHPWS_Note;
    $_SESSION["SES_NOTE"]->edit();
    break;

  case "my_notes":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE_MANAGER"]->myNotes();
    break;

  case "sent_notes":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE_MANAGER"]->sentNotes();
    break;

  case "selectUser":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE_MANAGER"]->chooseUserGroup();
    break;

  case "send_note":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE"]->send();
    break;

  case "read":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    if(isset($_REQUEST["NOTE_id"]) && is_numeric($_REQUEST["NOTE_id"])) {
      $_SESSION["SES_NOTE"] = new PHPWS_Note($_GET["NOTE_id"]);
      $_SESSION["SES_NOTE"]->read();
    }
    break;

  case "reply":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    
    if(isset($_REQUEST["NOTE_id"]) && isset($_REQUEST["NOTE_id"]) && is_numeric($_REQUEST["NOTE_id"]))
      $tmp = new PHPWS_Note($_REQUEST["NOTE_id"]);
    
    $_SESSION["SES_NOTE"] = new PHPWS_Note();
    if(isset($_REQUEST["NOTE_RPY_User"])) {
      $_SESSION["SES_NOTE"]->_toUser = $tmp->_fromUser;
      
      if(substr($tmp->_subject, 0, 3) != "Re:")
	$_SESSION["SES_NOTE"]->_subject = "Re: ".$tmp->_subject;
      else
	$_SESSION["SES_NOTE"]->_subject = $tmp->_subject;

      $text_array = PHPWS_Text::sentence($tmp->_message);
      $lines = count($text_array);
      
      $count = 0;
      $content = "\r\n\r\n\r\n----- " . $_SESSION["SES_NOTE"]->_toUser . " wrote:\r\n";
      $tmp_str = "";

      foreach ($text_array as $sentence){
	$tmp_str = $sentence;
	$index = 0;
	$content .= "<  ";
	
	while ($index < strlen($tmp_str)) {
	  if($tmp_str[$index] == '\r\n') {
	    $content .= "\r\n<t ";

	  } else if($count >= 40) {
	    if($tmp_str[$index] == " " || $tmp_str[$index] == ".") {
	      $content .= "\r\n< " . $tmp_str[$index];
	      $count = 0;
	    } else {
	      $content .= $tmp_str[$index];
	    }
	  } else {
	    $content .= $tmp_str[$index];
	  }

	  $count++;
	  $index++;
	}

	$count = 0;
	
	$content .= "\r\n";
      }
      
      $_SESSION["SES_NOTE"]->_message = $content;
    }
    $_SESSION["SES_NOTE"]->edit();
    break;
    
  case "delete":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    if(isset($_REQUEST["NOTE_id"]) && is_numeric($_REQUEST["NOTE_id"])) {
      $_SESSION["SES_NOTE"] = new PHPWS_Note($_REQUEST["NOTE_id"]);
      $_SESSION["SES_NOTE"]->delete();
    }
    break;

  case "deleteSent":
    $_SESSION["SES_NOTE_MANAGER"]->menu();
    $_SESSION["SES_NOTE"] = new PHPWS_Note($_REQUEST["NOTE_id"]);
    $_SESSION["SES_NOTE"]->deleteSent();
    break;
  }// END INDEX SWITCH
}

?>