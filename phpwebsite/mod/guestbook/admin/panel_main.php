<?php
include_once $this->base_dir."/admin/panel_intro.php";

$content ='
<script language=JavaScript>
<!--
function CheckValue() {
  if(!(document.FormMain.entries_per_page.value >= 1)) {
    alert("The maximum records per page must be greater than 0!");
    document.FormMain.entries_per_page.focus();
    return false;
  }
}
//-->
</script>';

$content .=gb_menu();

$content .= '
<form action="'.$this->SELF.'" name="FormMain" method="post" onsubmit="return CheckValue()">
<table border=0 width=100% bgcolor="#000000">
  <tr bgcolor="#000000">
    <td colspan=2 align=center height="25"><b><font size="2" face="Verdana, Arial" color="#FFFF00">General Options</font></b></td>
  </tr>
  <tr bgcolor="#FCF0C0">
    <td colspan=2><font size="1" face="Verdana, Arial"><b>Below are numerous configuration options for your guestbook.</b></font></td>
  </tr>
  <tr bgcolor="#dedfdf">
    <td width=50%> <b><font size="2" face="Verdana, Arial">Maximum Records Displayed
      Per Page </font></b> <font size="1" face="Verdana, Arial"><br>
      20 records per page is recommend.</font></td>
    <td width=50% valign=top bgcolor="#dedfdf">
      <input type="text" name="entries_per_page" value="'.$this->VARS["entries_per_page"].'" maxlength="5" size="5"></td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">Language</font></b><br>
      <font size="1" face="Verdana, Arial">The language file you want to use</font>.</td>
    <td width=50% valign=top><input type="text" name="lang" value="'.$this->VARS["lang"].'">
     <select name="lang_file" onChange="FormMain.lang.value=options[selectedIndex].value">
      <option value="english" selected>Language</option>';

$old_dir = getcwd();

chdir($this->base_dir."/lang");
$hnd = opendir(".");
while ($file = readdir($hnd)) {
    if(is_file($file)) {
        if (!ereg("^codes-",$file)) {
            $langlist[] = $file;
        }
    }
}
closedir($hnd);
if ($langlist) {
    asort($langlist);
    while (list ($key, $file) = each ($langlist)) {
        if (ereg(".php|.php3",$file,$regs)) {
            $language = str_replace("$regs[0]","","$file");
            $content .="<option value=\"$language\">$language</option>\n";
        }
    }
}
chdir($old_dir);

$content .= '
     </select>
    </td>
  </tr>
  <tr bgcolor="#dedfdf">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">Show Guest\'s IP or Hostname</font></b><br>
      <font size="1" face="Verdana, Arial">For security reasons, you may wish to display the IP or Hostname of
      the person signing your guestbook. The default is ON.</font>
    </td>
    <td width=50% valign=top><font size="2" face="Verdana, Arial">
      <input type="radio" name="show_ip" value="1" ';

      if ($this->VARS["show_ip"] == 1) {$content .="checked";}

$content .= '>
      Show IP or Hostname <br>
      <input type="radio" name="show_ip" value="0" ';
      
      if ($this->VARS["show_ip"] == 0) {$content .="checked";}

$content .= '>
      Hide IP or Hostname </font>
    </td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">HTML Codes</font></b><br>
      <font size="1" face="Verdana, Arial">If HTML Code is enabled, this means the users can use HTML
      Code in the comment field. </font></td>
    <td width=50% valign=top>
      <font size="2" face="Verdana, Arial">
        <input type="radio" name="allow_html" value="1" ';
        
        if ($this->VARS["allow_html"] == 1) {$content .="checked";}

$content .= '>
        allow HTML Codes <br>
        <input type="radio" name="allow_html" value="0" ';
        
        if ($this->VARS["allow_html"] == 0) {$content .="checked";}

$content .= '>
        disable HTML Codes</font>
    </td>
  </tr>
  <tr bgcolor="#dedfdf">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">Smilies</font></b><br>
      <font size="1" face="Verdana, Arial">If you have used email or internet chat, you are likely
      familiar with the smilie concept. Certain standard emoticons are automatically
      converted into smilies.</font></td>
    <td width=50% valign=top>
      <font size="2" face="Verdana, Arial">
        <input type="radio" name="smilies" value="1" ';
        
        if ($this->VARS["smilies"] == 1) {$content .="checked";}

