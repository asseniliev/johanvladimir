<?php

require_once(PHPWS_SOURCE_DIR . 'core/Item.php');

require_once(PHPWS_SOURCE_DIR . "core/Form.php");
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
require_once(PHPWS_SOURCE_DIR . "core/Error.php");

require_once(PHPWS_SOURCE_DIR . "mod/approval/class/Approval.php");

require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

if($GLOBALS["core"]->moduleExists("comments")) {
  require_once(PHPWS_SOURCE_DIR . "mod/comments/class/CommentManager.php");
}

/**
 * This class holds all the information for a single instance of a FAQ.
 *
 * @version $Id: Faq.php,v 1.31 2005/05/13 15:40:19 darren Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 * @package Faq
 */
class PHPWS_Faq extends PHPWS_Item {
  /*
   * The answer to this FAQ.
   *
   * @var text
   * @example $this->_answer = "Answer to this FAQ";
   * @access private
   */
  var $_answer = NULL;
  
  /*
   * Contact information of user that suggested FAQ (if applicable).
   *
   * @var text
   * @example $this->_contact = "NAME:EMAIL";
   * @access private
   */
  var $_contact = NULL;

  /* 
   * The number of times this FAQ has been viewed.
   * 
   * @var int
   * @example $this->_hits = "20";
   * @access private
   */
  var $_hits = 0;
     
  /* 
   * The number of times this FAQ has been rated.
   * 
   * @var int
   * @example $this->_numScores = "7";
   * @access private
   */
  var $_numScores = 1;
     
  /* 
   * The average of the ratings for this FAQ.
   * 
   * @var double
   * @example $this->_avgScore = "2.99";
   * @access private
   */
  var $_avgScore = 3.0;
     
  /* 
   * The total of the ratings for this FAQ.
   *
   * @var int
   * @example $this->_totalScores = "15";
   * @access private
   */
  var $_totalScores = 3.0;

  /*
   * The current composite score for this FAQ.
   *
   * @var int
   * @example $this->_compScore = "4.1";
   * @access private
   */
  var $_compScore = 3.0;

  /**
   * Constructors new or existing FAQ
   *
   * @param int $FAQ_id Used to initialize an existing FAQ
   * @access public
   */
  function PHPWS_Faq($FAQ_id = NULL) {
    $this->setTable("mod_faq_questions");

    if(is_array($FAQ_id)) {

      foreach($FAQ_id as $key => $value) {
        $key = "_{$key}";
        $this->$key = $value;
      }

    } else if($FAQ_id != NULL) {

      $this->setApproved("false");
      $this->setId($FAQ_id);
      $this->init();
    }
  }
  
