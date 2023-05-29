<?php

/**
 * Handles the repeating of events
 *
 * @version $Id: Repeat.php,v 1.13 2005/03/04 16:33:26 matt Exp $
 * @author Matt McNaney <matt [at] tux dot appstate dot edu>
 */

class PHPWS_Calendar_Repeat {

  var $id;
  var $endRepeat;
  var $repeatMode;
  var $monthMode;
  var $repeatWeekdays;
  var $every;
  var $pickDate;

  function repeatEvent($active){
    switch ($this->repeatMode){
    case 'daily':
      $this->createDaily($this->id);
      break;

    case 'weekly':
      $this->createWeekly($this->id);
    break;

    case 'monthly':
      $this->createMonthly($this->id);
    break;

    case 'yearly':
      $this->createYearly($this->id);
    break;

    case 'every':
      $this->createEvery($this->id);
      break;
    }

    $GLOBALS['core']->sqlUpdate(array('active'=>$active), 'mod_calendar_repeats', 'id', $this->id);
  }

  function getRepeat($id, $date){
    if ($repeat = $GLOBALS['core']->sqlSelect('mod_calendar_repeats', array('id'=>(int)$id, 'startDate'=>(int)$date)))
      return $repeat[0];
    else
      return NULL;
  }

  function clearRepeatsFromEvent($id=NULL){
    if ($id)
      $this->id = $id;
    elseif(!$this->id)
      return FALSE;

    $update['endRepeat'] = 0;
    $update['repeatMode'] = NULL;
    $update['monthMode'] = NULL;
    $update['repeatWeekdays'] = NULL;
    $update['every'] = NULL;

    return $GLOBALS['core']->sqlUpdate($update, 'mod_calendar_events', 'id', $this->id);

  }

  function createEvery($id){
    $occur = $_POST['everyNumber'];
    $day = $_POST['everyDay'];
    $month = $_POST['everyMonth'];
    $event = new PHPWS_Calendar_Event($id);

    $startDate = new Date;
    $endDate = new Date;

    PHPWS_Calendar::splitDateObject($startDate, $event->startDate);
    PHPWS_Calendar::splitDateObject($endDate, $this->endRepeat);

    $count = $startDate->getNextDay();

    while ($endDate->after($count) || $endDate->equals($count)){
      if ($month == 'a')
	$monthNumber = $count->month;
      else
	$monthNumber = $month;
      
      if (Date_Calc::NWeekdayOfMonth($occur, $day, $monthNumber, $count->year) == $count->format('%Y%m%d'))
	$this->writeRepeat($id, $count);

      $count = $count->getNextDay();
    }
    $event->setRepeatVars($this->endRepeat, 'every', NULL, NULL,array($occur, $day, $month));
  }

  function createDaily($id){
    $event = new PHPWS_Calendar_Event($id);

    $startDate = new Date;
    $endDate = new Date;

    PHPWS_Calendar::splitDateObject($startDate, $event->startDate);
    PHPWS_Calendar::splitDateObject($endDate, $this->endRepeat);

    $count = $startDate->getNextDay();
    while ($endDate->after($count) || $endDate->equals($count)){
      $this->writeRepeat($id, $count);
      $count = $count->getNextDay();
    }

    $event->setRepeatVars($this->endRepeat, 'daily');
  }

  function createWeekly($id){
    if (!$id || !is_numeric($id))
      exit("Error: createWeekly received an improper id : '$id'");

    $event = new PHPWS_Calendar_Event($id);
    
    $startDate = new Date;
    $endDate = new Date;

    PHPWS_Calendar::splitDateObject($startDate, $event->startDate);
    PHPWS_Calendar::splitDateObject($endDate, $this->endRepeat);

    $count = $startDate->getNextDay();
    while ($endDate->after($count) || $endDate->equals($count)){
      if ($this->repeatWeekdays[$count->getDayOfWeek()])
	$this->writeRepeat($id, $count);
      $count = $count->getNextDay();

    }

    $event->setRepeatVars($this->endRepeat, 'weekly', NULL, $this->repeatWeekdays);
  }

