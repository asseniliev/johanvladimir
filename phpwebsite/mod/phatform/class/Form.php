<?php

require_once(PHPWS_SOURCE_DIR . "core/Item.php");

require_once(PHPWS_SOURCE_DIR . "core/Error.php");

require_once(PHPWS_SOURCE_DIR . "core/Form.php");

require_once(PHPWS_SOURCE_DIR . "core/Text.php");

require_once(PHPWS_SOURCE_DIR . "core/Array.php");

require_once(PHPWS_SOURCE_DIR . "mod/help/class/CLS_help.php");

/**
 * This is the PHAT_Form class.
 *
 * This class contains all the variables and functions neccessary to represent
 * and edit an html form.
 *
 * @version $Id: Form.php,v 1.118 2005/04/08 19:44:27 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Phat Form
 */
class PHAT_Form extends PHPWS_Item {

  /**
   * The current element object being worked with
   *
   * @var     mixed
   * @example $this->element = new PHAT_Checkbox;
   * @access  public
   */
  var $element = NULL;

  /**
   * Hold the reporting object
   *
   * @var     PHAT_Report
   * @example $this->report = new PHAT_Report;
   * @access  public
   */
  var $report = NULL;

  /**
   * The id of the data row in the database, currently being edited by the user.
   *
   * @var     integer
   * @example $this->_dataId = 12;
   * @access  private
   */
  var $_dataId = NULL;

  /**
   * The elements that make up this form
   *
   * @var     array
   * @example $this->_elements = array("PHAT_Textfield:23", "PHAT_Dropbox:12");
   * @access  private
   */
  var $_elements = NULL;

  /**
   * The position in this form where the user is currently working.
   *
   * @var     integer
   * @example $this->_position = 4;
   * @access  private
   */
  var $_position = 0;

  /**
   * The textual introduction for this form.
   *
   * @var     string
   * @example $this->_blurb = "This form is for querying personality.";
   * @access  private
   */
  var $_blurb0 = NULL;

  /**
   * The text shown when a user submits to this form.
   *
   * @var     string
   * @example $this->_blurb = "Thank you for your submission!";
   * @access  private
   */
  var $_blurb1 = NULL;

  /**
   * Whether or not the form can be filled out multiple times
   *
   * @var     integer
   * @example $this->_multiSubmit = 0;
   * @access  private
   */
  var $_multiSubmit = 0;

  /**
   * Whether or not anonymous posts are allowed
   *
   * @var     integer
   * @example $this->_anonymous = 0;
   * @access  private
   */
  var $_anonymous = 0;

  /**
   * Whether or not the form data can be edited
   *
   * @var     integer
   * @example $this->_editData = 0;
   * @access  private
   */
  var $_editData = 0;

  /**
   * If the user has data and can edit it, it is stored here.
   *
   * @var     array
   * @example $this->_userData = $sqlResult;
   * @access  private
   */
  var $_userData = NULL;

  /**
   * Whether or not this form has been fully saved or not
   *
   * @var     integer
   * @example $this->_saved = 0;
   * @access  private
   */
  var $_saved = 0;

  /**
   * Whether or not to show numbers for elements in this form.
   *
   * @var     integer
   * @example $this->_showElementNumbers = 0;
   * @access  private
   */
  var $_showElementNumbers = 0;

  /**
   * Whether or not to show numbers for pages in this form.
   *
   * @var     integer
   * @example $this->_showPageNumbers = 0;
   * @access  private
   */
  var $_showPageNumbers = 0;

  /**
   * The maximum number of elements to show per page on display.
   *
   * @var     integer
   * @example $this->_pageLimit = 7;
   * @access  private
   */
  var $_pageLimit = NULL;

  /**
   * List of admin emails to email the submitted data to
   * 
   * @var     array
   * @example $this->_adminEmails = array("steven@res1.appstate.edu", "bob@test.com")
   * @access  private
   */
  var $_adminEmails = array();

  /**
   * Contains PHP code to be executed after submission of form. If no code is set 
   * then nothing happens. The code is executed as a lambda function. The code is 
   * passed an array, $form_details, of the the form elements set in the form submission.
   * It is up to the user to process this array. The function *should likely* 
   * return a string to be displayed to the user. However it is up to the user.
   *
   * @var	text
   * @example   print "hello world"; // Note there must be a trailing ;
   * @access	private
   */
  var $_postProcessCode = NULL;

  /**
   * Stores the table name associated with a form that is being viewed
   * from a archive saved on disk.
   *
   * @var     text
   * @example $this->_archiveTableName = "mod_phatform_1";
   * @access  private
   */
  var $_archiveTableName = NULL;

  /**
   * Stores the filename associated with a form that is being viewed
   * from a archive saved on disk.
   *
   * @var     text
   * @example $this->_archiveFileName = "1.1085518078.phat";
   * @access  private
   */
  var $_archiveFileName = NULL;

  /**
   * Constructor for the PHAT_Form class
   *
   * @param  integer $id The database id of the form to construct
   * @access public
   */
  function PHAT_Form($id = NULL) {
    $excludeVars = array();
    $excludeVars[] = "element";
    $excludeVars[] = "_position";
    $excludeVars[] = "_dataId";
    $excludeVars[] = "_userData";
    $excludeVars[] = "report";

    $this->setTable("mod_phatform_forms");
    $this->addExclude($excludeVars);

    if(isset($id)) {
      $this->setId($id);
      $this->init();
    }

    /* If user can edit the data in this form, grab this user's data */
    if($this->_saved && !$this->_anonymous && $this->hasSubmission(FALSE)) {
      $sql = "SELECT * FROM " . $this->getTableName() .
	     " WHERE user='" . $_SESSION["OBJ_user"]->username . "'";

      if(!$this->_editData)
	$sql .= " AND position!='-1'";
      
      $result = $GLOBALS["core"]->getAll($sql);

      if(sizeof($result) > 0) {
	$this->_userData = $result[0];
	$this->_dataId = $this->_userData["id"];

	if($this->_editData)
	  $this->_position = 0;
	else
	  $this->_position = $this->_userData["position"];
      } else {
	$result = NULL;
      }
    }
  }// END FUNC PHAT_Form

