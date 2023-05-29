<?php

/**
 * $Id: update.php,v 1.10 2004/11/04 18:43:46 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()) {
    header("location:index.php");
    exit();
}

require_once (PHPWS_SOURCE_DIR . "core/File.php");

$status = 1;

if ($currentVersion < ".92") {
    $content .= "- Fixed table prefix problems with approval.<br />";
    $content .= "- Default menu link now points to view.<br />";
    $content .= "- Fixed category indentions in view.<br />";
}

if ($currentVersion < ".94") {
    if ($GLOBALS['core']->query("ALTER TABLE mod_faq_settings ADD COLUMN `sorting_method` " . 
			     "int(10) NOT NULL DEFAULT 0 AFTER `paging_limit`", TRUE)) {
	$content .= "Added database attribute for the sorting method.<br />";
    } else {
	$content .= "Boost failed to create the attribute 'sorting_method' under the table 'mod_faq_settings'. <br /><br />";
	$status = 0;
    }
    

    if ($GLOBALS['core']->query("ALTER TABLE mod_faq_settings ADD COLUMN `custom_top_bullet` " . 
			     "varchar(3) DEFAULT NULL AFTER `sorting_method`", TRUE)) {
	$content .= "Added database attribute for the top level custom category bullet.<br />";
    } else {
	$content .= "Boost failed to create the attribute 'custom_top_bullet' under the table 'mod_faq_settings'. <br /><br />";
	$status = 0;
    }

    if ($GLOBALS['core']->query("ALTER TABLE mod_faq_settings ADD COLUMN `custom_sub_bullet` " .
			     "varchar(3) DEFAULT NULL AFTER `custom_top_bullet`", TRUE)) {
	$content .= "Added database attribute for the sub level custom category bullet.<br />";
    } else {
	$content .= "Boost failed to create the attribute 'custom_sub_bullet' under the table 'mod_faq_settings'. <br /><br />";
	$status = 0;
    }

    if ($GLOBALS['core']->query("ALTER TABLE mod_faq_settings ADD COLUMN `default_bullets` " .
				"int(1) NOT NULL DEFAULT 1 AFTER `custom_sub_bullet`", TRUE)) {
	$content .= "Added database attribute to indicate if only the default bullets should be used for category views. <br />";
    } else {
	$content .= "Boost failed to create the attribute 'default_bullets' under the table 'mod_faq_settings'. <br /><br />";
	$status = 0;
    }
    
    if ($GLOBALS['core']->query("ALTER TABLE mod_faq_settings ADD COLUMN `cat_show_updated` " .
				"int(1) NOT NULL DEFAULT 1 AFTER `default_bullets`", TRUE)) {
	$content .= "Added database attribut to indicate whether category should show updated text. <br /><br />";
    } else {
	$content .= "Boost failed to create the attribute 'cat_show_updated' under the table 'mod_faq_settings'. <br /><br />";
	$status = 0;
    }
  
    /* Create image directory */
    PHPWS_File::makeDir($GLOBALS['core']->home_dir . "images/faq");
    if (is_dir("{$GLOBALS['core']->home_dir}images/faq")) {
	$content .= "FAQ images directory successfully created!<br />{$GLOBALS['core']->home_dir}images/faq<br />";
    } else {
	$content .= "Boost could not create the FAQ image directory:<br />{$GLOBALS['core']->home_dir}images/faq<br />You will \
have to do this manually!<br />";
    }

    $content .= "FAQ successfully updated for version .94 changes.<br />";
}


if ($currentVersion < ".95") {
    if ($GLOBALS['core']->query("ALTER TABLE mod_faq_settings ADD COLUMN `cat_show_num_faqs` " . 
				"int(1) NOT NULL DEFAULT 1 AFTER `cat_show_updated`", TRUE)) {
	$content .= "Added database attribute to indicate whether to show the counts of FAQs in categories.<br />";
    } else {
	$content .= "Boost failed to create the attribute 'cat_show_num_faqs' under the table 'mod_faq_settings'. <br /><br />";
	$status = 0;
    }
    
    $content .= "FAQ successfully updated for version .95 changes.<br />";
}

if (in_array($currentVersion, array("0.70", "0.90", "0.92", "0.93", "0.94", "0.95", "0.96"))) {
    $currentVersion = "0.9.6";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "1.0.3") < 0){
    require_once(PHPWS_SOURCE_DIR.'mod/search/class/Search.php');
    PHPWS_Search::register("faq");
}

?>