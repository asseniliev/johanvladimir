CREATE TABLE mod_documents_docs (
  id int(10) NOT NULL default '0',
  owner varchar(20) binary default '',
  editor varchar(20) binary default '',
  ip varchar(255) NULL,
  created int(11) NOT NULL default '0',
  updated int(11) NOT NULL default '0',
  hidden int(1) NOT NULL default '1',
  approved int(1) NOT NULL default '0',
  label varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
);

CREATE TABLE mod_documents_files (
  id int(10) NOT NULL default '0',
  owner varchar(20) binary default '',
  ip varchar(255) NULL,
  created int(11) NOT NULL default '0',
  doc int(10) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  size varchar(255) NOT NULL default '',
  type varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
);

CREATE TABLE mod_documents_settings (
  userview int(1) NOT NULL default '0',
  userdownload int(1) NOT NULL default '0',
  showblock int(1) NOT NULL default '1',
  approval int(1) NOT NULL default '1',	
  index (userview, userdownload, showblock)
);

INSERT INTO mod_documents_settings VALUES ('1', '1', '0', '0');
