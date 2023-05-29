<?php

$ModName = basename(dirname(__FILE__));
$include_path = $GLOBALS["core"]->source_dir ."mod/".$ModName;
//include_once $include_path."/admin/config.inc.php";
include_once $include_path."/class/admin.class.php";

if (!isset($PHP_SELF)) {
    $PHP_SELF = $HTTP_SERVER_VARS["PHP_SELF"];
    if (isset($HTTP_GET_VARS)) {
        while (list($name, $value)=each($HTTP_GET_VARS)) {
            $$name=$value;
        }
    }
    if (isset($HTTP_POST_VARS)) {
        while (list($name, $value)=each($HTTP_POST_VARS)) {
            $$name=$value;
        }
    }
    if (isset($HTTP_COOKIE_VARS)) {
        while (list($name, $value)=each($HTTP_COOKIE_VARS)){
            $$name=$value;
        }
    }
}

if (!$_SESSION["OBJ_user"]->isDeity()){
    header("location:index.php");
    exit();
} else {
    $AUTH = true;
}

$VARS = $GLOBALS["core"]->sqlSelect($GB_TBL["cfg"]);

$template = new gb_template($include_path);
if (isset($HTTP_COOKIE_VARS['lang']) && !empty($HTTP_COOKIE_VARS['lang'])) {
    $template->set_lang($HTTP_COOKIE_VARS['lang']);
} else {
    $template->set_lang($VARS['lang']);
}
$LANG = $template->get_content();