  /**
   * allows the submission or suggestion of a new FAQ
   *
   * @access public
   */
  function edit() {
    $elements[0] = NULL;
      $tags["QUESTION_LABEL"] = "<b>".$_SESSION["translate"]->it("Question: ")."</b>";
      $tags["QUESTION_FIELD"] = PHPWS_Form::formTextArea("questionField", $this->_label, 2, 50);

      if(!$_SESSION["OBJ_user"]->admin_switch) {
        $tags["ANSWER_LABEL"] = $_SESSION["translate"]->it("Answer: ");
      } else {
        $tags["ANSWER_LABEL"] = "<b>".$_SESSION["translate"]->it("Answer: ")."</b>";
      }
      $tags["ANSWER_FIELD"] = PHPWS_Form::formTextArea("answerField", $this->_answer, 4, 50);

      /* show fatcat categories if installed */
      if(isset($_SESSION["OBJ_fatcat"])) {
        $tags["CATEGORY_LABEL"] = "<b>".$_SESSION["translate"]->it("Category: ")."</b>";
        $tags["CATEGORY_FIELD"] = $_SESSION["OBJ_fatcat"]->showSelect($this->_id, "multiple", 3, "faq");
      }

      /* show proper submit button according to user */
      if(!$_SESSION["OBJ_user"]->admin_switch || isset($_REQUEST["FAQ_user"])) {
	if(!$this->_contact == NULL) {
  	  list($name, $email) = explode("::", $this->_contact);
	} else {
	  $name = $email = "";
	}
	$tags["USER_LABEL"] = $_SESSION["translate"]->it("Use the form below to suggest a FAQ for consideration.<br />");
	$tags["USER_LABEL"] .= "<i>".$_SESSION["translate"]->it("The answer, name and email fields may be left blank.")."</i>";
	$tags["NAME_LABEL"] = $_SESSION["translate"]->it("Your Name: ");
        $tags["NAME_FIELD"] = PHPWS_Form::formTextField("nameField", $name, 30);
	$tags["EMAIL_LABEL"] = $_SESSION["translate"]->it("Your Email: ");
        $tags["EMAIL_FIELD"] = PHPWS_Form::formTextField("emailField", $email, 30);
	$tags["EMAIL_FIELD"] .= CLS_help::show_link("faq", "user_email");
        $elements[0] .= PHPWS_Form::formHidden("FAQ_user", "normal");
        $elements[0] .= PHPWS_Form::formHidden("notapproved", "false");
	$elements[0] .= PHPWS_Form::formHidden("FAQ_op", "submitSuggestedFAQ");
        $title = $_SESSION["translate"]->it("Suggest a FAQ");
        $tags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Submit Suggested FAQ"));  
      } else {
        if($this->_id === NULL) {
          $title = $_SESSION["translate"]->it("Submit New FAQ");
          $elements[0] .= PHPWS_Form::formHidden("FAQ_op", "submitNewFAQ");
          $tags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Submit FAQ"));  
	} else {
          $title = $_SESSION["translate"]->it("Update FAQ");
          $elements[0] .= PHPWS_Form::formHidden("FAQ_op", "updateFAQ");
          $tags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Update"));  
	}
      }

      $tags["TITLE"] = $title;
      $elements[0] .= PHPWS_Form::formHidden("module", "faq");
      $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "add.tpl");
      $content = PHPWS_Form::makeForm("edit_faq", "index.php", $elements);
  
      
      $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC edit

