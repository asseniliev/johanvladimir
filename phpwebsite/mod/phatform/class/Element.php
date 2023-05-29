<?php

require_once(PHPWS_SOURCE_DIR . "core/Item.php");

require_once(PHPWS_SOURCE_DIR . "core/Error.php");

require_once(PHPWS_SOURCE_DIR . "core/Form.php");

require_once(PHPWS_SOURCE_DIR . "core/Text.php");

/**
 * Element class for phatform
 *
 * @version $Id: Element.php,v 1.35 2004/03/30 15:36:41 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Phat Form
 */
class PHAT_Element extends PHPWS_Item {

  /**
   * The blurb for this PHAT_Element
   *
   * @var     string
   * @example $this->_blurb = "What is your name?";
   * @access  private
   */
  var $_blurb = NULL;

  /**
   * Value to be displayed in this PHAT_Element
   *
   * @var     mixed
   * @example $this->_value = "Steven Levin";
   * @example $this->_value = 3;
   * @access  private 
   */
  var $_value = NULL;

  /**
   * Whether or not this PHAT_Element is a required field
   *
   * @var     boolean
   * @example $this->_required = 1;
   * @access  private
   */
  var $_required = NULL;

  /**
   * Option Text list for this PHAT_Element
   *
   * @var     array
   * @example $this->_optionText = array("Pizza", "Pretzels", "PB&H");
   * @access  private
   */
  var $_optionText = array();

  /**
   * Option Value list for this PHAT_Element
   *
   * @var     array
   * @example $this->_optionValues = array(1, 2, 3);
   * @access  private
   */
  var $_optionValues = array();

  /**
   * The option set this PHAT_Element is currently using
   *
   * @var     integer
   * @example $this->_optionSet = 3;
   * @access  private
   */
  var $_optionSet = array();

  /**
   * Sets the blurb for this element
   *
   * @param  string $blurb The text for this blurb
   * @return mixed  TRUE on success PHPWS_Error on failure
   * @access public
   */
  function setBlurb($blurb = NULL) {
    if($blurb) {
      $this->_blurb = PHPWS_Text::parseInput($blurb);
      return TRUE;
    } else if(PHAT_BLURB_REQUIRED) {
      $message = $_SESSION['translate']->it("The associated text for this element was not set.");
      return new PHPWS_Error("phatform", "PHAT_Element::setBlurb()", $message, "continue", PHAT_DEBUG_MODE);
    } else {
      $this->_blurb = NULL;
      return TRUE;
    }
  }

  /**
   * Sets the value for this element
   *
   * @param  mixed  $value Whatever the value of this PHAT_Element is to be
   * @return mixed  TRUE on success PHPWS_Error on failure
   * @access public
   */
  function setValue($value = NULL) {
    if(isset($value) && $this->hasOptions()) {
      $this->_value = $value;
      return TRUE;
    } else if(isset($value)) {
      $this->_value = PHPWS_Text::parseInput($value);
    } else if(PHAT_VALUE_REQUIRED) {
      $message = $_SESSION['translate']->it("The value for this element was not set.");
      return new PHPWS_Error("phatform", "PHAT_Element::setValue()", $message, "continue", PHAT_DEBUG_MODE);
    } else {
      $this->_value = NULL;
      return TRUE;
    }
  }

  /**
   * Sets the required variable for this PHAT_Element
   *
   * @param  boolean $flag Whether or not to set required variable
   * @access public
   */
  function setRequired($flag = TRUE) {
    if($flag)
      $this->_required = 1;
    else
      $this->_required = 0;
  }

  /**
   * Sets the option set for this PHAT_Element
   *
   * @param  interger $set The id of the option set being used
   * @access public
   */
  function setOptionSet($set = NULL) {
    if(is_numeric($set))
      $this->_optionSet = $set;
    else
      $this->_optionSet = 0;
  }

  /**
   * Provides access to the blurb variable
   *
   * @return string The blurb for this PHAT_Element
   * @access public
   */
  function getBlurb() {
    if($this->_blurb)
      return $this->_blurb;
    else
      return NULL;
  }

  /**
   * Provides access to the value variable
   *
   * @return string The value for this PHAT_Element
   * @access public
   */
  function getValue() {
    if($this->_value !== NULL)
      return $this->_value;
    else
      return NULL;
  }

