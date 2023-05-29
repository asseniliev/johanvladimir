<? 
/*-----------------------------------------------------
COPYRIGHT NOTICE
Copyright (c) 2001 - 2008, Ketut Aryadana
All Rights Reserved

Script name : ArdGuest
Version : 1.8
Website : http://www.promosi-web.com/script/guestbook/
Email : aryasmail@yahoo.com.au
Download URL : 
   - http://www.promosi-web.com/script/guestbook/download/
   - http://www.9sites.net/download/ardguest_1.8.zip

This code is provided As Is with no warranty expressed or implied. 
I am not liable for anything that results from your use of this code.
------------------------------------------------------*/

//--Change the following variables

//Title of your guestbook
  $title = "Книга за гости на www.JohanVladimir.com";
//Change "admin" with your own password. It's required when you delete an entry
  $admin_password = "ioan";
//Enter your email here
  $admin_email = "johanvladimir@mail.bg";
//Your website URL
  $home = "http://www.johanvladimir.com";
//Send you an email when someone add your guestbook, YES or NO
  $notify = "YES";
//Your Operating System
//For Windows/NT user : WIN
//For Linux/Unix user : UNIX
  $os = "UNIX";
//Maximum entry per page when you view your guestbook
  $max_entry_per_page = 10;
//Name of file used to store your entry, change it if necessary
  $data_file = "guestbook.dat";
//Maximum entry stored in data file
  $max_record_in_data_file = 0;
//Maximum entries allowed per session, to prevent multiple entries made by one visitor
  $max_entry_per_session = 2;
//Enable Image verification code, set the value to NO if your web server doesn't support GD lib
  $imgcode = "YES";
//Color & font setting
  $background = "#555555";
  $table_top = "#D3B68E";
  $table_content_1a = "#EDEEE8";
  $table_content_1b = "#E4E4E4";
  $table_content_2a = "#FFF8F0";
  $table_content_2b = "#FFEFDF";
  $table_bottom = "#D3B68E";
  $table_border = "#000000";
  $title_color = "#FFFF00";
  $link = "#0000FF";
  $visited_link = "#0000FF";
  $active_link = "#FF0000";
  $font_face = "verdana";
  $message_font_face = "arial";
  $message_font_size = "2";

//-- Don't change bellow this line unless you know what you're doing

$do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";
$showdel = isset($_REQUEST['showdel']) ? trim($_REQUEST['showdel']) : "";
$id = isset($_GET['id']) ? trim($_GET['id']) : "";
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$self = $_SERVER['PHP_SELF'];

