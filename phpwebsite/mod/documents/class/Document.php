<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: Document.php,v 1.24 2005/05/18 21:07:49 darren Exp $
 */

require_once(PHPWS_SOURCE_DIR.'core/Item.php');
require_once(PHPWS_SOURCE_DIR.'core/List.php');
require_once(PHPWS_SOURCE_DIR.'core/Error.php');

require_once(PHPWS_SOURCE_DIR.'mod/documents/class/Files.php');

class JAS_Document extends PHPWS_Item {

  var $extras = array();
  var $files = NULL;
  var $message = NULL;
  var $list = NULL;

  function JAS_Document($ID=NULL) {
    $this->setTable("mod_documents_docs");
    $this->addExclude(array("extras", "files", "message", "list"));

    if(is_numeric($ID)) {
      $error = $this->setId($ID);
      if(PHPWS_Error::isError($error)) {
	return $error;
      }

      $this->extras = $this->init();
    } else {
      if(is_array($ID) && (sizeof($ID) > 0)) {
	$this->extras = $this->setVars($ID);
      }
    }
  }

  function _view() {
    require_once(PHPWS_SOURCE_DIR.'mod/documents/conf/form.php');

    $content = $links = $FormTags = array();

    $listText = $_SESSION['translate']->it('List Documents');
    $editText = $_SESSION['translate']->it("Edit");
    $printText = $_SESSION['translate']->it("Print");
    $backText  = $_SESSION['translate']->it("Back to Category Listing");

    if(!isset($_REQUEST['lay_quiet'])) {
      $links = array();

      if(isset($_SESSION['JAS_DocumentManager']->categoryLink))
	$links[] = "<a href=\"./".$_SESSION['JAS_DocumentManager']->categoryLink."\">$backText</a>";

      $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=list\">$listText</a>";
      if($_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
	$links[]=  "<a href=\"./index.php?module=documents&amp;JAS_Document_op=edit\">$editText</a>";
      }
      $links[]=  "<a href=\"./index.php?module=documents&amp;JAS_Document_op=print\" target=\"_blank\">$printText</a>";

      $content[] = "<div align=\"right\">".implode("&#160;|&#160;", $links)."</div>";
    }
    
    if($_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $FormTags['ID_LABEL'] = $_SESSION['translate']->it("ID");
      $FormTags['ID'] = $this->getId();

      $FormTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
      $FormTags['NAME'] = PHPWS_Text::parseOutput($this->getLabel());      
    }

    /* begin custom view */
    foreach($elementNames as $key=>$value) {
      if(strlen($this->extras[$value]) > 0) {
	$FormTags[strtoupper($elementNames[$key])] = PHPWS_Text::parseOutput($this->extras[$value]);
	if($this->extras[$value])
	  $FormTags[strtoupper($elementNames[$key]).'_LABEL'] = $elementLabels[$key];
      }
    }

    $tpl_form_layout = new HTML_Template_IT(PHPWS_SOURCE_DIR.'mod/documents/templates');
    $tpl_form_layout->loadTemplateFile('forms/form_layout.tpl');
    $tpl_form_layout->setVariable($FormTags);
    $content[] = $this->_formatTemplate($tpl_form_layout->get());
    /* end custom view */ 

    if(($_SESSION['OBJ_user']->allow_access("documents", "download_file")
	|| $_SESSION['JAS_DocumentManager']->settings['userdownload']) && (!isset($_REQUEST['lay_quiet']))
        || ($_SESSION["OBJ_user"]->isUser() && !JAS_USE_USER_RIGHTS)) {
      $doc = $_SESSION['JAS_DocumentManager']->document->getId();
      
      $listTags = array();
      $listTags['TITLE'] = $_SESSION['translate']->it("Current Files");
      $listTags['ID_LABEL'] = $_SESSION['translate']->it("ID");
      $listTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
      $listTags['SIZE_LABEL'] = $_SESSION['translate']->it("Size");
      $listTags['CREATED_LABEL'] = $_SESSION['translate']->it("Uploaded");
      $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it("Actions");

      if(!isset($this->list)) {
	$this->list = new PHPWS_List;
      }

      $this->list->setModule("documents");
      $this->list->setClass("JAS_File");
      $this->list->setTable("mod_documents_files");
      $this->list->setDbColumns(array("name", "size", "created"));
      $this->list->setListColumns(array("Name", "Size", "Created", "Actions"));
      $this->list->setName("files");
      $this->list->setOp("JAS_Document_op=view");
      $this->list->setExtraListTags($listTags);
      $this->list->setWhere("doc='$doc'");

      $content[] = $this->list->getList();
    }
    
    return implode("", $content);
  }

