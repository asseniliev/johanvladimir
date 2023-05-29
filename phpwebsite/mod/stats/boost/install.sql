CREATE TABLE mod_stats_counts (
   id int(10) unsigned NOT NULL default '0',
   label text NOT NULL,
   table_name text NOT NULL,
   link text,
   PRIMARY KEY(id)
);

CREATE TABLE mod_stats_settings (
   counts_show_block smallint NOT NULL default '1',
   counts_allow text,
   counts_show_diety int(1) NOT NULL default '1',
   counts_show_users int(1) NOT NULL default '0',
   counts_show_perms int(1) NOT NULL default '0',
   counts_show_any int(1) NOT NULL default '0',
   stats_viewable text,
   stats_num_show int(5) unsigned NOT NULL default '10',
   stats_show_empty int(1) NOT NULL default '0',
   webstats_enable int(1) NOT NULL default '1',
   graphs_md5 text NOT NULL
);

CREATE TABLE mod_stats_pm (
   page_id int(10),
   count int
);

CREATE TABLE mod_stats_hit_history (
   id int(10) unsigned NOT NULL,
   month int(2) unsigned NOT NULL,
   day int(2) unsigned NOT NULL,
   year int(4) unsigned NOT NULL,
   hits int,
   PRIMARY KEY (id)
);

CREATE TABLE mod_stats_today (
   id int(10) unsigned NOT NULL,
   date text NOT NULL,
   ip text NOT NULL,
   username text,
   PRIMARY KEY (id)
);



