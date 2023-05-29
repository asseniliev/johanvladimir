<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: Files.php,v 1.7 2005/05/18 21:07:49 darren Exp $
 */

require_once(PHPWS_SOURCE_DIR.'core/Item.php');

class JAS_Files {

  var $_num = NULL;
  var $_files = array();
  var $messages = array();

  function _addFiles() {
    if(isset($_REQUEST['JAS_Files_num']) && is_numeric($_REQUEST['JAS_Files_num'])) {
      $this->_num = $_REQUEST['JAS_Files_num'];
      if(!($this->_num > 0) && ($this->_num <= JAS_MAX_UPLOAD_NUM)) {
	$this->_num = JAS_MAX_UPLOAD_NUM;
      }
    } else {
      $this->_num = JAS_MAX_UPLOAD_NUM;
    }

    $formTags = array();

    $formTags['BACK_LINK'] = "<a href=\"./index.php?module=documents&amp;JAS_Document_op=edit\">".$_SESSION['translate']->it('Back to Document')."</a>";

    $formTags['FILE_NUM_TEXT'] = $_SESSION['translate']->it("How many files would you like to upload?");
    $formTags['FILE_NUM_SELECT'] = array();
    $formTags['FILE_NUM_SELECT'][] = "<select name=\"JAS_Files_num\">";
    for($i = 1; $i <= JAS_MAX_UPLOAD_NUM; $i++) {
      if($i == $this->_num) {
	$formTags['FILE_NUM_SELECT'][] = "<option value=\"$i\" selected=\"selected\">$i</option>";
      } else {
	$formTags['FILE_NUM_SELECT'][] = "<option value=\"$i\">$i</option>";
      }
    }
    $formTags['FILE_NUM_SELECT'][] = "</select>";
    $formTags['FILE_NUM_SELECT'] = implode("\n", $formTags['FILE_NUM_SELECT']);

    $formTags['FILE_UPLOAD_TEXT'] = $_SESSION['translate']->it("Please select your files and the click Upload.");
    $formTags['FILE_UPLOAD_ELEMENTS'] = array();
    for($i = 1; $i <= $this->_num; $i++) {
      $formTags['FILE_UPLOAD_ELEMENTS'][] = "<b>$i</b>:&#160;<input type=\"file\" name=\"JAS_Files[]\" />";
    }
    $formTags['FILE_UPLOAD_ELEMENTS'] = implode("<br /><br />\n", $formTags['FILE_UPLOAD_ELEMENTS']);

    $formTags['UPDATE_BUTTON_TEXT'] = $_SESSION['translate']->it("Update");
    $formTags['UPLOAD_BUTTON_TEXT'] = $_SESSION['translate']->it("Upload");

    $formTags['HIDDENS'] = "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".JAS_MAX_UPLOAD_SIZE."\" />";

    return PHPWS_Template::processTemplate($formTags, "documents", "upload/addFiles.tpl");
  }

