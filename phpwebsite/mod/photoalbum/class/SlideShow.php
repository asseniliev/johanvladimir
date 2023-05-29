<?php
/**
 * This class will show javascript slideshow for a photoalbum.
 *
 * @version $Id: SlideShow.php,v 1.8 2005/05/18 17:49:20 darren Exp $
 * @author  Darren Greene <dg49379@NOSPAM.tux.appstate.edu>
 */
class SlideShow {

  function play($photos) {
    $largest_height = 0;
    $filenames = "";
    $names = "";
    $widths = "";
    $heights = "";
    $blurbs = "";
    $jsTags=array();

    for($i = 0; $i < count($photos); $i++) {
      $photo = new PHPWS_Photo($photos[$i]);

      $filenames .= "'./images/photoalbum/";
      $filenames .= $photo->_album . "/";
      $filenames .= str_replace("'", "\'", $photo->_name) . "', ";

      $label = str_replace('"', '\"', $photo->getLabel());  
      $label = str_replace('&#39;', '\'', $label);  
      $names  .= "'" . str_replace("'", "\'", $label) . "', ";

      $blurb = str_replace('"', '\"', $photo->_blurb);  
      $blurb = str_replace('&#39;', '\'', $blurb);  
      $blurbs .= "'" . str_replace("'", "\'", $blurb) . "', ";

      if ((($photo->_width >= PHOTOALBUM_MAX_WIDTH ||
            $photo->_height >= PHOTOALBUM_MAX_HEIGHT))) {

        $ratio = $photo->_width / $photo->_height;
        if ($ratio >= 1) {
          $widths  .= PHOTOALBUM_MAX_WIDTH . ", ";
          $heights .= PHOTOALBUM_MAX_WIDTH / $ratio . ", ";

          if((PHOTOALBUM_MAX_HEIGHT / $ratio) > $largest_height)
	    $largest_height = PHOTOALBUM_MAX_HEIGHT / $ratio;
        } else {
          $widths  .= PHOTOALBUM_MAX_HEIGHT * $ratio . ", ";
          $heights .= PHOTOALBUM_MAX_HEIGHT . ", ";

          if(PHOTOALBUM_MAX_HEIGHT > $largest_height)
            $largest_height = PHOTOALBUM_MAX_HEIGHT;
        }
      } else {
	if(isset($photo->_width))
	  $widths .= $photo->_width . ", ";
	else
	  $widths .= "0, ";

	if(isset($photo->_height))
	  $heights .= $photo->_height . ", ";
	else
	  $heights .= "0, ";

        if($photo->_height > $largest_height)
          $largest_height = $photo->_height;
      }
    }

    $tags["LARGEST_IMHEIGHT"] = $largest_height + 100;
    $jsTags["IMAGES"] = substr($filenames, 0, -2);
    $jsTags["IMAGE_NAMES"] = substr($names, 0, -2);
    $jsTags["IMAGE_BLURBS"] = substr($blurbs, 0, -2);
    $jsTags["IMAGE_HEIGHTS"] = substr($heights, 0, -2);
    $jsTags["IMAGE_WIDTHS"] = substr($widths, 0, -2);
    $jsTags["PAUSE_TEXT"] = $_SESSION["translate"]->it("Stop Show");
    $jsTags["PLAY_TEXT"] = $_SESSION["translate"]->it("Start Show");
    $tags["QUIT_SLIDESHOW"] =
      "<a href='./index.php?module=photoalbum&amp;" .
      "PHPWS_Album_op=view&amp;PHPWS_Album_id=".
      $_SESSION['PHPWS_AlbumManager']->album->_id . "'>" .
      $_SESSION["translate"]->it("Back to Album") . "</a>";
    
    $speedOptions = array('2000'  =>$_SESSION['translate']->it('Two Seconds'),
			  '3000'  =>$_SESSION['translate']->it('Three Seconds'),
			  '5000'  =>$_SESSION['translate']->it('Five Seconds'),
			  '7000'  =>$_SESSION['translate']->it('Seven Seconds'),
			  '10000' =>$_SESSION['translate']->it('Ten Seconds'),
			  '30000' =>$_SESSION['translate']->it('Thirty Seconds'),
			  '60000' =>$_SESSION['translate']->it('One Minute'),
			  '120000'=>$_SESSION['translate']->it('Two Minutes'));
    
    $ieFilters = array('blendTrans(duration=1)' => $_SESSION['translate']->it('Fade'),
		       'revealTrans(duration=1, transition=0)' => $_SESSION['translate']->it('Box In'),
		       'revealTrans( transition=1, duration=1)' => $_SESSION['translate']->it('Box Out'),
		       'progid:DXImageTransform.Microsoft.Pixelate(duration=3)' => $_SESSION['translate']->it('Pixellate'),
		       'revealTrans(duration=1, transition=2)' => $_SESSION['translate']->it('Circle In'),
		       'revealTrans(duration=1, transition=3)' => $_SESSION['translate']->it('Circle Out'),
		       'revealTrans(duration=1, transition=10)' => $_SESSION['translate']->it('Horizontal Checkerboard'),
		       'revealTrans(duration=1, transition=11)' => $_SESSION['translate']->it('Vertical Checkerboard'),
		       'revealTrans(duration=1, transition=12)' => $_SESSION['translate']->it('Dissolve'),
		       'revealTrans(duration=1, transition=4)' => $_SESSION['translate']->it('Wipe Up'),
		       'progid:DXImageTransform.Microsoft.gradientWipe(duration=1)' => $_SESSION['translate']->it('Gradient Wipe'),
		       'progid:DXImageTransform.Microsoft.Spiral(duration=3, GridSizeX=205, GridSizeY=205)' => $_SESSION['translate']->it('Spiral'),
		       'progid:DXImageTransform.Microsoft.Wheel((duration=3, spokes=10)' => $_SESSION['translate']->it('Wheel'),
		       'progid:DXImageTransform.Microsoft.RadialWipe(duration=3)' => $_SESSION['translate']->it('Radial Wipe'),
		       'progid:DXImageTransform.Microsoft.Iris((duration=3)' => $_SESSION['translate']->it('Iris'),
		       'revealTrans(duration=3, transition=20)' => $_SESSION['translate']->it('Strips'),
		       'revealTrans(duration=3, transition=14)' => $_SESSION['translate']->it('Barn'));
    
    $tags["ADJUST_SPEED_TEXT_FIELD"] =
        PHPWS_Form::formSelect("adjustSpeedField", $speedOptions,
                               $_SESSION['translate']->it('Five Seconds'), FALSE, FALSE, "adjustSpeed()");
    $tags["ADJUST_SPEED_LABEL"] =
      $_SESSION["translate"]->it("Set Speed: &nbsp;");
    
    $jsTags["IE_FILTER_FIELD"] =
      str_replace("\n", "", PHPWS_Form::formSelect("ieFilterField", $ieFilters,
						   $_SESSION['translate']->it('Fade'), FALSE, FALSE, "changeFilter()"));
    $jsTags["IE_FILTER_LABEL"] =
      $_SESSION["translate"]->it("Transition Effect: &nbsp;");
    
    $tags["LOOP_LABEL"] = $_SESSION["translate"]->it("Loop:  ");
    $jsTags["LOADING_NEXT_TXT"] = $_SESSION["translate"]->it("Loading Next Image...");
    $jsTags["LOADING_TXT"] = $_SESSION["translate"]->it("Loading Image...");
    
    $tags["LOW_TECH_LINK"] = $_SESSION["translate"]->it("Not working, try " .
							"the ");
    $jsTags["PRE_FILLER"] = "\"http://" . PHPWS_SOURCE_HTTP . "mod/photoalbum/img/pre_filler.gif\"";
    
    $address = "./index.php";
    $linkText = $_SESSION["translate"]->it("low tech");
    $get_var["module"] = "photoalbum";
    $get_var["PHPWS_Album_op"] = "slideShow";
    $get_var["SS_mode"] = "nojsmode";

    $tags["LOW_TECH_LINK"] .=
      PHPWS_Text::link($address, $linkText, "index", $get_var);
    $tags["LOW_TECH_LINK"] .= $_SESSION["translate"]->it(" mode.");
    
    $jsContent = PHPWS_Template::processTemplate($jsTags, "photoalbum",
						 "slideshow/js.tpl");
    
    $_SESSION["OBJ_layout"]->addJavascript($jsContent);

    if(count($photos) == 0)
      $tags["DEFAULT_TITLE"] = $_SESSION["translate"]->it("Album Contains No Photos");
    else
      $tags["IMAGE"] = " ";

    return PHPWS_Template::processTemplate($tags, "photoalbum",
                                           "slideshow/slideshow.tpl");
  }

}

?>