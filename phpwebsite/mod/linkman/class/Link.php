<?php

require_once (PHPWS_SOURCE_DIR . "core/Form.php");
require_once (PHPWS_SOURCE_DIR . "core/Text.php");
require_once (PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once(PHPWS_SOURCE_DIR . "mod/approval/class/Approval.php");
require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

define("PHPWS_LINKMAN_DEFAULT_ACT", 1);

/**
 * Link class for Link Manager module
 *
 * @version $Id: Link.php,v 1.25 2004/03/20 00:13:55 darren Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Link Manager
 */
class PHPWS_Link {

  /**
   * id of this link
   * @var integer
   */
  var $id;

  /**
   * title of this link
   * @var string
   */
  var $title;

  /**
   * url for this link
   * @var string
   */
  var $url = "http://";

  /**
   * description for this link
   * @var string
   */
  var $description;

  /**
   * list of categories for this link, "," delimited
   * @var string
   */
  var $keywords;

  /**
   * user who added this link
   * @var string
   */
  var $username;

  /**
   * email for user
   * @var string
   */
  var $userEmail;

  /**
   * date this link was added to database
   * @var string
   */
  var $datePosted;

  /**
   * activity of the current link
   *
   * @var boolean
   */
  var $active = PHPWS_LINKMAN_DEFAULT_ACT;

  var $hits;

  /**
   * Constructor for the link class
   *
   * Sets all the link attributes
   *
   * @param int $link_id id of the link to be constructed
   */
  function PHPWS_Link($LMN_id = NULL) {
    if(is_numeric($LMN_id)){
      $this->id = $LMN_id;
      $link_result = $GLOBALS['core']->sqlSelect("mod_linkman_links", "id", $this->id);
      if($link_result){
	$this->title = $link_result[0]["title"];
	$this->url = $link_result[0]["url"];
	$this->description = $link_result[0]["description"];
	if (isset($link_result[0]["category"]))
	  $this->category = $link_result[0]["category"];
	$this->keywords = $link_result[0]["keywords"];
	$this->active = $link_result[0]["active"];
	$this->username = $link_result[0]["username"];
	$this->userEmail = $link_result[0]["userEmail"];
	$this->datePosted = $link_result[0]["datePosted"];
      } else {
	exit("Invalid ID passed to PHPWS_Link constructor");
      }
    } else {
      if(is_array($LMN_id)) {
	foreach($LMN_id as $key => $value) {
	  $this->$key = $value;
	} 
      }
    }
  }

  /**
   * Provides the functionality to add or edit a link
   *
   * @param  string $linkMode controls if the funtion is adding or editing
   * @return string html form for adding or editing a link
   */
  function link($linkMode) {
    $hiddens = array("module"=>"linkman", "LMN_op"=>"linkAction");
    if($linkMode == "user") {
      $hiddens['LMN_new'] = 1;
    } else {
      $hiddens['LMN_new'] = 0;
    }      
    $elements[0] = PHPWS_Form::formHidden($hiddens);

    $template['TITLE_TEXT'] = $_SESSION['translate']->it("Title");
    $template['TITLE'] = PHPWS_Form::formTextField("LMN_title", $this->title, 25);
    $template['URL_TEXT'] = $_SESSION['translate']->it("URL");
    $template['URL'] = PHPWS_Form::formTextField("LMN_url", $this->url, 42);

    if ($categoryForm = $_SESSION['OBJ_fatcat']->showSelect($this->id, "multiple", NULL, "linkman", FALSE, FALSE)){
      $template['CATEGORY_TEXT'] = $_SESSION['translate']->it("Category");
      $template['CATEGORY'] = $categoryForm;
    }

    $template['DESC_TEXT'] = $_SESSION['translate']->it("Description");
    if($_SESSION['OBJ_user']->js_on) {
      $template['DESCRIPTION'] = PHPWS_WizardBag::js_insert("wysiwyg", "LMN_addedit", "LMN_description");
    }
    if(isset($template['DESCRIPTION'])) {
      $template['DESCRIPTION'] = $template['DESCRIPTION'] . PHPWS_Form::formTextArea("LMN_description", $this->description, 6, 50);
    } else {
      $template['DESCRIPTION'] = PHPWS_Form::formTextArea("LMN_description", $this->description, 6, 50);
    }
    $template['KEYWORDS_TEXT'] = $_SESSION['translate']->it("Keywords (Comma Separated)");
    $template['KEYWORDS'] = PHPWS_Form::formTextField("LMN_keywords", $this->keywords, 42);
    $template['USER_EMAIL_TEXT'] = $_SESSION['translate']->it("Email");
    $template['USER_EMAIL'] = PHPWS_Form::formTextField("LMN_user_email", $this->userEmail, 25);

    if($linkMode == "add") {
      $template['ADD_HELP'] = CLS_help::show_link("linkman", "add");
      $template['SUBMIT'] =  PHPWS_Form::formSubmit($_SESSION['translate']->it("Add Link"), "LMN_insertLink");
    } else if($linkMode == "edit") {
      $template['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save Changes"), "LMN_updateLink");
    } else if($linkMode == "user") {
      $template['ADD_HELP'] = CLS_help::show_link("linkman", "add");
      $template['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Submit Link"), "LMN_submitLink");
    }

    $elements[0] .= PHPWS_Template::processTemplate($template, "linkman", "addEditLink.tpl");

    return PHPWS_Form::makeForm("LMN_addedit", "index.php", $elements, "post", NULL, NULL);
  }

  function view() {
    $template['TITLE_TEXT'] = $_SESSION['translate']->it("Title");
    $template['TITLE'] = $this->title;
    $template['URL_TEXT'] = $_SESSION['translate']->it("URL");
    $template['URL'] = "<a href=\"./index.php?module=linkman&amp;LMN_op=visitLink&amp;LMN_id=" . $this->id . "\" target=\"_blank\">" . $this->url . "</a>";
    $template['DESC_TEXT'] = $_SESSION['translate']->it("Description");
    $template['DESCRIPTION'] = $this->description;

    if (!empty($this->userEmail)){
      $template['USER_EMAIL_TEXT'] = $_SESSION['translate']->it("Email");
      $template['USER_EMAIL'] = $this->userEmail;
    }
    
    return PHPWS_Template::processTemplate($template, "linkman", "view.tpl");
  }

  function fatview($id){
    $fatView = new PHPWS_Link($id);
    return $fatView->view();
  }

  function linkAction() {
    if(isset($_REQUEST['LMN_submitLink'])) {
      $_SESSION['PHPWS_Linkman']->userMenu();
    } else {
      $_SESSION['PHPWS_Linkman']->adminMenu();
    }

    if(isset($_REQUEST['LMN_updateLink'])) {
      $this->saveLink("update");
    } else if(isset($_REQUEST['LMN_insertLink'])) {
      $this->saveLink("insert");
    } else if(isset($_REQUEST['LMN_submitLink'])) {
      $this->saveLink("insert");
      return;
    }

    $GLOBALS['CNT_linkman']['content'] .= $_SESSION['PHPWS_Linkman']->listLinks();
  }

  /**
   * Save Link
   *
   * Inserts a new link into the database
   *
   * @param string $save_mode controls whether db action is insert or update
   */
  function saveLink($saveMode) {
    $this->title = PHPWS_Text::parseInput($_REQUEST["LMN_title"]);
    $this->url = PHPWS_Text::checkLink($_REQUEST["LMN_url"]);
    $this->description = PHPWS_Text::parseInput($_REQUEST["LMN_description"]);
    $this->keywords = $_REQUEST["LMN_keywords"];
    $new = 0;

    if($saveMode == "insert") {
      $this->datePosted = date("Y-m-d");
      
      if($_SESSION['OBJ_user']->username) {
	$this->username = $_SESSION['OBJ_user']->username;
      } else {
	$this->username = "Annonymous";
      }

      $this->userEmail = $_POST["LMN_user_email"];
      $new = $_REQUEST['LMN_new'];
    }

    if($new == 1)
      $this->active = 0;

    $saveArray = array("title"=>"$this->title",
		       "url"=>"$this->url",
		       "description"=>"$this->description",
		       "keywords"=>"$this->keywords",
		       "username"=>"$this->username",
		       "userEmail"=>"$this->userEmail",
		       "datePosted"=>"$this->datePosted",
		       "active"=>$this->active,
		       "new"=>"$new");
    
    if($saveMode == "insert") {
      $this->id = $GLOBALS['core']->sqlInsert($saveArray, "mod_linkman_links", FALSE, TRUE, FALSE);

      if($new == 1) {
	$short = "<b>" . $this->title . "</b><br />" . $this->url;
	PHPWS_Approval::add($this->id, $short, "linkman");
	$this->active = 0;
	$title   = $_SESSION['translate']->it("Link Submitted");
	$content = $_SESSION['translate']->it("The link [var1] you entered has been submitted for approval.", "<b><i>" . 
					      $this->title . "</i></b>");
      } else {
	$title = $_SESSION['translate']->it("Link Added");
	$content = $_SESSION['translate']->it("The link [var1] you entered was successfully submitted to the database", "<b><i>" . 
					      $this->title . "</i></b>");
      }

      unset($_SESSION['PHPWS_Linkman']->newLink);

    } else if($saveMode == "update") {
      $GLOBALS['core']->sqlUpdate($saveArray, "mod_linkman_links", "id", $this->id);

      $title = $_SESSION['translate']->it("Link Saved");
      $content = $_SESSION['translate']->it("The link [var1] you modified was successfully saved to the database", "<b><i>" . $this->title . "</i></b>.");
    }

    $_SESSION['OBJ_fatcat']->saveSelect($this->title, "index.php?module=linkman&amp;LMN_op=visitLink&amp;LMN_id=$this->id", $this->id, NULL, "linkman");

    $GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";    
  }

  /**
   * setActivity
   *
   * Toggles the activity of the link
   */
  function setActivity() {
    PHPWS_WizardBag::toggle($this->active);

    if($this->active) {
      PHPWS_Fatcat::activate($this->id, "linkman");
      PHPWS_Approval::remove($this->id, "linkman");
    } else {
      PHPWS_Fatcat::deactivate($this->id, "linkman");
    }

    $updateArray = array("active"=>"$this->active");
    $GLOBALS['core']->sqlUpdate($updateArray, "mod_linkman_links", "id", $this->id);
  }

  /**
   * Delete Link
   *
   * Deletes a link from the database
   */
  function deleteLink() {
    if(isset($_REQUEST['LMN_yes'])) {
      $GLOBALS['core']->sqlDelete("mod_linkman_links", "id", $this->id);
      PHPWS_Fatcat::purge($this->id, "linkman");
      PHPWS_Approval::remove($this->id, "linkman");
      $title = $_SESSION['translate']->it("Link Deleted");
      $content = $_SESSION['translate']->it("The link [var1] was successfully deleted from the database.", $this->title);
      unset($_SESSION['PHPWS_Linkman']->currentLink);
    } else if(isset($_REQUEST['LMN_no'])) {
      $title = $_SESSION['translate']->it("Link Delete Canceled");
      $content = $_SESSION['translate']->it("No link was deleted from the database.");
    } else {
      $title = $_SESSION['translate']->it("Delete Link Confirmation");
      $content = $_SESSION['translate']->it("Are you sure you want to delete the link [var1]?", $this->title) . "<br /><br />";

      $hiddens = array("module"=>"linkman",
		       "LMN_op"=>"linkListAction",
		       "LMN_deleteLink"=>1,
		       "LMN_id"=>"$this->id"
		       );

      $elements[0] = PHPWS_Form::formHidden($hiddens);
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Yes"), "LMN_yes") . "&#160;&#160;";
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("No"), "LMN_no");
      
      $content .= PHPWS_Form::makeForm("LMN_delete", "index.php", $elements, "post", NULL, NULL);
    }

    $GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";
  }

  function approve($id) {
    $data['new'] = 0;
    $data['active'] = 1;
    $GLOBALS['core']->sqlUpdate($data, "mod_linkman_links", "id", $id);
  }

  function refuse($id) {
    $GLOBALS['core']->sqlDelete("mod_linkman_links", "id", $id);
  }

  function getListTitle() {
    return "<a href=\"./index.php?module=linkman&amp;LMN_op=visitLink&amp;LMN_id=$this->id\" target=\"_blank\">$this->title</a>";
  }

  function getListUrl() {   
    return "<a href=\"./index.php?module=linkman&amp;LMN_op=visitLink&amp;LMN_id=$this->id\" target=\"_blank\">$this->url</a>";
  }
  
  function getListHits() {
    return $this->hits;
  }
  
  function getListDatePosted() {
    return $this->dateposted;
  }

  function getListDescription() {
    return $this->description;
  }

  function getListActions() {
    $actions = array();
    
    if($_SESSION['OBJ_user']->allow_access("linkman", "link_act")) {
      if($this->active) {
	$actions[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=linkListAction&amp;LMN_id=$this->id&amp;LMN_setActivity=1\">".$_SESSION['translate']->it("Hide")."</a>";
      } else {
	$actions[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=linkListAction&amp;LMN_id=$this->id&amp;LMN_setActivity=1\">".$_SESSION['translate']->it("Show")."</a>";
      }
    }

    $actions[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=visitLink&amp;LMN_id=$this->id\" target=\"_blank\">".$_SESSION['translate']->it("Visit")."</a>";

    if($_SESSION['OBJ_user']->allow_access("linkman", "edit_link")) {
      $actions[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=linkListAction&amp;LMN_id=$this->id&amp;LMN_editLink=1\">".$_SESSION['translate']->it("Edit")."</a>";
    }

    if($_SESSION['OBJ_user']->allow_access("linkman", "delete_link")) {
      $actions[] = "<a href=\"./index.php?module=linkman&amp;LMN_op=linkListAction&amp;LMN_id=$this->id&amp;LMN_deleteLink=1\">".$_SESSION['translate']->it("Delete")."</a>";
    }

    return implode("&#160;|&#160;", $actions);
  }
}

?>