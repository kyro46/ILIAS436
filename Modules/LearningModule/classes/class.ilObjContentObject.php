<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once "./Services/Object/classes/class.ilObject.php";
require_once "Services/MetaData/classes/class.ilMDLanguageItem.php";
require_once("./Services/Xml/classes/class.ilNestedSetXML.php");

/** @defgroup ModulesIliasLearningModule Modules/IliasLearningModule
 */

/**
* Class ilObjContentObject
*
* @author Alex Killing <alex.killing@gmx.de>
* @author Sascha Hofmann <saschahofmann@gmx.de>
*
* @version $Id: class.ilObjContentObject.php 42760 2013-06-17 19:35:29Z akill $
*
* @ingroup ModulesIliasLearningModule
*/
class ilObjContentObject extends ilObject
{
	var $lm_tree;
	var $meta_data;
	var $layout;
	var $style_id;
	var $pg_header;
	var $online;
	
	private $import_dir = '';

	/**
	* Constructor
	* @access	public
	* @param	integer	reference_id or object_id
	* @param	boolean	treat the id as reference_id (true) or object_id (false)
	*/
	function ilObjContentObject($a_id = 0,$a_call_by_reference = true)
	{
		// this also calls read() method! (if $a_id is set)
		$this->ilObject($a_id,$a_call_by_reference);

		$this->mob_ids = array();
		$this->file_ids = array();
		$this->q_ids = array();
	}

	/**
	* create content object
	*/
	function create($a_no_meta_data = false)
	{
		global $ilUser;

		parent::create();
		
		// meta data will be created by
		// import parser
		if (!$a_no_meta_data)
		{
			$this->createMetaData();
		}

		$this->createProperties();
	}

	/**
	* init default roles settings
	* OBSOLETE. DON'T USE, READ TEXT BELOW
	* @access	public
	* @return	array	object IDs of created local roles.
	*/
	function initDefaultRoles()
	{
		return array();

		global $rbacadmin, $rbacreview;

		// create a local role folder
		$rfoldObj = $this->createRoleFolder("Local roles","Role Folder of content object ".$this->getId());

		// note: we don't need any roles here, local "author" roles must
		// be created manually. subscription roles have been abandoned.
		/*
		// create author role and assign role to rolefolder...
		$roleObj = $rfoldObj->createRole("author object ".$this->getRefId(),"author of content object ref id ".$this->getRefId());
		$roles[] = $roleObj->getId();

		// copy permissions from author template to new role
		$rbacadmin->copyRolePermissions($this->getAuthorRoleTemplateId(), 8, $rfoldObj->getRefId(), $roleObj->getId());

		// grant all allowed operations of role to current learning module
		$rbacadmin->grantPermission($roleObj->getId(),
			$rbacreview->getOperationsOfRole($roleObj->getId(), "lm", $rfoldObj->getRefId()),
			$this->getRefId());*/

		unset($rfoldObj);
		//unset($roleObj);

		return $roles ? $roles : array();
	}


	/**
	* read data of content object
	*/
	function read()
	{
		parent::read();
#		echo "Content<br>\n";

		$this->lm_tree = new ilTree($this->getId());
		$this->lm_tree->setTableNames('lm_tree','lm_data');
		$this->lm_tree->setTreeTablePK("lm_id");

		$this->readProperties();
		//parent::read();
	}

	/**
	* get title of content object
	*
	* @return	string		title
	*/
	function getTitle()
	{
		return parent::getTitle();
	}

	/**
	* set title of content object
	*/
	function setTitle($a_title)
	{
		parent::setTitle($a_title);
//		$this->meta_data->setTitle($a_title);
	}

	/**
	* get description of content object
	*
	* @return	string		description
	*/
	function getDescription()
	{
		return parent::getDescription();
	}

	/**
	* set description of content object
	*/
	function setDescription($a_description)
	{
		parent::setDescription($a_description);
//		$this->meta_data->setDescription($a_description);
	}


	function getImportId()
	{
		return $this->import_id;
	}

	function setImportId($a_id)
	{
		$this->import_id = $a_id;
	}

	/**
	* Set layout per page
	*
	* @param	boolean		layout per page
	*/
	function setLayoutPerPage($a_val)
	{
		$this->layout_per_page = $a_val;
	}
	
	/**
	* Get layout per page
	*
	* @return	boolean		layout per page
	*/
	function getLayoutPerPage()
	{
		return $this->layout_per_page;
	}
	
	function &getTree()
	{
		return $this->lm_tree;
	}

	/**
	* update complete object (meta data and properties)
	*/
	function update()
	{
		$this->updateMetaData();
		parent::update();
		$this->updateProperties();
	}


	/**
	* if implemented, this function should be called from an Out/GUI-Object
	*/
	function import()
	{
		// nothing to do. just display the dialogue in Out
		return;
	}


	/**
	* put content object in main tree
	*
	*/
	function putInTree($a_parent)
	{
		global $tree;

		// put this object in tree under $a_parent
		parent::putInTree($a_parent);

		// make new tree for this object
		//$tree->addTree($this->getId());
	}


	/**
	* create content object tree (that stores structure object hierarchie)
	*
	* todo: rename LM to ConOb
	*/
	function createLMTree()
	{
		$this->lm_tree =& new ilTree($this->getId());
		$this->lm_tree->setTreeTablePK("lm_id");
		$this->lm_tree->setTableNames('lm_tree','lm_data');
		$this->lm_tree->addTree($this->getId(), 1);
	}

	/**
	 * Add first chapter and page
	 */
	function addFirstChapterAndPage()
	{
		global $lng;
		
		include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
		include_once("./Modules/LearningModule/classes/class.ilStructureObject.php");
		include_once("./Modules/LearningModule/classes/class.ilLMPageObject.php");
		
		$root_id = $this->lm_tree->getRootId();
		
		// chapter
		$chap = new ilStructureObject($this);
		$chap->setType("st");
		$chap->setTitle($lng->txt("cont_new_chap"));
		$chap->setLMId($this->getId());
		$chap->create();
		ilLMObject::putInTree($chap, $root_id, IL_FIRST_NODE);

		// page
		$page = new ilLMPageObject($this);
		$page->setType("pg");
		$page->setTitle($lng->txt("cont_new_page"));
		$page->setLMId($this->getId());
		$page->create();
		ilLMObject::putInTree($page, $chap->getId(), IL_FIRST_NODE);
	}
	

	/**
	* get content object tree
	*/
	function &getLMTree()
	{
		return $this->lm_tree;
	}


	/**
	* creates data directory for import files
	* (data_dir/lm_data/lm_<id>/import, depending on data
	* directory that is set in ILIAS setup/ini)
	*/
	function createImportDirectory()
	{
		$lm_data_dir = ilUtil::getDataDir()."/lm_data";
		if(!is_writable($lm_data_dir))
		{
			$this->ilias->raiseError("Content object Data Directory (".$lm_data_dir
				.") not writeable.",$this->ilias->error_obj->FATAL);
		}

		// create learning module directory (data_dir/lm_data/lm_<id>)
		$lm_dir = $lm_data_dir."/lm_".$this->getId();
		ilUtil::makeDir($lm_dir);
		if(!@is_dir($lm_dir))
		{
			$this->ilias->raiseError("Creation of Learning Module Directory failed.",$this->ilias->error_obj->FATAL);
		}

		// create import subdirectory (data_dir/lm_data/lm_<id>/import)
		$import_dir = $lm_dir."/import";
		ilUtil::makeDir($import_dir);
		if(!@is_dir($import_dir))
		{
			$this->ilias->raiseError("Creation of Import Directory failed.",$this->ilias->error_obj->FATAL);
		}
	}

	/**
	* get data directory
	*/
	function getDataDirectory()
	{
		return ilUtil::getDataDir()."/lm_data".
			"/lm_".$this->getId();
	}

