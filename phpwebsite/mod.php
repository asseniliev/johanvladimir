<?php
// Use this file if you want old requests to bounce to your converted
// 0.9.0 version. Set the $NEW_SITE variable to your current web directory
// if the SERVER variable is not working for you.
// 
// Also make sure you have the proper file for the proper module in the 
// convert/reroute/ directory.
// If this is a fresh installation of phpwebsite 0.9.0, you may safely
// delete this file.

// $NEW_SITE = "/var/www/html/phpwebsite/";
$NEW_SITE = str_replace("mod.php", "", $_SERVER['SCRIPT_FILENAME']);
$NEW_SITE = str_replace("article.php", "", $NEW_SITE);


if (isset($_REQUEST['mod'])) $module = $_REQUEST['mod'];

switch ($module){
 case 'userpage':
   $pagemaster = $NEW_SITE . "convert/reroute/pagemaster.php";
   if (!file_exists($pagemaster))
     break;
   include ($pagemaster);
   $oldID = $_REQUEST['page_id'];
   $newID = $convert[$oldID];
   $reroute = "index.php?module=pagemaster&PAGE_user_op=view_page&PAGE_id=$newID";
   break;
   
 case 'announce':
   $announce = $NEW_SITE . "convert/reroute/announce.php";
   if (!file_exists($announce))
     break;
   include ($announce);
   $oldID = $sid;
   $newID = $convert[$oldID];
   if ($newID)
     $reroute = "index.php?module=announce&ANN_user_op=view&ANN_id=$newID";
   break;
}

if (empty($reroute))
     $reroute = "index.php";     
     header("location:" . $reroute);
exit();

?>