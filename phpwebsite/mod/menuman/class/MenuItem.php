<?php

require_once (PHPWS_SOURCE_DIR . "core/Form.php");

require_once (PHPWS_SOURCE_DIR . "core/Text.php");

require_once (PHPWS_SOURCE_DIR . "core/WizardBag.php");

/**
 * Class:  PHPWS_MenuItem
 *
 * Controls all actions to be done to a single menu item.
 *
 * @version $Id: MenuItem.php,v 1.43 2005/03/01 21:15:19 matt Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Menu Manager
 */
class PHPWS_MenuItem {

  /**
   * id of the current menu item
   * @var integer
   */
  var $menu_item_id;

  /**
   * parent id of the current menu item
   * @var integer
   */
  var $menu_item_pid;

  /**
   * title of this menu item
   * @var string
   */
  var $menu_item_title;

  /**
   * url for this menu item
   * @var string
   */
  var $menu_item_url;

  /**
   * flag controlling menu item activity
   * @var boolean
   */
  var $menu_item_active;

  /**
   * coordinates if menu item is for image map
   * @var string
   */
  var $menu_item_coords;

  /**
   * key for this menu item's display method
   * @var integer
   */
  var $display_key;

  /**
   * extra space var used in edit mode to show indention
   * @var string
   */
  var $edit_spacer;

  var $menu_id;

  /**
   * Constructor for the PHPWS_Menu_Item class 
   *
   * Initializes class variables
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param array $menu_item all of the attributes for this menu_item
   * @param int $level the level of the this menu_item
   * @return none
   */
  function PHPWS_MenuItem($menu_item=NULL, $level=NULL) {
    if($menu_item) {
      $this->menu_item_id = $menu_item["menu_item_id"];
      $this->menu_item_pid = $menu_item["menu_item_pid"];
      $this->menu_item_title = $menu_item["menu_item_title"];
      $this->menu_item_url = $menu_item["menu_item_url"];
      $this->menu_item_active = $menu_item["menu_item_active"];
      $this->menu_item_coords = $menu_item["menu_item_coords"];
      $this->display_key = $menu_item["display_key"]; 
      $this->edit_spacer = "";
      for($x = 0; $x < $level * 3; $x++) {
	$this->edit_spacer .= "&#160;";
      }
      $this->menu_id = $menu_item['menu_id'];
    } else {
      $this->menu_item_id = 0;
      $this->menu_item_pid = NULL;
      $this->menu_item_title = NULL;
      $this->menu_item_url = NULL;
      $this->menu_item_active = MMN_DEF_ITEM_ACT;
      $this->menu_item_coords = NULL;
      $this->display_key = NULL;
      $this->edit_spacer = NULL;
      $this->menu_id = NULL;
    }
  } // END FUNC PHPWS_Menu_Item


  /**
   * is_active
   *
   * Returns the activity of the current menu item
   *
   * @return boolean the activity of this item
   */
  function is_active(){return $this->menu_item_active;}


