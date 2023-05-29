File:    Item and Manager Readme/Howto
Authors: Adam Morton <adam@NOSPAM.tux.appstate.edu>
         Steven Levin <steven@NOSPAM.tux.appstate.edu>
Updated: 05/22/2003
------------------------------------------------------

A common relationship found in almost every module written
for phpwebsite is the Item to Manager relationship. The Item
is the actual object being manipulated by the module and the
Manager handles listing and operations on multiple Items. The
phpwebsite core provides 2 classes that take advantage of this
relationship to make development of a module faster, easier,
and more stable.  The 2 classes are PHPWS_Item and PHPWS_Manager.

PHPWS_Item:
-----------
Item does many things automatically for programmers that was
being re-implemented every time a module was created.  It tracks
who and when items were created or updated. It abstracts basic
database interaction (inserts, updates, etc).  Also, it tracks
whether your item is hidden and/or approved.

Before you can effectively use this class, you must identify
what pieces of your module are actually "items". Some examples
of items would be:

- a block in the blockmaker module
- a page and/or section in the pagemaster module
- an announcement in the announce module

Once you have identified these items and are ready to create
classes for them, you can begin to take advantage of the
functionality provided in PHPWS_Item.

Make sure your "item" class extends PHPWS_Item:

class MyItem extends PHPWS_Item {
}

By extending the Item class you now have access to standard
functions and variables that are found in most every item.
Basically, you don't have to implement half of your item.

Here are some of the basic functions you will be using in
Item:

- setTable()
- setId()
- addExclude()
- init()
- commit()
- kill()

There are also "set" and "get" functions provided that allow
you to manually manipulate the variables in your item.

Lets assume that the example item has 3 variables that are
specific to it.  Out of these 3 variables, only 2 need to be
saved in the database at any given time. The table being used
to store these item will be "mod_mymod_myitems". Here is our
example class now:

class MyItem extends PHPWS_Item {
  var $_subject;
  var $_body;
  var $_test;
}

The table in the database cooresponding to this item must
have a structure similar to this:

CREATE TABLE mod_mymod_myitems(
        id int NOT NULL default 0,
        owner varchar(20) default '',
        editor varchar(20) default '',
        ip text,
        label text NOT NULL,
        groups text,
        created int NOT NULL default 0,
        updated int NOT NULL default 0,
        hidden smallint NOT NULL default 1,
        approved smallint NOT NULL default 0,
        subject text NOT NULL,
        body text,
        PRIMARY KEY (id)
);

The first columns from id to approved are needed for PHPWS_Item
to work correctly.  The last 2 columns are needed by our example
module.  Notice the variable 'test' does not have a column. This
will be important when creating the constructor:

function MyItem($MY_ID=NULL) {
  /* These variables will be excluded when calling commit() */
  $exclude[] = "_test";

  $this->addExclude($exclude);
  $this->setTable("mod_mymod_myitems");

  if(isset($MY_ID)) {
    $this->setId($MY_ID);
    $this->init();
  }
}

Above is the most basic constructor using Item.  Here is a breakdown
of what is happening:

- An exclude array is created, contatining variables that are to
  be ignored by PHPWS_Item when doing database interaction.

- This exclude array is then added to the item using addExclude().

- Now the table containg the data for this item is set using
  setTable().

- If we received an ID, this item is already present in the database,
  so we set the id of this item with setId() and then call the init()
  function. (Note: The id and table name must be set before you call
  init()).

- init() hits the database and sets all your class variables based on
  the data found.

Now our MyItem object is initialized and all the class variables are
ready to go.  We can carry out operations in our module as much as we
wish now.  If changes are made to the class variables that you want
to see saved to the database simply call the commit() function like
this:

function save() {
  $this->_subject = $_REQUEST["MYMOD_Subject"];
  $this->_body = $_REQUEST["MYMOD_Body"];

  $result = $this->commit();
  if(PHPWS_Error::isError($result)) {
    $result->errorMessage("CNT_mymod");
  } else {
    return "Your item was successfully saved!";
  }
}

Upon the call to commit(), Item will do one of 2 things.  One, if your
item is new (i.e.: it has no id), Item will insert a new row into your
table and set the id of your item for you. Item will also set the owner's
username and created date as well as the updated user and date. Two,
if your item is an existing one, Item will update the database, saving
all your class variables as well as saving the updated date and user
information.

If at any time you would like to access the information stored in your
item, use the provided "get" functions.

PHPWS_Manager:
--------------
The PHPWS_Manager is designed to handle listing, sorting, and basic
operations for multiple PHPWS_Item(s). To begin using Manager you must
first determine how many lists you are going to need and what properties
each list will have (i.e.: saved items, unsaved items, unapproved items).
Once you have determined this, you can create your manager configuration
file.  The file must be named "manager.php" and must be located in your
module's conf/ directory.  Here is an example config file (for a more
detailed example see manager.php.txt in the ./docs/developer/ directory):

