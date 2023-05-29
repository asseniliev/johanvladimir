<?php

require_once (PHPWS_SOURCE_DIR . "core/Form.php");

require_once (PHPWS_SOURCE_DIR . "core/Text.php");

/**
 * This class holds all information for a single instance of a Note.
 *
 * @version $Id: Note.php,v 1.24 2005/01/04 17:15:07 darren Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Notes
 */
class PHPWS_Note {

  /**
   * The database id of this note.
   *
   * @var    integer
   * @access private
   */
  var $_id = NULL;

  /**
   * The username of the user to deliver this Note to.
   *
   * @var    string
   * @access private
   */
  var $_toUser = NULL;

  /**
   * The group name of the group to deliver this Note to.
   *
   * @var    string
   * @access private
   */
  var $_toGroup = NULL;

  /**
   * The username of the user who is sending this Note.
   *
   * @var    string
   * @access private
   */
  var $_fromUser = NULL;

  /**
   * The actual message body of the note.
   *
   * @var    string
   * @access private
   */
  var $_message = NULL;

  /**
   * The date this note was actually sent.
   *
   * @var    string
   * @access private
   */
  var $_dateSent = NULL;

  /**
   * The date this note was read by recipient. For a single user note,
   * this is the actual date read.  For a multiuser note, this is the
   * date this note was last read.
   *
   * @var    string
   * @access private
   */
  var $_dateRead = NULL;

  /**
   * The username of the user to last read this note. If this is a single
   * user note it will be the username of the user to recieve the note. In
   * the case of a multiuser note, this will be the username of the last
   * user to read this note.
   *
   * @var    string
   * @access private
   */
  var $_userRead = NULL;

  /**
   * The subject of the note.
   *
   * @var    string
   * @access private
   */
  var $_subject = NULL;

  var $_list = NULL;

  /**
   * Constructor for the PHPWS_Note class.
   *
   * @param  integer $NOTE_id The database id of the note to be constructed.
   *                          If NULL, a new note is constructed.
   * @access public
   */
  function PHPWS_Note($NOTE_id = NULL) {
    /* If this is a new note, simply set the from User */
    if($NOTE_id === NULL) {
      $this->_fromUser = $_SESSION["OBJ_user"]->username;
      if(isset($_REQUEST["NOTE_toUser"]))
        $this->_toUser = $_REQUEST["NOTE_toUser"];
    } else {
      $result = $GLOBALS["core"]->sqlSelect("mod_notes", "id", $NOTE_id);
      if($result) {
	if($_SESSION["OBJ_user"]->username == $result[0]["toUser"] || $_SESSION["OBJ_user"]->username == $result[0]["fromUser"]) {
	  $this->_id = $NOTE_id;
	  $this->_toUser = $result[0]["toUser"];
	  $this->_toGroup = $result[0]["toGroup"];
	  $this->_fromUser = $result[0]["fromUser"];
	  $this->_message = $result[0]["message"];
	  $this->_dateSent = $result[0]["dateSent"];
	  $this->_dateRead = $result[0]["dateRead"];
	  $this->_userRead = $result[0]["userRead"];
	  $this->_subject  = $result[0]["subject"];
	}
      }
    }
  }// END FUNC PHPWS_Note()