  function _edit() {
    require_once(PHPWS_SOURCE_DIR.'mod/documents/conf/form.php');

    $tags = $FormTags = array();
 
    $submitText = $_SESSION['translate']->it("Save");
    $addText = $_SESSION['translate']->it("Add Files");
    $fileListTitle = $_SESSION['translate']->it("Files");

    if(is_array($this->files->messages) && sizeof($this->files->messages)) { 
      $tags['MESSAGE'] = array();
      $tags['MESSAGE'][] = $_SESSION['translate']->it("File upload messages") . "<br />";
      foreach($this->files->messages as $key => $value) {
	$tags['MESSAGE'][] = "<b>" . ($key + 1) . "</b>&#160;:&#160;$value<br />";
      }
      
      $this->files->messages = array();  
      $tags['MESSAGE'] = implode("", $tags['MESSAGE']);
    } else if($this->message) {
      $tags['MESSAGE'] = $this->message;
      $this->message = NULL;
    }

    $id = $this->getId();
    $links[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=list\">".$_SESSION['translate']->it('List Documents')."</a>";

    if(isset($id)) {
      if($_SESSION['OBJ_user']->allow_access("documents", "view_document")) {
	$links[]=  "<a href=\"./index.php?module=documents&amp;JAS_Document_op=view\">".$_SESSION['translate']->it("View")."</a>";
      }
      if($_SESSION['OBJ_user']->allow_access("documents", "delete_document")) {
	$links[] = "<a href=\"./index.php?module=documents&amp;JAS_Document_op=delete\">".$_SESSION['translate']->it("Delete")."</a>";
      }
      
      $FormTags['ID_LABEL'] = $_SESSION['translate']->it("ID");
      $FormTags['ID'] = $id;
    }
    $tags['LINKS'] = implode("&#160;|&#160;", $links);
    
    /* begin custom form */
    $label = $this->getLabel();
    $FormTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
    $FormTags['NAME'] = "<input type=\"textfield\" name=\"JAS_Document_label\" value=\"$label\" size=\"33\" maxlength=\"255\" />";
    if(isset($_SESSION['OBJ_fatcat'])) {
      $FormTags['CATEGORY_LABEL'] = $_SESSION['translate']->it("Category");
      $FormTags['CATEGORY'] = $_SESSION['OBJ_fatcat']->showSelect($this->getId());
    }

    $tags['NAME'] = $formName; 
    foreach($formAttributes as $attribute=>$value) {
      $tags[strtoupper($attribute)] = $value;
    }

    $tags['ELEMENTS'] = array();
    foreach($elements as $key) {
      $tpl = new HTML_Template_IT(PHPWS_SOURCE_DIR.'mod/documents/templates');
      $elementTags = array();
      $FormTags[str_replace(" ", "_",strtoupper($elementNames[$key]).'_LABEL')] = $elementLabels[$key];

      $elementTags['TYPE'] = $elementTypes[$key];
      $elementTags['NAME'] = $elementNames[$key];
      if(isset($this->extras[$databaseColumns[$key]])) {
	$elementTags['VALUE'] = $this->extras[$databaseColumns[$key]];
      }
      foreach($elementAttributes[$key] as $attribute=>$value) {
	$elementTags[strtoupper($attribute)] = $value;
      }

      $tpl->loadTemplateFile(implode("/", array("elements", $elementTemplates[$key])));
      $tpl->setVariable($elementTags);
      $tags['ELEMENTS'][] = str_replace("\n", "", $tpl->get());
    }

    $tpl_form_layout = new HTML_Template_IT(PHPWS_SOURCE_DIR.'mod/documents/templates');
    $tpl_form_layout->loadTemplateFile('forms/form_layout.tpl');

    foreach($elementNames as $key=>$value) {
      $FormTags[strtoupper($value)] = $tags['ELEMENTS'][$key];
    }

    $tpl_form_layout->setVariable($FormTags);
    $tags['ELEMENTS'] = str_replace("\n", "", $tpl_form_layout->get());
    /* end custom form */

    if(isset($id) && $_SESSION['OBJ_user']->allow_access("documents", "update_files")) {
      $doc = $_SESSION['JAS_DocumentManager']->document->getId();

      $listTags = array();
      $listTags['TITLE'] = $_SESSION['translate']->it("Current Files");
      $listTags['ID_LABEL'] = $_SESSION['translate']->it("ID");
      $listTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
      $listTags['SIZE_LABEL'] = $_SESSION['translate']->it("Size");
      $listTags['CREATED_LABEL'] = $_SESSION['translate']->it("Uploaded");
      $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it("Actions");

      if(!isset($this->list)) {
	$this->list = new PHPWS_List;
      }

      $this->list->setModule("documents");
      $this->list->setClass("JAS_File");
      $this->list->setTable("mod_documents_files");
      $this->list->setDbColumns(array("name", "size", "created"));
      $this->list->setListColumns(array("Name", "Size", "Created", "Actions"));
      $this->list->setName("files");
      $this->list->setOp("JAS_Document_op=edit");
      $this->list->setExtraListTags($listTags);
      $this->list->setWhere("doc='$doc'");

      $tags['FILES'] = $this->list->getList();
      $tags['ADD'] = "<input type=\"submit\" name=\"JAS_Document_add\" value=\"$addText\" />";
    }

    $tags['HIDDENS'] = array();
    $tags['HIDDENS'][] = "<input type=\"hidden\" name=\"module\" value=\"documents\" />";
    $tags['HIDDENS'][] = "<input type=\"hidden\" name=\"JAS_Document_op\" value=\"save\" />";
    $tags['HIDDENS'] = implode("\n", $tags['HIDDENS']);
    $tags['SUBMIT'] = "<input type=\"submit\" value=\"$submitText\" />";

    $tpl = new HTML_Template_IT(PHPWS_SOURCE_DIR.'mod/documents/templates');
    $tpl->loadTemplateFile('forms/form.tpl');
    $tpl->setVariable($tags);

    return $this->_formatTemplate($tpl->get());
  }

  function _save() {
    if(isset($_REQUEST['JAS_Document_add'])) {
      $this->files = new JAS_Files;
      $_REQUEST['JAS_Files_op'] = "addFiles";
      return;
    }

    require_once(PHPWS_SOURCE_DIR.'mod/documents/conf/form.php');


    foreach($elementNames as $key=>$value) {
      $this->extras[$value] = PHPWS_Text::parseInput($_REQUEST[$value]);
    }

    /* begin custom save */
    $result = $this->setLabel($_REQUEST['JAS_Document_label']);
    if(PHPWS_Error::isError($result)) {
      $this->message = $_SESSION['translate']->it("You must enter a name for your document.");
      $_REQUEST['JAS_Document_op'] = "edit";
      $this->action();
      return;
    }
    /* end custom save */

    $id = $this->getId();
    if(!isset($id) && $_SESSION['JAS_DocumentManager']->settings['approval']) {
      $this->setApproved(FALSE);
    }
    
    $error = $this->commit($this->extras);
    if(PHPWS_Error::isError($error)) {
      $this->message = $_SESSION['translate']->it("There was a problem saving to the database");
      $_REQUEST['JAS_Document_op'] = "edit";
      $this->action();
      return;
    }

    if(isset($_SESSION['OBJ_fatcat'])) {
      if ($this->isHidden()) {
	$fatActive = FALSE;
      } else {
	$fatActive = TRUE;
      }

      $_SESSION['OBJ_fatcat']->saveSelect($this->getLabel(), "index.php?module=documents&amp;JAS_DocumentManager_op=viewDocument&amp;JAS_Document_id=" . $this->getId(), $this->getId(), NULL, "documents", NULL, NULL, $fatActive);
    }

    $_SESSION['JAS_DocumentManager']->message = $_SESSION['translate']->it("Save successful");
    $_REQUEST['JAS_DocumentManager_op'] = "list";
    $_SESSION['JAS_DocumentManager']->action();
  }

  function _delete() {
    if(!$_SESSION['OBJ_user']->allow_access("documents", "delete_document")) { 
      $_SESSION['JAS_DocumentManager']->message = $_SESSION['translate']->it('You do not have permission to [var1] documents', $_SESSION['translate']->it('delete'));
      $_REQUEST['JAS_DocumentManager_op'] = "list";
      $_SESSION['JAS_DocumentManager']->action();
      return;
    }

    if(isset($_REQUEST['JAS_Document_yes'])) {
      $this->kill();
      PHPWS_Fatcat::purge($this->getId(), "documents");

      $sql = "SELECT name FROM {$GLOBALS['core']->tbl_prefix}mod_documents_files WHERE doc='" . $this->getId() . "'";
      $result = $GLOBALS['core']->getAll($sql);
      $sql = "DELETE FROM {$GLOBALS['core']->tbl_prefix}mod_documents_files WHERE doc='" . $this->getId() . "'";
      $GLOBALS['core']->query($sql);

      foreach($result as $value) {
	@unlink("files/documents/{$value['name']}");
      }

      $message = $_SESSION['translate']->it("The document and all its files were successfully deleted from the database.");
      $_SESSION['JAS_DocumentManager']->message = $message;;
      
      $_REQUEST['JAS_DocumentManager_op'] = "list";
      $_SESSION['JAS_DocumentManager']->action();
      unset($this);

    } else if(isset($_REQUEST['JAS_Document_no'])) {
      $message = $_SESSION['translate']->it("No document was deleted from the database.");
      $this->message = $message;

      $_REQUEST['JAS_Document_op'] = "edit";
      $this->action();

    } else {
      $title = $_SESSION['translate']->it("Delete document confirmation");

      $form = new EZform("JAS_Document_delete");
      $form->add("module", "hidden", "documents");
      $form->add("JAS_Document_op", "hidden", "delete");

      $form->add("JAS_Document_yes", "submit", $_SESSION['translate']->it("Yes"));
      $form->add("JAS_Document_no", "submit", $_SESSION['translate']->it("No"));
      
      $tags = array();
      $tags = $form->getTemplate();
      $tags['MESSAGE'] = $_SESSION['translate']->it("Are you sure you want to delete the document [var1] and all the files associated with it?", "<b><i>" . $this->getLabel() . "</i></b>");
      
      $content = PHPWS_Template::processTemplate($tags, "documents", "delete.tpl");
      $GLOBALS['CNT_documents']['title'] = $title;     
      $GLOBALS['CNT_documents']['content'] = $content;
    }
  }

  function _deleteFile() {
    if($_SESSION['OBJ_user']->allow_access("documents", "update_files")) {
      $id = $_REQUEST['JAS_File_id'];
      $sql = "SELECT name FROM {$GLOBALS['core']->tbl_prefix}mod_documents_files WHERE id='$id'";
      
      $result = $GLOBALS['core']->quickFetch($sql);
      @unlink(PHPWS_HOME_DIR."files/documents/{$result['name']}");

      $sql = "DELETE FROM {$GLOBALS['core']->tbl_prefix}mod_documents_files WHERE id='$id' LIMIT 1";
      $GLOBALS['core']->query($sql);
    }

    $_REQUEST['JAS_Document_op'] = "edit";
    $this->action();
  }

  function _moveFile() {
    if(isset($_REQUEST['DOC_move'])) {
      $_REQUEST['JAS_Document_op'] = "edit";

      $update["doc"] = $_REQUEST["DOC_collections"];
      $match["id"] = $_REQUEST["JAS_File_id"];
      $GLOBALS["core"]->sqlUpdate($update, "mod_documents_files", $match);
      
      $this->action();

    } else if(isset($_REQUEST['DOC_cancel'])) {
      $_REQUEST['JAS_Document_op'] = "edit";      
      $this->action();

    } else {
      $sql = "SELECT name, type FROM {$GLOBALS['core']->tbl_prefix}mod_documents_files WHERE id='".$_REQUEST['JAS_File_id']."'";      
      $result = $GLOBALS['core']->quickFetch($sql);      
      $fileLabel = $result['name'];

      $title = $_SESSION['translate']->it("Move File");

      $sql = "SELECT id, label FROM {$GLOBALS['core']->tbl_prefix}mod_documents_docs";
      
      $result = $GLOBALS['core']->getAssoc($sql);
      $currDoc = $result[$this->getId()];
      unset($result[$this->getId()]);

      $form = new EZform();
      $form->add("module", "hidden", "documents");
      $form->add("JAS_Document_op", "hidden", "moveFile");
      $form->add("LMN_deleteLink", "hidden", 1);
      $form->add("JAS_File_id", "hidden", $_REQUEST['JAS_File_id']);
      
      if(count($result) > 0) {
	$tags["PRE_TITLE"] = $_SESSION['translate']->it("Select a document below that you would like to move the file");
	$tags["FILE_NAME"] = $fileLabel;
	$tags["POST_TITLE"] = $_SESSION["translate"]->it("to");
	$form->add("DOC_collections", "select", $result);
	$form->add("DOC_move", "submit", $_SESSION["translate"]->it("Move"));

      } else {
	$tags["SINGLE_DIRECTORY"] = $_SESSION["translate"]->it("There is only one document.  This feature can only be used if there are other documents to move the file into.");
      }

      $form->add("DOC_cancel", "submit", $_SESSION["translate"]->it("Cancel"));
      $tags = $form->getTemplate(TRUE, TRUE, $tags);

      $tags["CURR_DIRECTORY"] = $_SESSION['translate']->it("The file [var1] is currently in the document [var2].", $fileLabel, $currDoc);

      $content = PHPWS_Template::processTemplate($tags, "documents", "move.tpl");

      $GLOBALS['CNT_documents']['title'] = $title;     
      $GLOBALS['CNT_documents']['content'] = $content;
    }
  }

  function _downloadFile() {
    if($_SESSION['OBJ_user']->allow_access("documents", "download_file")
       || $_SESSION['JAS_DocumentManager']->settings['userdownload'] 
       || ($_SESSION["OBJ_user"]->isUser() && !JAS_USE_USER_RIGHTS)) {
      if(is_numeric($_REQUEST['JAS_File_id'])) {
	$id = $_REQUEST['JAS_File_id'];
	$sql = "SELECT name, size, type FROM {$GLOBALS['core']->tbl_prefix}mod_documents_files WHERE id='$id'";

	$result = $GLOBALS['core']->quickFetch($sql);

	$filename = $GLOBALS['core']->home_dir.JAS_DOCUMENT_DIR.$result['name'];
	
	/*
	require_once 'HTTP/Download.php';
        
        $student = & new CG_Student((int)$student_id);
        
        $id = $_REQUEST['id'];
        $file = new CG_File($id);
        $filePath = $student->getFileDirectory() . $file->getLabel();
        
        $dl = &new HTTP_Download();
        $dl->setFile($filePath);
        $dl->setContentDisposition(HTTP_DOWNLOAD_ATTACHMENT, $file->getLabel());
        $dl->setContentType($file->get("mime_type"));
        $dl->send();
	*/

	header("Content-Type: {$result['type']}");
        header("Content-Length: {$result['size']}");
	header("Content-Description: File Transfer");

	$user_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);

	$saveasname = basename($filename);

	if((is_integer(strpos($user_agent, "msie")))
	   && (is_integer (strpos($user_agent, "win")))) {
            header('Content-Disposition: filename="'.$saveasname.'"');
	} else {
            header('Content-Disposition: attachment; filename="'.$saveasname.'"');
	}
	
	header("Pragma: cache");
	readfile($filename);
	exit();
      }
    } else {
      $_SESSION['JAS_DocumentManager']->message = $_SESSION['translate']->it('You do not have permission to [var1] documents', $_SESSION['translate']->it("download"));
      $_REQUEST['JAS_DocumentManager_op'] = "list";
      $_SESSION['JAS_DocumentManager']->action();
    }
  }