$content .= '>
        activate Smilies <br>
        <input type="radio" name="smilies" value="0" ';
        
        if ($this->VARS["smilies"] == 0) {$content .="checked";}

$content .= '>
        disable Smilies</font>
    </td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">AGCodes</font></b><br>
      <font size="1" face="Verdana, Arial">AGCode is a variation on the HTML tags you may already be familiar with.
       Basically, it allows you to add functionality or style to your message that would normally require HTML.
       You can use AGCode even if HTML is not enabled for the guestbook.</font></td>
    <td width=50% valign=top>
      <font size="2" face="Verdana, Arial">
        <input type="radio" name="agcode" value="1" ';
        
        if ($this->VARS["agcode"] == 1) {$content .="checked";}

$content .= '>
        allow AGCodes <br>
        <input type="radio" name="agcode" value="0" ';
        
        if ($this->VARS["agcode"] == 0) {$content .="checked";}

$content .= '>
        disable AGCodes</font>
    </td>
  </tr>
</table>
  <table border=0 width=100% bgcolor="#000000">
    <tr bgcolor="#000000"> 
      <td colspan=2 align=center height="25"><b><font size="2" face="Verdana, Arial" color="#FFFF00">Field 
        Definitions</font></b></td>
    </tr>
    <tr bgcolor="#FCF0C0"> 
      <td colspan=2><font size="1" face="Verdana, Arial"><b>Below are numerous 
        configuration options for your guestbook fields.</b></font></td>
    </tr>
    <tr bgcolor="#dedfdf"> 
      <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">ICQ Field</font></b><br>
        <font size="1" face="Verdana, Arial">The ICQ field is optional. You can 
        also enable or disable it.</font> </td>
      <td width=50% valign=top><font size="2" face="Verdana, Arial"> 
        <input type="radio" name="allow_icq" value="1" ';
        
        if ($this->VARS["allow_icq"] == 1) {$content .="checked";}

$content .= '>
        use ICQ Field <br>
        <input type="radio" name="allow_icq" value="0" ';
        
        if ($this->VARS["allow_icq"] == 0) {$content .="checked";}

$content .= '>
        do not use ICQ Field </font> </td>
    </tr>
    <tr bgcolor="#f7f7f7"> 
      <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">AIM Field</font></b><br>
        <font size="1" face="Verdana, Arial">AIM means AOL Instant Messenger.</font></td>
      <td width=50% valign=top> <font size="2" face="Verdana, Arial"> 
        <input type="radio" name="allow_aim" value="1" ';
        
        if ($this->VARS["allow_aim"] == 1) {$content .="checked";}

$content .= '>
        use AIM Field <br>
        <input type="radio" name="allow_aim" value="0" ';
        
        if ($this->VARS["allow_aim"] == 0) {$content .="checked";}

$content .= '>
        do not use AIM Field</font> </td>
    </tr>
    <tr bgcolor="#dedfdf"> 
      <td width=50% valign=top bgcolor="#dedfdf"> <b><font size="2" face="Verdana, Arial">Gender 
        Field</font></b><br>
        <font size="1" face="Verdana, Arial">The gender field is also optional.</font></td>
      <td width=50% valign=top> <font size="2" face="Verdana, Arial"> 
        <input type="radio" name="allow_gender" value="1" ';
        
        if ($this->VARS["allow_gender"] == 1) {$content .="checked";}

$content .= '>
        use Gender Field <br>
        <input type="radio" name="allow_gender" value="0" ';
        
        if ($this->VARS["allow_gender"] == 0) {$content .="checked";}

$content .= '>
        do not use Gender Field </font></td>
    </tr>
    <tr bgcolor="#f7f7f7"> 
      <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">Picture 
        Upload</font></b><br>
        <font size="1" face="Verdana, Arial">You can also allow people uploading 
        images to your server. The image width and height can be set here. All
        images which are bigger will automatically be resized. </font></td>
      <td width=50% valign=top> <font size="2" face="Verdana, Arial"> 
        <input type="radio" name="allow_img" value="1" ';
        
        if ($this->VARS["allow_img"] == 1) {$content .="checked";}

