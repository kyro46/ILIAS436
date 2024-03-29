<?php

/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

/** @defgroup ModulesWiki Modules/Wiki
 */

include_once "./Services/Object/classes/class.ilObject.php";
include_once ("./Modules/Wiki/classes/class.ilWikiUtil.php");

/**
 * Class ilObjWiki
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 * @ingroup ModulesWiki
 */
class ilObjWiki extends ilObject
{
	protected $online = false;
	protected $public_notes = true;
	
	/**
	* Constructor
	* @access	public
	* @param	integer	reference_id or object_id
	* @param	boolean	treat the id as reference_id (true) or object_id (false)
	*/
	function ilObjWiki($a_id = 0,$a_call_by_reference = true)
	{
		$this->type = "wiki";
		$this->ilObject($a_id,$a_call_by_reference);
	}

	/**
	* Set Online.
	*
	* @param	boolean	$a_online	Online
	*/
	function setOnline($a_online)
	{
		$this->online = $a_online;
	}

	/**
	* Get Online.
	*
	* @return	boolean	Online
	*/
	function getOnline()
	{
		return $this->online;
	}

	/**
	* Set Enable Rating.
	*
	* @param	boolean	$a_rating	Enable Rating
	*/
	function setRating($a_rating)
	{
		$this->rating = (bool)$a_rating;
	}

	/**
	* Get Enable Rating.
	*
	* @return	boolean	Enable Rating
	*/
	function getRating()
	{
		return $this->rating;
	}
	
	/**
	* Set Enable Rating Side Block.
	*
	* @param	boolean	$a_rating	
	*/
	function setRatingAsBlock($a_rating)
	{
		$this->rating_block = (bool)$a_rating;
	}

	/**
	* Get Enable Rating Side Block.
	*
	* @return	boolean
	*/
	function getRatingAsBlock()
	{
		return $this->rating_block;
	}
	
	/**
	* Set Enable Rating For New Pages.
	*
	* @param	boolean	$a_rating	
	*/
	function setRatingForNewPages($a_rating)
	{
		$this->rating_new_pages = (bool)$a_rating;
	}

	/**
	* Get Enable Rating For New Pages.
	*
	* @return	boolean
	*/
	function getRatingForNewPages()
	{
		return $this->rating_new_pages;
	}
		
	/**
	* Set Enable Rating Categories.
	*
	* @param	boolean	$a_rating	
	*/
	function setRatingCategories($a_rating)
	{
		$this->rating_categories = (bool)$a_rating;
	}

	/**
	* Get Enable Rating Categories.
	*
	* @return	boolean
	*/
	function getRatingCategories()
	{
		return $this->rating_categories;
	}
	
	/**
	 * Set public notes
	 */
	public function setPublicNotes($a_val)
	{
		$this->public_notes = $a_val;
	}

	/**
	 * Get public notes
	 */
	public function getPublicNotes()
	{
		return $this->public_notes;
	}

	/**
	 * Set important pages
	 *
	 * @param	boolean	$a_val	important pages
	 */
	public function setImportantPages($a_val)
	{
		$this->imp_pages = $a_val;
	}

	/**
	 * Get important pages
	 *
	 * @return	boolean	important pages
	 */
	public function getImportantPages()
	{
		return $this->imp_pages;
	}

	/**
	* Set Start Page.
	*
	* @param	string	$a_startpage	Start Page
	*/
	function setStartPage($a_startpage)
	{
		$this->startpage = ilWikiUtil::makeDbTitle($a_startpage);
	}

	/**
	* Get Start Page.
	*
	* @return	string	Start Page
	*/
	function getStartPage()
	{
		return $this->startpage;
	}

	/**
	* Set ShortTitle.
	*
	* @param	string	$a_shorttitle	ShortTitle
	*/
	function setShortTitle($a_shorttitle)
	{
		$this->shorttitle = $a_shorttitle;
	}

	/**
	* Get ShortTitle.
	*
	* @return	string	ShortTitle
	*/
	function getShortTitle()
	{
		return $this->shorttitle;
	}

		/**
	* Set Introduction.
	*
	* @param	string	$a_introduction	Introduction
	*/
	function setIntroduction($a_introduction)
	{
		$this->introduction = $a_introduction;
	}