  /**
   * Creates the user interface for editing this form's settings
   *
   * @return string $content The templated string containing the html to display
   *                         a user interface for editing this form's settings.
   * @access public
   */
  function editSettings() {
    unset($this->report);

    if($this->getId()) {
      /* If not a new form get the templated form info */
      $formTags["FORM_INFORMATION"] = $this->getFormInfo();
    }

    /* Setup all editable values and their labels */
    $formTags["NAME_LABEL"] = $_SESSION["translate"]->it("Name");
    $formTags["NAME_INPUT"] = PHPWS_Form::formTextField("PHAT_FormName", $this->getLabel(), PHAT_DEFAULT_SIZE, PHAT_DEFAULT_MAXSIZE);
    $formTags["PAGELIMIT_LABEL"] = $_SESSION["translate"]->it("Item limit per page");
    $formTags["PAGELIMIT_INPUT"] = PHPWS_Form::formTextField("PHAT_FormPageLimit", $this->_pageLimit, 3, 3);
    $formTags["BLURB0_LABEL"] = $_SESSION["translate"]->it("Instructions");
    $formTags["BLURB0_INPUT"] = PHPWS_Form::formTextArea("PHAT_FormBlurb0", $this->_blurb0, PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    $formTags["BLURB1_LABEL"] = $_SESSION["translate"]->it("Submission Message");
    $formTags["BLURB1_INPUT"] = PHPWS_Form::formTextArea("PHAT_FormBlurb1", $this->_blurb1, PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    /* RBW Added a section to hold the post processing code 1/3/04 */
    $formTags["POSTPROCESS_LABEL"] = $_SESSION["translate"]->it("Post Process Code");
    $formTags["POSTPROCESS_HELP"]  = CLS_help::show_link("phatform", "post_process_code");
    $formTags["POSTPROCESS_INPUT"] = PHPWS_Form::formTextArea("PHAT_PostProcess", $this->getPostProcessCode() , PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);

    $formTags["EMAIL_LABEL"] = $_SESSION["translate"]->it("Admin Email (comma delimited)");
    $formTags["EMAIL_INPUT"] = PHPWS_Form::formTextArea("PHAT_FormEmails", $this->getAdminEmails(), PHAT_DEFAULT_ROWS, PHAT_DEFAULT_COLS);
    $formTags["MULTISUBMIT_LABEL"] = $_SESSION["translate"]->it("Allow multiple submissions");
    $formTags["MULTISUBMIT_INPUT"] = PHPWS_Form::formCheckBox("PHAT_FormMultiSubmit", 1, $this->_multiSubmit);
    $formTags["ANONYMOUS_LABEL"] = $_SESSION["translate"]->it("Allow anonymous submissions");
    $formTags["ANONYMOUS_INPUT"] = PHPWS_Form::formCheckBox("PHAT_FormAnonymous", 1, $this->_anonymous);
    $formTags["EDITDATA_LABEL"] = $_SESSION["translate"]->it("Allow users to edit their form data");
    $formTags["EDITDATA_INPUT"] = PHPWS_Form::formCheckBox("PHAT_FormEditData", 1, $this->_editData);
    $formTags["SHOWELNUMS_LABEL"] = $_SESSION["translate"]->it("Show numbers for form elements (eg: 1, 2, 3)");
    $formTags["SHOWELNUMS_INPUT"] =  PHPWS_Form::formCheckBox("PHAT_FormShowElementNumbers", 1, $this->_showElementNumbers);
    $formTags["SHOWPAGENUMS_LABEL"] = $_SESSION["translate"]->it("Show form page numbers (eg: page 1 of 6)");
    $formTags["SHOWPAGENUMS_INPUT"] =  PHPWS_Form::formCheckBox("PHAT_FormShowPageNumbers", 1, $this->_showPageNumbers);
    $formTags["HIDDEN_LABEL"] = $_SESSION["translate"]->it("Hide this form");
    $formTags["HIDDEN_INPUT"] = PHPWS_Form::formCheckBox("PHAT_FormHidden", 1, $this->isHidden());

    /* Can't forget the save button */
    $formTags["SAVE_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Save Settings"), "PHAT_SaveSettings");

    $formTags["FATCAT_LABEL"] = $_SESSION["translate"]->it("Categories");
    $formTags["FATCAT"] = $_SESSION["OBJ_fatcat"]->showSelect($this->getId(), "multiple");

    if($this->getId()) {
      $formTags["ELEMENTS_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Edit Elements"), "PHAT_EditElements");
      $GLOBALS['CNT_phatform']['title'] = $this->getLabel();
    } else {
      $GLOBALS['CNT_phatform']['title'] = PHAT_TITLE;
    }

    /* Add needed hiddens */
    $elements[0] = PHPWS_Form::formHidden("module", "phatform");
    $elements[0] .= PHPWS_Form::formHidden("PHAT_FORM_OP", "SaveFormSettings");
    $elements[0] .= PHPWS_Form::formHidden("PHAT_FormId", $this->getId());
    $elements[0] .= PHPWS_Template::processTemplate($formTags, "phatform", "form/settings.tpl");
    
    /* Make the form and return it to Manager */
    $content = PHPWS_Form::makeForm("PHAT_FormSettings", "index.php", $elements);

    if($GLOBALS['core']->moduleExists("menuman") && $this->isApproved() && $this->isSaved() && $_SESSION['OBJ_user']->allow_access("menuman", "add_item")) {
      $_SESSION['OBJ_menuman']->add_module_item("phatform", "&amp;PHAT_MAN_OP=View&amp;PHAT_FORM_ID=" . $this->getId(),  "./index.php?module=phatform&amp;PHAT_FORM_OP=ToolbarAction&amp;PHAT_Settings=1", 1);
    }

    return $content;
  }// END FUNC editSettings()

  /**
   * Saves this form's settings the the database
   *
   * This is the counterpart to editSettings.  It queries the form variables
   * used to input this form's settings and sets the appropriate variables.
   * Commit is then called and everything gets saved to the database. A string
   * is returned with a success message if successful and a boolean FALSE is
   * returned on failure.
   *
   * @return mixed String on success and FALSE on failure.
   * @access public
   */
  function _saveSettings() {
    /* Initialize error to NULL */
    $error = NULL;

    /* Make sure name is set. A form is required to have a name */
    if($_REQUEST["PHAT_FormName"]) {
      $this->setLabel($_REQUEST["PHAT_FormName"]);
    } else {
      $error = new PHPWS_Error("phatform", "PHAT_Form::saveSettings",
			       $_SESSION["translate"]->it("A name was not supplied for the form!"),
			       "continue", PHAT_DEBUG_MODE);
    }

    /* Check for a blurb and set it if there is one */
    if($_REQUEST["PHAT_FormBlurb0"]) {
      $result = $this->setBlurb0($_REQUEST["PHAT_FormBlurb0"]);
      if(!PHPWS_Error::isError($error))
	$error = $result;
    } else {
      $result = $this->setBlurb0(NULL);
      if(!PHPWS_Error::isError($error))
	$error = $result;
    }

    /* Check for a blurb and set it if there is one */
    if($_REQUEST["PHAT_FormBlurb1"]) {
      $result = $this->setBlurb1($_REQUEST["PHAT_FormBlurb1"]);
      if(!PHPWS_Error::isError($error))
	$error = $result;
    } else {
      $error = new PHPWS_Error("phatform", "PHAT_Form::_saveSettings()",
			       $_SESSION["translate"]->it("You must provide a submission message!"),
			       "continue", PHAT_DEBUG_MODE);
    }

    /* RBW if the user has written some PHP code to handle post processing then store it. */
    if(isset($_REQUEST['PHAT_PostProcess'])) {
      $this->setPostProcessCode($_REQUEST['PHAT_PostProcess']);
    }

    if(isset($_REQUEST['PHAT_FormEmails'])) {
      $this->setAdminEmails($_REQUEST['PHAT_FormEmails']);
    }

    /* Set the page limit or the default if nothing was input */
    if($_REQUEST["PHAT_FormPageLimit"]) {
      $result = $this->setPageLimit($_REQUEST["PHAT_FormPageLimit"]);
      if(!PHPWS_Error::isError($error))
	$error = $result;
    } else {
      $result = $this->setPageLimit();
      if(!PHPWS_Error::isError($error))
	$error = $result;
    }

    /* Check to see if edit data was selected */
    if(isset($_REQUEST["PHAT_FormEditData"])) {
      $this->setEditData(TRUE);
    } else {
      $this->setEditData(FALSE);
    }

    /* Check to see if multiple submissions was selected */
    if(isset($_REQUEST["PHAT_FormMultiSubmit"])) {
      if(!$this->_editData) {
	$this->setMultiSubmit(TRUE);
      } else {
	$this->setMultiSubmit(FALSE);
	$error = new PHPWS_Error("phatform", "PHAT_Form::saveSettings",
				 $_SESSION["translate"]->it("You cannot allow for multiple submissions when form data can be edited."),
				 "continue", PHAT_DEBUG_MODE);
      }
    } else {
      $this->setMultiSubmit(FALSE);
    }

    /* Check to see if anonymous submissions was selected */
    if(isset($_REQUEST["PHAT_FormAnonymous"])) {
      if(!$this->_editData) {
	$this->setAnonymous(TRUE);
      } else {
	$this->setAnonymous(FALSE);
	$error = new PHPWS_Error("phatform", "PHAT_Form::saveSettings",
				 $_SESSION["translate"]->it("You cannot allow for anonymous submissions when form data can be edited."),
				 "continue", PHAT_DEBUG_MODE);
      }
    } else {
      $this->setAnonymous(FALSE);
    }

    /* Check to see if show numbers was selected */
    if(isset($_REQUEST["PHAT_FormShowElementNumbers"])) {
      $this->setShowElementNumbers(TRUE);
    } else {
      $this->setShowElementNumbers(FALSE);
    }

    /* Check to see if show numbers was selected */
    if(isset($_REQUEST["PHAT_FormShowPageNumbers"])) {
      $this->setShowPageNumbers(TRUE);
    } else {
      $this->setShowPageNumbers(FALSE);
    }

    /* Check to see if hidden was selected */
    if(isset($_REQUEST["PHAT_FormHidden"])) {
      $this->setHidden(TRUE);
    } else {
      $this->setHidden(FALSE);
    }

    /* Check to see if this form is new, and set approval state */
    if(!$this->getId()) {
      if($_SESSION["OBJ_user"]->allow_access("phatform", "approve_forms")) {
	$this->setApproved(TRUE);
      } else {
	$this->setApproved(FALSE);
      }
    }

    if(PHPWS_Error::isError($error)) {
      $error->errorMessage("CNT_phatform");
      $content = $this->editSettings();
    } else {
      /* Commit changes and check to see if an error occured */
      $result = $this->commit();
      if(PHPWS_Error::isError($result)) {
	$result->errorMessage("CNT_phatform");
	$content = $this->editSettings();
      } else {
	$this->_position = 0;

	/* If everything else saved ok, save the category selected. */
	$_SESSION["OBJ_fatcat"]->saveSelect($this->getLabel(),
	         'index.php?module=phatform&amp;PHAT_MAN_OP=View&amp;PHAT_FORM_ID=' . $this->getId(),
	         $this->getId());

	$content = $_SESSION["translate"]->it("Form settings successfully saved!<br /><br />");
	if($this->isSaved()) {
	  $content .= $this->view(FALSE);
	} else {
	  $content .= $this->view(TRUE);
	}
      }
    }
    return $content;
  }// END FUNC saveSettings()

