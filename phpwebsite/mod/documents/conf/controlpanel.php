<?php

/**
 * @author Steven Levin <steven [at] jasventures [dot] com>
 * @author Jeremy Agee <jeremy [at] jasventures [dot] com>
 * @version $Id: controlpanel.php,v 1.3 2005/05/23 12:53:22 darren Exp $
 */

$image['name'] = "documents.png";

$image['alt'] = $_SESSION['translate']->it('Documents');

$link[0] = array ("label"=>$_SESSION['translate']->it('Documents'),
		  "module"=>"documents",
		  "url"=>"index.php?module=documents&amp;JAS_DocumentManager_op=list",
		  "description"=>'Documents allows you to manage documents which can have mutiple files associated with it.',
		  "image"=>$image,
		  "admin"=>TRUE,
		  "tab"=>"content");

?>