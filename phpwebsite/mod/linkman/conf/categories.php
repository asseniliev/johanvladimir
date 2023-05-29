<?php

$listTags                     = array();
$listTags['TITLE']            = $_SESSION["translate"]->it("Current links");
$listTags['TITLE_LABEL']      = $_SESSION["translate"]->it("Title");
$listTags['URL_LABEL']        = $_SESSION["translate"]->it("URL");
$listTags['HITS_LABEL']       = $_SESSION["translate"]->it("Visits");
$listTags['DATEPOSTED_LABEL'] = $_SESSION["translate"]->it("Date Posted");
$listTags['ACTIONS_LABEL']    = $_SESSION["translate"]->it("Actions");

$rowTags            = array();
$rowTags['COLSPAN'] = 4;

$class       = "PHPWS_Link";
$table       = "mod_linkman_links";
$dbColumns   = array("active", "title", "url", "description", "hits", "dateposted");
$listColumns = array("Title", "Url", "Description", "Hits", "DatePosted");
$name        = "categories";
$where       = "new='0' AND active='1'";
$order       = "title ASC";

?>