  /**
   * Displays this note for reading
   *
   * @access public
   * @see    _mark()
   */
  function read($showMenu=TRUE) {
    /* Check to make sure this user has access to read this note */
    if($this->_toUser == $_SESSION["OBJ_user"]->username || $this->_fromUser == $_SESSION["OBJ_user"]->username) {
      /* Mark this note as read on the current date/time if the current user is who the note is addressed to */
      if($this->_toUser == $_SESSION["OBJ_user"]->username && (isset($_REQUEST["NOTE_op"]) && $_REQUEST["NOTE_op"] != "delete") && $showMenu) {
        $this->_mark();

        $tags["REPLY"] = "<a href=\"index.php?module=notes&amp;NOTE_id=". $this->_id . 
                         "&amp;NOTE_op=reply&amp;NOTE_RPY_User=" . $this->_fromUser . "\">";
        $tags["REPLY"] .= "Reply</a>";
        $tags["DELETE"] = "<a href=\"index.php?module=notes&amp;NOTE_op=delete&amp;NOTE_id=" . $this->_id . "\">";
        $tags["DELETE"] .= $_SESSION["translate"]->it("Delete") . "</a>";
      }

      /* Check whether this is a group note or a single user note */
      if($this->_toGroup) {
	$tags["TO_GROUP_LABEL"] = "<b>".$_SESSION["translate"]->it("Group")."</b>";
	$tags["TO_GROUP"] = $this->_toGroup;
      } elseif ($this->_toUser) {
	$tags["TO_USER_LABEL"] = "<b>".$_SESSION["translate"]->it("To")."</b>";
	$tags["TO_USER"] = $this->_toUser;
      } else {
	/* Message did not contain a recipient, so print error and do not display note */
	$this->_error("bad_message");
      }

      $tags["FROM_USER_LABEL"] = "<b>".$_SESSION["translate"]->it("From")."</b>";
      $tags["FROM_USER"] = $this->_fromUser;
      $tags["DATE_SENT_LABEL"] = "<b>".$_SESSION["translate"]->it("Sent")."</b>";
      $tags["DATE_SENT"] = $this->_dateSent;
      $tags["MESSAGE_LABEL"] = "<b>".$_SESSION["translate"]->it("Message:")."</b>";
      $tags["MESSAGE_BODY"] = PHPWS_Text::parseOutput($this->_message);
      $tags["SUBJECT_LABEL"] = "<b>".$_SESSION["translate"]->it("Subject")."</b>";
      $tags["SUBJECT"] = $this->_subject;

      $content = PHPWS_Template::processTemplate($tags, "notes", "read.tpl");
    } else {
      /* The current user does not have access to read this note */
      $this->_error("unauthorized");
      return;
    }

    /* Display full note */
    $title = "<h3>" . $_SESSION["translate"]->it("Note from") . " " . $this->_fromUser . "</h3>";
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC read()

  /**
   * Displays this note in a format to be edited.
   *
   * @access public
   * @see    _getUsers(), _getGroups()
   */
  function edit() {
    /* Get list of users and groups */
    $users = $this->_getUsers();
    $groups = $this->_getGroups();

    /* Prepare tags array for template */
    $tags = array();
    $tags["TITLE"] = $_SESSION["translate"]->it("Send Note");
    $tags["TO_LABEL"] = $_SESSION["translate"]->it("To");
    

    $tags["TO_USER"] = PHPWS_Form::formTextField("toUser", $this->_toUser, 15);
    $tags["NEW_USER"] = "<a href=\"index.php?module=notes&amp;NOTE_op=selectUser\">".$_SESSION["translate"]->it("Show List of Users")."</a>";
    //    $tags["TO_USER"] = PHPWS_Form::formSelect("toUser", $users, $this->_toUser, TRUE);

    $tags["MESSAGE_FIELD"] = PHPWS_Form::formTextArea("message", $this->_message, 7, 50);
    $tags["SUBJECT_FIELD"] = PHPWS_Form::formTextField("subject", $this->_subject, 50);
    $tags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Send Note"), "NOTE_op[send_note]");

    $tags["MESSAGE_LBL"] = $_SESSION["translate"]->it("Message");
    $tags["SUBJECT_LBL"] = $_SESSION["translate"]->it("Subject");

    /* Create edit form */
    $elements[0] = PHPWS_Form::formHidden("module", "notes");
    $elements[0] .= PHPWS_Template::processTemplate($tags, "notes", "edit.tpl");
    $content = PHPWS_Form::makeForm("edit_note", "index.php", $elements);

    /* Display edit form */
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC edit()

  /**
   * Sends this note to it's appropriate user/group.  This is done through some
   * database interaction.
   *
   * @access public
   */
  function send() {
    /* Check message text and save it first hand */
    if($_POST["message"]) {
      $this->_message = PHPWS_Text::parseInput($_POST["message"]);
      $queryData["message"] = $this->_message;
    } else {
      $this->_error("no_body");
      $this->edit();
      return;
    }

    /* Check subject text and save it first hand */
    if($_POST["subject"]) {
      $this->_subject = PHPWS_Text::parseInput($_POST["subject"]);
      $queryData["subject"] = $this->_subject;
    } else {
      $this->_error("no_subject");
      $this->edit();
      return;
    }

    /* Check recipients.  Group overrides User */
    if(isset($_POST["toGroup"])) {
      $this->_toGroup = PHPWS_Text::parseInput($_POST["toGroup"]);
      $queryData["toGroup"] = $this->_toGroup;
    } elseif($_POST["toUser"]) {
      $this->_toUser = PHPWS_Text::parseInput($_POST["toUser"]);
      $queryData["toUser"] = $this->_toUser;
    } else {    $GLOBALS["CNT_notes"]["content"] .= $content;
      $this->_error("no_recipient");
      $this->edit();
      return;
    }

    /* Set fromUser and dateSent as current date/time */
    $queryData["fromUser"] = $this->_fromUser;
    $this->_dateSent = date("Y-m-d H:i:s");
    $queryData["dateSent"] = $this->_dateSent;

    $match_column["message"]   = $this->_message;
    $match_column["subject"]   = $this->_subject;
    $match_column["toUser"]    = $this->_toUser;
   
    $sql = "SELECT * FROM mod_users WHERE username LIKE BINARY '". $this->_toUser . "'";
    $result = $GLOBALS["core"]->query($sql, TRUE);
    if(!$row = $result->fetchRow()) {
      $this->_error("nouser");
      $this->edit();
      return;
    }

    $match_column["fromUser"]  = $this->_fromUser;

    if(!$GLOBALS["core"]->sqlSelect("mod_notes", $match_column)) {
      /* Save note in database */
      if(!$GLOBALS["core"]->sqlInsert($queryData, "mod_notes", "id")) {
	$this->_error("database");
	$this->edit();
	return;
      }

      /* Display menu and sent confirmation */
      $content = $_SESSION["translate"]->it("Your note was successfully sent!");
    } else {
      $content = $_SESSION["translate"]->it("You have already sent this note.");
    }

    $GLOBALS["CNT_notes"]["content"] .= "<br />" . $content;
  }// END FUNC send()

  /**
   * 2 functions:  Displays a delete confirmation to the user on first visit to this function.
   * Then, depending on the user's input, delete() will either keep the note (user_answer = "No")
   * or delete it from the database (user_answer = "Yes")
   *
   * @access public
   */
  function delete() {
    /* Make sure the current user is the owner of this note */
    if($this->_toUser == $_SESSION["OBJ_user"]->username) {
      if(isset($_POST["yes"])) {
	/* User submitted "yes" so delete the note and print the appropriate message */
	$GLOBALS["core"]->sqlDelete("mod_notes", "id", $_POST["NOTE_id"]);
	$content = $_SESSION["translate"]->it("Your note was successfully deleted.");
	$GLOBALS["CNT_notes"]["content"] .= $content;
	$_SESSION["SES_NOTE_MANAGER"]->myNotes();
      } elseif(isset($_POST["no"])) {
	/* User submitted "no" so keep the note and print appropriate message */
	$content = $_SESSION["translate"]->it("Your note was <b>not</b> deleted.") . "";
	$GLOBALS["CNT_notes"]["content"] .= $content;
	$_SESSION["SES_NOTE_MANAGER"]->myNotes();
      } else {
	/* First time through this function ask for a confirmation from the user */
	$elements[0] = PHPWS_Form::formHidden("module", "notes");
	$elements[0] .= PHPWS_Form::formHidden("NOTE_op", "delete");
	$elements[0] .= PHPWS_Form::formHidden("NOTE_id", $_GET["NOTE_id"]);
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Yes"), "yes");
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("No"), "no");

	$content = "<br />&nbsp;<span style=\"color:red\"><b>" . $_SESSION["translate"]->it("Are you sure you wish to delete this note from") . " " . $this->_fromUser . "?</b></span><br /><br />";
	$content .= PHPWS_Form::makeForm("delete_note", "index.php", $elements);
	$GLOBALS["CNT_notes"]["content"] .= $content;
	$this->read();
      }
    } else {
      /* Current user is not owner of this note, so print error and do not delete */
      $this->_error("unauthorized");
      return;
    }

  }// END FUNC delete()

  function deleteSent() {
    /* Make sure the current user is the owner of this note */
    if($this->_fromUser == $_SESSION["OBJ_user"]->username) {
      if(isset($_POST["yes"])) {
	$result = $GLOBALS["core"]->sqlSelect("mod_notes", "id", $_REQUEST["NOTE_id"]);
	
	if($result[0]["toUserHide"] == 1) {
	  /* User submitted "yes" so delete the note and print the appropriate message */
	  $GLOBALS["core"]->sqlDelete("mod_notes", "id", $_REQUEST["NOTE_id"]);
	  $content = $_SESSION["translate"]->it("Sent note was successfully deleted.");
	} else {
	  $data["fromUserHide"] = 1;
	  $GLOBALS["core"]->sqlUpdate($data, "mod_notes", "id", $_REQUEST["NOTE_id"]);
	  $content = $_SESSION["translate"]->it("Sent note was successfully deleted.");
	}
	
	$GLOBALS["CNT_notes"]["content"] .= $content;
	$_SESSION["SES_NOTE_MANAGER"]->sentNotes();

      } elseif(isset($_POST["no"])) {
	/* User submitted "no" so keep the note and print appropriate message */
	$content = $_SESSION["translate"]->it("Sent note was <b>not</b> deleted.");
	$GLOBALS["CNT_notes"]["content"] .= $content;
	$_SESSION["SES_NOTE_MANAGER"]->sentNotes();
      } else {
	/* First time through this function ask for a confirmation from the user */
	$elements[0] = PHPWS_Form::formHidden("module", "notes");
	$elements[0] .= PHPWS_Form::formHidden("NOTE_op", "deleteSent");
	$elements[0] .= PHPWS_Form::formHidden("NOTE_id", $_GET["NOTE_id"]);
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Yes"), "yes");
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("No"), "no");

	$content = "<br />&nbsp;<span style=\"color:red\"><b>" . $_SESSION["translate"]->it("Are you sure you wish to delete this note to ") . " " . $this->_toUser . "?</b></span><br /><br />";
	$content .= PHPWS_Form::makeForm("delete_note", "index.php", $elements);
	$GLOBALS["CNT_notes"]["content"] .= $content;
	$this->read(FALSE);
      }

    } else {
      /* Current user did not sent note */
      $this->_error("unauthorized");
      return;
    }
  }

