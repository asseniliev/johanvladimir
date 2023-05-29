<?php 
include_once $this->base_dir."/admin/panel_intro.php";

$content =gb_menu();

$content .= '
<form action="'. $this->SELF .'" name="FormMain" method="post">
  <table border=0 width=100% bgcolor="#000000">
    <tr bgcolor="#000000"> 
      <td colspan=2 align=center height="25"><b><font size="2" face="Verdana, Arial" color="#FFFF00">Templates</font></b></td>
    </tr>
    <tr bgcolor="#FCF0C0"> 
      <td colspan=2><font size="1" face="Verdana, Arial" color="#DD0000">
      <b>Give write permissions to the webserver on the template files!</b></font></td>
    </tr>
    <tr bgcolor="#f7f7f7"> 
      <td valign="top"> <b><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Guestbook 
        Templates</font></b><br>
          <br>
        <table border="0" cellspacing="0" cellpadding="1">';

for (reset($GB_TPL);$key=key($GB_TPL); next($GB_TPL)) {
    $content .= "<tr> 
            <td width=\"15\">-</td>
            <td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"$this->SELF&action=template&amp;tpl_name=$GB_TPL[$key]\"><font color=\"#000066\">
            $GB_TPL[$key]</font></a></font></td>
          </tr>\n";
}

$content .= '
         </table>
       <br>
      </td>
      <td valign=top align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#009900"> 
        </font> 
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#009900"><b> 
              '. $tpl_name .'
              </b> </font></td>
          </tr>
          <tr>
            <td>
              <textarea name="gb_template" cols="60" rows="30" class="textfield" wrap="VIRTUAL">'. htmlspecialchars($gb_template) .'</textarea>
            </td>
          </tr>
        </table>
        <br>
      </td>
    </tr>
  </table>
  <br>
  <center>
    <input type="submit" value="Submit Settings">
    <input type="reset" value="Reset">
    <input type="hidden" name="action" value="template">
    <input type="hidden" name="tpl_name" value="'. $tpl_name .'">
    <input type="hidden" name="save" value="update">
  </center>
</form>';

$this->content=$content;

?>