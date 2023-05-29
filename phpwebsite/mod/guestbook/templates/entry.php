<tr bgcolor="$bgcolor">
 <td width="32%" valign="top">
   <table border="0" cellspacing="0" cellpadding="2">
     <tr>
       <td class="font2" width="8%"><b>$id)</b></td>
       <td class="font1" width="92%"><b>$row[name]</b>&nbsp;$GENDER  </td>
     </tr>
     <tr>
       <td class="font2" valign="top" width="8%">&nbsp;</td>
       <td class="font2">$HOST <br />$LANG[FormLoc]:<br />$row[location]</td>
     </tr>
     <tr>
       <td class="font2" valign="top" width="8%">&nbsp;</td>
       <td class="font1"><img src="$GB_PG[base_url]/img/ip.gif" width="14" height="14" alt="$LANG[AltIP]">&nbsp;&nbsp;<img src="$GB_PG[base_url]/img/browser.gif" width="16" height="16" alt="$row[browser]">&nbsp;
        $URL
        $ICQ
        $AIM
        $EMAIL
        </td>
     </tr>
    </table>
  </td>
  <td width="68%" class="font1" valign="top"> 
    <div align="left" class="font3"><img src="$GB_PG[base_url]/img/post.gif" width="9" height="9">$DATE&nbsp;
    <a href="$GB_COMMENT"><img src="$GB_PG[base_url]/img/edit.gif" width="18" height="13" border="0" alt="$LANG[AltCom]"></a>
    </div>
    <hr size="1">
    <div align="left" class="font2">
    $USER_PIC $MESSAGE
    </div>
    <hr size="1">
    <div align="left" class="font2">
    $COMMENT
    </div>
 </td>
</tr>
