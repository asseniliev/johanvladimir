<?php 

include_once $this->base_dir."/admin/panel_intro.php";

$content =gb_menu();

$content .= '
<form method="post" action="'. $this->SELF .'">
  <table border="0" cellspacing="1" cellpadding="4" width="100%" align="center" bgcolor="#000000">
    <tr bgcolor="#000000">
      <td colspan="2" height="25"><b><font size="2" color="#FFFF00" face="Verdana, Arial, Helvetica, sans-serif">Edit the guestbook entry:</font></b></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name:</font></td>
      <td><input type="text" name="name" size="44" maxlength="50" value="'. $row['name'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-mail:</font></td>
      <td><input type="text" name="email" size="44" maxlength="60" value="'. $row['email'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Location:</font></td>
      <td><input type="text" name="location" size="44" maxlength="60" value="'. $row['location'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Homepage:</font></td>
      <td><input type="text" name="url" size="44" maxlength="60" value="'. $row['url'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">ICQ:</font></td>
      <td><input type="text" name="icq" size="44" maxlength="60" value="';

      if ($row['icq']!=0) {$content .= $row['icq'];}

$content .= '"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Aim:</font></td>
      <td><input type="text" name="aim" size="44" maxlength="60" value="'. $row['aim'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gender:</font></td>
      <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><input type="radio" name="gender" value="m" ';
      
      if (!$row['gender'] || $row['gender']=="m") {$content .= "checked";}

$content .= '>male
        <input type="radio" name="gender" value="f" ';
        
        if ($row['gender']=="f") {echo "checked";}

$content .= '>female</font></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Host:</font></td>
      <td><input type="text" name="host" size="44" maxlength="60" value="'. $row['host'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Browser:</font></td>
      <td><input type="text" name="browser" size="44" maxlength="60" value="'. $row['browser'] .'"></td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Your Message:</font></td>
      <td>
        <textarea name="comment" cols="42" rows="10" wrap="VIRTUAL">'. $row['comment'] .'</textarea>
      </td>
    </tr>
    <tr bgcolor="#EFEFEF">
      <td width="25%">&nbsp;</td>
      <td>
        <input type="submit" value="Save Changes">
        <input type="reset"  value="Reset">
        <input type="button" value="Go Back" onclick="javascript:history.go(-1)">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="record" value="'.$record .'">
        <input type="hidden" name="id" value="'.$row['id'] .'">
        <input type="hidden" name="tbl" value="'. $tbl .'">
      </td>
    </tr>
  </table>
</form>';

$this->content=$content;
?>