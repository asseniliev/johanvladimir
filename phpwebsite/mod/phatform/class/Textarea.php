<?php

require_once(PHPWS_SOURCE_DIR . "mod/phatform/class/Element.php");

/**
 * Textarea item
 *
 * @version $Id: Textarea.php,v 1.34 2003/10/30 15:04:33 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Phat Form
 */
class PHAT_Textarea extends PHAT_Element {

  /**
   * row attribute for textarea element
   *
   * @var     integer
   * @example $this->_rows = 8;
   * @access  private
   */
  var $_rows = NULL;

  /**
   * cols attribute for textarea element
   *
   * @var     integer
   * @example $this->_cols = 40;
   * @access  private
   */
  var $_cols = NULL;

  /**
   * PHAT_Textarea element constructor
   *
   * A PHAT_Textarea element can be constructed in two ways.  You can either
   * send a valid database ID to construct a PHAT_Textarea element that already
   * exists.  Or you can pass nothing and a new PHAT_Textarea will be created,
   * only the item table will be set.
   *
   * @param integer $id database id key for this PHAT_Textarea
   */
  function PHAT_Textarea($id = NULL) {
    $this->setTable("mod_phatform_textarea");
    $this->addExclude(array("_optionText", "_optionValues", "_optionSet"));

    if(isset($id)) {
      $this->setId($id);
      $leftOvers = $this->init();
    }
  } // END FUNC PHAT_Textarea
 
  function hasOptions() {return FALSE;}

  /**
   * View this PHAT_Textarea
   *
   * @return string The HTML to needed view this PHAT_Textarea
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
    $viewTags['ROWS'] = $this->_rows;
    $viewTags['COLS'] = $this->_cols;
    $viewTags['VALUE'] = $this->getValue();

    return PHPWS_Template::processTemplate($viewTags, "phatform", "textarea/view.tpl");
  } // END FUNC view

  /**
   * Edit this PHAT_Textarea
   *
   * @return string The HTML form needed to edit this PHAT_Textarea
   * @access public
   */
  function edit() {
    $elements[0] = PHPWS_Form::formHidden(array("module"=>"phatform", "PHAT_EL_OP"=>"SaveElement"));

    if(!$this->getLabel()) {
      $num = $_SESSION['PHAT_FormManager']->form->numElements();
      $this->setLabel("Element" . ($num + 1));
    }

    if(PHAT_SHOW_INSTRUCTIONS) {
      $editTags['INSTRUCTIONS'] = $_SESSION['translate']->it("Textarea Element Instructions");
    }

    $editTags['BLURB_LABEL'] = $_SESSION['translate']->it("Associated Text");
    $editTags['BLURB_INPUT'] = PHPWS_Form::formTextArea("PHAT_ElementBlurb", $this->getBlurb(), PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    $editTags['NAME_LABEL'] = $_SESSION['translate']->it("Name");
    $editTags['NAME_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementName", $this->getLabel(), PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
    $editTags['ROWS_LABEL'] = $_SESSION['translate']->it("Rows");
    $editTags['ROWS_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementRows", $this->_rows, 5, 3);
    $editTags['COLS_LABEL'] = $_SESSION['translate']->it("Columns");
    $editTags['COLS_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementCols", $this->_cols, 5, 3);
    $editTags['VALUE_LABEL'] = $_SESSION['translate']->it("Value");
    $editTags['VALUE_INPUT'] = PHPWS_Form::formTextArea("PHAT_ElementValue", $this->getValue(), PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    $editTags['REQUIRE_LABEL'] = $_SESSION['translate']->it("Require");
    $editTags['REQUIRE_INPUT'] = PHPWS_Form::formCheckBox("PHAT_ElementRequired", 1, $this->isRequired());
    $editTags['BACK_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Back"), "PHAT_ElementBack");
    $editTags['SAVE_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save Textarea"));

    $elements[0] .= PHPWS_Template::processTemplate($editTags, "phatform", "textarea/edit.tpl");

    return PHPWS_Form::makeForm("PHAT_TextareaEdit", "index.php", $elements, "post", NULL, NULL);
  } // END FUNC edit

  /**
   * Save this PHAT_Textarea
   *
   * @return mixed  Message on success and PHPWS_Error on failure
   * @access public
   */
  function save() {
    $error = FALSE;

    $result = $this->setValue($_REQUEST['PHAT_ElementValue']);
    if(PHPWS_Error::isError($result)) {
      $currentError = $result;
      $error = TRUE;
    }
    
    $label = $this->getLabel();
    if((!$_SESSION['PHAT_FormManager']->form->checkLabel($_REQUEST['PHAT_ElementName']) && (strcasecmp($label, $_REQUEST['PHAT_ElementName']) != 0))
       || PHPWS_Error::isError($this->setLabel(PHPWS_Database::sqlFriendlyName($_REQUEST['PHAT_ElementName'])))) {
      $message = $_SESSION['translate']->it("The name you entered for the Textarea is not valid or is already in use with this form.");
      $currentError = new PHPWS_Error("phatform", "PHAT_Textarea::save()", $message, "continue", PHAT_DEBUG_MODE);
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

    $rows = PHPWS_Text::parseInput($_REQUEST['PHAT_ElementRows']);
  
    if($rows)
      $this->_rows = $rows;
    else
      $this->_rows = PHAT_DEFAULT_ROWS;

    $cols = PHPWS_Text::parseInput($_REQUEST['PHAT_ElementCols']);

    if($cols)
      $this->_cols = $cols;
    else
      $this->_cols = PHAT_DEFAULT_COLS;

    if($error) {
      return $currentError;
    } else {
      if(PHPWS_Error::isError($this->commit())) {
	$message = $_SESSION['translate']->it("The [var1] element could not be saved to the database.", "<b><i>Textarea</i></b>");
	return new PHPWS_Error("phatform", "PHAT_Textarea::save()", $message, "continue", PHAT_DEBUG_MODE);
      } else {
	return $_SESSION['translate']->it("The [var1] element was saved successfully.", "<b><i>Textarea</i></b>");
      }
    }
  } // END FUNC save
} // END CLASS PHAT_Textarea

?>