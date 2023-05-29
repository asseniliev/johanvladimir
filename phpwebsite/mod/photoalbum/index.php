<?php

/**
 * @version $Id: index.php,v 1.12 2003/11/20 22:03:49 steven Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

$CNT_photoalbum['content'] = NULL;

if(!isset($_SESSION['PHPWS_AlbumManager']) && (isset($_REQUEST['module']) && $_REQUEST['module'] == "photoalbum")) {
  $_SESSION['PHPWS_AlbumManager'] = new PHPWS_AlbumManager;
}

$_SESSION['PHPWS_AlbumManager']->action();

if(isset($_SESSION['PHPWS_AlbumManager']->album)) {
  $_SESSION['PHPWS_AlbumManager']->album->action();
} 

if(isset($_SESSION['PHPWS_AlbumManager']->album->photo)) {
  $_SESSION['PHPWS_AlbumManager']->album->photo->action();
}

if(isset($_REQUEST['module']) && ($_REQUEST['module'] != "photoalbum")) {
  $_SESSION['PHPWS_AlbumManager'] = NULL;
  unset($_SESSION['PHPWS_AlbumManager']);
}

?>