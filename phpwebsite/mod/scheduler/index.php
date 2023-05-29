<?php

if (!isset($GLOBALS['core'])) {
    header("Location: ../../");
    exit();
}

require_once PHPWS_SOURCE_DIR . 'mod/scheduler/conf/defines.php';

if (!isset($_SESSION['PHPWS_Scheduler'])) {
    $_SESSION['PHPWS_Scheduler'] =& new PHPWS_Scheduler;
}

$protocol = 'http://';
if (isset($_SERVER['HTTPS'])) {
    $protocol = 'https://';
}

$_SESSION['OBJ_layout']->addImport('@import url("'. $protocol . PHPWS_SOURCE_HTTP .'mod/scheduler/templates/scheduler.css");');

$GLOBALS['CNT_scheduler']['title'] = 'Scheduler';

$GLOBALS['CNT_scheduler']['content'] = 
'<div class="scheduler">'. $_SESSION['PHPWS_Scheduler']->action() .'</div>';
