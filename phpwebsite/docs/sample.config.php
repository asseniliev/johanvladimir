<?php

// This is a sample config.php file. You can alter it if you are having problems
// with the automatic installer

// Set to your database. Choices are: ibase, mssql, mysql, msql, oci8, odbc, pgsql, sybase

$dbversion = "mysql";

// Set these to the appropiate information. Ask your web provider for more information
$dbhost    = "localhost";      /* Database Host */
$dbuser    = "your_username";  /* Database User Name*/
$dbpass    = "your_password";  /* Database Password */
$dbname    = "phpwebsite";     /* Database Name */

// Uncomment this the 'table_prefix' line if you wish to use a table prefix
// If this is a branch site, this will allow multiple branches in the
// same database.

// $table_prefix = "phpws_";

// Do not prefix with http://, make _sure_ there is a slash at the end
$source_http = "your.website.com/";

// Where your files are located. You might want to test this by pasting it
// into a terminal window like so:
// cd /var/www/html/your_directory/
// If there isn't a problem, it is safe.
// Make _sure_ there is a slash at the end
$source_dir = "/var/www/html/your_directory/";

/***********************************************************************************
 Your installation password
 You **MUST** change this in order to install phpWebSite
 If you keep the password as "default" you will not be able to
 install phpWebSite.
 If you do not plan on removing/renaming/changing permissions
 on the install.php file (though you should) make sure to enter
 a complex password here.
**********************************************************************************/
$install_pw = "default";

// This hash differentiates you from other websites. Make _sure_ to
// to make it as varied as possible
$hub_hash = "abcdefghijklmnopqrstuvwxyz123456789";

?>