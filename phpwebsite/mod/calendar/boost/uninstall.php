<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if(PHPWS_File::rmdir("images/calendar/")) {
  $content .= "The calendar images directory was fully removed.<br />";
} else {
  $content .= "The calendar images directory could not be removed.<br />";
}

if ($GLOBALS['core']->sqlTableExists("mod_calendar_events", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_events");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_repeats", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_repeats");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_settings", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_settings");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_imported", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_imported");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_calendars", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_calendars");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_exported_events", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_exported_events");

$status = 1;

?>
