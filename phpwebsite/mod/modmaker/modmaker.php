<?php
if (isset($_REQUEST["mod_op"])){
  $CNT_modmaker["content"] = NULL;
  if($_SESSION["OBJ_user"]->deity){
    switch ($_REQUEST["mod_op"]){
    case "mod_admin":
      $OBJ_modmaker->admin_menu();
      break;

    case "set_module":
      if (isset($_POST['new_install'])){
	$OBJ_modmaker->create_module();
      } elseif (isset($_POST['activate'])){
	$OBJ_modmaker->activate_mod();
      } elseif (isset($_POST['drop_mod'])){
	$OBJ_modmaker->drop_mod($_REQUEST["module_title"]);
      } elseif (isset($_POST['edit_mod'])){
	$OBJ_modmaker->edit_module($_REQUEST["module_title"]);
      } elseif (isset($_POST['export_mod'])){
	if (!$OBJ_modmaker->export_mod($_POST["module_title"])){
	  $GLOBALS["CNT_modmaker"]["title"]   = $_SESSION["translate"]->it("Error");
	  $GLOBALS["CNT_modmaker"]["content"] = $OBJ_modmaker->linkBack() . "<br /><br />" . $_SESSION["translate"]->it("Unable to write to this module's conf directory") . ".";
	} else {
	  $GLOBALS["CNT_modmaker"]["title"]   = $_SESSION["translate"]->it("Success");
	  $GLOBALS["CNT_modmaker"]["content"] = $OBJ_modmaker->linkBack() . "<br /><br />" . $_SESSION["translate"]->it("Module information file written to conf directory") . ".";
	}
	
      } elseif (isset($_POST['install_script'])){
	$OBJ_modmaker->install_script();
      }
      break;

    case "update_module":
      $OBJ_modmaker->set_template_array("update");
      break;

    case "write_module_install":
      $OBJ_modmaker->set_template_array("create");
      break;

    case "update_activation":
      $OBJ_modmaker->update_actives($_REQUEST["mm_modules"]);
      $OBJ_modmaker->force_to_admin();
      break;

    case "remove_module":
      if (!$_POST["confirm_off"] && $_POST["confirm_on"]) 
	$core->sqlDelete("modules", "mod_title", $_REQUEST["mm_mod_title"]);
      $OBJ_modmaker->force_to_admin();
      break;
    }
  } else
    exit();
}
?>
