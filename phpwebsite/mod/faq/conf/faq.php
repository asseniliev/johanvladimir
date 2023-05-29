<?php
/**
 * Configuration file FAQ module.
 */

/* Default limit to use */
define("PHPWS_FAQ_NUMLEGENDITEMS", "5.0");

/* Layout Options */
define("PHPWS_FAQ_NOCAT_CLICKQUES_VIEW", 0);
define("PHPWS_FAQ_NOCAT_QA_VIEW", 1);
define("PHPWS_FAQ_CAT_VIEW", 2);

/* NO SPACES in image types list */
define("FAQ_IMAGE_TYPES", "image/gif,image/jpeg,image/jpg,image/pjpeg,image/png,image/x-png");

define("FAQ_DIR", PHPWS_HOME_DIR . "images/faq/");
define("FAQ_HTTP_DIR", "./images/faq/");
define("FAQ_CTOP_IMAGE_PREFIX", "customTopBullet");
define("FAQ_CSUB_IMAGE_PREFIX", "customSubBullet");

// sorting constants
define("FAQ_ORDERBY_COMPOSITE_SCORE", 0);
define("FAQ_ORDERBY_UPDATED", 1);
define("FAQ_ORDERBY_QUESTION", 2);

?>