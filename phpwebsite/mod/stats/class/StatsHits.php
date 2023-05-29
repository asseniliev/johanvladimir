<?php

require_once(PHPWS_SOURCE_DIR . "mod/stats/class/StatsCommon.php");
require_once(PHPWS_SOURCE_DIR . "mod/stats/class/StatsBarGraph.php");

class PHPWS_Stats_Hits {
  var $_todaysHits = 0;
  var $_currMonthsHits = 0;
  var $_enableWebStats = 1;
  var $_site = NULL;
  var $_graph = NULL;
  var $_year  = NULL;
  var $_siteList = NULL;

  function PHPWS_Stats_Hits() {
    $this->init();
  }

  function init() {
    $hits = $GLOBALS["core"]->sqlSelect("mod_stats_today");
    $this->_todaysHits = count($hits);

    $sql = "SELECT SUM(hits), month, hits FROM 
           {$GLOBALS['core']->tbl_prefix}mod_stats_hit_history " . 
           "WHERE year=" . date("Y") . " AND month = " . date("m") . 
           " GROUP BY month";
    $GLOBALS["core"]->setFetchMode("assoc");

    if($result = $GLOBALS["core"]->query($sql)) {
      $row = $result->fetchRow();
      if($row["SUM(hits)"] > 0)
	$this->_currMonthsHits = $row["SUM(hits)"];    
      else 
	$this->_currMonthsHits = 0;
    } else {
      $this->_currMonthsHits = 0;
    }

    if($enable = $GLOBALS["core"]->sqlSelect("mod_stats_settings")) {
      $this->_enableWebStats     = $enable[0]["webstats_enable"];
    }
  }


  function getFreqUsers() {
    $sql = "select COUNT(username) as occurances, username FROM {$GLOBALS['core']->tbl_prefix}mod_stats_today group by username order by occurances DESC limit 10";
    
    $result = $GLOBALS["core"]->query($sql);
    $items  = false;
    
    if($result || $this->_showEmpty) {
      $users = array();      
      while($row = $result->fetchRow()) { 
	if($row["username"] != 'unknown') {
	  $items = true;
	  $users[$row["username"]] = $row["occurances"];
	}
      }
    }

    if($items == true)
      return $users;
    else
      return false;
  }

