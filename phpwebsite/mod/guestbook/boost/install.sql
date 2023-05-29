CREATE TABLE mod_book_ban (
  ban_ip varchar(15) NOT NULL default ''
);

CREATE TABLE mod_book_com (
  com_id int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  name varchar(50) NOT NULL default '',
  comments text NOT NULL,
  host varchar(60) NOT NULL default '',
  timestamp int(11) NOT NULL default '0',
  PRIMARY KEY  (com_id)
);

CREATE TABLE mod_book_config (
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
  show_sidebox smallint(1) NOT NULL default '1',
  PRIMARY KEY  (config_id)
);

CREATE TABLE mod_book_data (
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
);

CREATE TABLE mod_book_ip (
  guest_ip varchar(15) NOT NULL default '',
  timestamp int(11) NOT NULL default '0',
  KEY guest_ip (guest_ip)
);

CREATE TABLE mod_book_pics (
  msg_id int(11) NOT NULL default '0',
  book_id int(11) NOT NULL default '0',
  p_filename varchar(100) NOT NULL default '',
  p_size int(11) unsigned NOT NULL default '0',
  width int(11) unsigned NOT NULL default '0',
  height int(11) unsigned NOT NULL default '0',
  KEY msg_id (msg_id),
  KEY book_id (book_id)
);

CREATE TABLE mod_book_private (
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
);

CREATE TABLE mod_book_smilies (
  id int(11) NOT NULL auto_increment,
  s_code varchar(20) NOT NULL default '',
  s_filename varchar(60) NOT NULL default '',
  s_emotion varchar(60) NOT NULL default '',
  width smallint(6) unsigned NOT NULL default '0',
  height smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);

CREATE TABLE mod_book_words (
  word varchar(30) NOT NULL default ''
);

INSERT INTO mod_book_ban VALUES ('0.0.0.0');
INSERT INTO mod_book_config VALUES (1, 0, '0', 1, 'Euro', '24hr', 'root@localhost', 0, 0, 0, 'Thank you for signing the guestbook!', 10, 1, '#FFFFFF', '#000000', '#006699', '95%', '11px', '10px', 'Tahoma, Verdana, Helvetica', '#7878BE', '#000000', '#FFFFFF', '#E8E8E8', '#F7F7F7', 'english', 6, 1500, 80, 'comment', 0, 1, 0, 1, 80, 1, 1, 1, 1, 120, 320, 80, 1, 12, 1);
INSERT INTO mod_book_words VALUES ('fuck');
INSERT INTO mod_book_smilies VALUES (':-)', 'a1.gif', 'smile', 15, 15);
INSERT INTO mod_book_smilies VALUES (':-(', 'a2.gif', 'frown', 15, 15);
INSERT INTO mod_book_smilies VALUES (';-)', 'a3.gif', 'wink', 15, 15);
INSERT INTO mod_book_smilies VALUES (':o', 'a4.gif', 'embarrassment', 15, 15);
INSERT INTO mod_book_smilies VALUES (':D', 'a5.gif', 'big grin', 15, 15);
INSERT INTO mod_book_smilies VALUES (':p', 'a6.gif', 'razz (stick out tongue)', 15, 15);
INSERT INTO mod_book_smilies VALUES (':cool:', 'a7.gif', 'cool', 21, 15);
INSERT INTO mod_book_smilies VALUES (':rolleyes:', 'a8.gif', 'roll eyes (sarcastic)', 15, 15);
INSERT INTO mod_book_smilies VALUES (':mad:', 'a9.gif', '#@*%!', 15, 15);
INSERT INTO mod_book_smilies VALUES (':eek:', 'a10.gif', 'eek!', 15, 15);
INSERT INTO mod_book_smilies VALUES (':confused:', 'a11.gif', 'confused', 15, 22);
