<?php

/**
 * Class:  PHPWS_MenuActions
 *
 * Action class for Menu Manager Module.
 *
 * @version $Id: MenuActions.php,v 1.17 2004/05/21 14:24:22 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Menu Manager
 */
class PHPWS_MenuActions {

  function adminMenu() {
    $_SESSION['OBJ_menuman']->main_menu();
    if(isset($_REQUEST['listMenus'])) {
      $_SESSION['OBJ_menuman']->list_menus();
    } else if(isset($_REQUEST['createMenu']) && $_SESSION['OBJ_user']->allow_access("menuman", "create_menu")) {
      $_SESSION['OBJ_menuman']->menus[0]->menu("create");
    } else if(isset($_REQUEST['imageManage'])) {
      $_SESSION['OBJ_menuman']->image_manager();
    } else {
      $_SESSION['OBJ_menuman']->list_menus();
    }
  }

  function insertMenu() {
    if($_SESSION['OBJ_user']->allow_access("menuman", "create_menu")) {
      $_SESSION['OBJ_menuman']->main_menu();
      $_SESSION['OBJ_menuman']->menus[0]->save_menu("insert");
      $_SESSION['OBJ_menuman'] = new PHPWS_Menuman;
      $_SESSION['OBJ_menuman']->list_menus();
    }
  }

  function updateMenu() {
    if($_SESSION['OBJ_user']->allow_access("menuman", "edit_menu")) {
      $_SESSION['OBJ_menuman']->main_menu();
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->save_menu("update");
      $_SESSION['OBJ_menuman']->list_menus();
    }
  }

  function menuAction() {
    $_SESSION['OBJ_menuman']->main_menu();
    if(isset($_REQUEST['MMN_editMenu']) && $_SESSION['OBJ_user']->allow_access("menuman", "edit_menu")) {
      $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->list_menu_items();
    } else if(isset($_REQUEST['MMN_deleteMenu']) && $_SESSION['OBJ_user']->allow_access("menuman", "delete_menu")) {
      $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->delete_menu();
    } else if(isset($_REQUEST['MMN_setActivity']) && $_SESSION['OBJ_user']->allow_access("menuman", "set_activity")) {
      $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->set_activity();
      $_SESSION['OBJ_menuman']->list_menus();
    }
  }

  function addMenuItem() {
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->add_menu_item();
  }

  function addModuleDefault() {
    $item = explode("_", $_POST['addItem']);
    $count = 0;

    if(sizeof($item) > 2) {
      $linkNumber = $item[sizeof($item) - 1];
      unset($item[sizeof($item) - 1]);
      $mod_title = implode("_", $item);
    } else {
      $mod_title = $item[0];
      $linkNumber = $item[1];
    }

    $moduleInfo = $GLOBALS['core']->getModuleInfo($mod_title);
    $menumanFile = PHPWS_SOURCE_DIR . "mod/" . $moduleInfo['mod_directory'] . "/conf/menuman.php";
    if (is_file($menumanFile)){
      include ($menumanFile);
      if (isset($link) && is_array($link)){
	foreach ($link as $title=>$url){
	  $count++;
	  if ($count == $linkNumber){
	    $finalTitle = $title;
	    $finalUrl = $url;
	  } else
	    continue;
	}
      }
    }

    $_POST['MMN_item_title0'] = $finalTitle;
    $_POST['MMN_item_url0'] = $finalUrl;

    $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->add_menu_item();
  }

  function moveItemUp() {
    $_SESSION['OBJ_menuman']->main_menu();
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->menu_items[$_REQUEST['MMN_item_id']]->move_item_up();
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->build_items();
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->list_menu_items();
  }

  function moveItemDown() {
    $_SESSION['OBJ_menuman']->main_menu(); 
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->menu_items[$_REQUEST['MMN_item_id']]->move_item_down();
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->build_items();
    $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->list_menu_items();
  }

  function menuItemAction() {
    $_SESSION['OBJ_menuman']->main_menu();
    if(isset($_POST['add'])) {
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->add_menu_item();
    } else if(isset($_POST['moduleDefault'])) {
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->moduleDefault();
    } else if(isset($_POST['settings'])) {
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->menu("edit");
    } else if(isset($_POST['update'])) {
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->update_menu_items(0);
    } else if(isset($_POST['delete'])) {
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->delete_menu_items();
    } else if(isset($_POST['activity'])) {
      $_SESSION['OBJ_menuman']->menus[$_POST['MMN_menu_id']]->update_menu_items(1);
    } else if(isset($_POST['set_uber_edit'])) {
      $_SESSION['OBJ_menuman']->set_uber_edit();
      $_SESSION['OBJ_menuman']->menus[$_REQUEST['MMN_menu_id']]->list_menu_items();
    }
  }

  function deleteImage() {
    $_SESSION['OBJ_menuman']->delete_image();
  }

  function uploadImage() {
    $_SESSION['OBJ_menuman']->main_menu();
    $_SESSION['OBJ_menuman']->upload_image();
    $_SESSION['OBJ_menuman']->image_manager();
  }

  function displayMenus() {
    $allowed_ops = array("menuAction",
			 "list_items",
			 "add_menu_item",
			 "move_item_up",
			 "move_item_down",
			 "menu_items"
			 );
	  

    if(is_array($_SESSION['OBJ_menuman']->menus) && count($_SESSION['OBJ_menuman']->menus) > 1) {
      foreach($_SESSION['OBJ_menuman']->menus as $id => $menu){
	if($menu->is_active() && $menu->can_view() && ($id != 0) && ((isset($_REQUEST['module']) && in_array($_REQUEST['module'], $menu->allow_view))
				  || (!isset($_REQUEST['module']) && in_array("home", $menu->allow_view)))) {
	  $expand = 0;
	  
	  if((isset($_REQUEST['MMN_menuman_op']) && in_array($_REQUEST['MMN_menuman_op'], $allowed_ops)) 
	     && ($menu->menu_id == $_REQUEST['MMN_menu_id']) && ($menu->horizontal == 'FALSE')) {
	    $expand = 1;
	  }
	
	  if(PHPWS_MENUMAN_STAY_EXPANDED) {
	    $expand = 1;
	  }

	  $menu->build_items();
	  $menu->build_display_menu($expand);
	  $menu->menu_disp();
	}
      }
    }
  }

  function siteMap() {
    if(is_array($_SESSION['OBJ_menuman']->menus) && sizeof($_SESSION['OBJ_menuman']->menus) > 1) {
      foreach($_SESSION['OBJ_menuman']->menus as $id => $menu){
	if($menu->is_active() && ($id != 0)) {
	  $expand = 1;
	  $menu->build_items();
	  $menu->build_display_menu($expand);
	  $menu->menu_disp(TRUE);
	}
      }
    }
  }
}

?>