  /**
   * menu_item
   *
   * Provides the form to add, edit, and view this menu item data
   *
   * @param string $action the action to be taken
   * @param boolean $highlight whether or not to highlight the current row
   * @return string the current row of data
   */
  function menu_item($action, $highlight, $menu_id=NULL, $image_map=NULL) {

    $element = "<tr" . $highlight . ">";

    if($action == "edit") {
      $element .= "<td>&#160;&#160;";
      $element .= PHPWS_Form::formCheckBox("MMN_item_id[]", $this->menu_item_id);
      $element .= "</td>";
    } else if($action == "view") {
      $element .= "<td>" . $this->menu_item_title . "</td>";
    }
    
    if($action == "add" || $action == "edit") {
      $element .= "<td>";
      if($_SESSION['OBJ_menuman']->uber_edit) {
	$text_size = 20;
	$edit_spacer = "";
      } else {
	$text_size = 20;
	$edit_spacer = $this->edit_spacer;
      }
      $element .= $edit_spacer . PHPWS_Form::formTextField("MMN_item_title" . $this->menu_item_id, $this->menu_item_title, $text_size, 80);
      $element .= "</td>";

      if($_SESSION['OBJ_menuman']->uber_edit || $action == "add") {
	if(isset($_REQUEST['MMN_module'])){
	  $this->menu_item_url = "./index.php?module=" . $_REQUEST['MMN_module'];
	  if (isset($_REQUEST['MMN_op_string'])) {
	    $this->menu_item_url .= str_replace("&", "&amp;", $_REQUEST['MMN_op_string']);
	    $this->display_key = 1;
	  }
	}

	if (!isset($_POST['MMN_module']) || $_POST['MMN_module'] != "pagemaster"){
	  $element .= "<td>";
	  $element .= PHPWS_Form::formTextField("MMN_item_url" . $this->menu_item_id, $this->menu_item_url, 30);

	  if($image_map) {
	    $element .= "<br />" . $_SESSION['translate']->it("Coordinates") . "&#160;&#160;" . PHPWS_Form::formTextField("MMN_item_coords" . $this->menu_item_id, $this->menu_item_coords, 30);
	  }

	  $element .= "</td>";
	}

	$pageId = NULL;
	if($this->display_key == 1) {
	  $pageId = $this->menu_item_helper();	  
	}

	if(!isset($_POST['MMN_module']) && $GLOBALS['core']->moduleExists("pagemaster")) {
	  $element .= "<td>";
	  $onChange = "internalPageChange('MMN_item_display$this->menu_item_id', 'MMN_pagemaster_id$this->menu_item_id')";
	  $element .= PHPWS_Form::formSelect("MMN_pagemaster_id" . $this->menu_item_id, $_SESSION['OBJ_menuman']->pageOptions, $pageId, FALSE, TRUE, 
					     $onChange);
	  $element .= "</td>";
	}
      }

      if(($action == "add") && isset($_POST['MMN_module']) && $_POST['MMN_module'] == 'pagemaster' && $GLOBALS['core']->moduleExists("pagemaster")) {
	$element .= "<td>";

	//	$element .= PHPWS_Form::formSelect("MMN_pagemaster_id" . $this->menu_item_id, $_SESSION['OBJ_menuman']->pageOptions, $pageId, FALSE, TRUE);
	$onChange = "internalPageChange('MMN_item_display$this->menu_item_id', 'MMN_pagemaster_id$this->menu_item_id')";
	$element .= PHPWS_Form::formSelect("MMN_pagemaster_id" . $this->menu_item_id, $_SESSION['OBJ_menuman']->pageOptions, $pageId, FALSE, TRUE, 
					   $onChange);
	$element .= PHPWS_Form::formHidden("MMN_item_url" . $this->menu_item_id, str_replace("&", "&amp;", $this->menu_item_url));

	$element .= "</td>";
      } else if($action == "add") {
	$element .= "<td>";

	//	$element .= PHPWS_Form::formSelect("MMN_item_display" . $this->menu_item_id, $_SESSION['OBJ_menuman']->get_display_options(), NULL, NULL, 1);
	$onChange = "displayChange('MMN_item_display$this->menu_item_id', 'MMN_pagemaster_id$this->menu_item_id')";
	$element .= PHPWS_Form::formSelect("MMN_item_display" . $this->menu_item_id, $_SESSION['OBJ_menuman']->get_display_options(), NULL, NULL, 1, $onChange);
	$element .= "</td>";
      }
    }
     
    if($action == "edit" && !$_SESSION['OBJ_menuman']->uber_edit) {
      $element .= "<td>";
      $element .= "<a href=\"index.php?module=menuman&amp;MMN_menuman_op=move_item_up&amp;MMN_menu_id=$menu_id&amp;MMN_item_id=$this->menu_item_id\"><img src=\"./images/up.gif" . "\" alt=\"up\" border=\"0\" /></a>";
      $element .= "&#160;&#160;";
      $element .= "<a href=\"index.php?module=menuman&amp;MMN_menuman_op=move_item_down&amp;MMN_menu_id=$menu_id&amp;MMN_item_id=$this->menu_item_id\"><img src=\"./images/down.gif" . "\" alt=\"down\" border=\"0\" /></a>";
      $element .= "</td>";
      $element .= "<td>";
      if($this->menu_item_active) {
	$element .= "<span style=\"color:#00ff00\"><b><i>" . $_SESSION['translate']->it("Active") . "</i></b></span>";
      } else {
	$element .= "<span style=\"color:#ff0000\"><b><i>" . $_SESSION['translate']->it("Not Active") . "</i></b></span>";
      }
      $element .= "</td>";
    }

    if($_SESSION['OBJ_menuman']->uber_edit && $action == "edit") {
      $element .= "<td>";
      $onChange = "displayChange('MMN_item_display$this->menu_item_id', 'MMN_pagemaster_id$this->menu_item_id')";
      $element .= PHPWS_Form::formSelect("MMN_item_display" . $this->menu_item_id, $_SESSION['OBJ_menuman']->get_display_options(), $this->display_key, NULL, 1, $onChange);
      $element .= "</td>";
    }

    if (isset($_POST['MMN_item_pid']))
      $matchItem = $_POST['MMN_item_pid'];
    else
      $matchItem = 0;

    if($action == "view") {
      $element .= "<td align=\"center\">";
      $element .= PHPWS_Form::formRadio("MMN_item_pid", $this->menu_item_id, $matchItem);
      $element .= "</td>";
    }

    if($action == "edit") {
      $element .= "<td>" . $this->menu_item_id . "</td>";
    }

    $element .= "</tr>\n";

    return $element;
  } // END FUNC menu_item

