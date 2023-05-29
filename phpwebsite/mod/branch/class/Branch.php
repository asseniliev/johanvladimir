<?php

require_once(PHPWS_SOURCE_DIR . "core/EZform.php");
require_once(PHPWS_SOURCE_DIR . "core/Form.php");
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
require_once(PHPWS_SOURCE_DIR . "core/Array.php");
require_once(PHPWS_SOURCE_DIR . "core/File.php");

require_once(PHPWS_SOURCE_DIR . "mod/boost/class/Boost.php");

class PHPWS_Branch {
  
  var $error;
  var $branchName;
  var $IDhash;
  var $configFile;
  var $branchSites;
  var $branchDir;
  var $branchHttp;
  var $installDir;
  var $tablePrefix;
  var $dbUserName;
  var $dbPassword;
  var $dbName;
  var $dbHost;
  var $addModules = NULL;


  function PHPWS_Branch($submitName=NULL){
    if (!isset($installDirectory))
      $this->installDir = $this->defaultDirectory();
    else
      $this->installDir = $installDirectory;


    if (!($row = $GLOBALS["core"]->sqlSelect("branch_sites")))
      return;

    foreach ($row as $branchSites){
      $branchName = $branchSites["branchName"];
      $this->branchSites[$branchName] = $branchSites;

      if ($submitName && PHPWS_Text::isValidInput($submitName) && $submitName == $branchName)
	PHPWS_Array::arrayToObject($this->branchSites[$branchName], $this);
    }
  }

  function defaultDirectory(){
    $dirArray = explode("/", trim(PHPWS_SOURCE_DIR));
    unset($dirArray[0]);
    array_pop($dirArray);
    array_pop($dirArray);
    $installDir = "/" . implode("/", $dirArray) . "/";

    return $installDir;
  }
  
  function updateBranches($mod_title){
    if (!$GLOBALS['core']->sqlTableExists("branch_sites", TRUE))
      return NULL;

    if (!($sql = $GLOBALS["core"]->sqlSelect("branch_sites")))
      return NULL;
    $homeDir = $GLOBALS["core"]->home_dir;

    foreach ($sql as $branch){
      if (!$GLOBALS["core"]->loadDatabase(PHPWS_SOURCE_DIR . "conf/branch/". $branch["configFile"], TRUE)){
	$this->error[] = "<b>" .$_SESSION["translate"]->it("Unable to connect to the branch database") . ": " . $branch["branchName"] . "</b><br /><br />";
	$GLOBALS["core"] = new PHPWS_Core;
	$GLOBALS["core"]->loadDatabase();
	continue;
      }
      $GLOBALS['core']->home_dir = $branch['branchDir'];
      $GLOBALS["CNT_boost"]["content"] .= "<hr /><b>".$_SESSION["translate"]->it("Updating branch").": ".$branch["branchName"] . "</b><br /><br />";
      PHPWS_Boost::updateModule($mod_title);
    }
    $GLOBALS["core"]->loadDatabase();
    $GLOBALS["core"]->home_dir = $homeDir;
  }


  function editBranchForm(){
    $branchName = $_REQUEST["form_branchName"];

    $template["BRANCH_DIR"] = $_SESSION["translate"]->it("Branch Directory");
    $template["BRANCH_DIR_FORM"] = PHPWS_Form::formTextField("branchDir", $this->branchDir, 50);
    $template["BRANCH_WEB"] = $_SESSION["translate"]->it("Branch Web Address");
    $template["BRANCH_WEB_FORM"] = PHPWS_Form::formTextField("branchHttp", $this->branchHttp, 50);

    $content = "\n<form action=\"index.php\" method=\"post\">\n";
    $content .= PHPWS_Form::formHidden(array("module"=>"branch", "branch_op"=>"editBranchAction", "form_branchName"=>$branchName)) . "\n";
    $content .= PHPWS_Template::processTemplate($template, "branch", "editBranch.tpl");
    $content .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Update")) . "\n</form>";

    return $content;
  }

  function editBranchAction(){
    extract($_POST);

    if (!preg_match("/\/$/", $branchDir))
      $branchDir .= "/";

    $update["branchDir"] = $branchDir;
    $update["branchHttp"] = $branchHttp;
    $branchName = $form_branchName;
    
    return $GLOBALS["core"]->sqlUpdate($update, "branch_sites", "branchName", $branchName);

  }

