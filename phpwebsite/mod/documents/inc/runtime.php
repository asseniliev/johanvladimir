<?php

require_once(PHPWS_SOURCE_DIR.'mod/documents/conf/config.php');

function documentBlock() {
  $sql = "SELECT * FROM ".PHPWS_TBL_PREFIX."mod_documents_settings";
  $result = $GLOBALS['core']->quickFetch($sql);

  if($result['showblock']) {
    /* begin custom block */
    $sql = "SELECT id, label, updated FROM ".PHPWS_TBL_PREFIX."mod_documents_docs WHERE (approved='1' AND hidden='0') ORDER BY created DESC LIMIT ".JAS_RECENT_DOCUMENTS;
    $result = $GLOBALS['core']->getAll($sql);
    
    $tags = array();
    $tags['NAME_TEXT'] = $_SESSION['translate']->it("Name");
    $tags['UPDATED_TEXT'] = $_SESSION['translate']->it("Updated");
    
    if(is_array($result) && (sizeof($result) > 0)) {
      foreach($result as $key => $value) {
	$tags['ID'] = $value['id'];
	$tags['NAME'] = "<a href=\"./index.php?module=documents&amp;JAS_DocumentManager_op=viewDocument&amp;JAS_Document_id={$value['id']}\">{$value['label']}</a>";
	$tags['UPDATED'] = date(PHPWS_DATE_FORMAT."&#160;".PHPWS_TIME_FORMAT, $value['updated']);
	$content[] = PHPWS_template::processTemplate($tags, "documents", "block.tpl");
      }

      $GLOBALS['CNT_documents_block']['title'] = $_SESSION['translate']->it('Recently Added Documents');
      $GLOBALS['CNT_documents_block']['content'] = implode("<hr />\n", $content);
    }
    /* end custom block */
  }
}

documentBlock();

?>