	/**
	* Get Introduction.
	*
	* @return	string	Introduction
	*/
	function getIntroduction()
	{
		return $this->introduction;
	}

	/**
	* get ID of assigned style sheet object
	*/
	function getStyleSheetId()
	{
		return $this->style_id;
	}

	/**
	* set ID of assigned style sheet object
	*/
	function setStyleSheetId($a_style_id)
	{
		$this->style_id = $a_style_id;
	}

	/**
	 * Set page toc
	 *
	 * @param	boolean	$a_val	page toc
	 */
	public function setPageToc($a_val)
	{
		$this->page_toc = $a_val;
	}

	/**
	 * Get page toc
	 *
	 * @return	boolean	page toc
	 */
	public function getPageToc()
	{
		return $this->page_toc;
	}

	/**
	 * Is wiki an online help wiki?
	 *
	 * @return boolean true, if current wiki is an online help wiki
	 */
	static function isOnlineHelpWiki($a_ref_id)
	{
		if ($a_ref_id > 0 && $a_ref_id == OH_REF_ID)
		{
//			return true;
		}
		return false;
	}

	/**
	* Create new wiki
	*/
	function create($a_prevent_start_page_creation = false)
	{
		global $ilDB;

		parent::create();
		
		$ilDB->insert("il_wiki_data", array(
			"id" => array("integer", $this->getId()),
			"is_online" => array("integer", (int) $this->getOnline()),
			"startpage" => array("text", $this->getStartPage()),
			"short" => array("text", $this->getShortTitle()),
			"rating" => array("integer", (int) $this->getRating()),
			"public_notes" => array("integer", (int) $this->getPublicNotes()),
			"introduction" => array("clob", $this->getIntroduction())
			));

		// create start page
		if ($this->getStartPage() != "" && !$a_prevent_start_page_creation)
		{
			include_once("./Modules/Wiki/classes/class.ilWikiPage.php");
			$start_page = new ilWikiPage();
			$start_page->setWikiId($this->getId());
			$start_page->setTitle($this->getStartPage());
			$start_page->create();
		}

		if (((int) $this->getStyleSheetId()) > 0)
		{
			include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
			ilObjStyleSheet::writeStyleUsage($this->getId(), $this->getStyleSheetId());
		}
	}

	/**
	* update object data
	*
	* @access	public
	* @return	boolean
	*/
	function update($a_prevent_start_page_creation = false)
	{
		global $ilDB;
		
		if (!parent::update())
		{			
			return false;
		}
		
		$ilDB->update("il_wiki_data", array(
			"is_online" => array("integer", $this->getOnline()),
			"startpage" => array("text", $this->getStartPage()),
			"short" => array("text", $this->getShortTitle()),
			"rating" => array("integer", $this->getRating()),
			"rating_side" => array("integer", $this->getRatingAsBlock()),
			"rating_new" => array("integer", $this->getRatingForNewPages()),
			"rating_ext" => array("integer", $this->getRatingCategories()),
			"public_notes" => array("integer", $this->getPublicNotes()),
			"introduction" => array("clob", $this->getIntroduction()),
			"imp_pages" => array("integer", $this->getImportantPages()),
			"page_toc" => array("integer", $this->getPageToc())
			), array(
			"id" => array("integer", $this->getId())
			));

		// check whether start page exists
		include_once("./Modules/Wiki/classes/class.ilWikiPage.php");
		if (!ilWikiPage::exists($this->getId(), $this->getStartPage())
			&& !$a_prevent_start_page_creation)
		{
			$start_page = new ilWikiPage();
			$start_page->setWikiId($this->getId());
			$start_page->setTitle($this->getStartPage());
			$start_page->create();
		}

		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
		ilObjStyleSheet::writeStyleUsage($this->getId(), $this->getStyleSheetId());

		return true;
	}
	
	/**
	* Read wiki data
	*/
	function read()
	{
		global $ilDB;
		
		parent::read();
		
		$query = "SELECT * FROM il_wiki_data WHERE id = ".
			$ilDB->quote($this->getId(), "integer");
		$set = $ilDB->query($query);
		$rec = $ilDB->fetchAssoc($set);

		$this->setOnline($rec["is_online"]);
		$this->setStartPage($rec["startpage"]);
		$this->setShortTitle($rec["short"]);
		$this->setRating($rec["rating"]);
		$this->setRatingAsBlock($rec["rating_side"]);
		$this->setRatingForNewPages($rec["rating_new"]);
		$this->setRatingCategories($rec["rating_ext"]);
		$this->setPublicNotes($rec["public_notes"]);
		$this->setIntroduction($rec["introduction"]);
		$this->setImportantPages($rec["imp_pages"]);
		$this->setPageToc($rec["page_toc"]);

		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
		$this->setStyleSheetId((int) ilObjStyleSheet::lookupObjectStyle($this->getId()));

	}


