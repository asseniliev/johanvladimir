<?php

/**
 * $Id: update.php,v 1.22 2005/01/07 15:47:41 darren Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if ($currentVersion < "0.41") {
    $content .= "Photoalbum Updates (Version 0.41)<br />";
    $content .= "+ ability to hide and show albums and photos<br />";
    $content .= "+ next and previous links in the photo view<br />";
    $content .= "+ printable view of a photo<br />";
    $content .= "+ bug fixes<br />";
}

if ($currentVersion < "0.50") {
    $content .= "Photoalbum Updates (Version 0.50)<br />";
    $content .= "+ Converted all forms to EZforms<br />";
}

if ($currentVersion < "0.55") {
    $content .= "Photoalbum Updates (Version 0.55)<br />";
    $content .= "+ Made access denied error messages more specific<br />";
    $content .= "+ Updated all messages to use PHPWS_Message<br />";
    $content .= "+ Print view now opens in a new window<br />";
    $content .= "+ Other various bug fixes<br />";
}

if ($currentVersion < "0.58") {
    $content .= "Photoalbum Updates (Version 0.58)<br />";
    $content .= "+ added the ability to delete entire albums<br />";
}

if ($currentVersion < "0.60") {
    $content .= "Photoalbum Updates (Version 0.60)<br />";
    $content .= "+ bug fixes<br />";
}

if ($currentVersion < "0.62") {
    $content .= "Photoalbum Updates (Version 0.62)<br />";
    $content .= "+ fixed bug related to paging when photos were hidden<br />";
    $content .= "&#160;&#160;&#160;- improper amount of photos showing up on each page<br />";
    $content .= "+ session cleanup<br />";
}

if ($currentVersion < "0.72") {
    $content .= "Photoalbum Updates (Version 0.72)<br />";
    $content .= "+ Batch uploading of photos is now available<br />";
    $content .= "+ Listing photos within an album which are missing descriptions<br />";
    $content .= "+ Other various bug fixes<br />";
}

if ($currentVersion < "0.74") {
    $content .= "Photoalbum Updates (Version 0.74)<br />";
    $content .= "+ fixed bugs when magic quotes are not enabled<br />";
    $content .= "+ certain output is now being properly filtered<br />";
}

if ($currentVersion < "0.76") {
    $content .= "Photoalbum Updates (Version 0.76)<br />";
    $content .= "+ fixed bug where batch upload was not updating the image on the album list<br />";
    $content .= "+ photos in the main album view can now sorted ascending of descending<br />";
}

if ($currentVersion < "0.77") {
    $content .= "Photoalbum Updates (Version 0.77)<br />";
    $content .= "+ added the ability to set the max height and width when viewing an image<br />";
}

if ($currentVersion < "0.79") {
    $content .= "Photoalbum Updates (Version 0.79)<br />";
    $content .= "+ fixed a bug that caused photos to not show in IE<br />";
    $content .= "+ fixed a bug with proper permissions not being checked<br />";
    $content .= "+ recovery from files with spaces in the names<br />";
}

if ($currentVersion < "0.80") {
    $content .= "Photoalbum Updates (Version 0.80)<br />";
    $content .= "+ Fixed problems with batch uploading.<br />";
}

if ($currentVersion < "0.81") {
    $content .= "Photoalbum Updates (Version 0.81)<br />";
    $content .= "+ Put in error message for when the GD libs are not detected.<br />";
    $content .= "+ The updated time is now properly updated on a batch upload.<br />";
}

if ($currentVersion < "0.83") {
    $content .= "Photoalbum Updates (Version 0.82 - 0.83)<br />";
    $content .= "+ Fixed an issue which made it impossible to directly link to photos.<br />";
    $content .= "+ You can now use the same link as when navigating to the photo.<br />";
}

if (in_array($currentVersion, array("0.02", "0.10", "0.41", "0.50", "0.55", "0.58", "0.60", "0.62", "0.72", "0.74", "0.76", "0.77", "0.79", "0.80", "0.81", "0.82", "0.83"))) {
    $currentVersion = "0.8.3";
}

/* Begin using version_compare() */

if (version_compare($currentVersion, "1.1.0") < 0) {
    $content .= "Photoalbum Updates (Version 1.1.0)<br />";
    
    // upgrade to new format
    //   Old:  PHPWS_AlbumManager_op=view&amp;PHPWS_MAN_ITEMS[]=1
    //   New:  PHPWS_Album_op=view&amp;PHPWS_Album_id=1
    $result = $GLOBALS["core"]->sqlSelect("mod_fatcat_elements", "module_title", "photoalbum");
    $new_url = "";
    
    if ($result) {
	foreach($result as $row) {
	    $new_url  = "index.php?module=photoalbum&amp;PHPWS_Album_op=view&amp;PHPWS_Album_id=";
	    $new_url .= substr($row["link"],-1);
	    
	    $updated_url["link"] = $new_url;
	    
	    $GLOBALS["core"]->sqlUpdate($updated_url, "mod_fatcat_elements", "element_id", $row["element_id"]);
	}
    }
    
    $content .= "+ Converted fatcat entries to match new photoalbum format.<br />";
    $content .= "+ Version format changed to better match phpWebSite.<br />";
    $content .= "+ Fixed all links to make it easier to directly link to photos an albums.<br />";
}

if (version_compare($currentVersion, "1.1.2") < 0) {
    $content .= "Photoalbum Updates (Version 1.1.2)<br />";
    $content .= "+ Slideshow added by Darren, thanks!!!<br />";
}

if (version_compare($currentVersion, "1.1.4") < 0) {
    $content .= "Photoalbum Updates (Version 1.1.4)<br />";
    $content .= "+ Fixed bug causing order not to be remembered<br />";
    $content .= "+ Fixed bug in which images that were batch uploaded had a created date of the exact same second.<br />";
}

if (version_compare($currentVersion, "1.2.3") < 0) {
  $GLOBALS['core']->query("ALTER TABLE mod_photoalbum_albums MODIFY COLUMN `image` text", TRUE);
  $content .= "Photoalbum Updates (Verson 1.2.3)<br />";
  $content .= "+ Fixed bug of invalid album thumbnails for long labels.<br />";
}

?>