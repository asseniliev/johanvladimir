<?php

/* Graph Settings */

define('LNG_Y_AXIS', 'Visits');
define('LNG_X_AXIS_MONTH', 'Days');
define('LNG_X_AXIS_YEAR', 'Months');
define('LNG_ERROR_KEY', 'Invalid key passed to view graphs.  Please login before viewing graphs.');
define('LNG_ERROR_PEAR', 'Missing required pear library: Calendar/Engine/PearDate.php');
define('LNG_JAN', 'Jan');
define('LNG_FEB', 'Feb');
define('LNG_MAR', 'Mar');
define('LNG_APR', 'Apr');
define('LNG_MAY', 'May');
define('LNG_JUN', 'Jun');
define('LNG_JUL', 'Jul');
define('LNG_AUG', 'Aug');
define('LNG_SEP', 'Sep');
define('LNG_OCT', 'Oct');
define('LNG_NOV', 'Nov');
define('LNG_DEC', 'Dec');

/* Stat Settings */

define('ANNOUNCEMENTS',           1);
define('COMMENTED_ANNOUNCEMENTS', 2);
define('LINKS',                   3);
define('ANNOUNCEMENT_SUBMITTERS', 4);
define('FAQS',                    5);
define('RECENT_PHOTOS',           6);
define('RECENT_FILES',            7);
define('NOTES_SENT',              8);
define('NOTES_RECIEVED',          9);
define('RECENT_USERS',           10);
define('RECENT_COMMENTS',        11);
define('HIGHEST_RATED_FAQS',     12);
define('PAGEMASTER_PHITS',       13);
define('RECENT_PAGE_UPDATES',    14);

/* Web Stats - Graph Colors */

define('GRAPH_BG',      '0xFFFFFF');
define('GRAPH_BAR',     '0xFF4411');
define('GRAPH_EDGES',   '0x444475');
define('GRAPH_GRID',    '0xC0C0C0');
define('GRAPH_XLABELS', '0x444475');
define('GRAPH_YLABELS', '0x444475');


/* Turn on ability for adding advanced counters.  Allows any table in the
 * database to be tracked.
 */
define('SHOW_ADV_ADD_COUNTER',  FALSE);

?>