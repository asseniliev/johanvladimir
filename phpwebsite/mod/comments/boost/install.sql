CREATE TABLE mod_comments_data (
  cid int NOT NULL,
  pid int default NULL,
  module varchar(80) NOT NULL default '',
  itemId int NOT NULL default '0',
  subject varchar(120) NOT NULL default '',
  comment text NOT NULL,
  author varchar(60) NOT NULL default '',
  authorIp varchar(60) NOT NULL default '',
  postDate datetime NOT NULL default '0000-00-00 00:00:00',
  editor varchar(60) NOT NULL default '',
  editReason text NOT NULL,
  editDate datetime NOT NULL default '0000-00-00 00:00:00',
  score smallint NOT NULL default '0',
  anonymous smallint NOT NULL default '0',
  PRIMARY KEY  (cid)
);

CREATE TABLE mod_comments_cfg (
  listView smallint NOT NULL default '0',
  listOrder varchar(20) NOT NULL default '',
  maxSize int NOT NULL default '0',
  maxIp int NOT NULL default '0'
);

INSERT INTO mod_comments_cfg (listView, listOrder, maxSize, maxIp) values ('1', 'ASC', '0', '0');
