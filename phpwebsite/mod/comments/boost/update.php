<?php

/**
 * $Id: update.php,v 1.6 2004/11/04 18:42:43 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if ($currentVersion < "1.02") {
    $content .= "Comment Updates (Version 1.02)<br />";
    $content .= "+ fixed a bug when viewing comments threaded as anonymous user comments would crash<br />";
}

if (in_array($currentVersion, array("0.81", "0.82", "1.00", "1.02", "1.03", "1.04", "1.05"))) {
    $currentVersion = "1.0.5";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "2.0.5") < 0) {
    require_once(PHPWS_SOURCE_DIR.'mod/search/class/Search.php');
    PHPWS_Search::register("comments");
}

?>
