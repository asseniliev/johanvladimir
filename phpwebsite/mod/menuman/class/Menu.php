<?php

require_once (PHPWS_SOURCE_DIR . "core/EZform.php");
require_once (PHPWS_SOURCE_DIR . "core/Form.php");
require_once (PHPWS_SOURCE_DIR . "core/Text.php");
require_once (PHPWS_SOURCE_DIR . "core/File.php");
require_once (PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once (PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/* Default Menu Activity */
define("MMN_DEF_MENU_ACT", 1);
/* Default Item Activity */
define("MMN_DEF_ITEM_ACT", 1);

/**
 * Class:  PHPWS_Menu
 *
 * Controls all the information and actions needed to be done to a single menu.
 *
 * Patch - 2 Mar 2005 Eloi George - reduce db calls
 *
 * @version $Id: Menu.php,v 1.64 2005/03/14 21:00:10 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Menu Manager
 */
class PHPWS_Menu {

  /**
   * id of the current menu
   * @var integer
   */
  var $menu_id;

  /**
   * title of this menu
   * @var string
   */
  var $menu_title;

  /**
   * flag for controlling activity of the menu
   * @var boolean
   */
  var $menu_active;

  /**
   * indention scheme for the current menu
   * @var array
   */
  var $menu_indent;

  /**
   * key to the current indent item
   * @var integer
   */
  var $indent_key;

  /**
   * key to the current activity color
   * @var integer
   */
  var $color_key;

  /**
   * name of the image being used for inactive menu item
   * @var string
   */
  var $menu_image;

  /**
   * name of image for active menu items
   * @var string
   */
  var $active_image;

  /**
   * name of image for open menu items
   * @var string
   */
  var $open_image;

  /**
   * scaller for space put at each level of the menu
   * @var integer
   */
  var $menu_spacer;

  /**
   * flag controler whether or not menu is an image map
   * @var boolean
   */
  var $image_map;

  /**
   * content variable for the current menu
   * @var string
   */
  var $content_var;

  /**
   * list of modules needing to be active for menu to appear
   * @var array
   */
  var $allow_view;

  /**
   * all of the PHPWS_Menu_Item objects for this menu
   * @var array
   */
  var $menu_items;

  /**
   * flag controlling of menu is horizontal
   * @var boolean
   */
  var $horizontal;

  /**
   * current template being used
   * @var string
   */
  var $template;

  /**
   * all the vertical menu content
   * @var array
   */
  var $vert_content;

  /**
   * all the hrizontal menu content
   * @var array
   */
  var $horiz_content;

  /**
   * list of current items to be deleted
   * @var array
   */
  var $delete_items;

  /**
   * the date and time the menu was updated
   * @var integer
   */
  var $updated;

  /**
   * controls whether anonymous users can view menus
   * @var boolean
   */
  var $anon_view;
  
  /**
   * PHPWS_Menu
   *
   * Constructor for the PHPWS_Menu class 
   *
   * Initializes class variables and then calls build_items which
   * builds the menu_items array.  When an id id passed construct
   * that menu otherwise create a new menu
   *
   * @param integer $menu_id id of the menu to be constructed
   */
  function PHPWS_Menu($menu_id=NULL) {
    if(is_numeric($menu_id)) {
      $this->menu_id = $menu_id;
      $menu_result = $GLOBALS['core']->sqlSelect("mod_menuman_menus", "menu_id", $this->menu_id);
      $this->menu_title = $menu_result[0]["menu_title"];
      $this->menu_active = $menu_result[0]["menu_active"];
      $this->menu_indent = $menu_result[0]["menu_indent"];
      $this->indent_key = $menu_result[0]["indent_key"];
      $this->color_key = $menu_result[0]["color_key"];
      $this->menu_image = $menu_result[0]["menu_image"];
      $this->active_image = $menu_result[0]["active_image"];
      $this->open_image = $menu_result[0]["open_image"];
      $this->menu_spacer = $menu_result[0]["menu_spacer"];
      $this->image_map = $menu_result[0]["image_map"];
      $this->anon_view = $menu_result[0]["anon_view"];

      /* pull indent from the manager class */
      include(PHPWS_SOURCE_DIR . "mod/menuman/conf/config.php");
      if($this->menu_indent == "character") {
	$this->menu_indent = array("item"=>"character",
				   "indent"=>$indent_options[$this->indent_key],
				   "color"=>"$this->color_key"
				   );
      } else if($this->menu_indent == "image") {
	$this->menu_indent = array("item"=>"image",
				   "image"=>"$this->menu_image",
				   "image_active"=>"$this->active_image",
				   "image_open"=>"$this->open_image",
				   );
      } else {
	$this->menu_indent = array("item"=>"none");
      }

      $this->content_var = $menu_result[0]["content_var"];
      $this->horizontal = $menu_result[0]["horizontal"];
      $this->template = $menu_result[0]["template"];
      $this->allow_view = unserialize($menu_result[0]["allow_view"]);

      if (isset($menu_result[0]["updated"]))
	$this->updated = $menu_result[0]["updated"];

      $this->build_items();

    } else if(is_array($menu_id)) {
      foreach($menu_id as $key => $value) {
	$this->$key = $value;
      }      
    } else {
      $this->menu_title = NULL;
      $this->menu_active = MMN_DEF_MENU_ACT;
      $this->menu_indent['item'] = "none";
      $this->indent_key = 1;
      $this->color_key = "#";
      $this->menu_image = NULL;
      $this->active_image = NULL;
      $this->open_image = NULL;
      $this->menu_spacer = NULL;
      $this->content_var = NULL;
      $this->allow_view = array();
      $this->highlight = NULL;
      $this->horizontal = "FALSE";
      $this->template = NULL;
      $this->menu_items = array();
      $this->horiz_content = array();
      $this->vert_content = array();
    }

    if(isset($_SESSION['OBJ_menuman']->call_back))
      $_SESSION['OBJ_menuman']->call_back = null;

  } // END FUNC PHPWS_Menu


  /**
   * is_active 
   *
   * Returns the menus activity 
   *
   * @return boolean the activity of the menu
   */
  function is_active(){return $this->menu_active;}


  /**
   * can_view
   *
   * Returns whether the menu is viewable by the current user
   *
   * @return boolean
   */
  function can_view() {
    return ((!$this->anon_view && $_SESSION["OBJ_user"]->isUser()) || $this->anon_view);
  }

  // END FUNC get_item_child
  /**
   * get_item_child 
   *
   * Gets children for the build_items function
   * Patch by Eloi George
   * 
   * @param integer $parent_id id of the parent item
   */
  function get_item_child($parent_id, &$level, &$child_data) {
    if(!isset($child_data[$parent_id])) {
      return false;
    }

    $level++;
    foreach($child_data[$parent_id] as $menu_item) {
      $this->menu_items[$menu_item['menu_item_id']] = & new PHPWS_MenuItem($menu_item, $level);
      if(isset($child_data[$menu_item['menu_item_id']])) {
	$this->get_item_child($menu_item['menu_item_id'], $level, $child_data);
	$level--;
      }
    }
    unset($child_data[$parent_id]);
  } // END FUNC get_item_child

  /**
   * build_items 
   *
   * Builds the menu_items array
   * Patched by Eloi George aka adarkling
   */
  function build_items() {
    $level = 0;
    unset($this->menu_items);
    $sql = 'SELECT * FROM ' . $GLOBALS['core']->tbl_prefix 
      . 'mod_menuman_items WHERE menu_id=\'' . $this->menu_id
      . '\' ORDER BY menu_item_order';
    $menu_item_result = $GLOBALS['core']->query($sql);
    if($menu_item_result->numrows() == 0)
      return false;

    /* Initialize toplevel and child_data arrays */
    $toplevel = $child_data = array();
    while($menu_item = $menu_item_result->fetchrow(DB_FETCHMODE_ASSOC)) {
      if ($menu_item['menu_item_id'] == $menu_item['menu_item_pid']) {
	$toplevel[] = $menu_item;
      }
      else {
	if(!isset($child_data[$menu_item['menu_item_pid']])) {
	  $child_data[$menu_item['menu_item_pid']] = array();
	}
	$child_data[$menu_item['menu_item_pid']][] = $menu_item;
      }
    }
    unset($menu_item_result);

    foreach($toplevel as $menu_item) {
      $this->menu_items[$menu_item['menu_item_id']] = & new PHPWS_MenuItem($menu_item, $level);
      if(isset($child_data[$menu_item['menu_item_id']])) {
	$this->get_item_child($menu_item['menu_item_id'], $level, $child_data);
	$level--;
      }
    }
  } // END FUNC build_items


  /**
   * get_menu_display_child 
   *
   * Gets children for the build_display_menu function
   *
   * @param boolean $expand whether or not to expand this level
   * @param integer $parent_id id of the parent item
   * @param integer &$level the level of the current item
   * @param array $display ids of items to be displayed
   */
  function get_menu_display_child($expand, $parent_id, &$level, $display) {
    $template = $this->template . "/";

    /* increment level counter to reflect menu level */
    $level++;
    /* indentation calculation */
    $spacer = "";
    for($x = 0; $x < ($level * $this->menu_spacer); $x++) {
      $spacer .= "&#160;";
    }
    
    /* sql query */
    $sql = "SELECT menu_item_id FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_pid='$parent_id' AND menu_item_id!=menu_item_pid AND menu_id='$this->menu_id' ORDER BY menu_item_order";
    $menu_item_result = $GLOBALS['core']->getcol($sql);

    if($menu_item_result) {
	    if($this->horizontal == "FALSE") {
		    $this->vert_content[] = $level;
	    }
      foreach($menu_item_result as $menu_item_id) {
	$position = "&amp;MMN_position=" . $menu_item_id;	  
	
	reset($display);
	foreach($display as $value) {
	  $position .= ":" . $value;
	}
	

	if($this->menu_items[$menu_item_id]->is_active()) {
	  $content = $this->menu_items[$menu_item_id]->display_menu_item($level, $position, $template, $this->menu_indent, $spacer);
	
	  if($this->horizontal == "FALSE") {
	    $this->vert_content[] = $content;
	  } else {
	    $this->horiz_content[][$level] = $content;
	  }
	}

	/* check to see if parent expands */
	if($expand || (in_array($menu_item_id, $_SESSION['SES_parentlevel_id']) && $this->menu_items[$menu_item_id]->menu_item_active)) {
	  $new_display = $display;
	  array_push($new_display, $menu_item_id);
	  $this->get_menu_display_child($expand, $menu_item_id, $level, $new_display);
	  $level--;
	} else {
	  //$level--;
	}
      }

    if($this->horizontal == "FALSE") {
	    $this->vert_content[] = - $level;
    }
    }	// foreach
  } // END FUNC get_menu_display_child


  /**
   * build_display_menu
   *
   * Builds the menu for displaying
   * 
   * @param boolean $expand whether or not this level expands
   */
  function build_display_menu($expand) {
    $template = $this->template . "/";

    /* sql query */
    $sql = "SELECT menu_item_id FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id=menu_item_pid AND menu_id='$this->menu_id' ORDER BY menu_item_order";
    $menu_item_result = $GLOBALS['core']->getcol($sql);

    if($menu_item_result) {
      unset($this->vert_content);
      unset($this->horiz_content);
      foreach($menu_item_result as $menu_item_id) {
	$level = 0;
	$position = "&amp;MMN_position=" . $menu_item_id . ":" . $menu_item_id;	  

	if($this->menu_items[$menu_item_id]->is_active()) {
	  $content = $this->menu_items[$menu_item_id]->display_menu_item($level, $position, $template, $this->menu_indent, "");
	
	  if($this->horizontal == "FALSE") {
	    $this->vert_content[] = $content;
	  } else {
	    $this->horiz_content[][$level] = $content;
	  }
	}
	
	/* check to see if parent expands */
	if($expand || (in_array($menu_item_id, $_SESSION['SES_parentlevel_id']) && $this->menu_items[$menu_item_id]->menu_item_active)) {
	  $display = array($menu_item_id);
	  $this->get_menu_display_child($expand, $menu_item_id, $level, $display);
	}
      }
    }
  } // END FUNC build_display_menu


  /**
   * menu_disp
   *
   * Builds the menu content into the proper content variable 
   */
  function menu_disp($siteMap=FALSE) {
    $template = PHPWS_Template::getTemplateDir("menuman", $this->template . "/") . "/" . $this->template . "/";

    if($this->horizontal == "FALSE") {
		$table_template = PHPWS_File::readFile($template . "tableheader.tpl");
		$tableclose_template = PHPWS_File::readFile($template . "tableclose.tpl");
      $row_template = PHPWS_File::readFile($template . "tablerow.tpl");
      $rowclose_template = PHPWS_File::readFile($template . "tablerowclose.tpl");
      $box_content = "";
      if(is_array($this->vert_content)) {
	foreach($this->vert_content as $value) {
		if ( is_numeric( $value ) ) {
		   if ( $value > 0 ) {
		   	$box_content .= "<!-- lev " . $value . "-->" . PHPWS_File::readFile($template . "tableheader" . $value . ".tpl");
		   } else {
		   	$box_content .= PHPWS_File::readFile($template . "tableclose" . (-$value) . ".tpl") . "<!-- /lev" . $value . " -->";
		   }
		} else {
         $box_content .= $row_template . $value . $rowclose_template;
		}
	}
		$box_content = $table_template . $box_content . $tableclose_template;
   } else {
	$box_content = $_SESSION['translate']->it("No menu items for this menu.");
      }
    } else {
      $content = array();
      if(is_array($this->horiz_content)) {
	foreach($this->horiz_content as $value) {
	  foreach($value as $key => $menu_item) {
	    if(!isset($content[$key]))
	      $content[$key] = $menu_item;
	    else
	      $content[$key] .= $menu_item;
	  }
	}
      }

      $box_content = "";
      if(is_array($content)) {
	foreach($content as $key => $value) {
	  $table_template = PHPWS_File::readFile($template . "tableheader" . $key . ".tpl");
	  $row_template = PHPWS_File::readFile($template . "tablerow" . $key . ".tpl");
	  $tableclose_template = PHPWS_File::readFile($template . "tableclose" . $key . ".tpl");
	  $rowclose_template = PHPWS_File::readFile($template . "tablerowclose" . $key . ".tpl");
	  $box_content .= $table_template . $row_template . $value . $rowclose_template . $tableclose_template;
	}
      }
    }

    if(!$siteMap) {
      $GLOBALS[$this->content_var]['title']   =  $this->menu_title;
      $GLOBALS[$this->content_var]['content'] = $box_content;
    } else {
      $GLOBALS['CNT_menuman_options']['title'] = $_SESSION['translate']->it("Site Map");
      if(!isset($GLOBALS['CNT_menuman_options']['content'])) { 
	$GLOBALS['CNT_menuman_options']['content'] = "<h3>$this->menu_title</h3>$box_content";
      } else {
	$GLOBALS['CNT_menuman_options']['content'] .= "<hr /><h3>$this->menu_title</h3>$box_content";
      }
    }
  } // END FUNC menu_disp


  /**
   * menu
   *
   * Creates new menu and edit current menus, provides the form to 
   * create a menu when action = "create" is passed and the form to 
   * edit a menu when action = "edit" is passed
   *
   * @param string $action the action to be taken on the menu
   */
  function menu($action) {
    $elements[0] = PHPWS_Form::formHidden("module", "menuman");

    if($action == "create") {
      $title = $_SESSION['translate']->it("Create A New Menu");
      $elements[0] .= PHPWS_Form::formHidden("MMN_menuman_op", "insert_menu");
    } else if($action == "edit") {
      if(!$_SESSION['OBJ_user']->allow_access("menuman", "menu_settings")) return;

      $title = $_SESSION['translate']->it("Edit [var1] Settings", $this->menu_title) . "&#160;&#160;<a href=\"index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_editMenu=1&amp;MMN_menu_id=" . $this->menu_id . "\">" . $_SESSION['translate']->it("Back To Main") . "</a>";
      $elements[0] .= PHPWS_Form::formHidden("MMN_menuman_op", "update_menu");
      $elements[0] .= PHPWS_Form::formHidden("MMN_menu_id", $this->menu_id);
    }
    

    if(is_dir($GLOBALS['core']->home_dir.$_SESSION['OBJ_menuman']->get_upload_directory())) {
      $image_options = PHPWS_File::readDirectory($GLOBALS['core']->home_dir.$_SESSION['OBJ_menuman']->get_upload_directory(), FALSE, TRUE);
    } else {
      $_SESSION['OBJ_menuman']->error_message($_SESSION['translate']->it("The images directory set in the menuman config file could not be found this will cause all image options to fail."));
    }

    if(isset($this->template)) {
      $template = $this->template;
    } else {
      $template = "default";
    }

    $template_options = PHPWS_Template::listTemplates("menuman", 1);
      
    $menuSettings['MENU_TITLE_TEXT'] = $_SESSION['translate']->it("Menu Title");
    $menuSettings['MENU_TITLE_INPUT'] = PHPWS_Form::formTextField("MMN_title", $this->menu_title, 25, 50);

    $menuSettings['MENU_SPACER_TEXT'] = $_SESSION['translate']->it("Menu Spacer");
    $menuSettings['MENU_SPACER_HELP'] = CLS_help::show_link("menuman", "menu_spacer");
    $menuSettings['MENU_SPACER_INPUT'] = PHPWS_Form::formTextField("MMN_spacer", $this->menu_spacer, 3, 2);

    $menuSettings['MENU_INDENT_TEXT'] = $_SESSION['translate']->it("Menu Indent");
    $menuSettings['MENU_INDENT_HELP'] = CLS_help::show_link("menuman", "menu_indent");
    $menuSettings['MENU_INDENT_RADIO_CHAR'] = PHPWS_Form::formRadio("MMN_indent", "character", $this->menu_indent["item"]) . "&#160;" . $_SESSION['translate']->it("Character");
    $menuSettings['MENU_INDENT_RADIO_IMAGE'] = PHPWS_Form::formRadio("MMN_indent", "image", $this->menu_indent["item"]) . "&#160;" . $_SESSION['translate']->it("Image");
    $menuSettings['MENU_INDENT_RADIO_NONE'] = PHPWS_Form::formRadio("MMN_indent", "none", $this->menu_indent["item"]) . "&#160;" . $_SESSION['translate']->it("None");
    $menuSettings['MENU_INDENT_CHAR_TEXT'] = $_SESSION['translate']->it("Indent Item");
    $menuSettings['MENU_INDENT_CHAR_HELP'] = CLS_help::show_link("menuman", "menu_char");
    $menuSettings['MENU_INDENT_CHAR_SELECT'] = PHPWS_Form::formSelect("MMN_indent_key", $_SESSION['OBJ_menuman']->get_indent_options(), $this->indent_key, NULL, 1, NULL);
    $menuSettings['MENU_INDENT_HL_TEXT'] = $_SESSION['translate']->it("Highlight Color");
    $menuSettings['MENU_INDENT_HL_SELECT'] = PHPWS_Form::formSelect("MMN_color_key", $_SESSION['OBJ_menuman']->get_color_options(), $this->color_key, NULL, 1, NULL);

    $menuSettings['MENU_IMAGE_HELP'] = CLS_help::show_link("menuman", "menu_image");
    $menuSettings['MENU_IMAGE_DEF_TEXT'] = $_SESSION['translate']->it("Default Image");
    $menuSettings['MENU_IMAGE_DEF_SELECT'] = PHPWS_Form::formSelect("MMN_image", $image_options, $this->menu_image, 1);
    $menuSettings['MENU_IMAGE_OPEN_TEXT'] = $_SESSION['translate']->it("Open Image");
    $menuSettings['MENU_IMAGE_OPEN_SELECT'] = PHPWS_Form::formSelect("MMN_open_image", $image_options, $this->open_image, 1);
    $menuSettings['MENU_IMAGE_ACTIVE_TEXT'] = $_SESSION['translate']->it("Active Image");
    $menuSettings['MENU_IMAGE_ACTIVE_SELECT'] = PHPWS_Form::formSelect("MMN_active_image", $image_options, $this->active_image, 1);

    if(!is_array($image_options)) {
      $menuSettings["MENU_NO_IMAGES_MSG"] = $_SESSION["translate"]->it("No Images Found In:");
      $menuSettings["MENU_NO_IMAGES_DIR"] = $GLOBALS['core']->home_dir.$_SESSION["OBJ_menuman"]->get_upload_directory();
    }

    $menuSettings['MENU_TEMP_TEXT'] = $_SESSION['translate']->it("Template");
    $menuSettings['MENU_TEMP_HELP'] = CLS_help::show_link("menuman", "menu_template");
    $menuSettings['MENU_TEMPLATE'] = PHPWS_Form::formRadio("MMN_horizontal", "FALSE", $this->horizontal) . "&#160;" . $_SESSION['translate']->it("Vertical") . "<br />";
    $menuSettings['MENU_TEMPLATE'] .= PHPWS_Form::formRadio("MMN_horizontal", "TRUE", $this->horizontal) . "&#160;" .  $_SESSION['translate']->it("Horizontal") . "<br />";
    $menuSettings['MENU_TEMPLATE_SELECT'] = PHPWS_Form::formSelect("MMN_template", $template_options, $template, 1);

    $menuSettings['MENU_IMAGE_MAP'] = PHPWS_Form::formCheckBox("MMN_image_map", "1", $this->image_map) . $_SESSION['translate']->it("Image Map");

    $menuSettings['MENU_ALLOW_VIEW_TEXT'] = $_SESSION['translate']->it("Allow View (All are selected by default)");
     $menuSettings['MENU_ALLOW_VIEW_HELP'] = CLS_help::show_link("menuman", "allow_view");
    $menuSettings['MENU_ALLOW_VIEW_SELECT'] = PHPWS_Form::formMultipleSelect("MMN_allow_view", $_SESSION['OBJ_menuman']->get_modules_allowed(), $this->allow_view, 1, NULL, 5);
    
    if($action == "create")
      $this->anon_view = TRUE;

    $menuSettings['MENU_ALLOW_ANON_VIEW'] = PHPWS_Form::formCheckBox("MMN_allow_anon_view", "1", $this->anon_view) . $_SESSION['translate']->it("Allow Anonymous Access to this Menu");

    if($action == "create") {
      $menuSettings['MENU_THEME_VAR_TEXT'] = $_SESSION['translate']->it("Default Theme Variable");
      $menuSettings['MENU_THEME_VAR_SELECT'] = PHPWS_Form::formSelect("MMN_transfer_var", $_SESSION['OBJ_layout']->getThemeVars(), NULL, 1) . "<br /><br />";   
      $elements[1] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save New Menu"));
    } else if($action == "edit") {
      $elements[1] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save Changes"));
    }

    $elements[0] .= PHPWS_Template::processTemplate($menuSettings, "menuman", "settings.tpl");

    $content = PHPWS_Form::makeForm("MMN_menu_addedit", "index.php", $elements, "post", NULL, NULL);
    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
  } // END FUNC menu


  /**
   * save_menu
   *
   * Saves a menu's information to the database
   *
   * @param string $action insert or update whichever save method being used
   */
  function save_menu($action) {
    $this->menu_title = addslashes(PHPWS_Text::parseInput($_POST['MMN_title']));
    $this->menu_spacer = $_POST['MMN_spacer'];
    $this->menu_active = $this->menu_active;

    $this->menu_indent['item'] = $_POST['MMN_indent'];

    if($this->menu_indent['item'] == "character") {
      $this->indent_key = $_POST['MMN_indent_key'];
      $this->color_key = $_POST['MMN_color_key'];
    } else {
      $this->indent_key = NULL;
      $this->color_key = NULL;
    }

    if($this->menu_indent['item'] == "image") {
      if(isset($_POST['MMN_image']))
	$this->menu_image = $_POST['MMN_image'];
      else
	$this->menu_image = NULL;

      if(isset($_POST['MMN_image']))
	$this->active_image = $_POST['MMN_active_image'];
      else
	$this->active_image = NULL;

      if(isset($_POST['MMN_open_image']))
	$this->open_image = $_POST['MMN_open_image'];
      else
	$this->open_image = NULL;

    } else {
      $this->menu_image = NULL;
      $this->active_image = NULL;
      $this->open_image = NULL;
    }

    $this->horizontal = $_POST['MMN_horizontal'];

    if($this->horizontal == "TRUE") {
      $this->template = $_POST['MMN_template'];
    } else {
      $this->template = $_POST['MMN_template'];

      if(isset($_POST['MMN_image_map'])) {
	$this->image_map = $_POST['MMN_image_map'];
      } else {
	$this->image_map = null;
      }
    }

    if(!isset($_POST['MMN_allow_view'])) {
      $this->allow_view = $_SESSION['OBJ_menuman']->get_modules_allowed();
      array_shift($this->allow_view);
      array_shift($this->allow_view);
    } else {
      $this->allow_view = $_POST['MMN_allow_view'];
    }

    if(isset($_POST['MMN_allow_anon_view'])) {
      $this->anon_view = $_POST['MMN_allow_anon_view'];
    } else {
      $this->anon_view = 0;
    }

    $this->updated = time();
    $save_allow = serialize($this->allow_view);

    $save_array = array("menu_title"=>"$this->menu_title",
			"menu_spacer"=>"$this->menu_spacer",
			"menu_active"=>"$this->menu_active",
			"menu_indent"=>$this->menu_indent['item'],
			"indent_key"=>"$this->indent_key",
			"color_key"=>"$this->color_key",
			"menu_image"=>"$this->menu_image",
			"active_image"=>"$this->active_image",
			"open_image"=>"$this->open_image",
			"horizontal"=>"$this->horizontal",
			"image_map"=>"$this->image_map",
			"template"=>"$this->template",
			"allow_view"=>"$save_allow",
			"updated"=>"$this->updated",
			"anon_view"=>$this->anon_view
			);

    $this->menu_title = stripslashes($this->menu_title);

    if($action == "insert") {
      if(!$max_menu_id = $GLOBALS['core']->sqlInsert($save_array, "mod_menuman_menus", FALSE, TRUE, FALSE)) {
	$_SESSION['OBJ_menuman']->error($_SESSION['translate']->it("This menu was already found in the database.") . "&#160;" . $_SESSION['translate']->it("You may receive this error as a result of refreshing your browser."));
	return;
      } else {
	$this->content_var = "CNT_menuman_" . $max_menu_id;
	$save_array = array("content_var"=>"$this->content_var");
	$GLOBALS['core']->sqlUpdate($save_array, "mod_menuman_menus", "menu_id", $max_menu_id);
	$_SESSION['OBJ_layout']->create_temp("menuman", $this->content_var, $_POST['MMN_transfer_var']);

	$title = $_SESSION['translate']->it("Menu Successfully Created");
	$content = $_SESSION['translate']->it("The menu [var1] you created was successfully added to the database.", "<b><i>" . $this->menu_title . "</i></b>");
      }

    } else if($action == "update") {
      if(!$GLOBALS['core']->sqlUpdate($save_array, "mod_menuman_menus", "menu_id", $this->menu_id)) {
	$_SESSION['OBJ_menuman']->error($_SESSION['translate']->it("The menu [var1] you edited could not be updated to the database.", "<b><i>" . $this->menu_title . "</i></b>") . "&#160;" . $_SESSION['translate']->it("Please contact your systems administrator.")); 
	return;
      } else {
	$title = $_SESSION['translate']->it("Update Settings Successful");
	$content = $_SESSION['translate']->it("The menu [var1] you edited was successfully updated to the database.", "<b><i>" . $this->menu_title . "</i></b>");
      }
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";

    $this->PHPWS_Menu($this->menu_id);
  } // END FUNC save_menu


  /**
   * set_activity
   *
   * Sets a menu's activity accordingly
   */
  function set_activity() {
    PHPWS_WizardBag::toggle($this->menu_active);
    $save_array = array("menu_active"=>"$this->menu_active");
    $GLOBALS['core']->sqlUpdate($save_array, "mod_menuman_menus", "menu_id", $this->menu_id);
  } // END FUNC set_activity


  /**
   * delete_menu
   *
   * Deletes a menu from the database.
   * There must be a POST "MMN_menu_yes" or "MMN_menu_no" to take any action
   * otherwise it confirms with the user whether or not to delete the menu
   */
  function delete_menu() {
    if(isset($_POST['MMN_menu_yes'])) {
      if(!$GLOBALS['core']->sqlDelete("mod_menuman_menus", "menu_id", $this->menu_id)) { 
	$this->error($_SESSION['translate']->it("The menu [var1] could not be deleted from the database please contact your systems administrator.", "<b><i>" . $this->menu_title . "</i></b>"));
	return;
      }
      if(!$GLOBALS['core']->sqlDelete("mod_menuman_items", "menu_id", $this->menu_id)) { 
	$this->error($_SESSION['translate']->it("The menu items for [var1] could not be deleted from the database please contact your systems administrator.", "<b><i>" . $this->menu_title . "</i></b>"));
	return;
      } 

      if (!PHPWS_Layout::dropBox($this->content_var)){
	$this->error($_SESSION['translate']->it("The content variable could not be deleted for this menu."));
      } else {
	unset($_SESSION['OBJ_menuman']->menus[$this->menu_id]);
	$title = $_SESSION['translate']->it("Menu Deleted");
	$content = $_SESSION['translate']->it("The menu [var1] was successfully deleted from the database.", "<b><i>" . $this->menu_title . "</i></b>");
      }
    } else if(isset($_POST['MMN_menu_no'])) {
      $title = $_SESSION['translate']->it("No Menu Deleted");
      $content = $_SESSION['translate']->it("No menu was deleted from the database.");
    } else {
      $title = $_SESSION['translate']->it("Delete Menu Confirmation");
      $content = $_SESSION['translate']->it("Are you sure you want to delete the menu [var1] ?", "<b><i>" . $this->menu_title . "</i></b>");

      $elements[0] = "<br />";
      $elements[0] .= PHPWS_Form::formHidden("module", "menuman");
      $elements[0] .= PHPWS_Form::formHidden("MMN_menuman_op", "menuAction");
      $elements[0] .= PHPWS_Form::formHidden("MMN_deleteMenu", 1);
      $elements[0] .= PHPWS_Form::formHidden("MMN_menu_id", $this->menu_id);
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Yes"), "MMN_menu_yes");
      $elements[0] .= "&#160;&#160;";
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("No"), "MMN_menu_no");

      $content .= PHPWS_Form::makeForm("MMN_menu_delete", "index.php", $elements, "post", NULL, NULL);
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";

    if(isset($_POST['MMN_menu_yes']) || isset($_POST['MMN_menu_no'])) {
      $_SESSION['OBJ_menuman']->list_menus();
    }
  } // END FUNC delete_menu


  /**
   * list_menu_items
   *
   * Lists the items in the menu
   */
  function list_menu_items() {
    $title = $_SESSION['translate']->it("Menu Items for [var1].", $this->menu_title);

    $hiddens = array("module"=>"menuman",
		     "MMN_menuman_op"=>"menu_items",
		     "MMN_menu_id"=>"$this->menu_id"
		     );

    $elements[0] = PHPWS_Form::formHidden($hiddens);

    $elements[0] .= "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"1\">";
    $elements[0] .= "<tr class=\"bg_dark\"><td width=\"5%\"><b>" . $_SESSION['translate']->it("Select") . "</b></td>";
    $elements[0] .= "<td width=\"20%\"><b>" . $_SESSION['translate']->it("Title") . "</b></td>";

    $_SESSION['OBJ_menuman']->refreshPageOptions();

    if($_SESSION['OBJ_menuman']->uber_edit) {
      $elements[0] .= "<td width=\"50%\"><b>" . $_SESSION['translate']->it("URL") . "</b></td>";
      if($GLOBALS['core']->moduleExists("pagemaster")) {
	if($_SESSION["OBJ_user"]->js_on && $GLOBALS['core']->moduleExists("pagemaster")) {
	  $_SESSION['OBJ_layout']->addJavaScript($this->jsIntExt());
	}
	
	$elements[0] .= "<td width=\"30%\"><b>" . $_SESSION['translate']->it("Internal Web Page") . "</b></td>";
      }

    } else {
      $elements[0] .= "<td width=\"25%\"><b>" . $_SESSION['translate']->it("Order") . "</b></td>";
      $elements[0] .= "<td width=\"25%\"><b>" . $_SESSION['translate']->it("Activity") . "</b></td>";
    }

    if($_SESSION['OBJ_menuman']->uber_edit) {
      $elements[0] .= "<td width=\"10%\"><b>" . $_SESSION['translate']->it("Display") . "</b></td>";
    }

    $elements[0] .= "<td width=\"5%\"><b>" . $_SESSION['translate']->it("ID") . "</b></td></tr>";

    $highlight = NULL;
    $items = 0;
    if(is_array($this->menu_items)) {
      reset($this->menu_items);
      foreach($this->menu_items as $id => $menu_item) {
	if($id != 0) {
	  $elements[0] .= $menu_item->menu_item("edit", $highlight, $this->menu_id, $this->image_map);
	  PHPWS_WizardBag::toggle($highlight, " class=\"bg_light\"");
	  $items = 1;
	}
      }
      
      if($items) {
	$elements[0] .= "<tr><td colspan=\"6\" align=\"left\">";
	$elements[0] .= PHPWS_WizardBag::js_insert("check_all", "MMN_menu_items") . "&#160;&#160;<br /><br />";

	if($_SESSION['OBJ_user']->allow_access("menuman", "item_activity") && !$_SESSION['OBJ_menuman']->uber_edit) {
	  $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Activate / Deactivate"), "activity") . "&#160;&#160;";      
	}
	
	if($_SESSION['OBJ_user']->allow_access("menuman", "update_item")) {
	  $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Update Menu Items"), "update") . "&#160;&#160;";
	}
	
	if($_SESSION['OBJ_user']->allow_access("menuman", "delete_item")) {
	  $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Delete Menu Items"), "delete") . "&#160;&#160;";      
	}
	
	$elements[0] .= "</td></tr>";
      } else {
	$elements[0] .= "<tr><td colspan=\"6\" align=\"left\">" . $_SESSION['translate']->it("No items for this menu.") . "</td></tr>";
      }
    } else {
      $elements[0] .= "<tr><td colspan=\"6\" align=\"left\">" . $_SESSION['translate']->it("No items for this menu.") . "</td></tr>";
    }

    $elements[0] .= "</table><br />";

    if($_SESSION['OBJ_user']->allow_access("menuman", "add_item")) {
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Add Menu Item"), "add") . "&#160;&#160;";
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Add Module Default"), "moduleDefault") . "&#160;&#160;";
    }

    if($_SESSION['OBJ_user']->allow_access("menuman", "menu_settings")) {
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Settings"), "settings") . "&#160;&#160;";
    }

    if($_SESSION['OBJ_user']->allow_access("menuman", "uber_edit")) {
      if($_SESSION['OBJ_menuman']->uber_edit) {
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Turn Off Advanced Edit"), "set_uber_edit");
      } else {
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Turn On Advanced Edit"), "set_uber_edit");
      }
    }

    $content = PHPWS_Form::makeForm("MMN_menu_items", "index.php", $elements, "post", NULL, NULL);
    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";

  } // END FUNC list_menu_items


  /**
   * update_menu_items
   *
   * Updates all menu items to the database that where selected
   * also handles changing the activity of an item
   */
  function update_menu_items($activity_flag) {
    if($_SESSION['OBJ_user']->allow_access("menuman", "update_item") || $_SESSION['OBJ_user']->allow_access("menuman", "item_activity")) {
      if(isset($_POST['MMN_item_id']) && is_array($_POST['MMN_item_id'])) {
	foreach($_POST['MMN_item_id'] as $menu_item_id) {
	  if($activity_flag) {
	    $this->menu_items[$menu_item_id]->set_item_activity();
	  } else {
	    $this->menu_items[$menu_item_id]->save_item("update");
	  }
	}
	$title = $_SESSION['translate']->it("Update Successful");
	$content = $_SESSION['translate']->it("All the menu items were successfully updated to the database.");
      } else {
	$title = $_SESSION['translate']->it("Nothing Updated");
	$content = $_SESSION['translate']->it("No menu items were selected for update.");
      }
      //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
      $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
    }

    $this->list_menu_items();
  } // END FUNC update_menu_items

  /**
   * Form to add a module's user links to the menu
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */
  function moduleDefault(){
    $activeMods = $GLOBALS['core']->listModules(TRUE);
    $form = new EZform;

    foreach ($activeMods as $mod_title){
      $itemCount = 0;
      unset($link);
      $moduleInfo = $GLOBALS['core']->getModuleInfo($mod_title);
      $menumanFile = PHPWS_SOURCE_DIR . "mod/" . $moduleInfo['mod_directory'] . "/conf/menuman.php";
      if (is_file($menumanFile)){
	include ($menumanFile);
	if (isset($link) && is_array($link)){
	  foreach ($link as $label=>$nullit){
	    $itemCount++;
	    $items[$mod_title . "_" . $itemCount] = $moduleInfo['mod_pname'] . ": " . $label;
	  }
	}
      }
    }

    $form->add("addItem", "select", $items);
    $form->add("submit", "submit", $_SESSION["translate"]->it("Add Menu Item"));
    $form->add("module", "hidden", "menuman");
    $form->add("MMN_menuman_op", "hidden", "addModuleDefault");
    $form->add("MMN_menu_id", "hidden", $this->menu_id);

    $template = $form->getTemplate();
    $template['TITLE_LABEL'] = $_SESSION["translate"]->it("Menu Title");
    $template['ADD_LABEL'] = $_SESSION["translate"]->it("Add Under");

    $template['CURRENT_ITEMS'] = "<tr><td><b><i>" . $_SESSION['translate']->it("Add to top level") . "</i></b></td>";
    $template['CURRENT_ITEMS'] .= "<td align=\"center\">" . PHPWS_Form::formRadio("MMN_item_pid", 0, 0) . "</td></tr>";

    $count = 1;
    if(is_array($this->menu_items)) {
      foreach($this->menu_items as $id=>$menu_item) {
	if($id == 0)
	  continue;
	if ($count%2)
	  $highlight = " class=\"bg_light\"";
	else
	  $highlight = NULL;

	$template['CURRENT_ITEMS'] .= $menu_item->menu_item("view", $highlight);
	$count++;
      }
    }
    
    $template['INSTRUCTION'] = $_SESSION["translate"]->it("Choose a module's user functionality to add to your menu") . ".";
    $title = $_SESSION["translate"]->it("Add Module User Links") . "&#160;&#160;<a href=\"index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_editMenu=1&amp;MMN_menu_id=" . $this->menu_id . "\">" . $_SESSION['translate']->it("Back To Main") . "</a>";
    $content = PHPWS_Template::processTemplate($template, "menuman", "moduleItem.tpl");
    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
  }

  /**
   * add_menu_item
   *
   * Provides the form to add a menu item to the database,
   * also called to insert the item into the database
   */
  function add_menu_item() {
    $error = FALSE;
    $content = NULL;

    $PM_exists = $GLOBALS['core']->moduleExists("pagemaster");

    if(isset($_POST["MMN_menu_id"]) && ($_POST["MMN_menu_id"] != 0)) {
      if (isset($_POST['MMN_item_pid0']) && empty($_POST['MMN_item_url0']) && (isset($_POST['MMN_pagemaster_id']) && $_POST['MMN_pagemaster_id'] == 0)){
	$error = TRUE;
	$content = $_SESSION['translate']->it("Please enter an URL");
	if ($PM_exists)
	  $content .= " " . $_SESSION["translate"]->it("or choose a PageMaster page");
	
	$content .= ".<br />";
      }

      if (isset($_POST['MMN_item_title0']) && empty($_POST['MMN_item_title0'])){
	$error = TRUE;
	$content .= $_SESSION["translate"]->it("You must give your menu link a title") . "<br />";
      }

      if(!$error && isset($_POST["MMN_item_pid"]) && ($_POST["MMN_item_pid"] > -1)) {
	$title = $_SESSION['translate']->it("Add Menu Item");

	if($_SESSION['OBJ_user']->allow_access("menuman", "add_item")) {
	  $this->menu_items[0] = new PHPWS_MenuItem();
	  $this->menu_items[0]->save_item("insert");
	  $this->build_items();
	  
	  if(!isset($_SESSION['OBJ_menuman']->call_back)) {
	    $_SESSION['OBJ_menuman']->main_menu();
	    $title = $_SESSION['translate']->it("Menu Item Added");
	    $content = $_SESSION['translate']->it("The menu item you added was successfully updated to the database and added to [var1].", "<b><i>" . $this->menu_title . "</i></b>");
	    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
	  } else {
	    header("Location: ".$_SESSION['OBJ_menuman']->call_back);
	    $_SESSION['OBJ_menuman']->call_back = null;
	    exit;
	  }
	}
	
	$this->list_menu_items();
      } else {
	if($PM_exists) {
	  $_SESSION['OBJ_menuman']->refreshPageOptions();
	}
	
	$title = $_SESSION['translate']->it("Add Menu Item");
	
	$elements[0] = ""; 

	if($_SESSION["OBJ_user"]->js_on && $PM_exists) {
	  $_SESSION['OBJ_layout']->addJavaScript($this->jsIntExt());
	}

	if(!isset($_POST['MMN_module']) && $PM_exists) {
	  $title .= "&#160;&#160;<a href=\"index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_editMenu=1&amp;MMN_menu_id=" . $this->menu_id . "\">" . $_SESSION['translate']->it("Back To Main") . "</a>";
	  $elements[0] .= $_SESSION['translate']->it("When selecting a pagemaster page leave the menu url field blank.") . "<br /><br />";
	}
	
	$hiddens = array("module"=>"menuman",
			 "MMN_menuman_op"=>"add_menu_item",
			 "MMN_menu_id"=>"$this->menu_id",
			 );

	if (isset($_POST['MMN_module']))
	  $hiddens["MMN_module"] = $_POST['MMN_module'];

	if (isset($_POST['MMN_item_active']))
	  $hiddens["MMN_item_active"] = $_POST['MMN_item_active'];

	if (isset($_POST['MMN_op_string']))
	  $hiddens['MMN_op_string'] = str_replace("&", "&amp;", $_POST['MMN_op_string']);

	
	$elements[0] .= PHPWS_Form::formHidden($hiddens);
	
	$elements[0] .= "<table border=\"1\" width=\"100%\" cellpadding=\"5\" cellspacing=\"1\">";
	$elements[0] .= "<tr class=\"bg_dark\">";
	$elements[0] .= "<td width=\"30%\"><b>" . $_SESSION['translate']->it("Title") . "</b></td>";

	if(isset($_POST['MMN_module']) && $_POST['MMN_module'] == 'pagemaster' && $PM_exists) {
	  $elements[0] .= "<td width=\"20%\"><b>" . $_SESSION['translate']->it("Pagemaster Page") . "</b></td>";
	} else {
	  $elements[0] .= "<td width=\"50%\"><b>" . $_SESSION['translate']->it("URL") . "</b></td>";

	  if(!isset($_POST['MMN_module']) && $GLOBALS['core']->moduleExists("pagemaster")) {
	    $elements[0] .= "<td width=\"30%\"><b>" . $_SESSION['translate']->it("Internal Web Page") . "</b></td>";
	  }

	  $elements[0] .= "<td width=\"20%\"><b>" . $_SESSION['translate']->it("Display") . "</b></td>";
	}
	
       	$elements[0] .= "</tr>";

	$this->menu_items[0] = new PHPWS_MenuItem();
	$elements[0] .= $this->menu_items[0]->menu_item("add", NULL);
	
	$elements[0] .= "</table><br />";
	
	$elements[0] .= "<table border=\"0\" width=\"50%\" cellpadding=\"5\" cellspacing=\"1\">";
	$elements[0] .= "<tr class=\"bg_dark\"><td width=\"80%\">" . $_SESSION['translate']->it("Menu Title") . "</td>";
	$elements[0] .= "<td width=\"20%\" align=\"center\">" . $_SESSION['translate']->it("Add Under") . "</td></tr>";
	$elements[0] .= "<tr><td><b><i>" . $_SESSION['translate']->it("Add to top level") . "</i></b></td>";

	if (!isset($_POST['MMN_item_pid']))
	  $matchItem = 0;
	else
	  $matchItem = NULL;

	$elements[0] .= "<td align=\"center\">" . PHPWS_Form::formRadio("MMN_item_pid", 0, $matchItem) . "</td></tr>";
	
	$highlight = " class=\"bg_light\"";
	if(is_array($this->menu_items)) {
	  reset($this->menu_items);
	  foreach($this->menu_items as $id => $menu_item) {
	    if($id != 0) {
	      $elements[0] .= $menu_item->menu_item("view", $highlight);
	      PHPWS_WizardBag::toggle($highlight, " class=\"bg_light\"");
	    }
	  }
	}
	
	$elements[0] .= "</table><br />";
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Add Menu Item"));
	if ($error)
	  $title = $_SESSION["translate"]->it("Error");

	$content .= PHPWS_Form::makeForm("MMN_menu_items", "index.php", $elements, "post", NULL, NULL);
	//$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
	$GLOBALS['CNT_menuman_options']['content'] = "<h3>$title</h3>$content";
      }
    } else {
      header("Location: ".$_SESSION['OBJ_menuman']->call_back);
      $_SESSION['OBJ_menuman']->call_back = null;
      exit;
    }
  } // END FUNC add_menu_item

  /**
   * delete_item_child 
   *
   * Deletes a child item from the database
   *
   * @param integer $parent_id id of the parent item
   */
  function delete_item_child($parent_id) {
    $sql = "SELECT menu_item_id FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_pid='$parent_id' AND menu_item_id!=menu_item_pid AND menu_id='$this->menu_id' ORDER BY menu_item_order";
    $menu_item_result = $GLOBALS['core']->query($sql);
    if($menu_item_result->numrows() > 0) {
      while($menu_item = $menu_item_result->fetchrow(DB_FETCHMODE_ASSOC)) {
	$this->delete_item_child($menu_item['menu_item_id']);
	$GLOBALS['core']->sqlDelete("mod_menuman_items", "menu_item_id", $menu_item['menu_item_id']);
	unset($this->menu_items[$menu_item['menu_item_id']]);
      }    
    }
  } // END FUNC delete_item_child


  /**
   * delete_items 
   *
   * Deletes menu items recursively from the menu
   * also provides confimation
   */
  function delete_menu_items() {
    $in_confirm = 0;

    if((isset($_POST['MMN_item_id']) && is_array($_POST['MMN_item_id'])) || is_array($this->delete_items)) {
      if(isset($_POST['MMN_item_yes'])) {
	foreach($this->delete_items as $id) {
	  $sql = "SELECT menu_item_id FROM {$GLOBALS['core']->tbl_prefix}mod_menuman_items WHERE menu_item_id='$id' AND menu_id='$this->menu_id' ORDER BY menu_item_order";
	  $menu_item_result = $GLOBALS['core']->query($sql);
	  if($menu_item_result->numrows() > 0) {
	    while($menu_item = $menu_item_result->fetchrow(DB_FETCHMODE_ASSOC)) {
	      $this->delete_item_child($menu_item['menu_item_id']);
	      $GLOBALS['core']->sqlDelete("mod_menuman_items", "menu_item_id", $menu_item['menu_item_id']);
	      unset($this->menu_items[$menu_item['menu_item_id']]);
	    }
	  }
	}
	$title = $_SESSION['translate']->it("Update Successful");
	$content = $_SESSION['translate']->it("All selected menu items and sub-items were successfully deleted from the database.");
      } else if(isset($_POST['MMN_item_no'])) {
	$title = $_SESSION['translate']->it("Nothing Deleted");
	$content = $_SESSION['translate']->it("No menu items were deleted from the database.");
      } else {
	$in_confirm = 1;
	$title = $_SESSION['translate']->it("Delete Menu Items Confirmation");
	$content = $_SESSION['translate']->it("Are you sure you want to delete these menu items and their sub-items?") . "<br /><br />";

	foreach($_POST['MMN_item_id'] as $id) {
	  $content .= $this->menu_items[$id]->menu_item_title . "<br />";
	}
	
	$this->delete_items = $_POST['MMN_item_id'];
	$elements[0] = "<br />";
	$hiddens = array("module"=>"menuman",
			 "MMN_menuman_op"=>"menu_items",
			 "MMN_menu_id"=>"$this->menu_id",
			 "delete"=>"1"
			 );

	$elements[0] .= PHPWS_Form::formHidden($hiddens);
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Yes"), "MMN_item_yes");
	$elements[0] .= "&#160;&#160;";
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("No"), "MMN_item_no");
	$content .= PHPWS_Form::makeForm("MMN_item_delete", "index.php", $elements, "post", NULL, NULL);
      }
    } else {
      $title = $_SESSION['translate']->it("Nothing Deleted");
      $content = $_SESSION['translate']->it("No menu items were selected to be deleted.");
    }

    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";

    if(!$in_confirm) {
      $this->list_menu_items();
    }
  } // END FUNC delete_items

  function getListMenu_Title() {
    return $this->menu_title;
  }

  function getListUpdated() {
    return date(PHPWS_DATE_FORMAT."&#160;".PHPWS_TIME_FORMAT, $this->updated);
  }

  function getListActions() {
    $actions = array();

    if($_SESSION['OBJ_user']->allow_access("menuman", "set_activity")) {
      if($this->menu_active) {
	$actions[] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_setActivity=1&amp;MMN_menu_id=$this->menu_id\">".$_SESSION['translate']->it("Hide")."</a>";
      } else {
	$actions[] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_setActivity=1&amp;MMN_menu_id=$this->menu_id\">".$_SESSION['translate']->it("Show")."</a>";
      }
    }
 
    if($_SESSION['OBJ_user']->allow_access("menuman", "edit_menu")) {
      $actions[] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_editMenu=1&amp;MMN_menu_id=$this->menu_id\">".$_SESSION['translate']->it("Edit")."</a>";
    }

    if($_SESSION['OBJ_user']->allow_access("menuman", "delete_menu")) {
      $actions[] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=menuAction&amp;MMN_deleteMenu=1&amp;MMN_menu_id=$this->menu_id\">".$_SESSION['translate']->it("Delete")."</a>";
    }

    return implode("&#160;|&#160;", $actions);
  }

  /**
   * javascript
   *
   * Adds javascript so that when a user selects a pagemaster page the internal selection in the display list
   * will automatically be selected.  Also, when external is selected in the display list then any pagemaster
   * selections will be cleared.
   * 
   */
  function jsIntExt() {
    $js = "
function internalPageChange(formDispSelect, formIPSelect) {
   formHandleDisplay = document.getElementsByName(formDispSelect)[0];
   formHandlePMU = document.getElementsByName(formIPSelect)[0];

   if(!formHandlePMU.options[0].selected)
      formHandleDisplay.options[0].selected=true;
}

function displayChange(formDispSelect, formIPSelect) {
   formHandleDisplay = document.getElementsByName(formDispSelect)[0];
   formHandlePMU = document.getElementsByName(formIPSelect)[0];

   if(formHandleDisplay.options[3].selected || formHandleDisplay.options[1].selected)
      formHandlePMU.options[0].selected=true;
}
";

    return $js;
  }
} // END CLASS PHPWS_Menu

?>