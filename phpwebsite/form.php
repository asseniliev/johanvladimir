<?php

$_REQUEST['module'] = $_GET['module'] = 'phatform';

if (!isset($_GET['PHAT_MAN_OP']) && !isset($_GET['PHAT_MAN_OP'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$_REQUEST['PHAT_MAN_OP'] = $_GET['PHAT_MAN_OP'] = 'view';
	$_REQUEST['PHPWS_MAN_ITEMS'][] = $_GET['PHPWS_MAN_ITEMS'][] = $_GET['id'];
    } else {
	$_REQUEST['PHAT_MAN_OP'] = $_GET['PHAT_MAN_OP'] = 'list';
    }
}

include_once './index.php';
