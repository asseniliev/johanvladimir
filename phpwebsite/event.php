<?php

$_REQUEST['module'] = $_GET['module'] = 'calendar';

if (!isset($_GET['calendar']['view'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$_REQUEST['calendar']['view'] = $_GET['calendar']['view'] = 'event';
    } else {
	$_REQUEST['calendar']['view'] = $_GET['calendar']['view'] = 'month';
    }
}

include_once './index.php';