  function _uploadFiles() {
    if(isset($_REQUEST['JAS_Files_update'])) {
      $_REQUEST['JAS_Files_op'] = "addFiles";
      $this->action();
      return;
    }

    $this->messages = array();
    for($i = 0; $i < $this->_num; $i++) {
      if(!$_FILES['JAS_Files']['error'][$i]) {
	
	$this->_files[$i] = new JAS_File;
	$name = str_replace(" ", "_", $_FILES['JAS_Files']['name'][$i]);
	$file = JAS_DOCUMENT_DIR . $name;
	if(is_file($file)) {
	  $name = time() . "_" . str_replace(" ", "_", $_FILES['JAS_Files']['name'][$i]);
	  $file = JAS_DOCUMENT_DIR . $name;
	}
	
	$types = explode(",", JAS_DOCUMENT_TYPES);
	if(in_array($_FILES['JAS_Files']['type'][$i], $types) || JAS_DOCUMENT_TYPES == "all") {
	  @move_uploaded_file($_FILES['JAS_Files']['tmp_name'][$i], $file);
	  if(is_file($file)) {
	    chmod($file, 0644);
	    $this->_files[$i]->doc = $_SESSION['JAS_DocumentManager']->document->getId();
	    $this->_files[$i]->name = $name;
	    $this->_files[$i]->type = $_FILES['JAS_Files']['type'][$i];
	    $this->_files[$i]->size = $_FILES['JAS_Files']['size'][$i];

	    $this->_files[$i]->setOwner();
	    $this->_files[$i]->setIp();
	    $this->_files[$i]->setCreated();      
	    
	    $error = $this->_files[$i]->commit(NULL, TRUE);
	    if(PHPWS_Error::isError($error)) {
	      $this->messages[$i] = "<span style=\"color:#ff0000\">" . $_SESSION['translate']->it("Unable to save to the database.") . "</span>";
	      $this->_files[$i]->delete();
	    } else {
	      $this->messages[$i] = $_SESSION['translate']->it("File successfully uploaded.");
	    }
	  } else {
	    $this->messages[$i] = "<span style=\"color:#ff0000\">" . $_SESSION['translate']->it("There was a problem uploading the specified file.") . "</span>";
	  }
	} else {
	  $this->messages[$i] = "<span style=\"color:#ff0000\">".$_SESSION['translate']->it("The file type [var1] uploaded was not an allowed file type.", $_FILES['JAS_Files']['type'][$i])."</span>";
	}
      } else {
	switch($_FILES['JAS_Files']['error'][$i]) {
	case 1:
	case 2:
	  $this->messages[$i] = "<span style=\"color:#ff0000\">" . $_SESSION['translate']->it("The file exceeded the max size allowed.") . "</span>";
	  break;
	case 3:
	  $this->messages[$i] = "<span style=\"color:#ff0000\">" . $_SESSION['translate']->it("The file was only partially uploaded.") . "</span>";
	  break;
	case 4:
	  $this->messages[$i] = "<span style=\"color:#ff0000\">" . $_SESSION['translate']->it("No file was selected to be uploaded.") . "</span>";
	  break;
	}
      }
    }  
    
    $_REQUEST['JAS_Document_op'] = "edit";
    $_SESSION['JAS_DocumentManager']->document->action();
  }

  function action() {
    $content = array();

    switch($_REQUEST['JAS_Files_op']) {
    case "addFiles":
      $title = $_SESSION['translate']->it("Upload Files");
      $content[] = $this->_addFiles();
      break;

    case "uploadFiles":
      $this->_uploadFiles();
      break;
    }

    if(is_array($content) && (sizeof($content) > 0)) {
       $GLOBALS['CNT_documents']['title'] = $title;
       $GLOBALS['CNT_documents']['content'] = implode("<br />\n", $content);
    }
  }
}

class JAS_File extends PHPWS_Item {

  var $doc;
  var $name;
  var $type;
  var $size;

  function JAS_File($row=NULL) {
    $this->setTable("mod_documents_files");
    $this->addExclude(array("_label", "_editor", "_updated", "_hidden", "_approved"));

    if(is_array($row) && (sizeof($row) > 0)) {
      $this->setVars($row);
    }
  }

  function delete() {
    @unlink(JAS_DOCUMENT_DIR . $this->name);
  }

  function getListId() {
    return $this->getId();
  }

  function getListDoc() {
    return $this->doc;
  }

  function getListName() {
    return $this->name;
  }

  function getListType() {
    return $this->type;
  }

  function getListSize() {
    if($this->size < 1024) {
      // Display in bytes
      return number_format($this->size, 2) . " bytes";

    } else if($this->size < pow(2, 20)) {
      // Display in kilobytes
      return number_format(round(($this->size/1024),2), 2) . " KB";      

    } else {
      // Display in megabytes
      return number_format(round(($this->size/1024)/1024,2), 2) . " MB";
    }

  }

  function getListCreated() {
    return date(PHPWS_DATE_FORMAT, $this->_created);
  }

  function getListActions() {
    $deleteText = $_SESSION['translate']->it("Delete");
    $downloadText = $_SESSION['translate']->it("Download");
    $moveText = $_SESSION['translate']->it("Move");

    $actions = array();
    $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=downloadFile&amp;JAS_File_id=$this->_id\">$downloadText</a>";

    if($_SESSION['OBJ_user']->allow_access("documents", "delete_document")) {
      $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_Document_op=deleteFile&amp;JAS_File_id=$this->_id\">$deleteText</a>";
    }

    if($_SESSION['OBJ_user']->allow_access("documents", "move_document")) {
      $actions[] = "<a href=\"./index.php?module=documents&amp;JAS_Document_op=moveFile&amp;JAS_File_id=$this->_id\">$moveText</a>";
    }

    return implode("&#160;", $actions);
  }
}

?>