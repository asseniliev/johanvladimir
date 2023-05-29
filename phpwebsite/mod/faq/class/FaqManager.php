<?php

require_once(PHPWS_SOURCE_DIR . "core/Manager.php");
require_once(PHPWS_SOURCE_DIR . "core/EZform.php");
require_once(PHPWS_SOURCE_DIR . "core/Form.php");
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
require_once(PHPWS_SOURCE_DIR . "core/Error.php");

require_once(PHPWS_SOURCE_DIR . "/mod/faq/conf/faq.php");
require_once(PHPWS_SOURCE_DIR . "mod/fatcat/class/CategoryView.php");

require_once(PHPWS_SOURCE_DIR . "mod/faq/class/FaqViews.php");
require_once(PHPWS_SOURCE_DIR . "mod/approval/class/Approval.php");
require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * This class controls interactions with the Faq module and it's PHPWS_Faq
 * objects.
 *
 * @version $Id: FaqManager.php,v 1.47 2004/08/25 16:30:18 steven Exp $
 * @author Darren Greene <dg49379@appstate.edu>
 * @package Faq
 */
class PHPWS_FaqManager extends PHPWS_Manager {

  /**
   * Most recent faq that Manager has accessed
   *
   * @var object
   * @example $this->_currentFAQ = new PHPWS_Faq();
   * @access private
   */
  var $_currentFAQ;

  /**
   * Current layout view choosen by admin
   *
   * 0 => No Categories - Clickable Questions
   *   => PHPWS_FAQ_NOCAT_CLICKQUES_VIEW
   * 1 => No Categories - Question and Answer
   *   => PHPWS_FAQ_NOCAT_QA_VIEW
   * 2 => Fatcat Categories 
   *   => PHPWS_FAQ_CAT_VIEW
   *
   * @var integer
   * @example $this->_currentLayout = PHPWS_FAQ_CAT_VIEW;
   * @access private
   */
  var $_currentLayout = 0;

  /**
   * Flag to indicate the use of bookmarks for 'Question and Answer' view
   *
   * @ver integer
   * @example $this->_useBookmarks = 0;
   * @access private
   */
  var $_useBookmarks = 1;

  /**
   * Number of FAQs to display in the 'Question and Answer' view
   *
   * @var integer
   * @example $this->_pagingLimit = 6;
   * @access private
   */
  var $_pagingLimit = 5;

  /**
   * Flag to indicate if users not logged in can score a FAQs
   *
   * @var integer
   * @example $this->_allowScoring = 0;
   * @access private
   */
  var $_allowScoring = 0;

  /**
   * Flag to indicate how FAQs should be sorted
   *   0 => Sort By Rating System
   *   1 => Sort By Last Updated
   *
   * @var integer
   * @example $this->_sortingMethod = 0;
   * @access private
   */
  var $_sortingMethod = FAQ_ORDERBY_COMPOSITE_SCORE;

  /**
   * Flag to indicate if FAQs can have user comments
   *
   * @var integer
   * @example $this->_allowComments = 0;
   * @access private
   */
  var $_allowComments = 0;

  /**
   * Flag to indicate if users can suggest FAQs
   *
   * @var integer
   * @example $this->_allowSuggestions = 0;
   * @access private
   */
  var $_allowSuggestions = 0;

  /** 
   * Rating legend for scoring FAQs
   *
   * @var array
   * @example $this->_scoringLenged = array(1=>"High Score");
   * @access private
   */
  var $_scoringLegend = NULL;

  /**
   * Link back to FAQ listing
   *
   * @var text
   * @example $this->_faqLinkBack = "<a href='index.php?module=faq&amp;FAQ_op=view'>";
   * @access private
   */
  var $_faqLinkBack = NULL;

  /**
   * Used for category view to display a custom top level bullet. Contains file extension.
   *
   * @var text
   * @example $this->_customTopBullet = ".gif";
   * @access private
   */
  var $_customTopBullet = NULL;

  /**
   * Used for category view to display a custom sub level bullet. Contains file extension.
   *
   * @var text
   * @example $this->_customSubBullet = ".gif";
   * @access private
   */
  var $_customSubBullet = NULL;

  /**
   * Used for category view to disable showing any custom uploaded bullets.
   *
   * @var text
   * @example $this->_defaultBullets = 0;
   * @access private
   */
  var $_defaultBullets  = NULL;

  /**
   * Any user message that need to be written to the screen are stored in the variable.
   *
   * @var text
   * @example $this->_message = "Invaild Selection";
   * @access private
   */
  var $_message = NULL;

  /**
   * Used for categories view to turn on and off the displaying of the updated text.
   *
   * @var text
   * @example $this->_showUpdatedMsg = 1;
   * @access private
   */
  var $_showUpdatedMsg = NULL;

  /**
   * Used for categories view to turn on and off the displaying the number of FAQs in a category.
   *
   * @var text
   * @example $this->_showCatNumFAQs = 1;
   * @access private
   */
  var $_showCatNumFAQs   = NULL;

  var $_categoryView = NULL;

  /**
   * Constructor for PHPWS_FaqManager class
   *
   * @access public
   */
  function PHPWS_FaqManager() {
    $_SESSION["hasScored"] = "";

    /* initialize inherited manager class */
    $this->setModule("faq");
    $this->setRequest("FAQ_MAN_OP");
    $this->setTable("mod_faq_questions");
    $this->init();

    /* grab stored settings from database */
    $settingsRow = $GLOBALS["core"]->sqlSelect("mod_faq_settings");
    
    /* initialize this FAQ manager with values from database */
    if($settingsRow) {
      if(isset($settingsRow[0]["anon"])) 
	$this->_allowScoring     = $settingsRow[0]["anon"];

      if(isset($settingsRow[0]["comments"])) 
	$this->_allowComments    = $settingsRow[0]["comments"];

      if(isset($settingsRow[0]["suggestions"])) 
	$this->_allowSuggestions = $settingsRow[0]["suggestions"];

      if(isset($settingsRow[0]["score_text"])) 
	$this->_scoringLegend    = unserialize($settingsRow[0]["score_text"]);

      if(isset($settingsRow[0]["layout_view"])) 
	$this->_currentLayout    = $settingsRow[0]["layout_view"];

      if(isset($settingsRow[0]["use_bookmarks"])) 
	$this->_useBookmarks     = $settingsRow[0]["use_bookmarks"];

      if(isset($settingsRow[0]["paging_limit"])) 
	$this->_pagingLimit      = $settingsRow[0]["paging_limit"];

      if(isset($settingsRow[0]["sorting_method"])) 
	$this->_sortingMethod    = $settingsRow[0]["sorting_method"];
      
      if(isset($settingsRow[0]["custom_top_bullet"])) 
	$this->_customTopBullet  = $settingsRow[0]["custom_top_bullet"];

      if(isset($settingsRow[0]["custom_sub_bullet"])) 
	$this->_customSubBullet  = $settingsRow[0]["custom_sub_bullet"];

      if(isset($settingsRow[0]["default_bullets"])) 
	$this->_defaultBullets   = $settingsRow[0]["default_bullets"];

      if(isset($settingsRow[0]["cat_show_updated"])) 
	$this->_showUpdatedMsg   = $settingsRow[0]["cat_show_updated"];

      if(isset($settingsRow[0]["cat_show_num_faqs"])) 
	$this->_showCatNumFAQs   = $settingsRow[0]["cat_show_num_faqs"];
    }

    $this->_categoryView = new CategoryView;
    $this->checkCustomImages();
  } //END FUNC PHPWS_FaqManager()

