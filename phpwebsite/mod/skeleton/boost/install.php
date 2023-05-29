<?php

/**
 * This is a skeleton version of an installation file for boost.  Edit it to be
 * used with your module.
 *
 * $Id: install.php,v 1.13 2004/11/05 16:28:04 steven Exp $
 */

/* Make sure the user is a deity before running this script */
if (!$_SESSION['OBJ_user']->isDeity()){
    header('location:index.php');
    exit();
}

require_once PHPWS_SOURCE_DIR . 'core/File.php';

if (version_compare($GLOBALS['core']->version, '0.9.2-1') < 0) {
    $content .= 'This module requires a phpWebSite core version of 0.9.2-1 or greater to install.<br />';
    $content .= '<br />You are currently using phpWebSite core version ' . $GLOBALS['core']->version . '.<br />';
    return;
} 

/* Import installation database and dump result into status variable */
if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . 'mod/skeleton/boost/install.sql', TRUE)) {
    $content .= 'All skeleton tables successfully written.<br /><br />';
    
    /* Check for permissions and create files directory if possible */
    if (is_writable("{$GLOBALS['core']->home_dir}files/")) {
        if (!is_dir("{$GLOBALS['core']->home_dir}files/skeleton")) {
            PHPWS_File::makeDir($GLOBALS['core']->home_dir . 'files/skeleton');
            if (is_dir("{$GLOBALS['core']->home_dir}files/skeleton")) {
		$content .= 'Skeleton files directory successfully created in:<br />' . $GLOBALS['core']->home_dir .  'files/skeleton<br /><br />';
            } else {
		$content .= 'Boost could not create the skeleton files directory in:<br />' . $GLOBALS['core']->home_dir .  'files/skeleton<br />You will have to do this manually!<br /><br />';
            }
        }
    } else {
        $content .= 'Files directory is not writable.   Skeleton files directory could not be created.<br /><br />';
        $status = 0;
    }
    
    /* Check for permissions and create images directory if possible */
    if (is_writable("{$GLOBALS['core']->home_dir}images/")) {
        if (!is_dir("{$GLOBALS['core']->home_dir}images/skeleton")) {
            PHPWS_File::makeDir($GLOBALS['core']->home_dir . 'images/skeleton');
            if (is_dir("{$GLOBALS['core']->home_dir}images/skeleton")) {
		$content .= 'Skeleton images directory successfully created in:<br />' . $GLOBALS['core']->home_dir .
	            'images/skeleton<br /><br />';
            } else {
		$content .= 'Boost could not create the skeleton images directory in:<br />' . $GLOBALS['core']->home_dir .
	            'images/skeleton<br />You will have to do this manually!<br /><br />';
            }
        }
    } else {
        $content .= 'Images directory is not writable.  Skeleton images directory could not be created.<br /><br />';
        $status = 0;
    }
    
    $status = 1;
    
} else {
    $content .= 'There was a problem writing to the database!<br /><br />';
}

?>
