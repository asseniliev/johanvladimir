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
      <td height="25"> W  A T &nbsp;&nbsp;&nbsp;Z I J N&nbsp;&nbsp;&nbsp;S M I L I E S ?</td>
    </tr>
    <tr>
      <td>
        <p>'Smilies' zijn kleine plaatjes die kunnen worden gebruikt om een gevoel of emotie aan te duiden.
		  Als je ooit gechat hebt ben je waarschijnlijk bekend met het smilie concept.
		  Bepaalde tekens worden automatisch omgezet naar smilies. 
		  Hou je hoofd maar eens schuin als je de smilies niet begrijpt, met een beetje fantasie zie je dat de tekens een gezichtje vormen.</p>
        <p> Hier is de lijst van tekens die automatisch worden omgezet naar plaatjes: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>Als je typt</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Emotie</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Plaatje dat zal verschijnen</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">W A T &nbsp;&nbsp;&nbsp;I S &nbsp;&nbsp;&nbsp;A G &nbsp;&nbsp;&nbsp;C O D E ? </td>
    </tr>
    <tr>
      <td>
        <p>AGCode is een variatie op de HTML tags die je misschien al kent van webpaginas.
		  Kort gezegd kun je er functionaliteit aan je bericht mee toevoegen
          waar je normaal HTML voor nodig hebt. Je kunt AGCode zelfs gebruiken als HTML niet actief is in het gastenboek.
		  Je kunt er voor kiezen AGCode te gebruiken in plaats van HTML, zelfs als HTML WEL actief is in het gastenboek,
		  omdat je er minder voor hoeft in te typen en je minder snel fouten kunt maken die je bericht er lelijk uit laten zien.
        <p>Huidige AGCodes:
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
          <TD><b><font color="#FFFFFF">URL linking</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Als AGCode actief is enabled, hoef je geen [URL] code te gebruiken om een link te maken.
		   Typ gewoon de url in en de link wordt actief.
            De onderstaande manieren zorgen ervoor dat je link automatisch actief wordt:
            <UL>
              <LI><font color="#800000">http://www.jouwURL.nl </font>
              <LI><FONT color=#800000>www.jouwURL.nl </FONT><br>Let op je kunt als het adres begint met "www"
			  het "http:" stuk er af laten. Begint het adres echter niet met "www" dan moet je het complete "http://" adres  intypen.
			  Je mag ook https and ftp links gebruiken als AGCode AAN staat. <BR>
                <BR>
              <LI>Je kunt ook gewoon tekst laten doorlinken door de [url] code te gebruiken. Gebruik dan de volgende manier: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.jouwsite.nl]</FONT>jouw tekst<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>De oude [URL] code zal nog steeds werken, zoals hieronder staat. Zet de link dan tussen [URL] code(AGCode is
                  in <FONT color=#ff0000>rood</FONT>).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.jouwsite.nl<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>In de bovenstaande voorbeelden, genereert de AGCode automatisch een link naar het adres tussen de [URL] codes.
				 Het zorgt er ook voor dat je link in een nieuw venster wordt geopend. Het "http://" deel van de URL mag je hier weglaten.
				 Let op je mag GEEN aanhalingstekens gebruiken in de URL tag. </P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Email Links</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Om een link naar een email adres toe te voegen in je bericht , kun je het email adres met de volgende code activeren (AGCode in <FONT color=#ff0000>rood</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>ik@mijndomein.nl<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <P>In het bovenstaande voorbeeld, zal de AGCode automatisch een link naar het email adres maken. </P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Vet en schuingedrukt</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Je kunt schuingedrukte tekst of vetgedrukte tekst gebruiken door de tekst tussen de juiste AGcodes te zetten. Gebruik hiervoorder de [i] [/i] of [b] [/b] codes.
            <P>
              <CENTER>
                Hallo, <FONT color=#ff0000>[b]</FONT><B>Johnny</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hallo, <FONT color=#ff0000>[i]</FONT><I>Anita</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Plaatjes toevoegen</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Om een plaatje toe te voegen aan je bericht, zet je de link naar het plaatje tussen de volgende code (AGCode in <FONT color=#ff0000>rood</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.jouwURL.nl/plaatjes/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>In het bovenstaande voorbeeld, zal de AGCode automatisch het plaatje zichtbaar maken in je bericht. Let op het "http://" deel van de URL is
             hier VERPLCHT voor de <FONT color=#ff0000>[img]</FONT> code.</P>
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
    <td><font color=#800000>Voetnoot</font><br>
      Gebruik geen HTML en AGCode tegelijk om hetzelfde te doen. Bij AGCode wordt er geen verschil gemaakt tussen hoofd- en kleine letters (je kunt dus <font color=#ff0000>[URL]</font>
      of <font color=#ff0000>[url]</font>) gebruiken.<br><br>
      <font color="#800000">Verkeerde manier van het gebruik van AGCode :</font> <br>
       <font color="#ff0000">[url]</font> www.mijndomein.nl <font color=#ff0000>[/url]</font> - gebruik geen spaties tussen de codes en de tekst waar de code op moet werken.<br>
        <br>
        <font color="#ff0000">[email]</font>ik@mijndomein.nl.de<font color=#ff0000>[email]</font> - de code aan het eind moet een schuin streepje naar voren bevatten(<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
