<?php

require_once (PHPWS_SOURCE_DIR . 'core/Error.php');
require_once (PHPWS_SOURCE_DIR . 'core/Form.php');
require_once (PHPWS_SOURCE_DIR . 'core/Text.php');

require_once(PHPWS_SOURCE_DIR . 'mod/pagemaster/class/Section.php');

/**
 * This is the PHPWS_Page class.  It handles saving, updating, and organization
 * of sections.  It also contains functions that allow this page to be edited
 * and saved.
 *
 * @version $Id: Page.php,v 1.66 2005/03/22 19:34:39 steven Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package PageMaster
 */
class PHPWS_Page {
  /**
   * Database id of this page
   * @var    integer
   * @access private
   */
  var $id;

  /**
   * Title of this page
   * @var    string
   * @access private
   */
  var $title;

  /**
   * Array of sections controlled by this page (key=SECT_id, value=SECT_OBJ)
   * @var    array
   * @access private
   */
  var $sections;

  /**
   * Array denoting the order of the sections within this page (key=order 0,1,2..., value=SECT_id)
   * @var    array
   * @access private
   */
  var $order;

  /**
   * Whether this is a new page or not
   * @var    boolean
   * @access private
   */
  var $new_page;

  /**
   * Whether this page is created in advanced mode or not
   * @var    boolean
   * @access private
   */
  var $advanced;

  /**
   * Whether this page is the main page for this site or not
   * @var    boolean
   * @access private
   */
  var $mainpage;

  /**
   * Name of the page template file
   * @var    string
   * @access public
   */
  var $template;

  /**
   * Whether this page is active or not
   * @var    boolean
   * @access public
   */
  var $active;

  /**
   * username or the user that created this page
   * @var    string
   * @access private
   */
  var $created_username;

  /**
   * username of the lastuser that updated this page
   * @var    string
   * @access private
   */
  var $updated_username;

  /**
   * Date this page was created
   * @var    string
   * @access private
   */
  var $created_date;

  /**
   * Date this page was last updated
   * @var    string
   * @access private
   */
  var $updated_date;

  /**
   * Determines whether or not this page has been approved by an admin
   * @var    boolean
   * @access private
   */ 
  var $approved;

  /**
   * Used to check whether this page has been submitted for approval already
   *
   * @var boolean
   * @access private
   * @see update()
   */
  var $submitted;

  var $_comments;

  var $_anonymous;

  /**
   * Constructor for the PHPWS_Page object. PAGE_id = database id of a saved page.
   *
   * @param  integer $PAGE_id Database id of the page to be constructed.
   * @access public
   */
  function PHPWS_Page($PAGE_id=NULL) {
    if(is_numeric($PAGE_id)) {
      $result = $GLOBALS['core']->sqlSelect('mod_pagemaster_pages', 'id', $PAGE_id);

      if(sizeof($result) != 1) {
	$message = $_SESSION['translate']->it('Invalid page id specified! Page does not exist!');
	$error = new PHPWS_Error('pagemaster', 'PHPWS_Page::PHPWS_Page', $message, 'exit');
	$error->message();
      }

      $this->id = $PAGE_id;
      $this->title = $result[0]['title'];
      $this->order = unserialize($result[0]['section_order']);
      $this->new_page = $result[0]['new_page'];
      $this->advanced = $result[0]['advanced'];
      $this->mainpage = $result[0]['mainpage'];
      $this->template = $result[0]['template'];
      $this->active = $result[0]['active'];
      $this->created_username = $result[0]['created_username'];
      $this->updated_username = $result[0]['updated_username'];
      $this->created_date = $result[0]['created_date'];
      $this->updated_date = $result[0]['updated_date'];
      $this->approved = $result[0]['approved'];
      $this->_comments = $result[0]['comments'];
      $this->_anonymous = $result[0]['anonymous'];
      $this->submitted = TRUE;

      foreach($this->order as $SECT_id) {
	$this->sections[$SECT_id] = new PHPWS_Section($SECT_id);
      }
    } else if(is_array($PAGE_id)) {
	foreach($PAGE_id as $key => $value) {
	  $this->$key = $value;
	}
    } else {
      $this->title = '';
      $this->sections = array();
      $this->order = array();
      $this->new_page = 1;
      $this->template = 'default.tpl';
      $this->advanced = 0;
      $this->mainpage = 0;
      $this->active = 0;
      $this->_comments = 0;
      $this->_anonymous = 0;

      if(isset($_SESSION['OBJ_user']) && $_SESSION['OBJ_user']->allow_access('pagemaster', 'needs_approval')){
	$this->approved = 1;
	$this->submitted = TRUE;
      } else {
	$this->approved = 0;
	$this->submitted = FALSE;
      }

    }
  }// END FUNC PHPWS_Page()

