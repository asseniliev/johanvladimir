<?php

/**
 * $Id: update.php,v 1.26 2005/08/24 19:18:38 kevin Exp $
 */

if (!$_SESSION['OBJ_user']->isDeity()){
    header('location:index.php');
    exit();
}

$status = 1;

if ($currentVersion < '3.2') {
    $status = $GLOBALS['core']->query('ALTER TABLE mod_calendar_repeats ADD active SMALLINT DEFAULT \'1\' NOT NULL', TRUE);
}

if ($currentVersion < '3.3') {
    $status = $GLOBALS['core']->query('ALTER TABLE mod_calendar_repeats DROP PRIMARY KEY', TRUE);
    $status = $GLOBALS['core']->query('ALTER TABLE mod_calendar_repeats ADD INDEX ( id )', TRUE); 
    $content .= '+ Fixed primary key error that was causing problems with repeats.<br />';
}


if ($currentVersion < '3.4') {
    $content .= '+ Fixed bugs with Deadlines and Starts At. <br />';
    $content .= '+ Reduced memory overhead. <br />';
    $content .= '+ Increased speed on side panel calendar. <br />';
    $content .= '+ Fixed where Calendar was grabbing start day correctly.<br />';
    $content .= '+ Refreshes cache after an event is created, editted or deleted.<br />';
    $content .= '+ Removed class extentions.<br />';
    $content .= '+ Many notices and warnings squashed.<br />';
    $content .= '+ Removed Form class from normal operations.<br />';
}

if (in_array($currentVersion, array('2.05', '3.05', '3.1', '3.2', '3.3', '3.4', '3.5', '3.51'))) {
    $currentVersion = '3.4.0';
}

/* Begin using version_compare() */

if (version_compare($currentVersion, '3.4.2') < 0)
    $content .= '+ Fixed the week display.<br />';

if (version_compare($currentVersion, '3.5.0') < 0){
    $content .= '+ Added checks on user submitted data to prevent XSS attacks. <br />';
    $content .= '+ Fixed military time display and form selection. <br />';
    $content .= '+ Fixed military time display and form selection. <br />';
    $content .= '+ Add translations for \'View Options\'. <br />';
    $content .= '+ Fixed program crash if user submitted a faulty event. <br />';
}

if (version_compare($currentVersion, '3.7.0') < 0){
    $columns = array('search_past'=>'smallint NOT NULL DEFAULT 0');
    $GLOBALS['core']->sqlAddColumn('mod_calendar_settings', $columns);
    $content .= '+ Added ability to ignore past events in a search. <br />';
}

if (version_compare($currentVersion, '3.7.1') < 0){
    $content .= '+ Fixed past event missing from install. <br />';
    $content .= '+ Fixed bugs with times not getting set properly. <br />';
}


if (version_compare($currentVersion, '3.7.2') < 0){
    require_once(PHPWS_SOURCE_DIR . 'mod/search/class/Search.php');
    PHPWS_Search::register('calendar');
}

if (version_compare($currentVersion, '3.7.3') < 0) {
    $columns = array('sessionView'=>'smallint NOT NULL DEFAULT 1');
    $GLOBALS['core']->sqlAddColumn('mod_calendar_settings', $columns);
    $content .= '+ Added ability turn on/off storing the calendar display in a session variable. <br />';    
}

if (version_compare($currentVersion, '3.7.8') < 0) {
    $content .= '+ Security measure against XSS<br />';
}

if (version_compare($currentVersion, '3.7.9') < 0) {
    $content .= '+ Security additions tightened up.<br />';
}

if (version_compare($currentVersion, '3.8.4') < 0) {
    $content .= '+ Users not able to upload pics on submitted events.<br />';
    $content .= '+ Admin can lock down views to registered users only.<br />';
    $columns = array('restrict_view' => 'smallint NOT NULL default 0');
    $GLOBALS['core']->sqlAddColumn('mod_calendar_settings', $columns);
}

if (version_compare($currentVersion, '3.9.0') < 0) {
    $content .= '+ Admins can export events in vCalendar format.<br />';
    $content .= '+ Admins can import calendars exported from other phpWebsites.<br />';
    $GLOBALS['core']->query('ALTER TABLE mod_calendar_events ADD timestamp VARCHAR( 15 ) NOT NULL AFTER pmID', TRUE);
    $GLOBALS['core']->query('ALTER TABLE mod_calendar_events ADD export smallint( 15 ) NOT NULL AFTER timestamp', TRUE);
    $GLOBALS['core']->query('ALTER TABLE mod_calendar_events ADD exported_from VARCHAR( 50 ) AFTER export', TRUE);

    if(!is_dir("{$GLOBALS['core']->home_dir}files/calendar"))
        PHPWS_File::makeDir($GLOBALS['core']->home_dir . "files/calendar");

    if(is_dir("{$GLOBALS['core']->home_dir}files/calendar"))
        $content .= "Calendar file directory successfully created!<br />";
    else
        $content .= "Calendar could not create the export directory:<br /> "
            . "{$GLOBALS['core']->home_dir}files/calendar/<br />";

}

if (version_compare($currentVersion, '3.9.1') < 0) {
    $GLOBALS['core']->query('ALTER TABLE mod_calendar_events CHANGE exported_from imported_id INT NULL DEFAULT NULL', TRUE);
}

if (version_compare($currentVersion, '3.9.2') < 0) {
    if($success = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . 'mod/calendar/boost/update_3_9_2.sql', 1, 1))
        $content .= '+ Added ability to export multiple calendar files.<br />';

    else
        $content .= 'ERROR: Could not import from update_3_9_2.sql!<br />';
}

if (version_compare($currentVersion, '3.9.3') < 0) {
    $GLOBALS['core']->query('ALTER TABLE mod_calendar_calendars ADD UNIQUE ( `title`)', TRUE);
}

?>
