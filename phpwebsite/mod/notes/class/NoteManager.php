<?php

require_once(PHPWS_SOURCE_DIR . 'core/Form.php');
require_once(PHPWS_SOURCE_DIR . 'core/List.php');

/**
 * This class controls interactions with the Notes module and it's PHPWS_Note objects.
 *
 * @version $Id: NoteManager.php,v 1.24 2005/05/25 16:03:59 darren Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Notes
 */
class PHPWS_NoteManager {

  var $_sendOption = "all_users";

  /**
   * Displays the main menu for Notes
   *
   * @access public
   */
  function menu() {
    $tags = array();

    $tags["NEW_NOTE"]   = "<a href=\"./index.php?module=notes&amp;NOTE_op=new_note\">".$_SESSION["translate"]->it("New Note") ."</a>";
    $tags["MY_NOTES"]   = "<a href=\"./index.php?module=notes&amp;NOTE_op=my_notes\">".$_SESSION["translate"]->it("My Notes") ."</a>";
    $tags["SENT_NOTES"] = "<a href=\"./index.php?module=notes&amp;NOTE_op=sent_notes\">".$_SESSION["translate"]->it("Sent Notes") ."</a>";

    $content = PHPWS_Template::processTemplate($tags, "notes", "menu.tpl");

    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC menu()

  function adminOptions() {
    $show_form = FALSE;
    $form = new EZform("notes_admin_options");
    $availGroups = PHPWS_Note::_getGroups();

    if(!isset($_REQUEST["SEND_ERR"])) {
      $_SESSION["SES_NOTE"]->_subject = NULL;		
      $_SESSION["SES_NOTE"]->_message = NULL;
    }	

    if($_SESSION["OBJ_user"]->allow_access("notes", "contact_all_users")) {
      $show_form = TRUE;
      if(count($availGroups) > 1 && $_SESSION["OBJ_user"]->allow_access("notes", "contact_groups")) {
	$form->add("send_option", "radio", array("all_users", "groups"));	
	$form->setMatch("send_option", $this->_sendOption);
      } else {
	$form->add("send_option", "checkbox", "all_users");
      }
    }

    if($_SESSION["OBJ_user"]->allow_access("notes", "contact_groups") && count($availGroups) > 1) {
      $show_form = TRUE;
      if(isset($_SESSION["SES_NOTE"]->_toGroup))
	$form->add("to_group_field", "text", $_SESSION["SES_NOTE"]->_toGroup);
      else
	$form->add("to_group_field", "text");
    } 

    if($show_form == TRUE) {
      if(isset($_SESSION["SES_NOTE"]->_subject)) 
	$form->add("subject_field", "text", htmlspecialchars(PHPWS_Text::parseInput($_SESSION["SES_NOTE"]->_subject)));
      else
	$form->add("subject_field", "text");  
      
      if(isset($_SESSION["SES_NOTE"]->_message))
        $form->add("message_field", "textarea", htmlspecialchars(PHPWS_Text::parseInput($_SESSION["SES_NOTE"]->_message)));
      else
	$form->add("message_field", "textarea");
      
      $form->setWidth("subject_field", 70);
      $form->setCols("message_field", 45);
      $form->setRows("message_field", 13);
      $form->add("send_button", "submit", $_SESSION["translate"]->it("Send"));
      $form->add("module", "hidden", "notes");
      $form->add("NOTE_op", "hidden", "admin_send");

      $tags = array();
      $tags = $form->getTemplate();
      
      if($_SESSION["OBJ_user"]->allow_access("notes", "contact_all_users")) 
	$tags["ALL_USERS_LABEL"] = $_SESSION["translate"]->it("Send a note to all users.");
      
      if($_SESSION["OBJ_user"]->allow_access("notes", "contact_groups") && count($availGroups) > 1) {
	$tags["GROUPS_LABEL"]    = $_SESSION["translate"]->it("Send a note to a group of users: ");
	$tags["VIEW_GROUPS"] = "<a href=\"./index.php?module=notes&amp;NOTE_op=selectGroup\">" . $_SESSION["translate"]->it("View List of Groups");
      } else { 
	$tags["SEND_OPTION_2"] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	if(count($availGroups) == 1) {
	  $tags["GROUPS_LABEL"] = "<br />" . $_SESSION["translate"]->it("The group send option is disabled since there are no user groups.");
	}
      }

      $tags["SEND_OPTION_LABEL"] = $_SESSION["translate"]->it("Send Options:");
      $tags["SUBJECT_LABEL"] = $_SESSION["translate"]->it("Subject:  ");
      $tags["MESSAGE_LABEL"] = $_SESSION["translate"]->it("Message: ");
    
      $content = PHPWS_Template::processTemplate($tags, "notes", "adminOptions.tpl");
    } else {
      $content = $_SESSION["translate"]->it("You do not have permission to either send notes to all users or send notes to groups.");
    }

    $GLOBALS["CNT_notes"]["content"] .= $content;
  }

  function adminSend() {
    if(!$_SESSION["OBJ_user"]->allow_access("notes", "contact_all_users")) {
      $_REQUEST["send_option"] = "groups";
    }

    $_SESSION["SES_NOTE"]->_subject = $_REQUEST["subject_field"];
    $_SESSION["SES_NOTE"]->_message = $_REQUEST["message_field"];

    if(isset($_REQUEST["send_option"])) {
      $this->_sendOption = $_REQUEST["send_option"];

      // check subject
      if(!isset($_REQUEST["subject_field"]) || empty($_REQUEST["subject_field"]))
	$error = new PHPWS_Error("notes", "adminSend", $_SESSION["translate"]->it("No subject provided."));	

      // check message
      if(!isset($_REQUEST["message_field"]) || empty($_REQUEST["message_field"]))
	$error = new PHPWS_Error("notes", "adminSend", $_SESSION["translate"]->it("No message provided."));	

      // branch on send option
      if($_REQUEST["send_option"] == "all_users") {
	$this->sendAllUsersNote($_SESSION["SES_NOTE"]->_subject, 
				$_SESSION["SES_NOTE"]->_message);

      } else if($_REQUEST["send_option"] == "groups") {
	if(isset($_REQUEST["to_group_field"]) &&
	   $GLOBALS["core"]->sqlSelect("mod_user_groups", "group_name", $_REQUEST["to_group_field"])) {

	  $this->sendGroupNote($_REQUEST["to_group_field"], 
			       $_SESSION["SES_NOTE"]->_subject, 
			       $_SESSION["SES_NOTE"]->_message);
	  
	} else {
	  $error = new PHPWS_Error("notes", "adminSend", $_SESSION["translate"]->it("Invalid group name."));
	}

      }
    } else {
      $error = new PHPWS_Error("notes", "adminSend", $_SESSION["translate"]->it("No selection made for send option."));

    }
    
    if(isset($error) && PHPWS_Error::isError($error)) {
      $GLOBALS["CNT_notes"]["content"] = "";
      $error->message("CNT_notes");
      $_REQUEST["SEND_ERR"] = TRUE;
      $_SESSION["SES_NOTE_MANAGER"]->adminOptions();

    }
  }

  function sendToMultiSender($message, $subject, $groupName=NULL) {
    $note["toUser"]     = $_SESSION["OBJ_user"]->username;
    $note["toUserHide"] = 1;
    $note["fromUser"]   = $_SESSION["OBJ_user"]->username;
    $note["fromUserHide"] = 0;
    $note["message"]  = $message;
    $note["dateSent"] = date("Y-m-d H:i:s");
    $note["userRead"] = $_SESSION["OBJ_user"]->username;
    $note["subject"]  = $_SESSION["translate"]->it("Sent note with subject ") .          "'" . $subject . "'"; 
    if($groupName)
      $note["subject"] .= $_SESSION["translate"]->it(" to the group ") . $groupName . ".";
    else
      $note["subject"] .= $_SESSION["translate"]->it(" to all users.");
				 

    $GLOBALS["core"]->sqlInsert($note, "mod_notes");
  }

  function sendAllUsersNote($subject, $message) {
    if(isset($_POST["yes"])) {

      $sql = "select username from mod_users";
      $usersRows = $GLOBALS["core"]->query($sql, TRUE);
      
      while($row = $usersRows->fetchRow()) {
	if($row["username"] != $_SESSION["OBJ_user"]->username) {
	  $note["toUser"] = $row["username"];
	  $note["toUserHide"] = 0;
	  $note["fromUser"]   = $_SESSION["OBJ_user"]->username;
	  $note["fromUserHide"] = 1;
	  $note["message"]  = $message;
	  $note["dateSent"] = date("Y-m-d H:i:s");
	  $note["userRead"] = NULL;
	  $note["subject"]  = $subject;
	
	  $GLOBALS["core"]->sqlInsert($note, "mod_notes");
	}
      }
      
      $this->sendToMultiSender($message, $subject);

      $content = "<br />".$_SESSION["translate"]->it("Sent note to all users.");
      $content .= $this->getLinkToAdmin();      
      $GLOBALS["CNT_notes"]["content"] = $content;

    } elseif(isset($_POST["no"])) {
      $GLOBALS["CNT_notes"]["content"] = "";
      $this->adminOptions();
      return;

    } else {
      $elements[0]  = PHPWS_Form::formHidden("module", "notes");
      $elements[0] .= PHPWS_Form::formHidden("NOTE_op", "admin_send");
      $elements[0] .= PHPWS_Form::formHidden("send_option", $_REQUEST["send_option"]);

      $elements[0] .= PHPWS_Form::formHidden("subject_field", htmlspecialchars(PHPWS_Text::parseInput($_REQUEST["subject_field"])));
      $elements[0] .= PHPWS_Form::formHidden("message_field", htmlspecialchars(PHPWS_Text::parseInput($_REQUEST["message_field"])));
      
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Yes"), "yes");
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("No"), "no");
      
      $content = "<br />&nbsp;<span style=\"color:red;\"><b>" . $_SESSION["translate"]->it("Are you sure you want to send this note to **ALL** users??")."</b></span><br /><br />";
      $content .= PHPWS_Form::makeForm("notes_send_all", "index.php", $elements);      
      $GLOBALS["CNT_notes"]["content"] = $content;
    }
  }

