<?php

if(isset($_SESSION["OBJ_user"]->username)) {
  require_once(PHPWS_SOURCE_DIR.'mod/notes/class/NoteManager.php');

  PHPWS_NoteManager::showBlock();
}

?>