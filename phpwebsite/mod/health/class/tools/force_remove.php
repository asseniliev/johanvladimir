<?php	// force_remove.php -- part of health <http://www.kiesler.at/article147.html>



	function fetchFRModules() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct mod_title ";
		$sql.= "FROM ${prefix}modules";

		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRBoost() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct mod_title ";
		$sql.= "FROM ${prefix}mod_boost_version";


		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRLayout() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct mod_title ";
		$sql.= "FROM ${prefix}mod_layout_box";

		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRControlPanel() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct module ";
		$sql.= "FROM ${prefix}mod_controlpanel_link";

		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRFatcat() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct module_title ";
		$sql.= "FROM ${prefix}mod_fatcat_elements";

		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRSearch() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct module ";
		$sql.= "FROM ${prefix}mod_search";

		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRApproval() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct mod_title ";
		$sql.= "FROM ${prefix}mod_approval_jobs";

		return($GLOBALS['core']->getCol($sql));
	}


	function fetchFRDynamic() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct module_name ";
		$sql.= "FROM ${prefix}mod_dyn_modules";

		return($GLOBALS['core']->getCol($sql));
	}


	function moduleInTable($module_name, $table) {

		if(empty($table))
			return(false);

		if(is_array($table)) {
			foreach($table as $nr => $data)
				if($data == $module_name)
					return(true);
			return(false);
		}

		return($module_name == $table);
	}



	function genFRTable($modules, $results, $core, $flag, $link) {
		$html = "<table>\n";
		$html.= "<tr><th>module</th>";
		foreach($results as $module => $result)
			$html.="<th>$module</th>";
		if($link)
			$html.="<th>remove</th>";
		$html.= "</tr>\n";

		$unboosta=array();
		$unboosta['boost_op']='uninstallModule';

		foreach($modules as $name => $table)
			if(moduleInTable($name, $core)==$flag) {

				$html.="<tr><td>$name</td>";

				$table=explode(",", $table);

				foreach($results as $module => $result)
					if(moduleInTable($module, $table))
						$html.="<td>X</td>";
					else
						$html.="<td>&nbsp;</td>";

				if($link)
					if(moduleInTable("boost", $table)) {
						$unboosta['killMod']=$name;
						$link=PHPWS_Text::moduleLink(
							'unboost', 'boost',
							$unboosta);
						$html.="<td>$link</td>";

					}
					else
						$html.="<td>force</td>";

				$html.="</tr>\n";
			}

		$html.="</table>\n";

		return($html);
	}


	function showForceRemove() {

		$linka=array();
		$linka['health_op']='tools';


		$link=PHPWS_Text::moduleLink('back',
			'health', $linka);

		$core ="approval,boost,controlpanel,fatcat,help,language,";
		$core.="layout,search,security,users,core";

		$core=explode(",",$core);

		$results=array();
		$results['modules']=fetchFRModules();
		$results['boost']=fetchFRBoost();
		$results['layout']=fetchFRLayout();
		$results['controlpanel']=fetchFRControlPanel();
		$results['fatcat']=fetchFRFatcat();
		$results['search']=fetchFRSearch();

		/* I'm not sure about those. Let's leave them out for now.

		$results['approval']=fetchFRApproval();
		$results['dynamic']=fetchFRDynamic();
		*/

		$modules=array();

		foreach($results as $table_name=>$result)
			foreach($result as $nr => $module_name)
				if(empty($modules[$module_name]))
					$modules[$module_name]=$table_name;
				else
					$modules[$module_name].=",$table_name";

		$html.= "<h4>User Modules</h4>\n";

		/*
		$html.= "<p>If you remove a module through this ";
		$html.= "&ldquo;force remove&rdquo; tool, it will be ";
		$html.= "gone from the database. No questions asked. ";
		$html.= "Take care!</p>";
		*/

		$html.= "<p>Right now, this module can only show you ";
		$html.= "in which tables you find your modules. A later ";
		$html.= "version will be able to remove them as well.";

		$html.= genFRTable($modules, $results, $core, false, true);


		$html.= "<h4>Core Modules</h4>\n";
		$html.= "<p>The core modules can't be removed with ";
		$html.= "health for a reason. If one of them is gone, ";
		$html.= "your site won't work.</p>\n";
		$html.= "<p>Still, you might want to know the whereabouts ";
		$html.= "of your core modules.</p>\n";

		$html.= genFRTable($modules, $results, $core, true, false);

		$html.="<p>$link</p>\n";

		return($html);

	}
?>
