<?php
$include_path = dirname(dirname(__FILE__));

include( dirname(dirname($include_path)). "/conf/config.php");

include_once $include_path."/admin/config.inc.php";

global $table_prefix, $dbhost,$dbuser,$dbpass, $dbname;

$db = mysql_connect($dbhost,$dbuser,$dbpass) or die ("Can not connect to database server.");

if (!mysql_select_db($dbname, $db)) {
    die ("Database error.");
}

$sm_result = mysql_query("SELECT * FROM ".$table_prefix.$GB_TBL['smile']);
$i=0;
while ($smilies = mysql_fetch_array($sm_result,MYSQL_ASSOC)) {
    $bgcolor = ($i % 2) ? "#E1E1E1" : "#F7F7F7";
    echo "
    <tr>
      <td bgcolor=\"$bgcolor\"><b>".$smilies['s_code']." </b></td>
      <td bgcolor=\"$bgcolor\">".htmlspecialchars($smilies['s_emotion'])."</td>
      <td bgcolor=\"$bgcolor\"><img alt=\"".$smilies['s_code']."\" border=\"0\" src=\"../img/smilies/".$smilies['s_filename']."\" width=\"".$smilies['width']."\" height=\"".$smilies['height']."\"></td>
    </tr>\n";
    $i++;
}
?>