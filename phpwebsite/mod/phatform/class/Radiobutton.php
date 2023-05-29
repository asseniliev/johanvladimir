<?php

require_once(PHPWS_SOURCE_DIR . "mod/phatform/class/Element.php");

/**
 * Radiobutton item
 *
 * @version $Id: Radiobutton.php,v 1.28 2004/09/24 15:15:48 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Phat Form
 */
class PHAT_Radiobutton extends PHAT_Element {

  /**
   * PHAT_Radiobutton class constructor
   *
   * @param  integer $id The id of the radiobutton to be created
   * @access public
   */
  function PHAT_Radiobutton($id = NULL) {
    $this->setTable("mod_phatform_radiobutton");

    if(isset($id)) {
      $this->setId($id);
      $leftOvers = $this->init();
      unset($leftOvers);
    }
  } // END FUNC PHAT_Radiobutton

  function hasOptions() {return TRUE;}

  /**
   * View this PHAT_Radiobutton
   *
   * The view function provides the HTML for a user to view the PHAT_Radiobutton.
   *
   * @return string The HTML to be shown
   * @access public
   */
  function view($value = NULL) {
    $label = $this->getLabel();
    if(isset($_REQUEST['PHAT_' . $label])) {
      $this->setValue($_REQUEST['PHAT_' . $label]);
    }

    if($this->isRequired())
      $viewTags['REQUIRED_FLAG'] = "&#42;"; 

    $viewTags['BLURB'] = PHPWS_Text::parseOutput($this->getBlurb());
    $viewTags['RADIO_BUTTONS'] = "";

    $optionText = $this->getOptionText();
    $optionValues = $this->getOptionValues();

    for($i = 0; $i < sizeof($optionText); $i++)
      $viewTags['RADIO_BUTTONS'] .= PHPWS_Form::formRadio("PHAT_" . $label, $optionValues[$i], $this->getValue(), NULL, $optionText[$i]) . "<br />\n";

    return PHPWS_Template::processTemplate($viewTags, "phatform", "radiobutton/view.tpl");
  } // END FUNC view

  /**
   * Edit this PHAT_Radiobutton
   *
   * This function provides the HTML form to edit or create a new PHAT_Radiobutton
   *
   * @return string The HTML form for editing
   * @access public
   */
  function edit() {
    $numOptions = sizeof($this->getOptionText());
    if(!$numOptions) $numOptions="";

    $elements[0] = PHPWS_Form::formHidden(array("module"=>"phatform", "PHAT_EL_OP"=>"SaveElement"));

    if(!$this->getLabel()) {
      $num = $_SESSION['PHAT_FormManager']->form->numElements();
      $this->setLabel("Element" . ($num + 1));
    }

    if(PHAT_SHOW_INSTRUCTIONS) {
      $editTags['INSTRUCTIONS'] = $_SESSION['translate']->it("Radiobutton Element Instructions");
    }

    $editTags['BLURB_LABEL'] = $_SESSION['translate']->it("Associated Text");
    $editTags['BLURB_INPUT'] = PHPWS_Form::formTextArea("PHAT_ElementBlurb", $this->getBlurb(), PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    $editTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
    $editTags['NAME_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementName", $this->getLabel(), PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
    $editTags['OPTIONS_LABEL'] = $_SESSION['translate']->it("Number of Options");
    $editTags['OPTIONS_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementNumOptions", $numOptions, 5, 3);

    $options = $this->getOptionSets();
    if(is_array($options)) {
      $editTags['OPTION_SET_LABEL'] = $_SESSION['translate']->it("Predefined Option Set");
      $editTags['OPTION_SET_INPUT'] = PHPWS_Form::formSelect("PHAT_OptionSet", $options, $this->getOptionSet(), FALSE, TRUE);
    }

    $editTags['REQUIRE_LABEL'] = $_SESSION['translate']->it("Required");
    $editTags['REQUIRE_INPUT'] = PHPWS_Form::formCheckBox("PHAT_ElementRequired", 1, $this->isRequired());
    $editTags['BACK_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Back"), "PHAT_ElementBack");
    $editTags['NEXT_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Next"));

    $elements[0] .= PHPWS_Template::processTemplate($editTags, "phatform", "radiobutton/edit.tpl");

    return PHPWS_Form::makeForm("PHAT_RadiobuttonEdit", "index.php", $elements, "post", NULL, NULL);
  } // END FUNC edit

  /**
   * Save this PHAT_Radiobutton
   *
   * @return mixed  Content if going to getOptions stage, content for edit if first form not filled in properly,
   *                or PHPWS_Error on failure.
   * @access public
   */
  function save() {
    $error = FALSE;
    $label = $this->getLabel();
    if((!$_SESSION['PHAT_FormManager']->form->checkLabel($_REQUEST['PHAT_ElementName']) && (strcasecmp($label, $_REQUEST['PHAT_ElementName']) != 0))
       || PHPWS_Error::isError($this->setLabel(PHPWS_Database::sqlFriendlyName($_REQUEST['PHAT_ElementName'])))) {
      $message = $_SESSION['translate']->it("The name you entered for the Radiobutton is not valid or is already in use with this form.");
      $currentError = new PHPWS_Error("phatform", "PHAT_Radiobutton::save()", $message, "continue", PHAT_DEBUG_MODE);
      $error = TRUE;
    }

    $result = $this->setBlurb($_REQUEST['PHAT_ElementBlurb']);
    if(PHPWS_Error::isError($result)) {
      $currentError = $result;
      $error = TRUE;
    }

    if(isset($_REQUEST['PHAT_ElementRequired'])) {
      $this->setRequired(TRUE);
    } else {
      $this->setRequired(FALSE);
    }

    if($error) {
      return $currentError;
    } else {
      if((is_numeric($_REQUEST['PHAT_ElementNumOptions']) && ($_REQUEST["PHAT_ElementNumOptions"] > 0)) || isset($_REQUEST['PHAT_OptionSet'])) {
	return $this->getOptions();
      } else {
	$message = $_SESSION['translate']->it("The number of options must be a number greater than zero.");
	return new PHPWS_Error("phatform", "PHAT_Radiobutton::save()", $message, "continue", PHAT_DEBUG_MODE);
      }
    }
  } // END FUNC save
} // END CLASS PHAT_Radiobutton

?>