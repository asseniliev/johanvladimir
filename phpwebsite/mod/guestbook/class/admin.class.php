<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class gb_admin {

    var $db;
    var $SELF;
    var $VARS;
    var $table;
    var $content;
    var $base_dir;
    var $base_url;

    function gb_admin($include_path, $url) {
        global $HTTP_SERVER_VARS, $GLOBALS;

        $this->base_dir=$include_path;
        $this->base_url=$url;
        $GLOBALS['CNT_gbook']['title'] = '<font size="2" face="Verdana, Arial"><b>'.$_SESSION['translate']->it("Guestbook-Admin").'</b></font>';

    }

    function get_updated_vars() {
        $_VARS = $this->db->sqlSelect($this->table['cfg']);
        $this->VARS =& $_VARS[0];
    }

    function NoCacheHeader() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
    }

    function show_panel($panel) {
        global $smilie_list, $smilie_data, $GLOBALS;
        $this->NoCacheHeader();
        include_once $this->base_dir."/admin/panel_$panel.php";
        $GLOBALS['CNT_gbook']['content'] = $this->content;
    }

    function scan_smilie_dir() {
        $smilies = '';
        $old_dir = getcwd();
        chdir($this->base_dir."/img/smilies");
        $hnd = opendir(".");
        while ($file = readdir($hnd)) {
            if(is_file($file)) {
                if ($file != "." && $file != "..") {
                    if (ereg(".gif|.jpg|.png|.jpeg",$file)) {
                        $smilie_list[] = $file;
                    }
                }
            }
        }
        closedir($hnd);
        if (isset($smilie_list)) {
            asort($smilie_list);
            for ($i=0;$i<sizeof($smilie_list);$i++) {
                $size = GetImageSize($smilie_list[$i]);
                if (is_array($size)) {
                    $smilies[$smilie_list[$i]] = "<img src=\"{$this->base_url}img/smilies/$smilie_list[$i]\" $size[3]>";
                }
            }
        }
        chdir($old_dir);
        return $smilies;
    }

    function show_entry($tbl="gb") {
        global $entry, $record, $GB_UPLOAD, $GLOBALS;
        if ($tbl=="priv") {
            $gb_tbl = $this->table['priv'];
            $book_id = 1;
        } else {
            $gb_tbl = $this->table['data'];
            $tbl="gb";
            $book_id = 2;
        }
        $entries_per_page = $this->VARS["entries_per_page"];
        if(!isset($entry)) {
            $entry = 0;
        }
        if(!isset($record)) {
            $record = 0;
        }
        $next_page = $entry+$entries_per_page;
        $prev_page = $entry-$entries_per_page;
        $result1 = $this->db->quickFetch("select count(*) total from {$this->db->tbl_prefix}$gb_tbl");
        $total = $result1['total'];
        if ($record > 0 && $record <= $total) {
            $entry = $total-$record;
            $next_page = $entry+$entries_per_page;
            $prev_page = $entry-$entries_per_page;
        }
        $result = $this->db->query("select x.*, y.p_filename, y.width, y.height from {$this->db->tbl_prefix}$gb_tbl x left join ".$this->db->tbl_prefix.$this->table['pics']." y on (x.id=y.msg_id and y.book_id=$book_id) order by x.id desc limit $entry, $entries_per_page");

        $img = new gb_image();
        $img->set_border_size($this->VARS["img_width"], $this->VARS["img_height"]);
        $this->NoCacheHeader();
        include_once $this->base_dir."/admin/panel_easy.php";
        $GLOBALS['CNT_gbook']['content'] = $this->content;
    }

    function del_entry($entry_id,$tbl="gb") {
        global $GB_UPLOAD;
        switch ($tbl) {
            case "gb" :
                $result = $this->db->quickFetch("select p_filename from ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=2)",true);
                if ($result["p_filename"]) {
                    if (file_exists($this->base_dir."/$GB_UPLOAD/$result[p_filename]")) {
                        unlink ($this->base_dir."/$GB_UPLOAD/$result[p_filename]");
                    }
                    if (file_exists($this->base_dir."/$GB_UPLOAD/t_$result[p_filename]")) {
                        unlink ($this->base_dir."/$GB_UPLOAD/t_$result[p_filename]");
                    }
                }
                $this->db->query("DELETE FROM ".$this->table['data']." WHERE (id = '$entry_id')",true);
                $this->db->query("DELETE FROM ".$this->table['com']." WHERE (id = '$entry_id')",true);
                $this->db->query("DELETE FROM ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=2)",true);
                break;

            case "priv" :
                $result = $this->db->quickFetch("select p_filename from ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=1)",true);
                if ($result["p_filename"]) {
                    if (file_exists($this->base_dir."/$GB_UPLOAD/$result[p_filename]")) {
                        unlink ($this->base_dir."/$GB_UPLOAD/$result[p_filename]");
                    }
                    if (file_exists($this->base_dir."/$GB_UPLOAD/t_$result[p_filename]")) {
                        unlink ($this->base_dir."/$GB_UPLOAD/t_$result[p_filename]");
                    }
                }
                $this->db->query("DELETE FROM ".$this->table['priv']." WHERE (id = '$entry_id')", true);
                $this->db->query("DELETE FROM ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=1)", true);
                break;

            case "com" :
                $this->db->query("DELETE FROM ".$this->table['com']." WHERE (com_id = '$entry_id')", true);
                break;
        }
    }

    function update_record($entry_id,$tbl="gb") {
        global $HTTP_POST_VARS;
        $gb_tbl = ($tbl=="priv") ? $this->table['priv'] : $this->table['data'];
        if (!get_magic_quotes_gpc() ) {
            while (list($var, $value)=each($HTTP_POST_VARS)) {
                $HTTP_POST_VARS[$var]=addslashes($value);
            }
        }
        reset($HTTP_POST_VARS);
        while (list($var, $value)=each($HTTP_POST_VARS)) {
            $HTTP_POST_VARS[$var]=trim($value);
        }
        if (!eregi(".+@[-a-z0-9_]+", $HTTP_POST_VARS['email'])) {
            $HTTP_POST_VARS['email'] = '';
        }
        if (!eregi("^http://[-a-z0-9_]+", $HTTP_POST_VARS['url'])) {
            $HTTP_POST_VARS['url'] = '';
        }
        $sqlquery= "UPDATE {$this->db->tbl_prefix}$gb_tbl set name='$HTTP_POST_VARS[name]', email='$HTTP_POST_VARS[email]', gender='$HTTP_POST_VARS[gender]', url='$HTTP_POST_VARS[url]', location='$HTTP_POST_VARS[location]', ";
        $sqlquery.="host='$HTTP_POST_VARS[host]', browser='$HTTP_POST_VARS[browser]', comment='$HTTP_POST_VARS[comment]', icq='$HTTP_POST_VARS[icq]', aim='$HTTP_POST_VARS[aim]' WHERE (id = '$entry_id')";
        $this->db->query($sqlquery);
    }

    function show_form($entry_id,$tbl="gb") {
        global $record;
        $gb_tbl = ($tbl=="priv") ? $this->table['priv'] : $this->table['data'];
        $result = $this->db->sqlSelect($gb_tbl,'id',$entry_id);
        $row = $result[0];
        for(reset($row); $key=key($row); next($row)) {
            $row[$key] = htmlspecialchars($row[$key]);
        }
        $this->NoCacheHeader();
        include_once $this->base_dir."/admin/panel_edit.php";
        $GLOBALS['CNT_gbook']['content'] = $this->content;
    }

    function edit_template($tpl_name,$tpl_save) {
        global $HTTP_POST_VARS, $GB_TPL;
        $this->NoCacheHeader();
        $filename = $this->base_dir."/templates/$tpl_name";
        if (file_exists("$filename") && $tpl_name != '') {
            if ($tpl_save == "update") {
                if (get_magic_quotes_gpc()) {
                   $HTTP_POST_VARS['gb_template'] = stripslashes($HTTP_POST_VARS['gb_template']);
                }
                $fd = fopen ($filename, "w");
                fwrite($fd,$HTTP_POST_VARS['gb_template']);
                $gb_template = $HTTP_POST_VARS['gb_template'];
            } else {
                $fd = fopen ($filename, "r");
                $gb_template = fread ($fd, filesize ($filename));
            }
            fclose ($fd);
        } else {
            $gb_template ='';
        }
        include_once $this->base_dir."/admin/panel_template.php";
        $GLOBALS['CNT_gbook']['content'] = $this->content;
    }

    function show_settings($cat) {
        global $GLOBALS;
        $badwords = $this->db->sqlSelect($this->table['words']);
        $banned_ips = $this->db->sqlSelect($this->table['ban']);

        $this->NoCacheHeader();
        if ($cat == "general") {
            include_once $this->base_dir."/admin/panel_main.php";
        } elseif ($cat == "style") {
            include_once $this->base_dir."/admin/panel_style.php";
        }
        $GLOBALS['CNT_gbook']['content'] = $this->content;
    }

}

?>