  /**
   * submits this FAQ to the database
   *
   * @access public
   */
  function submitFAQ() {
    /* tracks if updating or creating a new FAQ */
    $newFaq = FALSE;
    if($this->_id === NULL) $newFaq = TRUE;

    $errorObj = NULL;
    $commitResult = "false";

    //CHECK QUESTION AND ANSWER FIELDS

    /* make sure there is a question for this FAQ */
    if(isset($_REQUEST["questionField"]) && $_REQUEST["questionField"] != "") {
      $this->_label = PHPWS_Text::parseInput($_REQUEST["questionField"]);
    } else {
      $error = $_SESSION["translate"]->it("Please enter a question.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::submitFaq()", $error);
      $errorObj->errorMessage("CNT_faq_body"); 
    }     
      
    /* check for answer field if user is suggesting a FAQ then no answer is allowed */
    if((isset($_REQUEST["answerField"]) && $_REQUEST["answerField"] != "") || isset($_REQUEST["notapproved"])) {
      $this->_answer = PHPWS_Text::parseInput($_REQUEST["answerField"]);
    } else if($errorObj == NULL) {
      $error    = $_SESSION["translate"]->it("Please enter an answer to your question.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::submitFaq()", $error);
      $errorObj->errorMessage("CNT_faq_body"); 
    }

    //IF APPLICABLE CHECK CONTACT INFORMATION FIELDS

    /* check if administrative */
    if(isset($_REQUEST["notapproved"])) {
      $this->_approved = 0;

      /* see if user specified a contact email address show fields */
      if(isset($_REQUEST["emailField"]) && $_REQUEST["emailField"] != "") {
        if(isset($_REQUEST["nameField"]) && $_REQUEST["nameField"] != "") {
	  $this->_contact = $_REQUEST["nameField"];
	} else {
	    $this->_contact = NULL;
	}  
        $this->_contact .= "::";

	/* check syntax of email address */
        if(PHPWS_Text::isValidInput($_REQUEST["emailField"], "email")) {
	  $this->_contact .= $_REQUEST["emailField"];
	} else {
	  $error = $_SESSION["translate"]->it("Please enter a valid email address.");
	  $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::submitFaq()", $error);
	  $errorObj->errorMessage("CNT_faq_body");
	  $this->_contact = "";
	}
      }
    }

    //COMMIT AND SAVE TO FATCAT
    /* make sure no error */
    if($errorObj == NULL) {
      /* save FAQ to fatcat module */
      if($this->_approved == 1 && $_SESSION["OBJ_fatcat"] && !is_null($_SESSION["OBJ_fatcat"]->getCategoryList())) {
	/* make sure a category was choose */
        if(isset($_REQUEST["fatSelect"])) {
          $errorObj = $this->commit();
          $link = "index.php?module=faq&amp;FAQ_op=view&amp;FAQ_id=".$this->_id;
          $_SESSION["OBJ_fatcat"]->saveSelect($this->_label, $link , $this->_id, NULL, "faq");
	} else {
	  $error = $_SESSION["translate"]->it("Please choose a category for this FAQ.");
	  $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::submitFaq()", $error);
          $errorObj->errorMessage("CNT_faq_body"); 
	}
      } else {
	  /* fatcat not installed or no categories go ahead and commit */
          $errorObj = $this->commit();
      }
    }

    //SHOW SUCCESS FAILURE MESSAGE

    /* display message to user about success or failure of submission */
    if(!(PHPWS_Error::isError($errorObj))) {

     /* if FAQ not approved add to approval module */
      if($this->_approved != 1) {
	//deactivate from fatcat so want show up in whats related
        if(isset($_REQUEST["fatSelect"])) {
           $_SESSION["OBJ_fatcat"]->deactivate($this->_id);
        }
        PHPWS_Approval::add($this->_id, $this->_label, "faq");
      }

      if($_SESSION["OBJ_user"]->isDeity()) {
	if($this->_approved == 0) 
	  $this->_approved = 0;
	else
	  $this->_approved = 1;	  

	/* determine if new FAQ or a FAQ that has been updated */
        if(!$newFaq) {
    	  $title = $_SESSION["translate"]->it("Updated FAQ");
          $content = $_SESSION["translate"]->it("Successfully updated FAQ.");
	} else {
   	  $title = $_SESSION["translate"]->it("FAQ Submited");
          $content = $_SESSION["translate"]->it("FAQ has successfully been added to the database.");
	}
      } else {
	/* check if FAQ needs to be approved by an administrator */
        if($this->_approved == 1) {
    	  $title = $_SESSION["translate"]->it("FAQ Submited");
          $content = $_SESSION["translate"]->it("Your FAQ has been added to the database.");
	}
	else {
    	  $title = $_SESSION["translate"]->it("FAQ Suggestion Submitted");
          $content = $_SESSION["translate"]->it("Thank You. &nbsp;Your FAQ has been submitted for consideration.");
	}
      }

      $content .= "<br /><hr /><b>".phpws_text::parseOutput($this->_label)."</b><br /><br />";
      $content .= phpws_text::parseOutput($this->_answer) . "<br /><hr />";

      $content .= "<br /><br />";
      $content .= "<span class='smalltext'><a href='index.php?module=faq&amp;FAQ_op=view&amp;FAQ_id=". $this->_id ."'>";
      $content .= $_SESSION["translate"]->it("View New Faq") . "</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      $content .= "<a href='index.php?module=faq&amp;FAQ_op=viewFAQs'>";
      $content .= $_SESSION["translate"]->it("List All Faqs") . "</a></span>";
      
      $GLOBALS["CNT_faq_body"]["content"] .= $content;
    } else if($errorObj == NULL) {
      $error = $_SESSION["translate"]->it("There was a problem saving your FAQ to the database.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::submitFaq()", $error);
      $errorObj->errorMessage("CNT_faq_body"); 
    }

    /* error so return to form */
    if(PHPWS_Error::isError($errorObj)) {
      /* save users input */
      $this->_label = $_REQUEST["questionField"];
      $this->_answer = $_REQUEST["answerField"];

      if(isset($_REQUEST["nameField"]))
	 $this->_contact = $_REQUEST["nameField"];

      $this->_contact .= "::";

      if(isset($_REQUEST["emailField"]))
	 $this->_contact .= $_REQUEST["emailField"];
 
      $this->edit();
    }
  } //END FUNC submitFAQ


