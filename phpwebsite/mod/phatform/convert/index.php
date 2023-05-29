<?php

/* Include configuration file for the conversion */
include("../conf/convert.php");

/* Require PEAR DB class for database interactions */
require_once("DB.php");

define("PHPWS_SOURCE_DIR", "../../../");
require_once("../../../core/Core.php");

require_once("../class/Form.php");
require_once("../class/Element.php");
require_once("../class/Textfield.php");
require_once("../class/Textarea.php");
require_once("../class/Multiselect.php");
require_once("../class/Dropbox.php");
require_once("../class/Radiobutton.php");

class CLS_phatform {
  var $form_name;
  var $form_description;
  var $form_id;
  var $form_saved;
  var $user_progress;
  var $data_id;
  var $element_limit;
  var $multi_submit;
  var $show_labels;
  var $elements;
  var $text_field;
  var $radio_button;
  var $text_area;
  var $drop_box;
  var $check_box;
  var $test_passed;
}

class CLS_phat_text_field {
  var $label;
  var $extra_text;
  var $name;
  var $size;
  var $maxsize;
  var $required;
  var $data;
}

class CLS_phat_radio_button {
  var $label;
  var $extra_text;
  var $name;
  var $options;
  var $required;
  var $data;
}

class CLS_phat_text_area {
  var $label;
  var $extra_text;
  var $name;
  var $rows;
  var $columns;
  var $required;
  var $data;
}

class CLS_phat_drop_box {
  var $label;
  var $extra_text;
  var $name;
  var $options;
  var $required;
  var $data;
}

class CLS_phat_check_box {
  var $label;
  var $extra_text;
  var $name;
  var $options;
  var $required;
  var $data;
}

function old_connect() {
  $dsn = "mysql://" . OLD_DBUSER . ":" . OLD_DBPASS . "@" . OLD_DBHOST . "/" . OLD_DBNAME;
  $db = DB::connect($dsn, TRUE);

  if(DB::isError($db)) {
    exit("There was a problem connecting to your <b>old</b> phatform <b>database</b>!<br />DSN: $dsn");
  } else {
    $db->setFetchMode(DB_FETCHMODE_ASSOC);
    return $db;
  }
}

function new_connect() {
  $dsn = "mysql://" . NEW_DBUSER . ":" . NEW_DBPASS . "@" . NEW_DBHOST . "/" . NEW_DBNAME;
  $db = DB::connect($dsn, TRUE);

  if(DB::isError($db)) {
    exit("There was a problem connecting to your <b>new</b> phatform <b>database</b>!<br />DSN: $dsn");
  } else {
    $db->setFetchMode(DB_FETCHMODE_ASSOC);
    return $db;
  }
}

$db = old_connect();
$formSQL = "SELECT * FROM " . OLD_PREFIX . "mod_phatform_forms WHERE id='5'";
$result = $db->getAll($formSQL);
$db->disconnect();
$db = NULL;

$GLOBALS["core"] = $core = new PHPWS_Core(NULL, "../../../");

if(sizeof($result) > 0) {
  $db = new_connect();
  foreach($result as $form) {
    $oldForm = unserialize($form["data"]);

    $newForm = new PHAT_Form;
    $newForm->_blurb0 = $oldForm->form_description;
    $newForm->_blurb1 = "Thank you for your submission!";
    $newForm->_label = $oldForm->form_name;
    $newForm->_saved = 0;

    if($oldForm->element_limit)
      $newForm->_pageLimit = $oldForm->element_limit;
    else
      $newForm->_pageLimit = 200;

    if($oldForm->multi_submit)
      $newForm->_multiSubmit = 1;
    else
      $newForm->_multiSubmit = 0;

    $newForm->_approved = 1;
    $newForm->_hidden = 0;

    /* Reset old element arrays to prepare for element conversion */
    reset($oldForm->text_field);
    reset($oldForm->text_area);
    reset($oldForm->radio_button);
    reset($oldForm->drop_box);
    reset($oldForm->check_box);

    /* Convert each form element */
    foreach($oldForm->elements as $arrayKey=>$arrayName) {
      $queryData = array();
      $oldElement = next($oldForm->$arrayName);

      switch($arrayName) {
        case "text_field":
	if(get_class($oldElement) == "cls_phat_text_field") {
	  $queryData["label"] = "question_" . ($arrayKey+1);
	  $queryData["blurb"] = $oldElement->extra_text;
	  $queryData["size"] = $oldElement->size;
	  $queryData["maxsize"] = $oldElement->maxsize;
	  $queryData["hidden"] = 0;
	  $queryData["approved"] = 1; 
	  $queryData["created"] = time();
	  $queryData["updated"] = time();
	  $queryData["owner"] = "converted";
	  $queryData["editor"] = "converted";
	  $queryData["ip"] = $_SERVER["REMOTE_ADDR"];
	  $queryData["groups"] = array();

	  if($oldElement->required)
	    $queryData["required"] = 1;
	  else
	    $queryData["required"] = 0;

	  $id = $core->sqlInsert($queryData, "mod_phatform_textfield", FALSE, TRUE);
	  $newForm->_elements[] = "phat_textfield:" . $id;
	}
	break;

        case "radio_button":
	if(get_class($oldElement) == "cls_phat_radio_button") {
	  $queryData["label"] = "question_" . ($arrayKey+1);
	  $queryData["blurb"] = $oldElement->extra_text;
	  $queryData["optionText"] = $oldElement->options;
	  $queryData["optionValues"] = $oldElement->options;
	  $queryData["hidden"] = 0;
	  $queryData["approved"] = 1; 
	  $queryData["created"] = time();
	  $queryData["updated"] = time();
	  $queryData["owner"] = "converted";
	  $queryData["editor"] = "converted";
	  $queryData["ip"] = $_SERVER["REMOTE_ADDR"];
	  $queryData["groups"] = array();

	  if($oldElement->required)
	    $queryData["required"] = 1;
	  else
	    $queryData["required"] = 0;

	  $id = $core->sqlInsert($queryData, "mod_phatform_radiobutton", FALSE, TRUE);
	  $newForm->_elements[] = "phat_radiobutton:" . $id;
	}
	break;
      }
    }

    $newForm->_owner = "converted";
    $newForm->_editor = "converted";
    $newForm->_created = time();
    $newForm->_updated = time();
    $newForm->_ip = $_SERVER["REMOTE_ADDR"];
    $newForm->_showElementNumbers = 0;
    $newForm->_showPageNumbers = 1;

    $_SESSION["OBJ_user"]->username = "converted";
    $newForm->commit();
  }
}

?>
