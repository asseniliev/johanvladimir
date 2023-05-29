CREATE TABLE branch_sites (
  branchName varchar(20) NOT NULL default '',
  configFile varchar(255) NOT NULL default '',
  IDhash varchar(32) NOT NULL default '',
  branchDir text NOT NULL,
  branchHttp varchar(255) NOT NULL default ''
);