  function menu_item_helper() {
    $data = explode("&", $this->menu_item_url);
    if (isset($data[2]))
      $data = explode("=", $data[2]);

    if (isset($data[0])){

      if((($data[0] == "PAGE_id") || ($data[0] == "amp;PAGE_id")) && is_numeric($data[1]))
	return $data[1];
      else
	return FALSE;
    }
  } // END FUNC menu_item_helper

  /**
   * display_menu_item
   *
   * Called by build_display_menu and its get child function to display this 
   * menu item data
   *
   * @param integer $level the current level of the menu
   * @param string $position the current position in the menu
   * @param string $template the template file to be applied to this item
   * @param array $menu_item info to handle indention scheme
   * @parap string $spacer the space to place before each item
   * @return string the item link to be displayed
   */
  function display_menu_item($level, $position, $template, $menu_indent, $spacer) {
    include(PHPWS_SOURCE_DIR . "mod/menuman/conf/config.php");

    /* build the url depending on the display method */
    switch($this->display_key) {
    case 1:
      if(!preg_match("/\?/", $this->menu_item_url)) $position = "?" . $position;
      $menu_href = "\"" . $this->menu_item_url . $position . "\"";
      break;
    case 2:
      $menu_href = "\"" . $this->menu_item_url . "\"";
      break;
    case 3:
      $menu_href = "\"" . $this->menu_item_url . "\" target=\"_blank\"";
      break;
    case 4:	
      $menu_href = "\"" . $this->menu_item_url . "\"";
      break;
    }
    
    foreach($menu_indent as $key=>$value) 
      if(isset($menu_indent[$key])) continue;

    if(isset($menu_indent['item']) && $menu_indent['item'] == "character") {
      $item_href = $menu_href;
      if(isset($_SESSION['SES_parentlevel_id'][0]) && $this->menu_item_id == $_SESSION['SES_parentlevel_id'][0]) {
	$indent_item = "<span style=\"color:" . $menu_indent['color'] . "\">" . $menu_indent['indent'] . "</span>";
	$template_prefix = "ACTIVE_";
      } else if(in_array($this->menu_item_id, $_SESSION['SES_parentlevel_id'])) {
	$indent_item = $menu_indent['indent'];
	$template_prefix = "OPEN_";
      } else {
	$indent_item = $menu_indent['indent'];
	$template_prefix = "INACTIVE_";
      }
    } else if(isset($menu_indent['item']) && $menu_indent['item'] == "image") {
      $item_href = $menu_href;
      if(isset($_SESSION['SES_parentlevel_id'][0]) && $this->menu_item_id == $_SESSION['SES_parentlevel_id'][0]) {
	$indent_item = "<img src=\"" . $upload_directory . $menu_indent['image_active'] . "\" alt=\"indent\" border=\"0\" />";
	$template_prefix = "ACTIVE_";
      } else if(in_array($this->menu_item_id, $_SESSION['SES_parentlevel_id'])) {
	$indent_item = "<img src=\"" . $upload_directory . $menu_indent['image_open'] . "\" alt=\"indent\" border=\"0\" />";
	$template_prefix = "OPEN_";
      } else {
	$indent_item = "<img src=\"" . $upload_directory . $menu_indent['image'] . "\" alt=\"indent\" border=\"0\" />";
	$template_prefix = "INACTIVE_";
      }
    } else {
      $item_href = NULL;
      $indent_item = NULL;
      if(isset($_SESSION['SES_parentlevel_id'][0]) && $this->menu_item_id == $_SESSION['SES_parentlevel_id'][0]) {
	$template_prefix = "ACTIVE_";
      } else if(in_array($this->menu_item_id, $_SESSION['SES_parentlevel_id'])) {
	$template_prefix = "OPEN_";
      } else {
	$template_prefix = "INACTIVE_";
      }
    }   

    $template_array = array("$template_prefix"."INDENT"=>$spacer,
			    "$template_prefix"."ITEM_HREF"=>$item_href,
			    "$template_prefix"."ITEM"=>$indent_item,
			    "$template_prefix"."HREF"=>$menu_href,
			    "$template_prefix"."TITLE"=>$this->menu_item_title,
			    "$template_prefix"."COORDS"=>$this->menu_item_coords,
			    "$template_prefix"."ITEM_ID"=>$this->menu_item_id
			    );

    $template_array["MENU_ID"] = $this->menu_id;
    $template_array["THEME_DIRECTORY"] = "themes/" . $_SESSION['OBJ_layout']->current_theme . "/";

    $template_file = "menuitem" . $level . ".tpl";


    return PHPWS_Template::processTemplate($template_array, "menuman", $template . $template_file);
  } // END FUNC display_menu_item