  function createMonthly($id){
    $event = new PHPWS_Calendar_Event($id);
    
    $startDate = new Date;
    $endDate   = new Date;
    $endRepeat = new Date;
    
    PHPWS_Calendar::splitDateObject($startDate, $event->startDate);
    PHPWS_Calendar::splitDateObject($endDate, $this->endDate);
    PHPWS_Calendar::splitDateObject($endRepeat, $this->endRepeat);
    
    $duration = Date_Calc::dateDiff($startDate->day, $startDate->month, $startDate->year, $endDate->day, $endDate->month, $endDate->year);
    $count = PHPWS_Calendar::addMonth($startDate);
    while ($endRepeat->after($count) || $endRepeat->equals($count)){
      switch ($this->monthMode){
      case 'begin':
	$count->setDay(1);
      $this->writeRepeat($id, $count, $duration);
      break;
      
      case 'end':
	$count->setDay($count->getDaysInMonth());
      $this->writeRepeat($id, $count, $duration);
      break;
      
      case 'date':
	if (checkdate($count->month, $count->day, $count->year))
	  $this->writeRepeat($id, $count, $duration);
	break;
      }

      $count = PHPWS_Calendar::addMonth($count);
    }
    $event->setRepeatVars($this->endRepeat, 'monthly', $this->monthMode);
  }

  function createYearly($id){
    $stop = 0;
    $event = new PHPWS_Calendar_Event($id);
    
    $startDate = new Date;
    $endDate = new Date;
    
    PHPWS_Calendar::splitDateObject($startDate, $event->startDate);
    PHPWS_Calendar::splitDateObject($endDate, $this->endRepeat);
    $duration = PHPWS_Calendar_Event::eventDuration($event);
    
    $count = PHPWS_Calendar::addYear($startDate);
    
    while ($endDate->after($count) || $endDate->equals($count)){
      $this->writeRepeat($id, $count, $duration);
      $count = PHPWS_Calendar::addYear($count);
      $stop++;
      if ($stop >10)
	exit('yearly died');

    }

    $event->setRepeatVars($this->endRepeat, 'yearly');
    
  }
    
  function processRepeats(){
    $pickDate_year = NULL;
    $pickDate_month = NULL;
    $pickDate_day = NULL;

    extract($_POST);

    if (!isset($repeatEvent)){
      $this->endRepeat = NULL;
      $this->repeatMode = NULL;
      $this->monthMode = NULL;
      $this->repeatWeekdays = NULL;
      $this->every = NULL;
      $this->pickDate= NULL;
      return;
    }

    $endRepeat = new Date;
    $endRepeat->setYear($endRepeat_year);
    $endRepeat->setMonth($endRepeat_month);
    $endRepeat->setDay($endRepeat_day);

    $pickDate = new Date;
    $pickDate->setYear($pickDate_year);
    $pickDate->setMonth($pickDate_month);
    $pickDate->setDay($pickDate_day);


    $this->endRepeat      = $endRepeat->format('%Y%m%d');
    $this->repeatMode     = $repeatMode;
    $this->monthMode      = $monthMode;

    if (isset($repeatWeekdays))
      for ($i=0; $i < 7; $i++)
	isset($repeatWeekdays[$i]) ? $this->repeatWeekdays[$i] = 1 : $this->repeatWeekdays[$i] = 0;

    $this->every          = array($everyNumber, $everyDay, $everyMonth);
    $this->pickDate       = $pickDate->format('%Y%m%d');


    if ($this->endDate >= $this->endRepeat && ($this->repeatMode != 'monthly' || $this->repeatMode != 'yearly'))
      $this->error[] = $_SESSION['translate']->it('Repeat dates must extend beyond the date of the event') . '.';

    if ($this->startDate != $this->endDate){
      switch ($this->repeatMode){

      case 'daily':
	$this->error[] = $_SESSION['translate']->it('Daily repeats must not exceed one day in duration') . '.';
	break;

      case 'weekly':
	$this->error[] = $_SESSION['translate']->it('Weekly repeats must not exceed one day in duration') . '.';
	break;
      }
    }

    if ($this->repeatMode == 'weekly' && is_null($this->repeatWeekdays))
      	$this->error[] = $_SESSION['translate']->it('Weekly repeats require at least one day selection') . '.';

  }

  function removeRepeats($id=NULL){
    if (!$id){
      if ($this->id)
	$id = $this->id;
      else
	return FALSE;
    }

    return $GLOBALS['core']->sqlDelete('mod_calendar_repeats', 'id', $id);
  }

  function writeRepeat($id, $date, $duration=0){
    if (!$id || !is_numeric($id))
      exit("Error writeRepeat received an invalid id : '$id'");
      
    if (is_object($date) && get_class($date) == 'date')
      $insert['startDate'] = $date->format('%Y%m%d');
    else
      exit('Error writeRepeat : date parameter is not a date object');

    $date->addSeconds($duration*86400);
    
    $insert['endDate'] = $date->format('%Y%m%d');
    $insert['id'] = $id;
    return $GLOBALS['core']->sqlInsert($insert, 'mod_calendar_repeats');
  }

}
?>