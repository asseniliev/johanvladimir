<?php

/**
 * Display handles the different calendar views
 *
 * @version $Id: Display.php,v 1.79 2005/08/17 14:41:01 matt Exp $
 * @author Matt McNaney <matt [at] tux dot appstate dot edu>
 */

require_once(PHPWS_SOURCE_DIR . 'core/Text.php');

require_once(PHPWS_SOURCE_DIR . 'core/Array.php');

require_once(PHPWS_SOURCE_DIR . 'core/WizardBag.php');

define('VIEW_TTL', 300);

/* PEAR date class */
require_once('Date.php');

class PHPWS_Calendar_Display{

  var $startDate;
  var $endDate;
  var $view;

  function setBoxMonth($month, $year){
    $_SESSION['miniView'] = array('month'=>$month, 'year'=>$year);
  }

  /**
   * Displays the user box 
   * 
   * All the views available are not finished. Eventually you will be able to set via
   * your template what will appear. The function is set to accept these later.
   */
  function showUserBox(){
    $settings = $GLOBALS['calendarSettings'];

    if ($settings['minimonth'] == 1){
      $showBox = 1;
      if (isset($_SESSION['miniView']['month']) && isset($_SESSION['miniView']['year'])){
        $month = $_SESSION['miniView']['month'];
        $year = $_SESSION['miniView']['year'];
      } else {
        $today = mktime() + $GLOBALS['core']->datetime->time_dif;
        $month = date('m', $today);
        $year = date('Y', $today);
      }

      if ((isset($settings['sessionView']) && $settings['sessionView'] == 1) && isset($_SESSION['miniView']['miniCal']))
        $template['MINI_MONTH'] = $_SESSION['miniView']['miniCal'];
      elseif ($settings['cacheView'] == 1 && $miniMonth = PHPWS_Cache::get(sprintf('miniMonth_%04d%02d', $year, $month)))
        $_SESSION['miniView']['miniCal'] = $template['MINI_MONTH'] = $miniMonth;
      else
        $_SESSION['miniView']['miniCal'] = $template['MINI_MONTH'] = PHPWS_Calendar_Display::miniMonth($month, $year, TRUE, 'event');
    }

    if ($settings['daysAhead'] > 0){
      if (isset($_SESSION['miniView']['daysahead']))
        $template['DAYSAHEAD'] = $_SESSION['miniView']['daysahead'];
      else
        $_SESSION['miniView']['daysahead'] = $template['DAYSAHEAD'] = PHPWS_Calendar_Display::daysAhead();
      
      if(!empty($_SESSION['miniView']['daysahead']) || 
         !empty($template['DAYSAHEAD'])) 
        $showBox = 1;
      
    }

    if ($settings['userSubmit'] == 1){
      $template['USER_SUBMIT'] = PHPWS_Calendar_Display::userSubmit();
      $showBox = 1;
    }

    if(isset($showBox))
      return PHPWS_Template::processTemplate($template, 'calendar', 'userbox/userBox.tpl');
  }

  function daysAhead($year=NULL, $month=NULL, $day=NULL){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }

    $settings = $GLOBALS['calendarSettings'];
    if (!($viewAhead = $settings['daysAhead']))
      return NULL;

    if (is_null($year))
      $year = date('Y');

    if (is_null($month))
      $month = date('m');

    if (is_null($day))
      $day = date('d');

    if ($settings['cacheView'] && $daysAhead = PHPWS_Cache::get(sprintf('daysAhead_%04d%02d%02d', $year, $month, $day)))
      return $daysAhead;

    $date = new Date;
    $date->setMonth($month);
    $date->setYear($year);
    $date->setDay($day);

    if ($GLOBALS['core']->datetime->time_dif < 0) {
      $date->subtractSeconds(($GLOBALS['core']->datetime->time_dif) * -1);
    }
    elseif ($GLOBALS['core']->datetime->time_dif > 0) {
      $date->addSeconds($GLOBALS['core']->datetime->time_dif);
    }

    $end = $date;
    $end->addSeconds(86400 * $viewAhead);

    $eventList = PHPWS_Calendar::loadEvents($date, $end);
    $eventCount = 0;

    $template = array();
    $template['DAYS'] = NULL;
    while($end->after($date)){
      if ($events = PHPWS_Calendar::getEvents($date, $eventList)){
        $daysTpl = PHPWS_Calendar_Display::getDateTemplate($date);
        $daysTpl['EVENTS'] = NULL;

        foreach ($events as $time=>$indexedEvent){
          if ($time == -1)
            $time = $daystpl['TIME'] = $_SESSION['translate']->it('All Day');
          else {
            $tempDate = PHPWS_Calendar::formatDateTime($time, $date->format('%Y%m%d'));
            $time = $daystpl['TIME'] = $tempDate['time'];
          }
          
          foreach ($indexedEvent as $id=>$eventInfo){
            $eventInfo['viewDate'] = date('Ymd', $date->getDate(DATE_FORMAT_UNIXTIME));
            $eventtpl = PHPWS_Calendar_Display::getEventTemplate($eventInfo);
            $eventCount++;

            $daysTpl['EVENTS'] .= PHPWS_Template::processTemplate($eventtpl, 'calendar', 'daysAhead/events.tpl');
          }
        }
        $template['DAYS'] .= PHPWS_Template::processTemplate($daysTpl, 'calendar', 'daysAhead/days.tpl');
      }
      $date = $date->getNextDay();
    }

