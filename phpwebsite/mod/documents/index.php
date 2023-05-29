<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: index.php,v 1.3 2005/05/18 21:07:45 darren Exp $
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../../?module=documents&JAS_DocumentManager_op=list");
  exit();
}

include_once(PHPWS_SOURCE_DIR.'mod/documents/conf/config.php');

$CNT_documents['title'] = $_SESSION['translate']->it('Documents');
$CNT_documents['content'] = NULL;

if(!isset($_SESSION['JAS_DocumentManager'])) {
  $_SESSION['JAS_DocumentManager'] = new JAS_DocumentManager;
}

if(isset($_REQUEST['JAS_DocumentManager_op']) && isset($_SESSION['JAS_DocumentManager'])) {
  $_SESSION['JAS_DocumentManager']->action();
}

if(isset($_REQUEST['JAS_Document_op']) && isset($_SESSION['JAS_DocumentManager']->document)) {
  $_SESSION['JAS_DocumentManager']->document->action();
}

if(isset($_REQUEST['JAS_Files_op']) && isset($_SESSION['JAS_DocumentManager']->document->files)) {
  $_SESSION['JAS_DocumentManager']->document->files->action();
}

//$_SESSION['JAS_DocumentManager']->block();

?>