  /**
   * deletes this FAQ
   *
   * @access public
   */
  function delete() {
    $errorObj = null;

    /* check to make sure user is an administrator */
    if($_SESSION["OBJ_user"]->allow_access("faq","delete_faqs")) {
      /* check to see if ready to delete FAQ */
      if(isset($_POST["YES"])) {
	//SAFE TO DELETE FAQ

        if($_SESSION["OBJ_fatcat"]) {
          $_SESSION["OBJ_fatcat"]->purge($this->_id, "faq");
	}

        $errorObj = $this->kill();

        /* determine if error occured */
        if(!PHPWS_Error::isError($errorObj)) {
         $title = $_SESSION["translate"]->it("FAQ Removed");
         $content = $_SESSION["translate"]->it("FAQ was successfully deleted.");
	 $GLOBALS["CNT_faq_body"]["content"] .= $content;
         $_SESSION["SES_FAQ_STATS"]->init();  //reset stats
	 $_SESSION["SES_FAQ_MANAGER"]->_list();
	}
      }
      else if(isset($_POST["NO"])) {

 	//USER ABORTED
        $title = $_SESSION["translate"]->it("Action Canceled");
        $content = $_SESSION["translate"]->it("FAQ was not deleted.");

        $GLOBALS["CNT_faq_body"]["content"] .= $content;

      }
      else {
	//SHOW CONFIRMATION ABOUT DELETION - FIRST TIME THROUGH

        $title = $_SESSION["translate"]->it("Confirm Deletion");
        $content = $_SESSION["translate"]->it("Are you sure you want to delete this FAQ?");
        $elements[0] = PHPWS_Form::formSubmit("Yes", "YES");
        $elements[0] .= PHPWS_Form::formSubmit("No", "NO");
	$action = "index.php?module=faq&amp;FAQ_adv=delete&amp;FAQ_id=".$this->_id;
        $content .= PHPWS_Form::makeForm("delete_FAQ", $action, $elements);

        $GLOBALS["CNT_faq_body"]["content"] .= $content;        
      }

    }
    else {

      /* user is not an administrator */
      $error = $_SESSION["translate"]->it("You do not have access to delete a FAQ.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::delete()", $error);
    }

    /* check if error occured */
    if(PHPWS_Error::isError($errorObj)) {
      $errorObj->errorMessage("CNT_faq_body");
    }

  } //END FUNC delete