  /**
   * Checks to the required variable for this PHAT_Element
   *
   * @return integer The value in the required variable
   * @access public
   */
  function isRequired() {
    return $this->_required;
  }

  /**
   * Provides access to the optionText for this PHAT_Element
   *
   * @return array  List of the options text
   * @access public
   */
  function getOptionText() {
    if($this->_optionText)
      return $this->_optionText;
    else
      return NULL;
  }

  /**
   * Provides access to the optionValues for this PHAT_Element
   *
   * @return array  List of the values for the options
   * @access public
   */
  function getOptionValues() {
    if($this->_optionValues)
      return $this->_optionValues;
    else
      return NULL;
  }

  /**
   * Provides access to the optionSet for this PHAT_Element
   *
   * @return integer The id of the current option set
   * @access public
   */
  function getOptionSet() {
    if($this->_optionSet)
      return $this->_optionSet;
    else
      return NULL;
  }

  /**
   * Provides a list of option sets currently stored in the database
   *
   * @return array  The listing of option sets
   * @access public
   */
  function getOptionSets() {
    $sql = "SELECT id, label FROM " . PHPWS_TBL_PREFIX . "mod_phatform_options";
    $optionResult = $GLOBALS['core']->query($sql);
    $options[0] = "";
    while($row = $optionResult->fetchrow(DB_FETCHMODE_ASSOC)) {
      $options[$row['id']] = $row['label'];
    }

    if(sizeof($options) > 1) {
      return $options;
    } else {
      return NULL;
    }
  }

