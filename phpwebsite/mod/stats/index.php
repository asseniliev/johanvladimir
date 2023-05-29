<?php
/**
 * Stats Module
 *
 */
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

if(!isset($_SESSION["SES_STATS_MANAGER"])) {
  $_SESSION["SES_STATS_MANAGER"] = new PHPWS_StatsManager();
}

$GLOBALS["CNT_stats"]["content"] = $_SESSION["SES_STATS_MANAGER"]->menu();
$_SESSION["SES_STATS_MANAGER"]->action();

?>