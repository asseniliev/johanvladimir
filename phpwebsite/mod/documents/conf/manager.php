<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: manager.php,v 1.1.1.1 2003/11/05 18:35:49 steven Exp $
 */

/* default labels
$id = $_SESSION['translate']->it("ID");
$label = $_SESSION['translate']->it("Label");
$updated = $_SESSION['translate']->it("Updated");
$desc = $_SESSION['translate']->it("Description");
$hide = $_SESSION['translate']->it("Hide");
$show = $_SESSION['translate']->it("Show");
$approve = $_SESSION['translate']->it("Approve");
$disapprove = $_SESSION['translate']->it("Disapprove");
$name = $_SESSION['translate']->it("Name");
*/

/* custom labels */
$id = $_SESSION['translate']->it("ID");
$date = $_SESSION['translate']->it("Date");
$description = $_SESSION['translate']->it("Description");
$hide = $_SESSION['translate']->it("Hide");
$show = $_SESSION['translate']->it("Show");
$approve = $_SESSION['translate']->it("Approve");
$delete = $_SESSION['translate']->it("Delete");
$name = $_SESSION['translate']->it("Name");
$uploaded = $_SESSION['translate']->it("Uploaded");

$lists = array("documents"=>"approved='1' AND hidden='0'",
	       "admin"=>"approved='1'",
	       "unapproved"=>"approved='0'",
	       "files"=>NULL,
	       "downloads"=>NULL);

$templates = array("documents"=>"list",
		   "admin"=>"admin",
		   "unapproved"=>"unapproved",
		   "files"=>"files",
		   "downloads"=>"downloads");

$tables = array("documents"=>"mod_documents_docs",
		"admin"=>"mod_documents_docs",
		"unapproved"=>"mod_documents_docs",
		"files"=>"mod_documents_files",
		"downloads"=>"mod_documents_files");

/* defualt document columns
$documentsColumns = array("id"=>$id,
			  "label"=>$label,
			  "updated"=>$updated,
			  "hidden"=>$hidden);
*/

$documentsColumns = array("id"=>NULL,
			  "country"=>$country,
			  "court_type"=>$courtType,
			  "doc_date"=>$date,
			  "description"=>$description);

$documentsActions = array();

$documentsPermissions = array();

$documentsPaging = array("op"=>"JAS_DocumentManager_op=list",
			 "limit"=>3,
			 "section"=>1,
			 "limits"=>array(3,5,7),
			 "back"=>"&#60;&#60;",
			 "forward"=>"&#62;&#62;");

/* default admin columns
$adminColumns = array("id"=>$id,
                      "label"=>$label,
                      "updated"=>$updated,
                      "hidden"=>$hidden);
*/

$adminColumns = array("id"=>$id,
		      "country"=>$country,
		      "court_type"=>$courtType,
		      "doc_date"=>$date,
		      "description"=>$description);

$adminActions = array("hide"=>$hide,
		      "show"=>$show);

$adminPermissions = array("hide"=>"hideshow_document",
			  "show"=>"hideshow_document");

$adminPaging = array("op"=>"JAS_DocumentManager_op=list",
		     "limit"=>3,
		     "section"=>1,
		     "limits"=>array(3,5,10),
		     "back"=>"&#60;&#60;",
		     "forward"=>"&#62;&#62;");

/* default unapproved columns
$unapprovedColumns = array("id"=>$id,
			       "label"=>$label,
			       "updated"=>$updated,
			       "hidden"=>$hidden);
*/

$unapprovedColumns = array("id"=>NULL,
			  "country"=>$country,
			  "court_type"=>$courtType,
			  "doc_date"=>$date,
			  "description"=>$description);

$unapprovedActions = array("approve"=>$approve,
			   "delete"=>$delete);

$unapprovedPermissions = array("approve"=>"approve_document",
			       "delete"=>"approve_document");

$unapprovedPaging = array("op"=>"JAS_DocumentManager_op=list",
			  "limit"=>3,
			  "section"=>1,
			  "limits"=>array(3,5,10),
			  "back"=>"&#60;&#60;",
			  "forward"=>"&#62;&#62;");

/* default files columns
$filesColumns = array("id"=>$id,
		      "name"=>$name,
		      "created"=>$uploaded);
*/

$filesColumns = array("id"=>$id,
		      "name"=>$name,
		      "created"=>$uploaded);

/* default download colums 
$downloadsColumns = array("id"=>$id,
			  "name"=>$name ,
			  "created"=>$uploaded);
*/

$downloadsColumns = array("id"=>$id,
			  "name"=>$name ,
			  "created"=>$uploaded);

?>