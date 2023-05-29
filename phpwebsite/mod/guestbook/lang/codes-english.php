<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Smilies And AGCodes</TITLE>
<META content="text/html; charset=windows-1252" http-equiv=Content-Type>
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
      <td height="25"> W H A T &nbsp;&nbsp;&nbsp;A R E&nbsp;&nbsp;&nbsp;S M I L I E S ?</td>
    </tr>
    <tr>
      <td>
        <p>'Smilies' are small graphical images that can be used to convey an
          emotion or feeling. If you have used email or internet chat, you are
          likely familiar with the smilie concept. Certain standard strings are
          automatically converted into smilies. Try twisting your head on one
          side if you do not get smilies; using a bit of imagination should reveal
          a face of some description.</p>
        <p> Here's the list of currently accepted smilies: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>What to Type</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Emotion</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Graphic That Will Appear</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">W H A T &nbsp;&nbsp;&nbsp;I S &nbsp;&nbsp;&nbsp;A D V A
        N D C E D &nbsp;&nbsp;&nbsp;G U E S T B O O K &nbsp;&nbsp;&nbsp;C O D E ? </td>
    </tr>
    <tr>
      <td>
        <p>AGCode is a variation on the HTML tags you may already be familiar
          with. Basically, it allows you to add functionality or style to your
          message that would normally require HTML. You can use AGCode even if
          HTML is not enabled for the guestbook. You may want to use
          AGCode as opposed to HTML, even if HTML is enabled for the guestbook,
          because there is less coding required and it is safer to use (incorrect
          coding syntax will not lead to as many problems).
        <p>Current AGCodes:
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
          <TD><b><font color="#FFFFFF">URL Hyperlinking</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>If AGCode is enabled, you no longer need to use the
            [URL] code to create a hyperlink. Simply type the complete URL in
            either of the following manners and the hyperlink will be created
            automatically:
            <UL>
              <LI><font color="#800000">http://www.yourURL.com </font>
              <LI><FONT color=#800000>www.yourURL.com </FONT>Notice that you can
                either use the complete http:// address or shorten it to the www
                domain. If the site does not begin with "www", you must use the
                complete "http://" address. Also, you may use https and ftp URL
                prefixes in auto-link mode (when AGCode is ON). <BR>
                <BR>
              <LI>You can also have true hyperlinks using the [url] code. Just
                use the following format: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.proxy2.de]</FONT>hyperlink<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>The old [URL] code will still work, as detailed below. Just
                  encase the link as shown in the following example (AGCode is
                  in <FONT color=#ff0000>red</FONT>).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.proxy2.de<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>In the examples above, the AGCode automatically generates
                  a hyperlink to the URL that is encased. It will also ensure
                  that the link is opened in a new window when the user clicks
                  on it. Note that the "http://" part of the URL is completely
                  optional. In the second example above, the URL will hypelink
                  the text to whatever URL you provide after the equal sign. Also
                  note that you should NOT use quotation marks inside the URL
                  tag. </P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Email Links</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>To add a hyperlinked email address within your message, just encase
            the email address as shown in the following example (AGCode is in
            <FONT color=#ff0000>red</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@proxy2.de<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <P>In the example above, the AGCode automatically generates a hyperlink
              to the email address that is encased. </P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Bold and Italics</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>You can make italicized text or make text bold by encasing the applicable
            sections of your text with either the [b] [/b] or [i] [/i] tags.
            <P>
              <CENTER>
                Hello, <FONT color=#ff0000>[b]</FONT><B>John</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hello, <FONT color=#ff0000>[i]</FONT><I>Maria</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Adding Images</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>To add a graphic within your message, just encase the URL of the
            graphic image as shown in the following example (AGCode is in <FONT color=#ff0000>red</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.yourURL.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>In the example above, the AGCode automatically makes the graphic
              visible in your message. Note: the "http://" part of the URL is
              REQUIRED for the <FONT color=#ff0000>[img]</FONT> code.</P>
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
    <td><font color=#800000>Of Note</font><br>
      You must not use both HTML and AGCode to do the same function. Also note
      that the AGCode is not case-sensitive (thus, you could use <font color=#ff0000>[URL]</font>
      or <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000">Incorrect AGCode Usage:</font> <br>
       <font color="#ff0000">[url]</font> www.proxy2.de <font color=#ff0000>[/url]</font> - don't put spaces between the bracketed code and
        the text you are applying the code to.<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color=#ff0000>[email]</font> - the end brackets must include a forward slash (<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
