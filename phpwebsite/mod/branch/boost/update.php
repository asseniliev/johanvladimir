<?php

/**
 * $Id: update.php,v 1.1 2004/11/04 18:40:15 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if (in_array($currentVersion, array("1.1", "1.2", "1.5", "1.51", "1.52"))) {
    $currentVersion = "1.5.2";
}

/* Begin using version_compare() */

?>