$content .= '>
        allow Picture Upload <br>
        <input type="radio" name="allow_img" value="0" ';
        
        if ($this->VARS["allow_img"] == 0) {$content .="checked";}

$content .= '>
        disable Picture Upload <br>
        <font size="1">Border</font>: 
        <input type="text" name="img_width" size="5" value="'.$this->VARS["img_width"].'">
        X 
        <input type="text" name="img_height" size="5" value="'.$this->VARS["img_height"].'">
        <font size="1">width x height &nbsp;&nbsp;max. 
        <input type="text" name="max_img_size" size="4" value="'.round($this->VARS["max_img_size"]).'">
        kb<br>
        </font></font></td>
    </tr>
    <tr bgcolor="#f7f7f7">
      <td width=50% valign=top bgcolor="#dedfdf"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Thumbnails<br>
        </b><font size="1">Requires Image Magick or PHP\'s GD extension</font></font></td>
      <td width=50% valign=top bgcolor="#dedfdf"><font size="2" face="Verdana, Arial"> 
        <input type="checkbox" name="thumbnail" value="1" ';
        
        if ($this->VARS["thumbnail"] == 1) {$content .="checked";}

$content .= '>
        create thumbnails<br>
        <font size="1">min. filesize</font> 
        <input type="text" name="thumb_min_fsize" size="5" value="'.$this->VARS["thumb_min_fsize"].'">
        <font size="1"> kb</font></font></td>
    </tr>
  </table>
<table border=0 width=100% bgcolor="#000000">
  <tr bgcolor="#000000">
    <td colspan=2 align=center height="25"><b><font size="2" face="Verdana, Arial" color="#FFFF00">Email Options</font></b></td>
  </tr>
  <tr bgcolor="#FCF0C0">
    <td colspan=2><font size="1" face="Verdana, Arial">
      <b>Sendmail is installed on most Unix/Linux servers by default. The
        path to sendmail is stored in the php.ini file. </b>
      </font></td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top>
      <font size="2" face="Verdana, Arial"><b>Webmaster E-mail</b> <br>
       <font size="1" face="Verdana, Arial">Your e-mail address</font></font>
    </td>
    <td width=50% valign=top>
      <input type="text" name="admin_mail" value="'.$this->VARS["admin_mail"].'" size=30 maxlength=60>
    </td>
  </tr>
  <tr bgcolor="#dedfdf">
    <td width=50% valign=top>
      <b><font size="2" face="Verdana, Arial">E-mail notification</font></b><br>
        <font size="1" face="Verdana, Arial" COLOR="#000080">Select whether
        you want to send emails to yourself when someone has signed your guestbook.
        Note: your email address above must be valid and an email process must
        be properly configured.</font>
    </td>
    <td width=50% valign=top> <font size="2" face="Verdana, Arial">
      <input type="checkbox" name="notify_private" value="1" ';
      
      if ($this->VARS["notify_private"] == 1) {$content .="checked";}

$content .= '>
      send e-mail to webmaster (private messages)<br>
      <input type="checkbox" name="notify_admin" value="1" ';
      
      if ($this->VARS["notify_admin"] == 1) {$content .="checked";}

$content .= '>
      send e-mail to webmaster<br>
      <input type="checkbox" name="notify_guest" value="1" ';
      
      if ($this->VARS["notify_guest"] == 1) {$content .="checked";}

$content .= '>
      send e-mail to guest</font></td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top>
      <font size="2" face="Verdana, Arial"><b>E-mail Notification Message</b></font><br>
       <font size="1" face="Verdana, Arial">You can customize the exact wording for the message.
        Edit the wording in the box to the right.</font>
    </td>
    <td width=50% valign=top>
      <textarea rows="5" cols="30" wrap="virtual" name="notify_mes">'.$this->VARS["notify_mes"].'</textarea>
    </td>
  </tr>
