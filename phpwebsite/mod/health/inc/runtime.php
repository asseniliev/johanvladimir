<?php

/**
 * Health module for phpWebSite
 *
 * @author rck <http://www.kiesler.at/>
 */

require_once(PHPWS_SOURCE_DIR . "mod/health/class/health.php");

$block = new PHPWS_health;

if ($status = $block->showStatus()) {
	$CNT_health_status["title"] = $_SESSION["translate"]->it("Site Health");
	$CNT_health_status["content"] = $status;
}
?>