  function sendGroupNote($groupname, $subject, $message) {
    $groupInfo = $GLOBALS["core"]->sqlSelect("mod_user_groups", "group_name", $groupname);

    if($groupInfo[0]["members"]) {
      $members =  explode(":", $groupInfo[0]["members"]);

      $note = array();
      foreach($members as $member) {
	$sql = "select username from mod_users where user_id = $member";
	$toUser = $GLOBALS["core"]->getOne($sql, TRUE);
	if($toUser != $_SESSION["OBJ_user"]->username) {
	   $note["toUser"] = $toUser;
	   $note["toUserHide"] = 0;
	   $note["toGroup"]    = $groupname;
	   $note["fromUser"]   = $_SESSION["OBJ_user"]->username;
	   $note["fromUserHide"] = 1;
	   $note["message"]  = $message;
	   $note["dateSent"] = date("Y-m-d H:i:s");
	   $note["userRead"] = NULL;
	   $note["subject"]  = $subject;
		
	   $GLOBALS["core"]->sqlInsert($note, "mod_notes");
	}
      }

      $this->sendToMultiSender($message, $subject, $groupname);
      
      $content = "<br />" . $_SESSION["translate"]->it("Sent note to group members.");
      $content .= $this->getLinkToAdmin();

      $GLOBALS["CNT_notes"]["content"] = $content;
    } else {
      $content = "<br />" . $_SESSION["translate"]->it("There are no members in this group.");
      $content .= $this->getLinkToAdmin();      

      $GLOBALS["CNT_notes"]["content"] = $content;
    }
  }

