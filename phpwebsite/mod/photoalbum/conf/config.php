<?php

/**
 * @version $Id: config.php,v 1.11 2004/12/07 15:38:51 steven Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

define("PHOTOALBUM_DIR", "{$GLOBALS['core']->home_dir}images/photoalbum/");

define("PHOTOALBUM_TN_WIDTH", 150);
define("PHOTOALBUM_TN_HEIGHT", 150);

define("PHOTOALBUM_DEBUG_MODE", 0);

define("PHOTOALBUM_MAX_UPLOADS", 10);

/* 0 for newest first, 1 for oldest */
define("PHOTOALBUM_DEFAULT_SORT", 1);

define("PHOTOALBUM_MAX_WIDTH", 5000);
define("PHOTOALBUM_MAX_HEIGHT", 5000);

?>