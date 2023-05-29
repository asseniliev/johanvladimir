<?php

/**
 * Manager Configuration File for PhatForm
 * 
 * @version $Id: manager.php,v 1.15 2004/12/10 21:49:17 darren Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 */

/* Labels */
$id = $_SESSION['translate']->it("ID");
$label = $_SESSION['translate']->it("Name");
$owner = $_SESSION['translate']->it("Owner");
$editor = $_SESSION['translate']->it("Editor");
$created = $_SESSION['translate']->it("Created");
$updated = $_SESSION['translate']->it("Updated");
$hidden = $_SESSION['translate']->it("Hidden");
$approved = $_SESSION['translate']->it("Approved");
$ip = $_SESSION['translate']->it("Ip");
$view = $_SESSION['translate']->it("View");
$edit = $_SESSION['translate']->it("Edit");
$delete = $_SESSION['translate']->it("Delete");
$hide = $_SESSION['translate']->it("Hide");
$show = $_SESSION['translate']->it("Show");
$approve = $_SESSION['translate']->it("Approve");
$refuse = $_SESSION['translate']->it("Refuse");
$actions = $_SESSION['translate']->it("Actions");

$lists = array("saved"=>"approved='1' AND saved='1' AND 
                  (archiveTableName is NULL OR archiveTableName = '')",
	       "unsaved"=>"approved='1' AND saved='0' AND 
                  (archiveTableName is NULL OR archiveTableName = '')",
	       "unapproved"=>"approved='0' AND 
                  (archiveTableName is NULL OR archiveTableName = '')",
	       "user"=>"approved='1' AND saved='1' AND hidden='0' AND 
                  (archiveTableName is NULL OR archiveTableName = '')",
	       "options"=>NULL);

$templates = array("saved"=>"manager",
		   "unsaved"=>"manager",
		   "unapproved"=>"manager",
		   "user"=>"manager/user",
		   "options"=>"options");

/* BEGIN SAVED LIST SETTINGS */
$savedColumns = array("id"=>$id,
		      "label"=>$label,
		      "editor"=>$editor,
		      "updated"=>$updated,
		      "hidden"=>$hidden);

$savedActions = array("hide"=>$hide,
		      "show"=>$show,
		      "delete"=>$delete);

$savedPermissions = array("hide"=>NULL,
			  "show"=>NULL,
			  "delete"=>"delete_forms");

$savedPaging = array("op"=>"PHAT_MAN_OP=list",
		     "limit"=>10,
		     "section"=>1,
		     "limits"=>array(5,10,25,50),
		     "back"=>"&#60;&#60;",
		     "forward"=>"&#62;&#62;");

/* BEGIN UNSAVED LIST SETTINGS */
$unsavedColumns = array("id"=>$id,
			"label"=>$label,
			"editor"=>$editor,
			"updated"=>$updated,
			"hidden"=>$hidden);

$unsavedActions = array("delete"=>$delete);

$unsavedPermissions = array("delete"=>"delete_forms");

$unsavedPaging = array("op"=>"PHAT_MAN_OP=list",
		       "limit"=>10,
		       "section"=>1,
		       "limits"=>array(5,10,25,50),
		       "back"=>"&#60;&#60;",
		       "forward"=>"&#62;&#62;");

/* BEGIN UNAPPROVED LIST SETTINGS */
$unapprovedColumns = array("id"=>$id,
			   "label"=>$label,
			   "editor"=>$editor,
			   "updated"=>$updated,
			   "hidden"=>$hidden);

$unapprovedActions = array("approve"=>$approve,
			   "refuse"=>$refuse);

$unapprovedPermissions = array("approve"=>"approve_forms",
			       "refuse"=>"approve_forms");

$unapprovedPaging = array("op"=>"PHAT_MAN_OP=list",
			  "limit"=>10,
			  "section"=>1,
			  "limits"=>array(5,10,25,50),
			  "back"=>"&#60;&#60;",
			  "forward"=>"&#62;&#62;");

/* BEGIN USER LIST SETTINGS */
$userColumns = array("id"=>$id,
		     "label"=>$label,
		     "updated"=>$updated);

$userActions = array();

$userPermissions = array();

$userPaging = array("op"=>"PHAT_MAN_OP=list",
		    "limit"=>10,
		    "section"=>1,
		    "limits"=>array(5,10,25,50),
		    "back"=>"&#60;&#60;",
		    "forward"=>"&#62;&#62;");

/* BEGIN OPTION LIST SETTINGS */
$optionsColumns = array("id"=>$id,
			"label"=>$label);

$optionsExtraLabels = array("actions_label"=>$actions, 
			    "delete_label"=>$delete,
			    "edit_label"=>$edit);

?>