<?php

require_once PHPWS_SOURCE_DIR .'mod/scheduler/class/Entry.php';

class PHPWS_Scheduler {
    
    var $_ts        = null;
    var $_tsSmall   = null;
    var $_entry     = null;
    var $_schedule  = null;
    var $_schedules = null;
    var $_conflicts  = array();
    var $_title     = 'schedule entry';
    var $error      = array();

    function PHPWS_Scheduler() {
	$this->_ts       = time();
	$this->_tsSmall  = time();
	
	$this->_schedules    = array();
	$this->_schedules[0] = "Global";
	
	if ($_SESSION['OBJ_user']->isDeity()) {
	    $where = "(deity='1' OR admin_switch='1')";
	} else {
	    $where = "(deity='0' AND admin_switch='1')";
	}

	$sql    = "SELECT user_id, username FROM {$GLOBALS['core']->tbl_prefix}mod_users WHERE $where";
	$result = $GLOBALS['core']->getAll($sql);
	
	if (is_array($result) && (sizeof($result) > 0)) {
	    foreach ($result as $row) {
		$this->_schedules[$row['user_id']] = $row['username'];
	    }
	}
	
	$this->_schedule = $_SESSION['OBJ_user']->user_id;
	
	if (!array_key_exists($this->_schedule, $this->_schedules)) {
	    $this->_schedule = 0;
	}
    }

    function set($name, $value) {
	$var = "_{$name}";
	$this->$var = $value;
    }
    
    function get($name) {
	$var = "_{$name}";
	return $this->$var;
    }
    
    function getMonth() {
	require_once 'Calendar/Month/Weekdays.php';
	require_once 'Calendar/Day.php';
	
	$Month =& new Calendar_Month_Weekdays(date("Y", $this->_ts), date("n", $this->_ts));
	
	$start = $Month->thisMonth(true);
	$end = $Month->nextMonth(true) - 1;
	
	$sql = "SELECT id, start, end, label FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries 
                WHERE ((start>='{$start}' AND start<='{$end}') OR (end>='{$start}' AND end<='{$end}')) 
                AND (user='{$this->_schedule}' OR administrative='1' OR global='1') 
                ORDER BY start ASC";
	$result = $GLOBALS['core']->getAll($sql);
	
	$selection = array();
	if (is_array($result) && (sizeof($result) > 0)) {
	     foreach ($result as $row) {
		 $start = $row['start'];
		 do {
		     $Day =& new Calendar_Day(date('Y', $row['start']), date('n', $row['start']), date('j', $row['start']));
		     $ts = $Day->getTimeStamp();
		     

		     $time = date('g:i A', $row['start']);

		     $label = "{$time}<br />". $row['label'];
		     
		     if (array_key_exists($ts, $selection)) {
			 $selection[$ts]->add($label);
		     } else {
			 $PHPWS_Scheduler_Day =& new PHPWS_Scheduler_Day($Day);
			 $PHPWS_Scheduler_Day->add($label);
			 $selection[$ts] = $PHPWS_Scheduler_Day;
		     }

		     $row['start'] = $ts + (3600*24);
		 } while ($row['start'] <= $row['end']);
	     }
	}
	
	$Month->build($selection);
	
	$tags    = array();
	$content = array();

	$tags = $this->getForm("month");
	
	if (isset($this->_conflicts[$this->_schedule])
	    && is_array($this->_conflicts[$this->_schedule])
	    && (sizeof($this->_conflicts[$this->_schedule]) > 0)) {
	    $tags['CONFLICTS'] = array();
	    $tags['CONFLICTS'][] = '<div class="error">The last repeat entry you added had conflicts on these days; please reschedule them individually.</div>';
	    $tags['CONFLICTS'][] = '<ul style="margin-left: 1em;">';	    
	    foreach ($this->_conflicts[$this->_schedule] as $value) {
		$date = date('m-d-Y h:i A', $value);
		$tags['CONFLICTS'][] = '<li>'. implode("&#160;", array(date("g:i a", $value), date("l, F d", $value))) ." : <a href=\"./index.php?module=scheduler&amp;op=editEntry&amp;time={$value}\">Add Entry</a></li>";
	    }
	    $tags['CONFLICTS'][] = '</ul>';
	    $tags['CONFLICTS'] = implode("\n", $tags['CONFLICTS']);
	}

	$tags['TODAY'] = date("l F d, Y", time());
	
	$prev_month = $Month->prevMonth(true);
	$next_month = $Month->nextMonth(true);

	$tags['PREV_SMALL'] = $this->getSmallMonth($prev_month);
	$tags['NEXT_SMALL'] = $this->getSmallMonth($next_month);

	$tags['PREV_MONTH'] = $prev_month;
	$tags['NEXT_MONTH'] = $next_month;
	
	$tags['DATE'] = date("F Y", $Month->getTimeStamp());
	
	while ($Day =& $Month->fetch()) {
	    if ($Day->isFirst()) {
		$content[] = "<tr>";
	    }
    
	    if ($Day->isEmpty()) {
		$content[] = "<td class=\"no-shade\">&#160;</td>";
	    } else {
		$timestamp = $Day->getTimeStamp();
		$date = date("l F d, Y", $timestamp);

		$js = implode(" ", array("onClick=\"viewEntry('{$timestamp}');\"",
					 "onMouseOver=\"this.className='shade cursor'; window.status='View {$date}';\"",
					 "onMouseOut=\"this.className='no-shade'; window.status='';\"",
					 "onMouseDown=\"this.className='click';\""));
		
		$content[] = "<td width=\"14%\" height=\"120\" valign=\"top\" class=\"no-shade\" title=\"View {$date}\" {$js}>";
		
		$js = implode(" ", array("onMouseOver=\"window.status='View {$date}'; return true;\"",
					 "onMouseOut=\"window.status='';\""));
		
		$content[] = "<div align=\"right\">". $Day->thisDay() ."</div><br />";
		
		if ($Day->isSelected()) {
		    $entries = $Day->getEntries();
		    $list = array();
		    foreach ($entries as $value) {
			$list[] = "<li>{$value}</li>\n";
		    }
		    
		    $content[] = implode("\n", array("<ul>",
						     implode("\n", $list),
						     "</ul>"));
		    
		}
		
		$content[] = "</td>";
	    }
	    
	    if ($Day->isLast()) {
		$content[] = "</tr>";
	    }
	}
	
	$tags['ROWS'] = implode("\n", $content);

	$js = "
function viewEntry(ts) {
   url = './index.php?module=scheduler&op=days&ts=' + ts;
   location.href = url;
}
";

	$_SESSION['OBJ_layout']->addJavascript($js);
	
	return PHPWS_Template::processTemplate($tags, "scheduler", "month.tpl");
    }
    
