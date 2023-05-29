<?php

/**
 * $Id: update.php,v 1.8 2004/11/05 16:28:04 steven Exp $
 */

if (!$_SESSION['OBJ_user']->isDeity()){
    header('location:index.php');
    exit();
}

$status = 1;

/* Use version_compare() and NOT < */

if (version_compare($currentVersion, "1.0.1") < 0) {
    $status = 0;

    $content .= "Skeleton updates for version 1.0.1 <br />\n";
    $content .= "-------------------------------------------<br />\n";
    $content .= "+ revised to bring it more up-to-date with current API <br />\n";

    $status = 1;	
}

?>