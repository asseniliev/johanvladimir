<?php

require_once(PHPWS_SOURCE_DIR . "mod/phatform/class/Element.php");

/**
 * Textfield item
 *
 * @version $Id: Textfield.php,v 1.33 2003/10/30 15:04:33 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Phat Form
 */
class PHAT_Textfield extends PHAT_Element {

  /**
   * size attribute for textfield element
   *
   * @var     integer
   * @example $this->_size = 20;
   * @access  private
   */
  var $_size = NULL;

  /**
   * maxsize attribute for textfield element
   *
   * @var     integer
   * @example $this->_maxsize = 255;
   * @access  private
   */
  var $_maxsize = NULL;

  /**
   * PHAT_Textfield element constructor
   *
   * A PHAT_Textfield element can be constructed in two ways.  You can either
   * send a valid database ID to construct a PHAT_Textfield element that already
   * exists.  Or you can pass nothing and a new PHAT_Textfield will be created,
   * only the item table will be set.
   *
   * @param integer $id database id key for this PHAT_Textfield
   */
  function PHAT_Textfield($id = NULL) {
    $this->setTable("mod_phatform_textfield");
    $this->addExclude(array("_optionText", "_optionValues", "_optionSet"));

    if(isset($id)) {
      $this->setId($id);
      $leftOvers = $this->init();
    }
  } // END FUNC PHAT_Textfield

  function hasOptions() {return FALSE;}

  /**
   * View this PHAT_Textfield
   *
   * @return string The HTML needed to view this PHAT_Textfield
   * @access public
   */
  function view() {
    $label = $this->getLabel();
    if(isset($_REQUEST['PHAT_' . $label])) {
      $this->setValue($_REQUEST['PHAT_' . $label]);
    }

    if($this->isRequired())
      $viewTags['REQUIRED_FLAG'] = "&#42;"; 

    $viewTags['BLURB'] = PHPWS_Text::parseOutput($this->getBlurb());
    $viewTags['NAME'] = "PHAT_" . $this->getLabel();
    $viewTags['SIZE'] = $this->_size;
    $viewTags['MAXSIZE'] = $this->_maxsize;
    $viewTags['VALUE'] = $this->getValue();

    return PHPWS_Template::processTemplate($viewTags, "phatform", "textfield/view.tpl");
  } // END FUNC edit

  /**
   * Edit this PHAT_Textfield
   *
   * @return string The HTML form needed to edit this PHAT_Textfield
   * @access public
   */
  function edit() {
    $elements[0] = PHPWS_Form::formHidden(array("module"=>"phatform", "PHAT_EL_OP"=>"SaveElement"));

    if(!$this->getLabel()) {
      $num = $_SESSION['PHAT_FormManager']->form->numElements();
      $this->setLabel("Element" . ($num + 1));
    }

    if(PHAT_SHOW_INSTRUCTIONS) {
      $editTags['INSTRUCTIONS'] = $_SESSION['translate']->it("Instructions");
    }

    $editTags['BLURB_LABEL'] = $_SESSION['translate']->it("Associated Text");
    $editTags['BLURB_INPUT'] = PHPWS_Form::formTextArea("PHAT_ElementBlurb", $this->getBlurb(), PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    $editTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
    $editTags['NAME_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementName", $this->getLabel(), PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
    $editTags['SIZE_LABEL'] = $_SESSION['translate']->it("Size");
    $editTags['SIZE_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementSize", $this->_size, 5, 3);
    $editTags['MAXSIZE_LABEL'] = $_SESSION['translate']->it("Maxsize");
    $editTags['MAXSIZE_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementMaxsize", $this->_maxsize, 5, 3);
    $editTags['VALUE_LABEL'] = $_SESSION['translate']->it("Value");
    $editTags['VALUE_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementValue", $this->getValue(), PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
    $editTags['REQUIRE_LABEL'] = $_SESSION['translate']->it("Require");
    $editTags['REQUIRE_INPUT'] = PHPWS_Form::formCheckBox("PHAT_ElementRequired", 1, $this->isRequired());
    $editTags['BACK_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Back"), "PHAT_ElementBack");
    $editTags['SAVE_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save Textfield"));

    $elements[0] .= PHPWS_Template::processTemplate($editTags, "phatform", "textfield/edit.tpl");

    return PHPWS_Form::makeForm("PHAT_TextfieldEdit", "index.php", $elements, "post", NULL, NULL);
  } // END FUNC view

  /**
   * Save this PHAT_Textfield
   *
   * @return string A message on success and PHPWS_Error on failure
   * @access public
   */
  function save() {
    $error = FALSE;

    $result = $this->setValue($_REQUEST['PHAT_ElementValue']);
    if(PHPWS_Error::isError($result)) {
      $currentError = $result;
      $error = TRUE;
    }
    
    if((!$_SESSION['PHAT_FormManager']->form->checkLabel($_REQUEST['PHAT_ElementName']) && ($this->getLabel() != $_REQUEST['PHAT_ElementName']))
       || PHPWS_Error::isError($this->setLabel(PHPWS_Database::sqlFriendlyName($_REQUEST['PHAT_ElementName'])))) {
      $message = $_SESSION['translate']->it("The name you entered for the Textfield is not valid or is already in use with this form.");
      $currentError = new PHPWS_Error("phatform", "PHAT_Textfield::save()", $message, "continue", PHAT_DEBUG_MODE);
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

    $size = PHPWS_Text::parseInput($_REQUEST['PHAT_ElementSize']);
  
    if($size)
      $this->_size = $size;
    else
      $this->_size = PHAT_DEFAULT_SIZE;

    $maxsize = PHPWS_Text::parseInput($_REQUEST['PHAT_ElementMaxsize']);

    if($maxsize)
      $this->_maxsize = $maxsize;
    else
      $this->_maxsize = PHAT_DEFAULT_MAXSIZE;

    
    if($error) {
      return $currentError;
    } else {
      if(PHPWS_Error::isError($this->commit())) {
	$message = $_SESSION['translate']->it("The [var1] element could not be saved to the database.", "<b><i>Textfield</i></b>");
	return new PHPWS_Error("phatform", "PHAT_Textfield::save()", $message, "continue", PHAT_DEBUG_MODE);
      } else {
	return $_SESSION['translate']->it("The [var1] element was saved successfully.", "<b><i>Textfield</i></b>");
      }
    }
  } // END FUNC save
} // END CLASS PHAT_Textfield

?>