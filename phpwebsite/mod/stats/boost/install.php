<?php
/**
 *
 */

/* Make sure the user is a deity before running this script */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/stats/boost/install.sql", 1, 1)) {
  if(!$GLOBALS["core"]->query("INSERT INTO mod_stats_settings VALUES (1, NULL, 1, 0, 0, 0, '', 10 ,1, 1, '" . md5(time()) . "')", TRUE)) {
    $content .= "Problem updating stats settings table.<br />";
    $status = 0;
  }

  $stats_options = "10";

  if($GLOBALS["core"]->moduleExists("announce"))
    $stats_options .= "::4::1";
  
  if($GLOBALS["core"]->moduleExists("comments"))
    $stats_options .= "::2";

  if($GLOBALS["core"]->moduleExists("linkman"))
    $stats_options .= "::3";

  if($GLOBALS["core"]->moduleExists("faq"))
    $stats_options .= "::5";

  if($GLOBALS["core"]->moduleExists("photoalbum"))
    $stats_options .= "::6";

  if($GLOBALS["core"]->moduleExists("documents"))
    $stats_options .= "::7";

  if($GLOBALS["core"]->moduleExists("notes"))
    $stats_options .= "::8::9";

  if(!$GLOBALS["core"]->sqlUpdate(array("stats_viewable"=>$stats_options),"mod_stats_settings")) {
    $content .= "Problem updating stats to show by default.<br />";
    $status = 0;
  }    

  
  if($status == 1)
    $content .= "All Stats tables successfully written.<br />";


} else {
  $content .= "Problem importing sql file for stats module.<br />";
  $status = 0;
}

?>
