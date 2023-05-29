<?php

/**
 * Health module for phpWebSite
 *
 * @author rck <http://www.kiesler.at/>
 */

require_once(PHPWS_SOURCE_DIR . "core/Pager.php");

require_once(PHPWS_SOURCE_DIR . "mod/health/class/overview.php");

require_once(PHPWS_SOURCE_DIR . "mod/health/class/tools/clear_cache.php");
require_once(PHPWS_SOURCE_DIR . "mod/health/class/tools/force_remove.php");
require_once(PHPWS_SOURCE_DIR . "mod/health/class/tools/box_mover.php");
require_once(PHPWS_SOURCE_DIR . "mod/health/class/tools/show_uservars.php");


class PHPWS_health {


	var $_pager;
	var $_sortid = "counter desc";
	var $_id;

	var $_core_version;
	var $_health_version;
	var $_notes_installed;


	var $_OK=0;
	var $_WARN=1;
	var $_ERROR=2;


	var $_active=true;
   


	function PHPWS_health() {

		include($GLOBALS["core"]->source_dir . "mod/health/conf/config.php");
		$this->_active=$active;

		require_once(PHPWS_SOURCE_DIR . "mod/boost/class/Boost.php");
		$boost = new PHPWS_Boost;

		$versionInfo = $boost->getVersionInfo("Core");
		$this->_core_version = version_compare($versionInfo['version'], "0.9.3-1");

		$versionInfo = $boost->getVersionInfo("notes");
		if ($versionInfo == FALSE)
			$this->_notes_installed = 0;
		else
			$this->_notes_installed = 1;

		$versionInfo = $boost->getVersionInfo("Health");
		$this->_health_version = $versionInfo['version'];

	}



	function realip() {

		$ip = FALSE;
	
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}

	
		if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ips = explode(", ", $_SERVER["HTTP_X_FORWARDED_FOR"]);

			if($ips){
				array_unshift($ips, $ip);
				$ip = FALSE;
			}
	    
