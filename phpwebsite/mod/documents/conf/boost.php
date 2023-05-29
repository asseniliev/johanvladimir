<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: boost.php,v 1.19 2005/05/23 12:53:22 darren Exp $
 */

include_once(PHPWS_SOURCE_DIR.'mod/documents/conf/config.php');

$mod_title = "documents";
$mod_pname = 'Documents';
$mod_directory = "documents";
$mod_filename = "index.php";
$mod_class_files = array("DocumentManager.php");
$allow_view = "all";
$admin_mod = 1;
$priority = 50;
$active = "on";
$version = "2.2.10";

?>