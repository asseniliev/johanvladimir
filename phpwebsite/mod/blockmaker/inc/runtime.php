<?php

$block_results = $GLOBALS['core']->sqlSelect("mod_blockmaker_data", "block_active", 1);

if($block_results) {
  require_once(PHPWS_SOURCE_DIR . "mod/blockmaker/class/Block.php");

  $block = new PHPWS_Block;
  foreach($block_results as $block_result) {
    $block->block_id    = $block_result["block_id"];
    $block->block_title = $block_result["block_title"];
    $block->block_content = $block_result["block_content"];
    $block->block_footer = $block_result["block_footer"];
    $block->block_active = $block_result["block_active"];
    $block->block_updated = $block_result["block_updated"];
    $block->content_var = $block_result["content_var"];
    $block->allow_view = unserialize($block_result["allow_view"]);

    if((!isset($_REQUEST['module']) && !in_array('home', $block->allow_view)) ||
       (isset($_REQUEST['module']) && !in_array($_REQUEST['module'], $block->allow_view)))
      continue;

    $block->display_block();
  }
}

?>