  /**
   * displays current FAQ
   *
   * @param int $_allowComments Flag to indicate whether to show comment module
   * @param int $_allowAnonSettings Flag to indicate whether users not logged in can score this FAQ
   * @param int $_numRatings Represents total number of score ratings
   * @param array $_legendContents Array containing the scoring legend
   * @access public
   */
  function view($_allowComments = 0, $_allowAnonScoring = 0, $linkBack = NULL, $approveView = NULL) {
    //IF ADMIN SHOW OPTIONS TO EDIT, DELETE, HIDE, APPROVE

    /* Check to see if have privilages to show administrative options. */
    if( $_SESSION["OBJ_user"]->admin_switch && $_SESSION["OBJ_user"]->allow_access("faq") && $approveView === NULL) {
      $titleFlag = FALSE;

      if($_SESSION["OBJ_user"]->allow_access("faq", "edit_faqs")) {
        $tags["EDIT_LABEL"]  =  "<a href=\"index.php?module=faq&amp;FAQ_adv=edit&amp;FAQ_id=";
        $tags["EDIT_LABEL"] .= $this->_id."\">".$_SESSION["translate"]->it("Edit")."</a>";
        $titleFlag = TRUE;
      }

      if($_SESSION["OBJ_user"]->allow_access("faq", "delete_faqs")) {
        $tags["DELETE_LABEL"]  = "<a href=\"index.php?module=faq&amp;FAQ_adv=delete&amp;FAQ_id=";
        $tags["DELETE_LABEL"] .= $this->_id."\">".$_SESSION["translate"]->it("Delete")."</a>";
        $titleFlag = TRUE;
      }

      if($_SESSION["OBJ_user"]->allow_access("faq", "hide_faqs")) {
        if($this->_hidden) {
          $tags["HIDE_LABEL"]  = "<a href=\"index.php?module=faq&amp;FAQ_adv=show&amp;FAQ_id=";
          $tags["HIDE_LABEL"] .= $this->_id."\">".$_SESSION["translate"]->it("Unhide")."</a>";
        } else {
          $tags["HIDE_LABEL"]  = "<a href=\"index.php?module=faq&amp;FAQ_adv=hide&amp;FAQ_id=";
          $tags["HIDE_LABEL"] .= $this->_id."\">".$_SESSION["translate"]->it("Hide")."</a>";
        }
        $titleFlag = TRUE;
      }

      if($_SESSION["OBJ_user"]->allow_access("faq", "approve_faqs")) {
        if($this->_approved) {
          $tags["UNAPPROVE_LABEL"]  = "<a href=\"index.php?module=faq&amp;FAQ_adv=unapprove&amp;FAQ_id=";
          $tags["UNAPPROVE_LABEL"] .= $this->_id."\">".$_SESSION["translate"]->it("Unapprove")."</a>";
        } else {
          $tags["UNAPPROVE_LABEL"]  = "<a href=\"index.php?module=faq&amp;FAQ_adv=approve&amp;FAQ_id=";
          $tags["UNAPPROVE_LABEL"] .= $this->_id."\">".$_SESSION["translate"]->it("Approve")."</a>";
        }
        $titleFlag = TRUE;
      }

      if($titleFlag) {
        $tags["ADMIN_MENU_CLASS_OPTION"] = "class=\"bg_light\"";
        $tags["ADMIN_TITLE"] = $_SESSION["translate"]->it("<b>Admin Options</b>");
      }
    }

    //SHOW QUESTION, ANSWER

    /* Build tags for display */
    $tags["QUESTION_LABEL"] = "<b><span style=\"color:red;\">".$_SESSION["translate"]->it("Q").":</span> &#160;</b>";
    $tags["QUESTION_CONTENTS"] = phpws_text::parseOutput($this->_label) . "<br /><br />";
    $tags["ANSWER_LABEL"] = $_SESSION["translate"]->it("A");
    $tags["ANSWER_CONTENTS"] = phpws_text::parseOutput($this->_answer) . "<br />";

    if($_SESSION["SES_FAQ_MANAGER"]->_currentLayout == PHPWS_FAQ_CAT_VIEW) {
      $tags["LINK_BACK"] = "<a href='./index.php?module=faq&amp;FAQ_op=viewFAQs&amp;category=" . $_SESSION["OBJ_fatcat"]->cat_id . "'>" .
	$_SESSION["translate"]->it("Back")."</a>";
    } else if($linkBack !== NULL) {
      $tags["LINK_BACK"] = $linkBack.$_SESSION["translate"]->it("Back")."</a>";
    }

    //IF SUGGESTING FAQ SHOW CONTACT INFORMATION

    if($this->_approved == 0 && $this->_contact != NULL) {
      list($name, $email) = explode("::", $this->_contact);

      if($email != NULL) {
       $tags["CONTACT_LABEL"] = $_SESSION["translate"]->it("Suggested by <b>");
       if($name != NULL) { 
	$tags["CONTACT_LABEL"] .= $name;
       } else {
	$tags["CONTACT_LABEL"] .= $_SESSION["translate"]->it("an anonymous user");
       }
       $tags["CONTACT_LABEL"] .= "&#160;&#160;</b>(";
       $tags["CONTACT_LABEL"] .= "<a href=\"index.php?module=faq&amp;FAQ_op=email_user&amp;";
       $tags["CONTACT_LABEL"] .= "FAQ_email=$email&amp;FAQ_name=$name\">Send Email</a></b>)";
      }
    }

    //SHOW OPTION TO SCORE THIS FAQ

    /* Check to see if have privilage and access to score this FAQ. */
    if(!($_SESSION["OBJ_user"]->isDeity() && $approveView === NULL) && $_allowAnonScoring && $approveView === NULL) {
        $tags["SCORE_FAQ_LABEL"] = "<b>".$_SESSION["translate"]->it("How helpful was this FAQ?")."</b>";
   
        $optArr = array();
        $counter = 4;
        foreach ($_SESSION["SES_FAQ_MANAGER"]->_scoringLegend as $description) {
	  $optArr[$counter] = $description;
          $counter = $counter - 1;
        }

        $tags["SCORE_FAQ_MENU"] = PHPWS_Form::formSelect("score_faq", $optArr, $optArr[2]);
        $tags["GO_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Score FAQ"), "submitRatedFAQ");
    }