</table>
<table border=0 width=100% bgcolor="#000000">
  <tr bgcolor="#000000">
    <td colspan=2 align=center height="25"><b><font size="2" face="Verdana, Arial" color="#FFFF00">Miscellaneous Options 
    </font></b></td>
  </tr>
  <tr bgcolor="#FCF0C0">
    <td colspan=2><font size="1" face="Verdana, Arial">
      <b>Below are numerous configuration options for your Guestbook.</b>
      </font></td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">Guestbook Comment</font></b><br>
      <font size="1" face="Verdana, Arial">If you want to password-protect the comment feature,
      set this option to \'Password required\'.
      </font></td>
    <td width=50% valign=top>
      <font size="2" face="Verdana, Arial">
        <input type="radio" name="need_pass" value="1" ';
        
        if ($this->VARS["need_pass"] == 1) {$content .="checked";}

$content .= '>
        Password required <br>
        <input type="radio" name="need_pass" value="0" ';
        
        if ($this->VARS["need_pass"] == 0) {$content .="checked";}

$content .= '>
        No Password required<br>
        Password: <input type="text" name="comment_pass" size="18" value="'.$this->VARS["comment_pass"].'"></font>
    </td>
  </tr>
  <tr bgcolor="#dedfdf">
      <td width=50% valign=top bgcolor="#dedfdf"> <b><font size="2" face="Verdana, Arial">Message 
        Length</font></b><br>
        <font size="1" face="Verdana, Arial">You can set the minimum and maximum 
        message length here. The max. word length is an option to avoid messages 
        from nice people entering a bunch of characters without spaces. :)</font> 
      </td>
    <td width=50% valign=top> <font size="2" face="Verdana, Arial">
      <input type="text" name="min_text" size="5" value="'.$this->VARS["min_text"].'">
      Min. message length<br>
      <input type="text" name="max_text" size="5" value="'.$this->VARS["max_text"].'">
      Max. message length<br>
      <input type="text" name="max_word_len" size="5" value="'.$this->VARS["max_word_len"].'">
      Max. Word length</font></td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top>
      <b><font size="2" face="Verdana, Arial">Censor Option</font></b><font size="1" face="Verdana, Arial"><br>
        You may have certain words censored on your Guestbook. Words you choose to
        censor will be replaced by asterisks. All subjects and messages will
        be affected. To activate the censor function, check the "yes" button below.
        To deactivate, press the "no" button. List all censored words in the field to the right.<br><br>
        Please follow the directions on the right for formatting the words correctly.
        </font><font size="2" face="Verdana, Arial"><CENTER>
        <INPUT TYPE="RADIO" NAME="censor" VALUE="1" ';
        
        if ($this->VARS["censor"] == 1) {$content .="checked";}

$content .= '> yes
        <INPUT TYPE="RADIO" NAME="censor" VALUE="0" ';
        
        if ($this->VARS["censor"] == 0) {$content .="checked";}

$content .= '> no
        </CENTER></font>
        
    </td>
    <td width=50% valign=top>
     <b><font size="2" face="Verdana, Arial">Censor Words</font></b><font size="1" face="Verdana, Arial"><br>
      Type all words you want censored in the field below. Do not use commas to separate words,
      put each word on its own line. If you type "dog", all messages containing the string "dog"
      would be censored (dog, for instance, would appear as "#@*%!").</font><br>
     <TEXTAREA NAME="badwords" ROWS=5 COLS=30 WRAP="VIRTUAL">';

if (isset($badwords) && sizeof($badwords)>0) {
  for ($i=0; $i<sizeof($badwords); $i++) {
    $content .="{$badwords[$i]['word']}\n";
  }
}
$content .= '
</TEXTAREA>
    </td>
  </tr>
  <tr bgcolor="#dedfdf" valign=top>
    <td width=50%> <b><font size="2" face="Verdana, Arial">Flood Check?</font></b> <font size="1" face="Verdana, Arial"><br>
      You may prevent your users from flooding your Guestbook with posts by activating this feature.
      By enabling floodcheck, you disallow users from posting within a given time span of their last post.
      In other words, if you set a floodcheck time span of 60 seconds, a user may not post a note within 60 seconds of his last post.</font>
      <br><br><CENTER><FONT SIZE="2" FACE="Verdana, Arial">
      <INPUT TYPE="RADIO" NAME="flood_check" VALUE="1" ';
      
      if ($this->VARS["flood_check"] == 1) {$content .="checked";}