if (!file_exists($data_file)) {
    echo "<b>Error !!</b> Can't find data file : $data_file.<br>";
	exit;
} else {
	if ($max_record_in_data_file != "0") {
		$f = file($data_file);
		rsort($f);
		$j = count($f);
		if ($j > $max_record_in_data_file) {
			$rf = fopen($data_file,"w");
            if (strtoupper($os) == "UNIX") {
	           if (flock($rf,LOCK_EX)) {
                  for ($i=0; $i<$max_record_in_data_file; $i++) {
                      fwrite($rf,$f[$i]);	     
			      }
                  flock($rf,LOCK_UN);
	           }
            } else {
               for ($i=0; $i<$max_record_in_data_file; $i++) {
                  fwrite($rf,$f[$i]);	     
	           }
	        }
			fclose($rf);
		}
	}
}
session_start();
$newline = (strtoupper($os) == "WIN") ? "\r\n" : "\n";
switch ($do) {
case "":
   $record = file($data_file);
   rsort($record);
   $jmlrec = count($record);

include '../head.html';
include '../body.html';
include '../gbheader.html';
?>
   <!--DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
   <html>
   <head>
   <title><?=$title?></title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   </head>
   <body bgcolor="<?=$background?>" link="<?=$link?>" vlink="<?=$visited_link?>" alink="<?=$active_link?>" topmargin="0" marginheight="0" style="font-family:<?=$font_face?>">
   <div align="center">
   <font size="6" color="<?=$title_color?>"><b><?=$title?></b></font><br>
   <font size="2" color="<?=$title_color?>">(:: <b><a href="<?=$home?>"><font color="<?=$title_color?>">Home</font></a></b> ::)</font><br><br>
   <table width="600" cellpadding="0" cellspacing="1" border="0"-->

   <table width="95%" cellpadding="0" cellspacing="1" border="0">
   <tr bgcolor="<?=$table_border?>">
   <td>
      <table width="100%" cellpadding="4" cellspacing="1" border="0">
      <tr>
	    <td bgcolor="<?=$table_top?>" colspan="3" width="100%">
           <!--font size="2" color="#ffffff"> <a href="<?="$self?do=add_form&page=$page"?>"><script>prt("signguestbook")</script></a>-->
		   <script>prt("guestbookdisabled")</script>
        </td>
	  </tr>
<?
      $jml_page = ceil($jmlrec/$max_entry_per_page);
	  $nomrec = $page * $max_entry_per_page - $max_entry_per_page;
	  $no = $page*$max_entry_per_page-$max_entry_per_page;
      //$no = ($jmlrec - $page * $max_entry_per_page) + $max_entry_per_page + 1;
      if ($jmlrec == 0) {
		  echo '<tr><td colspan="3" bgcolor="#FFE1E1" align="center"><font size="3"><script> prt(\"noentriesyet\") </script></font></td></tr>';
	  }
		$w = 0; //--Color
        for ($i=0; $i<$max_entry_per_page; $i++) {
			$nomrec++;
			$no++;
		    //$no--;
		    $recno = $nomrec-1;
		    if (isset($record[$recno])) {
		       $row = explode("|~|",$record[$recno]);
			   if ($w==0) { 
				   $warna = $table_content_1a;
				   $warna2 = $table_content_1b;
				   $w=1;
			   } else { 
				   $warna = $table_content_2a;
				   $warna2 = $table_content_2b;
				   $w=0;
			   }
			   echo "<tr>
			           <td bgcolor=\"$warna2\" align=\"center\" valign=\"top\" width=\"15\">
					     <font size=\"2\">$no</font>
					   </td>
					   <td bgcolor=\"$warna\" width=\"570\">
					   <table border=\"0\" width=\"100%\">
					   <tr>
					    <td>
					     <font size=\"1\">$row[2]</font><br>
						 <font size=\"2\"><b>$row[3]</b></font>
						</td>
					";
               echo "<td align=\"right\" valign=\"top\">";
						if (trim($row[4]) != "") {
							echo "<a href=\"mailto:$row[4]\"><img src=\"/images/email.gif\" border=\"0\" alt=\"$row[4]\"></a>";
						}
			            if (trim($row[6]) != "" && trim($row[6]) != "http://") {
                           if (ereg("^http://", trim($row[6]))) echo " <a href=\"$row[6]\" target=\"_blank\"><img src=\"/images/homepage.gif\" border=\"0\" alt=\"$row[6]\"></a>";
                           else echo " <a href=\"http://$row[6]\" target=\"_blank\"><img src=\"/images/homepage.gif\" border=\"0\" alt=\"$row[6]\"></a>";
			            }
			   echo '</td></tr></table>';
			   echo "<br><table border=\"0\" width=\"100%\">
			         <tr><td width=\"5\">&nbsp;</td><td>
			         <font size=\"2\" face=\"$message_font_face\" size=\"$message_font_size\">".stripslashes($row[5])."</font>
					 </td></tr>
                     </table>
			        ";
			   echo '</td>';
			   echo "<td valign=\"top\" bgcolor=\"$warna2\" align=\"center\" width=\"15\">";
			   if ( $showdel == "1" ) echo "<a href=\"$self?do=del&id=$row[1]&page=$page\">
					 <img src=\"/images/del.gif\" alt=\"Delete entry # $no\" border=0 align=\"center\"></a>";
				echo "</td> </tr>";
			} //--end if		
        } //--end for
      echo "<tr><td colspan=\"3\" bgcolor=\"$table_bottom\" align=\"center\" width=\"600\"><font size=\"2\">";	  	  
      if ($jml_page > 1) {	   
		  if ($page != 1) echo "[<a href=\"$self?page=1\"><script>prt('Top')</script></a>] "; else echo '<script>prt("Top")</script> ';
	      echo '<script> prt("PageN") </script>';
          if ($jml_page > 10) {
	 	      if ($page < 5) {
		          $start = 1;
			      $stop = 10;
		      } elseif ($jml_page - $page < 5) {
		          $start = $jml_page - 9;
			      $stop = $jml_page;
		      } else {
		          $start = $page-4;
			      $stop = $page+5;
			  }
		      if ($start != 1) echo '... ';
              for ($p=$start; $p<=$stop; $p++) {
				  if ($p == $page) echo "<font color=\"$active_link\"><b>$p</b></font>&nbsp;&nbsp;";
				  else echo "<a href=\"$self?page=$p\">$p</a>&nbsp;&nbsp;";
              }
		      if ($stop != $jml_page) echo '... ';		 		 
		      echo "<script> prt('of') </script> $jml_page ";
          } else {
              for ($p=1; $p<=$jml_page; $p++) {
	              if ($p == $page) echo "<font color=\"$active_link\"><b>$p</b></font>&nbsp;&nbsp;";
			      else echo "<a href=\"$self?page=$p\">$p</a>&nbsp;&nbsp;";
              }
	      }
          if ($page != $jml_page) echo "[<a href=\"$self?page=$jml_page\"><script> prt('Bottom') </script></a>]";
		  else echo '[<script> prt("bottom") </script>]'; 
      } else echo '<script> prt(\"page1of1\") </script>';
	  echo '</font></td></tr>';
?>
        </table>
		</td>
		</tr>
		</table>
		<br>
		<!-- Please don't remove this copyright notice.-->
		<!--a href="http://www.promosi-web.com/script/guestbook/" target="_blank"><font size="2" color="<?=$title_color?>">PHP Guestbook</font></a> &middot; <a href="http://hello.web.id" target="_blank"><font size="2" color="<?=$title_color?>">Web Directory</font></a></font-->
		<!-- Thank you -->
   </div>
   </body>
   </html>
<?
break;
case "add_form_disabled":
$_SESSION['secc'] = strtoupper(substr(sha1(time().$admin_email),0,4));
if (!isset($_SESSION['add'])) $_SESSION['add'] = 0;

if (!isset($_SESSION['name'])) $_SESSION['name'] = "";
if (!isset($_SESSION['email'])) $_SESSION['email'] = "";
if (!isset($_SESSION['url'])) $_SESSION['url'] = "http://";
if (!isset($_SESSION['comment'])) $_SESSION['comment'] = "";

include '../head.html';
include '../body.html';
include '../gbheader.html';
?>
<!--DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="<?=$background?>" style="font-family:<?=$font_face?>">
<div align="center"> 
  <font size="5" color="<?=$title_color?>"><b><?=$title?></b></font><br>
  <font size="1"><b><a href="<?=$home?>"><font color="<?=$title_color?>">Home</font></a> :: <a href="<?=$self?>"><font color="<?=$title_color?>">View entry</font></a></b></font>
  <br><br-->
<div align="center">
  <font size="5" color="<?=$title_color?>"><b><script>prt("addcomment")</script></b></font><br>
  <form method="post" action="<?=$self?>">
  <input type="hidden" name="do" value="add">
    <table width="500" border="0" cellspacing="0" cellpadding="0" bgcolor="<?=$table_border?>">
      <tr>
      <td>
        <div align="center">
            <table width="100%" border="0" cellspacing="1" cellpadding="5">
              <tr bgcolor="<?=$table_content_1a?>">
                <td width="28%">
                  <div align="right"><font size="2">*<script>prt("Name")</script> : </font></div>
                </td>
                <td width="72%">
                  <input type="text" name="vname" size="30" maxlength="70" value="<?=$_SESSION['name']?>">
                </td>
              </tr>
              <tr bgcolor="<?=$table_content_1a?>">
                <td width="28%">
                  <div align="right"><font size="2">Email : </font></div>
                </td>
                <td width="72%">
                  <input type="text" name="vemail" size="30" maxlength="100" value="<?=$_SESSION['email']?>">
                </td>
              </tr>
              <tr bgcolor="<?=$table_content_1a?>">
                <td width="28%">
                  <div align="right"><font size="2"><script>prt("Website")</script> : </font></div>
                </td>
                <td width="72%">
                  <input type="text" name="vurl" size="30" maxlength="150" value="<?=$_SESSION['url']?>">
                </td>
              </tr>
              <tr bgcolor="<?=$table_content_1a?>">
                <td valign="top" width="28%">
                  <div align="right"><font size="2">*<script>prt("Comment")</script> : </font></div>
                </td>
                <td width="72%">
                  <textarea name="vcomment" cols="40" rows="7" wrap="virtual"><?=$_SESSION['comment']?></textarea>
				  <br><font size="1">* <script>prt("requiredfield")</script></font>
                </td>
              </tr>
			  <?if (strtoupper($imgcode) == "YES") {?>
              <tr bgcolor="<?=$table_content_1a?>"> 
                <td width="28%"> 
                  <div align="right"><font size="2"><script>prt("vercode")</script> :</font></div>
                </td>
                <td width="72%"> 
                  <font size="1"><script>prt("retype")</script> :</font>
				  <img src="image.php?<?=time()?>" border="1"><br>
				  <input type="text" name="vsecc" size="4" maxlength="4">
                </td>
              </tr>
			  <?}?>
              <tr bgcolor="<?=$table_content_1b?>"> 
                <td colspan="2"> 
                  <div align="center">
                    <font size="2">
                    <script>prt("submit")</script>
                    <script>prt("reset")</script>
                    </font>
				   </div>
                </td>
              </tr>
            </table>
        </div>
      </td>
    </tr>
  </table>
  </form>
</div>
<!--/body>
</html-->

<!--#include file="../endhead.html" -->

<!-- End of entry form -->
<?
break;
case "add_disabled":
   $vname = isset($_POST['vname']) ? trim($_POST['vname']) : "";
   $vemail = isset($_POST['vemail']) ? trim($_POST['vemail']) : "";
   $vurl = isset($_POST['vurl']) ? trim($_POST['vurl']) : "";
   $vcomment = isset($_POST['vcomment']) ? trim($_POST['vcomment']) : "";
   $vsecc = isset($_POST['vsecc']) ? strtoupper($_POST['vsecc']) : "";

   if (strlen($vname) > 70) $vname = substr($vname,0,70);
   if (strlen($vemail) > 100) $vemail = substr($vemail,0,100);
   if (strlen($vurl) > 150) $vurl = substr($vurl,0,150);

   $_SESSION['name'] = $vname;
   $_SESSION['email'] = $vemail;
   $_SESSION['url'] = $vurl;
   $_SESSION['comment'] = stripslashes($vcomment);

   if ($vname == "" || $vcomment == "") {
	   input_err("missingfields");
   }

   if ($vemail != "" && !preg_match("/([\w\.\-]+)(\@[\w\.\-]+)(\.[a-z]{2,4})+/i", $vemail)) {
	   input_err("invalidemail");
   }

   if ($vurl != "" && strtolower($vurl) != "http://") {
       if (!preg_match ("#^http://[_a-z0-9-]+\\.[_a-z0-9-]+#i", $vurl)) {
		   input_err("invalidurl");
       }
   }

   $test_comment = preg_split("/[\s]+/",$vcomment);
   $jmltest = count($test_comment);
   for ($t=0; $t<$jmltest; $t++) {
      if (strlen(trim($test_comment[$t])) > 70) {
		  input_err("invalidword");
//		  input_err("Invalid word found on your entry : ".stripslashes($test_comment[$t]));
	  }
   }

   if (isset($_SESSION['add']) && $_SESSION['add'] >= $max_entry_per_session) {
	   input_err("toomanymessages",false);
//	   input_err("Sorry, only $max_entry_per_session message(s) allowed per session.",false);
   } elseif (!isset($_SESSION['add'])) {
	   exit;
   }

   if ($vsecc != $_SESSION['secc'] && strtoupper($imgcode) == "YES") {
	   input_err("invalidvercode");
   }
   //--only 2000 characters allowed for comment, change this value if necessary
   $maxchar = 2000;
   if (strlen($vcomment) > $maxchar) $vcomment = substr($vcomment,0,$maxchar)."...";

   $idx = date("YmdHis");
   $tgl = date("F d, Y - h:i A");

   $vname = str_replace("<","&lt;",$vname);
   $vname = str_replace(">","&gt;",$vname);
   $vname = str_replace("~","-",$vname);
   $vname = str_replace("\"","&quot;",$vname);
   $vcomment = str_replace("<","&lt;",$vcomment);
   $vcomment = str_replace(">","&gt;",$vcomment);
   $vcomment = str_replace("|","",$vcomment);
   $vcomment = str_replace("\"","&quot;",$vcomment);
   $vurl = str_replace("<","",$vurl);
   $vurl = str_replace(">","",$vurl);
   $vurl = str_replace("|","",$vurl);
   $vemail = str_replace("<","",$vemail);
   $vemail = str_replace(">","",$vemail);
   $vemail = str_replace("|","",$vemail);

   if (strtoupper($os) == "WIN") {
	   $vcomment = str_replace($newline,"<br>",$vcomment);
	   $vcomment = str_replace("\r","",$vcomment);
	   $vcomment = str_replace("\n","",$vcomment);
   } else {
	   $vcomment = str_replace($newline,"<br>",$vcomment);
	   $vcomment = str_replace("\r","",$vcomment);
   }

   if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && eregi("^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$",$_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ipnum = $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
       $ipnum = getenv("REMOTE_ADDR");
   }

   $newdata = "|~|$idx|~|$tgl|~|$vname|~|$vemail|~|$vcomment|~|$vurl|~|$ipnum|~|";
   $newdata = stripslashes($newdata);
   $newdata .= $newline;

   if (!is_spam($newdata)) {
		$tambah = fopen($data_file,"a");
		if (strtoupper($os)=="UNIX") {
			if (flock($tambah,LOCK_EX)) {
				fwrite($tambah,$newdata);
				flock($tambah,LOCK_UN);
			}
		} else {
			fwrite($tambah,$newdata);
		}
		fclose($tambah);

		//--send mail
		if (strtoupper($notify) == "YES") {
			$msgtitle = "Someone signed your guestbook";
			$vcomment = str_replace("&quot;","\"",$vcomment);   
			$vcomment = stripslashes($vcomment);
			$vcomment = str_replace("<br>","\n",$vcomment);
			$msgcontent = "Local time : $tgl\n\nThe addition from $vname :\n----------------------------\n\n$vcomment\n\n-----End Message-----";
			@mail($admin_email,$msgtitle,$msgcontent,"From: $vemail\n");
		}
		//--clear session
		$_SESSION['name'] = "";
		$_SESSION['email'] = "";
		$_SESSION['url'] = "http://";
		$_SESSION['comment'] = "";
		$_SESSION['add']++;
		$_SESSION['secc'] = "";
		redir($self,"Thank you, your entry has been added.");
	} else {
		redir($self,"Sorry, your entry can't be added into the guestbook.");
	}
break;

case "del":
   $record = file($data_file);
   $jmlrec = count($record);
   for ($i=0; $i<$jmlrec; $i++) {
       $row = explode("|~|",$record[$i]);
	   if ($id == $row[1]) {
			include '../head.html';
			include '../body.html';
			include '../gbheader.html';
	      ?>
		  <!--DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
		  <html>
		  <head>
		  <title>Delete record</title>
		  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		  </head>
		  <body bgcolor="<?=$background?>" style="font-family:<?=$font_face?>"-->
		  <div align="center">
		  <font size="4" color="<?=$title_color?>">Delete Confirmation</font>
		  <br><br>
		  <table border="0" cellpadding="5" cellspacing="1" width="450">
			<tr>
			<td bgcolor="<?=$table_top?>">
            <font size="2">
			<font size="1"><b><?=$row[2]?></font><br><?=$row[3]?></b> - <a href="mailto:<?=$row[4]?>"><?=$row[4]?></a>
			<br><br><?=$row[5]?>
			<br><br><font size="1">IP : <?=$row[7]?></font>
			</font>
			</td>
			</tr>
		  </table>
		  <form action="<?=$self?>" method="post">
			  <input type="hidden" name="do" value="del2">
			  <input type="hidden" name="id" value="<?=$id?>">
			  <input type="hidden" name="page" value="<?=$page?>">
			  <font color="<?=$title_color?>" size="2"><b>Admin password : </b></font> <input type="password" name="pwd">
			  <br><br>
			  <font size="2" color="<?=$title_color?>"><b>&raquo;</b><input type="checkbox" name="byip" value="<?=$row[7]?>"> Delete all records that using this IP : <?=$row[7]?></font>
			  <br><br>
			  <input type="submit" value="Delete"> <input type="button" value="Cancel" onclick="window.location='<?="$self?page=$page"?>'">
		  </form>
		  </div>
		  </body>
		  </html>
		  <?
	   }
   }
break;

case "del2":
   $pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : "";
   $id = isset($_POST['id']) ? trim($_POST['id']) : "";
   $page = isset($_POST['page']) ? $_POST['page'] : 1;
   $byip = isset($_POST['byip']) ? $_POST['byip'] : "";

   if ($pwd != $admin_password) {
	     redir("$self?page=$page","invalidadminpass");
   }

   $record = file($data_file);
   $jmlrec = count($record);
   for ($i=0; $i<$jmlrec; $i++) {
       $row = explode("|~|",$record[$i]);
	   if ($byip == "") {
		   if ($row[1] == $id) {
			   $record[$i] = "";
		       break;
	       }
	   } else {
		   if ($row[7] == $byip) {
			   $record[$i] = "";
		   }
	   }
   }

   $update_data = fopen($data_file,"w");
   if (strtoupper($os) == "UNIX") {
      if (flock($update_data,LOCK_EX)) {
	     for ($j=0; $j<$jmlrec; $j++) {
             if ($record[$j] != "") {
				 fputs($update_data,$record[$j]);
			 }
		 }
		 flock($update_data,LOCK_UN);
	  }
   } else {
	     for ($j=0; $j<$jmlrec; $j++) {
             if ($record[$j] != "") {
				 fputs($update_data,$record[$j]);
			 }
		 }
   }
   fclose($update_data);
   redir("$self?page=$page","entrydeleted");
break;
} //--end switch


