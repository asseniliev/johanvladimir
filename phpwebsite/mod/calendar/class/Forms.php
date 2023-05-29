<?php

/**
 * Controls event creation forms
 *
 * @version $Id: Forms.php,v 1.54 2005/08/24 18:45:53 kevin Exp $
 * @author Matthew McNaney
 */
require_once(PHPWS_SOURCE_DIR . 'core/EZform.php');

require_once(PHPWS_SOURCE_DIR . 'core/Form.php');

require_once(PHPWS_SOURCE_DIR . 'core/File.php');

require_once(PHPWS_SOURCE_DIR . 'core/WizardBag.php');

require_once(PHPWS_SOURCE_DIR . 'mod/help/class/CLS_help.php');

define('MAX_AHEAD', 14);

class PHPWS_Calendar_Forms{

    function deleteEventForm($id, $confirm=0){
        if (!$confirm){
            $GLOBALS['CNT_Calendar_Main']['title'] = $_SESSION['translate']->it('Delete Confirmation');
            $GLOBALS['CNT_Calendar_Main']['content'] .= $_SESSION['translate']->it('Are you certain you want to delete this event') . '?<br />';
            $GLOBALS['CNT_Calendar_Main']['content'] .= PHPWS_Text::moduleLink($_SESSION['translate']->it('Yes'), 'calendar', array('calendar[admin]'=>'deleteEvent', 'id'=>$id, 'confirm'=>1))
           .    ' <a href="'.$_SERVER['HTTP_REFERER'].'">'.$_SESSION['translate']->it('No').'</a>';
        } else {
            PHPWS_Calendar_Event::deleteEvent($id);
            $GLOBALS['CNT_Calendar_Main']['title'] .= $_SESSION['translate']->it('Event Deleted');
        }
  
    }
  
