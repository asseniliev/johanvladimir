<?php
/**
 * Stats boost configuration.
 *
 */

$mod_title = "stats";

$mod_pname = "Admin Stats";

$allow_view = "all";

$priority = 50;

$active = "on";

$mod_class_files = array("StatsManager.php", "Stats.php", "StatsCounts.php", "StatsHits.php");

$mod_directory = "stats";
$mod_filename = "index.php";
$admin_mod = TRUE;
$version = "0.1.11";

?>