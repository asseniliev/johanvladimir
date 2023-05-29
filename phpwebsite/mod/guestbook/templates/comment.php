<script language="JavaScript">
<!--
var flag=0;
function SetFlag() {
  flag=1;
}
//-->
</script>
<table border="0" cellspacing="0" cellpadding="2" align="center" width="$VARS[width]">
  <tr>
    <td width="45%" valign="bottom" class="font2">$LANG[BookMess9]</td>
    <td width="55%" align="right" valign="bottom" class="font2"> <b><img src="$GB_PG[base_url]/img/return.gif" width="10" height="10">
     <a href="$GB_PG[index]">$LANG[BookMess4]</a></b></td>
  </tr>
</table>
<form method="post" action="$GB_PG[comment]" onsubmit="return SetFlag()">
  <table border="0" cellspacing="1" cellpadding="4" width="$VARS[width]" align="center" bgcolor="$VARS[tb_bg_color]">
    <tr>
     <td colspan="2" bgcolor="$VARS[tb_hdr_color]"><b><font size="2" face="$VARS[font_face]" color="$VARS[tb_text]">$LANG[BookMess3]:</font></b></td>
    </tr>

$GB_ENTRY

    <tr bgcolor="$VARS[tb_color_1]">
      <td width="32%" class="font1"><img src="$GB_PG[base_url]/img/edit.gif" width="18" height="13">$LANG[BookMess7]:</td>
      <td><textarea name="comment" cols="32" rows="6"></textarea></td>
    </tr>
    <tr bgcolor="$VARS[tb_color_1]">
      <td width="32%" class="font1">$LANG[FormName]:</td>
      <td><input type="text" name="gb_user" size="35" maxlength="25" value="$G_NAME"></td>
    </tr>
    
$COMMENT_PASS

    <tr bgcolor="$VARS[tb_color_1]">
      <td width="32%">&nbsp;</td>
      <td>
        <input type="submit" name="action" value="$LANG[FormSubmit]" class="input" onclick="if(flag==1) return false;">
        <input type="reset" value="$LANG[FormReset]" class="input">
        <input type="hidden" name="gb_id" value="$id">
        <input type="hidden" name="gb_comment" value="1">
      </td>
    </tr>
  </table>
</form>

