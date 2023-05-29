<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class gb_comment {

    var $comment;
    var $ip;
    var $id;
    var $gb;
    var $user;
    var $pass_comment;
    var $template;
    var $path;

    function gb_comment($path='') {
        global $HTTP_SERVER_VARS;
        if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']) && !empty($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = getenv("REMOTE_ADDR");
        }
        $this->gb = new guestbook_vars($path);
        $this->gb->getVars();
        $this->path = $path;
        $this->template =& $this->gb->template;
    }

    function is_valid_id() {
        $result = $this->gb->db->quickFetch("select id from ".$this->gb->table['data']." WHERE (id = '$this->id')", true);

        return ($result) ? true : false;
    }

    function comment_form() {
        global $GB_UPLOAD, $GB_PG, $_SESSION;
        if($_SESSION['OBJ_user']->username) {
            $G_NAME  = $_SESSION['OBJ_user']->username;
        } else {
            $G_NAME  = '';
        }
        $result = $this->gb->db->query( "select x.*, y.p_filename, y.width, y.height from ".$this->gb->db->tbl_prefix.$this->gb->table['data']." x left join ".$this->gb->db->tbl_prefix.$this->gb->table['pics']." y on (x.id=y.msg_id and y.book_id=2) WHERE (id = '$this->id')");
        $row  = $result->fetchrow();
        $LANG =& $this->gb->LANG;
        $VARS =& $this->gb->VARS;
        $DATE = $this->gb->DateFormat($row['date']);
        $MESSAGE = nl2br($row['comment']);
        $id = $this->id;
        $bgcolor = $this->gb->VARS['tb_color_1'];
        $COMMENT ='';
        if ($row['p_filename'] && ereg("^img-",$row['p_filename'])) {
            $img = new gb_image();
            $img->set_border_size($this->gb->VARS["img_width"], $this->gb->VARS["img_height"]);
            $new_img_size = $img->get_img_size_format($row['width'], $row['height']);
            if (file_exists("$this->path/$GB_UPLOAD/t_$row[p_filename]")) {
                $row['p_filename'] = "t_$row[p_filename]";
            }
            eval("\$USER_PIC = \"".$this->template->get_template($this->gb->GB_TPL['image'])."\";");
        } else {
            $USER_PIC = '';
        }
        if ($this->gb->VARS["smilies"] == 1) {
            $MESSAGE = $this->gb->emotion($MESSAGE);
        }
        if (!$row['location']) {
            $row['location'] = "-";
        }
        if ($row['url']) {
            eval("\$URL = \"".$this->template->get_template($this->gb->GB_TPL['url'])."\";");
        } else {
            $URL = '';
        }
        if ($row['icq'] && $this->gb->VARS["allow_icq"]==1) {
            eval("\$ICQ = \"".$this->template->get_template($this->gb->GB_TPL['icq'])."\";");
        } else {
            $ICQ = '';
        }
        if ($row['aim'] && $this->gb->VARS["allow_aim"]==1) {
            eval("\$AIM = \"".$this->template->get_template($this->gb->GB_TPL['aim'])."\";");
        } else {
            $AIM = '';
        }
        if ($row['email']) {
            eval("\$EMAIL = \"".$this->template->get_template($this->gb->GB_TPL['email'])."\";");
        } else {
            $EMAIL = '';
        }
        if ($this->gb->VARS["allow_gender"]==1) {
            $GENDER = ($row['gender']=="f") ? "&nbsp;<img src=\"$GB_PG[base_url]/img/female.gif\" width=\"12\" height=\"12\">" : "&nbsp;<img src=\"$GB_PG[base_url]/img/male.gif\" width=\"12\" height=\"12\">";
        } else {
            $GENDER = '';
        }
        if ($this->gb->VARS["show_ip"] == 1) {
            $hostname = ( eregi("^[-a-z_]+", $row['host']) ) ? "Host" : "IP";
            $real_host=chop($row['host']);
            $host="";
            if(!empty($real_host)){
                $host_arr=explode(".", $real_host);
                $h_count=count($host_arr);
                if($h_count > 1){
                    if(intval($host_arr[$h_count-1])!=0){
                        $host=substr($real_host,0,strrpos($real_host,".")).".---";
                    }
                    else{
                        $host = "---".strstr($real_host, ".");
                    }
                }
                else{
                    $host=$real_host;
                }
                $HOST = "$hostname: $host\n";
            } else {
                $HOST = "";
            }
        } else {
            $HOST='';
        }
        if ($this->gb->VARS["need_pass"]==1) {
            eval("\$COMMENT_PASS = \"".$this->template->get_template($this->gb->GB_TPL['com_pass'])."\";");
        } else {
            $COMMENT_PASS = '';
        }
        $GB_COMMENT = "#";
        eval("\$GB_ENTRY = \"".$this->template->get_template($this->gb->GB_TPL['entry'])."\";");
        eval("\$comment_html = \"".$this->template->get_template($this->gb->GB_TPL['header'])."\";");
        eval("\$comment_html .= \"".$this->template->get_template($this->gb->GB_TPL['com_form'])."\";");
        eval("\$comment_html .= \"".$this->template->get_template($this->gb->GB_TPL['footer'])."\";");
        return $comment_html;
    }

    function check_comment() {
        $this->comment = $this->gb->FormatString($this->comment);
        if (empty($this->comment)) {
            return $this->gb->gb_error($_SESSION['translate']->it("You forgot to fill in the Comment field. Please correct it and re-submit."));
        }
        $this->user = $this->gb->FormatString($this->user);
        if (empty($this->user)) {
            return $this->gb->gb_error($_SESSION['translate']->it("You forgot to fill in the Name field. Please correct it and re-submit."));
        }
        if (!$this->gb->CheckWordLength($this->user)) {
            return $this->gb->gb_error($_SESSION['translate']->it("One of the input fields does not seem to be valid."));
        }
        if (!$this->gb->CheckWordLength($this->comment)) {
            return $this->gb->gb_error($_SESSION['translate']->it("Your message contains some invalid words. Please correct it and re-submit."));
        }
        if ($this->gb->VARS["allow_html"]==0) {
            $this->comment = htmlspecialchars($this->comment);
        }
        if ($this->gb->VARS["agcode"]==1) {
            $this->comment = $this->gb->AGCode($this->comment);
        }
        if (!get_magic_quotes_gpc()) {
            $this->user = addslashes($this->user);
            $this->comment = addslashes($this->comment);
        }
        $this->user = htmlspecialchars($this->user);
        if ($this->gb->VARS["need_pass"]==1) {
            if (get_magic_quotes_gpc()) {
                $this->pass_comment = stripslashes($this->pass_comment);
            }
            if ($this->gb->VARS["comment_pass"] != "$this->pass_comment") {
                return $this->gb->gb_error($_SESSION['translate']->it("Sorry, but the password you entered was not correct."));
            }
        }
        if ($this->gb->VARS["censor"]==1) {
            $this->user = $this->gb->CensorBadWords($this->user);
            $this->comment = $this->gb->CensorBadWords($this->comment);
        }
        if ($this->gb->VARS["flood_check"]==1) {
            if ($this->gb->FloodCheck($this->ip)) {
               return $this->gb->gb_error($_SESSION['translate']->it("Sorry! We have flood-control activated. Please try again after a period of time elapses!"));
            }
        }
        if ($this->gb->VARS["banned_ip"]==1) {
            if ($this->gb->isBannedIp($this->ip)) {
                return $this->gb->gb_error($_SESSION['translate']->it("Your IP adress is banned!"));
            }
        }
        return 1;
    }

    function insert_comment() {
        $the_time = time();
        $host = @gethostbyaddr($this->ip);
        $this->gb->db->query("INSERT INTO ".$this->gb->table['com']." (id,name,comments,host,timestamp) VALUES ('$this->id','$this->user','$this->comment','$host','$the_time')", true);
    }

    function comment_action($action='') {
        global $GB_PG, $GLOBALS;
        if ($this->id && $this->is_valid_id() && $action==1) {
            $status = $this->check_comment();
            if ($status == 1) {
                $this->insert_comment();
                header("Location: $GB_PG[index]");
            } else {
                $GLOBALS['CNT_gbook']['content'] = $status;
            }
        } elseif ($this->id && $this->is_valid_id()) {
            $GLOBALS['CNT_gbook']['content'] = $this->comment_form();
        } else {
            header("Location: $GB_PG[index]");
        }
    }

}

?>