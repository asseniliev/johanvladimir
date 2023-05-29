<?php

/**
 * $Id: update.php,v 1.14 2004/11/04 18:47:44 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if ($currentVersion < "2.30") {
    $GLOBALS['core']->query("ALTER TABLE mod_phatform_forms ADD COLUMN `adminEmails` text DEFAULT '' AFTER `pageLimit`", TRUE);
    $content .= "Phatform Updates Version (2.30)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Now you can set up to have form submissions emailed to admins<br />";
    $content .= "You must logout and log back in before this update will take affect!<br />";
}

if ($currentVersion < "2.32") {
    $content .= "Phatform Updates Version (2.32)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Fixed a bug with the email when data is edited from the report view<br />";
    $content .= "- Fixed some xhtml compliance errors<br />";
}

if ($currentVersion < "2.33") {
    $content .= "Phatform Updates Version (2.33)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Fixed IE display bug in report view<br />";
}

if ($currentVersion < "2.38") {
    $content .= "Phatform Updates Version (2.38)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Mailing bug fixes<br />";
    $content .= "- Removed permanent Form Generator title<br />";
}

if ($currentVersion < "2.39") {
    $content .= "Phatform Updates Version (2.39)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Added title of the current form in proper places<br />";
    $content .= "- Fixed bug in reporting after entry is deleted<br />";
}

if (in_array($currentVersion, array("2.01", "2.12", "2.14", "2.15", "2.16", "2.17", "2.18", "2.19", "2.20", "2.21", "2.32", "2.33", "2.34", "2.35", "2.36", "2.38", "2.39", "2.41", "2.42", "2.43"))) {
    $currentVersion = "2.4.3";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "2.6.0") < 0) {
    $GLOBALS['core']->query("ALTER TABLE mod_phatform_forms ADD COLUMN `postProcessCode` text DEFAULT '' AFTER `adminEmails`", TRUE);
    $content .= "Phatform Updates Version (2.6.0)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Added post processing patch submitted by: Rob Willett<br />";
}

if (version_compare($currentVersion, "2.6.1") < 0) {
    $GLOBALS['core']->query("ALTER TABLE mod_phatform_forms ADD COLUMN `archiveTableName` text DEFAULT NULL AFTER `postProcessCode`", TRUE);
    $GLOBALS['core']->query("ALTER TABLE mod_phatform_forms ADD COLUMN `archiveFileName` text DEFAULT NULL AFTER `archiveTableName`", TRUE);
    $content .= "Phatform Updates Version (2.6.1)<br />";
    $content .= "-------------------------------------<br />";
    $content .= "- Added ability to manage archives and exports.<br />";
}

?>