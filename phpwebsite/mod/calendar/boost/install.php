<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

if ($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR."mod/calendar/boost/install.sql", 1, 1)){
    if (!is_dir("{$GLOBALS['core']->home_dir}images/calendar"))
        PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/calendar");

    if(is_dir("{$GLOBALS['core']->home_dir}images/calendar"))
        $content .= "Calendar image directories successfully created!<br />";
    else
        $content .= "Calendar could not create the image directory:<br /> "
            . "{$GLOBALS['core']->home_dir}images/calendar/<br />";

    if(!is_dir("{$GLOBALS['core']->home_dir}files/calendar"))
        PHPWS_File::makeDir($GLOBALS['core']->home_dir . "files/calendar");

    if(is_dir("{$GLOBALS['core']->home_dir}files/calendar"))
        $content .= "Calendar file directory successfully created!<br />";
    else
        $content .= "Calendar could not create the export directory:<br /> "
            . "{$GLOBALS['core']->home_dir}files/calendar/<br />";
}
?>
