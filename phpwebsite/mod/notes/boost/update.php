<?php

/**
 * $Id: update.php,v 1.5 2004/11/04 18:46:57 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if ($currentVersion < "1.3") {
    $content .= "+ Fixed commands dependant upon translate statements. <br />";
}

if ($currentVersion < "1.4") {
    if($GLOBALS['core']->query("ALTER TABLE mod_notes ADD COLUMN `subject` text NOT NULL DEFAULT '' AFTER `userRead`", TRUE)) {
	
	//make any existing notes have a subject that is part of the message body
	$result = $GLOBALS["core"]->sqlSelect("mod_notes");
	
	if ($result) {
	    foreach($result as $entry) {
		$data["subject"] = substr($entry["message"], 0, 20);
		echo $data["subject"].$entry["id"]."<br />";
		$GLOBALS["core"]->sqlUpdate($data, "mod_notes", "id", $entry["id"]);      
	    }
	}
	
	$content .= "Notes successfully updated.<br />";
    } else {
	$status = 0;
	$content .= "Updates for the note module failed";
    }
}

if (in_array($currentVersion, array("1", "1.1", "1.2", "1.3", "1.4", "1.41"))) {
    $currentVersion = "1.4.1";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "1.6.1") < 0) {
    if ($GLOBALS['core']->query("ALTER TABLE mod_notes ADD COLUMN `toUserHide` smallint NOT NULL DEFAULT 0 AFTER `toUser`", TRUE)) {

	if ($GLOBALS['core']->query("ALTER TABLE mod_notes ADD COLUMN `fromUserHide` smallint NOT NULL DEFAULT 0 AFTER `fromUser`", TRUE)) {
	    $content .= "Added ability to hide sent notes.<br />";
	    
	} else {
	    $status = 0;
	    $content .= "Updates for the note module failed.<br />";
	}
	
    } else {
	$status = 0;
	$content .= "Updates for the note module failed.<br />";
    }
}

?>