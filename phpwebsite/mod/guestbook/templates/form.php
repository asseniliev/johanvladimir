<script language="JavaScript">
<!--
var flag=0;
function openCentered(theURL,winName,winWidth,winHeight,features) {
 var w = (screen.width - winWidth)/2;
 var h = (screen.height - winHeight)/2 - 30;
 features = features+',width='+winWidth+',height='+winHeight+',top='+h+',left='+w;
 window.open(theURL,winName,features);
}
function checkForm() {
 document.book.gb_name.value=trim(document.book.gb_name.value);
 document.book.gb_comment.value=trim(document.book.gb_comment.value);
 if(document.book.gb_name.value == "") {
   alert("$LANG[ErrorPost1]");
   document.book.gb_name.focus();
   return false;
 }
 if(document.book.gb_comment.value == "") {
   alert("$LANG[ErrorPost2]");
   document.book.gb_comment.focus();
   return false;
 }
 if(document.book.gb_comment.value.length<$VARS[min_text] || document.book.gb_comment.value.length>$VARS[max_text]) {
   alert("$LANG[ErrorPost3]");
   document.book.gb_comment.focus();
   return false;
 }
 flag=1;
 return true;
}
function trim(value) {
 startpos=0;
 while((value.charAt(startpos)==" ")&&(startpos<value.length)) {
   startpos++;
 }
 if(startpos==value.length) {
   value="";
 } else {
   value=value.substring(startpos,value.length);
   endpos=(value.length)-1;
   while(value.charAt(endpos)==" ") {
     endpos--;
   }
   value=value.substring(0,endpos+1);
 }
 return(value);
}
function emoticon(text) {
  text = ' ' + text + ' ';
  if (document.book.gb_comment.createTextRange && document.book.gb_comment.caretPos) {
   var caretPos = document.book.gb_comment.caretPos;
   caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
   document.book.gb_comment.focus();
  } else {
    document.book.gb_comment.value  += text;
	document.book.gb_comment.focus();
  }
}
//-->
</script>
<table border="0" cellspacing="0" cellpadding="2" align="center" width="$VARS[width]">
 <tr>
   <td width="56%" valign="bottom" class="font2">$LANG[FormMess1]</td>
   <td width="44%" align="right" valign="bottom" class="font2"><b><img src="$GB_PG[base_url]/img/return.gif" width="10" height="10">
     <a href="$GB_PG[index]">$LANG[BookMess4]</a> </b></td>
 </tr>
</table>
<form method="post" action="$GB_PG[addentry]" name="book" enctype="multipart/form-data" onsubmit="return checkForm()">
<table border="0" cellspacing="1" cellpadding="4" width="$VARS[width]" align="center" bgcolor="$VARS[tb_bg_color]">
  <tr>
    <td colspan="2" bgcolor="$VARS[tb_hdr_color]"><b><font size="2" face="$VARS[font_face]" color="$VARS[tb_text]">$LANG[BookMess3]:</font></b></td>
  </tr>
  <tr bgcolor="$VARS[tb_color_1]">
    <td width="25%" class="font1"><img src="$GB_PG[base_url]/img/user.gif" width="16" height="15"> $LANG[FormName]*:</td>
    <td><input type="text" name="gb_name" size="42" maxlength="30" value="$G_NAME"></td>
  </tr>
  <tr bgcolor="$VARS[tb_color_1]">
    <td width="25%" class="font1"><img src="$GB_PG[base_url]/img/email.gif" width="15" height="15"> $LANG[FormEmail]:</td>
    <td><input type="text" name="gb_email" size="42" maxlength="30"></td>
  </tr>
  <tr bgcolor="$VARS[tb_color_1]">
    <td width="25%" class="font1"><img src="$GB_PG[base_url]/img/location.gif" width="16" height="16"> $LANG[FormLoc]:</td>
    <td><input type="text" name="gb_location" size="42" maxlength="30"></td>
  </tr>
  <tr bgcolor="$VARS[tb_color_1]">
    <td width="25%" class="font1"><img src="$GB_PG[base_url]/img/home.gif" width="16" height="16"> $LANG[FormUrl]:</td>
    <td><input type="text" name="gb_url" size="42" value="http://" maxlength="45"></td>
  </tr> 

$OPTIONAL

  <tr bgcolor="$VARS[tb_color_1]">
    <td width="25%" valign="top" class="font1">$LANG[FormMessage]*:
    <br><br>
     <table border="0" cellspacing="0" cellpadding="6" align="center">
          <tr align="center" valign="middle"> 
            <td><a href="javascript:emoticon(':-)')"><img src="$GB_PG[base_url]/img/smilies/a1.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':-(')"><img src="$GB_PG[base_url]/img/smilies/a2.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(';-)')"><img src="$GB_PG[base_url]/img/smilies/a3.gif" border="0"></a></td>
          </tr>
          <tr align="center" valign="middle"> 
            <td><a href="javascript:emoticon(':o')"><img src="$GB_PG[base_url]/img/smilies/a4.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':D')"><img src="$GB_PG[base_url]/img/smilies/a5.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':p')"><img src="$GB_PG[base_url]/img/smilies/a6.gif" border="0"></a></td>
          </tr>
          <tr align="center" valign="middle"> 
            <td><a href="javascript:emoticon(':cool:')"><img src="$GB_PG[base_url]/img/smilies/a7.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':rolleyes:')"><img src="$GB_PG[base_url]/img/smilies/a8.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':mad:')"><img src="$GB_PG[base_url]/img/smilies/a9.gif" border="0"></a></td>
          </tr>
          <tr align="center" valign="middle"> 
            <td><a href="javascript:emoticon(':eek:')"><img src="$GB_PG[base_url]/img/smilies/a10.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':-)')"><img src="$GB_PG[base_url]/img/smilies/a1.gif" border="0"></a></td>
            <td><a href="javascript:emoticon(':confused:')"><img src="$GB_PG[base_url]/img/smilies/a11.gif" border="0"></a></td>
          </tr>
          <tr align="center">
            <td colspan="3"> 
              <div align="center" class="font2"><a href="javascript:openCentered('$LANG_CODES','codes','640','450','scrollbars=yes')">$LANG[FormMess4]</a>
              </div>
            </td>
          </tr>
        </table>
    </td>
    <td bgcolor="$VARS[tb_color_1]" valign="top"><textarea name="gb_comment" cols="41" rows="11" wrap="VIRTUAL"></textarea><br>
     <input type="checkbox" name="gb_private" value="1"> <font size="1" face="$VARS[font_face]">$LANG[FormPriv]</font>
    </td>
  </tr>
  <tr bgcolor="$VARS[tb_color_1]">
    <td width="25%"><div align="left" class="font2">$HTML_CODE<br>$SMILE_CODE<br>$AG_CODE</div></td>
    <td>
      <input type="submit" name="gb_action" value="$LANG[FormSubmit]" class="input" onclick="if(flag==1) return false;">
      <input type="submit" name="gb_action" value="$LANG[FormPreview]" class="input" onclick="if(flag==1) return false;">
      <input type="reset" value="$LANG[FormReset]" class="input">
    </td>
  </tr>
</table>
</form>
