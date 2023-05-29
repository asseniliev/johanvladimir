<?php

/**
 * Class:  PHPWS_MenuActions
 *
 * Action class for Menu Manager Module.
 *
 * @version $Id: MenuActions.php,v 1.14 2003/11/06 22:28:27 steven Exp $
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

      /* ATO: reset $menucatid and get all category id's of the current menu */
/*      $menucatid=array_fill(0,1,0); */
      $menucatid = $_SESSION["OBJ_fatcat"]->getModulesCategories('menuman', $id);
      if(!is_array($menucatid))
            $menucatid=array_fill(0,1,0);
      /*ATO: fill array with zero just to avoid php warnings*/
      $allCatIDs=array_fill(0,1,0);
      switch($_REQUEST['module'])
      {
         case 'pagemaster':
         /* ATO: first get the category id's of the current page */;
         $my_currentpage_id=(int)$_REQUEST['PAGE_id'];
         /* ATO: then get all category id's of the current page */
         $pagecatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('pagemaster', $my_currentpage_id);
         $allCatIDs = array_merge($allCatIDs, $pagecatids);
         break;

         case 'linkman':
         $linkcatids=$_REQUEST['category'];
         $allCatIDs = array_merge($allCatIDs,$linkcatids);
         break;

         case 'announce':
         $my_currentannounce_id=(int)$_REQUEST['ANN_id'];
         $announcecatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('announce', $my_currentannounce_id);
         $allCatIDs = array_merge($allCatIDs,$announcecatids);
         break;

         case 'faq':
         $my_currentfaq_id=(int)$_REQUEST['FAQ_id'];
         $faqcatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('faq', $my_currentfaq_id);
         $allCatIDs = array_merge($allCatIDs,$faqcatids);
         break;

         case 'phatform':
         $my_currentform_id=(int)$_REQUEST['PHAT_FORM_ID'];
         $formcatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('phatform', $my_currentform_id);
         $allCatIDs = array_merge($allCatIDs,$formcatids);
         break;

         case 'photoalbum':
         $my_currentalbum_id=(int)$_REQUEST['PHPWS_Album_id'];
         $albumcatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('photoalbum', $my_currentalbum_id);
         $allCatIDs = array_merge($allCatIDs,$albumcatids);
         break;

         case 'article':
         $my_currentarticle_id=$_REQUEST['view'];
         $articlecatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('article', $my_currentarticle_id);
         $allCatIDs = array_merge($allCatIDs,$articlecatids);
         break;

         case 'documents':
         $my_currentdocument_id=(int)$_REQUEST['JAS_Document_id'];
         $documentcatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('documents', $my_currentdocument_id);
         $documentcatids = array_merge($documentcatids,$_REQUEST['category']);
         $allCatIDs = array_merge($allCatIDs,$documentcatids);
         break;
      }

      if(!$_REQUEST['module'])
      {
         $homecatids = $_SESSION["OBJ_fatcat"]->getModulesCategories('pagemaster', 1);
         $allCatIDs = array_merge($allCatIDs, $homecatids);
      }

      /* if a category is displayed, remove the first zero so that uncategorized menus are not shown */
      if (array_sum($allCatIDs)>0)
           $dummy=array_shift($allCatIDs);

      if (!sizeof($menu->allow_view)==0)
               $menu->allow_view=array_fill(0,1,0);

      if(
       $menu->is_active()
       &&
       ($id != 0)
       &&
       (
          (
             (isset($_REQUEST['module']) && in_array($_REQUEST['module'], $menu->allow_view))
/* ATO: check if one or more displayed categories match with a menu's category */
                &&
             (array_intersect($allCatIDs, $menucatid))
/* ATO: end */
          )
             ||
             (!isset($_REQUEST['module']) && (array_intersect($allCatIDs, $menucatid)))
        )
        )

       {
          $expand = 0;

          if((isset($_REQUEST['MMN_menuman_op']) && in_array($_REQUEST['MMN_menuman_op'], $allowed_ops)) && ($menu->menu_id == $_REQUEST['MMN_menu_id']) && ($menu->horizontal == 'FALSE')) {
            $expand = 1;
          }

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
          $menu->build_display_menu($expand);
          $menu->menu_disp(TRUE);
        }
      }
    }
  }
}

?>