    function createEventForm($event){
        if (!isset($GLOBALS['CNT_Calendar_Main']['content']))
            $GLOBALS['CNT_Calendar_Main']['content'] = NULL;
  
        $template['EVENT_DATA'] = PHPWS_Calendar_Forms::eventDataForm($event);
        $template['REPEAT']     = PHPWS_Calendar_Forms::repeatForm($event);
  
        if($GLOBALS['core']->moduleExists('announce') && $_SESSION['OBJ_user']->allow_access('announce', 'edit_announcement')) {      
            $template['ANNOUNCE'] = PHPWS_Calendar_Forms::announce();
        }
  
        $template['EVENT_SUBMIT'] = $template['REPEAT_SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it('Create Event'));
  
        $content = 
            '
            <form name="createEvent" action="index.php" method="post" enctype="multipart/form-data">'
            . PHPWS_Form::formHidden(array('module'=>'calendar', 'calendar[admin]'=>'createEventAction'));
        $content .= PHPWS_Template::processTemplate($template, 'calendar', 'admin/eventForm.tpl');
        $content .= '</form>';
  
        $GLOBALS['CNT_Calendar_Main']['title'] = $_SESSION['translate']->it('Create New Event');
        $GLOBALS['CNT_Calendar_Main']['content'] .= $content;
  
    }
  
    function createUserEventForm($event){
        $template['EVENT_DATA'] = PHPWS_Calendar_Forms::eventDataForm($event, TRUE);
        $template['EVENT_SUBMIT']     = PHPWS_Form::formSubmit($_SESSION['translate']->it('Create Event'));
  
        $content = 
            '
            <form name="createEvent" action="index.php" method="post" enctype="multipart/form-data">'
            . PHPWS_Form::formHidden(array('module'=>'calendar', 'calendar[user]'=>'createUserEventAction'));
        $content .= PHPWS_Template::processTemplate($template, 'calendar', 'admin/eventForm.tpl');
        $content .= '</form>';
  
        $GLOBALS['CNT_Calendar_Main']['title'] = $_SESSION['translate']->it('Create New Event');
        $GLOBALS['CNT_Calendar_Main']['content'] .= $content;
  
    }
  
    function editEventForm($event){
        if (!isset($GLOBALS['CNT_Calendar_Main']['content']))
            $GLOBALS['CNT_Calendar_Main']['content'] = NULL; 
  
        if (!$event->id){
            $GLOBALS['CNT_Calendar_Main']['title'] = $_SESSION['translate']->it('Update Event');
            $GLOBALS['CNT_Calendar_Main']['content'] .= $_SESSION['translate']->it('This event is no longer listed') . '.';
            return;
        }
  
        $template['EVENT_DATA'] = PHPWS_Calendar_Forms::eventDataForm($event);
        $template['REPEAT']     = PHPWS_Calendar_Forms::repeatForm($event);
        $template['REPEAT_SUBMIT'] = $template['EVENT_SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it('Update Event'));
  
        if($GLOBALS['core']->moduleExists('announce') && $_SESSION['OBJ_user']->allow_access('announce', 'edit_announcement'))       
            $template['ANNOUNCE'] = PHPWS_Calendar_Forms::announce();
  
        $content = 
            ' <form name="createEvent" action="index.php" method="post" enctype="multipart/form-data">'
            . PHPWS_Form::formHidden(array('module'=>'calendar', 'calendar[admin]'=>'editEventAction'));
        $content .= PHPWS_Template::processTemplate($template, 'calendar', 'admin/eventForm.tpl');
  
        if(isset($_REQUEST['lay_quiet'])) {
            $content .= PHPWS_Form::formHidden('lay_quiet', 1) . '</form>';
            PHPWS_Approval::viewInApprovalWin($content, $_SESSION['translate']->it('Edit Event'));
            return;
        }
  
        $content .= '</form>';
  
        $GLOBALS['CNT_Calendar_Main']['title'] = $_SESSION['translate']->it('Update Event');
        $GLOBALS['CNT_Calendar_Main']['content'] .= $content;
  
    }
  
    function announce() {
        require_once(PHPWS_SOURCE_DIR . 'mod/announce/class/Announcement.php');
        $tags['ANN_CONFIRM']     = PHPWS_Form::formCheckBox('cal_post_announcement');
        $tags['ANN_CONFIRM_LBL'] = $_SESSION['translate']->it('Post event as an announcement');
        $tags['ANN_CONTENT'] = PHPWS_Announcement::extModForm();
  
        return PHPWS_Template::processTemplate($tags, 'calendar', 'admin/annForm.tpl');
    }
  
    function eventDataForm($event, $userForm=FALSE){
        $image_directory = PHPWS_HOME_DIR.'images/calendar';
  
        if ($userForm)
            $event->active = 0;
        else {
            if (!isset($event->active))
            $event->active = 1;
        }
  
        if (is_null($event->eventType))
            $event->eventType ='interval';
      
        if ($event->eventType == 'allday' && $event->startTime == -1){
            $event->startTime = '1300';
            $event->endTime = '1300';
        }
        $form = new EZform;
  
        if ($_SESSION['OBJ_user']->allow_access('calendar')) {
            if (!($form->imageForm(NULL, NULL, $event->image['name'])))
                $template['IMAGE_LABEL'] = $_SESSION['translate']->it('Image') . CLS_help::show_link('calendar', 'noImage');
            else {
                $template = $form->getTemplate();
                $template['IMAGE_LABEL'] = $_SESSION['translate']->it('Image');
            }
        }
  
        if ($GLOBALS['core']->moduleExists('fatcat')){
            $template['CAT_LIST'] = $_SESSION['OBJ_fatcat']->showSelect($event->id, 'multiple');
            $template['CAT_TITLE'] = $_SESSION['translate']->it('Categories');
        }
  
        $template['TEMPLATE_TITLE'] = $_SESSION['translate']->it('Template');
        $templateDir = PHPWS_SOURCE_DIR . 'mod/calendar/templates/event';
        if ($templateFiles = PHPWS_File::readDirectory($templateDir, FALSE, TRUE, FALSE, array('tpl')))
            $template['TEMPLATE_FORM'] = PHPWS_Form::formSelect('cal_template', $templateFiles, $event->template, TRUE);
        else
            exit('Error in eventDataForm: Unable to find any event templates.');
  
        if (!$userForm){
            $template['ACTIVE'] = $_SESSION['translate']->it('Active');
            $template['ACTIVE_RADIO_ON'] = PHPWS_Form::formRadio('cal_active', 1, $event->active) . ' ' . $_SESSION['translate']->it('On');
            $template['ACTIVE_RADIO_OFF'] = PHPWS_Form::formRadio('cal_active', 0, $event->active) . ' ' . $_SESSION['translate']->it('Off');
        }
  
        $template['TITLE'] = $_SESSION['translate']->it('Title');
        $template['TITLE_FORM'] = PHPWS_Form::formTextField('cal_title', $event->title, 40);
  
        $template['DESCRIPTION'] = $_SESSION['translate']->it('Description');
        $template['DESCRIPTION_FORM'] = PHPWS_WizardBag::js_insert('wysiwyg', 'createEvent', 'cal_description', 1);
        $template['DESCRIPTION_FORM'] .= PHPWS_Form::formTextArea('cal_description', $event->description, 8);
  
        /*******************************/
        $template['TYPE'] = $_SESSION['translate']->it('Event Type') . CLS_help::show_link('calendar', 'eventtype');
        $template['TYPE_FORM'] = PHPWS_Form::formRadio('cal_eventType', 'interval', $event->eventType) . ' ' . $_SESSION['translate']->it('Interval') . '<br />';
        $template['TYPE_FORM'] .= PHPWS_Form::formRadio('cal_eventType', 'start', $event->eventType) . ' ' . $_SESSION['translate']->it('Starts At') . '<br /> ';
        $template['TYPE_FORM'] .= PHPWS_Form::formRadio('cal_eventType', 'deadline', $event->eventType) . ' ' . $_SESSION['translate']->it('Deadline') . '<br />';
        $template['TYPE_FORM'] .= PHPWS_Form::formRadio('cal_eventType', 'allday', $event->eventType) . ' ' . $_SESSION['translate']->it('All Day') . '<br />';
  
        $template['START_TIME'] = $_SESSION['translate']->it('Start Time');
        $template['START_TIME_FORM'] = PHPWS_Form::clock('cal_startTime', $event->startTime, 5);
  
        $template['END_TIME'] = $_SESSION['translate']->it('End Time');
        $template['END_TIME_FORM'] = PHPWS_Form::clock('cal_endTime', $event->endTime, 5);
  
        $template['START_DATE'] = $_SESSION['translate']->it('Start Date');
        $template['START_DATE_FORM'] = PHPWS_Form::formDate('cal_startDate', $event->startDate, date('Y') - 1);
        $template['START_DATE_FORM'] .= PHPWS_WizardBag::js_insert('popcalendar', 
                                        NULL, NULL, FALSE,
                                        array('month'=>'cal_startDate_month', 
                                              'day'=>'cal_startDate_day', 
                                              'year'=>'cal_startDate_year'));
  
  
  
        $template['END_DATE'] = $_SESSION['translate']->it('End Date');
        $template['END_DATE_FORM'] = PHPWS_Form::formDate('cal_endDate', $event->endDate, date('Y') - 1);
        $template['END_DATE_FORM'] .= PHPWS_WizardBag::js_insert('popcalendar', 
                                        NULL, NULL, FALSE,
                                        array('month'=>'cal_endDate_month', 
                                              'day'=>'cal_endDate_day', 
                                              'year'=>'cal_endDate_year'));
  
  
        if ($_SESSION['OBJ_user']->allow_access('calendar', 'crossPost'))
            $groupList = PHPWS_User_Groups::listAllGroups();
        else
            $groupList = $_SESSION['OBJ_user']->listUserGroups();
 
        if($_SESSION['OBJ_user']->isAdmin()) {
            $export_warning = $_SESSION['translate']->it('Export this event in vCalendar format') . '? ';
            $export_warning .= $_SESSION['translate']->it('Exporting this event will cause it to be publicly accessible') . '.';

            $template['EXPORT']         = $export_warning;
            $template['EXPORT_FORM']    = PHPWS_Form::formCheckBox('export');

            if(!$GLOBALS['core']->sqlTableExists('mod_calendar_calendars', TRUE)) {
                exit ("You must update your calendar module in boost!");
            }

            $calendars = $GLOBALS['core']->sqlSelect('mod_calendar_calendars');
            
            if($calendars != NULL && $calendars != FALSE) {
                // drop down of calendars
                $template['CALENDARS_LABEL']    = $_SESSION['translate']->it("Save to calendar") . ":<br />";
                $stuff = array();
                    foreach($calendars as $calendar) {
                        $stuff[$calendar['title']] = $calendar['filename'];
                    }
                $template['CALENDARS_DROPDOWN'] = PHPWS_Form::formSelect('existing_calendar_name', $stuff, "match");
            }
            // add a new calendar option
            $template['NEW_CALENDAR_LABEL']     = $_SESSION['translate']->it("Add new calendar") . ":<br />";
            $template['NEW_CALENDAR_TEXTFIELD'] = PHPWS_Form::formTextField('new_calendar_name', NULL, 40, 100);
        }

        return PHPWS_Template::processTemplate($template, 'calendar', 'admin/eventDataForm.tpl');
    }
  
  
    function settings(){
        extract($GLOBALS['calendarSettings']);
        $content = NULL;
        if (!$cacheView)
            $cacheView = 0;
        else
            $cacheView = 1;
  
        for ($i=0; $i <= MAX_AHEAD; $i++)
            $dayarray[$i] = $i;
  
        $form = new EZform;
        $form->add('module', 'hidden', 'calendar');
        $form->add('calendar[admin]', 'hidden', 'updateSettings');
        $form->add('minimonth', 'checkbox', 1);
        $form->setMatch('minimonth', $minimonth);
        $form->add('cacheView', 'checkbox', 1);
        $form->setMatch('cacheView', $cacheView);
        $form->add('daysAhead', 'select', $dayarray);
        $form->setMatch('daysAhead', $daysAhead);
        $form->add('userSubmit', 'checkbox', 1);
        $form->setMatch('userSubmit', $userSubmit);
        $form->add('reindexFatcat', 'checkbox');
        $form->add('search_past', 'checkbox');
        $form->setMatch('search_past', $search_past);
        $form->add('sessionView', 'checkbox');
        $form->setMatch('sessionView', $sessionView);
        $form->add('restrict_view', 'checkbox', 1);
        $form->setMatch('restrict_view', $restrict_view);
  
        $form->dateForm('purge_event');
        $form->dateForm('purge_fatcat');
        $form->add('purge_fatcat_button', 'submit', $_SESSION['translate']->it('Purge Fatcat Entries Before'));
  
        $template = $form->getTemplate();
        $template['USERSUBMIT_LABEL'] = $_SESSION['translate']->it('User Submitted Events');
        $template['DAYSAHEAD_LABEL'] = $_SESSION['translate']->it('Days Ahead');
        $template['CACHEVIEW_LABEL'] = $_SESSION['translate']->it('Cache Calendar Views');
        $template['MINIMONTH_LABEL'] = $_SESSION['translate']->it('Mini Month');
        $template['REINDEX_LABEL'] = $_SESSION['translate']->it('Reindex FatCat');
        $template['VIEWS'] = $_SESSION['translate']->it('Box Views');
        $template['OTHER_SETTINGS'] = $_SESSION['translate']->it('Other Settings');
        $template['SEARCH_PAST_LABEL'] = $_SESSION['translate']->it('Search Past Events');
        $template['PURGE_SETTINGS'] = $_SESSION['translate']->it('Purge');
        $template['SESSIONVIEW_LABEL'] = $_SESSION['translate']->it('Session View');
        $template['RESTRICT_VIEW_LABEL'] = $_SESSION['translate']->it('Restrict Views to Registered Users');
  
        $content = PHPWS_Template::processTemplate($template, 'calendar', 'admin/settings.tpl');
        return $content;
    }
  
  
    function repeatForm($event){
        $template['WEEKDAYS'] = NULL;
  
        $template['MODE_LABEL'] = $_SESSION['translate']->it('Mode');
        $template['PROPERTIES_LABEL'] = $_SESSION['translate']->it('Properties');
  
        if ($event->repeatMode)
            $repeat_switch = 1;
        else
            $repeat_switch = NULL;
  
        $template['REPEAT_SWITCH'] = PHPWS_Form::formCheckBox('repeatEvent', 1, $repeat_switch);
  
        $template['REPEAT_UNTIL'] = $_SESSION['translate']->it('Repeat Event until') . PHPWS_Form::formDate('endRepeat', $event->endRepeat, date('Y') - 1);
        $template['REPEAT_UNTIL'] .= PHPWS_WizardBag::js_insert('popcalendar', 
                                        NULL, NULL, FALSE,
                                        array('month'=>'endRepeat_month', 
                                              'day'=>'endRepeat_day', 
                                              'year'=>'endRepeat_year'));  
  
        $template['MODE_DAILY'] = PHPWS_Form::formRadio('repeatMode', 'daily', $event->repeatMode) . ' ' . $_SESSION['translate']->it('Daily');
  
        $template['MODE_WEEKLY'] = PHPWS_Form::formRadio('repeatMode', 'weekly', $event->repeatMode) . ' ' . $_SESSION['translate']->it('Weekly');
  
        $day0 = (isset($event->repeatWeekdays[0])) ? $event->repeatWeekdays[0] : NULL;
        $day1 = (isset($event->repeatWeekdays[1])) ? $event->repeatWeekdays[1] : NULL;
        $day2 = (isset($event->repeatWeekdays[2])) ? $event->repeatWeekdays[2] : NULL;
        $day3 = (isset($event->repeatWeekdays[3])) ? $event->repeatWeekdays[3] : NULL;
        $day4 = (isset($event->repeatWeekdays[4])) ? $event->repeatWeekdays[4] : NULL;
        $day5 = (isset($event->repeatWeekdays[5])) ? $event->repeatWeekdays[5] : NULL;
        $day6 = (isset($event->repeatWeekdays[6])) ? $event->repeatWeekdays[6] : NULL;
  
        if (!$GLOBALS['core']->datetime->day_start)
            $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[0]', 1, $day0) . '&nbsp;' . $_SESSION['translate']->it('Sunday') . ' ';
  
        $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[1]', 1, $day1) . '&nbsp;' . $_SESSION['translate']->it('Monday') . ' ';
        $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[2]', 1, $day2) . '&nbsp;' . $_SESSION['translate']->it('Tuesday') . ' ';
        $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[3]', 1, $day3) . '&nbsp;' . $_SESSION['translate']->it('Wednesday') . ' ';
        $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[4]', 1, $day4) . '&nbsp;' . $_SESSION['translate']->it('Thursday') . ' ';
        $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[5]', 1, $day5) . '&nbsp;' . $_SESSION['translate']->it('Friday') . ' ';
        $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[6]', 1, $day6) . '&nbsp;' . $_SESSION['translate']->it('Saturday') . ' ';
  
        if ($GLOBALS['core']->datetime->day_start)
            $template['WEEKDAYS'] .= PHPWS_Form::formCheckBox('repeatWeekdays[0]', 1, $day0) . '&nbsp;' . $_SESSION['translate']->it('Sunday') . ' ';
  
  
        $template['MODE_MONTHLY'] = PHPWS_Form::formRadio('repeatMode', 'monthly', $event->repeatMode) . ' ' . $_SESSION['translate']->it('Monthly');
        $month_repeat_day['begin'] = $_SESSION['translate']->it('Beginning of each month');
        $month_repeat_day['end']   = $_SESSION['translate']->it('End of each month');
        $month_repeat_day['date']  = $_SESSION['translate']->it('Every month on Start Date');
  
        $template['MONTH_SELECT'] = PHPWS_Form::formSelect('monthMode', $month_repeat_day, $event->monthMode, NULL, 1);
  
        $template['MODE_YEARLY'] = PHPWS_Form::formRadio('repeatMode', 'yearly', $event->repeatMode) . ' ' . $_SESSION['translate']->it('Yearly');
  
        $input_every_num = array(1=>'1st', 2=>'2nd', 3=>'3rd', 4=>'4th');
        $input_every_day = array(
                                 0=>$_SESSION['translate']->it('Sunday'),
                                 1=>$_SESSION['translate']->it('Monday'),
                                 2=>$_SESSION['translate']->it('Tuesday'),
                                 3=>$_SESSION['translate']->it('Wednesday'),
                                 4=>$_SESSION['translate']->it('Thursday'),
                                 5=>$_SESSION['translate']->it('Friday'),
                                 6=>$_SESSION['translate']->it('Saturday')
                                );
        $input_every_month = array(
                                    'a'=>$_SESSION['translate']->it('Every Month'),
                                    1=>$_SESSION['translate']->it('January'),
                                    2=>$_SESSION['translate']->it('February'),
                                    3=>$_SESSION['translate']->it('March'),
                                    4=>$_SESSION['translate']->it('April'),
                                    5=>$_SESSION['translate']->it('May'),
                                    6=>$_SESSION['translate']->it('June'),
                                    7=>$_SESSION['translate']->it('July'),
                                    8=>$_SESSION['translate']->it('August'),
                                    9=>$_SESSION['translate']->it('September'),
                                    10=>$_SESSION['translate']->it('October'),
                                    11=>$_SESSION['translate']->it('November'),
                                    12=>$_SESSION['translate']->it('December')
                                  ); 
  
        $template['EVERY_NUMBER'] = PHPWS_Form::formSelect('everyNumber', $input_every_num, $event->every[0], NULL, 1);
        $template['EVERY_DAY']    = PHPWS_Form::formSelect('everyDay', $input_every_day, $event->every[1], NULL, 1);
        $template['EVERY_MONTH']  = PHPWS_Form::formSelect('everyMonth', $input_every_month, $event->every[2], NULL, 1);
        $template['MODE_EVERY']   = PHPWS_Form::formRadio('repeatMode', 'every', $event->repeatMode) . ' ' . $_SESSION['translate']->it('Every');
        $content = PHPWS_Template::processTemplate($template, 'calendar', 'admin/repeatForm.tpl');
        return $content;
      
    }

    function importDeleteCalendarForm() {
        $form = new EZform;
        $form->add('module', 'hidden', 'calendar');
        $form->add('calendar[admin]', 'hidden', 'importDeleteCalendar');

        $content = "Please enter the URL of a calendar to import.<br />";
        $content .= "You can import more than one calendar, but only one at a time.<br />";
       
        $form->add('cal_url', 'text');
        $form->setWidth('cal_url', 40);
        $form->add('importCalendar', 'submit', 'Import Calendar');
        $form->add('deleteCalendar2', 'submit', 'delete 2');
        
        $template = $form->getTemplate();
        
        if(!$GLOBALS['core']->sqlTableExists('mod_calendar_calendars', TRUE)) {
            exit ("You must update your calendar module in boost!");
        }

        $calendars = $GLOBALS['core']->sqlSelect('mod_calendar_calendars');
            
        if($calendars != NULL && $calendars != FALSE) {
            // drop down of calendars
            $template['DELETE_CALENDAR_LABEL']    = $_SESSION['translate']->it("Or select calendar file to delete") . ": ";
            $template['DELETE_CALENDAR_LABEL']   .= $_SESSION['translate']->it("\nOnly the export file will be deleted, not the calendar events") . "!!";
            $stuff = array();
            foreach($calendars as $calendar) {
                $stuff[$calendar['title']] = $calendar['title'];
            }
            $template['DELETE_CALENDAR_DROPDOWN']   = PHPWS_Form::formSelect('calendar_name', $stuff, "match");
            $template['DELETE_CHECKBOX_LABEL']      = $_SESSION['translate']->it("Check to enable deletion of calendar") . ":";
            $template['DELETE_CALENDAR_CHECKBOX']   = PHPWS_Form::formCheckBox('delete_checkbox');
            $template['DELETE_CALENDAR_BUTTON']     = PHPWS_Form::formSubmit('Delete Calendar', 'deleteCalendar');
        }
        
        $template['URL_LABEL']  = $_SESSION['translate']->it('URL') . ':';
        $content .= PHPWS_Template::processTemplate($template, 'calendar', 'admin/importDeleteCalendarForm.tpl');

        $GLOBALS['CNT_Calendar_Main']['title'] = $_SESSION['translate']->it('Import/Delete Calendar');
        $GLOBALS['CNT_Calendar_Main']['content'] = $content;
    }
}
?>
