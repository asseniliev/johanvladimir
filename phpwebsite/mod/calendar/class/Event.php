<?php

/**
 * Controls display and saving of events
 *
 * @version $Id: Event.php,v 1.54 2005/08/24 18:45:53 kevin Exp $
 * @author Matthew McNaney
 */

require_once(PHPWS_SOURCE_DIR . 'core/Text.php');

require_once (PHPWS_SOURCE_DIR . '/mod/calendar/class/Repeat.php');

class PHPWS_Calendar_Event extends PHPWS_Calendar_Repeat{

    var $id;
    var $title;
    var $description;
    var $startTime;
    var $endTime;
    var $startDate;
    var $endDate;
    var $template;
    var $eventType;
    var $groups;
    var $pmChoice;
    var $pmID;
    var $image;
    var $active;
    var $dayNumber;
    var $error;
    var $viewDate;
    var $timestamp;
    var $export;
    var $imported_id;
    var $calendar_name;

    function PHPWS_Calendar_Event ($id=NULL){
        if (!$id)
            return FALSE;

        if (!($event = $GLOBALS['core']->sqlSelect('mod_calendar_events', 'id', $id)))
            return FALSE;

        PHPWS_Array::arrayToObject($event[0], $this);

        if ($this->image){
            $file = explode(':', $this->image);
            $this->image = array();
            $this->image['name'] = $file[0];
            $this->image['width'] = $file[1];
            $this->image['height'] = $file[2];
        }

        if ($this->every)
            $this->every = explode(':', $this->every);

        $this->repeatWeekdays = explode(':', $this->repeatWeekdays);
    }


    function setRepeatVars($endRepeat, $repeatMode, $monthMode=NULL, $repeatWeekdays=NULL, $every=NULL){
        $update['endRepeat'] = $endRepeat;
        $update['repeatMode'] = $repeatMode;

        if ($repeatMode == 'monthly' && !is_null($monthMode))
            $update['monthMode'] = $monthMode;

        if ($repeatMode == 'weekly' && !is_null($repeatWeekdays))
            $update['repeatWeekdays'] = implode(':', $repeatWeekdays);

        if ($repeatMode == 'every' && !is_null($every))
            $update['every'] = implode(':', $every);

        return $GLOBALS['core']->sqlUpdate($update, 'mod_calendar_events', 'id', $this->id);
    }


    function eventDuration($event=NULL){
        if (is_null($event))
            $event = $this;

        $start = PHPWS_Calendar::splitDate($event->startDate);
        $end   = PHPWS_Calendar::splitDate($event->endDate);

        return Date_Calc::dateDiff($start['day'], $start['month'], $start['year'],
                                   $end['day'], $end['month'], $end['year']);
    }


    function getFormattedDateTime(){
        if ($this->eventType == 'allday'){
            $startDate = PHPWS_Calendar::formatDateTime(12, $this->startDate);
            $endDate   = PHPWS_Calendar::formatDateTime(12, $this->endDate);
        } else {
            $startDate = PHPWS_Calendar::formatDateTime($this->startTime, $this->startDate);
            $endDate   = PHPWS_Calendar::formatDateTime($this->endTime, $this->endDate);
        }
        switch ($this->eventType){
            case 'allday':
                $template['START_DATE'] = $startDate['full'];
                $template['START_TIME'] = $template['TIME'] = $_SESSION['translate']->it('All Day');
                if ($startDate['n_full'] == $endDate['n_full']){
                    $template['DATE_TIME'] = $startDate['full'] . '<br />'; 
                }
                else {
                    $template['END_DATE'] = $endDate['full'];
                    $template['DATE_TIME'] = $startDate['full'] . ' to ' .$endDate['full'] . '<br />';
                }
        
                $template['DATE_TIME'] .= $template['ALL_DAY'] = $_SESSION['translate']->it('All Day Event');
            break;
        
            case 'start':
                $template['DATE_TIME'] = $startDate['full'] . '<br />';
                $template['TIME'] = $startDate['time'];
                $template['START_DATE'] = $startDate['full'];
                $template['START_TIME'] = $startDate['time'];
                $template['DATE_TIME'] .= $_SESSION['translate']->it('Starts at') . ' ' . $startDate['time'];
            break;
        
            case 'deadline':
                $template['TIME'] = $endDate['time'];
                $template['END_DATE'] = $endDate['full'];
                $template['DATE_TIME'] = $endDate['full'] . '<br />';
                $template['DATE_TIME'] .= $_SESSION['translate']->it('Deadline at') . ' ' . $endDate['time'];
            break;
        
            case 'interval':
                $template['TIME'] = $startDate['time'] . ' - ' . $endDate['time'];
                $template['START_TIME'] = $startDate['time'];
                $template['END_TIME'] = $endDate['time'];
                $template['START_DATE'] = $startDate['full'];
     
                if ($startDate['n_full'] == $endDate['n_full']){
                    $template['DATE_TIME'] = $startDate['time'] . ' - ' . $endDate['time'] . '<br />';
                    $template['DATE_TIME'] .= $startDate['full']; 
                } else {
                    $template['END_DATE'] = $endDate['full'];
                    $template['DATE_TIME'] = '<b>' . $_SESSION['translate']->it('From') . ':</b> ' . $startDate['time'] . ', ' .$startDate['full'];
                    $template['DATE_TIME'] .= '<br /><b>' . $_SESSION['translate']->it('To') . ':</b> ' . $endDate['time'] . ', ' . $endDate['full'];
                }

                if ($this->startDate < $this->viewDate && $this->endDate > $this->viewDate) {
                    $template['START_TIME'] = $_SESSION['translate']->it('All Day');              
                } else if($startDate['time'] != '-1' && ($this->startDate < $this->viewDate && $this->endDate == $this->viewDate)) {
                    $template['START_TIME'] = $_SESSION['translate']->it('Until ') . $endDate['time'];
                }
            break;
        }

        return $template;
    }


