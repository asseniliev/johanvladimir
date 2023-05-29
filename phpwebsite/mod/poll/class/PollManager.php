<?php

require_once(PHPWS_SOURCE_DIR.'core/Manager.php');
require_once(PHPWS_SOURCE_DIR.'core/Form.php');

class PollManager extends PHPWS_Manager {

    var $poll;
    var $userbox_poll;

  
    function PollManager() {
        $this->setModule("poll");
        $this->setRequest("PHPWS_MAN_OP");
        $this->setTable("mod_poll");
        $this->init();
    }

	/**
	 * Provides menu options appropriate for user
	 *
	 * @modified Wendall Cada <wendall911@users.sourceforge.net>
	 */
    function menu() {
      $links = array();

      if($_SESSION["OBJ_user"]->allow_access("poll")){
	$links[] = "<a href=\"index.php?module=poll&amp;poll_op=newpoll\">".$_SESSION["translate"]->it("New Poll")."</a>";
      }

      $links[] = "<a href=\"index.php?module=poll&amp;poll_op=list\">".$_SESSION["translate"]->it("List Polls")."</a>";
      $links[] = "<a href=\"index.php?module=poll&amp;poll_op=showall\">".$_SESSION["translate"]->it("Show All Polls")."</a>";
      
      if($_SESSION["OBJ_user"]->allow_access("poll")){
	$links[] = "<a href=\"index.php?module=poll&amp;poll_op=Menu\">".$_SESSION["translate"]->it("Add Menu Link")."</a>";
      }

      $tags = array();
      $tags['LINKS'] = implode("&#160;|&#160;", $links);

      return PHPWS_Template::processTemplate($tags, "poll","menu.tpl");														
    }

	/**
	 * Provides standard list function: list all items in form appropriate for user
	 * If function is named _list(), is called directly by managerAction() via index.php
	 *
	 * @modified Wendall Cada <wendall911@users.sourceforge.net>
	 */
    function _list() {
        $this->init();
	$title = $_SESSION['translate']->it("Current polls");
        $content = $this->menu();
        if ($_SESSION["OBJ_user"]->isDeity() || $_SESSION["OBJ_user"]->allow_access("poll")){
            $content .= $this->getList("admin", $title);
        } else {
            $content .= $this->getList("user", $title);
        }

        $GLOBALS['CNT_POLL']['content'] = $content;
    }
  
    function _getUsers() {
        $result = $GLOBALS["core"]->sqlSelect("mod_users", NULL, NULL, "username");
        $users[] = " ";
    
        if($result)
            foreach($result as $resultRow)
        $users[] = $resultRow["username"];
        return $users;
    }
  
    function _delete($ids) {
      if($_SESSION['OBJ_user']->allow_access("poll", "edit")) {
        $content = $this->menu();
        $title = $_SESSION['translate']->it("Poll");
        foreach($ids as $value) {
            $this->poll = new PHPWS_Poll($value);
            $this->poll->kill();
        }
        $content .= $_SESSION['translate']->it("Deleted Poll Successfully");

        $GLOBALS['CNT_POLL']['content'] = $content;
      } else {
	$this->_error("access_denied");
      }
    }

	/**
	 * Provides view results of selected polls
	 *
	 * @modified Wendall Cada <wendall911@users.sourceforge.net>
	 */
    function _view($ids) {
      $GLOBALS['CNT_POLL']['content'] = $this->menu();
        foreach($ids as $value) {
            $this->poll = new PHPWS_Poll($value);
            $this->poll->showResult();
        }
    }

    function _edit($ids) {
      if($_SESSION['OBJ_user']->allow_access("poll", "edit")) {
        // warn only one edit at a time
	if (sizeof($ids) > 1) {
	  $content = "<span class=\"errortext\">" . $_SESSION['translate']->it("You may only edit one poll at a time.") . "</span><br />";
	} else {
	  $content = "";
	}
        
      $GLOBALS['CNT_POLL']['content'] = $this->menu();
        $this->poll = new PHPWS_Poll($ids[0]);
        $this->poll->edit();
      } else {
	$this->_error("access_denied");
      }
    }
  
	/**
	 * Perform non-standard list manager actions
	 *
	 * @modified Wendall Cada <wendall911@users.sourceforge.net>
	 */
    function action() {
      if(isset($_REQUEST['poll_id'])) {
	if(!isset($_SESSION["SES_POLL"]->userbox_poll) || ($_REQUEST['poll_id'] != $this->userbox_poll->getId())) {
	  $_SESSION["SES_POLL"]->userbox_poll = new PHPWS_Poll($_REQUEST['poll_id']);
	}
      }

        if(isset($_REQUEST["poll_op"])) {
            switch($_REQUEST["poll_op"]) {
                case "list":
                    $this->_list();
                break;

                case "newpoll":
                if ($_SESSION["OBJ_user"]->allow_access("poll", "create")) {
                    $content = $this->menu();
                    $title = "Poll";
		    $GLOBALS['CNT_POLL']['content'] = $content;
                    $this->poll = new PHPWS_Poll;
                    $this->poll->edit();
                } else {
                    $this->_error("access_denied");
                }
                break;

                case "showall":	/* view all items in the list */
				$this->listName = "user";   // use sort from current user list
				$this->setOrder("label");	// order by title
				//$_REQUEST["PHPWS_MAN_ITEMS"] = $this->_getIds();	//get array of all item ids
				$this->_view($this->_getIds());			// and display the corresponding items
				break;

                case "result":
		$this->userbox_poll->showResult();
                break;

                case "Menu":
		  /* allow admin to add a menu item pointing to polls list */
		  if ($_SESSION["OBJ_user"]->isDeity() || $_SESSION["OBJ_user"]->allow_access("poll")) {
		    if($GLOBALS['core']->moduleExists("menuman") && $_SESSION['OBJ_user']->allow_access("menuman", "add_item")) {
		      $_SESSION['OBJ_menuman']->add_module_item("poll", "&amp;poll_op=list",  "./index.php?module=poll&amp;poll_op=list", 1);
		    }
		  }
		  break;	
            }
        }

        //user chooose to add an option to their poll
        if(isset($_REQUEST["poll_add_op"])) {
	    $GLOBALS['CNT_POLL']['content'] = $this->menu();
            $this->poll->edit();
        }

        //user submitted poll
        if(isset($_REQUEST["poll_submit_op"])) {
	    $GLOBALS['CNT_POLL']['content'] = $this->menu();
            $this->poll->send();
        }

        //user voted on current poll
        if(isset($_REQUEST["poll_vote_op"])) {
	  $this->userbox_poll->vote();
        }
    }
  
    function _error($type) {
        $title = "<span class=\"errortext\">" . $_SESSION["translate"]->it("Error!") . "</span>";
        switch($type) {
            case "access_denied":
            $content = $_SESSION["translate"]->it("Access Denied!");
            break;
        }
        $GLOBALS['CNT_POLL']['content'] = "<h4>$title</h4>$content";
    }
}
?>