<?php

/**
 * $Id: update.php,v 1.12 2004/11/04 18:46:10 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

$status = 1;

if ($currentVersion < "1.01") {
    $GLOBALS['core']->query("ALTER TABLE mod_menuman_menus ADD COLUMN `updated` INT(11) NOT NULL DEFAULT '0' AFTER `template`", TRUE);
    $content .= $_SESSION['translate']->it("You must logout and log back in before this update will take affect!");
}

if ($currentVersion < "1.12") {
    $_SESSION['OBJ_layout']->create_temp("menuman", "CNT_menuman_add", "bottom");
    
    $content .= "Menuman Updates (Version 1.12)<br />";
    $content .= "+ Added the site map functionality<br />";
    $content .= "+ Added content variable for adding menu links so it appears below other mods<br />";
    $content .= "+ Main administration menu is now made up of links for easier navigation<br />";
}

if ($currentVersion < "1.14") {
    $content .= "Menuman Updates (Version 1.14)<br />";
    $content .= "+ fixed a bug when adding pagemaster pages via the menu<br />";
    $content .= "+ fixed a bug causing newly saved menu settings to not take effect immediately<br />";
    $content .= "+ added new template variables THEME_DIRECTORY and MENU_ID<br />";
}

if ($currentVersion < "1.15") {
    $content .= "Menuman Updates (Version 1.15)<br />";
    $content .= "+ control over whether or not menu stays expanded<br />";
    $content .= "+ other various bug fixes<br />";
}

if (!is_dir("{$GLOBALS['core']->home_dir}images/menuman")) {
    $content .= "+ menuman directory did not exist attempting to create<br />";
    PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/menuman");
    
    if (is_dir("{$GLOBALS['core']->home_dir}images/menuman")) {
	$content .= "&#160;&#160;- creation successful<br />";
    } else {
	$content .= "&#160;&#160;- creation failed, please check file permissions<br />";
    }
}

if (in_array($currentVersion, array("0.91", "1.0", "1.01", "1.03", "1.12", "1.14", "1.15", "1.17", "1.18", "1.19"))) {
    $currentVersion = "1.1.9";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "1.2.5") < 0) {
    $GLOBALS['core']->query("ALTER TABLE mod_menuman_menus ADD COLUMN `anon_view` smallint NOT NULL DEFAULT '1' AFTER `updated`", TRUE);
    $GLOBALS['core']->query("UPDATE mod_menuman_menus SET anon_view='1'", TRUE);
    $content .= $_SESSION["translate"]->it("Added ability to restrict menus to authenticated users.");
}

?>