  /**
   * Returns an indexed array of all the current users in the database
   *
   * @return array $users An array of all users
   * @access private
   * @see    edit()
   */
  function _getUsers() {
    /* Grab all users from database */
    $result = $GLOBALS["core"]->sqlSelect("mod_users", NULL, NULL, "username");

    /* Add blank user */
    $users[] = " ";

    /* Create users array */
    if($result)
    foreach($result as $resultRow)
      $users[] = $resultRow["username"];
    natcasesort($users);
    return $users;
  }// END FUNC _getUsers()

  /**
   * Returns an indexed array of all the current groups in the database
   *
   * @return array $users An array of all groups
   * @access private
   * @see    edit()
   */
  function _getGroups() {
    /* Grab all groups from database */
    $result = $GLOBALS["core"]->sqlSelect("mod_user_groups", NULL, NULL, "group_name");

    /* Add blank group */
    $groups[] = " ";

    /* Create groups array */
    if($result)
    foreach($result as $resultRow)
      $groups[] = $resultRow["group_name"];

    return $groups;
  }// END FUNC _getGroups()

  /**
   * Marks this note as read by the current user on the current date/time
   *
   * @access private
   * @see    read()
   */
  function _mark() {
    $this->_userRead = $_SESSION["OBJ_user"]->username;
    $queryData["userRead"] = $this->_userRead;

    $this->_dateRead = date("Y-m-d H:i:s");
    $queryData["dateRead"] = $this->_dateRead;

    $GLOBALS["core"]->sqlUpdate($queryData, "mod_notes", "id", $this->_id);
  }// END FUNC _mark()

  /**
   * Displays an error depending on the $type variable sent in
   *
   * @param  string $type The type of error to display
   * @access private
   */
  function _error($type) {
    $content = "<div class=\"errortext\"><h3>" . $_SESSION["translate"]->it("ERROR!") . "</h3></div>";

    switch($type) {
      case "no_recipient":
      $content .= $_SESSION["translate"]->it("You must designate a recipient for this note.");
      break;

      case "no_subject":
      $content .= $_SESSION["translate"]->it("You must provide a subject to your note.");
      break;

      case "nouser":
      $content .= $_SESSION["translate"]->it("Invalid username.");
      break;

      case "no_body":
      $content .= $_SESSION["translate"]->it("You must provide a body to your note.");
      break;

      case "database":
      $content .= $_SESSION["translate"]->it("There was a database error when attempting to send your note.");
      break;

      case "bad_message":
      $content .= $_SESSION["translate"]->it("There was an error in the note you are attempting to read.  It will not be
      displayed for security reasons.  Contact your systems administrator for help.");
      break;

      case "unauthorized":
      $content .= $_SESSION["translate"]->it("You are not allowed to access the note you specified.");
      break;
    }

    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC _error()

}// END CLASS PHPWS_AtomNote

?>