    function getDays($print=false) {
	require_once 'Calendar/Day.php';
	require_once 'Calendar/Minute.php';
	
	$time = time();
	
	$Day =& new Calendar_Day(date('Y', $this->_ts), date('n', $this->_ts), date('j', $this->_ts));
	$Day->build();
	
	$tags    = array();
	$content = array();

	if (!$print) {
	    $tags = $this->getForm("days");
	
	    $tags['TODAY']       = date("l F d, Y", $time);
	
	    $tags['SMALL_MONTH'] = $this->getSmallMonth();
	}
	
	if (isset($this->_conflicts[$this->_schedule])
	    && is_array($this->_conflicts[$this->_schedule])
	    && (sizeof($this->_conflicts[$this->_schedule]) > 0)) {
	    $tags['CONFLICTS'] = array();
	    $tags['CONFLICTS'][] = '<div class="error">The last repeat entry you added had conflicts on these days; please reschedule them individually.</div>';
	    $tags['CONFLICTS'][] = '<ul style="margin-left: 1em;">';	    
	    foreach ($this->_conflicts[$this->_schedule] as $value) {
		$date = date('m-d-Y h:i A', $value);
		$tags['CONFLICTS'][] = '<li>'. implode("&#160;", array(date("g:i a", $value), date("l, F d", $value))) ." : <a href=\"./index.php?module=scheduler&amp;op=editEntry&amp;time={$value}\">Add Entry</a></li>";
	    }
	    $tags['CONFLICTS'][] = '</ul>';
	    $tags['CONFLICTS'] = implode("\n", $tags['CONFLICTS']);
	}

	$tags['PREV_DAY']    = $Day->prevDay(true) - (3600 * 24);
	$tags['DATE1']       = date("l F d, Y", $Day->getTimeStamp());
	
	$tags['DAY1']        = $this->getDay($Day);
	
	$nextDay = $Day->nextDay(true);
	$Day->setTimeStamp($nextDay);
	$Day->build();

	$tags['NEXT_DAY'] = $Day->nextDay(true);
	$tags['DATE2'] = date("l F d, Y", $Day->getTimeStamp());
	
	$tags['DAY2'] = $this->getDay($Day);

	$js = "
function addEntry(ts) {
    url = './index.php?module=scheduler&op=editEntry&time=' + ts;
    location.href = url;
}

function editEntry(id) {
    url = './index.php?module=scheduler&op=editEntry&id=' + id;
    location.href = url;
}

function cancelEntry(id) {
    if (confirm('Are you sure you want to cancel this {$this->_title}?\\r\\nClick \'Ok\' to cancel.')) {
        url = './index.php?module=scheduler&op=cancelEntry&id=' + id;
        location.href = url;
    }
}
";

	$_SESSION['OBJ_layout']->addJavascript($js);
	
	return PHPWS_Template::processTemplate($tags, "scheduler", "days.tpl");
    }
    
