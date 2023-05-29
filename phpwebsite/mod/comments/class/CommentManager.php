<?php

require_once(PHPWS_SOURCE_DIR . 'core/List.php');
require_once(PHPWS_SOURCE_DIR . 'core/Form.php');
require_once(PHPWS_SOURCE_DIR . 'core/Array.php');
require_once(PHPWS_SOURCE_DIR . 'core/WizardBag.php');

require_once(PHPWS_SOURCE_DIR . 'mod/comments/class/Comment.php');
require_once(PHPWS_SOURCE_DIR . 'mod/help/class/CLS_help.php');

/**
 * Manages all of the current comments for a module with a given itemId for that module 
 *
 * @version $Id: CommentManager.php,v 1.28 2005/03/15 16:11:25 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Comment Manager
 */
class PHPWS_CommentManager {
 
  /**
   * module for comments to be managed
   * @var string
   */
  var $module;

  /**
   * item id for comments to be managed
   * @var integer
   */
  var $itemId;

  /**
   * place holder for the current comment
   * @var array
   */
  var $currentComment;

  /**
   * how the comments are to be viewed
   * @var integer
   */
  var $listView;
  var $userListView;

  /**
   * order in which comments are to be displayed
   * @var string
   */
  var $listOrder;
  var $userListOrder;

  /**
   * flag whether or not to allow anonymous posts to a comment
   * @var boolean
   */
  var $allowAnonymous;

  /**
   * link back to where comments was called from
   * @var string
   */
  var $linkBack;

  /**
   * the max size a comment can be
   * @var integer
   */
  var $maxCommentSize;

  /**
   * max submissions from a single IP for one instance of comments
   * @var integer
   */
  var $maxSubmissions;

  /**
   * holds rows of info about threads in html format
   * @var array
   */
  var $threads;

  /**
   * PHPWS_CommentManager
   *
   * Constructor for the PHPWS_CommentManager class
   */
  function PHPWS_CommentManager() {
    $configResult = $GLOBALS['core']->sqlSelect("mod_comments_cfg");

    $this->listView = $configResult[0]['listView'];
    $listView = $_SESSION['OBJ_user']->getUserVar("userListView", NULL, "comments");
    if($listView) {
      $this->userListView = $listView;
    } else {
      $this->userListView = NULL;
    }
    
    $this->listOrder = $configResult[0]['listOrder'];
    $listOrder = $_SESSION['OBJ_user']->getUserVar("userListOrder", NULL, "comments");
    if($listOrder) {
      $this->userListOrder = $listOrder;
    } else {
      $this->userListOrder = NULL;
    }      

    $this->maxCommentSize = $configResult[0]['maxSize'];
    $this->maxSubmissions = $configResult[0]['maxIp'];
    $this->allowAnonymous = FALSE;      

  } // END FUNC PHPWS_CommentManager

  /* various get and set functions */
  function getLinkBack() {return $this->linkBack;}
  function getCurrentModule() {return $this->module;}
  function getCurrentItemId() {return $this->itemId;}
  function getAllowAnonymous() {return $this->allowAnonymous;}
  function getMaxSubmissions() {return $this->maxSubmissions;}
  function getMaxCommentSize() {return $this->maxCommentSize;}

  function setUserListView($userListView) {$this->userListView = $userListView;}
  function setUserListOrder($userListOrder) {$this->userListOrder = $userListOrder;}