  function _formatTemplate($content) {
    return str_replace("</a>\n", "</a>", str_replace("\">\n", "\">", str_replace(">", ">\n", str_replace("\n", "", $content))));
  }

  function _print() {
    $_REQUEST['lay_quiet'] = 1;
    echo $this->_view();
  }

  function getListId() {
    return $this->getId();
  }

  function getListLabel() {
    return "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=viewDocument&amp;JAS_Document_id=$this->_id\">".$this->getLabel()."</a>";
  }

  function getListUpdated() {
    return date(PHPWS_DATE_FORMAT, $this->_updated);
  }
  
  function getListDescription() {
    return $this->extras['description'];
  }

  function getListActions() {
    $showText = $_SESSION['translate']->it("Show");
    $hideText = $_SESSION['translate']->it("Hide");
    $viewText = $_SESSION['translate']->it("View");
    $editText = $_SESSION['translate']->it("Edit");
    $deleteText = $_SESSION['translate']->it("Delete");
    $approveText = $_SESSION['translate']->it("Approve");

    $actions = array();

    if((!$this->_approved) && $_SESSION['OBJ_user']->allow_access("documents", "approve_document")) {
      $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=approveDocument&amp;JAS_Document_id=$this->_id\">$approveText</a><br />";
    } else {
      if($_SESSION['OBJ_user']->allow_access("documents", "hideshow_document")) {
	if($this->_hidden) {
	  $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=setVisibility&amp;JAS_Document_id=$this->_id\">$showText</a>";
	} else {
	  $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=setVisibility&amp;JAS_Document_id=$this->_id\">$hideText</a>";
	}
      }
    }

    $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=viewDocument&amp;JAS_Document_id=$this->_id\">$viewText</a>";
    

    if($_SESSION['OBJ_user']->allow_access("documents", "edit_document")) {
      $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=editDocument&amp;JAS_Document_id=$this->_id\">$editText</a>";
    }

    if($_SESSION['OBJ_user']->allow_access("documents", "delete_document")) {
      $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=deleteDocument&amp;JAS_Document_id=$this->_id\">$deleteText</a>";
    }

    return implode("&#160;|&#160;", $actions);
  }

  function action() {
    $content = array();

    switch($_REQUEST['JAS_Document_op']) {
    case "view":
      $title = $_SESSION['translate']->it('View Document');
      $content[] = $this->_view();
      break;

    case "edit":
      $title = $_SESSION['translate']->it('Edit Document');
      $content[] = $this->_edit();
      break;

    case "print":
      $this->_print();
      break;

    case "save":
      $this->_save();
      break;

    case "delete":
      $this->_delete();
      break;

    case "deleteFile":
      $this->_deleteFile();
      break;

    case "moveFile":
      $this->_moveFile();
      break;

    case "downloadFile":
      $this->_downloadFile();
      break;
    }

    if(is_array($content) && (sizeof($content) > 0)) {
      $GLOBALS['CNT_documents']['title'] = $title;
      $GLOBALS['CNT_documents']['content'] = implode("<br />\n", $content);
    }
  }
}

?>
