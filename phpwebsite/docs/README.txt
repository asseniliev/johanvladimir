File:    README.txt
Author:  Adam Morton <adam@NOSPAM.tux.appstate.edu>
Updated: 2/28/2003
--------------------------------------------------

This is a general readme file for phpwebsite.  It's purpose is to give a basic
overview of the phpwebsite v0.9.x series and some of it's available features.
This document is not meant as a tutorial or as a manual.  It is only meant to
help people get their "sea legs" if you will. Basically this is an Adam brain
dump, so I cant be held responsible for spelling, grammar, or any other random
wierdness that may show up in this document :)

Concepts
--------
- Templates: Everything in the 0.9.x series of phpwebsite is templated using
template files and the pear HTML_Template_IT templating system. All the files
are strictly html and contain no php. This allows you to customize every aspect
of your site and allows for some very interesting themes.  These templates can
be saved with each theme as well, allowing you to create a whole new look and
feel for each theme.  It also allows you to "inject" your own text or html
directly into module output without editing any php code.  This is one of the
most powerful tools for the web designers out there who really don't have the
time to learn php just to create a great looking site.

- Content Variables: Every module is assigned a content variable by the layout
module (yes, even layout has it's own content variable). When any page is
rendered, phpwebsite uses these content variables to position any output
recieved from modules.  When you turn on the box move feature via layout, you
are simply moving content variables around (see Layout Manager below).  Some
modules appear to have two blocks when they actually have two content variables.
A module is allowed as many content variables as they wish, though I've never
needed more than 2 myself.

- Modules: The 0.9.x series of phpwebsite has a true modular nature unlike the
old 0.8.x series.  Every module has it's own files, templates, installation and
version information, and graphics.  phpwebsite itself is really just a main core
and several core modules.  If, during installation, you choose to install the
core only, only the core of phpwebsite and the core modules are installed.  All
other modules will have to be added by hand via Boost (see Boost below).  Some
core modules include layout, users, and boost.  Core modules are only deemed core
modules when they provide functionality used by every other module in the system.

- Users: Users in the 0.9.x series have only one account. That account can be
setup as a default user account or can be given administrative privilages to
specific modules within the site (granulated administration).  This eliminates
the confusion of users that are also admins having 2 accounts like in the 0.8.x
series.

Common Issues
-------------
- PEAR libraries: Currently phpwebsite calls ini_set() in the core/Core.php file
to set the path to your pear libs to the files provided by Appalachian State
University (./lib/pear/).  If you comment out this line, phpwebsite will try to
use your system's pear libraries.  If you have them up to date and setup correctly,
everything will be fine...if they are not setup correctly, everything will break
and it's not our fault :)

- Memory Limits: Also set in the ./core/Core.php file is the memory limit allowed
for the script.  phpWebSite is a very big script and grows as you add modules to
it.  This creates an interesting problem in that it eats up memory...fast.  So the
memory limit for phpwebsite is set to 12M from the normal default of 8M.  If you
choose to remove or comment out the ini_set() that sets this limit, you may have
problems running within the 8M constraint.  Personally, I found if I install just
the modules I'm going to actually use, phpwebsite runs well within the 8M limit.
We are working on restructuring some of the underlying code in hopes of resolving
this memory issue as fast as we can.

Post-Install
------------
What now?  Well now go to your site and log in using the Login box provided on the
left of your site and the username and password combination you specified during
install.  Once you are logged in successfully, the box will change showing a "Hello
user" message and some links.  The link you will use the most often, to the point
of wearing it out, is the "Control Panel" link.  When you click that link, a page is
displayed with tabs running across the top.  These tabs help to categorize different
modules you have installed.  Upon selecting a tab you will see links to all the
modules assigned to that tab. The "Logout" link in your user block will log the
current user out and the "Home" link will return you to the main page.  It is important
to note that the user that was created during install has deity rights to the
phpwebsite instance. This basically means the user is "GOD" (excuse the religious
expression) and can administrate and use all features without restrictions.

The Modules
-----------
Here is a breakdown of the functionality of each module included in the 0.9.x series.
Again this is not meant as a HOWTO or MANUAL, but more of a compass to get you moving
in the right direction:

- Layout Manager: This module allows admins to choose the theme to use for their
phpwebsite.  It also allows users to change that theme so they can have their own look
and feel when they visit your site. Probably the most powerful feature of the Layout
Manager is the ability to move and change the "boxes" on your site.  If you enter the
Layout Manager, a toolbar of sorts appears up top.  Click the "Move On" button to turn
on the moving feature. Small arrows will appear next to all blocks on your site.  By
clicking these arrows you can position any block where you would like.

- Site Search: This module is mostly used by other modules to allow their content to
be searchable.  If you enter a module where searching is available, a block will appear
with a search form in it allowing you to enter and perform searches on that module.

- Language Administrator: This module is used to do translations on phrases found on
your site.  There is an administrative side that allows you to actually do the
translations yourself or you can import language files that someone else provided to
you.  If you're interested in helping do translations for phpwebsite, learn how to use
this module :)

