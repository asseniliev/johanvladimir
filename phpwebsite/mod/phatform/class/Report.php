<?php

require_once(PHPWS_SOURCE_DIR . "core/Error.php");

require_once(PHPWS_SOURCE_DIR . "core/Form.php");

require_once(PHPWS_SOURCE_DIR . "core/Text.php");

require_once(PHPWS_SOURCE_DIR . "core/Array.php");

require_once(PHPWS_SOURCE_DIR . "core/WizardBag.php");


/**
 * PHAT_Rport class for reporting on PHAT_Form data
 *
 * @version $Id: Report.php,v 1.34 2004/07/08 12:23:18 darren Exp $
 * @author  Adam Morton  <adam@NOSPAM.tux.appstate.edu>
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 */
class PHAT_Report {

  /**
   * Id of the current form to report on
   *
   * @var    integer
   * @access private
   */
  var $_formId = NULL;

  /**
   * Name of the current form to report on
   *
   * @var    string
   * @access private
   */
  var $_formName = NULL;

  /**
   * Entries for this form
   *
   * @var    array
   * @access private
   */
  var $_entries = NULL;

  /**
   * The total number of entries for this form
   *
   * @var    integer
   * @access private
   */
  var $_totalEntries = NULL;

  /**
   * The number of completed entries for this form
   *
   * @var    integer
   * @access private
   */ 
  var $_completeEntries = NULL;
 
  /**
   * The number of imcomlete entries for this form
   *
   * @var    integer
   * @access private
   */
  var $_incompleteEntries = NULL;

  /**
   * The current search query for this report
   *
   * This only searches usernames of users who have taken the form
   *
   * @var    string
   * @access private
   */
  var $_searchQuery = NULL;

  /**
   * The current filter being placed on the current entries
   *
   * Controls whether or not to list all, complete, or incomplete entries
   *
   * @var    integer
   * @access private
   */
  var $_listFilter = NULL;

  /**
   * Stores data for paging
   *
   * @var    integer
   * @access public
   */
  var $pageStart = NULL;

  /**
   * Stores data for paging
   *
   * @var    integer
   * @access public
   */
  var $pageSection = NULL;

  /**
   * Stores data for paging
   *
   * @var    integer
   * @access public
   */
  var $pageLimit = NULL;

  var $archive = NULL;

  /**
   *
   *
   *
   *
   */
  function PHAT_Report($archiveTable=NULL) {
    if(isset($archiveTable)) {
      $this->archive = $archiveTable;
    }

    $this->_formId = $_SESSION["PHAT_FormManager"]->form->getId();
    $this->_formName = $_SESSION["PHAT_FormManager"]->form->getLabel();

    $this->_searchQuery = NULL;
    $this->_listFilter = 1;
    $this->setEntries();
    $this->setComplete();
    $this->setIncomplete();

    $this->_totalEntries = sizeof($this->_entries);

    $this->pageStart = 0;
    $this->pageSection = 1;
    $this->pageLimit = PHAT_ENTRY_LIST_LIMIT;
  }

  function setArchive($tableName) {
    $this->archive = $tableName;
  }

  function getFormTable() {
    if(isset($this->archive)) {
      return PHPWS_TBL_PREFIX . $this->archive;
    } else {
      return PHPWS_TBL_PREFIX . "mod_phatform_form_" . $this->_formId;
    }
  }

  /**
   *
   *
   *
   *
   */
  function report() {
    $content = $_SESSION["PHAT_FormManager"]->menu();
    $content .= $this->formStats();
    $content .= $this->listEntries();

    return $content;
  }

