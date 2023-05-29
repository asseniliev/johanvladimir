<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<HTML>

<HEAD>

<TITLE>Smilies And AGCodes</TITLE>

<META content="text/html; charset=windows-1251" http-equiv=Content-Type>

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

      <td height="25"> ЧТО ТАКОЕ&nbsp;&nbsp;&nbsp;S M I L I E S ?</td>

    </tr>

    <tr>

      <td>

        <p>'Smilies' - небольшие графические
        изображения, отображающие настроение.
        Если Вы когда-нибудь пользовались
        интернет чатами, то наверняка знакомы
        со смайликами. Smiles заменяет эти
        смайлики на графические изображения.</p>

        <p>Список доступных Smiles: </p>

      </td>

    </tr>

  </table>

  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">

    <tbody>

    <tr>

      <td bgcolor="#996699"><font color="#ffffff"><b>Смайлики</b></font></td>

      <td bgcolor="#996699"><font color="#ffffff"><b>Эмоция</b></font></td>

      <td bgcolor="#996699"><font color="#ffffff"><b>Так будет
        выглядеть</b></font></td>

    </tr>



<?php include ("./smilies.inc"); ?>



    </tbody>

  </table>

  <br>

  <table width="95%" border="0" cellspacing="1" cellpadding="0">

    <tr>

      <td height="25">ЧТО ТАКОЕ&nbsp;&nbsp;&nbsp;A D V A

        N D C E D &nbsp;&nbsp;&nbsp;G U E S T B O O K &nbsp;&nbsp;&nbsp;C O D E&nbsp;
        (AGcode) ? </td>

    </tr>

    <tr>

      <td>

        <p>AGCode разновидность HTML тэгов. Вы можете
        использовать AGCode если HTML отключено в
        Гостевой книге.<p>Доступные AGCodes:

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

          <TD>Если AGCode включен, Вам не нужно
            использовать длинный код для
            создания ссылки, просто используйте
            тэг [URL]:

            <UL>

              <LI>Для добавления URL в сообщение
                используйте следующий код (AGCode - <font color="#ff0000">красн</font>).

                <P align="center">

                    <FONT color=#ff0000>[url]</FONT>http://www.proxy2.de<FONT color=#ff0000>[/url]</FONT>

                <p>Запомните, Вы должны
                использовать полный http:// адрес или
                короткий, начинающийся с www. Если в
                адресе не присутствует &quot;www&quot;, Вы
                должны начать его с &quot;http://&quot;.

                <BR>

              <LI>Вы можете использовать [url] код и в
                таком формате: <br><br>

                <CENTER>

                  <FONT color=#ff0000>[url=http://www.proxy2.de]</FONT>hyperlink<FONT color=#ff0000>[/url]</FONT></CENTER>

            </UL>

          </TD>

        <TR bgColor=#0099CC>

          <TD><b><font color="#FFFFFF">E-mail</font></b></TD>

        </TR>

        <TR bgColor=#ffffff>

          <TD>Для добавления e-mail в сообщение
            используйте следующий код (AGCode - <font color="#ff0000">красн</font>).

            <P>

              <CENTER>

                <FONT color=#ff0000>[email]</FONT>webmaster@proxy2.de<FONT color=#ff0000>[/email]<br>
                </FONT>

              </CENTER>

          </TD>

        </TR>

        <TR bgColor=#0099CC>

          <TD><b><font color="#FFFFFF">Bold и Italics</font></b></TD>

        </TR>

        <TR bgColor=#ffffff>

          <TD>Используйте обычные [b] [/b] и [i] [/i]
            тэги.

            <P>

              <CENTER>

                Hello, <FONT color=#ff0000>[b]</FONT><B>John</B><FONT color=#ff0000>[/b]</FONT><BR><br>

                Hello, <FONT color=#ff0000>[i]</FONT><I>Maria</I><font color="#FF0000">[i]<br>
                </font>

              </CENTER>

          </TD>

        </TR>

        <TR bgColor=#0099CC>

          <TD><b><font color="#FFFFFF">Добавление
            изображений</font></b></TD>

        </TR>

        <TR bgColor=#ffffff>

          <TD>Для добавления изображения в
            сообщение используйте следующий код
            (AGCode - <font color="#ff0000">красн</font>).

            <P>

              <CENTER>

                <FONT color=#ff0000>[img]</FONT>http://www.yourURL.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>

              </CENTER>

            <P>Запомните: &quot;http://&quot; часть
            ОБЯЗАТЕЛЬНА в <FONT color=#ff0000>[img]</FONT>  коде.<br>
            </P>

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

    <td><font color="#800000">Запомните:</font><br>

      Вы не должны использовать вместе HTML и AGCode
      для одной и той же функции. В AGCode <font color=#ff0000>[URL]</font>
      = <font color=#ff0000>[url]</font>.<br><br>

      <font color="#800000">Неправильное использование AGCode:</font> <br>

       <font color="#ff0000">[url]</font> www.proxy2.de <font color=#ff0000>[/url]</font> -
      не используйте пробелы перед или после
      ссылки.<br>

        <br>

        <font color="#ff0000">[email]</font>webmaster@proxy2.de<font color=#ff0000>[email]</font> -
      не забывайте слеш в закрывающем тэге (<font color=#ff0000>[/email]</font>)

    </td>

  </tr>

</table>

</CENTER>

<BR>

</BODY>

</HTML>