    function getDay(&$Day) { 
	$start = $Day->thisDay(true);
	$end = ($start + (3600 * 24) - 1);

	$PHPWS_Scheduler_Day =& new PHPWS_Scheduler_Day($Day);
	
	$sql = "SELECT id, start, end, label, administrative, global FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries 
                WHERE ((start>='{$start}' AND start<='{$end}') OR (end>='{$start}' AND end<='{$end}') OR (start<='{$start}' AND end>='{$end}'))
                AND (user='{$this->_schedule}' OR administrative='1' OR global='1') 
                ORDER BY start ASC";
	$result = $GLOBALS['core']->getAll($sql);

	$temp = array();

	$selection = array();
	$ts = $PHPWS_Scheduler_Day->getTimeStamp();

	$duplicates = 0;
	$sims = array();
	if (is_array($result) && (sizeof($result) > 0)) {
	    foreach ($result as $row) {
		$start = $row['start'];

		if ($start <= ($ts + (3600 * 8))) {
		    $start = $ts + (3600 * 8);
		}

		$hour = (int)date('G', $start);
		$minute = date('i', $start);
		$quarter = floor($minute / 15);
		$new_minute = $quarter * 15;

		$new_start = mktime($hour, $new_minute, 0);
		$diff = $minute % 15;
		
		if ($diff) {
		    $start = $start - ($diff * 60);
		}

		if (!isset($sims[$new_start])) {
		  $sims[$new_start] = TRUE;
		} else {
		  $duplicates++;
		}
		
		$temp[] = array("start"=>$start, "end"=>$row['end']);
		
		$Minute =& new Calendar_Minute(1970, 1, 1, 12, 0);
		$Minute->setTimeStamp($start);

		$label = $row['label'];
		
		if (isset($row['administrative']) || isset($row['global'])) {
		    $admin = true;
		} else {
		    $admin = false;
		}
		$start = $new_start;
		
		if (array_key_exists($start, $selection)) {
		    $selection[$start]->add($row['id'], $row['start'], $row['end'], $label, $admin);
		} else {
		    $PHPWS_Scheduler_Minute =& new PHPWS_Scheduler_Minute($Minute);
		    $PHPWS_Scheduler_Minute->add($row['id'], $row['start'], $row['end'], $label, $admin);
		    $selection[$start] = $PHPWS_Scheduler_Minute;
		}

	    }
	    $overlap = $duplicates + 1;
	} else {
	    $overlap = 1;
	}

	if ($overlap < 1) {
	  $overlap = 1;
	}


	$width = floor(84 / $overlap);

	/* ten hours * four slots in an hour */
	$numrows = (10 * 4);
	
	$slots = array();
	$ends  = array();
	for ($x = 1; $x <= $overlap; $x++) {
	    $slots[$x] = false; 
	    $ends[$x]  = 0;
	}
	
	$row = 0;
	while ($Hour =& $PHPWS_Scheduler_Day->fetch()) {
	    $Hour->build($selection);
	    
	    $PHPWS_Scheduler_Hour =& new PHPWS_Scheduler_Hour($Hour);
	    while ($Minute = & $PHPWS_Scheduler_Hour->fetch()) {
		$ts = $Minute->getTimeStamp();
		$time = date("g:i a", $ts);
		$date = date("l F d, Y", $ts);

		$js = implode(" ", array("onMouseOver=\"window.status='Add {$this->_title} for {$date} at {$time}'; return true;\"",
					 "onMouseOut=\"window.status='';\""));
		
		$content[] = "<tr class=\"scheduler\" align=\"right\">";
		$content[] = "<td width=\"16%\" class=\"no-shade\" nowrap=\"nowrap\">";
		$content[] = implode("", array("<a href=\"./index.php?module=scheduler&amp;op=editEntry&amp;time={$ts}\" title=\"Add {$this->_title} for {$date} at {$time}\" {$js}>",
					       date("g:i a", $ts),
					       "</a>"));
		$content[] = "&#160;&#160;&#160;&#160;&#160;&#160;</td>";

		if ($Minute->isSelected()) {
		    $entries = $Minute->getEntries();
		    foreach ($entries as $value) {
			$slot = array_search(false, $slots);
			$slots[$slot] = true;
			
			$id = $value['id'];
			$start = $value['start'];
			$end = $value['end'];

			$label = $value['label'];
			$admin = $value['admin'];
			
			if (($start >= $PHPWS_Scheduler_Day->getTimeStamp()) && ($start <= $PHPWS_Scheduler_Day->getTimeStamp() + (3600*24))) {
			    $startLabel = implode("&#160;", array(date("g:i a", $start), "Today"));
			} else {
			    $startLabel = implode("&#160;", array(date("g:i a", $start), date("l, F d", $start)));
			}
			
			if (($end >= $PHPWS_Scheduler_Day->getTimeStamp()) && ($end <= $PHPWS_Scheduler_Day->getTimeStamp() + (3600*24))) {
			    $endLabel = implode("&#160;", array(date("g:i a", $end), "Today"));
			} else {
			    $endLabel = implode("&#160;", array(date("g:i a", $end), date("l, F d", $end)));
			}
			
			$hour = (int)date('G', $end);
			$minute = date('i', $end);

			if ($minute >= 15) {
			  $diff = $minute % 15;
			}


			if ($diff) {
			    $end = $end + ((15 - $diff) * 60);
			}

			$rowspan = (($end - $ts) / (15 * 60));

			$rowspan = round($rowspan);
			if(($rowspan + $row) > $numrows) {
			    $rowspan = $numrows - $row;
			    $ends[$slot] = $numrows;
			} else {
			    $ends[$slot] = $row + $rowspan;
			}

			$textLabel = strip_tags($label);
			
			if (!$admin || ($this->_schedule == 0)) {
			    $content[] = "<td width=\"{$width}%\" align=\"left\" valign=\"top\" class=\"bg_medium\" rowspan=\"{$rowspan}\">";
			    $content[] = "<div class=\"buttons\">";

			    $js = implode(" ", array("onClick=\"editEntry({$id});\"",
						     "onMouseOver=\"window.status='Edit {$this->_title}'; return true;\"",
						     "onMouseOut=\"window.status='';\""));
			    
			    $content[] = "<img src=\"./images/mod/scheduler/edit.png\" height=\"15\" width=\"15\" alt=\"Edit {$this->_title}\" title=\"Edit {$this->_title}\" class=\"cursor\" {$js} />&#160;&#160;";

			    $js = implode(" ", array("onClick=\"cancelEntry({$id});\"",
						     "onMouseOver=\"window.status='Cancel {$this->_title}'; return true;\"",
						     "onMouseOut=\"window.status='';\""));
			    
			    $content[] = "<img src=\"./images/mod/scheduler/cancel.png\" height=\"15\" width=\"15\" alt=\"Cancel {$this->_title}\" title=\"Cancel {$this->_title}\" class=\"cursor\" {$js} />&#160;&#160;";
			    $content[] = "</div>";

			    $content[] = implode("<br />\n", array($label,
								   $startLabel,
								   "&#160;-&#160;",
								   $endLabel));
			    $content[] = "</td>";
			} else {
			    $content[] = "<td width=\"{$width}%\" align=\"left\" valign=\"top\" class=\"bg_medium\" rowspan=\"{$rowspan}\">";
			    $content[] = implode("<br />\n", array($label,
								   $startLabel,
								   "&#160;-&#160;",
								   $endLabel));
			    $content[] = "</td>";
			}
		    }
		}

		reset($slots);

		while (list($key, $value) = each($slots)) {
		    if (!$value) {
			$js = implode(" ", array("onClick=\"addEntry('$ts');\"",
						 "onMouseOver=\"this.className='shade cursor'; window.status='Add {$this->_title} for {$date} at {$time}';\"",
						 "onMouseOut=\"this.className='no-shade cursor'; window.status='';\"",
						 "onMouseDown=\"this.className='click';\""));
			
			$colspan = 1;

			do {
			    $continue = false;
			    $nextKey = $key + $colspan;
			    
			    if (array_key_exists($nextKey, $slots)) {
				if ($slots[$nextKey] === false) {
				    next($slots);
				    $colspan++;
				    $continue = true;
				}
			    }
			} while ($continue);

			if ($colspan > 1) {
			    $content[] = "<td colspan=\"{$colspan}\" class=\"no-shade\" title=\"Add {$this->_title} for {$date} at {$time}\" {$js}>"; 
			} else {
			  // gotta be here
			    $content[] = "<td width=\"{$width}%\" class=\"no-shade\" title=\"Add {$this->_title} for {$date} at {$time}\" {$js}>"; 
			}
			
			$content[] = "&#160;";	
			$content[] = "</td>";
		    }
		    
		    if ($ends[$key] == ($row + 1)) {
			$ends[$key] = 0;
			$slots[$key] = false;
		    }
		}
		
		$content[] = "</tr>";
		
		$row++;
	    }
	}
	
	return implode("\n", $content);
    }
    
