<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Emotikony a AG kódy</TITLE>
<META content="text/html; charset=iso-8859-2" http-equiv=Content-Type>
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
      <td height="25">Č O &nbsp;&nbsp;&nbsp; S Ú &nbsp;&nbsp;&nbsp; E M O T I K O N Y ?</td>
    </tr>
    <tr>
      <td>
        <p>Emotikony (alebo aj smejlíky) su malé grafické obrázky, ktoré môžu byť
		použité na vyjadrenie emócii alebo nálady. Ak používate e-mailové služby
		alebo internetové chaty či diskusné fóra, určite ste sa už s týmto
		stretli. Konkrétne postupnosti znakov sú automaticky zmenené na príslušné
		obrázky. Pokiaľ teraz nechápavo krútite hlavou, možno Vám k lepšej
		predstavivosti pomôže nasledovná tabuľka.</p>
		 
        <p> Tu je zoznam používaných emotikonov (smejlíkov): </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699"><font color=#ffffff><b>Čo napísať</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Emócia</b></font></td>
      <td bgcolor="#996699"><font color=#ffffff><b>Zobrazený obrázok</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25">Č O &nbsp;&nbsp;&nbsp;J E &nbsp;&nbsp;&nbsp;A G &nbsp;&nbsp;&nbsp;K Ó D ?</td>
    </tr>
    <tr>
      <td>
		<p>AG kód je variácia HTML tagov, ktoré už pravdepodobne poznáte. Hlavným
		účelom AG kódu je možnosť pridania funkčnosti alebo štýlu do správy, ktorá
		by normálne potrebovala HTML. AG kód sa môže totiž používať aj keď je HTML
		v Knihe návštev vypnuté. Výhody AG kódu však vynikajú aj keď je HTML
		zapnuté. Je to najmä preto, lebo je jednoduchší na použitie
		a viac bezpečný (nesprávna syntax nenarobí toľko problémov ako
		v prípade HTML).

        <p>Používané AG kódy:</p>
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
          <TD><b><font color="#FFFFFF">URL Hyperlinky</font></b></TD>
        </TR>
        <TR >
          <TD>Pokiaľ je AG kód zapnutý, nepotrebujete viac používať [URL] tag 
			na vytváranie hyperlinkových odkazov. Jednoducho napíšte kompletnú
			internetovú adresu (URL) v jednom z nasledovných tvarov
			a hyperlinkový odkaz bude automaticky vytvorený:
            <UL>
              <LI><font color="#ff0000">http://www.vašaURL.com </font>
              <LI><FONT color="#ff0000">www.vašaURL.com </FONT>Pamätajte, že
				môžete používať kompletný http:// typ adresy, ale i skrátenú
				verziu www domény. Pokiaľ však stránka nezačína s "www",
				musíte použiť kompletnú "http://" adresu. Samozrejme, môžete
				používať aj https a ftp adresy v automatickom vytváracom móde
				(tj. keď je AG kód zapnutý). <BR>
                <BR>
              <LI>Je tu tiež možnosť vytvoriť pravé hyperlinkové odkazy
				s použitím [url] tagu. Pre vytvorenie použite nasledovný formát:
				<br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.platon.sk]</FONT>hyperlink<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>Starý [URL] tag funguje tiež. Jednoducho uzavrite odkaz medzi
					tagy tak, ako je to ukázané v nasledujúcom príklade (AG kód
					je vyznačený <FONT color=#ff0000>červenou</FONT> farbou).
                <P>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.platon.sk<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
				<P>Vo vyššieuvedenom príklade AG kód automaticky vygeneruje
				hyperlink na internetovú adresu (URL), ktorá je uzavretá medzi
				tagmi. Taktiež zabezpečí, že stránka sa bude po kliknutí
				otvárať v novom okne. Časť http:// z URL je voliteľná.
				V príklade predtým, bude hyperlink vytvorený na URL, ktorá
				sa špecifikuje za znakom '='. Dôležité je, že NIE je možné
				používať apostrofy a úvodzovky v samotnom URL tagu. </P>
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">E-mailové linky</font></b></TD>
        </TR>
        <TR >
          <TD>Pokiaľ chcete pridať e-mailový odkaz do Vašej správy, jednoducho
			uzavrite e-mailovú adresu tak, ako je to ukázané v nasledujúcom
			príklade (AG kód je vyznačený <FONT color=#ff0000>červenou</FONT>
			farbou).
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>webmaster@platon.sk<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
			<P>V príklade vyššie AG kód automaticky vygeneruje hyperlikový odkaz
			na e-mailovú adresu, ktorá je uzavretá medzi tagmi. </P>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Zvýraznenie a Kurzíva</font></b></TD>
        </TR>
        <TR >
          <TD>Svoj text môžete zvýrazniť hrubým písmom alebo kurzívou, pokiaľ
			ohraničíte príslušné slová alebo časti textu s tagmi [b] [/b]
			alebo [i] [/i]. Použitie je zrejmé z nasledovného príkladu.
            <P>
              <CENTER>
                Vitaj, <FONT color=#ff0000>[b]</FONT><B>Janko</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Vitaj, <FONT color=#ff0000>[i]</FONT><I>Mária</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Použitie obrázkov</font></b></TD>
        </TR>
        <TR >
          <TD>Do správy sa dajú pridať aj obrázky. Pokiaľ tak chcete urobiť,
			opäť len jednoducho uzavrite internetovú adresu (URL) obrázku tak,
			ako je to ukázané v nasledujúcom príklade (AG kód je vyznačený
			<FONT color=#ff0000>červenou</FONT> farbou).
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.vašaURL.com/images/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>Vo vyššieuvedenom príklade AG kód automaticky pridá obrázok do
			Vašej správy. Pamätajte však, že pre <FONT color=#ff0000>[img]</FONT>
			tag je "http://" časť URL povinná.</P>
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
    <td><font color="#ffff99">Poznámky:</font><br>
		Nemusíte používať HTML aj AG kód na dosiahnutie rovnakej funkčnosti.
		Tiež berte na vedomie, že pri AG kóde nezáleží na veľkých a malých
		písmenách a teda možete používať <font color=#ff0000>[URL]</font>
		aj <font color=#ff0000>[url]</font> tag. Je len na Vás, ktorý si
		vyberiete.<br><br>
      <font color="#ffff99">Nesprávne použitie AG kódu:</font><br>
       <font color="#ff0000">[url]</font> www.platon.sk <font color=#ff0000>[/url]</font><br>
		Nedávajte medzery medzi tagy a samotný text, na ktorý chcete tag aplikovať.<br>
        <br>
        <font color="#ff0000">[email]</font>webmaster@platon.sk<font color=#ff0000>[email]</font> <br>
		Koncový tag musí obsahovať znak '/' (čiže správne je <font color=#ff0000>[/email]</font>).
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
