<?php

/**
 * This is a skeleton control panel configuration file. Edit it to be used
 * with you module.
 *
 * $Id: controlpanel.php,v 1.5 2004/11/05 16:28:15 steven Exp $
 */

$image['name'] = 'skeleton.jpg';
$image['alt']  = 'Module Author: Adam Morton';

/* Create a link to your module */
$link[] = array ('label'       => 'Skeleton Module',
		 'module'      => 'skeleton',
		 'url'         => 'index.php?module=skeleton',
		 'image'       => $image,
		 'admin'       => TRUE,
		 'description' => 'This module is a skeleton module for use by developers when creating a new module.',
		 'tab'         => 'developer');

?>