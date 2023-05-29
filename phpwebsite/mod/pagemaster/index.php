<?php

/**
 * This is the index file for the pagemaster module.
 *
 * @author  Adam Morton <adam@NOSPAM.appstate.edu>
 * @modified Matthew McNaney <matt@NOSPAM.appstate.edu>
 * @version $Id: index.php,v 1.33 2004/10/18 15:02:05 steven Exp $
 */

if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

if($GLOBALS["module"] == "pagemaster") {
  $GLOBALS["CNT_pagemaster"] = array("title"=>$_SESSION["translate"]->it("Web Pages"),
				     "content"=>NULL);
}

if($_SESSION["OBJ_user"]->allow_access("pagemaster") && isset($_REQUEST["MASTER_op"])){
  if ($_REQUEST["MASTER_op"] != "list_pages" && $_REQUEST["MASTER_op"] != "main_menu"){
    $core->killSession('PM_Pager');
    $core->killSession('PM_orderby');
  }

  if (is_array($_REQUEST['MASTER_op']))
    list($masterSwitch,) = each($_REQUEST['MASTER_op']);
  else
    $masterSwitch = $_REQUEST['MASTER_op'];

  if(!isset($_SESSION["SES_PM_master"])) {
    $_SESSION["SES_PM_master"] = new PHPWS_PageMaster;
  }

  switch($masterSwitch)
    {
    case "main_menu":
      $_SESSION["SES_PM_master"]->main_menu();
      $_SESSION["SES_PM_master"]->list_pages();
      break;

    case "new_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages"))
	{
	  $_SESSION["SES_PM_master"]->main_menu();
	  $_SESSION["SES_PM_page"] = new PHPWS_Page;
	  $_SESSION["SES_PM_page"]->get_page();
	}
      break;

    case "list_pages":
      $_SESSION["SES_PM_master"]->main_menu();
      $_SESSION["SES_PM_master"]->list_pages();
      break;

    case "edit_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	{
	  $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
	  $_SESSION["SES_PM_page"]->edit_page();
	}
      break;

    case "finish":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages"))
	{
	  $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
	  $_SESSION["SES_PM_page"]->edit_page();
	}
      break;

    case "delete_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "delete_pages"))
	{
	  $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
	  $_SESSION["SES_PM_page"]->delete_page();
	}
      break;

    case "hide_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "activate_pages"))
	{
	  $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
	  $_SESSION["SES_PM_page"]->toggle_active();
	  $_SESSION["SES_PM_master"]->main_menu();
	  $_SESSION["SES_PM_master"]->list_pages();
	}
      break;

    case "show_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "activate_pages"))
	{
	  $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
	  $_SESSION["SES_PM_page"]->toggle_active();
	  $_SESSION["SES_PM_master"]->main_menu();
	  $_SESSION["SES_PM_master"]->list_pages();
	}
      break;

    case "set_main":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "set_mainpage"))
	{
	  $_SESSION["SES_PM_master"]->main_menu();
	  $_SESSION["SES_PM_master"]->set_main_page();
	}
      break;
    }


}

if($_SESSION["OBJ_user"]->allow_access("pagemaster") && isset($_REQUEST["PAGE_op"])){
  if (is_array($_REQUEST["PAGE_op"]))
    list($option, $blank) = each($_REQUEST["PAGE_op"]);
  else
    $option = $_REQUEST["PAGE_op"];
  switch($option)
    {
    case "get_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	{
	  $_SESSION["SES_PM_master"]->main_menu();
	  $_SESSION["SES_PM_page"]->get_page();
	}
      break;

    case "set_page":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	{
	  if (isset($_REQUEST['PAGE_title']) && !empty($_REQUEST['PAGE_title'])){
	    $_SESSION["SES_PM_page"]->set_page();
	    $_SESSION["SES_PM_page"]->edit_page();
	  } else {
	    $error = new PHPWS_Error("pagemaster", "set_page", $_SESSION["translate"]->it("You must enter a Page Title"));
	    $error->message("CNT_pagemaster");
	    $_SESSION["SES_PM_master"]->main_menu();
	    $_SESSION["SES_PM_page"]->get_page();
	  }
	}
      break;


    case "remove":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	$_SESSION["SES_PM_page"]->remove_section((int)$_REQUEST["SECT_id"]);
      break;

    case "page_save":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	{
	  $_SESSION["SES_PM_page"]->save_page();
	  $_SESSION["SES_PM_master"]->main_menu();
	  $_SESSION["SES_PM_master"]->list_pages();
	}
      break;
      
    case "up_section":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	$_SESSION["SES_PM_page"]->move_section((int)$_REQUEST["SECT_id"],-1);
      break;
      
    case "down_section":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	$_SESSION["SES_PM_page"]->move_section((int)$_REQUEST["SECT_id"],1);
      break;
    }
}

if($_SESSION["OBJ_user"]->allow_access("pagemaster") && isset($_REQUEST["SECT_op"])){
  if (is_array($_REQUEST['SECT_op']))
    list($sectionSwitch,) = each($_REQUEST['SECT_op']);
  else
    $sectionSwitch = $_REQUEST['SECT_op'];

  switch($sectionSwitch)
    {
    case "save_section":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	{
	  $_SESSION["SES_PM_section"]->save_section();
	  if($_SESSION["SES_PM_error"]) $_SESSION["SES_PM_page"]->edit_page($_SESSION["SES_PM_section"]->id);
	  else $_SESSION["SES_PM_page"]->edit_page();
	}
      break;

    case "edit_section":
      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages") || $_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages"))
	$_SESSION["SES_PM_page"]->edit_page((int)$_REQUEST["SECT_id"]);
      break;
    }
}

if(isset($_REQUEST["PAGE_user_op"]))
     switch($_REQUEST["PAGE_user_op"])
{
 case "view_page":
   if (isset($_REQUEST["PAGE_id"]))
     $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
   else
     $_SESSION["SES_PM_page"] = new PHPWS_Page();
   $_SESSION["SES_PM_page"]->view_page();
   break;

 case "view_printable":
   $_SESSION["SES_PM_page"] = new PHPWS_Page((int)$_REQUEST["PAGE_id"]);
   $_SESSION["SES_PM_page"]->view_printable();
   break;
}

if($GLOBALS['module'] != "pagemaster") {
  $_SESSION['SES_PM_master'] = NULL;
  $_SESSION['SES_PM_page'] = NULL;
  $_SESSION['SES_PM_section'] = NULL;
}

/*
if($GLOBALS["module"] == "home") {
  $_SESSION["SES_PM_master"] = new PHPWS_PageMaster;
  $_SESSION["SES_PM_master"]->show_mainpage();
}
*/

?>