  /**
   * listCurrentComments
   *
   * Displays the currentComments in a list to the user
   *
   * @param string  $module the module to display the comments for
   * @param integer $itemId the module's item to show the comments for
   * @param boolean $allowAnnon flag whether or not to allow anonymous posts
   */
  function listCurrentComments($module="", $itemId=0, $allowAnnon=FALSE) {
    if(!isset($_REQUEST['CM_viewThread']) && ((($this->module == $module) && ($this->itemId == $itemId)) || (($this->module && ($module == "")) && ($this->itemId && ($itemId == 0))))) {
    } else {
      $this->module = $module;
      $this->itemId = $itemId;
      $this->allowAnonymous = $allowAnnon;
      $this->linkBack = PHPWS_WizardBag::whereami();
    }

    if(isset($this->userListView) && isset($this->userListOrder)) {
      $listView = $this->userListView;
      $listOrder = $this->userListOrder;
    } else {
      $listView = $this->listView;
      $listOrder = $this->listOrder;
    }

    $sql = "SELECT cid FROM {$GLOBALS['core']->tbl_prefix}mod_comments_data WHERE pid IS NULL AND module='$this->module' AND itemId='$this->itemId' ORDER BY postDate $listOrder";
    $parentResult = $GLOBALS['core']->query($sql);

    if($listView == 0) {
      $content = "<ul type=\"disc\">\n";
    } else {
      $content = "";
    }

    while($row = $parentResult->fetchrow(DB_FETCHMODE_ASSOC)) {
      $cid = $row['cid'];
      $this->currentComment = new PHPWS_Comment($cid);
      if($listView == 0) {
	if(strlen($this->currentComment->subject) > 0) {
	  $subject = $this->currentComment->subject;
	} else {
	  $subject = $_SESSION['translate']->it("No Subject");
	}
	
	$content .= "<li><a href=\"./index.php?module=comments&amp;CM_op=viewComment&amp;CM_cid=$cid\">" . $subject . "</a>";
	$content .= "<div align=\"right\"><b>" . $_SESSION['translate']->it("Date") . "</b>&#160;" . $this->currentComment->postDate;
	$content .= "&#160;&#160;<b>" . $_SESSION['translate']->it("Author") . "</b>&#160;" . $this->currentComment->author . "</div><hr align=\"center\" width=\"100%\" /></li>";
      } else {
	$content .= $this->currentComment->viewComment();
      }
      $this->listCurrentChildren($cid, $content);
    }

    if($listView == 0) {
      $content .= "</ul>";
    }
  
    $hiddens = array("module"=>"comments",
		     "CM_op"=>"refreshView"
		     );

    $option0 = $_SESSION['translate']->it("Threaded");
    $option1 = $_SESSION['translate']->it("Nested");
    $option2 = $_SESSION['translate']->it("Flat");
    
    $listViewOptions = array(0=>"$option0",
				   1=>"$option1",
				   2=>"$option2"
				   );

    $option0 = $_SESSION['translate']->it("Oldest First");
    $option1 = $_SESSION['translate']->it("Newest First");
    
    $listOrderOptions = array("ASC"=>"$option0",
				    "DESC"=>"$option1"
				    );

    $elements[0] = PHPWS_Form::formHidden($hiddens);
    $elements[0] .= PHPWS_Form::formSelect("CM_listView", $listViewOptions, $listView, FALSE, TRUE);
    $elements[0] .= PHPWS_Form::formSelect("CM_listOrder", $listOrderOptions, $listOrder, FALSE, TRUE);
    $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Refresh"));
    $refreshForm = PHPWS_Form::makeForm("CM_refresh", "index.php", $elements, "post", FALSE, FALSE);
    
    $title = $_SESSION['translate']->it("Comments");

    if(isset($_SESSION['OBJ_user']->username) || $this->getAllowAnonymous()) {
      $title .= "&#160;-&#160;" . "<a href=\"./index.php?module=comments&amp;CM_op=makeComment&amp;mod=$this->module&amp;item=$this->itemId\">" . $_SESSION['translate']->it("Make a comment") . "</a>";
    }

    $content = $_SESSION['translate']->it("The comments are owned by the poster. We are not responsible for its content.") . "<div align=\"right\">" . $refreshForm . "</div>" . $content;

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['title'] = $title;
    $GLOBALS['CNT_comments']['content'] = $content;
  } // END FUNC listCurrentComments