    function checkCalendarUpdates() {

        if(!$GLOBALS['core']->sqlTableExists('mod_calendar_imported', TRUE)) {
            return;
        }

        $frequency  = 15;
        $benchmark  = time() - ($frequency * 60);
        $sources    = $GLOBALS['core']->sqlSelect('mod_calendar_imported', 'time', $benchmark, NULL, '<');
        if($sources != FALSE && $sources != NULL) {
            foreach($sources as $source) {
                PHPWS_Calendar_Event::updateImported($source);
            }
        }
    }

    function updateImported($source) {
        $GLOBALS['core']->sqlDelete('mod_calendar_events', 'imported_id', $source['id']);
        PHPWS_Calendar_Event::importCalendarEvent($source['url']);
    }
    
    function processEvent(){
        $image_directory = 'images/calendar/';
        extract($_POST);

        if (checkdate($cal_startDate_month, $cal_startDate_day, $cal_startDate_year))
            $startDate = PHPWS_Calendar::buildDate($cal_startDate_month, $cal_startDate_day, $cal_startDate_year);
        else
            $this->error[] = $_SESSION['translate']->it('Invalid start date') . '.';

        if (checkdate($cal_endDate_month, $cal_endDate_day, $cal_endDate_year))
            $endDate   = PHPWS_Calendar::buildDate($cal_endDate_month, $cal_endDate_day, $cal_endDate_year);
        else
            $this->error[] = $_SESSION['translate']->it('Invalid end date') . '.';

        if ($cal_title)
            $this->title = PHPWS_Text::parseInput($cal_title);
        else
            $this->error[] = $_SESSION['translate']->it('Missing Title');

        if (isset($cal_active))
            $this->active = $cal_active;
        else
            $this->active = 0;
    
        $this->description = PHPWS_Text::parseInput($cal_description);
        $this->template    = $cal_template;
        $this->eventType   = $cal_eventType;

        if ($_SESSION['OBJ_user']->allow_access('calendar')) {
            if (isset($_FILES['NEW_IMAGE']['name']) && !empty($_FILES['NEW_IMAGE']['name'])){
                $image = EZform::saveImage('NEW_IMAGE', $image_directory, 1024, 1000);

            if (PHPWS_Error::isError($image)){
                $image->message('CNT_Calendar_Main');
                $this->error[] = $_SESSION['translate']->it('Image not saved') . '.';
            } else
                $this->image = $image;
        }
        elseif (isset($CURRENT_IMAGE) && $CURRENT_IMAGE != 'none'){
            if (isset($REMOVE_IMAGE)){
                @unlink($image_directory . $CURRENT_IMAGE);
                $this->error[] = $_SESSION['translate']->it('Image deleted') . '.';
            } else {
                $oldImage['name'] = $CURRENT_IMAGE;
                $size = getimagesize($image_directory . $CURRENT_IMAGE);
                $oldImage['width'] = $size[0];
                $oldImage['height'] = $size[1];
                $this->image = $oldImage;
            }
        } else
            $this->image = NULL;
        }
    

        if ($this->eventType == 'allday'){
            $this->startTime = -1;
            $this->endTime   = 9999;
        } else {
            if(!isset($cal_startTime_ampm))
                $cal_startTime_ampm = NULL;

            if(!isset($cal_endTime_ampm))
                $cal_endTime_ampm = NULL;

            $this->startTime   = PHPWS_Calendar::formatTime($cal_startTime_hour, $cal_startTime_minute, $cal_startTime_ampm);
            $this->endTime     = PHPWS_Calendar::formatTime($cal_endTime_hour, $cal_endTime_minute, $cal_endTime_ampm);
        }

        if (isset($startDate))
            $this->startDate   = $startDate->format('%Y%m%d');

        if (isset($endDate))
            $this->endDate     = $endDate->format('%Y%m%d');


        $this->template    = $cal_template;
        $this->eventType   = $cal_eventType;

        if (isset($viewGroups))
            $this->groups    = $viewGroups;

        if (isset($cal_pmChoice))
            $this->pmChoice  = $cal_pmChoice;

        if (isset($cal_pmID))
            $this->pmID      = $cal_pmID;
   
        if (isset($startDate) && isset($endDate)){
            if ($this->eventType == 'deadline'){
                $this->startTime = $this->endTime;
                $this->startDate = $this->endDate;
            }
      
            if ($this->eventType == 'start'){
                $this->endTime = $this->startTime;
                $this->endDate = $this->startDate;
            }

            if ($this->endDate < $this->startDate)
                $this->error[] = $_SESSION['translate']->it('The Start Date must be less than the End Date') . '.';
    
            if ($this->eventType == 'interval' && ($this->endTime <= $this->startTime) && ($this->endDate == $this->startDate))
                $this->error[] = $_SESSION['translate']->it('The End Time must be greater than the Start Time on a single day, interval event') . '.';
        }
        
        if(isset($_REQUEST['export'])) {
            $this->export = '1';
            if(isset($new_calendar_name) && $new_calendar_name) {
                $new_calendar_name = ltrim($new_calendar_name);
                $new_calendar_name = rtrim($new_calendar_name);
                if($new_calendar_name != NULL) {
                    $search = array("/ /", "/'/");
                    $replace = array("_", "");
                    $new_calendar_filename = preg_replace($search, $replace, $new_calendar_name);
                    $this->calendar_name = $new_calendar_name;
                    $insert_calendar['title']      = $new_calendar_name;
                    $insert_calendar['filename']   = $new_calendar_filename . ".ics";
                    $insert_calendar['created_by'] = $_SESSION["OBJ_user"]->getUsername();
                    $insert_calendar['created_on'] = time();
                    $result = $GLOBALS['core']->sqlSelect('mod_calendar_calendars', 'title', $insert_calendar['title']);
                    if($result != NULL && $result != FALSE) {
                        $this->error[] = $_SESSION['translate']->it('You entered a calendar name that already exists') . '!';
                    } else {
                        $GLOBALS['core']->sqlInsert($insert_calendar, 'mod_calendar_calendars');
                    }
                } else {
                    $this->error[] = $_SESSION['translate']->it('Invalid Calendar Name');
                }
            } else {
                $this->calendar_name = $existing_calendar_name;
            }
        } else {
            $this->export = '0';
        }

        $this->generateTimestamp();
               
        $this->processRepeats();

        if ($this->error)
            return FALSE;
        else {
            if(isset($_REQUEST['cal_post_announcement'])) 
                $this->postAnnouncement();

            return TRUE;
        }
    }

