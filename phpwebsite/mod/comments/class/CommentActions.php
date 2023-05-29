<?php

/**
 * Action class for Comment Manager module 
 *
 * @version $Id: CommentActions.php,v 1.16 2004/08/24 13:49:47 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Comment Manager
 */

class PHPWS_CommentActions {

  /**
   * adminMenu
   *
   * provides actions to menu selections
   */
  function adminMenu() {
    if($_SESSION['OBJ_user']->allow_access("comments", "settings")) {
      $_SESSION['PHPWS_CommentManager']->adminMenu();
      
      if(isset($_REQUEST['CM_adminSettings'])) {
	$_SESSION['PHPWS_CommentManager']->adminSettings();
      } else {
	$_SESSION['PHPWS_CommentManager']->listThreads();
      }
    } else {
      $_SESSION['PHPWS_CommentManager']->accessDenied(); 
    }
  }

  /**
   * adminSettings
   *
   * action for admin settings
   */
  function adminSettings() {
    if($_SESSION['OBJ_user']->allow_access("comments", "settings")) {
      $_SESSION['PHPWS_CommentManager']->adminMenu();    
      $_SESSION['PHPWS_CommentManager']->adminSettings();
    } else {
      $_SESSION['PHPWS_CommentManager']->accessDenied(); 
    }
  }

  /**
   * userSettings
   *
   * action for user settings
   */
  function userSettings() {
    $_SESSION['PHPWS_CommentManager']->userSettings();
  }

  /**
   * threadAction
   *
   * actions taken for an entire thread
   */
  function threadAction() {
    $_SESSION['PHPWS_CommentManager']->adminMenu();
    if(isset($_REQUEST['CM_viewThread'])) {
      $_SESSION["PHPWS_CommentManager"]->listCurrentComments($_REQUEST['CM_module'], $_REQUEST['CM_itemId'], FALSE);      
    } else if(isset($_REQUEST['CM_deleteThread'])) {
      if($_SESSION['OBJ_user']->allow_access("comments", "delete_thread")) {
	$_SESSION["PHPWS_CommentManager"]->deleteThread($_REQUEST['CM_module'], $_REQUEST['CM_itemId']);
      } else {
	$_SESSION["PHPWS_CommentManager"]->accessDenied();
      }
    }
  }

  function addAction() {
    if(isset($_REQUEST['CM_preview'])) {
      $_SESSION['PHPWS_CommentManager']->currentComment->previewComment("add");
    } else if(isset($_REQUEST['CM_post'])) {
      if($_SESSION['PHPWS_CommentManager']->currentComment->postComment(FALSE)) {
	$_SESSION['PHPWS_CommentManager']->goBack();
      }
    }
  }
  
  function addPreviewAction() {
    if(isset($_REQUEST['CM_edit'])) {
      $_SESSION['PHPWS_CommentManager']->currentComment->comment("add");
    } else if(isset($_REQUEST['CM_post'])) {
      if($_SESSION['PHPWS_CommentManager']->currentComment->postComment(TRUE)) {
	$_SESSION['PHPWS_CommentManager']->goBack();
      }
    }
  }
  
  function editAction() {
    if(isset($_REQUEST['CM_preview'])) {
      $_SESSION['PHPWS_CommentManager']->currentComment->previewComment("edit");;
    } else if(isset($_REQUEST['CM_post'])) {
      if($_SESSION['PHPWS_CommentManager']->currentComment->postComment(FALSE)) {
	$_SESSION['PHPWS_CommentManager']->goBack();
      }
    }
  }
  
  function editPreviewAction() {
    if(isset($_REQUEST['CM_edit'])) {
      $_SESSION['PHPWS_CommentManager']->currentComment->comment("edit");
    } else if(isset($_REQUEST['CM_post'])) {
      if($_SESSION['PHPWS_CommentManager']->currentComment->postComment(TRUE)) {
	$_SESSION['PHPWS_CommentManager']->goBack();
      }
    }
  }
  
  function makeNewComment() {
    $_SESSION['PHPWS_CommentManager']->currentComment = new PHPWS_Comment;
    $_SESSION['PHPWS_CommentManager']->currentComment->comment("add");
  }
  
  function refreshView() {
    $_SESSION['PHPWS_CommentManager']->setUserListView($_REQUEST['CM_listView']);
    $_SESSION['PHPWS_CommentManager']->setUserListOrder($_REQUEST['CM_listOrder']);
    $_SESSION['PHPWS_CommentManager']->goBack();
  }
  
  function editComment() {
    $_SESSION['PHPWS_CommentManager']->currentComment = new PHPWS_Comment($_REQUEST['CM_cid']);
    $_SESSION['PHPWS_CommentManager']->currentComment->comment("edit");
  }

  function deleteComment() { 
    $_SESSION['PHPWS_CommentManager']->currentComment = new PHPWS_Comment($_REQUEST['CM_cid']);
    $_SESSION['PHPWS_CommentManager']->currentComment->delete(); 
  }

  function viewComment() {
    $cid = $_REQUEST['CM_cid'];
    if(!isset($_SESSION['PHPWS_CommentManager']->module) || !isset($_SESSION['PHPWS_CommentManager']->itemId)) {
      $sql = "SELECT module, itemId FROM {$GLOBALS['core']->tbl_prefix}mod_comments_data WHERE cid='$cid'";
      $threadResult = $GLOBALS['core']->query($sql);
      $thread = $threadResult->fetchrow(DB_FETCHMODE_ASSOC);
      $_SESSION['PHPWS_CommentManager']->module = $thread['module'];
      $_SESSION['PHPWS_CommentManager']->itemId = $thread['itemId'];      
      $_SESSION['PHPWS_CommentManager']->linkBack = "./index.php?module=comments&CM_op=threadAction&CM_viewThread=1&CM_module=" . $thread['module'] . "&CM_itemId=" . $thread['itemId'];
    }
     
    $goBack = $_SESSION['PHPWS_CommentManager']->getLinkBack(); 

    $_SESSION['PHPWS_CommentManager']->currentComment = new PHPWS_Comment($cid);
    $comment = $_SESSION['PHPWS_CommentManager']->currentComment->viewComment();
    $title = $_SESSION['translate']->it("Comments");

    if((isset($_SESSION['OBJ_user']) && $_SESSION['OBJ_user']->isUser()) || $_SESSION['PHPWS_CommentManager']->getAllowAnonymous()) {
      $title .= "&#160;<b>-</b>&#160;" . "<a href=\"./index.php?module=comments&amp;CM_op=makeComment\">" . $_SESSION['translate']->it("Make a comment") . "</a>";
    }

    $content = $comment;

    if($goBack) {
      $content .= "<a href=\"$goBack\">" . $_SESSION['translate']->it("Back To Thread") . "</a>";
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['title'] = $title;
    $GLOBALS['CNT_comments']['content'] = $content;
  }

  function search($where) {
    $_SESSION['PHPWS_CommentManager']->module = NULL;
    $_SESSION['PHPWS_CommentManager']->itemId = NULL;
    $_SESSION['PHPWS_CommentManager']->linkBack = NULL;

    $sql = "SELECT cid, subject FROM {$GLOBALS['core']->tbl_prefix}mod_comments_data " . $where;
    $commentResult = $GLOBALS['core']->query($sql);
    if($commentResult->numrows()) {
      while($comment = $commentResult->fetchrow(DB_FETCHMODE_ASSOC)) {
	$results[$comment['cid']] = $comment['subject'];
      }
    } else {
      return FALSE;
    }

    return $results;
  }
}

?>