  /**
   * save_item
   *
   * Saves a single item to the database
   *
   * @param string $action the action to be taken (insert or update)
   */
  function save_item($action){
    $this->menu_item_title = PHPWS_Text::addslashes($_POST['MMN_item_title' . $this->menu_item_id]);

    if($_SESSION['OBJ_menuman']->uber_edit || $action == "insert") {

      if(isset($_POST['MMN_module']) && !empty($_POST['MMN_module'])) {

	$this->menu_item_url = preg_replace("/&(?!amp;)/Ui", "&amp;", $_POST['MMN_item_url' . $this->menu_item_id]);
	$this->menu_item_active = $_POST['MMN_item_active'];

      } else if(isset($_POST['MMN_pagemaster_id']) 
		&& ($_POST['MMN_pagemaster_id'] > 0) 
		&& (isset($_POST['MMN_item_display']) && $_POST['MMN_item_display'] == 1)) {

	$page_id = $_POST['MMN_pagemaster_id'];
	$this->menu_item_url = "./index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=$page_id";

      } else if(isset($_POST['MMN_pagemaster_id' . $this->menu_item_id]) 
		&& ($_POST['MMN_pagemaster_id' . $this->menu_item_id] > 0) 
		&& (isset($_POST['MMN_item_display' . $this->menu_item_id]) && $_POST['MMN_item_display' . $this->menu_item_id] == 1)) {

	$page_id = $_POST['MMN_pagemaster_id' . $this->menu_item_id];
	$this->menu_item_url = "./index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=$page_id";

      } elseif (isset($_POST['MMN_item_url' . $this->menu_item_id])) {

	$this->menu_item_url = preg_replace("/&(?!amp;)/Ui", "&amp;", $_POST['MMN_item_url' . $this->menu_item_id]);

      }

      if (isset($_POST['MMN_item_display' . $this->menu_item_id]))

	$this->display_key = $_POST['MMN_item_display' . $this->menu_item_id];

      if (isset($_POST['MMN_item_coords' . $this->menu_item_id]))

	$this->menu_item_coords = $_POST['MMN_item_coords' . $this->menu_item_id];

    } else {

      $this->menu_item_active = $this->menu_item_active;

    }

    if(isset($_POST['MMN_item_display' . $this->menu_item_id])) {
      $this->display_key = $_POST['MMN_item_display' . $this->menu_item_id];
    } else if($action == "insert") {
      $this->display_key = 1;
    }

    if($this->display_key == 2) {
      if(substr($this->menu_item_url, 0, 7) != "mailto:") {
	$this->menu_item_url = "mailto:" . $this->menu_item_url;
      }
    }

    if($this->display_key == 4 || $this->display_key == 3) {
      if(($this->menu_item_url[0] != ".") && ($this->menu_item_url[1] != "/")) {
	$this->menu_item_url = PHPWS_Text::checkLink($this->menu_item_url);
      }
    }

    $save_array = array("menu_item_title"=>"$this->menu_item_title",
			"menu_item_url"=>"$this->menu_item_url",
			"menu_item_active"=>"$this->menu_item_active",
			"display_key"=>"$this->display_key",
			"menu_item_coords"=>"$this->menu_item_coords"
			);

    $this->menu_item_title = stripslashes($this->menu_item_title);
    $this->menu_item_url = stripslashes($this->menu_item_url);

    $menu_id = $_POST['MMN_menu_id'];

    if($action == "insert") {
      $save_array['menu_id'] = $menu_id;
      $max_id = $GLOBALS['core']->sqlInsert($save_array, "mod_menuman_items", FALSE, TRUE, FALSE);
      $save_array = array();

      if($_POST['MMN_item_pid'] == 0) {
	$this->menu_item_id = $max_id;
	$this->menu_item_pid = $max_id;
	$save_array['menu_item_pid'] = $max_id;

	$sql = "SELECT max(menu_item_order) FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id=menu_item_pid AND menu_id='$menu_id'";
	$max_order = $GLOBALS['core']->quickFetch($sql);
	$save_array['menu_item_order'] = $max_order['max(menu_item_order)'] + 1;
      } else {	
	$this->menu_item_id = $max_id;
	$save_array['menu_item_pid'] = $this->menu_item_pid = $_POST['MMN_item_pid'];

	$sql = "SELECT max(menu_item_order) FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!=menu_item_pid";
	$max_order = $GLOBALS['core']->quickFetch($sql);
	$save_array['menu_item_order'] = $max_order['max(menu_item_order)'] + 1;
      }
      $GLOBALS['core']->sqlUpdate($save_array, "mod_menuman_items", "menu_item_id", $max_id);

    } else if($action == "update") {
      $GLOBALS['core']->sqlUpdate($save_array, "mod_menuman_items", "menu_item_id", $this->menu_item_id);
    }    
  } // END FUNC save_item