    function postAnnouncement() {
        require_once(PHPWS_SOURCE_DIR . 'mod/announce/class/Announcement.php');

        if($GLOBALS['core']->moduleExists('announce')) {
            // save subject
            $ann['subject']  = $this->title;

            // save image
            if(is_array($this->image) && !empty($this->image)) {
                if(!file_exists('images/announce'.$this->image['name'])) {
                    PHPWS_File::fileCopy('images/calendar/'.$this->image['name'], 
                                         'images/announce/',
                                         $this->image['name'], TRUE, FALSE);
                }
                $ann['image']    = serialize($this->image);
            }

            // save summary
            if($repeatTemplate = PHPWS_Calendar_Display::getRepeatTemplate($this)) {
                $annTags['DATE_TIME'] = $repeatTemplate['REPEAT'];
            } else if($dayTemplate = $this->getFormattedDateTime($this)) {
                $dateTime   = array();
                $dateTime[] = $_SESSION['translate']->it('Event occurs on ');
                $dateTime[] = $dayTemplate['START_DATE'];
                $dateTime[] = $_SESSION['translate']->it(' from ');
                $dateTime[] = $dayTemplate['START_TIME'];
                $dateTime[] = $_SESSION['translate']->it(' to ');
                $dateTime[] = $dayTemplate['END_TIME'] . '.';
                $annTags['DATE_TIME'] = implode('', $dateTime);
            }
            $annTags['DESCRIPTION'] = $this->description;
            $ann['summary']  = PHPWS_Template::processTemplate($annTags, 'calendar', 'event/ann.tpl');

            PHPWS_Announcement::extModSave($ann, 'calendar');
        }
    }

