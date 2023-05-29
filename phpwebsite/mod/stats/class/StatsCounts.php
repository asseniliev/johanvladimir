<?php 

require_once(PHPWS_SOURCE_DIR . "mod/stats/class/StatsCommon.php");
require_once(PHPWS_SOURCE_DIR . "core/List.php");

class PHPWS_Stats_Counts {
  var $_id = NULL;
  var $_tableName = NULL;
  var $_title = NULL;
  var $_link  = NULL;

  var $_visible;
  var $_allowView;
  var $_showDeity;
  var $_showUsers;
  var $_showPerms;
  var $_showAny;
  var $_list = NULL;
  var $_availCounters;

  function PHPWS_Stats_Counts() {
    $settings = $this->fetchSettings();
    $this->_visible = $settings[0]["counts_show_block"];
    
    if(!empty($settings[0]["counts_allow"]))
      $this->_allowView = unserialize($settings[0]["counts_allow"]);
    else
      $this->_allowView = NULL;

    $this->_showDeity = $settings[0]["counts_show_diety"];
    $this->_showUsers = $settings[0]["counts_show_users"];
    $this->_showPerms = $settings[0]["counts_show_perms"];
    $this->_showAny   = $settings[0]["counts_show_any"];    

    require_once(PHPWS_SOURCE_DIR . "mod/stats/conf/basicView.php");
    $this->_availCounters = $basicView;
  }

  function fetchSettings() {
    return $GLOBALS['core']->sqlSelect("mod_stats_settings");
  }

  function init($id) {
    $result = $GLOBALS["core"]->sqlSelect("mod_stats_counts", "id", $id);
    $this->_id        = $result[0]["id"];
    $this->_tableName = $result[0]["table_name"];
    $this->_title     = $result[0]["label"];
    $this->_link      = $result[0]["link"];
  }

  function reset() {
    $this->_id        = NULL;
    $this->_tableName = NULL;
    $this->_title     = NULL;
    $this->_link      = NULL;
  }

  function getBasicCounters() {
    return array("mod_announce"=>"Announcements", "mod_approval_jobs"=>"Approval", "mod_notes"=>"Notes");
  }

  function getBasicViewModules($basicOptions) {
    $mods = array();
    foreach($basicOptions as $key=>$options) {
      if($GLOBALS["core"]->sqlTableExists($options["table"]))	
        $mods[$options["table"]] = $options["title"];
    }
    return $mods;
  }

  function getBasicJSVars($basicOptions) {
    $content = "";
    
    foreach($basicOptions as $key=>$options) {
      $content .= "var ".$options["table"]."Title = \"".$options["title"] . "\";\n";
      $content .= "var ".$options["table"]."Link = \"".$options["link"] . "\";\n";
    }

    return $content;
  }

