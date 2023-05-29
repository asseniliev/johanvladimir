CREATE TABLE mod_skeleton_items (
  id int NOT NULL default '0',
  owner varchar(20) default NULL,
  editor varchar(20) default NULL,
  ip text,
  label text NOT NULL,
  created int NOT NULL default '0',
  updated int NOT NULL default '0',
  hidden smallint NOT NULL default '1',
  approved smallint NOT NULL default '0',
  muscle text NOT NULL,
  PRIMARY KEY (id)
);