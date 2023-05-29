<?php

/**
 * @author: Darren Greene
 * @version $Id: convert.php,v 1.3 2004/03/19 20:33:22 darren Exp $
 * 
 * This file contains a conversion script to transfer your phatfile files to the new document 
 * module.  If you have changed the permissions of the directories files/phatfile or files/documents 
 * you will need to make sure that the server has read access for files/phatfile and server write access 
 * for files/documents.
 *
 * To run script:
 * Point your browser to:  [YOUR_PHPWS_WEB_ADDRESS]/mod/documents/convert/convert.php
 * Follow the directions on the screen.
 *
 */

$GLOBALS["hub_dir"] = "../../../";               // needs to point to the main hub directory of your site

/*  DO NOT EDIT BELOW THIS LINE */

$GLOBALS["curr_dir"] = $GLOBALS["hub_dir"];     // current working directory, will change for branches

/* initialization of core and session */
define("PHPWS_SOURCE_DIR", $GLOBALS["hub_dir"]);
require_once($GLOBALS["hub_dir"] . "core/Core.php");
require_once($GLOBALS["hub_dir"] . "core/Form.php");
session_start();

$_SESSION["core"] = new PHPWS_Core(NULL, $GLOBALS["hub_dir"]);
$GLOBALS["core"] = &$_SESSION["core"];

print_html_header();      // echo header and other html tags

