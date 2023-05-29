<?php

/**
 * @version $Id: Album.php,v 1.52 2005/05/12 01:19:38 darren Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

require_once(PHPWS_SOURCE_DIR . "core/Item.php");
require_once(PHPWS_SOURCE_DIR . "core/Error.php");
require_once(PHPWS_SOURCE_DIR . "core/EZform.php");
require_once(PHPWS_SOURCE_DIR . "core/File.php");
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
require_once(PHPWS_SOURCE_DIR . "core/WizardBag.php");
require_once(PHPWS_SOURCE_DIR . "core/Message.php");
require_once(PHPWS_SOURCE_DIR . "mod/photoalbum/class/SlideShow.php");
require_once(PHPWS_SOURCE_DIR . "mod/photoalbum/class/NoJSSlideShow.php");

require_once(PHPWS_SOURCE_DIR . "mod/photoalbum/class/Photo.php");

class PHPWS_Album extends PHPWS_Item {

  /**
   * The short description of the photo album
   *
   * @var    string
   * @access private
   */
  var $_blurb0 = NULL;

  /**
   * The extended description of the photo album
   *
   * @var    string
   * @access private
   */
  var $_blurb1 = NULL;

  /**
   * The image to be shown in the album list
   *
   * @var    string
   * @access private
   */
  var $image = NULL;

  /**
   * The ids of all the photos for the current album
   *
   * @var    array
   * @access public
   */
  var $photos = array();

  /**
   * The current photo being edited or viewed
   *
   * @var    PHPWS_Photo
   * @access public
   */
  var $photo = NULL;

  /**
   * The paging data object
   *
   * @var    PHPWS_Pager
   * @access public
   */
  var $pager = NULL;

  /**
   * An array of the file information for files being batch added
   *
   * @var    array
   * @access private
   */
  var $_batch = array();  

  /**
   * Stores the order in which the photos in the album are being shown
   *
   * @var    integer
   * @access public
   */
  var $order = NULL;

  function PHPWS_Album($id=NULL) {
    $this->setTable("mod_photoalbum_albums");
    $this->addExclude(array("photos", "photo", "pager", "_batch", "order"));

    if(isset($id)) {
      $error = $this->setId($id);
      if(PHPWS_Error::isError($error)) {
	$error->message();
      }
      $this->init();

      $this->order = PHOTOALBUM_DEFAULT_SORT;

      $this->_orderIds();
    }
  }

  function _orderIds() {
    $sql = array();
    $sql[] = "SELECT id FROM ";
    $sql[] = PHPWS_TBL_PREFIX;
    $sql[] = "mod_photoalbum_photos WHERE album='";
    $sql[] = $this->getId();
    $sql[] = "'";
    
    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
      $sql[] = " AND hidden='0'";
    }

    if($this->order == 0) {
      $order = " ORDER BY created DESC";
    } else {
      $order = " ORDER BY created ASC";
    }

    $sql = implode("", $sql) . $order;
    $this->photos = $GLOBALS['core']->getCol($sql);
  }


  function _edit() {
    $id = $this->getId();
    $authorize = TRUE;
    if(isset($id)) {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to edit photoalbums.");
	$authorize = FALSE;
      }
    } else {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to add photoalbums.");
	$authorize = FALSE;
      }
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Album::_edit()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
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

    $form = new EZform("PHPWS_Album_edit");
    $form->add("Album_name", "text", $this->getLabel());
    $form->setSize("Album_name", 33);
    $form->setMaxSize("Album_name", 255);
    $form->setTab("Album_name", 1);

    $form->add("Album_short", "text", $this->_blurb0);
    $form->setSize("Album_short", 40);
    $form->setMaxSize("Album_short", 255);
    $form->setTab("Album_short", 2);

    $form->add("Album_hidden", "select", $options);
    $form->setMatch("Album_hidden", $hidden);
    $form->setTab("Album_hidden", 3);

    $form->add("Album_ext", "textarea", $this->_blurb1);
    $form->setTab("Album_ext", 4);

    $form->add("Album_save", "submit", $_SESSION['translate']->it("Save"));
    $form->setTab("Album_save", 5);
 
    $form->add("module", "hidden", "photoalbum");
    $form->add("PHPWS_Album_op", "hidden", "save");
     
    $tags = array();
    $tags = $form->getTemplate();

    $tags['NAME_TEXT'] = $_SESSION['translate']->it("Name");
    $tags['SHORT_TEXT'] = $_SESSION['translate']->it("Short");
    $tags['HIDDEN_TEXT'] = $_SESSION['translate']->it("Activity");
    $tags['EXT_TEXT'] = $_SESSION['translate']->it("Extended");

    if(isset($_SESSION['OBJ_fatcat'])) {
      $tags['CATEGORY_TEXT'] = $_SESSION['translate']->it("Category");
      $tags['CATEGORY'] = $_SESSION['OBJ_fatcat']->showSelect($this->getId());
    }

    if($_SESSION['OBJ_user']->js_on){
      $tags['ALBUM_EXT'] = PHPWS_WizardBag::js_insert("wysiwyg", "PHPWS_Album_edit", "Album_ext") . $tags['ALBUM_EXT'];
    }

    $id = $this->getId();
    if(isset($id)) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=view\">" . $_SESSION['translate']->it("Back") . "</a>";

      if($_SESSION['OBJ_user']->allow_access("photoalbum", "delete_album")) {
	$links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=delete\">" . $_SESSION['translate']->it("Delete Album") . "</a>";
      }

      if($GLOBALS['core']->moduleExists("menuman") && $_SESSION['OBJ_user']->allow_access("menuman", "add_item")) {
	$_SESSION['OBJ_menuman']->add_module_item("photoalbum", "&amp;PHPWS_Album_op=view&amp;PHPWS_Album_id=$id", "./index.php?module=photoalbum&amp;PHPWS_Album_op=view&amp;PHPWS_Album_id=$id", 1);
      }
    } else {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list\">" . $_SESSION['translate']->it("Back") . "</a>";
    }

    $tags['LINKS'] = implode("&#160;|&#160;", $links);

    return PHPWS_Template::processTemplate($tags, "photoalbum", "editAlbum.tpl");
  }

  function _save() {
    $id = $this->getId();
    $authorize = TRUE;
    if(isset($id)) {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to edit photoalbums.");
	$authorize = FALSE;
      }
    } else {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to add photoalbums.");
	$authorize = FALSE;
      }
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }
    
    if(isset($_REQUEST['Album_ext'])) {
      $this->_blurb1 = PHPWS_Text::parseInput($_REQUEST['Album_ext']);
    }

    if(isset($_REQUEST['Album_short'])) {
      $this->_blurb0 = PHPWS_Text::parseInput($_REQUEST['Album_short']);
    }

    if(isset($_REQUEST['Album_name'])) {
      $error = $this->setLabel($_REQUEST['Album_name']);
    }

    if(isset($_REQUEST['Album_hidden']) && ($_REQUEST['Album_hidden'] == 1)) {
      $this->setHidden();
    } else {
      $this->setHidden(FALSE);
    }

    if(PHPWS_Error::isError($error)) {
      $message =  $_SESSION['translate']->it("You must enter a name for the Photo Album.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $error->message("CNT_photoalbum");

      $_REQUEST['PHPWS_Album_op'] = "edit";
      $this->action();
      return;
    }

    $error = $this->commit();
    if(PHPWS_Error::isError($error)) {
      $message = $_SESSION['translate']->it("The Photo Album could not be updated to the database.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $error->message("CNT_photoalbum");
      
      $_REQUEST['PHPWS_Album_op'] = "edit";
      $this->action();
      return;
    } else {

      if ($this->isHidden())
	$fatActive = FALSE;
      else
	$fatActive = TRUE;

      $id = $this->getId();
      $_SESSION['OBJ_fatcat']->saveSelect($this->getLabel(), "index.php?module=photoalbum&amp;PHPWS_Album_op=view&amp;PHPWS_Album_id=$id", $id, NULL, "photoalbum", NULL, NULL, $fatActive);

      if(!is_dir(PHOTOALBUM_DIR . $this->getId() . "/")) {
	PHPWS_File::makeDir(PHOTOALBUM_DIR . $this->getId() . "/");
	if(!is_dir(PHOTOALBUM_DIR . $this->getId() . "/")) {
	  $message = $_SESSION['translate']->it("The photo album image directory could not be created.");
	  $error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
	  $error->message("CNT_photoalbum");
	  
	  $_REQUEST['PHPWS_Album_op'] = "edit";
	  $this->action();
	  return;
	}
      }

      $message = $_SESSION['translate']->it("The Photo Album [var1] was successfully saved.", "<b><i>" . $this->getLabel() . "</i></b>") . "<br />\n";
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

      $_REQUEST['PHPWS_Album_op'] = "view";
      $this->action();
    }
  }

  function _delete() {
    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "delete_album")) {
      $message = $_SESSION['translate']->it("You do not have permission to delete entire photo albums.");
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Album::_delete()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }

    if(isset($_REQUEST['Album_yes'])) {
      $this->kill();

      $sql = "DELETE FROM " . PHPWS_TBL_PREFIX . "mod_photoalbum_photos WHERE album='" . $this->getId() . "'";
      $GLOBALS['core']->query($sql);

      PHPWS_File::rmdir("images/photoalbum/" . $this->getId() . "/");
      PHPWS_Fatcat::purge($this->getId(), "photoalbum");

      $message = $_SESSION['translate']->it("The album [var1] and all its photos were successfully deleted from the database.", "<b><i>" . $this->getLabel() . "</i></b>");
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");
      $_SESSION['PHPWS_AlbumManager']->message->display();

      $_REQUEST['PHPWS_AlbumManager_op'] = "list";
      $_SESSION['PHPWS_AlbumManager']->action();
      unset($this);

    } else if(isset($_REQUEST['Album_no'])) {
      $message = $_SESSION['translate']->it("No album was deleted from the database.");
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

      $_REQUEST['PHPWS_Album_op'] = "view";
      $_SESSION['PHPWS_AlbumManager']->album->action();

    } else {
      $title = $_SESSION['translate']->it("Delete Album Confirmation");

      $form = new EZform("PHPWS_Album_delete");
      $form->add("module", "hidden", "photoalbum");
      $form->add("PHPWS_Album_op", "hidden", "delete");

      $form->add("Album_yes", "submit", $_SESSION['translate']->it("Yes"));
      $form->add("Album_no", "submit", $_SESSION['translate']->it("No"));
      
      $tags = array();
      $tags = $form->getTemplate();
      $tags['MESSAGE'] = $_SESSION['translate']->it("Are you sure you want to delete this album and all the photos associated with it?");
      
      $content = PHPWS_Template::processTemplate($tags, "photoalbum", "deleteAlbum.tpl");
      $GLOBALS['CNT_photoalbum']['content'] = "<h3>$title</h3>$content";
     
      $GLOBALS['CNT_photoalbum']['title'] = $_SESSION['translate']->it("Photo Album") . ":&#160;" . $_SESSION['PHPWS_AlbumManager']->album->getLabel();
      $GLOBALS['CNT_photoalbum']['content'] .= $this->_view();
    }
  }
    
  function _view() {
    $columns = NULL;
    $id = $this->getId();

    if(isset($_REQUEST['PHPWS_Album_order']) && is_numeric($_REQUEST['PHPWS_Album_order'])) {
      $this->order = $_REQUEST['PHPWS_Album_order'];
      $this->_orderIds();
    }

    if(!isset($this->pager) ||
       (!isset($_REQUEST['PAGER_Limit']) && !isset($_REQUEST['PAGER_Start']) && !isset($_REQUEST['PAGER_Section']))) {      
      $this->pager = new PHPWS_pager;
      $this->pager->setLimits(array(1,4,9,16));
      $this->pager->makeArray(TRUE);
      $this->pager->limit = 9;
    }

    $this->pager->setLinkBack("./index.php?module=photoalbum&amp;PHPWS_Album_op=view&amp;PHPWS_Album_id=$id");
    $this->pager->setData($this->photos);
    $this->pager->pageData();
    $photoIds = $this->pager->getData();

    $links = array();

    if($_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=new\">" . $_SESSION['translate']->it("New Photo") . "</a>";
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=batch\">" . $_SESSION['translate']->it("Batch Add") . "</a>";
    }

    $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list\">" . $_SESSION['translate']->it("List Albums") . "</a>";

    $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=slideShow\">" . 
      $_SESSION['translate']->it("Slide Show") . "</a>";

    if($_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=desc\">" . $_SESSION['translate']->it("Missing Descriptions") . "</a>";
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=edit\">" . $_SESSION['translate']->it("Settings") . "</a>";
    }

    $listTags = array();

    if($this->isHidden()) {
      $listTags['HIDDEN_INFO'] = $_SESSION['translate']->it("This album is currently hidden from the public.");
    }

    $listTags['ALBUM_LINKS'] = implode("&#160;|&#160;", $links);

    $listTags['ORDER_LABEL'] = $_SESSION['translate']->it("Order By");
    $listTags['FIRST_LABEL'] = $_SESSION['translate']->it("First");

    $listTags['BLURB0'] = PHPWS_Text::parseOutput($this->_blurb0);
    $listTags['BLURB1'] = PHPWS_Text::parseOutput($this->_blurb1);
    $listTags['CREATED'] = $this->getCreated();
    $listTags['UPDATED'] = $this->getUpdated();
    $listTags['OWNER'] = $this->getOwner();
    $listTags['EDITOR'] = $this->getEditor();

    $pagerLimit = "&amp;PAGER_limit=" . $this->pager->limit;
    if($this->order == 0) {
      $listTags['ASC_ORDER_LINK'] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=view&amp;PHPWS_Album_order=1$pagerLimit\">" . $_SESSION['translate']->it("Oldest") . "</a>";
      $listTags['DESC_ORDER_LINK'] = $_SESSION['translate']->it("Newest");
      $order = " ORDER BY created DESC";
    } else {
      $listTags['DESC_ORDER_LINK'] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=view&amp;PHPWS_Album_order=0$pagerLimit\">" . $_SESSION['translate']->it("Newest") . "</a>";
      $listTags['ASC_ORDER_LINK'] = $_SESSION['translate']->it("Oldest");
      $order = " ORDER BY created ASC";
    }

    if(is_array($photoIds) && (sizeof($photoIds) > 0)) {
      $sql = array();
      $sql[] = "SELECT * FROM ";
      $sql[] = PHPWS_TBL_PREFIX;
      $sql[] = "mod_photoalbum_photos WHERE (";
      
      foreach($photoIds as $photoId) {
	$sql[] = " id='$photoId' OR ";
      }
      $sql = implode("", $sql);
      $sql = substr($sql, 0, strlen($sql)-4) . ")$order";

      $photos = $GLOBALS['core']->query($sql);

      /* Initialize column information */
      for($i = 1; $i != ($this->pager->limit / $i); $i++) $columns = $i + 1;
      $column = 0;

      $navTags = array();
      $navTags['BACK_LINK'] = $this->pager->getBackLink();
      $navTags['SECTION_LINKS'] = $this->pager->getSectionLinks();
      $navTags['FORWARD_LINK'] = $this->pager->getForwardLink();
      $navTags['SECTION_INFO'] = $this->pager->getSectionInfo();
      $navTags['LIMIT_LINKS'] = $this->pager->getLimitLinks(TRUE);
      $navTags['LIMIT_TEXT'] = $_SESSION['translate']->it("Limits");

      $listTags['NAVIGATION'] = PHPWS_Template::processTemplate($navTags, "photoalbum", "photos/navigation.tpl");
      $listTags['LIST_ITEMS'] = NULL;

      while($photo = $photos->fetchrow(DB_FETCHMODE_ASSOC)) {
	$column ++;
	$cellTags = array();
	$cellTags['ID'] = $photo['id'];
	$cellTags['ALBUM'] = $photo['album'];
	$cellTags['TNNAME'] = $photo['tnname'];
	$cellTags['TNWIDTH'] = $photo['tnwidth'];
	$cellTags['TNHEIGHT'] = $photo['tnheight'];
	$cellTags['SHORT'] = /*htmlentities(*/$photo['label']/*)*/;

	$links = array();

	if($_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
	  $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_id=$id&amp;PHPWS_Photo_op=edit&amp;PHPWS_Photo_id={$photo['id']}\">" . $_SESSION['translate']->it("Edit") . "</a>";
	}

	if($_SESSION['OBJ_user']->allow_access("photoalbum", "delete_photo")) {
	  $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_id=$id&amp;PHPWS_Photo_op=delete&amp;PHPWS_Photo_id={$photo['id']}\">" . $_SESSION['translate']->it("Delete") . "</a>";	
	}

	$cellTags['EDIT_DELETE_LINKS'] = implode("&#160;|&#160;", $links);

	if($column == 1) {
	  $listTags['LIST_ITEMS'] .= "<tr>" . PHPWS_Template::processTemplate($cellTags, "photoalbum", "photos/cell.tpl");
	} else if($column == $columns) {
	  $listTags['LIST_ITEMS'] .= PHPWS_Template::processTemplate($cellTags, "photoalbum", "photos/cell.tpl") . "</tr>\n";
	  $column = 0;
	} else {
	  $listTags['LIST_ITEMS'] .= PHPWS_Template::processTemplate($cellTags, "photoalbum", "photos/cell.tpl");
	}
      }

      if($column != 0) {
	$listTags['LIST_ITEMS'] .= "<td colspan=\"" . ($columns - $column) . "\">&#160;</td></tr>";
      }
    } else {
      $listTags['LIST_ITEMS'] = "<tr><td colspan=\"$columns\">" . $_SESSION['translate']->it("No photos were found for this album.") . "</td></tr>";

      if($_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
	$listTags['LIST_ITEMS'] .= "<tr><td colspan=\"$columns\"><a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=new\">" . $_SESSION['translate']->it("Add a photo") . "</a></td></tr>";
      }
    }

    $this->pager->cleanUp();

    return PHPWS_Template::processTemplate($listTags, "photoalbum", "photos/list.tpl");
  }

  function _new() {
    $this->photo = new PHPWS_Photo;
    $_REQUEST['PHPWS_Photo_op'] = "edit";
  }

  function _batchAdd($numForms=5) {
    $id = $this->getId();
    $authorize = TRUE;
    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
      $message = $_SESSION['translate']->it("You do not have permission to add photos within an album.");
      $authorize = FALSE;
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_edit()", $message, "continue", PHOTOALBUM_DEBUG_MODE);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }

    if(isset($_REQUEST['Num_forms']) && ($_REQUEST['Num_forms'] <= PHOTOALBUM_MAX_UPLOADS)) {
      $numForms = $_REQUEST['Num_forms'];

      $orginSize = sizeof($this->_batch) + 1;
      for($i = $numForms; $i <= $orginSize; $i++) {
	if(isset($this->_batch[$i]))
	  unset($this->_batch[$i]);
      }
    }
   
    $form = new EZform("PHPWS_Batch_add");

    for($i = 1; $i <= PHOTOALBUM_MAX_UPLOADS; $i++) {
      $uploads[$i] = $i;
    }

    $form->add("Num_forms", "select", $uploads);
    $form->setMatch("Num_forms", $numForms);
    $form->add("Photos_update", "submit", $_SESSION['translate']->it("Update"));
    $form->add("Photos_save", "submit", $_SESSION['translate']->it("Save"));
    $form->add("module", "hidden", "photoalbum");
    $form->add("PHPWS_Album_op", "hidden", "batchSave");
    $form->setEncode(TRUE);
     
    $formTags = array();
    $formTags = $form->getTemplate();

    $formTags['BACK_LINK'] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=view\">" . $_SESSION['translate']->it("Back to album") . "</a>";
    $formTags['NUM_FORMS_TEXT'] = $_SESSION['translate']->it("Number of photos (select before uploading)");
    $formTags['PHOTO_FORMS'] = "";

    $options = array(0=>$_SESSION['translate']->it("Visible"),
		     1=>$_SESSION['translate']->it("Hidden"));

    $tags = array();
    $tags['PHOTO_TEXT'] = $_SESSION['translate']->it("Upload Image");
    $tags['SHORT_TEXT'] = $_SESSION['translate']->it("Short");
    $tags['HIDDEN_TEXT'] = $_SESSION['translate']->it("Activity");
    $tags['PHOTO_NUMBER_TEXT'] = $_SESSION['translate']->it("Photo Number");

    for($i = 0; $i < $numForms; $i++) {      
      if(isset($this->_batch[$i]['error'])) {
	$tags['ERROR'] = $this->_batch[$i]['error'];
	$this->_batch[$i]['error'] = NULL;
	unset($this->_batch[$i]['error']); 
      } else {
	$tags['ERROR'] = NULL;
      }

      $tags['PHOTO_NUMBER'] = $i+1;
      $tags['PHOTO_UPLOAD'] = NULL;
      if(isset($this->_batch[$i]['name'])) {
	$tags['PHOTO_UPLOAD'] = array();
	$tags['PHOTO_UPLOAD'][] = "<img src=\"images/photoalbum/" . $this->getId() . "/";
	$tags['PHOTO_UPLOAD'][] = $this->_batch[$i]['tnname'] . "\"";
	$tags['PHOTO_UPLOAD'][] = " width=\"" . $this->_batch[$i]['tnwidth'] . "\"";
	$tags['PHOTO_UPLOAD'][] = " height=\"" . $this->_batch[$i]['tnheight'] . "\"";
	$tags['PHOTO_UPLOAD'][] = " border=\"0\" />";
	$tags['PHOTO_UPLOAD'] =  implode("", $tags['PHOTO_UPLOAD']);
      }


      $tags['PHOTO_UPLOAD'] .= "<br /><input type=\"file\" name=\"Photo[]\" />";
      if(isset($this->_batch[$i]['label'])) {
	$label = $this->_batch[$i]['label'];
      } else {
	$label = NULL;
      }
      $tags['SHORT'] = "<input type=\"text\" name=\"Photo_short[]\" size=\"25\" maxsize=\"255\" value=\"$label\" />";

      $tags['HIDDEN'] = "<select name=\"Photo_hidden[]\">\n";
      foreach($options as $key => $value) {
	if(isset($this->_batch[$i]['hidden']) && ($key == $this->_batch[$i]['hidden'])) {
	  $tags['HIDDEN'] .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
	} else {
	  $tags['HIDDEN'] .= "<option value=\"$key\">$value</option>\n";
	}
      }
      $tags['HIDDEN'] .= "</select>";
      $formTags['PHOTO_FORMS'] .= PHPWS_Template::processTemplate($tags, "photoalbum", "albums/photoForm.tpl");
    }

    return PHPWS_Template::processTemplate($formTags, "photoalbum", "albums/batchAdd.tpl");
  }

  function _batchSave() {
    if(isset($_REQUEST['Photos_update'])) {
      $_REQUEST['PHPWS_Album_op'] = "batch";
      $this->action();
      return;
    }

    if(isset($_REQUEST['Num_forms']) && ($_REQUEST['Num_forms'] <= PHOTOALBUM_MAX_UPLOADS)) {
      $numForms = $_REQUEST['Num_forms'];

      $orginSize = sizeof($this->_batch) + 1;
      for($i = $numForms; $i <= $orginSize; $i++) {
	if(isset($this->_batch[$i]))
	  unset($this->_batch[$i]);
      }
    }

    $error = FALSE;

    foreach($_FILES['Photo']['error'] as $key => $value) {
      $this->_batch[$key]['hidden'] = $_REQUEST['Photo_hidden'][$key];

      if(isset($_REQUEST['Photo_short'][$key])) {
	$this->_batch[$key]['label'] = PHPWS_Text::parseInput($_REQUEST['Photo_short'][$key]);
      }

      if($value == 0) {
	if(isset($this->_batch[$key]['name'])) {
	  if(is_file(PHOTOALBUM_DIR . $this->getId() . "/" . $this->_batch[$key]['name'])) {
	    unlink(PHOTOALBUM_DIR . $this->getId() . "/" . $this->_batch[$key]['name']);

	  }
	}

	if(isset($this->_batch[$key]['tnname'])) {
	  if(is_file(PHOTOALBUM_DIR . $this->getId() . "/" . $this->_batch[$key]['tnname'])) {
	    unlink(PHOTOALBUM_DIR . $this->getId() . "/" . $this->_batch[$key]['tnname']);
	  }
	}

	$name = PHPWS_File::nameToSafe($_FILES['Photo']['name'][$key]);
	$file = PHOTOALBUM_DIR . $this->getId() . "/" . $name;
	if(is_file($file)) {
	  $name = time() . "_" . $name;
	  $file = PHOTOALBUM_DIR . $this->getId() . "/" . $name;
	}

	@move_uploaded_file($_FILES['Photo']['tmp_name'][$key], $file);
	if(is_file($file)) {
	  chmod($file, 0644);
	  $info = @getimagesize($file);

	  include(PHPWS_SOURCE_DIR.'conf/allowedImageTypes.php');

	  if(in_array($_FILES['Photo']['type'][$key], $allowedImageTypes)) {
	    $this->_batch[$key]['name'] = $name;
	    $this->_batch[$key]['type'] = $_FILES['Photo']['type'][$key];
	    $this->_batch[$key]['width'] = $info[0];
	    $this->_batch[$key]['height'] = $info[1];
	    
	    if($info[2] == 2 || $info[2] == 3) {
	      $dir = PHOTOALBUM_DIR . $this->getId() . "/";
	      $thumbnail = PHPWS_File::makeThumbnail($this->_batch[$key]['name'], $dir, $dir, PHOTOALBUM_TN_WIDTH, PHOTOALBUM_TN_HEIGHT);
	      if(is_file($dir . $thumbnail[0])) {
		$this->_batch[$key]['tnname'] = $thumbnail[0];
		$this->_batch[$key]['tnwidth'] = $thumbnail[1];
		$this->_batch[$key]['tnheight'] = $thumbnail[2];
	      }
	    }
	  } else {
	    $error = TRUE;
	    $this->_batch[$key]['error'] = $_SESSION['translate']->it("The image uploaded was not an allowed image type.");
	  }
	} else {
	  $error = TRUE;
	  $this->_batch[$key]['error'] = $_SESSION['translate']->it("There was a problem uploading the specified file.");
	}

	if(!(strlen($this->_batch[$key]['label']) > 0)) {
	  $error = TRUE;
	  $this->_batch[$key]['error'] = $_SESSION['translate']->it("A file was uploaded but no short description was given.");
	}
      } else if($value == 4) {
	if(!(isset($this->_batch[$key]['name']) && (strlen($this->_batch[$key]['label']) > 0))) {
	  $error = TRUE;
	  if(strlen($this->_batch[$key]['label']) > 0) {
	    $this->_batch[$key]['error'] = $_SESSION['translate']->it("A short description was added but no file was uploaded.");
	  } else if(isset($this->_batch[$key]['name'])) {
	    $this->_batch[$key]['error'] = $_SESSION['translate']->it("A file was uploaded but no short description was given.");
	  } else {
	    $this->_batch[$key]['error'] = $_SESSION['translate']->it("No file was uploaded and no short description was given.");
	  }
	}
      } else {
	$error = TRUE;
	if($value != 4) {
	  $this->_batch[$key]['error'] = $_SESSION['translate']->it("The uploaded file exceeded the max file size allowed.");
	}
      }
    }

    if($error) {
      $_REQUEST['PHPWS_Album_op'] = "batch";
      $this->action();
      return;
    } else {
      $count = 0;

      foreach($this->_batch as $value) {
	if(is_array($value) && (sizeof($value) > 0)) {
	  $value['album'] = $this->getId();
	  $value['created'] = time() + $count;
	  $value['updated'] = time() + $count;
	  $value['ip'] = $_SERVER['REMOTE_ADDR'];
	  $value['owner'] = $_SESSION['OBJ_user']->username;
	  $value['editor'] = $_SESSION['OBJ_user']->username;
	  $value['approved'] = TRUE;
	  
	  $GLOBALS['core']->sqlInsert($value, "mod_photoalbum_photos");
	  $count ++;
	}
      }

      $_SESSION['PHPWS_AlbumManager']->updateAlbumList($this->getId());

      $this->_batch = array();
      $_REQUEST['PHPWS_Album_op'] = "view";
      $this->PHPWS_Album($this->_id);
      $this->action();
    }
  }

  function _missingDesc() {
    if($_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
      $_SESSION['PHPWS_AlbumManager']->setClass("PHPWS_Photo");
      $_SESSION['PHPWS_AlbumManager']->setSort("album='" . $this->getId() . "'");
      $viewTags['DESCRIPTIONS_TEXT'] = $_SESSION['translate']->it("The following photos are missing descriptions.");
      $viewTags['DESCRIPTIONS'] = $_SESSION['PHPWS_AlbumManager']->getList("description", $_SESSION['translate']->it("Photos"), FALSE);
      $viewTags['BACK_LINK'] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=view\">" . $_SESSION['translate']->it("Back to album") . "</a>";

      return PHPWS_Template::processTemplate($viewTags, "photoalbum", "description/view.tpl");
    }
  }


  function _slideShow() {
    if($_SESSION['OBJ_user']->js_on && (!isset($_REQUEST["SS_mode"]) ||
                                        (isset($_REQUEST["SS_mode"]) &&
					 $_REQUEST["SS_mode"] != "nojsmode"))) {
      $this->_orderIds();
      return SlideShow::play($this->photos);
    } else {
      if(!isset($_SESSION["NO_JS_SLIDESHOW"]) || $_SESSION["NO_JS_SLIDESHOW"]->getCount() != count($this->photos) || 
         $_SESSION["NO_JS_SLIDESHOW"]->album_id != $this->_id)
	$_SESSION["NO_JS_SLIDESHOW"] = new NoJSSlideShow($this->photos, $this->_id);
      return $_SESSION["NO_JS_SLIDESHOW"]->play();
    }
  }

  function action() {
    if(PHPWS_Message::isMessage($_SESSION['PHPWS_AlbumManager']->message)) {
      $_SESSION['PHPWS_AlbumManager']->message->display();
      $_SESSION['PHPWS_AlbumManager']->message = null;
    }

    if(isset($_REQUEST['PHPWS_Photo_id']) && is_numeric($_REQUEST['PHPWS_Photo_id'])) {
      if(!isset($this->photo) || ($this->photo->getId() != $_REQUEST['PHPWS_Photo_id'])) {
	$this->photo = new PHPWS_Photo($_REQUEST['PHPWS_Photo_id']);
      }
    } 

    if(isset($_REQUEST['PHPWS_Album_op'])) {
      switch($_REQUEST['PHPWS_Album_op']) {
      case "new":
	$this->_new();
	break;
	
      case "edit":
	$title = $_SESSION['translate']->it("Edit Album");
	$content = $this->_edit();
	break;
	
      case "save":
	$this->_save();
	break;
	
      case "delete":
	$this->_delete();
	break;
	
      case "view":
	$title = $_SESSION['translate']->it("Photo Album") . ": " . $this->getLabel();
	$content = $this->_view();
	break;
	
      case "batch":
	$title = $_SESSION['translate']->it("Batch Add Photos");
	$content = $this->_batchAdd();
	break;
	
      case "batchSave":
	$this->_batchSave();
	break;
	
      case "slideShow":
	$title = $_SESSION['translate']->it("Slide Show");
	$content = $this->_slideShow();
	break;

      case "desc":
	$title = $_SESSION['translate']->it("Missing Descriptions");
	$content = $this->_missingDesc();
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