<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Smilies And AGCodes</TITLE>
<META content="text/html; charset=big5" http-equiv=Content-Type>
<style type="text/css">
<!--
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
-->
</style>
</HEAD>
<BODY bgColor=#ffffff link=#000080 text=#000000 vLink=#000080>
<CENTER>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">什麼是表情符號?</td>
    </tr>
    <tr>
      <td>
		<p>表情符號是由一個小型的圖案來代表情緒。假如你曾使用過電子郵件或網路聊天，你應該
		   很熟悉某些笑臉符號。在此留言裡，你可以輸入一些特定符號，在您送出後它會自行轉換
		   成表情圖案。</p>
        <p>表情圖案對照表: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>符號</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>情緒</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>圖形顯示</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">何謂AGCode (ADVANDCED GUESTBOOK CODE)</td>
    </tr>
    <tr>
      <td>
        <p>AGCode是一種簡易的標籤來代替HTML碼，讓您能夠很方便的輸入網址、圖片、Email等等，
		   即使在HTML碼關閉時，您也可以輕易的使用AGCode。
        <p>目前的AGCode:
      </td>
    </tr>
  </table>
<TABLE border=0 cellPadding=0 cellSpacing=0 width="95%" align="center">
  <TBODY>
  <TR>
    <TD bgColor=#000000>
      <TABLE border=0 cellPadding=4 cellSpacing=1 width="100%">
        <TBODY>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">URL 網址鏈結</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>假如AGCode是開啟的，你可以使用它來代表網址鏈結
            <UL>
              <LI><font color="#800000">http://www.yourURL.com </font>
              <LI><FONT color=#800000>www.yourURL.com </FONT>注意 如果網址不是www開頭的，
			    你必須加入 http:// 來完成，如果網址是www開頭的，在您送出留言後，系統會自行
				加入http://，https和ftp也是同樣的方式。<BR>
                <BR>
              <LI>你可以使用下列的方式: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.proxy2.de]</FONT>網址<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>下列這種方式也是有效的
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.proxy2.de<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>在上述的例子裡，AGCode會自行轉換為超連結，按下此連結則會以開啟新視窗展示。</P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Email鏈結</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>要加入Email可以按照下列方式。
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@proxy2.de<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
			<P>紅色字體為AGCode</P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">粗體和斜體</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>使用粗體可輸入[b] [/b]標籤，使用斜體可輸入[i] [/i]標籤。
            <P>
              <CENTER>
                Hello, <FONT color=#ff0000>[b]</FONT><B>John</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hello, <FONT color=#ff0000>[i]</FONT><I>Maria</I><font color="#FF0000">[i]</font>
              </CENTER>
			<P>紅色字體為AGCode</P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">加入圖片</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>若要加入圖片可參考下列方式。
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.yourURL.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>紅色字體為AGCode</P>
          </TD>
        </TR>
        </TBODY>
      </TABLE>
    </TD>
  </TR>
  </TBODY>
</TABLE>
<table width="95%" border="0" cellspacing="1" cellpadding="4" align="center">
  <tr>
    <td><font color=#800000>註</font><br>
      使用AGCode時不可以使用HTML碼做結尾。另外
      AGCode是不分大小寫的 (因此你可以使用 <font color=#ff0000>[URL]</font>
      or <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000">不正確的使用方式:</font> <br>
       <font color="#ff0000">[url]</font> www.proxy2.de <font color=#ff0000>[/url]</font> - 請勿在AGCode間插入空白。<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color=#ff0000>[email]</font> - AGCode的結尾需加入斜線 "/" (<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
