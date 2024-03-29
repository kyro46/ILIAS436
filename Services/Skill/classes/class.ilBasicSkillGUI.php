<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Skill/classes/class.ilSkillTreeNodeGUI.php");
include_once("./Services/Skill/classes/class.ilBasicSkill.php");

/**
* Basic skill GUI class
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
* @ilCtrl_isCalledBy ilBasicSkillGUI: ilObjSkillManagementGUI
* @ilCtrl_Calls ilBasicSkillGUI: ilCertificateGUI
*
* @ingroup ServicesSkill
*/
class ilBasicSkillGUI extends ilSkillTreeNodeGUI
{

	/**
	 * Constructor
	 */
	function __construct($a_node_id = 0)
	{
		global $ilCtrl;
		
		$ilCtrl->saveParameter($this, array("obj_id", "level_id"));
		
		parent::ilSkillTreeNodeGUI($a_node_id);
	}

	/**
	 * Get Node Type
	 */
	function getType()
	{
		return "skll";
	}

	/**
	 * Execute command
	 */
	function executeCommand()
	{
		global $ilCtrl, $tpl, $ilTabs;
		
		$tpl->getStandardTemplate();
		
		$next_class = $ilCtrl->getNextClass($this);
		$cmd = $ilCtrl->getCmd();
		switch($next_class)
		{
			case "ilcertificategui":
				$this->setLevelHead();
				$ilTabs->activateTab("level_certificate");
				include_once "./Services/Certificate/classes/class.ilCertificateGUI.php";
				include_once("./Services/Skill/classes/class.ilSkillCertificateAdapter.php");
				$output_gui = new ilCertificateGUI(
					new ilSkillCertificateAdapter($this->node_object, (int) $_GET["level_id"]));
				$ret = $ilCtrl->forwardCommand($output_gui);
				break;

			default:
				$ret = $this->$cmd();
				break;
		}
	}

	/**
	 * Show properties
	 */
	function showProperties()
	{
		global $tpl;
		
		$this->setTabs();
		$this->setLocator();

		$tpl->setContent("Properties");
	}


	/**
	 * Perform drag and drop action
	 */
	function proceedDragDrop()
	{
		global $ilCtrl;

//		$this->slm_object->executeDragDrop($_POST["il_hform_source_id"], $_POST["il_hform_target_id"],
//			$_POST["il_hform_fc"], $_POST["il_hform_as_subitem"]);
//		$ilCtrl->redirect($this, "showOrganization");
	}

	/**
	 * Save item
	 */
	function saveItem()
	{
		$it = new ilBasicSkill();
		$it->setTitle($this->form->getInput("title"));
		$it->setOrderNr($this->form->getInput("order_nr"));
		$it->setDraft($this->form->getInput("draft"));
		$it->setSelfEvaluation($_POST["self_eval"]);
		$it->create();
		ilSkillTreeNode::putInTree($it, (int) $_GET["obj_id"], IL_LAST_NODE);
		$this->node_object = $it;
	}

	/**
	 * After saving
	 */
	function afterSave()
	{
		global $ilCtrl;
		
		$ilCtrl->setParameterByClass("ilbasicskillgui", "obj_id",
			$this->node_object->getId());
		$ilCtrl->redirectByClass("ilbasicskillgui", "edit");
	}

	/**
	 * Update item
	 */
	function updateItem()
	{
		$this->node_object->setTitle($this->form->getInput("title"));
		$this->node_object->setOrderNr($this->form->getInput("order_nr"));
		$this->node_object->setSelfEvaluation($_POST["self_eval"]);
		$this->node_object->setDraft($_POST["draft"]);
		$this->node_object->update();
	}

	/**
	 * Edit skill
	 *
	 * @param
	 * @return
	 */
	function edit()
	{
		global $tpl, $ilToolbar, $lng, $ilCtrl;

		$this->setSkillHead("levels");
		
		$ilToolbar->addButton($lng->txt("skmg_add_level"),
			$ilCtrl->getLinkTarget($this, "addLevel"));
		
		include_once("./Services/Skill/classes/class.ilSkillLevelTableGUI.php");
		$table = new ilSkillLevelTableGUI((int) $_GET["obj_id"], $this, "edit");
		$tpl->setContent($table->getHTML());
	}

