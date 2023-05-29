<?php

$listTags = array();
$listTags['TITLE'] = $_SESSION['translate']->it('Current Documents');
$listTags['LABEL_LABEL'] = $_SESSION['translate']->it("Name");
$listTags['UPDATED_LABEL'] = $_SESSION['translate']->it("Updated");

$class       = "JAS_Document";
$table       = "mod_documents_docs";
$dbColumns   = array("hidden", "updated", "label", "description");
$listColumns = array("Updated", "Label", "Description");
$name        = "list";
$op          = "JAS_DocumentManager_op=categories";
$where       = "approved='1' AND hidden='0'";
$order       = "label ASC";

?>
