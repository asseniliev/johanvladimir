<?php

/**
 * @version $Id: manager.php,v 1.5 2003/08/22 15:27:13 steven Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

/* Labels */
$label = $_SESSION['translate']->it("Name");
$editor = $_SESSION['translate']->it("Editor");
$updated = $_SESSION['translate']->it("Updated");
$desc = $_SESSION['translate']->it("Description");
$thumbnail = $_SESSION['translate']->it("Thumbnail");
$short = $_SESSION['translate']->it("Short");
$hidden = $_SESSION['translate']->it("Hidden");

$lists = array("albums"=>"approved='1'",
	       "description"=>"blurb IS NULL OR blurb=''");

$templates = array("albums"=>"albums",
		   "description"=>"description");

$tables = array("albums"=>"mod_photoalbum_albums",
		"description"=>"mod_photoalbum_photos");

$albumsColumns = array("label"=>$label,
		       "blurb1"=>$desc,
		       "updated"=>$updated,
		       "id"=>NULL,
		       "blurb0"=>NULL,
		       "image"=>NULL);

$albumsActions = array();

$albumsPermissions = array();

$albumsPaging = array("op"=>"PHPWS_AlbumManager_op=list",
		      "limit"=>10,
		      "section"=>1,
		      "limits"=>array(5,10,20,50),
		      "back"=>"&#60;&#60;",
		      "forward"=>"&#62;&#62;");

$descriptionColumns = array("thumbnail"=>$thumbnail,
			    "label"=>$short,
			    "updated"=>$updated,
			    "hidden"=>$hidden,
			    "id"=>NULL);

$descriptionActions = array();

$descriptionPermissions = array();

$descriptionPaging = array("op"=>"PHPWS_Album_op=desc",
			   "limit"=>10,
			   "section"=>1,
			   "limits"=>array(5,10,20,50),
			   "back"=>"&#60;&#60;",
			   "forward"=>"&#62;&#62;");

?>