  /**
   * Get the options for this PHAT_Element
   *
   * @return string The HTML form for retrieving the options
   * @access public
   */
  function getOptions() {
    $className = get_class($this);
    $properName = ucfirst(str_replace("phat_", "", $className));

    if(isset($_REQUEST['PHAT_OptionSet']) && ($_REQUEST['PHAT_OptionSet'] != 0) && ($_REQUEST['PHAT_OptionSet'] != $this->getOptionSet())) {
      $this->setOptionSet($_REQUEST['PHAT_OptionSet']);
      $optionResult = $GLOBALS['core']->sqlSelect("mod_phatform_options", "id", $this->getOptionSet());

      $this->_optionText = array();
      $this->_optionValues = array();

      $this->_optionText = unserialize($optionResult[0]['optionSet']);
      $this->_optionValues = unserialize($optionResult[0]['valueSet']);
    }

    if(isset($_REQUEST['PHAT_ElementNumOptions']) && is_numeric($_REQUEST['PHAT_ElementNumOptions'])) {
      $loops = $_REQUEST['PHAT_ElementNumOptions'];
      
      /* must reset these arrays for when a new number of options is entered */
      $oldText = $this->_optionText;
      $oldValues = $this->_optionValues;
      $this->_optionText = array();
      $this->_optionValues = array();
      for($i = 0; $i < $loops; $i++) {
	if(isset($oldText[$i])) {
	  $this->_optionText[$i] = $oldText[$i];
	} else {
	  $this->_optionText[$i] = NULL;
	}
	if(isset($oldValues[$i])) {
	  $this->_optionValues[$i] = $oldValues[$i];
	} else {
	  $this->_optionValues[$i] = NULL;
	}
      }

    } else if(sizeof($this->_optionText) > 0) {
      $loops = sizeof($this->_optionText);
    } else {
      $message = $_SESSION['translate']->it("This should not happen, but if it does please contact your systems administrator.");
      return new PHPWS_Error("phatform", "PHAT_Element::getOptions()",  $message, "exit", PHAT_DEBUG_MODE);
    }

    $elements[0] = PHPWS_Form::formHidden(array("module"=>"phatform", "PHAT_EL_OP"=>"SaveElementOptions"));

    if(PHAT_SHOW_INSTRUCTIONS) {
      $editTags['INSTRUCTIONS'] = $_SESSION['translate']->it("Option Instructions");
    }

    $editTags['NUMBER_LABEL'] = $_SESSION['translate']->it("Option");
    $editTags['INPUT_LABEL'] = $_SESSION['translate']->it("Text");
    $editTags['VALUE_LABEL'] = $_SESSION['translate']->it("Value");
    $editTags['DEFAULT_LABEL'] = $_SESSION['translate']->it("Default");

    $editTags['OPTIONS'] = "";
    $rowClass = NULL;

    for($i = 0; $i < $loops; $i++) {
      $optionRow['OPTION_NUMBER'] = $i + 1;
      $optionRow['OPTION_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementOptions[$i]", $this->_optionText[$i], PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
      $optionRow['VALUE_INPUT'] = PHPWS_Form::formTextField("PHAT_ElementValues[$i]", $this->_optionValues[$i], PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);

      $check = NULL;
      if($className == "phat_checkbox" || $className == "phat_multiselect") {
	if(isset($this->_optionValues[$i]) && ($this->_optionValues[$i] == $this->_value[$i])) {
	  $check = $i;
	}
	$optionRow['OPTION_DEFAULT'] = PHPWS_Form::formCheckBox("PHAT_ElementDefault[$i]", $i, $check);
      }	else {
	if(isset($this->_optionValues[$i]) && $this->_optionValues[$i] == $this->_value) {
	  $check = $i;
	}
	$optionRow['OPTION_DEFAULT'] = PHPWS_Form::formRadio("PHAT_ElementDefault", $i, $check);
      }

      $optionRow['ROW_CLASS'] = $rowClass;
      PHPWS_WizardBag::toggle($rowClass, "class=\"bg_light\"");

      $editTags['OPTIONS'] .= PHPWS_Template::processTemplate($optionRow, "phatform", "element/option.tpl");
    }


    $check = NULL;
    if($this->getId()) {
      if(($this->_optionText == $this->_optionValues) && (sizeof($this->_optionText) > 0)) {
	$check = 1;
      }
    }

    if(isset($_REQUEST['PHAT_SaveOptionSet'])) {
      $setName = $_REQUEST['PHAT_SaveOptionSet'];
    } else {
      $setName = NULL;
    }

    $editTags['USE_TEXT_CHECK'] = $_SESSION['translate']->it("Use option text as values") . ": " . PHPWS_Form::formCheckBox("PHAT_ElementUseText", 1, $check);
    $editTags['SAVE_OPTION_SET'] = $_SESSION['translate']->it("Save option set as") . ": " . PHPWS_Form::formTextField("PHAT_SaveOptionSet", $setName, PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
    $editTags['BACK_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Back"), "PHAT_OptionBack");
    $editTags['SAVE_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save " . $properName));

    $elements[0] .= PHPWS_Template::processTemplate($editTags, "phatform", "element/optionList.tpl");

    return PHPWS_Form::makeForm("PHAT_Options", "index.php", $elements, "post", NULL, NULL);
  } // END FUNC getOptions

  /**
   * Save the options for this PHAT_Element
   *
   * @return mixed  Content if the editing is to continue, PHPWS_Error on failure, or message on success
   * @access public
   */
  function saveOptions() {
    $className = get_class($this);
    $properName = ucfirst(str_replace("phat_", "", $className));

    if(is_array($_REQUEST['PHAT_ElementOptions']) && is_array($_REQUEST['PHAT_ElementValues'])) {
      $saveText = TRUE;
      $returnText = NULL;
      $saveValues = TRUE;
      $atLeastOne = FALSE;

      if(isset($_REQUEST['PHAT_ElementUseText'])) {
	$_REQUEST['PHAT_ElementValues'] = $_REQUEST['PHAT_ElementOptions'];
	$this->setOptionSet();
      }

      for($i = 0; $i < sizeof($_REQUEST['PHAT_ElementOptions']); $i++) {
	if($_REQUEST['PHAT_ElementOptions'][$i] != NULL) {
	  $this->_optionText[$i] = PHPWS_Text::parseInput($_REQUEST['PHAT_ElementOptions'][$i]);
	} else {
	  $this->_optionText[$i] = NULL;
	  $saveText = FALSE;
	}

	if($_REQUEST['PHAT_ElementValues'][$i] != NULL) {
	  $this->_optionValues[$i] = PHPWS_Text::parseInput($_REQUEST['PHAT_ElementValues'][$i]);
	  $atLeastOne = TRUE;
	} else {
	  $this->_optionValues[$i] = NULL;
	  $saveValues = FALSE;
	}
      }
      
      if($className == "phat_checkbox" || $className == "phat_multiselect") {
	for($i = 0; $i < sizeof($_REQUEST['PHAT_ElementOptions']); $i++) {
	  if(isset($_REQUEST['PHAT_ElementDefault']) && isset($_REQUEST['PHAT_ElementDefault'][$i])) {
	    $this->_value[$i] = $_REQUEST['PHAT_ElementValues'][$_REQUEST['PHAT_ElementDefault'][$i]];
	  }
	}
      } else {
	if(isset($_REQUEST['PHAT_ElementDefault'])) {
	  $this->_value = $_REQUEST['PHAT_ElementValues'][$_REQUEST['PHAT_ElementDefault']];
	} else {
	  $this->_value = NULL;
	}
      }

      if($saveText && $saveValues) {
	if($_REQUEST['PHAT_SaveOptionSet']) {
	  $label = PHPWS_Text::parseInput($_REQUEST['PHAT_SaveOptionSet']);
	  $options = addslashes(serialize($this->_optionText));
	  $values = addslashes(serialize($this->_optionValues));

	  $saveArray = array("label"=>$label,
			     "optionSet"=>$options,
			     "valueSet"=>$values
			     );

	  if($id = $GLOBALS['core']->sqlInsert($saveArray, "mod_phatform_options", FALSE, TRUE)) {
	    $this->setOptionSet($id);
	    $returnText = $_SESSION['translate']->it("The option set [var1] was successfully saved.", "<b><i>" . $label . "</i></b>") . "<br />";
	  } else {
	    $message = $_SESSION['translate']->it("The option set [var1] was unable to be saved.", "<b><i>" . $label . "</i></b>");
	    return new PHPWS_Error("phatform", "PHAT_Element::saveOptions()",  $message, "continue", PHAT_DEBUG_MODE);
	  }
	}

	if(PHPWS_Error::isError($this->commit())) {
	  $message = $_SESSION['translate']->it("The [var1] was unable to be saved.", "<b><i>" . $properName . "</i></b>");
	  return new PHPWS_Error("phatform", "PHAT_Element::saveOptions()",  $message, "continue", PHAT_DEBUG_MODE);
	} else {
	  $returnText .= $_SESSION['translate']->it("The [var1] was saved successfully.", "<b><i>" . $properName . "</i></b>");
	  return $returnText;
	}

      } else {
	if($atLeastOne) {
	  $message = $_SESSION['translate']->it("All of the values were not set. You must fill out all of them.");
	  return new PHPWS_Error("phatform", "PHAT_Element::saveOptions()",  $message, "continue", PHAT_DEBUG_MODE);
	} else {
	  $message = $_SESSION['translate']->it("All of the options and values were not set.  Check the box below to use your options as values.");
	  return new PHPWS_Error("phatform", "PHAT_Element::saveOptions()",  $message, "continue", PHAT_DEBUG_MODE);
	}
      }
    } else {
      $message = $_SESSION['translate']->it("The [var1] were unable to be saved.", "<b><i>" . $properName . "</i></b>");
      return new PHPWS_Error("phatform", "PHAT_Element::saveOptions()",  $message, "continue", PHAT_DEBUG_MODE);
    }
  } // END FUNC saveOptions

  /**
   * Action function for this PHAT_Element
   *
   * @access public
   */
  function action() {
    $content = NULL;
    
    if($this->getId()) {
      $new = FALSE;
    } else {
      $new = TRUE;
    }

    if(isset($_REQUEST["PHAT_ElementBack"])) {
      $content = $_SESSION['PHAT_FormManager']->menu();
      $content .= $_SESSION['PHAT_FormManager']->form->view(TRUE);
    } else {
      switch($_REQUEST["PHAT_EL_OP"]) {
      case "SaveElement":
	if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	  $result = $this->save();
	  if(PHPWS_Error::isError($result)) {
	    $result->errorMessage("CNT_phatform");
	    $content .= $this->edit();
	  } elseif($this->hasOptions()) {
	    $content = $result;
	  } else {
	    $content = $_SESSION['PHAT_FormManager']->menu();
	    $content .= $result . "<br />";
	      
	    if($new) {
	      $result = $_SESSION['PHAT_FormManager']->form->pushElement();
	      if(PHPWS_Error::isError($result)) {
		$result->errorMessage("CNT_phatform");
		$content .= $this->edit();
		return;
	      } else {
		$content .= $result;
	      }
	    }

	    $content .= $_SESSION['PHAT_FormManager']->form->view(TRUE);
	  }	    
	} else {
	  $this->accessDenied();
	}
	break;
	
      case "SaveElementOptions":
	if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	  if(isset($_REQUEST["PHAT_OptionBack"])) {
	    $content = $this->edit();
	  } else {
	    $result = $this->saveOptions();
	    if(PHPWS_Error::isError($result)) {
	      $result->errorMessage("CNT_phatform");
	      $content .= $this->getOptions();
	    } else {
	      $content = $_SESSION['PHAT_FormManager']->menu();
	      $content .= $result . "<br />";

	      if($new) {
		$result = $_SESSION['PHAT_FormManager']->form->pushElement();
		if(PHPWS_Error::isError($result)) {
		  $result->errorMessage("CNT_phatform");
		  $content .= $this->edit();
		  return;
		} else {
		  $content .= $result;
		}
	      }

	      $content .= $_SESSION['PHAT_FormManager']->form->view(TRUE);
	    }

	  }
	} else {
	  $this->accessDenied();
	}
	break;

      case "RemoveElement":
	if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	  $result = $this->remove();
	  if(PHPWS_Error::isError($result)) {
	    $result->errorMessage("CNT_phatform");
	  } else {
	    $content = $_SESSION['PHAT_FormManager']->menu();
	    $content .= $result . "<br />";
	  }
	  
	  $content .= $_SESSION['PHAT_FormManager']->form->view(TRUE);
	} else {
	  $this->accessDenied();
	}
	break;
      } // END PHAT_EL_OP SWITCH
    }

    if(isset($content)) {
      $GLOBALS["CNT_phatform"]["content"] .= $content;
    }
  } // END FUNC action

  /**
   * Access denied
   *
   * Exits the script because user should not be wherever they were
   *
   * @access public
   */
  function accessDenied() {
    $message = $_SESSION['translate']->it("ACCESS DENIED!");
    $error = new PHPWS_Error("phatform", "PHAT_Element::action()", $message, "exit");
    $error->errorMessage();
  } // END FUNC accessDenied()

  /**
   * Remove this PHAT_Element
   *
   * @accesss public
   */
  function remove() {
    if($_REQUEST['PHAT_Yes']) {
      $result = $this->kill();
      if(PHPWS_Error::isError($result)) {
	$message = $_SESSION['translate']->it("The element could not be deleted from the database.");
	return new PHPWS_Error("phatform", "PHAT_Element::remove()", $message, "continue", PHAT_DEBUG_MODE);
      } else {
	$result = $_SESSION['PHAT_FormManager']->form->popElement();
	if(PHPWS_Error::isError($result)) {
	  return $result;
	} else {
	  return $_SESSION['translate']->it("The element was successfully removed.");
	}
      }
    } else if($_REQUEST['PHAT_No']) {
      return $_SESSION['translate']->it("No element was removed.");;
    } else {
      $className = get_class($this);
      $properName = ucfirst(str_replace("phat_", "", $className));

      $tags['MESSAGE'] = $_SESSION['translate']->it("Are you sure you want to remove this [var1] element?", "<b><i>" . $properName . "</i></b>");
      $tags['YES_BUTTON'] = PHPWS_Form::formSubmit("Yes", "PHAT_Yes");
      $tags['NO_BUTTON'] = PHPWS_Form::formSubmit("No", "PHAT_No");
      $tags['ELEMENT_PREVIEW'] = $this->view();

      $hiddens['module'] = "phatform";
      $hiddens['PHAT_EL_OP'] = "RemoveElement";

      $elements[0] = PHPWS_Form::formHidden($hiddens);
      $elements[0] .= PHPWS_Template::processTemplate($tags, "phatform", "form/deleteConfirm.tpl");

      $content = PHPWS_Form::makeForm("PHAT_Confirm", "index.php", $elements);
    }

    return $content;
  } // END FUNC remove
} // END CLASS PHAT_Element
?>