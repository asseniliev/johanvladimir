<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Smilies y AGCode</TITLE>
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
      <td height="25">&iquest; Q U E&nbsp;&nbsp;&nbsp;S O N&nbsp;&nbsp;&nbsp;L O S&nbsp;&nbsp;&nbsp;S M I L I E S ?</td>
    </tr>
    <tr>
      <td>
        <p>Los 'smilies' o 'emoticones' son peque&ntilde;as im&aacute;genes gr&aacute;ficas que se pueden usar para transmitir una emoci&oacute;n o un sentimiento. Si has usado el correo electr&oacute;nico o el chat seguramente ya estar&aacute;s familiarizado con este concepto. Algunas cadenas de caracteres est&aacute;ndar se convierten autom&aacute;ticamente en smilies. Si no, inclina la cabeza hacia la izquierda; con un poco de imaginaci&oacute;n ver&aacute;s una carita que expresa un sentimiento.</p>
        <p>Esta es la lista de los smilies aceptados actualmente: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>Qu&eacute; escribir</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Emoci&oacute;n</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Gr&aacute;fico que aparecer&aacute;</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">&iquest;Q U E &nbsp;&nbsp;&nbsp;E S &nbsp;&nbsp;&nbsp;E L  &nbsp;&nbsp;&nbsp;A G C O D E? </td>
    </tr>
    <tr>
      <td>
        <p>El AGCode (C&oacute;digo AG o Advanced Guestbook Code) es una variaci&oacute;n de las etiquetas HTML, con las que posiblemente ya est&eacute;s familiarizado. B&aacute;sicamente, te permite a&ntilde;adir a tus mensajes funciones o estilos que normalmente necesitar&iacute;an HTML. Puedes usar AGCode aunque el HTML no est&eacute; habilitado en el libro de visitas. Podr&iacute;as querer usar AGCode en lugar de HTML aunque el HTML estuviera activado en el libro de visitas, ya que necesita menos c&oacute;digo y es m&aacute;s seguro de utilizar (si usas sintaxis incorrrecta no se producir&aacute;n tantos problemas).
        <p>C&oacute;digos AG actuales:
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
          <TD><b><font color="#FFFFFF">V&iacute;nculos a URLs</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Si AGCode est&aacute; activado, no tienes que usar el c&oacute;digo [URL] para crear un hiperv&iacute;nculo. Simplemente escribe el URL completo de alguna de las dos siguientes maneras y el hiperv&iacute;nculo se crear&aacute; autom&aacute;ticamente:
            <UL>
              <LI><font color="#800000">http://www.tuURL.com </font>
              <LI><FONT color=#800000>www.tuURL.com </FONT>Como ver&aacute;s puedes usar o la direcci&oacute;n completa http:// o la forma corta para los dominios www. Si el sitio no empieza por "www", debes usar la direcci&oacute;n completa "http://". Adem&aacute;s puedes usar los prefijos https y ftp en el modo de v&iacute;nculos autom&aacute;ticos (cuando AGCode est&aacute; activado). <BR>
                <BR>
              <LI>Tambi&eacute;n puedes crear hiperv&iacute;nculos usando el c&oacute;digo [url]. Simplemente usa el formato siguiente: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.proxy2.de]</FONT>hiperv&iacute;nculo<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>El antiguo c&oacute;digo [URL] seguir&aacute; funcionando, como se explica m&aacute;s abajo. Lo &uacute;nico que tienes que hacer es encerrar el v&iacute;nculo como en el siguiente ejemplo (el c&oacute;digo AG est&aacute; en <FONT color=#ff0000>rojo</FONT>).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.proxy2.de<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>En los ejemplos precedentes, el AGCode genera autom&aacute;ticamente un hiperv&iacute;nculo al URL encerrado. Tambi&eacute;n se asegurar&aacute; de que el v&iacute;nculo se abre en una nueva ventana cuando el usuario haga clic sobre &eacute;l. Recuerda que la parte "http://" es completamente opcional. En el segundo ejemplo de arriba, el URL enlazar&aacute; el texto con cualquier URL que pongas despu&eacute;s del signo igual. Recuerda por &uacute;ltimo que NO deber&iacute;as usar comillas dentro de la etiqueta URL. </P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">V&iacute;nculos a direcciones de correo</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Para a&ntilde;adir un v&iacute;nculo a una direcci&oacute;n de correo electr&oacute;nico dentro de tu mensaje, simplemente encierra la direcci&oacute;n de correo como en el siguiente ejemplo (AGCode en <FONT color=#ff0000>rojo</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@proxy2.de<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <P>En este ejemplo, el AGCode genera autom&aacute;ticamente un hiperv&iacute;nculo a la direcci&oacute;n de correo encerrada. </P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Negrita e It&aacute;lica</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Puedes poner texto en it&aacute;lica o en negrita encerr&aacute;ndolo entre etiquetas [i] [/i] o [b] [/b] ("i" para it&aacute;lica, "b" para negrita o "bold").
            <P>
              <CENTER>
                Hola, <FONT color=#ff0000>[b]</FONT><B>Jos&eacute;</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hola, <FONT color=#ff0000>[i]</FONT><I>Mar&iacute;a</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">A&ntilde;adir Im&aacute;genes</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Para meter un gr&aacute;fico en tu mensaje simplemente encierra el URL de la imagen como se muestra en el siguiente ejemplo (AGCode en <FONT color=#ff0000>rojo</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.tuURL.com/imagen/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>En este ejemplo, el AGCode hace que aparezca autom&aacute;ticamente el gr&aacute;fico en tu mensaje. Nota: la parte "http://" del URL es OBLIGATORIA para el c&oacute;digo <FONT color=#ff0000>[img]</FONT>.</P>
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
    <td><font color=#800000>Recuerda</font><br>
      No debes usar al mismo tiempo HTML y AGCode para llevar a cabo la misma funci&oacute;n. Recuerda tambi&eacute;n que AGCode no distingue entre may&uacute;sculas y min&uacute;sculas (es decir, puedes usar <font color=#ff0000>[URL]</font> o
      <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000">Usos Incorrectos de AGCode:</font> <br>
       <font color="#ff0000">[url]</font> www.proxy2.de <font color=#ff0000>[/url]</font> - no pongas espacios entre los c&oacute;digos de corchetes y el texto al que est&aacute;s aplicando el c&oacute;digo.<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color=#ff0000>[email]</font> - los corchetes finales deben incluir una barra inclinada (<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
