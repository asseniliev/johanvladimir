<?php	
	
	require_once(PHPWS_SOURCE_DIR."mod/boost/class/Boost.php");


	function query_uptime() {

		$stats=@exec('uptime 2>&1');

		if(strlen(trim($stats))==0)
			return('cannot exec uptime.');


		$uptime_format='#: ([\d.,]+),\s+([\d.,]+),\s+([\d.,]+)$#';

		$ok=preg_match($uptime_format, $stats, $uptime);

		if($ok)
			return(	"$uptime[1]".
				"&nbsp;&nbsp;".
				"$uptime[2]".
				"&nbsp;&nbsp;".
				"$uptime[3]");
		else
			return("Cannot parse uptime result &ldquo;".
				$stats."&rdquo;");

	}



	function query_users() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql ="SELECT count(user_id) users ";
		$sql.="FROM ${prefix}mod_users ";

		$user_count=$GLOBALS['core']->getAllAssoc($sql);
		$user_count=$user_count[0]['users'];


		$sql = "SELECT username ";
		$sql.= "FROM ${prefix}mod_users ";
		$sql.= "ORDER BY user_id DESC ";
		$sql.= "LIMIT 1";

		$newest_user=$GLOBALS['core']->getAllAssoc($sql);
		$newest_user=$newest_user[0]['username'];

		return("$user_count users, ".
			"newest $newest_user");

	}


	function query_modules() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(mod_title) modules ";
		$sql.= "FROM ${prefix}modules";

		$module_count=$GLOBALS['core']->getAllAssoc($sql);
		$module_count=$module_count[0]['modules'];

		$sql = "SELECT count(mod_title) active ";
		$sql.= "FROM ${prefix}modules ";
		$sql.= "WHERE active='on'";

		$active=$GLOBALS['core']->getAllAssoc($sql);
		$active=$active[0]['active'];

		if($module_count == $active)
			return("$module_count ".
				"modules installed, ".
				"all active.");
		else
			return("$module_count ".
		       		"modules installed, ".
				"$active active.");

	}


	function query_cache() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(id) elements ";
		$sql.= "FROM ${prefix}cache";

		$elements=$GLOBALS['core']->getAllAssoc($sql);
		$elements=$module_count[0]['elements'];

		if($elements == 0)
			return("nothing cached.");
		else
		if($elements == 1)
			return("1 item in cache.");
		else
			return("$elements item in cache.");

	}



	function getInfrastructure() {

		$result=array();

		$result['uptime']=query_uptime();
		$result['users']=query_users();
		$result['modules']=query_modules();
		$result['cache']=query_cache();

		return($result);
	}



	function is_module_installed($module) {
		$boost=new PHPWS_Boost;
		$ver=$boost->getVersionInfo($module);

		if($ver==FALSE)
			return(false);
		else
			return(true);
	}


	function query_comments() {

		if(!is_module_installed("comments"))
			return("not installed.");


		$prefix=$GLOBALS['core']->tbl_prefix;


		$sql = "SELECT count(cid) comments ";
		$sql.= "FROM ${prefix}mod_comments_data ";

		$comments=$GLOBALS['core']->getAllAssoc($sql);
		$comments=$comments[0]['comments'];

		$sql = "SELECT author, module, anonymous ";
		$sql.= "FROM ${prefix}mod_comments_data ";
		$sql.= "ORDER BY cid DESC ";
		$sql.= "LIMIT 1";

		$newest=$GLOBALS['core']->getAllAssoc($sql);
		$author=$newest[0]['author'];
		$module=$newest[0]['module'];
		$anonymous=$newest[0]['anonymous'];

		if($comments==1)
			$result="1 comment";
		else
			$result="$comments comments";

		if($comments>0) {
			$result.=", newest by ";
			
			if($anonymous)
				$result.="a guest ";
			else
				$result.="$author ";

			$result.="in $module";
		}

		$result.=".";

		return($result);

	}


	function query_notes() {

		if(!is_module_installed("notes"))
			return("not installed.");

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(id) notes ";
		$sql.= "FROM ${prefix}mod_notes ";

		$notes=$GLOBALS['core']->getAllAssoc($sql);
		$notes=$notes[0]['notes'];

		if($notes==1)
			$result="1 note.";
		else
			$result="$notes notes.";


		return($result);

	}



	function query_forum() {

		if(!is_module_installed("phpwsbb"))
			return("not installed.");

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(id) threads ";
		$sql.= "FROM ${prefix}mod_phpwsbb_threads ";

		$threads=$GLOBALS['core']->getAllAssoc($sql);
		$threads=$threads[0]['threads'];

		$sql = "SELECT count(id) messages ";
		$sql.= "FROM ${prefix}mod_phpwsbb_messages ";

		$messages=$GLOBALS['core']->getAllAssoc($sql);
		$messages=$messages[0]['messages'];


		if($threads==1)
			$thread_str="1 thread";
		else
			$thread_str="$threads threads";

		if($messages==1)
			$mess_str="1 message";
		else
			$mess_str="$messages messages";

		return(	"$thread_str with ".
			"$mess_str.");
	}


	function query_articles() {


		if(!is_module_installed("article"))
			return("not installed.");


		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(id) sections ";
		$sql.= "FROM ${prefix}mod_article_sections ";

		$sections=$GLOBALS['core']->getAllAssoc($sql);
		$sections=$sections[0]['sections'];

		$sql = "SELECT count(id) articles ";
		$sql.= "FROM ${prefix}mod_article ";

		$articles=$GLOBALS['core']->getAllAssoc($sql);
		$articles=$articles[0]['articles'];

		if($articles==1)
			$article_str="1 article";
		else
			$article_str="$articles articles";

		if($sections==1)
			$section_str="1 section";
		else
			$section_str="$sections sections";

		return(	"$article_str with ".
			"$section_str.");
	}


	function query_webpages() {

		if(!is_module_installed("pagemaster"))
			return("not installed.");

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(id) sections ";
		$sql.= "FROM ${prefix}mod_pagemaster_sections ";

		$sections=$GLOBALS['core']->getAllAssoc($sql);
		$sections=$sections[0]['sections'];

		$sql = "SELECT count(id) pages ";
		$sql.= "FROM ${prefix}mod_pagemaster_pages ";

		$pages=$GLOBALS['core']->getAllAssoc($sql);
		$pages=$pages[0]['pages'];

		if($pages==1)
			$page_str="1 page";
		else
			$page_str="$pages pages";

		if($sections==1)
			$section_str="1 section";
		else
			$section_str="$sections sections";

		return(	"$page_str with ".
			"$section_str.");
	}


	function query_announcements() {

		if(!is_module_installed("announce"))
			return("not installed.");

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT count(id) announcements ";
		$sql.= "FROM ${prefix}mod_announce ";

		$announcements=$GLOBALS['core']->getAllAssoc($sql);
		$announcements=$announcements[0]['announcements'];

		if($announcements==1)
			$ann_str="1 announcement.";
		else
			$ann_str="$announcements announcements.";

		return($ann_str);
	}


	function getContent() {

		$result=array();

		$result['announcements']=query_announcements();
		$result['articles']=query_articles();
		$result['comments']=query_comments();
		$result['forum']=query_forum();
		$result['notes']=query_notes();
		$result['webpages']=query_webpages();

		return($result);
	}


	function renderOverview($data) {


		$html ="<table>\n";

		foreach($data as $what => $result) {

			$html.="<tr><th>$what</th>";
			$html.="<td>$result</td></tr>\n";
		}

		$html.="</table>\n";


		return($html);
	}

?>
