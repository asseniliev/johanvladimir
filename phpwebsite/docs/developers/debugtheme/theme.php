<?php
if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
   header("Content-Type: application/xhtml+xml; charset=UTF-8");
   header("Content-Style-Type: text/css");
   header("Content-Script-Type: text/javascript");
   header("Content-Language: en-US");
   $THEME["XML"] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
   $THEME["XML_STYLE"] = "<?xml-stylesheet alternate=\"yes\" title=\"Blue\" href=\"./themes/Default/blue-style.css\" type=\"text/css\"?>\n<?xml-stylesheet title=\"Orange\" href=\"./themes/Default/orange-style.css\" type=\"text/css\"?>\n<?xml-stylesheet href=\"./themes/Default/core-style.css\" type=\"text/css\"?>";
   $THEME["DOCTYPE"] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
   $THEME["XHTML"] = "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\">";
   $_SESSION["OBJ_layout"]->meta_content = FALSE;
} else {
   header("Content-Type: text/html; charset=UTF-8");
   header("Content-Style-Type: text/css");
   header("Content-Script-Type: text/javascript");
   header("Content-Language: en-US");
   $THEME["DOCTYPE"] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
   $THEME["XHTML"] = "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\">";
   $THEME["STYLESHEET"] = "<link rel=\"stylesheet\" href=\"./themes/Default/style.css\" type=\"text/css\" />";
}
?>
