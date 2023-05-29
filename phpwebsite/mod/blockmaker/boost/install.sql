CREATE TABLE mod_blockmaker_data (
  block_id int(10) unsigned NOT NULL,
  block_title varchar(60) NOT NULL default '',
  block_content text NOT NULL,
  block_footer varchar(60) NOT NULL default '',
  block_active tinyint(1) NOT NULL default '0',
  block_updated datetime NOT NULL default '0000-00-00 00:00:00',
  content_var varchar(40) NOT NULL default '',
  allow_view text NOT NULL,
  PRIMARY KEY  (block_id)
);
