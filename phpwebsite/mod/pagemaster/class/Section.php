<?php

require_once (PHPWS_SOURCE_DIR . "core/Error.php");
require_once (PHPWS_SOURCE_DIR . "core/EZform.php");
require_once (PHPWS_SOURCE_DIR . "core/Form.php");
require_once (PHPWS_SOURCE_DIR . "core/Text.php");
require_once (PHPWS_SOURCE_DIR . "core/WizardBag.php");

require_once (PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * This is the PHPWS_Section class. It holds data for a single section in a page.
 * It also contains functions for manipulation of the data and updating or
 * deleting this section.
 *
 * @version $Id: Section.php,v 1.46 2004/11/16 18:15:10 steven Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package PageMaster
 */
class PHPWS_Section {
  /**
   * Database id of this section
   * @var    integer
   * @access private
   */
  var $id = NULL;

  var $page_id = NULL;

  /**
   * Title of this section
   * @var    string
   * @access private
   */
  var $title = NULL;

  /**
   * Textual body of this section
   * @var    string
   * @access private
   */
  var $text = NULL;

  /**
   * An array of image attributes for this section's image
   * @var    array
   * @access private
   */
  var $image = array();

  var $template = "default.tpl";

  /**
   * Constructor for the PHPWS_Section object.
   *
   * @param  integer $SECT_id Database id of the section's data to be loaded into the class.
   * @access public
   */
  function PHPWS_Section ($SECT_id = NULL) {
    if($SECT_id) {
      $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_sections", "id", $SECT_id);

      $this->id = $SECT_id;
      $this->page_id = $result[0]["page_id"];
      $this->title = $result[0]["title"];
      $this->text = $result[0]["text"];
      if (!empty($result[0]["image"]))
	$this->image = unserialize($result[0]["image"]);
      $this->template = $result[0]["template"];
    }
  }// END FUNC PHPWS_Section()

  /**
   * Displays this section, usually in the context of a page object.
   *
   * @param  boolean $edit_mode Simple flag to tell section whether or not the page is in edit mode.
   * @access public
   */
  function view_section($edit_mode=FALSE) {
    include(PHPWS_SOURCE_DIR . "mod/pagemaster/conf/config.php");
    $content = NULL;
    if (isset($this->image['alt']))
      $altTag = $this->image['alt'];
    else
      $altTag = NULL;

    if($this->template) {
      $image_string = NULL;
      if(isset($this->image["name"])) {
	if(isset($this->image["url"]) && strlen($this->image["url"]) > 0) {
	  $image_string .= "<a href=\"{$this->image['url']}\">";
	}

	$image_string .= "<img src=\"$image_directory" . $this->image["name"] .
	   "\" width=\"" . $this->image["width"] . "\" height=\"" . $this->image["height"] .
	   "\" alt=\"" . $altTag . "\" title=\"" . $altTag . "\" border=\"0\" />";

	if(isset($this->image["url"]) && strlen($this->image["url"]) > 0) {
	  $image_string .= "</a>";
	}
      }

      $template_array['CREATED_INFO'] =
	 $_SESSION["translate"]->it("Created on [var1] by [var2]",
				    $_SESSION["SES_PM_page"]->created_date,
				    $_SESSION["SES_PM_page"]->created_username);
      $template_array['UPDATED_INFO'] =
	 $_SESSION["translate"]->it("Updated on [var1] by [var2]",
				    $_SESSION["SES_PM_page"]->updated_date,
				    $_SESSION["SES_PM_page"]->updated_username);
      $template_array["CREATED_DATE"] = $_SESSION["SES_PM_page"]->created_date;
      $template_array["UPDATED_DATE"] = $_SESSION["SES_PM_page"]->updated_date;
      $template_array["CREATED_USER"] = $_SESSION["SES_PM_page"]->created_username;
      $template_array["UPDATED_USER"] = $_SESSION["SES_PM_page"]->updated_username;
      $template_array["IMAGE"] = (isset($image_string)) ? $image_string : NULL;
      $template_array["TITLE"] = (isset($this->title) && $this->title != "") ? PHPWS_Text::parseOutput($this->title) : NULL;

      if ((substr($this->template, 0, 11) == 'page_insert')) {
	  $template_array["TEXT"] = $this->text;
      } else {
	  $template_array["TEXT"] = (isset($this->text) && $this->text != "") ? PHPWS_Text::parseOutput($this->text) : NULL;
      }

      $template_array["IFRAME_ERROR_MSG"] = $_SESSION['translate']->it("Sorry!  Your browser cannot display the page here.");

      $useTemplate = $this->template;
      $themeDir = $_SESSION['OBJ_layout']->theme_dir . "templates/pagemaster/" . $useTemplate;
      $templateDir = PHPWS_SOURCE_DIR . "mod/pagemaster/templates/$useTemplate";

      if (!is_file($themeDir) && !is_file($templateDir)){
	$useTemplate = "default.tpl";
	if ($_SESSION['OBJ_user']->allow_access("pagemaster")){
	  $error = new PHPWS_Error("pagemaster", "view_section", "<b>" . $_SESSION["translate"]->it("Warning") ."</b>"
				   . ": " . $_SESSION["translate"]->it("Unable to find requested template")
				   . " <b>" . $this->template . "</b>");
	  $error->message("CNT_pagemaster");
	  
	}
      }

      if(isset($_GET["PAGE_user_op"]) && $_GET["PAGE_user_op"] == "view_printable") {
	echo PHPWS_Template::processTemplate($template_array, "pagemaster", $useTemplate);
      } else {
	$content .=
	  PHPWS_Template::processTemplate($template_array, "pagemaster", $useTemplate);
      }
    }
    
    if($edit_mode) {
      if($_SESSION["OBJ_user"]->js_on) {
	$js_vars["message"] = $_SESSION["translate"]->it("Are you sure you want to delete") . " " . addslashes(strip_tags($this->title)) .
	  "?";
	$js_vars["name"] = $_SESSION["translate"]->it("Remove");
	$js_vars["value"] = "remove";
	$js_vars["location"] = "index.php?module=pagemaster&PAGE_op=remove&SECT_id=" . $this->id;

	$remove_button =
	  PHPWS_WizardBag::js_insert("confirm", "SECT_edit_delete_" . $this->id, $this->id, 0, $js_vars);
      } else {
	$remove_button =
	  PHPWS_Form::formSubmit($_SESSION["translate"]->it("Remove"), "PAGE_op[remove]");
      }

      $myelements[0] = PHPWS_Form::formHidden("module", "pagemaster");
      $myelements[0] .= PHPWS_Form::formHidden("SECT_id", $this->id);
      $myelements[0] .= $remove_button . CLS_help::show_link("pagemaster", "section_remove");

      $myelements[0] .= "<br clear=\"all\" />" .
	PHPWS_Form::formSubmit($_SESSION["translate"]->it("Edit"), "SECT_op[edit_section]") .
	CLS_help::show_link("pagemaster", "section_edit") . "&nbsp;";

      $myelements[0] .= "<br /><br />" . $_SESSION["translate"]->it("Move Section") . ": " .
	PHPWS_Form::formSubmit($_SESSION["translate"]->it("Up"), "PAGE_op[up_section]")."&nbsp;";
      $myelements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Down"), "PAGE_op[down_section]")."&nbsp;";

      $content .= PHPWS_Form::makeForm("SECT_edit_delete_" . $this->id, "index.php", $myelements, "post", 0, 0) . "<hr />";
    }
    return $content;
  }// END FUNC view_section()

  /**
   * Displays an editing interface for this section.
   *
   * @access public
   */
  function edit_section() {
    include(PHPWS_SOURCE_DIR . "mod/pagemaster/conf/config.php");

    if($this->id) {
      $section_title = "<div class=\"bg_dark\"><h3>" . $_SESSION["translate"]->it("Edit Section") . "</h3></div>";
    } else {
      $section_title = "<div class=\"bg_dark\"><h3>" . $_SESSION["translate"]->it("New Section") . "</h3></div>";
    }

    $myelements[0] = PHPWS_Form::formHidden("module", "pagemaster");

    $myelements[0] .= "<b>" . $_SESSION["translate"]->it("Section Title") . ":</b><br />" .
      PHPWS_WizardBag::js_insert("wysiwyg", "SECT_edit", "SECT_title");

    $myelements[0] .=
      PHPWS_Form::formTextField("SECT_title", htmlspecialchars($this->title), 60, 1000) .
      CLS_help::show_link("pagemaster", "section_title") . "<br /><br />";

    $myelements[0] .= "<b>" . $_SESSION["translate"]->it("Section Text") . ":</b><br />" .
      PHPWS_WizardBag::js_insert("wysiwyg", "SECT_edit", "SECT_text");

    $myelements[0] .= PHPWS_Form::formTextArea("SECT_text", $this->text, 20, 70) .
      CLS_help::show_link("pagemaster", "section_text") . "<br /><br />";

    if(!isset($this->image["alt"])) {
      $this->image["alt"] = NULL;
    }

    if(isset($this->image["name"])) {
      $myelements[0] .= "<img src=\"" . $image_directory . $this->image["name"] . "\" alt=\"" .
	 $this->image["alt"] . "\" title=\"" . $this->image["alt"] . "\" /><br />" .
	PHPWS_Form::formCheckBox("removeImage", 1, NULL, NULL, $_SESSION["translate"]->it("Remove Image?")) . "<br />";
    }

    $myelements[0] .=
      PHPWS_Form::formFile("SECT_image", 33, 255, "<b>" . $_SESSION["translate"]->it("Image") . ":</b><br />") .
      CLS_help::show_link("pagemaster", "section_image") . "<br />";

    if(!isset($this->image["alt"])) {
      $this->image["alt"] = NULL;
    }

    $myelements[0] .= PHPWS_Form::formTextField("SECT_alt", $this->image["alt"], 60, 255,
      "<b>" . $_SESSION["translate"]->it("Short Image Description") . ":</b><br />") .
      CLS_help::show_link("pagemaster", "section_image_desc") . "<br />";

    if(!isset($this->image["url"])) {
      $this->image["url"] = NULL;
    }

    $myelements[0] .= PHPWS_Form::formTextField("SECT_image_url", $this->image["url"], 50, 255,
      "<b>" . $_SESSION["translate"]->it("Image URL") . ":</b><br />") . "<br /><br />";

    if(!($temp_dir=@opendir("themes/".$_SESSION["OBJ_layout"]->current_theme."/templates/pagemaster/"))) {
      $temp_dir = opendir(PHPWS_SOURCE_DIR . $template_directory);
    }

    while($current_template = readdir($temp_dir)) {
      if (preg_match("/\.tpl\$/i", $current_template))
	$opt_array[$current_template] = $current_template;
    }

    $myelements[0] .= PHPWS_Form::formSelect("SECT_template", $opt_array, $this->template,
      NULL, NULL, NULL, "<b>" . $_SESSION["translate"]->it("Template") . ":</b>&#160;") .
      CLS_help::show_link("pagemaster", "section_template") . "<br /><br />";

    $myelements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Save Section"),
      "SECT_op[save_section]") . CLS_help::show_link("pagemaster", "section_save");

    $GLOBALS["CNT_pagemaster"]["content"] .= $section_title .
      PHPWS_Form::makeForm("SECT_edit", "index.php", $myelements, "post", 0, 1) .
      "<br /><div class=\"bg_dark\">&nbsp;</div>";
  }// END FUNC edit_section

  /**
   * Saves changes to this section in the database.
   *
   * @access public
   */
  function save_section () {
    include(PHPWS_SOURCE_DIR . "mod/pagemaster/conf/config.php");

    $_SESSION["SES_PM_error"] = NULL;

    if(!$_POST["SECT_title"] && !$_POST["SECT_text"] && !$_FILES["SECT_image"]["name"] &&
       !$_POST["SECT_alt"]) {
      $_SESSION["SES_PM_error"] = "empty_section";
      return;
    }

    if(isset($_POST["removeImage"])) {
      if($this->image["name"] && @unlink($GLOBALS['core']->home_dir . $image_directory . $this->image["name"])) {
	$this->image = array();
      } else {
	$_SESSION["SES_PM_error"] = "remove_image";
      }
    }

    if(!empty($_FILES["SECT_image"]["name"])) {
      $image = EZform::saveImage("SECT_image", $GLOBALS['core']->home_dir . $image_directory, 1024, 1000);
      if(PHPWS_Error::isError($image)) {
	$_SESSION["SES_PM_error"] = $image;
      } else {
	$this->image = $image;
      }
    }

    if(!empty($_FILES["SECT_image"]["name"]) || isset($this->image["name"])) {
      if(strlen($_POST["SECT_image_url"]) > 0) {
	//if(PHPWS_Text::isValidInput($_REQUEST["SECT_image_url"], "url")) {
	if(TRUE) {
	  $this->image["url"] = $_POST["SECT_image_url"];
	} else {
	  $message = $_SESSION["translate"]->it("The url entered for your image link is malformed. Please resubmit a valid url.");
	  $_SESSION["SES_PM_error"] = new PHPWS_Error("pagemaster", "PHPWS_Section::save_section", $message);
	}
      } else {
	  unset($this->image["url"]);
      }

      if($_POST["SECT_alt"]) {
	$this->image["alt"] = PHPWS_Text::parseInput($_POST["SECT_alt"]);
      } else {
	$_SESSION["SES_PM_error"] = "alt_tag";
      }
    }

    $this->page_id = $_SESSION["SES_PM_page"]->id;
    $this->title = PHPWS_Text::parseInput($_POST["SECT_title"]);
    $this->text = PHPWS_Text::parseInput($_POST["SECT_text"]);
    $this->template = $_POST["SECT_template"];

    /* Setup query data for this section. */
    $query_data["page_id"] = $this->page_id;
    $query_data["title"] = $this->title;    $query_data["text"] = $this->text;
    $query_data["image"] = serialize($this->image);
    $query_data["template"] = $this->template;

    /* Setup the query data for the current page.  Update info. */
    $query_data2["updated_date"] = date("Y-m-d H:i:s");
    $query_data2["updated_username"] = $_SESSION["OBJ_user"]->username;

    if($this->id) {
      $GLOBALS["core"]->sqlUpdate($query_data, "mod_pagemaster_sections", "id", $this->id);
      $this->PHPWS_Section($this->id);
      $_SESSION["SES_PM_page"]->update_section($this->id);
    } else {
      $this->id = $GLOBALS["core"]->sqlInsert($query_data, "mod_pagemaster_sections", FALSE, TRUE);
      $this->PHPWS_Section($this->id);
      $_SESSION["SES_PM_page"]->add_section();
    }

    $GLOBALS["core"]->sqlUpdate($query_data2, "mod_pagemaster_pages", "id", $_SESSION["SES_PM_page"]->id);
  }// END FUNC save_section()

  /**
   * Deletes this section from the database.
   *
   * @access public
   */
  function delete_section() {
    $GLOBALS["core"]->sqlDelete("mod_pagemaster_sections", "id", $this->id);
  }// END FUNC delete_section()

  /**
   * Prints an error cooresponding to this section.  The error message is selected
   * by passing the function an error $type
   *
   * @param  string $type Type of error that occured.
   * @access public
   */
  function error() {
    if(PHPWS_Error::isError($_SESSION["SES_PM_error"])) {
      $_SESSION["SES_PM_error"]->message("CNT_pagemaster");
    } else {
      $content = "<div class=\"errortext\"><h3>" . $_SESSION["translate"]->it("ERROR!") . "</h3></div>";
      switch($_SESSION["SES_PM_error"]) {
        case "image_upload":
	$content .= $_SESSION["translate"]->it("There was a problem uploading the image you submitted!<br />Please check the permissions on your image directory and try again.");
	break;

        case "alt_tag":
	$content .= $_SESSION["translate"]->it("You must provide a short description for your image.");
	break;

        case "remove_image":
	$content .= $_SESSION["translate"]->it("There was a problem removing the image from the file system.");
        break;

        case "alt_with_no_image":
	$content .= $_SESSION["translate"]->it("You have entered a short image description but no image is associated with this section.");
        break;

        case "empty_section":
	$content .= $_SESSION["translate"]->it("You have attempted to save an empty section.");
	break;
      }
      $GLOBALS["CNT_pagemaster"]["content"] .= $content;
    }

    $_SESSION["SES_PM_error"] = NULL;
  }// END FUNC error()

}// END CLASS PHPWS_Section

?>
