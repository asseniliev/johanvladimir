<?php


$GLOBALS["hub_dir"] = "../../../";

define("PHPWS_SOURCE_DIR", $GLOBALS["hub_dir"]);
require_once($GLOBALS["hub_dir"] . "core/Core.php");
session_start();

$branch = NULL;
if(isset($_REQUEST["branch"])) {
  $branch = $_REQUEST["branch"];
  if(isset($_REQUEST["hash"]))
    $GLOBALS["IDhash"] = $_REQUEST["hash"];
}

$_SESSION["core"] = new PHPWS_Core($branch, $GLOBALS["hub_dir"]);
$GLOBALS["core"] = &$_SESSION["core"];

require_once(PHPWS_SOURCE_DIR . 'mod/stats/conf/stats.php');

$settings = $GLOBALS["core"]->sqlSelect("mod_stats_settings");
if(!isset($_REQUEST["graph_md5"]) || 
   $_REQUEST["graph_md5"] != $settings[0]["graphs_md5"]) {
    echo LNG_ERROR_KEY;
    exit(1);  
}

require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/StatsBarGraph.php');
$barGraph = new PHPWS_Stats_Bar_Graph(630, 270);

if(isset($_REQUEST["graph_month"])) {
  $month = $_REQUEST["graph_month"];
} else { 
  $month = date("m");
}

if(isset($_REQUEST["graph_year"])) {
  $year = $_REQUEST["graph_year"];
} else {
  $year  = date("Y");
}

if(!@include(PHPWS_SOURCE_DIR . 'lib/pear/Calendar/Engine/PearDate.php')) {
    if(!@include('Calendar/Engine/PearDate.php')) {
	echo LNG_ERROR_PEAR;
	exit(1);
    }
}

$daysInMonth = Calendar_Engine_PearDate::getDaysInMonth($year, $month);

$data = array();
for($i=1; $i < $daysInMonth + 1; $i++) {
  $data[$i] = 0;
}

$options["month"] = $month;
$options["year"]  = $year;

$results = $GLOBALS["core"]->sqlSelect("mod_stats_hit_history", $options, NULL, "day");

if(isset($results)) {
  foreach($results as $hits) {
    $data[$hits["day"]] = $hits["hits"];
  }
}

$barGraph->setXLabel(LNG_X_AXIS_MONTH);
$barGraph->setData($data);
$barGraph->setTitleFont(3);
$barGraph->draw();

?>