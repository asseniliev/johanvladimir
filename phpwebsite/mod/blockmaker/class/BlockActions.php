<?php

/**
 * Action class for Block Maker module
 *
 * @version $Id: BlockActions.php,v 1.8 2003/10/21 16:38:14 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Block Maker
 */
class PHPWS_BlockActions {

  function blockMenu() {
    if($_SESSION['OBJ_user']->allow_access("blockmaker")){
      $_SESSION['OBJ_blockmaker']->block_menu();
      $_SESSION['OBJ_blockmaker']->list_blocks();
    }
  }

  function menuSelect() {
    if($_SESSION['OBJ_user']->allow_access("blockmaker")){
      if(isset($_REQUEST['create_block'])){
	$_SESSION['OBJ_blockmaker']->new_block = new PHPWS_Block();
	$_SESSION['OBJ_blockmaker']->new_block->block("create");
      } else{
	$_SESSION['OBJ_blockmaker']->block_menu();
	$_SESSION['OBJ_blockmaker']->list_blocks();
      }
    }
  }

  function insertBlock() {
    if($_SESSION['OBJ_user']->allow_access("blockmaker", "create_block")){
      $_SESSION['OBJ_blockmaker']->block_menu();
      $_SESSION['OBJ_blockmaker']->new_block->save("insert");
      $_SESSION['OBJ_blockmaker']->reload();
      $_SESSION['OBJ_blockmaker']->list_blocks();
    }
  }

  function updateBlock() {
    if($_SESSION['OBJ_user']->allow_access("blockmaker", "edit_block")){
      $_SESSION['OBJ_blockmaker']->block_menu();
      $_SESSION['OBJ_blockmaker']->blocks[$_REQUEST['BLK_block_id']]->save("update");
      $_SESSION['OBJ_blockmaker']->reload();
      $_SESSION['OBJ_blockmaker']->list_blocks();
    }
  }

  function blockAction() {
    $_SESSION['OBJ_blockmaker']->block_menu();
    if(isset($_REQUEST['BLK_edit']) && $_SESSION['OBJ_user']->allow_access("blockmaker", "edit_block")){
      $_SESSION['OBJ_blockmaker']->blocks[$_REQUEST['BLK_block_id']]->block("edit");
    } else if(isset($_REQUEST['BLK_delete']) && $_SESSION['OBJ_user']->allow_access("blockmaker", "delete_block")){
      $_SESSION['OBJ_blockmaker']->blocks[$_REQUEST['BLK_block_id']]->delete();
    } else if(isset($_REQUEST['BLK_activity']) && $_SESSION['OBJ_user']->allow_access("blockmaker", "block_activity")){
      $_SESSION['OBJ_blockmaker']->blocks[$_REQUEST['BLK_block_id']]->set_activity();
      $_SESSION['OBJ_blockmaker']->list_blocks();
    }
  }

  /*
  function showBlocks() {
    foreach($_SESSION['OBJ_blockmaker']->blocks as $value){
      if($value->block_active == 1){
	if(isset($_REQUEST['module']) && (!in_array($_REQUEST['module'], $value->allow_view)))
	  continue;
	$value->display_block();
      }
    }
  }
  */
}

?>