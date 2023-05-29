# MySQL dump 8.16
#
# Host: localhost    Database: gb23
#--------------------------------------------------------
# Server version	3.23.42

#
# Current Database: gb22
#

CREATE DATABASE /*!32312 IF NOT EXISTS*/ gb22;

USE gb22;

#
# Table structure for table 'book_auth'
#

CREATE TABLE book_auth (
  ID smallint(5) NOT NULL auto_increment,
  username varchar(60) NOT NULL default '',
  password varchar(60) NOT NULL default '',
  session varchar(32) NOT NULL default '',
  last_visit int(11) NOT NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'book_auth'
#

INSERT INTO book_auth VALUES (1,'test','773359240eb9a1d9','80c1b50318676c8f324c985b94ac780a',1013100791);

#
# Table structure for table 'book_ban'
#

CREATE TABLE book_ban (
  ban_ip varchar(15) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table 'book_ban'
#

INSERT INTO book_ban VALUES ('123.123.123.123');

#
# Table structure for table 'book_com'
#

CREATE TABLE book_com (
  com_id int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  comments text NOT NULL,
  host varchar(60) NOT NULL default '',
  timestamp int(11) NOT NULL default '0',
  PRIMARY KEY  (com_id)
) TYPE=MyISAM;

#
# Dumping data for table 'book_com'
#


#
# Table structure for table 'book_config'
#

CREATE TABLE book_config (
  config_id smallint(4) NOT NULL auto_increment,
  agcode smallint(1) NOT NULL default '0',
  allow_html smallint(1) NOT NULL default '0',
  offset varchar(5) NOT NULL default '0',
  smilies smallint(1) NOT NULL default '1',
  dformat varchar(6) NOT NULL default '',
  tformat varchar(4) NOT NULL default '24hr',
  admin_mail varchar(50) NOT NULL default '',
  notify_private smallint(1) NOT NULL default '0',
  notify_admin smallint(1) NOT NULL default '0',
  notify_guest smallint(1) NOT NULL default '0',
  notify_mes varchar(150) NOT NULL default '',
  entries_per_page int(6) NOT NULL default '10',
  show_ip smallint(1) NOT NULL default '0',
  pbgcolor varchar(7) NOT NULL default '0',
  text_color varchar(7) NOT NULL default '0',
  link_color varchar(7) NOT NULL default '0',
  width varchar(4) NOT NULL default '0',
  tb_font_1 varchar(7) NOT NULL default '',
  tb_font_2 varchar(7) NOT NULL default '',
  font_face varchar(60) NOT NULL default '',
  tb_hdr_color varchar(7) NOT NULL default '',
  tb_bg_color varchar(7) NOT NULL default '',
  tb_text varchar(7) NOT NULL default '',
  tb_color_1 varchar(7) NOT NULL default '',
  tb_color_2 varchar(7) NOT NULL default '',
  lang varchar(30) NOT NULL default '',
  min_text smallint(4) NOT NULL default '0',
  max_text int(6) NOT NULL default '0',
  max_word_len smallint(4) NOT NULL default '0',
  comment_pass varchar(50) NOT NULL default '',
  need_pass smallint(1) NOT NULL default '0',
  censor smallint(1) NOT NULL default '0',
  flood_check smallint(1) NOT NULL default '0',
  banned_ip smallint(1) NOT NULL default '0',
  flood_timeout smallint(5) NOT NULL default '0',
  allow_icq smallint(1) NOT NULL default '0',
  allow_aim smallint(1) NOT NULL default '0',
  allow_gender smallint(1) NOT NULL default '0',
  allow_img smallint(1) NOT NULL default '0',
  max_img_size int(10) NOT NULL default '0',
  img_width smallint(5) NOT NULL default '0',
  img_height smallint(5) NOT NULL default '0',
  thumbnail smallint(1) NOT NULL default '0',
  thumb_min_fsize int(10) NOT NULL default '0',
  PRIMARY KEY  (config_id)
) TYPE=MyISAM;

#
# Dumping data for table 'book_config'
#

INSERT INTO book_config VALUES (1,1,0,'0',1,'Euro','24hr','root@localhost',0,0,0,'Thank you for signing the guestbook!',10,1,'#FFFFFF','#000000','#006699','95%','11px','10px','Verdana, Arial, Helvetica, sans-serif','#7878BE','#000000','#FFFFFF','#DEDFDF','#F7F7F7','english',6,1500,25,'comment',0,1,0,1,80,1,1,1,1,120,320,80,1,12);

#
# Table structure for table 'book_data'
#

CREATE TABLE book_data (
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  gender char(1) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  url varchar(70) NOT NULL default '',
  date int(11) NOT NULL default '0',
  location varchar(50) NOT NULL default '',
  host varchar(60) NOT NULL default '',
  browser varchar(70) NOT NULL default '',
  comment text NOT NULL,
  icq int(11) NOT NULL default '0',
  aim varchar(70) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'book_data'
#


#
# Table structure for table 'book_ip'
#

CREATE TABLE book_ip (
  guest_ip varchar(15) NOT NULL default '',
  timestamp int(11) NOT NULL default '0',
  KEY guest_ip (guest_ip)
) TYPE=MyISAM;

#
# Dumping data for table 'book_ip'
#


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

#
# Dumping data for table 'book_pics'
#


#
# Table structure for table 'book_private'
#

CREATE TABLE book_private (
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  gender char(1) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  url varchar(70) NOT NULL default '',
  date int(11) NOT NULL default '0',
  location varchar(50) NOT NULL default '',
  host varchar(60) NOT NULL default '',
  browser varchar(70) NOT NULL default '',
  comment text NOT NULL,
  icq int(11) NOT NULL default '0',
  aim varchar(70) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'book_private'
#


#
# Table structure for table 'book_smilies'
#

CREATE TABLE book_smilies (
  id int(11) NOT NULL auto_increment,
  s_code varchar(20) NOT NULL default '',
  s_filename varchar(60) NOT NULL default '',
  s_emotion varchar(60) NOT NULL default '',
  width smallint(6) unsigned NOT NULL default '0',
  height smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table 'book_smilies'
#

INSERT INTO book_smilies VALUES (1,':-)','a1.gif','smile',15,15);
INSERT INTO book_smilies VALUES (2,':-(','a2.gif','frown',15,15);
INSERT INTO book_smilies VALUES (3,';-)','a3.gif','wink',15,15);
INSERT INTO book_smilies VALUES (4,':o','a4.gif','embarrassment',15,15);
INSERT INTO book_smilies VALUES (5,':D','a5.gif','big grin',15,15);
INSERT INTO book_smilies VALUES (6,':p','a6.gif','razz (stick out tongue)',15,15);
INSERT INTO book_smilies VALUES (7,':cool:','a7.gif','cool',21,15);
INSERT INTO book_smilies VALUES (8,':rolleyes:','a8.gif','roll eyes (sarcastic)',15,15);
INSERT INTO book_smilies VALUES (9,':mad:','a9.gif','#@*%!',15,15);
INSERT INTO book_smilies VALUES (10,':eek:','a10.gif','eek!',15,15);
INSERT INTO book_smilies VALUES (11,':confused:','a11.gif','confused',15,22);

#
# Table structure for table 'book_words'
#

CREATE TABLE book_words (
  word varchar(30) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table 'book_words'
#

INSERT INTO book_words VALUES ('fuck');

