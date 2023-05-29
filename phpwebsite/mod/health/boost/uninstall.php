<?php

    /**
     * Health module for phpWebSite
     *
     * $Id: uninstall.php,v 1.4 2004/01/03 19:13:22 admin Exp $
     * @author rck <http://www.kiesler.at/>
     */


    /* Make sure the user is a deity before running this script */
    
    if(!$_SESSION["OBJ_user"]->isDeity()) {
        header("location:index.php");
        exit();
    }


    $status = 1;

?>
