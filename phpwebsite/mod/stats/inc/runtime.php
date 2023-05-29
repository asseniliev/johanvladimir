<?php
require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/StatsManager.php');
require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/StatsCounts.php');
require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/StatsHits.php');
require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/Stats.php');

if($statContent = PHPWS_Stats_Counts::show_counts()) {
  $GLOBALS["CNT_stats_block"]["title"] = $_SESSION["translate"]->it("Stats");
  $GLOBALS["CNT_stats_block"]["content"] = $statContent;
}

PHPWS_Stats_Hits::registerHit();
$_SESSION["STATS_REG_HIT"] = TRUE;

if(PHPWS_Stats::isTrackingPMHits() && PHPWS_Stats::isPMHit()) {
  PHPWS_Stats::addPMHit();
}

?>