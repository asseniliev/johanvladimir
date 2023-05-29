<?php

/**
 * Skeleton item class
 *
 * $Id: Skeleton.php,v 1.10 2004/11/05 16:28:10 steven Exp $
 */

require_once PHPWS_SOURCE_DIR . 'core/Item.php';

class PHPWS_Skeleton extends PHPWS_Item {

    /**
     * Stores the main content for this item
     *
     * @var string
     */
    var $_muscle = null;

    /**
     * Constructor
     */
    function PHPWS_Skeleton($id = null) {
        $this->setTable('mod_skeleton_items');

        if (isset($id)) {
            $this->setId($id);
            $this->init();
        }
    }

    /**
     * Set Muscle
     */
    function setMuscle($muscle) {
	require_once PHPWS_SOURCE_DIR . 'core/Text.php';

        if (is_string($muscle)) {
            if (strlen($muscle) > 0) {
		$this->_muscle = PHPWS_Text::parseInput($muscle);
		return true;
            } else {
		$message = $_SESSION['translate']->it('You must provide some meat!');
		return new PHPWS_Error('skeleton', 'PHPWS_Skeleton::save', $message);
            }
        } else {
            $message = $_SESSION['translate']->it('Muscle must be a string!');
            return new PHPWS_Error('skeleton', 'PHPWS_Skeleton::save', $message);
        }
    }

    /**
     * Get Muscle
     */
    function getMuscle() {
	require_once PHPWS_SOURCE_DIR . 'core/Text.php';

        if (isset($this->_muscle) && strlen($this->_muscle) > 0) {
            return PHPWS_Text::parseOutput($this->_muscle);
        } else {
            return NULL;
        }
    }

    /**
     * View
     */
    function _view() {
        $tags = array();
        $tags['LABEL'] = $this->getLabel();
        $tags['MUSCLE'] = $this->getMuscle();

        return PHPWS_Template::processTemplate($tags, 'skeleton', 'view.tpl');
    }

    /**
     * Edit
     */
    function _edit() { 
	require_once PHPWS_SOURCE_DIR . 'core/EZform.php';

	$form = new EZForm('Skeleton_edit');
        $form->add('module', 'hidden', 'skeleton');
        $form->add('skeleton_op', 'hidden', 'save');

        $form->add('label', 'text', $this->getLabel());
        $form->add('muscle', 'textarea', $this->_muscle);
        $form->add('save', 'submit', $_SESSION['translate']->it('Save Skeleton'));
    
        $tags = $form->getTemplate();
    
        $id = $this->getId();
        if (isset($id)) {
            $tags['TITLE'] = $_SESSION['translate']->it('Edit Skeleton');
        } else {
            $tags['TITLE'] = $_SESSION['translate']->it('Add Skeleton');
        }

        $tags['BACK'] = '<a href="index.php?module=skeleton">' .
            $_SESSION['translate']->it('Back to Skeleton List') . '</a>';

        $tags['LABEL_LABEL'] = $_SESSION['translate']->it('Label');
        $tags['MUSCLE_LABEL'] = $_SESSION['translate']->it('The Meat');

        return PHPWS_Template::processTemplate($tags, 'skeleton', 'edit.tpl');
    }

    /**
     * Save
     */
    function _save() {
	require_once PHPWS_SOURCE_DIR . 'core/Error.php';

        $error = $this->setLabel($_REQUEST['label']);
        if (PHPWS_Error::isError($error)) {
            $error->message('CNT_skeleton');
            return $this->_edit();
        }

        $error = $this->setMuscle($_REQUEST['muscle']);
        if (PHPWS_Error::isError($error)) {
            $error->message('CNT_skeleton');
            return $this->_edit();
        }

        $error = $this->commit();

        $_SESSION['PHPWS_SkeletonManager']->message = $_SESSION['translate']->it('Skeleton Saved!');
	    
	$_REQUEST['id']          = null;
	$_REQUEST['skeleton_op'] = null;

        $_SESSION['PHPWS_SkeletonManager']->action();
    }

    /**
     * Delete
     */
    function _delete() {
        if (isset($_REQUEST['yes'])) {
            $this->kill();
            $_SESSION['PHPWS_SkeletonManager']->message = $_SESSION['translate']->it('Skeleton deleted!');
	    
	    $_REQUEST['id']          = null;
	    $_REQUEST['skeleton_op'] = null;

	    $_SESSION['PHPWS_SkeletonManager']->action();
        } else if (isset($_REQUEST['no'])) {
            $_SESSION['PHPWS_SkeletonManager']->message = $_SESSION['translate']->it('Skeleton was not deleted!');
 
	    $_REQUEST['id']          = null;
	    $_REQUEST['skeleton_op'] = null;

            $_SESSION['PHPWS_SkeletonManager']->action();
        } else {
            $tags = array();

            $tags['MESSAGE'] = $_SESSION['translate']->it('Are you sure you want to delete this skeleton?');

            $tags['YES'] = '<a href="index.php?module=skeleton&amp;skeleton_op=delete&amp;yes=1">' .
	        $_SESSION['translate']->it('Yes') . '</a>';

            $tags['NO'] = '<a href="index.php?module=skeleton&amp;skeleton_op=delete&amp;no=1">' .
	        $_SESSION['translate']->it('No') .'</a>';

            $tags['SKELETON'] = $this->_view();

            return PHPWS_Template::processTemplate($tags, 'skeleton', 'confirm.tpl');
        }
    }

    /**
     * Hide Show
     */
    function _hideshow() {
	if ($this->isHidden()) {
	    $this->setHidden(false);
	} else {
	    $this->setHidden();
	}

	$error = $this->commit();

	$_SESSION['PHPWS_SkeletonManager']->action();
    }


    /**
     * Action
     */
    function action() {
        $content = NULL;

        switch($_REQUEST['skeleton_op']) {
	    case 'hide':
	    case 'show':
		$this->_hideshow();
		break;

	    case 'edit':
		$content .= $this->_edit();
		break;
		
	    case 'save':
		$content .= $this->_save();
		break;
      
	    case 'delete':
		$content .= $this->_delete();
		break;

	    default:
		$content .= $this->_view();
        }

        if (isset($content)) {
            $GLOBALS['CNT_skeleton']['content'] .= $content;
        }
    }
}

?>
