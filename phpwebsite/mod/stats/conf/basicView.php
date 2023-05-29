<?php

$basicView = array();

if($GLOBALS["core"]->moduleExists("photoalbum")) {
  $basicView["photoalbum_albums"] = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_photoalbum_albums",
	"title"=>"Albums",
	"link"=>"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list");
}

if($GLOBALS["core"]->moduleExists("announce")) {
  $basicView["announce"] = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_announce",
	"title"=>"Announcements",
	"link"=>"./index.php?module=announce&amp;ANN_op=list");
}

$basicView["approval"] = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_approval_jobs",
	"title"=>"Approval Items",
	"link"=>"./index.php?module=approval&amp;approval_op=admin");

if($GLOBALS["core"]->moduleExists("calendar")) {
  $basicView["calendar"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_calendar_events",
	"title"=>"Calendar Events",
	"link"=>"./index.php?module=calendar&amp;calendar[view]=month");
}

if($GLOBALS["core"]->moduleExists("comments")) {
  $basicView["comments"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_comments_data",
	"title"=>"Comments",
	"link"=>" ");
}

if($GLOBALS["core"]->moduleExists("documents")) {
  $basicView["documents"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_documents_docs",
	"title"=>"Documents",
	"link"=>"./index.php?module=documents&amp;JAS_DocumentManager_op=list");
}

if($GLOBALS["core"]->moduleExists("documents")) {
  $basicView["documents"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_documents_files",
	"title"=>"Document Files",
	"link"=>"./index.php?module=documents&amp;JAS_DocumentManager_op=list");
}

if($GLOBALS["core"]->moduleExists("faq")) {
  $basicView["faq"]   = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_faq_questions",
	"title"=>"FAQs",
	"link"=>"./index.php?module=faq&amp;FAQ_op=viewFAQs");
}

if($GLOBALS["core"]->moduleExists("linkman")) {
  $basicView["documents"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_linkman_links",
	"title"=>"Links",
	"link"=>"./index.php?module=linkman&amp;LMN_op=userMenuAction");
}

if($GLOBALS["core"]->moduleExists("notes")) {
  $basicView["notes"] = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_notes",
	"title"=>"Notes",
	"link"=>"./index.php?module=notes&amp;NOTE_op=my_notes");
}

if($GLOBALS["core"]->moduleExists("photoalbum")) {
  $basicView["photoalbum_photos"] = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_photoalbum_photos",
	"title"=>"Photos",
	"link"=>"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list");
}


if($GLOBALS["core"]->moduleExists("poll")) {
  $basicView["poll"] = array(
        "table"=>$GLOBALS["core"]->tbl_prefix . "mod_poll",
	"title"=>"Polls",
	"link"=>"./index.php?module=poll&amp;poll_op=list"); 
}

$basicView["users"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_users",
	"title"=>"Users",
	"link"=>"./index.php?module=users&amp;user_op=panelCommand&amp;usrCommand[user]=edit");

if($GLOBALS["core"]->moduleExists("pagemaster")) {
  $basicView["pagemaster"] = array(
	"table"=>$GLOBALS["core"]->tbl_prefix . "mod_pagemaster_pages",
	"title"=>"Web Pages",
	"link"=>"./index.php?module=pagemaster&amp;MASTER_op=main_menu");
}

$firstElementTable = "mod_approval_jobs";

?>