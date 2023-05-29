<?php
if (!isset($GLOBALS['core'])){
  header('location:../../');
  exit();
}

require_once(PHPWS_SOURCE_DIR . 'mod/approval/class/Approval.php');

$CNT_Calendar_Main['title'] = $CNT_Calendar_Main['content'] = NULL;
$calendarSettings = PHPWS_Calendar::getSettings();

/**
 * Command Section
 */
if (isset($_REQUEST['module']) && $_REQUEST['module'] == 'calendar'){

  PHPWS_Calendar::panel();
  if ($calCommand = $_REQUEST['calendar'])
    foreach ($calCommand as $section=>$command);

  // Administrative functions
  if ($section == 'admin' && $_SESSION['OBJ_user']->allow_access('calendar')){

    switch ($command){
    case 'admin_menu':
      $_SESSION['CreateEvent'] = new PHPWS_Calendar_Event;
      PHPWS_Calendar_Forms::createEventForm($_SESSION['CreateEvent']);
      break;

    case 'createEventForm':
      $_SESSION['CreateEvent'] = new PHPWS_Calendar_Event;
      PHPWS_Calendar_Forms::createEventForm($_SESSION['CreateEvent']);
    break;

    case 'createEventAction':
      if (isset($_SESSION['CreateEvent'])){
        if ($_SESSION['CreateEvent']->processEvent()){
          $_SESSION['CreateEvent']->writeEvent();
          $CNT_Calendar_Main['title'] = $_SESSION['translate']->it('Event created successfully').'!';
          $CNT_Calendar_Main['content'] .=  PHPWS_Calendar_Display::viewEvent($_SESSION['CreateEvent']->id);
          $core->killSession('CreateEvent');
          PHPWS_Calendar::resetCache();
        } else {
          $CNT_Calendar_Main['content'] .= $_SESSION['CreateEvent']->printErrors();
          PHPWS_Calendar_Forms::createEventForm($_SESSION['CreateEvent']);
        }
      }
      break;

    case 'editEvent':
      $_SESSION['EditEvent'] = new PHPWS_Calendar_Event((int)$_REQUEST['id']);
      PHPWS_Calendar_Forms::editEventForm($_SESSION['EditEvent']);
      break;

    case 'deleteEvent':
      PHPWS_Calendar_Forms::deleteEventForm((int)$_REQUEST['id'], isset($_REQUEST['confirm']) ? (int)$_REQUEST['confirm'] : NULL);
      PHPWS_Calendar::resetCache();
      break;

    case 'editEventAction':
      if ($_SESSION['EditEvent']){
        if ($_SESSION['EditEvent']->processEvent()){
          $_SESSION['EditEvent']->updateEvent();
          PHPWS_Calendar::resetCache();
          $CNT_Calendar_Main['title'] = $_SESSION['translate']->it('Event updated successfully').'!';
          $CNT_Calendar_Main['content'] =  PHPWS_Calendar_Display::viewEvent($_SESSION['EditEvent']->id);
          $core->killSession('EditEvent');
        } else {
          $CNT_Calendar_Main['content'] .= $_SESSION['EditEvent']->printErrors();
          PHPWS_Calendar_Forms::editEventForm($_SESSION['EditEvent']);
        }
      }
      break;

    case 'settings':
    $CNT_Calendar_Main['title']   = $_SESSION['translate']->it('Calendar Settings');
    $CNT_Calendar_Main['content'] = PHPWS_Calendar_Forms::settings();
      break;

    case 'updateSettings':
      PHPWS_Calendar::updateSettings();
      PHPWS_Calendar::resetCache();
      $CNT_Calendar_Main['title'] = $_SESSION['translate']->it('Calendar Settings');
      $CNT_Calendar_Main['content'] = '<b>' . $_SESSION['translate']->it('Settings Updated') . '!</b>';
      $CNT_Calendar_Main['content'] .= PHPWS_Calendar_Forms::settings();
      break;

    case 'importDeleteCalendarForm':
        PHPWS_Calendar_Forms::importDeleteCalendarForm();
        break;
    case 'importDeleteCalendar':
        if(isset($_REQUEST['importCalendar'])) {
            $CNT_Calendar_Main['content'] = PHPWS_Calendar_Event::importCalendarEvent();
        } else if (isset($_REQUEST['deleteCalendar']) && isset($_REQUEST['delete_checkbox'])) {
            $CNT_Calendar_Main['content'] = PHPWS_Calendar_Event::deleteCalendar();
        } else if (isset($_REQUEST['deleteCalendar']) && !isset($_REQUEST['delete_checkbox'])) {
            $CNT_Calendar_Main['content'] = "You tried to delete a calendar without checking the checkbox!<br />";
            $CNT_Calendar_Main['content'] = "No calendars were deleted.";
        } else {
            $CNT_Calendar_Main['content'] = "ERROR!! No action detected!";
        }
        break;
    default:
      break;
    } // End admin command switch
  }// End admin section
  elseif ($section == 'user'){
    switch ($command){
    case 'changeBoxMonth':
      PHPWS_Calendar_Display::setBoxMonth((int)$_GET['month'], (int)$_GET['year']);
      header('location:./' . preg_replace('/.*(index\.php.*|)$/Ui', '\\1', $_SERVER['HTTP_REFERER']));
      exit();
      break;

    case 'userEvent':
      if ($calendarSettings['userSubmit']){
        $_SESSION['CreateUserEvent'] = new PHPWS_Calendar_Event;
        PHPWS_Calendar_Forms::createUserEventForm($_SESSION['CreateUserEvent']);
      }
      break;

    case 'createUserEventAction':
      if ($_SESSION['CreateUserEvent']){
        if ($_SESSION['CreateUserEvent']->processEvent()){
          $_SESSION['CreateUserEvent']->writeEvent();
          PHPWS_Approval::add($_SESSION['CreateUserEvent']->id, $_SESSION['CreateUserEvent']->title);
          $GLOBALS['core']->killSession('CreateUserEvent');
          PHPWS_Calendar::resetCache();
          $CNT_Calendar_Main['content'] .= $_SESSION['translate']->it('Event submitted for approval').'.';
        } else {
          $CNT_Calendar_Main['content'] .= $_SESSION['CreateUserEvent']->printErrors();
          PHPWS_Calendar_Forms::createUserEventForm($_SESSION['CreateUserEvent']);
        }
      }
      break;

    case 'reset':
      PHPWS_Calendar::resetCache();
    header('location:./' . preg_replace('/.*(index\.php.*|)$/Ui', '\\1', $_SERVER['HTTP_REFERER']));
    exit();
      break;
    } // End user command switch
  }// End user section
  elseif ($section == 'view'){
    $yearSet  = (isset($_REQUEST['year'])) ? (int)$_REQUEST['year'] : NULL;
    $monthSet = (isset($_REQUEST['month'])) ? (int)$_REQUEST['month'] : NULL;
    $daySet   = (isset($_REQUEST['day'])) ? (int)$_REQUEST['day'] : NULL;
    $weekSet   = (isset($_REQUEST['week'])) ? (int)$_REQUEST['week'] : NULL;

    switch ($command){
    case 'year':
      $CNT_Calendar_Main['content'] = PHPWS_Calendar_Display::viewYear($yearSet);
    break;

    case 'month':
      $CNT_Calendar_Main['content'] = PHPWS_Calendar_Display::viewMonth($yearSet, $monthSet);
    break;

    case 'day':
      $CNT_Calendar_Main['content'] = PHPWS_Calendar_Display::viewDay($yearSet, $monthSet, $daySet);
    break;

    case 'week':
      $CNT_Calendar_Main['content'] = PHPWS_Calendar_Display::viewWeek($yearSet, $monthSet, $weekSet);
    break;

    case 'event':
      $eventcontent = PHPWS_Calendar_Display::viewEvent((int)$_REQUEST['id'], (isset($_REQUEST['date'])) ? (int)$_REQUEST['date'] : NULL);
  if (!$eventcontent)
    $CNT_Calendar_Main['content'] = $_SESSION['translate']->it('This event is no longer listed') . '.';
  else
    $CNT_Calendar_Main['content'] = isset($CNT_Calendar_Main['content']) ? $CNT_Calendar_Main['content'] . $eventcontent : $eventcontent;
      break;

    case 'minievent':
      echo PHPWS_Calendar_Display::viewMiniEvent((int)$_REQUEST['id']);
      break;

    }// End view command switch
  }// End view section
}

?>