	/**
	* get import directory of lm
	*/
	function getImportDirectory()
	{
		if(strlen($this->import_dir))
		{
			return $this->import_dir;
		}
		
		$import_dir = ilUtil::getDataDir()."/lm_data".
			"/lm_".$this->getId()."/import";
		if(@is_dir($import_dir))
		{
			return $import_dir;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Set import directory for further use in ilContObjParser
	 * 
	 * @param string import directory
	 * @return void
	 */
	public function setImportDirectory($a_import_dir)
	{
		$this->import_dir = $a_import_dir;
	}


	/**
	* creates data directory for export files
	* (data_dir/lm_data/lm_<id>/export, depending on data
	* directory that is set in ILIAS setup/ini)
	*/
	function createExportDirectory($a_type = "xml")
	{
		$lm_data_dir = ilUtil::getDataDir()."/lm_data";
		if(!is_writable($lm_data_dir))
		{
			$this->ilias->raiseError("Content object Data Directory (".$lm_data_dir
				.") not writeable.",$this->ilias->error_obj->FATAL);
		}
		// create learning module directory (data_dir/lm_data/lm_<id>)
		$lm_dir = $lm_data_dir."/lm_".$this->getId();
		ilUtil::makeDir($lm_dir);
		if(!@is_dir($lm_dir))
		{
			$this->ilias->raiseError("Creation of Learning Module Directory failed.",$this->ilias->error_obj->FATAL);
		}
		// create Export subdirectory (data_dir/lm_data/lm_<id>/Export)
		switch ($a_type)
		{
			// html
			case "html":
				$export_dir = $lm_dir."/export_html";
				break;

			// scorm
			case "scorm":
				$export_dir = $lm_dir."/export_scorm";
				break;

			default:		// = xml
				$export_dir = $lm_dir."/export";
				break;
		}
		ilUtil::makeDir($export_dir);

		if(!@is_dir($export_dir))
		{
			$this->ilias->raiseError("Creation of Export Directory failed.",$this->ilias->error_obj->FATAL);
		}
	}

	/**
	* get export directory of lm
	*/
	function getExportDirectory($a_type = "xml")
	{
		switch  ($a_type)
		{
			case "html":
				$export_dir = ilUtil::getDataDir()."/lm_data"."/lm_".$this->getId()."/export_html";
				break;

			case "scorm":
				$export_dir = ilUtil::getDataDir()."/lm_data"."/lm_".$this->getId()."/export_scorm";
				break;
				
			default:			// = xml
				$export_dir = ilUtil::getDataDir()."/lm_data"."/lm_".$this->getId()."/export";
				break;
		}
		return $export_dir;
	}


	/**
	* delete learning module and all related data
	*
	* this method has been tested on may 9th 2004
	* meta data, content object data, data directory, bib items
	* learning module tree and pages have been deleted correctly as desired
	*
	* @access	public
	* @return	boolean	true if all object data were removed; false if only a references were removed
	*/
	function delete()
	{
		global $ilDB;

		global $ilBench;

		// always call parent delete function first!!
		if (!parent::delete())
		{
			return false;
		}

		// delete lm object data
		include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
		ilLMObject::_deleteAllObjectData($this);

		// delete meta data of content object
		$this->deleteMetaData();

		// delete bibitem data
		$nested = new ilNestedSetXML();
		$nested->init($this->getId(), "bib");
		$nested->deleteAllDBData();


		// delete learning module tree
		$this->lm_tree->removeTree($this->lm_tree->getTreeId());

		// delete data directory
		ilUtil::delDir($this->getDataDirectory());

		// delete content object record
		$q = "DELETE FROM content_object WHERE id = ".
			$ilDB->quote($this->getId(), "integer");
		$ilDB->manipulate($q);

		// delete lm menu entries
		$q = "DELETE FROM lm_menu WHERE lm_id = ".
			$ilDB->quote($this->getId(), "integer");
		$ilDB->manipulate($q);

		return true;
	}


	/**
	* get default page layout of content object (see directory layouts/)
	*
	* @return	string		default layout
	*/
	function getLayout()
	{
		return $this->layout;
	}

	/**
	* set default page layout
	*
	* @param	string		$a_layout		default page layout
	*/
	function setLayout($a_layout)
	{
		$this->layout = $a_layout;
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
	* write ID of assigned style sheet object to db
	*/
	function writeStyleSheetId($a_style_id)
	{
		global $ilDB;

		$q = "UPDATE content_object SET ".
			" stylesheet = ".$ilDB->quote((int) $a_style_id, "integer").
			" WHERE id = ".$ilDB->quote($this->getId(), "integer");
		$ilDB->manipulate($q);

		$this->style_id = $a_style_id;
	}
	
	/**
	* move learning modules from one style to another
	*/
	function _moveLMStyles($a_from_style, $a_to_style)
	{
		global $ilDB, $ilias;

		if ($a_from_style < 0)	// change / delete all individual styles
		{
			$q = "SELECT stylesheet FROM content_object, style_data ".
				" WHERE content_object.stylesheet = style_data.id ".
				" AND style_data.standard = ".$ilDB->quote(0, "integer").
				" AND content_object.stylesheet > ".$ilDB->quote(0, "integer");
			$style_set = $ilDB->query($q);
			while($style_rec = $ilDB->fetchAssoc($style_set))
			{
				// assign learning modules to new style
				$q = "UPDATE content_object SET ".
					" stylesheet = ".$ilDB->quote((int) $a_to_style, "integer").
					" WHERE stylesheet = ".$ilDB->quote($style_rec["stylesheet"], "integer");
				$ilDB->manipulate($q);
				
				// delete style
				$style_obj =& $ilias->obj_factory->getInstanceByObjId($style_rec["stylesheet"]);
				$style_obj->delete();
			}
		}
		else
		{
			$q = "UPDATE content_object SET ".
				" stylesheet = ".$ilDB->quote((int) $a_to_style, "integer").
				" WHERE stylesheet = ".$ilDB->quote($a_from_style, "integer");
			$ilDB->manipulate($q);
		}
	}

	/**
	* lookup style sheet ID
	*/
	function _lookupStyleSheetId($a_cont_obj_id)
	{
		global $ilDB;

		$q = "SELECT stylesheet FROM content_object ".
			" WHERE id = ".$ilDB->quote($a_cont_obj_id, "integer");
		$res = $ilDB->query($q);
		$sheet = $ilDB->fetchAssoc($res);

		return $sheet["stylesheet"];
	}
	
	/**
	* lookup style sheet ID
	*/
	function _lookupContObjIdByStyleId($a_style_id)
	{
		global $ilDB;

		$q = "SELECT id FROM content_object ".
			" WHERE stylesheet = ".$ilDB->quote($a_style_id, "integer");
		$res = $ilDB->query($q);
		$obj_ids = array();
		while($cont = $ilDB->fetchAssoc($res))
		{
			$obj_ids[] = $cont["id"];
		}
		return $obj_ids;
	}
	
	/**
	* gets the number of learning modules assigned to a content style
	*
	* @param	int		$a_style_id		style id
	*/
	function _getNrOfAssignedLMs($a_style_id)
	{
		global $ilDB;
		
		$q = "SELECT count(*) as cnt FROM content_object ".
			" WHERE stylesheet = ".$ilDB->quote($a_style_id, "integer");
		$cset = $ilDB->query($q);
		$crow = $ilDB->fetchAssoc($cset);

		return (int) $crow["cnt"];
	}
	
	
	/**
	* get number of learning modules with individual styles
	*/
	function _getNrLMsIndividualStyles()
	{
		global $ilDB;
		
		// joining with style table (not perfectly nice)
		$q = "SELECT count(*) as cnt FROM content_object, style_data ".
			" WHERE stylesheet = style_data.id ".
			" AND standard = ".$ilDB->quote(0, "integer");
		$cset = $ilDB->query($q);
		$crow = $ilDB->fetchAssoc($cset);

		return (int) $crow["cnt"];
	}

	/**
	* get number of learning modules assigned no style
	*/
	function _getNrLMsNoStyle()
	{
		global $ilDB;
		
		$q = "SELECT count(*) as cnt FROM content_object ".
			" WHERE stylesheet = ".$ilDB->quote(0, "integer");
		$cset = $ilDB->query($q);
		$crow = $ilDB->fetchAssoc($cset);

		return (int) $crow["cnt"];
	}

	/**
	* delete all style references to style
	*
	* @param	int		$a_style_id		style_id
	*/
	function _deleteStyleAssignments($a_style_id)
	{
		global $ilDB;
		
		$q = "UPDATE content_object SET ".
			" stylesheet = ".$ilDB->quote(0, "integer").
			" WHERE stylesheet = ".$ilDB->quote((int) $this->getId($a_style_id), "integer");

		$ilDB->manipulate($q);
	}

	/**
	* get page header mode (IL_CHAPTER_TITLE | IL_PAGE_TITLE | IL_NO_HEADER)
	*/
	function getPageHeader()
	{
		return $this->pg_header;
	}

	/**
	* set page header mode
	*
	* @param string $a_pg_header		IL_CHAPTER_TITLE | IL_PAGE_TITLE | IL_NO_HEADER
	*/
	function setPageHeader($a_pg_header = IL_CHAPTER_TITLE)
	{
		$this->pg_header = $a_pg_header;
	}

	/**
	* get toc mode ("chapters" | "pages")
	*/
	function getTOCMode()
	{
		return $this->toc_mode;
	}
	
	/**
	* get public access mode ("complete" | "selected")
	*/
	function getPublicAccessMode()
	{
		return $this->public_access_mode;
	}

	/**
	* set toc mode
	*
	* @param string $a_toc_mode		"chapters" | "pages"
	*/
	function setTOCMode($a_toc_mode = "chapters")
	{
		$this->toc_mode = $a_toc_mode;
	}

	function setOnline($a_online)
	{
		$this->online = $a_online;
	}

	function getOnline()
	{
		return $this->online;
	}

	function setActiveLMMenu($a_act_lm_menu)
	{
		$this->lm_menu_active = $a_act_lm_menu;
	}

	function isActiveLMMenu()
	{
		return $this->lm_menu_active;
	}

	function setActiveTOC($a_toc)
	{
		$this->toc_active = $a_toc;
	}

	function isActiveTOC()
	{
		return $this->toc_active;
	}

	function setActiveNumbering($a_num)
	{
		$this->numbering = $a_num;
	}

	function isActiveNumbering()
	{
		return $this->numbering;
	}

	function setActivePrintView($a_print)
	{
		$this->print_view_active = $a_print;
	}

	function isActivePrintView()
	{
		return $this->print_view_active;
	}

	function setActivePreventGlossaryAppendix($a_print)
	{
		$this->prevent_glossary_appendix_active = $a_print;
	}
	
	function isActivePreventGlossaryAppendix()
	{
		return $this->prevent_glossary_appendix_active;
	}

	function setActiveDownloads($a_down)
	{
		$this->downloads_active = $a_down;
	}
	
	function isActiveDownloads()
	{
		return $this->downloads_active;
	}
	
	function setActiveDownloadsPublic($a_down)
	{
		$this->downloads_public_active = $a_down;
	}
	
	function isActiveDownloadsPublic()
	{
		return $this->downloads_public_active;
	}

	function setPublicNotes($a_pub_notes)
	{
		$this->pub_notes = $a_pub_notes;
	}

	function publicNotes()
	{
		return $this->pub_notes;
	}
	
	function setCleanFrames($a_clean)
	{
		$this->clean_frames = $a_clean;
	}

	function cleanFrames()
	{
		return $this->clean_frames;
	}
	
	function setHistoryUserComments($a_comm)
	{
		$this->user_comments = $a_comm;
	}

	function setPublicAccessMode($a_mode)
	{
		$this->public_access_mode = $a_mode;
	}

	function isActiveHistoryUserComments()
	{
		return $this->user_comments;
	}

	function setHeaderPage($a_pg)
	{
		$this->header_page = $a_pg;
	}
	
	function getHeaderPage()
	{
		return $this->header_page;
	}
	
	function setFooterPage($a_pg)
	{
		$this->footer_page = $a_pg;
	}
	
	function getFooterPage()
	{
		return $this->footer_page;
	}

	/**
	* read content object properties
	*/
	function readProperties()
	{
		global $ilDB;
		
		$q = "SELECT * FROM content_object WHERE id = ".
			$ilDB->quote($this->getId(), "integer");
		$lm_set = $ilDB->query($q);
		$lm_rec = $ilDB->fetchAssoc($lm_set);
		$this->setLayout($lm_rec["default_layout"]);
		$this->setStyleSheetId((int) $lm_rec["stylesheet"]);
		$this->setPageHeader($lm_rec["page_header"]);
		$this->setTOCMode($lm_rec["toc_mode"]);
		$this->setOnline(ilUtil::yn2tf($lm_rec["is_online"]));
		$this->setActiveTOC(ilUtil::yn2tf($lm_rec["toc_active"]));
		$this->setActiveNumbering(ilUtil::yn2tf($lm_rec["numbering"]));
		$this->setActivePrintView(ilUtil::yn2tf($lm_rec["print_view_active"]));
		$this->setActivePreventGlossaryAppendix(ilUtil::yn2tf($lm_rec["no_glo_appendix"]));
		$this->setActiveDownloads(ilUtil::yn2tf($lm_rec["downloads_active"]));
		$this->setActiveDownloadsPublic(ilUtil::yn2tf($lm_rec["downloads_public_active"]));
		$this->setActiveLMMenu(ilUtil::yn2tf($lm_rec["lm_menu_active"]));
		$this->setCleanFrames(ilUtil::yn2tf($lm_rec["clean_frames"]));
		$this->setPublicNotes(ilUtil::yn2tf($lm_rec["pub_notes"]));
		$this->setHeaderPage((int) $lm_rec["header_page"]);
		$this->setFooterPage((int) $lm_rec["footer_page"]);
		$this->setHistoryUserComments(ilUtil::yn2tf($lm_rec["hist_user_comments"]));
		$this->setPublicAccessMode($lm_rec["public_access_mode"]);
		$this->setPublicExportFile("xml", $lm_rec["public_xml_file"]);
		$this->setPublicExportFile("html", $lm_rec["public_html_file"]);
		$this->setPublicExportFile("scorm", $lm_rec["public_scorm_file"]);
		$this->setLayoutPerPage($lm_rec["layout_per_page"]);
	}

	/**
	* Update content object properties
	*/
	function updateProperties()
	{
		global $ilDB;
		
		// force clean_frames to be set, if layout per page is activated
		if ($this->getLayoutPerPage())
		{
			$this->setCleanFrames(true);
		}
		
		$q = "UPDATE content_object SET ".
			" default_layout = ".$ilDB->quote($this->getLayout(), "text").", ".
			" stylesheet = ".$ilDB->quote($this->getStyleSheetId(), "integer").",".
			" page_header = ".$ilDB->quote($this->getPageHeader(), "text").",".
			" toc_mode = ".$ilDB->quote($this->getTOCMode(), "text").",".
			" is_online = ".$ilDB->quote(ilUtil::tf2yn($this->getOnline()), "text").",".
			" toc_active = ".$ilDB->quote(ilUtil::tf2yn($this->isActiveTOC()), "text").",".
			" numbering = ".$ilDB->quote(ilUtil::tf2yn($this->isActiveNumbering()), "text").",".
			" print_view_active = ".$ilDB->quote(ilUtil::tf2yn($this->isActivePrintView()), "text").",".
			" no_glo_appendix = ".$ilDB->quote(ilUtil::tf2yn($this->isActivePreventGlossaryAppendix()), "text").",".
			" downloads_active = ".$ilDB->quote(ilUtil::tf2yn($this->isActiveDownloads()), "text").",".
			" downloads_public_active = ".$ilDB->quote(ilUtil::tf2yn($this->isActiveDownloadsPublic()), "text").",".
			" clean_frames = ".$ilDB->quote(ilUtil::tf2yn($this->cleanFrames()), "text").",".
			" pub_notes = ".$ilDB->quote(ilUtil::tf2yn($this->publicNotes()), "text").",".
			" hist_user_comments = ".$ilDB->quote(ilUtil::tf2yn($this->isActiveHistoryUserComments()), "text").",".
			" public_access_mode = ".$ilDB->quote($this->getPublicAccessMode(), "text").",".
			" public_xml_file = ".$ilDB->quote($this->getPublicExportFile("xml"), "text").",".
			" public_html_file = ".$ilDB->quote($this->getPublicExportFile("html"), "text").",".
			" public_scorm_file = ".$ilDB->quote($this->getPublicExportFile("scorm"), "text").",".
			" header_page = ".$ilDB->quote($this->getHeaderPage(), "integer").",".
			" footer_page = ".$ilDB->quote($this->getFooterPage(), "integer").",".
			" lm_menu_active = ".$ilDB->quote(ilUtil::tf2yn($this->isActiveLMMenu()), "text").", ".
			" layout_per_page = ".$ilDB->quote($this->getLayoutPerPage(), "integer")." ".
			" WHERE id = ".$ilDB->quote($this->getId(), "integer");
		$ilDB->manipulate($q);
	}

	/**
	* create new properties record
	*/
	function createProperties()
	{
		global $ilDB;
		
		$q = "INSERT INTO content_object (id) VALUES (".$ilDB->quote($this->getId(), "integer").")";
		$ilDB->manipulate($q);
		$this->readProperties();		// to get db default values
	}

	/**
	* check wether content object is online
	*/
	function _lookupOnline($a_id)
	{
		global $ilDB;
		
//echo "class ilObjContentObject::_lookupOnline($a_id) called. Use Access class instead.";

		$q = "SELECT is_online FROM content_object WHERE id = ".$ilDB->quote($a_id, "integer");
		$lm_set = $ilDB->query($q);
		$lm_rec = $ilDB->fetchAssoc($lm_set);

		return ilUtil::yn2tf($lm_rec["is_online"]);
	}

	/**
	* get all available lm layouts
	*
	* static
	*/
	function getAvailableLayouts()
	{
		$dir = opendir("./Modules/LearningModule/layouts/lm");

		$layouts = array();

		while($file = readdir($dir))
		{
			if ($file != "." && $file != ".." && $file != "CVS" && $file != ".svn")
			{
				// directories
				if (@is_dir("./Modules/LearningModule/layouts/lm/".$file))
				{
					$layouts[$file] = $file;
				}
			}
		}
		asort($layouts);
		
		// workaround: fix ordering
		$ret = array(
			'toc2win' => 'toc2win',
			'toc2windyn' => 'toc2windyn',
			'1window' => '1window',
			'2window' => '2window',
			'3window' => '3window',
			'presentation' => 'presentation',
			'fullscreen' => 'fullscreen'
			);
		
		foreach ($layouts as $l)
		{
			if (!in_array($l, $ret))
			{
				$ret[$l] = $l;
			}
		}

		return $ret;
	}

	/**
	* checks wether the preconditions of a page are fulfilled or not
	*/
	function _checkPreconditionsOfPage($cont_ref_id,$cont_obj_id, $page_id)
	{
		global $ilUser,$ilErr;

		$lm_tree = new ilTree($cont_obj_id);
		$lm_tree->setTableNames('lm_tree','lm_data');
		$lm_tree->setTreeTablePK("lm_id");

		if ($lm_tree->isInTree($page_id))
		{
			$path = $lm_tree->getPathFull($page_id, $lm_tree->readRootId());
			foreach ($path as $node)
			{
				if ($node["type"] == "st")
				{
					if (!ilConditionHandler::_checkAllConditionsOfTarget($cont_ref_id,$node["child"], "st"))
					{
						return false;
					}
				}
			}
		}
		
		return true;
	}

	/**
	* gets all missing preconditions of page
	*/
	function _getMissingPreconditionsOfPage($cont_ref_id,$cont_obj_id, $page_id)
	{
		$lm_tree = new ilTree($cont_obj_id);
		$lm_tree->setTableNames('lm_tree','lm_data');
		$lm_tree->setTreeTablePK("lm_id");

		$conds = array();
		if ($lm_tree->isInTree($page_id))
		{
			// get full path of page
			$path = $lm_tree->getPathFull($page_id, $lm_tree->readRootId());
			foreach ($path as $node)
			{
				if ($node["type"] == "st")
				{
					// get all preconditions of upper chapters
					$tconds = ilConditionHandler::_getConditionsOfTarget($cont_ref_id,$node["child"], "st");
					foreach ($tconds as $tcond)
					{
						// store all missing preconditions
						if (!ilConditionHandler::_checkCondition($tcond["id"]))
						{
							$conds[] = $tcond;
						}
					}
				}
			}
		}
		
		return $conds;
	}

	/**
	* get top chapter of page for that any precondition is missing
	*/
	function _getMissingPreconditionsTopChapter($cont_obj_ref_id,$cont_obj_id, $page_id)
	{
		$lm_tree = new ilTree($cont_obj_id);
		$lm_tree->setTableNames('lm_tree','lm_data');
		$lm_tree->setTreeTablePK("lm_id");

		$conds = array();
		if ($lm_tree->isInTree($page_id))
		{
			// get full path of page
			$path = $lm_tree->getPathFull($page_id, $lm_tree->readRootId());
			foreach ($path as $node)
			{
				if ($node["type"] == "st")
				{
					// get all preconditions of upper chapters
					$tconds = ilConditionHandler::_getConditionsOfTarget($cont_obj_ref_id,$node["child"], "st");
					foreach ($tconds as $tcond)
					{
						// look for missing precondition
						if (!ilConditionHandler::_checkCondition($tcond["id"]))
						{
							return $node["child"];
						}
					}
				}
			}
		}
		
		return "";
	}

	/**
	* notifys an object about an event occured
	* Based on the event happend, each object may decide how it reacts.
	*
	* @access	public
	* @param	string	event
	* @param	integer	reference id of object where the event occured
	* @param	array	passes optional paramters if required
	* @return	boolean
	*/
	function notify($a_event,$a_ref_id,$a_parent_non_rbac_id,$a_node_id,$a_params = 0)
	{
		global $tree;
		
		switch ($a_event)
		{
			case "link":

				//var_dump("<pre>",$a_params,"</pre>");
				//echo "Content Object ".$this->getRefId()." triggered by link event. Objects linked into target object ref_id: ".$a_ref_id;
				//exit;
				break;
			
			case "cut":
				
				//echo "Content Object ".$this->getRefId()." triggered by cut event. Objects are removed from target object ref_id: ".$a_ref_id;
				//exit;
				break;
				
			case "copy":
			
				//var_dump("<pre>",$a_params,"</pre>");
				//echo "Content Object ".$this->getRefId()." triggered by copy event. Objects are copied into target object ref_id: ".$a_ref_id;
				//exit;
				break;

			case "paste":
				
				//echo "Content Object ".$this->getRefId()." triggered by paste (cut) event. Objects are pasted into target object ref_id: ".$a_ref_id;
				//exit;
				break;
			
			case "new":
				
				//echo "Content Object ".$this->getRefId()." triggered by paste (new) event. Objects are applied to target object ref_id: ".$a_ref_id;
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
	* checks if page has a successor page
	*/
	function hasSuccessorPage($a_cont_obj_id, $a_page_id)
	{
		$tree = new ilTree($a_cont_obj_id);
		$tree->setTableNames('lm_tree','lm_data');
		$tree->setTreeTablePK("lm_id");
		if ($tree->isInTree($a_page_id))
		{
			$succ = $tree->fetchSuccessorNode($a_page_id, "pg");
			if ($succ > 0)
			{
				return true;
			}
		}
		return false;
	}

	
	function checkTree()
	{
		$tree = new ilTree($this->getId());
		$tree->setTableNames('lm_tree','lm_data');
		$tree->setTreeTablePK("lm_id");
		$tree->checkTree();
		$tree->checkTreeChilds();
//echo "checked";
	}

	/**
	* fix tree
	*/
	function fixTree()
	{
		global $ilDB;

		$tree =& $this->getLMTree();
		
		// check numbering, if errors, renumber
		// it is very important to keep this step before deleting subtrees
		// in the following steps
		$set = $ilDB->query("SELECT DISTINCT l1.lm_id".
			" FROM lm_tree l1".
			" JOIN lm_tree l2 ON ( l1.child = l2.parent".
			" AND l1.lm_id = l2.lm_id )".
			" JOIN lm_data ON ( l1.child = lm_data.obj_id )".
			" WHERE (l2.lft < l1.lft".
			" OR l2.rgt > l1.rgt OR l2.lft > l1.rgt OR l2.rgt < l1.lft)".
			" AND l1.lm_id = ".$ilDB->quote($this->getId(), "integer").
			" ORDER BY lm_data.create_date DESC"
			);
		if ($rec = $ilDB->fetchAssoc($set))
		{
			$tree->renumber();
		}

		// delete subtrees that have no lm_data records
		$nodes = $tree->getSubtree($tree->getNodeData($tree->getRootId()));
		foreach ($nodes as $node)
		{
			$q = "SELECT * FROM lm_data WHERE obj_id = ".
				$ilDB->quote($node["child"], "integer");
			$obj_set = $ilDB->query($q);
			$obj_rec = $ilDB->fetchAssoc($obj_set);
			if (!$obj_rec)
			{
				$node_data = $tree->getNodeData($node["child"]);
				$tree->deleteTree($node_data);
			}
		}

		// delete subtrees that have pages as parent
		$nodes = $tree->getSubtree($tree->getNodeData($tree->getRootId()));
		foreach ($nodes as $node)
		{
			$q = "SELECT * FROM lm_data WHERE obj_id = ".
				$ilDB->quote($node["parent"], "integer");
			$obj_set = $ilDB->query($q);
			$obj_rec = $ilDB->fetchAssoc($obj_set);
			if ($obj_rec["type"] == "pg")
			{
				$node_data = $tree->getNodeData($node["child"]);
				if ($tree->isInTree($node["child"]))
				{
					$tree->deleteTree($node_data);
				}
			}
		}

		// check for multi-references pages or chapters
		// if errors -> create copies of them here
		$set = $ilDB->query("SELECT DISTINCT l1.lm_id".
				" FROM lm_tree l1".
				" JOIN lm_tree l2 ON ( l1.child = l2.child AND l1.lm_id <> l2.lm_id )".
				" JOIN lm_data ON (l1.child = lm_data.obj_id)".
				" WHERE l1.child <> 1".
				" AND l1.lm_id <> lm_data.lm_id".
				" AND l1.lm_id = ".$ilDB->quote($this->getId(), "integer"));
		if ($rec = $ilDB->fetchAssoc($set))
		{
			$set = $ilDB->query("SELECT DISTINCT l1.child ".
				" FROM lm_tree l1".
				" JOIN lm_tree l2 ON ( l1.child = l2.child AND l1.lm_id <> l2.lm_id )".
				" JOIN lm_data ON (l1.child = lm_data.obj_id)".
				" WHERE l1.child <> 1".
				" AND l1.lm_id <> lm_data.lm_id".
				" AND l1.lm_id = ".$ilDB->quote($this->getId(), "integer"));
			include_once("./Modules/LearningModule/classes/class.ilLMObjectFactory.php");
			while ($rec = $ilDB->fetchAssoc($set))
			{
				$cobj = ilLMObjectFactory::getInstance($this, $rec["child"]);

				if (is_object($cobj))
				{
					if ($cobj->getType() == "pg")
					{
						// make a copy of it
						$pg_copy = $cobj->copy($this);
						
						// replace the child in the tree with the copy (id)
						$ilDB->manipulate("UPDATE lm_tree SET ".
							" child = ".$ilDB->quote($pg_copy->getId(), "integer").
							" WHERE child = ".$ilDB->quote($cobj->getId(), "integer").
							" AND lm_id = ".$ilDB->quote($this->getId(), "integer")
							);
					}
					else if ($cobj->getType() == "st")
					{
						// make a copy of it
						$st_copy = $cobj->copy($this);
						
						// replace the child in the tree with the copy (id)
						$ilDB->manipulate("UPDATE lm_tree SET ".
							" child = ".$ilDB->quote($st_copy->getId(), "integer").
							" WHERE child = ".$ilDB->quote($cobj->getId(), "integer").
							" AND lm_id = ".$ilDB->quote($this->getId(), "integer")
							);
						
						// make all childs refer to the copy now
						$ilDB->manipulate("UPDATE lm_tree SET ".
							" parent = ".$ilDB->quote($st_copy->getId(), "integer").
							" WHERE parent = ".$ilDB->quote($cobj->getId(), "integer").
							" AND lm_id = ".$ilDB->quote($this->getId(), "integer")
							);
					}
				}
			}
		}
	}


	/**
	* export object to xml (see ilias_co.dtd)
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportXML(&$a_xml_writer, $a_inst, $a_target_dir, &$expLog)
	{
		global $ilBench;

		$attrs = array();
		switch($this->getType())
		{
			case "lm":
				$attrs["Type"] = "LearningModule";
				break;

			case "dbk":
				$attrs["Type"] = "LibObject";
				break;
		}
		$a_xml_writer->xmlStartTag("ContentObject", $attrs);

		// MetaData
		$this->exportXMLMetaData($a_xml_writer);

		// StructureObjects
//echo "ContObj:".$a_inst.":<br>";
		$expLog->write(date("[y-m-d H:i:s] ")."Start Export Structure Objects");
		$ilBench->start("ContentObjectExport", "exportStructureObjects");
		$this->exportXMLStructureObjects($a_xml_writer, $a_inst, $expLog);
		$ilBench->stop("ContentObjectExport", "exportStructureObjects");
		$expLog->write(date("[y-m-d H:i:s] ")."Finished Export Structure Objects");

		// PageObjects
		$expLog->write(date("[y-m-d H:i:s] ")."Start Export Page Objects");
		$ilBench->start("ContentObjectExport", "exportPageObjects");
		$this->exportXMLPageObjects($a_xml_writer, $a_inst, $expLog);
		$ilBench->stop("ContentObjectExport", "exportPageObjects");
		$expLog->write(date("[y-m-d H:i:s] ")."Finished Export Page Objects");

		// MediaObjects
		$expLog->write(date("[y-m-d H:i:s] ")."Start Export Media Objects");
		$ilBench->start("ContentObjectExport", "exportMediaObjects");
		$this->exportXMLMediaObjects($a_xml_writer, $a_inst, $a_target_dir, $expLog);
		$ilBench->stop("ContentObjectExport", "exportMediaObjects");
		$expLog->write(date("[y-m-d H:i:s] ")."Finished Export Media Objects");

		// FileItems
		$expLog->write(date("[y-m-d H:i:s] ")."Start Export File Items");
		$ilBench->start("ContentObjectExport", "exportFileItems");
		$this->exportFileItems($a_target_dir, $expLog);
		$ilBench->stop("ContentObjectExport", "exportFileItems");
		$expLog->write(date("[y-m-d H:i:s] ")."Finished Export File Items");

		// Questions
		if (count($this->q_ids) > 0)
		{
			$qti_file = fopen($a_target_dir."/qti.xml", "w");
			include_once("./Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php");
			$pool = new ilObjQuestionPool();
			fwrite($qti_file, $pool->toXML($this->q_ids));
			fclose($qti_file);
		}
		
		// To do: implement version selection/detection
		// Properties
		$expLog->write(date("[y-m-d H:i:s] ")."Start Export Properties");
		$this->exportXMLProperties($a_xml_writer, $expLog);
		$expLog->write(date("[y-m-d H:i:s] ")."Finished Export Properties");

		$a_xml_writer->xmlEndTag("ContentObject");
	}

	/**
	* export content objects meta data to xml (see ilias_co.dtd)
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportXMLMetaData(&$a_xml_writer)
	{
		include_once("Services/MetaData/classes/class.ilMD2XML.php");
		$md2xml = new ilMD2XML($this->getId(), 0, $this->getType());
		$md2xml->setExportMode(true);
		$md2xml->startExport();
		$a_xml_writer->appendXML($md2xml->getXML());
	}

	/**
	* export structure objects to xml (see ilias_co.dtd)
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportXMLStructureObjects(&$a_xml_writer, $a_inst, &$expLog)
	{
		include_once './Modules/LearningModule/classes/class.ilStructureObject.php';

		$childs = $this->lm_tree->getChilds($this->lm_tree->getRootId());
		foreach ($childs as $child)
		{
			if($child["type"] != "st")
			{
				continue;
			}

			$structure_obj = new ilStructureObject($this, $child["obj_id"]);
			$structure_obj->exportXML($a_xml_writer, $a_inst, $expLog);
			unset($structure_obj);
		}
	}


	/**
	* export page objects to xml (see ilias_co.dtd)
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportXMLPageObjects(&$a_xml_writer, $a_inst, &$expLog)
	{
		global $ilBench;

		include_once "./Modules/LearningModule/classes/class.ilLMPageObject.php";

		$pages = ilLMPageObject::getPageList($this->getId());
		foreach ($pages as $page)
		{
			if (ilPageObject::_exists($this->getType(), $page["obj_id"]))
			{
				$expLog->write(date("[y-m-d H:i:s] ")."Page Object ".$page["obj_id"]);
	
				// export xml to writer object
				$page_obj = new ilLMPageObject($this, $page["obj_id"]);
				$page_obj->exportXML($a_xml_writer, "normal", $a_inst);
	
				// collect media objects
				$mob_ids = $page_obj->getMediaObjectIDs();
				foreach($mob_ids as $mob_id)
				{
					$this->mob_ids[$mob_id] = $mob_id;
				}
	
				// collect all file items
				$file_ids = $page_obj->getFileItemIds();
				foreach($file_ids as $file_id)
				{
					$this->file_ids[$file_id] = $file_id;
				}

				// collect all questions
				$q_ids = $page_obj->getQuestionIds();
				foreach($q_ids as $q_id)
				{
					$this->q_ids[$q_id] = $q_id;
				}
	
				unset($page_obj);
			}
		}
	}

	/**
	* export media objects to xml (see ilias_co.dtd)
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportXMLMediaObjects(&$a_xml_writer, $a_inst, $a_target_dir, &$expLog)
	{
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");

		$linked_mobs = array();
		
		// mobs directly embedded into pages
		foreach ($this->mob_ids as $mob_id)
		{
			if ($mob_id > 0 && ilObject::_lookupType($mob_id) == "mob")
			{
				$expLog->write(date("[y-m-d H:i:s] ")."Media Object ".$mob_id);
				$media_obj = new ilObjMediaObject($mob_id);
				$media_obj->exportXML($a_xml_writer, $a_inst);
				$media_obj->exportFiles($a_target_dir);
				
				$lmobs = $media_obj->getLinkedMediaObjects($this->mob_ids);
				$linked_mobs = array_merge($linked_mobs, $lmobs);
				
				unset($media_obj);
			}
		}

		// linked mobs (in map areas)
		foreach ($linked_mobs as $mob_id)
		{
			if ($mob_id > 0)
			{
				$expLog->write(date("[y-m-d H:i:s] ")."Media Object ".$mob_id);
				$media_obj = new ilObjMediaObject($mob_id);
				$media_obj->exportXML($a_xml_writer, $a_inst);
				$media_obj->exportFiles($a_target_dir);
				unset($media_obj);
			}
		}

	}

	/**
	* export files of file itmes
	*
	*/
	function exportFileItems($a_target_dir, &$expLog)
	{
		include_once("./Modules/File/classes/class.ilObjFile.php");

		foreach ($this->file_ids as $file_id)
		{
			$expLog->write(date("[y-m-d H:i:s] ")."File Item ".$file_id);
			$file_obj = new ilObjFile($file_id, false);
			$file_obj->export($a_target_dir);
			unset($file_obj);
		}
	}

	/**
	* export properties of content object
	*
	*/
	function exportXMLProperties($a_xml_writer, &$expLog)
	{
		$attrs = array();
		$a_xml_writer->xmlStartTag("Properties", $attrs);

		// Layout
		$attrs = array("Name" => "Layout", "Value" => $this->getLayout());
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// Page Header
		$attrs = array("Name" => "PageHeader", "Value" => $this->getPageHeader());
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// TOC Mode
		$attrs = array("Name" => "TOCMode", "Value" => $this->getTOCMode());
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// LM Menu Activation
		$attrs = array("Name" => "ActiveLMMenu", "Value" =>
			ilUtil::tf2yn($this->isActiveLMMenu()));
		$a_xml_writer->xmlElement("Property", $attrs);

		// Numbering Activation
		$attrs = array("Name" => "ActiveNumbering", "Value" =>
			ilUtil::tf2yn($this->isActiveNumbering()));
		$a_xml_writer->xmlElement("Property", $attrs);

		// Table of contents button activation
		$attrs = array("Name" => "ActiveTOC", "Value" =>
			ilUtil::tf2yn($this->isActiveTOC()));
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// Print view button activation
		$attrs = array("Name" => "ActivePrintView", "Value" =>
			ilUtil::tf2yn($this->isActivePrintView()));
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// Note that download button is not saved, because
		// download files do not exist after import

		// Clean frames
		$attrs = array("Name" => "CleanFrames", "Value" =>
			ilUtil::tf2yn($this->cleanFrames()));
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// Public notes activation
		$attrs = array("Name" => "PublicNotes", "Value" =>
			ilUtil::tf2yn($this->publicNotes()));
		$a_xml_writer->xmlElement("Property", $attrs);
		
		// History comments for authors activation
		$attrs = array("Name" => "HistoryUserComments", "Value" =>
			ilUtil::tf2yn($this->isActiveHistoryUserComments()));
		$a_xml_writer->xmlElement("Property", $attrs);

		// Header Page
		if ($this->getHeaderPage() > 0)
		{
			$attrs = array("Name" => "HeaderPage", "Value" =>
				"il_".IL_INST_ID."_pg_".$this->getHeaderPage());
			$a_xml_writer->xmlElement("Property", $attrs);
		}

		// Footer Page
		if ($this->getFooterPage() > 0)
		{
			$attrs = array("Name" => "FooterPage", "Value" =>
				"il_".IL_INST_ID."_pg_".$this->getFooterPage());
			$a_xml_writer->xmlElement("Property", $attrs);
		}

		$a_xml_writer->xmlEndTag("Properties");
	}

	/**
	* get export files
	*/
	function getExportFiles()
	{
		$file = array();
		
		$types = array("xml", "html", "scorm");
		
		foreach ($types as $type)
		{
			$dir = $this->getExportDirectory($type);
			// quit if import dir not available
			if (!@is_dir($dir) or
				!is_writeable($dir))
			{
				continue;
			}
	
			// open directory
			$cdir = dir($dir);
	
			// initialize array
	
			// get files and save the in the array
			while ($entry = $cdir->read())
			{
				if ($entry != "." and
					$entry != ".." and
					substr($entry, -4) == ".zip" and
					ereg("^[0-9]{10}_{2}[0-9]+_{2}(lm_)*[0-9]+\.zip\$", $entry))
				{
					$file[$entry.$type] = array("type" => $type, "file" => $entry,
						"size" => filesize($dir."/".$entry));
				}
			}
	
			// close import directory
			$cdir->close();
		}

		// sort files
		ksort ($file);
		reset ($file);
		return $file;
	}
	
	/**
	* specify public export file for type
	*
	* @param	string		$a_type		type ("xml" / "html")
	* @param	string		$a_file		file name
	*/
	function setPublicExportFile($a_type, $a_file)
	{
		$this->public_export_file[$a_type] = $a_file;
	}

	/**
	* get public export file
	*
	* @param	string		$a_type		type ("xml" / "html")
	*
	* @return	string		$a_file		file name	
	*/
	function getPublicExportFile($a_type)
	{
		return $this->public_export_file[$a_type];
	}
	
	/**
	* get offline files
	*/
	function getOfflineFiles($dir)
	{
		// quit if offline dir not available
		if (!@is_dir($dir) or
			!is_writeable($dir))
		{
			return array();
		}

		// open directory
		$dir = dir($dir);

		// initialize array
		$file = array();

		// get files and save the in the array
		while ($entry = $dir->read())
		{
			if ($entry != "." and
				$entry != ".." and
				substr($entry, -4) == ".pdf" and
				ereg("^[0-9]{10}_{2}[0-9]+_{2}(lm_)*[0-9]+\.pdf\$", $entry))
			{
				$file[] = $entry;
			}
		}

		// close import directory
		$dir->close();

		// sort files
		sort ($file);
		reset ($file);

		return $file;
	}
	
	/**
	* export scorm package
	*/
	function exportSCORM($a_target_dir, $log)
	{
		ilUtil::delDir($a_target_dir);
		ilUtil::makeDir($a_target_dir);
		//ilUtil::makeDir($a_target_dir."/res");
		
		// export everything to html
		$this->exportHTML($a_target_dir."/res", $log, false, "scorm");
		
		// build manifest file
		include("./Modules/LearningModule/classes/class.ilContObjectManifestBuilder.php");
		$man_builder = new ilContObjectManifestBuilder($this);
		$man_builder->buildManifest();
		$man_builder->dump($a_target_dir);
		
		// copy scorm 1.2 schema definitions
		copy("Modules/LearningModule/scorm_xsd/adlcp_rootv1p2.xsd", $a_target_dir."/adlcp_rootv1p2.xsd");
		copy("Modules/LearningModule/scorm_xsd/imscp_rootv1p1p2.xsd", $a_target_dir."/imscp_rootv1p1p2.xsd");
		copy("Modules/LearningModule/scorm_xsd/imsmd_rootv1p2p1.xsd", $a_target_dir."/imsmd_rootv1p2p1.xsd");
		copy("Modules/LearningModule/scorm_xsd/ims_xml.xsd", $a_target_dir."/ims_xml.xsd");

		// zip it all
		$date = time();
		$zip_file = $a_target_dir."/".$date."__".IL_INST_ID."__".
			$this->getType()."_".$this->getId().".zip";
	//echo "zip-".$a_target_dir."-to-".$zip_file;
		ilUtil::zip(array($a_target_dir."/res",
			$a_target_dir."/imsmanifest.xml",
			$a_target_dir."/adlcp_rootv1p2.xsd",
			$a_target_dir."/imscp_rootv1p1p2.xsd",
			$a_target_dir."/ims_xml.xsd",
			$a_target_dir."/imsmd_rootv1p2p1.xsd")
			, $zip_file);

		$dest_file = $this->getExportDirectory("scorm")."/".$date."__".IL_INST_ID."__".
			$this->getType()."_".$this->getId().".zip";
		
		rename($zip_file, $dest_file);
		ilUtil::delDir($a_target_dir);

	}

	
	/**
	* export html package
	*/
	function exportHTML($a_target_dir, $log, $a_zip_file = true, $a_export_format = "html")
	{
		global $tpl, $ilBench, $ilLocator, $ilUser;

		// initialize temporary target directory
		ilUtil::delDir($a_target_dir);
		ilUtil::makeDir($a_target_dir);
		$mob_dir = $a_target_dir."/mobs";
		ilUtil::makeDir($mob_dir);
		$file_dir = $a_target_dir."/files";
		ilUtil::makeDir($file_dir);
		$teximg_dir = $a_target_dir."/teximg";
		ilUtil::makeDir($teximg_dir);
		$style_dir = $a_target_dir."/style";
		ilUtil::makeDir($style_dir);
		$style_img_dir = $a_target_dir."/style/images";
		ilUtil::makeDir($style_img_dir);
		$content_style_dir = $a_target_dir."/content_style";
		ilUtil::makeDir($content_style_dir);
		$content_style_img_dir = $a_target_dir."/content_style/images";
		ilUtil::makeDir($content_style_img_dir);
		$GLOBALS["teximgcnt"] = 0;

		// export system style sheet
		$location_stylesheet = ilUtil::getStyleSheetLocation("filesystem");
		$style_name = $ilUser->prefs["style"].".css";
		copy($location_stylesheet, $style_dir."/".$style_name);
		$fh = fopen($location_stylesheet, "r");
		$css = fread($fh, filesize($location_stylesheet));
		preg_match_all("/url\(([^\)]*)\)/",$css,$files);
		foreach (array_unique($files[1]) as $fileref)
		{
			$fileref = dirname($location_stylesheet)."/".$fileref;
			if (is_file($fileref))
			{
				copy($fileref, $style_img_dir."/".basename($fileref));
			}
		}
		fclose($fh);
		$location_stylesheet = ilUtil::getStyleSheetLocation();
		
		// export content style sheet
		$ilBench->start("ExportHTML", "exportContentStyle");
		if ($this->getStyleSheetId() < 1)
		{
			$cont_stylesheet = "./Services/COPage/css/content.css";
			
			$css = fread(fopen($cont_stylesheet,'r'),filesize($cont_stylesheet));
			preg_match_all("/url\(([^\)]*)\)/",$css,$files);
			foreach (array_unique($files[1]) as $fileref)
			{
				if (is_file(str_replace("..", ".", $fileref)))
				{
					copy(str_replace("..", ".", $fileref), $content_style_img_dir."/".basename($fileref));
				}
				$css = str_replace($fileref, "images/".basename($fileref),$css);
			}	
			fwrite(fopen($content_style_dir."/content.css",'w'),$css);
		}
		else
		{
			$style = new ilObjStyleSheet($this->getStyleSheetId());
			$style->writeCSSFile($content_style_dir."/content.css", "images");
			$style->copyImagesToDir($content_style_img_dir);
		}
		$ilBench->stop("ExportHTML", "exportContentStyle");
		
		// export syntax highlighting style
		$syn_stylesheet = ilObjStyleSheet::getSyntaxStylePath();
		copy($syn_stylesheet, $a_target_dir."/syntaxhighlight.css");

		// get learning module presentation gui class
		include_once("./Modules/LearningModule/classes/class.ilLMPresentationGUI.php");
		$_GET["cmd"] = "nop";
		$lm_gui =& new ilLMPresentationGUI();
		$lm_gui->setOfflineMode(true);
		$lm_gui->setOfflineDirectory($a_target_dir);
		$lm_gui->setExportFormat($a_export_format);

		// export pages
		$ilBench->start("ExportHTML", "exportHTMLPages");
		$this->exportHTMLPages($lm_gui, $a_target_dir);
		$ilBench->stop("ExportHTML", "exportHTMLPages");

		// export glossary terms
		$ilBench->start("ExportHTML", "exportHTMLGlossaryTerms");
		$this->exportHTMLGlossaryTerms($lm_gui, $a_target_dir);
		$ilBench->stop("ExportHTML", "exportHTMLGlossaryTerms");

		// export all media objects
		$ilBench->start("ExportHTML", "exportHTMLMediaObjects");
		$linked_mobs = array();
		foreach ($this->offline_mobs as $mob)
		{
			if (ilObject::_exists($mob) && ilObject::_lookupType($mob) == "mob")
			{
				$this->exportHTMLMOB($a_target_dir, $lm_gui, $mob, "_blank", $linked_mobs);
			}
		}
		$linked_mobs2 = array();				// mobs linked in link areas
		foreach ($linked_mobs as $mob)
		{
			if (ilObject::_exists($mob))
			{
				$this->exportHTMLMOB($a_target_dir, $lm_gui, $mob, "_blank", $linked_mobs2);
			}
		}
		$_GET["obj_type"]  = "MediaObject";
		$_GET["obj_id"]  = $a_mob_id;
		$_GET["cmd"] = "";
		$ilBench->stop("ExportHTML", "exportHTMLMediaObjects");

		// export all file objects
		$ilBench->start("ExportHTML", "exportHTMLFileObjects");
		foreach ($this->offline_files as $file)
		{
			$this->exportHTMLFile($a_target_dir, $file);
		}
		$ilBench->stop("ExportHTML", "exportHTMLFileObjects");

		// export table of contents
		$ilBench->start("ExportHTML", "exportHTMLTOC");
		$ilLocator->clearItems();
		if ($this->isActiveTOC())
		{
			$tpl = new ilTemplate("tpl.main.html", true, true);
			//$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");
			$content =& $lm_gui->showTableOfContents();
			$file = $a_target_dir."/table_of_contents.html";
				
			// open file
			if (!($fp = @fopen($file,"w+")))
			{
				die ("<b>Error</b>: Could not open \"".$file."\" for writing".
					" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
			}
			chmod($file, 0770);
			fwrite($fp, $content);
			fclose($fp);
		}
		$ilBench->stop("ExportHTML", "exportHTMLTOC");

		// export images
		$ilBench->start("ExportHTML", "exportHTMLImages");
		$image_dir = $a_target_dir."/images";
		ilUtil::makeDir($image_dir);
		ilUtil::makeDir($image_dir."/browser");
		copy(ilUtil::getImagePath("enlarge.png", false, "filesystem"),
			$image_dir."/enlarge.png");
		copy(ilUtil::getImagePath("browser/blank.png", false, "filesystem"),
			$image_dir."/browser/plus.png");
		copy(ilUtil::getImagePath("browser/blank.png", false, "filesystem"),
			$image_dir."/browser/minus.png");
		copy(ilUtil::getImagePath("browser/blank.png", false, "filesystem"),
			$image_dir."/browser/blank.png");
		copy(ilUtil::getImagePath("spacer.png", false, "filesystem"),
			$image_dir."/spacer.png");
		copy(ilUtil::getImagePath("icon_st.png", false, "filesystem"),
			$image_dir."/icon_st.png");
		copy(ilUtil::getImagePath("icon_pg.png", false, "filesystem"),
			$image_dir."/icon_pg.png");
		copy(ilUtil::getImagePath("icon_st_s.png", false, "filesystem"),
			$image_dir."/icon_st_s.png");
		copy(ilUtil::getImagePath("icon_pg_s.png", false, "filesystem"),
			$image_dir."/icon_pg_s.png");
		copy(ilUtil::getImagePath("icon_lm.png", false, "filesystem"),
			$image_dir."/icon_lm.png");
		copy(ilUtil::getImagePath("icon_lm_s.png", false, "filesystem"),
			$image_dir."/icon_lm_s.png");
		copy(ilUtil::getImagePath("nav_arr_L.png", false, "filesystem"),
			$image_dir."/nav_arr_L.png");
		copy(ilUtil::getImagePath("nav_arr_R.png", false, "filesystem"),
			$image_dir."/nav_arr_R.png");
		copy(ilUtil::getImagePath("browser/forceexp.png", false, "filesystem"),
			$image_dir."/browser/forceexp.png");
			
		copy(ilUtil::getImagePath("download.png", false, "filesystem"),
			$image_dir."/download.png");
		$ilBench->stop("ExportHTML", "exportHTMLImages");

		// export flv/mp3 player
		$services_dir = $a_target_dir."/Services";
		ilUtil::makeDir($services_dir);
		$media_service_dir = $services_dir."/MediaObjects";
		ilUtil::makeDir($media_service_dir);
		include_once("./Services/MediaObjects/classes/class.ilPlayerUtil.php");
		$flv_dir = $a_target_dir."/".ilPlayerUtil::getFlashVideoPlayerDirectory();
		ilUtil::makeDir($flv_dir);
		$mp3_dir = $media_service_dir."/flash_mp3_player";
		ilUtil::makeDir($mp3_dir);
//		copy(ilPlayerUtil::getFlashVideoPlayerFilename(true),
//			$flv_dir."/".ilPlayerUtil::getFlashVideoPlayerFilename());
		ilPlayerUtil::copyPlayerFilesToTargetDirectory($flv_dir);
		copy("./Services/MediaObjects/flash_mp3_player/mp3player.swf",
			$mp3_dir."/mp3player.swf");

		// js files
		ilUtil::makeDir($a_target_dir.'/js');
		ilUtil::makeDir($a_target_dir.'/js/yahoo');
		ilUtil::makeDir($a_target_dir.'/css');
		include_once("./Services/YUI/classes/class.ilYuiUtil.php");
		foreach (self::getSupplyingExportFiles($a_target_dir) as $f)
		{
			if ($f["source"] != "")
			{
				copy($f["source"], $f["target"]);
			}
		}
		
		// template workaround: reset of template 
		$tpl = new ilTemplate("tpl.main.html", true, true);
		$tpl->setVariable("LOCATION_STYLESHEET",$location_stylesheet);
		$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");

		// zip everything
		$ilBench->start("ExportHTML", "zip");
		if (true)
		{
			if ($a_zip_file)
			{
				// zip it all
				$date = time();
				$zip_file = $this->getExportDirectory("html")."/".$date."__".IL_INST_ID."__".
					$this->getType()."_".$this->getId().".zip";
				ilUtil::zip($a_target_dir, $zip_file);
				ilUtil::delDir($a_target_dir);
			}
		}
		$ilBench->stop("ExportHTML", "zip");

	}

	/**
	 * Get supplying export files
	 *
	 * @param
	 * @return
	 */
	static function getSupplyingExportFiles($a_target_dir = ".")
	{
		include_once("./Services/YUI/classes/class.ilYuiUtil.php");
		include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
		include_once("./Services/MediaObjects/classes/class.ilPlayerUtil.php");
		$scripts = array(
			array("source" => ilYuiUtil::getLocalPath('yahoo/yahoo-min.js'),
				"target" => $a_target_dir.'/js/yahoo/yahoo-min.js',
				"type" => "js"),
			array("source" => ilYuiUtil::getLocalPath('yahoo-dom-event/yahoo-dom-event.js'),
				"target" => $a_target_dir.'/js/yahoo/yahoo-dom-event.js',
				"type" => "js"),
			array("source" => ilYuiUtil::getLocalPath('animation/animation-min.js'),
				"target" => $a_target_dir.'/js/yahoo/animation-min.js',
				"type" => "js"),
			array("source" => './Services/JavaScript/js/Basic.js',
				"target" => $a_target_dir.'/js/Basic.js',
				"type" => "js"),
			array("source" => './Services/Accordion/js/accordion.js',
				"target" => $a_target_dir.'/js/accordion.js',
				"type" => "js"),
			array("source" => './Services/Accordion/css/accordion.css',
				"target" => $a_target_dir.'/css/accordion.css',
				"type" => "css"),
			array("source" => iljQueryUtil::getLocaljQueryPath(),
				"target" => $a_target_dir.'/js/jquery.js',
				"type" => "js"),
			array("source" => iljQueryUtil::getLocalMaphilightPath(),
				"target" => $a_target_dir.'/js/maphilight.js',
				"type" => "js"),
			array("source" => iljQueryUtil::getLocaljQueryUIPath(),
				"target" => $a_target_dir.'/js/jquery-ui-min.js',
				"type" => "js"),
			array("source" => './Services/COPage/js/ilCOPagePres.js',
				"target" => $a_target_dir.'/js/ilCOPagePres.js',
				"type" => "js"),
			array("source" => './Modules/Scorm2004/scripts/questions/pure.js',
				"target" => $a_target_dir.'/js/pure.js',
				"type" => "js"),
			array("source" => './Modules/Scorm2004/scripts/questions/question_handling.js',
				"target" => $a_target_dir.'/js/question_handling.js',
				"type" => "js"),
			array("source" => './Modules/Scorm2004/templates/default/question_handling.css',
				"target" => $a_target_dir.'/css/question_handling.css',
				"type" => "css"),
			array("source" => ilPlayerUtil::getLocalMediaElementJsPath(),
				"target" => $a_target_dir."/".ilPlayerUtil::getLocalMediaElementJsPath(),
				"type" => "js"),
			array("source" => ilPlayerUtil::getLocalMediaElementCssPath(),
				"target" => $a_target_dir."/".ilPlayerUtil::getLocalMediaElementCssPath(),
				"type" => "css")
		);
		
		$mathJaxSetting = new ilSetting("MathJax");
		$use_mathjax = $mathJaxSetting->get("enable");
		if ($use_mathjax)
		{
			$scripts[] = array("source" => "",
				"target" => $mathJaxSetting->get("path_to_mathjax"),
				"type" => "js");
		}
		
		return $scripts;

	}
	
	/**
	* export file object
	*/
	function exportHTMLFile($a_target_dir, $a_file_id)
	{
		$file_dir = $a_target_dir."/files/file_".$a_file_id;
		ilUtil::makeDir($file_dir);
		include_once("./Modules/File/classes/class.ilObjFile.php");
		$file_obj = new ilObjFile($a_file_id, false);
		$source_file = $file_obj->getDirectory($file_obj->getVersion())."/".$file_obj->getFileName();
		if (!is_file($source_file))
		{
			$source_file = $file_obj->getDirectory()."/".$file_obj->getFileName();
		}
		if (is_file($source_file))
		{
			copy($source_file, $file_dir."/".$file_obj->getFileName());
		}
	}

	/**
	* export media object to html
	*/
	function exportHTMLMOB($a_target_dir, &$a_lm_gui, $a_mob_id, $a_frame, &$a_linked_mobs)
	{
		global $tpl;

		$mob_dir = $a_target_dir."/mobs";

		$source_dir = ilUtil::getWebspaceDir()."/mobs/mm_".$a_mob_id;
		if (@is_dir($source_dir))
		{
			ilUtil::makeDir($mob_dir."/mm_".$a_mob_id);
			ilUtil::rCopy($source_dir, $mob_dir."/mm_".$a_mob_id);
		}
		
		$tpl = new ilTemplate("tpl.main.html", true, true);
		$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");
		$_GET["obj_type"]  = "MediaObject";
		$_GET["mob_id"]  = $a_mob_id;
		$_GET["frame"] = $a_frame;
		$_GET["cmd"] = "";
		$content =& $a_lm_gui->media();
		$file = $a_target_dir."/media_".$a_mob_id.".html";

		// open file
		if (!($fp = @fopen($file,"w+")))
		{
			die ("<b>Error</b>: Could not open \"".$file."\" for writing".
				" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
		}
		chmod($file, 0770);
		fwrite($fp, $content);
		fclose($fp);
		
		// fullscreen
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mob_obj = new ilObjMediaObject($a_mob_id);
		if ($mob_obj->hasFullscreenItem())
		{
			$tpl = new ilTemplate("tpl.main.html", true, true);
			$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");
			$_GET["obj_type"]  = "";
			$_GET["frame"]  = "";
			$_GET["mob_id"]  = $a_mob_id;
			$_GET["cmd"] = "fullscreen";
			$content =& $a_lm_gui->fullscreen();
			$file = $a_target_dir."/fullscreen_".$a_mob_id.".html";
	
			// open file
			if (!($fp = @fopen($file,"w+")))
			{
				die ("<b>Error</b>: Could not open \"".$file."\" for writing".
					" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
			}
			chmod($file, 0770);
			fwrite($fp, $content);
			fclose($fp);
		}
		$linked_mobs = $mob_obj->getLinkedMediaObjects();
		$a_linked_mobs = array_merge($a_linked_mobs, $linked_mobs);
	}
	
	/**
	* export glossary terms
	*/
	function exportHTMLGlossaryTerms(&$a_lm_gui, $a_target_dir)
	{
		global $ilLocator;
		
		foreach($this->offline_int_links as $int_link)
		{
			$ilLocator->clearItems();
			if ($int_link["type"] == "git")
			{
				$tpl = new ilTemplate("tpl.main.html", true, true);
				$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");

				$_GET["obj_id"] = $int_link["id"];
				$_GET["frame"] = "_blank";
				$content =& $a_lm_gui->glossary();
				$file = $a_target_dir."/term_".$int_link["id"].".html";
					
				// open file
				if (!($fp = @fopen($file,"w+")))
				{
					die ("<b>Error</b>: Could not open \"".$file."\" for writing".
							" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
				}
				chmod($file, 0770);
				fwrite($fp, $content);
				fclose($fp);

				// store linked/embedded media objects of glosssary term
				include_once("./Modules/Glossary/classes/class.ilGlossaryDefinition.php");
				$defs = ilGlossaryDefinition::getDefinitionList($int_link["id"]);
				foreach($defs as $def)
				{
					$def_mobs = ilObjMediaObject::_getMobsOfObject("gdf:pg", $def["id"]);
					foreach($def_mobs as $def_mob)
					{
						$this->offline_mobs[$def_mob] = $def_mob;
					}
					
					// get all files of page
					$def_files = ilObjFile::_getFilesOfObject("gdf:pg", $page["obj_id"]);
					$this->offline_files = array_merge($this->offline_files, $def_files);

				}
				
			}
		}
	}
	
	/**
	* export all pages of learning module to html file
	*/
	function exportHTMLPages(&$a_lm_gui, $a_target_dir)
	{
		global $tpl, $ilBench, $ilLocator;
				
		$pages = ilLMPageObject::getPageList($this->getId());
		
		$lm_tree =& $this->getLMTree();
		$first_page = $lm_tree->fetchSuccessorNode($lm_tree->getRootId(), "pg");
		$this->first_page_id = $first_page["child"];

		// iterate all learning module pages
		$mobs = array();
		$int_links = array();
		$this->offline_files = array();

		include_once("./Services/COPage/classes/class.ilPageContentUsage.php");
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");

		// get html export id mapping
		$lm_set = new ilSetting("lm");
		$exp_id_map = array();

		if ($lm_set->get("html_export_ids"))
		{
			foreach ($pages as $page)
			{
				$exp_id = ilLMPageObject::getExportId($this->getId(), $page["obj_id"]);
				if (trim($exp_id) != "")
				{
					$exp_id_map[$page["obj_id"]] = trim($exp_id);
				}
			}
		}
//exit;
		reset($pages);
		foreach ($pages as $page)
		{
			if (ilPageObject::_exists($this->getType(), $page["obj_id"]))
			{
				$ilLocator->clearItems();
				$ilBench->start("ExportHTML", "exportHTMLPage");
				$ilBench->start("ExportHTML", "exportPageHTML");
				$this->exportPageHTML($a_lm_gui, $a_target_dir, $page["obj_id"],
					"", $exp_id_map);
				$ilBench->stop("ExportHTML", "exportPageHTML");

				// get all snippets of page
				$pcs = ilPageContentUsage::getUsagesOfPage($page["obj_id"], $this->getType().":pg");
				foreach ($pcs as $pc)
				{
					if ($pc["type"] == "incl")
					{
						$incl_mobs = ilObjMediaObject::_getMobsOfObject("mep:pg", $pc["id"]);
						foreach($incl_mobs as $incl_mob)
						{
							$mobs[$incl_mob] = $incl_mob;
						}
					}
				}

				// get all media objects of page
				$pg_mobs = ilObjMediaObject::_getMobsOfObject($this->getType().":pg", $page["obj_id"]);
				foreach($pg_mobs as $pg_mob)
				{
					$mobs[$pg_mob] = $pg_mob;
				}
				
				// get all internal links of page
				$pg_links = ilInternalLink::_getTargetsOfSource($this->getType().":pg", $page["obj_id"]);
				$int_links = array_merge($int_links, $pg_links);
				
				// get all files of page
				include_once("./Modules/File/classes/class.ilObjFile.php");
				$pg_files = ilObjFile::_getFilesOfObject($this->getType().":pg", $page["obj_id"]);
				$this->offline_files = array_merge($this->offline_files, $pg_files);
				
				$ilBench->stop("ExportHTML", "exportHTMLPage");
			}
		}
		$this->offline_mobs = $mobs;
		$this->offline_int_links = $int_links;
		
		
	}



	/**
	* export page html
	*/
	function exportPageHTML(&$a_lm_gui, $a_target_dir, $a_lm_page_id, $a_frame = "",
		$a_exp_id_map = array())
	{
		global $tpl, $ilBench;
		
//echo "<br>B: export Page HTML ($a_lm_page_id)"; flush();
		// template workaround: reset of template 
		$tpl = new ilTemplate("tpl.main.html", true, true);
		$tpl->addBlockFile("CONTENT", "content", "tpl.adm_content.html");

		$_GET["obj_id"] = $a_lm_page_id;
		$_GET["frame"] = $a_frame;

		if ($a_frame == "")
		{
			//if ($nid = ilLMObject::_lookupNID($a_lm_gui->lm->getId(), $a_lm_page_id, "pg"))
			if (is_array($a_exp_id_map) && isset($a_exp_id_map[$a_lm_page_id]))
			{
				$file = $a_target_dir."/lm_pg_".$a_exp_id_map[$a_lm_page_id].".html";
			}
			else
			{
				$file = $a_target_dir."/lm_pg_".$a_lm_page_id.".html";
			}
		}
		else
		{
			if ($a_frame != "toc")
			{
				$file = $a_target_dir."/frame_".$a_lm_page_id."_".$a_frame.".html";
			}
			else
			{
				$file = $a_target_dir."/frame_".$a_frame.".html";
			}
		}
		
		// return if file is already existing
		if (@is_file($file))
		{
			return;
		}

		$ilBench->start("ExportHTML", "layout");
		$ilBench->start("ExportHTML", "layout_".$a_frame);
		$content =& $a_lm_gui->layout("main.xml", false);
		$ilBench->stop("ExportHTML", "layout_".$a_frame);
		$ilBench->stop("ExportHTML", "layout");

		// open file
		if (!($fp = @fopen($file,"w+")))
		{
			die ("<b>Error</b>: Could not open \"".$file."\" for writing".
					" in <b>".__FILE__."</b> on line <b>".__LINE__."</b><br />");
		}
	
		// set file permissions
		chmod($file, 0770);
			
		// write xml data into the file
		fwrite($fp, $content);
		
		// close file
		fclose($fp);

		if ($this->first_page_id == $a_lm_page_id && $a_frame == "")
		{
			copy($file, $a_target_dir."/index.html");
		}

		// write frames of frameset
		$ilBench->start("ExportHTML", "getCurrentFrameSet");
		$frameset = $a_lm_gui->getCurrentFrameSet();
		$ilBench->stop("ExportHTML", "getCurrentFrameSet");
		
		foreach ($frameset as $frame)
		{				
			$this->exportPageHTML($a_lm_gui, $a_target_dir, $a_lm_page_id, $frame);
		}

	}

	/**
	* export object to fo
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportFO(&$a_xml_writer, $a_target_dir)
	{
		global $ilBench;

		// fo:root (start)
		$attrs = array();
		$attrs["xmlns:fo"] = "http://www.w3.org/1999/XSL/Format";
		$a_xml_writer->xmlStartTag("fo:root", $attrs);

		// fo:layout-master-set (start)
		$attrs = array();
		$a_xml_writer->xmlStartTag("fo:layout-master-set", $attrs);

		// fo:simple-page-master (start)
		$attrs = array();
		$attrs["master-name"] = "DinA4";
		$attrs["page-height"] = "29.7cm";
		$attrs["page-width"] = "21cm";
		$attrs["margin-top"] = "4cm";
		$attrs["margin-bottom"] = "1cm";
		$attrs["margin-left"] = "2.8cm";
		$attrs["margin-right"] = "7.3cm";
		$a_xml_writer->xmlStartTag("fo:simple-page-master", $attrs);

		// fo:region-body (complete)
		$attrs = array();
		$attrs["margin-top"] = "0cm";
		$attrs["margin-bottom"] = "1.25cm";
		$a_xml_writer->xmlElement("fo:region-body", $attrs);

		// fo:region-before (complete)
		$attrs = array();
		$attrs["extent"] = "1cm";
		$a_xml_writer->xmlElement("fo:region-before", $attrs);

		// fo:region-after (complete)
		$attrs = array();
		$attrs["extent"] = "1cm";
		$a_xml_writer->xmlElement("fo:region-after", $attrs);

		// fo:simple-page-master (end)
		$a_xml_writer->xmlEndTag("fo:simple-page-master");

		// fo:layout-master-set (end)
		$a_xml_writer->xmlEndTag("fo:layout-master-set");

		// fo:page-sequence (start)
		$attrs = array();
		$attrs["master-reference"] = "DinA4";
		$a_xml_writer->xmlStartTag("fo:page-sequence", $attrs);

		// fo:flow (start)
		$attrs = array();
		$attrs["flow-name"] = "xsl-region-body";
		$a_xml_writer->xmlStartTag("fo:flow", $attrs);


		// StructureObjects
		//$expLog->write(date("[y-m-d H:i:s] ")."Start Export Structure Objects");
		$ilBench->start("ContentObjectExport", "exportFOStructureObjects");
		$this->exportFOStructureObjects($a_xml_writer, $expLog);
		$ilBench->stop("ContentObjectExport", "exportFOStructureObjects");
		//$expLog->write(date("[y-m-d H:i:s] ")."Finished Export Structure Objects");*/

		// fo:flow (end)
		$a_xml_writer->xmlEndTag("fo:flow");

		// fo:page-sequence (end)
		$a_xml_writer->xmlEndTag("fo:page-sequence");

		// fo:root (end)
		$a_xml_writer->xmlEndTag("fo:root");
	}

	/**
	* export structure objects to fo
	*
	* @param	object		$a_xml_writer	ilXmlWriter object that receives the
	*										xml data
	*/
	function exportFOStructureObjects(&$a_xml_writer)
	{
		$childs = $this->lm_tree->getChilds($this->lm_tree->getRootId());
		foreach ($childs as $child)
		{
			if($child["type"] != "st")
			{
				continue;
			}

			$structure_obj = new ilStructureObject($this, $child["obj_id"]);
			$structure_obj->exportFO($a_xml_writer, $expLog);
			unset($structure_obj);
		}
	}

	function getXMLZip()
	{
		include_once("./Modules/LearningModule/classes/class.ilContObjectExport.php");

		$cont_exp = new ilContObjectExport($this,'xml');

		$export_file = $cont_exp->buildExportFile();
		return $export_file;
	}		

	/**
	* Execute Drag Drop Action
	*
	* @param	string	$source_id		Source element ID
	* @param	string	$target_id		Target element ID
	* @param	string	$first_child	Insert as first child of target
	* @param	string	$movecopy		Position ("move" | "copy")
	*/
	function executeDragDrop($source_id, $target_id, $first_child, $as_subitem = false, $movecopy = "move")
	{
		$lmtree = new ilTree($this->getId());
		$lmtree->setTableNames('lm_tree','lm_data');
		$lmtree->setTreeTablePK("lm_id");
//echo "-".$source_id."-".$target_id."-".$first_child."-".$as_subitem."-";
		$source_obj = ilLMObjectFactory::getInstance($this, $source_id, true);
		$source_obj->setLMId($this->getId());

		if (!$first_child)
		{
			$target_obj = ilLMObjectFactory::getInstance($this, $target_id, true);
			$target_obj->setLMId($this->getId());
			$target_parent = $lmtree->getParentId($target_id);
		}

		// handle pages
		if ($source_obj->getType() == "pg")
		{
//echo "1";
			if ($lmtree->isInTree($source_obj->getId()))
			{
				$node_data = $lmtree->getNodeData($source_obj->getId());

				// cut on move
				if ($movecopy == "move")
				{
					$parent_id = $lmtree->getParentId($source_obj->getId());
					$lmtree->deleteTree($node_data);

					// write history entry
					require_once("./Services/History/classes/class.ilHistory.php");
					ilHistory::_createEntry($source_obj->getId(), "cut",
						array(ilLMObject::_lookupTitle($parent_id), $parent_id),
						$this->getType().":pg");
					ilHistory::_createEntry($parent_id, "cut_page",
						array(ilLMObject::_lookupTitle($source_obj->getId()), $source_obj->getId()),
						$this->getType().":st");
				}
				else
				{
					// copy page
					$new_page =& $source_obj->copy();
					$source_id = $new_page->getId();
					$source_obj =& $new_page;
				}

				// paste page
				if(!$lmtree->isInTree($source_obj->getId()))
				{
					if ($first_child)			// as first child
					{
						$target_pos = IL_FIRST_NODE;
						$parent = $target_id;
					}
					else if ($as_subitem)		// as last child
					{
						$parent = $target_id;
						$target_pos = IL_FIRST_NODE;
						$pg_childs =& $lmtree->getChildsByType($parent, "pg");
						if (count($pg_childs) != 0)
						{
							$target_pos = $pg_childs[count($pg_childs) - 1]["obj_id"];
						}
					}
					else						// at position
					{
						$target_pos = $target_id;
						$parent = $target_parent;
					}

					// insert page into tree
					$lmtree->insertNode($source_obj->getId(),
						$parent, $target_pos);

					// write history entry
					if ($movecopy == "move")
					{
						// write history comments
						include_once("./Services/History/classes/class.ilHistory.php");
						ilHistory::_createEntry($source_obj->getId(), "paste",
							array(ilLMObject::_lookupTitle($parent), $parent),
							$this->getType().":pg");
						ilHistory::_createEntry($parent, "paste_page",
							array(ilLMObject::_lookupTitle($source_obj->getId()), $source_obj->getId()),
							$this->getType().":st");
					}

				}
			}
		}

		// handle chapters
		if ($source_obj->getType() == "st")
		{
//echo "2";
			$source_node = $lmtree->getNodeData($source_id);
			$subnodes = $lmtree->getSubtree($source_node);

			// check, if target is within subtree
			foreach ($subnodes as $subnode)
			{
				if($subnode["obj_id"] == $target_id)
				{
					return;
				}
			}

			$target_pos = $target_id;

			if ($first_child)		// as first subchapter
			{
				$target_pos = IL_FIRST_NODE;
				$target_parent = $target_id;
				
				$pg_childs =& $lmtree->getChildsByType($target_parent, "pg");
				if (count($pg_childs) != 0)
				{
					$target_pos = $pg_childs[count($pg_childs) - 1]["obj_id"];
				}
			}
			else if ($as_subitem)		// as last subchapter
			{
				$target_parent = $target_id;
				$target_pos = IL_FIRST_NODE;
				$childs =& $lmtree->getChilds($target_parent);
				if (count($childs) != 0)
				{
					$target_pos = $childs[count($childs) - 1]["obj_id"];
				}
			}

			// insert into
/*
			if ($position == "into")
			{
				$target_parent = $target_id;
				$target_pos = IL_FIRST_NODE;

				// if target_pos is still first node we must skip all pages
				if ($target_pos == IL_FIRST_NODE)
				{
					$pg_childs =& $lmtree->getChildsByType($target_parent, "pg");
					if (count($pg_childs) != 0)
					{
						$target_pos = $pg_childs[count($pg_childs) - 1]["obj_id"];
					}
				}
			}
*/


			// delete source tree
			if ($movecopy == "move")
			{
				$lmtree->deleteTree($source_node);
			}
			else
			{
				// copy chapter (incl. subcontents)
				$new_chapter =& $source_obj->copy($lmtree, $target_parent, $target_pos);
			}

			if (!$lmtree->isInTree($source_id))
			{
				$lmtree->insertNode($source_id, $target_parent, $target_pos);

				// insert moved tree
				if ($movecopy == "move")
				{
					foreach ($subnodes as $node)
					{
						if($node["obj_id"] != $source_id)
						{
							$lmtree->insertNode($node["obj_id"], $node["parent"]);
						}
					}
				}
			}

			// check the tree
			$this->checkTree();
		}

		$this->checkTree();
	}

	/**
	* Validate all pages
	*/
	function validatePages()
	{
		include_once "./Modules/LearningModule/classes/class.ilLMPageObject.php";
		include_once "./Services/COPage/classes/class.ilPageObject.php";

		$mess = "";
		
		$pages = ilLMPageObject::getPageList($this->getId());
		foreach ($pages as $page)
		{
			if (ilPageObject::_exists($this->getType(), $page["obj_id"]))
			{
				$cpage = new ilPageObject($this->getType(), $page["obj_id"]);
				$cpage->buildDom();
				$error = @$cpage->validateDom();
				
				if ($error != "")
				{
					$this->lng->loadLanguageModule("content");
					ilUtil::sendInfo($this->lng->txt("cont_import_validation_errors"));
					$title = ilLMObject::_lookupTitle($page["obj_id"]);
					$page_obj = new ilLMPageObject($this, $page["obj_id"]);
					$mess.= $this->lng->txt("obj_pg").": ".$title;
					$mess.= '<div class="small">';
					foreach ($error as $e)
					{
						$err_mess = implode($e, " - ");
						if (!is_int(strpos($err_mess, ":0:")))
						{
							$mess.= htmlentities($err_mess)."<br />";
						}
					}
					$mess.= '</div>';
					$mess.= "<br />";
				}
			}
		}
		
		return $mess;
	}

	/**
	 * Import lm from zip file
	 *
	 * @param
	 * @return
	 */
	function importFromZipFile($a_tmp_file, $a_filename, $a_validate = true,
		$a_import_into_help_module = 0)
	{
		global $lng;

		// create import directory
		$this->createImportDirectory();

		// copy uploaded file to import directory
		$file = pathinfo($a_filename);
		$full_path = $this->getImportDirectory()."/".$a_filename;

		ilUtil::moveUploadedFile($a_tmp_file,
			$a_filename, $full_path);

		// unzip file
		ilUtil::unzip($full_path);

		$subdir = basename($file["basename"],".".$file["extension"]);

		$mess =  $this->importFromDirectory(
			$this->getImportDirectory()."/".$subdir, $a_validate);

		// this should only be true for help modules
		if ($a_import_into_help_module > 0)
		{
			// search the zip file
			$dir = $this->getImportDirectory()."/".$subdir;
			$files = ilUtil::getDir($dir);
			foreach ($files as $file)
			{
				if (is_int(strpos($file["entry"], "__help_")) && 
					is_int(strpos($file["entry"], ".zip")))
				{
					include_once("./Services/Export/classes/class.ilImport.php");
					$imp = new ilImport();
					$imp->getMapping()->addMapping('Services/Help', 'help_module', 0, $a_import_into_help_module);
					include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
					$chaps = ilLMObject::getObjectList($this->getId(), "st");
					foreach ($chaps as $chap)
					{
						$chap_arr = explode("_", $chap["import_id"]);
						$imp->getMapping()->addMapping('Services/Help', 'help_chap',
							$chap_arr[count($chap_arr) - 1], $chap["obj_id"]);
					}
					$imp->importEntity($dir."/".$file["entry"], $file["entry"],
						"help", "Services/Help", true);
				}
			}
		}
		
		// delete import directory
		ilUtil::delDir($this->getImportDirectory());

		return $mess;
	}


	/**
	 * Import lm from directory
	 *
	 * @param
	 * @return
	 */
	function importFromDirectory($a_directory, $a_validate = true)
	{
		global $lng;
		
		// determine filename of xml file
		$subdir = basename($a_directory);
		$xml_file = $a_directory."/".$subdir.".xml";

		// check directory exists within zip file
		if (!is_dir($a_directory))
		{
			return sprintf($lng->txt("cont_no_subdir_in_zip"), $subdir);
		}

		// check whether xml file exists within zip file
		if (!is_file($xml_file))
		{
			return sprintf($lng->txt("cont_zip_file_invalid"), $subdir."/".$subdir.".xml");
		}

		// import questions
		$qti_file = $a_directory."/qti.xml";
		$qtis = array();
		if (is_file($qti_file))
		{
			include_once "./Services/QTI/classes/class.ilQTIParser.php";
			include_once("./Modules/Test/classes/class.ilObjTest.php");
			$qtiParser = new ilQTIParser ($qti_file,
				IL_MO_VERIFY_QTI, 0, "");
			$result = $qtiParser->startParsing ();
			$founditems = & $qtiParser->getFoundItems ();
			$testObj = new ilObjTest(0, true);
			if (count($founditems) > 0)
			{
				$qtiParser = new ilQTIParser($qti_file, IL_MO_PARSE_QTI, 0, "");
				$qtiParser->setTestObject($testObj);
				$result = $qtiParser->startParsing();
				$qtis = array_merge($qtis, $qtiParser->getImportMapping());
			}
		}

		include_once ("./Modules/LearningModule/classes/class.ilContObjParser.php");
		$contParser = new ilContObjParser($this, $xml_file, $subdir, $qmapping);
		$contParser->setQuestionMapping($qtis);
		$contParser->startParsing();
		ilObject::_writeImportId($this->getId(), $this->getImportId());
		$this->MDUpdateListener('General');

		// import style
		$style_file = $a_directory."/style.xml";
		$style_zip_file = $a_directory."/style.zip";
		if (is_file($style_zip_file))	// try to import style.zip first
		{
			require_once("./Services/Style/classes/class.ilObjStyleSheet.php");
			$style = new ilObjStyleSheet();
			$style->import($style_zip_file);
			$this->writeStyleSheetId($style->getId());
		}
		else if (is_file($style_file))	// try to import style.xml
		{
			require_once("./Services/Style/classes/class.ilObjStyleSheet.php");
			$style = new ilObjStyleSheet();
			$style->import($style_file);
			$this->writeStyleSheetId($style->getId());
		}

//		// validate
		if ($a_validate)
		{
			$mess = $this->validatePages();
		}

		if ($mess == "")
		{
			// handle internal links to this learning module
			include_once("./Services/COPage/classes/class.ilPageObject.php");
			ilPageObject::_handleImportRepositoryLinks($this->getImportId(),
				$this->getType(), $this->getRefId());
		}

		return $mess;
	}

	/**
	 * Clone learning module
	 *
	 * @access public
	 * @param int target ref_id
	 * @param int copy id
	 *
	 */
	public function cloneObject($a_target_id,$a_copy_id = 0)
	{
		global $ilDB, $ilUser, $ilias;

		$new_obj = parent::cloneObject($a_target_id,$a_copy_id);
		$this->cloneMetaData($new_obj);
		//$new_obj->createProperties();
	 	
		$new_obj->setTitle($this->getTitle());
		$new_obj->setDescription($this->getDescription());
		$new_obj->setLayoutPerPage($this->getLayoutPerPage());
		$new_obj->setLayout($this->getLayout());
		$new_obj->setTOCMode($this->getTOCMode());
		$new_obj->setActiveLMMenu($this->isActiveLMMenu());
		$new_obj->setActiveTOC($this->isActiveTOC());
		$new_obj->setActiveNumbering($this->isActiveNumbering());
		$new_obj->setActivePrintView($this->isActivePrintView());
		$new_obj->setActivePreventGlossaryAppendix($this->isActivePreventGlossaryAppendix());
		$new_obj->setActiveDownloads($this->isActiveDownloads());
		$new_obj->setActiveDownloadsPublic($this->isActiveDownloadsPublic());
		$new_obj->setPublicNotes($this->publicNotes());
		$new_obj->setCleanFrames($this->cleanFrames());
		$new_obj->setHistoryUserComments($this->isActiveHistoryUserComments());
		$new_obj->setPublicAccessMode($this->getPublicAccessMode());
		$new_obj->setPageHeader($this->getPageHeader());
		
		$new_obj->update();
		
		$new_obj->createLMTree();
		
		// copy style
		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
		$style_id = $this->getStyleSheetId();
		if ($style_id > 0 &&
			!ilObjStyleSheet::_lookupStandard($style_id))
		{
			$style_obj = $ilias->obj_factory->getInstanceByObjId($style_id);
			$new_id = $style_obj->ilClone();
			$new_obj->setStyleSheetId($new_id);
			$new_obj->update();
		}
		
		// copy content
		$this->copyAllPagesAndChapters($new_obj);

		// Copy learning progress settings
		include_once('Services/Tracking/classes/class.ilLPObjSettings.php');
		$obj_settings = new ilLPObjSettings($this->getId());
		$obj_settings->cloneSettings($new_obj->getId());
		unset($obj_settings);
		

		return $new_obj;
	}
	
	/**
	 * Copy all pages and chapters
	 *
	 * @param object $a_target_obj target learning module
	 */
	function copyAllPagesAndChapters($a_target_obj)
	{
		$parent_id = $a_target_obj->lm_tree->readRootId();
		
		include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
		include_once("./Modules/LearningModule/classes/class.ilLMPageObject.php");
		
		// get all chapters of root lm
		$chapters = $this->lm_tree->getChildsByType($this->lm_tree->readRootId(), "st");
		$copied_nodes = array();
		//$time = time();
		foreach ($chapters as $chap)
		{
			$cid = ilLMObject::pasteTree($a_target_obj, $chap["child"], $parent_id,
				IL_LAST_NODE, $time, $copied_nodes, true, $this);
			$target = $cid;
		}
		
		// copy free pages
		$pages = ilLMPageObject::getPageList($this->getId());
		foreach ($pages as $p)
		{
			if (!$this->lm_tree->isInTree($p["obj_id"]))
			{
				$item = new ilLMPageObject($this, $p["obj_id"]);
				$target_item = $item->copy($a_target_obj);
				$copied_nodes[$item->getId()] = $target_item->getId();
			}
		}
		
		ilLMObject::updateInternalLinks($copied_nodes);

		$a_target_obj->checkTree();

	}
	
	////
	//// Online help
	////

	/**
	 * Is module an online module
	 *
	 * @return boolean true, if current wiki is an online help wiki
	 */
	static function isOnlineHelpModule($a_ref_id)
	{
		if ($a_ref_id > 0 && $a_ref_id == OH_REF_ID)
		{
			return true;
		}
		return false;
	}

}
?>
