<?php	//
	//
	// Health Module for phpWebSite
	//
	// (c) 2005 rck <http://www.kiesler.at/>
	//
	//



	define("OK", "0");
	define("WARN", "1");
	define("ERROR", "2");

	$core=null;



	function getHealthPath() {

		return(realpath("."));

	}

	function getPWSPath() {

		return(realpath("../.."));

	}



	function queryHealthFiles() {

		$health=getHealthPath();


		$files=array();

		$files[]="index.php";
		$files[]="manual.php";
		$files[]="class/health.php";
		$files[]="boost/install.php";
		$files[]="boost/uninstall.php";
		$files[]="conf/boost.php";
		$files[]="conf/config.php";
		$files[]="conf/controlpanel.php";
		$files[]="conf/layout.php";
		$files[]="img/health.gif";
		$files[]="inc/runtime.php";


		$missing=array();

		foreach($files as $nr => $file)
			if(!file_exists($health."/".$file))
				$missing[]=$file;


		$result['name']="Health Files";

		if(count($missing)>0) {

			$result['code']=ERROR;

			$result['text']="<p>The following files are missing in $health: <ul>\n";

			foreach($missing as $nr => $file)
				$result['text'].="<li>$health/$file</li>\n";

			$result['text'].="</ul></p>\n";
			$result['text'].="<p>Please install them and try again.</p>";

		} else {


			$result['code']=OK;

			$result['text'] ="<p>found all ".count($files)." files of ";
			$result['text'].="health in $health.</p>";
		}


		return($result);

	}


	function queryHealthSanity() {

		$health=getHealthPath()."/class/health.php";

                require_once(getHealthPath()."/conf/boost.php");

		$md5=md5_file($health);



		// 0.2 and 0.3 are the same -- the only new thing
		// in 0.3 is manual.php

		$checksums["0.2"]="3debd68f3e7a95a6f48e53dc30dd45d4";
		$checksums["0.3"]="3debd68f3e7a95a6f48e53dc30dd45d4";
		$checksums["0.4"]="07b2c40221fde34bd326c1b8e6fd4d72";
		$checksums["1.0"]="46fe01ac7bbd18495782d8e8cb12d968";
		$checksums["1.1"]="b1c87dffe4ad694f55e491ec9e92439e";


		$available=!empty($checksums[$version]);

		$result=array();
		$result['name']="Health Sanity";


		if(!$available) {

			$result['code']=ERROR;
			$result['text'] ="<p>Don't know about Health $version -- ";
			$result['text'].="did you write it yourself? Anyway, the ";
			$result['text'].="MD5 checksum I got is ${md5}.</p>";

		} else
		if($md5 == $checksums[$version]) {

			$result['code'] =OK;
			$result['text'] ="<p>The checksum of your installed ";
			$result['text'].="Health $version matches with the one ";
			$result['text'].="I know about.</p>";

		} else {

			$result['code'] = ERROR;

			$result['text'] = "<p>This Health $version has been tampered ";
			$result['text'].= "with. Please download the newest version from ";

			$result['text'].= "<a href=\"http://www.kiesler.at/article147.html\">kiesler.at</a>, ";

			$result['text'].= "reinstall it and run this check again. If this error ";
			$result['text'].= "persists, please contact the Author via ";

			$result['text'].= "<a href=\"http://www.kiesler.at/index.php?module=phpwsbb&PHPWSBB_MAN_OP=list\">";
			$result['text'].= "his forums</a>.";

		}

		return($result);
	}


	function queryPWSFiles() {

		$pws=getPWSPath();

		$files=array();

		$files[]="index.php";
		$files[]="conf/allowedImageTypes.php";
		$files[]="conf/cache.php";
		$files[]="conf/core_info.php";
		$files[]="conf/dateSettings.en.php";
		$files[]="conf/javascriptSettings.php";
		$files[]="conf/textSettings.php";

		$files[]="core/Array.php";
		$files[]="core/Cache.php";
		$files[]="core/Core.php";
		$files[]="core/Database.php";
		$files[]="core/DateTime.php";
		$files[]="core/Debug.php";
		$files[]="core/EZelement.php";
		$files[]="core/EZform.php";
		$files[]="core/Error.php";
		$files[]="core/File.php";
		$files[]="core/Form.php";
		$files[]="core/Item.php";
		$files[]="core/List.php";
		$files[]="core/Manager.php";
		$files[]="core/Message.php";
		$files[]="core/Pager.php";
		$files[]="core/Template.php";
		$files[]="core/Text.php";
		$files[]="core/WizardBag.php";

		$files[]="core/img/manager/down_pointer.png";
		$files[]="core/img/manager/sort_none.png";
		$files[]="core/img/manager/up_pointer.png";
		$files[]="core/img/manager/up_pointer.png";

		$files[]="templates/defaultList.tpl";
		$files[]="templates/defaultRow.tpl";
		$files[]="templates/error.tpl";
		$files[]="templates/message.tpl";


		$missing=array();

		foreach($files as $nr => $file)
			if(!file_exists($pws."/".$file))
				$missing[]=$file;

		$result['name']="phpWebSite Files";

		if(count($missing)>0) {

			$result['code']=ERROR;

			$result['text']="<p>The following files are missing in $pws: <ul>\n";

			foreach($missing as $nr => $file)
				$result['text'].="<li>$pws/$file</li>\n";

			$result['text'].="</ul></p>\n";
			$result['text'].="<p>Please install them and try again.</p>";

		} else {

			$result['code']=OK;

			$result['text'] ="<p>found all ".count($files)." files of ";
			$result['text'].="phpWebSite in $pws</p>";

		}

		return($result);
	}


	function querySourceDir() {
		$result['name']="Source Directory";

                $pws=getPWSPath();

		if(substr($pws, -1) != DIRECTORY_SEPARATOR)
			$pws.=DIRECTORY_SEPARATOR;

		$config=$pws."conf/config.php";


		if(!file_exists($config)) {

			$result['code']=ERROR;

			$result['text'] ="<p>I need a valid configuration file. Couldn't find ";
			$result['text'].="$config</p>";

			return($result);

		}

		require($config);


		if($pws == $source_dir) {

			$result['code']=OK;

			$result['text'] ="<p>phpWebSite source directory correctly ";
			$result['text'].="set to $pws</p>";

		} else {

			$result['code']=ERROR;

			$result['text'] ="<p>phpWebSite source directory wrong. ";
			$result['text'].="You conf/config.php says, it's $source_dir ";
			$result['text'].="but it looks more like $pws to me!</p>";
		}

		return($result);
	}


	function queryDatabase() {

		$result['name']="php MySQL connectivity";

		$pws=getPWSPath();

		if(substr($pws, -1) != DIRECTORY_SEPARATOR)
			$pws.=DIRECTORY_SEPARATOR;

		$config=$pws."conf/config.php";
		
		if(!file_exists($config)) {

			$result['code']=ERROR;

			$result['text'] ="<p>I need a valid configuration file. Couldn't find ";
			$result['text'].="$config</p>";

			return($result);

		}

		require($config);


		$result['code']=ERROR;

		if(empty($dbversion)) {

			$result['text'] = '<p>The variable <em>$dbversion</em> is ';
			$result['text'].= "not set in $config. ";
			$result['text'].= 'If you cannot create that file interactively ';
			$result['text'].= '(using the setup-directory) please create it ';
			$result['text'].= 'yourself.</p>';
			$result['text'].= '<p>Manual mode needs at least ';
			$result['text'].= '$source_dir, $dbversion, $dbhost, $dbuser, ';
			$result['text'].= '$dbpass and $dbname in it to continue.</p>';

			$result['code']=ERROR;
			return($result);
		} else
		if($dbversion != 'mysql') {

			$result['text'] = "<p>So... You are running a $dbversion database, ";
			$result['text'].= "right? I only know about mysql. Please tell ";
			$result['text'].= "me about your database in ";
			$result['text'].= "<a href=\"http://www.kiesler.at/index.php?";
			$result['text'].= "module=phpwsbb&PHPWSBB_MAN_OP=list\">";
			$result['text'].= "my forum</a>, so I can support it in a future ";
			$result['text'].= "of health.</p>";
			$result['text'].= "<p>Test skipped.</p>";

			$result['code']=OK;

			return($result);
		}


		if(!function_exists("mysql_connect")) {

			$result['text'] = "<p>Your php does not support the function ";
			$result['text'].= "<em>mysql_connect</em>. Are you sure you've ";
			$result['text'].= "compiled the mysql support in?</p>";

			$result['code']=ERROR;

			return($result);

		}


		$link=@mysql_connect($dbhost, $dbuser, $dbpass);

		if(!$link) {


			$result['text'] = "<p>Could not connect to host ";
			$result['text'].= "&bdquo;$dbhost&ldquo; with user ";
			$result['text'].= "&bdquo;$dbuser&ldquo; and the password you've ";
			$result['text'].= "provided in conf/config.php.</p>";

			$errno=mysql_errno();
			$error=mysql_error();
		       	$result['text'].= "<p>MySQL Error $errno: $error</p>";

			$result['code']=ERROR;

			return($result);

		}


		$ping=@mysql_ping($link);

		if(!$ping) {

			$result['text'] = "<p>What the ?!#!... The connection immediately ";
			$result['text'].= "closed after the connect. This is very weird, ";
			$result['text'].= "to say the least.</p>";

			$errno=mysql_errno();
			$error=mysql_error();
		       	$result['text'].= "<p>MySQL Error $errno: $error</p>";

			$result['code']=ERROR;

			return($result);
		}


		$db_selected=@mysql_select_db($dbname, $link);

		if(!$db_selected) {

			$result['text'] = "<p>The connection to mysql was succesful, so ";
			$result['text'].= "hostname, username and password are ok. Still, ";
			$result['text'].= "I couldn't select the db &bdquo;$dbname&ldquo;. ";
			$result['text'].= "Please make sure it exists and dbuser &bdquo;$dbuser&ldquo; ";
			$result['text'].= "has the proper rights to access it.</p>";

			$errno=mysql_errno();
			$error=mysql_error();
		       	$result['text'].= "<p>MySQL Error $errno: $error</p>";

			$result['code']=ERROR;

			return($result);
		}


		$closed=@mysql_close($link);

		if(!$closed) {

			$result['text'] = "<p>Could not close connection to MySQL.</p>";

			$errno=mysql_errno();
			$error=mysql_error();
		       	$result['text'].= "<p>MySQL Error $errno: $error</p>";

			$result['code']=ERROR;

			return($result);
		}

		$result['text'] = "<p>php can access your mysql installation with ";
		$result['text'].= "the parameters provided in $config</p>";

		$result['code']=OK;

		return($result);

	}



	function queryPEAR_DB() {
                $result['name']="PEAR::DB check";

                $pws=getPWSPath();

                if(substr($pws, -1) != DIRECTORY_SEPARATOR)
			$pws.=DIRECTORY_SEPARATOR;

                $config=$pws."/conf/config.php";

                if(!file_exists($config)) {

                        $result['code']=ERROR;

                        $result['text'] ="<p>I need a valid configuration file. Couldn't find ";
                        $result['text'].="$config</p>";

                        return($result);

                }

                require($config);
		require_once($pws."/lib/pear/DB.php");

		$connect_string="$dbversion://$dbuser:$dbpass@$dbhost/$dbname";

		$db=DB::connect($connect_string);


		if(DB::isError($db)) {

			$result['code']=ERROR;

			$result['text'] = "<p>Connect to your database through PEAR::DB ";
			$result['text'].= "failed.</p>";

			$error=$db->getMessage();
			$debug=$db->getDebugInfo();
			$result['text'].= "<p>$error<br />Debug-Info: $debug</p>";
			return($result);

		}


		$result['code']=OK;
		$result['text'] = "<p>The PEAR::DB Connection works.</p>";

		return($result);
	}


	function quickRender($result) {


		$html="<h2>".$result['name'];

		if($result['code']==OK)
			$html.=" - OK";
		else
		if($result['code']==WARN)
			$html.=" - Warning";
		else
		if($result['code']==ERROR)
			$html.=" - ERROR";

		$html.="</h2>\n";


		$html.=$result['text'];

		return($html);
	}



	echo "<h1>Health MANUAL MODE</h1>\n";
	echo "<p>Health is a phpWebSite Module brought to you by ";
	echo "<a href=\"http://www.kiesler.at/\">kiesler.at</a></p>\n";


	$result=queryHealthFiles();
	echo(quickRender($result));

	if($result['code'] != OK)
		exit;

	$result=queryHealthSanity();
	echo(quickRender($result));

	if($result['code'] != OK)
		exit;

	$result=queryPWSFiles();
	echo(quickRender($result));

	$result=querySourceDir();
	echo(quickRender($result));

	if($result['code'] != OK)
		exit;

	$result=queryDatabase();
	echo(quickRender($result));

	$result=queryPEAR_DB();
	echo(quickRender($result));
?>
