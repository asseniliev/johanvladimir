<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: config.php,v 1.11 2005/05/23 12:53:22 darren Exp $
 */

//define("JAS_MAX_UPLOAD_NUM", 5);
if(!defined("JAS_MAX_UPLOAD_NUM"))
     define("JAS_MAX_UPLOAD_NUM", 5);

//define("JAS_MAX_UPLOAD_SIZE", 5120000);
if(!defined("JAS_MAX_UPLOAD_SIZE"))
     define("JAS_MAX_UPLOAD_SIZE", 5120000);

//define("JAS_RECENT_DOCUMENTS", 10);
if(!defined("JAS_RECENT_DOCUMENTS"))
     define("JAS_RECENT_DOCUMENTS", 7);

//define("JAS_DOCUMENT_TYPES", "application/x-gzip");
//define("JAS_DOCUMENT_TYPES", "all");  /* this will allow any type file to be uploaded (not recommended) */
if(!defined("JAS_DOCUMENT_TYPES"))
     define("JAS_DOCUMENT_TYPES", "text/plain,text/richtext,text/html,text/css,text/xml,application/octet-stream,application/postscript,application/rtf,application/applefile,application/mac-binhex40,application/wordperfect5.1,application/pdf,application/x-stuffit,application/zip,application/msword,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/xml,application/x-gzip,application/x-gunzip,application/x-zip-compressed,audio/basic,audio/mpeg,video/mpeg,video/quicktime");

//define("JAS_DOCUMENT_DIR", "files/documents/");
if(!defined("JAS_DOCUMENT_DIR"))
     define("JAS_DOCUMENT_DIR", "files/documents/");

//define("JAS_USE_USER_RIGHTS", TRUE);
// set to true if you would like to check each user to make sure
// they have the proper privilages enabled to view and download documents
if(!defined("JAS_USE_USER_RIGHTS"))
     define("JAS_USE_USER_RIGHTS", TRUE);

?>