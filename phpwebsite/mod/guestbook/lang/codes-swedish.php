<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//SWE">
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
        <p>'Smilies' är små bilder du kan använda för att visa känslor. Om du vrider huvudet
	i sida så kan du vanligen se vad de föreställer.</p>
        <p> Här är en lista på allmännt accepterade smilies: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>Så här skrivs det</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Känsla</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Vad som visar sig grafiskt.</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">V A D &nbsp;&nbsp;&nbsp;Ä R &nbsp;&nbsp;&nbsp;A V A
        N C E R A D &nbsp;&nbsp;&nbsp;G Ä S T B  O K &nbsp;&nbsp;&nbsp;C O D E ? </td>
    </tr>
    <tr>
      <td>
        <p>AGCode är en variation på vanlig enkel HTML kod. Man använder dessa koder
	för att formatera sitt meddelande. Du kan använda AGCode även om
          HTML inte får användas i gästboken. Du kan också välja att använda AGCode istället
	för HTML, den innehåller färre kommando att hålla reda på och är säkrare.
        <p>Vanliga AGCodes:
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
          <TD>Om AGCode är tillåten så behöver du inte längre använda 
            [URL] code för att skapa en länk. Bara skriv in hela URL:en på något av
		följande sätt så kommer länken att skapas automatiskt.:
            <UL>
              <LI><font color="#800000">http://www.yourURL.com </font>
              <LI><FONT color=#800000>www.yourURL.com </FONT>Notera att du antingen kan
                använda den kompletta http:// address eller den kortare varianten www
                domain. Om hemsida inte börjar med "www", så måste du använda den längre varianten.
		 Du kan också använda https och ftp URL
                prefix i auto-link läge (när AGCode is på). <BR>
                <BR>
              <LI>Du kan också ha äkta [url] länkar genom att skriva code. Andvänd
		bara följande format: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.proxy2.de]</FONT>hyperlink<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>Den gamla [URL] coden kommer fortfarande att fungera, som beskrivs nedan. Men 
                  utifall länken visas som i exemplet(AGCode är
                  i <FONT color=#ff0000>rött</FONT>).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.proxy2.de<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>I exemplet ovan, kommer AGCoden automatiskt att generera
                  en länkt till URL:en. Den kommer också att göra så att länken öppnas i ett nytt fönster när 
		man klicka på den. Notera att "http://" delen av URL:en är frivillig. 
		I det andra exemplet ovan, kommer URL:en länka texten till den URL du skriver in efter likhetstecknet.
                  Notera också att du inte skall använda citationstecken inne i en länk. 
		</P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Email Links</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>För att lägga till en länkad e-mail adress inuti ett meddelande, skriva bara in den som i exemplet nedan. 	(AGCode is in
            <FONT color=#ff0000>red</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@proxy2.de<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <P>I exemplet ovan kommer AGCoden automatiskt att generera en länk till e-mail adressen som finns där.
              </P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Fet ock kursiv stil</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Om du vill ha fet- eller kursiv stil så skriver du bara in den mellan taggarna [b] [/b] [i] [/i] .
            <P>
              <CENTER>
                Hello, <FONT color=#ff0000>[b]</FONT><B>John</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hello, <FONT color=#ff0000>[i]</FONT><I>Maria</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Lägga till bilder</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>För att lägga in en bild i ett meddelande så skriver du bara in adressen och namnet på bilden
		som det är beskrivet här nedan. (AGCode is in <FONT color=#ff0000>red</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.yourURL.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>I exemplet ovan så kommer AGCcoden automatiskt visa bilden i ditt meddelande.Notera: "http://" delen av URL:en 		måste skrivas in.<FONT color=#ff0000>[img]</FONT> </P>
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
    <td><font color=#800000>Tillägg</font><br>
      Du skall inte använda både HTML och AGCode för att göra samma sak. Notera också
      att AGCoden inte är beroende av små och stora bokstäver (så du kan använda <font color=#ff0000>[URL]</font>
      or <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000">Felaktig AGCode användning:</font> <br>
       <font color="#ff0000">[url]</font> www.proxy2.de <font color=#ff0000>[/url]</font> - lägg inte in mellanslag mellan 
	code inom paranteser eller texten som du formaterar.<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color=#ff0000>[email]</font> - den avslutande parantesen har alltid ett framåt lutande snedstreck.(<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
