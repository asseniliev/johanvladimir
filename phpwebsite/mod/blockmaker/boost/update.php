<?php

/**
 * $Id: update.php,v 1.3 2004/11/04 18:37:48 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if (in_array($currentVersion, array("0.99", "1.00", "1.01"))) {
    $currentVersion = "1.0.1";
}

/* Begin using version_compare() */

?>