    function getSmallMonth($override=null) {
	require_once 'Calendar/Month/Weekdays.php';
	require_once 'Calendar/Day.php';

	$tags    = array();
	$content = array();

	if (isset($override)) {
	    $Month =& new Calendar_Month_Weekdays(date("Y", $override), date("n", $override));
	} else {
	    $Month =& new Calendar_Month_Weekdays(date("Y", $this->_tsSmall), date("n", $this->_tsSmall));
	    $tags['PREV_MONTH'] = $Month->prevMonth(true);
	    $tags['NEXT_MONTH'] = $Month->nextMonth(true);
	}

	$Month->build();
		
	$timestamp = $Month->getTimeStamp();
	$month = date("F", $timestamp);
	$year = date("Y", $timestamp);
	
	$js = implode(" ", array("onMouseOver=\"window.status='View {$month} {$year}'; return true;\"",
				 "onMouseOut=\"window.status='';\""));
	
	$tags['DATE']   = array();
	$tags['DATE'][] = "<a href=\"./index.php?module=scheduler&amp;op=month&amp;ts={$timestamp}\" {$js}>{$month}</a>";
	$tags['DATE'][] = $year;
	$tags['DATE']   = implode("&#160;", $tags['DATE']);
	
	while ($Day =& $Month->fetch()) {
	    if ($Day->isFirst()) {
		$content[] = "<tr class=\"shade\">";
	    }
	    
	    if ($Day->isEmpty()) {
		$content[] =  "<td class=\"no-shade\">&#160;</td>";
	    } else {
		$timestamp = $Day->getTimeStamp();
		
		$class = "cursor";
		if (date('Y n j', time()) == date('Y n j', $Day->getTimeStamp())) {
		    $class = "today-border cursor";
		}
		
		if (date('Y n j', $timestamp) == date('Y n j', $this->_ts)) {
		    $class = "selected-border cursor";
		}
		
		$date = date("l F d, Y", $timestamp);
		
		$js = implode(" ", array("onClick=\"location.href='./index.php?module=scheduler&amp;op=days&amp;ts={$timestamp}';\"",
					 "onMouseOver=\"window.status='View {$date}'; return true;\"",
					 "onMouseOut=\"window.status='';\""));
		
		$content[] = "<td class=\"{$class}\" title=\"View {$date}\" {$js}>". $Day->thisDay() ."</td>";
	    }
	    
	    if($Day->isLast()) {
		$content[] = "</tr>";
	    }
	}
	
	$tags['ROWS'] = implode("\n", $content);
	
	return PHPWS_Template::processTemplate($tags, "scheduler", "smallmonth.tpl");
    }
    
