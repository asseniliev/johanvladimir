<?php

/**
 * This is a skeleton index file.  Edit it to be used with your module.
 *
 * $Id: index.php,v 1.8 2004/11/05 16:27:59 steven Exp $
 */

/* Make sure core is set before executing otherwise it means someone is trying
   to access the module directory directly */
if (!isset($GLOBALS['core'])){
    header('location:../../');
    exit();
}

$GLOBALS['CNT_skeleton'] = array('title'   => 'Skeleton Module',
				 'content' => null);

if (!isset($_SESSION['PHPWS_SkeletonManager'])) {
    $_SESSION['PHPWS_SkeletonManager'] = new PHPWS_SkeletonManager;
}

$_SESSION['PHPWS_SkeletonManager']->action();

?>
