<?php

/**
 * $Id: update.php,v 1.3 2004/11/04 18:43:23 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

/* Begin using version_compare() */

if (version_compare($currentVersion, "2.1.5") < 0) {
    require_once(PHPWS_SOURCE_DIR.'mod/search/class/Search.php');
    PHPWS_Search::register("documents");
}

?>