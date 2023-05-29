<?php

if(DEBUG_MODE) {
  if(!isset($_SESSION['PHPWS_Debug'])) {
    $_SESSION['PHPWS_Debug'] = new PHPWS_Debug;
  }

  if($_SESSION['PHPWS_Debug']->getShowBlock()) {
    $GLOBALS['CNT_debug_block']['title'] = $_SESSION['translate']->it("Debugger");
    
    $hiddens = array("module"=>"debug",
		     "DBUG_op"=>"admin_settings"
		     );
    
    $elements[0] = PHPWS_Form::formHidden($hiddens);
    $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Settings"));
    
    $content = PHPWS_Form::makeForm("DBUG_block0", "index.php", $elements, "post", NULL, NULL);
    
    
    $hiddens = array("module"=>"debug",
		     "DBUG_op"=>"setActivity"
		     );
    
    $elements[0] = PHPWS_Form::formHidden($hiddens);
    
    if($_SESSION['PHPWS_Debug']->isActive()) {
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Deactivate"));
    } else {
      $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Activate"));
    }
    
    $content .= PHPWS_Form::makeForm("DBUG_block1", "index.php", $elements, "post", NULL, NULL);
    
    $GLOBALS['CNT_debug_block']['content'] = $content;
  }
}

?>