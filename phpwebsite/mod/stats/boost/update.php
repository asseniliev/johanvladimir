<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
}

$status = 1;

if ($currentVersion < "0.1.4") {
  $GLOBALS['core']->query("CREATE TABLE mod_stats_pm (page_id int(10), count int)", TRUE);
  $content .= "Added pagemaster support.<br />";
}

if ($currentVersion < "0.1.9") {
  if($GLOBALS['core']->sqlTableExists('mod_stats_graph', TRUE)) {
	if(!$GLOBALS['core']->sqlDropTable('mod_stats_graph')) {
	  $content .= "Problem removing unneeded 'mod_stats_graph' table.<br />";
           $status = 0;
        }
  }

if ($GLOBALS['core']->query("ALTER TABLE mod_stats_settings ADD COLUMN `graphs_md5` text NOT NULL AFTER `webstats_enable`", TRUE)) {
    if(!$GLOBALS['core']->sqlUpdate(array('graphs_md5'=>md5(time())), 'mod_stats_settings')) {
       $content .= "Problem adding default value for 'graphs_md5' column.<br />";
       $status = 0;	
    }

    if($status == 1) 
      $content .= "Added ability to prevent non-logged in users from viewing graphs showing web visits.";

} else {
    $content .= "Boost failed to create the attribute 'graphs_md5' for the table 'mod_stats_settings'. <br /><br />";
    $status = 0;
}

   
}

?>