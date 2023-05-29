<?php

/**
 * Skeleton Manager
 *
 * $Id: SkeletonManager.php,v 1.9 2004/12/08 21:39:20 darren Exp $
 */


require_once PHPWS_SOURCE_DIR . 'core/List.php';
require_once PHPWS_SOURCE_DIR . 'mod/skeleton/class/Skeleton.php';

class PHPWS_SkeletonManager {

    /**
     * Holds reference to list object
     *
     * @var reference
     */
    var $_list     = null;

    /**
     * Holds reference to skeleton
     *
     * @var reference
     */
    var $_skeleton = null;
    
    /**
     * Contains message string
     *
     * @var string
     */
    var $message   = null;
    

    /**
     * Constructor
     */
    function PHPWS_SkeletonManager() {
    }

    /**
     * Menu
     */
    function _menu() {
        $addSkeleton   = $_SESSION['translate']->it('Add New Skeleton');
        $listSkeletons = $_SESSION['translate']->it('List Skeletons');
    
	$links = array();
        if ($_SESSION['OBJ_user']->allow_access('skeleton', 'edit_skeletons')) {
            $links[] = '<a href="index.php?module=skeleton&amp;op=add">' . $addSkeleton . '</a>';
        }

        $links[] =  '<a href="index.php?module=skeleton">' . $listSkeletons . '</a>';

	$tags = array();

	$tags['LINKS'] = implode("&#160;&#124;&#160;", $links);

        if (isset($this->message)) {
            $tags['MESSAGE'] = $this->message;
            $this->message            = null;
        }

        return PHPWS_Template::processTemplate($tags, 'skeleton', 'menu.tpl');
    }

    /**
     * Main
     */
    function _main() {
	if(!isset($this->_list)) {
	    $this->_list =& new PHPWS_List;
	}

	$listSettings = array('limit'   => 10,
			      'section' => true, 
			      'limits'  => array(5,10,20,50),
			      'back'    => '&#60;&#60;',
			      'forward' => '&#62;&#62;',
			      'anchor'  => false);
	
	$this->_list->setModule('skeleton');
	$this->_list->setClass('PHPWS_SkeletonList');
	$this->_list->setTable('mod_skeleton_items');
	$this->_list->setDbColumns(array('id', 'label', 'updated', 'hidden', 'muscle'));
	$this->_list->setListColumns(array('label', 'updated', 'actions'));
	$this->_list->setName('list');
	$this->_list->setOp('');
	$this->_list->anchorOn();
	$this->_list->setPaging($listSettings);
	$this->_list->setOrder('created');
	$this->_list->setWhere("approved='1'");

	$this->_list->setExtraListTags(array('TITLE' => $_SESSION['translate']->it('Skeletons'), 'UPDATED_LBL' => $_SESSION["translate"]->it("Updated")));

	return $this->_list->getList();
    }

    /**
     * Add
     */
    function _add() {
        $this->_skeleton         =& new PHPWS_Skeleton;
        $_REQUEST['skeleton_op'] = 'edit';

	$this->_skeleton->action();
    }

    /**
     * Action
     */
    function action() {	
	$content = null;

	if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
	    $this->_skeleton         =& new PHPWS_Skeleton($_REQUEST['id']);

	    if(!isset($_REQUEST['op']) && !isset($_REQUEST['skeleton_op'])) {
		$_REQUEST['skeleton_op'] = 'view';
	    }
	}

	if(isset($_REQUEST['skeleton_op']) && isset($this->_skeleton)) {
	    $this->_skeleton->action();
	    return;
	}
	
        switch(@$_REQUEST['op']) {
	    case 'add':
		$this->_add();
		break;

	    default:
		$content .= $this->_menu();
		$content .= $this->_main();
        }

	if (isset($content)) {
	    $GLOBALS['CNT_skeleton']['content'] .= $content;
	}
    }
}

class PHPWS_SkeletonList extends PHPWS_Skeleton {

    function PHPWS_SkeletonList($vars) {
	/* Function provided by PHPWS_Item */
	$this->setVars($vars);
    }

    function getListLabel() {
	/* Function provided by PHPWS_Item */
	$label = $this->getLabel();

	return "<a href=\"./index.php?module=skeleton&amp;id={$this->_id}\">{$label}</a>";
    }

    function getListUpdated() {
	/* Function provided by PHPWS_Item */
	return $this->getUpdated();
    }

    function getListActions() {
	$actions = array();

	$view   = $_SESSION['translate']->it('View');
	$hide   = $_SESSION['translate']->it('Hide');
	$show   = $_SESSION['translate']->it('Show');
	$edit   = $_SESSION['translate']->it('Edit');
	$delete = $_SESSION['translate']->it('Delete');


	$actions[] =  "<a href=\"./index.php?module=skeleton&amp;id={$this->_id}\">{$view}</a>";

	if($_SESSION['OBJ_user']->allow_access("skeleton", "hideshow_skeletons")) {
	    if($this->isHidden())
		$actions[] = "<a href=\"./index.php?module=skeleton&amp;skeleton_op=show&amp;id={$this->_id}\">{$show}</a>";
	    else
		$actions[] = "<a href=\"./index.php?module=skeleton&amp;skeleton_op=hide&amp;id={$this->_id}\">{$hide}</a>";
	}

	if($_SESSION['OBJ_user']->allow_access("skeleton", "edit_skeletons")) {
		$actions[] = "<a href=\"./index.php?module=skeleton&amp;skeleton_op=edit&amp;id={$this->_id}\">{$edit}</a>";
	}

	if($_SESSION['OBJ_user']->allow_access("skeleton", "_skeletons")) {
		$actions[] = "<a href=\"./index.php?module=skeleton&amp;skeleton_op=delete&amp;id={$this->_id}\">{$delete}</a>";
	}

	return implode("&#160;&#124;&#160;", $actions);
    }
}

?>