  function manageBranch(){
    $CNT_Branch_Main["title"] = $_SESSION["translate"]->it("Manage Branches");
    $template['BRANCHES'] = NULL;
    $template['COMMANDS_LBL'] = $_SESSION["translate"]->it("Commands");
    $template['BRANCH_NAME_LBL'] = $_SESSION["translate"]->it("Branch Name");
    $branch = new PHPWS_Branch;

    if (!$branch->branchSites) {
      $GLOBALS["CNT_Branch_Main"]["content"] .= $_SESSION["translate"]->it("No branch sites found") . ".";
      return;
    }

    $branches = $branch->branchSites;

    foreach ($branches as $info){
      extract($info);
      $rowtpl["BRANCHNAME"] = "<a href=\"http://" . $branchHttp . "\">$branchName</a>";
      $rowtpl["EDIT"]    = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Edit"), "branch", array("branch_op"=>"editBranchForm", "form_branchName"=>"$branchName"));
      $rowtpl["REMOVE"]  = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Remove"), "branch", array("branch_op"=>"removeBranchForm", "form_branchName"=>"$branchName"));
      $template["BRANCHES"] .=  PHPWS_Template::processTemplate($rowtpl, "branch", "manageRows.tpl");
    }

    $GLOBALS["CNT_Branch_Main"]["content"] .= PHPWS_Template::processTemplate($template, "branch", "manage.tpl");

  }


  function unregisterBranch(){
    $GLOBALS["core"]->sqlDelete("branch_sites", "branchName", $this->branchName);
    if(@unlink(PHPWS_SOURCE_DIR . "conf/branch/". $this->branchName . ".php"))
      $GLOBALS["CNT_Branch_Main"]["content"] .= $_SESSION["translate"]->it("Configuration file deleted") . ".<br />";
    else
      $GLOBALS["CNT_Branch_Main"]["content"] .= $_SESSION["translate"]->it("Unable to delete configuration file [var1]", $this->branchName.".php") . ".<br />";

    $GLOBALS["CNT_Branch_Main"]["content"] .= $_SESSION["translate"]->it("Make sure to delete the branch directory and database") .".";
  }

  function removeBranchForm(){
    $content = $_SESSION["translate"]->it("Are you absolutely sure you want to unregister this branch") . "?<br />";
    $content .= PHPWS_Text::moduleLink($_SESSION["translate"]->it("Yes"), "branch", array("branch_op"=>"unregisterBranch", "confirm"=>"yes")) . " ";
    $content .= PHPWS_Text::moduleLink($_SESSION["translate"]->it("No"), "branch", array("branch_op"=>"unregisterBranch", "confirm"=>"no"));
    return $content;
  }

