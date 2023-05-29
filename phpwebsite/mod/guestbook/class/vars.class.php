<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class guestbook_vars {

    var $VARS;
    var $LANG;
    var $table = array();
    var $GB_TPL = array();
    var $SMILIES;
    var $template;
    var $db;

    function guestbook_vars($path='') {
        global $GB_TPL, $GB_TBL, $GLOBALS;
        $this->table =& $GB_TBL;
        $this->GB_TPL =& $GB_TPL;
        $this->db =& $GLOBALS["core"];
        $this->template = new gb_template($path);
    }

    function getVars() {
        global $HTTP_COOKIE_VARS;

        $_VARS = $this->db->sqlSelect($this->table['cfg']);
        $this->VARS =& $_VARS[0];
        if (isset($HTTP_COOKIE_VARS['lang']) && !empty($HTTP_COOKIE_VARS['lang'])) {
            $this->template->set_lang($HTTP_COOKIE_VARS['lang']);
        } else {
            $this->template->set_lang($this->VARS["lang"]);
        }
        $this->LANG =& $this->template->get_content();
        return $this->VARS;
    }

    function emotion($message) {
        global $GB_PG;
        if (!isset($this->SMILIES)) {
            $result = $this->db->query("SELECT * FROM ".$this->table['smile'], true);
            while ($row = $result->fetchrow()) {
                $this->SMILIES[$row['s_code']] = "<img src=\"$GB_PG[base_url]/img/smilies/".$row['s_filename']."\" width=\"".$row['width']."\" height=\"".$row['height']."\">";
            }
        }
        if (isset($this->SMILIES)) {
            for(reset($this->SMILIES); $key=key($this->SMILIES); next($this->SMILIES)) {
                $message = str_replace("$key",$this->SMILIES[$key],$message);
            }
        }
        return $message;
    }

    function DateFormat($timestamp) {
        $timestamp += $this->VARS["offset"]*3600;
        list($wday,$mday,$month,$year,$hour,$minutes,$hour12,$ampm) = split("( )",date("w j n Y H i h A",$timestamp));
        if ($this->VARS["tformat"] == "AMPM") {
            $newtime = " $hour12:$minutes $ampm";
        } else {
            $newtime = " $hour:$minutes";
        }
        if ($this->VARS["dformat"] == "USx") {
            $newdate = " $month-$mday-$year";
        } elseif ($this->VARS["dformat"] == "US") {
            $month -= 1;
            $newdate = $this->template->WEEKDAY[$wday].", ".$this->template->MONTHS[$month]." $mday, $year";
        } elseif ($this->VARS["dformat"] == "Euro") {
            $month -= 1;
            $newdate = $this->template->WEEKDAY[$wday].", $mday. ".$this->template->MONTHS[$month]." $year";
        } else {
            $newdate = "$mday.$month.$year";
        }
        return ($newdate=$newdate.$newtime);
    }

    function AGCode($string) {
        $string=eregi_replace("\\[img\\](http://[^\\[]+)\\[/img\\]","<img src=\"\\1\" border=0>",$string);
        $string=eregi_replace("\\[b\\]([^\\[]*)\\[/b\\]","<b>\\1</b>",$string);
        $string=eregi_replace("\\[i\\]([^\\[]*)\\[/i\\]","<i>\\1</i>",$string);
        $string=eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=\"mailto:\\1\">\\1</a>",$string);
        $string=eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]","<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",$string);
        $string=eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=\"_blank\">\\1</a>",$string);
        $string=eregi_replace("\\[url=http://([^\\[]+)\\]([^\\[]*)\\[/url\\]","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$string);
        return $string;
    }

    function FormatString($strg) {
        $strg = trim($strg);
        $strg = ereg_replace("[ ]+", " ", $strg);
        return $strg;
    }

    function CheckWordLength($strg) {
        $word_array = split ("[ |\n]",$strg);
        for ($i=0;$i<sizeof($word_array);$i++) {
            if (ereg("^\\[[a-z]{3,5}\\].+\\]",$word_array[$i])) {
                if (strlen($word_array[$i]) > 200) {
                    return false;
                }
            } elseif (strlen($word_array[$i]) > $this->VARS["max_word_len"]) {
                return false;
            }
        }
        return true;
    }

    function isBannedIp($ip) {
        $result = $this->db->query("SELECT * from ".$this->table['ban'], true);
        if (!$result) {
            return false;
        }
        while ($row = $result->fetchrow()) {
            if (ereg("^$row[ban_ip]",$ip)) {
                return true;
            }
        }
        return false;

    }

    function FloodCheck($ip) {
        $the_time = time()-$this->VARS["flood_timeout"];
        $this->db->sqlDelete($this->table['ip'],'timestamp',$the_time.'<');

        $_FC = $this->db->sqlSelect($this->table['ip'],'guest_ip',$ip);

        return ($_FC) ? true : false;
    }

    function CensorBadWords($strg) {
        $replace = "#@*%!";
        $result = $this->db->query("SELECT * from ".$this->table['words'], true);
        while ($row = $result->fetchrow()) {
            $strg = eregi_replace($row["word"], $replace, $strg);
        }
        return $strg;
    }

    function gb_error($ERROR) {
        global $GB_PG;
        $LANG =& $this->LANG;
        $VARS =& $this->VARS;
        eval("\$error_html = \"".$this->template->get_template($this->GB_TPL['header'])."\";");
        eval("\$error_html .= \"".$this->template->get_template($this->GB_TPL['error'])."\";");
        eval("\$error_html .= \"".$this->template->get_template($this->GB_TPL['footer'])."\";");
        return $error_html;
    }

}

?>