    function getForm($op) {
	require_once PHPWS_SOURCE_DIR .'core/EZform.php';
	$form = new EZform("scheduleSelect");
	$form->add("module", "hidden", "scheduler");
	$form->add("op", "hidden", $op);
	$form->add("schedule", "dropbox", $this->_schedules);
	$form->setMatch("schedule", $this->_schedule);
	$form->setExtra("schedule", "onChange=\"form.submit();\"");
	//$form->add("go", "submit", "Go");
	$form->add("searchSchedule", "submit", "Search");
	$form->add("findOpenings", "submit", "Find Openings");
	return $form->getTemplate();
    }

    function find($search=false) {
	if (isset($_REQUEST['add_entry'])) {
	    return $this->editEntry();
	}

	require_once PHPWS_SOURCE_DIR .'core/EZform.php';
	
	$tags = array();
	
	$form = new EZform("scheduleSearch");
	$form->add("module", "hidden", "scheduler");

	if ($search) {
	    $form->add("op", "hidden", "search");
	    $form->add("schedules", "select", $this->_schedules);
	} else {
	    $form->add("op", "hidden", "find");
	    $form->add("schedules", "multiple", $this->_schedules);
	}

	if (!isset($_REQUEST['schedules'])) {
	    if ($search) {
		$_REQUEST['schedules'] = $this->_schedule;
	    } else {
		$_REQUEST['schedules'][] = $this->_schedule;
	    }
	}

	$form->setMatch("schedules", @$_REQUEST['schedules']);

	$months = array();
	for ($i = 1; $i <= 12; $i++) {
	    $months[$i] = $i;
	}
	
	$days = array();
	for ($i = 1; $i <= 31; $i++) {
	    $days[$i] = $i;
	}
	
	$years = array();
	for ($i = (date("Y", time()) - SCHEDULER_ENTRY_YEAR_BUFFER); $i <= (date("Y", time()) + SCHEDULER_ENTRY_YEAR_BUFFER); $i++) {
	    $years[$i] = $i;
	}

	$hours = array();
	for ($i = 0; $i <= 9; $i++) {
	    $hours[$i] = $i;
	}
	
	$minutes = array(0=>"00");
	for ($i = 15; $i <= 45; $i += 15) {
	    $minutes[$i] = $i;
	}
	
	$ampm = array("am"=>"am", "pm"=>"pm");
	
	$start = time();
	
	if (!empty($_REQUEST['startMonth'])) {
	    $startMonth = $_REQUEST['startMonth'];
	} else {
	    $startMonth  = date("n", $start);
	}

	if (!empty($_REQUEST['startDay'])) {
	    $startDay = $_REQUEST['startDay'];
	} else {
	    $startDay    = date("j", $start);
	}

	if (!empty($_REQUEST['startYear'])) {
	    $startYear = $_REQUEST['startYear'];
	} else {
	    $startYear   = date("Y", $start);
	}
	
	$end = $start + 3600;

	if (!empty($_REQUEST['endMonth'])) {
	    $endMonth = $_REQUEST['endMonth'];
	} else {
	    $endMonth  = date("n", $end);
	}

	if (!empty($_REQUEST['endDay'])) {
	    $endDay = $_REQUEST['endDay'];
	} else {
	    $endDay    = date("j", $end);
	}

	if (!empty($_REQUEST['endYear'])) {
	    $endYear = $_REQUEST['endYear'];
	} else {
	    $endYear   = date("Y", $end);
	}
	
	$form->add("startMonth", "dropbox", $months);
	$form->setMatch("startMonth", $startMonth);
	
	$form->add("startDay", "dropbox", $days);
	$form->setMatch("startDay", $startDay);
	
	$form->add("startYear", "dropbox", $years);
	$form->setMatch("startYear", $startYear);
	
	$form->add("endMonth", "dropbox", $months);
	$form->setMatch("endMonth", $endMonth);
	
	$form->add("endDay", "dropbox", $days);
	$form->setMatch("endDay", $endDay);
	
	$form->add("endYear", "dropbox", $years);
	$form->setMatch("endYear", $endYear);

	$form->add("hours", "dropbox", $hours);
	$form->setMatch("hours", @$_REQUEST['hours']);

	$form->add("minutes", "dropbox", $minutes);
	$form->setMatch("minutes", @$_REQUEST['minutes']);

	if ($search) {
	    $form->add("query", "text", @$_REQUEST['query']);
	    $form->setSize("query", 35);
	    $form->setMaxSize("query", 50);
	    $form->add("search", "submit", "Search");
	} else {
	    $form->add("find", "submit", "Find Openings");
	}

	if ((!empty($_REQUEST['startYear']) && !empty($_REQUEST['startMonth']) && !empty($_REQUEST['startDay']))
	   && (!empty($_REQUEST['endYear']) && !empty($_REQUEST['endMonth']) && !empty($_REQUEST['endDay']))) {
	
	    if (!isset($_REQUEST['schedules'])) {
		$this->error[] = "No schedules were selected.";
	    }

	    require_once 'Calendar/Minute.php';

	    $start = new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth'], $_REQUEST['startDay'], 8, 0);
	    $end   = new Calendar_Minute($_REQUEST['endYear'], $_REQUEST['endMonth'], $_REQUEST['endDay'], 17, 59);
	    
	    if ($start->isValid()) {
		$start = $start->getTimeStamp();
	    } else {
		$this->error[] = "The start date you selected is not valid.";
	    }
	    
	    if ($end->isValid()) {
		$end = $end->getTimeStamp();
	    } else {
		$this->error[] = "The end date you selected is not valid.";
	    }
	    
	    if ($start > $end) {
		$this->error[] = "The end date must be later than the start date.";
	    }
	    
	    if (!$search) {
		if (($_REQUEST['hours'] == 0) && ($_REQUEST['minutes'] == 0)) {
		    $this->error[] = "The time block selected was zero.";
		} else {
		    $range = ($_REQUEST['hours'] * 3600) + ($_REQUEST['minutes'] * 60);
		}
	    }
		
	    if ($search) {
		$where = array();
		
		if (!empty($_REQUEST['query'])) {
		    $where[] = "label LIKE '%{$_REQUEST['query']}%'";
		}

		if (sizeof($where) == 0) {
		    $this->error[] = "Search did not receive a query string.";
		}
	    }

	    if (sizeof($this->error) == 0) {
		$cals = null;
		
		if ($search) {
		    if ($_REQUEST['schedules'] == 0) {
			$cals = " AND global='1'";
		    } else {
			$cals = " AND user='{$_REQUEST['schedules']}'";
		    }
		
		    $sql = "SELECT id, start FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries WHERE
                            ((start>='{$start}' AND start<='{$end}') OR (end>='{$start}' AND end<='{$end}')) AND ((" . implode(" AND ", $where) . ") {$cals})
                            ORDER BY start ASC";
		    $results = $GLOBALS['core']->getAll($sql);

		    if (is_array($results) && sizeof($results)) {
			$tags['RESULTS'][] = "<h3>Schedule entries found for:</h3>";
			$tags['RESULTS'][] = "<ul>";

			foreach ($results as $entry) {
			    $date = date('m-d-Y h:i A', $entry['start']);
			    $tags['RESULTS'][] = "<li><a href=\"./index.php?module=scheduler&amp;op=days&amp;ts={$entry['start']}\">{$date}</a></li>";
			}

			$tags['RESULTS'][] = "</ul>";
			$tags['RESULTS'] = implode("\n", $tags['RESULTS']);
		    } else {
			$tags['RESULTS'] = "No schedule entries were found matching your query.";
		    }
		} else {
		    if (isset($_REQUEST['find_next']) && is_numeric($_REQUEST['time'])) {
			$result = $_REQUEST['time'] + (15 * 60);
			unset($_REQUEST['find_next']);
			unset($_REQUEST['time']);
		    }		    
			
		    $ors = array();
		    foreach ($_REQUEST['schedules'] as $schedule) {
			if ($schedule == 0) continue;
			$ors[] = "user='{$schedule}'";
		    }
		    
		    if (sizeof($ors) > 0) {
			$cals = " AND (". implode(" OR ", $ors) ."";
		    }
		    
		    if (in_array(0, $_REQUEST['schedules'])) {
			if (isset($cals)) {
			    $cals .= " OR global='1')";
			} else {
			    $cals = " AND global='1'";
			}
		    } else {
			if (isset($cals)) {
			    $cals .= ")";
			}
		    }
		    
		    do {
			if (isset($result)) {
			    $start = $result;
			}
			
			$block = $start + $range;
			
			if ((date('H:i', $block) > '18:00') || (date('D', $block) == 'Sat') || (date('D', $block) == 'Sun')) {
			    $start1 = new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth'], $_REQUEST['startDay']++, 8, 0);
			    if ($start1->isValid()) {
				$result = $start1->getTimeStamp();
			    } else {
				$start2 = new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth']++, 1, 8, 0);
				if ($start2->isValid()) {
				    $result = $start2->getTimeStamp();
				} else {
				    $start3 = new Calendar_Minute($_REQUEST['startYear']++, 1, 1, 8, 0);
				    $result = $start3->getTimeStamp();
				}
			    }
			    continue;
			}
			
			if ($block < $end) {
			    $sql = "SELECT end FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries 
                                    WHERE (((start<='{$start}' AND end>'{$start}')
                                         OR (start<'{$block}' AND end>='{$block}')
                                         OR (start>='{$start}' AND end<='{$block}'))
                                    {$cals})";

			    $result = $GLOBALS['core']->getOne($sql);
			    $success = true;
			} else {
			    $result = null;
			    $success = false;
			}
		    } while(isset($result));
		    
		    if ($success) {
			$appointment = date('m-d-Y h:i A', $start) . ' to ' . date('m-d-Y h:i A', $block);
			
			$form->add("time", "hidden", $start);
			$form->add("block", "hidden", $block);
			$form->add("find_next", "submit", "Find Next Opening");
			$form->add("add_entry", "submit", $appointment);
			
			$tags['RESULTS'] = "Add {$this->_title}: ";
		    } else {
			$tags['RESULTS'] = 'No avaiable openings where found for the time block needed, please try another search.';
		    }
		}
	    }
	}
	    
	$tags = $form->getTemplate(true, true, $tags);
	    
	if (sizeof($this->error) > 0) {
	    $tags['ERROR']   = array();
	    $tags['ERROR'][] = "There was a problem when trying to search for a {$this->_title}.<br />";
	    $tags['ERROR'][] = "<ul>";
	    foreach ($this->error as $value) {
		$tags['ERROR'][] = "<li>$value</li>";
	    }
	    $tags['ERROR'][] = "</ul>";
	    $tags['ERROR']   = implode("\n", $tags['ERROR']); 
	    $this->error     = array();
	}
	
	if ($search) {
	    return PHPWS_Template::processTemplate($tags, "scheduler", "search.tpl");
	} else {
	    return PHPWS_Template::processTemplate($tags, "scheduler", "find.tpl");
	}
    }
    
