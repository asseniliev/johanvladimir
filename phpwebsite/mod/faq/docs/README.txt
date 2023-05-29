Filename:  README.txt
Purpose:   Provides user and administrative documentation for the FAQ module.
Author:    Darren Greene <dg49379@NOSPAM.tux.appstate.edu>

VERSION HISTORY
------------------------------

Last Revised: June 05, 2003


OVERVIEW
------------------------------

The FAQ module provides an interface to view frequently asked
questions and answers.  The questions that are answered over and over
by people calling or emailing you are the type of questions that
should be placed in the FAQ module. 

The FAQ module uses on an agorithm for sorting and listing FAQs, which consists of
the how many times a FAQ has been accessed, how long it has been
since it was last viewed, and the average rating given by users.  The rating system was
designed to force the FAQs that are accessed the most often and have
the highest score rating to appear toward the top of a users search results.


GETTING STARTED
------------------------------

Read the INSTALL.txt file for how to install the FAQ module.  After
FAQ is installed you will need to log in with the administrative
permission to add new FAQs.  If you do not login with the 
user permission to add FAQs, then you will only be able to suggest a
FAQ, which then must be approved by an administrator. To add a new
question click the 'New' link from the FAQ menu.  The FAQs you have
entered will then be shown by clicking the 'View' menu link.  


VIEWING FAQs
------------------------------

The button that says 'View FAQs' allows users to view all the FAQs that
have been approved and are not hidden.  The order of the FAQs will
depend on the composite scores.  The layout of the FAQs will be
determined by which layout mode is chosen under settings.


SUGGESTING FAQs
------------------------------

Users not logged in as an administrator with the permission to add
FAQs will only be able to suggest a FAQ.  This means that the FAQ
suggested must then be approved by an administrator.  For suggesting
FAQs the question and category fields are the only fields that are
required.  But, these must be filled in by an administrator before the
FAQ can be approved.  The email and name fields are only used as an
aid for administrators to contact the user who submitted the FAQ if
the need arises.  Once the FAQ has been approved the contact
information is no longer saved. 


SPECIAL NOTICES
------------------------------

-For Categories: It is suggested that you adjust the constant titled
FATCAT_LINK_CUTOFF in mod/fatcat/conf/config.php file to the number of
characters that you want to chopoff your questions to store in
fatcat.  You don't want this cutoff small, since the questions will
appear in the 'whats related' box as uncomprehensible.  In other
words, you don't want to have questions that show up as 'What about...'.

OPTIONAL
-Suggested layout changes that require the use of the Layout Manager
 module to adjust: 
-The user comments which will display when a FAQ is viewed would
probably look best if set to show at the bottom of the page.

-The Menuman box to add a new menulink, which will display in the
Settings  section after clicking 'Add menu link for the FAQ module'
would also look better if moved to the bottom of the page.


APPROVING FAQs
------------------------------
First login with an administrative account.

Option 1:  
Use the Approval module.

Option 2:  
The FAQ module has a menu option to search for 'Hidden and
Unapproved FAQs', which will list any FAQs that are waiting to be
approved.  So, select the FAQs to approve, choose Approve from the
list of choices, and click GO. 

Option 3:  
When you view a FAQ there is an administrative menu that has an
'Approve' option.  


DELETING, HIDING, ETC.
------------------------------
First login with an administrative account.

Option 1: 
Any of the administor queries under the 'stats' menu will provide the
means to delete, hide, show, approve, and delete FAQs selected from
the list.  If you need to change several FAQs, the recommended query
to use would be 'Show All FAQs in the datebase'.

Option 2: 
When you view a FAQ, there is an administrative menu
that has an all the options to edit,delete, hide, and approve.

 
STATS SECTION
------------------------------

By clicking on stats, an administrator can immediately see how many
FAQs are hidden, unapproved, and currently viewable by users.

The special queries section lets administrators quickly see which
FAQs are being accessed most frequently, have the highest score
ratings, and the FAQs that have not been viewed in a select number of
months.  The queries do not use static values to determine a 'high'
score or 'low' score but calculate these values by taking into
consideration all the FAQs in the datebase.  Hopefully, these queries
will be a tool for administrators to delete unused FAQs or change them
if they have a low score rating.


SETTINGS - OPTIONS
------------------------------

-Allow Users to Rate FAQs
This allows you turn off the ability for users to rate FAQs.  If this
option is turned off then the 'faq feedback' and 'How helpful was this
FAQ?' options will not appear on certain layout views.

-Allow users to post comments. 
Turn on and off whether to show comments when viewing FAQs. 

-Allow users to suggest FAQs. 
This option will disable the ability for non-administrators to suggest
FAQs and instead they will be presented with the list
of viewable FAQs without any type of menu.


SETTINGS - LAYOUTS
------------------------------

You may change the layout at anytime, even after FAQs have been
inserted.

Options:
Bookmarked questions
This view lists the questions at the top of the page and when you
click a question the page will jump down to the appropriate question
and answer.  You can set the number of FAQs that appear on a page
under 'settings'.

Question and Answer
The question is listed and then on the next line the answer is
provided.  You can set the number of FAQs that appear on a page under
'settings'. 

No categories - all questions
Under this view the questions are displayed and are hyperlinks a user
must click to go to the answer.  The number of FAQs that display on
the page is controlled by the user.

Categories
This view uses the categories from the Fatcat Module to group the
FAQs.  On the main page the FAQs are grouped under a particular
category choosen when the FAQ was created, a count of how many FAQs
are in that category is displayed beside the category name.  Once a
user clicks on a category name, a list of the FAQs that are in that
category is displayed, and to access the answer the user must
click on a FAQ.

There are four different layout views to choose from and are also explained
with examples by the help system, you can access help by clicking on
the ? help system icon beside the layout options.

If you choose 'Basic Question and Answer View' then the dynamic
sorting of FAQs will still be used, but will not be as effective.  The
reason is that users are able to view the FAQ answers directly instead
of having to click on the individual questions.  So, therefore the
hit count for each FAQ will not be updated unless the user clicks the
link for 'faq feedback'.  This will probably not be a problem since if
you use this view then you will probably not have many FAQs.


SETTINGS - LEGEND
------------------------------

The legend is used for giving users a description of what the score
ratings of 1 to 5 mean.  The text that shows up in the drop
down menu to score FAQs can be changed using these fields.