if (!$AUTH) {
    header("location:index.php");
    exit();
} else {

    $action = (!isset($action)) ? "" : $action;
    $admin = new gb_admin($include_path, "mod/".$ModName."/");
    $admin->VARS =& $VARS[0];
    $admin->db =& $GLOBALS["core"];
    $admin->table =& $GB_TBL;
    $admin->SELF =& basename($HTTP_SERVER_VARS["PHP_SELF"]);
    $admin->SELF .= "?module=".$ModName."&agbook=admin";
    
    switch ($action) {
    
        case "show":
            $admin->show_entry($tbl);
            break;
    
        case "del":
            $admin->del_entry($id,$tbl);
            $admin->show_entry($tbl);
            break;
    
        case "edit":
            $admin->show_form($id,$tbl);
            break;
    
        case "info":
            $admin->show_panel("info");
            break;
    
        case "smilies":
            if (isset($scan_dir)) {
                $smilie_list = $admin->scan_smilie_dir();
            }
            if (isset($del_smilie)) {
                $GLOBALS["core"]->sqlDelete($GB_TBL['smile'],'id',$del_smilie);
            }
            if (isset($edit_smilie)) {
                if (isset($s_code) && isset($s_emotion)) {
                    if (!get_magic_quotes_gpc()) {
                        $s_code = addslashes($s_code);
                        $s_emotion = addslashes($s_emotion);
                    }
                    $GLOBALS["core"]->query("UPDATE ".$GB_TBL['smile']." SET s_code='$s_code', s_emotion='$s_emotion' WHERE id='$edit_smilie'", true);
                } else {
                    $s_data = $GLOBALS["core"]->sqlSelect($GB_TBL['smile'],'id',$edit_smilie);
                    $smilie_data = $s_data[0];
                }
            }
            if (isset($add_smilies)) {
                if(isset($new_smilie) && isset($new_emotion)) {
                    for(reset($new_smilie); $key=key($new_smilie); next($new_smilie)) {
                        if (!empty($new_emotion[$key]) && !empty($new_smilie[$key])) {
                            $size = GetImageSize($include_path."/img/smilies/$key");
                            $GLOBALS["core"]->query("INSERT INTO ".$GB_TBL['smile']." (s_code,s_filename,s_emotion,width,height) VALUES('".$new_smilie[$key]."','$key','".$new_emotion[$key]."','".$size[0]."','".$size[1]."')", true);
                        }
                    }
                }
            }
            $admin->show_panel("smilies");
            break;
    
        case "update":
            $admin->update_record($id,$tbl);
            $admin->show_entry($tbl);
            break;
    
        case "template":
            $tpl_name = (isset($tpl_name)) ? $tpl_name : '';
            $save = (isset($save)) ? $save : '';
            $admin->edit_template($tpl_name,$save);
            break;
    
        case "save":
            if ($panel == "general") {
                if ($allow_img==1) {
                    $upload_dir = $include_path."/$GB_UPLOAD";
                    $test = @is_dir($upload_dir);
                    if (!$test) {
                        @mkdir($upload_dir, 0777);
                    }
                }
                $notify_private = (isset($notify_private)) ? 1 : 0;
                $notify_admin = (isset($notify_admin)) ? 1 : 0;
                $notify_guest = (isset($notify_guest)) ? 1 : 0;
                $thumbnail = (isset($thumbnail)) ? 1 : 0;
                $sqlquery= "UPDATE ".$GLOBALS["core"]->tbl_prefix.$GB_TBL['cfg']." set agcode='$agcode', allow_html='$allow_html', smilies='$smilies', ";
                $sqlquery.="admin_mail='$admin_mail', notify_private='$notify_private', notify_admin='$notify_admin', notify_guest='$notify_guest', notify_mes='$notify_mes', entries_per_page='$entries_per_page', ";
                $sqlquery.="show_ip='$show_ip', lang='$lang', min_text='$min_text', max_text='$max_text', max_word_len='$max_word_len', ";
                $sqlquery.="censor='$censor', flood_check='$flood_check', banned_ip='$banned_ip', flood_timeout='$flood_timeout', allow_icq='$allow_icq', ";
                $sqlquery.="allow_aim='$allow_aim', allow_gender='$allow_gender', allow_img='$allow_img', max_img_size='$max_img_size', allow_icq='$allow_icq', ";
                $sqlquery.="need_pass='$need_pass', comment_pass='$comment_pass', img_width='$img_width', img_height='$img_height', thumbnail='$thumbnail', thumb_min_fsize='$thumb_min_fsize', show_sidebox='$show_sidebox' WHERE (config_id = '1')";
                $GLOBALS["core"]->query($sqlquery);
                $badwords=trim($badwords);
                $badwords=str_replace("\r", "", $badwords);
                if (!get_magic_quotes_gpc()) {
                    $badwords = stripslashes($badwords);
                }
                $word_array = explode("\n", $badwords);
                if (sizeof($word_array)>0) {
                    $sqlquery= "DELETE from ".$GB_TBL['words'];
                    $GLOBALS["core"]->query($sqlquery, true);
                    for($i=0;$i<sizeof($word_array);$i++) {
                        if (trim($word_array[$i]) != "") {
                            $sqlquery= "INSERT INTO ".$GB_TBL['words']." (word) VALUES('$word_array[$i]')";
                            $GLOBALS["core"]->query($sqlquery ,true);
                        }
                    }
                }
                $banned_ips=trim($banned_ips);
                $banned_ips=str_replace("\r", "", $banned_ips);
                $ip_array = explode("\n", $banned_ips);
                if (sizeof($ip_array)>0) {
                    $sqlquery= "DELETE from ".$GB_TBL['ban'];
                    $GLOBALS["core"]->query($sqlquery ,true);
                    for($i=0;$i<sizeof($ip_array);$i++) {
                        if (ereg("^[0-9]{1,3}\\.[0-9]{1,3}\\.",$ip_array[$i])) {
                            $sqlquery= "INSERT INTO ".$GB_TBL['ban']." (ban_ip) VALUES('$ip_array[$i]')";
                            $GLOBALS["core"]->query($sqlquery, true);
                        }
                    }
                }
                $admin->get_updated_vars();
                $admin->show_settings("general");
            } elseif ($panel == "style") {
                $sqlquery= "UPDATE ".$GLOBALS["core"]->tbl_prefix.$GB_TBL['cfg']." set pbgcolor='$pbgcolor', text_color='$text_color', link_color='$link_color', width='$width', ";
                $sqlquery.="tb_font_1='$tb_font_1', tb_font_2='$tb_font_2', font_face='$font_face', tb_hdr_color='$tb_hdr_color', tb_bg_color='$tb_bg_color', tb_text='$tb_text', ";
                $sqlquery.="tb_color_1='$tb_color_1', tb_color_2='$tb_color_2', dformat='$dformat', tformat='$tformat', offset='$offset' WHERE (config_id = '1')";
                $GLOBALS["core"]->query($sqlquery);
                $admin->get_updated_vars();
                $admin->show_settings("style");
            } else {
                $admin->show_panel();
            }
            break;
    
        case "settings":
            if ($panel == "style") {
                $admin->show_settings("style");
            } else {
                $admin->show_settings("general");
            }
            break;
    
        default:
            $admin->show_settings("general");
            break;
    }

}

?>