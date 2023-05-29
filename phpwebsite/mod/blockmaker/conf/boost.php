<?php

$mod_title = "blockmaker";
$mod_pname = "Block Maker";
$mod_directory = "blockmaker";
$mod_filename = "index.php";
$allow_view = "all";
$admin_mod = 1;
$priority = 50;
$mod_class_files = array("BlockActions.php", "BlockMaker.php", "Block.php");
$mod_sessions = array("OBJ_blockmaker");
$init_object = array("OBJ_blockmaker"=>"PHPWS_BlockMaker");
$active = "on";
$version = "2.0.3";

?>
