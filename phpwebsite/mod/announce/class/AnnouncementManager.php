<?php

require_once(PHPWS_SOURCE_DIR . 'core/Form.php');
require_once(PHPWS_SOURCE_DIR . 'core/List.php');

require_once(PHPWS_SOURCE_DIR . 'mod/announce/class/Announcement.php');
require_once(PHPWS_SOURCE_DIR . 'mod/fatcat/class/CategoryView.php');
require_once(PHPWS_SOURCE_DIR . 'mod/help/class/CLS_help.php');

/**
 * This is the control class for the Announce module.
 *
 * @version $Id: AnnouncementManager.php,v 1.40 2005/03/03 19:45:18 matt Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
class PHPWS_AnnouncementManager {

  var $_numHome;
  var $_numPast;
  var $_showCurrent;
  var $_showPast;
  var $_allowedImageTypes;
  var $_list;

  function PHPWS_AnnouncementManager() {
    $allowedImageTypes = NULL;

    include(PHPWS_SOURCE_DIR . 'mod/announce/conf/config.php');
    $this->_allowedImageTypes = explode(',', $allowedImageTypes);
    foreach($this->_allowedImageTypes as $key=>$type)
      $this->_allowedImageTypes[$key] = trim($type);

    $result = $GLOBALS['core']->sqlSelect('mod_announce_settings');

    $this->_numHome = $result[0]['numHome'];
    $this->_numPast = $result[0]['numPast'];
    $this->_showCurrent = $result[0]['showCurrent'];
    $this->_showPast = $result[0]['showPast'];
  }

  function menu() {
    $links = array();

    if($_SESSION['OBJ_user']->allow_access('announce', 'edit_announcement')) {
      $links[] = '<a href="index.php?module=announce&amp;ANN_op=new">'.$_SESSION['translate']->it('New Announcement').'</a>';
    }

    $links[] = '<a href="index.php?module=announce&amp;ANN_op=list">'.$_SESSION['translate']->it('List Announcements').'</a>';

    if($_SESSION['OBJ_user']->allow_access('announce', 'modify_settings')) {
      $links[] = '<a href="index.php?module=announce&amp;ANN_op=settings">'.$_SESSION['translate']->it('Settings').'</a>';
    }

    $tags = array();
    $tags['LINKS'] = implode('&#160;|&#160;', $links);
    $tags['HELP'] = CLS_help::show_link('announce', 'admin_menu');

    $GLOBALS['CNT_announce']['content'] = PHPWS_Template::processTemplate($tags, 'announce', 'menu.tpl');
  }

  function showAnnouncements() {
    if(!$this->_showCurrent)
      return;

    $sql = "SELECT * FROM " . PHPWS_TBL_PREFIX .
       "mod_announce WHERE expiration > '" . date('Y-m-d H:i:s') .
       "' AND poston<='" . date('Y-m-d H:i:s') .
       "' AND approved='1' ORDER BY dateCreated DESC LIMIT " . $this->_numHome;
    $result = $GLOBALS['core']->getAll($sql);

    if($result) {
      foreach($result as $row) {
	$current_announcement = new PHPWS_Announcement($row['id']);
	$current_announcement->view('small');
      }
    }
  }// END FUNC showAnnouncements()

  function listAnnouncements() {
    $listTags = array();
    $listTags['TITLE'] = $_SESSION['translate']->it('Current announcements');
    $listTags['SUBJECT_LABEL'] = $_SESSION['translate']->it('Subject');
    $listTags['DATECREATED_LABEL'] = $_SESSION['translate']->it('Date');
    $listTags['USERCREATED_LABEL'] = $_SESSION['translate']->it('Posted by');
    $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it('Actions');

    if(!isset($this->_list)) {
      $this->_list = new PHPWS_List;
    }

    $this->_list->setModule('announce');
    $this->_list->setClass('PHPWS_Announcement');
    $this->_list->setTable('mod_announce');
    $this->_list->setDbColumns(array('active', 'subject', 'dateCreated', 'userCreated'));
    $this->_list->setListColumns(array('Subject', 'DateCreated', 'UserCreated', 'Actions'));
    $this->_list->setName('list');

    if($_SESSION['OBJ_user']->allow_access('announce')) {
      $this->_list->setOp('ANN_op=list');
    } else {
      $this->_list->setOp('ANN_user_op=list');
    }

    $this->_list->setPaging(array('limit'=>10, 'section'=>TRUE, 'limits'=>array(5,10,20,50), 'back'=>'&#60;&#60;', 'forward'=>'&#62;&#62;', 'anchor'=>FALSE));
    $this->_list->setExtraListTags($listTags);
    $this->_list->setWhere('approved=\'1\'');
    $this->_list->setOrder('dateCreated DESC');    

    $GLOBALS['CNT_announce']['content'] .= $this->_list->getList();
  }// END FUNC listAnnouncements()

  function getSettings() {
    $tags = array();
    $tags['TITLE'] = $_SESSION['translate']->it('Announcement Settings');
    $tags['SHOW_ANN_LABEL'] = $_SESSION['translate']->it('Show Announcements');
    $tags['SHOW_ANN'] = PHPWS_Form::formCheckBox('ANN_showCurrent', 1, $this->_showCurrent);
    $tags['SHOW_PAST_ANN_LABEL'] = $_SESSION['translate']->it('Show Past Announcements');
    $tags['SHOW_PAST_ANN'] = PHPWS_Form::formCheckBox('ANN_showPast', 1, $this->_showPast);
    $tags['NUM_HOME_ANN_LABEL'] = $_SESSION['translate']->it('Number of announcements shown on home page');
    $tags['NUM_HOME_ANN'] = PHPWS_Form::formTextField('ANN_numHome', $this->_numHome, 3);
    $tags['NUM_PAST_ANN_LABEL'] = $_SESSION['translate']->it('Number of past announcements shown');
    $tags['NUM_PAST_ANN'] = PHPWS_Form::formTextField('ANN_numPast', $this->_numPast, 3);

    $tags['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it('Save Settings'));
    $tags['UPDATE_FATCAT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it('Synchronize With FatCat'), 'update_fatcat');
    $tags['UPDATE_FATCAT'] .= CLS_help::show_link('announce', 'sync_fatcat');

    $elements[0] = PHPWS_Form::formHidden('module', 'announce');
    $elements[0] .= PHPWS_Form::formHidden('ANN_op', 'save_settings');
    $elements[0] .= PHPWS_Template::processTemplate($tags, 'announce', 'settings.tpl');

    $content = PHPWS_Form::makeForm('announce_settings', 'index.php', $elements);

    $GLOBALS['CNT_announce']['content'] .= $content;
  }

  function syncWithFatCat() {
      $all_announcements = $GLOBALS['core']->sqlSelect('mod_announce');
      $today = time();

      foreach($all_announcements as $announce) {	
	$year   = substr($announce['poston'], 0, 4);
	$month  = substr($announce['poston'], 5, 2);
	$day    = substr($announce['poston'], 8, 2);
	$poston = mktime(0, 0, 0, $month, $day, $year); 

	$year       = substr($announce['expiration'], 0, 4);
	$month      = substr($announce['expiration'], 5, 2);
	$day        = substr($announce['expiration'], 8, 2);
	$expiration = mktime(0, 0, 0, $month, $day, $year); 

	if($poston >= $today) {
	  $_SESSION['OBJ_fatcat']->deactivate($announce['id'], 'announce');
	}
	if($expiration <= $today) {
	  $_SESSION['OBJ_fatcat']->deactivate($announce['id'], 'announce');
	}
      }

  }

  function setSettings() {
    if(isset($_REQUEST['update_fatcat'])) {
      $this->syncWithFatCat();

      $content = $_SESSION['translate']->it('Successfully synchronized announcements and fatcat.');      
      $GLOBALS['CNT_announce']['content'] .= $content;
    } else {
      if(isset($_POST['ANN_showCurrent']))
	$this->_showCurrent = $_POST['ANN_showCurrent'];
      else
	$this->_showCurrent = 0;

      if(isset($_POST['ANN_showPast']))
	$this->_showPast = $_POST['ANN_showPast'];
      else
	$this->_showPast = 0;

      if(isset($_POST['ANN_numHome']))
	$this->_numHome = $_POST['ANN_numHome'];
      else
	$this->_numHome = '';

      if(isset($_POST['ANN_numPast']))
	$this->_numPast = $_POST['ANN_numPast'];
      else
	$this->_numPast = '';

      $data['showCurrent'] = $this->_showCurrent;
      $data['showPast'] = $this->_showPast;
      $data['numHome'] = $this->_numHome;
      $data['numPast'] = $this->_numPast;
      
      $GLOBALS['core']->sqlUpdate($data, 'mod_announce_settings');
      
      $content = $_SESSION['translate']->it('Your settings have successfully been saved!');      
      $GLOBALS['CNT_announce']['content'] .= $content;
    }
  }

  function isAllowedImageType($type) {
    return in_array($type, $this->_allowedImageTypes);
  }

  function search($where) {
    $sql = 'SELECT id, subject FROM ' . $GLOBALS['core']->tbl_prefix . 'mod_announce ' . $where;
    $sql .= 'AND active=1';
    $result = $GLOBALS['core']->query($sql);

    $array = array();
    if($result) {
      while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	$array[$row['id']] = $row['subject'];
      }
    }

    return $array;
  }

  function userMenu() {
    $links = array();

    $links[] = '<a href="index.php?module=announce&amp;ANN_user_op=categories">'.$_SESSION['translate']->it('Categories').'</a>';
    $links[] = '<a href="./index.php?module=announce&amp;ANN_user_op=submit_announcement">'.$_SESSION['translate']->it('Submit News').'</a>';

    $tags = array();
    $tags['LINKS'] = implode('&#160;|&#160;', $links);

    $GLOBALS['CNT_announce']['content'] = PHPWS_Template::processTemplate($tags, 'announce', 'userMenu.tpl');
  }

  function categories() {
    $this->userMenu();
    
    $categoryView = new CategoryView;
    $categoryView->setModule('announce');
    $categoryView->setOp('ANN_user_op=categories');
    if(!isset($_REQUEST['category'])) {
      $content = $categoryView->categoriesMainListing();
    } else {
      $content = $categoryView->categoriesSCView();
    }
    
    $GLOBALS['CNT_announce']['content'] .= $content;      
  }
}// END CLASS PHPWS_AnnouncementManager

?>