CREATE TABLE mod_scheduler_entries (
  id int(11) unsigned NOT NULL default '0',
  owner varchar(20) default NULL,
  editor varchar(20) default NULL,
  ip text,
  created int(11) default NULL,
  updated int(11) default NULL,
  hidden smallint(1) NOT NULL default '0',
  start int(11) default NULL,
  end int(11) default NULL,
  label text,
  user int(11) default NULL,
  global smallint(1) default NULL,
  administrative smallint(1) default NULL,
  repeat smallint(1) default NULL,
  repeat_until int(11) default NULL,
  mode int(2) default NULL,
  properties varchar(255) default NULL,
  pid int(11) default NULL,
  PRIMARY KEY  (id)
);
