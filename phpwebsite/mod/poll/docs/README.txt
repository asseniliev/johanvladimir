File:         README.txt
Author:	      Darren Greene
Purpose:      Documentation for the Poll module
Last Updated: June 9, 2003


Overview
-------------------------------
The Poll module of phpWebsite allows the ability to create a survey
question, which users can then cast their vote.  Each poll question
will have several choices for the user to chose from.  Typical
questions are opinion based such as 'How do you like the new website?'.  


Adding a new poll
-------------------------------
Log in as an administrator and click the menu option of Poll that
say 'New Poll'.

The fields for creating a new poll are described below along with an
example of each:

-Title-
This is not the field for the question, but instead a short title that
will be used as the block title.  The title should be short(one or two words).

Ex. 0.9.x

-Poll Question-
The main survey question you want your users to answer.

Ex. What you do think of the new site?

-Options-
The options are the different choices that users are allowed to choose
from to vote on a poll question.

Ex. Excellent
    Fair
    Horrible

If you would like to add another option then click the button 'Add Option', which
will insert a new blank option field.

-Users only-
If you would like for only registered users that are logged into
phpWebsite to be able to vote, then you should set this to 'YES', else
to allow all users the ability to vote, set this to 'NO'.  So if this
is set to 'NO' then users that are not logged in are able to still vote.

-Active-
Only one poll is allowed to be active at one time.  Active refers to
whether or not the poll will show up on the homepage for users to see
and cast their vote. 

-Allow Comments-
If you would like the ability for users to post comments to the poll
results then set this to 'YES', else set to 'NO'.


Listing, Updating Polls
---------------------------
To change the content of various features for a particular poll start
by logging in as an administrator, click the menu option to 'Show
Polls', and find the poll you would like to update.  

To delete a poll select the poll, choose the option to Delete
from the drop down menu, and press go.

To change a poll, select the poll you would like to update, choose Edit
from the drop down menu, and click Go.  The next screen will be the
poll you choose with its values for editing.


Show results of a poll
--------------------------
To track and see the latest stats on how users have voted, click the
the 'View Results' link at the bottom of the active poll on the main
homepage.  This will show a graphical representation of each option
for the poll question and show each option as a percentage of 100% that users
have choosen.  On the 'View Results' page any comments for this poll
will show at the bottom, if this comments were turned on for the poll.


FAQ
--------------------------
Why is my poll I created not showing up on the homepage?
Check to make sure the poll has been set to active.  You can only have
one poll active at one time.


Where do the user comments for a poll show up?
The comment will display on the 'View Results' page.


Why do I get a message that says I must be logged in to vote?
This message will appear if a poll has been set to only allow
registered users to vote.  If you would like for all users to be able
to vote, set the poll option for 'User Only' to NO.
