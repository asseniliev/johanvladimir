<?php

/**
 * Main control switch for Comment Manager module
 *
 * @version $Id: index.php,v 1.19 2003/11/06 20:52:25 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Comment Manager
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

if($GLOBALS["module"] == "comments") {
  $GLOBALS["CNT_comments"] = array("title"=>$_SESSION["translate"]->it("Manage Comment Threads"),
				   "content"=>NULL);
}

if (isset($_REQUEST['CM_op']))
switch($_REQUEST['CM_op']) {

 case "adminMenu":
   PHPWS_CommentActions::adminMenu();
   break;

 case "adminSettings":
   PHPWS_CommentActions::adminSettings();
   break;
   
 case "userSettings":
   PHPWS_CommentActions::userSettings();
   break;
   
 case "threadAction":
   PHPWS_CommentActions::threadAction();
   break;

 case "makeComment":
   PHPWS_CommentActions::makeNewComment();
   break;

 case "addAction":
   PHPWS_CommentActions::addAction();
   break;

 case "addPreviewAction":
   PHPWS_CommentActions::addPreviewAction();
   break;

 case "editAction":
   PHPWS_CommentActions::editAction();
   break;

 case "editPreviewAction":
   PHPWS_CommentActions::editPreviewAction();
   break;

 case "replyToComment":
   PHPWS_CommentActions::makeNewComment();
   break;

 case "viewComment":
   PHPWS_CommentActions::viewComment();
   break;

 case "editComment":
   PHPWS_CommentActions::editComment();
   break;

 case "deleteComment":
   PHPWS_CommentActions::deleteComment();
   break;

 case "refreshView":
   PHPWS_CommentActions::refreshView();
   break;

 case "listCurrent":
   PHPWS_CommentActions::listComments();
   break;
}

/* Memory Cleanup */
if(isset($_REQUEST['module']) && $_REQUEST['module'] != "comments") {
  unset($_SESSION['PHPWS_CommentManager']->threads);
}
?>
