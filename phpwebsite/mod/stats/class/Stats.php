<?php 

define("ANN_TABLE", "mod_announce");
define("LINKS_TABLE", "mod_linkman_links");
define("COMMENTS_TABLE", "mod_comments_data");
define("FAQ_TABLE", "mod_faq_questions");
define("PHOTOS_ALBUMS_TABLE", "mod_photoalbum_albums");
define("PHOTOS_PICS_TABLE", "mod_photoalbum_photos");
define("DOCUMENTS_FILES_TABLE", "mod_documents_files");
define("DOCUMENTS_DOCS_TABLE", "mod_documents_docs");
define("NOTES_TABLE", "mod_notes");
define("USERS_TABLE", "mod_users");
define("PAGEMASTER_TABLE", "mod_pagemaster_pages");

require_once(PHPWS_SOURCE_DIR . "mod/stats/class/StatsCommon.php");

class PHPWS_Stats {
  var $_numStats;
  var $_showEmpty;

  function PHPWS_Stats() {
    $show = $GLOBALS["core"]->sqlSelect("mod_stats_settings");
    $this->_numStats = $show[0]["stats_num_show"];
    $this->_showEmpty = $show[0]["stats_show_empty"];
  }

  function isTrackingPMHits() {
    $show = $GLOBALS["core"]->sqlSelect("mod_stats_settings");
    return 
      (strpos($show[0]["stats_viewable"], (string)PAGEMASTER_PHITS) !== FALSE);
  }

  function addPMHit() {
    $id = $_REQUEST["PAGE_id"];
    
    if($result = $GLOBALS["core"]->sqlSelect("mod_stats_pm", "page_id", $id)) {
      $update["count"] = ++$result[0]["count"];
      $GLOBALS["core"]->sqlUpdate($update, "mod_stats_pm", "page_id", $id);

    } else {
      $newEntry["page_id"] = $id;
      $newEntry["count"] = 1;      

      $GLOBALS["core"]->sqlInsert($newEntry, "mod_stats_pm");
    }
  }

  function isPMHit() {
    return ((isset($_REQUEST["module"]) && $_REQUEST["module"] == "pagemaster") &&
	    (isset($_REQUEST["PAGE_user_op"]) && $_REQUEST["PAGE_user_op"] == "view_page") &&
	    (isset($_REQUEST["PAGE_id"])));
  }

  function getItem($choice) {
    switch($choice) {
    case ANNOUNCEMENTS:
      if($GLOBALS["core"]->moduleExists("announce")) {
	return $this->_getTopAnn();
      }
      break;
    case COMMENTED_ANNOUNCEMENTS:
      if($GLOBALS["core"]->moduleExists("comments")) {
	return $this->_getTopCommentedAnn();
      }
      break;
    
    case LINKS:
      if($GLOBALS["core"]->moduleExists("linkman")) {
	return $this->_getTopLinks();
      }
      break;

    case ANNOUNCEMENT_SUBMITTERS:
      if($GLOBALS["core"]->moduleExists("announce")) {
	return $this->_getTopAnnSubmitters();
      }
      break;

    case FAQS:
      if($GLOBALS["core"]->moduleExists("faq")) {
	return $this->_getTopFAQs();
      }

    case HIGHEST_RATED_FAQS:
      if($GLOBALS["core"]->moduleExists("faq")) {
	return $this->_getHighestRatedFaqs();
      }      
      break;

    case RECENT_PHOTOS:
      if($GLOBALS["core"]->moduleExists("photoalbum")) {
	return $this->_getRecentPhotos();       
      }
      break;

    case RECENT_FILES:
      if($GLOBALS["core"]->moduleExists("documents")) {
	return $this->_getRecentFiles();
      }
      break;

    case NOTES_SENT:
      if($GLOBALS["core"]->moduleExists("notes")) {
	return $this->_getTopNoteSenders();
      }
      break;

    case NOTES_RECIEVED:
      if($GLOBALS["core"]->moduleExists("notes")) {
	return $this->_getTopNoteRecievers();
      }
      break;

    case RECENT_USERS:
      return $this->_getRecentUsers();
      break;
      
    case RECENT_COMMENTS:
      if($GLOBALS["core"]->moduleExists("comments")) {
	return $this->_getRecentComments();
      }
      break;

    case PAGEMASTER_PHITS:
      if($GLOBALS["core"]->moduleExists("pagemaster")) {
	return $this->_getTopPageMasterHits();
      }
      break;
      
    case RECENT_PAGE_UPDATES:
      if($GLOBALS["core"]->moduleExists("pagemaster")) {
	return $this->_getRecentPageMasterUpdates();
      }
      break;
    
    }
  }