	/**
	 * Init form.
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initForm($a_mode = "edit")
	{
		global $lng, $ilCtrl;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		// title
		$ti = new ilTextInputGUI($lng->txt("title"), "title");
		$ti->setMaxLength(200);
		$ti->setSize(50);
		$ti->setRequired(true);
		$this->form->addItem($ti);
		
		// order nr
		$ni = new ilNumberInputGUI($lng->txt("skmg_order_nr"), "order_nr");
		$ni->setMaxLength(6);
		$ni->setSize(6);
		$ni->setRequired(true);
		$this->form->addItem($ni);

		// draft
		$cb = new ilCheckboxInputGUI($lng->txt("skmg_draft"), "draft");
		$cb->setInfo($lng->txt("skmg_draft_info"));
		$this->form->addItem($cb);

		// selectable
		$cb = new ilCheckboxInputGUI($lng->txt("skmg_selectable"), "self_eval");
		$cb->setInfo($lng->txt("skmg_selectable_info"));
		$this->form->addItem($cb);

		// save and cancel commands
		if ($a_mode == "create")
		{
			$this->form->addCommandButton("save", $lng->txt("save"));
			$this->form->addCommandButton("cancelSave", $lng->txt("cancel"));
			$this->form->setTitle($lng->txt("skmg_create_skll"));
		}
		else
		{
			$this->form->addCommandButton("update", $lng->txt("save"));
			$this->form->setTitle($lng->txt("skmg_edit_skll"));
		}
		
		$ilCtrl->setParameter($this, "obj_id", $_GET["obj_id"]);
		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}
	
	/**
	 * Edit properties
	 */
	function editProperties()
	{
		$this->setSkillHead("properties");
		parent::editProperties();
	}
	

	//
	//
	// Skill level related methods
	//
	//

	/**
	 * Add new level
	 *
	 * @param
	 * @return
	 */
	function addLevel()
	{
		global $tpl;

		$this->initLevelForm("create");
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Edit level
	 *
	 * @param
	 * @return
	 */
	function editLevel()
	{
		global $tpl;

		$this->initLevelForm();
		$this->getLevelValues();
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Save level form
	 */
	public function saveLevel()
	{
		global $tpl, $lng, $ilCtrl;

		$this->initLevelForm("create");
		if ($this->form->checkInput())
		{
			// perform save
			$this->node_object->addLevel(
				$this->form->getInput("title"),
				$this->form->getInput("description"));

			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "edit");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}

	/**
	 * Update level form
	 */
	function updateLevel()
	{
		global $lng, $ilCtrl, $tpl;

		$this->initLevelForm("edit");
		if ($this->form->checkInput())
		{
			$this->node_object->writeLevelTitle(
					(int) $_GET["level_id"],
					$this->form->getInput("title"));
			$this->node_object->writeLevelDescription(
					(int) $_GET["level_id"],
					$this->form->getInput("description"));

			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "edit");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}

	/**
	 * Init level form.
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initLevelForm($a_mode = "edit")
	{
		global $lng, $ilCtrl, $ilTabs;

		$ilCtrl->saveParameter($this, "level_id");
		$this->setLevelHead();
		$ilTabs->activateTab("level_settings");
		
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		// title
		$ti = new ilTextInputGUI($lng->txt("title"), "title");
		$ti->setMaxLength(200);
		$ti->setRequired(true);
		$this->form->addItem($ti);

		// description
		$ta = new ilTextAreaInputGUI($lng->txt("description"), "description");
		$ta->setCols(50);
		$ta->setRows(5);
		$this->form->addItem($ta);

		// save and cancel commands
		if ($a_mode == "create")
		{
			$this->form->addCommandButton("saveLevel", $lng->txt("save"));
			$this->form->addCommandButton("edit", $lng->txt("cancel"));
			$this->form->setTitle($lng->txt("skmg_new_level"));
		}
		else
		{
			$this->form->addCommandButton("updateLevel", $lng->txt("save"));
			$this->form->addCommandButton("edit", $lng->txt("cancel"));
			$this->form->setTitle($lng->txt("skmg_edit_level"));
		}

		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}

	/**
	 * Get current values for level from
	 */
	public function getLevelValues()
	{
		$values = array();

		$data = $this->node_object->getLevelData((int) $_GET["level_id"]);
		$values["title"] = $data["title"];
		$values["description"] = $data["description"];
		$this->form->setValuesByArray($values);
	}

	/**
	 * Update level order
	 *
	 * @param
	 * @return
	 */
	function updateLevelOrder()
	{
		global $lng, $ilCtrl;

		$order = ilUtil::stripSlashesArray($_POST["order"]);
		$this->node_object->updateLevelOrder($order);
		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "edit");
	}

	/**
	 * Confirm level deletion
	 */
	function confirmLevelDeletion()
	{
		global $ilCtrl, $tpl, $lng;

		$this->setSkillHead("levels");

		if (!is_array($_POST["id"]) || count($_POST["id"]) == 0)
		{
			ilUtil::sendInfo($lng->txt("no_checkbox"), true);
			$ilCtrl->redirect($this, "edit");
		}
		else
		{
			include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");
			$cgui = new ilConfirmationGUI();
			$cgui->setFormAction($ilCtrl->getFormAction($this));
			$cgui->setHeaderText($lng->txt("skmg_really_delete_levels"));
			$cgui->setCancel($lng->txt("cancel"), "edit");
			$cgui->setConfirm($lng->txt("delete"), "deleteLevel");

			foreach ($_POST["id"] as $i)
			{
				$cgui->addItem("id[]", $i, ilBasicSkill::lookupLevelTitle($i));
			}

			$tpl->setContent($cgui->getHTML());
		}
	}

	/**
	 * Delete levels
	 *
	 * @param
	 * @return
	 */
	function deleteLevel()
	{
		global $lng, $ilCtrl;

		if (is_array($_POST["id"]))
		{
			foreach ($_POST["id"] as $id)
			{
				$this->node_object->deleteLevel((int) $id);
			}
			$this->node_object->fixLevelNumbering();
		}
		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this, "edit");
	}

