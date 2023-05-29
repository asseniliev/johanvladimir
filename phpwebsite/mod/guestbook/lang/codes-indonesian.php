<!--//
Indonesian Translation
by
Oeban Poetih
oeban@poetih.com
www.poetih.com
//-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Smilies Dan AGCodes</TITLE>
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
      <td height="25"><b>S M I L I E S ?&nbsp;&nbsp;&nbsp;A P A K A H&nbsp;&nbsp;&nbsp;I T U ?</b></td>
    </tr>
    <tr>
      <td>
        <p>'Smilies' adalah suatu gambar/simbol untuk mewakili emosi/perasaan seseorang. Jika Anda sering menggunakan e-mail atau chatting, biasanya Anda akan memahami konsep smilies ini. Beberapa karakter string standard secara otomatis akan diubah menjadi smilies. Cobalah miringkan kepala Anda ke kiri untuk melihat smilies; dengan sedikit imajinasi Anda akan melihat gambar wajah yang mewakili perasaan/emosi tertentu.</p>
        <p>Dibawah ini adalah daftar smilies yang saat ini tersedia: </p>
      </td>
    </tr>
  </table>
  <table bgcolor=#f7f7f7 border=0 width="95%" cellspacing="1" cellpadding="4">
    <tbody>
    <tr>
      <td bgcolor="#996699" align="center"><font color=#ffffff><b>SIMBOL</b></font></td>
      <td bgcolor="#996699" align="center"><font color=#ffffff><b>EMOSI</b></font></td>
      <td bgcolor="#996699" align="center"><font color=#ffffff><b>GAMBAR YANG AKAN TAMPIL</b></font></td>
    </tr>

