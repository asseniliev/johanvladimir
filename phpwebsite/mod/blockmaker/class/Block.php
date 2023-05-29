<?php

require_once(PHPWS_SOURCE_DIR . "core/Form.php");
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
require_once(PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * Class:  PHPWS_Block
 *
 * Controls all the information and actions needed to be done to a single block.
 *
 * @version $Id: Block.php,v 1.29 2005/03/02 20:56:27 matt Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Block Maker
 */
class PHPWS_Block{

  /**
   * block_id of current block which corresponds to one in db
   * @var integer
   */
  var $block_id;

  /**
   * title to be placed in title field of boxstyle
   * @var string
   */
  var $block_title;

  /**
   * content to be placed in content field of boxstyle
   * @var string
   */
  var $block_content;

  /**
   * footer to be placed in footer field of boxstyle
   * @var string
   */
  var $block_footer;

  /**
   * flag for block activity
   * @var boolean
   */
  var $block_active;

  /**
   * block updated date
   * @var string
   */
  var $block_updated;

  /**
   * name of content variable current block appears in
   * @var string
   */
  var $content_var;

  /**
   * list of modules in which the current block can appear when active
   * @var array
   */
  var $allow_view;


  /**
   * PHPWS_block
   *
   * Constructor for the block class
   *
   * @param integer $BLK_block_id id of the block to be constructed
   */
  function PHPWS_Block($BLK_block_id=NULL){
    if(is_numeric($BLK_block_id)){
      $this->block_id = $BLK_block_id;

      $block_result = $GLOBALS['core']->sqlSelect("mod_blockmaker_data", "block_id", $this->block_id);
      if($block_result){
	$this->block_title = $block_result[0]["block_title"];
	$this->block_content = $block_result[0]["block_content"];
	$this->block_footer = $block_result[0]["block_footer"];
	$this->block_active = $block_result[0]["block_active"];
	$this->block_updated = $block_result[0]["block_updated"];
	$this->content_var = $block_result[0]["content_var"];
	$this->allow_view = unserialize($block_result[0]["allow_view"]);
      } else{
	$this->error($_SESSION['translate']->it("No result was returned for the block id: [var1].", $this->block_id));
      }
    } else if(is_array($BLK_block_id)) {
      foreach($BLK_block_id as $key => $value) {
	$this->$key = $value;
      }
    } else{
      $this->block_title = NULL;
      $this->block_content = NULL;
      $this->block_footer = NULL;
      $this->block_active = 0;
      $this->block_updated = NULL;
      $this->content_var = NULL;
      $this->allow_view = array();
    }
  }


  /**
   * display_box
   *
   * Creates a block with the title, content, and if applicable the footer 
   */
  function display_block(){
    if ($translation = $_SESSION['translate']->dyn("mod_blockmaker_data", $this->block_id)) {
      $GLOBALS[$this->content_var]['title'] = PHPWS_Text::parseOutput($translation['block_title']);
      $GLOBALS[$this->content_var]['content'] = PHPWS_Text::parseOutput($translation['block_content']);
      $GLOBALS[$this->content_var]['footer'] = PHPWS_Text::parseOutput($translation['block_footer']);
    } else {
      $GLOBALS[$this->content_var]['title'] = PHPWS_Text::parseOutput($this->block_title);
      $GLOBALS[$this->content_var]['content'] = PHPWS_Text::parseOutput($this->block_content);
      $GLOBALS[$this->content_var]['footer'] = PHPWS_Text::parseOutput($this->block_footer);
    }
  }

  function viewDate($date) {
    $info = explode(" ", $date);
    $date = explode("-", $info[0]);
    $time = explode(":", $info[1]);
    return date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, (mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0])+$GLOBALS['core']->datetime->time_dif));
  }

  /**
   * block
   *
   * Handles both the adding a editing of a block
   * Displays the for needed to either add or edit a block
   *
   * @param string $action "create" for adding a new block, "edit" for modifying an existing block
   */
  function block($action){
    $hiddens['module'] = "blockmaker";

    /* set different op for creating and editing a block */
    if($action == "create"){
      $title = $_SESSION['translate']->it("Create A New Block") . "&#160;&#160;" . "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=menu_select\">" . $_SESSION['translate']->it("Back To Main") . "</a>&#160;";
      $hiddens['BLK_block_op'] = "insert_block";
    } else if($action == "edit"){
      $title = $_SESSION['translate']->it("Edit A Block");
      $hiddens['BLK_block_op'] = "update_block"; 
      $hiddens['BLK_block_id'] = $this->block_id;
    }

    $elements[0] = PHPWS_Form::formHidden($hiddens);

    $template['MAIN_HELP'] = CLS_help::show_link("blockmaker", "main");
    $template['TITLE_TEXT'] = $_SESSION['translate']->it("Block Title");
    $template['TITLE'] = PHPWS_Form::formTextField("BLK_block_title", $this->block_title, 35);
    $template['CONTENT_TEXT'] = $_SESSION['translate']->it("Block Content");

    $template['CONTENT'] = "";
    /* checking to see if user has java script enabled before adding the wysiwyg */
    if($_SESSION['OBJ_user']->js_on){
      $template['CONTENT'] = PHPWS_WizardBag::js_insert("wysiwyg", "BLK_block_addedit", "BLK_block_content");
    }
    $template['CONTENT'] .= PHPWS_Form::formTextArea("BLK_block_content", $this->block_content, 10, 50);

    $template['FOOTER_TEXT'] = $_SESSION['translate']->it("Block Footer (Boxstyle must support footer)");
    $template['FOOTER'] = PHPWS_Form::formTextField("BLK_block_footer", $this->block_footer, 35);

    if($action == "create"){
      $template['THEMEVAR_TEXT'] = $_SESSION['translate']->it("Default Theme Variable");
      $template['THEMEVAR'] = PHPWS_Form::formSelect("BLK_block_transfer_var", $_SESSION['OBJ_layout']->getThemeVars(), "right_col_top", 1);
      $template['THEMEVAR_HELP'] = CLS_help::show_link("blockmaker", "theme_var");
    }

    $template['ALLOW_TEXT'] = $_SESSION['translate']->it("Allow View (Only home selected by default)");
    $template['ALLOW'] = PHPWS_Form::formMultipleSelect("BLK_block_allow_view", $_SESSION['OBJ_blockmaker']->get_modules_allowed(), $this->allow_view, 1, NULL, 5);
    $template['ALLOW_HELP'] = CLS_help::show_link("blockmaker", "allow_view");

    /* display a different submit button for creating and editing a block */
    if($action == "create"){
      $template['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Create Block"));
    } else if($action == "edit"){
      $template['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save Changes"));
    }

    $elements[0] .= PHPWS_Template::processTemplate($template, "blockmaker", "addEditBlock.tpl");
    $content = PHPWS_Form::makeForm("BLK_block_addedit", "index.php", $elements, "post", NULL, NULL);

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");
    $GLOBALS['CNT_blockmaker_main']['title'] = $title;
    $GLOBALS['CNT_blockmaker_main']['content'] = $content;
  }


  /**
   * save
   *
   * Saves blocks to the database
   * Controls both inserts and updates to the database
   *
   * @param string $action "insert" to insert a new value in the database or "update" to update what is currently
   *                         in the database
   */
  function save($action){
    $this->block_title = PHPWS_Text::parseInput($_POST["BLK_block_title"]);
    $this->block_content = PHPWS_Text::parseInput($_POST["BLK_block_content"]);
    $this->block_footer = PHPWS_Text::parseInput($_POST["BLK_block_footer"]);
    $this->block_updated = date("Y-m-d H:i:s");

    if(!isset($_POST['BLK_block_allow_view'])) {
      $this->allow_view = $_SESSION['OBJ_blockmaker']->get_modules_allowed();
      $this->allow_view = array("home");
      //array_shift($this->allow_view);
      //array_shift($this->allow_view);
    } else {
      $this->allow_view = $_POST['BLK_block_allow_view'];
    }

    $save_allow = serialize($this->allow_view);

    $save_array = array("block_title"=>"$this->block_title",
			"block_content"=>"$this->block_content",
			"block_footer"=>"$this->block_footer",
			"block_active"=>"$this->block_active",
			"block_updated"=>"$this->block_updated",
			"allow_view"=>"$save_allow"
			);

    $this->block_title = stripslashes($this->block_title);
    $this->block_content = stripslashes($this->block_content);
    $this->block_footer = stripslashes($this->block_footer);

    if($action == "insert"){
      $this->block_active = BLK_DEF_BLOCK_ACT;

      if(!$this->block_id = $GLOBALS['core']->sqlInsert($save_array, "mod_blockmaker_data", TRUE, TRUE, FALSE)){
	$this->error($_SESSION['translate']->it("This block already exists in the database.  You may receive this error as a result of refreshing your browser."));
	return;
      } else{
	$this->content_var = "CNT_blockmaker_" . $this->block_id;
	$save_array = array("content_var"=>"$this->content_var", "block_active"=>"$this->block_active");
	$GLOBALS['core']->sqlUpdate($save_array, "mod_blockmaker_data", "block_id", $this->block_id);
	$_SESSION['OBJ_layout']->create_temp("blockmaker", $this->content_var, $_POST['BLK_block_transfer_var']);
	$_SESSION['translate']->registerDyn("mod_blockmaker_data", $this->block_id);

	$title = $_SESSION['translate']->it("Block Creation Successful");
	$content = $_SESSION['translate']->it("The block [var1] you created was successfully saved to the database.", "<b><i>" . $this->block_title . "</i></b>");
	unset($_SESSION['OBJ_blockmaker']->new_block);
      }
    } else if($action == "update"){
      //      $this->block_id = $_POST["BLK_block_id"];
      if(!$GLOBALS['core']->sqlUpdate($save_array, "mod_blockmaker_data", "block_id", $this->block_id)){
	$this->error($_SESSION['translate']->it("The block [var1] you edited could not be updated please contact your systems administrator.", "<b><i>" . $this->block_title . "</i></b>"));
	return;
      } else{
	$_SESSION['translate']->dynUpdate("mod_blockmaker_data", $this->block_id);
	$title = $_SESSION['translate']->it("Block Changes Successfull");
	$content = $_SESSION['translate']->it("The block [var1] you edited was successfully saved to the database.", "<b><i>" . $this->block_title . "</i></b>");
      }
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");
    $GLOBALS['CNT_blockmaker_main']['content'] .= "<b>$title</b><br />$content";
  }


  /**
   * delete
   *
   * Deletes a block from the database
   * There must be a POST "BLK_block_yes" or "BLK_block_no" to take any action
   * otherwise it confirms with the user whether or not to delete the block
   */
  function delete(){
    if(isset($_POST['BLK_block_yes'])){
      $GLOBALS['core']->sqlDelete("mod_layout_box", "content_var", $this->content_var);
      $_SESSION["OBJ_layout"]->reorder_boxes();
      if(!$GLOBALS['core']->sqlDelete("mod_blockmaker_data", "block_id", $this->block_id)){
	$this->error($_SESSION['translate']->it("The block [var1] could not be deleted from the database please contact your systems administrator."));
	return;
      } else{
	$title = $_SESSION['translate']->it("Block Deleted");
	$content = $_SESSION['translate']->it("The block [var1] was successfully deleted from the database.", "<b><i>" . $this->block_title . "</i></b>");
	$_SESSION["translate"]->dynDrop("mod_blockmaker_data", $this->block_id);
	$_SESSION['OBJ_blockmaker']->reload();
      }
    } else if(isset($_POST['BLK_block_no'])){
      $title = $_SESSION['translate']->it("No Block Deleted");
      $content = $_SESSION['translate']->it("No block was deleted from the database.");
    } else{
      $title = $_SESSION['translate']->it("Delete Block Confirmation");
      $content = $_SESSION['translate']->it("Are you sure you want to delete the block [var1] ?", "<b><i>" . $this->block_title . "</i></b>");

      $elements[0] = "<br />";
      $elements[0] .= PHPWS_Form::formHidden(array("module"=>"blockmaker", "BLK_block_op"=>"blockAction", "BLK_delete"=>1, "BLK_block_id"=>$this->block_id));
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Yes"), "BLK_block_yes");
      $elements[0] .= "&#160;&#160;";
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("No"), "BLK_block_no");

      $content .= PHPWS_Form::makeForm("BLK_block_delete", "index.php", $elements, "post", NULL, NULL);
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");
    $GLOBALS['CNT_blockmaker_main']['content'] .= "<b>$title</b><br />$content";

    /* only list blocks after a choice is made */
    if(isset($_POST["BLK_block_yes"]) || isset($_POST["BLK_block_no"])){
      $_SESSION['OBJ_blockmaker']->list_blocks();
    }
  }

  /**
   * error
   *
   * Handles printing error messages for this class
   *
   * @param string error_type the description of the error that has occurred
   */
  function error($error_type){
    $title = "<br /><span class=\"errortext\">" . $_SESSION['translate']->it("Error") . "</span><br />";
    $content = $error_type;

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");
    $GLOBALS['CNT_blockmaker_main']['content'] .= $title . $content;    
  }


  /**
   * set_activity
   *
   * Sets the activity for the block ("on" or "off")
   */
  function set_activity(){
    PHPWS_WizardBag::toggle($this->block_active);
    $update_array = array("block_active"=>"$this->block_active");
    $GLOBALS['core']->sqlUpdate($update_array, "mod_blockmaker_data", "block_id", $this->block_id);
  }

  function getListblock_id() {
    return $this->block_id;
  }

  function getListBlock_Title() {
    return PHPWS_Text::parseOutput($this->block_title);
  }

  function getListBlock_Updated() {
    return $this->viewDate($this->block_updated);
  }

  function getListActions() {
    $actions = array();

    if($_SESSION['OBJ_user']->allow_access("blockmaker", "block_activity")) {
      if($this->block_active) {
	$actions[] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=blockAction&amp;BLK_block_id=$this->block_id&amp;BLK_activity=1\">".$_SESSION['translate']->it("Hide")."</a>"; 
      } else{
	$actions[] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=blockAction&amp;BLK_block_id=$this->block_id&amp;BLK_activity=1\">".$_SESSION['translate']->it("Show")."</a>"; 
      }
    }

    if($_SESSION['OBJ_user']->allow_access("blockmaker", "edit_block")) {
      $actions[] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=blockAction&amp;BLK_block_id=$this->block_id&amp;BLK_edit=1\">".$_SESSION['translate']->it("Edit")."</a>"; 
    }
        
    if($_SESSION['OBJ_user']->allow_access("blockmaker", "delete_block")) {
      $actions[] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=blockAction&amp;BLK_block_id=$this->block_id&amp;BLK_delete=1\">".$_SESSION['translate']->it("Delete")."</a>"; 
    }
    return implode("&#160;|&#160;", $actions);
  }
}

?>