	/**
	 * Set header for level
	 *
	 * @param
	 * @return
	 */
	function setLevelHead()
	{
		global $ilTabs, $ilCtrl, $tpl, $lng;

		// tabs
		$ilTabs->clearTargets();
		$ilTabs->setBackTarget($lng->txt("skmg_skill_levels"),
			$ilCtrl->getLinkTarget($this, "edit"));

		if ($_GET["level_id"] > 0)
		{
			$ilTabs->addTab("level_settings",
				$lng->txt("settings"),
				$ilCtrl->getLinkTarget($this, "editLevel"));

/*			$ilTabs->addTab("level_trigger",
				$lng->txt("skmg_trigger"),
				$ilCtrl->getLinkTarget($this, "editLevelTrigger"));

			$ilTabs->addTab("level_certificate",
				$lng->txt("certificate"),
				$ilCtrl->getLinkTargetByClass("ilcertificategui", "certificateEditor"));*/
		}

		// title
		if ($_GET["level_id"] > 0)
		{
			$tpl->setTitle($lng->txt("skmg_skill_level").": ".
				ilBasicSkill::lookupLevelTitle((int) $_GET["level_id"]));
		}
		else
		{
			$tpl->setTitle($lng->txt("skmg_skill_level"));			
		}

		include_once("./Services/Skill/classes/class.ilSkillTree.php");
		$tree = new ilSkillTree();
		$path = $tree->getPathFull($this->node_object->getId());
		$desc = "";
		foreach ($path as $p)
		{
			if (in_array($p["type"], array("scat", "skll")))
			{
				$desc.= $sep.$p["title"];
				$sep = " > ";
			}
		}
		$tpl->setDescription($desc);
	}

	/**
	 * Set header for skill
	 *
	 * @param
	 * @return
	 */
	function setSkillHead($a_tab = "levels")
	{
		global $ilTabs, $ilCtrl, $tpl, $lng;

		$ilTabs->clearTargets();
//		$ilTabs->setBackTarget($lng->txt("skmg_skill_hierarchie"),
//			$ilCtrl->getLinkTargetByClass("ilobjskillmanagementgui", "editSkills"));

		if (is_object($this->node_object))
		{

			// levels
			$ilTabs->addTab("levels", $lng->txt("skmg_skill_levels"),
				$ilCtrl->getLinkTarget($this, 'edit'));
	
			// properties
			$ilTabs->addTab("properties", $lng->txt("settings"),
				$ilCtrl->getLinkTarget($this, 'editProperties'));
			
			$ilCtrl->setParameterByClass("ilskillrootgui", "obj_id",
				$this->node_object->skill_tree->getRootId());
			$ilTabs->setBackTarget($lng->txt("obj_skmg"),
				$ilCtrl->getLinkTargetByClass("ilskillrootgui", "listSkills"));
			$ilCtrl->setParameterByClass("ilskillrootgui", "obj_id",
				$_GET["obj_id"]);
			
			$ilTabs->activateTab($a_tab);

			$tpl->setTitle($lng->txt("skmg_skill").": ".
				$this->node_object->getTitle());
		
			$this->setSkillNodeDescription();
		}
		else
		{
			$tpl->setTitle($lng->txt("skmg_skill"));
			$tpl->setDescription("");
		}
		parent::setTitleIcon();
	}