    function editEntry() {
	$this->_entry = new PHPWS_Entry(@$_REQUEST['id']);
	if (isset($_REQUEST['time']) && is_numeric($_REQUEST['time'])) {
	    $this->_entry->set("start", $_REQUEST['time']);

	    if (isset($_REQUEST['block'])) {
		$this->_entry->set("end", $_REQUEST['block']);
	    }
	}
	
	if (!isset($_REQUEST['schedules'])) {
	    if ($this->_schedule == 0) {
		$this->_entry->set("global", 1);
	    } else {
		$this->_entry->set("user", $this->_schedule);
	    }
	}
	
	return $this->_entry->edit();
    }
    
    function saveEntry() {
	$result = $this->_entry->save();
	if (is_bool($result) && $result) {
	    return $this->getDays();
	} else {
	    return $result;
	}
    }
    
    function printDays() {
	echo $this->getDays(true);
	exit();
    }
    
    function cancelEntry() {
	if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
	    $sql = "DELETE FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries WHERE id='{$_REQUEST['id']}' OR pid='{$_REQUEST['id']}'";
	    $GLOBALS['core']->query($sql);
	}
    }
    
    function action() {
	if (isset($_REQUEST['op'])) {
	    $op = $_REQUEST['op'];
	} else {
	    $op = null;
	}
	
	if (isset($_REQUEST['ts']) && is_numeric($_REQUEST['ts'])) {
	    $this->set("ts", $_REQUEST['ts']);
	}
	
	if (isset($_REQUEST['tsSmall']) && is_numeric($_REQUEST['tsSmall'])) {
	    $this->set("tsSmall", $_REQUEST['tsSmall']);
	}
	
	if (isset($_REQUEST['schedule']) && is_numeric($_REQUEST['schedule'])) {
	    $this->set("schedule", $_REQUEST['schedule']);
	}

	if (isset($_REQUEST['searchSchedule'])) {
	    $op = 'search';
	}

	if (isset($_REQUEST['findOpenings'])) {
	    $op = 'find';
	}
	
	switch($op) {
	    case 'month':
		$content = $this->getMonth();
		break;
		
	    case 'days':
		$content = $this->getDays();
		break;
		
	    case 'search':
		$content = $this->find(true);
		break;
		
	    case 'find':
		$content = $this->find();
		break;
		
	    case 'editEntry':
		$content = $this->editEntry();
		break;
		
	    case 'saveEntry':
		$content = $this->saveEntry();
		break;
		
	    case 'print':
		$this->printDays();
		break;
		
	    case 'cancelEntry':
		$this->cancelEntry();
	    default:
		$content = $this->getDays();
	}
	
	return $content;
    }
}