if(!isset($_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"]))
     $_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"] = FALSE;

if(isset($_REQUEST["begin"]) && $_REQUEST["begin"] == "true") 
{

  /* check to see if user has run script before */
  if(isset($_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"]) && $_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"] != "runOnce") {
    $_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"] = "runOnce";
    begin();

    echo "<a href='../../../index.php'>Return to mainsite...</a>";

  } else {
    attempted_second_run();
  }
} else {
  
  if(isset($_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"]) && $_SESSION["PHATFILE_CONVERT_TO_DOCUMENTS"] != "runOnce")
    show_form();  // display list of sites to convert
  else
    attempted_second_run();      
}

print_html_footer(); 

function print_html_header() {
  echo "<html><head><title>Phatfile to Document Manager - Conversion Utility</title></head><body>";
}

function show_form() {
  echo "<table width='50%'><tr><td>Use this conversion script to convert your documents from the phatfile module to the new documents module.  ".
       "A new category will be created in your documents modules labeled 'Converted Phatfile Documents', which can be ".
       "changed later by click 'Edit' under the actions column.</td></tr></table><br />";

  echo "<font color='red'>The documents modules should already be installed on the sites to run the conversion on.</font><br />";

  if($any_branches = getBranches()) {
    
    $elements[0] = "<br />Select the sites to convert from the list below:<br />";
    $elements[0] .= PHPWS_Form::formHidden("begin", "true");
    $elements[0] .= PHPWS_Form::formHidden("branches", "true");
    $elements[0] .= PHPWS_Form::formCheckBox("PHPWS_hub", "true", NULL, NULL, "Hub (Main Site)") . "<br />";
    $elements[0] .= "<br />Branch Sites:<br />";
    $elements[0] .= branch_form($any_branches);
    $elements[0] .= "<br />";
    $elements[0] .= PHPWS_Form::formSubmit("Begin Conversion", $name=NULL, $class=NULL);
    $content = PHPWS_Form::makeForm("documents_conversion", "./convert.php", $elements, "post", FALSE, TRUE);
    echo $content;
  } else {
    echo "To begin the conversion, <a href='./convert.php?begin=true'>click here</a>.";
  }
}

function attempted_second_run() {
   echo "<br /><font color='red'>Warning...The conversion has already been run at least once and should not be run multiple times.";
   echo " If you wish to run the script again please close all open browser windows and try accessing this script again.</font>";
   echo "<br /><br />Stopping execution of stript...<br /><br />";
   
   echo "<a href='../../../index.php'>Return to mainsite...</a>";
}

function branch_form($branchData) {
  $b_content = "";

  for($i=0; $i < sizeof($branchData); $i++) 
    {      
      $b_content .= PHPWS_Form::formCheckBox($branchData[$i]["branchName"], "true", NULL, NULL, $branchData[$i]["branchName"]) . "<br />";
    }

  return $b_content;
}

function begin() {
  $run = false;

  echo "<br /><font size=\"4\"><b>Progess:</b></font><br />";

  if(isset($_REQUEST["PHPWS_hub"])) {
    // core should already be set to the hub

    echo "<font size=\"4\"><i>Converting hub...</i></font><br />";
    if(check_eligibility())
      convert(); 
    $run = true;
  } 

  if (isset($_REQUEST["branches"]) && $_REQUEST["branches"] == "true") {
    // handle branches
    for($i=0; $i < sizeof($_SESSION["PTD_branches"]); $i++) {
      if(isset($_REQUEST[$_SESSION["PTD_branches"][$i]["branchName"]])) {
	branch_switch($_SESSION["PTD_branches"][$i]["branchName"]);      

	echo "<font size=\"4\"><i>Converting ". $_SESSION["PTD_branches"][$i]["branchName"] . "...</i></font><br />";
	if(check_eligibility())
	  convert();

	$run = true;
      }
    }
  } 
  
  echo "<br /><br />";

  if($run) {
    echo "<font size='4px'><b>The Conversion is now Complete.</b></font><br /><br />";
  } else {
    echo "<b>No sites were selected to convert.</b><br /><br /><br />";
  }

}

/***
 * Invarient: The core should be initialized with the site you wish to convert.
 *            Need to change if dealing with a branch.
 */
function convert() {
  $num_to_convert    = $GLOBALS["core"]->getOne("SELECT COUNT(id) FROM " . $GLOBALS["core"]->tbl_prefix . "mod_phatfile_files");
  $num_ex_documents  = $GLOBALS["core"]->getOne("SELECT MAX(id) FROM "   . $GLOBALS["core"]->tbl_prefix . "mod_documents_docs");
  $num_ex_files      = $GLOBALS["core"]->getOne("SELECT MAX(id) FROM "   . $GLOBALS["core"]->tbl_prefix . "mod_documents_files");

  // update mod_documents_doc_seq
  if($GLOBALS['core']->sqlTableExists($GLOBALS["core"]->tbl_prefix . "mod_documents_docs_seq", FALSE)) {
    $curr_seq_docs     = $GLOBALS["core"]->getOne("SELECT MAX(id) FROM "  . $GLOBALS["core"]->tbl_prefix . "mod_documents_docs_seq");

    $doc_seq["id"] = $curr_seq_docs + 1;
    if(!$GLOBALS['core']->sqlUpdate($doc_seq, "mod_documents_docs_seq", "id", $curr_seq_docs)) {
      echo "The database insert updating mod_documents_files_seq was unsuccessful.";
    }
  }

  // update mod_documents_files_seq
  if($GLOBALS['core']->sqlTableExists($GLOBALS["core"]->tbl_prefix . "mod_documents_files_seq", FALSE)) {
    $curr_seq_files    = $GLOBALS["core"]->getOne("SELECT MAX(id) FROM "  . $GLOBALS["core"]->tbl_prefix . "mod_documents_files_seq");

    $files_seq["id"] = $curr_seq_files + $num_to_convert;
    if(!$GLOBALS['core']->sqlUpdate($files_seq, "mod_documents_files_seq", "id", $curr_seq_files)) {
      echo "The database insert updating mod_documents_files_seq was unsuccessful.";
    }
  }

  // create document folder to put files in
  $sql_nfolder["owner"]       = NULL;
  $sql_nfolder["editor"]      = NULL;
  $sql_nfolder["ip"]          = NULL;
  $sql_nfolder["created"]     = time();
  $sql_nfolder["updated"]     = time();
  $sql_nfolder["hidden"]      = 0;
  $sql_nfolder["approved"]    = 1;
  $sql_nfolder["label"]       = "Converted Phatfile Documents";
  $sql_nfolder["description"] = NULL;
  $sql_nfolder["full_text"]   = NULL;

  if($new_doc_id = $GLOBALS['core']->sqlInsert($sql_nfolder, "mod_documents_docs", FALSE, TRUE, FALSE)) {

  } else {
    echo "The database insert for creating document category was unsuccessful.";
  }      

  // copy all entries for phatfile
  $sql = "SELECT * FROM ". $GLOBALS["core"]->tbl_prefix . "mod_phatfile_files";
  $GLOBALS["core"]->setFetchMode("assoc");
  $result = $GLOBALS["core"]->getAll($sql);

  for($i=0; $i < sizeof($result); $i++) 
    {      
      $name =  $result[$i]["label"];
      $name = str_replace(" ", "_", $name);
      $file = $GLOBALS["curr_dir"]."/files/documents/".$name;

      // do a quick check to make sure the file doesn't already exist
      if(is_file($file)) {
	$name = time() . "_" . str_replace(" ", "_", $result[$i]["label"]);
	$file = $GLOBALS["curr_dir"]."/files/documents/" . $name;
      }

      $sql_ifile["owner"]   = $result[$i]["owner"];
      $sql_ifile["ip"]      = $result[$i]["ip"];
      $sql_ifile["created"] = time();
      $sql_ifile["doc"]     = $new_doc_id;
      $sql_ifile["name"]    = $name;
      $sql_ifile["size"]    = $result[$i]["size"];
      $sql_ifile["type"]    = $result[$i]["type"];

      if(!$GLOBALS['core']->sqlInsert($sql_ifile, "mod_documents_files", FALSE, TRUE, FALSE))
	{
	  echo "The database insert for file was unsuccessful.\n";
	}      
      
      copy($GLOBALS["curr_dir"]."files/phatfile/".$result[$i]["label"], $file);
    }
}

function branch_switch($branchName) {
  for($i=0; $i < sizeof($_SESSION["PTD_branches"]); $i++) {
    if($_SESSION["PTD_branches"][$i]["branchName"] == $branchName) {
      $GLOBALS["curr_dir"] = $_SESSION["PTD_branches"][$i]["branchDir"];
      $GLOBALS["IDhash"] = $_SESSION["PTD_branches"][$i]["IDhash"];

      $_SESSION["core"] = new PHPWS_Core($_SESSION["PTD_branches"][$i]["branchName"], $GLOBALS["hub_dir"]);
      $GLOBALS["core"] = &$_SESSION["core"];
      break;
    }
  }
}

function check_eligibility() {
  if(!$GLOBALS['core']->sqlTableExists($GLOBALS["core"]->tbl_prefix . "mod_phatfile_files", FALSE)) {
    echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;Missing phatfile table: mod_phatfile_files</font><br />";
    return false;

  } else {
    if($GLOBALS["core"]->getOne("SELECT COUNT(id) FROM " . $GLOBALS["core"]->tbl_prefix . "mod_phatfile_files") == 0) {
      echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;No phatfile documents were found.</font><br />";
      return false;

    }
  }

  if(!$GLOBALS['core']->sqlTableExists($GLOBALS["core"]->tbl_prefix . "mod_documents_docs", FALSE)) {
    echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;Missing documents table: mod_documents_docs</font><br />";
    return false;
  }

  if(!$GLOBALS['core']->sqlTableExists($GLOBALS["core"]->tbl_prefix . "mod_documents_files", FALSE)) {
    echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;Missing documents table: mod_documents_files</font><br />";
    return false;
  }

  if(!is_dir($GLOBALS["curr_dir"]."files/phatfile/")) {
    echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;Missing phatfile directory: " . 
      $GLOBALS["cur_dir"]."files/phatfile/" ."</font><br />";
    return false;
  }

  if(!is_dir($GLOBALS["curr_dir"]."files/documents/")) {
    echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;Missing documents directory: " . 
      $GLOBALS["cur_dir"]."files/documents/" ."</font><br />";
    return false;
  }

  if(!is_writeable($GLOBALS["curr_dir"]."files/documents/")) {
    echo "<font color=\"red\">&nbsp;&nbsp;&nbsp;Error:&nbsp;&nbsp;Directory not writable by web server: " . 
      $GLOBALS["cur_dir"]."files/documents/" ."</font><br />";
    return false;
  }

  return true;
}

function getBranches() {
 $sql = "SELECT * FROM ". $GLOBALS["core"]->tbl_prefix . "branch_sites";
 $GLOBALS["core"]->setFetchMode("assoc");
 $result = $GLOBALS["core"]->getAll($sql);

 if(sizeof($result) == 0) {
   return NULL;
 } else {
   $_SESSION["PTD_branches"] = $result;
   return $result;
 }
}

function print_html_footer() {
  echo "</body></html>";
}

?>