  function view() {
    require_once(PHPWS_SOURCE_DIR . "mod/stats/conf/stats.php");

    $content = "";

    if($_SESSION["OBJ_user"]->allow_access("stats", "stats_settings")) {
      $tags["SETTINGS"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Settings"), "stats", array("stats[stats]"=>"setup"));
      $showBar = true;
    }

    $tags["STATS_ITEMS"]  = "";
    
    if($show = $GLOBALS["core"]->sqlSelect("mod_stats_settings")) {
      $items = explode('::', $show[0]["stats_viewable"]);
      foreach($items as $item) {
	$tags['STATS_ITEMS'] .= $this->getItem($item);
      }

      if($tags["STATS_ITEMS"] == "")
	$tags["STATS_ITEMS"] = " &nbsp;".$_SESSION["translate"]->it("No items to view.");

      $content .= PHPWS_Template::processTemplate($tags,
						  "stats", "stats/view/view.tpl");
    } else {
      $content = $_SESSION["translate"]->it("There was a problem viewing the stats.");
    }

    return $content;
    
  }

  function setup() {
    if(!$_SESSION["OBJ_user"]->allow_access("stats", "stats_settings")) {
	return $_SESSION["translate"]->it("You do not have the correct permissions enabled to change stats settings.");
    }

    require_once(PHPWS_SOURCE_DIR . "mod/stats/conf/stats.php");

    $show = $GLOBALS["core"]->sqlSelect("mod_stats_settings");

    $topTen = array();
    if($GLOBALS["core"]->moduleExists("announce"))
      $topTen[ANNOUNCEMENTS] = $_SESSION["translate"]->it("Most Read Announcements");

    if($GLOBALS["core"]->moduleExists("comments")) {
      $topTen[COMMENTED_ANNOUNCEMENTS] = $_SESSION["translate"]->it("Most Commented Announcements");
      $topTen[RECENT_COMMENTS] = $_SESSION["translate"]->it("Most Recent Comments");
    }

    if($GLOBALS["core"]->moduleExists("announce"))
      $topTen[ANNOUNCEMENT_SUBMITTERS] = $_SESSION["translate"]->it("Most Active Announcement Submitters");

    if($GLOBALS["core"]->moduleExists("linkman"))
      $topTen[LINKS] = $_SESSION["translate"]->it("Most Visited Links");

    if($GLOBALS["core"]->moduleExists("faq")) {
      $topTen[FAQS] = $_SESSION["translate"]->it("Most Read FAQs");
      $topTen[HIGHEST_RATED_FAQS] = $_SESSION["translate"]->it("Higest Rated FAQs");
    }

    if($GLOBALS["core"]->moduleExists("pagemaster")) {
      $topTen[PAGEMASTER_PHITS] = $_SESSION["translate"]->it("Most Accessed Web Pages");
      $topTen[RECENT_PAGE_UPDATES] = $_SESSION["translate"]->it("Most Recent Web Page Updates");
    }

    if($GLOBALS["core"]->moduleExists("photoalbum"))
      $topTen[RECENT_PHOTOS] = $_SESSION["translate"]->it("Most Recent Photos");
    if($GLOBALS["core"]->moduleExists("documents"))
      $topTen[RECENT_FILES] = $_SESSION["translate"]->it("Most Recent Uploaded Files");

    if($GLOBALS["core"]->moduleExists("notes"))
      $topTen[NOTES_SENT] = $_SESSION["translate"]->it("Most Active Note Senders");

    if($GLOBALS["core"]->moduleExists("notes"))
      $topTen[NOTES_RECIEVED] = $_SESSION["translate"]->it("Most Active Note Receiver");

    $topTen[RECENT_USERS] = $_SESSION["translate"]->it("Most Recently Logged in Users");

    $form = new EZform("Stats_Setup");

    $form->add("viewable_stats", "multiple", $topTen);
    $form->setMatch("viewable_stats", explode("::",$show[0]["stats_viewable"]));
    $form->setSize("viewable_stats", 10);
    $form->add("num_show_fld", "text", $show[0]["stats_num_show"]);
    $form->setSize("num_show_fld", 2);
    $form->add("show_empty_fld", "checkbox");
    if($this->_showEmpty == 1) {      
      $form->setMatch("show_empty_fld", 1);
    }
    $form->add("module", "hidden", "stats");
    $form->add("stats[stats]", "hidden", "saveSetup");
    $form->add("saveSetup", "submit", $_SESSION["translate"]->it("Save Setup"));

    $form->setExtra("saveSetup",
	 	"onclick=\"selectAll(this.form.elements['viewable_stats[]'])\"");
    $tags = $form->getTemplate();

    if(!empty($show[0]["stats_viewable"]))
      $num_sels = explode("::", $show[0]["stats_viewable"]);
    else
      $num_sels = array();

    $selections = array();
    foreach($num_sels as $num) {
	if(!isset($topTen[$num])) {
	   $this->removeSelection($num_sels, $num);
	   continue;
        }
	$selections[$num] = $topTen[$num];
    }

    $tags["VIEWABLE_STATS"] = PHPWS_WizardBag::js_insert("swapper", "Stats_Setup", NULL, 0, array("viewable_stats_options"=>$topTen,
												 "viewable_stats"=>$selections,
												 "sorting"=>0)); 

    $tags["BACK"] = PHPWS_Text::moduleLink($_SESSION["translate"]->it("Back"), "stats", array("stats[stats]"=>"view"));
    $tags["VIEWABLE_STATS_LBL"] = $_SESSION["translate"]->it("Stats to show");
    $tags["NUM_SHOW_LBL"] = $_SESSION["translate"]->it("Number of items to show for each stat");
    $tags["SHOW_EMPTY_LBL"] = $_SESSION["translate"]->it("Show titles for stats that have no entries.");

    if(isset($_SESSION["OBJ_menuman"])) {
      $_SESSION["OBJ_menuman"]->add_module_item(
			     "stats","&amp;module=stats&amp;stats[stats]=view",
			     "./index.php?module=stats&amp;stats[stats]=setup", TRUE);				     
    }

    $content = PHPWS_Template::processTemplate($tags, "stats",
					       "stats/setup.tpl");
    return $content;
    
  }

  function removeSelection($orginal, $num) {
    unset($orginal[array_search($num, $orginal)]);
    $update['stats_viewable'] = implode('::', $orginal);
    $GLOBALS["core"]->sqlUpdate($update, "mod_stats_settings");		
  }

  function saveSetup() {
    $content = "";

    $update["stats_viewable"] = "";    

    if(isset($_REQUEST["viewable_stats"])) {
      foreach($_REQUEST["viewable_stats"] as $stat) {
	$update["stats_viewable"] .= $stat . "::";
      }
      $update["stats_viewable"] = substr($update["stats_viewable"], 0, -2);
    } 

    if(isset($_REQUEST["num_show_fld"])) {
      if($_REQUEST["num_show_fld"] >= 1)
	$this->_numStats = $update["stats_num_show"] = $_REQUEST["num_show_fld"];
      else {	
	$error = $_SESSION["translate"]->it("The numbers of items to show must be greater than 0, the value has been reset.");
      }
    }

    if(isset($_REQUEST["show_empty_fld"])) {
      $this->_showEmpty = $update["stats_show_empty"] = 1;      
    } else {
      $this->_showEmpty = $update["stats_show_empty"] = 0;      
    }
    
    $result = $GLOBALS["core"]->sqlUpdate($update, "mod_stats_settings");

    if(!isset($error)) {
      if($result) {
	$content .= $_SESSION["translate"]->it("Successfully Updated Settings");
      } else {
	$content .= $_SESSION["translate"]->it("There was a problem updating stats setting.");
      }
    } else {
      $content =  $error . $content;
    }

    return PHPWS_Stats_Common::getMsg($content);
  }

  function _getRecentPageMasterUpdates() {
    $recentPageUpdates = $GLOBALS["core"]->sqlSelect(PAGEMASTER_TABLE, array("approved"=>1,"active"=>1), NULL, "updated_date DESC", NULL, NULL, $this->_numStats);

    if($recentPageUpdates || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Most Recent Web Page Updates");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      if(!empty($recentPageUpdates)) {
	foreach($recentPageUpdates as $update) {
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($update["title"],
						    "pagemaster", array("PAGE_user_op"=>"view_page", "PAGE_id"=>$update["id"]));
	
	  $itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Updated: ") . $update["updated_date"];
	  $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	}
      }
      
      return PHPWS_Template::processTemplate($tags, "stats", 
					     "stats/view/stat_item.tpl");
    }
  }

