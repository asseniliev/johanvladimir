<?php	// clear_cache.php -- part of health <http://www.kiesler.at/article147.html>



	function countCacheElements() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql="SELECT count(id) elements FROM ${prefix}cache";

		$data=$GLOBALS['core']->getAllAssoc($sql);

		return($data[0]['elements']);

	}



	function clearCache() {

		$prefix=$GLOBALS['core']->tbl_prefix;

		$sql="DELETE FROM ${prefix}cache";

		$data=$GLOBALS['core']->query($sql);

		return($data);
	}


	function showClearCache() {

		$linka=array();
		$linka['health_op']='tools';


		$link=PHPWS_Text::moduleLink('back',
			'health', $linka);


		$cache_elements=countCacheElements();

		if($cache_elements == 0) {
			$html ="<p>cache is empty, nothing to do!</p>\n";
			$html.="<p>$link</p>";

			return($html);
		}
		else
		if($cache_elements == 1)
			$html="<p>about to clear 1 cache entry...</p>\n";
		else
			$html="<p>about to clear $cache_elements cache entries...</p>\n";

		clearCache();

		$cache_elements_new=countCacheElements();

		if($cache_elements_new == 0)
			$html.="<p>Success!</p>\n";
		else
		if($cache_elements_new == 1)
			$html.="<p>There is still 1 cache entry left.</p>\n";
		else
			$html.="<p>There are still $cache_elements_new ";
				"cache entries left.</p>\n";
		
		$html.="<p>$link</p>";

		return($html);

	}
?>
