<?php

require_once (PHPWS_SOURCE_DIR . "core/Form.php");

require_once (PHPWS_SOURCE_DIR . "core/Text.php");

require_once (PHPWS_SOURCE_DIR . "core/File.php");

require_once (PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

class CLS_modmaker {
  var $mod_info;

  function force_to_admin(){
    header("location:index.php?module=modmaker&mod_op=mod_admin");
    exit();
  }

  function admin_menu(){
    if (!isset($GLOBALS["CNT_modmaker"]["content"]))
      $GLOBALS["CNT_modmaker"]["content"] = NULL;
    
    $GLOBALS["CNT_modmaker"]["title"] = $_SESSION["translate"]->it("Mod Maker Administration");
    $sql = $GLOBALS["core"]->sqlSelect("modules", NULL, NULL, "mod_title");

    foreach ($sql as $info)
      $modules[] = $info["mod_title"];

    $GLOBALS["CNT_modmaker"]["content"] .= 
       "\n<form action=\"index.php\" method=\"post\">\n"
       . PHPWS_Form::formHidden(array("module"=>"modmaker", "mod_op"=>"set_module")) . "\n"
       . PHPWS_Form::formSubmit($_SESSION["translate"]->it("Create Module Entry"), "new_install")
       . CLS_help::show_link("modmaker", "create_module_entry")."<br /><br />"
       . PHPWS_Form::formSelect("module_title", $modules, NULL, TRUE)
       . PHPWS_Form::formSubmit($_SESSION["translate"]->it("Edit Module"), "edit_mod")
       . " " . PHPWS_Form::formSubmit($_SESSION["translate"]->it("Remove Module"), "drop_mod"). " "
       . PHPWS_Form::formSubmit($_SESSION["translate"]->it("Export Mod Info"), "export_mod")
       . CLS_help::show_link("modmaker", "edit_module")."<br /><br />"
       . PHPWS_Form::formSubmit($_SESSION["translate"]->it("Activate/Deactivate"), "activate")
       . CLS_help::show_link("modmaker", "act_deact")."<br />"
       . "\n</form>\n";
  }

  function activate_mod(){
    $GLOBALS["CNT_modmaker"]["title"] = "Activate Modules";
    $GLOBALS["CNT_modmaker"]["content"] = "Turning off an active module could be BAD. For example, turning off the user module means you are locked out the web site. Make sure you know what you are doing.";
    $GLOBALS["CNT_modmaker"]["content"] .= "
<form action=\"index.php\" method=\"post\">
<input type=\"hidden\" name=\"module\" value=\"modmaker\" />
<input type=\"hidden\" name=\"mod_op\" value=\"update_activation\" />";
    $row = $GLOBALS["core"]->sqlSelect ("modules", NULL, NULL, "mod_title");
    foreach ($row as $sql_result){
      extract ($sql_result);
      $GLOBALS["CNT_modmaker"]["content"] .= "\n".PHPWS_Form::formRadio("mm_modules[$mod_title]", 'off', $active)."Off | ".PHPWS_Form::formRadio("mm_modules[$mod_title]", 'on', $active)."On &gt;&gt; $mod_pname<br />";
    }
    $GLOBALS["CNT_modmaker"]["content"] .= "<br /><br />".PHPWS_Form::formSubmit("Activate/Deactivate Modules")."
</form>";
  }

  function drop_mod ($module_title){
    $GLOBALS["CNT_modmaker"]["title"] = "Remove $module_title Module from Database";
    $GLOBALS["CNT_modmaker"]["content"] .= "<div class=\"errortext\" align=\"center\"><b>"
       . $_SESSION["translate"]->it("ARE YOU ABSOLUTELY SURE YOU WANT TO DELETE THIS MODULE")."?</b></div><br />"
       . $_SESSION["translate"]->it("Removing this module from the system could have serious consequences") . ". "
       . $_SESSION["translate"]->it("It might cause you to have to reinstall phpWebSite") . "!<br />";

    $GLOBALS["CNT_modmaker"]["content"] .= 
       "\n<form action=\"index.php\" method=\"post\">"
       . PHPWS_Form::formHidden(array("module"=>"modmaker", "mod_op"=>"remove_module", "mm_mod_title"=>$module_title)) . "\n"
       . PHPWS_Form::formCheckBox("confirm_off", 1, 1) . " " . $_SESSION["translate"]->it("Uncheck to confirm") . "<br />\n"
       . PHPWS_Form::formCheckBox("confirm_on", 1, 0) . " " . $_SESSION["translate"]->it("Check to confirm") . "<br /><br />\n"
       . PHPWS_Form::formSubmit($_SESSION["translate"]->it("Remove Module"))."
</form>";

  }


  function create_module(){
    $GLOBALS["CNT_modmaker"]["title"] = $_SESSION["translate"]->it("Create Module Installation");
    $GLOBALS["CNT_modmaker"]["content"] .= $this->linkBack() . "<br /><br />
<form action=\"index.php\" method=\"post\">
<input type=\"hidden\" name=\"module\" value=\"modmaker\" />
<input type=\"hidden\" name=\"mod_op\" value=\"write_module_install\" />
";

    $this->module_form($this->mod_info, "create");

    $GLOBALS["CNT_modmaker"]["content"] .= "
<br />
<div align=\"center\">
".PHPWS_Form::formSubmit($_SESSION['translate']->it('Install Module'))."
</div>
</form>";
  }

  function export_mod($module_title){
    $exportDir = PHPWS_SOURCE_DIR . "mod/" . $GLOBALS["core"]->getModuleDir($module_title) . "/conf/";

    if (!is_dir($exportDir) || !is_writable($exportDir))
      return FALSE;

    $sql = "select * from ".PHPWS_TBL_PREFIX."modules where mod_title='$module_title'";
    $row = $GLOBALS["core"]->quickFetch($sql);

    if ($row){
      extract($row);
      $mod_file .= "<?php
\$mod_title = \"$mod_title\";
\$mod_pname = \"$mod_pname\";
\$mod_directory = \"$mod_directory\";
\$mod_filename = \"$mod_filename\";
\$priority = $priority;\n";

      if ($allow_view){
	$allow_view = unserialize($allow_view);
	if (is_array($allow_view)){
	  foreach($allow_view as $key=>$value){
	    if ($loop)
	      $allow_array .=", ";
	    $allow_array .= "\"$key\"=>$value";
	    $loop = 1;
	  }
	  $mod_file .= "\$allow_view = array ($allow_array);\n";
	} else 
	  $mod_file .= "\$allow_view = \"all\";\n";
      }
      unset($loop);

      $user_mod ? $mod_file .= "\$user_mod = 1;\n" : $mod_file .= "\$user_mod = 0;\n";
      $admin_mod ? $mod_file .= "\$admin_mod = 1;\n" : $mod_file .= "\$admin_mod = 0;\n";
      $deity_mod ? $mod_file .= "\$deity_mod = 1;\n" : $mod_file .= "\$deity_mod = 0;\n";
 
      if ($mod_class_files){
	$moduleFiles = unserialize($mod_class_files);
	$mod_file .= "\$mod_class_files = array(";
	foreach ($moduleFiles as $filename){
	  if ($loop)
	    $mod_file .= ",";

	  $mod_file .= "\"$filename\"";
	  $loop = 1;
	}
	$mod_file .= ");\n";
	$loop = 0;
      }

      if ($mod_sessions){
	$moduleSessions = unserialize($mod_sessions);
	$mod_file .= "\$mod_sessions = array(";
	foreach ($moduleSessions as $sessions){
	  if ($loop)
	    $mod_file .= ",";

	  $mod_file .= "\"$sessions\"";
	  $loop = 1;
	}
	$mod_file .= ");\n";
	$loop = 0;
      }

      if ($init_object){
	$moduleInit = unserialize($init_object);
	$mod_file .= "\$init_object = array(";
	foreach ($moduleInit as $objName=>$className){
	  if ($loop)
	    $mod_file .= ",";

	  $mod_file .= "\"$objName\"=>\"$className\"";
	  $loop = 1;
	}
	$mod_file .= ");\n";
      }
      $mod_file .= "\$active = \"on\";\n";

      if ($version)
	$mod_file .= "\$version = \"$version\";\n";

      if ($update_link)
	$mod_file .= "\$update_link = \"$update_link\";\n";

      if ($branch_allow == 0)
	$mod_file .= "\$branch_allow = 0;\n";
      else
	$mod_file .= "\$branch_allow = 1;\n";

      $mod_file .= "\$install_file = \"install.php\";\n";
      $mod_file .= "\$uninstall_file = \"uninstall.php\";\n";
      $mod_file .= "?>";
    } else
      return FALSE;

    return PHPWS_File::writeFile($exportDir."boost.php", $mod_file, TRUE);
  }

  function edit_module($module_title=NULL){
    $sql = "select * from ".PHPWS_TBL_PREFIX."modules where mod_title='$module_title'";
    $row = $GLOBALS["core"]->quickFetch($sql);

    if ($row["allow_view"] != 'all')
      $row["allow_view"] = unserialize($row["allow_view"]);
    
    if ($row["mod_class_files"])
      $row["mod_class_files"] = unserialize($row["mod_class_files"]);
    if ($row["mod_sessions"])
      $row["mod_sessions"] = unserialize($row["mod_sessions"]);
    if ($row["init_object"])
     $row["init_object"] = unserialize($row["init_object"]);

    $GLOBALS["CNT_modmaker"]["title"] = $_SESSION["translate"]->it("Edit Module");
    $GLOBALS["CNT_modmaker"]["content"] .= $this->linkBack() . "<br /><br />
<form action=\"index.php\" method=\"post\">
<input type=\"hidden\" name=\"module\" value=\"modmaker\" />
<input type=\"hidden\" name=\"mod_op\" value=\"update_module\" />
".PHPWS_Form::formHidden("install_mod_title", $row["mod_title"]);

    $this->module_form($row);

    $GLOBALS["CNT_modmaker"]["content"] .= "
<br />
<div align=\"center\">
".PHPWS_Form::formSubmit($_SESSION["translate"]->it("Update Module"))."
</div>
</form>";
  }


  function write_module(){
    $sql_array = $this->mod_info;
    $temp_title = $sql_array["mod_title"];
    
    if ($sql_array["allow_view"] != 'all')
      $sql_array["allow_view"][$temp_title] = 1; 
      
    PHPWS_Array::dropNulls($sql_array);
    $sql_array["active"] = "off";

    $sql_array["mod_pname"] = $sql_array["mod_pname"];
    $GLOBALS["core"]->sqlInsert($sql_array, "modules");
    $this->force_to_admin();
    
  }

  function update_module(){
    $temp_title = $this->mod_info["mod_title"];
    $sql_array = $this->mod_info;
    if ($sql_array["allow_view"] != 'all')
      $sql_array["allow_view"][$temp_title] = 1; 

    $sql_array["mod_pname"] = $sql_array["mod_pname"];
    $GLOBALS["core"]->sqlUpdate($sql_array, "modules", "mod_title", $temp_title);
    $this->force_to_admin();
  }

  function module_form($form_array=NULL, $mode=NULL){
    $mod_list = NULL;
    $mod_class_files = NULL;
    if ($form_array){
      if (!is_array($form_array["allow_view"]))
	$allow_all = "TRUE";
      else
	$allow_all = "FALSE";
      
      if ($form_array["mod_class_files"])
	$mod_class_files = implode(", ", $form_array["mod_class_files"]);
      
      if ($form_array["mod_sessions"])
	$mod_sessions = implode(", ", $form_array["mod_sessions"]);

      if ($form_array["init_object"]){
	$init_obj_name = key($form_array["init_object"]);
	$init_class_name =  $form_array["init_object"][$init_obj_name];
      }
      if ($form_array["priority"]){
	$priority = $form_array["priority"];
      }
    }

    if (!isset($priority))
      $priority = 50;

    if (!isset($allow_all))
      $allow_all = "TRUE";


    if ($mode == 'create')
      $title_info = PHPWS_Form::formTextField("install_mod_title",@$form_array["mod_title"],12,20);
    else
      $title_info = $form_array["mod_title"];

    $table[] = array(CLS_help::show_link("modmaker", "mod_title")."<b>".$_SESSION['translate']->it("Mod Title")."</b>:", $title_info);
    $table[] = array(CLS_help::show_link("modmaker", "proper_name")."<b>".$_SESSION['translate']->it("Proper Name").":</b>", PHPWS_Form::formTextField("mod_pname",@$form_array["mod_pname"],20,30));
    $table[] = array(CLS_help::show_link("modmaker", "mod_directory")."<b>".$_SESSION['translate']->it("Mod Directory")."</b>:", PHPWS_Form::formTextField("mod_directory", @$form_array["mod_directory"], 20, 255)); 
    $table[] = array(CLS_help::show_link("modmaker", "mod_filename")."<b>".$_SESSION['translate']->it("Mod Filename").":</b>", PHPWS_Form::formTextField("mod_filename", @$form_array["mod_filename"], 20, 30));

    $mod_list .= PHPWS_Form::formCheckBox("run_modules[home]", 1, $form_array["allow_view"]["home"])." Home<br />";
    $sql_result = $GLOBALS["core"]->sqlSelect("modules", NULL, NULL, "mod_pname");
    foreach ($sql_result as $row){
      $mod_list .= PHPWS_Form::formCheckBox("run_modules[".$row["mod_title"]."]", 1, $form_array["allow_view"][$row["mod_title"]])." ".$row["mod_pname"]."<br />\n";
    }
    $table[] = array(CLS_help::show_link("modmaker", "allow_view")."<b>".$_SESSION['translate']->it("Allow View").":</b>" ,
		     PHPWS_Form::formRadio("allow_all", "TRUE", $allow_all)." ".$_SESSION['translate']->it("All")."&#160;&#160;&#160;&#160;".PHPWS_Form::formRadio("allow_all", "FALSE", $allow_all)." ".$_SESSION['translate']->it("Only")
		     ."<br />".$mod_list);
    $table[] = array(CLS_help::show_link("modmaker", "priority")."<b>".$_SESSION['translate']->it("Priority").":</b>", PHPWS_Form::formTextField("priority", $priority));

    $table[] = array(CLS_help::show_link("modmaker", "administrator_module")."<b>".$_SESSION['translate']->it("Module Permission")."</b>" , PHPWS_Form::formCheckBox("user_mod", 1, $form_array["user_mod"])."<b>".$_SESSION['translate']->it("User")
		     ."</b><br />".PHPWS_Form::formCheckBox("admin_mod", 1, $form_array["admin_mod"])."<b>".$_SESSION['translate']->it("Administrator")."</b>");
    $table[] = array(CLS_help::show_link("modmaker", "class_files"). "<b>".$_SESSION['translate']->it("Class Files").":</b>", PHPWS_Form::formTextArea("mod_class_files", $mod_class_files));

    if (!isset($mod_sessions))
      $mod_sessions = NULL;

    $table[] = array(CLS_help::show_link("modmaker", "session_variables")."<b>".$_SESSION['translate']->it("Session Variables").":</b>", PHPWS_Form::formTextArea("mod_sessions", $mod_sessions));
    
    if (!isset($init_obj_name))
      $init_obj_name = NULL;

    if (!isset($init_class_name))
      $init_class_name = NULL;

    $table[] = array(CLS_help::show_link("modmaker", "initialize_class_name")."<b>".$_SESSION['translate']->it("Initialize Class Name").":</b>",
		     PHPWS_Form::formTextField("init_obj_name", $init_obj_name)."<br /> ".PHPWS_Form::formTextField("init_class_name", $init_class_name));
    $table[] = array(CLS_help::show_link("modmaker", "deity_only")."<b>".$_SESSION['translate']->it("Deity Only")."</b>", PHPWS_Form::formCheckBox("mmk_deity_mod",1,$form_array["deity_mod"])." ".$_SESSION['translate']->it("Yes, only deities may access"));
    $GLOBALS["CNT_modmaker"]["content"] .= PHPWS_Text::ezTable($table, 5, 1, 0, "50%", NULL, 1, "top", "grid");
  }

  function update_actives($modules){
    foreach ($modules as $mod_title=>$active){
      $temp_array = array("active"=>$active);
      $GLOBALS["core"]->sqlUpdate($temp_array, "modules", "mod_title", $mod_title);
    }
  }


  function set_template_array($mode){

    $error_found = NULL;

    if (PHPWS_Text::isValidInput($_POST["install_mod_title"])){
      $this->mod_info["mod_title"] = $_POST["install_mod_title"];
      if ($mode == 'create' && $GLOBALS["core"]->sqlSelect("modules", "mod_title", $this->mod_info["mod_title"])){
	$GLOBALS["CNT_modmaker"]["content"] .= "<span class=\"errortext\">Your module title is already in use.</span><br />";
	$error_found = 1;
      }
    } else {
      $GLOBALS["CNT_modmaker"]["content"] .= "<span class=\"errortext\">Your module title should only contain alphanumeric characters.</span><br />";
      $error_found = 1;
    }

    $this->mod_info["mod_pname"] = $_POST["mod_pname"];
    if ($_POST["mod_directory"])
      $this->mod_info["mod_directory"] = $_POST["mod_directory"];
    else {
      $GLOBALS["CNT_modmaker"]["content"] .= "<span class=\"errortext\">You must enter a directory path.</span><br />";
      $error_found = 1;
    }

    if ($_POST["mod_filename"])
      $this->mod_info["mod_filename"] = $_POST["mod_filename"];
    else {
      $GLOBALS["CNT_modmaker"]["content"] .= "<span class=\"errortext\">You must enter a file name.</span><br />";
      $error_found = 1;
    }

  
    if ($_POST["allow_all"]=="TRUE"){
      $this->mod_info["allow_view"] = "all";
    } else {
      if ($_POST["run_modules"]){
	foreach ($_POST["run_modules"] as $allow_mod=>$value){
	  $allow_mod = PHPWS_Text::stripQuotes($allow_mod);
	  $this->mod_info["allow_view"][$allow_mod] = $value;
	}
      }
      if ($this->mod_title)
	$this->mod_info["allow_view"][$this->mod_title] = 1;
    }

    if (PHPWS_Text::isValidInput($_POST["priority"], 'number') && $_POST["priority"] < 100 && $_POST["priority"] > 0)
      $this->mod_info["priority"] = $_POST["priority"];
    else {
      $GLOBALS["CNT_modmaker"]["content"] .= "<span class=\"errortext\">Priority needs to be a number over 0 and under 100.</span><br />";
      $error_found = 1;
    }

    if (isset($_POST['user_mod']))
      $this->mod_info["user_mod"] = 1;
    else
      $this->mod_info["user_mod"] = 0;

    if (isset($_POST['admin_mod']))
      $this->mod_info["admin_mod"] = 1;
    else
      $this->mod_info["admin_mod"] = 0;

    if ($_POST["mod_class_files"] && !empty($_POST["mod_class_files"]))
      $class_temp = explode(",", str_replace(" ", "", $_POST["mod_class_files"]));
    else
      $class_temp = NULL;

    if ($_POST["mod_sessions"] && !empty($_POST["mod_sessions"]))
      $sess_temp = explode(",", str_replace(" ", "", $_POST["mod_sessions"]));
    else
      $sess_temp = NULL;

    if ($mode == 'create'){
      if (is_array($class_temp) && $key = array_search(NULL, $class_temp))
	unset($class_temp[$key]);
      
      if (is_array($sess_temp) && $key = array_search(NULL, $sess_temp))
	unset($sess_temp[$key]);
    }

    if ($_POST["init_class_name"] && $_POST["init_obj_name"])
      $this->mod_info["init_object"][$_POST["init_obj_name"]] = $_POST["init_class_name"];
    else
      $this->mod_info["init_object"] = NULL;

    $this->mod_info["mod_class_files"] = $class_temp;
    $this->mod_info["mod_sessions"] = $sess_temp;

    if (isset($_POST["mmk_deity_mod"]))
      $this->mod_info["deity_mod"] = 1;
    else
      $this->mod_info["deity_mod"] = 0;

    if ($error_found){
      if ($mode == 'create')
	$this->create_module();
      else
	$this->edit_module($this->mod_info["mod_title"]);
    } else {
      if ($mode == 'create')
	$this->write_module();
      else
	$this->update_module();
    }
    
  }


  function install_script($step=NULL){
    if (!$step){
      $sql = "select * from ".PHPWS_TBL_PREFIX."modules";
      $GLOBALS["CNT_modmaker"]["title"] = $_SESSION["translate"]->it("Create Installation Script");
      $GLOBALS["CNT_modmaker"]["content"] .= "Once you have finished writing your masterful module, you will need to create the installation script. ModMaker will help you by letting you create your first script (version 1.0!).";
      $GLOBALS["CNT_modmaker"]["content"] .= "<br /><hr />Is your module currently installed?<br />
<form action=\"index.php\" method=\"post\">
<input type=\"hidden\" name=\"module\" value=\"modmaker\" />
<input type=\"hidden\" name=\"mod_op\" value=\"create_script\" />
<select name=\"current_module\">"
	 . PHPWS_Form::formSqlSelect($sql, "mod_title") .
"</select>".PHPWS_Form::formSubmit("Yes, right here", "installed")." &nbsp;|&nbsp;".
PHPWS_Form::formSubmit("No, it isn't", "installed")."
</form>";
    }

  }

  function linkBack(){
    return PHPWS_Text::moduleLink($_SESSION["translate"]->it("Admin"), "modmaker", array("mod_op"=>"mod_admin"));
  }

}

?>
