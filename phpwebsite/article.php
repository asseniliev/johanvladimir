<?php
// This is a crutch file. It will be removed in future versions.
// It is only needed for 0.8.3 conversions. You may remove it
// if you installed 0.9.0 from scratch.

if ($_REQUEST['sid']){
  $sid = $_REQUEST['sid'];
  $module = 'announce';
}
include('mod.php');

?>