  function checkCustomImages() {
    $runQuery = FALSE;

    if(!is_dir(FAQ_DIR)) {

      if(isset($this->_defaultBullets)) {
	$runQuery = TRUE;
	$queryData["default_bullets"] = 1;
      }

      $this->_defaultBullets = 1;
    }

    if(!is_null($this->_customTopBullet)) {
      $file = FAQ_DIR . FAQ_CTOP_IMAGE_PREFIX . "." . $this->_customTopBullet;
      if(!is_file($file)) {
	$runQuery = TRUE;
	$queryData["custom_top_bullet"] = NULL;
	$this->_customTopBullet = NULL;
      }
    }

    if(!is_null($this->_customSubBullet)) {
      $file = FAQ_DIR . FAQ_CSUB_IMAGE_PREFIX . "." . $this->_customSubBullet;
      if(!is_file($file)) {
	$runQuery = TRUE;
	$queryData["custom_sub_bullet"] = NULL;
	$this->_customSubBullet = NULL;
      }
    }

    if($runQuery)
      $GLOBALS["core"]->sqlUpdate($queryData, "mod_faq_settings");

  }

  function getSortingMethod() {
    return $this->_sortingMethod;
  }


  /**
   * displays menu to access and manage FAQs
   *
   * @access public
   */
  function menu() {
    $beg_active_link = "<a style=\"text-decoration:none\" href=\"index.php?module=faq&amp;FAQ_op=";

    if($this->_currentLayout == PHPWS_FAQ_CAT_VIEW) {
      $view_label = $_SESSION["translate"]->it("Categories");      
    } else {
      $view_label = $_SESSION["translate"]->it("View");
    }

    $view_op    = "viewFAQs";
    $new_label  = $_SESSION["translate"]->it("New");
    $new_op     = "newFAQ";
    $stats_label = $_SESSION["translate"]->it("Stats");
    $stats_op    = "viewStats";
    $unapproved_label = $_SESSION["translate"]->it("Unapproved / Hidden");
    $unapproved_op    = "viewUnapprovedHidden";
    $settings_label   = $_SESSION["translate"]->it("Settings");
    $settings_op      = "viewSettings";
    $suggest_label    = $_SESSION["translate"]->it("Suggest a FAQ");
    $suggest_op       = "suggestFAQ";

    /* Determine if user is allowed to configure setting for FAQ module */
    if($_SESSION["OBJ_user"]->admin_switch) {      
      /* ADMIN */

      if(isset($_REQUEST["FAQ_op"]) && ($_REQUEST["FAQ_op"] == "viewFAQs" || $_REQUEST["FAQ_op"] == "view")) {
	$tags["ACTIVE_VIEW_FAQ_LABEL"] = $beg_active_link . $view_op . "\">" . $view_label . "</a>";
      } else {
        $tags["VIEW_FAQ_LABEL"] = $view_label;
        $tags["VIEW_FAQ_OP"] = $view_op;
      }

      if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "newFAQ") {
	$tags["ACTIVE_NEW_FAQ_LABEL"] = $beg_active_link . $new_op . "\">" . $new_label . "</a>";
      } else {
        $tags["NEW_FAQ_LABEL"] = $new_label;
        $tags["NEW_FAQ_OP"] = $new_op;
      }

      if((isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewStats") || isset($_REQUEST["FAQ_Stats_op"])) {
	$tags["ACTIVE_STATS_FAQ_LABEL"] = $beg_active_link . $stats_op . "\">" . $stats_label . "</a>";
      } else {
	$tags["STATS_FAQ_LABEL"] = $stats_label;
        $tags["STATS_FAQ_OP"] = $stats_op;
      }

      if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewUnapprovedHidden") {
	$tags["ACTIVE_UNAPPROVED_FAQ_LABEL"] = $beg_active_link . $unapproved_op . "\">" . $unapproved_label . "</a>";
      } else {
	$tags["UNAPPROVED_FAQ_LABEL"] = $unapproved_label;
        $tags["UNAPPROVED_FAQ_OP"] = $unapproved_op;
      }


      if($_SESSION["OBJ_user"]->allow_access("faq", "change_settings")) {
        if(isset($_REQUEST["FAQ_op"]) && ($_REQUEST["FAQ_op"] == "viewSettings" || $_REQUEST["FAQ_op"] == "savesettings")) {
   	  $tags["ACTIVE_SETTINGS_FAQ_LABEL"] = $beg_active_link . $settings_op . "\">" . $settings_label . "</a>";
	} else {
  	  $tags["SETTINGS_FAQ_LABEL"] = $settings_label;
  	  $tags["SETTINGS_FAQ_OP"] = $settings_op;
	}

      }

      $elements[0] = PHPWS_Form::formHidden("module", "faq");
      $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "menu.tpl"); 
      $content = PHPWS_Form::makeForm("faq_admin_menu", "index.php", $elements);