  /**
   * Displays this page to the user.
   *
   * @access public
   */
  function view_page () {
    $template['CONTENT'] = NULL;

    if(isset($this->id) && (($this->active && $this->approved) || $_SESSION['OBJ_user']->allow_access('pagemaster', 'needs_approval'))) {
      $template['PAGE_TITLE'] = $page_title = $this->title;

      if (!$this->active)
	$template['CONTENT'] .= $_SESSION['translate']->it('Page is not currently active') . '.  ';

      if (!$this->approved)
	$template['CONTENT'] .= $_SESSION['translate']->it('Page is currently hidden') . '.';

      $template['CREATED_INFO'] = $_SESSION['translate']->it('Created on [var1] by [var2]', $this->viewDate($this->created_date), $this->created_username);
      $template['UPDATED_INFO'] = $_SESSION['translate']->it('Updated on [var1] by [var2]', $this->viewDate($this->updated_date), $this->updated_username);
      $template['CREATED_DATE'] = $this->viewDate($this->created_date);
      $template['UPDATED_DATE'] = $this->viewDate($this->updated_date);
      $template['CREATED_USER'] = $this->created_username;
      $template['UPDATED_USER'] = $this->updated_username;

      $image = '<img src="./images/print.gif" border="0" width="22" height="20" alt="'
	. $_SESSION['translate']->it('Printable Version') . '"/>';

      if (isset($_REQUEST['module']) && $_REQUEST['module']=='pagemaster')
	PHPWS_Layout::addPageTitle($page_title);

      $template['PRINT_ICON'] = PHPWS_Text::moduleLink($image, 'pagemaster' , array('PAGE_user_op'=>'view_printable', 'PAGE_id'=>$this->id, 'lay_quiet'=>1), TRUE);

      if($_SESSION['OBJ_user']->allow_access('pagemaster', 'edit_pages') &&
	 !isset($_REQUEST['lay_quiet']))
	$template['EDIT'] = '<a href="./index.php?module=pagemaster&amp;MASTER_op=edit_page&amp;PAGE_id=' .
	  $this->id . '">' . $_SESSION['translate']->it('Edit This Page') . '</a>';

      foreach($this->order as $value)
	$template['CONTENT'] .= $this->sections[$value]->view_section();

      if($GLOBALS['core']->moduleExists('comments') && $this->_comments) {
	$_SESSION['PHPWS_CommentManager']->listCurrentComments('pagemaster', $this->id, $this->_anonymous);
      }

    } else {
      $page_title = $_SESSION['translate']->it('Page not viewable');
      $template['CONTENT'] = $_SESSION['translate']->it('Currently this page is not available') . '.';
    }

    $GLOBALS['CNT_pagemaster']['title'] = $this->title;
    $GLOBALS['CNT_pagemaster']['content'] = PHPWS_Template::processTemplate($template, 'pagemaster', 'page/' . $this->template);
    $_SESSION['OBJ_fatcat']->whatsRelated($this->id, 'pagemaster');
  }// END FUNC view_page()

  /**
   * Displays this page in a printable format to the user.
   *
   * The content of this page CAN affect whether this page prints correctly or not.
   *
   * @access public
   */
  function view_printable() {
    if($this->active && $this->approved) {
      $page_title = $this->title;

      echo "<h2>$page_title</h2>";
      PHPWS_Layout::addPageTitle($page_title);
      foreach($this->order as $value) {
	echo $this->sections[$value]->view_section();
      }
    }
  }// END FUNC view_printable()

