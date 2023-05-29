<?php	// show_uservars.php -- part of health <http://www.kiesler.at/article147.html>


	function getUservars() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT u.username, v.varName, v.varValue ";
		$sql.=	"FROM ${prefix}mod_users u LEFT JOIN ";
	       	$sql.=	 "${prefix}mod_user_uservar v ";
		$sql.=		"ON u.user_id=v.user_id ";
		$sql.=	"ORDER BY username, varName ";

		return($GLOBALS['core']->getAllAssoc($sql));

	}


	function renderUservars($data) {

		if(empty($data) || sizeof($data) < 0)
			return('no user variables found!');


		$last_user=$data['username'];
		$vars=array();

		$html="<table>\n";

		$last=array();
		$last['username']=null;
		$last['varName']=null;
		$last['varValue']=null;

		$data[]=$last;

		foreach($data as $nr => $line) {

			$user=$line['username'];
			$key=$line['varName'];
			$value=$line['varValue'];

			if($user != $last_user) {

				if(isset($last_user) || sizeof($vars)>0) {

					$html.="<tr><th>$last_user</th>";

					if(sizeof($vars)<=0)
						$html.="<td><em>no variables</em></td>";
					else {
						$vars=implode($vars, '<br>');
						$html.="<td>$vars</td>";
					}

					$html.="</tr>\n";

				}

				$last_user=$user;
				$vars=array();

			}

			if(isset($key) || isset($value))
				$vars[]="$key=&ldquo;$value&rdquo;";

		}

		$html.="</table>\n";

		return($html);

	}


	function showUservars() {

		$linka=array();
		$linka['health_op']='tools';


		$link=PHPWS_Text::moduleLink('back',
			'health', $linka);


		$data=getUservars();

		$html.=renderUservars($data);

		$html.="<p>$link</p>";

		return($html);

	}
?>