    if(isset($template['DAYS']))
      $template['COMING_SOON_LABEL'] = $_SESSION['translate']->it('Coming Soon...');
    return PHPWS_Template::processTemplate($template, 'calendar', 'daysAhead/full.tpl');
  }


  function getDateTemplate($date, $suffix=NULL, $template=NULL){
    $template['FULL_WEEKDAY'.$suffix] = $_SESSION['translate']->it($date->format('%A'));
    $template['ABBR_WEEKDAY'.$suffix] = $_SESSION['translate']->it($date->format('%a'));
    $template['LETTER_WEEKDAY'.$suffix] = substr($template['ABBR_WEEKDAY'.$suffix], 0, 1);
    $template['DAY']          = PHPWS_Text::moduleLink($date->format('%e'), 'calendar', array('calendar[view]'=>'day', 'month'=>$date->month, 'year'=>$date->year, 'day'=>$date->day));
    $template['FULL_MONTH']   = PHPWS_Text::moduleLink($_SESSION['translate']->it($date->format('%B')), 'calendar', array('calendar[view]'=>'month', 'month'=>$date->month, 'year'=>$date->year));
    $template['ABBR_MONTH']   = PHPWS_Text::moduleLink($_SESSION['translate']->it($date->format('%b')), 'calendar', array('calendar[view]'=>'month', 'month'=>$date->month, 'year'=>$date->year));
    $template['NUM_MONTH'] = PHPWS_Text::moduleLink($date->month, 'calendar', array('calendar[view]'=>'month', 'month'=>$date->month, 'year'=>$date->year));
    $template['FULL_YEAR'] = PHPWS_Text::moduleLink($date->format('%Y'), 'calendar', array('calendar[view]'=>'year', 'year'=>$date->year));
    $template['ABRV_YEAR'] = PHPWS_Text::moduleLink($date->format('%y'), 'calendar', array('calendar[view]'=>'year', 'year'=>$date->year));
    return $template;
  }


  function getRepeatTemplate($event){
    $extra = NULL;
    $days  = '';

    if (!isset($event->repeatMode))
      return NULL;

    switch ($event->repeatMode){
    case 'daily':
      $mode = $_SESSION['translate']->it('daily');
    break;

    case 'weekly':
      $mode = $_SESSION['translate']->it('weekly');

    foreach ($event->repeatWeekdays as $day=>$value)
      if ($value)
        $days .= PHPWS_Calendar::getWeekdayName($day) . ' ';

    $extra = $_SESSION['translate']->it('every [var1]', $days);
    break;
    
    case 'monthly':
      switch($event->monthMode){
      case 'begin':
        $mode = $_SESSION['translate']->it('at the beginning of each month');
        break;

      case 'end':
        $mode = $_SESSION['translate']->it('at the end of each month'); 
        break;

      case 'date':
        $mode = $_SESSION['translate']->it('on this day each month');   
        break;
      }
    break;
    
    case 'yearly':
      $mode = $_SESSION['translate']->it('on this day each year');
    break;
    
    case 'every':
      switch ($event->every[0]){
      case 1:
        $number = $_SESSION['translate']->it('first');
        break;

      case 2:
        $number = $_SESSION['translate']->it('second');
        break;

      case 3:
        $number = $_SESSION['translate']->it('third');
        break;

      case 4:
         $number = $_SESSION['translate']->it('fourth');
        break;
      }

      $day = PHPWS_Calendar::getWeekdayName($event->every[1]);

      if ($event->every[2] == 'a')
        $month = $_SESSION['translate']->it('month');
      else
        $month = $_SESSION['translate']->it(date('F', mktime(12,0,0, $event->every[2],1,2002)));

      $mode = $_SESSION['translate']->it('on the [var1] [var2] of every [var3]', $number, $day, $month);
    break;
    }

    $endDate = $GLOBALS['core']->datetime->date($GLOBALS['core']->datetime->mkdate($event->endRepeat));

    $until = $_SESSION['translate']->it('until') . ' ' . $endDate['full'];

    $template['REPEAT'] =  $_SESSION['translate']->it('This event repeats [var1] [var2] [var3]', $mode, $extra, $until);
    return $template;
  }

  function getEventTemplate($event, $repeat=FALSE, $showFatcat=FALSE){
    $image_directory = PHPWS_HOME_DIR . 'images/calendar/';
    $image_address   = PHPWS_HOME_HTTP . 'images/calendar/';

    if (is_array($event)){
      $temp = $event;
      $event = new PHPWS_Calendar_Event;
      PHPWS_Array::arrayToObject($temp, $event);
    }

    if ($repeat)
      $template = PHPWS_Calendar_Display::getRepeatTemplate($event);

    $template['PLAIN_TITLE'] = $event->title;

    if (isset($event->isRepeat) && $event->isRepeat == 1)
      $template['TITLE'] = PHPWS_Text::moduleLink($event->title, 'calendar', array('calendar[view]'=>'event', 'id'=>$event->id, 'date'=>$event->startDate));
    else
      $template['TITLE'] = PHPWS_Text::moduleLink($event->title, 'calendar', array('calendar[view]'=>'event', 'id'=>$event->id));

    if (!$event->active){
      $template['PLAIN_TITLE'] .= '*';
      $template['TITLE'] .= '*';
    }


    if ($event->eventType == 'interval' && $event->startDate < $event->endDate)
      $template['INTERVAL'] = $_SESSION['translate']->it('Day').'&nbsp;'.$event->dayNumber;

    $template['JSICON']      = PHPWS_Calendar_Display::getEventIcon($event->id);
    $template['DESCRIPTION'] = PHPWS_Text::parseOutput($event->description);

    if ($_SESSION['OBJ_user']->allow_access('calendar')){
      $edit['calendar[admin]']      = 'editEvent';
      $delete['calendar[admin]']    = 'deleteEvent';
      $delete['id'] = $edit['id']   = $event->id;

      if (!PHPWS_Approval::waitingForApproval($event->id, 'calendar')){
        $template['EDIT']  = PHPWS_Text::moduleLink($_SESSION['translate']->it('Edit'), 'calendar', $edit);
        $template['DELETE'] = PHPWS_Text::moduleLink($_SESSION['translate']->it('Delete'), 'calendar', $delete);
      }
    }

    if ($showFatcat && isset($_SESSION['OBJ_fatcat'])){
      $template['CATLINKS'] = $_SESSION['OBJ_fatcat']->fatcatLinks($event->id, 'calendar');
      $template['CATEGORY'] = $_SESSION['translate']->it('Categories');
      $template['CAT_ICON'] = $_SESSION['OBJ_fatcat']->getIcon($event->id, FALSE, TRUE, 'calendar');
    }

    if ($event->image && file_exists($image_directory . $event->image['name']))
      $template['IMAGE'] = PHPWS_Text::imageTag($image_address . $event->image['name'], $event->title, $event->image['width'], $event->image['height']);

    $dateTime = $event->getFormattedDateTime();
    $template = array_merge($template, $dateTime);

    return $template;
  }

  function viewMiniEvent($id){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }

    require_once (PHPWS_SOURCE_DIR . '/mod/calendar/class/Event.php');    
    if (!$id)
      exit('Error: viewMiniEvent received a zero or null as the event Id.');
    
    $event = new PHPWS_Calendar_Event($id);

    $theme_dir = $_SESSION['OBJ_layout']->theme_dir; 

    $template = PHPWS_Calendar_Display::getEventTemplate($event);
    $template['STYLE'] = $_SESSION['OBJ_layout']->pickCSS();
    $template['CHARSET'] = $_SESSION['OBJ_layout']->meta_content;

    $content = PHPWS_Template::processTemplate($template, 'calendar', 'event/miniEventView.tpl');

    /*
     // why is this here?
    $content = $content . '<br /><br />&nbsp;&nbsp;';

    $content .= PHPWS_Text::moduleLink(
                            $_SESSION['translate']->it('Edit Event'),
                            'calendar', array('calendar[admin]'=>'editEvent',
                                              'lay_quiet'=>1,
                                              'id'=>$id));
    */
    echo $content;
    exit();
    // not needed
    //    PHPWS_Approval::viewInApprovalWin($content, $_SESSION['translate']->it('View Event'));
  }

  function viewDay($year=NULL, $month=NULL, $day=NULL){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }
    PHPWS_Layout::blockRobot();
    if (is_null($year))
      $year = date('Y');
    
    if (is_null($month))
      $month = date('m');
    
    if (is_null($day))
      $day = date('d');

    $settings = $GLOBALS['calendarSettings'];
    
    if ($settings['cacheView'])
      if ($cache = PHPWS_Cache::get(sprintf('day_%04d%02d%02d', $year, $month, $day)))
        return $cache;

    $date = new Date;
    $date->setYear($year);
    $date->setMonth($month);
    $date->setDay($day);

    $nextDay = $date->getNextDay();
    $prevDay = $date->getPrevDay();

    $formattedDate = $GLOBALS['core']->datetime->date($date->format('%Y%m%d').'120000', TRUE);
    $template['DATE'] = $formattedDate['full'];

    $nextLink = PHPWS_Template::processTemplate(array('NEXT'=>' '), 'calendar', 'day/dayViewSettings.tpl');
    $prevLink = PHPWS_Template::processTemplate(array('PREV'=>' '), 'calendar', 'day/dayViewSettings.tpl');

    $template = PHPWS_Calendar_Display::getDateTemplate($date);
    $template['PREV'] = str_replace("\n", '', PHPWS_Text::moduleLink($prevLink, 'calendar', array('calendar[view]'=>'day', 'year'=>$prevDay->year, 'month'=>$prevDay->month, 'day'=>$prevDay->day)));
    $template['NEXT'] = str_replace("\n", '', PHPWS_Text::moduleLink($nextLink, 'calendar', array('calendar[view]'=>'day', 'year'=>$nextDay->year, 'month'=>$nextDay->month, 'day'=>$nextDay->day)));
    
    $eventList = PHPWS_Calendar::loadEvents($date);

    if (!($events = PHPWS_Calendar::getEvents($date, $eventList))){
      $template['EVENTS'] = $_SESSION['translate']->it('No events today') . '.';
      return PHPWS_Template::processTemplate($template, 'calendar', 'day/viewDay.tpl');
    } else
      $template['EVENTS'] = NULL;

    $count = 0;
    foreach ($events as $time=>$eventList){
      
      $timeSection = NULL;
      if ($time == -1)
        $timeSection['TIME'] = $_SESSION['translate']->it('All Day');
      else {
        $tempDate = PHPWS_Calendar::formatDateTime($time, $date->format('%Y%m%d'));
        $timeSection['TIME'] = $tempDate['time'];
      }

      foreach($eventList as $id=>$info){
        $dayRow = NULL;
        $count++;
        $event = new PHPWS_Calendar_Event;
        PHPWS_Array::arrayToObject($info, $event); 

        if (is_string($event->repeatWeekdays))
          $event->repeatWeekdays = explode(':', $event->repeatWeekdays);

        if ($count%2)
          $dayRow['TOGGLE1'] = ' ';
        else
          $dayRow['TOGGLE2'] = ' ';

        $timeInfo = $event->getFormattedDateTime();
        $dayRow = PHPWS_Calendar_Display::getEventTemplate($event, TRUE);
        $dayRow['TIME']        = $timeInfo['TIME'];
        $template['EVENTS']   .= PHPWS_Template::processTemplate($dayRow, 'calendar', 'day/viewDayRow.tpl');
      }

    }

    $content = PHPWS_Template::processTemplate($template, 'calendar', 'day/viewDay.tpl', TRUE, TRUE);
    if ($settings['cacheView'])
      PHPWS_Cache::set($content, sprintf('day_%04d%02d%02d', $year, $month, $day), 'calendar', VIEW_TTL);
    return $content;
  }


  function viewEvent($id, $date=NULL){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }
    PHPWS_Layout::blockRobot();
    require_once (PHPWS_SOURCE_DIR . '/mod/calendar/class/Event.php');
    $image_address   = PHPWS_HOME_HTTP . 'images/calendar/';
    $template = $content = NULL;

    $_SESSION['OBJ_fatcat']->whatsRelated($id);

    $event = new PHPWS_Calendar_Event($id);
    if ($date){
      if ($repeat = PHPWS_Calendar_Repeat::getRepeat($id, $date)){
        $event->startDate = $repeat['startDate'];
        $event->endDate = $repeat['endDate'];
      }
    }

    if (!$event->id)
      return FALSE;

    $edit['calendar[admin]'] = 'editEvent';
    $edit['returnView'] = 'event';
    $delete['calendar[admin]']    = 'deleteEvent';
    $delete['id'] = $edit['id']   = $id;

    $template = PHPWS_Calendar_Display::getEventTemplate($event, TRUE, TRUE);
    $content .= PHPWS_Template::processTemplate($template, 'calendar', 'event/'.$event->template, TRUE, TRUE);

    return $content;
  }

  function miniMonth($month=NULL, $year=NULL, $change=NULL, $linkMode=NULL){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }

    $template = NULL;
    $defaultWeekStart = $GLOBALS['core']->datetime->day_start;

    $settings = $GLOBALS['calendarSettings'];

    if ($settings['cacheView'])
      if ($cache = PHPWS_Cache::get('miniMonth_%04d%02d', $year, $month))
        return $cache;

    if (!isset($month))
      $month = date('n');

    if (!isset($year))
      $year = date('Y');
    
    $date = new Date;
    $today = new Date;

    if ($GLOBALS['core']->datetime->time_dif < 0) {
      $today->subtractSeconds(($GLOBALS['core']->datetime->time_dif) * -1);
    }
    elseif ($GLOBALS['core']->datetime->time_dif > 0) {
      $today->addSeconds($GLOBALS['core']->datetime->time_dif);
    }

    $date->setMonth($month);
    $date->setYear($year);
    $date->setDay(1);

    $prevMonth = new Date;
    $prevMonth->setYear(Date_Calc::beginOfPrevMonth(1, $date->month, $date->year, '%Y'));
    $prevMonth->setMonth(Date_Calc::beginOfPrevMonth(1, $date->month, $date->year, '%m'));
    $prevMonth->setDay(1);

    $nextMonth = new Date;
    $nextMonth->setYear(Date_Calc::beginOfNextMonth(1, $date->month, $date->year, '%Y'));
    $nextMonth->setMonth(Date_Calc::beginOfNextMonth(1, $date->month, $date->year, '%m'));
    $nextMonth->setDay(1);    

    $weekdayOfFirst = $date->getDayOfWeek();
    if ($defaultWeekStart){
      if ($weekdayOfFirst == 0)
        $weekdayOfFirst = 6;
      else
        $weekdayOfFirst--;
    }

    $calStart = $date;

    for ($i = $weekdayOfFirst; $i > 0; $i--)
      $calStart = $calStart->getPrevDay();

    $date->setDay($date->getDaysInMonth());
    $lastWeekday = $date->getDayOfWeek();

    if ($defaultWeekStart){
      if ($lastWeekday == 0)
        $lastWeekday = 6;
      else
        $lastWeekday--;
    }

    $calEnd = $date;
    for ($i = $lastWeekday; $i < 6; $i++)
      $calEnd = $calEnd->getNextDay();

    $calDays = $calStart;

    if ($linkMode == 'event')
      $eventList = PHPWS_Calendar::loadEvents($calStart, $calEnd);

    $rowNumber = 1;
    while ($calEnd->after($calDays) || $calEnd->equals($calDays)){
      $colNumber = $calDays->getDayOfWeek() + 1 - $defaultWeekStart;
      if ($defaultWeekStart && $colNumber == 0)
        $colNumber = 7;


      if (!isset($weekDaysSet))
        $template = PHPWS_Calendar_Display::getDateTemplate($calDays, $colNumber, $template);
      
      if ($linkMode == 'all')
        $rowTpl['COL'.$colNumber] = PHPWS_Text::moduleLink($calDays->format('%d'), 'calendar', array('calendar[view]'=>'day', 'month'=>$calDays->month, 'year'=>$calDays->year, 'day'=>$calDays->day));
      elseif ($linkMode == 'event'){
        if (PHPWS_Calendar::eventExists($calDays, $eventList))
          $rowTpl['COL'.$colNumber] = PHPWS_Text::moduleLink($calDays->format('%d'), 'calendar', array('calendar[view]'=>'day', 'month'=>$calDays->month, 'year'=>$calDays->year, 'day'=>$calDays->day));
        else
          $rowTpl['COL'.$colNumber] = $calDays->format('%d');
      }
      else
        $rowTpl['COL'.$colNumber] = $calDays->format('%d');

      if (Date::compare($calDays, $today) == 0)
        $rowTpl['COL'.$colNumber.'_BG'] = str_replace("\n", "", PHPWS_Template::processTemplate(array("TODAY"=>" "), "calendar", "month/miniMonthSettings.tpl"));
      elseif ($calDays->month != $date->month)
        $rowTpl["COL".$colNumber."_BG"] = str_replace("\n", "", PHPWS_Template::processTemplate(array("OFFMONTH"=>" "), "calendar", "month/miniMonthSettings.tpl"));
      else
        $rowTpl["COL".$colNumber."_BG"] = str_replace("\n", "", PHPWS_Template::processTemplate(array("DEFAULT"=>" "), "calendar", "month/miniMonthSettings.tpl"));

      if ($colNumber == 7){
        if (!empty($rowTpl))
          $template["ROW".$rowNumber] = PHPWS_Template::processTemplate($rowTpl, "calendar", "month/miniMonthRow.tpl");
        $rowTpl = NULL;
        $weekDaysSet = 1;
        $rowNumber++;
      }

      $calDays = $calDays->getNextDay();
    }

    if (!empty($rowTpl))
      $template['ROW'.$rowNumber] = PHPWS_Template::processTemplate($rowTpl, 'calendar', 'month/miniMonthRow.tpl');

    $nextLink = PHPWS_Template::processTemplate(array('NEXT'=>' '), 'calendar', 'month/miniMonthSettings.tpl');
    $prevLink = PHPWS_Template::processTemplate(array('PREV'=>' '), 'calendar', 'month/miniMonthSettings.tpl');


    if ($change && $settings['cacheView']){
      $template['PREV'] = str_replace("\n", '', PHPWS_Text::moduleLink($prevLink, 'calendar', array('calendar[user]'=>'changeBoxMonth', 'month'=>$prevMonth->month, 'year'=>$prevMonth->year)));
      $template['NEXT'] = str_replace("\n", '', PHPWS_Text::moduleLink($nextLink, 'calendar', array('calendar[user]'=>'changeBoxMonth', 'month'=>$nextMonth->month, 'year'=>$nextMonth->year)));
    }

    $miniCal = PHPWS_Template::processTemplate($template, 'calendar', 'month/miniMonth.tpl').'<br />';
    if ($settings['cacheView'])
      PHPWS_Cache::set($miniCal, sprintf('miniMonth_%04d%02d', $year, $month), 'calendar', VIEW_TTL);

    return $miniCal;
  }


  function viewWeek($year=NULL, $month=NULL, $week=NULL){
    PHPWS_Layout::blockRobot();
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }

    $defaultWeekStart = $GLOBALS['core']->datetime->day_start;

    if (is_null($year))
      $year = date('Y');

    if (is_null($month))
      $month = date('m');

    if (is_null($week))
      $begin = date('d');
    else
      $begin = PHPWS_Calendar::getDayFromWeek($month, $year, $week);

    $settings = $GLOBALS['calendarSettings'];

    if ($settings['cacheView'])
      if ($cache = PHPWS_Cache::get(sprintf('week_%04d%02d_%s', $year, $month, $begin['weekDay'])))
        return $cache;

    $date = new Date;
    $date->setMonth($month);
    $date->setYear($year);
    $date->setDay($begin);

    $dayOfWeek = $date->getDayOfWeek();
    $date->subtractSeconds(($dayOfWeek - $defaultWeekStart) * 86400);

    // Note: subtractSeconds changes the value to a character
    // Contact baba
    $date->setDay((int)$date->day);
    $end   = $date;
    $end->addSeconds(6 * 86400);

    $eventList = PHPWS_Calendar::loadEvents($date, $end);

    $count = 0;
    while($end->after($date) || $end->equals($date)){
      $count++;
      $template['EVENTS_'.$count] = NULL;
      $dateTpl = PHPWS_Calendar_Display::getDateTemplate($date);

      $template['DATE_'.$count] = PHPWS_Text::moduleLink(PHPWS_Template::processTemplate($dateTpl, 'calendar', 'week/weekViewDate.tpl'),
                                                         'calendar',
                                                         array('calendar[view]'=>'day',
                                                               'year'=>$date->year,
                                                               'month'=>$date->month,
                                                               'day'=>$date->day));
      if ($events = PHPWS_Calendar::getEvents($date, $eventList)){
        foreach ($events as $time=>$timeIndexedEvent){
          $timeSection = NULL;
          if ($time == -1)
            $time = $timeSection['TIME'] = $_SESSION['translate']->it('All Day');
          else {
            $tempDate = PHPWS_Calendar::formatDateTime($time, $date->format('%Y%m%d'));
            $time = $timeSection['TIME'] = $tempDate['time'];
          }
          
          $eventCount = 0;
          $timeSection['EVENTS'] = NULL;
          foreach ($timeIndexedEvent as $id=>$eventInfo){

            $weekDate = date('Ymd', $date->getDate(DATE_FORMAT_UNIXTIME));
            if ($eventInfo['startDate'] < $weekDate && $eventInfo['endDate'] > $weekDate) {
              $time = $timeSection['TIME'] = $_SESSION['translate']->it('All Day');           
            } else if($eventInfo['startTime'] != '-1' && ($eventInfo['startDate'] < $weekDate && $eventInfo['endDate'] == $weekDate)) {
              $tempDate = PHPWS_Calendar::formatDateTime($eventInfo['endTime'], $date->format('%Y%m%d'));
              $time = $timeSection['TIME'] = $_SESSION['translate']->it('Until ') . $tempDate['time'];        
            }

            $eventCount++;
            $eventtpl = NULL;
            $eventtpl['TIME'] = $time;
            $eventtpl['JS_ICON'] = PHPWS_Calendar_Display::getEventIcon($eventInfo['id']);
            
            $title = $eventInfo['title'];
            if ($eventInfo['eventType'] == 'interval' && $eventInfo['startDate'] < $eventInfo['endDate'])
              $title .= ' ('.$_SESSION['translate']->it('Day').'&nbsp;'.$eventInfo['dayNumber'].')';
            
            $eventtpl['TITLE'] = PHPWS_Text::moduleLink($title, 'calendar', array('module'=>'calendar', 'calendar[view]'=>'event', 'id'=>$eventInfo['id']));

            $timeSection['EVENTS'] .= PHPWS_Template::processTemplate($eventtpl, 'calendar', 'week/weekViewEvents.tpl');
          }
          $template['EVENTS_'.$count] .= PHPWS_Template::processTemplate($timeSection, 'calendar', 'week/weekViewTime.tpl');
        }
      } else
        $template['EVENTS_'.$count] = '<i>'.$_SESSION['translate']->it('No events').'</i>';
      $date = $date->getNextDay();
    }
    $content = PHPWS_Template::processTemplate($template, 'calendar', 'week/weekView.tpl');

    return $content;
  }

  function viewMonth($year=NULL, $month=NULL){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }
    PHPWS_Layout::blockRobot();

    $defaultWeekStart = $GLOBALS['core']->datetime->day_start;
    $template = NULL;

    if (is_null($year))
      $year = date('Y');

    if (is_null($month))
      $month = date('m');

    $settings = $GLOBALS['calendarSettings'];

    if ($settings['cacheView'])
      if ($cache = PHPWS_Cache::get(sprintf('month_%04d%02d', $year, $month)))
        return $cache;

    $date = new Date;
    $today = new Date;

    if ($GLOBALS['core']->datetime->time_dif < 0) {
      $today->subtractSeconds(($GLOBALS['core']->datetime->time_dif) * -1);
    }
    elseif ($GLOBALS['core']->datetime->time_dif > 0) {
      $today->addSeconds($GLOBALS['core']->datetime->time_dif);
    }

    $date->setMonth($month);
    $date->setYear($year);
    $date->setDay(1);

    $prevMonth = new Date;
    $prevMonth->setYear(Date_Calc::beginOfPrevMonth(1, $date->month, $date->year, '%Y'));
    $prevMonth->setMonth(Date_Calc::beginOfPrevMonth(1, $date->month, $date->year, '%m'));
    $prevMonth->setDay(1);

    $nextMonth = new Date;
    $nextMonth->setYear(Date_Calc::beginOfNextMonth(1, $date->month, $date->year, '%Y'));
    $nextMonth->setMonth(Date_Calc::beginOfNextMonth(1, $date->month, $date->year, '%m'));
    $nextMonth->setDay(1);    

    $weekdayOfFirst = $date->getDayOfWeek();
    if ($defaultWeekStart){
      if ($weekdayOfFirst == 0)
        $weekdayOfFirst = 6;
      else
        $weekdayOfFirst--;
    }
      
    $calStart = $date;

    for ($i = $weekdayOfFirst; $i > 0; $i--)
      $calStart = $calStart->getPrevDay();

    $date->setDay($date->getDaysInMonth());
    $lastWeekday = $date->getDayOfWeek();

    if ($defaultWeekStart){
      if ($lastWeekday == 0)
        $lastWeekday = 6;
      else
        $lastWeekday--;
    }

    $calEnd = $date;
    for ($i = $lastWeekday; $i < 6; $i++)
      $calEnd = $calEnd->getNextDay();

    $calDays = $calStart;

    $eventList = PHPWS_Calendar::loadEvents($calStart, $calEnd);

    $rowNumber = 1;
    while ($calEnd->after($calDays) || $calEnd->equals($calDays)){
      $colNumber = $calDays->getDayOfWeek() + 1 - $defaultWeekStart;
      if ($defaultWeekStart && $colNumber == 0)
        $colNumber = 7;
      
      if (!isset($weekDaysSet))
        $template = PHPWS_Calendar_Display::getDateTemplate($calDays, $colNumber, $template);

      $eventCount = 0;
      $daytpl['FILLER'] = $daytpl['EVENTS'] = NULL;

      if ($events = PHPWS_Calendar::getEvents($calDays, $eventList)){
        foreach ($events as $time=>$timeIndexedEvent){
          $timeSection = NULL;
          $timeSection['EVENTS'] = NULL;

          if ($time == -1)
            $eventtpl['TIME'] = $timeSection['TIME'] = $_SESSION['translate']->it('All Day');
          else {
            $tempDate = PHPWS_Calendar::formatDateTime($time, $calDays->format('%Y%m%d'));
            $eventtpl['TIME'] = $timeSection['TIME'] = $tempDate['time'];
          }

          foreach ($timeIndexedEvent as $id=>$eventInfo){
            $eventCount++;
            $eventtpl = NULL;
            
            $weekDate = date('Ymd', $calDays->getDate(DATE_FORMAT_UNIXTIME));
            if ($eventInfo['startDate'] < $weekDate && $eventInfo['endDate'] > $weekDate) {
              $eventtpl['TIME'] = $timeSection['TIME'] = $_SESSION['translate']->it('All Day');       
            } else if($eventInfo['startTime'] != '-1' && ($eventInfo['startDate'] < $weekDate && $eventInfo['endDate'] == $weekDate)) {
              $tempDate = PHPWS_Calendar::formatDateTime($eventInfo['endTime'], $date->format('%Y%m%d'));
              $eventtpl['TIME'] = $timeSection['TIME'] = $_SESSION['translate']->it('Until ') . $tempDate['time'];            
            }

            $eventtpl['JS_ICON'] = PHPWS_Calendar_Display::getEventIcon($id);
            $title = $eventInfo['title'];
            if ($eventInfo['eventType'] == 'interval' && $eventInfo['startDate'] < $eventInfo['endDate'])
              $title .= ' ('.$_SESSION['translate']->it('Day').'&nbsp;'.$eventInfo['dayNumber'].')';
            
            if (isset($eventInfo['isRepeat']) && $eventInfo['isRepeat'] == 1)
              $eventtpl['TITLE'] = PHPWS_Text::moduleLink($title, 'calendar', array('module'=>'calendar', 'calendar[view]'=>'event', 'id'=>$eventInfo['id'], 'date'=>$eventInfo['startDate']));
            else
              $eventtpl['TITLE'] = PHPWS_Text::moduleLink($title, 'calendar', array('module'=>'calendar', 'calendar[view]'=>'event', 'id'=>$eventInfo['id']));

            $timeSection['EVENTS'] .= PHPWS_Template::processTemplate($eventtpl, 'calendar', 'month/monthViewEvents.tpl');
          }
          $daytpl['EVENTS'] .= PHPWS_Template::processTemplate($timeSection, 'calendar', 'month/monthViewTime.tpl');      
        }
      }
      $filler = 5 - $eventCount;
      for ($i = 0; $i < $filler; $i++)
        $daytpl['FILLER'] .= '<br />';
      
      $daytpl['DAY'] = PHPWS_Text::moduleLink($calDays->format('%d'), 'calendar', array('calendar[view]'=>'day', 'year'=>$calDays->year, 'month'=>$calDays->month, 'day'=>$calDays->day));
      $rowTpl['COL'.$colNumber] = PHPWS_Template::processTemplate($daytpl, 'calendar', 'month/monthViewDay.tpl');
      $daytpl = NULL;

      $specialDate = 'DAY_' . $calDays->year.$calDays->format('%m').$calDays->format('%d');
      if ($special = PHPWS_Template::processTemplate(array($specialDate=>' '), 'calendar', 'month/special.tpl')){
        $rowTpl['COL'.$colNumber.'_BG'] = $special;
      }elseif (Date::compare($calDays, $today) == 0)
         $rowTpl['COL'.$colNumber.'_BG'] = str_replace("\n", '', PHPWS_Template::processTemplate(array('TODAY'=>' '), 'calendar', 'month/monthViewSettings.tpl'));
      elseif ($calDays->month != $date->month)
        $rowTpl['COL'.$colNumber.'_BG'] = str_replace("\n", '', PHPWS_Template::processTemplate(array('OFFMONTH'=>' '), 'calendar', 'month/monthViewSettings.tpl'));
      else
        $rowTpl['COL'.$colNumber.'_BG'] = str_replace("\n", '', PHPWS_Template::processTemplate(array('DEFAULT'=>' '), 'calendar', 'month/monthViewSettings.tpl'));

      if ($colNumber == 7){
        if (!empty($rowTpl))
          $template['ROW'.$rowNumber] = PHPWS_Template::processTemplate($rowTpl, 'calendar', 'month/monthViewRow.tpl');
        $rowTpl = NULL;
        $weekDaysSet = 1;
        $rowNumber++;
      }

      $calDays = $calDays->getNextDay();
    }
    if (!empty($rowTpl))
      $template['ROW'.$rowNumber] = PHPWS_Template::processTemplate($rowTpl, 'calendar', 'month/monthViewRow.tpl');

    $nextLink = PHPWS_Template::processTemplate(array('NEXT'=>' '), 'calendar', 'month/monthViewSettings.tpl');
    $prevLink = PHPWS_Template::processTemplate(array('PREV'=>' '), 'calendar', 'month/monthViewSettings.tpl');

    $template['PREV'] = str_replace("\n", '', PHPWS_Text::moduleLink($prevLink, 'calendar', array('calendar[view]'=>'month', 'year'=>$prevMonth->year, 'month'=>$prevMonth->month)));
    $template['NEXT'] = str_replace("\n", '', PHPWS_Text::moduleLink($nextLink, 'calendar', array('calendar[view]'=>'month', 'year'=>$nextMonth->year, 'month'=>$nextMonth->month)));

    $template['WEEK_LINKS'] = PHPWS_Calendar_Display::listWeeks($date->month, $date->year);
    $template['WEEK'] = $_SESSION['translate']->it('Week');
    $monthView = PHPWS_Template::processTemplate($template, 'calendar', 'month/monthView.tpl').'<br />';

    if ($settings['cacheView'])
      PHPWS_Cache::set($monthView, sprintf('month_%04d%02d', $year, $month), 'calendar', VIEW_TTL);

    return $monthView;


  }// End func viewMonth

  function reset(){
    PHPWS_Template::refreshTemplate('calendar');
  }

  function viewYear($year=NULL){
    if (!PHPWS_Calendar::allowView()) {
      return NULL;
    }
    PHPWS_Layout::blockRobot();

    if (is_null($year))
      $year = date('Y');

    $settings = $GLOBALS['calendarSettings'];

    if ($settings['cacheView'])
      if ($cache = PHPWS_Cache::get(sprintf('year_%04d', $year)))
        return $cache;

    $template['YEAR'] = $year;
    $nextYear = $year + 1;
    $prevYear = $year - 1;

    $nextLink = PHPWS_Template::processTemplate(array('NEXT'=>' '), 'calendar', 'year/yearViewSettings.tpl');
    $prevLink = PHPWS_Template::processTemplate(array('PREV'=>' '), 'calendar', 'year/yearViewSettings.tpl');

    $template['PREV'] = str_replace("\n", '', PHPWS_Text::moduleLink($prevLink, 'calendar', array('calendar[view]'=>'year', 'year'=>$prevYear)));
    $template['NEXT'] = str_replace("\n", '', PHPWS_Text::moduleLink($nextLink, 'calendar', array('calendar[view]'=>'year', 'year'=>$nextYear)));

    for($i = 1; $i < 13; $i++)
      $template['MONTH'.$i] = PHPWS_Calendar_Display::miniMonth($i, $year, FALSE, 'all');
    
    $yearView = PHPWS_Template::processTemplate($template, 'calendar', 'year/yearView.tpl');

    if ($settings['cacheView'])
      PHPWS_Cache::set($yearView, sprintf('year_%04d', $year), 'calendar', VIEW_TTL);

    return $yearView;
  }


  function listWeeks($month, $year){
    $defaultWeekStart = $GLOBALS['core']->datetime->day_start;
    $weeks = NULL;

    $date = new Date;
    $date->setMonth($month);
    $date->setYear($year);
    $date->setDay(1);

    $endDate = $date;
    $daysInMonth = $endDate->getDaysInMonth();
    $endDate->setDay($daysInMonth);
    $endDate->addSeconds(86400 * (6 - $endDate->getDayOfWeek()));
    
    $count = 0;
    while ($endDate->after($date) || $endDate->equals($date)){
      $count++;

      if((int)$date->month > $month)
        $weeks .= PHPWS_Text::moduleLink($count, 'calendar', array('calendar[view]'=>'week', 'year'=>$date->year, 'month'=>($date->month - 1), 'week'=>$count)) . '&nbsp;';
      else
        $weeks .= PHPWS_Text::moduleLink($count, 'calendar', array('calendar[view]'=>'week', 'year'=>$date->year, 'month'=>$date->month, 'week'=>$count)) . '&nbsp;';
      $date = PHPWS_Calendar::addWeek($date);
    }

    return $weeks;
  }


  function getEventIcon($id){
    
    $width = 400;
    $height = 400;

    if($_SESSION['OBJ_user']->js_on) {
      $window_array = array(
                            'type'=>'link',
                            'url'=>'./index.php?module=calendar&amp;calendar[view]=minievent&amp;id='.$id,
                            'label'=>'<img src="./images/info.gif" width="10" height="10" alt="" border="0" />',
                            'window_title'=>'calendar',
                            'scrollbars'=>'yes',
                            'width'=>$width,
                            'height'=>$height,
                            'toolbar'=>'no'
                            );
     return PHPWS_WizardBag::js_insert('window', NULL, NULL, NULL, $window_array);
    } else
      return NULL;

  }

  function userSubmit(){
    $content = NULL;
    $form = new EZform;
    $form->add('module', 'hidden', 'calendar');
    $form->add('calendar[user]', 'hidden', 'userEvent');
    $form->add('newEvent', 'submit', $_SESSION['translate']->it('Submit Event'));
    $template = $form->getTemplate();
    unset($template['DEFAULT_SUBMIT']);
    unset($template['DEFAULT_RESET']);
    foreach ($template as $inputVar)
      $content .= $inputVar;

    return $content;
  }

}

?>
