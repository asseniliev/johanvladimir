<?php

    /**
     * Health module for phpWebSite 0.9.3
     *
     * $Id: install.php,v 1.4 2004/09/24 21:19:08 admin Exp $
     * @author rck <http://www.kiesler.at>
     */

    /* Make sure the user is deity before running this script */
    if (!$_SESSION["OBJ_user"]->isDeity()) {
	header("location:index.php");
	    exit();
    }

    $status = 1;

?>