	/**
	* delete object and all related data	
	*
	* @access	public
	* @return	boolean	true if all object data were removed; false if only a references were removed
	*/
	function delete()
	{
		global $ilDB;
		
		// always call parent delete function first!!
		if (!parent::delete())
		{
			return false;
		}
				
		// delete record of table il_wiki_data
		$query = "DELETE FROM il_wiki_data".
			" WHERE id = ".$ilDB->quote($this->getId(), "integer");
		$ilDB->manipulate($query);

		// remove all notifications
		include_once "./Services/Notification/classes/class.ilNotification.php";
		ilNotification::removeForObject(ilNotification::TYPE_WIKI, $this->getId());
		
		include_once("./Modules/Wiki/classes/class.ilWikiPage.php");
		ilWikiPage::deleteAllPagesOfWiki($this->getId());
		
		return true;
	}

	/**
	* Check availability of short title
	*/
	static function checkShortTitleAvailability($a_short_title)
	{
		global $ilDB;
		
		$res = $ilDB->queryF("SELECT id FROM il_wiki_data WHERE short = %s",
			array("text"), array($a_short_title));
		if ($ilDB->fetchAssoc($res))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	* init default roles settings
	* 
	* If your module does not require any default roles, delete this method 
	* (For an example how this method is used, look at ilObjForum)
	* 
	* @access	public
	* @return	array	object IDs of created local roles.
	*/
	function initDefaultRoles()
	{
		global $rbacadmin;
		
		// create a local role folder
		//$rfoldObj = $this->createRoleFolder("Local roles","Role Folder of forum obj_no.".$this->getId());

		// create moderator role and assign role to rolefolder...
		//$roleObj = $rfoldObj->createRole("Moderator","Moderator of forum obj_no.".$this->getId());
		//$roles[] = $roleObj->getId();

		//unset($rfoldObj);
		//unset($roleObj);

		return $roles ? $roles : array();
	}

	/**
	* notifys an object about an event occured
	* Based on the event happend, each object may decide how it reacts.
	*
	* If you are not required to handle any events related to your module, just delete this method.
	* (For an example how this method is used, look at ilObjGroup)
	*
	* @access	public
	* @param	string	event
	* @param	integer	reference id of object where the event occured
	* @param	array	passes optional parameters if required
	* @return	boolean
	*/
	function notify($a_event,$a_ref_id,$a_parent_non_rbac_id,$a_node_id,$a_params = 0)
	{
		global $tree;
		
		switch ($a_event)
		{
			case "link":
				
				//var_dump("<pre>",$a_params,"</pre>");
				//echo "Module name ".$this->getRefId()." triggered by link event. Objects linked into target object ref_id: ".$a_ref_id;
				//exit;
				break;
			
			case "cut":
				
				//echo "Module name ".$this->getRefId()." triggered by cut event. Objects are removed from target object ref_id: ".$a_ref_id;
				//exit;
				break;
				
			case "copy":
			
				//var_dump("<pre>",$a_params,"</pre>");
				//echo "Module name ".$this->getRefId()." triggered by copy event. Objects are copied into target object ref_id: ".$a_ref_id;
				//exit;
				break;

			case "paste":
				
				//echo "Module name ".$this->getRefId()." triggered by paste (cut) event. Objects are pasted into target object ref_id: ".$a_ref_id;
				//exit;
				break;
			
			case "new":
				
				//echo "Module name ".$this->getRefId()." triggered by paste (new) event. Objects are applied to target object ref_id: ".$a_ref_id;
				//exit;
				break;
		}

		// At the beginning of the recursive process it avoids second call of the notify function with the same parameter
		if ($a_node_id==$_GET["ref_id"])
		{
			$parent_obj =& $this->ilias->obj_factory->getInstanceByRefId($a_node_id);
			$parent_type = $parent_obj->getType();
			if($parent_type == $this->getType())
			{
				$a_node_id = (int) $tree->getParentId($a_node_id);
			}
		}
		
		parent::notify($a_event,$a_ref_id,$a_parent_non_rbac_id,$a_node_id,$a_params);
	}
	
	/**
	 * Lookup whether rating is activated.
	 *
	 * @param	int			$a_wiki_id		Wiki ID
	 *
	 * @return	boolean		Rating activated?
	 */
	static function _lookupRating($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "rating");
	}
	