<?php include ("./smilies.inc"); ?>

    </tbody>
  </table>
  <br>
  <table width="95%" border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td height="25"><b>A D V A N C E D &nbsp;&nbsp;&nbsp;G U E S T B O O K &nbsp;&nbsp;&nbsp;C O D E ? &nbsp;&nbsp;&nbsp;A P A K A H &nbsp;&nbsp;&nbsp; I T U ?</b></td>
    </tr>
    <tr>
      <td>
        <p>AGCode adalah alternatif untuk tag HTML yang mungkin sudah sangat familiar bagi Anda. Pada dasarnya, AGCode memungkinkan Anda untuk menambahkan suatu fungsi atau <i>style</i> pada pesan yang Anda kirimkan yang biasanya menggunakan tag HTML. Anda dapat menggunakan AGCode bahkan jika tag HTML dinon-aktifkan (<i>disabled</i>) pada setting buku tamu ini.<!--// Anda dapat menggunakan AGCode sebagai alternatif walaupun tag HTML sedang aktif pada setting buku tamu ini, sebab AGCode hanya menggunakan sedikit kode dan lebih aman untuk digunakan (pengkodean yang tidak sesuai tidak akan menyebabkan masalah yang berarti).//--></p>
        <p>Contoh AGCode:
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
          <TD><b><font color="#FFFFFF">Membuat <i>hyperlink</i> untuk URL</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Jika AGCode <i>enabled</i>, Anda tidak perlu lagi menggunakan tag <FONT color=#ff0000>[URL]</FONT> untuk membuat suatu <i>hyperlink</i>. Cukup ketikkan URL dengan lengkap maka secara otomatis akan diubah menjadi <i>hyperlink</i>:
            <UL>
              <LI><font color="#800000">http://www.URLAnda.com</font> atau <FONT color=#800000>www.URLAnda.com</FONT><br>
                  Anda tidak diharuskan menulis URL lengkap seperti "http://www.URLAnda.com" cukup dengan "www.URLAnda.com". Tetapi jika URL Anda tidak diawali dengan "www" maka Anda harus menuliskannya dengan diawali oleh "http://". Selain itu, Anda diperbolehkan menggunakan "https" atau URL ftp dalam mode auto-link (jika AGCode <i>enabled</i>).<BR><BR>
              <LI>Untuk menuliskan URL, gunakan format dibawah ini: <br><br>
                <CENTER>
                  <FONT color=#ff0000>[url=http://www.URLAnda.com]</FONT>teks hyperlink<FONT color=#ff0000>[/url]</FONT>
                </CENTER><br><br>
              <LI>
                <P>Cara penulisan <FONT color=#ff0000>[URL]</FONT> yang lama masih dapat dipergunakan, contoh:<br><br>
                  <CENTER>
                    <FONT color=#ff0000>[url]</FONT>http://www.URLAnda.com<FONT color=#ff0000>[/url]</FONT>
                  </CENTER>
                <!--//<P>
                Pada contoh diatas, AGCode secara otomatis akan membuat suatu <i>hyperlink</i> untuk URL yang diapit olehnya. Juga akan membuka <i>window</i> baru jika <i>link</i> tersebut diklik.
                Pada contoh kedua, kode URL akan membuat <i>link</i> dari suatu teks yang diapit oleh tag [url]..[/url]. Penulisan tag URL jangan diapit oleh tanda kutip ganda ("").<br>Penulisan "http://" pada contoh tersebut bersifat <i>optional</i>.
                </P>//-->
              </LI>
            </UL>
          </TD>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Membuat <i>hyperlink</i> untuk alamat Email</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Untuk membuat <i>hyperlink</i> dari suatu alamat email dalam komentar Anda, cukup tuliskan alamat email Anda diantara tag <b>[email]..[/email]</b>.
            <P>
              <CENTER>
                <FONT color=#ff0000>[email]</FONT>namaAnda@domainAnda.com<FONT color=#ff0000>[/email]</FONT>
              </CENTER>
            <!--//<P>Pada contoh diatas, AGCode secara otomatis akan membuat <i>hyperlink</i> dari alamat email yang Anda tuliskan diantara tag [email]..[/email]. </P>//-->
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Membuat huruf tebal (<i>Bold</i>) dan miring (<i>Italics</i>)</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Untuk membuat huruf tebal atau miring teks Anda, gunakan tag <b>[b] [/b]</b> atau <b>[i] [/i]</b>.
            <P>
              <CENTER>
                Ini <FONT color=#ff0000>[b]</FONT><B>tebal</B><FONT color=#ff0000>[/b]</FONT><BR><br>
                Nah, yang ini <FONT color=#ff0000>[i]</FONT><I>miring</I><font color="#FF0000">[i]</font>
              </CENTER>
          </TD>
        </TR>
        <TR bgColor=#0099CC>
          <TD><b><font color="#FFFFFF">Menampilkan Gambar/Image</font></b></TD>
        </TR>
        <TR bgColor=#ffffff>
          <TD>Gunakan tag <b>[img] [/img]</b> untuk menampilkan gambar pada pesan/komentar Anda.
            <P>
              <CENTER>
                <FONT color=#ff0000>[img]</FONT>http://www.URLAnda.com/image/logo.gif<FONT color=#ff0000>[/img]</FONT>
              </CENTER>
            <P>Pada contoh diatas, AGCode secara otomatis akan menampilkan gambar dari URL Anda. Catatan: Pada tag <FONT color=#ff0000>[img]</FONT> ini Anda <b>harus</b> menuliskan alamat URL lengkap dengan "http://".</P>
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
    <td><font color=#800000><b>Catatan:</b></font><br>
      Anda tidak diperbolehkan menggunakan tag HTML dan AGCode secara bersamaan. Perlu diketahui bahwa AGCode tidak membedakan penulisan huruf BESAR atau kecil (Anda dapat menuliskan <font color=#ff0000>[URL]</font> atau <font color=#ff0000>[url]</font>).<br><br>
      <font color="#800000"><b>Penulisan AGCode yang salah</b>:</font> <br>
       <br><font color="#ff0000">[url]</font> www.URLAnda.com <font color=#ff0000>[/url]</font> - jangan ada spasi diantara tag <font color="ff0000">[url] [/url]</font>.<br>
       <br><font color="#ff0000">[email]</font>webmaster@proxy2.de<font color="#ff0000">[email]</font> - penulisan tag tidak diakhiri dengan tag penutup (<font color=#ff0000>[/email]</font>)
    </td>
  </tr>
</table>
</CENTER>
<BR>
</BODY>
</HTML>
