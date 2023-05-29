<?php

$id = $_SESSION['translate']->it("ID");
$label = $_SESSION['translate']->it("Name");
$owner = $_SESSION['translate']->it("Owner");
$editor = $_SESSION['translate']->it("Editor");
$created = $_SESSION['translate']->it("Created");
$view = $_SESSION['translate']->it("View");
$edit = $_SESSION['translate']->it("Edit");
$delete = $_SESSION['translate']->it("Delete");
$hide = $_SESSION['translate']->it("Hide");
$show = $_SESSION['translate']->it("Show");
$visibility = $_SESSION['translate']->it("Visibility");
$body = $_SESSION['translate']->it("Description");
$restrict = $_SESSION['translate']->it("Restricted");
$active = $_SESSION['translate']->it("Active");
$comments = $_SESSION['translate']->it("Allow Comments");

/* define the lists manager will control and 
	the database constraint for identifying a list member */
$lists = array("admin"=>"id=id",						/* an admin and a user list */
				"user"=>"hidden=0");		/* user list omits hidden items */

/* For each list, indicate templates directory where the manager will find layout templates.
	Must have list.tpl and row.tpl for each list. */
$templates = array("admin"=>"manager",
					"user"=>"manager/user");

$adminColumns = array("id"=>$id,
				"active"=>$active,
				"restricted"=>$restrict,
				"allowComments"=>$comments,
				"label"=>$label,
				"body"=>$body,
				"created"=>$created,
                "hidden"=>$visibility);

$adminActions = array("edit"=>$edit,
				"delete"=>$delete,
                "view"=>$view,
                "show"=>$show,
                "hide"=>$hide);

$adminPermissions = array("edit"=>NULL,
				"delete"=>NULL,
                "view"=>NULL,
                "show"=>NULL,
                "hide"=>NULL);
                
$adminPaging = array("op"=>"PHPWS_MAN_OP=list",
             "limit"=>10,
             "section"=>1,
             "limits"=>array(5,10,25,50),
             "back"=>"&#60;&#60;",
             "forward"=>"&#62;&#62;");
                
$userActions = array("view"=>$view);

$userPermissions = array("view"=>NULL);

$userColumns = array("id"=>$id,
				"active"=>$active,
				"restricted"=>$restrict,
				"allowComments"=>$comments,
				"label"=>$label,
				"body"=>$body,
				"created"=>$created);

$userPaging = array("op"=>"PHPWS_MAN_OP=list",
             "limit"=>10,
             "section"=>1,
             "limits"=>array(5,10,25,50),
             "back"=>"&#60;&#60;",
             "forward"=>"&#62;&#62;");
?>