/* The name of your lists and their corresponding database constraints */
$lists = array("mylist"=>"approved='1' AND saved='1'");

/* The name of the table to pull the list from */
/* This was added as of 06/18/2003 */
$tables = array("mylist"=>"mod_mytable_myitems");

/* The directory where your templates are located "mod/mymod/templates/dir/" */
$templates = array("mylist"=>"manager");

/* Text to show for PHPWS_Item variables */
$hiddenValues = array(0=>"Public",
                      1=>"Private");

$approvedValues = array(0=>"Inactive",
                        1=>"Active");

/* The columns to list for our defined "saved" list and their labels */
$mylistColumns = array("id"=>"ID",
                       "label"=>"TITLE",
                       "editor"=>"EDITED BY",
                       "updated"=>"UPDATED DATE",
                       "hidden"=>"HIDDEN");

/* The actions to show in the defined "saved" list and their labels */
$mylistActions = array("view"=>"View",
                       "edit"=>"Edit",
                       "hide"=>"Hide",
                       "show"=>"Show",
                       "delete"=>"Delete");

/* The permissions associated with the defined actions above */
$mylistPermissions = array("view"=>NULL,
                           "edit"=>"edit_myitem",
                           "hide"=>NULL,
                           "show"=>NULL,
                           "delete"=>"delete_myitem");

/* The default paging information to use when paging lists */
$mylistPaging = array("op"=>"MY_MANAGER_OP=Main",
		     "limit"=>10,
		     "section"=>1,
		     "limits"=>array(5,10,25,50),
		     "back"=>"&#60;&#60;",
		     "forward"=>"&#62;&#62;");

This config file defines a list "mylist" that will display the columns
id, label, editor, updated, and hidden.  The actions available to the
user are view, edit, hide, show, and delete.  The permissions imposed
on edit and delete are edit_myitem and delete_myitem.  These permissions
must be implemented by the programmer in the module_rights.txt file
in your module's conf/ directory.  The text used in the action array can
be translated in the config using the $_SESSION["translate"]->it()
function if you wish.

The templates must reside in a sub-directory of your modules template
directory (ie. the templates for mylist are in mymod/templates/manager/)
For some example list and row template look in the templates directory
of your phpwebsite base (they are called defaultList.tpl and defaultRow.tpl)
Although when they are located in you mods template directory they must be
named list.tpl and row.tpl.  The template tags that the manager creates to
be parsed in your templates match exactly what your variables are named,
without the underscore prefix if your var has that to distinguish it as
a private variable.  For the columns it will add on a _LABEL to the end
of it to mark it as a column heading.  So for our example manager would
make the following template tags for list.tpl: {ID_LABEL}, {LABEL_LABEL},
{EDITOR_LABEL}, {UPDATED_LABEL}, {HIDDEN_LABEL}.  For the row.tpl the manager
would create the template tags: {ID}, {LABEL}, {EDITOR}, {UPDATED}, {HIDDEN}.

There are some tags that are always replaced in the list.tpl if you just add
the tag. {SELECT_LABEL}, {TOGGLE_ALL}.  You would only want to add these if
you are making a form out of your list to have thge select column and toggle
all link to check all the item checkboxes. Tags which are always replaced for
a row.tpl include: {SELECT} for the checkbox next to the item if you are making
a form with the list and {ROW_CLASS} which will add a style sheet class for
making rows alternate colors.

The variables $hiddenValues and $approvedValues allow you to overide the
text the manager will show for the two PHPWS_Item variables $hidden and
$approved.  If left out these columns will show the text Hidden and Visible
for the $hidden variable, and Approved and Unapproved for the $approved
variable.

The information contained in the $mylistPaging array is used by
PHPWS_Manager to support paging for your lists.  The 'op' is the default
op to get to your particular list.  The limit is the number of items
shown per page in your list.  The 'section' variable designates whether
or not to show the section links for your list.  'Limits' is an array of
item limits that you would like shown.  'Back' is the textual link or
image tag you would like to use for the back link when paging and 'forward'
is the same but for the forward link.  Look at the defaultList.tpl
to see what template tags are parsed for the paging links and information
to make it into your list. Anything with a prefix NAV_ has to do with 
the paging information.

Now you can create your manager class which extends PHPWS_Manager:

class MyManager extends PHPWS_Manager {
}

Your constructor for MyManager needs to contain the following:

function MyManager() {
  $this->setModule("mymod");
  $this->setRequest("MYMOD_OP");
  $this->init();
}

