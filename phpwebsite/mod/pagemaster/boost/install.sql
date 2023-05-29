CREATE TABLE mod_pagemaster_pages (
     id int PRIMARY KEY,
     title text NOT NULL,
     template varchar(50) NOT NULL,
     section_order text,
     new_page smallint NOT NULL DEFAULT '1',
     advanced smallint NOT NULL DEFAULT '0',
     approved smallint NOT NULL DEFAULT '0',
     mainpage smallint NOT NULL DEFAULT '0',
     active smallint NOT NULL DEFAULT '0',
     created_username varchar(20) NOT NULL,
     updated_username varchar(20) NOT NULL,
     created_date datetime NOT NULL,
     updated_date datetime NOT NULL,
     comments smallint NOT NULL DEFAULT '0',
     anonymous smallint NOT NULL DEFAULT '0'
);

INSERT INTO mod_pagemaster_pages VALUES ('Web Page Demo', 'default.tpl', 'a:1:{i:0;s:1:"1";}', 0, 0, 1, 1, 1, 'admin', 'admin', '2003-02-04 10:24:27', '2003-02-04 10:24:27');

CREATE TABLE mod_pagemaster_sections (
     id int PRIMARY KEY,
     page_id int,
     title text,
     text text,
     image text,
     template text
);

INSERT INTO mod_pagemaster_sections VALUES ('1', 'Web Page Demo Section', 
'phpWebSite provides a complete web site content management system. Web-based administration allows for easy maintenance of interactive, community-driven web sites.\r\n\r\n
phpWebSite\'s growing number of modules allow for easy site customization without the need for unwanted or unused features. Client output from phpWebSite is valid XHTML 1.0 and meets the W3C\'s Web Accessibility Initiative requirements.\r\n\r\n
Founded and hosted by the Web Technology Group at Appalachian State University, phpWebSite is developed by the phpWebSite Development Team, a network of developers from around the world. phpWebSite is free, open source software and is licensed under the GNU LGPL.\r\n\r\n
Thank you! - The phpWebSite Development Team\r\n\r\n
Theme packs are now available through the phpWebSite Community Project and can be found <a href=\"http://sourceforge.net/project/showfiles.php?group_id=81360&package_id=83571\">here</a>.', 'a:0:{}', 'default.tpl');