function redir($target,$msg) {
global $background,$font_face,$title_color;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=windows-1251" http-equiv="content-type">
	<meta http-equiv="refresh" content="3; url=<?=$target?>">
	<title> Johan Vladimir </title>
</head>
<script type="text/javascript" src="/cookies.js"></script>
<script type="text/javascript" src="/lang.js"></script>
<?
include '../body.html';
include '../gbheader.html';
?>

<center><font color="<?=$title_color?>" face="<?=$font_face?>"><h3><script>prt("<?=$msg?>")</script></h3><script>prt("pleasewait")</script></font></center>

<!--#include file="../endhead.html" -->

<!--/head>
<body bgcolor="<?=$background?>">
<div align="center"><font color="<?=$title_color?>" face="<?=$font_face?>"><h3><?=$msg?></h3>Please wait...</font></div>
</body>
</html-->
<?
exit;
}

function input_err($err_msg,$linkback=true) {
global $background,$font_face;

include '../head.html';
include '../body.html';
include '../gbheader.html';
?>
<!--DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Error !</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="<?=$background?>"-->
<div align="center">
<br>
<table border="1" bgcolor="#000000" cellspacing="0" cellpadding="6">
<tr>
	<td bgcolor="#FFCC00" align="center">
		<font size="3" color="#000000" face="<?=$font_face?>"><b><script>prt("<?=$err_msg?>")</script></b><br>
		<?if ($linkback) {?>
		<font size="2"><a href="javascript:history.back()"><script>prt("tryagain")</script></a></font>
	    <?}?>
		</font>
	</td>
</tr>
</table>
</div>
<!--#include file="../endhead.html" -->
<!--/body>
</html-->
<?
exit;
}

function is_spam($string) {
	$data = "spamwords.dat";
	$is_spam = false;
	if (file_exists($data)) {
		$spamword = file($data);
		$jmlrec = count($spamword);
		for ($i=0; $i<$jmlrec; $i++) {
			$spamword[$i] = trim($spamword[$i]);
			if (eregi($spamword[$i],$string)) {
				$is_spam = true;
				break;
			}
		}
	}
	return $is_spam;
}
?>
