<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: DocumentManager.php,v 1.16 2005/05/23 12:53:19 darren Exp $
 */

require_once(PHPWS_SOURCE_DIR.'core/List.php');
require_once(PHPWS_SOURCE_DIR.'core/WizardBag.php');

require_once(PHPWS_SOURCE_DIR.'mod/fatcat/class/CategoryView.php');
require_once(PHPWS_SOURCE_DIR.'mod/documents/class/Document.php');

class JAS_DocumentManager {

  var $document = NULL;
  var $sort = NULL;
  var $settings = array();
  var $categoryLink = NULL;
  var $unapproved = NULL;
  var $lists = array();

  function JAS_DocumentManager() { 
    $sql = "SELECT * FROM {$GLOBALS['core']->tbl_prefix}mod_documents_settings";
    $this->settings = $GLOBALS['core']->quickFetch($sql);
  }

  function _menu() {
    $settingsText = $_SESSION['translate']->it("Settings");
    $newText = $_SESSION['translate']->it('New Document');
    $listText = $_SESSION['translate']->it('List Documents');
    $categoryText = $_SESSION['translate']->it("Categories");
    $unapprovedText = $_SESSION['translate']->it("Unapproved");

    $links = array();

    if($_SESSION['OBJ_user']->allow_access("documents", "add_document")) {
      $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=newDocument\">$newText</a>";
    }

    $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=categories\">$categoryText</a>";
    $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=list\">$listText</a>";

    if($_SESSION['OBJ_user']->allow_access("documents", "approve_document")) {
      $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=list&amp;JAS_Document_unapproved=1\">$unapprovedText</a>";
    }

    if($_SESSION['OBJ_user']->allow_access("documents", "edit_settings")) {
      $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=settings\">$settingsText</a>";
    }

    $tags = array();
    $tags['LINKS'] = implode("&#160;|&#160;", $links);

    $GLOBALS['CNT_documents']['content'] = PHPWS_Template::processTemplate($tags, "documents", "menu.tpl");
  }

  function _list() {
    $this->_menu();
    $this->categoryLink = NULL;

    $tags = array();
    if(isset($this->message)) {
      $tags['MESSAGE'] = $this->message;
      $this->message = NULL;
    }

    if($_REQUEST['JAS_DocumentManager_op'] == "list") {
      $this->sort = NULL;
    }

    $tags['LISTS'] = array();
    
    if($_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $listTags = array();
      $listTags['TITLE'] = $_SESSION['translate']->it('Current Documents');
      $listTags['ID_LABEL'] = $_SESSION['translate']->it("ID");
      $listTags['LABEL_LABEL'] = $_SESSION['translate']->it("Name");
      $listTags['UPDATED_LABEL'] = $_SESSION['translate']->it("Updated");
      $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it("Actions");

      if(!isset($this->lists['admin'])) {
	$this->lists['admin'] = new PHPWS_List;
      }

      $this->lists['admin']->setModule("documents");
      $this->lists['admin']->setClass("JAS_Document");
      $this->lists['admin']->setTable("mod_documents_docs");
      $this->lists['admin']->setDbColumns(array("hidden", "updated", "label", "description", "approved"));
      $this->lists['admin']->setListColumns(array("Updated", "Label", "Description", "Actions"));
      $this->lists['admin']->setName("admin");
      $this->lists['admin']->setTemplate("list");
      $this->lists['admin']->setOp("JAS_DocumentManager_op=list");
      $this->lists['admin']->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
      $this->lists['admin']->setExtraListTags($listTags);
      $this->lists['admin']->setWhere("approved='1'");

      $tags['LISTS'][] = $this->lists['admin']->getList();
    } else {
      $listTags = array();
      $listTags['TITLE'] = $_SESSION['translate']->it('Current Documents');
      $listTags['LABEL_LABEL'] = $_SESSION['translate']->it("Name");
      $listTags['UPDATED_LABEL'] = $_SESSION['translate']->it("Updated");
      $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it("Actions");

      if(!isset($this->lists['list'])) {
	$this->lists['list'] = new PHPWS_List;
      }

      $this->lists['list']->setModule("documents");
      $this->lists['list']->setClass("JAS_Document");
      $this->lists['list']->setTable("mod_documents_docs");
      $this->lists['list']->setDbColumns(array("hidden", "updated", "label", "description", "approved"));
      $this->lists['list']->setListColumns(array("Updated", "Label", "Description", "Actions"));
      $this->lists['list']->setName("list");
      $this->lists['list']->setOp("JAS_DocumentManager_op=list");
      $this->lists['list']->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
      $this->lists['list']->setExtraListTags($listTags);
      $this->lists['list']->setWhere("approved='1' AND hidden='0'");

      $tags['LISTS'][] = $this->lists['list']->getList();
    }
    
    if(isset($_REQUEST['JAS_Document_unapproved'])) {
      PHPWS_WizardBag::toggle($this->unapproved, 1);
    }
    if($_SESSION['OBJ_user']->allow_access("documents", "approve_document") && $this->unapproved) { 
      $listTags = array();
      $listTags['TITLE'] = $_SESSION['translate']->it('Unapproved Documents');
      $listTags['LABEL_LABEL'] = $_SESSION['translate']->it("Name");
      $listTags['UPDATED_LABEL'] = $_SESSION['translate']->it("Updated");
      $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it("Actions");
      
      if(!isset($this->lists['unapproved'])) {
	$this->lists['unapproved'] = new PHPWS_List;
      }

      $this->lists['unapproved']->setModule("documents");
      $this->lists['unapproved']->setClass("JAS_Document");
      $this->lists['unapproved']->setTable("mod_documents_docs");
      $this->lists['unapproved']->setDbColumns(array("hidden", "updated", "label", "description", "approved"));
      $this->lists['unapproved']->setListColumns(array("Updated", "Label", "Description", "Actions"));
      $this->lists['unapproved']->setName("unapproved");
      $this->lists['unapproved']->setTemplate("list");
      $this->lists['unapproved']->setOp("JAS_DocumentManager_op=list");
      $this->lists['unapproved']->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
      $this->lists['unapproved']->setExtraListTags($listTags);
      $this->lists['unapproved']->setWhere("approved='0' AND hidden='0'");

      $tags['LISTS'][] = $this->lists['unapproved']->getList();
    }
    $tags['LISTS'] = implode("<hr />\n", $tags['LISTS']);
    
    $GLOBALS['CNT_documents']['title'] = $_SESSION['translate']->it('Documents');
    $GLOBALS['CNT_documents']['content'] .= PHPWS_Template::processTemplate($tags, "documents", "documents.tpl");
  }

