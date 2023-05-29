<?php

/**
 * This is a skeleton boost.php configuration file.  Edit it to be
 * used with your module.
 *
 * $Id: boost.php,v 1.8 2004/12/08 21:39:20 darren Exp $
 */

/* The version of your module. Make sure to increment this on updates */
$version = '1.0.2';

/* The unix style name for your module */
$mod_title = 'skeleton';

/* The proper name for your module to be shown to users */
$mod_pname = 'Skeleton Module';

/* The modules you wish to allow your module to be viewed on */
$allow_view = array('skeleton'=>1);

/* The priority of your module when being loaded.  Leave at 50 if you're unsure */
$priority = 50;

/* Whether or not your module is active when it is initially installed */
$active = 'on';

/* An array of class files used by your module */
$mod_class_files = array('SkeletonManager.php');

/* NOTE: The following variables are soon to be depreciated. */
$mod_directory = 'skeleton';
$mod_filename = 'index.php';
$admin_mod = TRUE;

?>