    function writeEvent(){
        if ($this->groups)
            $insert['groups']    = implode(':', $this->groups);

        $insert['active']      = $this->active;
        $insert['title']       = $this->title;
        $insert['description'] = $this->description;
        if ($this->image)
            $insert['image']       = implode(':', $this->image);

        $template = preg_replace('/[^\.\w]+/i', '', $this->template);

        if (is_file(PHPWS_SOURCE_DIR . "mod/calendar/templates/event/$template"))
            $insert['template'] = $template;
        else
            $insert['template'] = 'default.tpl';
      
        $insert['eventType']   = $this->eventType;

        if ($this->eventType=='deadline')
            $insert['startTime'] = $this->endTime;
        else
            $insert['startTime'] = $this->startTime;

        $insert['startTime']   = $this->startTime;
        $insert['endTime']     = $this->endTime;
        $insert['startDate']   = $this->startDate;
        $insert['endDate']     = $this->endDate;

        if ($this->pmChoice)
            $insert['pmChoice']    = $this->pmChoice;
        if ($this->pmID)
            $insert['pmID']        = $this->pmID;

        $insert['timestamp'] = $this->timestamp;

        $insert['export']    = $this->export;

        if($this->imported_id)
            $insert['imported_id'] = $this->imported_id;

        if ($this->id = $GLOBALS['core']->sqlInsert($insert, 'mod_calendar_events', NULL, TRUE)){
            $this->removeRepeats($this->id);
            if ($this->repeatMode)
                $this->repeatEvent($this->active);

            $link = 'index.php?module=calendar&calendar[view]=event&id=' . $this->id;
            if ($this->active)
                $_SESSION['OBJ_fatcat']->saveSelect($this->title, $link, $this->id, $this->groups, 'calendar');
            else
                $_SESSION['OBJ_fatcat']->saveSelect($this->title, $link, $this->id, $this->groups, 'calendar', NULL, NULL, FALSE);
      
            if($this->export == '1') {
                $this->update_exports();
                $this->exportCalendar();
            }
     
            return TRUE;
        } else 
            return FALSE;
    }

    function update_exports() {
        $calendar['event_id'] = $this->id;
        $calendar['exported_to'] = $this->calendar_name;
        $result = $GLOBALS['core']->sqlSelect('mod_calendar_exported_events', 'event_id', $this->id);
        if($result == NULL || $result == FALSE) {
            $result = $GLOBALS['core']->sqlInsert($calendar, 'mod_calendar_exported_events');
        } else {
            $result = $GLOBALS['core']->sqlUpdate($calendar, 'mod_calendar_exported_events');
        }
    }
    
