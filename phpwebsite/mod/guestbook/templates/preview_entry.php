<table border="0" cellspacing="1" cellpadding="5" width="$VARS[width]" align="center" bgcolor="$VARS[tb_bg_color]">
 <tr bgcolor="$VARS[tb_color_1]">
   <td width="32%" valign="top">
     <table border="0" cellspacing="0" cellpadding="2">
        <tr><td class="font2" width="8%"><b>1)</b></td>
          <td class="font1" width="92%"><b>$row[name]</b>&nbsp;$GENDER
          </td>
        </tr>
        <tr><td class="font2">&nbsp;</td>
        <td class="font1">$row[email]</td></tr>
        <tr><td class="font2">&nbsp;</td>
        <td class="font2">$HOST <br />$LANG[FormLoc]:<br>$row[location]</td></tr>
        <tr><td class="font2">&nbsp;</td>
        <td class="font1">
            <img src="$GB_PG[base_url]/img/ip.gif" width="14" height="14" alt="$LANG[AltIP]">&nbsp;&nbsp;<img src="$GB_PG[base_url]/img/browser.gif" width="16" height="16" alt="$AGENT">&nbsp;
           $URL
           $ICQ
           $AIM
           $EMAIL
        </td></tr>

     </table>
   </td>
   <td width="68%" class="font1" valign="top">
     <div align="left" class="font3"><img src="$GB_PG[base_url]/img/post.gif" width="9" height="9">$DATE&nbsp;
     </div>
       <hr size="1">$USER_PIC $message
   </td>
 </tr>
 <tr bgcolor="$VARS[tb_color_1]">
   <td width="32%">&nbsp;</td>
   <td>
    <input type="button" name="back" value="$LANG[FormBack]" class="input" onclick="javascript:history.back()">
    <input type="submit" name="gb_action" value="$LANG[FormSubmit]" class="input" onclick="if(flag==1) return false;">
    $HIDDEN
   </td>
 </tr>
</table>
