<?php

/**
 * Whosonline Module for phpWebSite 0.9.3
 *
 * @author rck <http://www.kiesler.at/>
 */

if(!isset($GLOBALS['core']))
{
    header("Location: ../..");
    exit();
}

if (!isset($_SESSION["OBJ_health"])) {
	$_SESSION["OBJ_health"] = new PHPWS_health;
}

if ($GLOBALS["module"] == "health") {
	$GLOBALS["CNT_health"] = array("title"=>"Health", "content"=>"<p>don't know what to do...</p>");

	if (isset($_REQUEST["health_op"]))
		$operation = $_REQUEST["health_op"];

	if($operation=="check")
		$GLOBALS["CNT_health"]["content"]=$_SESSION["OBJ_health"]->showReport();
	else
	if($operation=="overview")
		$GLOBALS["CNT_health"]["content"]=$_SESSION["OBJ_health"]->showOverview();
	else
	if($operation=="tools")
		$GLOBALS["CNT_health"]["content"]=$_SESSION["OBJ_health"]->showTools($_REQUEST['tool'],
			$_REQUEST['tool_op'], $_REQUEST['item1'], $_REQUEST['item2']);

	if(empty($_REQUEST['health_op']))
		$GLOBALS['CNT_health']['content']=$_SESSION['OBJ_health']->showMainMenu();
	
}


?>
