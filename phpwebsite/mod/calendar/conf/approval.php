<?php
if ($_SESSION["OBJ_user"]->allow_access("calendar")){
  require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Calendar.php");
  require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Event.php");
  $event = new PHPWS_Calendar_Event($id);
  if ($approvalChoice == "yes"){
    $event->active = 1;
    $event->updateEvent();
    PHPWS_Fatcat::activate($id, "calendar");
  }

  elseif ($approvalChoice == "no")
    $event->deleteEvent($event->id);

  elseif ($approvalChoice == "view") {
    require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Display.php");
    echo PHPWS_Calendar_Display::viewMiniEvent($id);
  }
}

?>