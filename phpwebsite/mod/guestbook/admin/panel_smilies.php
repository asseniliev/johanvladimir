<?php
include_once $this->base_dir."/admin/panel_intro.php";

$content =gb_menu();

$content .= '
<form action="'. $this->SELF .'" name="FormMain" method="post">
  <table bgcolor="#000000" border="0" cellspacing="1" cellpadding="7" align="center" width="100%">
        <tr bgcolor="#000000"> 
            <td colspan="6" align="center" height="25"><b><font size="2" face="Verdana, Arial" color="#FFFF00">Smilies</font></b></td>
        </tr>
        <tr bgcolor="#663333"> 
            <td height="25"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Smilie</b></font></td>
            <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Filename</b></font></td>
            <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Code</b></font></td>
            <td><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Emotion</b></font></td>
            <td colspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>Action</b></font></td>
          </tr>';

if (isset($smilie_data)) {
$content .= "
          <tr bgcolor=\"#f7f7f7\"> 
            <td><img src=\"".$this->base_url."img/smilies/".$smilie_data['s_filename']."\" width=\"".$smilie_data['width']."\" height=\"".$smilie_data['height']."\"></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$smilie_data['s_filename']."</font></td>
            <td><input type=\"text\" name=\"s_code\" value=\"".htmlspecialchars($smilie_data['s_code'])."\" size=\"15\"></td>
            <td><input type=\"text\" name=\"s_emotion\" value=\"".htmlspecialchars($smilie_data['s_emotion'])."\" size=\"25\"><input type=\"hidden\" name=\"edit_smilie\" value=\"".$smilie_data['id']."\"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>\n";
} else {
    $result=$this->db->query("select * from ".$this->table['smile']." order by s_filename ASC",true);
    while ($record =$result->fetchrow()) {
$content .= "
          <tr bgcolor=\"#f7f7f7\"> 
            <td><img src=\"{$this->base_url}img/smilies/".$record['s_filename']."\" width=\"".$record['width']."\" height=\"".$record['height']."\"></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$record['s_filename']."</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$record['s_code']."</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">".$record['s_emotion']."</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"$this->SELF&action=smilies&amp;edit_smilie=".$record['id']."\"><font color=\"#000033\">edit</font></a></font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"$this->SELF&action=smilies&amp;del_smilie=".$record['id']."\"><font color=\"#000033\">delete</font></a></font></td>
          </tr>\n";
    }
}
if (isset($smilie_list)) {
    for(reset($smilie_list); $key=key($smilie_list); next($smilie_list)) {
$content .= "
          <tr bgcolor=\"#f7f7f7\"> 
            <td>$smilie_list[$key]</td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">$key</font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><input type=\"text\" name=\"new_smilie[$key]\" size=\"15\"></font></td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><input type=\"text\" name=\"new_emotion[$key]\" size=\"25\"></font></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>\n";
    }
}
$content .= '
        </table>
        <div align="center"><br>
          <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="'. $this->SELF .'&action=smilies&amp;scan_dir=1">Scan directory (img/smilies)</a></b><br><br>
          </font></div>
  <br>
  <center>
    <input type="submit" value="Submit Settings">
    <input type="reset" value="Reset">
    <input type="hidden" name="action" value="smilies">
    <input type="hidden" name="add_smilies" value="1">
  </center>
</form>';

$this->content=$content;
?>