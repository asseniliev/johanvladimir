<?php

/**
 * $Id: update.php,v 1.9 2004/11/04 18:45:42 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if ($currentVersion < "1.02") {
    $sql = "ALTER TABLE mod_linkman_links CHANGE user username TEXT NOT NULL";
    $result = $GLOBALS['core']->query($sql, TRUE);
    
    if ($result) {
	$content .= "Link Manager Updates (Version 1.02)<br />";
	$content .= "+ changed user column in table to username to be postgre compatible<br />";
    } else {
	$status = 0;
    }
}

if ($currentVersion < "1.03") {
    $content .= "Link Manager Updates (Version 1.03)<br />";
    $content .= "+ Security update with permissions<br />";
}

if (in_array($currentVersion, array("1", "1.00", "1.02", "1.03", "1.04", "1.05"))) {
    $currentVersion = "1.0.5";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "2.0.2") < 0) {
    require_once(PHPWS_SOURCE_DIR.'mod/search/class/Search.php');
    PHPWS_Search::register("linkman");
}

?>