  function _getTopAnn() {
    $topAnnouncements = $GLOBALS["core"]->sqlSelect(ANN_TABLE, array("approved"=>1,"active"=>1), NULL, "hits DESC", NULL, NULL, $this->_numStats);

    if($topAnnouncements || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' .$this->_numStats . $_SESSION["translate"]->it(" Most Read Announcements");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      if(!empty($topAnnouncements)) {
	foreach($topAnnouncements as $announcement) {
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($announcement["subject"],
						    "announce", array("ANN_user_op"=>"view", "ANN_id"=>$announcement["id"]));
	
	  $itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Read: ") . $announcement["hits"];
	  $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	}
      }
      
      return PHPWS_Template::processTemplate($tags, "stats", 
					     "stats/view/stat_item.tpl");
    }
  }

  function _getTopPageMasterHits() {
    $topPageHits = $GLOBALS["core"]->sqlSelect("mod_stats_pm", NULL, NULL, "count DESC", NULL, NULL, $this->_numStats);

    if($topPageHits || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' .$this->_numStats . $_SESSION["translate"]->it(" Most Accessed Web Pages");
      $tags["STAT_ITEM_LISTINGS"] = "";      

      if(!empty($topPageHits)) {
	foreach($topPageHits as $pageHit) {
	  if($pageInfo = $GLOBALS["core"]->sqlSelect(PAGEMASTER_TABLE, array("active"=>1, "approved"=>1, "id"=>$pageHit["page_id"]))) {

	    $itemTag["ITEM"] = PHPWS_Text::moduleLink($pageInfo[0]["title"],
						      "pagemaster", array("PAGE_user_op"=>"view_page", "PAGE_id"=>$pageInfo[0]["id"]));
	    

	    $itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Hits: ") . $pageHit["count"];

	    $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	  } else {
	    $GLOBALS["core"]->sqlDelete("mod_stats_pm", "page_id", $pageHit["page_id"]);
	  }
	}
      }


      return PHPWS_Template::processTemplate($tags, "stats", 
					     "stats/view/stat_item.tpl");
    }
  }

  function _getTopFAQs() {
    $topFAQs = $GLOBALS["core"]->sqlSelect(FAQ_TABLE, array("hidden"=>0, "approved"=>1), NULL, "hits DESC", NULL, NULL, $this->_numStats);

    if($topFAQs || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' . $this->_numStats . $_SESSION["translate"]->it(" Most Read FAQs");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      if(!empty($topFAQs)) {
	foreach($topFAQs as $faq) {
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($faq["label"],
						  "faq", array("FAQ_op"=>"view", "FAQ_id"=>$faq["id"]));
	  
	  $itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Read: ") . $faq["hits"];
	  $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	}
      }
      
      return PHPWS_Template::processTemplate($tags, "stats", 
						   "stats/view/stat_item.tpl");
    }
  }

  function _getHighestRatedFAQs() {
    $highestRatedFAQs = $GLOBALS["core"]->sqlSelect(FAQ_TABLE, array("hidden"=>0, "approved"=>1), NULL, "compScore DESC", NULL, NULL, $this->_numStats);

    if($highestRatedFAQs || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' . $this->_numStats . $_SESSION["translate"]->it(" Highest Rated FAQs");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      if(!empty($highestRatedFAQs)) {
	foreach($highestRatedFAQs as $faq) {
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($faq["label"],
						  "faq", array("FAQ_op"=>"view", "FAQ_id"=>$faq["id"]));
	  
	  $itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Rated: ") . $faq["compScore"];
	  $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	}
      }
      
      return PHPWS_Template::processTemplate($tags, "stats", 
					     "stats/view/stat_item.tpl");
    }
  }

  function _getRecentUsers() {
    $recentUsers = $GLOBALS["core"]->sqlSelect(USERS_TABLE, NULL, NULL, "last_on DESC", NULL, NULL, $this->_numStats);
    $item = false;

    if($recentUsers || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Most Recently Logged in Users");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      if(!empty($recentUsers)) {
	foreach($recentUsers as $user) {
	  $item = true;
	  $itemTag["ITEM"] = $user["username"];
	
	  $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	}
      }

      if($item || $this->_showEmpty)
	return PHPWS_Template::processTemplate($tags, "stats", 
					       "stats/view/stat_item.tpl");
    }
  }

  function _getTopLinks() {    
    $topLinks = $GLOBALS["core"]->sqlSelect(LINKS_TABLE, array("active"=>1), NULL, "hits DESC", NULL, NULL, $this->_numStats);
    
    if($topLinks || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' .
	$this->_numStats . $_SESSION["translate"]->it(" Most Active Links");
      $tags["STAT_ITEM_LISTINGS"] = "";

      if(!empty($topLinks)) {
	foreach($topLinks as $link) {
	  if($link["hits"] > 0) {
	    $itemTag["ITEM"] = PHPWS_Text::moduleLink($link["title"], "linkman", 
						      array("LMN_op"=>"visitLink", "LMN_id"=>$link["id"]), "blank");
	  
	    $itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Hits: ") . $link["hits"];
	    $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
	  }
	}
      }

      return PHPWS_Template::processTemplate($tags, "stats", 
					   "stats/view/stat_item.tpl");
    }
  }


  function _getRecentFiles() {
    $sql = "SELECT files.name, files.doc FROM ".$GLOBALS["core"]->tbl_prefix . DOCUMENTS_FILES_TABLE . " AS files, ".
      $GLOBALS["core"]->tbl_prefix . DOCUMENTS_DOCS_TABLE . " AS docs WHERE files.doc=docs.id AND docs.hidden=0 AND docs.approved=1 ORDER BY files.created DESC LIMIT " . $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false;

    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Most Recent Uploaded Document Files");
      $tags["STAT_ITEM_LISTINGS"] = "";

      while($document = $result->fetchRow()) {
	$itemTag["ITEM"] = PHPWS_Text::moduleLink($document["name"], 
						  "documents", 
						  array("JAS_DocumentManager_op"=>"viewDocument", "JAS_Document_id"=>$document["doc"]));
	
	$tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
      }

      return PHPWS_Template::processTemplate($tags, "stats", 
					   "stats/view/stat_item.tpl");
    }
  }

  function _getRecentComments() {
    $sql = "SELECT module, itemid, subject, author FROM ".$GLOBALS["core"]->tbl_prefix . COMMENTS_TABLE . " AS comments ORDER BY postDate DESC LIMIT ". $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false;

    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Most Recent Comments");
      $tags["STAT_ITEM_LISTINGS"] = "";

      while($comment = $result->fetchRow()) {
	if(empty($comment["subject"])) {
	  $comment["subject"] = $_SESSION["translate"]->it("No Subject");
	}

	switch($comment["module"]) {
	case "announce":
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($comment["subject"], 
						    "announce", 
						    array("ANN_user_op"=>"view", "ANN_id"=>$comment["itemid"]));	  
	  break;
	case "pagemaster":
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($comment["subject"], 
						    "pagemaster", 
						    array("PAGE_user_op"=>"view_page", "PAGE_id"=>$comment["itemid"]));	  
	  break;
	case "faq":
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($comment["subject"], 
						    "faq", 
						    array("FAQ_op"=>"view", "FAQ_id"=>$comment["itemid"]));	  
	  break;
	case "poll":
	  $itemTag["ITEM"] = PHPWS_Text::moduleLink($comment["subject"], 
						    "poll", 
						    array("PHPWS_MAN_OP"=>"view", "PHPWS_MAN_ITEMS[0]"=>$comment["itemid"]));	  
	  break;
	default:
	  $itemTag["ITEM"] = $comment["subject"];
	  break;
	}

	$itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Submitted by: ").
	  $comment["author"];
	
	$tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
      }

      return PHPWS_Template::processTemplate($tags, "stats", 
					   "stats/view/stat_item.tpl");
    }
  }

  function _getRecentPhotos() {
    $sql = "SELECT pics.id, pics.album, pics.tnname, pics.label FROM ".$GLOBALS["core"]->tbl_prefix . PHOTOS_PICS_TABLE . " AS pics, ".
      $GLOBALS["core"]->tbl_prefix . PHOTOS_ALBUMS_TABLE . " AS albums WHERE pics.album=albums.id AND albums.hidden=0 AND albums.approved=1 AND pics.hidden=0 AND pics.approved=1 ORDER BY pics.created DESC LIMIT " . $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false; 
    
    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it(" Most Recent Photos");
      $tags["STAT_ITEM_LISTINGS"] = "";

      $rowCounter = 0;

      while($photo = $result->fetchRow()) {
	  if($rowCounter == 0)
	    $tags["STAT_ITEM_LISTINGS"] .= "<tr>";

	  $image = "<img src=\"./images/photoalbum/" . $photo["album"] .
	  "/".$photo["tnname"] . "\" />" ;

	  $itemTag["IMAGE"] = PHPWS_Text::moduleLink($image,"photoalbum",
						     array("PHPWS_Album_id"=>$photo["album"], "PHPWS_Photo_op"=>"view",
							   "PHPWS_Photo_id"=>$photo["id"]));


	  $itemTag["TEXT"] = PHPWS_Text::moduleLink($photo["label"],"photoalbum",
	     array("PHPWS_Album_id"=>$photo["album"], "PHPWS_Photo_op"=>"view",
		    "PHPWS_Photo_id"=>$photo["id"]));
      
	  $tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/photo_listing.tpl");

	  $rowCounter++;
	  if($rowCounter == 3) {
	    $tags["STAT_ITEM_LISTINGS"] .= "</tr>";
	    $rowCounter = 0;
	  }
      }
      if($rowCounter != 0)
	$tags["STAT_ITEM_LISTINGS"] .= "</tr>";	      

      return PHPWS_Template::processTemplate($tags, "stats", 
					   "stats/view/photo_item.tpl");
    }
  }

  function _getTopCommentedAnn() {
    $sql = "select COUNT(itemId) as occurances, itemId FROM ".$GLOBALS["core"]->tbl_prefix . COMMENTS_TABLE . " where module='announce' group by itemId order by occurances DESC limit " . $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false;

    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' . 
	$this->_numStats . $_SESSION["translate"]->it(" Most Commented Announcements");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      while($row = $result->fetchRow()) {
	$items = true;
	$ann = $GLOBALS["core"]->sqlSelect(ANN_TABLE, array("id"=>$row["itemId"], "approved"=>1, "active"=>1));
	$itemTag["ITEM"] = PHPWS_Text::moduleLink($ann[0]["subject"],
						  "announce", array("ANN_user_op"=>"view", "ANN_id"=>$row["itemId"]));

	$itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Comments: ") . $row["occurances"];
	$tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
      }
      if($items == true || $this->_showEmpty) {
	return PHPWS_Template::processTemplate($tags, "stats", 
					       "stats/view/stat_item.tpl"); 
      }
    }
  }

  function _getTopNoteSenders() {
    $sql = "select COUNT(fromUser) as occurances, fromUser FROM " . $GLOBALS["core"]->tbl_prefix . NOTES_TABLE . " group by fromUser order by occurances DESC limit " . $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false;

    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Most Active Note Senders");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      while($row = $result->fetchRow()) {
	$items = true;
	$itemTag["ITEM"] = $row["fromUser"];
	$itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Sent: ") . $row["occurances"];
	$tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
      }
      
      if($items || $this->_showEmpty)
	return PHPWS_Template::processTemplate($tags, "stats", 
					       "stats/view/stat_item.tpl"); 
    }
  }

  function _getTopNoteRecievers() {
    $sql = "select COUNT(toUser) as occurances, toUser FROM " . $GLOBALS["core"]->tbl_prefix . NOTES_TABLE . " group by toUser order by occurances DESC limit " . $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false;

    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Most Active Note Receivers");
      $tags["STAT_ITEM_LISTINGS"] = "";
      
      while($row = $result->fetchRow()) {
	$items = true;
	$itemTag["ITEM"] = $row["toUser"];
	$itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Received: ") . $row["occurances"];
	$tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
      }

      if($items || $this->_showEmpty)
	return PHPWS_Template::processTemplate($tags, "stats", 
					       "stats/view/stat_item.tpl"); 
    }
  }

  function _getTopAnnSubmitters() {
    $sql = "select COUNT(userCreated) as occurances, userCreated FROM " . $GLOBALS["core"]->tbl_prefix . ANN_TABLE . " group by userCreated order by occurances DESC limit " . $this->_numStats;

    $result = $GLOBALS["core"]->query($sql);
    $items  = false;

    if($result || $this->_showEmpty) {
      $tags["STAT_ITEM_TITLE"] = $_SESSION["translate"]->it("Top") . ' ' . 
	$this->_numStats . $_SESSION["translate"]->it(" Most Active Announcement Submitters");
      $tags["STAT_ITEM_LISTINGS"] = "";

      while($row = $result->fetchRow()) {
	$items = true;
	$itemTag["ITEM"] = $row["userCreated"];

	$itemTag["ITEM_COUNT"] = $_SESSION["translate"]->it("Submitted: ") . $row["occurances"];
	$tags["STAT_ITEM_LISTINGS"] .= PHPWS_Template::processTemplate($itemTag, "stats", "stats/view/listing.tpl");
      }
      
      if($items || $this->_showEmpty)
	return PHPWS_Template::processTemplate($tags, "stats", 
					       "stats/view/stat_item.tpl"); 
    }
  }

  function action($command, $getTitle=FALSE) {
    switch($command) {
    case "view":
      if($getTitle)
	return $_SESSION["translate"]->it("Stats");
      else
	return $this->view();
      break;
    case "setup":
      if($getTitle)
	return $_SESSION["translate"]->it("Stats Settings");
      else
	return $this->setup();
      break;
    case "saveSetup":
      if($getTitle)
	return $_SESSION["translate"]->it("Stats Settings");
      else
	return $this->saveSetup() . $this->setup();      
      break;
    }
  }

}


?>