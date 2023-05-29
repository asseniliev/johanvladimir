<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * ----------------------------------------------
 */
 // yk - seems to be more secure?
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}// yk
 
$include_path = dirname(__FILE__);
$ModName = basename($include_path);
include_once $include_path."/admin/config.inc.php";

$GB_PG["base_url"] .= "/mod/$ModName";

$GB_SELF = basename($HTTP_SERVER_VARS['PHP_SELF']);
$GB_PG["index"]    = "$GB_SELF?module=$ModName";
$GB_PG["addentry"] = "$GB_SELF?module=$ModName&agbook=addentry";
$GB_PG["admin"]    = "$GB_SELF?module=$ModName&agbook=admin";
$GB_PG["comment"]  = "$GB_SELF?module=$ModName&agbook=comment";

if (isset($_REQUEST["module"]) && $_REQUEST["module"] == $ModName){

    if (substr_count($_SELF,"/mod/")) {
   		$url="<meta http-equiv=\"refresh\" content=\"0;URL=../../index.php?module=$ModName\">";
        printf("<html><head>%s</head><body></body></html>", $url);
        exit();
    } 

    ob_start();

    if (!isset($_REQUEST["agbook"])) {
        $agbook = '';
    }else {
        $agbook =$_REQUEST["agbook"];
    }
    include_once $include_path."/class/template.class.php";
    include_once $include_path."/class/image.class.php";
    include_once $include_path."/class/vars.class.php";

    switch ($agbook) {


        case "admin":
            include $include_path."/admin.php";
            break;

        case "comment":
            include_once $include_path."/class/comment.class.php";
		    $gb_com = new gb_comment($include_path);
            $gb_com->id = (isset($HTTP_GET_VARS["gb_id"])) ? $HTTP_GET_VARS["gb_id"] : '';
            $gb_com->id = (isset($HTTP_POST_VARS["gb_id"])) ? $HTTP_POST_VARS["gb_id"] : $gb_com->id;
            $gb_com->comment = (isset($HTTP_POST_VARS["comment"])) ? $HTTP_POST_VARS["comment"] : '';
            $gb_com->user = (isset($HTTP_POST_VARS["gb_user"])) ? $HTTP_POST_VARS["gb_user"] : '';
            $gb_com->pass_comment = (isset($HTTP_POST_VARS["pass_comment"])) ? $HTTP_POST_VARS["pass_comment"] : '';
            $gb_action = (isset($HTTP_POST_VARS["gb_comment"])) ? $HTTP_POST_VARS["gb_comment"] : '';
            $GLOBALS['CNT_gbook']['title'] = $_SESSION['translate']->it("Guestbook").' - '.$_SESSION['translate']->it("Comments");
            $gb_com->comment_action($gb_action);

            break;

        case "addentry":
            include_once $include_path."/class/add.class.php";
            $gb_post = new addentry($include_path);
            $GLOBALS['CNT_gbook']['title'] = $_SESSION['translate']->it("Sign the Guestbook");
            if (isset($HTTP_POST_VARS["gb_action"])) {
                $gb_post->name = (isset($HTTP_POST_VARS["gb_name"])) ? $HTTP_POST_VARS["gb_name"] : '';
                $gb_post->email = (isset($HTTP_POST_VARS["gb_email"])) ? $HTTP_POST_VARS["gb_email"] : '';
                $gb_post->url = (isset($HTTP_POST_VARS["gb_url"])) ? $HTTP_POST_VARS["gb_url"] : '';
                $gb_post->comment = (isset($HTTP_POST_VARS["gb_comment"])) ? $HTTP_POST_VARS["gb_comment"] : '';
                $gb_post->location = (isset($HTTP_POST_VARS["gb_location"])) ? $HTTP_POST_VARS["gb_location"] : '';
                $gb_post->icq = (isset($HTTP_POST_VARS["gb_icq"])) ? $HTTP_POST_VARS["gb_icq"] : '';
                $gb_post->aim = (isset($HTTP_POST_VARS["gb_aim"])) ? $HTTP_POST_VARS["gb_aim"] : '';
                $gb_post->gender = (isset($HTTP_POST_VARS["gb_gender"])) ? $HTTP_POST_VARS["gb_gender"] : '';
                $gb_post->userfile = (isset($HTTP_POST_FILES["userfile"]["tmp_name"]) && $HTTP_POST_FILES["userfile"]["tmp_name"] != "") ? $HTTP_POST_FILES : '';
                $gb_post->user_img = (isset($HTTP_POST_VARS["gb_user_img"])) ? $HTTP_POST_VARS["gb_user_img"] : '';
                $gb_post->preview = (isset($HTTP_POST_VARS["gb_preview"])) ? 1 : 0;
                $gb_post->private = (isset($HTTP_POST_VARS["gb_private"])) ? 1 : 0;
                $GLOBALS['CNT_gbook']['content'] = $gb_post->process($HTTP_POST_VARS["gb_action"]);

            } else {
                $GLOBALS['CNT_gbook']['content'] = $gb_post->process();
            }

            break;

        default:
            include_once $include_path."/class/gb.class.php";
            $gb = new guestbook($include_path);
            $entry = (isset($HTTP_GET_VARS["entry"])) ? $HTTP_GET_VARS["entry"] : 0;
            $entry = (isset($HTTP_POST_VARS["entry"])) ? $HTTP_POST_VARS["entry"] : $entry;
            $GLOBALS['CNT_gbook']['title'] = $_SESSION['translate']->it("Guestbook");
            $GLOBALS['CNT_gbook']['content'] = $gb->show_entries($entry);
    }
    ob_end_flush();

} else {
    include_once $include_path."/class/template.class.php";
    include_once $include_path."/class/vars.class.php";

    $gb = new guestbook_vars($include_path);
    $gb->getVars();

    if ($gb->VARS["show_sidebox"]) {
        $gb_box = '<img src="'.$GB_PG[base_url].'/img/sign.gif" width="9" height="12" /> <a href="'.$GB_PG[addentry].'">'.$_SESSION['translate']->it("Sign the Guestbook").'</a>';

        $gb_total = $gb->db->quickFetch("select count(*) total from {$gb->db->tbl_prefix}".$gb->table['data']);

        $menu_array[] = "<form method=\"post\" action=\"".$GB_PG['index']."\">";
        $menu_array[] = "<select name=\"entry\" class=\"select\">";
        $menu_array[] = "<option value=\"0\" selected>".$_SESSION['translate']->it("Guestbook")."</option>";
        if ($gb->VARS["entries_per_page"] < $gb_total['total']) {
            $remain = $gb_total['total'] % $gb->VARS["entries_per_page"];
            $i = $gb_total['total']-$remain;
            while ($i > 0) {
                $num_max = $i;
                $num_min = $num_max-$gb->VARS["entries_per_page"];
                $num_min++;
                $menu_array[] = "<option value=\"$remain\">$num_min-$num_max</option>";
                $i = $num_min-1;
                $remain += $gb->VARS["entries_per_page"];
           }
        }
        $menu_array[] = "</select>";
        $menu_array[] = "<input type=\"submit\" value=\"".$_SESSION['translate']->it("Go")."\" class=\"input\"></form>";
        $gb_box .= implode("\n",$menu_array);

        $GLOBALS['CNT_gbook_box']['title'] = $_SESSION['translate']->it("Guestbook");
        $GLOBALS['CNT_gbook_box']['content'] = $gb_box;
    }
}

?>