  function basic_counts_form() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "add_counter")) {
	return $_SESSION["translate"]->it("Permission denied for adding counter.");
    }

    $form = new EZform("STATS_counts_form");
    $form->add("name_field", "text", $this->_title);
    $form->setSize("name_field", 40);
    $form->add("module", "hidden", "stats");
    $form->add("stats[counters]", "hidden", "add_counter");   
    $form->add("type", "hidden", "basic");
    $form->add("cb_link", "checkbox");    

    if(isset($this->_link))    
      $form->setMatch("cb_link", TRUE);
    
    $form->add("tb_link", "text", $this->_link);
    $form->setSize("tb_link", 50);
    $modules = array("empty"=>$_SESSION["translate"]->it("Select"));
    $modules += $this->getBasicViewModules($this->_availCounters);
    $form->add("tableField", "select", $modules);
    $form->setExtra("tableField", "onchange=\"suggest(this.form, this.value);\"");
    $form->setMatch("tableField", $this->_tableName);
    $form->add("save_counter", "submit", $_SESSION["translate"]->it("Save"));

    $tags = array();
    $tags = $form->getTemplate();

    $tags["JS_VARS"] = $this->getBasicJSVars($this->_availCounters);
    $tags["BACK_LINK"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Back"), "stats", array("stats[counters]"=>"setup"));
    $tags["TABLE_LABEL"] = $_SESSION["translate"]->it("Module:  ");
    $tags["NAME_LABEL"] = $_SESSION["translate"]->it("Suggested Title:  ");
    $tags["LINK_LABEL"] = $_SESSION["translate"]->it("Make Title a Link?");
    $tags["TB_LINK_LABEL"] = $_SESSION["translate"]->it("Suggested Link: ");

    $content = PHPWS_Template::processTemplate($tags, "stats", "counter/basic_counts_form.tpl");
    $this->reset();
    return $content;
  }

  function adv_counts_form() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "add_counter")) {
	return $_SESSION["translate"]->it("Permission denied for adding counter.");
    }

    $allTables = $GLOBALS["core"]->listTables();

    $form = new EZform("STATS_counts_form");
    $form->add("name_field", "text", $this->_title);
    $form->setSize("name_field", 40);
    $form->add("module", "hidden", "stats");
    $form->add("stats[counters]", "hidden", "add_counter");   
    $form->add("type", "hidden", "advanced");
    if(isset($this->_id))
      $form->add("id", "hidden", $this->_id);

    $form->add("cb_link", "checkbox");

    if(isset($this->_link))
      $form->setMatch("cb_link", TRUE);

    $form->add("tb_link", "text", htmlentities($this->_link));
    $form->setSize("tb_link", 50);

    $form->add("tableField", "select", $allTables);
    $form->reindexValue("tableField");
    $form->setMatch("tableField", $this->_tableName);

    $form->add("save_counter", "submit", $_SESSION["translate"]->it("Save"));

    $tags = array();
    $tags = $form->getTemplate();

    $tags["BACK_LINK"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Back"), "stats", array("stats[counters]"=>"setup"));
    $tags["TABLE_LABEL"] = $_SESSION["translate"]->it("Table:  ");
    $tags["NAME_LABEL"] = $_SESSION["translate"]->it("Title:  ");
    $tags["LINK_LABEL"] = $_SESSION["translate"]->it("Make Title a Link?");
    $tags["TB_LINK_LABEL"] = $_SESSION["translate"]->it("Enter Link: ");

    $content = PHPWS_Template::processTemplate($tags, "stats", "counter/adv_counts_form.tpl");
    $this->reset();
    return $content;
  }

  function save_settings() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "counter_settings")) {
	return $_SESSION["translate"]->it("Permission denied for changing counter settings.");
    }

    $update["counts_show_block"] = @$_POST["hide_block"];
    $this->_visible = @$_POST["hide_block"];

    $this->_showDeity = $update["counts_show_diety"] = 0;
    $this->_showUsers = $update["counts_show_users"] = 0;
    $this->_showPerms = $update["counts_show_perms"] = 0;
    $this->_showAny   = $update["counts_show_any"]   = 0;    

    if($_POST["show"] == "deity") {
      $this->_showDeity = $update["counts_show_diety"]    = 1;
    } else if($_POST["show"] == "users") {
      $this->_showUsers = $update["counts_show_users"] = 1;
    } else if($_POST["show"] == "perms") {
      $this->_showPerms = $update["counts_show_perms"] = 1;
    } else if($_POST["show"] == "any") {
      $this->_showAny   = $update["counts_show_any"]   = 1;    
    }

    $update["counts_allow"] = @$_POST["allow_view"];
    $this->_allowView = @$_POST["allow_view"];
    
    if($GLOBALS["core"]->sqlUpdate($update, "mod_stats_settings")) {
      $msg = $_SESSION["translate"]->it("Successfully Updated Settings");
    } else {
      $msg = $_SESSION["translate"]->it("There was a problem updating stat settings.");
    }

    return PHPWS_Stats_Common::getMsg($msg) . $this->list_counts();
  }

  function settings() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "counter_settings")) {
	return $_SESSION["translate"]->it("Permission denied for changing counter settings.");
    }

    $form = new EZform("counter_setup");
    $form->add("hide_block", "checkbox");
    $form->setMatch("hide_block", $this->_visible);
    $form->add("allow_view","multiple",$this->get_modules_allowed());
    if(is_array($this->_allowView))
      $form->setMatch("allow_view",$this->_allowView,FALSE);
    $form->setSize("allow_view",10);
    $form->add("show", "radio");
    $form->setValue("show", array("deity", "users", "perms", "any"));

    if($this->_showDeity)
      $form->setMatch("show", "deity");
    else if($this->_showUsers)
      $form->setMatch("show", "users");
    else if($this->_showPerms)
      $form->setMatch("show", "perms");
    else if($this->_showAny)
      $form->setMatch("show", "any");

    $form->add("save_settings", "submit", $_SESSION["translate"]->it("Save Settings"));
    $form->add("module", "hidden", "stats");
    $form->add("stats[counters]", "hidden", "save_settings");
    
    $tags = $form->getTemplate();
    $tags["ALLOW_VIEW_LBL"] = $_SESSION["translate"]->it("Select where you would like the block to appear:");
    $tags["HIDE_BLOCK_LBL"] = $_SESSION["translate"]->it("Make Block Visible");
    $tags["BACK_LINK"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Back"), "stats", array("stats[counters]"=>"setup"));
    $tags["VISIBLE_TITLE"] = $_SESSION["translate"]->it("Select who should see the counter block:");
    $tags["DEITY"] = $_SESSION["translate"]->it("Deity users only");
    $tags["USERS"] = $_SESSION["translate"]->it("Logged in users only");
    $tags["PERMS"] = $_SESSION["translate"]->it("Must have permission enabled to view");
    $tags["ANY"] = $_SESSION["translate"]->it("Any User");
    return PHPWS_Template::processTemplate($tags, "stats", "counter/setup.tpl");
  }

  /*
   * @author  Wendal Cada
   * @modifed Darren Greene
  */
  function get_modules_allowed() {

    if(!isset($modulesAllowed)){
      $modulesAllowed = $GLOBALS['core']->listModules();
    }

    $options = array("home");
    $modulesAllowed = array_merge($options, $modulesAllowed);
    
    return $modulesAllowed;
  }//END function get_modules_allowed

  function saveCounter() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "add_counter")) {
	return $_SESSION["translate"]->it("Permission denied for adding counter.");
    }

    $error = NULL;

    if(!isset($_REQUEST["tableField"]) || empty($_REQUEST["tableField"]) ||
       $_REQUEST["tableField"] == "empty")
      $error = new PHPWS_Error("stats", "add_table", $_SESSION["translate"]->it("Missing table selection."));
    else
      $insert["table_name"] = $_REQUEST["tableField"];

    if(!isset($_REQUEST["name_field"]) || empty($_REQUEST["name_field"]))
      $error = new PHPWS_Error("stats", "add_table", $_SESSION["translate"]->it("Missing a name to call this counter."));
    else
      $insert["label"] = $_REQUEST["name_field"];

    if(isset($_REQUEST["cb_link"])) {
      if(isset($_REQUEST["tb_link"])) {
	$insert["link"] = $_REQUEST["tb_link"];
      } else {
	$insert["link"] = NULL;
      }
    } else {
      $insert["link"] = NULL;
    }

    if(!isset($error)) {
      $content = "";
      if(isset($_REQUEST["id"])) {
	$GLOBALS["core"]->sqlUpdate($insert, "mod_stats_counts", "id", $_REQUEST["id"]);
	$content = PHPWS_Stats_Common::getMsg($_SESSION["translate"]->it("Succesfully updated counter."));
      } else {
	if($id = $GLOBALS["core"]->sqlInsert($insert, "mod_stats_counts", FALSE, TRUE)) {
	  $this->init($id);
	  $content = PHPWS_Stats_Common::getMsg($_SESSION["translate"]->it("Succesfully added counter."));
	}
      }

      return $content . $this->list_counts();
    } else if(PHPWS_Error::isError($error)) {
      $error->message("CNT_stats", "Error");
      if(isset($_REQUEST["type"])) {
	if($_REQUEST["type"] == "basic") {
	  return $this->basic_counts_form();
	} else if($_REQUEST["type"] == "advanced") {
	  return $this->adv_counts_form();
	}
      }
    }    

  }

  function setVisibility() {
    if($this->_show_counts)
      $this->_show_counts = FALSE;
    else
      $this->_show_counts = TRUE;

    $GLOBALS["core"]->sqlUpdate(array("show_counts_block"=>1), "mod_stats_settings");
  }

 function deleteCounter() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "delete_counter")) {
	return $_SESSION["translate"]->it("Permission denied for deleting counter.");
    }

   if(isset($_POST['Stats_counter_yes'])) {
     if($GLOBALS["core"]->sqlDelete("mod_stats_counts", "id", $_REQUEST["counter_id"]))
       $content = $_SESSION["translate"]->it("Successfully Deleted Counter.");
     else
       $content = $_SESSION["translate"]->it("There was a problem deleting counter.");
     $content = PHPWS_Stats_Common::getMsg($content);
     $content .= $this->list_counts();
     
   } else if(isset($_POST['Stats_counter_no'])) {
     $content = PHPWS_Stats_Common::getMsg($_SESSION["translate"]->it("No counter was deleted"));
     $content .= $this->list_counts();
   } else {     
     $content = "<b>" . $_SESSION["translate"]->it("Are you sure you want to delete this counter?");
     $content .= "</b><br /><br />";
     $elements[0] = PHPWS_Form::formHidden(array("module"=>"stats", "stats[counters]"=>"delete","counter_id"=>$_REQUEST["counter_id"]));
     $elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Yes"), "Stats_counter_yes");
     $elements[0] .= "&nbsp;&nbsp;";
     $elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("No"), "Stats_counter_no");
     $content .= PHPWS_Form::makeForm("Stats_counter_delete", "index.php", $elements, "post", NULL, NULL);
   }

   return $content;
 }

  function list_counts() {
    if(!$_SESSION["OBJ_user"]->isUser()) {
	return $_SESSION["translate"]->it("You must be logged in to view counters.");
    }	

    $this->_list = NULL;
    if(!isset($this->_list)) {
      $this->_list = new PHPWS_List;
    }

    if($_SESSION["OBJ_user"]->allow_access("stats", "counter_settings")) {
      $mainTags["SETTINGS"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Settings"), "stats", array("stats[counters]"=>"settings"));       
    }

    if($_SESSION["OBJ_user"]->allow_access("stats", "add_counter")) {
      if(SHOW_ADV_ADD_COUNTER == TRUE)
	$mainTags["ADV_ADD"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Advanced Add"), "stats", array("stats[counters]"=>"adv_counter_form"));      
    
      $mainTags["BASIC_ADD"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("New Counter"), "stats", array("stats[counters]"=>"basic_counter_form"));   
    }

    $listTags["TITLE"] = $_SESSION["translate"]->it("Counters");
    $listTags["NAME_LABEL"] = $_SESSION["translate"]->it("Title");
    $listTags["TABLE_NAME_LABEL"] = $_SESSION["translate"]->it("Table Name");
    $actions = "";
    if($_SESSION["OBJ_user"]->allow_access("stats", "edit_counter") ||
       $_SESSION["OBJ_user"]->allow_access("stats", "delete_counter")) {
      $listTags["ACTIONS_LABEL"] = $_SESSION["translate"]->it("Actions");
      $actions = "actions";
    }

    $listTags["CURR_COUNT_LABEL"] = $_SESSION["translate"]->it("Count");

    $this->_list->setIdColumn("id");
    $this->_list->setTable("mod_stats_counts");
    $this->_list->setDbColumns(array("id", "label", "table_name"));
    $this->_list->setListColumns(array("label", "table_name", "currentCount", $actions));
    $this->_list->setName("list_counts");
    $this->_list->setTemplate("counter/list_counts");
    $this->_list->setClass("PHPWS_Stats_List_Counts");
    $this->_list->setOp("stats[counters]=setup");
    
    $this->_list->setModule("stats");
    
    $this->_list->setPaging(array("limit"=>10, "section"=>TRUE, 
				  "limits"=>array(5,10,20,50),
				  "back"=>"&#60;&#60;", 
				  "forward"=>"&#62;&#62;", "anchor"=>FALSE));
    
    $this->_list->setExtraRowTags(array("COLSPAN"=>"3"));
    
    $this->_list->setExtraListTags($listTags);
    
    $mainTags["LISTING"] = $this->_list->getList();
    return PHPWS_Template::processTemplate($mainTags, "stats", "counter/counters.tpl");
  }

 function show_counts() {
    $settings = PHPWS_Stats_Counts::fetchSettings();
    if(!empty($settings[0]["counts_allow"]))
      $allowView = unserialize($settings[0]["counts_allow"]);
    else
      $allowView = NULL;


   $confirm = FALSE;

   if(!$settings[0]["counts_show_block"])
     return;

  if($settings[0]["counts_show_diety"] && !$_SESSION["OBJ_user"]->isDeity())
    return;

  if($settings[0]["counts_show_users"] && !$_SESSION["OBJ_user"]->isUser())
    return;

  if($settings[0]["counts_show_perms"] && !$_SESSION["OBJ_user"]->allow_access("stats", "show_counter_block"))
     return;

   if(is_array($allowView)){
     $modules_allowed = PHPWS_Stats_Counts::get_modules_allowed();

     foreach($allowView as $num){
	if(!isset($modules_allowed[$num])) {
	  PHPWS_Stats_Counts::updateAllowedMods($allowView, $num); 
	  continue;
        }

       if(((isset($_REQUEST['module']) && ($_REQUEST['module'] == $modules_allowed[$num]))
	   || (!isset($_REQUEST['module']) && ("home" == $modules_allowed[$num]))))
	 $confirm = TRUE;
     }
   }
  
   if($confirm) {
     $mainTags["MONITORS"] = "";

     if($monitors = $GLOBALS["core"]->sqlSelect("mod_stats_counts", NULL, NULL, 'label')) {
       
       foreach($monitors as $monitor) {
	 if($GLOBALS["core"]->sqlTableExists($monitor["table_name"])) {
	   $countRow = $GLOBALS["core"]->query("SELECT COUNT(*) FROM " . $monitor["table_name"]);
	   $row = $countRow->fetchRow();
	   if(isset($monitor["link"])) {
	     $link = $monitor["link"];
	   
	     if(stristr($link,'&amp;') === FALSE)
	       $link = htmlentities($link);
	       
	     $subTags["COUNTER_NAME"] = "<a href=\"".$link."\">".$monitor["label"]."</a>";
	   } else {
	     $subTags["COUNTER_NAME"] = $monitor["label"];
	   }
	   $subTags["COUNT"] = $row["COUNT(*)"];
	   $mainTags["MONITORS"] .= PHPWS_Template::processTemplate($subTags, "stats", "counter/single_count.tpl");
	 }
       }
       
       return PHPWS_Template::processTemplate($mainTags, "stats", "counter/counts.tpl");
     }
   }
 }


  function updateAllowedMods($orginal, $removeIndex) {
    unset($orginal[array_search($removeIndex, $orginal)]);
    $update['counts_allow'] = serialize($orginal);
    $GLOBALS["core"]->sqlUpdate($update, 'mod_stats_settings');
  }	

  function action($command, $getTitle=FALSE) {
    switch($command) {
    case "setup":
      if($getTitle)
	return $_SESSION["translate"]->it("Counter Block");
      else
	return $this->list_counts();
      break;

    case "adv_counter_form":
      if($getTitle)
       return 
	$_SESSION["translate"]->it("Add a New Counter (Advanced View)");
      else {
	$this->reset();
	return $this->adv_counts_form();
      }
      break;

    case "basic_counter_form":
      if($getTitle)
	return 
	  $_SESSION["translate"]->it("Add a New Counter");
      else {
	$this->reset();
	return $this->basic_counts_form();
      }
      break;

    case "add_counter":      
      if($getTitle) 
	return $_SESSION["translate"]->it("Counter Block");
      else
	return $this->saveCounter();     
      break;

    case "delete":
      if($getTitle)
	return $_SESSION["translate"]->it("Delete Confirmation");
      else
	return $this->deleteCounter();
      break;

    case "edit":
      if($getTitle)
	return $_SESSION["translate"]->it("Edit Counter");
      else {
	$this->init($_REQUEST["counter_id"]);
	$tableListing = $this->getBasicViewModules($this->_availCounters);
	if(isset($tableListing[$this->_tableName]))
	  return $this->basic_counts_form();
	else
	  return $this->adv_counts_form();
      }
      break;
    case "settings":
      if($getTitle)
	return $_SESSION["translate"]->it("Counter Block Settings");
      else
	return $this->settings();
      break;
    case "save_settings":
      if($getTitle)
	return $_SESSION["translate"]->it("Counter Block Settings");
      else
	return $this->save_settings();
      break;
    }
  }

 }

class PHPWS_Stats_List_Counts {
  var $id;
  var $label = NULL;
  var $table_name;
  var $count;

  function PHPWS_Stats_List_Counts($NLU_id = NULL) {
    foreach($NLU_id as $key => $value) {
      $this->$key = $value;
    }     
  }

  function _getCount() {
    if($GLOBALS["core"]->sqlTableExists($this->table_name)) {
      $countRow = $GLOBALS["core"]->query("SELECT COUNT(*) FROM " . $this->table_name);
      $row = $countRow->fetchRow();
      return $row["COUNT(*)"];
    } else {
      return $_SESSION["translate"]->it("Table Doesn't Exist");
    }
  }

  function getListlabel() {
    return $this->label;
  }

  function getListtable_name() {
    return $this->table_name;
  }

  function getListactions() {
    if($_SESSION["OBJ_user"]->allow_access("stats", "edit_counter") &&
	$GLOBALS["core"]->sqlTableExists($this->table_name))
      $content[] = "<a href=\"./index.php?module=stats&amp;stats[counters]=edit&amp;counter_id=".$this->id."\">".$_SESSION["translate"]->it("Edit")."</a>";

    if($_SESSION["OBJ_user"]->allow_access("stats", "delete_counter"))
      $content[] = "<a href=\"./index.php?module=stats&amp;stats[counters]=delete&amp;counter_id=".$this->id."\">".$_SESSION["translate"]->it("Delete")."</a>";

    return implode("&nbsp;&nbsp;|&nbsp;&nbsp;", $content);
  }

  function getListcurrentCount() {
    return $this->_getCount();
  }
}

?>