    function exportCalendar($calendar_name=NULL){
        $write_dir = PHPWS_SOURCE_DIR . 'files/calendar/';
     
        if($calendar_name != NULL) {
            if(!($filename = $GLOBALS['core']->sqlSelect('mod_calendar_calendars', 'title', $calendar_name))) {
                return;
            }
        } else {
            if(!($filename = $GLOBALS['core']->sqlSelect('mod_calendar_calendars', 'title', $this->calendar_name))) {
                return;
            }
        } 
     
        $outfile = ""; 
        $cal_title = "";
        foreach($filename as $file) {
            $outfile = $file["filename"];
            $cal_title  = $file["title"];
        }

        $file = $write_dir . $outfile;
        
        /* if the calendar file for this site does not exist
         * then create one, writing the appropriate "basic" headers
         */
        if(file_exists($file) && !is_writable($file)) {
            /* if we are at this point the calendar file exists but
             * is not writable
             */
            $this->error[] = $_SESSION['translate']->it('calendar file not writable') . '!<br />';
            $this->error[] .= $_SESSION['translate']->it('event not exported') . '!';
            return;
        } else {
            $event_ids = $GLOBALS['core']->sqlSelect('mod_calendar_exported_events', 'exported_to', $cal_title);
            $cal_events = array();
            foreach($event_ids as $event_id) {
                array_push($cal_events, $GLOBALS['core']->sqlSelect('mod_calendar_events', 'id', $event_id['event_id']));
            }
            if($cal_events == NULL || $cal_events == FALSE) {
                $this->error[] = $_SESSION['translate']->it('No calendar events marked for export') . '!<br />';
                if(file_exists($file)) {
                    unlink($file);
                }
                return;
            }

            $file_handle = fopen($file, "w");
            $headers     = "BEGIN:VCALENDAR\r\n";
            $headers    .= "VERSION:1.0\r\n";
            $headers    .= "X-WR-CALNAME:{$_SERVER['HTTP_HOST']} Calendar\r\n";
            $headers    .= "CALSCALE:GREGORIAN\r\n";
            fwrite($file_handle, $headers);

            $num_events = count($cal_events);
           
            $cal_contents = NULL;

            foreach($cal_events as $event_array) {
                foreach($event_array as $event) {
                    $title = PHPWS_Text::parseOutput($event['title']);
                    $title = preg_replace('/<br \/>\n/m', '\n', $title);
                    $title = PHPWS_Calendar_Event::fixReservedVCalTags($title);

                    $desc  = PHPWS_Text::parseOutput($event['description']);
                    $desc  = preg_replace('/<br \/>\n/m', '\n', $desc);
                    $desc  = preg_replace('/\r\n/', '\n', $desc);
                    $desc  = PHPWS_Calendar_Event::fixReservedVCalTags($desc);

                    $start_time = $event['startTime'];
                    if(strlen($start_time) < 4) {
                        $start_time = str_pad($start_time, 4, "0", STR_PAD_LEFT);
                    }

                    $end_time   = $event['endTime'];
                    if(strlen($end_time) < 4) {
                        $end_time = str_pad($end_time, 4, "0", STR_PAD_LEFT);
                    }

                    $cal_contents .= "BEGIN:VEVENT\r\n";
                    $cal_contents .= "SUMMARY:"     . $title                . "\r\n";
                    $cal_contents .= "DESCRIPTION:" . $desc                 . "\r\n";
                    $cal_contents .= "DTSTART:"     . $event['startDate']   . "T"       . $start_time  . "00\r\n";
                    $cal_contents .= "DTEND:"       . $event['endDate']     . "T"       . $end_time    . "00\r\n";
                    $cal_contents .= "DTSTAMP:"     . $event['timestamp']   . "\r\n";
                    $cal_contents .= "END:VEVENT\r\n";
                    fwrite($file_handle, $cal_contents);
                    $cal_contents = NULL;
                }
            }
             
            $cal_contents .= "END:VCALENDAR\r\n";
            fwrite($file_handle, $cal_contents);
            fclose($file_handle);
        }
    }

