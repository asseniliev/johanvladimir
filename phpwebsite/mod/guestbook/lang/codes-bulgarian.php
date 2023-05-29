<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Усмивки и AG код</TITLE>
<META content="text/html; charset=windows-1251" http-equiv=Content-Type>
<style type="text/css">
<!--
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt}
-->
</style>
</HEAD>
<BODY bgColor=#9CB5DC link=#000080 text=#000000 vLink=#000080>
<CENTER>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25"> К А К В О &nbsp;&nbsp;&nbsp;С А &nbsp;&nbsp;&nbsp;У С М И В К И Т Е?</td>
    </tr>
    <tr>
      <td>
        <p>'Усмивките' са малки картинки които могат да бъдат използвани да придадат емоция на Вашето съобщение.Ако сте използвали e-mail или IRC, 
          Вие сте запознати вече с тези "изрази". Тук стандартния текстов низ
          автоматично се конвертира в усмивка. Завъртете главата си на една страна
          и ако не видите усмивка; използвайте малко въображение.</p>
        <p> Тук е списъка с използваните усмивки: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#9CB5DC border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#6288C6"><font color=#ffffff>Какво да напиша</font></td>
      <td bgcolor="#6288C6"><font color=#ffffff>Емоция</font></td>
      <td bgcolor="#6288C6"><font color=#ffffff>Картинка, която се появява</font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">К А К В О &nbsp;&nbsp;&nbsp; Е  &nbsp;&nbsp;&nbsp; У С Ъ В Ъ Р Ш Е Н С Т В А Н &nbsp;&nbsp;&nbsp;К О Д  &nbsp;&nbsp;&nbsp;З А  &nbsp;&nbsp;&nbsp; К Н И Г А&nbsp;&nbsp;&nbsp;   З А &nbsp;&nbsp;&nbsp; Г О С Т И ? </td>
    </tr>
    <tr>
      <td>
        <p>AG кода е вариант на HTML таговете,  с които може би вече сте запознати. Основно, позволява функционалност и стил в Вашите
          съобщения, за които се изисква HTML. Вие можете да използвате AG код дори когато
          HTML е забранен в книгата за гости. Вие можете да го използвате и когато HTML е позволен,
          а също така е и по-безопасен да употреба (некоректния синтаксис няма да създаде проблем).
        <p>Текущ усъвършенстван AG код:
      </td>
    </tr>
  </table>
<TABLE border=0 cellPadding=0 cellSpacing=0 width="95%" align="center">
  <TBODY>
  <TR>
    <TD bgColor=#000000>
      <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
        <TBODY>
        <TR bgColor=#6288C6>
          <TD><b><font color="#FFFFFF">URL хипервръзка</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Когато AG кода е активиран, вече не е необходимо да използвате
            [URL] код за да създадете хипервръзка. Просто напишете пълния URL както е посочено по-долу и хипервръзката ще бъде създадена
            автоматично:
            <UL>
              <LI><font color="#800000">http://www.yourURL.com </font>
              <LI><FONT color=#800000>www.yourURL.com </FONT>Забележете, че можете да използвате пълния  http:// или краткия www
                domain. Ако адреса не започва с "www", Вие трябва да използвате 
                пълния "http://" адрес. Също може да използвате https и  ftp URL
                префикс в autolink режима(when AGCode is ON). <BR>
                <BR>
              <LI>Може също да имате истинска хипервръзка [url] код. Просто
                използвайте следния формат: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.atlanticradio.net]</FONT>hyperlink<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>Стария [URL] код пак ще си работи, както е показано. Просто
                  в случай,  връзката е показана в следния пример(AGCode is
                  in <FONT color=#ff0000>red</FONT>).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.atlanticradio.net<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <P>В горните примери,  AG кода автоматично генерира
                  хипервръзка до  URL който е означен. Това също осигурява,
                  че връзката ще се отвори в нов прозорец при клик
                  върху нея.  </P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#6288C6>
          <TD><b><font color="#FFFFFF">Email връзки</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>За да поставите хипервръзка на email адрес във Вашето съобщение, просто оградете
             email адреса, както е показано в следния пример(AGCode is in
            <FONT color=#ff0000>red</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@atlanticradio.net<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <P> AG кода автоматимно генерира хипервръзка
              към означения e-mail. </P>
          </TD>
        </TR>
        <TR bgColor=#6288C6>
          <TD><b><font color="#FFFFFF">Bold and Italics</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Можете да създавате болд и италик текст като ограждате съответните текстови секции със следните [b] [/b] or [i] [/i] тагове.
            <P>
              <CENTER>
                Hello, <FONT color=#ff0000>[b]</FONT><B>John</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Hello, <FONT color=#ff0000>[i]</FONT><I>Maria</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#6288C6>
          <TD><b><font color="#FFFFFF">Добавяне на картинки</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>За да поставите графичен обект във Вашето съобщение, просто оградете URL на
            графичния обект, както е показано в следния пример(AGCode is in <FONT color=#ff0000>red</FONT>).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.yourURL.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>В примера AG кода прави автоматично Вашия графичен обект видим. Забележете "http://" частта ат URL е
              НЕОБХОДИМА за  <FONT color=#ff0000>[img]</FONT> code.</P>
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
      Можете да използватеи двата HTML и AGCode за една и съща функция. Само забележете
      че AG кода не е case чувствителен (можете да използвате <font color=#ff0000>[URL]</font>
      и <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000">Incorrect AGCode Usage:</font> <br>
       <font color="#ff0000">[url]</font> www.atlanticradio.net <font color=#ff0000>[/url]</font> - не оставяйте разстояния между кода в скобите
        .<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@atlanticradio.net<font color=#ff0000>[email]</font>  (<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