  function getLinkToAdmin() {
    return "<br /><br /><a href=\"index.php?module=notes&amp;NOTE_op=adminMenu\">Back</a>";
  }

  /**
   * Displays the current user's notes in a list format.
   *
   * @access public
   * @see    _showNotes()
   */
  function myNotes() {
    /* Get all notes that were sent to this user */
    $result = $GLOBALS["core"]->sqlSelect("mod_notes", "toUser", $_SESSION["OBJ_user"]->username);

    /* Create a list of notes depending on result */
    $content = "<h3>" . $_SESSION["translate"]->it("My Notes") . "</h3>";
    $content .= $this->_showNotes($result, TRUE);

    /* Display notes */
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC myNotes()

  /**
   * Displays the current user's sent messages in a list format
   *
   * @access public
   * @see    _showNotes()
   */
  function sentNotes() {
    /* Get all notes sent by the current user */
    $match_columns["fromUser"] = $_SESSION["OBJ_user"]->username;
    $match_columns["fromUserHide"] = 0;
    $result = $GLOBALS["core"]->sqlSelect("mod_notes", $match_columns);

    /* Create a list of notes depending on result */
    $content = "<h3>" . $_SESSION["translate"]->it("Sent Notes") . "</h3>";
    $content .= $this->_showNotes($result);

    /* Display notes */
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }//END FUNC sentNotes()

  /**
   *
   * Displays my notes or sent notes.
   *
   */
  function _showNotes($result, $myNote=FALSE) {
    $itemTags = array();
    $listTags = array();

    /* Check for result and create table containing sent messages or warn that no messages were found */
    if($result) {
      $listTags["LIST_ITEMS"] = NULL;

      /* Build headers for table containing messages */
      $itemTags["STYLE_CLASS"] = " class=\"bg_light\"";
      $itemTags["TITLE"] = "<b>" . $_SESSION["translate"]->it("Subject") . "</b>";
      if(!isset($_REQUEST["NOTE_op"]["sent_notes"]) || (isset($_REQUEST["NOTE_op"]) && $_REQUEST["NOTE_op"] == "my_notes"))
        $itemTags["FROM"]  = "<b>" . $_SESSION["translate"]->it("From") . "</b>";

      if(!isset($_REQUEST["NOTE_op"]["my_notes"]))
        $itemTags["TO"]    = "<b>" . $_SESSION["translate"]->it("To") . "</b>";
      $itemTags["DATE_SENT"] = "<b>" . $_SESSION["translate"]->it("Date Sent") . "</b>";
      $itemTags["NEW"] = "<b>" . $_SESSION["translate"]->it("Status") . "</b>";
      $itemTags["ACTIONS"] = "<b>" . $_SESSION["translate"]->it("Actions") . "</b>";

      $listTags["LIST_ITEMS"] .= PHPWS_Template::processTemplate($itemTags, "notes", "list_item.tpl");
      $itemTags["STYLE_CLASS"] = NULL;

      /* Build table of actual messages */
      foreach($result as $resultRow) {
	$itemTags["TITLE"] = "<a href=\"index.php?module=notes&amp;NOTE_op=read&amp;NOTE_id=" . $resultRow["id"] . "\">" . $resultRow["subject"] . "</a>";

	if(!isset($_REQUEST["NOTE_op"]["sent_notes"]) || (isset($_REQUEST["NOTE_op"]) && $_REQUEST["NOTE_op"] == "my_notes"))
	  $itemTags["FROM"] = $resultRow["fromUser"];

	if(!isset($_REQUEST["NOTE_op"]["my_notes"]))
	  $itemTags["TO"]    = $resultRow["toUser"];
	$itemTags["DATE_SENT"] = $resultRow["dateSent"];
	$resultRow["dateRead"] != "0000-00-00 00:00:00" ? $itemTags["NEW"] = "<i>" . $_SESSION["translate"]->it("READ") .
	  "</i>" : $itemTags["NEW"] = "<span style=\"color:green;\"><b><i>" . $_SESSION["translate"]->it("NEW") . "</i></b></span>";

	if($myNote) {
	  $delete_op = "delete";
	} else {
	  $delete_op = "deleteSent";
	}

          $itemTags["ACTIONS"] = "<a href=\"index.php?module=notes&amp;NOTE_op=$delete_op&amp;NOTE_id=" . $resultRow["id"] . "\">" .
            $_SESSION["translate"]->it("Delete") . "</a>";


        $myNote ? $itemTags["ACTIONS"] .= "&nbsp;&nbsp;&nbsp;<a href=\"index.php?module=notes&amp;NOTE_id=".
          $resultRow["id"] . "&amp;NOTE_op=reply&amp;NOTE_RPY_User=" . $resultRow["fromUser"] . "\">" .
          $_SESSION["translate"]->it("Reply") . "</a>" : NULL;

        $listTags["LIST_ITEMS"] .= PHPWS_Template::processTemplate($itemTags, "notes", "list_item.tpl");
      }
      $content = PHPWS_Template::processTemplate($listTags, "notes", "list.tpl");
    } else {
      /* Oops, no messages sent out by this user */
      $content = $_SESSION["translate"]->it("No messages found!");
    }
    return $content;
  }

  /**
   *
   *  Displays a list of user with an a-z listings.
   * 
   */
  function chooseUserGroup($group_view=FALSE) {
    if(isset($_REQUEST["NOTE_touser"])) {
      $_SESSION["SES_NOTE"]->_toUser = $_REQUEST["NOTE_touser"];
      $_SESSION["SES_NOTE"]->edit();
      return;

    } else if(isset($_REQUEST["NOTE_toGroup"])) {
      $_SESSION["SES_NOTE"]->_toGroup = $_REQUEST["NOTE_toGroup"];
      $_SESSION["SES_NOTE_MANAGER"]->adminOptions();
      return;      
    }

    if($group_view)
      $listTags["USERNAME_LABEL"] = $_SESSION["translate"]->it("Groups");
    else
      $listTags["USERNAME_LABEL"] = $_SESSION["translate"]->it("Users");

    $alphabet = PHPWS_User::alphabet();

    if($group_view) {
      $getVars = array("NOTE_op"=>"selectGroup", "manageLetter"=>"all");
    } else {
      $getVars = array("NOTE_op"=>"selectUser", "manageLetter"=>"all");
    }

    $listTags["ALPHABET"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("ALL"), "notes", $getVars ) . "&nbsp;\n";

    foreach ($alphabet as $alphachar) {      
      if($group_view) {
	$getVars = array("NOTE_op"=>"selectGroup", "manageLetter"=>$alphachar);
      } else {
	$getVars = array("NOTE_op"=>"selectUser", "manageLetter"=>$alphachar);
      }

      $listTags["ALPHABET"] .= PHPWS_Text::moduleLink($alphachar, "notes", $getVars) . "&nbsp;\n";
    }

    $this->_list = NULL;
    if(!isset($this->_list)) {
      $this->_list = new PHPWS_List;
    }

    if($group_view) {
      $_SESSION["SES_NOTE"]->_toGroup = NULL;
      $this->_sendOption = "groups";
      $listTags["TITLE"] = $_SESSION["translate"]->it("Select from the list below the group you would like to send a note to.");

      $this->_list->setIdColumn("group_id");      
      $this->_list->setTable("mod_user_groups");
      $this->_list->setDbColumns(array("group_name"));
      $this->_list->setListColumns(array("Group_Name"));
      $this->_list->setName("list_groups");
      $this->_list->setTemplate("list_groups");
      $this->_list->setClass("PHPWS_Note_List_Groups");
      $this->_list->setOp("NOTE_op=selectGroup");

    } else {
      $listTags["TITLE"] = $_SESSION["translate"]->it("Select from the list below the user you would like to send a note to.");

      $this->_list->setIdColumn("user_id");
      $this->_list->setTable("mod_users");
      $this->_list->setDbColumns(array("username"));
      $this->_list->setListColumns(array("Username"));
      $this->_list->setOrder("last_on DESC");
      $this->_list->setName("list_users");
      $this->_list->setTemplate("list_users");
      $this->_list->setClass("PHPWS_Note_List_User");
      $this->_list->setOp("NOTE_op=selectUser");
    }

    $this->_list->setModule("notes");

    $this->_list->setPaging(array());
    $this->_list->setPaging(array(
	  "limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), 
	  "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));

    $this->_list->setExtraRowTags(array("COLSPAN"=>"3"));

    if(isset($_REQUEST["manageLetter"]) && $_REQUEST["manageLetter"] != "all"){
      if($group_view) 
	$this->_list->setWhere("group_name regexp '^[" . $_REQUEST["manageLetter"] . strtolower($_REQUEST["manageLetter"]) . "]'");
      else
	$this->_list->setWhere("username regexp '^[" . $_REQUEST["manageLetter"] . strtolower($_REQUEST["manageLetter"]) . "]'");
    } 

    $this->_list->setExtraListTags($listTags);

    $content = $this->_list->getList();
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }

  /**
   * Displays the user block that contains new note information
   *
   * @access public
   */
  function showBlock() {
    /* Grab the current user's notes */
    $userResult = $GLOBALS["core"]->sqlSelect("mod_notes", "toUser", $_SESSION["OBJ_user"]->username);

    /* If the userResult exists, count number of new notes */
    if($userResult) {
      $userNotes = 0;

      foreach($userResult as $resultRow)
	if($resultRow["dateRead"] == "0000-00-00 00:00:00")
	  $userNotes++;

      if($userNotes > 0) {
	$tags["YOU_HAVE"]   = $_SESSION["translate"]->it("You have ");
        $tags["USER_NOTES"] = $userNotes;

	if($userNotes > 1)
	    $tags["NEW_NOTE"]   = $_SESSION["translate"]->it(" new notes.");
	else
	    $tags["NEW_NOTE"]   = $_SESSION["translate"]->it(" new note.");                                                               

        $tags["CLICK_LINK"] = "<a href=\"index.php?module=notes&amp;NOTE_op=my_notes\">" .
           "View Notes</a>";
	

	/* Display block */
	$title = $_SESSION["translate"]->it("My Notes");
	$content = PHPWS_Template::processTemplate($tags, "notes", "block.tpl");
	$GLOBALS["CNT_notes_block"]["title"] = $title;
	$GLOBALS["CNT_notes_block"]["content"] = $content;
      }
    }
  }// END FUNC showBlock()

}// END CLASS PHPWS_NoteManager

class PHPWS_Note_List_User {
  var $username = NULL;

  function PHPWS_Note_List_User($NLU_id = NULL) {
    foreach($NLU_id as $key => $value) {
      $this->$key = $value;
    }     
  }

  function getListUsername() {
    return "<a href=\"index.php?module=notes&amp;NOTE_op=selectUser&amp;NOTE_touser=".$this->username."\">".$this->username."</a>";
  }

}

class PHPWS_Note_List_Groups {
  var $group_name = NULL;

  function PHPWS_Note_List_Groups($NLU_id = NULL) {
    foreach($NLU_id as $key => $value) {
      $this->$key = $value;
    }     
  }

  function getListGroup_Name() {
    return "<a href=\"index.php?module=notes&amp;NOTE_op=selectGroup&amp;NOTE_toGroup=".$this->group_name."\">".$this->group_name."</a>";
  }

}

?>