  /**
   *
   *
   *
   *
   */
  function formStats() {
    $statsTags["FORM_NAME"] = $this->_formName;
    $statsTags["COMPLETED_LABEL"] = $_SESSION["translate"]->it("Completed");
    $statsTags["COMPLETED_NUM"] = $this->_completeEntries;
    $statsTags["INCOMPLETE_LABEL"] = $_SESSION["translate"]->it("Incomplete");
    $statsTags["INCOMPLETE_NUM"] = $this->_incompleteEntries;
    $statsTags["TOTAL_LABEL"] = $_SESSION["translate"]->it("Total");
    $statsTags["TOTAL_NUM"] = $this->_totalEntries;

    $statsTags["LAST_ENTRY_LABEL"] = $_SESSION["translate"]->it("Last Entry");
    $statsTags["LAST_ENTRY"] = $this->getLastEntry();
    
    $statsTags["LIST_LINK"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=list&amp;PHAT_FullList=1\">" . $_SESSION['translate']->it("Full List") . "</a>";

    $statsTags["PRINT"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=list&amp;lay_quiet=1\" target=\"_blank\">" . $_SESSION['translate']->it("Print") . "</a>";

    if(isset($_REQUEST["ARCHIVE_OP"]))
      $statsTags["BACK_LINK"] = $_SESSION["PHAT_advViews"]->archiveBack();

    $elements = array();
    $elements[0] = PHPWS_Form::formHidden("PHAT_REPORT_OP", "export");
    $elements[0] .= PHPWS_Form::formHidden("module", "phatform");
    $elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Export"), "export");

    if(!isset($this->archive))
      $statsTags["EXPORT"] = PHPWS_Form::makeForm("export_button", "index.php", $elements);

    return PHPWS_Template::processTemplate($statsTags, "phatform", "report/stats.tpl");
  }

  /**
   *
   *
   *
   *
   */
  function listEntries() {
    if(isset($_REQUEST['PHAT_EntrySearch'])) {
      $this->_searchQuery = PHPWS_Text::parseInput($_REQUEST['PHAT_EntrySearch']);
      $this->_listFilter = $_REQUEST['PHAT_ListFilter'];
      $this->setEntries();
      $this->pageStart = 0;
      $this->pageSection = 1;
      $this->pageLimit = $_REQUEST['PDA_limit'];
    } elseif(isset($_REQUEST['PHAT_FullList'])) {
      $this->_searchQuery = NULL;
      $this->_listFilter = 1;
      $this->setEntries();
      $this->pageStart = 0;
      $this->pageSection = 1;
    } else {
      if(isset($_REQUEST['PDA_start'])) {
	$this->pageStart = $_REQUEST['PDA_start'];
      } else {
	$_REQUEST['PDA_start'] = $this->pageStart;
      }
	
      if(isset($_REQUEST['PDA_section'])) {
	$this->pageSection = $_REQUEST['PDA_section'];
      } else {
	$_REQUEST['PDA_section'] = $this->pageSection;
      }

      if(isset($_REQUEST['PDA_limit'])) {
	$this->pageLimit = $_REQUEST['PDA_limit'];
      } else {
	$_REQUEST['PDA_limit'] = $this->pageLimit;
      }
    }

    $listTags = array();
    $listTags["ID_LABEL"] = $_SESSION["translate"]->it("ID");
    $listTags["USER_LABEL"] = $_SESSION["translate"]->it("User");
    $listTags["UPDATED_LABEL"] = $_SESSION["translate"]->it("Updated");
    $listTags["ACTION_LABEL"] = $_SESSION["translate"]->it("Action");

    $highlight = " class=\"bg_light\"";
    if(sizeof($this->_entries) > 0) {
      $data = PHPWS_Array::paginateDataArray($this->_entries, "index.php?module=phatform&amp;PHAT_REPORT_OP=list", $this->pageLimit, TRUE, array("<b>[ ", " ]</b>"), NULL, 10, TRUE);
    }

    if(isset($data) && is_array($data[0]) && (sizeof($data[0]) > 0)) {
      $listTags['LIST_ITEMS'] = NULL;
      foreach($data[0] as $entry) {
	$rowTags = array();
	$rowTags["HIGHLIGHT"] = $highlight;
	$rowTags["ID"] = $entry["id"];
	$rowTags["USER"] = $entry["user"];
	$rowTags["UPDATED"] = date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, $entry["updated"]);

	$rowTags["VIEW"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=view&amp;PHAT_ENTRY_ID=" . $entry['id'] . "\">" . $_SESSION['translate']->it("View") . "</a>";

	if(!isset($this->archive)) {
	  $rowTags["EDIT"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=edit&amp;PHAT_ENTRY_ID=" . $entry['id'] . "\">" . $_SESSION['translate']->it("Edit") . "</a>";
	  $rowTags["DELETE"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=confirmDelete&amp;PHAT_ENTRY_ID=" . $entry['id'] . "\">" . $_SESSION['translate']->it("Delete") . "</a>";
	}	

	PHPWS_WizardBag::toggle($highlight, " class=\"bg_light\"");
	$listTags['LIST_ITEMS'] .= PHPWS_Template::processTemplate($rowTags, "phatform", "report/row.tpl");
      }

      if(!isset($_REQUEST['lay_quiet'])) {
	if(($this->_totalEntries > $this->pageLimit)) {
	  $listTags['NAVIGATION_LINKS'] = $data[1]; 
	}
      
	$listTags['SECTION_INFO'] = $data[2];
	$listTags['SECTION_INFO_LABEL'] = $_SESSION['translate']->it("Entries");
      }
    } else {
      $listTags['LIST_ITEMS'] = "<tr><td colspan=\"4\" class=\"smalltext\">" . $_SESSION['translate']->it("No entries were found matching your search query.") . "</td></tr>";
    }      

    if(!isset($_REQUEST['lay_quiet'])) {
      $filterOptions = array(1=>$_SESSION['translate']->it("All"), 2=>$_SESSION['translate']->it("Incomplete"), 3=>$_SESSION['translate']->it("Complete"));
      $limitOptions = array(10, 20, 30, 40, 50);
      $hiddens = array("module"=>"phatform", "PHAT_REPORT_OP"=>"list");
      $elements[0] = PHPWS_Form::formHidden($hiddens);
      $elements[0] .= PHPWS_Form::formSelect("PHAT_ListFilter", $filterOptions, $this->_listFilter, FALSE, TRUE);
      
      $elements[0] .= PHPWS_Form::formSelect("PDA_limit", $limitOptions, $this->pageLimit, TRUE);
      
      if(!$_SESSION['PHAT_FormManager']->form->isAnonymous()) {
	$elements[0] .= PHPWS_Form::formTextField("PHAT_EntrySearch", $this->_searchQuery, 20, 255);
      }
      
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Search"));
      $listTags['SEARCH_FORM'] = PHPWS_Form::makeForm("PHAT_SearchEntries", "index.php", $elements);
    }

    $GLOBALS['CNT_phatform']['title'] = $_SESSION['PHAT_FormManager']->form->getLabel();
    if(isset($_REQUEST['lay_quiet']))
      echo PHPWS_Template::processTemplate($listTags, "phatform", "report/list.tpl");
    else
      return PHPWS_Template::processTemplate($listTags, "phatform", "report/list.tpl");
   }

  /**
   *
   *
   *
   *
   */
  function view($showLinks = TRUE) {
    /* Find the key into the entries array for the selected entry */
    foreach($this->_entries as $entryKey=>$entryValue) {
      if($entryValue["id"] == $_REQUEST["PHAT_ENTRY_ID"])
	break;
    }

    /* Get the data for the selected entry from the database */
    $sql = "SELECT * FROM " . $this->getFormTable() . " WHERE id='" . $_REQUEST["PHAT_ENTRY_ID"] . "'";
    $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $GLOBALS["core"]->getAll($sql);
    $entry = $result[0];

    $rowClass = NULL;
    $entryTags = array();
    $entryTags["ENTRY_DATA"] = NULL;
    /* Step through the entries values and feed them through the entryRow template */
    foreach($entry as $key=>$value) {
      $rowTags = array();
      if($key == "position") {
	continue;
      } elseif($key == "updated") {
	$value = date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, $value);
      }

      $attribute = " class=\"bg_light\" ";

      /* Toggle the row colors for better visability */
      PHPWS_WizardBag::toggle($rowClass, $attribute);
      if(isset($rowClass)) {
	$rowTags["ROW_CLASS"] = $rowClass;
      }
      $rowTags["ENTRY_LABEL"] = $key;

      if(preg_match("/a:.:{/", $value)) {
	$rowTags["ENTRY_VALUE"] = implode(", ", unserialize($value));
      } else {
	$rowTags["ENTRY_VALUE"] = PHPWS_Text::parseOutput($value);
      }

      $entryTags["ENTRY_DATA"] .= PHPWS_Template::processTemplate($rowTags, "phatform", "report/entryRow.tpl");
    }

    if(isset($this->archive)) 
      $entryTags["BACK_LINK"] = $_SESSION["PHAT_advViews"]->getArchiveViewLink();

    if($showLinks && !isset($_REQUEST['lay_quiet'])) {
      $entryTags["PRINT"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=view&amp;PHAT_ENTRY_ID=" . $_REQUEST['PHAT_ENTRY_ID'] . "&amp;lay_quiet=1\" target=\"_blank\">" . $_SESSION['translate']->it("Print View") . "</a>";

      /* Show the next and/or previous links to step through entries */
      if($entryKey < sizeof($this->_entries) - 1)
	$entryTags["NEXT"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=view&amp;PHAT_ENTRY_ID=" . $this->_entries[$entryKey+1]['id'] . "\">" . $_SESSION['translate']->it("Next Entry") . "</a>";
      
      if($entryKey > 0)
	$entryTags["PREVIOUS"] = "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=view&amp;PHAT_ENTRY_ID=" . $this->_entries[$entryKey-1]['id'] . "\">" . $_SESSION['translate']->it("Previous Entry") . "</a>";
    }

    $GLOBALS['CNT_phatform']['title'] = $_SESSION['PHAT_FormManager']->form->getLabel();
    /* Return the entire processed entry */
    if(isset($_REQUEST['lay_quiet']))
      echo PHPWS_Template::processTemplate($entryTags, "phatform", "report/entry.tpl");
    else
      return PHPWS_Template::processTemplate($entryTags, "phatform", "report/entry.tpl");
  }

  /**
   *
   *
   *
   *
   */
  function edit() {
    $_SESSION["PHAT_FormManager"]->form->setEditData(TRUE);
    $_SESSION["PHAT_FormManager"]->form->setDataId($_REQUEST["PHAT_ENTRY_ID"]);
    $_SESSION["PHAT_FormManager"]->form->loadUserData();
    return $_SESSION["PHAT_FormManager"]->form->view();
  }

  /**
   *
   *
   *
   *
   */
  function confirmDelete() {
    $hiddens['module'] = "phatform";
    $hiddens['PHAT_REPORT_OP'] = "delete";
    $hiddens['PHAT_ENTRY_ID'] = $_REQUEST['PHAT_ENTRY_ID'];
    $elements[0] = PHPWS_Form::formHidden($hiddens);
    
    $confirmTags['MESSAGE'] = $_SESSION['translate']->it("Are you sure you want to delete this entry?");
    $confirmTags['NO_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("No"), "PHAT_DeleteNo");
    $confirmTags['YES_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Yes"), "PHAT_DeleteYes");
    
    $elements[0] .= PHPWS_Template::processTemplate($confirmTags, "phatform", "report/deleteConfirm.tpl");
    $content = PHPWS_Form::makeForm("PHAT_EntryDeleteConfirm", "index.php", $elements);
    $content .= "<br /><hr /><br />";
    $content .= $this->view(FALSE);
    
    return $content;
  }

  /**
   *
   *
   *
   *
   */
  function delete() {
    if(isset($_REQUEST['PHAT_DeleteYes'])) {
      $GLOBALS['core']->sqlDelete("mod_phatform_form_" . $this->_formId, "id", $_REQUEST['PHAT_ENTRY_ID']);
      $_REQUEST['PHAT_REPORT_OP'] = "list";
      /* Find the key into the entries array for the selected entry */
      foreach($this->_entries as $entryKey=>$entryValue) {
	if($entryValue["id"] == $_REQUEST["PHAT_ENTRY_ID"])
	  break;
      }
      unset($this->_entries[$entryKey]);
      $message = $_SESSION['translate']->it("The form entry was successfully deleted from the database.");
      $this->PHAT_Report();
    } else if(isset($_REQUEST['PHAT_DeleteNo'])) {
      $_REQUEST['PHAT_REPORT_OP'] = "list";
      $message = $_SESSION['translate']->it("No form entry was deleted from the database.");
    }

    $GLOBALS["CNT_phatform"]["content"] .= $message;
    $this->action();
  }

  /**
   *
   *
   *
   *
   */
  function getLastEntry() {
    $lastEntry = NULL;
    $sql = "SELECT id, user, MAX(updated) FROM " . $this->getFormTable() . " GROUP BY user";
    $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $GLOBALS["core"]->getAll($sql);

    if(sizeof($result) > 0) {
      $lastEntry = $result[0]["user"] . " (" . date(PHPWS_DATE_FORMAT, $result[0]["MAX(updated)"]) . ")";
    }

    return $lastEntry;
  }

  /**
   *
   *
   *
   *
   */
  function setEntries() {
    //    $sql = "SELECT id, user, updated FROM " . PHPWS_TBL_PREFIX . "mod_phatform_form_" . $this->_formId;
    $sql = "SELECT id, user, updated FROM " . $this->getFormTable();


    if($this->_searchQuery || $this->_listFilter) {
      $sql .= " WHERE";
    }

    if(isset($this->_searchQuery) && ($this->_searchQuery != "")) {
      $sql .= " user LIKE '%" . $this->_searchQuery . "%' AND"; 
    }

    if($this->_listFilter) {
      switch($this->_listFilter) {
        case "1":
	$sql .= " position>='-1'";
	break;

        case "2":
        $sql .= " position!='-1'";
	break;

        case "3":
	$sql .= " position='-1'";
	break;	
      }
    }

    $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $GLOBALS["core"]->getAll($sql);

    $this->_entries = $result;
  }

  /**
   *
   *
   *
   *
   */
  function setComplete() {
    $sql = "SELECT count(id) FROM " . $this->getFormTable() . " WHERE position='-1'";
    $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $GLOBALS["core"]->getAll($sql);

    $this->_completeEntries = $result[0]["count(id)"];
  }

  /**
   *
   *
   *
   *
   */
  function setIncomplete() {
    $sql = "SELECT count(id) FROM " . $this->getFormTable() . " WHERE position!='-1'";
    $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $GLOBALS["core"]->getAll($sql);

    $this->_incompleteEntries = $result[0]["count(id)"];
  }

  function getEntries() {
    return $this->_entries;
  }

  /**
   * Called when a user tries to access functionality he/she has no permission to access
   *
   * @access private
   */
  function _accessDenied() {
    $error = new PHPWS_Error("phatform",
			     "PHAT_Report::action",
			     $_SESSION["translate"]->it("ACCESS DENIED!"),
			     "exit",
			     PHAT_DEBUG_MODE);
    /* Print error and exit script */
    $error->errorMessage();
  }// END FUNC accessDenied()


  /**
   *
   *
   *
   *
   */
  function action() {
    switch($_REQUEST["PHAT_REPORT_OP"]) {
      case "list":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_view")) {
	$content = $this->report();
      } else {
	$this->accessDenied();
      }
      break;

      case "edit":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_edit")) {
	$content = $_SESSION["PHAT_FormManager"]->menu() . $this->edit();
      } else {
	$this->accessDenied();
      }
      break;

      case "view":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_view")) {
	$content = $_SESSION["PHAT_FormManager"]->menu() . $this->view();
      } else {
	$this->accessDenied();
      }
      break;

      case "confirmDelete":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_delete")) {
	$content = $this->confirmDelete();
      } else {
	$this->accessDenied();
      }
      break;

      case "delete":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_delete")) {
	$content = $this->delete();
      } else {
	$this->accessDenied();
      }
      break;

      case "export":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_export")) {
	include(PHPWS_SOURCE_DIR . "mod/phatform/inc/Export.php");
	$error = export($this->_formId);
	if(PHPWS_Error::isError($error)) {
	  $error->errorMessage("CNT_phatform");
	  $content = $this->report();
	}
      } else {
	$this->accessDenied();
      }
      break;
    }

    if($content) {
      $GLOBALS["CNT_phatform"]["content"] .= $content;
    }
  }// END FUNC action()

}// END CLASS PHAT_Report


?>