- User Manager:  The user manager does just what the name suggests. It allows you to
create and manage users and groups and their permissions on your site. Unlike the 0.8.x
series, there is not a "user account" and an "admin account".  You create a user then
give that user admin privilages if you wish. So no more remembering 2 logins for your
site :) You can also turn on a feature to allow you to edit the "Modules Menu". You can
remove and move modules around on the menu to suit your needs. Some lunch would be good
now...

- Announcements: The Announcements module allows you to post announcements to your site
that show up on the home page.  Users can also submit their own announcements, though
they must be approved by an admin before they will show up on the mainpage (See Approval
Module).

- Comment Manager: This module is mostly used by other modules to allow comment threads
to be associated with and item such as an announcement. Anywhere you see comments being
posted, this module is behind the scenes, handling all the work needed to track those
comments and their replies.

- Link Manager: This module allows you to add and manage links for your site. It's
functionality is currently lacking due to the fact you cannot "surf" your links according
to categories or any other way really. There will hopefully be more time spent on this in
the near future.

- Block Maker: This is the module that allows you to create blocks for your site. You
choose to have these blocks show up anywhere you wish and blocks are not designated as a
"right" block or "left" block anymore. You choose modules that you wish the block to show
up in.  Fairly straight forward.

- Menu Manager: This module allows you to create and manage multiple menus for your site.
On install there is a default menu created to show the basic format of an existing menu.
Menus can be hidden and shown depending on which module is active and links can be nested
and ordered.

- PageMaster: This module allows you to create and manage web pages for your site. Each
page is created a section at a time and is no longer restricted by "layouts" as in the old
0.8.x userpages.  Once created, pages can be added to the menu very easily and at the
admin's leisure.

- Form Generator: This module is quite powerful in the right hands.  It allows for forms
to be created and saved then presented to the public or to users who have accounts on your
site.  The results saved via the online form can be exported or viewed via Form Generator.
In the future we hope to add a polling feature to this module that would allow a poll-like
form to be created and show the results in percentages of answers, etc.

- Calendar: The calendar module allows you to post and track events that may be occuring
and you want to let your users know about.  With several views and many attributes that can
be assigned to these events, users are sure to know about upcoming events if you use this
module.  Some other features include fully templated calendar views allowing for many
different styles or formats, repeating events, and image support.

- Boost: This is the module installer for phpwebsite. It allows admins to install, upgrade,
and uninstall any module in the system.  To install a module, simply place it's files in
the ./mod/ directory and go into Boost. Assuming the developer of the module uses Boost
functionality, the module will show up in the module list and allow you to choose to install
the module. I could really use another soda...

- Security: This module allows you to do some basic security functions for your site. You
can ban IPs or allow only specific IPs and create and manage custom error pages.

- Branch Creator: If setup correctly, this module allows admins to create "branch" sites
that extend the "hub" site. Branch sites rely on the same codebase as the hub site but
need their own database to function correctly. When a branch is created there is a basic
directory structure that is created for that branch that contains any themes, images, or
other site specific files.

- Debugger: This module is mostly for development use.  It allows you to turn on debugging
information that is useful in tracking down errors in code.

- Module Maker: This module is mostly for development use. It allows you to change any
information for a given module (i.e.: module directory, module name, session variables,
class files, etc.).  What was that girl's number again?

- Help: The help system provides inline help where ever a programmer has taken advantage.
This module allows you to edit the help content for specific modules at any time and you
can turn on and off help for specific modules or site wide.

- Approval: This is the central approval body for the phpwebsite system. Upon visiting
this module, an admin is presented with any user submitted content that requires approval.
With a simple click of "Yes" or "No" an admin can accept or refuse the content submitted.

- FatCat: This module is mostly used by other modules to handle categorizing their data.
You can create or manage those categories through this module.