This tells your manager the information needed to locate your config
file, templates, and database table for creating it's lists.  It also
tells manager what op variable to use when executing operations from
these lists.  Then init() will use this information to intialize the
manager's variables and get it ready to display lists.

Now to display your list of items use the following function call:

$content = $this->getList("mylist", "My List of Items");

This function returns the templated list in the form of a string.
Once the programmer has that string, they can display it where ever
they wish.  If you are using the manager to display more than one
list you will need to call setTable() to tell the Manager where it 
is listing from.  Otherwise if you only have one list you could just
put the call to setTable() in your constructor and not have to set
it again anymore.

Actions can be handled from this list using the request variable we
defined with setRequest(). The possible actions for our example here
would be:

- $_REQUEST["MYMOD_OP"] = "view";
- $_REQUEST["MYMOD_OP"] = "edit";
- $_REQUEST["MYMOD_OP"] = "hide";
- $_REQUEST["MYMOD_OP"] = "show";
- $_REQUEST["MYMOD_OP"] = "delete";

You as the programmer, can handle these actions any way you want.

You set the request variable to "PHPWS_MAN_OP", the Manager will handle 
some operations for you.  In the case of hiding or showing, Manager can 
update multiple items that were selected from your list. The same is true 
for approve and refuse.  However, things get a little tricky when an list, 
edit or view action is taken. There are two template tags which the manager 
will parse in the list.tpl that will add the other form stuff you need to 
get an action to execute: {ACTION_SELECT} {ACTION_BUTTON}
this will provide a dropdown of actions and a go button to submit the form.

When these actions are selected, Manager will call either the "_edit"
function or "_delete" function for your Manager.  These function names
can be changed by using the setEditFunction() and setDeleteFunction()
respectively, if you wish to name your functions differently.  There
are other default functions you will need to implement as well. The
Manager will automatically pass these functions an array of item ids
that were selected and the programmer can handle this array any way
they wish. Here is a list of the functions needed and their purpose:

_list():       Your basic listing function.
_edit($ids):   The function to call to edit an item(s).
_delete($ids): The function to call to delete an item(s).
_view($ids):   The function to call to view an item(s).

You can either implement the default functions that manager will call
like _list(),_edit() and _view(), or use the provided set functions in
Manager to set the names of the functions you have implemented for these
actions (i.e.: setDeleteFunction, setEditFunction, etc.). Manager will
call these defined functions instead of the default ones for you.

To have manager handle operations for you you must include the following
in your index.php file BEFORE you check for MyManager's operations:

if($_REQUEST["PHPWS_MAN_OP"]) {
  $_SESSION["MyManager"]->managerAction();
}

This just checks to see if PHPWS_Manager's op was set and calls the
appropriate action function if it was.

New Item/Manager functionality as of 06/18/2003
------------------------------------------------------
You can now have the Manager instantiate an object for you when getting a
list.  If you call setClass() before getting your list and pass it the name
of the class the manager will create the object for you.  In order for this
to completely work you must modify your Item's constructor function to accept
an associative array (the row from the database passed by the manager) to be
created with.  All you have to do is pass that array to init() and PHPWS_Item 
will take care of the rest.  So here is an example of a new Item constructor:

function PHPWS_MyItem($id=NULL) {
  if(is_int($id)) {
    $this->setTable("mytable");
    $this->setId($id);
    $this->init();
  } else if(is_array($id)) {
    $this->setTable("mytable");
    $this->init($id);
  }
}

Another new feature is the ability for PHPWS_Manager to call get functions
for class varaibles you are showing in a list.  All you must do is have the
Manager construct an object as described above.  If you put the class varaible
in you listColumns array then the manager will check for getyourclassvariable()
and if it exists then it will use that to get the value.  Otherwise it will
use what it got out of the database.  This is helpful for manipulating data 
before you how it.  (Ex. unix time stored in the database is just one big int.
you can modify that int to be human readable before showing it to the user.

Other Useful Function for Manager
-------------------------------------------
setSort():  This function will allow you to add extra sort paramaters
            to the sql query that the manager generates.  So you could
            then use a previously defined list and add a couple of extra
            constraints and you have a whole new list.

Example usage:
$this->setSort("hidden='0'");
$this->getList("mylist");

setOrder():  Works just like set sort but it is for setting extra order
             contraints.

anchorOn():  Turn on anchors for a list.  This will cause the page to 
             jump down to the list if the page is big when you are paging 
             or sorting columns.

Example Usage:
$this->anchorOn();
$this->getList("mylist");
$this->anchorOff(NULL);

We hope you switch to using PHPWS_Item and PHPWS_Manager soon.  We did
and it's saved us hours of tedious, repetative work.

Report errors or requested changes to this document to:
adam@NOSPAM.tux.appstate.edu
           -or-
steven@NOSPAM.tux.appstate.edu
