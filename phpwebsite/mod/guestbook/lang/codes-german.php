<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Smilies und AGCodes</TITLE>
<META content="text/html; charset=windows-1252" http-equiv=Content-Type>
<style type="text/css">
<!--
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
-->
</style>
</HEAD>
<BODY bgColor=#FFFFFF link=#000080 text=#000000 vLink=#000080>
<CENTER>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr> 
      <td height="25"><font color="#000000"> W A S&nbsp;&nbsp;&nbsp;S I N&nbsp;D&nbsp;&nbsp;&nbsp;S 
        M I L I E S ?</font></td>
    </tr>
    <tr> 
      <td> 
        <p><font color="#000000">Smilies sind kleine Grafiken, die ein Gefühl 
          für Ihren Beitrag oder einen Teil Ihres Beitrages darstellen sollen. 
          Manches läßt sich durch die Verwendung eines Smilies besser darstellen! 
          So zeigt zum Beispiel ein zwinkernder Smilie eindeutig, das Ihre Aussage 
          als Scherz gedacht ist!</font></p>
        <p><font color="#000000"> Folgende Phrasen in Ihrem Beitrag werden automatisch 
          zu Smilies umgewandelt:</font></p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody> 
    <tr> 
      <td width="25%" bgcolor="#996699"><font color="#FFFFFF"><b>Eingabe</b></font></td>
      <td width="28%" bgcolor="#996699"><font color="#FFFFFF"><b>Bedeutung</b></font></td>
      <td width="47%" bgcolor="#996699"><font color="#FFFFFF"><b>Grafik erscheint 
        im G&auml;stebuch</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody> 
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr> 
      <td height="25"><font color="#000000">W A S &nbsp;&nbsp;&nbsp;I S T&nbsp;&nbsp;&nbsp;&nbsp;A 
        D V A N D C E D &nbsp;&nbsp;&nbsp;G U E S T B O O K &nbsp;&nbsp;&nbsp;C 
        O D E ? </font></td>
    </tr>
    <tr> 
      <td> 
        <p><font color="#000000">AG Code ist eine Sammlung von HTML-Befehlen. 
          Er erlaubt Ihnen, bestimmte Funktionen hinzuzufügen oder Ihren Beitrag 
          zu verändern, wozu Sie normalerweise HTML-Befelhe einsetzen müssten. 
          Sie können AG Code verwenden, selbst wenn die HTML-Funktion für das 
          G&auml;stebuch abgeschaltet ist. Sie sollten AG Code anstelle von HTML 
          verwenden, selbst wenn die HTML-Funktion für das G&auml;stebuch eingeschaltet 
          ist, da wenig Befehle verwendet werden und er sicherer zu benutzen ist 
          (falscher Syntax kann zu wenigeren Problemen führen). </font> 
        <p><font color="#000000">Aktuelle AG Codes: </font> 
      </td>
    </tr>
  </table>
  <TABLE border=0 cellPadding=0 cellSpacing=0 width="95%" align="center">
    <TBODY> 
    <TR> 
      <TD bgColor=#000000> 
        <TABLE border=0 cellPadding=4 cellSpacing=1 width="100%">
          <TBODY> 
          <TR bgColor=#006666> 
            <TD bgcolor="#0099CC"><b><font color="#FFFFFF">URL Hyperlinks</font></b></TD>
          </TR>
          <TR bgColor=#CCCCCC> 
            <TD bgcolor="#FFFFFF">Ist AG Code eingeschaltet, brauchen Sie nicht 
              länger den [URL] Code benutzen, um Hyperlinks zu erstellen. Schreiben 
              Sie einfach die komplette URL in irgendeiner der folgenden Weise 
              und der Link wird automatisch erstellt: 
              <UL>
                <LI><font color="#800000">http://www.IhreURL.com </font> 
                <LI><FONT color=#800000>www.IhreURL.com </FONT>Sie k&ouml;nnen 
                  entweder die komplette http:// Addresse eingeben oder sie auf 
                  die www Domain k&uuml;rzen. Wenn die Seite nicht mit "www" beginnt, 
                  müssen Sie die komplette Adresse mit "http://" angeben. Die 
                  Autolink-Funktion können Sie auch für https und ftp URL's verwenden 
                  (wenn AG Code eingeschaltet ist). <BR>
              </UL>
              <ul>
                Sie können jetzt richtige Links mit dem [url] Code erstellen. 
                Verwenden Sie folgendes Format (AG Code ist <font color="#FF0000">Rot</font>).<br>
                <br><br><center>
                  <font color="#FF0000"> [url=http://www.proxy2.de]</font>Ihre 
                  Homepage<font color="#FF0000">[/url]</font> 
                </center>
                <br>
              </ul>
              <UL>
                <LI> 
                  <P>Der alte [URL] Code arbeitet weiterhin nach der unten beschriebenen 
                    Methode. Wie im folgendem Beispiel: (AG Code ist in <font color="#FF0000">Rot</font>). 
                  <P align="center"> <font color="#FF0000">[url]</font>http://www.proxy2.de<font color="#FF0000">[/url]</font> 
                  <P>In diesem Beispiel generiert der AG Code automatisch einen 
                    Link der URL, welche zwischen dem AG Code liegt. Bei einem 
                    Klick, wird der Link in einem neuen Fenster geöffnet. Beachten 
                    Sie, das der "http://" Teil der URL nicht unbedingt notwendig 
                    ist. Im ersten Beispiel wird die URL dem Text zugeordnet, 
                    die nach dem = Zeichen steht. Verwenden Sie innerhalb der 
                    URL keine Anführungszeichen.</P>
                </LI>
              </UL>
            </TD>
          <TR bgColor=#006666> 
            <TD bgcolor="#0099CC"><b><font color="#FFFFFF">e-mail Links</font></b></TD>
          </TR>
          <TR bgColor=#CCCCCC> 
            <TD bgcolor="#FFFFFF">Um einen e-Mail Link in Ihrem Beitrag zu verwenden, 
              benutzen Sie folgendes Format (AGCode ist in <font color="#FF0000">Rot</font>). 
              <P align="center"> <font color="#FF0000">[email]</font>webmaster@proxy2.de<font color="#FF0000">[/email]</font> 
              <P>Im Beispiel wird automatisch ein Link zur e-Mail Adresse generiert, 
                welche innerhalb des AG Code steht.</P>
            </TD>
          </TR>
          <TR bgColor=#006666> 
            <TD bgcolor="#0099CC"><b><font color="#FFFFFF">Fett- und Kursivschrift</font></b></TD>
          </TR>
          <TR bgColor=#CCCCCC> 
            <TD bgcolor="#FFFFFF">Sie können Kursiv- oder Fettschrift verwenden, 
              indem Sie den Text zwischen den AG Code einfügen [b] [/b] oder [i] 
              [/i]. 
              <P align="center"> Hello, <font color="#FF0000">[b]</font><B>John</B><font color="#FF0000">[/b]</font><BR>
                <br>
                Hello, <font color="#FF0000">[i]</font><I>Maria</I><font color="#FF0000">[i]</font> 
            </TD>
          </TR>
          <TR bgColor=#006666> 
            <TD bgcolor="#0099CC"><b><font color="#FFFFFF">Einfügen von Bildern</font></b></TD>
          </TR>
          <TR bgColor=#CCCCCC> 
            <TD bgcolor="#FFFFFF">Um eine Grafik in Ihren Beitrag einzufügen, 
              können Sie die URL der Grafik innerhalb des AG Code verwenden (AG 
              Code ist <font color="#FF0000">Rot</font>): 
              <P align="center"> <font color="#FF0000">[img]</font>http://www.IhreURL.de/image/logo.gif<font color="#FF0000">[/img]</font> 
              <P>In diesem Beispiel macht der AG Code die Grafik in Ihrem Beitrag 
                automatisch sichtbar. Hinweis: der "http://" Teil der URL ist 
                NOTWENDIG für den <font color="#FF0000">[img]</font> Code.</P>
            </TD>
          </TR>
          </TBODY> 
        </TABLE>
      </TD>
    </TR>
    </TBODY> 
  </TABLE>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="4" align="center">
    <tr bgcolor="#CCCCCC"> 
      <td bgcolor="#FFFFFF"><font color=#800000><u>Hinweise:</u></font><br>
        Sie dürfen HTML und UBB Code nicht gleichzeitig verwenden, um die gleiche 
        Funktion auszuführen. Weiterhin brauchen Sie beim AG Code nicht auf Groß-/Kleinschreibung 
        zu achten (Sie können <font color="#FF0000">[URL]</font> oder <font color="#FF0000">[url]</font> 
        verwenden). <br>
        <br>
        <font color="#800000"><u>Falscher AG Code:</u></font> <br>
        <font color="#FF0000">[url]</font> www.proxy2.de <font color="#FF0000">[/url]</font> 
        schreiben Sie keine Leerzeichen zwischen dem UBB Code und dem Text, welcher 
        kodiert werden soll. <br>
        <br>
        <font color="#FF0000">[email]</font>webmaster@proxy2.de<font color="#FF0000">[email]</font> 
        - der End-Code enthält jeweils einen Schrägstrich (<font color="#FF0000">[/email]</font>) 
      </td>
    </tr>
  </table>
</CENTER>
<BR>
</BODY>
</HTML>
