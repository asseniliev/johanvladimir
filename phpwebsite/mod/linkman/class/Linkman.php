<?php

require_once (PHPWS_SOURCE_DIR . "core/List.php");
require_once (PHPWS_SOURCE_DIR . "core/Error.php");
require_once (PHPWS_SOURCE_DIR . "core/Form.php");
require_once (PHPWS_SOURCE_DIR . "core/Array.php");
require_once (PHPWS_SOURCE_DIR . "core/Text.php");
require_once (PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once(PHPWS_SOURCE_DIR . "mod/linkman/class/Link.php");
require_once(PHPWS_SOURCE_DIR . "mod/fatcat/class/CategoryView.php");
require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * Link Manager module master class
 *
 * @version $Id: Linkman.php,v 1.30 2004/11/16 18:27:36 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Link Manager
 */
class PHPWS_Linkman {

  var $_list;
  /**
   * Current link being edited
   *
   * @var    object
   * @access public
   */
  var $currentLink;

  /**
   * Constructor for the linkman class
   *
   * Builds array of links in the database and grabs settings
   */
  function PHPWS_Linkman() {
    $this->currentLink = NULL;
  }
 
  /**
   * admin menu
   */
  function adminMenu() {
    $links = array();

    if($_SESSION['OBJ_user']->allow_access("linkman", "add_link")) {
      $links[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=adminMenuAction&amp;LMN_addLink=1\">".$_SESSION['translate']->it("New Link")."</a>";
    }
    $links[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=adminMenuAction&amp;LMN_listLinks=1\">".$_SESSION['translate']->it("List Links")."</a>";

    $tags = array();
    $tags['LINKS'] = implode("&#160;|&#160;", $links);

    $GLOBALS['CNT_linkman']['content'] = PHPWS_Template::processTemplate($tags, "linkman", "adminMenu.tpl");
  }

  /**
   * admin menu action
   */
  function adminMenuAction() {
    if($_SESSION['OBJ_user']->allow_access("linkman")) {
      $this->adminMenu();
      
      if(isset($_REQUEST['LMN_addLink']) && $_SESSION['OBJ_user']->allow_access("linkman", "add_link")) {
	$title = $_SESSION['translate']->it("Add A Link");
	$this->currentLink = new PHPWS_Link;
	$content = $this->currentLink->link("add");
	$GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";
      } else {
	$GLOBALS['CNT_linkman']['content'] .= $this->listLinks();
      }
    } else {
      $message = $_SESSION['translate']->it("You do not have access to administrate links.");
      $error = new PHPWS_Error("linkman", "PHPWS_Linkman::adminMenuAction()", $message, "continue", 0);
      $error->message("CNT_linkman", $_SESSION['translate']->it("Access Denied"));
    }
  }

  function listLinks() {
    $listTags = array();
    $listTags['TITLE'] = $_SESSION["translate"]->it("Current links")."&#160;".CLS_help::show_link("linkman", "main");
    $listTags['TITLE_LABEL'] = $_SESSION["translate"]->it("Title");
    $listTags['URL_LABEL'] = $_SESSION["translate"]->it("URL");
    $listTags['HITS_LABEL'] = $_SESSION["translate"]->it("Visits");
    $listTags['ACTIONS_LABEL'] = $_SESSION["translate"]->it("Actions");

    if(!isset($this->_list)) {
      $this->_list = new PHPWS_List;
    }

    $this->_list->setModule("linkman");
    $this->_list->setClass("PHPWS_Link");
    $this->_list->setTable("mod_linkman_links");
    $this->_list->setDbColumns(array("active", "title", "url", "description", "hits"));
    $this->_list->setListColumns(array("Title", "Url", "Description", "Hits", "Actions"));
    $this->_list->setName("list");
    $this->_list->setTemplate("list");
    $this->_list->setOp("LMN_op=linkListAction");
    $this->_list->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
    $this->_list->setExtraListTags($listTags);
    $this->_list->setExtraRowTags(array("COLSPAN"=>"3"));

    return $this->_list->getList();
  }

  function linkListAction() {
    if($_SESSION['OBJ_user']->allow_access("linkman")) {
      $this->adminMenu();
      
      if(isset($_REQUEST['LMN_id'])) {
	$this->currentLink = new PHPWS_Link($_REQUEST['LMN_id']);
      }

      $paging = TRUE;
      if(isset($_REQUEST['LMN_editLink'])) {
	$title = $_SESSION['translate']->it("Edit A Link");
	$content = $this->currentLink->link("edit");
	$GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";
      } else if(isset($_REQUEST['LMN_deleteLink'])) {
	$this->currentLink->deleteLink();
	if(!isset($_REQUEST['LMN_yes']) && !isset($_REQUEST['LMN_no'])) {
	  return;
	}
      } else if(isset($_REQUEST['LMN_setActivity'])) {
	$this->currentLink->setActivity();
	$paging = FALSE;
      }
            
      $GLOBALS['CNT_linkman']['content'] .= $this->listLinks($paging);
    } else {
      $message = $_SESSION['translate']->it("You do not have access to administrate links.");
      $error = new PHPWS_Error("linkman", "PHPWS_Linkman::linkListAction()", $message, "continue", 0);
      $error->message("CNT_linkman", $_SESSION['translate']->it("Access Denied"));
    }
  }

  function visitLink() {
    if(isset($_REQUEST['LMN_id'])) {
      $this->currentLink = new PHPWS_Link($_REQUEST['LMN_id']); 
      $id = $this->currentLink->id;
      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_linkman_links SET hits=hits+1 WHERE id='$id'";
      $GLOBALS['core']->query($sql);
      $header = $this->currentLink->url;
      header("Location: $header");
      exit();
    } else {
      exit("No ID passed to PHPWS_Linkman::visitLink");
    }
  }

  /**
   * user menu
   */
  function userMenu() {
    $links = array();

    $links[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction\">".$_SESSION['translate']->it("Categories")."</a>";
    $links[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction&amp;LMN_topLinks=1\">".$_SESSION['translate']->it("Top Ten")."</a>";
    $links[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction&amp;LMN_recent=1\">".$_SESSION['translate']->it("Most Recent")."</a>";
    $links[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction&amp;LMN_submitLink=1\">".$_SESSION['translate']->it("Submit Link")."</a>";

    $tags['LINKS'] = implode("&#160;|&#160;", $links);

    $GLOBALS['CNT_linkman']['content'] = PHPWS_Template::processTemplate($tags, "linkman", "userMenu.tpl");
  }

  /**
   * user menu action
   */
  function userMenuAction() {
    $this->userMenu();

    if(isset($_REQUEST['LMN_topLinks'])) {
      $title = $_SESSION['translate']->it("Top Ten Links");
      $content = $this->userList("top");
    } else if(isset($_REQUEST['LMN_recent'])) {
      $title = $_SESSION['translate']->it("Recent Links");
      $content = $this->userList("recent");
    } elseif(isset($_REQUEST['LMN_submitLink'])){
      $title = $_SESSION['translate']->it("Submit A Link");
      $this->currentLink = new PHPWS_Link;
      $content = $this->currentLink->link("user");
    } else {
      $categoryView = new CategoryView;
      $categoryView->setModule("linkman");
      $categoryView->setOp("LMN_op=userMenuAction");
      if(!isset($_REQUEST["category"])) {
	$content = $categoryView->categoriesMainListing();
      } else {
	$content = $categoryView->categoriesSCView();
      }
    }

    $GLOBALS['CNT_linkman']['content'] .= $content;
  }

  function userList($mode) {
    $listTags = array();
    $listTags['TITLE_LABEL'] = $_SESSION["translate"]->it("Title");
    $listTags['URL_LABEL'] = $_SESSION["translate"]->it("URL");

    if(!isset($this->_list)) {
      $this->_list = new PHPWS_List;

      $this->_list->setModule("linkman");
      $this->_list->setClass("PHPWS_Link");
      $this->_list->setTable("mod_linkman_links");
    }

    $this->_list->setPaging(array());

    if($mode == "top") {
      $listTags['TITLE'] = $_SESSION["translate"]->it("Top ten links");
      $listTags['HITS_LABEL'] = $_SESSION["translate"]->it("Visits");
      $this->_list->setDbColumns(array("active", "title", "url", "description", "hits"));
      $this->_list->setListColumns(array("Title", "Description", "Url", "Hits"));
      $this->_list->setExtraRowTags(array("COLSPAN"=>"3"));
      $this->_list->setOrder("hits DESC LIMIT 10");

    } else if($mode == "recent") {
      $listTags['TITLE'] = $_SESSION["translate"]->it("Most recent links");
      $listTags['DATEPOSTED_LABEL'] = $_SESSION["translate"]->it("Posted");
      $this->_list->setDbColumns(array("active", "title", "description", "url", "dateposted"));
      $this->_list->setListColumns(array("Title", "Description", "Url", "DatePosted"));
      $this->_list->setExtraRowTags(array("COLSPAN"=>"3"));
      $this->_list->setOrder("dateposted DESC LIMIT 10");
    }

    $this->_list->setOp("LMN_op=userMenuAction");
    $this->_list->setWhere("active = 1");
    $this->_list->setName("categories");
    $this->_list->setTemplate("categories");
    $this->_list->setExtraListTags($listTags);

    return $this->_list->getList();
  }

  function search($where) {
    $sql = "SELECT id, title FROM {$GLOBALS['core']->tbl_prefix}mod_linkman_links " . $where . " AND (new='0' AND active='1')";
    $linkResult = $GLOBALS['core']->query($sql);
    $results = array();

    if($linkResult->numrows()) {
      while($link = $linkResult->fetchrow(DB_FETCHMODE_ASSOC)) {
	$results[$link['id']] = $link['title'];
      }
    }

    return $results;
  }
}

?>
