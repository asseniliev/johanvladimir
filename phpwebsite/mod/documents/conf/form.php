<?php

/* name for the form associated within this app */
$formName = "JAS_Document_edit";

/* extra attributes a form might have */
/* ex. array("enctype"=>"multipart/form-data") */
$formAttributes = array("method"=>"post",
                        "action"=>"index.php");

/* template for the form */
$formTemplate = "form.tpl";

/* keys for each of the elements in the form */
//$elements = array(0,1);
$elements = array(0,1);

/* key of element pointing to its type attribute */
$elementTypes = array(0=>"textarea",
		      1=>"textarea");

/* key of element pointing to its name attribute */
$elementNames = array(0=>"description",
		      1=>"full_text");

/* key of element pointing to the extra element attributes */
/* ex. 0=>array("size"=>33, "maxlength"=>255) */
$elementAttributes = array(0=>array("cols"=>60, "rows"=>4, "wrap"=>"virtual"),
			   1=>array("cols"=>60, "rows"=>6, "wrap"=>"virtual"));

/* key of element pointing to the label for that element */
$elementLabels = array(0=>$_SESSION["translate"]->it("Description"),
		       1=>$_SESSION["translate"]->it("Full Text"));

/* key of element pointing to the template for that element */
$elementTemplates = array(0=>"textarea.tpl",
			  1=>"textarea.tpl");

/* key of element pointing to its corresponding db column */
$databaseColumns = array(0=>"description",
			 1=>"full_text");

/* key of element pointing to the database properties */
$databaseProperties = array(0=>"text DEFAULT NULL",
			    1=>"text DEFAULT NULL");

?>