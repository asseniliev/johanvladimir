<?php

class PHPWS_Stats_Common {

  function getMsg($content) {
    $msgTag["MESSAGE"] = $content;
    return PHPWS_Template::processTemplate($msgTag, "stats", "message.tpl");
  }
}

?>