<?php

/**
 * Routing file for phpWebSite
 *
 * Index initializes the core and database
 *
 * @version $Id: index.php,v 1.80 2005/03/02 20:59:20 matt Exp $
 * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
 * @modified by Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @modified by Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package phpWebSite
 */

/* Show all errors */
//error_reporting (E_ALL);

// Change to TRUE to allow DEBUG mode
define('DEBUG_MODE', FALSE);

$GLOBALS['ALWAYS'] = array('layout', 'users', 'language', 'fatcat', 'search', 'menuman', 'comments');


if (!isset($hub_dir)) {
    $hub_dir = NULL;
}

/* Check to make sure $hub_dir is not set to an address */
if (!preg_match ("/:\/\//i", $hub_dir)) {
    loadConfig($hub_dir);
} else {
    exit('FATAL ERROR! Hub directory was malformed.');
}
require_once PHPWS_SOURCE_DIR . 'security.php';

if (file_exists(PHPWS_SOURCE_DIR . 'core/Core.php') && file_exists(PHPWS_SOURCE_DIR . 'core/Debug.php')) {
    require_once PHPWS_SOURCE_DIR . 'core/Core.php';
   
    if(DEBUG_MODE) {
	require_once 'Benchmark/Timer.php';
	$PHPWS_Timer =& new Benchmark_Timer();
	$PHPWS_Timer->start();
	$PHPWS_Timer->setMarker('Begin Core Initialization');
    }
} else {
    exit('FATAL ERROR! Required file <b>Core.php</b> not found.');
}

if (!isset($branchName)) {
    $branchName = NULL;
}

$GLOBALS['core'] =& new PHPWS_Core($branchName, $hub_dir);

if (DEBUG_MODE) {
    $PHPWS_Timer->setMarker('End Core Initialization');
}

$includeList = $core->initModules();

if (DEBUG_MODE) {
    /* phpWebSite debugger */
    if (!isset($_SESSION['PHPWS_Debug'])) {
	$_SESSION['PHPWS_Debug'] =& new PHPWS_Debug();
    }

    if ($_SESSION['PHPWS_Debug']->isActive()) {
	$_SESSION['PHPWS_Debug']->displayDebugInfo(TRUE);
	
	if ($_SESSION['PHPWS_Debug']->getBeforeExecution()) {
	    $_SESSION['PHPWS_Debug']->displayDebugInfo(FALSE);
	}
    }
}

$current_mod_file = NULL;

foreach ($includeList as $mod_title=>$current_mod_file) {
    if (in_array($mod_title, $GLOBALS['ALWAYS']) || (isset($_REQUEST['module']) && ($_REQUEST['module'] == $mod_title))) {	
	if (DEBUG_MODE) {
	    $PHPWS_Timer->setMarker("Begin $mod_title Execution");
	}
    
	$core->current_mod = $mod_title;
	
	if (is_file($current_mod_file)) {
	    include_once($current_mod_file);
	}
	
	if (DEBUG_MODE) {
	    $PHPWS_Timer->setMarker("End $mod_title Execution");
	}
    }
    
    if (is_file(PHPWS_SOURCE_DIR . "mod/$mod_title/inc/runtime.php")) {
	include PHPWS_SOURCE_DIR . "mod/$mod_title/inc/runtime.php";
    }
}

/* Preventing last mod loaded from being 'current_mod' */
$core->current_mod = NULL;
$core->db->disconnect();

if (DEBUG_MODE) {
    $PHPWS_Timer->stop();
    
    /* phpWebSite debugger */
    if ($_SESSION['PHPWS_Debug']->isActive()) {
	if ($_SESSION['PHPWS_Debug']->getShowTimer()) {
	    echo '<br /><font size="+1">phpWebSite Timer</font><br />';
	    $PHPWS_Timer->display();
	    echo '<br />';
	}
	if ($_SESSION['PHPWS_Debug']->getAfterExecution()) {
	    $_SESSION['PHPWS_Debug']->displayDebugInfo(FALSE);
	}
    }
}

/* Loads the hubs config file and sets the source directory */
function loadConfig($hub_dir){
    if (file_exists($hub_dir . 'conf/config.php')) {
	if (filesize($hub_dir . 'conf/config.php') > 0) {
	    include($hub_dir . 'conf/config.php');
	    define('PHPWS_SOURCE_DIR', $source_dir);
	} else {
	    header('Location: ./setup/set_config.php');
	    exit();
	}
    } else {
	header('Location: ./setup/set_config.php');
	exit();
    }  
}


/* Uncomment to see memory usage */
//echo round((memory_get_usage()/1024)/1024, 2) . 'MB';

?>