  function viewDate($date) {
    $info = explode(' ', $date);
    $date = explode('-', $info[0]);
    $time = explode(':', $info[1]);
    return date(PHPWS_DATE_FORMAT . ' ' . PHPWS_TIME_FORMAT, (mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0])+$GLOBALS['core']->datetime->time_dif));
  }


  function get_page(){
    $tmplist = PHPWS_Template::listTemplates('pagemaster', FALSE, 'page');
    if ($tmplist === FALSE){
      $error = new PHPWS_Error('pagemaster', 'set_page', 'Missing PageMaster page template files', 'exit', 1);
      $error->message('CNT_pagemaster');
    }

    foreach ($tmplist as $filename)
      $tplList[$filename] = $filename;

    $form = new EZform;
    $form->add('module', 'hidden', 'pagemaster');
    $form->add('PAGE_op[set_page]', 'hidden', 1);
    $form->add('submit', 'submit', $_SESSION['translate']->it('Go'));
    $form->add('PAGE_title', 'text' , $this->title);
    $form->setSize('PAGE_title', 50);

    $form->add('PAGE_template', 'select', $tplList);
    $form->setMatch('PAGE_template', $this->template);

    $template = $form->getTemplate();

    $template['TITLE'] = $_SESSION['translate']->it('Set Page Options');
    $template['PAGE_TITLE'] .= $_SESSION['OBJ_help']->show_link('pagemaster', 'new_title');
    $template['PAGE_LABEL'] = $_SESSION['translate']->it('Page Title');
    $template['FATCAT_LABEL'] = $_SESSION['translate']->it('Category');
    $template['TEMPLATE_LABEL'] = $_SESSION['translate']->it('Page Template');

    $template['FATCAT'] = $_SESSION['OBJ_fatcat']->showSelect($this->id, 'multiple');

    if($GLOBALS['core']->moduleExists('comments')) {
      $template['COMMENTS_LABEL'] = $_SESSION['translate']->it('Allow Comments?');
      $template['YES_COMMENTS'] = PHPWS_Form::formRadio('PAGE_comments', 1, $this->_comments, NULL, 'Yes');
      $template['NO_COMMENTS'] = PHPWS_Form::formRadio('PAGE_comments', 0, $this->_comments, NULL, 'No');
      $template['ANON_LABEL'] = $_SESSION['translate']->it('Anonymous Posts?');
      $template['ANON_YES'] = PHPWS_Form::formRadio('PAGE_anonymous', 1, $this->_anonymous, NULL, 'Yes');
      $template['ANON_NO'] = PHPWS_Form::formRadio('PAGE_anonymous', 0, $this->_anonymous, NULL, 'No');
    }

    $content = PHPWS_Template::processTemplate($template, 'pagemaster', 'forms/page_edit.tpl');

    $GLOBALS['CNT_pagemaster']['content'] .= $content;
  }

  function set_page(){
    $this->title = $_POST['PAGE_title'];
    $this->template = $_POST['PAGE_template'];

    if (isset($_POST['PAGE_comments'])) {
	$this->_comments = $_POST['PAGE_comments'];
    } else {
	$this->_comments = 0;
    }

    if (isset($_POST['PAGE_anonymous'])) {
	$this->_anonymous = $_POST['PAGE_anonymous'];
    } else {
	$this->_anonymous = 0;
    }

    $query_data['title'] = $this->title;
    $query_data['template'] = $this->template;
    $query_data['comments'] = $this->_comments;
    $query_data['anonymous'] = $this->_anonymous;

    if($this->id) {
      $GLOBALS['core']->sqlUpdate($query_data, 'mod_pagemaster_pages', 'id', $this->id);
    } else {
      $query_data['section_order'] = serialize($this->order);
      $query_data['new_page'] = $this->new_page;
      $query_data['advanced'] = $this->advanced;
      $query_data['mainpage'] = $this->mainpage;
      $query_data['active'] = $this->active;
      $query_data['approved'] = $this->approved;
      $query_data['created_date'] = date('Y-m-d H:i:s');
      $query_data['created_username'] = $_SESSION['OBJ_user']->username;
      $query_data['updated_date'] = date('Y-m-d H:i:s');
      $query_data['updated_username'] = $_SESSION['OBJ_user']->username;
      
      $this->id = $GLOBALS['core']->sqlInsert($query_data, 'mod_pagemaster_pages', FALSE, TRUE);
    }

    $_SESSION['OBJ_fatcat']->saveSelect($this->title,'index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=' . $this->id , $this->id, NULL, 'pagemaster', NULL, NULL, FALSE);
    if($this->active) {
      PHPWS_Fatcat::activate($this->id, 'pagemaster');
    } else {
      PHPWS_Fatcat::deactivate($this->id, 'pagemaster');      
    }

    $this->title = stripslashes($this->title);
  }


  /**
   * Sets the title in this page as well as in the database based on user input from get_title().
   *
   * @access public
   */
  function set_title () {
    $this->title = $_POST['PAGE_title'];

    $query_data['title'] = PHPWS_Text::addslashes($this->title);

    if($this->id) {
      $GLOBALS['core']->sqlUpdate($query_data, 'mod_pagemaster_pages', 'id', $this->id);
    } else {
      $query_data['section_order'] = serialize($this->order);
      $query_data['new_page'] = $this->new_page;
      $query_data['advanced'] = $this->advanced;
      $query_data['mainpage'] = $this->mainpage;
      $query_data['active'] = $this->active;
      $query_data['approved'] = $this->approved;
      $query_data['created_date'] = date('Y-m-d H:i:s');
      $query_data['created_username'] = $_SESSION['OBJ_user']->username;
      $query_data['updated_date'] = date('Y-m-d H:i:s');
      $query_data['updated_username'] = $_SESSION['OBJ_user']->username;

      $this->id = $GLOBALS['core']->sqlInsert($query_data, 'mod_pagemaster_pages', FALSE, TRUE);
    }

  }// END FUNC set_title()

  /**
   * Displays this page in a format that allows the user to make edits to the content. SECT_index
   * id provided to specify editing of a specific section within this page.
   *
   * @param  integer $SECT_id Database id of the section within this page to be edited.
   * @access public
   */
  function edit_page ($SECT_id = NULL) {
    $myelements[0] = PHPWS_Form::formHidden('module', 'pagemaster');
    $myelements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it('Edit Page Settings'),
      'PAGE_op[get_page]') . $_SESSION['OBJ_help']->show_link('pagemaster', 'edit_title');

    $GLOBALS['CNT_pagemaster']['title'] = $this->title;
    $GLOBALS['CNT_pagemaster']['content'] = '<table width="100%" border="0"><tr><td align="left">' .
      PHPWS_Form::makeForm('PAGE_edit_title', 'index.php', $myelements, 'post', 0, 0). '</td>';

    $GLOBALS['CNT_pagemaster']['content'] .= '<td align="right">' . '<a href="index.php?module=pagemaster&amp;MASTER_op=main_menu">' .
      $_SESSION['translate']->it('Back to List Pages') . '</a></td></tr></table><hr />';

    if($_SESSION['OBJ_user']->js_on) {
      $js = "
function verify() {
   var themessage = \"\";
   var error = \"Make sure you save all your sections before saving the page.\";

   section = document.getElementsByName('SECT_edit')[0];
   form = document.getElementsByName('PAGE_save')[0]

   if (section.SECT_title.value!=\"\") {
      themessage = error;
   }
   if (section.SECT_text.value!=\"\") {
      themessage = error;
   }
   if (section.SECT_image.value!=\"\") {
      themessage = error;
   }
   if (section.SECT_alt.value!=\"\") {
      themessage = error;
   }
   if (section.SECT_image_url.value!=\"\") {
      themessage = error;
   }
   if (themessage == \"\") {
      form.submit();
   }
   else {
      alert(themessage);
      return false;
   }
}";

      $_SESSION['OBJ_layout']->addJavaScript($js);
    }

    foreach($this->order as $value) {
      if($SECT_id == $value) {
	if(isset($_SESSION['SES_PM_error'])) {
	  $_SESSION['SES_PM_section']->error();
	}

	$_SESSION['SES_PM_section'] = $this->sections[$value];
	$_SESSION['SES_PM_section']->edit_section();
      } else {
	$GLOBALS['CNT_pagemaster']['content'] .= $this->sections[$value]->view_section(1);
      }
    }

    if(!$SECT_id) {
      if(isset($_SESSION['SES_PM_error'])) {
	$_SESSION['SES_PM_section']->error();
      }

      $_SESSION['SES_PM_section'] = new PHPWS_Section;
      $_SESSION['SES_PM_section']->edit_section();
    }

    if(sizeof($this->sections)) {
      if(!$this->new_page) {
	/* Begin creating "Page Information" box */
	$content = '<b>' . $_SESSION['translate']->it('Page Information') .
	  $_SESSION['OBJ_help']->show_link('pagemaster', 'page_info') . '</b><br /><br />';

	$content .= '<b><i>' . $_SESSION['translate']->it('Created By') .
	  ':</i></b> ' . $this->created_username . ' <b><i>' . $_SESSION['translate']->it('on') .
	  '</i></b> ' . $this->created_date . '<br />';

	$content .= '<b><i>' . $_SESSION['translate']->it('Last Updated By') .
	  ':</i></b> ' . $this->updated_username . ' <b><i>' . $_SESSION['translate']->it('on') .
	  '</i></b> ' . $this->updated_date . '<br /><br />';

	$GLOBALS['CNT_pagemaster']['content'] .= $content;
      }

      /* Begin creating "Save Page" box */
      $myelements[0] = PHPWS_Form::formHidden('module', 'pagemaster');

      if($_SESSION['OBJ_user']->js_on) {
	$myelements[0] .= PHPWS_Form::formHidden('PAGE_op[page_save]', $_SESSION['translate']->it('SAVE PAGE'));
	$myelements[0] .= PHPWS_Form::formButton($_SESSION['translate']->it('SAVE PAGE'), NULL, 'verify();');
      } else {
	$myelements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it('SAVE PAGE'), 'PAGE_op[page_save]');
      }
      $myelements[0] .= $_SESSION['OBJ_help']->show_link('pagemaster', 'page_save');

      $content = '<div class="errortext"><h3>' . $_SESSION['translate']->it('ATTENTION!').'</h3></div>' .
	$_SESSION['translate']->it('Please make sure the page is setup the way you like and <b>all sections are saved</b> before selecting SAVE PAGE!')
	. '<br />' . $_SESSION['translate']->it('You <b>can</b> return and edit this page after you have saved it') . '.<br /><br /><span style="text-align:center;">' .
	PHPWS_Form::makeForm('PAGE_save', 'index.php', $myelements, 'post', 0, 0) .
	'</span>';

      $GLOBALS['CNT_pagemaster']['content'] .= $content;

      if($GLOBALS['core']->moduleExists('menuman') && $_SESSION['OBJ_user']->allow_access('menuman', 'add_item')) {
	$_SESSION['OBJ_menuman']->add_module_item('pagemaster',
						  '&amp;PAGE_user_op=view_page&amp;PAGE_id=' . $this->id,
						  './index.php?module=pagemaster&amp;MASTER_op=edit_page&amp;PAGE_id=' . $this->id, 1);
      }
    }
  }// END FUNC edit_page()

  /**
   * Sets new_page to 0 and updates this page in the database.
   *
   * @access public
   */
  function save_page () {
    $this->new_page = 0;
    $this->update_page();

    if ($this->approved){
      $content = $_SESSION['translate']->it('Your page has successfully been saved to the database!') . '<br /><br />';
    } else {
      $content = $_SESSION['translate']->it('Your page has been sent to the administrator for approval!') . '<br /><br />';
    }

    $GLOBALS['CNT_pagemaster']['content'] .= $content;
  }// END FUNC save_page()

  /**
   * Updates this page's information in the database
   *
   * @access public
   */
  function update_page () {
    $query_data['title'] = PHPWS_Text::addslashes($this->title);
    $query_data['section_order'] = serialize($this->order);
    $query_data['new_page'] = $this->new_page;
    $query_data['advanced'] = $this->advanced;
    $query_data['mainpage'] = $this->mainpage;
    $query_data['active'] = $this->active;
    $query_data['approved'] = $this->approved;
    $query_data['updated_date'] = date('Y-m-d H:i:s');
    $query_data['updated_username'] = $_SESSION['OBJ_user']->username;
    
    if(!$this->approved && !$this->submitted) {
      $short = '<b>' . $this->title . '</b><br />';
      if (isset($this->sections[0]->text))
	$short .= substr($this->sections[0]->text, 0, 50);
      $info['id'] = $this->id;
      PHPWS_Approval::add($this->id, $short, 'pagemaster');
      $this->submitted = TRUE;
    }
    
    $GLOBALS['core']->sqlUpdate($query_data, 'mod_pagemaster_pages', 'id', $this->id);
  }// END FUNC update_page()


  /**
   * Deletes a pagemaster menu link
   *
   * @author            Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  integer id Id number of the particular page
   */
  function deleteMenuLink($id){
    $row = $GLOBALS['core']->sqlSelect('mod_menuman_items', 'menu_item_url', "\\\./index\\\.php\\\?module=pagemaster.+PAGE_id=$id", NULL, 'REGEXP');
    if (!is_array($row))
      return 0;
    
    unset($_SESSION['OBJ_menuman']);
    foreach ($row as $item){
      $sql = "select * from mod_menuman_items where menu_item_pid='".$item['menu_item_id']."' and menu_item_id != '".$item['menu_item_id']."'";
      if (  $GLOBALS['core']->getOne($sql ,TRUE) ){
	$parentLink = TRUE;
	$update['menu_item_url'] = preg_replace('/&PAGE_id=\d.+/', '', $item['menu_item_url']);
	$GLOBALS['core']->sqlUpdate($update, 'mod_menuman_items', 'menu_item_id', $item['menu_item_id']);
      }
      else
	$GLOBALS['core']->sqlDelete('mod_menuman_items', 'menu_item_id', $item['menu_item_id']);
    }

    if (isset($parentLink))
      $content = $_SESSION['translate']->it('Some menu links could not be removed') . '.';
    else
      $content = $_SESSION['translate']->it('All menu links removed') . '.';

    $GLOBALS['CNT_pagemaster']['content'] .= $content;
  }

  /**
   * Deletes this page and all associated sections from the database. Images remain untouched
   * and should be manually removed if you are sure no other page is using them.
   *
   * @access public
   */
  function delete_page () {
    include(PHPWS_SOURCE_DIR . 'mod/pagemaster/conf/config.php');
    if(isset($_POST['yes'])) {

      if (is_array($this->sections)){
	foreach ($this->sections as $section){
	  if (!empty($section->image))
	    @unlink(PHPWS_HOME_DIR . $image_directory . $section->image['name']);
	}
      }

      foreach($this->order as $value)
	$GLOBALS['core']->sqlDelete('mod_pagemaster_sections', 'id', $value);


      $GLOBALS['core']->sqlDelete('mod_pagemaster_pages', 'id', $this->id);
      PHPWS_Approval::remove($this->id, 'pagemaster');
      PHPWS_Fatcat::purge($this->id, 'pagemaster');
      if($GLOBALS['core']->moduleExists('menuman')) {
	PHPWS_Page::deleteMenuLink($this->id);
      }
      $_SESSION['SES_PM_master']->main_menu();
      $content = $_SESSION['translate']->it('The page') . ' <b>' . $this->title . '</b> ' .
	$_SESSION['translate']->it('has successfully been <b>deleted</b>!') . '<br /><br />';
      $GLOBALS['CNT_pagemaster']['content'] .= $content;
      $_SESSION['SES_PM_master']->list_pages();
    } else if(isset($_POST['no'])) {
      $_SESSION['SES_PM_master']->main_menu();
      $content = $_SESSION['translate']->it('You have <b>kept</b> the page') . ' <b>' . $this->title . '</b><br /><br />';
      $GLOBALS['CNT_pagemaster']['content'] .= $content;
      $_SESSION['SES_PM_master']->list_pages();
    } else {
      $_SESSION['SES_PM_master']->main_menu();
      $content = '<div class="errortext"><h3>'.$_SESSION['translate']->it('Confirm Page Deletion!').'</h3></div>';
      $content .= $_SESSION['translate']->it('Are you sure you wish to ') .
	'<span style="text-decoration:underline;font-weight:bold;">'.
	$_SESSION['translate']->it('delete') . '</span>' .
	$_SESSION['translate']->it(' the page ') .
	'<span style="text-decoration:underline;font-weight:bold;">' . $this->title . '</span>?<br /><br />';

      $myelements[0] = PHPWS_Form::formHidden('module', 'pagemaster');
      $myelements[0] .= PHPWS_Form::formHidden('PAGE_id', $this->id);
      $myelements[0] .= PHPWS_Form::formHidden('MASTER_op', 'delete_page');
      $myelements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it('Yes'), 'yes') .
	'&nbsp;&nbsp;';
      $myelements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it('No'), 'no');

      $content .= PHPWS_Form::makeForm('PAGE_confirm_delete', 'index.php', $myelements, 'post', 0, 0);

      $GLOBALS['CNT_pagemaster']['content'] .= $content;
    }
  }// END FUNC delete_page()

  /**
   * Adds a section to the sections array and order array in this page.
   *
   * @access public
   */
  function add_section () {
    $this->sections[$_SESSION['SES_PM_section']->id] = $_SESSION['SES_PM_section'];
    array_push($this->order, $_SESSION['SES_PM_section']->id);
    $this->update_page();
  }// END FUNC add_section()
       
  /**
   * Updates a section's data in the sections array of this page.  SECT_id is the id
   * of the section to be updated
   *
   * @param  integer $SECT_id Database id of the section to be updated.
   * @access public
   */
  function update_section ($SECT_id) {
    $this->sections[$SECT_id] = $_SESSION['SES_PM_section'];
  }// END FUNC update_section()

  /**
   * Removes the section at SECT_id from this page.
   *
   * @param  integer $SECT_id Database id of the section to be removed.
   * @access public
   */
  function remove_section ($SECT_id) {
    if(isset($_POST['yes']) || $_SESSION['OBJ_user']->js_on) {
      foreach($this->order as $key => $value) {
	if($value == $SECT_id) {
	  $this->order = PHPWS_Array::yank($this->order, $key);
	}
      }

      $this->sections[$SECT_id]->delete_section();
      unset($this->sections[$SECT_id]);
      $this->update_page();
      $this->edit_page();
    } else if(isset($_POST['no'])) {
      $this->edit_page();
    } else {
      $content = '<div class="errortext"><h3>' . $_SESSION['translate']->it('Confirm Section Deletion!') .
	'</h3></div>';
      $content .= $_SESSION['translate']->it('Are you sure you wish to ') .
	'<span style="text-decoration:underline;font-weight:bold;">'.
	$_SESSION['translate']->it('delete') . '</span>' .
	$_SESSION['translate']->it(' the section ') .
	'<span style="text-decoration:underline;font-weight:bold;">' . $this->sections[$SECT_id]->title . '</span>?<br /><br />';

      $myelements[0] = PHPWS_Form::formHidden('module', 'pagemaster');
      $myelements[0] .= PHPWS_Form::formHidden('SECT_id', $SECT_id);
      $myelements[0] .= PHPWS_Form::formHidden('PAGE_op[remove]', 1);
      $myelements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it('Yes'), 'yes') .
	'&nbsp;&nbsp;';
      $myelements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it('No'), 'no');

      $content .= PHPWS_Form::makeForm('SECT_confirm_delete', 'index.php', $myelements, 'post', 0, 0);

      $GLOBALS['CNT_pagemaster']['content'] .= $content;
    }
  }// END FUNC remove_section()

  /**
   * Toggles the active flag for this page, either hiding this page from users or allowing
   * users to view this page.
   *
   * @access public
   */
  function toggle_active () {
    PHPWS_WizardBag::toggle($this->active);
    if($this->active) {
      PHPWS_Fatcat::activate($this->id, 'pagemaster');
    } else {
      PHPWS_Fatcat::deactivate($this->id, 'pagemaster');      
    }

    $this->update_page();
  }// END FUNC toggle_active()

  /**
   * Toggles the mainpage flag for this page, either using this page on the main/home page
   * or removing it from the main/home page.
   *
   * @access public
   */
  function toggle_mainpage () {
    PHPWS_WizardBag::toggle($this->mainpage);
    if($this->mainpage == 1) {
      $this->active = 1;
    }
    $this->update_page();
  }// END FUNC toggle_mainpage()

  /**
   * Required function for approval. This function will "approve" this page to be viewed by the public
   *
   * @access public
   */
  function approve($id) {
    $data['approved'] = 1;
    $GLOBALS['core']->sqlUpdate($data, 'mod_pagemaster_pages', 'id', $id);
  }// END FUNC approve()

  /**
   * Required function for approval. This function will "refuse" this page, which in turn deletes the page
   *
   * @access public
   */
  function refuse($id) {
    $GLOBALS['core']->sqlDelete('mod_pagemaster_pages', 'id', $id);
  }// END FUNC refuse()

  /**
   * Moves the section at SECT_id either up or down
   *
   * @param integer $SECT_id Database id of the section to be moved
   * @param integet $SECT_direct Integer representing direction section is to move
   *
   * Written by Mike Samuel - mike at atomikcorp dot net, 30th October 2003
   */
  function move_section ($SECT_id, $SECT_direct) {
    /* Find current current key of $SECT_id */
    $currentplace = array_search($SECT_id, $this->order);
    $sectiontotal = count($this->order);
    $newarray = array();
    $newplace = $currentplace + $SECT_direct;
    if ($newplace > -1 && $newplace < $sectiontotal) {
      // Here we swap the values
      $swapvalue = $this->order[$newplace];
      $this->order[$newplace] = $this->order[$currentplace];
      $this->order[$currentplace] = $swapvalue;
    }
    $this->update_page();
    $this->edit_page();
  }//END FUNC move_section()
  
  function getListId() {
    return $this->id;
  }

  function getListTitle() {
    return '<a href="index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=' . $this->id . '">' . PHPWS_Text::parseOutput($this->title) . '</a>';
  }

  function getListMainpage() {
    if($this->mainpage) {
      return '<span style="color:lime;"><b><i>'.$_SESSION['translate']->it('CURRENT').'</i></b></span>';
    } else {
      return '&nbsp;';
    }
  }

  function getListUpdated_Username() {
    return $this->updated_username;
  }

  function getListUpdated_Date() {
    return $this->viewDate($this->updated_date);
  }

  function getListActions() {
    $actions = array();

    if($_SESSION['OBJ_user']->allow_access('pagemaster', 'activate_pages')) {
      if($this->active) {
	$actions[] = '<a href="./index.php?module=pagemaster&amp;MASTER_op=hide_page&amp;PAGE_id=' . $this->id . '">' . $_SESSION['translate']->it('Hide') . '</a>';
      } else {
	$actions[] = '<a href="./index.php?module=pagemaster&amp;MASTER_op=show_page&amp;PAGE_id=' . $this->id . '">' . $_SESSION['translate']->it('Show') . '</a>';
      }
    }

    $actions[] = '<a href="index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=' . $this->id . '">' . $_SESSION['translate']->it('View') . '</a>';

    if($_SESSION['OBJ_user']->allow_access('pagemaster', 'edit_pages')) {
      $actions[] = '<a href="./index.php?module=pagemaster&amp;MASTER_op=edit_page&amp;PAGE_id=' . $this->id . '">' . $_SESSION['translate']->it('Edit') . '</a>';
    }

    if($_SESSION['OBJ_user']->allow_access('pagemaster', 'delete_pages')) {
      $actions[] = '<a href="./index.php?module=pagemaster&amp;MASTER_op=delete_page&amp;PAGE_id=' . $this->id . '">' . $_SESSION['translate']->it('Delete') . '</a>';
    }

    return implode('&#160;|&#160;', $actions);
  }

}// END CLASS PHPWS_Page

?>
