<?php
function gb_menu() {

    $mod_url = basename($HTTP_SERVER_VARS["PHP_SELF"])."?module=".basename(dirname(dirname(__FILE__)));

    $content ='
    </center>
    <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><a href="'.$mod_url.'&agbook=admin&action=show&amp;tbl=priv">Private 
    Messages</a> | <a href="'.$mod_url.'&agbook=admin&action=show&amp;tbl=gb">Easy 
    Admin</a> | <a href="'.$mod_url.'&agbook=admin&action=settings&amp;panel=general">General 
    Settings</a> | <a href="'.$mod_url.'&agbook=admin&action=settings&amp;panel=style">Style</a> 
    | <a href="'.$mod_url.'&agbook=admin&action=template">Templates</a> 
    | <a href="'.$mod_url.'&agbook=admin&action=smilies">Smilies</a> 
    | <a href="'.$mod_url.'">go to Guestbook</a> 

    <hr>
    <b><font size="1" face="Verdana, Arial">To check your environmental variables, <a href="'.$mod_url.'&agbook=admin&action=info" target="_new">click here.</a></b>
    <br><br><br>';

    return $content;
}

?>