  function _view() { 
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "webstats_settings"))
      return;

    $this->init();
    $tags = array();

    if(!isset($_REQUEST["view_graph_fld"])) {
      $_REQUEST["view_graph_fld"] = "currMonth";
      $_REQUEST["graph_year_fld_YEAR"] = date('Y');
      $this->_graph = "currMonth";
    }
    
    $site_list = array($_SESSION["translate"]->it("Hub"));
	
    $form = new EZform("STATS_webstats");
    $form->setAction("index.php#viewgraph");
    $form->add("enable_webstats_fld", "checkbox");
    if($this->_enableWebStats == 1)
      $form->setMatch("enable_webstats_fld", TRUE);

    $form->add("module", "hidden", "stats");
    $form->add("stats[webstats]", "hidden", "updateSettings");
    $form->add("save_webstats_settings", "submit", $_SESSION["translate"]->it("Update"));

    $tags["ENABLE_WEBSTATS_LBL"] = $_SESSION["translate"]->it("Enable Web Stats");
    if($this->_enableWebStats) {
      $tags["HITS_LBL"] = $_SESSION["translate"]->it("Unique Visits");
      $tags["TODAYS_HITS_LBL"] = $_SESSION["translate"]->it("Today");
      $tags["TODAYS_HITS_FLD"] = $this->_todaysHits;
      $tags["CURR_MONTHS_HITS_LBL"] = $_SESSION["translate"]->it("This Month");
      $tags["CURR_MONTHS_HITS_FLD"] = $this->_currMonthsHits + $this->_todaysHits;
      
      if($freqUsers = $this->getFreqUsers()) {
	$tags["MOST_LOGIN_TITLE"]  = $_SESSION["translate"]->it("Top 10 Most Frequenty Logged In Users");
	$tags["USERNAME_TITLE"] = $_SESSION["translate"]->it("Username");
	$tags["HITS_TITLE"]     = $_SESSION["translate"]->it("Visits");
	
	$tags["USER_ROWS"] = "";
	foreach($freqUsers as $username=>$hits) {
	  $tags["USER_ROWS"] .= "<tr><td>$username</td><td align=\"left\">$hits</td></tr>";
	}
      }
      
      if(function_exists('imagecreate')) {
	$settings = $GLOBALS["core"]->sqlSelect('mod_stats_settings');

	$tags["VIEW_GRAPH_LBL"] = $_SESSION["translate"]->it("phpWebSite Charts for User Visits");
	$tags["GRAPH_YEAR_LBL"] = $_SESSION["translate"]->it("Year");
	$tags["GRAPH_MONTH_LBL"] = $_SESSION["translate"]->it("Month");
	
	if(isset($_REQUEST["site_fld"]))
	  $this->_site = $_REQUEST["site_fld"];
	
	if(isset($_REQUEST["view_graph_fld"]))
	  $this->_graph = $_REQUEST["view_graph_fld"];
	if(isset($_REQUEST["graph_year_fld_YEAR"])) {
	  if($_REQUEST["view_graph_fld"] == "currMonth")
	    $this->_year = $_REQUEST["graph_year_fld_YEAR"] = date('Y');
	  else
	    $this->_year  = $_REQUEST["graph_year_fld_YEAR"];
	}
	       
	$month_list = array("currMonth"=>$_SESSION["translate"]->it("Current Month"),
			    "1"=>$_SESSION["translate"]->it("January"),
			    "2"=>$_SESSION["translate"]->it("February"),
			    "3"=>$_SESSION["translate"]->it("March"),
			    "4"=>$_SESSION["translate"]->it("April"),
			    "5"=>$_SESSION["translate"]->it("May"),
			    "6"=>$_SESSION["translate"]->it("June"),
			    "7"=>$_SESSION["translate"]->it("July"),
			    "8"=>$_SESSION["translate"]->it("August"),
			    "9"=>$_SESSION["translate"]->it("September"),
			    "10"=>$_SESSION["translate"]->it("October"),
			    "11"=>$_SESSION["translate"]->it("November"),
			    "12"=>$_SESSION["translate"]->it("December"),
			    "last12Months"=>$_SESSION["translate"]->it("All Months"));

	$form->add("view_graph_fld", "select", $month_list);
	if(isset($this->_graph))
	  $form->setMatch("view_graph_fld", $this->_graph);
	
	$form->add("view_graph_btn", "submit", $_SESSION["translate"]->it("View"));

	if(isset($this->_year)) {
	  $form->dateForm("graph_year_fld", mktime(0,0,0,1,1,$this->_year), date("Y")-10);
	} else {
	  $form->dateForm("graph_year_fld", date("U"), date("Y")-10);
	}

	
	if($GLOBALS["core"]->moduleExists("branch") && $GLOBALS["core"]->isHub) {
	  
	  if($branches = $GLOBALS["core"]->sqlSelect("branch_sites")) {
	    if(!isset($this->_siteList)) {	    	    
	      if(isset($GLOBALS["IDhash"])) 
		$bkHash = $GLOBALS["IDhash"];
	      foreach($branches as $branchRow) {
		$GLOBALS["IDhash"] = $branchRow["IDhash"];
		$tmp = new PHPWS_Core($branchRow["branchName"], $GLOBALS["hub_dir"]);
		if($tmp->moduleExists("stats"))
		  $site_list[] = $branchRow["branchName"];
		$tmp = NULL;
	      }
	      if(isset($bkHash))
		$GLOBALS["IDhash"] = $bkHash;
	      else
		$GLOBALS["IDhash"] = NULL;
	      
	      $this->_siteList = $site_list;
	    } else {
	      $site_list = $this->_siteList;
	    }
	    
	    if(count($site_list) > 1) {
	      $form->add("site_fld", "select", $site_list);
	      if(isset($this->_site)) 
		$form->setMatch("site_fld", $this->_site);
	      
	      $tags["SITE_LABEL"] = $_SESSION["translate"]->it("Site");
	    }
	  }		  	 
	  
	  if(!empty($GLOBALS["core"]->branch)) {
	    $branch = $GLOBALS["core"]->branch["branchName"];
	    $hash   = $GLOBALS["core"]->branch["IDhash"];
	  } else if(isset($_REQUEST["site_fld"]) && 
		    $_REQUEST["site_fld"] != 0) {
	    if($result = $GLOBALS["core"]->sqlSelect("branch_sites", "branchName", $site_list[$_REQUEST["site_fld"]])) {
	      $branch = $result[0]["branchName"];
	      $hash   = $result[0]["IDhash"];
	    }
	  }
	}
	  
	  if(isset($_REQUEST["view_graph_fld"]) && 
	   ($_REQUEST["view_graph_fld"] == "currMonth")) {
	    $tags["CURR_MONTH_BAR_GRAPH"] = "<img src=\"http://".$GLOBALS["core"]->source_http."mod/stats/graphs/Month.php?graph_month=".date('m')."&amp;graph_year=".$_REQUEST["graph_year_fld_YEAR"]."&amp;graph_md5=".$settings[0]['graphs_md5'];
	    
	    if(isset($branch)) {
	      $tags["CURR_MONTH_BAR_GRAPH"] .= "&amp;branch=$branch&amp;hash=$hash";
	    }
	
	    $tags["CURR_MONTH_BAR_GRAPH"] .= "\" />";
	    $tags['CURR_MONTH_TITLE'] = $_SESSION['translate']->it('Unique visits for the month of ') . ucfirst(strftime('%B')) . ' ' . $_REQUEST['graph_year_fld_YEAR'];
	  }

	  if(isset($_REQUEST["view_graph_fld"]) &&
	     is_numeric($_REQUEST["view_graph_fld"])) {
	    
	    $tags["CURR_MONTH_BAR_GRAPH"] = "<img src=\"http://".$GLOBALS["core"]->source_http."mod/stats/graphs/Month.php?graph_month=".$_REQUEST["view_graph_fld"]."&amp;graph_year=".$_REQUEST["graph_year_fld_YEAR"]."&amp;graph_md5=".$settings[0]['graphs_md5'];
	    if(isset($branch)) {
	      $tags["CURR_MONTH_BAR_GRAPH"] .= "&amp;branch=$branch&amp;hash=$hash";
	    }
	    $tags["CURR_MONTH_BAR_GRAPH"] .= "\" />";
	    $tags['CURR_MONTH_TITLE'] = $_SESSION['translate']->it('Unique visits for the month of ') . $_SESSION['translate']->it($month_list[$_REQUEST['view_graph_fld']]) . ' ' . $_REQUEST['graph_year_fld_YEAR'];
	  }

	  if(isset($_REQUEST["view_graph_fld"]) && 
	   ($_REQUEST["view_graph_fld"] == "last12Months")) {
	    $tags["ALL_MONTHS_BAR_GRAPH"] = "<img src=\"http://".$GLOBALS["core"]->source_http."/mod/stats/graphs/AllMonths.php?year=".$_REQUEST["graph_year_fld_YEAR"]."&amp;graph_md5=".$settings[0]["graphs_md5"];
	    if(isset($branch)) {
	      $tags["ALL_MONTHS_BAR_GRAPH"] .= "&amp;branch=$branch&amp;hash=$hash";
	    }
	    
	    $tags['ALL_MONTHS_BAR_GRAPH'] .= "\" />";
	    $tags['ALL_MONTHS_TITLE'] = $_SESSION['translate']->it('Unique visits for the year ') . $_REQUEST['graph_year_fld_YEAR'];
	  }
	  
      } else {
	$tags["GD_MSG"] = $_SESSION["translate"]->it("To enable support for graphics, please compile PHP with the GD libraries.");
      }
      
    }

    $tags = $form->getTemplate(TRUE, TRUE, $tags);
    
    return PHPWS_Template::processTemplate($tags, "stats", "webstats/view.tpl");
  }
  
  function _updateSettings() {
    if(isset($_REQUEST["enable_webstats_fld"])) {
      $update["webstats_enable"] = 1;
    } else {
      $update["webstats_enable"] = 0;
    }

    $this->_enableWebStats     = $update["webstats_enable"];

    if($GLOBALS["core"]->sqlUpdate($update, "mod_stats_settings")) {
      return PHPWS_Stats_Common::getMsg($_SESSION["translate"]->it("Update was successful."));
    } else {
      return PHPWS_Stats_Common::getMsg($_SESSION["translate"]->it("Update was unsuccessful."));
    }
  }

  function registerHit() {
    $hit["ip"] = $_SERVER['REMOTE_ADDR'];
    if($_SESSION["OBJ_user"]->isUser()) {
      $hit["username"] = $_SESSION["OBJ_user"]->getUsername();
    } else {
      $hit["username"] = "unknown";
    }
    
    $hit["date"] = date("m-d-Y");

    // check if another day
    if($result = $GLOBALS["core"]->sqlSelect("mod_stats_today")) {
      if($result[0]["date"] != $hit["date"]) {
	$hitsToday = $GLOBALS["core"]->query("SELECT COUNT(*), date FROM ".$GLOBALS["core"]->tbl_prefix."mod_stats_today GROUP BY date");

	if(!DB::isError($hitsToday)) { 
	  while($row = $hitsToday->fetchRow()) {
	    $date = explode("-", $row["date"]);

	    $num["month"] = (int)$date[0];
	    $num["day"]   = (int)$date[1];
	    $num["year"]  = (int)$date[2];

	    // check to make sure date hasn't been recorded
	    if(!$result = $GLOBALS["core"]->sqlSelect("mod_stats_hit_history", $num)) {	  	    	    
	      $num["hits"] = (int) $row['COUNT(*)'];
	      $GLOBALS["core"]->sqlInsert($num, "mod_stats_hit_history");
	    } else {
	      $num["hits"] = $result[0]["hits"] + (int) $row['COUNT(*)'];
	      $GLOBALS["core"]->sqlUpdate($num, "mod_stats_hit_history","id",$result[0]["id"]);
	    }
	  }

	  $GLOBALS["core"]->sqlDelete("mod_stats_today", "date", $hit["date"], "!=");
	}
      } 
    }

    // register new hit
    $result = $GLOBALS["core"]->sqlSelect("mod_stats_today", "ip", $hit["ip"]);
    if(!$result) {
      // new
      $GLOBALS["core"]->sqlInsert($hit, "mod_stats_today");
    } else {
      // repeat visitor - but logged in now
      if($result[0]["username"] != $hit["username"] && $hit["username"] != "unknown")
	$GLOBALS["core"]->sqlUpdate($hit, "mod_stats_today", "ip", $result[0]["ip"]);

      if(!isset($_SESSION["STATS_REG_HIT"])) {
	$GLOBALS["core"]->sqlInsert($hit, "mod_stats_today");	
      }
    }
  }

  function action($command, $getTitle=FALSE) {
    switch($command) {
    case "view":
      if($getTitle)
	return $_SESSION["translate"]->it("Unique Visits");
      else
	return $this->_view();
      break;
    case "updateSettings":
      if($getTitle)
	return $_SESSION["translate"]->it("Web Stats");
      else {
	if(!isset($_REQUEST["view_graph_btn"]))
	  return $this->_updateSettings() . $this->_view();
	else 
	  return $this->_view();
      }
      break;
    }
  }
}

?>