    function updateEvent(){
        if ($this->groups)
            $update['groups']    = implode(':', $this->groups);

        $update['active']      = $this->active;
        $update['title']       = $this->title;
        $update['description'] = $this->description;

        if ($this->image)
            $update['image']       = implode(':', $this->image);
        else
            $update['image']       = NULL;

        $template = preg_replace('/[^\.\w]+/i', '', $this->template);

        if (is_file(PHPWS_SOURCE_DIR . 'mod/calendar/templates/event/' . $template))
            $update['template'] = $template;
        else
            $update['template'] = 'default.tpl';

        $update['eventType']   = $this->eventType;

        if ($this->eventType=='deadline')
            $update['startTime'] = $this->endTime;
        else
            $update['startTime'] = $this->startTime;

        $update['endTime']     = $this->endTime;
        $update['startDate']   = $this->startDate;
        $update['endDate']     = $this->endDate;

        if ($this->pmChoice)
            $update['pmChoice']    = $this->pmChoice;
        if ($this->pmID)
            $update['pmID']        = $this->pmID;

        if ($this->export)
            $update['export']      = $this->export;

        if ($GLOBALS['core']->sqlUpdate($update, 'mod_calendar_events', 'id', $this->id)){
            $this->removeRepeats($this->id);

            if ($this->repeatMode)
                $this->repeatEvent($this->active);
            else
                $this->clearRepeatsFromEvent();

            $link = 'index.php?module=calendar&calendar[view]=event&id=' . $this->id;
            if ($this->active)
                $_SESSION['OBJ_fatcat']->saveSelect($this->title, $link, $this->id, $this->groups, 'calendar');
            else
                $_SESSION['OBJ_fatcat']->saveSelect($this->title, $link, $this->id, $this->groups, 'calendar', NULL, NULL, FALSE);

            if(isset($_REQUEST['lay_quiet']))
                PHPWS_Calendar_Display::viewMiniEvent($this->id);

            if($this->export == 1) {            
                $this->update_exports();
                $this->exportCalendar();
            }

            return TRUE;
        } else 
            return FALSE;
    }

    function printErrors(){
        $content = NULL;
        if ($this->error){
            foreach ($this->error as $error)
            $content .= '<span class="errortext">' . $error . '</span><br />' . "\n";

            unset($this->error);

            return $content;
        }
    }

    function deleteEvent($id){
        $calendar_name = PHPWS_Calendar_Event::get_calendar_name($id);
        $GLOBALS['core']->sqlDelete('mod_calendar_events', 'id', $id);
        $GLOBALS['core']->sqlDelete('mod_calendar_repeats', 'id', $id);
        $GLOBALS['core']->sqlDelete('mod_calendar_exported_events', 'event_id', $id);
        PHPWS_Fatcat::purge($id, 'calendar');
        if($calendar_name != NULL) {
            PHPWS_Calendar_Event::exportCalendar($calendar_name);
        }
    }

    function get_calendar_name($id) {
        $calendars = $GLOBALS['core']->sqlSelect('mod_calendar_exported_events','event_id',$id);
        if($calendars == NULL || $calendars == FALSE) {
            return NULL;
        }
        $calendar_name = "";
        foreach ($calendars as $calendar) {
            $calendar_name = $calendar['exported_to'];
        }
        return $calendar_name;
    }

    function setTitle($title){
        if (empty($title))
            return FALSE;

        $this->title = PHPWS_Text::parseInput($title);
        return TRUE;
    }

    function setDesc($description){
        $this->description = PHPWS_Text::parseInput($description);
        return TRUE;
    }

    function setStartTime($hour, $minute, $ampm=NULL){
        $this->startTime = PHPWS_Calendar::formatTime($hour, $minute, $ampm);
        return TRUE;
    }

    function setEndTime($hour, $minute, $ampm=NULL){
        $this->endTime = PHPWS_Calendar::formatTime($hour, $minute, $ampm);
        return TRUE;
    }

    function fixReservedVCalTags($text){
        $search     = array('/^BEGIN:VCALENDAR/m',
                            '/^BEGIN:VEVENT/m',
                            '/^DTSTART:/m',
                            '/^DTEND:/m',
                            '/^DTSTAMP:/m',
                            '/^END:VEVENT/m',
                            '/^END:VCALENDAR/m');

        $replace    = array(' BEGIN:VCALENDAR',
                            ' BEGIN:VEVENT',
                            ' DTSTART:',
                            ' DTEND:',
                            ' DTSTAMP:',
                            ' END:VEVENT',
                            ' END:VCALENDAR');

        $text = preg_replace($search, $replace, $text);
        return $text;
    }

    function generateTimestamp(){
        $unix_timestamp   = localtime(time(), true);
        $today            = date('Ymd');
        $hour             = $unix_timestamp['tm_hour'];
        $minute           = $unix_timestamp['tm_min'];
        $second           = $unix_timestamp['tm_sec'];

        if(strlen($hour) < 2)   $hour   = str_pad($hour,   2, "0", STR_PAD_LEFT);
        if(strlen($minute) < 2) $minute = str_pad($minute, 2, "0", STR_PAD_LEFT);
        if(strlen($second) < 2) $second = str_pad($second, 2, "0", STR_PAD_LEFT);

        $this->timestamp  =  $today . "T" . "$hour" . "$minute" . "$second";
    }