    //IF ADMIN SHOW FORM TO CHANGE SCORE RATING

    /* ADMIN ONLY - Build tags for the ability to change the scoring of this FAQ - ADMIN ONLY */
    if($_SESSION["OBJ_user"]->isDeity() && $approveView === NULL) {
	$tags["ADMIN_OPTIONS_LABEL"] = $_SESSION['translate']->it("FAQ Options");
      $tags["ADMIN_SCORE_CLASS_OPTION"] = " class=\"bg_light\"";
      $tags["ADMIN_SCORING_TITLE"] = $_SESSION["translate"]->it("Adjust Score Rating for this FAQ")."<br />";
      $tags["HITS_LABEL"] = $_SESSION["translate"]->it("Number of Hits: ");
      $tags["HITS_FIELD"] = PHPWS_Form::formTextField("admin_hits", $this->_hits, 5);
      $tags["AVERAGE_LABEL"] = $_SESSION["translate"]->it("Average of Scores: ");
      $tags["AVERAGE_FIELD"] = PHPWS_Form::formTextField("admin_average", $this->_avgScore, 5);
      $tags["AVERAGE_FIELD"] .= " (0.0 to ".PHPWS_FAQ_NUMLEGENDITEMS.")";
      $tags["COMPOSITE_LABEL"] = $_SESSION["translate"]->it("Composite Score: ");
      $tags["COMPOSITE_ENTRY"] = $this->_compScore;
      $tags["RESET_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Reset"), "resetScore");
      $tags["NEW_COMPOSITE_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Recalculate"), "recalculateScore");
    }
    
    
    /* check to see if FAT_CAT module is installed */
     if($_SESSION["OBJ_fatcat"] && $approveView === NULL && $this->_approved == 1) {       
        $_SESSION["OBJ_fatcat"]->whatsRelated($this->_id);
     }

    $elements[0]  = PHPWS_Form::formHidden("module", "faq");
    $elements[0] .= PHPWS_Form::formHidden("FAQ_op", "");
    $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "view.tpl");
    $content = PHPWS_Form::makeForm("view_faq", "index.php", $elements);

    $title = "FAQ";

    if($approveView === NULL) {
      $GLOBALS["CNT_faq_body"]["content"] .= $content;
    } else {
      echo $content;
      return;
    }
 
    if($_allowComments && $GLOBALS["core"]->moduleExists("comments")) {
      if(!isset($_SESSION["PHPWS_CommentManager"]))
	$_SESSION["PHPWS_CommentManager"] = new PHPWS_CommentManager;

      $_SESSION["PHPWS_CommentManager"]->listCurrentComments("faq", $this->_id, TRUE);
    }

    /* Update rating and hits for this FAQ. */
    if(!isset($_SESSION["visitedFAQs"]) || (isset($_SESSION["visitedFAQs"]) && !stristr($_SESSION["visitedFAQs"], $this->_id))) {
      if(!isset($_SESSION["visitedFAQs"])) 
	$_SESSION["visitedFAQs"] = "";

      $this->_hits++;
      $this->newCompositeScore();
      $this->commit();
      $_SESSION["visitedFAQs"] .= $this->_id." ";
      $_SESSION["SES_FAQ_STATS"]->setQuickStats();
    }

    PHPWS_Layout::addPageTitle($_SESSION["translate"]->it("FAQ: ") .$this->_label);

  } //END FUNC view

  /**
   * adds a new score to this FAQ
   *
   * @param int $newScore The new score to be added
   * @access public
   */
  function addScore($newScore) {
    if(isset($_SESSION["hasScored"]) && !stristr($_SESSION["hasScored"], $this->_id)) {
      $this->_numScores++;
      $this->_totalScores += $newScore;
      $this->_avgScore = round((double)($this->_totalScores / $this->_numScores), 2);    
      $this->newCompositeScore();
      $result = $this->commit();

      if(PHPWS_Error::isError($result)) {
        $result->errorMessage("CNT_faq_body"); 
      }
      else {
	$_SESSION["hasScored"] .= $this->_id." ";
        $title = $_SESSION["translate"]->it("Scoring Information Updated");
        $content = $_SESSION["translate"]->it("Thank you for scoring this FAQ.");
        $GLOBALS["CNT_faq_body"]["content"] .= $content;
      }
    }
    else {
      $error = $_SESSION["translate"]->it("Please only score this FAQ once.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::submitFaq()", $error);
      $errorObj->errorMessage("CNT_faq_body");
    }
  } //END FUNC addScore

  /**
   * changes number of hits and average rating for this FAQ
   *
   * @param int $newNumHits The new value for the number of hits
   * @param double $newAverage The new value for the average
   * @access public
   */
  function changeHitsAverage($newNumHits = 0, $newAverage = 0) {
    /* check to make sure number of hits is greater than zero */
    if($newNumHits < 0) {
      $error = $_SESSION["translate"]->it("The number of hits cannot be negative.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::_changeHitsAverage()", $error);
      $errorObj->errorMessage("CNT_faq_body");
    }
    else {
      $this->_hits = $newNumHits;
    }

    /* check to make sure number of hits is between the max and lowest average score rating */
    if($newAverage > 5 || $newAverage < 0) {
      $error = $_SESSION["translate"]->it("Invaid range for average score.");
      $errorObj = new PHPWS_Error("faq", "PHPWS_Faq::_changeHitsAverage()", $error);
      $errorObj->errorMessage("CNT_faq_body");
    }
    else {
      $this->_avgScore = $newAverage;
    }

    /* calculate new composite score */
    $this->newCompositeScore();
  } //END FUNC changeHitsAverage

  /* 
   * calulates new composite score using current hits and average score
   *
   * @access public
   */
  function newCompositeScore() {
    $newScore = 3;

    /* check to see if not viewed in 7 months */
    $currDate = getdate();
    $currMonth = $currDate['mon'];
    $compareDate = mktime(0,0,0,$currMonth - 7);
    
    if($this->_updated <= $compareDate) {
      //      echo "DEBUG: faq older than 7 months";
      $newScore -= 1;
    }

    if($this->_hits > $_SESSION["SES_FAQ_STATS"]->getMaxHits()) {
      $newScore += ((log($this->_hits) / log(100)) / 2 );
    }

    if($this->_avgScore > 0 ) {
      if($this->_avgScore < 1) {
	$newScore -= 2.5;
      } else if($this->_avgScore > 1 && $this->_avgScore < 3) {
        $newScore -= .5;
      } else if($this->_avgScore > 3 && $this->_avgScore < 4) {
        $newScore += .5;
      } else if($this->_avgScore > 4 && $this->_avgScore <= 5) {
	$newScore += 1;
      }
    }

    if($newScore > 5) { 
      $newScore = 5;
    }

    $this->_compScore = round($newScore, 3);
    //    echo "Composite: ".$this->_compScore;
  } //END FUNC newCompositeScore

  /**
   * approves this FAQ if has an answer
   *
   * @access private
   * @see edit
   */
  function approve() {
    if(strlen($this->_answer) <= 1) {
      $error = $_SESSION["translate"]->it("Every FAQ needs to have an answer before being approved.");
      $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_edit()", $error);
      $errorobj->errorMessage("CNT_faq_body");
    } else {
      if(isset($_SESSION["OBJ_fatcat"])) {
        $_SESSION["OBJ_fatcat"]->activate($this->_id);
      }

      PHPWS_Approval::remove($this->_id, "faq");

      $this->_approved = 1;
      $this->_contact = NULL;
      $this->commit();
    }
  }

  /**
   * returns this FAQ question
   *
   * @return text
   * @access public
   */
  function getQuestion() {
    return $this->_label;
  }

  function getListLabel() {
    return "<a href=\"./index.php?module=faq&amp;FAQ_op=view&amp;".
           "FAQ_id=" . $this->_id ."\">".$this->_label."</a>";
  }

  function getListAvgScore() {
    return $this->_avgScore;
  }

  /**
   * handles FAQ operations
   *
   * @access public
   */
  function action() {
    $showView = FALSE;

    if(isset($_REQUEST["FAQ_adv"])) {
      $showView = TRUE;

      switch ($_REQUEST["FAQ_adv"]) {
      case "edit":
        $this->edit();
      return;
      case "delete":
        $this->delete();
        if(isset($_REQUEST["YES"])) return;
      break;
      case "hide":
        $this->_hidden = 1;
        if(isset($_SESSION["OBJ_fatcat"])) {
          $_SESSION["OBJ_fatcat"]->deactivate($this->_id);
        }
      break;
      case "show":
        $this->_hidden = 0;
        if(isset($_SESSION["OBJ_fatcat"])) {
          $_SESSION["OBJ_fatcat"]->activate($this->_id);
        }
      break;
      case "approve":
	$this->approve();
        $this->view($_SESSION["SES_FAQ_MANAGER"]->isCommentsAllowed(), 
		    $_SESSION["SES_FAQ_MANAGER"]->isAnonScoringAllowed(), 
		    $_SESSION["SES_FAQ_MANAGER"]->_faqLinkBack);
      return;
      break;
      case "unapprove":
	$this->_approved = 0;
        if(isset($_SESSION["OBJ_fatcat"])) {
          $_SESSION["OBJ_fatcat"]->deactivate($this->_id);
        }
      break;
      } //END SWITCH
    }

    if(isset($_REQUEST["recalculateScore"])) {
       $showView = TRUE;
       $this->changeHitsAverage($_REQUEST["admin_hits"], $_REQUEST["admin_average"]);
    }

    if(isset($_REQUEST["resetScore"])) {
       $showView = TRUE;
       $this->changeHitsAverage(0, 3.0);
    }

    if($showView) {
      $this->commit();

      $this->view($_SESSION["SES_FAQ_MANAGER"]->isCommentsAllowed(), 
		  $_SESSION["SES_FAQ_MANAGER"]->isAnonScoringAllowed(), 
		  $_SESSION["SES_FAQ_MANAGER"]->_faqLinkBack);

    } //END IF FAQ_adv
  } //END FUNC action

} //END OF PHPWS_FAQ CLASS

?>