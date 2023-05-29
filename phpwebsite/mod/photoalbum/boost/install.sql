CREATE TABLE mod_photoalbum_albums (
  id int(10) unsigned NOT NULL default '0',
  owner varchar(20) binary default '',
  editor varchar(20) binary default '',
  ip text,
  label text NOT NULL,
  groups mediumtext,
  created int(11) NOT NULL default '0',
  updated int(11) NOT NULL default '0',
  hidden int(1) NOT NULL default '1',
  approved int(1) NOT NULL default '0',
  blurb0 text,
  blurb1 text,
  image text default NULL,
  PRIMARY KEY  (id)
);

CREATE TABLE mod_photoalbum_photos (
  id int(10) unsigned NOT NULL default '0',
  owner varchar(20) binary default '',
  editor varchar(20) binary default '',
  ip text default NULL,
  label text NOT NULL,
  groups mediumtext,
  created int(11) NOT NULL default '0',
  updated int(11) NOT NULL default '0',
  hidden int(1) NOT NULL default '1',
  approved int(1) NOT NULL default '0',
  album int(10) unsigned NOT NULL default '0',
  name varchar(255) default NULL,
  type varchar(60) default NULL,
  width int(4) default NULL,
  height int(4) default NULL,
  tnname varchar(255) default NULL,
  tnwidth int(4) default NULL,
  tnheight int(4) default NULL,
  blurb text,
  PRIMARY KEY  (id)
);
