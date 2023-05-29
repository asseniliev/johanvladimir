<?php

require_once(PHPWS_SOURCE_DIR . 'core/Manager.php');
require_once(PHPWS_SOURCE_DIR . 'core/Form.php');

require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * This class maintains statistics for FAQs.
 *
 * @version $Id: FaqStats.php,v 1.18 2003/10/30 17:10:22 steven Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 * @package FAQ
 */
class PHPWS_FaqStats extends PHPWS_Manager {
  /**
   * toggle to determine if should paged ids
   *
   * This is useful if you are using the category view to save memory.
   *
   * @var boolean
   * @access private
   */
  var $_isPagerOn = TRUE;

  /**
   * array with ids of viewable FAQs
   *
   * @var array
   * @access private
   */
  var $_pagedIDs = NULL;

  /**
   * flag to indicate that the pager needs updated
   *
   * @var boolean
   * @access private
   */
  var $_resetPager = 0;

  /**
   * contains section info in associative array
   *
   * @example $this->_pagerInfo["BackLink"]
   * @example $this->_pagerInfo["SectionLinks"]
   * @example $this->_pagerInfo["ForwardLink"]
   * @example $this->_pagerInfo["LimitLinks"]
   * @example $this->_pagerInfo["SectionInfo"]
   */
  var $_pagerInfo = NULL;

  /**
   * number of FAQS that are hidden
   *
   * @var int
   * @access private
   */
  var $_numHidden = 0;

  /**
   * number of FAQs that are not approved
   *
   * @var int
   * @access private
   */
  var $_numUnapproved = 0;

  /**
   * number of viewable FAQs (not hidden nor unapproved)
   *
   * @var int
   * @access private
   */
  var $_numViewable = 0;

  /**
   * highest average rating
   *
   * @var int
   * @access private
   */
  var $_maxAverage = 0;

  /**
   * highest number of hits
   *
   * @var int
   * @access private
   */
  var $_maxHits = 0;

  /**
   * highest number of people who have rated a FAQ
   *
   * @var int
   * @access private
   */
  var $_maxScore = 0;

  /**
   * stores user's choice for time period to view older FAQs
   *
   * @var int
   * @access private
   */
  var $_timeValue = 5;

  /**
   * stores name of last query executed
   *
   * @ver text
   * @access private
   */
  var $_currList = NULL;

  /**
   * constructor for PHPWS_FaqStats
   *
   * Probes the database to initialize all the data variables.
   *
   * @access public
   */
  function PHPWS_FaqStats() {
    /* initialize inherited manager class */
    $this->setModule("faq");
    $this->setRequest("FAQ_STATS_MAN_OP");
    $this->setTable("mod_faq_questions");
    PHPWS_Manager::init();

    //determine if should use paging
    if($_SESSION["SES_FAQ_MANAGER"]) {
      if($_SESSION["SES_FAQ_MANAGER"]->_currentLayout == 2) {
        $this->turnPagingOff();
      }
    }


    /* set class variables for statistics */
    $this->init();
  }

