<?php

/**
 * @version $Id: Photo.php,v 1.40 2005/05/10 13:21:34 darren Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

require_once(PHPWS_SOURCE_DIR . "core/Item.php");
require_once(PHPWS_SOURCE_DIR . "core/EZform.php");
require_once(PHPWS_SOURCE_DIR . "core/Error.php");
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
require_once(PHPWS_SOURCE_DIR . "core/Message.php");
require_once(PHPWS_SOURCE_DIR . "core/File.php");
require_once(PHPWS_SOURCE_DIR . "core/WizardBag.php");

class PHPWS_Photo extends PHPWS_Item {

  var $_album = NULL;
  var $_name = NULL;
  var $_width = NULL;
  var $_height = NULL;
  var $_type = NULL;
  var $_tnname = NULL;
  var $_tnwidth = NULL;
  var $_tnheight = NULL;
  var $_blurb = NULL;

  function PHPWS_Photo($id=NULL) {
    $this->setTable("mod_photoalbum_photos");
 
    if(isset($id)) {
      if(is_array($id)) {
	$this->init($id);

      } else {

	$error = $this->setId($id);
	if(PHPWS_Error::isError($error)) {
	  $error->message();
	}
	
	$this->init();
      }
    } else {
      if(!isset($this->_album)) {
	$this->_album = $_SESSION['PHPWS_AlbumManager']->album->getId();
      }
    }
  }

  function _view($showLinks=TRUE) {
    $tags = array();

    $tags['PHOTO_ALBUM'] = $this->_album;
    $tags['PHOTO_NAME'] = $this->_name;
    $tags['PHOTO_TYPE'] = $this->_type;

    $tags['PHOTO_WIDTH'] = $this->_width;
    $tags['PHOTO_HEIGHT'] = $this->_height;
    
    $tags['SRC_WIDTH'] = $this->_width;
    $tags['SRC_HEIGHT'] = $this->_height;
    
    if ((($this->_width >= PHOTOALBUM_MAX_WIDTH || $this->_height >= PHOTOALBUM_MAX_HEIGHT)) && ($_REQUEST['PHPWS_Photo_op'] != 'print')) {
      $ratio = $this->_width / $this->_height;
      if ($ratio >= 1) {
	$tags['SRC_WIDTH'] = PHOTOALBUM_MAX_WIDTH;
	$tags['SRC_HEIGHT'] = PHOTOALBUM_MAX_WIDTH / $ratio;
      } else {
	$tags['SRC_WIDTH'] = PHOTOALBUM_MAX_HEIGHT * $ratio;
	$tags['SRC_HEIGHT'] = PHOTOALBUM_MAX_HEIGHT;
      }
    }

    $tags['WIDTH_TEXT'] = $_SESSION['translate']->it("Width");
    $tags['HEIGHT_TEXT'] = $_SESSION['translate']->it("Height");
    $tags['TYPE_TEXT'] = $_SESSION['translate']->it("Type");
    
    $tags['PHOTO_TEXT'] = $_SESSION['translate']->it("Upload Image");
    $tags['SHORT_TEXT'] = $_SESSION['translate']->it("Short");

    $tags['SHORT'] = PHPWS_Text::parseOutput($this->getLabel());

    if($showLinks) {
      if($this->isHidden()) {
	$tags['HIDDEN_INFO'] = $_SESSION['translate']->it("This photo is currently hidden from the public.");
      }

      if(isset($_SESSION["PHPWS_AlbumManager"]->album->pager->limit)) {
	$getArray = array(
	    "PHPWS_Album_op"=>"view",
	    "PAGER_limit"   => $_SESSION["PHPWS_AlbumManager"]->album->pager->limit,
	    "PAGER_start"   => $_SESSION["PHPWS_AlbumManager"]->album->pager->start,
	    "PAGER_section"=>$_SESSION["PHPWS_AlbumManager"]->album->pager->section);
      } else {
	$getArray = array("PHPWS_Album_op"=>"view");  //bookmarked pic
      }

      $tags['BACK_LINK'] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Back to album"), "photoalbum", $getArray);

      $links = array();

      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Photo_op=print\" target=\"_blank\">" . $_SESSION['translate']->it("Print") . "</a>";

      if($_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
	$links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Photo_op=edit\">" . $_SESSION['translate']->it("Edit") . "</a>";
      }
      if($_SESSION['OBJ_user']->allow_access("photoalbum", "delete_photo")) {
	$links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Photo_op=delete\">" . $_SESSION['translate']->it("Delete") . "</a>";
      }

      $tags['PRINT_EDIT_DELETE_LINKS'] = implode("&#160;|&#160;", $links);

      if(is_array($_SESSION['PHPWS_AlbumManager']->album->photos)) {
	$key = array_search($this->getId(), $_SESSION['PHPWS_AlbumManager']->album->photos);
	if($key > 0) {
	  $tags['PREV_LINK'][] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_id=$this->_album&amp;PHPWS_Photo_op=view&amp;PHPWS_Photo_id=" . $_SESSION['PHPWS_AlbumManager']->album->photos[$key - 1] . "\">&#60;&#60;</a>";
	  $tags['PREV_LINK'][] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_id=$this->_album&amp;PHPWS_Photo_op=view&amp;PHPWS_Photo_id=" . $_SESSION['PHPWS_AlbumManager']->album->photos[$key - 1] . "\">" . $_SESSION['translate']->it("Prev") . "</a>";
	  $tags['PREV_LINK'] = implode("&#160;&#160;", $tags['PREV_LINK']);
	}

	if($key != (sizeof($_SESSION['PHPWS_AlbumManager']->album->photos) - 1)) {
	  $tags['NEXT_LINK'][] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_id=$this->_album&amp;PHPWS_Photo_op=view&amp;PHPWS_Photo_id=" . $_SESSION['PHPWS_AlbumManager']->album->photos[$key + 1] . "\">" . $_SESSION['translate']->it("Next") . "</a>";
	  $tags['NEXT_LINK'][] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_id=$this->_album&amp;PHPWS_Photo_op=view&amp;PHPWS_Photo_id=" . $_SESSION['PHPWS_AlbumManager']->album->photos[$key + 1] . "\">&#62;&#62;</a>";
	  $tags['NEXT_LINK'] = implode("&#160;&#160;", $tags['NEXT_LINK']);
	}
      }
    }

    if(isset($this->_blurb) && (strlen($this->_blurb) > 0)) {
      $tags['EXT_TEXT'] = $_SESSION['translate']->it("Extended");
      $tags['EXT'] = PHPWS_Text::parseOutput($this->_blurb);
    }

    $tags['UPDATED_TEXT'] = $_SESSION['translate']->it("Updated");
    $tags['UPDATED'] = $this->getUpdated();

    return PHPWS_Template::processTemplate($tags, "photoalbum", "viewPhoto.tpl");
  }

  function _edit() {
    $PAGER_limit   = $_SESSION["PHPWS_AlbumManager"]->album->pager->limit;
    $PAGER_start   = $_SESSION["PHPWS_AlbumManager"]->album->pager->start;
    $PAGER_section = $_SESSION["PHPWS_AlbumManager"]->album->pager->section;

    $id = $this->getId();
    $authorize = TRUE;
    if(isset($id)) {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
	$message = $_SESSION['translate']->it("You do not have permission to edit photos within an album.");
	$authorize = FALSE;
      }
    } else {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
	$message = $_SESSION['translate']->it("You do not have permission to add photos within an album.");
	$authorize = FALSE;
      }
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_edit()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }
    
    $options = array(0=>$_SESSION['translate']->it("Visible"),
		     1=>$_SESSION['translate']->it("Hidden"));

    $hidden = 0;
    if($this->isHidden()) {
      $hidden = 1;
    }

    $form = new EZform("PHPWS_Photo_edit");

    if(isset($this->_name)) {
      $form->add("Photo_remove", "checkbox");
      $form->setTab("Photo_remove", 1);
    }

    $form->add("Photo", "file");
    $form->add("Photo_short", "text", $this->getLabel());
    $form->setSize("Photo_short", 40);
    $form->setMaxSize("Photo_short", 255);
    $form->setTab("Photo_short", 2);

    $form->add("Photo_hidden", "select", $options);
    $form->setMatch("Photo_hidden", $hidden);
    $form->setTab("Photo_hidden", 3);

    $form->add("Photo_ext", "textarea", $this->_blurb);
    $form->setTab("Photo_ext", 4);

    $form->add("Photo_save", "submit", $_SESSION['translate']->it("Save"));
    $form->setTab("Photo_save", 5);

    $form->add("module", "hidden", "photoalbum");
    $form->add("PAGER_limit", "hidden", $PAGER_limit);
    $form->add("PAGER_start", "hidden", $PAGER_start);
    $form->add("PAGER_section", "hidden", $PAGER_section);
    $form->add("PHPWS_Photo_op", "hidden", "save");
     
    $tags = array();
    $tags = $form->getTemplate();

    if(isset($this->_name)) {
      $tags['PHOTO_ALBUM'] = $this->_album;
      $tags['PHOTO_NAME'] = $this->_name;
      $tags['PHOTO_WIDTH'] = $this->_width;
      $tags['PHOTO_HEIGHT'] = $this->_height;
      $tags['PHOTO_TYPE'] = $this->_type;

      $tags['WIDTH_TEXT'] = $_SESSION['translate']->it("Width");
      $tags['HEIGHT_TEXT'] = $_SESSION['translate']->it("Height");
      $tags['TYPE_TEXT'] = $_SESSION['translate']->it("Type");
      $tags['REMOVE_TEXT'] = $_SESSION['translate']->it("Remove Image");

      $tags['UPDATED_TEXT'] = $_SESSION['translate']->it("Updated");
      $tags['UPDATED'] = $this->getUpdated();
    }
      
    if($_SESSION['OBJ_user']->js_on) {
      $tags['PHOTO_EXT'] = PHPWS_WizardBag::js_insert("wysiwyg", "PHPWS_Photo_edit", "Photo_ext") . $tags['PHOTO_EXT'];
    }

    $getArray = array(
      "PHPWS_Album_op"=>"view",
      "PAGER_limit"   => $PAGER_limit,
      "PAGER_start"   => $PAGER_start,
      "PAGER_section" => $PAGER_section);

    $tags['BACK_LINK'] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Back to album"), "photoalbum", $getArray);

    $tags['PHOTO_TEXT'] = $_SESSION['translate']->it("Upload Image");
    $tags['SHORT_TEXT'] = $_SESSION['translate']->it("Short");
    $tags['EXT_TEXT'] = $_SESSION['translate']->it("Extended");
    $tags['HIDDEN_TEXT'] = $_SESSION['translate']->it("Activity");

    return PHPWS_Template::processTemplate($tags, "photoalbum", "editPhoto.tpl");
  }

  function _save() {
    $id = $this->getId();
    $authorize = TRUE;
    if(isset($id)) {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
	$message = $_SESSION['translate']->it("You do not have permission to edit photos within an album.");
	$authorize = FALSE;
      }
    } else {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
	$message = $_SESSION['translate']->it("You do not have permission to add photos within an album.");
	$authorize = FALSE;
      }
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }

    if(isset($_REQUEST['Photo_short'])) {
      $error = $this->setLabel($_REQUEST['Photo_short']);
    }
    
    if(isset($_REQUEST['Photo_remove']) && ($_REQUEST['Photo_remove'] == 1)) {
      if($this->_unlink()) {
	if(!isset($this->_label))
	  $this->_label = '';

	$this->commit();

	trim($this->_label);

	$message = $_SESSION['translate']->it("The image was successfully removed.") . "<br /><br />";
	$_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");
      } else {
	$message =  $_SESSION['translate']->it("There was a problem removing the image.");
	$error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
	$error->message("CNT_photoalbum");
      }

      $_REQUEST['PHPWS_Photo_op'] = "edit";
      $this->action();
      return;
    }

    if($_FILES['Photo']['error'] == 0) { 
      if(isset($this->_name)) {
	$message =  $_SESSION['translate']->it("You must remove the image before uploading a new one.");
	$error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
	$error->message("CNT_photoalbum");

	$_REQUEST['PHPWS_Photo_op'] = "edit";
	$this->action();
	return;
      }

      $name = PHPWS_File::nameToSafe($_FILES['Photo']['name']);
      $file = PHOTOALBUM_DIR . $this->_album . "/" . $name;

      if(is_file($file)) {
	$name = time() . "_" . $name;
	$file = PHOTOALBUM_DIR . $this->_album . "/" . $name;
      }

      @move_uploaded_file($_FILES['Photo']['tmp_name'], $file);
      if(is_file($file)) {
	chmod($file, 0644);
	$info = @getimagesize($file);

	include(PHPWS_SOURCE_DIR.'conf/allowedImageTypes.php');

	if(in_array($_FILES['Photo']['type'], $allowedImageTypes)) {
	  $this->_name = $name;
	  $this->_type = $_FILES['Photo']['type'];
	  $this->_width = $info[0];
	  $this->_height = $info[1];      

	  if($info[2] == 1 || $info[2] == 2 || $info[2] == 3) {
	    $dir = "images/photoalbum/" . $this->_album . "/";

	    $thumbnail = PHPWS_File::makeThumbnail($this->_name, $dir, $dir, PHOTOALBUM_TN_WIDTH, PHOTOALBUM_TN_HEIGHT);

	    if(!is_array($thumbnail))
	      $thumbnail->message("CNT_photoalbum");

	    if(is_file(PHOTOALBUM_DIR . $this->_album . "/" . $thumbnail[0])) {
	      $this->_tnname = $thumbnail[0];
	      $this->_tnwidth = $thumbnail[1];
	      $this->_tnheight = $thumbnail[2];

	      $_SESSION['PHPWS_AlbumManager']->album->image = "<img src=\"./images/photoalbum/$this->_album/$this->_tnname\" width=\"$this->_tnwidth\" height=\"$this->_tnheight\" border=\"0\" alt=\"" . htmlentities($this->getLabel()) . "\" />";
	      $_SESSION['PHPWS_AlbumManager']->album->commit();
	    }
	  } else {
	    $thumbnail = new PHPWS_Error("Photo", "_save", "Submitted image type does not have support built for the GD libraries.");
	  }
	} else {
	  @unlink($file);
	  $message = $_SESSION['translate']->it("The image uploaded was not an allowed image type.");
	  $error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
	  $error->message("CNT_photoalbum");

	  $_REQUEST['PHPWS_Photo_op'] = "edit";
	  $this->action();
	  return;
	}	  
      } else {
	$message = $_SESSION['translate']->it("There was a problem uploading the specified image.");
	$error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
	$error->message("CNT_photoalbum");
	
	$_REQUEST['PHPWS_Photo_op'] = "edit";
	$this->action();
	return;
      }
    } else if($_FILES['Photo']['error'] != 4) {
      $message = $_SESSION['translate']->it("The file uploaded exceeded the max size allowed.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $error->message("CNT_photoalbum");
      
      $_REQUEST['PHPWS_Photo_op'] = "edit";
      $this->action(); 
      return;
    }

    if(isset($_REQUEST['Photo_ext'])) {
      $this->_blurb = PHPWS_Text::parseInput($_REQUEST['Photo_ext']);
    }

    if(isset($_REQUEST['Photo_hidden']) && ($_REQUEST['Photo_hidden'] == 1)) {
      $this->setHidden();
    } else {
      $this->setHidden(FALSE);
    }

    if(isset($error) && PHPWS_Error::isError($error)) {
      $message =  $_SESSION['translate']->it("You must enter a short description for the photo.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $error->message("CNT_photoalbum");

      $_REQUEST['PHPWS_Photo_op'] = "edit";
      $this->action();
      return;
    }

    $error = $this->commit();
    if(PHPWS_Error::isError($error)) {
      $message = $_SESSION['translate']->it("There was a problem saving the information to the database.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $error->message("CNT_photoalbum");
      
      $_REQUEST['PHPWS_Photo_op'] = "edit";
      $this->action(); 
      return;
    }

    $sql = "UPDATE " . PHPWS_TBL_PREFIX . "mod_photoalbum_albums SET image='" . $this->getThumbnail() . "' WHERE id='" . $this->_album . "'";
    $GLOBALS['core']->query($sql);

    if(isset($thumbnail) && PHPWS_Error::isError($thumbnail)) {
      $message="<span style=\"color:red\">".$_SESSION['translate']->it("The Photo [var1] was saved but their was a problem creating the thumbnail image.", 
								       "<b><i>" . $this->getLabel() . "</i></b>") . "</span><br /><br />";      
    } else {
      $message = $_SESSION['translate']->it("The Photo [var1] was successfully saved.", "<b><i>" . $this->getLabel() . "</i></b>") . "<br />\n";
    }

    $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

    $_REQUEST['PHPWS_Album_op'] = "view";
    $_SESSION['PHPWS_AlbumManager']->album = new PHPWS_Album($this->_album);
    $_SESSION['PHPWS_AlbumManager']->album->action();
  }

  function _unlink() {
    if(isset($this->_name)) {
      @unlink(PHOTOALBUM_DIR . $this->_album . "/" . $this->_name);
    }
    if(!is_file(PHOTOALBUM_DIR . $this->_album . "/" . $this->_name)) {
      $this->_name = NULL;
      $this->_type = NULL;
      $this->_width = NULL;
      $this->_height = NULL;
    } else {
      return FALSE;
    }

    if(isset($this->_tnname)) {
      @unlink(PHOTOALBUM_DIR . $this->_album . "/" . $this->_tnname);
    }
    if(!is_file(PHOTOALBUM_DIR . $this->_album . "/" . $this->_tnname)) {
      $this->_tnname = NULL;
      $this->_tnwidth = NULL;
      $this->_tnheight = NULL;
    } else {
      return FALSE;
    }

    return TRUE;
  }

  function _delete() {
    $pagerSection= (int)$_SESSION["PHPWS_AlbumManager"]->album->pager->section;
    $pagerLimit  = (int)$_SESSION["PHPWS_AlbumManager"]->album->pager->limit;
    $pagerStart  = (int)$_SESSION["PHPWS_AlbumManager"]->album->pager->start;
    
    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "delete_photo")) {
      $message = $_SESSION['translate']->it("You do not have permission to delete photos within an album.");
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_delete()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }

    if(isset($_REQUEST['Photo_yes'])) {
      $this->_unlink();
      $this->kill();

      $message = $_SESSION['translate']->it("The photo [var1] was successfully deleted from the database.", "<b><i>" . $this->getLabel() . "</i></b>");
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

      $_SESSION['PHPWS_AlbumManager']->updateAlbumList($this->_album);

      $_REQUEST['PHPWS_Album_op'] = "view";
      $_SESSION['PHPWS_AlbumManager']->album = new PHPWS_Album($this->_album);
      $_SESSION['PHPWS_AlbumManager']->album->action();

    } else if(isset($_REQUEST['Photo_no'])) {
      $message = $_SESSION['translate']->it("No photo was deleted from the database.");
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

      $_REQUEST["PAGER_section"] = $pagerSection;
      $_REQUEST["PAGER_start"]   = $pagerStart;

      $_REQUEST['PHPWS_Album_op'] = "view";
      $_SESSION['PHPWS_AlbumManager']->album->action();

    } else {
      $title = $_SESSION['translate']->it("Delete Photo Confirmation");

      $form = new EZform("PHPWS_Photo_delete");
      $form->add("module", "hidden", "photoalbum");
      $form->add("PHPWS_Photo_op", "hidden", "delete");

      $form->add("PAGER_limit", "hidden", $pagerLimit);

      if($_SESSION["PHPWS_AlbumManager"]->album->pager->_itemCount == 1 && 
	 ($pagerSection != 1 || $pagerSection != 0)) {
	$form->add("PAGER_section", "hidden", $pagerSection - 1);
	$form->add("PAGER_start", "hidden", $pagerStart - $pagerLimit);

      } else {
	$form->add("PAGER_section", "hidden", $pagerSection);
	$form->add("PAGER_start", "hidden", $_SESSION["PHPWS_AlbumManager"]->album->pager->start);
      }

      $form->add("Photo_yes", "submit", $_SESSION['translate']->it("Yes"));
      $form->add("Photo_no", "submit", $_SESSION['translate']->it("No"));
      
      $tags = array();
      $tags = $form->getTemplate();
      $tags['MESSAGE'] = $_SESSION['translate']->it("Are you sure you want to delete this photo?");
      
      $content = PHPWS_Template::processTemplate($tags, "photoalbum", "deletePhoto.tpl");
      $GLOBALS['CNT_photoalbum']['content'] = "<h3>$title</h3>$content";

      $GLOBALS['CNT_photoalbum']['title'] = $_SESSION['translate']->it("Photo Album") . ":&#160;" . $_SESSION['PHPWS_AlbumManager']->album->getLabel();
      $GLOBALS['CNT_photoalbum']['content'] .= $this->_view(FALSE);
    }
  }

  function _print() {
    $_REQUEST['lay_quiet'] = 1;
    echo $this->_view(FALSE);
  }

  function getThumbnail() {
    $label = htmlentities($this->getLabel());

    $image = array();
    $image[] = "<img src=\"images/photoalbum/";
    $image[] = $this->_album . "/";
    $image[] = $this->_tnname . "\" ";
    $image[] = "width=\"" . $this->_tnwidth . "\" ";
    $image[] = "height=\"" . $this->_tnheight . "\" ";
    $image[] = "alt=\"" . addslashes($label) . "\" ";
    $image[] = "title=\"" . addslashes($label) . "\" ";
    $image[] = "border=\"0\" />";
    return implode("", $image);
  }

  function getAlbum() {
    return $this->_album;
  }

  function action() {
    if(PHPWS_Message::isMessage($_SESSION['PHPWS_AlbumManager']->message)) {
      $_SESSION['PHPWS_AlbumManager']->message->display();
      $_SESSION['PHPWS_AlbumManager']->message = null;
    }

    if(isset($_REQUEST['PHPWS_Photo_op'])) {
      switch($_REQUEST['PHPWS_Photo_op']) {
      case "view":
	$title = $_SESSION['translate']->it("View Photo");
	$content = $this->_view();
	break;
	
      case "edit":
	if($this->_id == NULL)
	  $title = $_SESSION['translate']->it("New Photo");
	else
	  $title = $_SESSION['translate']->it("Edit Photo");
	$content = $this->_edit();
	break;
	
      case "save":
	$this->_save();
	break;
	
      case "delete":
	$this->_delete();
	break;
	
      case "print":
	$this->_print();
	break;
      }
    }
      
    if(isset($content)) {
      $GLOBALS['CNT_photoalbum']['title'] = $title;
      $GLOBALS['CNT_photoalbum']['content'] .= $content;
    }
  }
}

?>