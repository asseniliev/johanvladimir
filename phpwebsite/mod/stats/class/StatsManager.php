<?php 

require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/StatsCounts.php');
require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/Stats.php');
require_once(PHPWS_SOURCE_DIR . 'mod/stats/class/StatsHits.php');

class PHPWS_StatsManager {
  var $_statView;
  var $_counters;
  var $_webstats;

  function PHPWS_StatsManager() {
    $this->_counters = new PHPWS_Stats_Counts();
    $this->_statView = new PHPWS_Stats();
    $this->_webstats = new PHPWS_Stats_Hits();
  }

  function menu() {
    $showBar = false;

    if($_SESSION["OBJ_user"]->allow_access("stats", "counter_settings") || 
       $_SESSION["OBJ_user"]->allow_access("stats", "add_counter")      || 
       $_SESSION["OBJ_user"]->allow_access("stats", "edit_counter")     ||
       $_SESSION["OBJ_user"]->allow_access("stats", "delete_counter")) {
      $tags["COUNTERS"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Counter Block"), "stats", array("stats[counters]"=>"setup"));
      $showBar = true;
    }

    if($_SESSION["OBJ_user"]->allow_access("stats", "webstats_settings")) {
      $tags["WEBSTATS"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Web Visits"), "stats", array("stats[webstats]"=>"view"));
      $showBar = true;
    }

    if($_SESSION["OBJ_user"]->isUser() && $showBar == true) {
      $tags["VIEW_STATS"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("View Stats"), "stats", array("stats[stats]"=>"view"));    
    }

    $tags["MAIN_TITLE"] = $this->caller("getTitle");
    $content = PHPWS_Template::processTemplate($tags, "stats", "menu.tpl");
    return $content;
  }

  function caller($funcName) {
    if(isset($_REQUEST["stats"])) {
      $statsCommand = $_REQUEST["stats"];
      
      foreach($statsCommand as $section=>$command) {
	
	if($section == "stats") {
	  $content = $this->_statView->action($command, TRUE);
	} else if($section == "counters") {
	  $content = $this->_counters->action($command, TRUE);
	} else if($section == "webstats") {
	  $content = $this->_webstats->action($command, TRUE);
	}
      }

      if(isset($content))
	return $content;
      else 
	return NULL;
    }}

  function action() {
    $content = "";

    if(!isset($GLOBALS["CNT_stats"]["content"]))
      $GLOBALS["CNT_stats"]["content"] = "";


    if(isset($_REQUEST["stats"])) {
      $statsCommand = $_REQUEST["stats"];

      foreach($statsCommand as $section=>$command) {
	
	if($section == "stats") {
	  $content .= $this->_statView->action($command);
	} else if($section == "counters") {
	  $content .= $this->_counters->action($command);
	} else if($section == "webstats") {
	  $content .= $this->_webstats->action($command);
	}

      }

    } 
    
    $GLOBALS["CNT_stats"]["content"] .= $content;
  }
}


?>