CREATE TABLE mod_user_groups (
  group_id int  NOT NULL default '0',
  group_name varchar(30) default NULL,
  description text,
  members text,
  PRIMARY KEY  (group_id)
  );

CREATE TABLE mod_user_groupvar (
  group_id int  NOT NULL default '0',
  module_title varchar(20) NOT NULL default '',
  varName varchar(30) NOT NULL default '',
  varValue text,
  index (group_id)
  );

CREATE TABLE mod_user_settings (
  allow_cookies smallint NOT NULL default '0',
  timelimit int NOT NULL default '30',
  secure smallint NOT NULL default '0',
  user_signup varchar(6) default NULL,
  max_log_attempts int NOT NULL default '10',
  nu_subj varchar(255) default NULL,
  greeting text,
  user_contact varchar(255) default NULL,
  user_authentication text,
  external_auth_file text,
  show_login smallint NOT NULL default '1',
  show_remember_me smallint NOT NULL default '1',
  welcomeURL text
  );

INSERT INTO mod_user_settings VALUES (1, 10, 0, 'send', 10, 'Welcome message displayed in email subject!', 'Fill this text area with a greeting to your new users. Make sure to include an address to your web site so they can log in.\r\n', '', 'local', 'external_authorization.php', 1, 1, NULL);


CREATE TABLE mod_user_uservar (
  user_id int  NOT NULL default '0',
  module_title varchar(20) NOT NULL default '',
  varName varchar(30) NOT NULL default '',
  varValue text,
  index (user_id)
  );


CREATE TABLE mod_users (
  user_id int  NOT NULL default '0',
  username varchar(20) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  email varchar(50) default NULL,
  admin_switch smallint NOT NULL default '0',
  groups text,
  deity smallint NOT NULL default '0',
  log_sess int  NOT NULL default '0',
  last_on int  NOT NULL default '0',
  cookie varchar(32),
  PRIMARY KEY  (user_id)
  );
