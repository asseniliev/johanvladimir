<?php

require_once(PHPWS_SOURCE_DIR . "mod/stats/conf/stats.php");

define("RED",   0);
define("GREEN", 1);
define("BLUE",  2);
define("ID",    3);
 
class PHPWS_Stats_Bar_Graph {
  var $_data;
  var $_width;
  var $_height;
  var $_image;
  var $_title = '';
  var $_titleFont;
  var $_bgColor    = array();
  var $_barColor   = array();
  var $_edgesColor = array();
  var $_gridColor  = array();
  var $_xlabel = '';

  function PHPWS_Stats_Bar_Graph($width, $height, $data=NULL) {
    $this->_width  = $width;
    $this->_height = $height;
    $this->_data   = $data;
    $this->_image  = imagecreate($this->_width, $this->_height); 
    $this->_initColors();
  }

  function setXLabel($lbl) {
    $this->_xlabel = $lbl;
  }

  function _buildColorArr($classVar, $define) {
    $this->{$classVar}[] = base_convert(substr($define, 2, 2), 16, 10);
    $this->{$classVar}[] = base_convert(substr($define, 4, 2), 16, 10);
    $this->{$classVar}[] = base_convert(substr($define, 6, 2), 16, 10);

    $this->{$classVar}[] = imagecolorallocate($this->_image, 
	$this->{$classVar}[RED], $this->{$classVar}[GREEN], $this->{$classVar}[BLUE]); 
  }

  function _initColors() {
    $this->_buildColorArr("_bgColor",    GRAPH_BG);
    $this->_buildColorArr("_barColor",   GRAPH_BAR);
    $this->_buildColorArr("_edgesColor", GRAPH_EDGES);
    $this->_buildColorArr("_gridColor",  GRAPH_GRID);
    $this->_buildColorArr("_xlabelsColor",  GRAPH_XLABELS);
    $this->_buildColorArr("_ylabelsColor",  GRAPH_YLABELS);
  }

  function draw() {
      require_once(PHPWS_SOURCE_DIR . 'mod/stats/conf/stats.php');

    // layout
    $maxval = max($this->_data); 
    $nval = sizeof($this->_data); 

    $vmargin = 30; // top (bottom) vertical margin for title (x-labels)
    $hmargin = 54; // left horizontal margin for y-labels
    
    $base = floor(($this->_width - $hmargin) / $nval);     
    $ysize = $this->_height - 2*$vmargin; // y-size of plot

    $xsize = $nval * $base; // x-size of plot

    // title    
    $txtsz = imagefontwidth($this->_titleFont) * strlen($this->_title); // pixel-width of title

    $xpos = (int)($hmargin + ($xsize - $txtsz)/2); // center the title
    $xpos = max(1, $xpos); // force positive coordinates
    $ypos = 3; // distance from top
    
    imagestring($this->_image, $this->_titleFont, 0, ($ysize+$vmargin)/2, LNG_Y_AXIS , $this->_ylabelsColor[ID]); 
    imagestring($this->_image, $this->_titleFont, $xsize/2, $this->_height-($vmargin/2), $this->_xlabel, $this->_xlabelsColor[ID]); 

    // y labels and grid lines
    $labelfont = 2; 
    $ngrid = 4; // number of grid lines
    
    $dydat = round($maxval / $ngrid); // data units between grid lines
    $dypix = round($ysize / ($ngrid + 1)); // pixels between grid lines

    if($dydat == 0)
      $dydat = 1;

    for ($i = 0; $i <= ($ngrid + 1); $i++) { 
      // iterate over y ticks
      
      // height of grid line in units of data
      if($maxval <= $ngrid) 
	$ydat = (double)($i * $dydat); 
      else
	$ydat = (int)($i * $dydat); 

      // height of grid line in pixels
      $ypos = $vmargin + $ysize - (int)($i*$dypix); 
      
      $txtsz = imagefontwidth($labelfont) * strlen($ydat); // pixel-width of label
      $txtht = imagefontheight($labelfont); // pixel-height of label
      
      $xpos = (int)(($hmargin - $txtsz)-5); 
      $xpos = max(1, $xpos); 
      
      imagestring($this->_image, $labelfont, $xpos, 
		  $ypos - (int)($txtht/2), $ydat, $this->_ylabelsColor[ID]); 
      
      if (!($i == 0) && !($i > $ngrid)) {
        imageline($this->_image, $hmargin, 
		  $ypos, $hmargin + $xsize, $ypos, $this->_edgesColor[ID]); 
      }
    } 

    // columns and x labels
    $padding = 3; // half of spacing between columns
    if($dydat != 0) 
      $yscale = $ysize / (($ngrid+1) * $dydat); // pixels per data unit
    else
      $yscale = 1;

    for ($i = 0; list($xval, $yval) = each($this->_data); $i++) { 
      
      // vertical columns
      $ymax = round($vmargin + $ysize); 
      $ymin = round($ymax - (int)($yval*$yscale)); 
      $xmax = round($hmargin + ($i+1)*$base - $padding); 
      $xmin = round($hmargin + $i*$base + $padding); 
      
      imagefilledrectangle($this->_image, $xmin, $ymin, $xmax, $ymax, $this->_barColor[ID]); 
      
      // x labels
      $txtsz = imagefontwidth($labelfont) * strlen($xval); 
      
      $xpos = $xmin + (int)(($base - $txtsz) / 2); 
      $xpos = max($xmin, $xpos); 
      $ypos = $ymax + 3; // distance from x axis
      
      imagestring($this->_image, $labelfont, $xpos, $ypos, $xval, $this->_xlabelsColor[ID]); 
    } 
    
    // plot frame
    imagerectangle($this->_image, $hmargin, $vmargin, 
		   $hmargin + $xsize-.5, $vmargin + $ysize, $this->_edgesColor[ID]); 
    
    // flush image
    header("Content-type: image/png"); // or "Content-type: image/png"
    imagepng($this->_image); // or imagepng($image)
    imagedestroy($this->_image); 
  }

  function setData($dataArr) {
    $this->_data = $dataArr;
  }

  function setWidth($width) {
    $this->_width = $width;
  }

  function setHeight($height) {
    $this->_height = $height;
  }

  function setTitle($title) {
    $this->_title  = $title;
  }

  function setTitleFont($font) {
    $this->_titleFont = $font;
  }
}



?>