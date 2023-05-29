<?php

require_once(PHPWS_SOURCE_DIR . 'core/Text.php');
require_once(PHPWS_SOURCE_DIR . 'core/WizardBag.php');

/**
 * This class is responsible for showing all the views for FAQ, except the category layout.
 * The category layout is in a seperate file because of its complexity.
 *
 * @version $Id: FaqViews.php,v 1.2 2003/10/29 14:02:25 steven Exp $
 * @author Darren Greene <dg49379@appstate.edu>
 * @package Faq
 */
class FaqViews {

  /**
   * shows the setting for showing the layout option of having a list of clickable questions
   *
   * @access private
   */
  function _noCategoriesQuesClickView() {
    $rowContents = NULL;
    /* check to see if any FAQs to display */
    if($_SESSION["SES_FAQ_STATS"]->getViewable() > 0) {
      //            $this->_pageCurrentView($pagedIds, $pagingInfo, $tags);
      $_SESSION["SES_FAQ_MANAGER"]->_pageCurrentView($pagedIds, $pagingInfo, $tags);

      $rowClass = "";
      foreach ($pagedIds as $row) {
        $query     = "SELECT id, label, compScore FROM ".
	  PHPWS_TBL_PREFIX."mod_faq_questions WHERE id = ".$row['id'];

        $rowResult = $GLOBALS["core"]->getAll($query);

	PHPWS_WizardBag::toggle($rowClass, " class=\"bg_light\"");
        $subTags["ROW_CLASS"] = $rowClass;
	
        $viewLink  = "<a ";
        $viewLink .= "href=\"index.php?module=faq&amp;FAQ_op=view&amp;FAQ_id=";
        $viewLink .= $rowResult[0]["id"]."\">".$rowResult[0]["label"]."</a>";

        $subTags["QUESTION_LABEL"] = $viewLink;
        $subTags["RATING_LABEL"]   = $rowResult[0]["compScore"];
        $rowContents .= 
	  PHPWS_Template::processTemplate($subTags, "faq", "userLists/nocategories/row.tpl");
      }

      $tags["LIST_ITEMS"] = $rowContents;
    }
    else {
      $tags["NO_ITEMS"] = $_SESSION["translate"]->it("No FAQs to display");
    }

    $content = 
      PHPWS_Template::processTemplate($tags, "faq", "userLists/nocategories/list.tpl");

    $prefixTitle = $_SESSION["translate"]->it("FAQs &nbsp;");
    if(!isset($pagingInfo))
      $pagingInfo["SectionInfo"] = "";

    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC _noCategoriesQuesClickView()


  /**
   * shows the setting for showing the layout option of having questions followed by an answer
   *
   * @access private
   */
  function _noCategoriesQuesAnsView() {
    /* check to see if any FAQs to display */
    if($_SESSION["SES_FAQ_STATS"]->getViewable() > 0) {
      $this->_pageCurrentView($pagedIds, $pagingInfo, $tags);

      $rowContents = "";
      foreach ($pagedIds as $row) {
        $query     = "SELECT id, label, answer, compScore FROM ".
	  PHPWS_TBL_PREFIX."mod_faq_questions WHERE id = ".$row['id'];
        $rowResult = $GLOBALS["core"]->getAll($query);

        $subTags["QUESTION_LABEL"]    = "<b><span style=\"color:red;\">".
	  $_SESSION["translate"]->it("Q").":</span></b>";
        $subTags["QUESTION_CONTENTS"] = $rowResult[0]["label"];

        $subTags["ANSWER_LABEL"]      = "<b><span style=\"color:red;\">".
	  $_SESSION["translate"]->it("A");
        $subTags["ANSWER_LABEL"]     .= ":</span></b>";
        $subTags["ANSWER_CONTENTS"]   = $rowResult[0]["answer"];

        if($this->_allowScoring) {
          $address        = PHPWS_HOME_HTTP."index.php";
          $displayContent = $_SESSION["translate"]->it("feedback");
          $get_var["module"] = "faq";
          $get_var["FAQ_op"] = "view";
          $get_var["FAQ_id"] = $row["id"];
          $subTags["RATING_OPTION"] = 
	    PHPWS_Text::link($address, $displayContent, NULL, $get_var);
          $subTags["RATING_OPTION"] = "[ ".$subTags["RATING_OPTION"]." ]";
        }

        $rowContents .= 
	  PHPWS_Template::processTemplate($subTags, "faq", "userLists/quesAns/row.tpl");
      }

      $tags["LIST_ITEMS"] = $rowContents;
    }
    else {
      $tags["NO_ITEMS"] = $_SESSION["translate"]->it("No FAQs to display");
    }

    $content = PHPWS_Template::processTemplate($tags, "faq", "userLists/quesAns/list.tpl");

    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC _noCategoriesQuesAnsView()

  /**
   * shows the setting for showing the layout option of having questions followed by an answer
   *
   * @access private
   */
  function _noCategoriesBookmarkesQuesAnsView() {
    /* check to see if any FAQs to display */
    if($_SESSION["SES_FAQ_STATS"]->getViewable() > 0) {
      $this->_pageCurrentView($pagedIds, $pagingInfo, $tags);

      $tags["TOP_BOOKMARK"] = "<a name=\"top\"></a>";

      $rowContents = "";
      foreach ($pagedIds as $row) {
        $query = "SELECT label FROM mod_faq_questions WHERE id = ".$row['id'];
        $rowResult = $GLOBALS["core"]->getAll($query, TRUE);

        $subTags["QUESTION_CONTENTS"] = 
	  "<a style=\"color:blue;:visited {background: blue}\" href=\"#".$row['id']."\">";
        $subTags["QUESTION_CONTENTS"] .= $rowResult[0]["label"];
        $subTags["QUESTION_CONTENTS"] .= "</a>";

        $rowContents .= 
	  PHPWS_Template::processTemplate($subTags, "faq", 
					    "userLists/bookmarkedQuesAns/topRow.tpl");
      }

      $tags["ALL_QUESTIONS"] = $rowContents;

      $rowContents = "";
      foreach ($pagedIds as $row) {
        $query = "SELECT label, answer FROM ".
	  PHPWS_TBL_PREFIX."mod_faq_questions WHERE id = ".$row['id'];
        $rowResult = $GLOBALS["core"]->getAll($query);

        $subTags["QUESTION_CONTENTS"] = "<a name=\"".$row['id']."\">";
        $subTags["QUESTION_CONTENTS"] .= $rowResult[0]["label"];
        $subTags["ANSWER_CONTENTS"] = $rowResult[0]["answer"];

        if($this->_allowScoring) {
          $address        = PHPWS_HOME_HTTP."index.php";
          $displayContent = $_SESSION["translate"]->it("feedback");
          $get_var["module"] = "faq";
          $get_var["FAQ_op"] = "view";
          $get_var["FAQ_id"] = $row["id"];
          $subTags["RATING_OPTION"] = 
	    PHPWS_Text::link($address, $displayContent, NULL, $get_var);
          $subTags["RATING_OPTION"] = "[ ".$subTags["RATING_OPTION"]." ]";
        }

        $subTags["TOP_LINK"]  = 
	  "<a style=\"text-decoration:none;color:blue;:visited {background: blue}\"";
        $subTags["TOP_LINK"] .= "href=\"#top\">[top]</a>";

        $rowContents .= 
	  PHPWS_Template::processTemplate($subTags, "faq", 
					    "userLists/bookmarkedQuesAns/bottomRow.tpl");
      }

      $tags["QUESTION_ANSWER"] = $rowContents;
    }
    else {
      $tags["NO_ITEMS"] = $_SESSION["translate"]->it("No FAQs to display");
    }

    $content = 
      PHPWS_Template::processTemplate($tags, "faq", "userLists/bookmarkedQuesAns/list.tpl");
    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC _noCategoriesBookmarkesQuesAnsView()

}