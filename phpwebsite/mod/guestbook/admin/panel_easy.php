<?php
include_once $this->base_dir."/admin/panel_intro.php";

$content = '
<script language=JavaScript>
<!--
function CheckValue() {
  if(!(document.FormMain.record.value >= 1)) {
    alert("Invalid record number!");
    document.FormMain.record.focus();
    return false;
  }
}
function gb_picture(Image,imgWidth,imgHeight) {
    var border = 24;
    var img = Image;
    var features;
    var w;
    var h;
    winWidth = (imgWidth<100) ? 100 : imgWidth+border;
    winHeight = (imgHeight<100) ? 100 : imgHeight+border;
    if (imgWidth+border > screen.width) {
        winWidth = screen.width-10;
        w = (screen.width - winWidth)/2;
        features = "scrollbars=yes";      
    } else {
        w = (screen.width - (imgWidth+border))/2;
    }
    if (imgHeight+border > screen.height) {
        winHeight = screen.height-60;
        h = 0;
        features = "scrollbars=yes";      
    } else {
        h = (screen.height - (imgHeight+border))/2 - 20;
    }
    winName = (img.indexOf("t_") == -1) ? img.substr(4,(img.length-8)) : img.substr(6,(img.length-10));
    features = features+\',toolbar=no,width=\'+winWidth+\',height=\'+winHeight+\',top=\'+h+\',left=\'+w;
    theURL = \''.$this->base_url.'picture.php?img=\'+Image;
    popup = window.open(theURL,winName,features);
    popup.focus();  
}
//-->
</script>';

$content .=gb_menu();

$content .= '
<form method="post" action="'.$this->SELF.'" name="FormMain" onsubmit="return CheckValue()">
  <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
    <tr>
      <td>
        <input type="text" name="record" size="12">
        <input type="submit" value="Jump to record">
        <input type="hidden" name="action" value="show">
        <input type="hidden" name="tbl" value="'.$tbl.'">
      </td>
      <td align="right">&nbsp;';
$content .="<a href=\"$this->SELF&action=show&amp;tbl=$tbl&amp;entry=0\"><b>Goto Top</b></a>\n";
if ($prev_page >= 0) {
 $content .="  &nbsp;&nbsp;<a href=\"$this->SELF&action=show&amp;tbl=$tbl&amp;entry=$prev_page\"><font color=\"#FFFF00\" size=\"2\"><b>Previous Page</b></font></a>\n";
}
if ($next_page < $total) {
 $content .="  &nbsp;&nbsp;<a href=\"$this->SELF&action=show&amp;tbl=$tbl&amp;entry=$next_page\"><font color=\"#FFFF00\" size=\"2\"><b>Next Page</b></font></a>\n";
}

$content .='
    </td>
   </tr>
  </table>
  <table border="0" cellspacing="1" cellpadding="5" align="center" width="100%" bgcolor="#000000">
    <tr bgcolor="#BCBCDE">
      <td width="30%"><font size="2"><b>Name</b></font></td>
      <td width="60%"><font size="2"><b>Comments</b></font></td>
      <td width="10%">&nbsp;</td>
    </tr>';

$id = $total-$entry;
$i=0;

while($row = $result->fetchrow()) {

$name = $row['name'];
$date = date("D, F j, Y H:i",$row['date']);
$comment = nl2br($row['comment']);
$bgcolor = ($i % 2) ? "#F7F7F7" : "#DEDFDF";
$i++;

$content .=" <tr bgcolor=\"$bgcolor\">\n   <td width=\"30%\" valign=\"top\">
  <table border=0 cellspacing=0 cellpadding=2>\n    <tr>
     <td><font size=1>$id)</font></td>
     <td><font size=2><b>$name</b></font></td>\n    </tr>\n    <tr>\n";
if ($row['email']) {
  $content .="     <td><font size=1><b>e-mail</b></font></td>
     <td><font size=1>$row[email]</font></td>\n    </tr>\n";
}
if ($row['url']) {
  $content .="    <tr>\n     <td><b><font size=1>URL:</font></b></td>
     <td><font size=1>$row[url]</font></td>\n    </tr>\n";
}
if ($row['icq'] && $this->VARS["allow_icq"]==1) {
  $content .="    <tr>\n     <td><b><font size=1>ICQ:</font></b></td>
     <td><font size=1>$row[icq]</font></td>\n    </tr>\n";
}
if ($row['aim'] && $this->VARS["allow_aim"]==1) {
  $content .="    <tr>\n     <td><b><font size=1>Aim:</font></b></td>
     <td><font size=1>$row[aim]</font></td>\n    </tr>\n";
}
if ($this->VARS["allow_gender"]==1) {
  if ($row['gender']=="f") {
    $content .="    <tr>\n     <td><b><font size=1>Gender:</font></b></td>
     <td><font size=1>female</font></td>\n    </tr>\n";
  } else {
    $content .="    <tr>\n     <td><b><font size=1>Gender:</font></b></td>
     <td><font size=1>male</font></td>\n    </tr>\n";
  }
}
if ($row['location']) {
  $content .="    <tr>\n     <td><b><font size=1>Location:</font></b></td>
     <td><font size=1>$row[location]</font></td>\n    </tr>\n";
}
$hostname = ( eregi("^[-a-z_]+", $row['host']) ) ? "Host" : "IP";
$content .="  </table>\n   </td>\n   <td width=\"60%\" valign=\"top\"><font face=Arial size=1><b>$date $hostname: $row[host]</b></font>\n    <hr size=1>
    <font size=2>";
if ($row['p_filename'] && ereg("^img-",$row['p_filename'])) {
    $new_img_size = $img->get_img_size_format($row['width'], $row['height']);
    if (file_exists("./$GB_UPLOAD/t_$row[p_filename]")) {
        $row['p_filename'] = "t_$row[p_filename]";       
    }
    $content .="<a href=\"javascript:gb_picture('$row[p_filename]',$row[width],$row[height])\"><img src=\"{$this->base_url}$GB_UPLOAD/$row[p_filename]\" align=\"left\" border=\"0\" $new_img_size[2]></a>";
}
$content .="$comment</font>\n";
if ($tbl=="gb") {
    $result = $this->db->query("select * from ".$this->table['com']." where id='$row[id]' order by com_id asc", true);
    while ($com = $result->fetchrow()) {
      $com["comments"] = nl2br($com["comments"]);
      $content .="<table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n";
      $content .="<tr><td colspan=2><hr size=\"1\"></td></tr>\n";
      $content .="<tr><td valign=top><b><font size=\"1\" face=\"Arial, Helvetica, sans-serif\">".date("D, F j, Y H:i",$com['timestamp'])." Host: $com[host]</font></b></td>";
      $content .="<td align=right><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\"><a href=\"$this->SELF&action=del&amp;tbl=com&amp;id=$com[com_id]\">delete</a></font></td>";
      $content .="<tr><td valign=top colspan=2><font size=\"1\" face=\"Verdana, Arial, Helvetica, sans-serif\">$com[name]:<br>\n";
      $content .="$com[comments]</font></td></tr></table>";
    }
}
$content .="   </td>
   <td width=\"10%\"><font size=1><b><a href=\"$this->SELF&action=edit&amp;tbl=$tbl&amp;id=$row[id]&amp;record=$id\">edit</a><br><br>
    <a href=\"$this->SELF&action=del&amp;tbl=$tbl&amp;id=$row[id]\">delete</a></b></font>
   </td>\n </tr>\n";
$id--;

}

$content .='
    </table>
  </form>';

$this->content=$content;
?>