  function panel(){
    $template["CREATE"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Create Branch"), "branch", array("branch_op"=>"createBranch"));
    $template["MANAGE"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Manage Branches"), "branch", array("branch_op"=>"manageBranch"));
    return PHPWS_Template::processTemplate($template, "branch", "panel.tpl");
    
  }

  function expertEditBranch(){
    if(!$this->testBranchConf())
      return FALSE;

    $form = new EZForm;
    $form->add("module", "hidden", "branch");
    $form->add("branch_op", "hidden", "expertBranchUpdate");
    $form = $this->branchForm($form);
    $template = $form->getTemplate();
    $template["FORM_BRANCHNAME"] = $this->branchName;
    $template["DATABASE_INFO_LBL"] = $_SESSION["translate"]->it("Database Information");
    $template["DATABASE_HOST_LBL"] = $_SESSION["translate"]->it("Database Host");
    $content = PHPWS_Template::processTemplate($template, "branch", "expertForm.tpl");
    $GLOBALS["CNT_Branch_Main"]["title"] = $_SESSION["translate"]->it("Expert Update Branch");
    $GLOBALS["CNT_Branch_Main"]["content"] .= $content;
  }

  
  function expertCreateBranch(){
    if(!$this->testBranchConf())
      return FALSE;

    if (!isset($this->dbHost))
      $this->dbHost = "localhost";

    $form = new EZForm('new_branch');
    $form->add("module", "hidden", "branch");
    $form->add("branch_op", "hidden", "expertBranchAction");
    $form->add("form_branchName", "text", $this->branchName);
    $form = $this->databaseForm($form);
    $form = $this->branchForm($form);
    $template = $form->getTemplate();
    $template['MODULES'] = $this->moduleForm();
    $template["DATABASE_INFO_LBL"] = $_SESSION["translate"]->it("Database Information");
    $template["DATABASE_HOST_LBL"] = $_SESSION["translate"]->it("Database Host");
    $template["DATABASE_USERNAME_LBL"] = $_SESSION["translate"]->it("Database Username");
    $template["DATABASE_PASSWORD_LBL"] = $_SESSION["translate"]->it("Database Password");
    $template["DATABASE_NAME_LBL"] = $_SESSION["translate"]->it("Database Name");
    $template["TABLE_PREFIX_LBL"] = $_SESSION["translate"]->it("Table Prefix");
    $template["BRANCH_INFO_LBL"] = $_SESSION["translate"]->it("Branch Information");
    $template["BRANCH_NAME_LBL"] = $_SESSION["translate"]->it("Branch Name");
    $template["BRANCH_WEBADD_LBL"] = $_SESSION["translate"]->it("Branch Web Address");
    $template["BRANCH_FD_LBL"] = $_SESSION["translate"]->it("Branch File Directory");
    $template["ID_HASH_LBL"] = $_SESSION["translate"]->it("ID Hash");
    $template["PREINSTALL_MOD"] = $_SESSION["translate"]->it("PreInstall Modules");

    $content = PHPWS_Template::processTemplate($template, "branch", "expertForm.tpl");
    $GLOBALS["CNT_Branch_Main"]["title"] = $_SESSION["translate"]->it("Expert Create Branch");
    $GLOBALS["CNT_Branch_Main"]["content"] .= $content;

  }

  function moduleForm(){
    $count = 0;
    include(PHPWS_SOURCE_DIR . "setup/defaultMods.php");

    $form = new EZForm;
    if (!($dir = phpws_file::readDirectory(PHPWS_SOURCE_DIR . "mod/", 1)))
      return NULL;
    
    foreach ($dir as $mod_dir_name){
      $count++;
      $filename = PHPWS_SOURCE_DIR . "mod/$mod_dir_name/conf/boost.php";
      if (!(is_file($filename)))
	continue;
      
      unset($mod_directory);
      unset($mod_pname);
      unset($mod_title);
      unset($branch_allow);

      include($filename);

      if (isset($branch_allow) && ($branch_allow == 0))
	continue;

      if (!in_array($mod_title, $defaultMods)) {
	$mods[$count]['dir'] = $mod_directory;
	$mods[$count]['name'] = $mod_pname;
	$mods[$count]['title'] = $mod_title;
      }
    }

    if (count($mods)){
      foreach ($mods as $module){
	$form->add("addModules[" . $module['title'] . "]", "checkbox", $module['dir']);
	if (isset($this->addModules[$module['title']]))
	  $form->setMatch("addModules[" . $module['title'] . "]", $module['dir']);
	$content[] = $form->get("addModules[" . $module['title'] . "]") . " " . $module['name'] . "<br />\n";
      }
      
      $content[] = PHPWS_WizardBag::js_insert("check_all", "new_branch");
    }

    return implode("", $content);
  }


  function branchForm($form){
    $form->add("branchHttp", "text", $this->branchHttp);
    $form->setSize("branchHttp", 50);
    $form->add("branchDir", "text", $this->branchDir);
    $form->setSize("branchDir", 50);

    $form->add("IDhash", "text", md5(rand()));
    $form->setSize("IDhash", 40);
    return $form;
  }

  function databaseForm($form){
    $form->add("tablePrefix", "text", $this->tablePrefix);
    $form->add("dbUserName", "text", $this->dbUserName);
    $form->add("dbPassword", "password", $this->dbPassword);
    $form->add("dbName", "text", $this->dbName);
    $form->add("dbHost", "text", $this->dbHost);
    return $form;
  }

  function processExpert(){
    extract($_POST);

    $this->dbName      = $dbName;
    $this->dbHost      = $dbHost;
    $this->dbUserName  = $dbUserName;
    $this->dbPassword  = $dbPassword;
    $this->tablePrefix = $tablePrefix;

    if (empty($dbName) || empty($dbHost) || empty($dbUserName) || empty($dbPassword))
      $this->error[] = $_SESSION["translate"]->it("Your are missing crucial database information").".";
    else {
      include(PHPWS_SOURCE_DIR . "conf/config.php");

      $db = DB::connect("$dbversion://$dbUserName:$dbPassword@$dbHost");

      if (!isset($db->dsn))
	$this->error[] = $_SESSION["translate"]->it("Unable to make database connection with the information supplied").".";

      if(method_exists($db, "getlistOf")) {
	if(DB::iserror($databases = $db->getlistOf("databases")))
	  exit (DB::errorMessage($databases));
	else {

	  if(!empty($dbName) && !in_array($dbName, $databases))
	    $this->error[] = $_SESSION["translate"]->it("You need to create a database named [var1]", ": $dbName") . ".";	      
	}
      }
    }
    
    $branchName        = $form_branchName;
    $this->branchName  = $branchName;

    if (!empty($branchHttp)){
      $branchHttp = str_replace("http://", "", $branchHttp);
      if(!preg_match("/\/$/", $branchHttp))
	$branchHttp .= "/";
    }
    $this->branchHttp  = $branchHttp;

    if (!empty($branchDir) && !preg_match("/\/$/", $branchDir))
      $branchDir .= "/";
    $this->branchDir   = $branchDir;

    if(strlen($IDhash) > 32) {
      $this->error[] = $_SESSION["translate"]->it("The ID Hash cannot exceed 32 characters").".";      
    } else {
      $this->IDhash      = $IDhash;
    }

    $this->testBranchName($branchName);
    $this->testBranchInstall($branchDir);
    $this->configFile   = $this->branchName . ".php";

    if (isset($_POST['addModules']))
      $this->addModules = $_POST['addModules'];

    if (count($this->error))
      return FALSE;
    else
      return TRUE;
  }


  function branchExists($branchName=NULL){
    $temp = new PHPWS_Branch;

    if (is_null($branchName) && $temp->branchSites)
      return TRUE;
    elseif (isset($temp->branchSites[$branchName]))
      return TRUE;
    else
      return FALSE;
  }

  function testBranchName($branchName){
    if (!PHPWS_Text::isValidInput($branchName)){
      $this->error[] = $_SESSION["translate"]->it("Invalid Branch Name") . ".";
      return FALSE;
    } elseif ($this->branchExists($branchName)){
      $this->error[] = $_SESSION["translate"]->it("Branch already exists") . ".";
      return FALSE;
    } else
      return TRUE;
  }


  function testBranchInstall($installDir){
    if (empty($installDir))
      $installDir = "<i>" . $_SESSION["translate"]->it("Missing") . "</i>";
    if (!@is_dir($installDir) || !@is_writable($installDir)){
      $this->error[] = $_SESSION["translate"]->it("The installation directory is not writable"). ".<br />" .
      $_SESSION["translate"]->it("Return after you have changed the permissions"). ".<br />" .
      $_SESSION["translate"]->it("Directory") . ": " . $installDir;
      return FALSE;
    } elseif (@is_file($installDir."index.php")){
      $this->error[] = $_SESSION["translate"]->it("There appears to be an installation in this directory"). ".<br />" .
      $_SESSION["translate"]->it("Remove the index.php file in this directory"). ".<br />" .
      $_SESSION["translate"]->it("Directory") . ": " . $installDir;
      return FALSE;
    } else
      return TRUE;
  }


  function writeBranch(){
    if (!$this->writeConfig()){
      $this->error[] = $_SESSION["translate"]->it("Unable to create branch config file") . ".";
      return FALSE;
    }

    $content = "<b>" .$_SESSION["translate"]->it("Configuration File written successfully") . ".</b><hr />";

    if (!$this->writeDirectory()){
      $this->error[] = $_SESSION["translate"]->it("Unable to write branch directory") . ".<br />";
      return FALSE;
    }

    $content .= "<b>" . $_SESSION["translate"]->it("Branch directory created successfully") . ".</b><hr />";
    
    
    if (!($content .= $this->writeDatabase())){
      $this->error[] = $_SESSION["translate"]->it("Error writing branch to database") . ".";
      return FALSE;
    }

    $content .= "<b>" . $_SESSION["translate"]->it("Branch database populated successfully") . ".</b><hr />";
    
    if (!$this->registerBranch()){
      $this->error[] = $_SESSION["translate"]->it("Unable to register branch to hub") . ".";
      return FALSE;
    }

    $content .= $_SESSION["translate"]->it("Branch registered successfully to hub") . ".<br />";

    return $content;
  }

  function registerBranch(){
    $insert["branchName"] = $this->branchName;
    $insert["configFile"] = $this->configFile;
    $insert["IDhash"]     = $this->IDhash;
    $insert["branchHttp"] = $this->branchHttp;
    $insert["branchDir"]  = $this->branchDir;

    return $GLOBALS["core"]->sqlInsert($insert, "branch_sites");
  }


  function writeDirectory(){
    $branch_info = "<?php
\$hub_dir = \"" . PHPWS_SOURCE_DIR . "\";
\$branchName = \"".$this->branchName."\";
\$IDhash = \"".$this->IDhash."\";
include(\$hub_dir.\"index.php\");
?>";

    PHPWS_File::recursiveFileCopy(PHPWS_SOURCE_DIR . "themes/", $this->branchDir . "themes/");
    PHPWS_File::recursiveFileCopy(PHPWS_SOURCE_DIR . "admin/", $this->branchDir . "admin/");
    PHPWS_File::recursiveFileCopy(PHPWS_SOURCE_DIR . "js/", $this->branchDir . "js/");
    chdir(PHPWS_SOURCE_DIR);

    PHPWS_File::makeDir($this->branchDir."images/");
    
    PHPWS_File::recursiveFileCopy(PHPWS_SOURCE_DIR . "images/mod/", $this->branchDir . "images/mod/");
    PHPWS_File::recursiveFileCopy(PHPWS_SOURCE_DIR . "images/javascript/", $this->branchDir . "images/javascript/");
 
    PHPWS_File::makeDir($this->branchDir."images/core/");
    PHPWS_File::makeDir($this->branchDir."images/core/list/");

    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/core/list/down_pointer.png", $this->branchDir . "images/core/list/", "down_pointer.png", TRUE, TRUE);
    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/core/list/up_pointer.png", $this->branchDir . "images/core/list/", "up_pointer.png", TRUE, TRUE);
    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/core/list/sort_none.png", $this->branchDir . "images/core/list/", "sort_none.png", TRUE, TRUE);

    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/info.gif", $this->branchDir . "images/", "info.gif", TRUE, FALSE);
    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/print.gif", $this->branchDir . "images/", "print.gif", TRUE, FALSE);
    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/up.gif", $this->branchDir . "images/", "up.gif", TRUE, FALSE);
    PHPWS_File::fileCopy(PHPWS_SOURCE_DIR . "images/down.gif", $this->branchDir . "images/", "down.gif", TRUE, FALSE);

    PHPWS_File::makeDir($this->branchDir."files/");

    $fp = fopen($this->branchDir."index.php", "w");
    fwrite($fp, stripslashes($branch_info));
    fclose($fp);

    return TRUE;
  }

  function writeDatabase(){
    include(PHPWS_SOURCE_DIR . "setup/defaultMods.php");
    $sourceDirectory = PHPWS_SOURCE_DIR;
    $deities = $GLOBALS['core']->sqlSelect("mod_users", "deity", 1);

    //$GLOBALS["core"] = new PHPWS_Core;
    if (!$GLOBALS["core"]->loadDatabase($sourceDirectory . "conf/branch/" . $this->configFile, TRUE)){
      $this->error[] = $_SESSION["translate"]->it("Unable to connect to the branch database") . ": " . $this->branchName . "</b><br /><br />";

      //$GLOBALS["core"] = new PHPWS_Core;
      $GLOBALS["core"]->loadDatabase();
      return FALSE;
    }

    $tables = $GLOBALS["core"]->listTables();

    if (in_array($this->tablePrefix . "modules", $tables)){
      $this->error[] = $_SESSION["translate"]->it("There is already an installation of phpWebSite in this database") . ".";

      //$GLOBALS["core"] = new PHPWS_Core;
      $GLOBALS["core"]->loadDatabase();
      return FALSE;
    }

    if (!($install_language = $_SESSION["translate"]->default_language))
      $install_language = "en";

    $GLOBALS['core']->home_dir = $this->branchDir;
    $GLOBALS['core']->home_http = $this->branchHttp;

    $langCreate = "
CREATE TABLE mod_lang_".strtoupper($install_language)." (
  phrase_id int unsigned NOT NULL,
  module varchar(30) NOT NULL default '',
  phrase text NOT NULL,
  translation text NOT NULL,
  PRIMARY KEY  (phrase_id),
  KEY module (module)
);";

    if (!$GLOBALS["core"]->query($langCreate, TRUE))
      echo $_SESSION["translate"]->it("There was a problem creating the default language table") . ".<br />";

    if ($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "setup/install.sql", 1,1))
      $content = "<b>".$_SESSION["translate"]->it("Core tables successfully installed") . "!</b><br />";
    else {
      $this->error[] = $_SESSION["translate"]->it("There was a problem installing the core tables") . ". <br />";
      $this->error[] = $_SESSION["translate"]->it("Please check your core and try the installation again") . ".<br />";

      //$GLOBALS["core"] = new PHPWS_Core;
      $GLOBALS["core"]->loadDatabase();
      return FALSE;
    }

    $content .= "<h2>" . $_SESSION["translate"]->it("Building required modules") . "</h2><hr />";
    $content .=  PHPWS_Boost::installModuleList($defaultMods);
    PHPWS_ControlPanel::import("controlpanel");

    // change default language to match hub
    $langUpdate = array("default_language" => $install_language);
    $GLOBALS["core"]->sqlUpdate($langUpdate, "mod_language_settings");

    $content .=  "<h2>" . $_SESSION["translate"]->it("Registering Default Language") . "</h2><hr />";
    $content .=  PHPWS_Language::installModuleLanguage($defaultMods);

    $content .=  PHPWS_Boost::postInstall($defaultMods);
    
    PHPWS_Boost::setVersionInfo(PHPWS_SOURCE_DIR."conf/core_info.php", "insert");

    foreach ($deities as $accounts){
      unset($accounts['user_id']);
      $GLOBALS['core']->sqlInsert($accounts, "mod_users");
    }

    $content .= $_SESSION["translate"]->it("User account copied") . ".<br />";

    if (isset($this->addModules)){
      $content .= $_SESSION["translate"]->it("Installing extra modules");
      $content .= PHPWS_Boost::installModuleList($this->addModules, FALSE, TRUE, TRUE);
    }

    //$GLOBALS["core"] = new PHPWS_Core;
    $GLOBALS["core"]->loadDatabase();
    return $content;
  }


  function writeConfig(){
    if (!$this->configFile)
      exit("Error Branch.php in writeConfig : missing object configFile");

    include (PHPWS_SOURCE_DIR . "conf/config.php");
    $dbhost = $this->dbHost;
    $dbuser = $this->dbUserName;
    $dbpass = $this->dbPassword;

    $exportDir = PHPWS_SOURCE_DIR . "conf/branch/";

    $config = "<?php
\$dbversion = \"".$dbversion."\";
\$dbhost = \"".$dbhost."\";
\$dbuser = \"".$dbuser."\";
\$dbpass = \"".$dbpass."\";
\$dbname = \"".$this->dbName."\";
\$table_prefix = \"".$this->tablePrefix."\";
?>";

    $result = PHPWS_File::writeFile($exportDir . $this->configFile, $config, TRUE);
    if (!$result){
      $this->error[] = $_SESSION["translate"]->it("There was a problem writing the file") . ".";
      $this->error[] = $_SESSION["translate"]->it("Make sure there is not a file other same name in the conf directory") . ". <br />";
      return FALSE;
    } else
      return TRUE;
  }


  function listErrors(){
    if (!($this->error))
      return;
    $content = NULL;
    foreach ($this->error as $error){
      $content .= "<span class=\"errortext\">$error</span><br />";
      $loop = 1;
    }

    unset($this->error);
    return $content."<hr />";
  }

  function testBranchConf(){
    $configDir = PHPWS_SOURCE_DIR . "conf/branch/";
    if (!is_dir($configDir) || !is_writable($configDir)){
      $this->error[] .= $_SESSION["translate"]->it("Your configuration directory is not writable"). ".<br />" .
      $_SESSION["translate"]->it("Return after you have changed the permissions"). ".<br />" .
      $_SESSION["translate"]->it("Directory") . ": " . $configDir;
    }

    if (isset($this->error))
      return FALSE;
    else
      return TRUE;
  }

  /* 
   * Function branch_info will return a 2D associative array 
   * of information on different branches that exist in your 
   * instance of phpws.  Order of fields and branches is <b>not</b> 
   * important since returned array is associative. 
   * 
   * @author Ryan Roland <rmroland@NOSPAM.indiana.edu> 
   * @param  string $branch_name comma separated list of branches to return information on (or "all") 
   * @param  string $desired_info comma separated list of information fields to return (or "all") 
   * @return array 2D associative array with requested information on requested branches 
   */
  function branch_info($branch_name="all",$desired_info="all"){  
    // if not hub, then get dbname and table_prefix for hub  
    if (!$GLOBALS["core"]->isHub){    
      require(PHPWS_SOURCE_DIR."conf/config.php");  
    }  else {    
      $dbname = $GLOBALS['core']->db->dsn["database"];    
      $table_prefix = $GLOBALS['core']->tbl_prefix;
    } 
    // create sql for specified branches  
    $sql = "SELECT * FROM " . $dbname . "." . $table_prefix . "branch_sites";  
    if ($branch_name != "all") {    
      $branches = explode(",",$branch_name);    
      $addon = " WHERE 1=0 ";    
      foreach ($branches as $next_branch){      
	$addon .= " OR branchName='" . $next_branch . "'";    
      }    
      $sql .= $addon;  
    }  
    // get DB data on specified branches  
    $result = $GLOBALS["core"]->getAll($sql);  
    // array of desired information  
    $return_info = array();  
    if(!DB::isError($result) && is_array($result) && sizeof($result) > 0) { 
      $all_file_items = array("dbversion","dbhost","dbuser","dbpass","dbname","table_prefix");
      $all_db_items = array("branchName","configFile","IDhash","branchDir","branchHttp"); 
      // create list of desired items 
      if ($desired_info == "all"){ 
	$file_items = $all_file_items;
	$db_itesm = $all_db_itesm;
      } else {
	// find which are wanted 
	$info_types = explode(",",$desired_info);
	$file_items = array_intersect($all_file_items, $info_types);
	$db_items = array_intersect($all_db_items, $info_types);
      }    
      // loop through each branch gathering info 
      foreach ($result as $branch){
	$return_info[$branch["branchName"]] = array();
	if (sizeof($db_items) > 0){
	  // loop through adding to array, the requested info from the db result resource
	  foreach ($db_items as $type){
	    switch($type){
            case "branchName":
              $return_info[$branch["branchName"]]["branchName"] = $branch["branchName"]; 
	      break;     
	    case "configFile":
              $return_info[$branch["branchName"]]["configFile"] = $branch["configFile"];
              break;
            case "IDhash":
              $return_info[$branch["branchName"]]["IDhash"] = $branch["IDhash"];
              break;
            case "branchDir":
              $return_info[$branch["branchName"]]["branchDir"] = $branch["branchDir"];
              break;
            case "branchHttp":
              $return_info[$branch["branchName"]]["branchHttp"] = $branch["branchHttp"];
              break;
	    }// end switch
	  }// end foreach
	}// end if
	if (sizeof($file_items) > 0){
	  // include the config file for each branch to get additional info
	  require(PHPWS_SOURCE_DIR . "conf/branch/" . $branch["configFile"]);
	  // loop through adding to array, the requested info from branch config file
	  foreach ($file_items as $type){
	    switch($type){  
	    case "dbversion":
              $return_info[$branch["branchName"]]["dbversion"] = $dbversion;
              break; 
	    case "dbhost":
              $return_info[$branch["branchName"]]["dbhost"] = $dbhost;
              break;
            case "dbuser": 
	      $return_info[$branch["branchName"]]["dbuser"] = $dbuser; 
	      break;
            case "dbpass":   
	      $return_info[$branch["branchName"]]["dbpass"] = $dbpass;
              break;
            case "dbname":
              $return_info[$branch["branchName"]]["dbname"] = $dbname;
              break;
            case "table_prefix":
              $return_info[$branch["branchName"]]["table_prefix"] = $table_prefix;
              break;

	    }// end switch
	  }// end foreach
	}// end if
      }// end foreach
    }// end if
    return $return_info;
  }//END FUNC branch_info
}

?>