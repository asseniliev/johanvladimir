

health 1.1, 2005-02-23
~~~~~~~~~~~~~~~~~~~~~~


by rck <http://www.kiesler.at/>



Based on the needs of the phpws support forum (http://phpwsforums.com/)
I've created my first phpws module. I'd recommend it to run all the time.
As soon, as you log in as a deity, it will do it's checking and show you,
wether there are any problems.


Checks right now include:

	- are the filesystems /, /tmp or /var full or crowded?
	- is the session-filesystem or the application-filesystem full or
	  crowded?
	- is safe mode on?
	- is register globals on?
	- is there enough memory allocated for php?
	- how much memory does php use actually?
	- is the server unix based?
	- is phpwebsite running on apache?
	- are file uploads enabled?
	- is phpwebsite caching enabled?


The manual mode (mod/health/manual.php) is capable of the following checks:

	- are there all files of health installed?
	- have they been tampered with?
	- are there all files of phpwebsite installed?
	- is the source directory of phpwebsite set up correctly?


Installing health is rather easy. Just put it in the mod-directory
like any other phpwebsite module and boost it. Health will be running
all the time from now on, as soon as a deity logs in. Other users or
guests won't take any processing power of your server.



CONTACT


Did you get any warnings or errors? Got questions about health or
phpwebsite in general? Please visit us at the phpwebsite support
forums:

	http://phpwsforums.com/


Got suggestions for improving this module? Maybe even code for
actually checking certain things? Please post in the forum of the
author:

	http://www.kiesler.at/


Happy 2005!



CHANGELOG


health 1.1: 2005-02-23

	new tool: show user variables,
	bugfix: didn't update the checksum for manual mode

	- shows all set user variables

	- released earlier than planned, as billypurdue
	  noticed that manual mode wouldn't work
	  (http://phpwsforums.com/showthread.php?p=7395#post7395)


health 1.0: 2005-02-13

	two new main modes: Overview and Tools

	- Overview shows you the cpu-utilization of your server,
	  how many users are registered with your site, who's the
	  youngest. also:

	  o how many modules
	  o what is in the cache
	  o how much content do you have (announcements, articles,
	    comments, posts, notes, webpages)

	- Tools enables you to clear the phpWebSite cache, show
	  in which tables your modules are maintained by phpWebSite
	  as well as show the boxes of your themes. All of them,
	  even the invisible ones.

	- The new main-menu also contains a collection of links from
	  this version on.


health 0.4: 2005-01-18

	new checks

	- is register_globals enabled? if yes, this would be
	  a security risk.

	- is there enough space in the sessions-directory?
	  if the session-directory equals /tmp, that test
	  will be skipped.

	- is there enough space in the application directory?

	- (in manual mode) does the database information you
	  entered in the config.php work?

	- (in manual mode) does the PEAR::DB connection work?

	- bugfix: there are versions of php4.3.4 and older, that
	  don't include the apache_check_version function. So
	  I've included a function_exists check there as well.



health 0.3: 2005-01-06

	engine stayed the same but new "manual" mode. Can
	be envoked by calling mod/health/manual.php.

	checks of manual mode:

        - are there all files of health installed?
        - have they been tampered with?
        - are there all files of phpwebsite installed?
        - is the source directory of phpwebsite set up correctly?


health 0.2: 2005-01-04

	health can be disabled, three new checks.

	- you can disable health from this version on. set
	  $active to false in mod/health/conf/config.php to
	  do so.

	- is GD installed properly? health now checks for
	  a php version newer than 4.3.0 as well as wether
	  the imagecreate function is available.

	- is the image directory writeable?

	- has the setup directory been removed?

	- also, there's now a second link in the status
	  window: "view only warnings and errors". If you
	  press it, health won't show you things that
	  are ok.



health 0.1.1: 2005-01-03


	enhanced robustness. three bugfixes and a new check.


	- are we running on windows? if yes, we won't check
	  memory_get_usage(). thanks to Yves Kuendig.

	- are the methods memory_get_usage and disk_free_space
	  available? if no, we'll print out a ERROR. thanks
	  to Q.

	- is a open_basedir restriction in effect? if yes,
	  we'll warn the user and won't check the free space.
	  thanks to GardeTerbot.


	new check:

	- if the open_basedir restriction is in effect, we'll
	  print out, for what directories.



health 0.1: 2005-01-02

	initial version

	checks: 

		- are the filesystems /, /tmp or /var full or crowded?
		- is safe mode on?
		- is there enough memory allocated for php?
		- how much memory does php use actually?
		- is the server unix based?
		- is phpwebsite running on apache?
		- are file uploads enabled?
		- is phpwebsite caching enabled?

