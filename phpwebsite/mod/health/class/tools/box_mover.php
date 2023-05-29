<?php	// box_mover.php -- part of health <http://www.kiesler.at/article147.html>



	function enumerateThemes() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT distinct theme ";
		$sql.= "FROM ${prefix}mod_layout_box ";
		$sql.= "ORDER BY theme";

		return($GLOBALS['core']->getCol($sql));
	}



	function enumerateBoxes($theme) {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql = "SELECT id, mod_title, content_var, ";
		$sql.= "theme_var, box_order ";
		$sql.= "FROM ${prefix}mod_layout_box ";
		$sql.= "WHERE theme='$theme' ";
		$sql.= "ORDER BY box_order ";

		return($GLOBALS['core']->getAllAssoc($sql));
	}


	function presentThemes() {

		$html="";

		$themes=enumerateThemes();

		if(empty($themes))
			$html.="<p>no themes in mod_layout_box!</p>\n";
		else {

			$html.="<p>Please click on a theme-title ";
			$html.="to choose it.</p>\n";

			$html.="<ul>\n";

			$linka=array();
			$linka['health_op']='tools';
			$linka['tool']='box_mover';

			foreach($themes as $nr => $theme) {

				$linka['item1']=$theme;

				$html.="<li>";
				$html.=PHPWS_Text::moduleLink($theme,
					'health', $linka);
				$html.="</li>\n";
			}

			$html.="</ul>\n";
		}

		return($html);
	}


	function showContentVars($boxes, $theme_var) {

		$html= "";

		foreach($boxes as $nr => $box)
			if($theme_var==$box['theme_var']) {
				$box_order=$box['box_order'];
				$content_var=$box['content_var'];

				$html.="<h4>$box_order:&nbsp;$content_var</h4>\n";
			}

		return($html);

	}


	function showTheme($theme) {

		$boxes=enumerateBoxes($theme);

		$html.="<table border='1'>\n";
		$html.="<tr><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;left_col_top</h4>";
		$html.= showContentVars($boxes, 'left_col_top');
		$html.="</td><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;top</h4>";
		$html.= showContentVars($boxes, 'top');
		$html.="</td><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;right_col_top</h4>";
		$html.= showContentVars($boxes, 'right_col_top');
		$html.="</td></tr>\n";

		$html.="<tr><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;left_col_mid</h4>";
		$html.= showContentVars($boxes, 'left_col_mid');
		$html.="</td><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;body</h4>";
		$html.= showContentVars($boxes, 'body');
		$html.="</td><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;right_col_mid</h4>";
		$html.= showContentVars($boxes, 'right_col_mid');
		$html.="</td></tr>\n";

		$html.="<tr><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;left_col_bottom</h4>";
		$html.= showContentVars($boxes, 'left_col_bottom');
		$html.="</td><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;bottom</h4>";
		$html.= showContentVars($boxes, 'bottom');
		$html.="</td><td valign='top' align='left'>\n";
		$html.="<h4>$theme:&nbsp;right_col_bottom</h4>";
		$html.= showContentVars($boxes, 'right_col_bottom');
		$html.="</td></tr>\n";

		$html.="</table>\n";

		return($html);

	}


	function showBoxMover($op, $item1, $item2) {

		// item1: theme-name
		// item2: conten-var to move
		// op: move_top, move_middle, move_bottom
		//     move_left, move_center, move_right
		//	up, down

		$theme=$item1;

		$linka=array();
		$linka['health_op']='tools';

		$html="";


		if(empty($theme)) {
			$html.="<h3>Where are the boxes?</h3>";
			$html.=presentThemes();
		} else {

			$html.="<h3>Showing boxes of $theme</h3>";
			$linka['tool']='box_mover';
			$html.=showTheme($theme);
		}


		$link=PHPWS_Text::moduleLink('back',
			'health', $linka);

		$html.="<p>$link</p>\n";

		return($html);

	}
?>