$content .= '> FloodCheck On
      <INPUT TYPE="RADIO" NAME="flood_check" VALUE="0" ';
      
      if ($this->VARS["flood_check"] == 0) {$content .="checked";}

$content .= '> FloodCheck Off
      </FONT></CENTER>
      </td>
    <td width=50% valign=top> <font size="2" face="Verdana, Arial"><b>FloodCheck Time Span</b></font><font size="1" face="Verdana, Arial"><br>
      Set the amount of time in seconds used by FloodCheck to prevent post flooding.
      Recommended: 60. Type the number of seconds only.<br>
      <input type="text" name="flood_timeout" size="5" value="'.$this->VARS["flood_timeout"].'">
    </font></td>
  </tr>
  <tr bgcolor="#f7f7f7">
    <td width=50% valign=top>
      <b><font size="2" face="Verdana, Arial">Banned IP?</font></b><font size="1" face="Verdana, Arial"><br>
        You may ban any IP numbers from signing your Guestbook.
        Type in the complete IP number (as in 243.21.31.7),
        or use a partial IP number (as in 243.21.31.). The Guestbook will do matches from the beginning of each IP number that you enter.
        Thus, If you enter a partial IP of 243.21.31., someone attempting to sign who has an IP number of 243.21.31.5
        will not be able to sign. Similarly, if you have an IP ban on 243.21., someone signing who has an IP of 243.21.3.44
        will not be able to sign. Thus, be careful when you add IPs to your ban list and be as specific as possible.
        The IP Ban prevents anyone with matching IP number from
        signing your Guestbook.
        </font><font size="2" face="Verdana, Arial"><CENTER>
        <INPUT TYPE="RADIO" NAME="banned_ip" VALUE="1" ';
        
        if ($this->VARS["banned_ip"] == 1) {$content .="checked";}

$content .= '> yes
        <INPUT TYPE="RADIO" NAME="banned_ip" VALUE="0" ';
        
        if ($this->VARS["banned_ip"] == 0) {$content .="checked";}

$content .= '> no
        </CENTER></font>
    </td>
    <td width=50% valign=top>
     <b><font size="2" face="Verdana, Arial">IP Number Ban List:</font></b><br><font size="1" face="Verdana, Arial">
      Put each IP number on its own line.</font><br>
     <TEXTAREA NAME="banned_ips" ROWS=8 COLS=30 WRAP="VIRTUAL">';

if (isset($banned_ips) && sizeof($banned_ips)>0) {
  for ($i=0; $i<sizeof($banned_ips); $i++) {
    $content .="{$banned_ips[$i]['ban_ip']}\n";
  }
}

$content .= '
</TEXTAREA>
    </td>
  </tr>
  <tr bgcolor="#dedfdf">
    <td width=50% valign=top> <b><font size="2" face="Verdana, Arial">Show Guestbook\'s Side Box</font></b><br>
      <font size="1" face="Verdana, Arial">You may wish to display the guestbook box on main page. The default is ON.</font>
    </td>
    <td width=50% valign=top><font size="2" face="Verdana, Arial">
      <input type="radio" name="show_sidebox" value="1" ';

      if ($this->VARS["show_sidebox"] == 1) {$content .="checked";}

$content .= '>
      Show Guestbook side box <br>
      <input type="radio" name="show_sidebox" value="0" ';
      
      if ($this->VARS["show_sidebox"] == 0) {$content .="checked";}

$content .= '>
      Hide Guestbook side box </font>
    </td>
  </tr>
</table>
 <br>
  <center>
    <input type="submit" value="Submit Settings">
    <input type="reset" value="Reset">
    <input type="hidden" value="save" name="action">
    <input type="hidden" value="general" name="panel">
  </center>
</form>';

$this->content=$content;
?>