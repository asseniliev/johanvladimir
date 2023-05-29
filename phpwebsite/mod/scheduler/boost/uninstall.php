<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("Location:./index.php");
    exit();
}

$status = 0;

if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/scheduler/boost/uninstall.sql", TRUE)) {
    $content .= "Scheduler tables successfully removed from the database.<br />";

    $images = $GLOBALS['core']->home_dir .'images/mod/scheduler/';
    if(PHPWS_File::rmdir($images))
       $content .= "Removed image directory for scheduler.<br />";
    
}
