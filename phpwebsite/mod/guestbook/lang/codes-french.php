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
      <td height="25"> P O U R Q U O I&nbsp;&nbsp;&nbsp;D E S&nbsp;&nbsp;&nbsp;S M I L I E S ?</td>
    </tr>
    <tr>
      <td>
        <p>Les 'Smilies' sont de petites images utilis&eacutes;es pour traduire une &eacute;motion ou un sentiment. Si vous utilisez l'email ou le chat internet, le concept vous est d&eacute;j&agrave; familier. Certaines cha&icirc;nes standards sont automatiquement converties en smilies. Penchez la t&ecirc;te sur le c&ocirc;t&eacute; et avec un peu d'imagination vous verrez un visage exprimant un sentiment</p>
        <p> Voici la liste des smilies accept&eacute;s ici: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>Ecrire</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Sentiment</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Image affich&eacute;e</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">Q U'E S T - C E  Q U E&nbsp;&nbsp;&nbsp;I S &nbsp;&nbsp;&nbsp;L' A D V A
        N D C E D &nbsp;&nbsp;&nbsp;G U E S T B O O K &nbsp;&nbsp;&nbsp;C O D E ? </td>
    </tr>
    <tr>
      <td>
        <p>AGCode est une variante du HTML qui vous est familier. Il vous permet d'ajouter des fonctionnalit&eacute;s ou des styles &agrave; votre message. Vous pouvez utiliser AGCode m&ecirc;me si le HTML est interdit sur le livre d'or. Vous pouvez utiliser AGCode au lieu du HTML, m&ecirc;me si leHTML est autoris&eacute; sur le livre d'or, parce que le codage est plus simple et plus s&ucirc;r (une mauvaise syntaxe ne posera pas tant de probl&egrave;mes).
        <p>AGCodes courants:
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
          <TD><b><font color="#FFFFFF">Liens URL</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Si AGCode est autoris&eacute;, vous n'avez pas besoin d'utiliser le code
            [URL] pour cr&eacute;er un lien. Tapez simplement l'URL d'une des mani&egrave;res suivantes:
            <UL>
              <LI><font color="#800000">http://www.votreURL.com </font>
              <LI><FONT color=#800000>www.votreURL.com </FONT>Vous pouvez utiliser l'adresse compl&egrave;te http://... ou la raccourcir en www.adresse... Si le site ne commence pas par "www", vous devez utiliser l'adresse compl&egrave;te "http://...". Vous pouvez aussi utiliser les prefixes https et ftp si l'AGCode est autoris&eacute;. <BR>
                <BR>
              <LI>Vous pouvez faire de vrais liens avec le code [url]. Utilisez le format suivant: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.proxy2.de]</FONT>lien<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>L'ancien code [URL] fonctionne aussi, comme ci-apr&egrave;s. Encadrez le lien comme dans cet exemple(l'AGCode est en <FONT color=#ff0000>rouge</FONT>).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.proxy2.de<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>Dans ces exemples, l'AGCode g&eacute;n&egrave;re un lien vers l'URL. Il permet aussi que le lien ouvre une nouvelle fen&ecirc;tre quand on clique dessus. Notez que la partie "http://" de l'URL est optionnelle. Dans le premier exemple, le texte sera un lien vers l'URL sp&eacute;cifi&eacute;e apr&egrave;s le signe &eacute;gal. Attention de ne PAS utiliser de guillemets dans l'URL. </P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Liens de messagerie (mailto)</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Pour ins&eacute;rer un lien de messagerie dans votre message, utilisez l'exemple suivant (l'AGCode est en
            <FONT color=#ff0000>rouge</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@proxy2.de<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <P>Dans cet exemple, l'AGCode g&eacute;n&egrave;re automatiquement un lien de messagerie vers l'adresse email indiqu&eacute;e. </P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Gras et Italique</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Vous pouvez &eacute;crire en gras ou en italique en utilisant les balises [b] [/b] ou [i] [/i].
            <P>
              <CENTER>
                Hello, <FONT color=#ff0000>[b]</FONT><B>Marc</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hello, <FONT color=#ff0000>[i]</FONT><I>Sophie</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Ajouter des Images</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>pour ajouter une image &agrave; votre message, ins&eacute;rez l'URL de l'image comme dans l'exemple suivant(l'AGCode est en <FONT color=#ff0000>rouge</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.votreURL.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>Dans cet exemple, l'AGCode fait automatiquement appara&icirc;tre votre image dans le message. Attention: la partie "http://" de l'URL est INDISPENSABLE pour la balise <FONT color=#ff0000>[img]</FONT>.</P>
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
    <td><font color=#800000>Important</font><br>
      Vous ne devez pas combiner HTML et AGCode pour la m&ecirc;me fonction. Notez aussi que l'AGCode n'est pas sensible &agrave; la casse (donc, vous pouvez utiliser <font color=#ff0000>[URL]</font>
      ou <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000">Usage d'AGCode Incorrect:</font> <br>
       <font color="#ff0000">[url]</font> www.proxy2.de <font color=#ff0000>[/url]</font> - Ne laissez pas d'espaces entre le texte et le code que vous lui appliquez.<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color=#ff0000>[email]</font> - la balise de fin doit avoir un anti-slash(<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
