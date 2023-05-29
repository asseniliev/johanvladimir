Module     : Guestbook
Version    : 2.1
System     : phpwebsite 0.9.x
Author     : badguy <http://phpwebsite.chula-rural.net/>
Bug report : http://phpwebsite.chula-rural.net/mod.php?mod=bugs
Last update: 30/04/2003
Updated again by John Bartram - eSolutionsWork.com - 2/11/2004

License
-------
http://www.gnu.org

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the  GNU General Public License for more details.

Introduction
------------

Portions of the Guestbook plugin are based on GPL code from the following:

phpWebSite
Copyright (c) 2000, 2001 Web Technology Group, Appalachian State University

Advanced Guestbook 2.3.1 (PHP/MySQL)
Copyright (c)2001 Chi Kien Uong
URL: http://www.proxy2.de

Requirements:
- PHP 4.0+
- phpWebSite 0.9.x
- MySQL database (with appropriate permissions).

Installation
------------
Fresh install

    1. Unzip the guestbook module files to a temporary directory.
    2. Upload folder and files from temporary directory 'guestbook' to your module directory.
    3. Give write permissions to these directories:
        - mod/guestbook/public - 777 (drwxrwxrwx) (directory)
        - mod/guestbook/tmp    - 777 (drwxrwxrwx) (directory)
    4. Login as 'administrator' and install guestbook module from Boost
    5. You may also wish to make a link on your menu to 'index.php?module=guestbook'
    6. If you want to show any menu during open phpBB module, go to Menu Manager => select menu => Edit => Setting. Add guestbook module to menu's 'Allow View' by Ctrl+click on 'guestbook' in modules list then save the setting.

Upgrade from guestbook module 1.10 for phpwebsite 0.8.x

    1. Export your guestbook table ({$table_prefix}mod_guestbook) from phpwebsite 0.8.x database to text file.
    2. Change $table_prefix in text file from step 1 to phpwebsite 0.9.x's $table_prefix
    3. Import text file (guestbook's database) from step 2 to phpwebsite 0.9.x database.
    4. Follows fresh install instruction.

Notes:
------
1. When you log in as Admin and go to the guestbook module (in Adminstration/Administration), the menu to use is not the default, but under General Settings.
2.The option Show Guestbook's Side Box does not appear to work.
3.To see the smilies, you must change the url for the smilies folder. Look in admin/config.inc.php line 55:
$TEC_MAIL  = "you_at_your_domain_dot_com";
Use you own site's url here.

Uninstall
---------
    1. Login as 'administrator' and uninstall guestbook module from Boost
    2. delete the guestbook dir in your module dir.

DISCLAIMER
----------
I don't accept responsibility if this piece of code screws up your database or causes your site to crash, so PLEASE BACKUP YOUR FILES BEFORE YOU INSTALL IT! 
