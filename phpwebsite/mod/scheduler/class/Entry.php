<?php

require_once PHPWS_SOURCE_DIR .'core/Item.php';

class PHPWS_Entry extends PHPWS_Item {

    var $_start          = null;
    var $_end            = null;
    var $_user           = null;
    var $_global         = null;
    var $_administrative = null;
    var $_repeat         = null;
    var $_repeat_until   = null;
    var $_mode           = null;
    var $_properties     = null;
    var $_pid            = null;
    var $_title          = 'schedule entry';
    var $error           = array();
    
    function PHPWS_Entry($id = null) {
	$this->setTable("mod_scheduler_entries");
	$this->addExclude(array("_approved", "error", "_title"));
	if (isset($id) && is_numeric($id)) {
	    $this->setId($id);
	    $this->init();
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
    
    function edit() {
	require_once PHPWS_SOURCE_DIR .'core/EZform.php';

	$form = new EZform("editEntry");
	$form->add("module", "hidden", "scheduler");
	$form->add("op", "hidden", "saveEntry");
	
	$id = $this->getId();
	if (isset($id)) {
	    $form->add("id", "hidden", $id);
	}
	
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
	for ($i = 1; $i <= 12; $i++) {
	    $hours[$i] = $i;
	}
	
	$minutes = array(0=>"00");
	for ($i = 15; $i <= 45; $i += 15) {
	    $minutes[$i] = $i;
	}
	
	$ampm = array("am"=>"am", "pm"=>"pm");
	
	$startMonth  = date("n", $this->_start);
	$startDay    = date("j", $this->_start);
	$startYear   = date("Y", $this->_start);
	$startHour   = date("h", $this->_start);
	$startMinute = date("i", $this->_start);
	$startAMPM   = date("a", $this->_start);
	
	if (!isset($this->_end)) {
	    $this->_end = $this->_start + 3600;
	}
	
	$endMonth  = date("n", $this->_end);
	$endDay    = date("j", $this->_end);
	$endYear   = date("Y", $this->_end);
	$endHour   = date("h", $this->_end);
	$endMinute = date("i", $this->_end);
	$endAMPM   = date("a", $this->_end);
	
	$form->add("label", "textarea", $this->getLabel());
	$form->setRows("label", 8);
	$form->setCols("label", 60);
	
	$form->add("startMonth", "dropbox", $months);
	$form->setMatch("startMonth", $startMonth);
	
	$form->add("startDay", "dropbox", $days);
	$form->setMatch("startDay", $startDay);
	
	$form->add("startYear", "dropbox", $years);
	$form->setMatch("startYear", $startYear);
	
	$form->add("startHour", "dropbox", $hours);
	$form->setMatch("startHour", $startHour);
	
	$form->add("startMinute", "dropbox", $minutes);
	$form->setMatch("startMinute", $startMinute);
	
	$form->add("startAMPM", "dropbox", $ampm);
	$form->setMatch("startAMPM", $startAMPM);
	
	$form->add("endMonth", "dropbox", $months);
	$form->setMatch("endMonth", $endMonth);
	
	$form->add("endDay", "dropbox", $days);
	$form->setMatch("endDay", $endDay);
	
	$form->add("endYear", "dropbox", $years);
	$form->setMatch("endYear", $endYear);
	
	$form->add("endHour", "dropbox", $hours);
	$form->setMatch("endHour", $endHour);
	
	$form->add("endMinute", "dropbox", $minutes);
	$form->setMatch("endMinute", $endMinute);
	
	$form->add("endAMPM", "dropbox", $ampm);
	$form->setMatch("endAMPM", $endAMPM);

	
	if (isset($_REQUEST['schedules']) && in_array(0, $_REQUEST['schedules'])) {
	    $this->_global = 1;
	    unset($_REQUEST['schedules']);
	}

	if ((isset($this->_global) && ($this->_global == 1))
	    || (isset($this->_administrative) && ($this->_administrative == 1))) {
	    $form->add("administrative", "checkbox", 1);
	    $form->setMatch("administrative", $this->_administrative);
	}

	$form->add("repeat", "checkbox", 1);
	$form->setMatch("repeat", $this->_repeat);

	if (!isset($this->_repeat_until)) {
	    $this->_repeat_until = $this->_start;
	}

	$repeatMonth  = date("n", $this->_repeat_until);
	$repeatDay    = date("j", $this->_repeat_until);
	$repeatYear   = date("Y", $this->_repeat_until);
	
	$form->add("repeatMonth", "dropbox", $months);
	$form->setMatch("repeatMonth", $repeatMonth);
	
	$form->add("repeatDay", "dropbox", $days);
	$form->setMatch("repeatDay", $repeatDay);
	
	$form->add("repeatYear", "dropbox", $years);
	$form->setMatch("repeatYear", $repeatYear);
	
	$form->add("mode", "radio", array(SCHEDULER_ENTRY_MODE_DAILY,
					  SCHEDULER_ENTRY_MODE_WEEKLY,
					  SCHEDULER_ENTRY_MODE_MONTHLY,
					  SCHEDULER_ENTRY_MODE_YEARLY,
					  SCHEDULER_ENTRY_MODE_EVERY));
	$form->setMatch("mode", $this->_mode);

	$form->add("properties_sun", "checkbox", 1);
	$form->add("properties_mon", "checkbox", 1);
	$form->add("properties_tue", "checkbox", 1);
	$form->add("properties_wed", "checkbox", 1);
	$form->add("properties_thu", "checkbox", 1);
	$form->add("properties_fri", "checkbox", 1);
	$form->add("properties_sat", "checkbox", 1);

	$options_monthly = array(SCHEDULER_ENTRY_MONTHLY_BEGIN => 'Beginning of each month',
				 SCHEDULER_ENTRY_MONTHLY_END   => 'End of each month',
				 SCHEDULER_ENTRY_MONTHLY_EVERY => 'Every month on start date');
	
	$form->add("properties_monthly", "select", $options_monthly);

	$options_every = array(1 => '1st',
			       2 => '2nd',
			       3 => '3rd',
			       4 => '4th');

	$form->add("properties_every", "select", $options_every);

	$options_day   = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

	$form->add("properties_day", "select", $options_day);

	$options_month = array('Every', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

	$form->add("properties_month", "select", $options_month);

	switch ($this->_mode) {
	    case SCHEDULER_ENTRY_MODE_WEEKLY:
		$properties = explode('::', $this->_properties);
		$form->setMatch("properties_sun", $properties[0]);
		$form->setMatch("properties_mon", $properties[1]);
		$form->setMatch("properties_tue", $properties[2]);
		$form->setMatch("properties_wed", $properties[3]);
		$form->setMatch("properties_thu", $properties[4]);
		$form->setMatch("properties_fri", $properties[5]);
		$form->setMatch("properties_sat", $properties[6]);
		break;

	    case SCHEDULER_ENTRY_MODE_MONTHLY:
		$form->setMatch("properties_monthly", $this->_properties);
		break;

	    case SCHEDULER_ENTRY_MODE_EVERY:
		$properties = explode('::', $this->_properties);
		$form->setMatch("properties_every", $properties[0]);
		$form->setMatch("properties_day", $properties[1]);
		$form->setMatch("properties_month", $properties[2]);
		break;
	}

	
	$form->add("save", "submit", "Save");
	
	$tags = $form->getTemplate();

	if (isset($_REQUEST['schedules'])) {
	    $tags['HIDDENS'] = array();
	    $ors = array();
	    foreach ($_REQUEST['schedules'] as $value) {
		$tags['HIDDENS'][] = "<input type=\"hidden\" name=\"schedules[]\" value=\"{$value}\" />";
		$ors[] = "user_id='{$value}'";
	    }
	    $tags['HIDDENS'][] = "<input type=\"hidden\" name=\"ignore_conflict\" value=\"1\" />";
	    $tags['HIDDENS'] = implode("\n", $tags['HIDDENS']);
	    $ors = implode(" OR ", $ors);

	    $sql = "SELECT username FROM {$GLOBALS['core']->tbl_prefix}mod_users WHERE {$ors}";
	    $schedule = implode(', ', $GLOBALS['core']->getCol($sql));
	} else {
	    if ($this->_global == 1) {
		$schedule = 'the Global Schedule';
	    } else {
		$sql = "SELECT user_id, username FROM {$GLOBALS['core']->tbl_prefix}mod_users WHERE user_id='{$this->_user}'";
		$row = $GLOBALS['core']->getRow($sql);
		$schedule = $row['username'];
	    }
	}
	
	if(isset($id)) {
	    $tags['TITLE'] = "Edit {$this->_title} for {$schedule}";
	} else {
	    $tags['TITLE'] = "Add {$this->_title} for {$schedule}";
	}
	
	if (sizeof($this->error) > 0) {
	    $tags['ERROR']   = array();
	    $tags['ERROR'][] = "There was a problem when trying to save the {$this->_title}.<br />";
	    $tags['ERROR'][] = "<ul>";
	    foreach($this->error as $value) {
		$tags['ERROR'][] = "<li>$value</li>";
	    }
	    $tags['ERROR'][] = "</ul>";
	    $tags['ERROR']   = implode("\n", $tags['ERROR']); 
	    $this->error     = array();
	}
	
	return PHPWS_Template::processTemplate($tags, "scheduler", "entry/edit.tpl");
    }
    
    function save() {
	require_once 'Calendar/Day.php';    
	require_once 'Calendar/Minute.php';    
	require_once PHPWS_SOURCE_DIR .'core/Text.php';
	require_once PHPWS_SOURCE_DIR .'core/Error.php';

	$startHour = $this->convertToMilitary($_REQUEST['startHour'], $_REQUEST['startAMPM']);
	$endHour = $this->convertToMilitary($_REQUEST['endHour'], $_REQUEST['endAMPM']);
	
	$StartMinute =& new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth'], $_REQUEST['startDay'], $startHour, $_REQUEST['startMinute']);
	$EndMinute =& new Calendar_Minute($_REQUEST['endYear'], $_REQUEST['endMonth'], $_REQUEST['endDay'], $endHour, $_REQUEST['endMinute']);   
	
	$error = $this->setLabel(PHPWS_Text::parseInput($_REQUEST['label']));
	if (PHPWS_Error::isError($error)) {
	    $this->error[] = "You must enter in some details for the {$this->_title}.";
	}
	
	if (isset($_REQUEST['administrative'])) {
	    $this->_administrative = 1;
	}
	
	if ($StartMinute->isValid()) {
	    $this->_start = $StartMinute->getTimeStamp();
	} else {
	    $this->error[] = "The start time you selected was not valid.";
	}
	
	if ($EndMinute->isValid()) {
	    $this->_end = $EndMinute->getTimeStamp();
	} else {
	    $this->error[] = "The end time you selected was not valid.";
	}
	
	if (!($this->_start < $this->_end)) {
	    $this->error[] = "The end time must be past the start time.";
	}


	if (!empty($_REQUEST['repeat'])) {
	    $this->_repeat = 1;
	    
	    $repeat =& new Calendar_Day($_REQUEST['repeatYear'], $_REQUEST['repeatMonth'], $_REQUEST['repeatDay']);

	    if ($repeat->isValid()) {
		$this->_repeat_until = $repeat->getTimeStamp();
	    } else {
		$this->error[] = "The repeat until date you selected was not valid.";
	    }

	    $this->_mode = @$_REQUEST['mode'];

	    $error = true;
	    switch ($this->_mode) {
		case SCHEDULER_ENTRY_MODE_DAILY:
		    $error = false;
		    break;

		case SCHEDULER_ENTRY_MODE_WEEKLY:
		    $this->_properties = array();
		    for($i = 0; $i < 7; $i++) $this->_properties[$i] = 0;
		    if (!empty($_REQUEST['properties_sun'])) {
			$this->_properties[0] = 1;
		    }
		    if (!empty($_REQUEST['properties_mon'])) {
			$this->_properties[1] = 1;
		    }
		    if (!empty($_REQUEST['properties_tue'])) {
			$this->_properties[2] = 1;
		    }
		    if (!empty($_REQUEST['properties_wed'])) {
			$this->_properties[3] = 1;
		    }
		    if (!empty($_REQUEST['properties_thu'])) {
			$this->_properties[4] = 1;
		    }
		    if (!empty($_REQUEST['properties_fri'])) {
			$this->_properties[5] = 1;
		    }
		    if (!empty($_REQUEST['properties_sat'])) {
			$this->_properties[6] = 1;
		    }
		    if (array_sum($this->_properties)) {
			$this->_properties = implode('::', $this->_properties);
			$error = false;
		    } else {
			$error = true;
		    }
		    break;
		    
		case SCHEDULER_ENTRY_MODE_MONTHLY:
		    $this->_properties = $_REQUEST['properties_monthly'];
		    $error = false;
		    break;
		    
		case SCHEDULER_ENTRY_MODE_YEARLY:
		    $error = false;
		    break;
		    
		case SCHEDULER_ENTRY_MODE_EVERY:
		    $this->_properties = implode('::', array($_REQUEST['properties_every'],
							     $_REQUEST['properties_day'],
							     $_REQUEST['properties_month']));
		    $error = false;
		    break;
	    }

	    if ($error) {
		$this->error[] = 'Problem with repeat data.';
	    }

	} else {
	    $this->_repeat       = null;
	    $this->_repeat_until = null;
	    $this->_mode         = null;
	    $this->_properties   = null;
	    $this->_pid          = null;
	}
	
	if (!isset($_REQUEST['ignore_conflict'])) {
	    $result = $this->checkForConflict();
	    
	    if (sizeof($result) > 0) {
		$this->error[] = "The {$this->_title} conflicts with another. ({$result['label']})<br />\n
                                  <input type=\"submit\" name=\"ignore_conflict\" value=\"Ignore Conflict\" />";
	    }
	}
	
	if (sizeof($this->error) > 0) {
	    return $this->edit();
	} else {
	    if ($this->_repeat) {
		if (isset($this->_id)) {
		    $sql = "DELETE FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries WHERE pid='{$this->_id}'";
		    $GLOBALS['core']->query($sql);
		}

		$this->commit();
		$true_start   = $this->_start;
		$pid          = $this->_id;
		$repeat_until = $this->_repeat_until;
		$mode         = $this->_mode;
		$properties   = explode('::', $this->_properties);

		$this->_repeat       = null;
		$this->_repeat_until = null;
		$this->_mode         = null;
		$this->_properties   = null;

		do {
		    $_REQUEST['startDay']++;
		    $_REQUEST['endDay']++;
		    $start1 =& new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth'], $_REQUEST['startDay'], $startHour, $_REQUEST['startMinute']);
		    $end1   =& new Calendar_Minute($_REQUEST['endYear'], $_REQUEST['endMonth'], $_REQUEST['endDay'], $endHour, $_REQUEST['endMinute']);
		    if ($start1->isValid()) {
			$start = $start1->getTimeStamp();
			$end   = $end1->getTimeStamp();
		    } else {
			$_REQUEST['startDay'] = 1;
			$_REQUEST['endDay']   = 1;
			$_REQUEST['startMonth']++;
			$_REQUEST['endMonth']++;
			$start2 =& new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth'], $_REQUEST['startDay'], $startHour, $_REQUEST['startMinute']);
			$end2   =& new Calendar_Minute($_REQUEST['endYear'], $_REQUEST['endMonth'], $_REQUEST['endDay'], $endHour, $_REQUEST['endMinute']);
			if ($start2->isValid()) {
			    $start = $start2->getTimeStamp();
			    $end   = $end2->getTimeStamp();
			} else {
			    $_REQUEST['startMonth'] = 1;
			    $_REQUEST['endMonth']   = 1;
			    $_REQUEST['startYear']++;
			    $_REQUEST['endYear']++;
			    $start3 =& new Calendar_Minute($_REQUEST['startYear'], $_REQUEST['startMonth'], $_REQUEST['startDay'], $startHour, $_REQUEST['startMinute']);
			    $end3   =& new Calendar_Minute($_REQUEST['endYear'], $_REQUEST['endMonth'], $_REQUEST['endDay'], $endHour, $_REQUEST['endMinute']);
			    $start = $start3->getTimeStamp();
			    $end   = $end3->getTimeStamp();
			}
		    }

		    $save = false;
		    switch($mode) {
			case SCHEDULER_ENTRY_MODE_DAILY:
			    $save = true;
			    break;

			case SCHEDULER_ENTRY_MODE_WEEKLY:
			    if ((date('D', $start) == 'Sun') && $properties[0]) {
				$save = true;
			    } else if ((date('D', $start) == 'Mon') && $properties[1]) {
				$save = true;
			    } else if ((date('D', $start) == 'Tue') && $properties[2]) {
				$save = true;
			    } else if ((date('D', $start) == 'Wed') && $properties[3]) {
				$save = true;
			    } else if ((date('D', $start) == 'Thu') && $properties[4]) {
				$save = true;
			    } else if ((date('D', $start) == 'Fri') && $properties[5]) {
				$save = true;
			    } else if ((date('D', $start) == 'Sat') && $properties[6]) {
				$save = true;
			    }
			    break;
		    
			case SCHEDULER_ENTRY_MODE_MONTHLY:
			    if ($properties[0] == SCHEDULER_ENTRY_MONTHLY_BEGIN) {
				if (date('j', $start) == 1) {
				    $save = true;
				}
			    } else if ($properties[0] == SCHEDULER_ENTRY_MONTHLY_END) {
				require_once ('Date/Calc.php');
				$day = Date_Calc::daysInMonth(date('m', $start), date('Y', $start));
				if (date('d', $start) == $day) {
				    $save = true;
				}
			    } else if ($properties[0] == SCHEDULER_ENTRY_MONTHLY_EVERY) {
				if (date('j', $start) == date('j', $true_start)) {
				    $save = true;
				}
			    }
			    break;
		    
			case SCHEDULER_ENTRY_MODE_YEARLY:
			    if (date('m-d', $start) == date('m-d', $true_start)) {
				$save = true;
			    }
			    break;
		    
			case SCHEDULER_ENTRY_MODE_EVERY:
			    require_once ('Date/Calc.php');
			    if ($properties[2] == 0) {
				$day = Date_Calc::NWeekdayOfMonth($properties[0], $properties[1], date('m', $start), date('Y', $start));
				if (date('Ymd', $start) == $day) {
				    $save = true;
				}
			    } else {
				if ($properties[2] == date('n', $start)) {
				    $day = Date_Calc::NWeekdayOfMonth($properties[0], $properties[1], date('m', $start), date('Y', $start));
				    if (date('Ymd', $start) == $day) {
					$save = true;
				    }
				}
			    }
			    break;
		    }

		    if ($save) {
			$this->_id    = null;
			$this->_start = $start;
			$this->_end   = $end;
			$this->_pid   = $pid;
	
			$result = $this->checkForConflict();
		
			if (sizeof($result) > 0) {
			    if (isset($this->_global)) {
				$_SESSION['PHPWS_Scheduler']->_conflicts[0][] = $this->_start;
			    } else {
				$_SESSION['PHPWS_Scheduler']->_conflicts[$this->_user][] = $this->_start;
			    }
			} else {
			    $this->commit();
			}
		    }

		} while($start <= $repeat_until);

		return true;
	    } else {
		if (isset($_REQUEST['schedules'])) {
		    foreach ($_REQUEST['schedules'] as $value) {
			$this->_id = null;
			$this->_user = $value;
			$this->commit();
		    }
		} else {
		    $this->commit();

		    if ($this->_global) {
			if (isset($_SESSION['PHPWS_Scheduler']->_conflicts[0])
			    && is_array($_SESSION['PHPWS_Scheduler']->_conflicts[0])) {
			    $key = array_search($this->_start, $_SESSION['PHPWS_Scheduler']->_conflicts[0]);
			    unset($_SESSION['PHPWS_Scheduler']->_conflicts[0][$key]);
			}
		    } else {
			if (isset($_SESSION['PHPWS_Scheduler']->_conflicts[$this->_user])
			    && is_array($_SESSION['PHPWS_Scheduler']->_conflicts[$this->_user])) {
			    $key = array_search($this->_start, $_SESSION['PHPWS_Scheduler']->_conflicts[$this->_user]);
			    unset($_SESSION['PHPWS_Scheduler']->_conflicts[$this->_user][$key]);
			}
		    }
		}
		
		return true;
	    }
	}
    }
    
    function convertToMilitary($hour, $ampmFlag) {
	if ($ampmFlag == "am") {
	    if ($hour == 12) {
		return 0;
	    } else {
		return $hour;
	    }
	} else {
	    if ($hour == 12) {
		return $hour;
	    } else {
		return ($hour + 12);
	    }
	}
    }

    function checkForConflict() {
	if ($this->_global == 1) {
	    $where = " AND global='1'";
	}
	
	if (isset($this->_user)) {
	    $where = "AND user='{$this->_user}'";
	}
	
	if (isset($this->_id)) {
	    $sql = "SELECT id, label FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries 
                        WHERE (((start<='{$this->_start}' AND end>'{$this->_start}')
                        OR (start<'{$this->_end}' AND end>='{$this->_end}')
                        OR (start>='{$this->_start}' AND end<='{$this->_end}'))
                        AND id!='{$this->_id}' {$where})";
	} else {
	    $sql = "SELECT id, label FROM {$GLOBALS['core']->tbl_prefix}mod_scheduler_entries
                        WHERE (((start<='{$this->_start}' AND end>'{$this->_start}')
                        OR (start<'{$this->_end}' AND end>='{$this->_end}')
                        OR (start>='{$this->_start}' AND end<='{$this->_end}')) {$where})";
	}
	
	return $GLOBALS['core']->getRow($sql);
    }
}
