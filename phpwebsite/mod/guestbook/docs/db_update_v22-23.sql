# MySQL dump 8.16
#
# Host: localhost    Database: gb23
#--------------------------------------------------------
# Server version	3.23.42

#
# Table structure for table 'book_pics'
#

CREATE TABLE book_pics (
  msg_id int(11) NOT NULL default '0',
  book_id int(11) NOT NULL default '0',
  p_filename varchar(100) NOT NULL default '',
  p_size int(11) unsigned NOT NULL default '0',
  width int(11) unsigned NOT NULL default '0',
  height int(11) unsigned NOT NULL default '0',
  KEY msg_id (msg_id),
  KEY book_id (book_id)
) TYPE=MyISAM;

ALTER TABLE `book_config` ADD `thumbnail` SMALLINT(1) NOT NULL, ADD `thumb_min_fsize` INT(10) NOT NULL;  