  function _settings() {
    if(!$_SESSION['OBJ_user']->allow_access("documents", "edit_settings")) {
      $this->message = $_SESSION['translate']->it("You do not have permission to [var1]", $_SESSION['translate']->it("edit settings"));
      $this->_list();
      return;
    }

    $listText = $_SESSION['translate']->it('All Documents');

    if($_REQUEST['JAS_DocumentManager_op'] == "save") {
      if(isset($_REQUEST['JAS_User_view'])) {
	$this->settings['userview'] = 1;
      } else {
	$this->settings['userview'] = 0;
      }
      if(isset($_REQUEST['JAS_User_download'])) {
	$this->settings['userdownload'] = 1;
      } else {
	$this->settings['userdownload'] = 0;
      }
      if(isset($_REQUEST['JAS_Show_block'])) {
	$this->settings['showblock'] = 1;
      } else {
	$this->settings['showblock'] = 0;
      }
      if(isset($_REQUEST['JAS_Doc_approval'])) {
	$this->settings['approval'] = 1;
      } else {
	$this->settings['approval'] = 0;
      }
    }

    $form = new EZform("JAS_DocumentManager_settings");
    $form->add("module", "hidden");
    $form->setValue("module", "documents");
    $form->add("JAS_DocumentManager_op", "hidden");
    $form->setValue("JAS_DocumentManager_op", "save");

    $form->add("JAS_User_view", "checkbox");
    $form->setValue("JAS_User_view", 1);
    $form->setMatch("JAS_User_view", $this->settings['userview']);
    $form->add("JAS_User_download", "checkbox");
    $form->setValue("JAS_User_download", 1);
    $form->setMatch("JAS_User_download", $this->settings['userdownload']);
    $form->add("JAS_Show_block", "checkbox");
    $form->setValue("JAS_Show_block", 1);
    $form->setMatch("JAS_Show_block", $this->settings['showblock']);
    $form->add("JAS_Doc_approval", "checkbox");
    $form->setValue("JAS_Doc_approval", 1);
    $form->setMatch("JAS_Doc_approval", $this->settings['approval']);

    $form->add("JAS_Submit", "submit");
    $form->setValue("JAS_Submit", $_SESSION['translate']->it("Save"));  

    $formTags = array();
    $formTags = $form->getTemplate();

    if($_REQUEST['JAS_DocumentManager_op'] == "save") {
      if($GLOBALS['core']->sqlUpdate($this->settings, "mod_documents_settings")) {
	$formTags['MESSAGE'] = $_SESSION['translate']->it("Settings saved successfully");
      } else {
	$formTags['MESSAGE'] = $_SESSION['translate']->it("There was a problem saving to the database");
      }
    }

    $links = array();
    $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=list\">$listText</a>";    

    $formTags['LINKS'] = implode("&#160;|&#160;", $links);    
    $formTags['JAS_USER_VIEW_TEXT'] = $_SESSION['translate']->it("Anonymous users can [var1]", $_SESSION['translate']->it("view"));
    $formTags['JAS_USER_DOWNLOAD_TEXT'] = $_SESSION['translate']->it("Anonymous users can [var1]", $_SESSION['translate']->it("download"));
    $formTags['JAS_SHOW_BLOCK_TEXT'] = $_SESSION['translate']->it("Show most recent block");
    $formTags['JAS_DOC_APPROVAL_TEXT'] = $_SESSION['translate']->it('Documents must be approved');

    return PHPWS_Template::processTemplate($formTags, "documents", "settings.tpl");
  }

