<?php

$_REQUEST['module'] = $_GET['module'] = 'pagemaster';

if (!isset($_GET['PAGE_user_op'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$_REQUEST['PAGE_user_op'] = $_GET['PAGE_user_op'] = 'view_page';
	$_REQUEST['PAGE_id'] = $_GET['PAGE_id'] = $_GET['id'];
    } else {
	unset($_REQUEST['module']);
	unset($_GET['module']);
	$GLOBALS['module'] = 'home';
    }
}

include_once './index.php';