	/**
	 * Lookup whether rating categories are activated.
	 *
	 * @param	int			$a_wiki_id		Wiki ID
	 *
	 * @return	boolean		Rating categories activated?
	 */
	static function _lookupRatingCategories($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "rating_ext");
	}
	
	/**
	 * Lookup whether rating side block is activated.
	 *
	 * @param	int			$a_wiki_id		Wiki ID
	 *
	 * @return	boolean		Rating side block activated?
	 */
	static function _lookupRatingAsBlock($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "rating_side");
	}

	/**
	 * Lookup whether public notes are activated
	 *
	 * @param	int			$a_wiki_id		Wiki ID
	 *
	 * @return	boolean		public notes activated?
	 */
	static function _lookupPublicNotes($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "public_notes");
	}

	/**
	* Lookup a data field
	*
	* @param	int			$a_wiki_id		Wiki ID
	* @param	string		$a_field		Field Name
	*
	* @return	mixed		field value
	*/
	private static function _lookup($a_wiki_id, $a_field)
	{
		global $ilDB;

		$query = "SELECT $a_field FROM il_wiki_data WHERE id = ".
			$ilDB->quote($a_wiki_id, "integer");
		$set = $ilDB->query($query);
		$rec = $ilDB->fetchAssoc($set);
		return $rec[$a_field];
	}

	/**
	* Lookup start page
	*
	* @param	int			$a_wiki_id		Wiki ID
	*
	* @return	boolean		Rating activated?
	*/
	static function _lookupStartPage($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "startpage");
	}

	/**
	 * Write start page
	 */
	static function writeStartPage($a_id, $a_name)
	{
		global $ilDB;

		include_once("./Modules/Wiki/classes/class.ilWikiUtil.php");
		$ilDB->manipulate("UPDATE il_wiki_data SET ".
			" startpage = ".$ilDB->quote(ilWikiUtil::makeDbTitle($a_name), "text").
			" WHERE id = ".$ilDB->quote($a_id, "integer")
			);
	}

		/**
	* Search in Wiki
	*/
	static function _performSearch($a_wiki_id, $a_searchterm)
	{
		// query parser
		include_once 'Services/Search/classes/class.ilQueryParser.php';

		$query_parser = new ilQueryParser($a_searchterm);
		$query_parser->setCombination("or");
		$query_parser->parse();

		include_once 'Services/Search/classes/class.ilSearchResult.php';
		$search_result = new ilSearchResult();
		if($query_parser->validate())
		{

			include_once 'Services/Search/classes/class.ilObjectSearchFactory.php';
			$wiki_search =& ilObjectSearchFactory::_getWikiContentSearchInstance($query_parser);
			$wiki_search->setFilter(array('wpg'));
			$search_result->mergeEntries($wiki_search->performSearch());
		}
		
		$entries = $search_result->getEntries();
		
		$found_pages = array();
		foreach($entries as $entry)
		{
			if ($entry["obj_id"] == $a_wiki_id && is_array($entry["child"]))
			{
				foreach($entry["child"] as $child)
				{
					$found_pages[] = $child;
				}
			}
		}

		return $found_pages;
	}

	//
	// Important pages
	//

	/**
	 * Lookup whether important pages are activated.
	 *
	 * @param	int			$a_wiki_id		Wiki ID
	 *
	 * @return	boolean		Important pages activated?
	 */
	static function _lookupImportantPages($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "imp_pages");
	}

	/**
	 * Get important pages list
	 *
	 * @param
	 * @return
	 */
	static function _lookupImportantPagesList($a_wiki_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM il_wiki_imp_pages WHERE ".
			" wiki_id = ".$ilDB->quote($a_wiki_id, "integer")." ORDER BY ord ASC "
			);

		$imp_pages = array();

		while ($rec = $ilDB->fetchAssoc($set))
		{
			$imp_pages[] = $rec;
		}
		return $imp_pages;
	}

	/**
	 * Get important pages list
	 *
	 * @param
	 * @return
	 */
	static function _lookupMaxOrdNrImportantPages($a_wiki_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT MAX(ord) as m FROM il_wiki_imp_pages WHERE ".
			" wiki_id = ".$ilDB->quote($a_wiki_id, "integer")
			);

		$rec = $ilDB->fetchAssoc($set);
		return (int) $rec["m"];
	}


	/**
	 * Add important page
	 *
	 * @param	int		page id
	 */
	function addImportantPage($a_page_id, $a_nr = 0, $a_indent = 0)
	{
		global $ilDB;

		if (!$this->isImportantPage($a_page_id))
		{
			if ($a_nr == 0)
			{
				$a_nr = ilObjWiki::_lookupMaxOrdNrImportantPages($this->getId()) + 10;
			}

			$ilDB->manipulate("INSERT INTO il_wiki_imp_pages ".
				"(wiki_id, ord, indent, page_id) VALUES (".
				$ilDB->quote($this->getId(), "integer").",".
				$ilDB->quote($a_nr, "integer").",".
				$ilDB->quote($a_indent, "integer").",".
				$ilDB->quote($a_page_id, "integer").
				")");
		}
	}

	/**
	 * Is page an important page?
	 *
	 * @param
	 * @return
	 */
	function isImportantPage($a_page_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT * FROM il_wiki_imp_pages WHERE ".
			" wiki_id = ".$ilDB->quote($this->getId(), "integer")." AND ".
			" page_id = ".$ilDB->quote($a_page_id, "integer")
		);
		if ($rec = $ilDB->fetchAssoc($set))
		{
			return true;
		}
		return false;
	}

	/**
	 * Remove important page
	 *
	 * @param	int		page id
	 */
	function removeImportantPage($a_id)
	{
		global $ilDB;

		$ilDB->manipulate("DELETE FROM il_wiki_imp_pages WHERE "
			." wiki_id = ".$ilDB->quote($this->getId(), "integer")
			." AND page_id = ".$ilDB->quote($a_id, "integer")
			);

		$this->fixImportantPagesNumbering();
	}

	/**
	 * Save ordering and indentation
	 *
	 * @param
	 * @return
	 */
	function saveOrderingAndIndentation($a_ord, $a_indent)
	{
		global $ilDB;

		$ipages = ilObjWiki::_lookupImportantPagesList($this->getId());

		foreach ($ipages as $k => $v)
		{
			if (isset($a_ord[$v["page_id"]]))
			{
				$ipages[$k]["ord"] = (int) $a_ord[$v["page_id"]];
			}
			if (isset($a_indent[$v["page_id"]]))
			{
				$ipages[$k]["indent"] = (int) $a_indent[$v["page_id"]];
			}
		}
		$ipages = ilUtil::sortArray($ipages, "ord", "asc", true);

		// fix indentation: no 2 is allowed after a 0
		$c_indent = 0;
		$fixed = false;
		foreach ($ipages as $k => $v)
		{
			if ($ipages[$k]["indent"] == 2 && $c_indent == 0)
			{
				$ipages[$k]["indent"] = 1;
				$fixed = true;
			}
			$c_indent = $ipages[$k]["indent"];
		}
		
		$ord = 10;
		reset($ipages);
		foreach ($ipages as $k => $v)
		{
			$ilDB->manipulate($q = "UPDATE il_wiki_imp_pages SET ".
				" ord = ".$ilDB->quote($ord, "integer").",".
				" indent = ".$ilDB->quote($v["indent"], "integer").
				" WHERE wiki_id = ".$ilDB->quote($v["wiki_id"], "integer").
				" AND page_id = ".$ilDB->quote($v["page_id"], "integer")
				);
			$ord+=10;
		}
		
		return $fixed;
	}

	/**
	 * Fix important pages numbering
	 */
	function fixImportantPagesNumbering()
	{
		global $ilDB;

		$ipages = ilObjWiki::_lookupImportantPagesList($this->getId());

		// fix indentation: no 2 is allowed after a 0
		$c_indent = 0;
		$fixed = false;
		foreach ($ipages as $k => $v)
		{
			if ($ipages[$k]["indent"] == 2 && $c_indent == 0)
			{
				$ipages[$k]["indent"] = 1;
				$fixed = true;
			}
			$c_indent = $ipages[$k]["indent"];
		}

		$ord = 10;
		foreach ($ipages as $k => $v)
		{
			$ilDB->manipulate($q = "UPDATE il_wiki_imp_pages SET ".
				" ord = ".$ilDB->quote($ord, "integer").
				", indent = ".$ilDB->quote($v["indent"], "integer").
				" WHERE wiki_id = ".$ilDB->quote($v["wiki_id"], "integer").
				" AND page_id = ".$ilDB->quote($v["page_id"], "integer")
				);
			$ord+=10;
		}

	}

	//
	// Page TOC
	//

	/**
	 * Lookup whether important pages are activated.
	 *
	 * @param	int			$a_wiki_id		Wiki ID
	 *
	 * @return	boolean		Important pages activated?
	 */
	static function _lookupPageToc($a_wiki_id)
	{
		return ilObjWiki::_lookup($a_wiki_id, "page_toc");
	}

	/**
	 * Clone wiki
	 *
	 * @param int target ref_id
	 * @param int copy id
	 */
	public function cloneObject($a_target_id,$a_copy_id = 0)
	{
		global $ilDB, $ilUser, $ilias;

		$new_obj = parent::cloneObject($a_target_id,$a_copy_id);
	 	
		$new_obj->setTitle($this->getTitle());
		$new_obj->setStartPage($this->getStartPage());
		$new_obj->setShortTitle($this->getShortTitle());
		$new_obj->setRating($this->getRating());
		$new_obj->setRatingAsBlock($this->getRatingAsBlock());
		$new_obj->setRatingForNewPages($this->getRatingForNewPages());
		$new_obj->setRatingCategories($this->getRatingCategories());
		$new_obj->setPublicNotes($this->getPublicNotes());
		$new_obj->setIntroduction($this->getIntroduction());
		$new_obj->setImportantPages($this->getImportantPages());
		$new_obj->setPageToc($this->getPageToc());
		$new_obj->update();

		// set/copy stylesheet
		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
		$style_id = $this->getStyleSheetId();
		if ($style_id > 0 && !ilObjStyleSheet::_lookupStandard($style_id))
		{
			$style_obj = $ilias->obj_factory->getInstanceByObjId($style_id);
			$new_id = $style_obj->ilClone();
			$new_obj->setStyleSheetId($new_id);
			$new_obj->update();
		}

		// copy content
		include_once("./Modules/Wiki/classes/class.ilWikiPage.php");
		$pages = ilWikiPage::getAllPages($this->getId());
		if (count($pages) > 0)
		{
			// if we have any pages, delete the start page first
			$pg_id = ilWikiPage::getPageIdForTitle($new_obj->getId(), $new_obj->getStartPage());
			$start_page = new ilWikiPage($pg_id);
			$start_page->delete();
		}
		$map = array();
		foreach ($pages as $p)
		{
			$page = new ilWikiPage($p["id"]);
			$new_page = new ilWikiPage();
			$new_page->setTitle($page->getTitle());
			$new_page->setWikiId($new_obj->getId());
			$new_page->setTitle($page->getTitle());
			$new_page->setBlocked($page->getBlocked());
			$new_page->setRating($page->getRating());
			$new_page->create();
			
			$new_page->setXMLContent($page->copyXMLContent(true));
			$new_page->buildDom();
			$new_page->update();
			$map[$p["id"]] = $new_page->getId();
		}
		
		// copy important pages
		foreach (ilObjWiki::_lookupImportantPagesList($this->getId()) as $ip)
		{
			$new_obj->addImportantPage($map[$ip["page_id"]], $ip["ord"], $ip["indent"]);
		}

		// copy rating categories
		include_once("./Services/Rating/classes/class.ilRatingCategory.php");
		foreach (ilRatingCategory::getAllForObject($this->getId()) as $rc)
		{
			$new_rc = new ilRatingCategory();
			$new_rc->setParentId($new_obj->getId());
			$new_rc->setTitle($rc["title"]);
			$new_rc->setDescription($rc["description"]);
			$new_rc->save();
		}
		
		return $new_obj;
	}

}
?>
