<?php

require_once(PHPWS_SOURCE_DIR . 'mod/calendar/class/Calendar.php');

/***************************************************/
/* Create Calendar box                             */
/***************************************************/

$calendarSettings = PHPWS_Calendar::getSettings();

if(($calendarSettings['userSubmit'] == 1) || ($calendarSettings['minimonth'] == 1) || ($calendarSettings['daysAhead'] > 0)) {
  require_once(PHPWS_SOURCE_DIR . 'mod/calendar/class/Display.php');
  require_once(PHPWS_SOURCE_DIR . 'mod/approval/class/Approval.php');
}

if (PHPWS_Calendar::allowView()) {
  if ($calContent = PHPWS_Calendar_Display::showUserBox()){
    $CNT_Calendar_Box["title"] = $_SESSION["translate"]->it("Calendar");
    $CNT_Calendar_Box["content"] = $calContent;
  }
}

?>