      $GLOBALS["CNT_faq_body"]["title"]    = $_SESSION["translate"]->it("FAQ (Frequently Asked Questions)");
      $GLOBALS["CNT_faq_body"]["content"] .= $content;
    }
    else {
      /* Normal User */
      if($this->_allowSuggestions) {
        $suggestText = $_SESSION["translate"]->it("Suggest a FAQ");
        if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "suggestFAQ") {
   	  $tags["ACTIVE_SUGGEST_FAQ_LABEL"] = $beg_active_link . $suggest_op . "\">" . $suggest_label . "</a>";
	} else {
   	  $tags["SUGGEST_FAQ_LABEL"] = $suggest_label;
	  $tags["SUGGEST_FAQ_OP"] = $suggest_op;
	}

        $viewText = $_SESSION["translate"]->it("View");
        if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewFAQs") {
   	  $tags["ACTIVE_VIEW_FAQ_LABEL"] = $beg_active_link . $view_op . "\">" . $view_label . "</a>";
        } else {
          $tags["VIEW_FAQ_LABEL"] = $view_label;
          $tags["VIEW_FAQ_OP"] = $view_op;
        }
	
        $elements[0]  = PHPWS_Form::formHidden("module", "faq");
        $elements[0] .= PHPWS_Form::formHidden("FAQ_user", "normal");
        $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "menu.tpl"); 
        $content      = PHPWS_Form::makeForm("faq_admin_menu", "index.php", $elements);

	$GLOBALS["CNT_faq_body"]["title"]    = $_SESSION["translate"]->it("FAQ (Frequently Asked Questions)");
	$GLOBALS["CNT_faq_body"]["content"] .= $content;
      } 
    }
  } //END FUNC menu()

  /**
   * Show FAQs that have not been approved or are hidden
   *
   * @access private
   */
  function _viewUnapproved() {
    /* list obtained through inherited manager */
    $title   = $_SESSION["translate"]->it("Unapproved / Hidden FAQs");
    $content = $this->getList("unapproved", $_SESSION["translate"]->it("Unapproved / Hidden FAQs"));
    $GLOBALS["CNT_faq_body"]["content"] .= $content;

    $this->setListFunction("_viewUnapproved");
  } //END FUNC _viewUnapproved()

  /**
   * allows editing of faqs
   *
   * If multiple ids are passed then only the first id in the array
   * is choosen to be edited.
   *
   * @param array
   * @access private
   */
  function _edit($ids = NULL) {
    /* check to see if have access to edit FAQ */
    if($_SESSION["OBJ_user"]->admin_switch) {
      /* if more than one id then only choose first id */
      if($ids) {
        $this->_currentFAQ = new PHPWS_Faq($ids[0]);
        $this->_currentFAQ->edit();
      }
      else {
        /* no id passed so create new FAQ */
        $this->_currentFAQ = new PHPWS_Faq();
        $this->_currentFAQ->edit();
      }
    }
    else {
      $error = $_SESSION["translate"]->it("You are not authorized to perform this action.");
      $errorObj = new PHPWS_Error("darren_notes", "PHPWS_FAQ_MANAGER::_edit()", $error);
      $errorObj->errorMessage("CNT_faq_body");
    }
  } //END FUNC _edit()


  /**
   * allows deletion of FAQs from database
   *
   * @param array
   * @access private
   */
  function _delete($ids) {
    /* check to see if any FAQs to delete */
    if($ids) {

      /* check to see if more than one FAQ needs deletion */
      if(sizeOf($ids) >= 1 || $_REQUEST["ids"]) {

        /* check to see if ready to delete */
        if(isset($_REQUEST["MYES"])) { 
          $errorObj = NULL;

          /* delete FAQs */
          $idarr = explode(",", $_REQUEST["ids"]);

          foreach ($idarr as $id) {

            if($id && !PHPWS_Error::isError($errorObj)) {

	      $this->_currentFAQ = new PHPWS_Faq($id);
              $errorObj = $this->_currentFAQ->kill();

              /* if removed from database then remove entry from fatcat */
	      if($_SESSION["OBJ_fatcat"] && !PHPWS_Error::isError($errorObj)) {
		$_SESSION["OBJ_fatcat"]->purge($id, "faq");
	      }
	    }
	  }

          /* determine if error occurred */
          if(PHPWS_Error::isError($errorObj)) {

	    $errorObj->errorMessage;   //print error

	  } else {

            $_SESSION["SES_FAQ_STATS"]->init();  //reset stats

	    $tags["TITLE"] = $_SESSION["translate"]->it("Removed Entries");
	    $tags["MESSAGE_BODY"] = $_SESSION["translate"]->it("FAQ(s) have been removed from database.").
	                         "<br /><br />".$this->_faqLinkBack.$_SESSION["translate"]->it("Return to Listing")."</a>";
	    $content = PHPWS_Template::processTemplate($tags, "faq", "general_msg.tpl");
	    $GLOBALS["CNT_faq_body"]["content"] .= $content;

	  }

        } else if(isset($_REQUEST["MNO"])) {

  	    /* user aborted deletion */
	    $tags["TITLE"] = $_SESSION["translate"]->it("Action Canceled");
	    $tags["MESSAGE_BODY"] = $_SESSION["translate"]->it("FAQ(s) have not been removed.").
                                    "<br /><br />".$this->_faqLinkBack.$_SESSION["translate"]->it("Return to Listing").
                                    "</a>";

            $content = PHPWS_Template::processTemplate($tags, "faq", "general_msg.tpl");
   	    $GLOBALS["CNT_faq_body"]["content"] .= $content;

	} else {
	  /* first time through show confirmation */
         $title = $_SESSION["translate"]->it("Confirm");
         $content = $_SESSION["translate"]->it("Are you sure you want to delete the following FAQs?")."<br />";
    
	 /* show question so user can identify FAQ */
	 $arrids[0] = "";
         foreach ($ids as $id) {
	  $this->_currentFAQ = new PHPWS_Faq($id);
          $content .= "<br /><b>".$this->_currentFAQ->getQuestion()."</b>";
          $arrids[0] .= $id.",";
 	 }

         /* passed list of ids with form for later deletion */
         $associativeIds["ids"] = $arrids[0];

         $formElements[0]  = PHPWS_Form::formHidden($associativeIds);
         $formElements[0] .= PHPWS_Form::formHidden("PHPWS_MAN_ITEMS[]");
         $formElements[0] .= PHPWS_Form::formHidden("FAQ_MAN_OP", "delete");
         $formElements[0] .= "<br />";
         $formElements[0] .= PHPWS_Form::formSubmit("Yes", "MYES");
         $formElements[0] .= PHPWS_Form::formSubmit("No", "MNO");

	 $action = "index.php?module=faq";
         $content .= PHPWS_Form::makeForm("delete_multiple", $action, $formElements); 

	 $GLOBALS["CNT_faq_body"]["content"] .= $content;
	}
      }
    }
  } //END FUNC _delete()


  /** 
   * allows viewing a FAQ
   *
   * @param array
   * @access private
   */
  function _view($ids, $approvalView = NULL) {
    /* determine if any FAQs to view */
    if($ids) {

      $this->_currentFAQ = new PHPWS_Faq($ids[0]);

      if($approvalView === NULL) {

        $this->_currentFAQ->view($this->_allowComments, $this->_allowScoring, $this->_faqLinkBack);

      } else {

	$this->_currentFAQ->view(0, 0, NULL, 1);
      }
    }
  } //END FUNC _view()


  /**
   * administor choose to change the composite score of a FAQ
   *
   * @access private
   */
  function _scoreCurrentFAQ() {
    if($this->_currentFAQ) {
      $this->_currentFAQ->addScore($_REQUEST["score_faq"] + 1);
      $this->_currentFAQ->view($this->_allowComments, $this->_allowScoring, $this->_faqLinkBack);
    }
    else {
      //build error object
    }
  } //END FUNC _scoreCurrentFAQ()
  

  /**
   * used by layout view functions
   *
   * @access private
   */
  function _pageCurrentView(&$pagedIds, &$pagingInfo, &$tags) {
      $pagedIds   = $_SESSION["SES_FAQ_STATS"]->pageFAQs();
      $pagingInfo = $_SESSION["SES_FAQ_STATS"]->getPagingInfo();
 
      /* set tags for showing paging info */
      if($_SESSION["SES_FAQ_STATS"]->isPagerNeeded() ||
         $this->_currentLayout == PHPWS_FAQ_NOCAT_CLICKQUES_VIEW
	) {
        $tags["PAGING_BACKWARD_LINK"] = $pagingInfo["BackLink"];
        $tags["PAGING_SECTION_LINKS"] = $pagingInfo["SectionLinks"];
        $tags["PAGING_FORWARD_LINK"]  = $pagingInfo["ForwardLink"];
        $tags["PAGING_LIMIT_LINKS"]   = $pagingInfo["LimitLinks"];
        $tags["PAGING_SECTION_INFO"]  = $_SESSION["translate"]->it("Currently viewing FAQS ").$pagingInfo["SectionInfo"];
      }
  }

  function _categoriesView() {
    $this->_categoryView->setModule("faq");
    $this->_categoryView->setOp("FAQ_op=viewFAQs");
    $this->_categoryView->showCount($this->_showCatNumFAQs);
    $this->_categoryView->showUpdated($this->_showUpdatedMsg);

    if(!$this->_defaultBullets) {
      if(!is_null($this->_customTopBullet)) {
        $extension = $this->_customTopBullet;
        $file = "./images/faq/" . FAQ_CTOP_IMAGE_PREFIX . "." . $extension;

        if(!is_file($file)) {
          $queryData["custom_top_bullet"] = NULL;
          $GLOBALS["core"]->sqlUpdate($queryData, "mod_faq_settings");
          $this->_categoryView->setCustomTopBullet(NULL);
        } else {
          $this->_categoryView->setCustomTopBullet($file);
        }
      } else {
	$this->_categoryView->setCustomTopBullet(NULL);
      }

      if(!is_null($this->_customSubBullet)) {
        $extension = $this->_customSubBullet;
        $file = "./images/faq/" . FAQ_CSUB_IMAGE_PREFIX . "." . $extension;

        if(!is_file($file)) {
          $queryData["custom_top_bullet"] = NULL;
          $GLOBALS["core"]->sqlUpdate($queryData, "mod_faq_settings");
          $this->_categoryView->setCustomSubBullet(NULL);
        } else {

          $this->_categoryView->setCustomSubBullet($file);
        }
      } else {
	$this->_categoryView->setCustomSubBullet(NULL);
      }

    } else {
      $this->_categoryView->setCustomTopBullet(NULL);
      $this->_categoryView->setCustomSubBullet(NULL);
    }

    if(!isset($_REQUEST["category"])) {
      $content = $this->_categoryView->categoriesMainListing();
    } else {
      $content = $this->_categoryView->categoriesSCView();
    }

    $GLOBALS["CNT_faq_body"]["content"] .= $content;    
  }

  /**
   * shows listing of current viewable FAQs
   *
   * @access private
   */ 
  function _list() {
    if($this->_currentLayout == PHPWS_FAQ_NOCAT_QA_VIEW) {
      $_SESSION["SES_FAQ_STATS"]->setPagerLimit($this->_pagingLimit);
    } 

    /* determine what type of query and layout the user has choosen */
    if(isset($_REQUEST["PHPWS_MAN_PAGE"]) && $_REQUEST["PHPWS_MAN_PAGE"] == "unapproved") {
      $this->_viewUnapproved();
    } else if($this->_currentLayout == PHPWS_FAQ_NOCAT_CLICKQUES_VIEW) {
      FaqViews::_noCategoriesQuesClickView();
    } else if($this->_currentLayout == PHPWS_FAQ_NOCAT_QA_VIEW) {

      if($this->_useBookmarks) {
	FaqViews::_noCategoriesBookmarkesQuesAnsView();
      } else {
	FaqViews::_noCategoriesQuesAnsView();
      }

    } else if($this->_currentLayout == PHPWS_FAQ_CAT_VIEW) {
      $this->_categoriesView();
    }
  } //END FUNC _list()


  /**
   * show current legend for scoring FAQs
   *
   * This function returns the current legend in either a form to edit the legend or 
   * as simply a string of text.
   *
   * @param text $mode The type format the legend should be returned in i.e. form, text
   * @return array
   * @access private
   */
  function _showLegend($mode = "form") {
    $legendElements[0] = NULL;
    $_counterForScores = 6;

    if($mode == "form" ) {

      foreach ($this->_scoringLegend as $description) {
        $_counterForScores--;

	$legendTags["SCORE_NUMBER"]      = $_counterForScores;
	$legendTags["SCORE_DESCRIPTION"] = PHPWS_Form::formTextField("score_text[]" , $description, 30); 

        $legendElements[0] .= PHPWS_Template::processTemplate($legendTags, "faq", "scoreItems.tpl");
      }

      return $legendElements[0];
    }
    else if($mode == "text") {

      foreach ($this->_scoringLegend as $description) {
        $_counterForScores--;

	$content .= $_SESSION["translate"]->it($_counterForScores).")&nbsp;&nbsp;";
	$content .= $description; 
        $content .= "<br />";
      }

      return $content;
    }
  } //END FUNC _showLegend()


  /**
   * Allows an admistrator to change the settings of FAQ.
   *
   * @access public
   */
  function changeSettings() {
    $elements[0] = NULL;
    if(!isset($_REQUEST["FAQ_menu"])) {
      if($_SESSION["OBJ_user"]->allow_access("faq")) {
	if(isset($this->_message)) {
	  $tags["MESSAGE"] = $this->_message;
	  $this->_message = NULL;
	}

        $tags["OPTIONS_HEADER_LABEL"] = $_SESSION["translate"]->it("Options");

	/* link to add FAQ to a menu */
        if($GLOBALS["core"]->moduleExists("menuman") && $_SESSION['OBJ_user']->allow_access("menuman", "add_item")) {
          $linkText = $_SESSION["translate"]->it("Add a menu link for the FAQ module.");
          $get_var["FAQ_op"] = "viewSettings";
          $get_var["FAQ_menu"] = "yes";	  
	  $tags["ADD_TO_MENU_LABEL"] = PHPWS_Text::moduleLink($linkText, "faq", $get_var);
	}

        $tags["ALLOW_ANON_CHECKBOX"]  = PHPWS_Form::formCheckBox("allowAnon", "1", $this->_allowScoring);
        $tags["ALLOW_ANON_LABEL"]     = $_SESSION["translate"]->it("Allow users to rate FAQs.");

        if($GLOBALS["core"]->moduleExists("comments")) {
          $tags["ALLOW_COMMENTS_CHECKBOX"] = PHPWS_Form::formCheckBox("allowComments", "1", $this->_allowComments);
          $tags["ALLOW_COMMENTS_LABEL"]    = $_SESSION["translate"]->it("Allow users to post comments.");
        }

        $tags["ALLOW_SUGGESTIONS_CHECKBOX"] = PHPWS_Form::formCheckBox("allowSuggestions", "1", $this->_allowSuggestions);
        $tags["ALLOW_SUGGESTIONS_LABEL"]    = $_SESSION["translate"]->it("Allow users to suggest FAQs.");

        $tags["LAYOUT_VIEW_HEADER_LABEL"]   = $_SESSION["translate"]->it("Layout View");

        $tags["BASIC_QA_LAYOUT_RADIO"] = PHPWS_Form::formRadio("layout_option", PHPWS_FAQ_NOCAT_QA_VIEW, $this->_currentLayout);
        $tags["BASIC_QA_LAYOUT_TITLE"] = $_SESSION["translate"]->it("Basic Question and Answer View");

        $tags["BASIC_QA_LAYOUT_PAGINGLIMIT"]  = $_SESSION["translate"]->it("Limit results by showing ");
        $tags["BASIC_QA_LAYOUT_PAGINGLIMIT"] .= PHPWS_Form::formTextField("paging_limit", $this->_pagingLimit, 3);
        $tags["BASIC_QA_LAYOUT_PAGINGLIMIT"] .= $_SESSION["translate"]->it(" questions per page.");

        $tags["BASIC_QA_NOBOOKMARKS_RADIO"]  = PHPWS_Form::formRadio("bookmark_option", 0, $this->_useBookmarks);
        $tags["BASIC_QA_NOBOOKMARKS_TITLE"]  = $_SESSION["translate"]->it("Question and Answer");
        $tags["BASIC_QA_NOBOOKMARKS_HELP"]   = CLS_help::show_link("faq", "basic_no_bookmarks_view");      
        $tags["BASIC_QA_USEBOOKMARKS_RADIO"] = PHPWS_Form::formRadio("bookmark_option", 1, $this->_useBookmarks);
        $tags["BASIC_QA_USEBOOKMARKS_TITLE"] = $_SESSION["translate"]->it("Bookmarked Questions");
        $tags["BASIC_QA_USEBOOKMARKS_HELP"]  =  CLS_help::show_link("faq", "basic_bookmarks_view");

        $tags["LISTING_LAYOUT_RADIO"] = PHPWS_Form::formRadio("layout_option", PHPWS_FAQ_NOCAT_CLICKQUES_VIEW, $this->_currentLayout);
        $tags["LISTING_LAYOUT_TITLE"] = $_SESSION["translate"]->it("No Categories - Hyperlinked Questions");
        $tags["LISTING_LAYOUT_HELP"]  = CLS_help::show_link("faq", "nocat_clickable_view");

	if(isset($_SESSION["OBJ_fatcat"])) {
          $tags["CATEGORY_LAYOUT_RADIO"] = PHPWS_Form::formRadio("layout_option", PHPWS_FAQ_CAT_VIEW, $this->_currentLayout);
          $tags["CATEGORY_LAYOUT_TITLE"] = $_SESSION["translate"]->it("Category View");
          $tags["CATEGORY_LAYOUT_HELP"]  = CLS_help::show_link("faq", "category_view");

	  $tags["CATEGORY_TOP_TEXT"]  = $_SESSION["translate"]->it("Top-Level Bullet Image: ");
	  $tags["CATEGORY_TOP_FIELD"] = PHPWS_Form::formFile("topBullet");

	  if(!empty($this->_customTopBullet)) {
	    $tags["CATEGORY_TOP_IMAGE"] = "<img src=\"". FAQ_HTTP_DIR . "customTopBullet." . $this->_customTopBullet . "\" />"; 
	    $tags["CATEGORY_TOP_REMOVE"] = "<a href=\"index.php?module=faq&amp;FAQ_op=deleteCatImage&amp;Cat_Level=top\">" . 
	      $_SESSION["translate"]->it("Delete") . "</a>";
	  }

	  $tags["CATEGORY_SUBL_TEXT"] = $_SESSION["translate"]->it("Sub-Level Bullet Image: ");
	  $tags["CATEOGRY_SUBL_FIELD"] = PHPWS_Form::formFile("sub_level_bullet");

	  if(!empty($this->_customSubBullet)) {
	    $tags["CATEGORY_SUB_IMAGE"] = "<img src=\"". FAQ_HTTP_DIR . "customSubBullet." . $this->_customSubBullet . "\" />"; 
	    $tags["CATEGORY_SUB_REMOVE"] = "<a href=\"index.php?module=faq&amp;FAQ_op=deleteCatImage&amp;Cat_Level=sub\">" . 
	      $_SESSION["translate"]->it("Delete") . "</a>";
	  }

          $tags["CATEGORY_DEFAULT_CB"] = 
	    PHPWS_Form::formCheckBox("default_bullets", "1", $this->_defaultBullets);
	  $tags["CATEGORY_DEFAULT_TEXT"] = $_SESSION["translate"]->it("Use Only Default Bullets");

	  $tags["CB_SHOW_UPDATED"] = PHPWS_Form::formCheckBox("catShowUpdated", "1", $this->_showUpdatedMsg);
	  $tags["SHOW_UPDATED_TEXT"] = $_SESSION["translate"]->it("For each category indicate the date a FAQ was last updated.");

	  $tags["CB_SHOW_NUM_FAQS"] = PHPWS_Form::formCheckBox("catShowNumFAQs", "1", $this->_showCatNumFAQs);
	  $tags["SHOW_NUM_FAQS_TEXT"] = $_SESSION["translate"]->it("Show the number of FAQs in each category.");

	  if($this->_defaultBullets) {
	    $tags["HIGHLIGHT_START"] = "<span style='color:maroon'>**";
	    $tags["HIGHLIGHT_END"]   = "**</span>";
	    $tags["HIGHLIGHT_END"]   .= "<span class='smalltext'>". $_SESSION["translate"]->it(" (Must be unchecked to use custom bullets)") . "</span>";
	  }

	}

        $tags["LEGEND_TITLE"] = $_SESSION["translate"]->it("Legend for Rating FAQs");

        $tags["SUBMIT_BUTTON"]    = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Save Settings"));
        $tags["SCORE_LABEL_LIST"] = $this->_showLegend("form");
	$tags["TITLE"] = $_SESSION["translate"]->it("Settings");


	$tags["SORTING_TITLE"] = $_SESSION["translate"]->it("Sorting Methods");
	$tags["SORTING_NOTICE"] = $_SESSION["translate"]->it("*This option does not apply apply to the category view.");
	$tags["RD_SORTING_COMPSCORE"] = PHPWS_Form::formRadio("sorting_option", FAQ_ORDERBY_COMPOSITE_SCORE, $this->_sortingMethod);
	$tags["TX_SORTING_COMPSCORE"] = $_SESSION["translate"]->it("Composite Score (Based On User Rating)");

	$tags["RD_SORTING_UPDATED"] = PHPWS_Form::formRadio("sorting_option", FAQ_ORDERBY_UPDATED, $this->_sortingMethod);
	$tags["TX_SORTING_UPDATED"] = $_SESSION["translate"]->it("Most Recently Accessed");

	$tags["RD_SORTING_QUESTION"] = PHPWS_Form::formRadio("sorting_option", FAQ_ORDERBY_QUESTION, $this->_sortingMethod);
	$tags["TX_SORTING_QUESTION"] = $_SESSION["translate"]->it("Alphabetized by Question");

	$elements[0] .= PHPWS_Form::formHidden("FAQ_op", "savesettings");
        $elements[0] .= PHPWS_Form::formHidden("module", "faq");
        $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "settings.tpl");

        $content = PHPWS_Form::makeForm("change_settings", "index.php", $elements, "post", FALSE, TRUE);
	$GLOBALS["CNT_faq_body"]["content"] .= $content;
      }
      else {
        $content  = $_SESSION["translate"]->it("You are not authorized to perform this action.");
        $errorobj = new PHPWS_Error("darren_notes", "PHPWS_FAQ_MANAGER::_edit()", $content);
        $errorobj->errorMessage("CNT_faq_body");
      }
    } else {
      if($GLOBALS['core']->moduleExists("menuman")) {
        $op_string = "&amp;FAQ_op=viewFAQs";
        $call_back = "./index.php?module=faq&amp;FAQ_op=viewSettings";
        $item_active = 1;
        $_SESSION['OBJ_menuman']->add_module_item("faq", $op_string, $call_back, $item_active);
      }
    }
  } //END FUNC changeSettings()


  /**
   * saves any changes made to the settings for FAQ.
   *
   * @access public
   */
  function saveSettings() {
    if($_SESSION["OBJ_user"]->allow_access("faq")) {

      if(isset($_REQUEST["allowAnon"])) {
        $queryData["anon"] = 1;
      }
      else {
        $queryData["anon"] = 0;
      }

      if(isset($_REQUEST["allowComments"])) {
        $queryData["comments"] = 1;
      } else {
	$queryData["comments"] = 0;
      }

      if(isset($_REQUEST["allowSuggestions"])) {
        $queryData["suggestions"] = 1;
      } else {
	$queryData["suggestions"] = 0;
      }

      if(isset($_REQUEST["score_text"])) {
        $counter = 0;
        foreach ($_REQUEST["score_text"] as $legendDescription) {
	  $legendArr[$counter] = $legendDescription;
          $counter++;
        }
      }

      if(isset($_SESSION["OBJ_fatcat"])) {
	if(isset($_REQUEST["default_bullets"])) {
	  $queryData["default_bullets"] = 1;
	} else {
	  $queryData["default_bullets"] = 0;
	}

	if(isset($_REQUEST["catShowUpdated"])) {
	  $queryData["cat_show_updated"] = 1;
	} else {
	  $queryData["cat_show_updated"] = 0;	  
	}

	if(isset($_REQUEST["catShowNumFAQs"])) {
	  $queryData["cat_show_num_faqs"] = 1;
	} else {
	  $queryData["cat_show_num_faqs"] = 0;	  
	}

	if(isset($_FILES)){ 
	  $extension = "";
	}
	

	if($_FILES["topBullet"]["name"] == 0) {
	  if(!empty($_FILES["topBullet"]["name"])) {
	    $result = $this->_saveImage("topBullet", FAQ_CTOP_IMAGE_PREFIX, $extension);
	    
	    if(PHPWS_Error::isError($result)) {
	      $errorobj = $result;
	      $queryData["custom_top_bullet"] = NULL;
	      
	    } else {
	      $queryData["custom_top_bullet"] = $extension;
	    }
	  }
	}

	if($_FILES["sub_level_bullet"]["name"] == 0) {
	  if(!empty($_FILES["sub_level_bullet"]["name"])) {
	    $result = $this->_saveImage("sub_level_bullet", FAQ_CSUB_IMAGE_PREFIX, $extension);
	      
	    if(PHPWS_Error::isError($result)) {
	      $errorobj = $result;
	      $queryData["custom_sub_bullet"] = NULL;
	    } else {
	      $queryData["custom_sub_bullet"] = $extension;
	    }
	  }
	}
      }

      $queryData["use_bookmarks"] = $_REQUEST["bookmark_option"];
      $queryData["layout_view"] = $_REQUEST["layout_option"];
      $queryData["sorting_method"] = $_REQUEST["sorting_option"];
      $queryData["score_text"]  = serialize($legendArr);

      if(!is_numeric($_REQUEST["paging_limit"]) || $_REQUEST["paging_limit"] <= 0) {
         $content = $_SESSION["translate"]->it("The paging limit must be a number and greater than zero.");
         $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveSettings()", $content);
      } else {
        $queryData["paging_limit"] = PHPWS_Text::parseInput($_REQUEST["paging_limit"]);
      }

      if(!isset($errorobj) || !PHPWS_Error::isError($errorobj)) {
        $result = $GLOBALS["core"]->sqlUpdate($queryData, "mod_faq_settings");

        if($result) { 
          $row = $GLOBALS["core"]->sqlSelect("mod_faq_settings");

          $this->_allowScoring = $row[0]["anon"];
	  $this->_allowComments = $row[0]["comments"];
	  $this->_allowSuggestions = $row[0]["suggestions"];
          $this->_scoringLegend = unserialize($row[0]["score_text"]);
	  $this->_sortingMethod = $row[0]["sorting_method"];

	  $this->_currentLayout = $row[0]["layout_view"];
          if($_SESSION["SES_FAQ_STATS"]) {
            if($this->_currentLayout == 2) {
	      $_SESSION["SES_FAQ_STATS"]->turnPagingOff();
            } else {
	      $_SESSION["SES_FAQ_STATS"]->turnPagingOn();
	    }
          }

	  if(isset($_SESSION["OBJ_fatcat"])) {
	    $this->_defaultBullets   = $row[0]["default_bullets"];
	    $this->_customSubBullet  = $row[0]["custom_sub_bullet"];
	    $this->_customTopBullet  = $row[0]["custom_top_bullet"];
	    $this->_showUpdatedMsg   = $row[0]["cat_show_updated"];
	    $this->_categoryView->showUpdated($this->_showUpdatedMsg);
	    $this->_showCatNumFAQs   = $row[0]["cat_show_num_faqs"];
            $this->_categoryView->showCount($this->_showCatNumFAQs);
	  }

	  $this->_useBookmarks = $row[0]["use_bookmarks"];
	  $this->_pagingLimit = $row[0]["paging_limit"];


          $title = "Changed Settings";
          $content = "The changes you requested for the FAQ module have been successfully saved.<br /><br />";

	  $GLOBALS["CNT_faq_body"]["content"] .= $content;
          $this->changeSettings();

        } else {

          $content = $_SESSION["translate"]->it("There was a problem saving your settings.  Check the permission of the images/faq directory.");
          $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_edit()", $content);

        } //END CHECK TO SEE IF SAVED TO DATABASE
      } 
     } else {
       $content = $_SESSION["translate"]->it("You are not authorized to perform this action.");
       $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveSettings()", $content);
     } 

    /* Show any errors */
    if(isset($errorobj) && PHPWS_Error::isError($errorobj)) {
       $errorobj->errorMessage("CNT_faq_body");
       $this->changeSettings();
    }
  } //END FUNC saveSettings()


  function deleteCatImage($level) {

    if(isset($_REQUEST['Cat_bullet_yes'])) {
      if($level == "top")
	$name = FAQ_CTOP_IMAGE_PREFIX . "." . $this->_customTopBullet;
      else
	$name = FAQ_CSUB_IMAGE_PREFIX . "." . $this->_customSubBullet;

	@unlink(FAQ_DIR . $name);
	
	if(is_file(FAQ_DIR . $name)) {
	  $message = $_SESSION['translate']->it("There was a problem removing the bullet.  " . 
						"Check the permissions of the images directory for faq.");
	} else {

	  if($level == "top")
	    $queryData["custom_top_bullet"] = NULL;
	  else
	    $queryData["custom_sub_bullet"] = NULL;

	  $result = $GLOBALS["core"]->sqlUpdate($queryData, "mod_faq_settings");
	  if($result) {

	    if($level == "top") 
	      $this->_customTopBullet = NULL;
	    else
	      $this->_customSubBullet = NULL;

	    $message = $_SESSION['translate']->it("Bullet successfully removed.");
	  } else {
	    $message = $_SESSION['translate']->it("There was a problem when trying to update the database.");
	  }
	}

	$this->_message = $message;     
	$this->changeSettings();

    } else if(isset($_REQUEST['Cat_bullet_no'])) {
      $this->_message = $_SESSION['translate']->it("No image was deleted from the database.");
      $this->changeSettings();

    } else {
      $title = $_SESSION['translate']->it("Delete Category Bullet Confirmation");

      $form = new EZform("PHPWS_Cat_Bullet_delete");
      $form->add("module", "hidden", "faq");
      $form->add("FAQ_op", "hidden", "deleteCatImage");
      $form->add("Cat_Level", "hidden", $level);

      $form->add("Cat_bullet_yes", "submit", $_SESSION['translate']->it("Yes"));
      $form->add("Cat_bullet_no", "submit", $_SESSION['translate']->it("No"));
      
      $tags = array();
      $tags = $form->getTemplate();
      $tags["MESSAGE"] = $_SESSION['translate']->it("Are you sure you want to delete this bullet?");

      if($level == "top") {
	$tags["IMAGE_PREVIEW"] = "<img src=\"". FAQ_HTTP_DIR . FAQ_CTOP_IMAGE_PREFIX . "." . $this->_customTopBullet . "\" />"; 
      } else {
	$tags["IMAGE_PREVIEW"] = "<img src=\"". FAQ_HTTP_DIR . FAQ_CSUB_IMAGE_PREFIX . "." . $this->_customSubBullet . "\" />"; 
      }      

      $content = PHPWS_Template::processTemplate($tags, "faq", "deleteBullet.tpl");

      $GLOBALS['CNT_faq_body']['title'] = $title;
      $GLOBALS['CNT_faq_body']['content'] = $content;
    }
  }

  function _saveImage($formName, $imageNameNoExt, &$extension) {
    if(is_dir(FAQ_DIR)) {
      include(PHPWS_SOURCE_DIR."conf/allowedImageTypes.php");
      $types = $allowedImageTypes;
      if(in_array($_FILES[$formName]['type'], $types)) {
	$extension = substr($_FILES[$formName]['name'], -3);
      
	$file = FAQ_DIR . $imageNameNoExt . "." . $extension;

	if(@move_uploaded_file($_FILES[$formName]['tmp_name'], $file) && is_file($file)) {	      
	  chmod($file, 0644);
	  return true;
	} else {
	  $content = $_SESSION["translate"]->it("There was a problem uploading the specified bullet.");
	  $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveSettings()", $content);
	  return $errorobj;
	}

      } else {
	$content = $_SESSION["translate"]->it("The uploaded image was not an allowed type.");
	$errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveSettings()", $content);
	return $errorobj;
      }
    } else {
      $content = $_SESSION["translate"]->it("The directory images/faq directory either doesn't not exist or is not readable.");
      $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveImage()", $content);
      return $errorobj;
    }
  }


  /**
   * diplays form to contact a user that has suggested a faq
   *
   * @access public
   */
  function contactUser($email, $recipient, $dontSend = FALSE) {
    if(!isset($_REQUEST["send_now"]) || $dontSend) {
      $tags["RECIPIENT_LABEL"] = "<b>".$_SESSION["translate"]->it("To:")."</b>";
      $tags["RECIPIENT_FIELD"] = "$recipient &#160;&#160; &lt;<a href=mailto::$email>$email</a>&gt;";

      $tags["SUBJECT_LABEL"] = "<b>".$_SESSION["translate"]->it("Subject:")."</b>";
      $defaultSubject = "Question Regarding:  ".$this->_currentFAQ->_label;
      $tags["SUBJECT_FIELD"] = PHPWS_Form::formTextField("subject", $defaultSubject, 60);
 
      $tags["BODY_LABEL"] = "<b>".$_SESSION["translate"]->it("Message:")."</b>";
      
      if(isset($_REQUEST["body"]))
	$body = $_REQUEST["body"];
      else
	$body = "";

      $tags["BODY_FIELD"] = PHPWS_Form::formTextArea("body", $body, 15, 50);
 
      $tags["CANCEL_BUTTON"] = PHPWS_Form::formSubmit("Cancel", "FAQ_op");

      $tags["SEND_EMAIL_BUTTON"] = PHPWS_Form::formSubmit("Send Email", "FAQ_op");
      $tags["TITLE"] = $_SESSION["translate"]->it("Contact User");

      $elements[0] = PHPWS_Form::formHidden("FAQ_id", $this->_currentFAQ->_id);
      $elements[0] .= PHPWS_Form::formHidden("FAQ_email", $email);
      $elements[0] .= PHPWS_Form::formHidden("send_now", "true");
      $elements[0] .= PHPWS_Form::formHidden("FAQ_name", $recipient);
      $elements[0] .= PHPWS_Form::formHidden("module", "faq");
      $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "email.tpl");
      $content = PHPWS_Form::makeForm("faq_email_user", "index.php", $elements);

      $GLOBALS["CNT_faq_body"]["content"] .= $content;
    } else {
       if($_REQUEST["body"] == "") {
        $content = $_SESSION["translate"]->it("The body of the message may not be left blank.");
        $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_contactUser()", $content);
        $errorobj->errorMessage("CNT_faq_body");

	$this->contactUser($_REQUEST["FAQ_email"], $_REQUEST["FAQ_name"], TRUE);
	return;
       }

       if($_REQUEST["FAQ_email"]) {
         $to = PHPWS_Text::parseInput($_REQUEST["FAQ_email"]);
       }

       if($_REQUEST["subject"]) {
         $subject = PHPWS_Text::parseInput($_REQUEST["subject"]);
       }

       if($_REQUEST["body"]) {
         $message = PHPWS_Text::parseInput($_REQUEST["body"]);
       }

       $headers = "From:  Admin-".$_SESSION["OBJ_user"]->username." <".$_SESSION["OBJ_user"]->email.">\r\n";
       $headers .= "Reply-To: ".$_SESSION["OBJ_user"]->email;
       $result = mail($to, $subject, $message, $headers, "-f{$_SESSION['OBJ_user']->email}");
       if(!$result) {
         $content  = $_SESSION["translate"]->it("Error Sending Email. Try Again");
         $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_contactUser()", $content);
         $errorobj->errorMessage("CNT_faq_body");
	 $this->contactUser($_REQUEST["FAQ_email"], $_REQUEST["FAQ_name"], TRUE);
       } else {
	 $content  = $_SESSION["translate"]->it("The email to ");
	 $content .= $_REQUEST["FAQ_name"];
         $content .= $_SESSION["translate"]->it(" has been sent.")."<br />";
	 $content .= "<br /><a href=\"index.php?module=faq&amp;FAQ_op=view&amp;FAQ_id=".$this->_currentFAQ->_id."\">";
	 $content .= $_SESSION["translate"]->it("Return to suggested FAQ")."</a>";

         $GLOBALS["CNT_faq_body"]["content"] .= $content;
       }
    }
  }

  /*
   * overides function in Manager class to allow the ability to set fatcat category active state
   * 
   * @param text $column The name of the column to update
   * @param mixed $value The value to set the column to
   * @return boolean TRUE on success and FALSE on failure
   * @access private
   */
  function _doMassUpdate($column, $value) {
    $errorObj = NULL;

    if(is_array($_REQUEST["PHPWS_MAN_ITEMS"]) && sizeof($_REQUEST["PHPWS_MAN_ITEMS"]) > 0) {
      // Begin sql update statement 
      $sql = "UPDATE " . PHPWS_TBL_PREFIX . $this->_table ." SET $column='$value' ";
   
      $isApproved = 0;
      $isHidden = 0;

      foreach($_REQUEST["PHPWS_MAN_ITEMS"] as $itemId) {
 	$isApproved = $GLOBALS["core"]->getOne("SELECT approved FROM mod_faq_questions WHERE id=".$itemId, TRUE);
        $isHidden = $GLOBALS["core"]->getOne("SELECT hidden FROM mod_faq_questions WHERE id=".$itemId, TRUE);

        switch($column) {
         case "hidden":
           if($_SESSION["OBJ_fatcat"]) {
             if($value) { 
	       // FAQ is hidden 
  	      $_SESSION["OBJ_fatcat"]->deactivate($itemId);          
	     }
             else if($isApproved) {
               // FAQ is not visible and is approved 
	       $_SESSION["OBJ_fatcat"]->activate($itemId);
	     }
	   }
         break;
	case "approved":
          /* add a check here to make sure that an answer field has been provided */
   	  $answer = $GLOBALS["core"]->getOne("SELECT answer FROM mod_faq_questions WHERE id=".$itemId, TRUE);

	  if(strlen($answer) <= 1) {
            $error  = $_SESSION["translate"]->it("Every FAQ needs to have an answer before being approved.");
	    $error .= "<br />";
            $error .= $_SESSION["translate"]->it("Please choose the option to edit this FAQ and add an answer.");
	    $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_edit()", $error);
	    $errorobj->errorMessage("CNT_faq_body");
            return FALSE;
	  }

          PHPWS_Approval::remove($itemId, "faq");

	  if($_SESSION["OBJ_fatcat"]) {
	    if(!$isHidden) {
              $_SESSION["OBJ_fatcat"]->activate($itemId);
	      $sql .= ", contact=NULL";
	    }
	  }
	}
      }

      // change ownership of FAQ
      $sql .= ", owner='".$_SESSION["OBJ_user"]->username."'";
      $sql .= ", editor='".$_SESSION["OBJ_user"]->username."'";

      $sql .= " WHERE id='";

      // Set flag to know when to add sql for checking against extra ids 
      $flag = FALSE;
      foreach($_REQUEST["PHPWS_MAN_ITEMS"] as $itemId) {
	if($flag)
	  $sql .= " OR id='";

	$sql .= $itemId . "'";
	$flag = TRUE;
      }

      // Execute query and test for failure 
      $result = $GLOBALS["core"]->query($sql);
      if($result) {
        $_SESSION["SES_FAQ_STATS"]->init();
	return TRUE;
      }
      else {
	return FALSE;
      }
    }

    $_SESSION["SES_FAQ_STATS"]->init();
  }// END FUNC _doMassUpdate()

  /**
   * returns flag to indicate if users can score FAQs
   *
   * @access public
   */
  function isAnonScoringAllowed() {
    return $this->_allowScoring;
  }

  /**
   * returns flag to indicate if users can post comments
   *
   * @access public
   */
  function isCommentsAllowed() {
    return $this->_allowComments;
  }

  /**
   * sets the link to get back to a listing
   *
   * format should be '<a href=\"index.php?module=faq&amp;FAQ_op=view>'
   *
   * @access public
   */
  function setFaqLinkBack($newLinkBack) {
    $this->_faqLinkBack = $newLinkBack;
  }

  /**
   * returns the link to get back to the last listing
   *
   * @access public
   */
  function getLinkBack() {
    return $this->_faqLinkBack;
  }

  /**
   * used by approval module to delete suggested FAQs
   *
   */
  function approvalRefuse($id) {
    $GLOBALS["core"]->sqlDelete("mod_faq_questions", "id", $id);
    PHPWS_Fatcat::purge($id, "faq");
  }

  /**
   * used by approval module to approve suggested FAQs
   *
   */
  function approvalApprove($id) {
    $data["approved"] = 1;
    $GLOBALS["core"]->sqlUpdate($data, "mod_faq_questions", "id", $id);
    PHPWS_Fatcat::activate($id, "faq");
  }

  function sqlForSorting() {
    switch($this->_sortingMethod) {
    case 0:
      return " compScore ";
    case 1:
      return " updated ";
    }
  }

  /**
   * handles user interaction with FAQ Manager
   *
   * @access public
   */
  function action() {
    $this->menu();

    // an admin option was selected inside the view of a FAQ so returns
    // control to PHPWS_Faq
    if(isset($_REQUEST["FAQ_adv"]) && !isset($_REQUEST["FAQ_op"]) || isset($this->_currentFAQ)) {
      // checked added for specific layouts
      if(!is_object($this->_currentFAQ) && isset($_REQUEST["FAQ_id"])) {
	$this->_currentFAQ = new PHPWS_Faq($_REQUEST["FAQ_op"]);
      }
      $this->_currentFAQ->action();
      $_SESSION["SES_FAQ_STATS"]->init();
    }

    /* Show view user requested */
    if(isset($_REQUEST["FAQ_op"])) {
      /* if not administor or deity then default to show all FAQs */
      if(!$_SESSION["OBJ_user"]->admin_switch && !$_SESSION["OBJ_user"]->isDeity() && 
	 $_REQUEST["FAQ_op"] != "suggestFAQ" && 
	 !isset($_REQUEST["submitRatedFAQ"]) &&
	 $_REQUEST["FAQ_op"] != "viewFAQs" && 
	 $_REQUEST["FAQ_op"] != "view" && 
	 $_REQUEST["FAQ_op"] != "submitSuggestedFAQ") {
        $this->_faqLinkBack = "<a href='index.php?module=faq&amp;FAQ_op=";
	$this->_faqLinkBack .= $_SESSION["translate"]->it("View FAQs")."'>";
	$this->_list();
      } 

      /* Check for submit buttons */
      // User choose to score FAQ
      if(isset($_REQUEST["submitRatedFAQ"])) {
	$this->_scoreCurrentFAQ();
      } 
      
      switch ($_REQUEST["FAQ_op"]) { 
      /* User actions */
      case "view":
      case "Cancel":
        $id = array($_REQUEST["FAQ_id"]);
        $this->_view($id);
        break;
      case "suggestFAQ":
        $this->_currentFAQ = new PHPWS_Faq();
        $this->_currentFAQ->edit();
        break;
      case "submitSuggestedFAQ":
        $this->_currentFAQ->submitFAQ();
        $_SESSION["SES_FAQ_STATS"]->init();
        break;
      case "viewFAQs":
        $this->_faqLinkBack  = "<a href='index.php?module=faq&amp;FAQ_op=";
        $this->_faqLinkBack .= "viewFAQs'>";
	$this->_list();
        break;
       //END USER ACTIONS

      /* More Admin options */
      case "deleteCatImage":
	$this->deleteCatImage($_REQUEST["Cat_Level"]);
	break;
      case "viewUnapprovedHidden":
        $this->_faqLinkBack  = "<a href='index.php?module=faq&amp;FAQ_op=";
        $this->_faqLinkBack .= "viewUnapprovedHidden'>";
	$this->_viewUnapproved();
      break;

      case "viewStats":
        $_SESSION["SES_FAQ_STATS"]->menuOptions();
      break;

      case "newFAQ":
	$this->_edit();
      break;

      case "submitNewFAQ":
      case "updateFAQ":
        $this->_currentFAQ->submitFAQ();
        $_SESSION["SES_FAQ_STATS"]->init();
      break;

      case "viewSettings":
	$this->changeSettings();
      break;

      case "savesettings":
	$this->saveSettings();
      break;

      case "email_user":
      case "Send Email":
	$this->contactUser($_REQUEST["FAQ_email"], $_REQUEST["FAQ_name"]);
      break;
      /* END ADMIN ACTIONS */
      }//END SWITCH STATEMENT

    } else {
       if(isset($_REQUEST["FAQ_Stats_op"])) {
         $_SESSION["SES_FAQ_STATS"]->action();
       }
    } //END FAQ_op
  }


  /** 
   * A operation on a FAQ listing was called in FAQ_Stats and since
   * all of manager's listing functions are centralized in this class
   * this function acts as a redirect back to the last query executed in
   * the FAQ_Stats class.
   */
  function bounceToStatsList() {
    $_SESSION["SES_FAQ_STATS"]->showLastQuery();
  }

  function search($where) {
    $sql = "SELECT id, label FROM " . PHPWS_TBL_PREFIX . "mod_faq_questions ";
    $sql .= $where . " AND hidden='0' AND approved = '1'";
    $result = $GLOBALS["core"]->getAll($sql);
    $resultsArray = array();

    if(!DB::isError($result) && is_array($result) && sizeof($result) > 0) {
      foreach($result as $row) {
	$resultsArray[$row['id']] = $row['label'];
      }
    } 

    return $resultsArray;
  }


  function showCatUpdatedMsg() {
    return $this->_showUpdatedMsg;
  }

  function showCatNumFAQs() {
    return $this->_showCatNumFAQs;
  }

}

?>