  /**
   * show menu to use FAQ statistics
   *
   * @access public
   */
  function menuOptions() {
    /* Build tags for template */
    /* Tags for Quick Stats */
    $tags["QUICK_STATS_TITLE"] = "<br /><b>".$_SESSION["translate"]->it("Quick FAQ Statistics")."</b>";
    $tags["NUM_VIEWABLE_TITLE"] = $_SESSION["translate"]->it("Viewable:  ");
    $tags["NUM_VIEWABLE_LABEL"] = $this->_numViewable;
    $tags["NUM_HIDDEN_TITLE"] = $_SESSION["translate"]->it("Hidden:  ");
    $tags["NUM_HIDDEN_LABEL"] = $this->_numHidden;
    $tags["NUM_UNAPPROVED_TITLE"] = $_SESSION["translate"]->it("Unapproved:  ");
    $tags["NUM_UNAPPROVED_LABEL"] = $this->_numUnapproved;

    /* Tags for special queries  */


    $tags["QUERIES_TITLE"] = "<b>".$_SESSION["translate"]->it("Special Queries")."</b>";
    $timeValues = array(1=>"1", 2=>"2", 3=>"3", 4=>"4", 5=>"5", 10=>"10", 15=>"15", 30=>"30");
    $tags["TIME_CHECKBOX"] = PHPWS_Form::formRadio("FAQ_Stats_action", "time_query");
    $tags["TIME_LABEL1"] = $_SESSION["translate"]->it("FAQs with no activity in last");
    $tags["SELECT_TIME_LABEL"] = PHPWS_Form::formSelect("time_value", $timeValues);
    $tags["TIME_LABEL2"] = $_SESSION["translate"]->it("months.");
    $tags["TIME_LABEL2"] .= CLS_help::show_link("faq", "older_faqs");
    $tags["HIGH_HITS_LOW_SCORE_CHECKBOX"] = PHPWS_Form::formRadio("FAQ_Stats_action", "high_hits_query");
    $tags["HIGH_HITS_LOW_SCORE"] = $_SESSION["translate"]->it("FAQs with a high number of hits and low score rating.");
    $tags["LOW_HITS_HIGH_SCORE_CHECKBOX"] = PHPWS_Form::formRadio("FAQ_Stats_action", "low_hits_query");
    $tags["LOW_HITS_HIGH_SCORE"] = $_SESSION["translate"]->it("FAQs with a low number of hits and high score rating.");

    if(isset($_SESSION["OBJ_fatcat"])) {
      $tags["NO_CATEGORY_CHECKBOX"] = PHPWS_Form::formRadio("FAQ_Stats_action", "no_category");
      $tags["NO_CATEGORY_LABEL"] = $_SESSION["translate"]->it("FAQs that do not have a category selected.");
    }

    $tags["SHOW_ALL_CHECKBOX"] = PHPWS_Form::formRadio("FAQ_Stats_action", "show_all_query", isset($this->_currList) ? FALSE : TRUE);
    $tags["SHOW_ALL_LABEL"] = $_SESSION["translate"]->it("Show all FAQs in database.");
    $tags["VIEW_QUERY_SUBMIT"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Show Results"), "FAQ_Stats_op");
    $tags["TITLE"] = $_SESSION["translate"]->it("Stats and Queries");

    $elements[0] = PHPWS_Form::formHidden("module", "faq");
    $elements[0] .= PHPWS_Template::processTemplate($tags, "faq", "stats/menu.tpl");
    $content = PHPWS_Form::makeForm("faq_stats_menu", "index.php", $elements);


    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  }

  /**
   * initalizes stats class for use (sets quick stats and updates pager) 
   *
   * @access public
   */
  function init() {
    $GLOBALS["core"]->setFetchMode("assoc");

    $this->setQuickStats();

    if($this->_isPagerOn) {
      $this->_setPager();
    } else {
      $this->_pagedIDs = NULL;
      $this->_pagerInfo = NULL;
    }
  }

  /**
   * updates the quick stats values 
   *
   * @access public
   */
  function setQuickStats() {
    /* fetch count objects */
    $_numHiddenObj = $GLOBALS["core"]->query("SELECT COUNT(hidden) FROM ".PHPWS_TBL_PREFIX."mod_faq_questions WHERE hidden=1");
    $_numUnapprovedObj = $GLOBALS["core"]->query("SELECT COUNT(approved) FROM ".PHPWS_TBL_PREFIX."mod_faq_questions WHERE approved=0");
    $_numViewableObj = $GLOBALS["core"]->query("SELECT COUNT(*) FROM ".PHPWS_TBL_PREFIX."mod_faq_questions WHERE hidden=0 AND approved=1");

    /* retreive contents of count objects and set class variables */
    //set hidden count
    if(!DB::isError($_numHiddenObj)) { 
      $hiddenRow = $_numHiddenObj->fetchRow();
      $this->_numHidden = $hiddenRow['COUNT(hidden)'];
    } else { $this->_numHidden = 0; }

    //set unapproved count
    if(!DB::isError($_numUnapprovedObj)) { 
      $unapprovedRow = $_numUnapprovedObj->fetchRow();
      $this->_numUnapproved = $unapprovedRow['COUNT(approved)'];
    } else { $this->_numUnapproved = 0; }

    //set unapproved count
    if(!DB::isError($_numViewableObj)) { 
      $viewableRow = $_numViewableObj->fetchRow();
      $this->_numViewable = $viewableRow['COUNT(*)'];
    } else { $this->_numViewable = 0; }

    $this->_maxHits = $GLOBALS["core"]->sqlMaxValue("mod_faq_questions", "hits");
    $this->_maxScore = $GLOBALS["core"]->sqlMaxValue("mod_faq_questions", "numScores");
    $this->_maxAverage = $GLOBALS["core"]->sqlMaxValue("mod_faq_questions", "avgScore");
  }

  /**
   * sets the number of ids to be viewed on a single page
   *
   * @access public
   */
  function setPagerLimit($newLimit) {
    if($this->_pagedIDs !== NULL) {
      $this->_pagedIDs->limit = $newLimit;
    }
  }

  function getSortingSQL() {
    $type = $_SESSION["SES_FAQ_MANAGER"]->getSortingMethod();

    switch($type) {
    case FAQ_ORDERBY_COMPOSITE_SCORE:
      return " ORDER BY compScore desc";
      break;
    case FAQ_ORDERBY_UPDATED:
      return " ORDER BY updated desc";
      break;
    case FAQ_ORDERBY_QUESTION:
      return " ORDER BY label ";
    }
  }



  /**
   * used to get ids of all FAQs users can view
   *
   * @access private
   */
  function _setPager() {
    $whereOrderClause = "where hidden = 0 AND approved = 1 ";

    $whereOrderClause .= $this->getSortingSQL();

    $currIDs = $GLOBALS["core"]->getAll("select id FROM ".PHPWS_TBL_PREFIX."mod_faq_questions ".$whereOrderClause);

    /* initialize pager with ids */
    $this->_pagedIDs = new PHPWS_Pager;
    $this->_pagedIDs->makeArray(TRUE);
    $this->_pagedIDs->setData($currIDs);
    $linkBack = "index.php?module=faq&amp;FAQ_op=viewFAQs";
    $this->_pagedIDs->setLinkBack($linkBack);
    $this->setQuickStats();
  }

  /**
   * gets next group of ids in pager class
   *
   * @return array
   * @access public
   */
  function pageFAQs() {
    if($this->_resetPager == 1 || $this->_pagedIDs == null) {
      $this->_setPager();
    }

    /* get new paged FAQs */
    $this->_pagedIDs->pageData();
    $currPagedIDs = $this->_pagedIDs->getData();

    /* update pager info for section details */
    $this->_pagerInfo["BackLink"] = $this->_pagedIDs->getBackLink();
    $this->_pagerInfo["SectionLinks"] = $this->_pagedIDs->getSectionLinks();
    $this->_pagerInfo["ForwardLink"] = $this->_pagedIDs->getForwardLink();
    $this->_pagerInfo["LimitLinks"] = $this->_pagedIDs->getLimitLinks();
    $this->_pagerInfo["SectionInfo"] = $this->_pagedIDs->getSectionInfo();

    return $currPagedIDs;
  }

  /**
   * query to display all FAQs approved, hidden, unapproved, etc. from database
   *
   * @access public
   */
  function showAllFAQs() {
    // used in FAQ_Manager class to get back to query
    $this->_currList = "showAllFAQs";
    $this->_listFunction = "showAllFAQs";
    $_SESSION["SES_FAQ_MANAGER"]->setListFunction("bounceToStatsList");
    $content = $_SESSION["SES_FAQ_MANAGER"]->getList("time_query", $_SESSION["translate"]->it("All FAQs Query"));

    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  }

  function showFAQsNotInACategory() {
    // used in FAQ_Manager class to get back to query
    $this->_currList = "showFAQsNotInACategory";
    $this->_listFunction = "showFAQsNotInACategory";
    $_SESSION["SES_FAQ_MANAGER"]->setListFunction("bounceToStatsList");

    $atLeastOne = FALSE;
    $_faqCatIds = $GLOBALS["core"]->query("SELECT module_id FROM ".PHPWS_TBL_PREFIX."mod_fatcat_elements");

    $sqlBlockIds = "";
    while ($faqId = $_faqCatIds->fetchRow()) {
      $atLeastOne = TRUE;
      $sqlBlockIds .= "id != ".$faqId['module_id']." AND ";
    }
    
    if($atLeastOne) {
      $sqlBlockIds = substr($sqlBlockIds, 0, -5);  
      $_SESSION["SES_FAQ_MANAGER"]->setSort($sqlBlockIds); 
    } else {
      if($this->_numViewable) {
        $_SESSION["SES_FAQ_MANAGER"]->setSort("id != -1");          
      } else {
        $_SESSION["SES_FAQ_MANAGER"]->setSort("id = 1"); 
      }
    }

    $content = $_SESSION["SES_FAQ_MANAGER"]->getList("specialqueries", $_SESSION["translate"]->it("FAQs With No Category"));
    $GLOBALS["CNT_faq_body"]["content"] .= $content;

  }

  /**
   * query to show FAQs with an age of so many months, default is five 
   *
   * @access public
   */
  function showOldFAQs() {
    // used in FAQ_Manager class to get back to query
    $this->_currList = "showOldFAQs";
    $this->_listFunction = "showOldFAQs";
    $_SESSION["SES_FAQ_MANAGER"]->setListFunction("bounceToStatsList");

    /* set to make sure Pager object in manager will remember time choosen by user */
    if(isset($_REQUEST["time_value"])) { 
      $this->_timeValue = $_REQUEST["time_value"];
    }

    $currDate = getdate();
    $currMonth = $currDate['mon'];

    $testDate = mktime (0,0,0,$currMonth - $this->_timeValue);

    /* set sorting in manager class */
    $_SESSION["SES_FAQ_MANAGER"]->setSort("updated <= $testDate");

    $title = $_SESSION["translate"]->it("FAQs that have not been active in over ").$this->_timeValue;

    if($this->_timeValue > 1) {
      $title .= $_SESSION["translate"]->it(" months."); 
    } else {
      $title .= $_SESSION["translate"]->it(" month."); 
    }

    $content = $_SESSION["SES_FAQ_MANAGER"]->getList("time_query", $title);
    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  }

  /**
   * shows FAQs with a high number of hits and low average score
   * 
   * @access public
   */
  function showHighHitsLowScore($highHits = null, $lowScore = null) {
    // used in FAQ_Manager class to get back to query
    $this->_currList = "showHighHitsLowScore";
    $this->_listFunction = "showHighHitsLowScore";
    $_SESSION["SES_FAQ_MANAGER"]->setListFunction("bounceToStatsList");

    $calHighHits = ((double)($this->_maxHits * (5 / 6)) + 1);
    $calLowAverage = PHPWS_FAQ_NUMLEGENDITEMS / 1.655;

    $_SESSION["SES_FAQ_MANAGER"]->setSort("hits > $calHighHits AND avgScore <= $calLowAverage");    
    $content = $_SESSION["SES_FAQ_MANAGER"]->getList("specialqueries", $_SESSION["translate"]->it("High Hits / Low Rating"));
    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  }

  /**
   * shows FAQs with a low number of hits and high average score
   *
   * @access public
   */
  function showLowHitsHighScore($lowHits = null, $highScore = null) {
    $this->_currList = "showLowHitsHighScore";
    $this->_listFunction = "showLowHitsHighScore";
    $_SESSION["SES_FAQ_MANAGER"]->setListFunction("bounceToStatsList");

    $calLowHits = $this->_maxHits * (1 / 4);
    $calHighAverage = $this->_maxAverage * (3 / 4);

    $_SESSION["SES_FAQ_MANAGER"]->setSort("hits <= $calLowHits AND avgScore >= $calHighAverage");    
    $content = $_SESSION["SES_FAQ_MANAGER"]->getList("specialqueries", $_SESSION["translate"]->it("Low Hits / High Rating"));
    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  }

  /**
   * determines if all the paged ids can be shown in one view
   *
   * @access public
   */
  function isPagerNeeded() {
    if($this->_pagedIDs !== NULL) {
      return !($this->_pagedIDs->_numrows == $this->_pagedIDs->_itemCount);
    } else {
      return FALSE;
    }
  }

  /**
   * returns paging info in an assoc array
   *
   * @return array
   * @access public
   */
  function getPagingInfo() {
    return $this->_pagerInfo;
  }

  /**
   * return maximum hits
   *
   * @return int
   * @access public
   */
  function getMaxHits() {
    return $this->_maxHits;
  }

  /**
   * return maximum number a FAQ has been rated by users
   *
   * @return int
   * @access public
   */
  function getMaxNumScores() {
    return $this->_maxScore;
  }

  /**
   * return maximum average score
   *
   * @return int
   * @access public
   */
  function getMaxAverage() {
    return $this->_maxAverage;
  }

  /**
   * returns number of viewable FAQs
   *
   * @return int
   * @access public
   */
  function getViewable() {
    return $this->_numViewable;
  }

  function turnPagingOff() {
    $this->_isPagerOn = FALSE;
    $this->_pagedIDs = NULL;
    $this->_pagerInfo = NULL;
  }

  function turnPagingOn() {
    $this->_isPagerOn = TRUE;
    $this->_setPager();
  }


  /**
   * Listing functions are in FAQ_Manager so this function is called to in FAQ_Manager to 
   * execute the last query again (user has made modifications to a FAQ)
   *
   */
  function showLastQuery() {
    eval("\$this->".$this->_currList."();");
  }

  /**
   * determines which query user has request and call appropriate function
   *
   * @access public
   */
  function action() {
    if(isset($_REQUEST["FAQ_Stats_op"])) {
      $this->_faqLinkBack  = "<a href='index.php?module=faq&amp;FAQ_Stats_op=".$_SESSION["translate"]->it("Show Results")."&amp;";

      if(isset($_REQUEST["FAQ_Stats_action"])) {
       switch($_REQUEST["FAQ_Stats_action"]) {
        case "high_hits_query":
          $this->_faqLinkBack .= "FAQ_Stats_action=high_hits_query'>";
          $this->showHighHitsLowScore();
	break;
        case "low_hits_query":
          $this->_faqLinkBack .= "FAQ_Stats_action=low_hits_query'>";
          $this->showLowHitsHighScore();
	break;
        case "time_query":
          $this->_faqLinkBack .= "FAQ_Stats_action=time_query'>";
	  $this->showOldFAQs();
	break;
        case "show_all_query":
          $this->_faqLinkBack .= "FAQ_Stats_action=show_all_query'>";
	  $this->showAllFAQs();
	break;
        case "no_category":
          $this->_faqLinkBack .= "FAQ_Stats_action=no_category'>";
          $this->showFAQsNotInACategory();
	break;
        } //END SWITCH STATEMENT
        $_SESSION["SES_FAQ_MANAGER"]->setFaqLinkBack($this->_faqLinkBack);
       } else {
	 $error = $_SESSION["translate"]->it("No option selected.");
	 $errorObj = new PHPWS_Error("faq", "PHPWS_FaqStats::action()", $error);
	 $errorObj->errorMessage("CNT_faq_body");
       }
    }
  }
}
?>