  /**
   * listCurrentChildren
   *
   * List the children for a comment for the listCurrentComments function
   * @param integer $pid the id of the parent to list the children for
   * @param string  $content holds the output for the comments
   */
  function listCurrentChildren($pid, &$content) {
    if(isset($this->userListView) && isset($this->userListOrder)) {
      $listView = $this->userListView;
      $listOrder = $this->userListOrder;
    } else {
      $listView = $this->listView;
      $listOrder = $this->listOrder;
    }

    $sql = "SELECT cid FROM {$GLOBALS['core']->tbl_prefix}mod_comments_data WHERE pid='$pid' AND module='$this->module' AND itemId='$this->itemId' ORDER BY postDate $listOrder";
    $childResult = $GLOBALS['core']->query($sql);

    if($childResult->numrows() > 0) {
      if($listView == 0) {
	$content .= "<ul type=\"circle\">\n";
      } else if($listView == 1) {
	$content .= "<ul>";
      }
      
      while($row = $childResult->fetchrow(DB_FETCHMODE_ASSOC)) {
	$cid = $row['cid'];
	$this->currentComment = new PHPWS_Comment($cid);
	if($listView == 0) {
	  if(strlen($this->currentComment->subject) > 0) {
	    $subject = $this->currentComment->subject;
	  } else {
	    $subject = $_SESSION['translate']->it("No Subject");
	  }

	  $content .= "<li><a href=\"./index.php?module=comments&amp;CM_op=viewComment&amp;CM_cid=$cid\">" . $subject . "</a>";
	  $content .= "<div align=\"right\"><b>" . $_SESSION['translate']->it("Date") . "</b>&#160;" . $this->currentComment->postDate;
	  $content .= "&#160;&#160;<b>" . $_SESSION['translate']->it("Author") . "</b>&#160;" . $this->currentComment->author . "</div><hr align=\"center\" width=\"100%\" /></li>";
	} else {
	  $content .= $this->currentComment->viewComment();
	}
	$this->listCurrentChildren($cid, $content, $listView);
      }    
      
      if($listView == 0 || $listView == 1) {
	$content .= "</ul>\n";
      }
    }
  } // END FUNC listCurrentChildren

  /**
   * goBack
   *
   * headers back to where the comment module was called
   */
  function goBack() {
    if(preg_match("/['IIS']/", $_SERVER['SERVER_SOFTWARE'])) {
      header("Refresh: 0;url=$this->linkBack");
    } else {
      header("Location: $this->linkBack");
    }
    exit();
  } // END FUNC goBack

  /**
   * adminMenu
   *
   * admin options for the Comment Manager
   */
  function adminMenu() {
    $links = array();
    $links[] = "<a href=\"./index.php?module=comments&amp;CM_op=adminMenu&amp;CM_listThreads=1\">".$_SESSION['translate']->it("List Threads")."</a>";
    $links[] = "<a href=\"./index.php?module=comments&amp;CM_op=adminMenu&amp;CM_adminSettings=1\">".$_SESSION['translate']->it("Settings")."</a>";
 
    $tags = array();
    $tags["LINKS"] = implode("&#160;|&#160;", $links);

    $GLOBALS['CNT_comments']['content'] = PHPWS_Template::processTemplate($tags, "comments", "adminMenu.tpl");
  } // END FUNC adminMenu

  /**
   * listThreads
   *
   * lists all the threads currently found in the database
   */
  function listThreads() {
    $listTags = array();
    $listTags['TITLE'] = $_SESSION['translate']->it("Current Threads") . "&#160;&#160;" . CLS_help::show_link("comments", "main");
    $listTags['MODULE_LABEL'] = $_SESSION["translate"]->it("Module");
    $listTags['SUBJECT_LABEL'] = $_SESSION["translate"]->it("Subject");
    $listTags['POSTDATE_LABEL'] = $_SESSION["translate"]->it("Last Post");
    $listTags['ACTIONS_LABEL'] = $_SESSION["translate"]->it("Actions");

    if(!isset($this->threads)) {
      $this->threads = new PHPWS_List;
    }

    $this->threads->setModule("comments");
    $this->threads->setClass("PHPWS_Comment");
    $this->threads->setTable("mod_comments_data");
    $this->threads->setIdColumn("cid");
    $this->threads->setDbColumns(array("module", "itemId", "subject", "postDate"));
    $this->threads->setListColumns(array("module", "subject", "postDate", "actions"));
    $this->threads->setName("list");
    $this->threads->setOp("CM_op=adminMenu&amp;CM_listThreads=1");
    $this->threads->setPaging(array("limit"=>10, "section"=>TRUE, "limits"=>array(5,10,20,50), "back"=>"&#60;&#60;", "forward"=>"&#62;&#62;", "anchor"=>FALSE));
    $this->threads->setExtraListTags($listTags);
    $this->threads->setOrder("postDate DESC");
    $this->threads->setWhere("pid IS NULL");

    $GLOBALS["CNT_comments"]["content"] .= $this->threads->getList();
  } // END FUNC listThreads

