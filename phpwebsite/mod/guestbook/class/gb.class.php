<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class guestbook {

    var $total;
    var $gb;
    var $template;
    var $path;

    function guestbook($path='') {
        $this->gb = new guestbook_vars($path);
        $this->gb->getVars();
        $this->total = 0;
        $this->path = $path;
        $this->template =& $this->gb->template;
    }

    function get_nav($entry=0) {
        global $HTTP_SERVER_VARS, $GB_PG;
        $self = (IS_MODULE && eregi("\?",$GB_PG["index"])) ? "$GB_PG[index]&entry=" : basename($HTTP_SERVER_VARS['PHP_SELF'])."?entry=";
        $next_page = $entry+$this->gb->VARS['entries_per_page'];
        $prev_page = $entry-$this->gb->VARS['entries_per_page'];
        $navigation = '';
        if ($prev_page >= 0) {
            $navigation = "   <img src=\"$GB_PG[base_url]/img/back.gif\" width=\"16\" height=\"14\"><a href=\"$self$prev_page\">".$_SESSION['translate']->it("Previous Page")."</a>\n";
        }
        if ($next_page < $this->total) {
            $navigation = $navigation."   &nbsp;&nbsp;<a href=\"$self$next_page\">".$_SESSION['translate']->it("Next Page")."</a><img src=\"$GB_PG[base_url]/img/next.gif\" width=\"16\" height=\"14\">\n";
        }
        return $navigation;
    }

    function show_entries($entry=0) {
        global $GB_PG;
        $LANG =& $this->gb->LANG;
        $VARS =& $this->gb->VARS;

        $result = $this->gb->db->quickFetch("select count(*) total from {$this->gb->db->tbl_prefix}".$this->gb->table['data']);
        $this->total = $result['total'];

        $TPL = $this->get_entries($entry,$this->gb->VARS["entries_per_page"]);
        $TPL['GB_TOTAL'] = $this->total;
        $TPL['GB_JUMPMENU'] = implode("\n",$this->generate_JumpMenu());
        $TPL['GB_TIME'] = $this->gb->DateFormat(time());
        $TPL['GB_NAVIGATION'] = $this->get_nav($entry);
        $TPL['GB_HTML_CODE'] = ($this->gb->VARS["allow_html"] == 1) ? $_SESSION['translate']->it("HTML code is enabled") : $_SESSION['translate']->it("HTML code is disabled");
        eval("\$guestbook_html = \"".$this->template->get_template($this->gb->GB_TPL['header'])."\";");
        eval("\$guestbook_html .= \"".$this->template->get_template($this->gb->GB_TPL['body'])."\";");
        eval("\$guestbook_html .= \"".$this->template->get_template($this->gb->GB_TPL['footer'])."\";");
        return $guestbook_html;
    }

    function generate_JumpMenu() {
        $menu_array[] = "<select name=\"entry\" class=\"select\">";
        $menu_array[] = "<option value=\"0\" selected>".$_SESSION['translate']->it("Guestbook")."</option>";
        if ($this->gb->VARS["entries_per_page"] < $this->total) {
            $remain = $this->total % $this->gb->VARS["entries_per_page"];
            $i = $this->total-$remain;
            while ($i > 0) {
                $num_max = $i;
                $num_min = $num_max-$this->gb->VARS["entries_per_page"];
                $num_min++;
                $menu_array[] = "<option value=\"$remain\">$num_min-$num_max</option>";
                $i = $num_min-1;
                $remain += $this->gb->VARS["entries_per_page"];
           }
        }
        $menu_array[] = "</select>";
        $menu_array[] = "<input type=\"submit\" value=\"".$_SESSION['translate']->it("Go")."\" class=\"input\">";
        return $menu_array;
    }

    function get_entries($entry,$last_entry) {
        global $GB_UPLOAD, $GB_PG;
        $img = new gb_image();
        $img->set_border_size($this->gb->VARS["img_width"], $this->gb->VARS["img_height"]);
        $LANG =& $this->gb->LANG;
        $id = $this->total-$entry;
        $HOST = '';
        $COMMENT = '';
        $GB_ENTRIES = '';
        $i=0;
        $template['entry'] = $this->template->get_template($this->gb->GB_TPL['entry']);
        $template['com'] = $this->template->get_template($this->gb->GB_TPL['com']);
        $template['url'] = $this->template->get_template($this->gb->GB_TPL['url']);
        $template['icq'] = $this->template->get_template($this->gb->GB_TPL['icq']);
        $template['aim'] = $this->template->get_template($this->gb->GB_TPL['aim']);
        $template['email'] = $this->template->get_template($this->gb->GB_TPL['email']);
        $template['image'] = $this->template->get_template($this->gb->GB_TPL['image']);
        $result1 = $this->gb->db->query("select x.*, y.p_filename, y.width, y.height, z.comments from ".$this->gb->db->tbl_prefix.$this->gb->table['data']." x left join ".$this->gb->db->tbl_prefix.$this->gb->table['pics']." y on (x.id=y.msg_id and y.book_id=2) left join ".$this->gb->db->tbl_prefix.$this->gb->table['com']." z on (x.id=z.id) group by x.id order by x.id desc limit $entry, $last_entry");
        while ($row = $result1->fetchrow()) {
            $DATE = $this->gb->DateFormat($row['date']);
            $MESSAGE = nl2br($row['comment']);
            if ($row['p_filename'] && ereg("^img-",$row['p_filename'])) {
                if (file_exists("$this->path/$GB_UPLOAD/t_$row[p_filename]")) {
                    $row['p_filename'] = "t_$row[p_filename]";
                }
                $new_img_size = $img->get_img_size_format($row['width'], $row['height']);
                eval("\$USER_PIC = \"".$template['image']."\";");
            } else {
                $USER_PIC = '';
            }
            if ($this->gb->VARS["smilies"] == 1) {
                $MESSAGE = $this->gb->emotion($MESSAGE);
            }
            if (!$row['location']) {
                $row['location'] = "-";
            }
            $bgcolor = ($i % 2) ? $this->gb->VARS["tb_color_2"] : $this->gb->VARS["tb_color_1"];
            $i++;
            if ($row['url']) {
                eval("\$URL = \"".$template['url']."\";");
            } else {
                $URL = '';
            }
            if ($row['icq'] && $this->gb->VARS["allow_icq"]==1) {
                eval("\$ICQ = \"".$template['icq']."\";");
            } else {
                $ICQ = '';
            }
            if ($row['aim'] && $this->gb->VARS["allow_aim"]==1) {
                eval("\$AIM = \"".$template['aim']."\";");
            } else {
                $AIM = '';
            }
            if ($row['email']) {
                eval("\$EMAIL = \"".$template['email']."\";");
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
                            $hostname = "IP";
                            $host=substr($real_host,0,strrpos($real_host,".")).".---";
                        }
                        else{
                            $hostname = "Host";
                            $host = "---".strstr($real_host, ".");
                        }
                    }
                    else{
                        $host=$real_host;
                    }
                }
                $HOST = "$hostname: $host\n";
            } else {
                $HOST = "";
            }
            if ($row['comments']) {

                $result = $this->gb->db->query("select * from ".$this->gb->db->tbl_prefix.$this->gb->table['com']." where id='$row[id]' order by com_id asc");
                while ($com = $result->fetchrow()) {
                    $com['comments'] = nl2br($com['comments']);
                    eval("\$COMMENT .= \"".$template['com']."\";");
                }
            }
            $GB_COMMENT = (IS_MODULE && eregi("\?",$GB_PG["comment"])) ? "$GB_PG[comment]&gb_id=$row[id]" : "$GB_PG[comment]?gb_id=$row[id]"; 
            eval("\$GB_ENTRIES .= \"".$template['entry']."\";");
            $COMMENT = "";
            $id--;
        }
        $TPL['GB_ENTRIES'] = $GB_ENTRIES;
        return $TPL;
    }

}

?>