  /**
   * set_item_activity
   *
   * Sets the activity of this item
   */
  function set_item_activity() {
    PHPWS_WizardBag::toggle($this->menu_item_active);
    $save_array = array("menu_item_active"=>"$this->menu_item_active");
    $GLOBALS['core']->sqlUpdate($save_array, "mod_menuman_items", "menu_item_id", $this->menu_item_id);
  } // END FUNC set_activity


  /**
   * move_item_up
   *
   * Move menu item up one position on its level
   */
  function move_item_up() {
    $menu_id=$_REQUEST['MMN_menu_id'];

    $sql = "SELECT menu_item_order FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id=$this->menu_item_id";
    $item = $GLOBALS['core']->quickFetch($sql);
    extract($item);

    /* checking if at the top */
    if($menu_item_order == 1) {
      if($this->menu_item_id == $this->menu_item_pid) {
	$sql = "SELECT max(menu_item_order) FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id=menu_item_pid AND menu_id='$menu_id'";
      } else {
	$sql = "SELECT max(menu_item_order) FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!=menu_item_pid";
      }

      $max = $GLOBALS['core']->quickFetch($sql);
      $max = $max['max(menu_item_order)'];

      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=$max WHERE menu_item_id='$this->menu_item_id'";
      $GLOBALS['core']->query($sql);

      if($this->menu_item_id == $this->menu_item_pid) {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order-1 WHERE menu_item_id=menu_item_pid AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id'";
      } else {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order-1 WHERE menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id' AND menu_item_id!=menu_item_pid";
      }
      $GLOBALS['core']->query($sql);
    } else {
      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order-1 WHERE menu_item_id='$this->menu_item_id'";
      $GLOBALS['core']->query($sql);

      if($this->menu_item_id == $this->menu_item_pid){
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order+1 WHERE menu_item_order=$menu_item_order-1 AND menu_item_id=menu_item_pid AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id'";
      } else {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order+1 WHERE menu_item_order=$menu_item_order-1 AND menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id' AND menu_item_id!=menu_item_pid";
      }
      $GLOBALS['core']->query($sql);
    }
  } // END FUNC move_item_up
  
  
  /**
   * move_item_down
   *
   * Move menu item down one position on its level
   */
  function move_item_down() {
    $menu_id=$_REQUEST['MMN_menu_id'];

    $sql = "SELECT menu_item_order FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id='$this->menu_item_id'";
    $item = $GLOBALS['core']->quickFetch($sql);
    extract($item);
    
    if($this->menu_item_id == $this->menu_item_pid) {
      $sql = "SELECT max(menu_item_order) FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id=menu_item_pid AND menu_id='$menu_id'";
    } else {
      $sql = "SELECT max(menu_item_order) FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!=menu_item_pid";
    }
    $max = $GLOBALS['core']->quickFetch($sql);
    
    /* checking if at the bottom */
    if($menu_item_order == $max['max(menu_item_order)']) {
      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order='1' WHERE menu_item_id='$this->menu_item_id'";
      $GLOBALS['core']->query($sql);

      if($this->menu_item_id == $this->menu_item_pid) {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order+1 WHERE menu_item_id=menu_item_pid AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id'";
      } else {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order+1 WHERE menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id' AND menu_item_id!=menu_item_pid";
      }
      $GLOBALS['core']->query($sql);
    } else {
      $sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order+1 WHERE menu_item_id='$this->menu_item_id'";
      $GLOBALS['core']->query($sql);

      if($this->menu_item_id == $this->menu_item_pid) {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order-1 WHERE menu_item_order=$menu_item_order+1 AND menu_item_id=menu_item_pid AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id'";
      } else {
	$sql = "UPDATE {$GLOBALS['core']->tbl_prefix}mod_menuman_items SET menu_item_order=menu_item_order-1 WHERE menu_item_order=$menu_item_order+1 AND menu_item_pid='$this->menu_item_pid' AND menu_id='$menu_id' AND menu_item_id!='$this->menu_item_id' AND menu_item_id!=menu_item_pid";
      }
      $GLOBALS['core']->query($sql);
    }
  } // END FUNC move_item_down
} // END CLASS PHPWS_Menu_Item

?>