  /**
   * adminSettings
   *
   * provides admin with the ability to edit default settings for comments
   */
  function adminSettings() {
    if(isset($_POST['CM_adminSave'])) {
      $this->listView = $_POST['CM_listView'];
      $this->listOrder = $_POST['CM_listOrder'];
      $this->maxCommentSize = $_POST['CM_maxSize'];
      $this->maxSubmissions = $_POST['CM_maxIp'];
      $saveArray = array("listView"=>"$this->listView",
			 "listOrder"=>"$this->listOrder",
			 "maxSize"=>"$this->maxCommentSize",
			 "maxIp"=>"$this->maxSubmissions"
			 );

      $GLOBALS['core']->sqlUpdate($saveArray, "mod_comments_cfg");
    }

    $hiddens = array("module"=>"comments",
		     "CM_op"=>"adminSettings"
	);

    $option0 = $_SESSION['translate']->it("Threaded");
    $option1 = $_SESSION['translate']->it("Nested");
    $option2 = $_SESSION['translate']->it("Flat");
    
    $listViewOptions = array(0=>"$option0",
			     1=>"$option1",
			     2=>"$option2"
	);

    $option0 = $_SESSION['translate']->it("Oldest First");
    $option1 = $_SESSION['translate']->it("Newest First");
    
    $listOrderOptions = array("ASC"=>"$option0",
			      "DESC"=>"$option1"
	);
    
    $elements[0] = PHPWS_Form::formHidden($hiddens);
    $CM_template['0INFO'] = $_SESSION['translate']->it("The following settings are the default settings for users and anonymous vistors of the site.");
    $CM_template['LIST_VIEW_TEXT'] = $_SESSION['translate']->it("Display method for the comments.");
    $CM_template['LIST_VIEW'] = PHPWS_Form::formSelect("CM_listView", $listViewOptions, $this->listView, FALSE, TRUE);
    $CM_template['LIST_ORDER_TEXT'] = $_SESSION['translate']->it("Order the comments are to be displayed.");
    $CM_template['LIST_ORDER'] = PHPWS_Form::formSelect("CM_listOrder", $listOrderOptions, $this->listOrder, FALSE, TRUE);

    $CM_template['1INFO'] = $_SESSION['translate']->it("These settings set restrictions for the Comment Manager to enforce.");
    $CM_template['MAX_SIZE_TEXT'] = $_SESSION['translate']->it("Max size for a comment in characters.") . $_SESSION['translate']->it("(0 is maximum)");
    $CM_template['MAX_SIZE'] = PHPWS_Form::formTextField("CM_maxSize", $this->maxCommentSize, 8, 6);
    $CM_template['MAX_SUB_TEXT'] = $_SESSION['translate']->it("Max submissions from one ip.") . $_SESSION['translate']->it("(0 is unlimited)");
    $CM_template['MAX_SUBMISSIONS'] = PHPWS_Form::formTextField("CM_maxIp", $this->maxSubmissions, 6, 6);
    $CM_template['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save"), "CM_adminSave");
    $elements[0] .= PHPWS_Template::processTemplate($CM_template, "comments", "adminSettings.tpl");
    $adminSettingsForm = PHPWS_Form::makeForm("CM_adminSettings", "index.php", $elements, "post", FALSE, FALSE);

    $title = $_SESSION['translate']->it("Comment Administrative Settings") . "&#160;&#160;" . CLS_help::show_link("comments", "settings");
    $content = $adminSettingsForm;

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['title'] = $title;
    $GLOBALS['CNT_comments']['content'] = $content;
  } // END FUNC adminSettings

  /**
   * userSettings
   *
   * provides user with ability to edit their comment settings
   * this information is written to a cookie
   */
  function userSettings() {
    if(isset($_POST['CM_userSave'])) {
      $_SESSION['OBJ_user']->setUserVar("userListView", $_REQUEST['CM_listView'], NULL, "comments");
      $_SESSION['OBJ_user']->setUserVar("userListOrder", $_REQUEST['CM_listOrder'], NULL, "comments");
      $this->userListView = $_REQUEST['CM_listView'];
      $this->userListOrder = $_REQUEST['CM_listOrder'];
    } else {
      $this->userListView = $_SESSION['OBJ_user']->getUserVar("userListView", NULL, "comments");
      $this->userListOrder = $_SESSION['OBJ_user']->getUserVar("userListOrder", NULL, "comments");
    }

    $hiddens = array("module"=>"comments",
		     "CM_op"=>"userSettings"
		     );

    $option0 = $_SESSION['translate']->it("Threaded");
    $option1 = $_SESSION['translate']->it("Nested");
    $option2 = $_SESSION['translate']->it("Flat");
    
    $listViewOptions = array(0=>"$option0",
			     1=>"$option1",
			     2=>"$option2"
	);
    
    $option0 = $_SESSION['translate']->it("Oldest First");
    $option1 = $_SESSION['translate']->it("Newest First");
    
    $listOrderOptions = array("ASC"=>"$option0",
			      "DESC"=>"$option1"
	);    
    
    $elements[0] = PHPWS_Form::formHidden($hiddens);
    $CM_template['0INFO'] = $_SESSION['translate']->it("The following settings are the default settings for when you view comments.");
    $CM_template['LIST_VIEW_TEXT'] = $_SESSION['translate']->it("Display method for the comments.");
    $CM_template['LIST_VIEW'] = PHPWS_Form::formSelect("CM_listView", $listViewOptions, $this->userListView, FALSE, TRUE);
    $CM_template['LIST_ORDER_TEXT'] = $_SESSION['translate']->it("Order the comments are to be displayed.");
    $CM_template['LIST_ORDER']= PHPWS_Form::formSelect("CM_listOrder", $listOrderOptions, $this->userListOrder, FALSE, TRUE);
    $CM_template['SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("Save"), "CM_userSave");
    $elements[0] .= PHPWS_Template::processTemplate($CM_template, "comments", "userSettings.tpl");
    $userSettingsForm = PHPWS_Form::makeForm("CM_userSettings", "index.php", $elements, "post", FALSE, FALSE);

    $title = $_SESSION['translate']->it("Comment User Settings");
    $content = $userSettingsForm;

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['title'] = $title;
    $GLOBALS['CNT_comments']['content'] = $content;
  } // END FUNC userSettings

  /**
   * numComments
   *
   * returns the number of comments for a compound id consisting of module and itemId
   *
   * @param string $module name of the module to count comments for
   * @param integer $itemId id of the item to count comments for
   * @return integer number of comments found for the given compound id
   */
  function numComments($module, $itemId) {
    $sql = "SELECT cid FROM {$GLOBALS['core']->tbl_prefix}mod_comments_data WHERE module='$module' and itemId='$itemId'";
    $commentResult = $GLOBALS['core']->query($sql);
    return $commentResult->numrows();
  } // END FUNC numComments

  /**
   * deleteThread
   *
   * deletes an entire comment manager thread from the database
   */
  function deleteThread() {
    $module = $_REQUEST['CM_module'];
    $itemId = $_REQUEST['CM_itemId'];
    
    if(isset($_POST['CM_yes'])) {
      $sql = "DELETE FROM {$GLOBALS['core']->tbl_prefix}mod_comments_data WHERE module='$module' AND itemId='$itemId'";
      $GLOBALS['core']->query($sql);

      unset($this->threads);
      unset($this->currentComment);
      
      $title = $_SESSION['translate']->it("Thread Deleted");
      $content = $_SESSION['translate']->it("The thread was successfully deleted from the database.");
    } else if(isset($_POST['CM_no'])) {
      $title = $_SESSION['translate']->it("No Thread Deleted");
      $content = $_SESSION['translate']->it("No thread was deleted from the database.");
    } else { 
      $title = $_SESSION['translate']->it("Delete Thread Confirmation");
      $content = $_SESSION['translate']->it("Are you sure you want to delete this thread and all its comments?");

      $hiddens = array("module"=>"comments",
		       "CM_op"=>"threadAction",
		       "CM_deleteThread"=>1,
		       "CM_module"=>$module,
		       "CM_itemId"=>$itemId
		       );

      $elements[0] = "<br />";
      $elements[0] .= PHPWS_Form::formHidden($hiddens);
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Yes"), "CM_yes");
      $elements[0] .= "&#160;&#160;";
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("No"), "CM_no");

      $content .= PHPWS_Form::makeForm("CM_deleteThread", "index.php", $elements, "post", NULL, NULL);
    }

    //$GLOBALS['OBJ_layout']->popbox($title, $content, NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['content'] .= "<b>$title</b><br />$content";
  } // END FUNC deleteThread

  function accessDenied() {
    $GLOBALS['CNT_comments']['title'] = $_SESSION['translate']->it("Comments");
    $GLOBALS['CNT_comments']['content'] = $_SESSION['translate']->it("Access Denied!");
  }

} // END CLASS PHPWS_CommentManager

?>