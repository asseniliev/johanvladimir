CREATE TABLE mod_calendar_calendars (
    id int(11) NOT NULL default '0',
    title varchar(50) NOT NULL default '',
    filename varchar(50) NOT NULL default '',
    created_by varchar(20) NOT NULL default '',
    created_on int(11) NOT NULL default '0',
    PRIMARY KEY  (id)
);

CREATE TABLE `mod_calendar_exported_events` (
    id INT NOT NULL AUTO_INCREMENT ,
    event_id INT NOT NULL ,
    exported_to varchar(50) NOT NULL ,
    PRIMARY KEY ( `id` )
);
