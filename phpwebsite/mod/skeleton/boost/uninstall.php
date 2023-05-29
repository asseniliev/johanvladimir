<?php

/**
 * This is a skeleton version of an uninstall file for boost. Edit it to
 * be used with your module.
 *
 * $Id: uninstall.php,v 1.8 2004/11/05 16:28:04 steven Exp $
 */

/* Make sure the user is a deity before running this script */
if (!$_SESSION['OBJ_user']->isDeity()){
    header('location:index.php');
    exit();
}

/* Import the uninstall database file and dump the result into the status variable */
if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . 'mod/skeleton/boost/uninstall.sql', 1, 1)) {
    $content .= 'All skeleton tables successfully removed!<br /><br />';

    /* Check for files directory and remove if it exists */
    if (is_dir($GLOBALS['core']->home_dir . 'files/skeleton')) {
        $content .= 'Removing skeleton files directory at:<br />' . $GLOBALS['core']->home_dir . 'files/skeleton<br /><br />';
        PHPWS_File::rmdir($GLOBALS['core']->home_dir . 'files/skeleton/');
    } else {
        $content .= 'No files directory found for removal.<br /><br />';
    }
    
    /* Check for images directory and remove if it exists */
    if (is_dir($GLOBALS['core']->home_dir . 'images/skeleton')) {
        $content .= 'Removing skeleton images directory at:<br />' . $GLOBALS['core']->home_dir . 'images/skeleton<br /><br />';
        PHPWS_File::rmdir($GLOBALS['core']->home_dir . 'images/skeleton/');
    } else {
        $content .= 'No images directory found for removal.<br /><br />';
    }

} else {
    $content .= 'There was a problem accessing the database.<br /><br />';
}

?>
