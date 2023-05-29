<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("Location: ./index.php");
    exit();
}

$status = 0;

require_once PHPWS_SOURCE_DIR .'core/File.php';

if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR ."mod/scheduler/boost/install.sql", TRUE)) {
    $content .= "All Scheduler tables successfully written.<br />";

    $images = $GLOBALS['core']->home_dir .'images/mod/scheduler/';
    PHPWS_File::makeDir($images);
    PHPWS_File::recursiveFileCopy(PHPWS_SOURCE_DIR . 'mod/scheduler/images/', $images);
} else {
    $content .= "There was a problem writing to the database.<br />";
}
