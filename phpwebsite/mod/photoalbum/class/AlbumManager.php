<?php

/**
 * @version $Id: AlbumManager.php,v 1.20 2004/08/19 20:07:15 steven Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

require_once(PHPWS_SOURCE_DIR . "core/Manager.php");
require_once(PHPWS_SOURCE_DIR . "core/Error.php");
require_once(PHPWS_SOURCE_DIR . "core/Message.php");

require_once(PHPWS_SOURCE_DIR . "mod/photoalbum/class/Album.php");

include(PHPWS_SOURCE_DIR . "mod/photoalbum/conf/config.php");

class PHPWS_AlbumManager extends PHPWS_Manager {

  /**
   * Stores the ids of the photos for the current album being viewed
   *
   * @var    PHPWS_Album
   * @access public
   */
  var $album = NULL;

  /**
   * Stores the current error that has occured in the photoalbum
   *
   * @var    PHPWS_Error
   * @access public
   */
  var $error = NULL;

  /**
   * Stores the current message to display for the photoalbum
   *
   * @var    PHPWS_Message
   * @access public
   */
  var $message = NULL;

  function PHPWS_AlbumManager() {
    $this->setModule("photoalbum");
    $this->setRequest("PHPWS_AlbumManager_op");
    $this->init();
  }

  function _list() {
    $GLOBALS['CNT_photoalbum']['title'] = $_SESSION['translate']->it("Photo Albums"); 

    if(!function_exists('imagecreate')) {
      $gdMessage = "<div style=\"color:#ff0000;\">Error!</div>The photoalbum module requires the GD library functions.
                    If you are getting this error then your GD libs are missing.
                    Please contact your systems administrator to resolve this issue.
                    With versions of php prior to 4.3.0 you must compile the GD libs into your build of php (--with-gd[=DIR], where DIR is the GD base install directory)
                    Php 4.3.0 and greater have the GD libs already built in.
                    For more information please refer to the
                    <a href=\"http://www.php.net/manual/en/ref.image.php\" target=\"_blank\">PHP Image Function Manual</a><br /><br />";
      $GLOBALS['CNT_photoalbum']['content'] = $gdMessage;
    }

    $links = array();

    if($_SESSION['OBJ_user']->allow_access("photoalbum", "add_album")) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=new\">" . $_SESSION['translate']->it("New Album") . "</a>";
    }

    $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list\">" . $_SESSION['translate']->it("List Albums") . "</a>";
    
    $GLOBALS['CNT_photoalbum']['content'] .= "<div align=\"right\">" . implode("&#160;|&#160;", $links) . "</div><hr />";

    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
      $this->setSort("hidden='0'");
    }

    $this->setOrder("UPDATED DESC");

    $GLOBALS['CNT_photoalbum']['content'] .= $this->getList("albums", $_SESSION['translate']->it("Photo Albums"), FALSE);
    $this->setSort(NULL);
  }

  function _new() {
    $this->album = new PHPWS_Album;
    $_REQUEST['PHPWS_Album_op'] = "edit";
  }

  function _accessDenied() {
    if(PHPWS_Error::isError($this->error)) {
      $this->error->message("CNT_photoalbum", $_SESSION['translate']->it("Access Denied!"));
      $this->error = NULL;
    } else {
      $message = $_SESSION['translate']->it("Access denied function was called without a proper error initialized.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_AlbumManager::_accessDenied()", $message, "exit", 1);
      $error->message();
    }
  }

  function updateAlbumList($albumId) {
    $sql = "SELECT label, tnname, tnwidth, tnheight FROM ".PHPWS_TBL_PREFIX."mod_photoalbum_photos WHERE album='$albumId' ORDER BY updated DESC LIMIT 1";
    $result = $GLOBALS['core']->getAll($sql);

    if(isset($result[0])) {
      $image[] = "<img src=\"images/photoalbum/";
      $image[] = $albumId . "/";
      $image[] = $result[0]['tnname'] . "\" ";
      $image[] = "width=\"" . $result[0]['tnwidth'] . "\" ";
      $image[] = "height=\"" . $result[0]['tnheight'] . "\" ";
      $image[] = "alt=\"" . $result[0]['label'] . "\" ";
      $image[] = "title=\"" . $result[0]['label'] . "\" ";
      $image[] = "border=\"0\" />";
      $image = implode("", $image);
      
      $time = time();
      $sql = "UPDATE ".PHPWS_TBL_PREFIX."mod_photoalbum_albums SET image='$image', updated='$time' WHERE id='$albumId'";
      $GLOBALS['core']->query($sql);
    }
  }

  function action() {
    if(PHPWS_Message::isMessage($this->message)) {
      $this->message->display();
      $this->message = null;
    }

    if(isset($_REQUEST['PHPWS_Album_id']) && is_numeric($_REQUEST['PHPWS_Album_id'])) {
      if(!isset($this->album) || ($this->album->getId() != $_REQUEST['PHPWS_Album_id'])) {
	$this->album = new PHPWS_Album($_REQUEST['PHPWS_Album_id']);
      }
    } 

    if(isset($_REQUEST['PHPWS_AlbumManager_op'])) {
      switch($_REQUEST['PHPWS_AlbumManager_op']) {
      case "list":
	$this->_list();
	break;

      case "new":
	$this->_new();
	break;
	
      case "accessDenied":
	$this->_accessDenied();
	break;
      }
    }
  }
}

?>