  function _new() {
    if($_SESSION['OBJ_user']->allow_access("documents", "add_document")) {
      $this->document = new JAS_Document;
      $_REQUEST['JAS_Document_op'] = "edit";
    } else {
      $this->message = $_SESSION['translate']->it('You do not have permission to [var1] documents', $_SESSION['translate']->it("add"));
      $this->_list();
    }
  }

  function _view() {
    if((is_numeric($_REQUEST['JAS_Document_id']) && $_SESSION['OBJ_user']->allow_access("documents", "view_document"))
	|| ($_SESSION["OBJ_user"]->isUser() == true && !JAS_USE_USER_RIGHTS) || $this->settings['userview']) {
      $this->document = new JAS_Document($_REQUEST['JAS_Document_id']);
      $_REQUEST['JAS_Document_op'] = "view";
    } else {
      $this->message = $_SESSION['translate']->it('You do not have permission to [var1] documents', $_SESSION['translate']->it("view"));
      $this->_list();
    }
  }

  function _edit() {
    if(is_numeric($_REQUEST['JAS_Document_id']) && $_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $this->document = new JAS_Document($_REQUEST['JAS_Document_id']);
      $_REQUEST['JAS_Document_op'] = "edit";
    } else {
      $this->message = $_SESSION['translate']->it('You do not have permission to [var1] documents', $_SESSION['translate']->it("edit"));
      $this->_list();
    }
  }

  function _delete() {
    if(is_numeric($_REQUEST['JAS_Document_id']) && $_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $this->document = new JAS_Document($_REQUEST['JAS_Document_id']);
      $_REQUEST['JAS_Document_op'] = "delete";      
      $this->document->action();
      return;
    } else {
      $this->_list();
    }
  }

  function _visibility() {
    if(is_numeric($_REQUEST['JAS_Document_id']) && $_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $this->document = new JAS_Document($_REQUEST['JAS_Document_id']);
      if($this->document->_hidden) {
	$this->document->setHidden(FALSE);
	PHPWS_Fatcat::activate($this->document->getId(), "documents");
      } else {
	$this->document->setHidden(TRUE);
	PHPWS_Fatcat::deactivate($this->document->getId(), "documents");
      }
      $this->document->commit();
      $this->document = NULL;
    }

    $this->_list();
  }

  function _approve() {
    if(is_numeric($_REQUEST['JAS_Document_id']) && $_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $this->document = new JAS_Document($_REQUEST['JAS_Document_id']);
      $this->document->setApproved(TRUE);
      $this->document->commit();
      $this->document = NULL;
    }

    $this->_list();
  }

  function _categories() {
    $this->_menu();

    $categoryView = new CategoryView;
    $categoryView->setModule("documents");
    $categoryView->setOp("JAS_DocumentManager_op=categories");
    if(!isset($_REQUEST["category"])) {
      $content = $categoryView->categoriesMainListing();
    } else {
      $this->categoryLink = "./index.php?module=documents&amp;JAS_DocumentManager_op=categories&amp;category=".$_REQUEST["category"];
      $content = $categoryView->categoriesSCView();
    }

    $GLOBALS["CNT_documents"]["content"] .= $content;
  }

  function search($where) {
    $sql = "SELECT id, label FROM {$GLOBALS['core']->tbl_prefix}mod_documents_docs $where";
    $result = $GLOBALS["core"]->query($sql);

    if($result) {
      $array = array();
      while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	$array[$row["id"]] = $row["label"];
      }
    }
    return $array;
  }

  function _download() {
    if((is_numeric($_REQUEST['JAS_File_id']) && $_SESSION['OBJ_user']->allow_access("documents", "view_document"))
       || ($_SESSION["OBJ_user"]->isUser() == true && !JAS_USE_USER_RIGHTS) || $this->settings['userview']) {
      if(!isset($this->document))	
        $this->document = new JAS_Document();

      $_REQUEST['JAS_Document_op'] = "downloadFile";
      $this->document->action();
      return;
    } else {
      $this->message = $_SESSION['translate']->it('You do not have permission to [var1] documents', $_SESSION['translate']->it("view"));
      $this->_list();
    }
  }


  function action() {
    $content = array();

    switch($_REQUEST['JAS_DocumentManager_op']) {
    case "list":
      $this->_list();
      break;

    case "settings":
      $title = $_SESSION['translate']->it('Documents Settings');
      $content[] = $this->_settings();
      break;

    case "save":
      $title = $_SESSION['translate']->it('Documents Settings');
      $content[] = $this->_settings();
      break;

    case "newDocument":
      $this->_new();
      break;

    case "viewDocument":
      $this->_view();
      break;

    case "editDocument":
      $this->_edit();
      break;

    case "deleteDocument":
      $this->_delete();
      break;

    case "setVisibility":
      $this->_visibility();
      break;

    case "approveDocument":
      $this->_approve();
      break;
      
    case "categories":
      $this->_categories();
      break;

    case "downloadFile":
      $this->_download();
      break;      
    }

    if(is_array($content) && (sizeof($content))) {
      $GLOBALS['CNT_documents']['title'] = $title;
      $GLOBALS['CNT_documents']['content'] = implode("<br />\n", $content);
    }
  }
}

?>