	/**
	 * Edit level trigger
	 *
	 * @param
	 * @return
	 */
	function editLevelTrigger()
	{
		global $lng, $ilCtrl, $tpl, $ilTabs;

		$this->setLevelHead();
		$ilTabs->activateTab("level_trigger");

		$trigger = ilBasicSkill::lookupLevelTrigger((int) $_GET["level_id"]);
		if (ilObject::_lookupType($trigger["obj_id"]) != "crs" ||
			ilObject::_isInTrash($trigger["ref_id"]))
		{
			$trigger = array();
		}
		
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();
		
		// trigger
		$ne = new ilNonEditableValueGUI($lng->txt("skmg_trigger"), "trigger");
		if ($trigger["obj_id"] > 0)
		{
			$ne->setValue(ilObject::_lookupTitle($trigger["obj_id"]));
		}
		else
		{
			$ne->setValue($lng->txt("skmg_no_trigger"));
		}
		$this->form->addItem($ne);

		if ($trigger["obj_id"] > 0)
		{
			$this->form->addCommandButton("removeLevelTrigger", $lng->txt("skmg_remove_trigger"));
		}
		$this->form->addCommandButton("selectLevelTrigger", $lng->txt("skmg_select_trigger"));

		$this->form->setTitle($lng->txt("skmg_skill_level_trigger"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));

		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Select skill level trigger
	 *
	 */
	function selectLevelTrigger()
	{
		global $ilCtrl, $ilTabs, $lng, $tree, $tpl;

		$this->setLevelHead();
		$ilTabs->activateTab("level_trigger");

		include_once 'Services/Search/classes/class.ilSearchRootSelector.php';
		$exp = new ilSearchRootSelector(
			$ilCtrl->getLinkTarget($this,'showRepositorySelection'));
		$exp->setExpand($_GET["search_root_expand"] ? $_GET["search_root_expand"] : $tree->readRootId());
		$exp->setExpandTarget($ilCtrl->getLinkTarget($this,'selectLevelTrigger'));
		$exp->setTargetClass(get_class($this));
		$exp->setCmd('saveLevelTrigger');
		$exp->setClickableTypes(array("crs"));

		// build html-output
		$exp->setOutput(0);
		$tpl->setContent($exp->getOutput());

	}

	/**
	 * Save level trigger
	 *
	 * @param
	 * @return
	 */
	function saveLevelTrigger()
	{
		global $ilCtrl;

		ilBasicSkill::writeLevelTrigger((int) $_GET["level_id"], (int) $_GET["root_id"]);
		$ilCtrl->redirect($this, "editLevelTrigger");
	}

	/**
	 * Remove trigger
	 *
	 * @param
	 * @return
	 */
	function removeLevelTrigger()
	{
		global $ilCtrl;

		ilBasicSkill::writeLevelTrigger((int) $_GET["level_id"], 0);
		$ilCtrl->redirect($this, "editLevelTrigger");
	}
	
	/**
	 * Redirect to parent (identified by current obj_id)
	 *
	 * @param
	 * @return
	 */
	function redirectToParent()
	{
		global $ilCtrl;
		
		$t = ilSkillTreeNode::_lookupType((int) $_GET["obj_id"]);

		switch ($t)
		{
			case "skrt":
				$ilCtrl->setParameterByClass("ilskillrootgui", "obj_id", (int) $_GET["obj_id"]);
				$ilCtrl->redirectByClass("ilskillrootgui", "listSkills");
				break;
		}
		
		parent::redirectToParent();
	}

}
?>
