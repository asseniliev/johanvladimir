<?php

$_REQUEST['module'] = $_GET['module'] = 'announce';

if (!isset($_GET['ANN_op']) && !isset($_GET['ANN_user_op'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$_REQUEST['ANN_user_op'] = $_GET['ANN_user_op'] = 'view';
	$_REQUEST['ANN_id'] = $_GET['ANN_id'] = $_GET['id'];	
    } else {
	$_REQUEST['ANN_user_op'] = $_GET['ANN_user_op'] = 'categories';
    }
}

include_once './index.php';
