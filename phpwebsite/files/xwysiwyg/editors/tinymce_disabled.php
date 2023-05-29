<?php
require_once(PHPWS_SOURCE_DIR . "core/Text.php"); //this we use to parse input and for ezTable

/**
 * Maker file for TinyMCE 2.0 RC4
 *
 * $Id$
 * @author Yves Kuendig <phpws@NOSPAM.firebird.ch>
 * @module xwysiwyg
 * @moduletype mixed module / hack
 * @package phpwebsite = 0.9.3-4 +
 */

class PHPWS_xw_editor extends PHPWS_xwysiwyg {


function loadPlugins($settings) {
	// tiny needs some special configurations for plugins, this is made here.
	// add a line for each plugin you want to install; fill the array with the configs
	// buttons have allways a trailing comma; also the special code (here also an \n)
	$tinyplug = array();
	$tinyplug['advhr']			= array('button' => 'advhr,', 'elements' => 'hr[class|width|size|noshade]', 'spezial' => '');
	$tinyplug['advimage']		= array('button' => '', 'elements' => 'a[name|href|target|title|onclick]', 'spezial' => '');
	$tinyplug['advlink']		= array('button' => '', 'elements' => 'a[name|href|target|title|onclick]', 'spezial' => '');
	$tinyplug['contextmenu']	= array('button' => '', 'elements' => '', 'spezial' => '');
	$tinyplug['emotions']		= array('button' => 'emotions,', 'elements' => '', 'spezial' => '');
	$tinyplug['iespell']		= array('button' => 'iespell,', 'elements' => '', 'spezial' => '');
	$tinyplug['print']			= array('button' => 'print,', 'elements' => '', 'spezial' => '');
	$tinyplug['save']			= array('button' => 'save,', 'elements' => '', 'spezial' => '');
	$tinyplug['searchreplace']	= array('button' => 'search,replace,', 'elements' => '', 'spezial' => '');
	$tinyplug['zoom']			= array('button' => 'zoom,', 'elements' => '', 'spezial' => '');
	$tinyplug['directionality']	= array('button' => 'ltr,rtl,', 'elements' => '', 'spezial' => '');
	$tinyplug['paste']			= array('button' => 'pastetext,pasteword,selectall,', 'elements' => '', 'spezial' => "		paste_create_paragraphs : false,\n		paste_use_dialog : true,\n");
	$tinyplug['insertdatetime']	= array('button' => 'insertdate,inserttime,', 'elements' => '', 'spezial' => "		plugin_insertdate_dateFormat : \"%Y-%m-%d\",\n		plugin_insertdate_timeFormat : \"%H:%M:%S\",\n");
	$tinyplug['preview']		= array('button' => 'preview,', 'elements' => '', 'spezial' => "		plugin_preview_width : \"500\",\n		plugin_preview_height : \"600\",\n");
	$tinyplug['table']			= array('button' => 'tablecontrols,', 'elements' => '', 'spezial' => "		table_color_fields : true,\n");
	$tinyplug['fullscreen']		= array('button' => 'fullscreen,', 'elements' => '', 'spezial' => "		fullscreen_settings : { theme_advanced_path_location : \"top\" },\n");

	$loadplugs	= array();
	$elements	= array();
	$loadplugs['plugins']	= "		plugins		: \"";
	$loadplugs['buttons']	= "";
	$loadplugs['spezial']	= "";
	
	if($settings['plugins']<>'none') {
		foreach($settings['plugins'] as $key => $value) {
			if(array_key_exists($key, $tinyplug)) {
				if($settings['plugins'][$key])	{
					$loadplugs['plugins']	.= "$key,";
					$loadplugs['buttons']	.= $tinyplug[$key]['button'];
					$loadplugs['spezial']	.= $tinyplug[$key]['spezial'];
					$elements[] = $tinyplug[$key]['elements'];
				}
			}
		}
	}

	$elements = array_unique($elements);
	$loadplugs['elements'] = implode(",",$elements);
	if (substr( $loadplugs['elements'], -1 ) == ',') $loadplugs['elements'] = substr( $loadplugs['elements'], 0, -1 );
	if (substr( $loadplugs['plugins'], -1 ) == ',') $loadplugs['plugins'] = substr( $loadplugs['plugins'], 0, -1 );
	if (substr( $loadplugs['buttons'], -1 ) == ',') $loadplugs['buttons'] = substr( $loadplugs['buttons'], 0, -1 );
	$loadplugs['elements']	= "		extended_valid_elements : \"".$loadplugs['elements']."\",\n";
	$loadplugs['plugins']  .= "\",\n";
	$loadplugs['buttons']	= "		theme_advanced_buttons3_add : \"".$loadplugs['buttons']."\",\n";
	return $loadplugs;
}


function Areas($areas,$index) {
	$loadareas = "		elements	: \"";
	if(is_array($areas)) {
		foreach($areas as $area) {
			$loadareas	.= "$area,";
		}
	} else	$loadareas	.= "$areas";
	if (substr( $loadareas, -1 ) == ',') $loadareas = substr( $loadareas, 0, -1 );
	$loadareas .= "\",\n";
	return $loadareas;
}


function getCode($settings,$areas,$lang) {
	$css = "";
	if($settings['enable_css']) $css = PHPWS_xwysiwyg::pickCSS(); //pick css to import
	$scriptHeader = "<script type=\"text/javascript\">\n//<![CDATA[\n";
	$scriptFooter = "//]]>\n</script>\n";
	$index = 1;
	$code = "";
	$loadplugs = PHPWS_xw_editor::loadPlugins($settings);	//load plugins

	// TinyMCE for all Modes
	$code .= "<!-- load the main TinyMCE files -->\n";
	$code .= "<script type=\"text/javascript\" src=\"".$settings['path']."jscripts/tiny_mce/tiny_mce.js\"></script>\n".$scriptHeader;
	$code .= "	tinyMCE.init({\n";
	$code .= "		mode		: \"exact\",\n";
	$code .= "		language	: \"".$lang."\",\n";	//define language
	$code .= "		theme_advanced_toolbar_location : \"top\",\n"; //put the buttons to the top
	$code .= "		theme_advanced_buttons1_add : \"fontselect,fontsizeselect\",\n";
	$code .= "		theme_advanced_styles : \"Story text=storyText;Story title=storyTitle\",\n";
	if($css) $code .= "		content_css	: \"".$css."\",\n";	//add css-file
	if($settings['width']!="auto")	$code .= "		width		: \"".$settings['width']."\",\n";
	if($settings['height']!="auto")	$code .= "		height		: \"".$settings['height']."\",\n";
	$code .= $loadplugs['plugins'];						//load plugins
	$code .= $loadplugs['buttons'];						//load buttons
	$code .= $loadplugs['elements'];					//load add elements
	$code .= $loadplugs['spezial'];						//load special plugin config

	if($settings['request_mode']) { 	// Request-Mode
		$code .= "		theme		: \"".$settings['theme']."\"\n";
		$code .= "\n	});\n";
		foreach($areas as $area) {
			$code .= "	function initEditor".$index."() {\n";
			$code .= "		tinyMCE.execCommand(\"mceAddEditor\",false,\"".$area."\")\n";
			$code .= "	}\n";
			$index++;
		}
	} else {							// Normal-Mode
		$code .= PHPWS_xw_editor::Areas($areas,$index);		//load areas
		$code .= "		theme		: \"".$settings['theme']."\"\n";
		$code .= "\n	});\n";
	}
	$code = $scriptFooter.$code.$scriptFooter.$scriptHeader; //ugly hack to work with $GLOBALS['core']->js_func[]
	return $code;
}

} //end class
?>