  /**
   * Constructs a view of this form and returns it in a string.
   *
   * This function will construct a view of this form whether in edit mode
   * or submission mode and return it in a string for display.
   *
   * @param  boolean $edit Whether the view is in edit mode or not.
   * @return mixed   A templated string on success, or a FALSE on failure.
   * @access public
   */
  function view($edit = FALSE) {
    if(($this->isHidden() && !$edit) || (!$this->isSaved() && !$_SESSION['OBJ_user']->allow_access("phatform", "edit_forms"))) {
      return $_SESSION['translate']->it("This form is not available for viewing at this time.");
    }

    $GLOBALS['CNT_phatform']['title'] = $this->getLabel();
    /* Do some basic checks if we're not in edit mode */
    if(!$edit) {
      /* If this form is not anonymous and the user is not logged in, print message and bail */
      if(($this->_editData || !$this->_anonymous) && !isset($_SESSION["OBJ_user"]->username))
	return $_SESSION["translate"]->it("You must be logged in to view this form!");

      /* If this form is not multi submit and the user has filled out this for before,
         print message and bail */
      if(!$this->_editData && !$this->_multiSubmit && $this->hasSubmission())
	return $_SESSION["translate"]->it("You have already filled out this form!");

      if(!$_SESSION["OBJ_user"]->isDeity() && $_SESSION["OBJ_user"]->allow_access("phatform", "user_forms_only")) {
	if($_SESSION['OBJ_user']->username != $this->getOwner()) {
	  return $_SESSION["translate"]->it("You only have permission to edit your own forms!");
	}
      }
    }

    /* Assume the PHAT position :) */
    if(!isset($this->_position)) {
      $this->_position = 0;
    }

    /* Setup limit for loop */
    if(($this->_position + $this->_pageLimit) > sizeof($this->_elements)) {
      $limit = $this->_position + (sizeof($this->_elements) - $this->_position);
    } else {
      $limit = $this->_position + $this->_pageLimit;
    }

    /* Begin view template array */
    if($this->currentPage() == 1) {
      $viewTags["BLURB0"] = PHPWS_Text::parseOutput($this->_blurb0);

      if(!$this->_saved) {
	$viewTags["WARNING"] = $_SESSION['translate']->it("The form must be saved before it is available to the public.");
      }
    }

    $formTags = array();
    /* If this form has elements, loop and add them to the form template array */
    if(is_array($this->_elements) && sizeof($this->_elements) > 0) {
      for($i = $this->_position; $i < $limit; $i++) {
	$sectionTags = array();

	$elementInfo = explode(":", $this->_elements[$i]);

	$this->element = new $elementInfo[0]($elementInfo[1]);

	/* If user can edit data, populate for element with it */
	if(!$edit && $this->_editData && is_array($this->_userData)) {
	  if(isset($this->_userData[$this->element->getLabel()]) && $this->isSerialized($this->_userData[$this->element->getLabel()])) {
	    $value = unserialize($this->_userData[$this->element->getLabel()]);
	    $this->element->setValue($value);
	  } else {
	    $this->element->setValue($this->_userData[$this->element->getLabel()]);
	  }
	}

	/* Setup color for alternating rows in the section template */
	if(isset($flag) && $flag) {
	  $flag = FALSE;
	} else {
	  $sectionTags["BGCOLOR"] = " class=\"bg_light\" ";
	  $flag = TRUE;
	}

	/* Get view of the current element */
	$sectionTags["ELEMENT"] = $this->element->view();

	if($this->_showElementNumbers)
	  $sectionTags["ELEMENT"] = $i+1 . ". " . $sectionTags["ELEMENT"];

	/* If in edit mode, show the element editor for the current element */
	if($edit) {
	  $sectionTags["ELEMENT_NAME"] = PHPWS_Text::parseOutput($this->element->getLabel());
	  $sectionTags["ELEMENT_EDITOR"] = $this->_elementEditor($i);
	}

	if(!isset($formTags["ELEMENTS"])) {
	  $formTags["ELEMENTS"] = PHPWS_Template::processTemplate($sectionTags, "phatform", "form/section.tpl");
	} else {
	  $formTags["ELEMENTS"] .= PHPWS_Template::processTemplate($sectionTags, "phatform", "form/section.tpl");
	}
      }

      /* If we are on last page...show the submit button */
      if(!$edit) {
	if($this->currentPage() == $this->numPages()) {
	  if($this->_editData && $this->currentPage() > 1) {
	    $formTags["BACK_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Back"), "PHAT_Back");
	  }
	  $formTags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Finish"), "PHAT_Submit");
	} else {
	  if($this->_editData && $this->currentPage() > 1) {
	    $formTags["BACK_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Back"), "PHAT_Back");
	  }
	  $formTags["NEXT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Next"), "PHAT_Next");
	}
      }

      /* Check if we're in edit mode and set the phat man accordingly */
      if($edit) {
	$hiddens["PHAT_FORM_OP"] = "EditAction";
      } else {
	$hiddens["PHAT_FORM_OP"] = "Action";
      }

      /* Actually load hidden variables into the elements array */
      $hiddens["module"] = "phatform";
      $elements[0] = PHPWS_Form::formHidden($hiddens);
      $elements[0] .= PHPWS_Template::processTemplate($formTags, "phatform", "form/form.tpl");
      $viewTags["FORM"] = PHPWS_Form::makeForm("PHAT_Form", "index.php", $elements);
    }

    /* Check to see if we should show page numbers or not */
    if($this->_showPageNumbers)
      $viewTags["PAGE_NUMBER"] = $_SESSION["translate"]->it("Page [var1] of [var2]", $this->currentPage(), $this->numPages());

    /* If in edit mode, display the toolbar */
    if($edit)
      $viewTags["TOOLBAR"] = $this->_toolbar();

    return PHPWS_Template::processTemplate($viewTags, "phatform", "form/view.tpl");
  }// END FUNC view()

  function getTableName() {
    if(isset($this->_archiveTableName)) {
      return $GLOBALS['core']->tbl_prefix . $this->_archiveTableName;
    } else {
      return "{$GLOBALS['core']->tbl_prefix}mod_phatform_form_" . $this->getId();
    }
  }

  /**
   * Checks to see if the current user has a submission to this form or not.
   *
   * @return boolean TRUE is the current user has a submission. FALSE if not.
   * @access public
   */
  function hasSubmission($finished=TRUE) {
    if(!$this->_saved)
      return FALSE;

    /* Build sql statement based on the current user */

    $sql = "SELECT id FROM " . $this->getTableName() . 
           " WHERE user='" . $_SESSION["OBJ_user"]->username . "'";

    if($finished)
      $sql .= " AND position='-1'";

    /* Set fetch mode and execute the sql created above */
    $GLOBALS["core"]->setFetchMode("assoc");
    $result = $GLOBALS["core"]->getAll($sql);

    /* If a result comes back return TRUE (current user has a submission) */
    if(sizeof($result) > 0)
      return TRUE;
    else
      return FALSE;
  }

  /**
   * Pushes the current element onto the end of this form's elements array.
   *
   * @return mixed A success message on success and a PHPWS_Error object on failure.
   * @access public
   */
  function pushElement() {
    /* Calculate position based on current amount of elements */
    if((sizeof($this->_elements) % $this->_pageLimit) == 0)
      $this->_position = sizeof($this->_elements);

    /* If on first page and element added to "last" page, calculate that position */
    if($this->_position == 0 && sizeof($this->_elements) > $this->_pageLimit) {
      $this->_position = floor(sizeof($this->_elements)/$this->_pageLimit) * $this->_pageLimit;
    }

    /* Push the current element onto the elements array and unset the class variable */
    $this->_elements[] = get_class($this->element) . ":" . $this->element->getId();
    unset($this->element);

    /* Commit changes to database */
    $result = $this->commit();
    if(PHPWS_Error::isError($result)) {
      return $result;
    } else {
      return $_SESSION["translate"]->it("Element successfully added!") . "<br />";
    }
  }// END FUNC pushElement()

  /**
   * Pops an element out of the elements array, effectively removing it from this form.
   *
   * @return mixed A success message on success and a PHPWS_Error object on failure.
   * @access public
   */
  function popElement() {
    /* Create needle to search for index into elements array */
    $needle = get_class($this->element) . ":" . $this->element->getId();
    $key = array_search($needle, $this->_elements);

    /* Unset the element in the elements array and in the element class variable */
    unset($this->_elements[$key]);
    unset($this->element);

    /* Reindex the elements array after removal of the element */
    PHPWS_Array::reindex($this->_elements);

    /* Commit changes and test for errors */
    $result = $this->commit();
    if(PHPWS_Error::isError($result)) {
      return $result;
    } else {
      return $_SESSION["translate"]->it("Element successfully removed!") . "<br />";
    }
  }

  /**
   * Returns the html for the toolbar
   *
   * This function creates the toolbar which is used in edit mode to do
   * operations on this form (i.e.: Add Element, Settings, Save). It is
   * templated according to the form/toolbar.tpl template.
   *
   * @return string The html needed to display the toolbar
   * @access private
   * @see    view()
   */
  function _toolbar() {
    $elementTypes = array("PHAT_Dropbox"=>$_SESSION["translate"]->it("Dropbox"),
			  "PHAT_Textfield"=>$_SESSION["translate"]->it("Textfield"),
			  "PHAT_Textarea"=>$_SESSION["translate"]->it("Textarea"),
			  "PHAT_Multiselect"=>$_SESSION["translate"]->it("Multiple Select"),
			  "PHAT_Radiobutton"=>$_SESSION["translate"]->it("Radio Button"),
			  "PHAT_Checkbox"=>$_SESSION["translate"]->it("Checkbox"));

    for($i=0; $i < $this->numPages(); $i++)
      $pageNumber[] = $i+1;

    $toolbarTags["PAGE_DROPDOWN_LABEL"] = $_SESSION["translate"]->it("Page");
    $toolbarTags["PAGE_DROPDOWN"] = PHPWS_Form::formSelect("PHAT_PageNumber", $pageNumber, $this->currentPage());
    $toolbarTags["GO_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Go!"), "PHAT_Go");
    $toolbarTags["ACTION_SELECT"] = PHPWS_Form::formSelect("PHAT_ElementType", $elementTypes);
    $toolbarTags["ADD_BUTTON"] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Add"), "PHAT_Add");
    $toolbarTags["SETTINGS_BUTTON"] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Form Settings"), "PHAT_Settings");

    if($this->isApproved())
      $toolbarTags["SAVE_BUTTON"] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save Form"), "PHAT_Save");

    $elements[0] = PHPWS_Form::formHidden("module", "phatform");
    $elements[0] .= PHPWS_Form::formHidden("PHAT_FORM_OP", "ToolbarAction");
    $elements[0] .= PHPWS_Template::processTemplate($toolbarTags, "phatform", "form/toolbar.tpl");

    return PHPWS_Form::makeForm("PHAT_Toolbar", "index.php", $elements);
  }// END FUNC _toolbar()

  /**
   * Executes actions for this form's toolbar
   *
   * This function catches actions from the toolbar and executes them.
   *
   * @return mixed The content returned from function calls from within this function.
   * @access public
   */
  function _toolbarAction() {
    $content = NULL;

    if(isset($_REQUEST["PHAT_Add"]) && isset($_REQUEST["PHAT_ElementType"])) {
	$this->element = new $_REQUEST["PHAT_ElementType"];
	$content = $this->element->edit();
    } elseif(isset($_REQUEST["PHAT_Settings"])) {
      unset($this->report);
      $content = $_SESSION["PHAT_FormManager"]->menu();
      $content .= $this->editSettings();
    } elseif(isset($_REQUEST["PHAT_Save"])) {
      if($this->_saved) {
	$content = $this->view(TRUE);
      } else {
	$this->_save();
      }
    } elseif(isset($_REQUEST["PHAT_Go"])) {
      $this->_position = $_REQUEST["PHAT_PageNumber"] * $this->_pageLimit;
      $content = $_SESSION["PHAT_FormManager"]->menu();
      $content .= $this->view(TRUE);
    }

    return $content;
  }// END FUNC _toolbarAction()

  /**
   * Returns the html for a simple dropbox with element actions and a Go! button.
   *
   * @param  integer $key    The index of the element in this form's elements array.
   * @return string  $editor The html needed to display the editor within a form.
   * @access private
   * @see    view()
   */
  function _elementEditor($key) {
    $actions["edit"] = $_SESSION["translate"]->it("Edit");
    $actions["remove"] = $_SESSION["translate"]->it("Remove");
    $actions["moveUp"] = $_SESSION["translate"]->it("Move Up");
    $actions["moveDown"] = $_SESSION["translate"]->it("Move Down");

    $editor = PHPWS_Form::formSelect("PHAT_Action_$key", $actions);
    $editor .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Go"), "go_$key");

    return $editor;
  }// END FUNC _elementEditor()

  /**
   * Performs an action based off the element editor for a specific element in this form.
   *
   * @return mixed $content The content returned from any functions called via this function.
   * @access private
   */
  function _editAction() {
    /* Loop through elements and try to determine which element had it's go button pressed */
    foreach($this->_elements as $key=>$elementInfo) {

      if(isset($_REQUEST["go_$key"])) {
	$elementInfo = explode(":", $elementInfo);
	$this->element = new $elementInfo[0]($elementInfo[1]);

	switch($_REQUEST["PHAT_Action_$key"]) {
	  case "edit":
	  $content = $this->element->edit();
	  break;

	  case "remove":
	  $content = $this->element->remove();
	  break;

	  case "moveUp":
	  if($key > 0) {
	    PHPWS_Array::swap($this->_elements, $key, $key-1);
	  } else {
	    $temp = array($this->_elements[$key]);
	    unset($this->_elements[$key]);
	    $this->_elements = array_merge($this->_elements, $temp);
	    $this->_position = sizeof($this->_elements) - (sizeof($this->_elements) % $this->_pageLimit);
	  }
	  PHPWS_Array::reindex($this->_elements);
	  $content = $_SESSION["PHAT_FormManager"]->menu();
	  $content .= $this->view(TRUE);
	  break;

	  case "moveDown":
	  if($key < sizeof($this->_elements) - 1) {
	    PHPWS_Array::swap($this->_elements, $key, $key+1);
	  } else {
	    $temp = array($this->_elements[$key]);
	    unset($this->_elements[$key]);
	    $this->_elements = array_merge($temp, $this->_elements);
	    $this->_position = 0;
	  }
	  PHPWS_Array::reindex($this->_elements);
	  $content = $_SESSION["PHAT_FormManager"]->menu();
	  $content .= $this->view(TRUE);
	  break;
	}
	break;
      }

    }// END FOR LOOP

    return $content;
  }// END FUNC _editAction()

  function _formAction() {
    if(isset($_REQUEST["PHAT_Next"])) {
      if($this->isSaved()) {
	$error = $this->_saveFormData();
	if(PHPWS_Error::isError($error)) {
	  $error->errorMessage("CNT_phatform");
	}
      } else {
	$this->_position += $this->_pageLimit;
      }

      if($_SESSION["OBJ_user"]->allow_access("phatform")) {
	$content = $_SESSION["PHAT_FormManager"]->menu() . $this->view();
      } else {
	$content = $this->view();
      }
      return $content;
    } elseif(isset($_REQUEST["PHAT_Back"])) {
      $this->_position = $this->_position - $this->_pageLimit;
      if($_SESSION["OBJ_user"]->allow_access("phatform")) {
	$content = $_SESSION["PHAT_FormManager"]->menu() . $this->view();
      } else {
	$content = $this->view();
      }
      return $content;
    } elseif($_REQUEST["PHAT_Submit"]) {
      if($this->isSaved()) {
	$error = $this->_saveFormData();
	if(PHPWS_Error::isError($error)) {
	  $error->errorMessage("CNT_phatform");
	  if($_SESSION["OBJ_user"]->allow_access("phatform")) {
	    $content = $_SESSION["PHAT_FormManager"]->menu() . $this->view();
	  } else {
	    $content = $this->view();
	  }
	  return $content;
	} else {
	  if($_SESSION["OBJ_user"]->allow_access("phatform")) {
	    $content = $_SESSION["PHAT_FormManager"]->menu() . $this->_thanks();
	  } else {
	    $content = $this->_thanks();
	  }

	  $this->_emailData();

	  return $content;
	}
      } else {
	$_SESSION["PHAT_FormManager"]->_list();
	return NULL;
      }
    }
  }// END FUNC _formAction()

  function _saveFormData() {
    $error = NULL;

    /* Setup start and end values for the elements loop */
    $start = $this->_position;
    if(($this->_position + $this->_pageLimit) > sizeof($this->_elements)) {
      $end = $this->_position + (sizeof($this->_elements) - $this->_position);
    } else {
      $end = $this->_position + $this->_pageLimit;
    }

    /* Loop through elements and setup query array for database interaction */
    for($i = $start; $i < $end; $i++) {
      $elementInfo = explode(":", $this->_elements[$i]);
      $this->element = new $elementInfo[0]($elementInfo[1]);

      if($this->element->isRequired() && (!isset($_REQUEST["PHAT_" . $this->element->getLabel()]) || $_REQUEST["PHAT_" . $this->element->getLabel()] == NULL)) {
	$msg = $_SESSION["translate"]->it("You must fill out all required fields to continue.");
	$error = new PHPWS_Error("phatform", "PHAT_Form::_saveFormData", $msg,
				 "continue", PHAT_DEBUG_MODE);
      }

      if($this->_editData)
	$this->_userData[$this->element->getLabel()] =  $_REQUEST["PHAT_" . $this->element->getLabel()];

      if(isset($_REQUEST["PHAT_" . $this->element->getLabel()])) {
	require_once(PHPWS_SOURCE_DIR . "/mod/phatform/conf/phatform.php");
	
	if(strlen($_REQUEST["PHAT_" . $this->element->getLabel()]) > PHAT_MAX_CHARS_TEXT_ENTRY) {
	  $msg = $_SESSION["translate"]->it("Text entry \"". $this->element->getLabel() ."\" exceeds the maximum number of allowed characters.");
	  $error = new PHPWS_Error("phatform", "PHAT_Form::_saveFormData", $msg,
				   "continue", PHAT_DEBUG_MODE);
	}
	  
	$queryData[$this->element->getLabel()] = $_REQUEST["PHAT_" . $this->element->getLabel()];
      }
    }

    /* If no errors occured, move the user to the next page in this form */
    if(!PHPWS_Error::isError($error)) {
      if($this->currentPage() != $this->numPages()) {
	$this->_position += $this->_pageLimit;
      } else {
	$this->_position = -1;
      }
    }

    if(!$this->_anonymous)
      $queryData["user"] = $_SESSION["OBJ_user"]->username;
    else
      $queryData["user"] = "anonymous";

    $queryData["position"] = $this->_position;
    $queryData["updated"] = time();

    /* Check to see if this user has started entering data for this form yet */
    if(isset($this->_dataId)) {
      $GLOBALS["core"]->sqlUpdate($queryData, "mod_phatform_form_" . $this->getId(), "id", $this->_dataId);
    } else {
      $this->_dataId = $GLOBALS["core"]->sqlInsert($queryData, "mod_phatform_form_" . $this->getId(), FALSE, TRUE);
    }

    return $error;
  }// END FUNC _saveFormData()

  function _thanks() {
    $thanksTags["MESSAGE"] = PHPWS_Text::parseOutput($this->_blurb1);

    $dataId = $this->_dataId;

    $thanksTags["RETURN"] = null;

    /* RBW Now we handle the post processing. Check to see if somethings has been set and it's not null. Note that
       we don't check the quality or syntax of the code. There might be a way but I don't know how */
    if (isset($this->_postProcessCode) && $this->_postProcessCode != NULL) {
      /* Pull out the data from the last submission and run this code over it */
      $this->loadUserData();
      $form_details = array();

      /* Is there a better way to do this? I just want the data elements of the form, not the rest of the row. Perhaps
          we should write a better SQL statement. */
      foreach($this->_userData as $key=>$value) {
          if (($key != "id") && ($key != "user") && ($key != "updated") && ($key != "position"))
	    $form_details[$key] = $value;
      }

      /* Now create a lambda function to execute the form submission data. As mentioned before no checking is done
          on code validity! You're on your own. */
      $newfunc = create_function('$form_details', $this->_postProcessCode);
        
      /* Store any output from the function to be displayed on the screen */
      $thanksTags["RETURN"] = $newfunc($form_details);
    } 

    if(isset($this->report)) {
      PHAT_Form($this->getId());
      $this->report = new PHAT_Report;
      $this->setDataId($dataId);

      /* RBW Append any output onto what we already have from the post processing code. */
      $thanksTags["RETURN"] .= "<a href=\"index.php?module=phatform&amp;PHAT_REPORT_OP=list\">" . $_SESSION["translate"]->it("Return to Report") . "</a>";
    } elseif($this->_multiSubmit) {
      /* RBW Append any output onto what we already have from the post processing code. */
      $thanksTags["RETURN"] .= "<a href=\"index.php?module=phatform&amp;PHAT_MAN_OP=View&amp;PHAT_FORM_ID=" . $this->getId() . "\">" . $_SESSION["translate"]->it("Retake Form") . "</a>";
    }

    $thanksTags["HOME"] = "<a href=\"./index.php\">" . $_SESSION["translate"]->it("Home") . "</a>";

    $GLOBALS['CNT_phatform']['title'] = $this->getLabel();
    return PHPWS_Template::processTemplate($thanksTags, "phatform", "form/thanks.tpl");
  }

  function checkLabel($label) {
    if(is_array($this->_elements)) {
      foreach($this->_elements as $value) {
	$elementInfo = explode(":", $value);
	$element = new $elementInfo[0]($elementInfo[1]);

	if(strcasecmp($label, $element->getLabel()) == 0) {
	  unset($element);
	  return FALSE;
	}
      }
    }
    return TRUE;
  }

  /**
   * This function is used to fully save a form and create it's table for data storage.
   *
   * @access private
   */
  function _save() {
    if(is_array($this->_elements) && (sizeof($this->_elements) > 0)) {
      /* Start sql to create a table for this form */
      $sql = "CREATE TABLE {$GLOBALS['core']->tbl_prefix}mod_phatform_form_" . $this->getId() . "(
              id int(11) NOT NULL PRIMARY KEY,
              user varchar(20) NOT NULL,
              updated int(11) NOT NULL default '0',
              position int(11) NOT NULL default '0',";
      
      /* Flag used to check if we need to add a comma in the sql statement */
      $flag = FALSE;
      /* Step through this form's elements and add the sql for those columns */
      foreach($this->_elements as $value) {
	if($flag)
	  $sql .= ", ";
	
	$elementInfo = explode(":", $value);
	$this->element = new $elementInfo[0]($elementInfo[1]);
	$sql .= $this->element->getLabel() . " longtext";
	$flag = TRUE;
      }
      $sql .= ")";
      
      $GLOBALS["core"]->query($sql);
      $this->setSaved();
      $this->commit();
      
      $_SESSION["PHAT_FormManager"]->_list();
    } else {
      $error = new PHPWS_Error("phatform",
			       "PHAT_Form::_save()",
			       $_SESSION["translate"]->it("Must have at least one element to save a form."),
			       "continue",
			       PHAT_DEBUG_MODE);

      $error->message("CNT_phatform");      

      $_REQUEST['PHAT_FORM_OP'] = "EditAction";
      $_REQUEST['PHAT_Back'] = 1;
      $this->action();
    }
  }

  /**
   * Fully deletes this form and it's elements
   *
   * @access public
   */
  function delete() {
    if(is_array($this->_elements)) {
      foreach($this->_elements as $value) {
	$elementInfo = explode(":", $value);
	$this->element = new $elementInfo[0]($elementInfo[1]);
	$this->element->kill();
      }
    }

    /* If the form is saved archive all data in it's table and remove the table. */
    if($this->isSaved()) {
      $this->report = new PHAT_Report;
      $sql = "DROP TABLE {$GLOBALS['core']->tbl_prefix}mod_phatform_form_" . $this->getId();
      $GLOBALS["core"]->query($sql);
      if($this->report->getEntries()) {
	$sql = "DROP TABLE {$GLOBALS['core']->tbl_prefix}mod_phatform_form_" .
	   $this->getId() . "_seq";
	$GLOBALS["core"]->query($sql);
      }
    }

    PHPWS_Fatcat::purge($this->getId(), "phatform");
    $this->kill();
    $_SESSION["PHAT_FormManager"]->form = null;
    $_SESSION["PHAT_FormManager"]->_list();
  }

  /**
   * Returns the templated form information for this form.
   *
   * @return string The templated string containing this form's information.
   * @access pulic
   */
  function getFormInfo() {
    /* Created info tags */
      $infoTags["CREATED_LABEL"] = $_SESSION['translate']->it("Created");
      $infoTags["UPDATED_LABEL"] = $_SESSION['translate']->it("Updated");
    $infoTags["CREATED"] = $this->getCreated();
    $infoTags["UPDATED"] = $this->getUpdated();
    $infoTags["OWNER"] = $this->getOwner();
    $infoTags["EDITOR"] = $this->getEditor();
    $infoTags["IP_ADDRESS"] = $this->getIp();
    $infoTags["TITLE"] = $_SESSION["translate"]->it("Form Information");

    /* Return processed template */
    return PHPWS_Template::processTemplate($infoTags, "phatform", "form/info.tpl");
  }// END FUNC getFormInfo()

  function currentPage() {
    return ceil(($this->_position+1)/$this->_pageLimit);
  }// END FUNC currentPage()

  function numPages() {
    if(sizeof($this->_elements) > 0)
      return ceil(sizeof($this->_elements)/$this->_pageLimit);
    else
      return 1;
  }// END FUNC numPages()

  function numElements() {
    return sizeof($this->_elements);
  }// END FUNC numElements()

  function getPosition() {
    return $this->_position;
  }// END FUNC getPosition()

  function loadUserData() {
    $sql = "SELECT * FROM " . $this->getTableName() . " WHERE id='" . $this->_dataId . "'";
    $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
    $result = $GLOBALS["core"]->getAll($sql);

    $this->_userData = $result[0];
    $this->_position = 0;
  }

  function isArchiveView() {
    return (!empty($this->_archiveFileName) && !empty($this->_archiveTableName));
  }

  function setDataId($id) {
    if(is_numeric($id)) {
      $this->_dataId = $id;
    } else {
      $this->_dataId = NULL;
    }
  }// END FUNC setDataId()

  function setPosition($position) {
    if(is_numeric($position)) {
      $this->_position = $position;
      return TRUE;
    } else {
      return new PHPWS_Error("phatform",
			     "PHAT_Form::setPosition()",
			     $_SESSION["translate"]->it("Position must be an integer value!"),
			     "continue",
			     PHAT_DEBUG_MODE);
    }
  }// END FUNC setPosition()

  /**
   * Sets the blurb for this form to the provided string.
   *
   * @param  string  $blurb The blurb to set this forms text to.
   * @return boolean TRUE on success or FALSE on failure.
   * @access public
   */
  function setBlurb0($blurb) {
    if($blurb === NULL || is_string($blurb)) {
      $this->_blurb0 = PHPWS_Text::parseInput($blurb);
      return TRUE;
    } else {
      return new PHPWS_Error("phatform",
			     "PHAT_Form::setBlurb0",
			     $_SESSION["translate"]->it("Instructions must be a string!"),
			     "continue",
			     PHAT_DEBUG_MODE);
    }
  }// END FUNC setBlurb0()

  function setBlurb1($blurb) {
    if(is_string($blurb)) {
      $this->_blurb1 = PHPWS_Text::parseInput($blurb);
      return TRUE;
    } else {
      return new PHPWS_Error("phatform",
			     "PHAT_Form::setBlurb1",
			     $_SESSION["translate"]->it("Thank you message must be a string!"),
			     "continue",
			     PHAT_DEBUG_MODE);
    }
  }// END FUNC setBlurb1()

  /**
   * Sets the current element for this form
   *
   * @param  mixed   $element The element object to set the current element to.
   * @return boolean TRUE on success or FALSE on failure.
   * @access public
   */
  function setElement($element) {
    if(is_object($element)) {
      $this->element = $element;
      return TRUE;
    } else {
      return new PHPWS_Error("phatform",
			     "PHAT_Form::setElement",
			     $_SESSION["translate"]->it("Element must be an object!"),
			     "continue",
			     PHAT_DEBUG_MODE);
    }
  }// END FUNC setElement()

  /**
   * Sets the page limit for this form to the provided $limit
   *
   * @param  integer $limit The munber to set the page limit to.
   * @access public
   */
  function setPageLimit($limit=PHAT_PAGE_LIMIT) {
    if(is_numeric($limit) && $limit > 0) {
      $this->_pageLimit = $limit;
      return TRUE;
    } else {
      return new PHPWS_Error("phatform",
			     "PHAT_Form::setPageLimit",
			     $_SESSION["translate"]->it("Page Limit must be an integer greater than zero!"),
			     "continue",
			     PHAT_DEBUG_MODE);
    }
  }// END FUNC setPageLimit()

  /**
   * Sets the multiple submissions flag to on or off
   *
   * If anything besides a '0' or 'FALSE' is recieved, multiple submission
   * are allowed for this form. Otherwise they are not.
   *
   * @param  mixed   $flag Can be anything evaluating to TRUE or FALSE.
   * @access public
   */
  function setMultiSubmit($flag=TRUE) {
    if($flag)
      $this->_multiSubmit = 1;
    else
      $this->_multiSubmit = 0;
  }// END FUNC setMultiSubmit()

  /**
   * Sets the anonymous submissions on or off for this form
   *
   * If anything besides a '0' or 'FALSE' is recieved, anonymous submissions
   * are allowed for this form. Otherwise they are not.
   *
   * @param  mixed   $flag Can be anything evaluating to TRUE or FALSE.
   * @access public
   */
  function setAnonymous($flag=TRUE) {
    if($flag)
      $this->_anonymous = 1;
    else
      $this->_anonymous = 0;
  }// END FUNC setAnonymous()

  /**
   * Sets the edit data flag on or off
   *
   * If anything besides a '0' or 'FALSE' is recieved, users are allowed to edit
   * data they submitted to this form. Otherwise they cannot.
   *
   * @param  mixed   $flag Can be anything evaluating to TRUE or FALSE.
   * @access public
   */
  function setEditData($flag=TRUE) {
    if($flag)
      $this->_editData = 1;
    else
      $this->_editData = 0;
  }// END FUNC setEditData()

  /**
   * Sets the show elements numbers flag on or off
   *
   * If anything besides a '0' or 'FALSE' is recieved, element numbers are turned on.
   * Otherwise they are turned off.
   *
   * @param  mixed   $flag Can be anything evaluating to TRUE or FALSE.
   * @access public
   */
  function setShowElementNumbers($flag=TRUE) {
    if($flag)
      $this->_showElementNumbers = 1;
    else
      $this->_showElementNumbers = 0;
  }// END FUNC setShowElementNumbers()

  /**
   * Sets the show page numbers flag on or off
   *
   * If anything besides a '0' or 'FALSE' is recieved, page numbers are turned on.
   * Otherwise they are turned off.
   *
   * @param  mixed   $flag Can be anything evaluating to TRUE or FALSE.
   * @access public
   */
  function setShowPageNumbers($flag=TRUE) {
    if($flag)
      $this->_showPageNumbers = 1;
    else
      $this->_showPageNumbers = 0;
  }// END FUNC setShowPageNumbers()

  /**
   * Sets the saved flag on or off
   *
   * If anything besides a '0' or 'FALSE' is recieved, saved is set to 1.
   * Otherwise it is set to 0.
   *
   * @param  mixed   $flag Can be anything evaluating to TRUE or FALSE.
   * @access public
   */
  function setSaved($flag=TRUE) {
    if($flag)
      $this->_saved = 1;
    else
      $this->_saved = 0;
  }// END FUNC setSaved()

  /**
   * Returns whether or not this form is saved.
   *
   * @return boolean TRUE if saved or FALSE if not saved
   * @access public
   */
  function isSaved() {
    if($this->_saved)
      return TRUE;
    else
      return FALSE;
  }

  /**
   * Returns whether or not this form is set for anonymous submissions.
   *
   * @return boolean TRUE if anonymous or FALSE if not. 
   * @access public
   */
  function isAnonymous() {
    if($this->_anonymous)
      return TRUE;
    else
      return FALSE;
  }

  /**
   * Called when a user tries to access functionality he/she has no permission to access
   *
   * @access private
   */
  function _accessDenied() {
    $error = new PHPWS_Error("phatform",
			     "PHAT_Form::action",
			     $_SESSION["translate"]->it("ACCESS DENIED!"),
			     "exit",
			     PHAT_DEBUG_MODE);
    /* Print error and exit script */
    $error->errorMessage();
  }// END FUNC accessDenied()

  function _confirmArchive() {
    if(isset($_REQUEST['PHAT_ArchiveConfirm'])) {
      include(PHPWS_SOURCE_DIR . "mod/phatform/inc/Archive.php");
      $error = NULL;
      $error = archive($this->getId());

      if(PHPWS_Error::isError($error)) {
	$error->errorMessage("CNT_phatform");
	unset($_REQUEST['PHAT_ArchiveConfirm']);
	unset($error);
	$_REQUEST['PHAT_FORM_OP'] = "ArchiveConfirm";
	$this->action();
	return;
      }

      $this->_saved = 0;
      $this->_position = 0;
      $sql = "UPDATE " . PHPWS_TBL_PREFIX . "mod_phatform_forms SET saved='" . $this->_saved . "' WHERE id='" . $this->getId() . "'";
      $GLOBALS['core']->query($sql);

      $sql = "DROP TABLE " . PHPWS_TBL_PREFIX . "mod_phatform_form_" . $this->getId();
      $GLOBALS['core']->query($sql);

      $table = PHPWS_TBL_PREFIX . "mod_phatform_form_" . $this->getId() . "_seq";
      if($GLOBALS['core']->sqlTableExists($table, TRUE)) {
	$sql = "DROP TABLE " . $table;
	$GLOBALS['core']->query($sql);
      }
      $_REQUEST['PHAT_FORM_OP'] = "EditAction";
      $_REQUEST['PHAT_Submit'] = 1;
      $this->action();
    } else if(isset($_REQUEST['PHAT_ArchiveCancel'])) {
      $_REQUEST['PHAT_MAN_OP'] = "List";
      $_SESSION['PHAT_FormManager']->action();
    } else {
      $hiddens['module'] = "phatform";
      $hiddens['PHAT_FORM_OP'] = "ArchiveConfirm";
      $elements[0] = PHPWS_Form::formHidden($hiddens);

      $confirmTags["WARNING_TAG"] = $_SESSION["translate"]->it("WARNING!");
      $confirmTags['MESSAGE'] = $_SESSION['translate']->it("You have chosen to edit a saved form! All current data will be archived and cleared if you chose to continue!  Make sure you export your data from your form before you continue!");
      $confirmTags['CANCEL_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Cancel"), "PHAT_ArchiveCancel");
      $confirmTags['CONFIRM_BUTTON'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Confirm"), "PHAT_ArchiveConfirm");

      $elements[0] .= PHPWS_Template::processTemplate($confirmTags, "phatform", "form/archiveConfirm.tpl");
      $content =  PHPWS_Form::makeForm("PHAT_FormArchiveConfirm", "index.php", $elements);

      $GLOBALS["CNT_phatform"]["title"] = $_SESSION['translate']->it("Form").": ".$this->getLabel();
      $GLOBALS["CNT_phatform"]["content"] .= $content;
    }
  }

  /* This function is never 100% sure of a serialized string. */
  function isSerialized($string) {
    if(is_string($string)) {
      $end = substr($string, -2);
      if($end == ";}") {
	return TRUE;
      } else {
	return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  function setAdminEmails($emails) {
    $emails = explode(",", str_replace(" ", "", $emails));
    $this->_adminEmails = $emails;
    return TRUE;
  }

  function getAdminEmails() {
    return implode(", ", $this->_adminEmails);
  }

  /* RBW */
  function setPostProcessCode($code) {
    if(get_magic_quotes_gpc() || get_magic_quotes_runtime()) {
      $this->_postProcessCode = stripslashes($code);
    } else {
      $this->_postProcessCode = $code;
    }
  }

  /* RBW */
  function getPostProcessCode() {
    return $this->_postProcessCode;
  }

    function _emailData() {
	require_once PHPWS_SOURCE_DIR .'mod/phatform/conf/phatform.php';

	if (is_array($this->_adminEmails) && (sizeof($this->_adminEmails) > 0)) {
	    $to = $this->getAdminEmails();
	    if (!(strlen($to) > 0)) {
		return;
	    }
	    
	    require_once 'Mail.php';
	    require_once 'Mail/mime.php';
		
	    $this->loadUserData();

	    $mime    = new Mail_mime();        
	    $headers = array('From' => "forms@{$_SERVER['SERVER_NAME']}", 'Subject' => $this->getLabel() .' (Form Submission)');

	    if (PHAT_MAIL_CONTENT_TYPE == "text/html") {
		$message = $this->emailBodyHtml($this->_userData);       
		$mime->setHTMLBody($message);

	    } else {
		$message = $this->emailBodyPlain($this->_userData);
		$mime->setTXTBody($message);
	    }

	    $this->_userData = NULL;

	    $body    = $mime->get();
	    $headers = $mime->headers($headers);
        
	    $mail =& Mail::factory("mail");
	    $mail->send($to, $headers, $body);
	}
    }

  function emailBodyPlain($content) {
    $message = "";
    foreach($content as $key=>$value) {
	if($key == "position") {
	  continue;
	} elseif($key == "updated") {
	  $value = date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, $value);
	}
	
	$message .= $key . ":  ";

	if(preg_match("/a:.:{/", $value)) {
	  $message .= implode(", ", unserialize($value));
	} else {
	  $message .= PHPWS_Text::parseOutput($value);
	}

	$message = $message . 
	  "\r\n-------------------------------------------------\r\n";
    }
    
    return $message;
  }

  function emailBodyHtml($content) {
    $entryTags = array();
    $entryTags["ENTRY_DATA"] = array();
    /* Step through the entries values and feed them through the entryRow template */

    $rowClass = NULL;
    foreach($content as $key=>$value) {
      $rowTags = array();
	if($key == "position") {
	  continue;
	} elseif($key == "updated") {
	  $value = date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, $value);
	}
	
	/* Toggle the row colors for better visability */
	PHPWS_WizardBag::toggle($rowClass, PHAT_SECTION_HEX);
	if(isset($rowClass)) {
	  $rowTags["ROW_CLASS"] = " bgcolor=\"$rowClass\"";
	}
	$rowTags["ENTRY_LABEL"] = $key;
	
	if(preg_match("/a:.:{/", $value)) {
	  $rowTags["ENTRY_VALUE"] = implode(", ", unserialize($value));
	} else {
	  $rowTags["ENTRY_VALUE"] = PHPWS_Text::parseOutput($value);
	}
	
	$entryTags["ENTRY_DATA"][] = PHPWS_Template::processTemplate($rowTags, "phatform", "report/entryRow.tpl");
    }
    
    $entryTags["ENTRY_DATA"] = implode("", $entryTags['ENTRY_DATA']);
    $message = PHPWS_Template::processTemplate($entryTags, "phatform", "report/entry.tpl");
    
    return $message;
    
  }

  /**
   * The action function for this PHAT_Form.
   *
   * 
   * @return mixed  $content Returns the contents handed to it from functions called within.
   * @access public
   */
  function action() {
    if(isset($_SESSION["PHAT_Message"])) {
      $content = $_SESSION["PHAT_Message"];
      $GLOBALS["CNT_phatform"]["content"] .= $content;
      $_SESSION["PHAT_Message"] = NULL;
    }

    switch($_REQUEST["PHAT_FORM_OP"]) {
      case "SaveFormSettings":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	if(isset($_REQUEST["PHAT_SaveSettings"])) {
	  $content = $this->_saveSettings();
	  $content = $_SESSION["PHAT_FormManager"]->menu() . $content;
	} else if($_REQUEST["PHAT_EditElements"]) {
	  if($this->isSaved()) {
	    $content = $this->_confirmArchive();
	  } else {
	    $content = $_SESSION["PHAT_FormManager"]->menu();
	    $content .= $this->view(TRUE);
	  }
	}
      } else {
	$this->_accessDenied();
      }
      break;

      case "editSettings":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	$content = $_SESSION["PHAT_FormManager"]->menu();
	$content .= $this->editSettings();
      } else {
	$this->_accessDenied();
      }
      break;

      case "editElements":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	$content = $_SESSION["PHAT_FormManager"]->menu();
	$content .= $this->view(TRUE);
      } else {
	$this->_accessDenied();
      }
      break;

      case "report":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "report_view")) {
	$this->report = new PHAT_Report;
	$content = $this->report->report();
      } else {
	$this->_accessDenied();
      }
      break;

      case "archive":
      if(!$_SESSION["OBJ_user"]->allow_access("phatform", "archive_form")) {
	$this->_accessDenied();
      } else {
	include(PHPWS_SOURCE_DIR . "mod/phatform/inc/Archive.php");
	$error = NULL;
	$error = archive($this->getId());

	if(PHPWS_Error::isError($error)) {
	  $error->errorMessage("CNT_phatform");
	} else {

	  $_SESSION["PHAT_Message"] = $_SESSION["translate"]->it(
	  "The form [var1] was successfully archived.", "<b><i>" . $this->getLabel() . "</i></b>");
	}

	$_REQUEST['PHAT_FORM_OP'] = "report";
	$this->action();
      }
      break;

      case "ToolbarAction":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	$content = $this->_toolbarAction();
      } else {
	$this->_accessDenied();
      }
      break;

      case "EditAction":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	if(isset($_REQUEST["PHAT_Submit"]) || isset($_REQUEST["PHAT_Next"]) || isset($_REQUEST["PHAT_Back"])) {
	  $content = $_SESSION["PHAT_FormManager"]->menu();
	  $content .= $this->view(TRUE);
	} else {
	  $content = $this->_editAction();
	}
      } else {
	$this->_accessDenied();
      }
      break;

      case "Action":
      $content = $this->_formAction();
      break;

      case "ArchiveConfirm":
      $this->_confirmArchive();
      break;
    }// END PHAT_FORM_OP SWITCH

    if(isset($content)) {
      $GLOBALS["CNT_phatform"]["content"] .= $content;
    }
  }// END FUNC action()

}// END CLASS PHAT_Form

?>