CREATE TABLE mod_menuman_items (
  menu_item_id int NOT NULL,
  menu_item_pid int NOT NULL default '0',
  menu_item_title varchar(60) NOT NULL default '',
  menu_item_url text NOT NULL,
  menu_item_active smallint NOT NULL default '0',
  menu_item_coords text,
  menu_item_order int NOT NULL default '0',
  display_key smallint NOT NULL default '0',
  menu_id int NOT NULL default '0',
  PRIMARY KEY  (menu_item_id)
);

CREATE TABLE mod_menuman_menus (
  menu_id int NOT NULL,
  menu_title varchar(60) NOT NULL default '',
  menu_active smallint NOT NULL default '0',
  menu_indent varchar(40) default NULL,
  indent_key int NOT NULL default '0',
  color_key varchar(8) default NULL,
  menu_image varchar(80) default NULL,
  active_image varchar(80) default NULL,
  open_image varchar(80) default NULL,
  menu_spacer int NOT NULL default '0',
  image_map smallint NOT NULL default '0',
  content_var text,
  allow_view text,
  horizontal varchar(10) default NULL,
  template varchar(60) NOT NULL default 'default',
  updated int NOT NULL default '0',
  anon_view smallint NOT NULL default '1',
  PRIMARY KEY  (menu_id)
);

INSERT INTO mod_menuman_menus VALUES ('Menu', 1, 'none', 0, '', '', '', '', 2, 0, 'CNT_menuman_1', 'a:32:{i:0;s:4:"home";i:1;s:8:"announce";i:2;s:8:"security";i:3;s:8:"approval";i:4;s:7:"article";i:5;s:10:"blockmaker";i:6;s:5:"boost";i:7;s:6:"branch";i:8;s:7:"phpwsbb";i:9;s:8:"calendar";i:10;s:8:"comments";i:11;s:12:"controlpanel";i:12;s:8:"phatfile";i:13;s:9:"documents";i:14;s:3:"faq";i:15;s:6:"fatcat";i:16;s:8:"phatform";i:17;s:4:"help";i:18;s:8:"language";i:19;s:6:"layout";i:20;s:7:"linkman";i:21;s:7:"menuman";i:22;s:8:"modmaker";i:23;s:5:"notes";i:24;s:10:"photoalbum";i:25;s:5:"debug";i:26;s:4:"poll";i:27;s:13:"phpwsrssfeeds";i:28;s:6:"search";i:29;s:8:"skeleton";i:30;s:5:"users";i:31;s:10:"pagemaster";}', 'FALSE', 'default');
INSERT INTO mod_menuman_items VALUES (1, 'Home', './index.php?', 1, '', 1, 1, 1);
