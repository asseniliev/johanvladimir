<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class addentry {

    var $gb;
    var $ip;
    var $include_path;
    var $template;
    var $name = '';
    var $email = '';
    var $url = '';
    var $comment = '';
    var $location = '';
    var $icq = '';
    var $aim = '';
    var $gender = '';
    var $userfile = '';
    var $user_img = '';
    var $preview = '';
    var $private = '';
    var $image_file = '';
    var $image_tag = '';
    var $GB_TPL = array();
    var $table = array();

    function addentry($path='') {
        global $GB_TPL, $GB_TBL, $HTTP_SERVER_VARS;
        if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']) && eregi("^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$",$HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = getenv("REMOTE_ADDR");
        }
        $this->gb = new guestbook_vars($path);
        $this->gb->getVars();
        $this->template =& $this->gb->template;
        $this->include_path = $path;
        $this->GB_TPL =& $GB_TPL;
        $this->table =& $GB_TBL;
    }

    function undo_htmlspecialchars($string) {
        $html = array (
            "&amp;"  => "&",
            "&quot;" => "\"",
            "&lt;"   => "<",
            "&gt;"   => ">"
        );
        for(reset($html); $key=key($html); next($html)) {
            $string = str_replace("$key","$html[$key]",$string);
        }
        return ($string);
    }

    function clear_tmpfiles($cachetime=1800) {
        global $GB_TMP;
        $delfiles = 0;
        $filelist = '';
        if (is_dir("$this->include_path/$GB_TMP")) {
            chdir("$this->include_path/$GB_TMP");
            $hnd = opendir(".");
            while ($file = readdir($hnd)) {
                if(is_file($file)) {
                    $filelist[] = $file;
                }
            }
            closedir($hnd);
        }
        if (is_array($filelist)) {
            while (list ($key, $file) = each ($filelist)) {
                $tmpfile = explode(".",$file);
                $tmpfile[0] = ereg_replace ("img-", "", $tmpfile[0]);
                if ($tmpfile[0] < (time()-$cachetime)) {
                    if (unlink($file)) {
                        $delfiles ++;
                    }
                }
            }
        }
        return $delfiles;
    }

    function is_uploaded_file_readable($uploaded_file_tmp_name) {
        $check = @fopen($uploaded_file_tmp_name, "rb");
        if ($check) {
            fclose($check);
            return 1;   
        } else {
            $is_safe_mode = get_cfg_var("safe_mode");
            if ($is_safe_mode) {
                return -1;
            } else {          
                return 2;
            }
        }
    }

    function check_entry($type='') {
        global $GB_UPLOAD, $GB_TMP, $GB_PG;
        $this->gb->VARS["max_img_size"] = $this->gb->VARS["max_img_size"]*1024;
        if ($this->gb->VARS["banned_ip"]==1) {
            if ($this->gb->isBannedIp($this->ip)) {
                 return $this->gb->gb_error($_SESSION['translate']->it("Your IP adress is banned!"));
            }
        }
        if ($this->gb->VARS["flood_check"]==1) {
            if ($this->gb->FloodCheck($this->ip)) {
                return $this->gb->gb_error($_SESSION['translate']->it("Sorry! We have flood-control activated. Please try again after a period of time elapses!"));
            }
        }
        if (is_array($this->userfile) && $this->userfile["userfile"]["tmp_name"] != "none") {
            $uploaded_img_file_stat = $this->is_uploaded_file_readable($this->userfile["userfile"]["tmp_name"]);        
        } else {
            $uploaded_img_file_stat = -1;
        }
        if ($uploaded_img_file_stat > 0) {
            $extension = array("1" => 'gif',"2" => 'jpg',"3" => 'png',"4" => 'swf');
            $the_time = time();
            if ($this->userfile["userfile"]["size"] > $this->gb->VARS["max_img_size"]) {
                return $this->gb->gb_error($_SESSION['translate']->it("Image file is too big"));
            } else {
                if ($uploaded_img_file_stat == 1) {
                    $size = GetImageSize($this->userfile["userfile"]["tmp_name"]);
                    $open_basedir_res = false;
                } else {
                    $open_basedir_res = true;
                    if (!eregi("WIN",PHP_OS)) {
                        exec("cp ".$this->userfile["userfile"]["tmp_name"]." $this->include_path/$GB_TMP/img-$the_time.tmp");
                    } else {
                        $win_command = str_replace("/","\\",$this->userfile["userfile"]["tmp_name"]);
                        $win_loc = str_replace("/","\\", "$this->include_path/$GB_TMP/img-$the_time.tmp");
                        exec("copy $win_command $win_loc");
                    }                    
                    $size = GetImageSize("$this->include_path/$GB_TMP/img-$the_time.tmp");  
                }
                if ($size[2]>0 && $size[2]<4) {
                    $this->image_file = "img-$the_time.".$extension[$size[2]];
                    $img = new gb_image();
                    $img->set_destdir("$this->include_path/$GB_UPLOAD");
                    $img->set_border_size($this->gb->VARS["img_width"], $this->gb->VARS["img_height"]);
                    if ($type=="preview") {
                        if (!$open_basedir_res) {
                            copy($this->userfile["userfile"]["tmp_name"], "$this->include_path/$GB_TMP/$this->image_file");
                        } else {
                            rename("$this->include_path/$GB_TMP/img-$the_time.tmp", "$this->include_path/$GB_TMP/$this->image_file"); 
                        }
                        $new_img_size = $img->get_img_size_format($size[0], $size[1]);
                        $GB_UPLOAD = $GB_TMP;
                        $row['p_filename'] = $this->image_file;
                        $row['width'] = $size[0];
                        $row['height'] = $size[1];
                        eval("\$this->tmp_image = \"".$this->template->get_template($this->GB_TPL['image'])."\";");
                    } else {
                        if (!$open_basedir_res) {
                            copy($this->userfile["userfile"]["tmp_name"], "$this->include_path/$GB_UPLOAD/$this->image_file");
                        } else {
                            rename("$this->include_path/$GB_TMP/img-$the_time.tmp", "$this->include_path/$GB_UPLOAD/$this->image_file");    
                        }
                        if ($this->gb->VARS["thumbnail"]==1) {
                            $min_size = 1024*$this->gb->VARS["thumb_min_fsize"];
                            $img->set_min_filesize($min_size);
                            $img->set_prefix("t_");
                            $img->create_thumbnail("$this->include_path/$GB_UPLOAD/$this->image_file","$this->image_file");
                        }
                    }
                } else {
                    return $this->gb->gb_error($_SESSION['translate']->it("Wrong image file type"));
                }
            }
        }
        if (!empty($this->user_img)) {
            $this->image_file = trim($this->user_img);
        }
        $this->name = $this->gb->FormatString($this->name);
        $this->location = $this->gb->FormatString($this->location);
        $this->comment = $this->gb->FormatString($this->comment);
        $this->icq = $this->gb->FormatString($this->icq);
        $this->aim = $this->gb->FormatString($this->aim);
        $this->aim = htmlspecialchars($this->aim);
        if ($this->icq < 1000 || $this->icq >999999999) {
            $this->icq='';
        }
        if ($this->name == "") {
            return $this->gb->gb_error($_SESSION['translate']->it("You forgot to fill in the Name field. Please correct it and re-submit."));
        } elseif (strlen($this->comment)<$this->gb->VARS["min_text"] || strlen($this->comment)>$this->gb->VARS["max_text"]) {
            return $this->gb->gb_error($_SESSION['translate']->it("Your message is either too short or too long. Please correct it and re-submit."));
        } else {
            $this->url = trim($this->url);
            $this->email = trim($this->email);
            if (!eregi("^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\\.)+[a-z]{2,5}$", $this->email) ) {
                $this->email = '';
            }
            if (!eregi("^http://[_a-z0-9-]+\\.[_a-z0-9-]+", $this->url)) {
                $this->url = '';
            }
            if (htmlspecialchars($this->url) != "$this->url") {
                $this->url = '';
            }
        }
        if ($this->gb->VARS["censor"]==1) {
            $this->name = $this->gb->CensorBadWords($this->name);
            $this->location = $this->gb->CensorBadWords($this->location);
            $this->comment = $this->gb->CensorBadWords($this->comment);
        }
        if (!$this->gb->CheckWordLength($this->name) || !$this->gb->CheckWordLength($this->location)) {
            return $this->gb->gb_error($_SESSION['translate']->it("One of the input fields does not seem to be valid."));
        }
        if (!$this->gb->CheckWordLength($this->comment)) {
            return $this->gb->gb_error($_SESSION['translate']->it("Your message contains some invalid words. Please correct it and re-submit."));
        }
        return 1;
    }

    function add_guest() {
        global $GB_TMP, $GB_UPLOAD, $GB_PG;
        if ($this->preview==1 && $this->user_img) {
            $img = new gb_image();
            $img->set_destdir("$this->include_path/$GB_UPLOAD");
            $img->set_border_size($this->gb->VARS["img_width"], $this->gb->VARS["img_height"]);
            if ($this->gb->VARS["thumbnail"]==1) {
                $min_size = 1024*$this->gb->VARS["thumb_min_fsize"];
                $img->set_min_filesize($min_size);
                $img->set_prefix("t_");
                $img->create_thumbnail("$this->include_path/$GB_TMP/$this->user_img",$this->user_img);
            }
            copy("$this->include_path/$GB_TMP/$this->user_img", "$this->include_path/$GB_UPLOAD/$this->user_img");
            unlink("$this->include_path/$GB_TMP/$this->user_img");
            $this->image_file = $this->user_img;
        }
        $this->name = htmlspecialchars($this->name);
        $this->location = htmlspecialchars($this->location);
        if ($this->gb->VARS["allow_html"] == 0) {
            $this->comment = htmlspecialchars($this->comment);
        }
        if ($this->gb->VARS["agcode"] == 1) {
            $this->comment = $this->gb->AGCode($this->comment);
        }
        if (!get_magic_quotes_gpc()) {
            $this->name = addslashes($this->name);
            $this->location = addslashes($this->location);
            $this->aim = addslashes($this->aim);
            $this->email = addslashes($this->email);
            $this->url = addslashes($this->url);
            $this->comment = addslashes($this->comment);
        }
        $host = gethostbyaddr($this->ip);
        $agent = getenv("HTTP_USER_AGENT");
        $the_time = time();
        $sql_usertable = ($this->private==1) ? $this->table['priv'] : $this->table['data'];
        $this->gb->db->query("INSERT INTO ".$sql_usertable." (name,gender,email,url,date,location,host,browser,comment,icq,aim) VALUES ('$this->name','$this->gender','$this->email','$this->url','$the_time','$this->location','$host','$agent','$this->comment','$this->icq','$this->aim')", true);
        if (!empty($this->image_file) || !empty($this->user_img)) {
            $size = GetImageSize("$this->include_path/$GB_UPLOAD/$this->image_file");
            if (is_array($size) && $size[2]>0 && $size[2]<4) {
                $book_id = ($this->private==1) ? 1 : 2;
                $p_filesize = filesize("$this->include_path/$GB_UPLOAD/$this->image_file");
                $result=$this->gb->db->query("SELECT MAX(id) AS msg_id FROM ".$this->gb->db->tbl_prefix.$sql_usertable);
                $record = $result->fetchrow();
                $this->gb->db->query("INSERT INTO ".$this->table['pics']." (msg_id,book_id,p_filename,p_size,width,height) VALUES ('".$record['msg_id']."',$book_id,'$this->image_file','$p_filesize','$size[0]','$size[1]')", true);
            }
        }
        $from_email = ($this->email == "") ? "nobody@$host" : $this->email;
        if ($this->gb->VARS["notify_private"]==1 && $this->private==1) {
            @mail($this->gb->VARS["admin_mail"],$_SESSION['translate']->it("New guestbook entry"),"$this->name\n$this->host\n\n$this->comment", "From: <".$this->name."> $from_email\nX-Mailer: Advanced Guestbook 2");
        }
        if ($this->gb->VARS["notify_admin"]==1 && $this->private==0) {
            @mail($this->gb->VARS["admin_mail"],$_SESSION['translate']->it("New guestbook entry"),"$this->name\n$this->host\n\n$this->comment", "From: <".$this->name."> $from_email\nX-Mailer: Advanced Guestbook 2");
        }
        if ($this->gb->VARS["notify_guest"]==1 && $this->email != '') {
            @mail($this->email,$_SESSION['translate']->it("Thank you for signing the guestbook"),$this->gb->VARS["notify_mes"], "From: <".$this->gb->VARS['admin_mail']."> ".$this->gb->VARS['admin_mail']."\nX-Mailer: Advanced Guestbook 2");
        }
        if ($this->gb->VARS["flood_check"]==1) {
            $this->gb->db->query("INSERT INTO ".$this->table['ip']." (guest_ip,timestamp) VALUES ('$this->ip','$the_time')", true);
        }
        $LANG =& $this->gb->LANG;
        $VARS =& $this->gb->VARS;
        eval("\$success_html = \"".$this->template->get_template($this->GB_TPL['success'])."\";");
        eval("\$success_html .= \"".$this->template->get_template($this->GB_TPL['footer'])."\";");
        return $success_html;
    }

    function form_addguest() {
        global $GB_PG, $HTTP_COOKIE_VARS, $_SESSION;
        if($_SESSION['OBJ_user']->username) {
            $G_NAME  = $_SESSION['OBJ_user']->username;
        } else {
            $G_NAME  = '';
        }
        $HTML_CODE = ($this->gb->VARS["allow_html"] == 1) ? $_SESSION['translate']->it("HTML code is enabled") : $_SESSION['translate']->it("HTML code is disabled");
        $SMILE_CODE = ($this->gb->VARS["smilies"] == 1) ? $_SESSION['translate']->it("Smilies are ON") : $_SESSION['translate']->it("Smilies are OFF");
        $AG_CODE = ($this->gb->VARS["agcode"] == 1) ? $_SESSION['translate']->it("AGCode is ON") : $_SESSION['translate']->it("AGCode is OFF");
        $LANG =& $this->gb->LANG;
        $VARS =& $this->gb->VARS;
        $OPTIONS[] ='';
        if ($this->gb->VARS["allow_icq"]==1) {
            eval("\$OPTIONS['icq'] = \"".$this->template->get_template($this->GB_TPL['frm_icq'])."\";");
        }
        if ($this->gb->VARS["allow_aim"]==1) {
            eval("\$OPTIONS['aim'] = \"".$this->template->get_template($this->GB_TPL['frm_aim'])."\";");
        }
        if ($this->gb->VARS["allow_gender"]==1) {
            eval("\$OPTIONS['gender'] = \"".$this->template->get_template($this->GB_TPL['frm_gender'])."\";");
        }
        if ($this->gb->VARS["allow_img"]==1) {
            eval("\$OPTIONS['img'] = \"".$this->template->get_template($this->GB_TPL['frm_image'])."\";");
        }
        $OPTIONAL = implode("\n",$OPTIONS);
        if (isset($HTTP_COOKIE_VARS['lang']) && !empty($HTTP_COOKIE_VARS['lang']) && file_exists("$this->include_path/lang/codes-".$HTTP_COOKIE_VARS['lang'].".php")) {
            $LANG_CODES = "$GB_PG[base_url]/lang/codes-".$HTTP_COOKIE_VARS['lang'].".php";
        } elseif (file_exists("$this->include_path/lang/codes-".$VARS['lang'].".php")) {
            $LANG_CODES = "$GB_PG[base_url]/lang/codes-".$VARS['lang'].".php";
        } else {
            $LANG_CODES = "$GB_PG[base_url]/lang/codes-english.php";
        }
        eval("\$addform_html = \"".$this->template->get_template($this->GB_TPL['header'])."\";");
        eval("\$addform_html .= \"".$this->template->get_template($this->GB_TPL['form'])."\";");
        eval("\$addform_html .= \"".$this->template->get_template($this->GB_TPL['footer'])."\";");
        return $addform_html;
    }

    function preview_entry() {
        global $GB_PG;
        if (get_magic_quotes_gpc()) {
            $this->name = stripslashes($this->name);
            $this->comment = stripslashes($this->comment);
            $this->location = stripslashes($this->location);
        }
        $this->name = htmlspecialchars($this->name);
        if ($this->gb->VARS["allow_html"] == 0) {
            $message = htmlspecialchars($this->comment);
            $message = nl2br($message);
        } else {
            $message = nl2br($this->comment);
        }
        if ($this->gb->VARS["smilies"] == 1) {
            $message = $this->gb->emotion($message);
        }
        if ($this->gb->VARS["agcode"] == 1) {
            $message = $this->gb->AGCode($message);
        }
        $this->location = htmlspecialchars($this->location);
        $this->comment = htmlspecialchars($this->comment);
        $USER_PIC =(isset($this->tmp_image)) ? $this->tmp_image : '';
        $DATE = $this->gb->DateFormat(time());
        $host = @gethostbyaddr($this->ip);
        $AGENT = getenv("HTTP_USER_AGENT");
        $LANG =& $this->gb->LANG;
        $VARS =& $this->gb->VARS;
        if ($this->url) {
            $row['url'] = $this->url;
            eval("\$URL = \"".$this->template->get_template($this->GB_TPL['url'])."\";");
        } else {
            $URL = '';
        }
        if ($this->icq && $this->gb->VARS["allow_icq"]==1) {
            $row['icq'] = $this->icq;
            eval("\$ICQ = \"".$this->template->get_template($this->GB_TPL['icq'])."\";");
        } else {
            $ICQ = '';
        }
        if ($this->aim && $this->gb->VARS["allow_aim"]==1) {
            $row['aim'] = $this->aim;
            eval("\$AIM = \"".$this->template->get_template($this->GB_TPL['aim'])."\";");
        } else {
            $AIM = '';
        }
        if ($this->email) {
            $row['email'] = $this->email;
            eval("\$EMAIL = \"".$this->template->get_template($this->GB_TPL['email'])."\";");
        } else {
            $EMAIL = '';
        }
        if ($this->gb->VARS["allow_gender"]==1) {
            $GENDER = ($this->gender=="f") ? "&nbsp;<img src=\"$GB_PG[base_url]/img/female.gif\" width=\"12\" height=\"12\">" : "&nbsp;<img src=\"$GB_PG[base_url]/img/male.gif\" width=\"12\" height=\"12\">";
        } else {
            $GENDER = '';
        }
        if ($this->gb->VARS["show_ip"] == 1) {
            $hostname = ( eregi("^[-a-z_]+", $host) ) ? "Host" : "IP";
            $HOST = "$hostname: $host\n";
        } else {
            $HOST = '';
        }
        $HIDDEN = "<input type=\"hidden\" name=\"gb_preview\" value=\"1\">\n";
        $HIDDEN .= "<input type=\"hidden\" name=\"gb_name\" value=\"".$this->name."\">\n";
        $HIDDEN .= "<input type=\"hidden\" name=\"gb_email\" value=\"".$this->email."\">\n";
        $HIDDEN .= "<input type=\"hidden\" name=\"gb_url\" value=\"".$this->url."\">\n";
        $HIDDEN .= "<input type=\"hidden\" name=\"gb_comment\" value=\"".$this->comment."\">\n";
        $HIDDEN .= "<input type=\"hidden\" name=\"gb_location\" value=\"".$this->location."\">\n";
        if ($this->image_file) {
            $HIDDEN .= "<input type=\"hidden\" name=\"gb_user_img\" value=\"".$this->image_file."\">\n";
        }
        if ($this->private==1) {
            $HIDDEN .= "<input type=\"hidden\" name=\"gb_private\" value=\"".$this->private."\">\n";
        }
        if ($this->gb->VARS["allow_gender"]==1) {
            $HIDDEN .= "<input type=\"hidden\" name=\"gb_gender\" value=\"".$this->gender."\">\n";
        }
        if ($this->icq && $this->gb->VARS["allow_icq"]==1) {
            $HIDDEN .= "<input type=\"hidden\" name=\"gb_icq\" value=\"".$this->icq."\">\n";
        }
        if ($this->aim && $this->gb->VARS["allow_aim"]==1) {
            $HIDDEN .= "<input type=\"hidden\" name=\"gb_aim\" value=\"".$this->aim."\">\n";
        }
        $row['name'] = $this->name;
        $row['location'] = $this->location;
        $row['email'] = $this->email;
        eval("\$GB_PREVIEW = \"".$this->template->get_template($this->GB_TPL['prev_entry'])."\";");
        eval("\$preview_html = \"".$this->template->get_template($this->GB_TPL['header'])."\";");
        eval("\$preview_html .= \"".$this->template->get_template($this->GB_TPL['preview'])."\";");
        eval("\$preview_html .= \"".$this->template->get_template($this->GB_TPL['footer'])."\";");
        return $preview_html;
    }

    function process($action='') {
        switch ($action) {
            case $_SESSION['translate']->it("Submit"):
                if ($this->preview==1) {
                    $this->comment = $this->undo_htmlspecialchars($this->comment);
                    $this->name = $this->undo_htmlspecialchars($this->name);
                }
                $this->clear_tmpfiles();
                $status = $this->check_entry();
                return ($status == 1) ? $this->add_guest() : $status;
                break;

            case $_SESSION['translate']->it("Preview"):
                $status = $this->check_entry("preview");
                return ($status == 1) ? $this->preview_entry() : $status;
                break;

            default:
                return $this->form_addguest();
        }
    }

}

?>