    function importCalendarEvent($url = NULL) {
        $content = NULL;
        $ctr = 0;
        
        if($url == NULL) {
            $location = $_REQUEST['cal_url'];
        } else {
            $location = $url;
        }
        
        $index = NULL;

        if(!($file = @fopen($location, "r"))) {
            $content = "<H1>A calendar file does not exist at {$location}!</H1>";
            return $content;
        }
     
        while(!feof($file)) {
            $contents = fgets($file);
            while($contents != "BEGIN:VEVENT\r\n") {
                $contents = fgets($file);
                if(substr($contents,0,13) == "X-WR-CALNAME:") {
                    $calendar['name']   = rtrim(substr($contents, 13));
                    $calendar['url']    = $location;
                    $calendar['time']   = time();

                    $result             = $GLOBALS['core']->sqlSelect('mod_calendar_imported', 'name', $calendar['name']);

                    if($result == NULL || $result == FALSE) {
                        $GLOBALS['core']->sqlInsert($calendar, 'mod_calendar_imported');
                    }

                    $results = $GLOBALS['core']->sqlSelect('mod_calendar_imported', 'name', $calendar['name']);
                    foreach($results as $result) {
                        $index = $result['id'];
                    }
                    $result['time']    = $calendar['time'];
                    $GLOBALS['core']->sqlUpdate($result, 'mod_calendar_imported', 'name', $calendar['name']);
                    $GLOBALS['core']->sqlDelete('mod_calendar_events', 'imported_id', $result);
                }
            }

            
            for($ctr = 0; $contents != "END:VCALENDAR\r\n"; $ctr++) {
                $event = new PHPWS_Calendar_Event;
                $event->active        = 1;
                $event->export        = 0;
                $event->template      = "miniEventView.tpl";
                $event->eventType     = "interval";
                $event->imported_id   = $index;
                $event->endRepeat     = 0;
                $event->pmID          = 0;
           
                $contents               = fgets($file);
                $event->title           = substr($contents, 8);
                $event->title           = preg_replace('/\r\n/', "", $event->title);
                $event->title           = preg_replace('/\n/', '\r\n', $event->title);
           
                $contents               = fgets($file);
                $event->description     = substr($contents, 12);
                $event->description     = rtrim($event->description);
           
                $contents               = fgets($file);
                $contents               = ltrim($contents, 'DTSTART:');
                $event->startDate       = (int)substr($contents, 0, 8);
                $event->startTime       = (int)substr($contents, 9, 4);
           
                $contents               = fgets($file);
                $contents               = ltrim($contents, 'DTEND:');
                $event->endDate         = (int)substr($contents, 0, 8);
                $event->endTime         = (int)substr($contents, 9, 4);
           
                $contents               = fgets($file);
                $event->timestamp       = substr($contents, 8);

                $event->writeEvent();

                $contents               = fgets($file);
                if($contents == "END:VEVENT\r\n") {
                    $contents = fgets($file);
                }
            }
        }
        $content = "Calendar successfully imported $ctr events!";
        return $content;
    }

    function deleteCalendar() {
        $filename    = "";
       
        // get the filename to unlink 
        $title = $_REQUEST['calendar_name'];
        $results = $GLOBALS['core']->sqlSelect('mod_calendar_calendars', 'title', $title);
        foreach($results as $result) {
            $filename       = $result['filename'];
        }

        // change all of the exported events to non-exported
        $results = $GLOBALS['core']->sqlSelect('mod_calendar_exported_events', 'exported_to', $title);
        if($results != NULL && $results != FALSE) {
            foreach($results as $result) {
                 $event_id = $result['event_id'];
                 $events = $GLOBALS['core']->sqlSelect('mod_calendar_events', 'id', $event_id);
                 if($events != NULL && $events != FALSE) {
                    foreach($events as $event) {
                        $event['export'] = '0';
                        $result = $GLOBALS['core']->sqlUpdate($event, 'mod_calendar_events', 'id', $event['id']);
                    }
                 }
            }
        }
       
        // delete from exported_events table
        $results = $GLOBALS['core']->sqlDelete('mod_calendar_exported_events', 'exported_to', $title);

        // delete from calendars table
        $results = $GLOBALS['core']->sqlDelete('mod_calendar_calendars', 'title', $title);
       
        // delete export file
        $deletion = @unlink(PHPWS_SOURCE_DIR . 'files/calendar/' . $filename);

        return "Calendar file deleted!";
    }
}

?>
