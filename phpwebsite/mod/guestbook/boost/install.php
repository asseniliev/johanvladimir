<?php
/**
 * @version $Id: install.php,v 1.1 2003/01/21 21:06:07 matt Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

  if($GLOBALS["core"]->sqlImport($GLOBALS["core"]->source_dir . "mod/guestbook/boost/install.sql", TRUE)) {
    $content .= $_SESSION['translate']->it("All Guestbook tables successfully written.")."<br />";

    if ($GLOBALS["core"]->sqlTableExists("mod_guestbook", true)) {
        $result = $GLOBALS["core"]->query("SELECT * FROM mod_guestbook", true);
        if ($result) {
            while ($old_gb = $result->fetchrow()) {
                $gb_data[]  = "INSERT INTO mod_book_data (id, name, email, url, date, host, comment) VALUES (". $old_gb['id'].", '".addslashes($old_gb['name'])."', '".addslashes($old_gb['email'])."', '".addslashes($old_gb['url'])."', '".$old_gb['date']."', '".$old_gb['host']."', '".addslashes($old_gb['comment'])."')";
            }

            for ($i=0;$i<sizeof($gb_data);$i++) {
                 $GLOBALS["core"]->query($gb_data[$i],true);
            }
            $GLOBALS["core"]->sqlDropTable("mod_guestbook");
        } else {
            $content .= $_SESSION['translate']->it("Can not query existing guestbook database.")."<br />";
            $content .= $_SESSION['translate']->it("You must transfer existing guestbook data manually.")."<br />";
        }
    }
    $status = 1;
  } else
    $content .= $_SESSION['translate']->it("There was a problem writing to the database.")."<br />";

?>