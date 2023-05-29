<?php

/**
* View bar graph for web visits for each month of the year.
*/

$GLOBALS["hub_dir"] = "../../../";

define("PHPWS_SOURCE_DIR", $GLOBALS["hub_dir"]);
require_once($GLOBALS["hub_dir"] . "core/Core.php");
require_once(PHPWS_SOURCE_DIR . "core/Array.php");

session_start();
$branch = NULL;
if(isset($_REQUEST["branch"])) {
  $branch = $_REQUEST["branch"];
  if(isset($_REQUEST["hash"]))
    $GLOBALS["IDhash"] = $_REQUEST["hash"];
}

$_SESSION["core"] = new PHPWS_Core($branch, $GLOBALS["hub_dir"]);
$GLOBALS["core"] = &$_SESSION["core"];

require_once(PHPWS_SOURCE_DIR . "mod/stats/conf/stats.php");

define('JAN', LNG_JAN);
define('FEB', LNG_FEB);
define('MAR', LNG_MAR);
define('APR', LNG_APR);
define('MAY', LNG_MAY);
define('JUN', LNG_JUN);
define('JUL', LNG_JUL);
define('AUG', LNG_AUG);
define('SEP', LNG_SEP);
define('OCT', LNG_OCT);
define('NOV', LNG_NOV);
define('DEC', LNG_DEC);

$settings = $GLOBALS["core"]->sqlSelect("mod_stats_settings");
if(!isset($_REQUEST["graph_md5"]) ||
   $_REQUEST["graph_md5"] != $settings[0]["graphs_md5"]) {
    echo LNG_ERROR_KEY;
    exit(1);
}

require_once(PHPWS_SOURCE_DIR . "mod/stats/class/StatsBarGraph.php");

$barGraph = new PHPWS_Stats_Bar_Graph(600, 270);

$data = array(JAN => 0, 
	      FEB => 0, 
	      MAR => 0, 
	      APR => 0, 
	      MAY => 0, 
	      JUN => 0, 
	      JUL => 0, 
	      AUG => 0, 
	      SEP => 0, 
	      OCT => 0, 
	      NOV => 0, 
	      DEC => 0); 

if(isset($_REQUEST["year"]))
   $year = $_REQUEST["year"];
else
   $year  = date("Y");

$sql = "SELECT SUM(hits), month, hits FROM {$GLOBALS['core']->tbl_prefix}mod_stats_hit_history WHERE year="
       .$year . " GROUP BY month";
$GLOBALS["core"]->setFetchMode("assoc");
$result = $GLOBALS["core"]->query($sql);

while($row = $result->fetchRow()) {
  switch($row["month"]) {
  case 1:
    $data[JAN] = $row["SUM(hits)"];  
    break;
  case 2:
    $data[FEB] = $row["SUM(hits)"];
    break;
  case 3:
    $data[MAR] = $row["SUM(hits)"];
    break;
  case 4:
    $data[APR] = $row["SUM(hits)"];
    break;
  case 5:
    $data[MAY] = $row["SUM(hits)"];
    break;
  case 6:
    $data[JUN] = $row["SUM(hits)"];
    break;
  case 7:
    $data[JUL] = $row["SUM(hits)"];
    break;
  case 8:
    $data[AUG] = $row["SUM(hits)"];
    break;
  case 9:
    $data[SEP] = $row["SUM(hits)"];
    break;
  case 10:
    $data[OCT] = $row["SUM(hits)"];
    break;
  case 11:
    $data[NOV] = $row["SUM(hits)"];
    break;
  case 12:
    $data[DEC] = $row["SUM(hits)"];
    break;
  }
}

$barGraph->setXLabel(LNG_X_AXIS_YEAR);
$barGraph->setData($data);
$barGraph->setTitleFont(3);
$barGraph->draw();

?>