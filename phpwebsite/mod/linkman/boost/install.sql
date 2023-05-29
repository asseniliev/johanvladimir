CREATE TABLE mod_linkman_links (
  id int NOT NULL,
  title text NOT NULL,
  url text NOT NULL,
  description text NOT NULL,
  keywords text NOT NULL,
  username text NOT NULL,
  userEmail text NOT NULL,
  datePosted date NOT NULL default '0000-00-00',
  active smallint NOT NULL default '0',
  hits int NOT NULL default '0',
  new smallint NOT NULL default '1',
  PRIMARY KEY  (id)
);
