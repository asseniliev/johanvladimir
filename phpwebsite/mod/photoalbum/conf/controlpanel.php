<?php

/**
 * @version $Id: controlpanel.php,v 1.7 2003/08/22 15:27:13 steven Exp $
 * @author  Steven Levin <steven at NOSPAM tux[dot]appstate[dot]edu>
 */

$image['name'] = "photo.png";
$image['alt'] = "Photo Albums Author: Steven Levin";

$link[0] = array ("label"=>"Photo Albums",
		  "module"=>"photoalbum",
		  "url"=>"index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list",
		  "description"=>"The Photo Album allows you to manage sets of image galleries.",
		  "image"=>$image,
		  "admin"=>TRUE,
		  "tab"=>"content");

?>