require_once 'Calendar/Decorator.php';

class PHPWS_Scheduler_Day extends Calendar_Decorator {
    var $_data = array();
    
    function PHPWS_Scheduler_Day(&$Day) {
	parent::Calendar_Decorator($Day);
    }
    
    function fetch() {
	if($Hour = parent::fetch()) {      
	    if($Hour->thisHour() < 8 || $Hour->thisHour() > 17) {
		return $this->fetch();
	    } else {
		return $Hour;
	    }
	} else {
	    return false;
	}
    }
    
    function add($label) {
	$this->_data[] = $label;
    }
    
    function getEntries() {
	return $this->_data;
    }
}

class PHPWS_Scheduler_Hour extends Calendar_Decorator {
    function PHPWS_Scheduler_Hour(&$Hour) {
	parent::Calendar_Decorator($Hour);
    }
    
    function fetch() { 
	if ($Minute = parent::fetch()) { 
	    if (($Minute->thisMinute() == 0) || ($Minute->thisMinute() == 15) || ($Minute->thisMinute() == 30) || ($Minute->thisMinute() == 45)) {
		return $Minute;
	    } else {
		return $this->fetch();
	    }
	} else {
	    return false;
	}
    }
}

class PHPWS_Scheduler_Minute extends Calendar_Decorator {
    var $_data = array();
    
    function PHPWS_Scheduler_Minute(&$Minute) {
	parent::Calendar_Decorator($Minute);
    }
    
    function add($id, $start, $end, $label, $admin) {
	$this->_data[] = array("id"=>$id, "start"=>$start, "end"=>$end, "label"=>$label, "admin"=>$admin);
    }
    
    function getEntries() {
	return $this->_data;
    }
}
