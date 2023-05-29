<?php

$listTags                      = array();
$listTags['TITLE']             = $_SESSION["translate"]->it("Current FAQs");
$listTags['QUESTION_LABEL']     = $_SESSION["translate"]->it("Questions");
$listTags['RATING_LABEL']      = $_SESSION["translate"]->it("Ratings");

$class       = "PHPWS_Faq";
$table       = "mod_faq_questions";
$dbColumns   = array("label", "avgScore");
$listColumns = array("Label", "AvgScore");
$name        = "categories";
$where       = "approved='1' AND hidden='0'";
$order       = "created DESC";

?>
