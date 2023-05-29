<?php

require_once (PHPWS_SOURCE_DIR . "core/List.php");
require_once (PHPWS_SOURCE_DIR . "core/Form.php");
require_once (PHPWS_SOURCE_DIR . "core/Text.php");
require_once (PHPWS_SOURCE_DIR . "core/Pager.php");
require_once (PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");
require_once(PHPWS_SOURCE_DIR . "mod/pagemaster/class/Page.php");

/**
 * This is the PHPWS_PageMaster class.  It controls interaction and
 * organization with PHPWS_Page objects.
 *
 * @version $Id: PageMaster.php,v 1.34 2004/10/18 15:02:23 steven Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package PageMaster
 */
class PHPWS_PageMaster {
  /**
   * Array of pages from the database (key=PAGE_id, value=page title)
   * @var    array
   * @access private
   */
  var $pages;

  /**
   * The current homepage stored as a PHPWS_Page object.
   * @var    object PHPWS_PAGE
   * @access private
   */
  var $homepage;

  var $_list;
  
  /**
   * Pagemaster constructor.
   *
   * @access public
   */
  function PHPWS_PageMaster () { 
    $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages");
    
    if($result) {
      foreach($result as $value) {
	$this->pages[$value["id"]] = $value["title"];
	if($value["mainpage"]) {
	  $this->homepage = new PHPWS_Page($value["id"]);
	}
      }
    }
  }// END FUNC PHPWS_PageMaster()

  /**
   * Displays the main menu with main functions like "New Page" and "List Pages"
   *
   * @access public
   */
  function main_menu () {
    $bg = NULL;
    $links = array();
 
    if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages")) {
      $links[] = "<a href=\"./index.php?module=pagemaster&amp;MASTER_op=new_page\">".$_SESSION["translate"]->it("New Page")."</a>";
    }

    $links[] = "<a href=\"./index.php?module=pagemaster&amp;MASTER_op=list_pages\">".$_SESSION["translate"]->it("List Pages")."</a>";

    if($_SESSION["OBJ_user"]->allow_access("pagemaster", "set_mainpage")) {
      $links[] = "<a href=\"./index.php?module=pagemaster&amp;MASTER_op=set_main\">".$_SESSION["translate"]->it("Set Main Page")."</a>";
    }

    $tags = array();
    $tags['LINKS'] = implode("&#160;|&#160;", $links);

    $GLOBALS["CNT_pagemaster"]["content"] .= PHPWS_Template::processTemplate($tags, "pagemaster", "menu/menu.tpl");

    if(@$_REQUEST["MASTER_op"] == "main_menu" || @$_REQUEST["MASTER_op"] == "list_pages" || isset($_REQUEST["PAGE_op"]["page_save"])) {
      $sql = "SELECT * FROM {$GLOBALS['core']->tbl_prefix}mod_pagemaster_pages WHERE new_page='1'";
      if(!$_SESSION["OBJ_user"]->allow_access("pagemaster", "needs_approval")) {
	$sql .= " AND created_username='" . $_SESSION["OBJ_user"]->username . "'";
      }
      $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages", "new_page", 1);
    }

    if(isset($result)) {
      $content = "<br /><b>" . $_SESSION["translate"]->it("Unsaved Pages") . "</b><br />";
      $content .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">
        <tr><td width=\"50%\" class=\"bg_dark\">" . $_SESSION["translate"]->it("Title") .
	CLS_help::show_link("pagemaster", "unsaved_title") .
	"</td><td width=\"30%\" align=\"center\" class=\"bg_dark\">" .
	$_SESSION["translate"]->it("Created By") .
	CLS_help::show_link("pagemaster", "unsaved_created") .
	"</td><td width=\"20%\" align=\"center\" class=\"bg_dark\">" .
	$_SESSION["translate"]->it("Actions") .
	CLS_help::show_link("pagemaster", "unsaved_action") .
	"</td></tr>";

      foreach($result as $value) {
	$tags = array();
	$content .= "<tr><td width=\"50%\"$bg>" . $value["title"] .
	  "</td><td width=\"30%\" align=\"center\"$bg>" .
	$value["created_username"] . " - " . $value["created_date"] .
	  "</td><td width=\"20%\" align=\"center\"$bg>";
	
	if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages")) {
	  $content .= "<a href=\"./index.php?module=pagemaster&amp;MASTER_op=finish&amp;PAGE_id={$value['id']}\">" . $_SESSION["translate"]->it("Finish") . "</a>";
	  $content .= "&#160;";
	}

	if($_SESSION["OBJ_user"]->allow_access("pagemaster", "delete_pages")) {
	  $content .= "<a href=\"./index.php?module=pagemaster&amp;MASTER_op=delete_page&amp;PAGE_id={$value['id']}\">" . $_SESSION["translate"]->it("Delete") . "</a>";
	}

	$content .=
	  "</td></tr>";

	PHPWS_WizardBag::toggle($bg, " class=\"bg_medium\"");
      }

      $content .= "</table><br />";

      $GLOBALS["CNT_pagemaster"]["content"] .= $content;
    }
  }// END FUNC main_menu()

  /**
   * Lists the current saved pages stored in the database
   *
   * @access public
   */
  function list_pages () {
    $listTags = array();
    $listTags['TITLE'] = $_SESSION["translate"]->it("Current web pages");
    $listTags['ID_LABEL'] = $_SESSION["translate"]->it("ID");
    $listTags['TITLE_LABEL'] = $_SESSION["translate"]->it("Title");
    $listTags['MAINPAGE_LABEL'] = $_SESSION["translate"]->it("Mainpage");
    $listTags['UPDATED_LABEL'] = $_SESSION["translate"]->it("Updated");
    $listTags['ACTIONS_LABEL'] = $_SESSION["translate"]->it("Actions");

    $listTags['TITLE_HELP'] = CLS_help::show_link("pagemaster", "current_title");
    $listTags['MAINPAGE_HELP'] = CLS_help::show_link("pagemaster", "current_mainpage");
    $listTags['UPDATED_HELP'] = CLS_help::show_link("pagemaster", "current_updated");
    $listTags['ACTIONS_HELP'] = CLS_help::show_link("pagemaster", "current_action");

    if(!isset($this->_list)) {
      $this->_list = new PHPWS_List;
    }

    $this->_list->setModule("pagemaster");
    $this->_list->setClass("PHPWS_Page");
    $this->_list->setTable("mod_pagemaster_pages");
    $this->_list->setDbColumns(array("active", "title", "updated_date", "mainpage"));
    $this->_list->setListColumns(array("Title", "Updated_Date", "Mainpage", "Actions"));
    $this->_list->setName("list");
    $this->_list->setOp("MASTER_op=list_pages");
    $this->_list->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
    $this->_list->setExtraListTags($listTags);

    $where = "new_page='0' AND approved='1'";
    if(!$_SESSION["OBJ_user"]->allow_access("pagemaster", "needs_approval")) {
      $where .= " AND created_username='" . $_SESSION["OBJ_user"]->username . "'";
    }
    $this->_list->setWhere($where);
    $this->_list->setOrder("title ASC");

    $GLOBALS['CNT_pagemaster']['content'] .= $this->_list->getList();
  }// END FUNC list_pages()

  /**
   * 2 functions: Displays interface for choosing which page you want to set as the home
   * or main page.  Actually sets the main page in the database and in this PHPWS_PageMaster class.
   *
   * @access public
   */
  function set_main_page () {
    $bg = NULL;

    if(isset($_POST["PAGE_id"]) && is_numeric($_POST["PAGE_id"])) {
      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_pagemaster_pages SET mainpage='0'";
      $GLOBALS['core']->query($sql);
      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_pagemaster_pages SET mainpage='1' WHERE id='{$_POST['PAGE_id']}'";
      $GLOBALS['core']->query($sql);
      
      $this->homepage = new PHPWS_Page($_POST["PAGE_id"]);
    }

    $content = "<h3>" . $_SESSION["translate"]->it("Set Main Page") . "</h3>"; 
    $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages", "new_page", 0);

    if($result) {
      $content .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\"><tr>
        <td width=\"80%\" class=\"bg_medium\"><b><i>" . $_SESSION["translate"]->it("Title") .
	"</i></b></td><td align=\"center\" width=\"20%\" class=\"bg_medium\"><b><i>" .
	$_SESSION["translate"]->it("Mainpage") . "</i></b></td></tr>";

      $myelements[0] = PHPWS_Form::formHidden("MASTER_op", "set_main");
      $myelements[0] .= PHPWS_Form::formHidden("module", "pagemaster");

      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "set_mainpage")) {
	$myelements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Select"), "select");
      }

      foreach($result as $value) {
	$content .= "<tr><td width=\"80%\"$bg>" . $value["title"] . "</td>";
	$myelements[1] = PHPWS_Form::formHidden("PAGE_id", $value["id"]);
	$content .= "<td align=\"center\" width=\"20%\"$bg>";

	if($value["mainpage"]) {
	  $content .= "<span style=\"color:lime;\"><b><i>" . $_SESSION["translate"]->it("CURRENT") .
	    "</i></b></span>";
	} else {
	  $content .= PHPWS_Form::makeForm("MASTER_set_main_page", "index.php", $myelements, "post", 0, 0);
	}

	$content .= "</td></tr>";
	PHPWS_WizardBag::toggle($bg, " class=\"bg_medium\"");
      }
      $content .= "</table>";
    } else {
      $content .= $_SESSION["translate"]->it("No pages found!");
    }

    $GLOBALS["CNT_pagemaster"]["content"] .= $content;
  }// END FUNC set_main_page()

  /**
   * Simply displays the homepage.
   *
   * @access public
   */
  function show_mainpage () {
    if(isset($this->homepage)) {
      $_SESSION["SES_PM_page"] = $this->homepage;
      $_SESSION["SES_PM_page"]->view_page();
    }
  }// END FUNC show_mainpage()

  /**
   * Function used by search module to search pages
   *
   * @access public
   */
  function search($where) {
    $resultArray = array();

    $sql = "SELECT * FROM {$GLOBALS['core']->tbl_prefix}mod_pagemaster_sections $where";
    $result = $GLOBALS["core"]->getAll($sql);

    if(!DB::isError($result) && is_array($result) && sizeof($result) > 0) {
      $pages = array();
      $text = array();
      foreach($result as $row) {
	$pages[] = "id='" . $row["page_id"] . "'";
	$text[$row["page_id"]] = $row["text"];
      }
      $pages = array_unique($pages);
      $pages = implode(" OR ", $pages);

      $sql = "SELECT id, title FROM {$GLOBALS['core']->tbl_prefix}mod_pagemaster_pages WHERE $pages";
      $result = $GLOBALS["core"]->getAll($sql);
      if(!DB::isError($result) && is_array($result) && sizeof($result) > 0) {
	foreach($result as $row) {
	  if(isset($text[$row["id"]])) {
	    $resultArray[$row["id"]] = "<b>" . $row["title"] . "</b><br />" . $text[$row["id"]];
	  }
	}
      }
    }

    return $resultArray;
  }// END FUNC search

}// END CLASS PHPWS_PageMaster

?>