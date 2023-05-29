<?php
/**
 * @version $Id: boost.php,v 1.45 2005/06/20 18:28:39 darren Exp $
 */
$mod_title = "phatform";
$mod_pname = "Form Generator";
$mod_directory = "phatform";
$mod_filename = "index.php";
$allow_view = "all";
$priority = 50;
$active = "on";
$version = "2.7.1";
$admin_mod = 1;

$mod_class_files = array("Form.php",
			 "FormManager.php",
			 "Report.php",
			 "Element.php",
			 "Checkbox.php",
			 "Dropbox.php",
			 "Multiselect.php",
			 "Radiobutton.php",
			 "Textarea.php",
			 "Textfield.php");

$mod_sessions = array("PHAT_FormManager");
$init_object = array("PHAT_FormManager"=>"PHAT_FormManager");

?>