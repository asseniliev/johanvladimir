<?php

require_once(PHPWS_SOURCE_DIR . "core/List.php");
require_once(PHPWS_SOURCE_DIR . "core/Form.php");
require_once(PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * Class: PHPWS_BlockMaker
 *
 * Controls all of the individual blocks in the database.
 *
 * @version $Id: BlockMaker.php,v 1.13 2004/08/19 20:06:25 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Block Maker
 */
class PHPWS_BlockMaker {
  
  /**
   * all block objects for corresponding blocks in the db
   * @var array
   */
  var $blocks;

  /**
   * list of html allowed to be entered in a block
   * @var array
   */
  var $allowed_html;

  /**
   * block object for creating new blocks
   * @var object
   */
  var $new_block;

  var $list;
  
  /**
   * PHPWS_Blockmaker
   *
   * Constructor for the blockmaker class
   */
  function PHPWS_Blockmaker() {
    $this->blocks = array();
    $this->new_block = new PHPWS_Block();
    $this->list = NULL;
    $this->reload();
  }
  

  /**
   * get_allowed_html
   *
   * Returns the allowed html list
   *
   * @return array all of the allowed html tags
   */
  function get_allowed_html() {return $this->allowed_html;}


  /**
   * get_modules_allowed
   *
   * Returns the modules allowed
   *
   * @return array all of the modules allowed to have blocks active
   */
  function get_modules_allowed() {
    $modulesAllowed = $GLOBALS['core']->listModules();

    $text = $_SESSION['translate']->it("Select Modules Allowed");
    $options = array($text, 
		     "----------------------------------------------");
    array_push($options, "home");
    $options = array_merge($options, $modulesAllowed);

    return $options;
  }


  /**
   * reload
   *
   * Reinitializes the blocks array
   */
  function reload() {
    unset($this->blocks);
    $this->blocks = array();

    $sql = "SELECT block_id FROM " . PHPWS_TBL_PREFIX . "mod_blockmaker_data ORDER BY block_title";
    $blocks_result = $GLOBALS['core']->getcol($sql);

    foreach($blocks_result as $id) {
      $this->blocks[$id] = new PHPWS_Block($id);
    }
  }


  /**
   * block_menu
   *
   * Administration options for blocks
   */
  function block_menu() {
    $links = array();

    if($_SESSION['OBJ_user']->allow_access("blockmaker", "create_block")) {
      $links[] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=menu_select&amp;create_block=1\">".$_SESSION['translate']->it("New Block")."</a>".CLS_help::show_link("blockmaker", "create_block");
    }

    if($_SESSION['OBJ_user']->allow_access("blockmaker")) {
      $links[] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=menu_select&amp;list_blocks=1\">".$_SESSION['translate']->it("List Blocks")."</a>".CLS_help::show_link("blockmaker", "list_blocks");
    }

    $tags = array();
    $tags["LINKS"] = implode("&#160;|&#160;", $links);

    $GLOBALS['CNT_blockmaker_main']['content'] = PHPWS_Template::processTemplate($tags, "blockmaker", "adminMenu.tpl");
  }


  /**
   * list_blocks
   *
   * Lists all of the blocks
   * Provides the edit, delete and activity submit buttons
   */
  function list_blocks() {
    $listTags = array();
    $listTags['TITLE'] = $_SESSION['translate']->it("Current blocks");
    $listTags['ID_LABEL'] = $_SESSION['translate']->it("ID");
    $listTags['TITLE_LABEL'] = $_SESSION['translate']->it("Title");
    $listTags['UPDATED_LABEL'] = $_SESSION['translate']->it("Updated");
    $listTags['ACTIONS_LABEL'] = $_SESSION['translate']->it("Actions");

    if(sizeof($this->blocks) > 0) {
      $listTags['LIMIT_LABEL'] = $_SESSION["translate"]->it("Limit");
    }

    if(!isset($this->list)) {
      $this->list = new PHPWS_List;
    }

    $this->list->setModule("blockmaker");
    $this->list->setClass("PHPWS_Block");
    $this->list->setTable("mod_blockmaker_data");
    $this->list->setIdColumn("block_id");
    $this->list->setDbColumns(array("block_active", "block_title", "block_updated"));
    $this->list->setListColumns(array("Block_Title", "Block_Updated", "Actions"));
    $this->list->setName("list");
    $this->list->setOp("BLK_block_op=block_menu");

    $this->list->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
    $this->list->setExtraListTags($listTags);

    $GLOBALS['CNT_blockmaker_main']['content'] .= $this->list->getList();
  }


  /**
   * error
   *
   * Handles error messages
   *
   * @param string $error_type description of the error to be displayed
   */
  function error($error_type) {
    $title = "<br /><span class=\"errortext\">" . $_SESSION['translate']->it("Error") . "</span><br />";
    $content = $error_type;

    //$_SESSION['layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");
    $GLOBALS['CNT_blockmaker_main']['content'] = $title . $content; 
  }
}

?>