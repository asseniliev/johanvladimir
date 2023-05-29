<?php

$_REQUEST['module'] = $_GET['module'] = 'documents';

if (!isset($_GET['JAS_DocumentManager_op']) && !isset($_GET['JAS_Document_op'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$_REQUEST['JAS_DocumentManager_op'] = $_GET['JAS_DocumentManager_op'] = 'downloadFile';
	$_REQUEST['JAS_File_id'] = $_GET['JAS_File_id'] = $_GET['id'];
    } else {
	$_REQUEST['JAS_DocumentManager_op'] = $_GET['JAS_DocumentManager_op'] = 'categories';
    }
}

include_once './index.php';
