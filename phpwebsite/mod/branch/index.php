<?php
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

if (!$_SESSION["OBJ_user"]->isDeity())
  exit();

if (isset($_REQUEST["branch_op"])){
  $CNT_Branch_Main["content"] = NULL;
  $CNT_Branch_Panel["content"] = PHPWS_Branch::panel();

  switch ($_REQUEST["branch_op"]){

  case "unregisterBranch":
    if ($_REQUEST["confirm"] == "yes"){
      $_SESSION["removeBranch"]->unregisterBranch();
    } else

    PHPWS_Branch::manageBranch();
    $core->killSession("removeBranch");
    break;
    
  case "manageBranch":
    PHPWS_Branch::manageBranch();
  break;

  case "editBranchForm":
    $_SESSION["editBranch"] = new PHPWS_Branch($_REQUEST["form_branchName"]);
    $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Edit Branch") . " " . $_REQUEST["form_branchName"];
    $CNT_Branch_Main["content"] = $_SESSION["editBranch"]->editBranchForm();
    break;

  case "removeBranchForm":
    $_SESSION["removeBranch"] = new PHPWS_Branch($_REQUEST["form_branchName"]);
    $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Remove Branch") . " " . $_REQUEST["form_branchName"];
    $CNT_Branch_Main["content"] = $_SESSION["removeBranch"]->removeBranchForm();
    break;


  case "editBranchAction":
    if (isset($_SESSION["editBranch"])){
      if ($_SESSION["editBranch"]->editBranchAction()){
	$CNT_Branch_Main["content"] = "<div class=\"bg_light\"><b>" . $_SESSION["translate"]->it("Message") . ":</b> " . $_SESSION["translate"]->it("Update Complete") . ".</div><br />";
	PHPWS_Branch::manageBranch();
      }
      else {
	$CNT_Branch_Main["content"] .= $_SESSION["editBranch"]->listErrors();
	$_SESSION["editBranch"]->editBranchForm();
      }
    }
  break;


  case "firstPage":
    if (PHPWS_Branch::branchExists()){
      PHPWS_Branch::manageBranch();
    }
    else {
      $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Welcome to Branch Creator") . "!";
      $CNT_Branch_Main["content"] = $_SESSION["translate"]->it("Click on Create Branch to get started") . ".";
    }
  break;

  case "createBranch":
    $_SESSION["createBranch"] = new PHPWS_Branch;
    $_SESSION["createBranch"]->expertCreateBranch();
    

    if ($_SESSION["createBranch"]->error){
      $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Error");
      $CNT_Branch_Main["content"] .= $_SESSION["createBranch"]->listErrors();
    }
    break;

  case "expertBranchAction":
    if ($_SESSION["createBranch"]){
      if ($_SESSION["createBranch"]->processExpert()){
	if (!($content = $_SESSION["createBranch"]->writeBranch())){
	  $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Error");
	  $CNT_Branch_Main["content"] .= $_SESSION["createBranch"]->listErrors();
	} else {
	  $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Creating Branch");
	  $CNT_Branch_Main["content"] .= $content;
	  $CNT_Branch_Main["content"] .= "<h2>" . $_SESSION["translate"]->it("Branch Created Successfully") . "!</h2>";
	  $CNT_Branch_Main["content"] .= $_SESSION["translate"]->it("Try logging on") . "!<br />";
	  $CNT_Branch_Main["content"] .= "<a href=\"http://" . $_SESSION["createBranch"]->branchHttp . "\">" . $_SESSION["createBranch"]->branchHttp . "</a>";
	  $core->killSession("createBranch");
	}
      } else {
	$CNT_Branch_Main["content"] .= $_SESSION["createBranch"]->listErrors();
	$_SESSION["createBranch"]->expertCreateBranch();
      }
    }
    break;

  } //END branch_op switch
}
?>