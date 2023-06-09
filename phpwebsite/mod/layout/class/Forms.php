<?php

require_once (PHPWS_SOURCE_DIR . "core/Text.php");

require_once (PHPWS_SOURCE_DIR . "core/Form.php");

require_once (PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

class PHPWS_Layout_Forms extends PHPWS_Layout_Box {

  function admin(){
    $this->_panel = TRUE;
    $this->settings();
  }

  function panel(){
    $form = new EZform;
    list($settings) = $GLOBALS["core"]->sqlSelect("mod_layout_config");
    $theme_names = $this->get_themes();

    $form->add("theme_select", "select", $theme_names);
    $form->add("theme_submit", "submit", $_SESSION["translate"]->it("Set Default Theme"));
    $form->add("module", "hidden", "layout");
    if (isset($_REQUEST["lay_adm_op"]) && $_REQUEST["lay_adm_op"] == "admin")
          $form->add("adminMenu", "hidden", "1");

    $form->add("lay_adm_op", "hidden", "panelCommand");
    $form->add("panelOff", "submit", $_SESSION["translate"]->it("Close Panel"));
    $form->setMatch("theme_select", $settings["default_theme"]);

    $template = $form->getTemplate();

    $template['MOVE_LABEL'] = $_SESSION["translate"]->it("Move Boxes");
    $template['CHANGE_LABEL'] = $_SESSION["translate"]->it("Change Box Style");

    if (!$this->_move)
      $template['BOX_MOVE'] = $_SESSION["translate"]->it("Off") . " | " . PHPWS_Text::moduleLink($_SESSION["translate"]->it("On"), "layout", array("box_move"=>1, "lay_adm_op"=>"panelCommand"));
    else
      $template['BOX_MOVE'] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Off"), "layout", array("box_move"=>0, "lay_adm_op"=>"panelCommand")) . " | " . $_SESSION["translate"]->it("On");

    if (!$this->_change)
      $template['BOX_CHANGE'] = $_SESSION["translate"]->it("Off") . " | " . PHPWS_Text::moduleLink($_SESSION["translate"]->it("On"), "layout", array("box_change"=>1, "lay_adm_op"=>"panelCommand"));
    else
      $template['BOX_CHANGE'] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Off"), "layout", array("box_change"=>0, "lay_adm_op"=>"panelCommand"))  . " | " . $_SESSION["translate"]->it("On");

    $template['SETTINGS'] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Settings"), "layout", array("settings"=>1, "lay_adm_op"=>"panelCommand"));
    $content = NULL;
    $content = PHPWS_Template::processTemplate($template, "layout", "panel.tpl");
  
    $GLOBALS["Layout_Panel"]["content"] = (isset($GLOBALS["Layout_Panel"]["content"])) ? $GLOBALS["Layout_Panel"]["content"] . $content : $content;
  }
  
  function changeBox($content_var){
    $content = NULL;
    $content =
       "<form action=\"index.php\" method=\"post\">"
      . PHPWS_Form::formHidden(array("module"=>"layout", "lay_adm_op"=>"updateBox", "change_content_var"=>$content_var))
      . PHPWS_Form::formSelect("change_box_name", $this->getBoxstyles(), $this->getBoxByContent($content_var), 1)
      . PHPWS_Form::formSubmit("Set") .
       "\n</form>";
       
    return $content;
  }

  function changePop($content_var){
    $content =
       "<form action=\"index.php\" method=\"post\">"
       . PHPWS_Form::formHidden(array("module"=>"layout", "lay_adm_op"=>"updatePop", "change_content_var"=>$content_var)) .
       "\n" . PHPWS_Form::formSelect("change_pop_name", $this->getBoxstyles(), $this->getPopByContent($content_var), 1) .
       "\n" . PHPWS_Form::formSubmit("Set") .
       "\n</form>";
       
    return $content;
  }


  function panelCommand(){
    $backHome = preg_replace("/.*(index\.php.*|)$/Ui", "\\1", $_SERVER['HTTP_REFERER']);

    if (isset($_GET["box_move"])){
      $this->_toggleMove();
      header("location:./" . $backHome);
      exit();
    }

    if (isset($_GET["box_change"])){
      $this->_toggleChange();
      header("location:./" . $backHome);
      exit();
    }

    if (isset($_POST["theme_submit"])){
      $this->_changeDefault($_POST["theme_select"]);
      header("location:./" . $backHome);
      exit();
    }

    if (isset($_POST["panelOff"])){
      $this->_panel = FALSE;
      $this->_move  = FALSE;
      $this->_change= FALSE;
      if (isset($_POST["adminMenu"])){
	header("location:index.php?module=controlpanel");
	exit();
      }

      header("location:./" . $backHome);
      exit();
    }

    if (isset($_GET["settings"]))
      $this->settings();

  }


  function settings(){
    $form = new EZform;

    $meta["keywords"] = $_SESSION["translate"]->it("Keywords");    
    $meta["description"] = $_SESSION["translate"]->it("Description");
    $meta["robots"] = $_SESSION["translate"]->it("Robots");
    $meta["author"]  = $_SESSION["translate"]->it("Author");
    $meta["owner"]  = $_SESSION["translate"]->it("Owner");
    $meta["content"] = $_SESSION["translate"]->it("Content Type");
    /*

    $meta["expires"] = "Expires";    $meta["refresh"] = $_SESSION["translate"]->it("Refresh");
    $meta["PICS"] = "PICS Rating";
    $meta["script Type"] = "Script Type";
    */
    $form->add("module", "hidden", "layout");
    $form->add("lay_adm_op", "hidden", "settingsCommand");
    $form->add("metaList", "select", $meta);
    $form->add("editMeta", "submit", $_SESSION["translate"]->it("Edit Metatags"));
    $form->add("pageTitle", "text", $this->page_title);
    $form->add("titleButton", "submit", $_SESSION["translate"]->it("Change Page Title"));
    //$form->add("reinit", "submit", $_SESSION["translate"]->it("Re-Initialize Default Theme"));
    $form->add("update_user", "submit", $_SESSION["translate"]->it("User can change theme"));
    $form->add("purgeBoxes", "submit", $_SESSION["translate"]->it("Refresh Boxes"));
    $form->add("fixTable", "submit", $_SESSION["translate"]->it("Fix duplicate boxes"));
    $form->add("userAllow", "radio", array(1, 0));
    $form->setMatch("userAllow", $this->userAllow);

    $template = $form->getTemplate();
    $template['YES'] = $_SESSION["translate"]->it("Yes");
    $template['NO'] = $_SESSION["translate"]->it("No");

    $GLOBALS["CNT_layout"]["title"] = $_SESSION["translate"]->it("Layout Settings") . CLS_help::show_link("layout", "settings");
    $content = PHPWS_Template::processTemplate($template, "layout", "settings.tpl");
    $GLOBALS["CNT_layout"]["content"] = (isset($GLOBALS["CNT_layout"]["content"])) ? $GLOBALS["CNT_layout"]["content"] . $content : $content;

  }
  
  function settingsCommand(){
    if (isset($_POST["editMeta"]))
      $this->_editMeta();

    if (isset($_POST["titleButton"])){
      $this->_changeDefaultTitle($_POST["pageTitle"]);
      $GLOBALS["CNT_layout"]["content"] .= $_SESSION["translate"]->it("Title Changed");
      $this->settings();
    }

    /*
    if (isset($_POST["reinit"])){
      $this->_dropTheme($this->current_theme);
      $this->initializeLayout();
      $this->loadBoxInfo();
      $this->settings();
    }
    */

    if (isset($_POST["purgeBoxes"])){
      $GLOBALS['CNT_layout']['content'] = $this->purgeBoxes() . "<hr />";
      $this->settings();
    }

    if (isset($_POST["fixTable"])) {
      $GLOBALS['CNT_layout']['content'] = $this->fixTable() . "<hr />";
      $this->settings();
    }

    if (isset($_POST['update_user'])){
      if ($GLOBALS['core']->sqlUpdate(array('userAllow'=>(int)$_POST['userAllow']), "mod_layout_config")){
	$this->userAllow = (int)$_POST['userAllow'];
	$GLOBALS["CNT_layout"]["content"] = "<b>" . $_SESSION["translate"]->it("User option updated") . ".</b><hr />";
      }
      $this->settings();
    }
  }

  function purgeBoxes(){
    $themes = $this->get_themes();
    $allBoxes = $GLOBALS['core']->sqlSelect("mod_layout_box");
    $check = array();
    $missing = array();

    foreach ($allBoxes as $box){
      if (in_array($box['theme'], $check) || in_array($box['theme'], $missing))
	continue;

      if (in_array($box['theme'], $themes)){
	unset($themes[$box['theme']]);
	$check[] = $box['theme'];
      } else
	$missing[] = $box['theme'];
    }

    if (count($missing)){
      $content = "<b>" . $_SESSION["translate"]->it("Removed all boxes from missing theme(s)") . ":</b><br />";
      foreach ($missing as $killtheme){
	$GLOBALS['core']->sqlDelete("mod_layout_box", "theme", $killtheme);
	$content .= $killtheme . "<br />";
      }
    } else
      $content = "<b>" . $_SESSION["translate"]->it("All theme boxes are active") . ".</b>";

    return $content;    
  }


  function get_all_boxcounts() {
        /* returns assoc. array in the format
           theme => number of boxes */

        $sql =  "SELECT theme, count(theme) FROM {$GLOBALS['core']->tbl_prefix}".
                "mod_layout_box GROUP BY theme ORDER BY theme";

        $box_counts = $GLOBALS["core"]->getAssoc($sql);

        return($box_counts);
  }

  function get_all_boxes() {
        /* returns assoc. array in the format
           id => [ theme, mod_title, content_var ] */

        $sql =  "SELECT id, theme, mod_title, content_var FROM {$GLOBALS['core']->tbl_prefix}".
                "mod_layout_box ORDER BY id";

        $all_boxes = $GLOBALS["core"]->getAllAssoc($sql);

        return($all_boxes);
  }

  function get_duplicates($arr) {

        $whitelist = array();
        $blacklist = array();

        $min_id=0;

        foreach($arr as $k => $v) {
                $theme=$v["theme"];
                $mod=$v["mod_title"];
                $content=$v["content_var"];
                $id=$v["id"];

                $entry="$theme|$mod|$content";
                $black="$entry|$id";

                if(isset($whitelist[$entry]))
                        $blacklist[$id]=$black;
                else
                        $whitelist[$entry]=$id;
        }

        return($blacklist);
  }

  function orify($arr) {

        $str="";

        foreach($arr as $id => $entry) {
                if($str != "")
                        $str .= " or ";

                $str .= "id='$id'";
        }

        return($str);
  }

  function rm_duplicates($arr) {

        if(count($arr)<=0)
                return("nothing to do!");

        $sql =  "DELETE FROM {$GLOBALS['core']->tbl_prefix}mod_layout_box WHERE ".
                $this->orify($arr);

        $result = $GLOBALS["core"]->query($sql);

        if($result == FALSE)
                return("sorry, didn't work out!</p>");
        else
                return("removed all the duplicates.");
  }

  function fixTable() {
        $themes         = $this->get_themes();
        $all_boxes      = $this->get_all_boxes();
        $dups           = $this->get_duplicates($all_boxes);

        $dis_themes=implode(", ", $themes);
        if($dis_themes=="")
                $dis_themes="none";

        $dis_dups=implode(", ", $dups);
        if($dis_dups=="")
                $dis_dups="none";

        $content="<p><em>currently installed themes:</em> ".
                 "$dis_themes</p>\n";

        $content.="<p><em>duplicate boxes:</em> ".
                  "$dis_dups</p>\n";

        if(count($dups) <= 0)
                $content.="<p>nothing to do!</p>";
        else {
                $content.="<p>".$this->rm_duplicates($dups)."</p>";
        }

        return($content);
  }

  function _editMeta(){
    $form = new EZform;
    $form->add("module", "hidden", "layout");
    $form->add("lay_adm_op", "hidden", "editMeta");

    switch ($_POST["metaList"]){
    case "contentType":
      $form = $this->_contentTypeForm($form);
      $label = $_SESSION["translate"]->it("Content Type");
      break;
      
    case "keywords":
      $form = $this->_keywordsForm($form);
      $label = $_SESSION["translate"]->it("Keywords") . CLS_help::show_link("layout", "keyword");
      break;

    case "description":
      $form = $this->_descriptionForm($form);
    $label = $_SESSION["translate"]->it("Description") . CLS_help::show_link("layout", "description");
    break;

    case "robots":
      $form = $this->_robotsForm($form);
    $label = $_SESSION["translate"]->it("Robots") . CLS_help::show_link("layout", "robots");
    break;

    case "owner":
      $form = $this->_ownerForm($form);
    $label = $_SESSION["translate"]->it("Owner's Email");
    break;

    case "author":
      $form = $this->_authorForm($form);
    $label = $_SESSION["translate"]->it("Author Name");
    break;

    case "content":
      $form = $this->_contentForm($form);
    $label = $_SESSION["translate"]->it("Content Type");
    break;
    }
    
    $template = $form->getTemplate();
    $template["LABEL"] = $label;
    $GLOBALS["CNT_layout"]["title"] = $_SESSION["translate"]->it("Edit Meta Tags");
    $GLOBALS["CNT_layout"]["content"] = PHPWS_Template::processTemplate($template, "layout", "generic.tpl");

  }


  function _keywordsForm($form){
    $form->add("metaType", "hidden", "keywords");
    $form->add("element", "textarea", $this->meta_keywords);
    return $form;
  }

  function _descriptionForm($form){
    $form->add("metaType", "hidden", "description");
    $form->add("element", "textarea", $this->meta_description);
    return $form;
  }

  function _authorForm($form){
    $form->add("metaType", "hidden", "author");
    $form->add("element", "text", $this->meta_author);
    return $form;
  }

  function _ownerForm($form){
    $form->add("metaType", "hidden", "owner");
    $form->add("element", "text", $this->meta_owner);
    return $form;
  }


  function _robotsForm($form){
    $options = array("11"=>"all",
		     "00"=>"none",
		     "10"=>"index, nofollow",
		     "01"=>"noindex, follow");
    $form->add("metaType", "hidden", "robots");
    $form->add("element", "select", $options);
    $form->setMatch("element", $this->meta_robots);
    return $form;
  }

  function _contentForm($form){
    $charSets = array("GB2312"=>"GB2312",
		      "US-ASCII"=>"US-ASCII",
		      "Windows-1251"=>"Windows-1251",                      
		      "CP 1255"=>"CP 1255",                      
		      "ISO-8859-1"=>"ISO-8859-1",
		      "ISO-8859-2"=>"ISO-8859-2",
		      "ISO-8859-3"=>"ISO-8859-3",
		      "ISO-8859-4"=>"ISO-8859-4",
		      "ISO-8859-5"=>"ISO-8859-5",
		      "ISO-8859-6"=>"ISO-8859-6",
		      "ISO-8859-7"=>"ISO-8859-7",
		      "ISO-8859-8"=>"ISO-8859-8",
		      "ISO-8859-8-i"=>"ISO-8859-8-i",
		      "ISO-8859-9"=>"ISO-8859-9",
		      "ISO-8859-15"=>"ISO-8859-15",
		      "ISO-2022-JP"=>"ISO-2022-JP",
		      "ISO-2022-JP-2"=>"ISO-2022-JP-2",
		      "ISO-2022-KR"=>"ISO-2022-KR",
		      "SHIFT_JIS"=>"SHIFT_JIS",
		      "EUC-KR"=>"EUC-KR",
		      "BIG5"=>"BIG5",
		      "KOI8-R"=>"KOI8-R",
		      "KSC_5601"=>"KSC_5601",
		      "HZ-GB-2312"=>"HZ-GB-2312",
		      "JIS_X0208"=>"JIS_X0208",
		      "UTF-8"=>"UTF-8"
		      );
    $form->add("metaType", "hidden", "content");
    $form->add("element", "select", $charSets);
    $form->setMatch("element", $this->meta_content);
    return $form;
  }


  function editMeta(){
    switch ($_POST["metaType"]){
    case "keywords":
      $this->meta_keywords = preg_replace("/[^\w ,.!\-']*/", "", strip_tags($_POST["element"]));
      $update["meta_keywords"]  = $this->meta_keywords;
      break;

    case "description":
      $this->meta_description = strip_tags($_POST["element"]);
      $update["meta_description"]  = $this->meta_description;
      break;

    case "robots":
      $this->meta_robots = $_POST["element"];
    $update["meta_robots"]  = $this->meta_robots;
      break;

    case "author":
      $this->meta_author = strip_tags($_POST["element"]);
    $update["meta_author"]  = $this->meta_author;
      break;

    case "owner":
      $this->meta_owner = strip_tags($_POST["element"]);
    $update["meta_owner"]  = $this->meta_owner;
      break;

    case "content":
      $this->meta_content = strip_tags($_POST["element"]);
    $update["meta_content"]  = $this->meta_content;
      break;

    }

    $GLOBALS["core"]->sqlUpdate($update, "mod_layout_config");

  }


  function userAdmin(){
    $this->_userPanel = TRUE;
    $GLOBALS["CNT_layout"]["title"] = $_SESSION["translate"]->it("User Layout Manager");
    $GLOBALS["CNT_layout"]["content"] = $_SESSION["translate"]->it("Choose an option from the menu above") . ".";
  }

  function userPanel(){
    $theme_names = $this->get_themes();
    $form = new EZForm;
    $form->add("theme_select", "select", $theme_names);
    $form->add("theme_submit", "submit", $_SESSION["translate"]->it("Set My Theme"));
    $form->add("module", "hidden", "layout");
    $form->add("layout_user", "hidden", "userPanelCommand");
    $form->add("setDefault", "submit", $_SESSION["translate"]->it("Use Site Theme"));
    $form->add("panelOff", "submit", $_SESSION["translate"]->it("Close Panel"));
    $form->setMatch("theme_select", $this->current_theme);
    if (isset($_REQUEST["layout_user"]) && $_REQUEST["layout_user"] == "admin")
          $form->add("adminMenu", "hidden", "1");

    $template = $form->getTemplate();

    $content = PHPWS_Template::processTemplate($template, "layout", "userpanel.tpl");
    $GLOBALS["Layout_Panel"]["content"] .= $content;
  }


  function userPanelCommand(){
    if (isset($_POST["theme_submit"]))
      $this->_changeUserTheme($_POST["theme_select"]);

    if (isset($_POST["setDefault"])){
      $_SESSION['OBJ_user']->dropUserVar("theme");
      $this->current_theme = $this->_default;
    }


    if (isset($_POST["panelOff"])){
      $this->_userPanel = FALSE;
      if (isset($_POST["adminMenu"])){
	header("location:index.php?module=controlpanel");
	exit();
      }
    }

    header("location:./" . preg_replace("/.*(index\.php.*|)$/Ui", "\\1", $_SERVER['HTTP_REFERER']));
    exit();
  }


}

?>