			for($i = 0; $i < count($ips); $i++)	{
				if (!eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
	
		return ($ip ? $ip : $_SERVER["REMOTE_ADDR"]);
	}



	
	function size_readable($size, $unit=null, $retstring=null) {

		// taken from
		// http://aidan.dotgeek.org/lib/?file=function.size_readable.php

		$sizes=array('B', 'KB', 'MB', 'GB', 'TB');
		$ii = count($sizes)-1;

		$unit=array_search((string)$unit, $sizes);

		if($unit==null || $unit=false) {
			$unit=$ii;
		}

		if($retstring==null) {
			$retstring='%01.2f %s';
		}

		$i=0;

		while($unit!=$i && $size>=1024 && $i<$ii) {
			$size /= 1024;
			$i++;
		}

		return(sprintf($retstring, $size, $sizes[$i]));

	}


	function return_bytes($val) {

		// taken from
		// http://www.php.net/manual/en/function.ini-get.php

		$val=trim($val);

		if(strlen($val)<=1)
			return($val);
		else
			$last=$val{strlen($val)-1};

		switch($last) {
			case 'k':
			case 'K':
				return ((int) $val*1024);
				break;

			case 'm':
			case 'M':
				return ((int) $val*1048576);
				break;

			default:
				return($val);
		}

	}


	function isRunningWindows() {

		// returns true, if windows detected.
		// false otherwise

		return(strtoupper(substr(PHP_OS, 0, 3))==="WIN");

	}


	function isRunningUnix() {

                // I am not too sure about that Unix-Check here.
                // do you know a better one?

                return( (DIRECTORY_SEPARATOR == "/") &&
                        (PHP_SHLIB_SUFFIX == "so") &&
                        (PATH_SEPARATOR == ":"));

	}



	function isOpenBaseDirInEffect() {

		return((bool)ini_get('open_basedir'));

	}




	function queryOpenBaseDir() {


		$status=$this->_ERROR;

		$active=(bool)ini_get('open_basedir');

		if(!$active) {

			$description = "Your host does not restrict your ";
			$description.= "directory access with open_basedir.";

			$status=$this->_OK;

		} else {


			$description = "Right now, only access to the following ";
			$description.= "directories is allowed through the ";
			$description.= "open_basedir statement: ";

			$basedirs=explode(PATH_SEPARATOR,
				ini_get('open_basedir'));

			$basedirs=implode(", ", $basedirs);
			$description.=$basedirs;

			$status=$this->_WARN;
		}
	

                $result=array();

                $result['what']="open_basedir restriction";
                $result['code']=$status;
                $result['text']=$description;

                return($result);

	}



	// $data is an associative array of following structure:
	// $data["directory"] = where to look
	// $data["error"] = how much space has there to be at least free?
	// $data["warn"] = how much bytes must there be at max free so we
	//                 issue a warning?
	// $data["error_text"] = text to be returned in case of error. use
	//                       [var1] for directory name and [var2] for
	//	                 the current free space
	// $data["warn_text"]  = same like error_text but for warnings
	function queryFreeSpace($data)  {

		$status=$this->_ERROR;
		$directory=$data["directory"];

		if(function_exists(disk_free_space)) {
			$free=disk_free_space($directory);
			$free_str=$this->size_readable($free);
		} else {
			$free="func_not_found";
		}

		$error_level=$data["error"];
		$warn_level=$data["warn"];

		if($free == "func_not_found") {
			$description = "The php on your server does not ";
			$description.= "support the function ";


			$description.= "<a href=\"";
			$description.= "http://www.php.net/manual/en/function.disk-free-space.php";
			$description.= "\">";
			$description.= " disk_free_space(). ";
			$description.= "</a>";

			$description.= "Please doublecheck with your host, wether ";
			$description.= "he compiled in. It has been available in ";
			$description.= "php as of version 4.1.0.";

			$status=$this->_ERROR;
		} else

		if($free > $warn_level) {

			$text="Your [var1] directory currently has [var2] worth of free space.";
			$description=$_SESSION["translate"]->it($text, $directory, $free_str);

			$status=$this->_OK;

		} else

		if($free > $error_level) {

			$text=$data["warn_text"];

			$text="Your [var1] directory currently has [var2] worth of free space.";
			$description=$_SESSION["translate"]->it($text, $directory, $free_str);

			$description.=" ".$data["warn_text"];

			$status=$this->_WARN;
		} else {

			$text="Your [var1] directory currently has [var2] worth of free space.";
			$description=$_SESSION["translate"]->it($text, $directory, $free_str);

			$description.=" ".$data["error_text"];

			$status=$this->_ERROR;

		}

		$result=array();

		$result['what']="Free space in $directory";
		$result['code']=$status;
		$result['text']=$description;

		return($result);

	}



	function queryTmpSpace() {

		$data=array();

		$data["directory"]="/tmp";
		$data["warn"]=512*1024*1024; // 512 MB
		$data["error"]=64*1024*1024; // 64 MB

		$data["warn_text"]=	"It's getting rather crowded. Please ".
					"try to have at least about 512 MB free.";

		$data["error_text"]=	"This is probably one of the reasons, ".
					"why your site doesn't behave the way you ".
					"want it to. Please free about 512 MB.";

		return($this->queryFreeSpace($data));
	}



	function queryVarSpace() {

		$data=array();

		$data["directory"]="/var";

		$data["warn"]=256*1024*1024; // 256 MB
		$data["error"]=50*1024*1024; // 50 MB

		$data["warn_text"]=	"Please ask your sysadmin to consider ".
					"removing old print jobs, log files and maybe ".
					"unclutter mail files as well. As a rule ".
					"of thumb, at least 256 MB free in /var would ".
					"be good.";

		$data["error_text"]=	"Did you lose any mails recently? This ".
					"could be the reason. Please ask your sysadmin ".
					"to free at least about 256 MB in /var";

		return($this->queryFreeSpace($data));
	}



	function queryRootSpace() {

		$data=array();

		$data["directory"]="/";

		$data["warn"]=128*1024*1024; // 128 MB;
		$data["error"]=64*1024*1024; // 64 MB

		$data["warn_text"]=	"Please ask your sysadmin to consider ".
					"removing unneeded applications, unclutter his ".
					"home directory and/or increase the partition ".
					"for / (=the root directory). As a rule of ".
					"thumb, 128 MB free in / would be good.";

		$data["error_text"]=	"Did any of your applications behave strange ".
					"recently? A full root directory can lead to ".
					"strange problems and is usually a very bad ".
					"sign. Please ask your sysadmin to free about ".
					"128 MB in the root directory of your server.";

		return($this->queryFreeSpace($data));
	}


	function querySessionSpace() {

		$session_path=ini_get("session.save_path");

		$avail=(bool)$session_path;

		$status=$this->_ERROR;

		$result=array();

		if(!$avail) {

			$result['code']=$this->_ERROR;
			$result['text']="session.save_path not set! Please do so ".
					"in php.ini to make sessions work.";

		}
		if($session_path == "/tmp") {
			$result['code']=$this->_OK;
			$result['text']="Session directory is set to /tmp which has ".
					"already been queried.";
		} else {

			$data["directory"]=$session_path;
			$data["warn"]=512*1024*1024;	// 512 MB
			$data["error"]=64*1024*1024;	// 64 MB

			$data["warn_text"]=	"If your session directory gets full, ".
						"people won't be able to log in your site. ";
						"Also, your site will get slower. Please try ";
						"to free about 512 MB in the session directory.";

			$data["error_text"]=	"If phpWebSite behaves very oddly and/or is ".
						"slow, this is one of the reasons. Please free about ".
						"512 MB.";
			
			$result=$this->queryFreeSpace($data);

		}

		$result['what']='Free space in Session Directory';
		return($result);
	}


	function querySessionFiles() {

		$session_path=ini_get("session.save_path");

		$avail=(bool)$session_path;

		$status=$this->_ERROR;

		$result=array();

		if(!$avail) {

			$result['code']=$this->_ERROR;
			// $result['text']="

		}
	}



	function queryMemoryUsage() {

		$status=$this->_ERROR;


		if(!function_exists(memory_get_usage)) {
			$usage="func_not_found";
		}else
		if($this->isRunningWindows()) {
			$usage="running_windows";
		} else {

			$usage=memory_get_usage();
			$usage_str=$this->size_readable($usage);
		}


		$error_level=64*1024*1024; // 64 MB
		$warn_level=32*1024*1024; // 32MB


		if($usage=="func_not_found") {

                        $description = "The php on your server does not ";
                        $description.= "support the function ";

                        $description.= "<a href=\"";
                        $description.= "http://www.php.net/manual/en/function.memory-get-usage.php";
                        $description.= "\">";
                        $description.= "memory_get_usage()";
                        $description.= "</a>.";

                        $description.= "Please doublecheck with your host, wether ";
                        $description.= "he compiled in. It has been available in ";
                        $description.= "php as of version 4.3.2 but is only available, ";
			$description.= "if your php is compiled with the --enable-memory-limit ";
			$description.= "option.";

                        $status=$this->_ERROR;

		} else

		if($usage=="running_windows") {

			$description = "You Server is running Windows, thus ";
			$description.= "the memory_get_usage() method is not ";
			$description.= "available. I cannot find out how ";
			$description.= "much memory phpWebSite takes in your ";
			$description.= "case.";

			$status=$this->_WARN;

		} else

		if($usage < $warn_level) {

			$description = "This phpWebSite session currently uses ";
			$description.= $usage_str." memory. ";
	
			$description.= "Pretty lean.";

			$status=$this->_OK;

		} else
		if($usage < $error_level) {

			$description = "This phpWebSite session currently uses ";
			$description.= $usage_str." memory. ";

			$description.="This is a bit on the heavy side. ";
			$description.="You might consider unboosting some ";
			$description.="unneeded modules.";

			$status=$this->_WARN;
		} else {

			$description = "This phpWebSite session currently uses ";
			$description.= $usage_str." memory. ";

			$description.="That's quite a lot. Please strip your ";
			$description.="installation down if you can. Boost is ";
			$description.="able to help you out here.";

			$status=$this->_ERROR;
		}


		$result=array();

		$result['what']="Used Memory";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}


	function queryMemoryAllocation() {

		$status=$this->_ERROR;

		$mem=(bool)ini_get('memory_limit');

		if($mem) {
			$mem=$this->return_bytes(ini_get("memory_limit"));
			$mem_str=$this->size_readable($mem);
		} else {
			$mem="unlimited";
		}

		$error_level=32*1024*1024; // 32 MB
		$warn_level=64*1024*1024; // 64 MB

		if($mem == "unlimited") {

			$description = "Right now, phpWebSite can use as ";
			$description.= "much memory as there is available. ";

			$status=$this->_WARN;

		} else
		if($mem < $error_level) {

			$description = "phpWebSite is allowed to use up to ";
			$description.= $mem_str." memory. ";

			$description.= "This is to little. Please increase ";
			$description.= "to 64M in Core.php or in php.ini. ";
			$description.= "The attribute you want to change ";
			$description.= "is called memory_limit.";

			$status=$this->_ERROR;

		} else
		if($mem < $warn_level) {

			$description = "phpWebSite is allowed to use up to ";
			$description.= $mem_str." memory. ";

			$description.= "Please consider increasing ";
			$description.= "memory_limit to 64 MB.";

			$status=$this->_WARN;

		} else {
			$description = "phpWebSite is allowed to use up to ";
			$description.= $mem_str." memory. ";

			$description.="As there aren't any known phpWebSite ";
			$description.="that use more memory, this should ";
			$description.="be right.";

			$status=$this->_OK;
		}

		$result=array();

		$result['what']="Available Memory";
		$result['code']=$status;
		$result['text']=$description;

		return($result);

	}



	function querySafeMode() {

		$status=$this->_ERROR;
		$safemode=(bool)ini_get('safe_mode');

		if($safemode) {

			$description = "You currently have ";
			$description.= "<a href=\"";
			$description.= "http://php.planetmirror.com/manual/en/features.safe-mode.php";
			$description.= "\">safe mode</a> ";
			$description.= "enabled for php. This limits the possibilities ";
			$description.= "of this script as well as your phpWebSite.";

			$status=$this->_WARN;

		} else {

			$description = "PHP safe mode is disabled. That's good, ";
			$description.= "because otherwise phpWebSite and this script ";
			$description.= "would not be able the way they are meant to ";
			$description.= "run.";

			$status=$this->_OK;
		}

		$result=array();

		$result['what']="Safe Mode";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}



	function queryFileUploads() {

		$status=$this->_ERROR;
		$uploads_allowed=ini_get("file_uploads");


		if($uploads_allowed <= 0)
			$uploads_allowed=false;
		else
			$uploads_allowed=true;


		if($uploads_allowed) {
			$description = "php is configured to allow file uploads ";
			$description.= "via http. That's good!";

			$status=$this->_OK;
		} else {

			$description = "You currently cannot upload any files via ";
			$description.= "http to your phpWebSite. This renders your ";
			$description.= "installation pretty useless. Please enable ";
			$description.= "file uploads in your php.ini.";

			$status=$this->_ERROR;
		}

		$result=array();

		$result['what']="File Uploading";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}



	function queryRegisterGlobals() {

		$status=$this->_ERROR;
		$register_globals=ini_get("register_globals");

		if($register_globals) {

			$description = "You have register_globals enabled. This is ";
			$description.= "a ";
			$description.= "<a href=\"http://www.php.net/register_globals\">";
			$description.= "security risk</a> and should be changed as soon ";
			$description.= "as possible.";

			$status=$this->_ERROR;

		} else {

			$description.= "Register Globals disabled.";
			$status=$this->_OK;

		}


		$result=array();
		$result['what']="Register Globals";
		$result['code']=$status;
		$result['text']=$description;

		return($result);


	}



	function queryOS() {

		$status=$this->_ERROR;

		$windows=$this->isRunningWindows();
		$unix=$this->isRunningUnix();

		$os=php_uname("s v");

		if($windows) {

			$description = "Your server is running ";
			$description.= "${os}. There are a couple of ";
			$description.= "phpWebSites out there running ";
			$description.= "Windows, still it's rather ";
			$description.= "unsupported. Please consider ";
			$description.= "getting a host using Linux for ";
			$description.= "your site.";

			$status=$this->_WARN;

		} else
		if($unix) {
	
			$description = "Your server is running ${os} which ";
			$description.= "seems to be some kind of Unix ";
			$description.= "derivate. That's good, you should be ";
			$description.= "on the safe side.";

			$status=$this->_OK;

		} else {

			$description = "Your server is running ${ok} which ";
			$description.= "we probably don't know too much ";
			$description.= "about. Please consider Linux.";

			$status=$this->_ERROR;
		}

		$result=array();

		$result['what']="Operating System";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}


	function queryPHPVersion() {
		$status=$this->_ERROR;

		if(version_compare(phpversion(), "4.3.4", "<")) {

			$description = "You are currently running php version ";
			$description.= phpversion().", which is even older than ";
			$description.= "the minium requirement of php 4.3.4. Please ";
			$description.= "upgrade to a current version.";

			$status=$this->_ERROR;

		} else
		if(version_compare(phpversion(), "4.3.10", "<")) {

			$description = "You are running a php version ".phpversion().", ";
			$description.= "which is older than 4.3.10. php prior to 4.3.10 ";
			$description.= "has ";

			$description.= "<a href=\"http://www.php.net/ChangeLog-4.php\#4.3.10\">";
			$description.= "a couple of security holes</a>.";

			$status=$this->_WARN;

		} else {

			$description = "You are using php version ".phpversion().". ";
			$description.= "Looks very recent to me.";

			$status=$this->_OK;
		}

		$result=array();

		$result['what']="PHP Version";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}



	function queryGDVersion() {

		$status=$this->_ERROR;


		$php_ok=version_compare(phpversion(), "4.3.0", ">=");

		$func=function_exists("imagecreate");



		if($php_ok && $func) {

			$description = "You are running php 4.3.0 or greater which ";
			$description.= "has the GD image manipulation libraries ";
			$description.= "built in.";

			$status=$this->_OK;

		} else
		if(!$php_ok && $func) {

			$description = "You are php prior to 4.3.0 but have ";
			$description.= "GD support compiled in. While photoalbum ";
			$description.= "and other modules will work, you might ";
			$description.= "encounter security problems.";

			$status=$this->_ERROR;

		} else
		if($php_ok && !$func) {

			$description = "Even though you are running php 4.3.0 or newer, ";
			$description.= "you don't have the imagecreate function of ";
			$description.= "GD. This is really odd. Please contact your ";
			$description.= "host about that.";

			$status=$this->_ERROR;
		
		} else {

			$description = "You have php prior to 4.3.0. What's even more: ";
			$description.= "Your php hasn't compiled the GD libary in ";
			$description.= "either. Please contact your host about that.";

			$status=$this->_ERROR;	
		}


		$result=array();

		$result['what']="GD Support";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}




	function queryApacheVersion() {


		$status=$this->_ERROR;


		if(version_compare(phpversion(), "4.3.4", "<="))
			$version="version_to_old";
		else
		if(!function_exists("apache_get_version"))
			$version="method_not_available";
		else
			$version=apache_get_version();

		if($version=="version_to_old") {
			$description = "Your current php ".phpversion()." is to old. ";
			$description.= "Please install the newest php/4 from its homepage. ";

			$status=$this->_ERROR;

		} else
		if($version=="method_not_available") {

			$description = "The php-method apache_get_version is not ";
			$description.= "available in you php installation. Are you ";
			$description.= "running a cgi'd version of php? Did you compile ";
			$description.= "Apache-support in?";

			$status=$this->_ERROR;

		} else
		if($version==FALSE) {

			$description = "Your server is running phpWebSite on a ";
			$description.= "unsupported webserver. While you might ";
			$description.= "be lucky and get everything working, ";
			$description.= "we recommend running phpWebSite on a ";
			$description.= "<a href=\"http://www.apache.org\">Apache ";
			$description.= "Webserver</a>";

			$status=$this->_WARN;

		} else {

			$description = "You are running Apache <em>".$version."</em>. ";
			$description.= "This webserver is fully supported by ";
			$description.= "phpWebSite.";

			$status=$this->_OK;
		}


		$result=array();

		$result['what']="Webserver";
		$result['code']=$status;
		$result['text']=$description;

		return($result);

	}



	function queryImageDirectory() {

		$status=$this->_ERROR;


		$dir = PHPWS_SOURCE_DIR."images/";

		$exists=file_exists($dir);
		$writeable=is_writeable($dir);


		if($exists && $writeable) {

			$description = "The image directory of your site ($dir) is ";
			$description.= "writeable.";

			$status=$this->_OK;

		} else
		if(!$exists) {

			$description = "The image directory of your site ($dir) ";
			$description.= "does not exist.";

			$status=$this->_ERROR;

		} else
		if(!$writeable) {

			$description = "The image directory of your site ($dir) ";
			$description.= "is not writeable. Please change that.";

			$status=$this->_ERROR;

		}

		$result=array();

		$result['what']="Image Directory";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}



	function renderSetupDirLink($caption) {

		$link.= "<a href=\"";
		$link.= "http://www.nexusportal.net/showthread/t-2455.html";
		$link.= "\">";

		$link.= $caption;

		$link.= "</a>";

		return($link);

	}


	function querySetupDirectory() {

		$status=$this->_ERROR;


		$dir = PHPWS_SOURCE_DIR."setup/";

		$exists=file_exists($dir);


		if($exists) {

			$description = "You still have the setup directory of ";
			$description.= "your phpWebSite ($dir). This is a ";

			$description.= $this->renderSetupDirLink("security risk");

			$description.= ". Please remove it as soon ";
			$description.= "as possible.";

			$status=$this->_ERROR;

		} else {

			$description = "Your setup directory does not exist ";
			$description.= "any more. This is good, as it gives ";
			$description.= "you ";

			$description.= $this->renderSetupDirLink("a bit more security");

			$description.= ".";

			$status=$this->_OK;

		}

		$result=array();

		$result['what']="Setup Directory";
		$result['code']=$status;
		$result['text']=$description;

		return($result);
	}



	function queryCachingStatus() {

		$status=$this->_ERROR;

		if(CACHE) {

			$description = "You have caching enabled. For most sites, ";
			$description.= "this will decrease performance.";

			$status=$this->_WARN;

		} else {

			$description = "Caching is disabled for your site. Usually, ";
			$description.= "your site performance will benefit from this.";

			$status=$this->_OK;

		}

		$result=array();

		$result['what']="Caching";
		$result['code']=$status;
		$result['text']=$description;

		return($result);

	}



	function renderQuickStatus($treshold, $result) {

		$content=null;


		$error  =($result['code'] == $this->_ERROR) &&
			 ($treshold       <= $this->_ERROR);

		$warn   =($result['code'] == $this->_WARN) &&
			 ($treshold       <= $this->_WARN);

		$ok     =($result['code'] == $this->_OK) &&
			 ($treshold       <= $this->_OK);


		if($error) {

			$content="<h4>".$result['what']." - ERROR!</h4>";

			$content.="<p><strong>Error!</strong> ";
			$content.=$result['text']."</p>\n";

		} else
		if($warn) {

			$content="<h4>".$result['what']." - Warning</h4>\n";

			$content.="<p><em>Warning!</em> ";
			$content.=$result['text']."</p>\n";
			
		} else
		if($ok) {

			$content="<h4>".$result['what']." - OK</h4>\n";

			$content.="<p>".$result['text']."</p>\n";
		}


		return($content);
	}



	function count_matches($treshold, $results) {

		$matches=0;

		foreach($results as $nr => $row)
			if($row['code']>=$treshold)
				$matches++;

		return($matches);
	}



	function renderQuickStatusDescriptions($treshold, $results) {

		$content=null;

		$matches=$this->count_matches($treshold, $results);

		if($matches==0) {
			$content="<p>Every Message is below current ";
			$content.="treshold.</p>";
		} else {
			$content="<p>Showing $matches Messages.</p>";
		}

		foreach($results as $nr => $row) {

			$content.=$this->renderQuickStatus($treshold, $row);

		}

		return($content);
	}



	function calcStatusSummary($results) {

		$errors=0;
		$warnings=0;
		$oks=0;
		$others=0;

		foreach($results as $nr => $row) {

			if($row['code'] == $this->_ERROR)
				$errors++;
			else
			if($row['code'] == $this->_WARN)
				$warnings++;
			else
			if($row['code'] == $this->_OK)
				$oks++;
			else
				$others++;
		}


		$result=array();

		$result['errors']=$errors;
		$result['warnings']=$warnings;
		$result['oks']=$oks;
		$result['others']=$others;

		return($result);
	}



	function renderStatusSummary($result) {

		$errors=$result['errors'];
		$warnings=$result['warnings'];
		$oks=$result['oks'];
		$others=$result['others'];

		$content="<p>";

		if( ($errors==0) && ($warnings==0) && ($others==0) ) {

			$content.="Congratulations, all ".$oks." ";
			$content.="checks passed! ";

		} else {

			$content.=$oks." tests passed. ";
			$content.="However, there were $warnings warning(s) ";
			$content.="and $errors error(s) as well. ";
		}

		$content.="</p><p>";


		$linka=array();
		$linka['health_op']='overview';

		$content.="<ul>";

		$content.="<li>";

		$content.=PHPWS_Text::moduleLink('Overview', 'health',
			$linka);

		$content.="</li>\n<li>";
		$linka['health_op']='check';
		$content.=PHPWS_Text::moduleLink('view full report', 'health',
			$linka);
		$content.="</li>\n<li>";


		$linka['verbosity']='warn';
		$content.=PHPWS_Text::moduleLink('view only warnings and errors',
			'health', $linka);


		$content.="</li>\n<li>";

		unset($linka['verbosity']);
		$linka['health_op']='tools';
		$content.=PHPWS_Text::moduleLink('Tools',
			'health', $linka);

		$content.='</li></ul>';

		$content.="</p>\n";

		return($content);
	}



	function genReport() {

		$result=array();

		$result[]=$this->queryOpenBasedir();

		if(!$this->isOpenBasedirInEffect()) {
			$result[]=$this->queryRootSpace();
			$result[]=$this->queryTmpSpace();
			$result[]=$this->queryVarSpace();
			$result[]=$this->querySessionSpace();
			// $result[]=$this->querySessionFiles();
		}


		$result[]=$this->queryMemoryUsage();
		$result[]=$this->queryMemoryAllocation();
		$result[]=$this->querySafeMode();
		$result[]=$this->queryFileUploads();
		$result[]=$this->queryRegisterGlobals();

		$result[]=$this->queryOS();
		$result[]=$this->queryPHPVersion();
		$result[]=$this->queryGDVersion();
		$result[]=$this->queryApacheVersion();

		$result[]=$this->queryImageDirectory();
		$result[]=$this->querySetupDirectory();

		$result[]=$this->queryCachingStatus();

		return($result);
	}


	function menu($current) {

		$menua=array();
		$menua[]=array(	'caption'=>'Overview',
				'op'=>'overview');
		$menua[]=array(	'caption'=>'Report',
				'op'=>'check');
		$menua[]=array(	'caption'=>'Tools',
				'op'=>'tools');

		$html="<p>";
		$content=array();

		foreach($menua as $nr => $item)
			if($item['caption'] != $current) {

				$linka['health_op']=$item['op'];
				$content[]=PHPWS_Text::moduleLink(
					$item['caption'],
					'health', $linka);
			} else
				$content[]="[".$item['caption']."]";

		$html.=implode(" | ", $content);
		$html.="</p>\n";

		return($html);
	}


	function cookieCrumb($health_op, $tool, $tool_op, $item1) {


		$linka=array();
		$crumb=PHPWS_Text::moduleLink('control&nbsp;panel',
			'controlpanel', null);

		if(empty($health_op))
			return("<p>$crumb &gt; health</p>");

		$crumb.=" &gt; ".PHPWS_Text::moduleLink('health',
			'health', null);

		if(empty($tool))
			return("<p>$crumb &gt; $health_op</p>");

		$linka=array();
		$linka['health_op']=$health_op;

		$crumb.=" &gt; ".PHPWS_Text::moduleLink($health_op,
			'health', $linka);

		if(empty($item1))
			return("<p>$crumb &gt; $tool</p>");

		$linka['tool']=$tool;

		$crumb.=" &gt; ".PHPWS_Text::moduleLink($tool,
			'health', $linka);

		return("<p>$crumb &gt; $item1</p>");

	}



	function showMainMenu() {
		$isDeity=$_SESSION["OBJ_user"]->isDeity();

		if(!$isDeity) {
			$content = "<p>Please log in as a deity to ";
			$content.= "see the generated report.</p>";

			return($content);
		}

		if(!$this->_active) {
			$content.= "<p><em>The Health Module is currently disabled. ";
			$content.= "You can enable it in mod/health/conf/config.php.</em></p>\n";
		}


		$content.=$this->cookieCrumb(null, null, null, null);

		$content.="<h3>Health</h3>\n";

		$linka['health_op']='overview';
		$link=PHPWS_Text::moduleLink('Overview', 'health', $linka);
		$go  =PHPWS_Text::moduleLink('go!', 'health', $linka);
		$content.="<h4>$link</h4>";

		$content.="<p style='text-align:justify'>View various informations about your site at a glance. ";
		$content.="How high is your server load? How many users are there registered, ";
		$content.="how much is cached, how much content do you have?&nbsp;$go</p>";


		$linka['health_op']='check';
		$link=PHPWS_Text::moduleLink('Full Health Report', 'health', $linka);
		$go  =PHPWS_Text::moduleLink('go!', 'health', $linka);
		$content.="<h4>$link</h4>";

		$content.="<p style='text-align:justify'>A whole bunch of automated checks ensure that ";
		$content.="your site remains healthy. See the full report through this option.&nbsp;$go";


		$linka['health_op']='tools';
		$link=PHPWS_Text::moduleLink('Tools', 'health', $linka);
		$go  =PHPWS_Text::moduleLink('go!', 'health', $linka);
		$content.="<h4>$link</h4>";

		$content.="<p style='text-align:justify'>Got some nasty remains of some modules ";
		$content.="in your database? Want to change the position of boxes but they wouldn't ";
		$content.="move? Try your luck with these tools.&nbsp;$go</p>";


		$content.="<h3>Places to visit</h3>\n";

		$link="<a href='http://www.kiesler.at/article147.html'>Official Health Site</a>";
		$go  ="<a href='http://www.kiesler.at/article147.html'>go!</a>";

		$content.="<h4>$link</h4>\n";
		$content.="<p style='text-align:justify'>get the newest health-version here&nbsp;$go</p>\n";


		$link="<a href='http://phpwsforums.com/'>phpWebSite Support Forums</a>";
		$go  ="<a href='http://phpwsforums.com/'>go!</a>";

		$content.="<h4>$link</h4>\n";
		$content.="<p style='text-align:justify'>Your site is broken and you don't know ";
		$content.="how to fix it? You want to announce a module you did? Or simply hang ";
		$content.="around with nice people?&nbsp;$go</p>\n";


		$link="<a href='http://www.kiesler.at/index.php?module=phpwsbb&PHPWSBB_MAN_OP=list'>kiesler.at Forums</a>";
		$go  ="<a href='http://www.kiesler.at/index.php?module=phpwsbb&PHPWSBB_MAN_OP=list'>go!</a>";

		$content.="<h4>$link</h4>\n";
		$content.="<p>Want to reach the author of this program? Looking for other modules? \n";
		$content.="Have a site you want to show to others, want to talk about your dog, a shave ";
		$content.="and loud music? This is the place you shouldn't miss.&nbsp;$go</p>\n";


		$link=PHPWS_Text::moduleLink('back', 'controlpanel', null);

		$content.="<p>$link</p>";

		return($content);

	}



	function showOverview($tool=null, $tool_op=null, $item1=null, $item2=null) {
		$isDeity=$_SESSION["OBJ_user"]->isDeity();

		if(!$isDeity) {
			$content = "<p>Please log in as a deity to ";
			$content.= "see the generated report.</p>";

			return($content);
		}

		// $content=$this->menu("Overview");
	
		if(!$this->_active) {
			$content.= "<p><em>The Health Module is currently disabled. ";
			$content.= "You can enable it in mod/health/conf/config.php.</em></p>\n";
		}


		$content.=$this->cookieCrumb("overview", $tool, $tool_op, $item1);

		// $content.="<h3>Overview</h3>\n";

		$content.="<h3>Infrastructure</h3>\n";

		$data=getInfrastructure();

		$content.=renderOverview($data);


		$content.="<h3>Content</h3>\n";

		$data=getContent();

		$content.=renderOverview($data);


		$link=PHPWS_Text::moduleLink('back', 'health', null);

		$content.="<p>$link</p>";

		return($content);
	}



	function showReport($tool=null, $tool_op=null, $item1=null, $item2=null) {


		$isDeity=$_SESSION["OBJ_user"]->isDeity();

		if(!$isDeity) {
			$content = "<p>Please log in as a deity to ";
			$content.= "see the generated report.</p>";

			return($content);
		}

		// $content=$this->menu("Report");

	
		if(!$this->_active) {
			$content.= "<p><em>The Health Module is currently disabled. ";
			$content.= "You can enable it in mod/health/conf/config.php.";
			$content.= "</em></p>";
		}


		$content.=$this->cookieCrumb("report", $tool, $tool_op, $item1);

		$results=$this->genReport();
		$verbosity=$_REQUEST["verbosity"];

		if($verbosity=="warn") {
			$content.="<h3>Showning Warnings + Errors</h3>";
			$tres=$this->_WARN;
		} else {
			$content.="<h3>Full Report</h3>";
			$tres=$this->_OK;
		}

		$content.=$this->renderQuickStatusDescriptions($tres, $results);


		$link=PHPWS_Text::moduleLink('back', 'health', null);

		$content.="<p>$link</p>";

		return($content);
	}



	function toolSelector() {

		$html = "<h4>Maintenance</h4>\n";

		$html.= "<ul>\n";


		$linka=array();
		$linka['health_op']='tools';

		$html.= "<li>";
		$linka['tool']='clear_cache';

		$html.= PHPWS_Text::moduleLink(
			'Clear phpWebSite Cache Table',
			'health', $linka);

		$html.= "</li><li>\n";

		$linka['tool']='show_uservars';

		$html.= PHPWS_Text::moduleLink(
			'show User variables',
			'health', $linka);

		$html.= "</li><li>\n";

		$linka['tool']='force_remove';

		$html.= PHPWS_Text::moduleLink(
			'Show where phpWebSite modules are',
			'health', $linka);
		
		$html.=" -- this will become a module remover ";
		$html.="in a later version</li></ul>\n";


		$html.= "<h4>Layout</h4>\n";

		$html.= "<ul>\n";
		$html.= "<li>";

		$linka['tool']='box_mover';

		$html.= PHPWS_Text::moduleLink(
			'Show Theme-Boxes', 'health', $linka);

		$html.= " -- this will become a box mover ";
		$html.= "as soon as i know how to do it.</li></ul>\n";

		return($html);

	}




	function showTools($tool, $tool_op, $item1, $item2) {
		$isDeity=$_SESSION["OBJ_user"]->isDeity();

		if(!$isDeity) {
			$content = "<p>Please log in as a deity to ";
			$content.= "see the generated report.</p>";

			return($content);
		}

		// $content=$this->menu("Tools");
	
		if(!$this->_active) {
			$content.= "<p><em>The Health Module is currently disabled. ";
			$content.= "You can enable it in mod/health/conf/config.php.";
			$content.="</em></p>";
		}

		// $content.="<h3>Tools</h3>\n";

		$content.=$this->cookieCrumb("tools", $tool, $tool_op, $item1);


		if(empty($tool)) {
			$link=PHPWS_Text::moduleLink('back', 'health', null);

			$content.=$this->toolSelector();
			$content.="<p>$link</p>";
		} else
		if($tool=='clear_cache') {
			$content.=showClearCache($tool_op, $item1, $item2);
		}
		else
		if($tool=='force_remove')
			$content.=showForceRemove($tool_op, $item1, $item2);
		else
		if($tool=='box_mover')
			$content.=showBoxMover($tool_op, $item1, $item2);
		else
		if($tool=='show_uservars')
			$content.=showUservars($tool_op, $item1, $item2);
		else {
			$content.= "<em>don't know about tool &ldquo;$tool&rdquo;</em>";
			$content.=$this->toolSelector();
		}


		return($content);
	}



	function showStatus() {


		$isDeity=$_SESSION["OBJ_user"]->isDeity();


		if($isDeity) {

			if($this->_active) {

				$results=$this->genReport();
				$tres=$this->_WARN;

				$summary=$this->calcStatusSummary($results);
				$content=$this->renderStatusSummary($summary);

			} else {

				$content = "<p><em>The Health Module is currently disabled. ";
				$content.= "You can enable it in ";
				$content.= "mod/health/conf/config.php.</em></p>";
			}
		}


		// please don't remove this.

		$link = "<a href=\"http://www.kiesler.at\"";
		$link.= ">kiesler.at</a>";


		$content.="<p>Health ".$this->_health_version;
		$content.=" by ".$link."</p>";

		return($content);
	}



   } 


?>
