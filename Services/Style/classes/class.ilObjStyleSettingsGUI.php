<?php
/* Copyright (c) 1998-2012 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once "./Services/Object/classes/class.ilObjectGUI.php";
include_once("./Services/Style/classes/class.ilPageLayout.php");

/**
 * Style settings GUI class
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id: class.ilObjStyleSettingsGUI.php 46273 2013-11-19 12:20:01Z jluetzen $
 * 
 * @ilCtrl_Calls ilObjStyleSettingsGUI: ilPermissionGUI, ilPageLayoutGUI
 * 
 * @ingroup	ServicesStyle
 */
class ilObjStyleSettingsGUI extends ilObjectGUI
{
	//page_layout editing
	var $peditor_active = false;
	var $pg_id = null;
	
	/**
	 * Constructor
	 */
	function ilObjStyleSettingsGUI($a_data,$a_id,$a_call_by_reference,$a_prepare_output = true)
	{
		global $lng,$ilCtrl;
		
		$this->type = "stys";
		
		$cmd = $ilCtrl->getCmd();
		
		if ($cmd == "editPg") {
			$this->peditor_active = true;
		}
					
		$this->ilObjectGUI($a_data,$a_id,$a_call_by_reference,$a_prepare_output);
		
		$lng->loadLanguageModule("style");
	}
	
	/**
	 * Execute command
	 */
	function &executeCommand()
	{
		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();
		 
		if ($next_class == "ilpagelayoutgui" || $cmd =="createPg") {
			$this->peditor_active =true;
		}
		
		$this->prepareOutput();
		
		switch($next_class)
		{
			case 'ilpermissiongui':
				include_once("Services/AccessControl/classes/class.ilPermissionGUI.php");
				$perm_gui =& new ilPermissionGUI($this);
				$ret =& $this->ctrl->forwardCommand($perm_gui);
				break;
				
			case 'ilpagelayoutgui':
				include_once("./Services/Style/classes/class.ilPageLayoutGUI.php");
				$this->tpl->getStandardTemplate();
				$this->ctrl->setReturn($this, "edit");
				if ($this->pg_id!=null) {
					$layout_gui =& new ilPageLayoutGUI($this->type,$this->pg_id);
				} else {
					$layout_gui =& new ilPageLayoutGUI($this->type,$_GET["obj_id"]);	
				}				
				$layout_gui->setTabs();
				$layout_gui->setEditPreview(true);
				$this->ctrl->saveParameter($this, "obj_id");
				$ret =& $this->ctrl->forwardCommand($layout_gui);
				$this->tpl->setContent($ret);
				break;	

			default:
				if ($cmd == "" || $cmd == "view")
				{
					$cmd = "editBasicSettings";
				}
				$cmd .= "Object";
				$this->$cmd();

				break;
		}
		return true;
	}
	
	/**
	 * Save object
	 */
	function saveObject()
	{
		global $rbacadmin;

		// create and insert forum in objecttree
		$newObj = parent::saveObject();

		// put here object specific stuff
			
		// always send a message
		ilUtil::sendInfo($this->lng->txt("object_added"),true);
		
		ilUtil::redirect($this->getReturnLocation("save",$this->ctrl->getLinkTarget($this,"","",false,false)));
	}
	
	/**
	* edit basic style settings
	*/
	function editBasicSettingsObject()
	{
		global $rbacsystem;

		if (!$rbacsystem->checkAccess("visible,read",$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}
		
		$this->tpl->addBlockfile("ADM_CONTENT", "style_basic_settings", "tpl.stys_basic_settings.html", "Services/Style");
		//$this->tpl->setCurrentBlock("style_settings");

		$settings = $this->ilias->getAllSettings();
		
		if ($rbacsystem->checkAccess("write", $this->object->getRefId()))
		{
			$this->tpl->setCurrentBlock("save_but");
			$this->tpl->setVariable("TXT_SAVE", $this->lng->txt("save"));
			$this->tpl->parseCurrentBlock();
		}

		$this->tpl->setVariable("FORMACTION_STYLESETTINGS", $this->ctrl->getFormAction($this));

		$this->tpl->setVariable("TXT_TREE_FRAME", $this->lng->txt("tree_frame"));
		$this->tpl->setVariable("TXT_TREE_FRAME_INFO", $this->lng->txt("tree_frame_info"));
		$this->tpl->setVariable("TXT_FRAME_LEFT", $this->lng->txt("tree_left"));
		$this->tpl->setVariable("TXT_FRAME_RIGHT", $this->lng->txt("tree_right"));

		$this->tpl->setVariable("TXT_STYLE_SETTINGS", $this->lng->txt("settings"));
		$this->tpl->setVariable("TXT_ICONS_IN_TYPED_LISTS", $this->lng->txt("icons_in_typed_lists"));
		$this->tpl->setVariable("TXT_ICONS_IN_HEADER", $this->lng->txt("icons_in_header"));
		$this->tpl->setVariable("TXT_ICONS_IN_ITEM_ROWS", $this->lng->txt("icons_in_item_rows"));
		$this->tpl->setVariable("TXT_ICONS_IN_TYPED_LISTS_INFO", $this->lng->txt("icons_in_typed_lists_info"));
		
		$this->tpl->setVariable("TXT_ENABLE_CUSTOM_ICONS", $this->lng->txt("enable_custom_icons"));
		$this->tpl->setVariable("TXT_ENABLE_CUSTOM_ICONS_INFO", $this->lng->txt("enable_custom_icons_info"));
		$this->tpl->setVariable("TXT_CUSTOM_ICON_SIZE_BIG", $this->lng->txt("custom_icon_size_big"));
		$this->tpl->setVariable("TXT_CUSTOM_ICON_SIZE_SMALL", $this->lng->txt("custom_icon_size_standard"));
		$this->tpl->setVariable("TXT_CUSTOM_ICON_SIZE_TINY", $this->lng->txt("custom_icon_size_tiny"));
		$this->tpl->setVariable("TXT_WIDTH_X_HEIGHT", $this->lng->txt("width_x_height"));
		
		// set current values
		if ($settings["tree_frame"] == "right")
		{
			$this->tpl->setVariable("SEL_FRAME_RIGHT","selected=\"selected\"");
		}
		else
		{
			$this->tpl->setVariable("SEL_FRAME_LEFT","selected=\"selected\"");
		}
		
		if ($settings["custom_icons"])
		{
			$this->tpl->setVariable("CHK_CUSTOM_ICONS","checked=\"checked\"");
		}
/*		if ($settings["icon_position_in_lists"] == "item_rows")
		{
			$this->tpl->setVariable("SEL_ICON_POS_ITEM_ROWS","selected=\"selected\"");
		}
		else
		{
			$this->tpl->setVariable("SEL_ICON_POS_HEADER","selected=\"selected\"");
		}*/
		$this->tpl->setVariable("CUST_ICON_BIG_WIDTH", $settings["custom_icon_big_width"]);
		$this->tpl->setVariable("CUST_ICON_BIG_HEIGHT", $settings["custom_icon_big_height"]);
		$this->tpl->setVariable("CUST_ICON_SMALL_WIDTH", $settings["custom_icon_small_width"]);
		$this->tpl->setVariable("CUST_ICON_SMALL_HEIGHT", $settings["custom_icon_small_height"]);
		$this->tpl->setVariable("CUST_ICON_TINY_WIDTH", $settings["custom_icon_tiny_width"]);
		$this->tpl->setVariable("CUST_ICON_TINY_HEIGHT", $settings["custom_icon_tiny_height"]);

//		$this->tpl->parseCurrentBlock();
	}
	
	/**
	* save basic style settings
	*/
	function saveBasicStyleSettingsObject()
	{
		$this->ilias->setSetting("tree_frame", $_POST["tree_frame"]);
//		$this->ilias->setSetting("icon_position_in_lists", $_POST["icon_position_in_lists"]);
		$this->ilias->setSetting("custom_icons", $_POST["custom_icons"]);
		$this->ilias->setSetting("custom_icon_big_width", (int) $_POST["custom_icon_big_width"]);
		$this->ilias->setSetting("custom_icon_big_height", (int) $_POST["custom_icon_big_height"]);
		$this->ilias->setSetting("custom_icon_small_width", (int) $_POST["custom_icon_small_width"]);
		$this->ilias->setSetting("custom_icon_small_height", (int) $_POST["custom_icon_small_height"]);
		$this->ilias->setSetting("custom_icon_tiny_width", (int) $_POST["custom_icon_tiny_width"]);
		$this->ilias->setSetting("custom_icon_tiny_height", (int) $_POST["custom_icon_tiny_height"]);
		ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
		ilUtil::redirect($this->ctrl->getLinkTarget($this,"editBasicSettings","",false,false));
	}
	
	/**
	* view list of styles
	*/
	function editContentStylesObject()
	{
		global $rbacsystem, $ilias, $tpl, $ilToolbar, $ilCtrl, $lng;
		
		if (!$rbacsystem->checkAccess("visible,read",$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}

		// this may not be cool, if styles are organised as (independent) Service
		include_once("./Modules/LearningModule/classes/class.ilObjContentObject.php");
		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");

		$from_styles = $to_styles = $data = array();
		$styles = $this->object->getStyles();

		foreach($styles as $style)
		{
			$style["active"] = ilObjStyleSheet::_lookupActive($style["id"]);
			$style["lm_nr"] = ilObjContentObject::_getNrOfAssignedLMs($style["id"]);
			$data[$style["title"].":".$style["id"]]
				= $style;
			if ($style["lm_nr"] > 0)
			{
				$from_styles[$style["id"]] = $style["title"];
			}
			if ($style["active"] > 0)
			{
				$to_styles[$style["id"]] = $style["title"];
			}
		}

		// number of individual styles
		if ($fixed_style <= 0)
		{
			$data[-1] =
				array("title" => $this->lng->txt("sty_individual_styles"),
					"id" => 0, "lm_nr" => ilObjContentObject::_getNrLMsIndividualStyles());
			$from_styles[-1] = $this->lng->txt("sty_individual_styles");
		}

		// number of default style (fallback default style)
		if ($default_style <= 0 && $fixed_style <= 0)
		{
			$data[0] =
				array("title" => $this->lng->txt("sty_default_style"),
					"id" => 0, "lm_nr" => ilObjContentObject::_getNrLMsNoStyle());
			$from_styles[0] = $this->lng->txt("sty_default_style");
			$to_styles[0] = $this->lng->txt("sty_default_style");
		}

		if ($rbacsystem->checkAccess("write",$this->object->getRefId()))
		{
			$ilToolbar->addButton($lng->txt("sty_add_content_style"),
				$ilCtrl->getLinkTarget($this, "createStyle"));
			$ilToolbar->addSeparator();
			include_once("./Services/Form/classes/class.ilSelectInputGUI.php");
			
			// from styles selector
			$si = new ilSelectInputGUI($lng->txt("sty_move_lm_styles").": ".$lng->txt("sty_from"), "from_style");
			$si->setOptions($from_styles);
			$ilToolbar->addInputItem($si, true);
	
			// from styles selector
			$si = new ilSelectInputGUI($lng->txt("sty_to"), "to_style");
			$si->setOptions($to_styles);
			$ilToolbar->addInputItem($si, true);
			$ilToolbar->addFormButton($lng->txt("sty_move_style"), "moveLMStyles");
	
			$ilToolbar->setFormAction($ilCtrl->getFormAction($this));
		}

		include_once("./Services/Style/classes/class.ilContentStylesTableGUI.php");
		$table = new ilContentStylesTableGUI($this, "editContentStyles", $data, $this->object);
		$tpl->setContent($table->getHTML());

	}
	
	/**
	* move learning modules from one style to another
	*/
	function moveLMStylesObject()
	{
		if ($_POST["from_style"] == -1)
		{
			$this->confirmDeleteIndividualStyles();
			return;
		}
		
		include_once("./Modules/LearningModule/classes/class.ilObjContentObject.php");
		ilObjContentObject::_moveLMStyles($_POST["from_style"], $_POST["to_style"]);
		$this->ctrl->redirect($this, "editContentStyles");
	}
	
	
	/**
	* move all learning modules with individual styles to new style
	*/
	function moveIndividualStylesObject()
	{
		include_once("./Modules/LearningModule/classes/class.ilObjContentObject.php");
		ilObjContentObject::_moveLMStyles(-1, $_GET["to_style"]);
		$this->ctrl->redirect($this, "editContentStyles");
	}

	/**
	 *
	 */
	function confirmDeleteIndividualStyles()
	{
		global $ilCtrl, $tpl, $lng;

		include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");

		$ilCtrl->setParameter($this, "to_style", $_POST["to_style"]);

		$cgui = new ilConfirmationGUI();
		$cgui->setFormAction($ilCtrl->getFormAction($this));
		$cgui->setHeaderText($lng->txt("sty_confirm_del_ind_styles").": ".
			sprintf($this->lng->txt("sty_confirm_del_ind_styles_desc"),
			ilObject::_lookupTitle($_POST["to_style"])));
		$cgui->setCancel($lng->txt("cancel"), "editContentStyles");
		$cgui->setConfirm($lng->txt("ok"), "moveIndividualStyles");
		$tpl->setContent($cgui->getHTML());
	}
		
	/**
	* edit system styles
	*/
	function editSystemStylesObject()
	{
		global $rbacsystem, $ilias, $styleDefinition, $ilToolbar, $ilCtrl, $lng, $tpl;
		
		if (!$rbacsystem->checkAccess("visible,read",$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}
	
		// toolbar
		
		// default skin/style
		if ($rbacsystem->checkAccess("write",$this->object->getRefId()))
		{
			include_once("./Services/Form/classes/class.ilSelectInputGUI.php");
			
			$options = array();
			foreach (ilStyleDefinition::getAllSkinStyles() as $st)
			{
				$options[$st["id"]] = $st["title"];
			}
			
			// from styles selector
			$si = new ilSelectInputGUI($lng->txt("sty_move_user_styles").": ".$lng->txt("sty_from"), "from_style");
			$si->setOptions($options + array("other" => $lng->txt("other")));
			$ilToolbar->addInputItem($si, true);
	
			// from styles selector
			$si = new ilSelectInputGUI($lng->txt("sty_to"), "to_style");
			$si->setOptions($options);
			$ilToolbar->addInputItem($si, true);
			$ilToolbar->addFormButton($lng->txt("sty_move_style"), "moveUserStyles");
	
			$ilToolbar->setFormAction($ilCtrl->getFormAction($this));
		}
		
		include_once("./Services/Style/classes/class.ilSystemStylesTableGUI.php");
		$tab = new ilSystemStylesTableGUI($this, "editSystemStylesObject");
		$tpl->setContent($tab->getHTML());

	}
	

	/**
	* save skin and style settings
	*/
	function saveStyleSettingsObject()
	{
		global $styleDefinition, $ilCtrl;
		
		// check if one style is activated
		if (count($_POST["st_act"]) < 1)
		{
			$this->ilias->raiseError($this->lng->txt("at_least_one_style"), $this->ilias->error_obj->MESSAGE);
		}
		
		//set default skin and style
		if ($_POST["default_skin_style"] != "")
		{
			$sknst = explode(":", $_POST["default_skin_style"]);

			if ($this->ilias->ini->readVariable("layout","style") != $sknst[1] ||
				$this->ilias->ini->readVariable("layout","skin") != $sknst[0])
			{
				$this->ilias->ini->setVariable("layout","skin", $sknst[0]);
				$this->ilias->ini->setVariable("layout","style",$sknst[1]);
			}
			$this->ilias->ini->write();
		}
		
		// check if a style should be deactivated, that still has
		// a user assigned to
		$all_styles = ilStyleDefinition::getAllSkinStyles();
		foreach ($all_styles as $st)
		{
			if (!isset($_POST["st_act"][$st["id"]]))
			{
				if (ilObjUser::_getNumberOfUsersForStyle($st["template_id"], $st["style_id"]) > 1)
				{
					$this->ilias->raiseError($this->lng->txt("cant_deactivate_if_users_assigned"), $this->ilias->error_obj->MESSAGE);
				}
				else
				{
					ilObjStyleSettings::_deactivateStyle($st["template_id"], $st["style_id"]);
				}
			}
			else
			{
				ilObjStyleSettings::_activateStyle($st["template_id"], $st["style_id"]);
			}
		}

		ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this , "editSystemStyles");
	}
	
	/**
	 * Move user styles
	 *
	 * @param
	 * @return
	 */
	function moveUserStylesObject()
	{
		global $ilCtrl, $lng;
		
		$to = explode(":", $_POST["to_style"]);
		
		if ($_POST["from_style"] != "other")
		{
			$from = explode(":", $_POST["from_style"]);
			ilObjUser::_moveUsersToStyle($from[0],$from[1],$to[0],$to[1]);
		}
		else
		{
			// get all user assigned styles
			$all_user_styles = ilObjUser::_getAllUserAssignedStyles();
			
			// move users that are not assigned to
			// currently existing style
			foreach($all_user_styles as $style)
			{
				if (!in_array($style, $all_styles))
				{
					$style_arr = explode(":", $style);
					ilObjUser::_moveUsersToStyle($style_arr[0],$style_arr[1],$to[0],$to[1]);
				}
			}
		}
		
		ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
		$ilCtrl->redirect($this , "editSystemStyles");
	}
	
	
	
	/**
	* display deletion confirmation screen
	*
	* @access	public
 	*/
	function deleteStyleObject($a_error = false)
	{
		if (!isset($_POST["id"]))
		{
			$this->ilias->raiseError($this->lng->txt("no_checkbox"),$this->ilias->error_obj->MESSAGE);
		}
		
		// display confirmation message
		include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");
		$cgui = new ilConfirmationGUI();
		$cgui->setFormAction($this->ctrl->getFormAction($this));
		$cgui->setHeaderText($this->lng->txt("info_delete_sure"));
		$cgui->setCancel($this->lng->txt("cancel"), "cancelDelete");
		$cgui->setConfirm($this->lng->txt("confirm"), "confirmedDelete");
		
		foreach ($_POST["id"] as $id)
		{			
			$caption =  ilUtil::getImageTagByType("sty", $this->tpl->tplPath).
				" ".ilObject::_lookupTitle($id);
			
			$cgui->addItem("id[]", $id, $caption);
		}

		$this->tpl->setContent($cgui->getHTML());		
	}
	
	
	/**
	* delete selected style objects
	*/
	function confirmedDeleteObject()
	{
		global $ilias;
		
		foreach($_POST["id"] as $id)
		{
			$this->object->removeStyle($id);
			$style_obj =& $ilias->obj_factory->getInstanceByObjId($id);
			$style_obj->delete();
		}
		$this->object->update();
		
		ilUtil::redirect($this->getReturnLocation("delete",
			$this->ctrl->getLinkTarget($this,"editContentStyles","",false,false)));
	}
	
	
	/**
	 * Toggle global default style
 	 */
	function toggleGlobalDefaultObject()
	{
		global $ilSetting, $lng;
		
		if ($_GET["id"] > 0)
		{
			$ilSetting->delete("fixed_content_style_id");
			$def_style = $ilSetting->get("default_content_style_id");
		
			if ($def_style != $_GET["id"])
			{
				$ilSetting->set("default_content_style_id", (int) $_GET["id"]);
			}
			else
			{
				$ilSetting->delete("default_content_style_id");
			}
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		}
		ilUtil::redirect($this->ctrl->getLinkTarget($this, "editContentStyles", "", false, false));
	}

	/**
	 * Toggle global fixed style
 	 */
	function toggleGlobalFixedObject()
	{
		global $ilSetting, $lng;
		
		if ($_GET["id"] > 0)
		{
			$ilSetting->delete("default_content_style_id");
			$fixed_style = $ilSetting->get("fixed_content_style_id");
			if ($fixed_style == (int) $_GET["id"])
			{
				$ilSetting->delete("fixed_content_style_id");
			}
			else
			{
				$ilSetting->set("fixed_content_style_id", (int) $_GET["id"]);
			}
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		}
		ilUtil::redirect($this->ctrl->getLinkTarget($this, "editContentStyles", "", false, false));
	}
	
	
	/**
	 * Save active styles
	 */
	function saveActiveStylesObject()
	{
		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
		$styles = $this->object->getStyles();
		foreach($styles as $style)
		{
			if ($_POST["std_".$style["id"]] == 1)
			{
				ilObjStyleSheet::_writeActive((int) $style["id"], 1);
			}
			else
			{
				ilObjStyleSheet::_writeActive((int) $style["id"], 0);
			}
		}
		ilUtil::redirect($this->ctrl->getLinkTarget($this, "editContentStyles", "", false, false));
	}
	
	/**
	* show possible action (form buttons)
	*
	* @param	boolean
	* @access	public
 	*/
	function showActions($with_subobjects = false)
	{

		// delete
		$this->tpl->setCurrentBlock("tbl_action_btn");
		$this->tpl->setVariable("BTN_NAME", "deleteStyle");
		$this->tpl->setVariable("BTN_VALUE", $this->lng->txt("delete"));
		$this->tpl->parseCurrentBlock();

		// set global default
		$this->tpl->setCurrentBlock("tbl_action_btn");
		$this->tpl->setVariable("BTN_NAME", "toggleGlobalDefault");
		$this->tpl->setVariable("BTN_VALUE", $this->lng->txt("toggleGlobalDefault"));
		$this->tpl->parseCurrentBlock();
		
		// set global default
		$this->tpl->setCurrentBlock("tbl_action_btn");
		$this->tpl->setVariable("BTN_NAME", "toggleGlobalFixed");
		$this->tpl->setVariable("BTN_VALUE", $this->lng->txt("toggleGlobalFixed"));
		$this->tpl->parseCurrentBlock();

		// set global default
		$this->tpl->setCurrentBlock("tbl_action_btn");
		$this->tpl->setVariable("BTN_NAME", "setScope");
		$this->tpl->setVariable("BTN_VALUE", $this->lng->txt("sty_set_scope"));
		$this->tpl->parseCurrentBlock();

		// save active styles
		$this->tpl->setCurrentBlock("tbl_action_btn");
		$this->tpl->setVariable("BTN_NAME", "saveActiveStyles");
		$this->tpl->setVariable("BTN_VALUE", $this->lng->txt("sty_save_active_styles"));
		$this->tpl->parseCurrentBlock();

		if ($with_subobjects === true)
		{
			$this->showPossibleSubObjects();
		}
		
		$this->tpl->setCurrentBlock("tbl_action_row");
		$this->tpl->setVariable("IMG_ARROW", ilUtil::getImagePath("arrow_downright.png"));
		$this->tpl->parseCurrentBlock();
	}

	/**
	* cancel deletion of object
	*
	* @access	public
	*/
	function cancelDeleteObject()
	{
		ilUtil::sendInfo($this->lng->txt("msg_cancel"),true);
		$this->ctrl->redirect($this, "editContentStyles");

	}

	function setScopeObject()
	{
		if ($_GET["id"] > 0)
		{		
			include_once ("./Services/Style/classes/class.ilStyleScopeExplorer.php");
			$exp = new ilStyleScopeExplorer("ilias.php?baseClass=ilRepositoryGUI&amp;cmd=goto");
			$exp->setExpandTarget("ilias.php?baseClass=ilRepositoryGUI&amp;cmd=showTree");
			$exp->setTargetGet("ref_id");
			$exp->setFilterMode(IL_FM_POSITIVE);
			$exp->forceExpandAll(true, false);
			$exp->addFilter("root");
			$exp->addFilter("cat");

			if ($_GET["expand"] == "")
			{
				$expanded = $this->tree->readRootId();
			}
			else
			{
				$expanded = $_GET["expand"];
			}

			$exp->setExpand($expanded);

			// build html-output
			$exp->setOutput(0);
			$output = $exp->getOutput();
		}

		$this->tpl->setVariable("ADM_CONTENT", $output);
	}
	
	/**
	* save scope for style
	*/
	function saveScopeObject()
	{
		global $ilias;
		
		include_once("./Services/Style/classes/class.ilObjStyleSheet.php");
		if ($_GET["cat"] == 0)
		{
			$_GET["cat"] == "";
		}
		ilObjStyleSheet::_writeScope($_GET["style_id"], $_GET["cat"]);
		
		ilUtil::redirect($this->ctrl->getLinkTarget($this, "editContentStyles", "", false, false));
	}


	/**
	* view list of page layouts
	*/
	function viewPageLayoutsObject()
	{
		global $tpl, $lng, $ilCtrl, $ilTabs, $ilToolbar, $rbacsystem;
		
		$ilTabs->setTabActive('page_layouts');
		
		// show toolbar, if write permission is given
		if ($rbacsystem->checkAccess("write",$this->object->getRefId()))
		{
			$ilToolbar->addButton($lng->txt("sty_add_pgl"),
				$ilCtrl->getLinkTarget($this, "addPageLayout"));
			$ilToolbar->addButton($lng->txt("sty_import_page_layout"),
				$ilCtrl->getLinkTarget($this, "importPageLayoutForm"));
		}

		$oa_tpl = new ilTemplate("tpl.stys_pglayout.html", true, true, "Services/Style");
   		
		include_once("./Services/Style/classes/class.ilPageLayoutTableGUI.php");
		$pglayout_table = new ilPageLayoutTableGUI($this, "viewPageLayouts");
		$oa_tpl->setVariable("PGLAYOUT_TABLE", $pglayout_table->getHTML());
		$tpl->setContent($oa_tpl->get());
		
	}
	
	
	function activateObject($a_activate=true){
		if (!isset($_POST["pglayout"]))
		{
			ilUtil::sendInfo($this->lng->txt("no_checkbox"),true);
		} else {
			ilUtil::sendSuccess($this->lng->txt("sty_opt_saved"),true);
			foreach ($_POST["pglayout"] as $item)
			{
				$pg_layout = new ilPageLayout($item);
				$pg_layout->activate($a_activate);
			}
		}	
		$this->ctrl->redirect($this, "viewPageLayouts");
	}
	
	function deactivateObject(){
		$this->activateObject(false);
	}
	
	
	
	/**
	* display deletion confirmation screen
	*/
	function deletePglObject()
	{
		global $ilTabs;
		
		if(!isset($_POST["pglayout"]))
		{
			$this->ilias->raiseError($this->lng->txt("no_checkbox"),$this->ilias->error_obj->MESSAGE);
		}
		
		$ilTabs->setTabActive('page_layouts');		
		unset($this->data);
		
		// display confirmation message
		include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");
		$cgui = new ilConfirmationGUI();
		$cgui->setFormAction($this->ctrl->getFormAction($this));
		$cgui->setHeaderText($this->lng->txt("info_delete_sure"));
		$cgui->setCancel($this->lng->txt("cancel"), "cancelDeletePg");
		$cgui->setConfirm($this->lng->txt("confirm"), "confirmedDeletePg");		
		
		foreach($_POST["pglayout"] as $id)
		{
			$pg_obj = new ilPageLayout($id);
			$pg_obj->readObject();
			
			$caption = ilUtil::getImageTagByType("stys", $this->tpl->tplPath).
				" ".$pg_obj->getTitle();
			
			$cgui->addItem("pglayout[]", $id, $caption);
		}
		
		$this->tpl->setContent($cgui->getHTML());
	}
	
	/**
	* cancel deletion of Page Layout
	*/
	function cancelDeletePgObject()
	{
		ilUtil::sendInfo($this->lng->txt("msg_cancel"),true);
		$this->ctrl->redirect($this, "viewPageLayouts");
	}
	
	/**
	* conform deletion of Page Layout
	*/
	function confirmedDeletePgObject()
	{	 	 
	 	foreach ($_POST["pglayout"] as $id)
	 	{
   	 		$pg_obj = new ilPageLayout($id);
			$pg_obj->delete();	 		
	 	}
  
	 	$this->ctrl->redirect($this, "viewPageLayouts");
	}
	
	function addPageLayoutObject($a_form = null)
	{
    	global $ilTabs;
   
		$ilTabs->setTabActive('page_layouts');
		
		if(!$a_form)
		{
			$a_form = $this->initAddPageLayoutForm();
		}

    	$this->tpl->setContent($a_form->getHTML());
	}
	
	function initAddPageLayoutForm()
	{
		global $lng, $ilCtrl;
		
		$lng->loadLanguageModule("content");
		
    	include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
    	$form_gui = new ilPropertyFormGUI();
    	$form_gui->setFormAction($ilCtrl->getFormAction($this));
    	$form_gui->setTitle($lng->txt("sty_create_pgl"));
   
    	include_once("Services/Form/classes/class.ilRadioMatrixInputGUI.php");
   
   
    	$title_input = new ilTextInputGUI($lng->txt("title"),"pgl_title");
    	$title_input->setSize(50);
    	$title_input->setMaxLength(128);
    	$title_input->setValue($this->layout_object->title);
    	$title_input->setTitle($lng->txt("title"));
    	$title_input->setRequired(true);
   
    	$desc_input = new ilTextAreaInputGUI($lng->txt("description"),"pgl_desc");
    	$desc_input->setValue($this->layout_object->description);
    	$desc_input->setRows(3);
    	$desc_input->setCols(37);
    	
    	// special page? 
    	$options = array(
    		"0" => $lng->txt("cont_layout_template"),
    		"1" => $lng->txt("cont_special_page"),
    		);
    	$si = new ilSelectInputGUI($this->lng->txt("type"), "special_page");
    	$si->setOptions($options);
		
		// modules
		$mods = new ilCheckboxGroupInputGUI($this->lng->txt("modules"), "module");
		// $mods->setRequired(true);
		foreach(ilPageLayout::getAvailableModules() as $mod_id => $mod_caption)
		{
			$mod = new ilCheckboxOption($mod_caption, $mod_id);
			$mods->addOption($mod);			
		}

		$ttype_input = new ilSelectInputGUI($lng->txt("sty_based_on"), "pgl_template");
		
		$arr_templates = ilPageLayout::getLayouts();
		$arr_templates1 = ilPageLayout::getLayouts(false, true);
		foreach ($arr_templates1 as $v)
		{
			$arr_templates[] = $v;
		}
		
		$options = array();
		$options['-1'] = $lng->txt("none");
		
		foreach ($arr_templates as $templ) {
			$templ->readObject();
			$key = $templ->getId();
			$value = $templ->getTitle();
			$options[$key] = $value;
		}
		
		$ttype_input->setOptions($options);
		$ttype_input->setValue(-1);
		$ttype_input->setRequired(true);
   
    	$desc_input->setTitle($lng->txt("description"));
    	$desc_input->setRequired(false);
   
    	$form_gui->addItem($title_input);
    	$form_gui->addItem($desc_input);
    	$form_gui->addItem($si);
    	$form_gui->addItem($mods);
    	$form_gui->addItem($ttype_input);

   
    	$form_gui->addCommandButton("createPg", $lng->txt("save"));
		$form_gui->addCommandButton("cancelCreate", $lng->txt("cancel"));
		
		return $form_gui;		
	}
	

	function createPgObject()
	{
		global $ilCtrl;
		
		$form_gui = $this->initAddPageLayoutForm();
		if(!$form_gui->checkInput())
		{
			$form_gui->setValuesByPost();
			return $this->addPageLayoutObject($form_gui);			
		}
				
		//create Page-Layout-Object first
		$pg_object = new ilPageLayout();
		$pg_object->setTitle($form_gui->getInput('pgl_title'));
		$pg_object->setDescription($form_gui->getInput('pgl_desc'));
		$pg_object->setSpecialPage($form_gui->getInput('special_page'));
		$pg_object->setModules($form_gui->getInput('module'));		
		$pg_object->update();
		
		include_once("./Services/COPage/classes/class.ilPageObject.php");
		
		//create Page
		if(!is_object($pg_content))
		{
			$this->pg_content =& new ilPageObject($this->type);
		}
		
		$this->pg_content->setId($pg_object->getId());
		
		$tmpl = $form_gui->getInput('pgl_template');
		if ($tmpl != "-1") 
		{
			$layout_obj = new ilPageLayout($tmpl);
			$this->pg_content->setXMLContent($layout_obj->getXMLContent());
			$this->pg_content->create(false);
		} 
		else 
		{
			$this->pg_content->create(false);
		}
		
		$ilCtrl->setParameterByClass("ilpagelayoutgui", "obj_id", $pg_object->getId());
		$ilCtrl->redirectByClass("ilpagelayoutgui", "edit");
	}
	
	function cancelCreateObject() {
		$this->viewPageLayoutsObject();
	}
	
	function editPgObject()
	{
		global $ilCtrl, $rbacsystem;
		
		if (!$rbacsystem->checkAccess("write",$this->object->getRefId()))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}

		$ilCtrl->setCmdClass("ilpagelayoutgui");
		$ilCtrl->setCmd("edit");
		$this->executeCommand();
	}
	
	
	function setTabs()
	{
		echo "settings_setTabs";
	}
	
	function getAdminTabs(&$tabs_gui)
	{
		$this->getTabs($tabs_gui);
	}
		
	/**
	* get tabs
	* @access	public
	* @param	object	tabs gui object
	*/
	function getTabs(&$tabs_gui)
	{
		global $rbacsystem, $lng, $ilTabs;
		
		if ($this->peditor_active) {
			$tabs_gui->setBackTarget($this->lng->txt("page_layouts"),
			$this->ctrl->getLinkTarget($this, "viewPageLayouts"));
		}
			
		if ($rbacsystem->checkAccess("visible,read",$this->object->getRefId()) && !$this->peditor_active)
		{
			$tabs_gui->addTarget("settings",
				$this->ctrl->getLinkTarget($this, "editBasicSettings"), array("editBasicSettings","", "view"), "", "");

			$tabs_gui->addTarget("system_styles",
				$this->ctrl->getLinkTarget($this, "editSystemStyles"), "editSystemStyles", "", "");
				
			$tabs_gui->addTarget("content_styles",
				$this->ctrl->getLinkTarget($this, "editContentStyles"), "editContentStyles", "", "");
				
			$tabs_gui->addTarget("page_layouts",
				$this->ctrl->getLinkTarget($this, "viewPageLayouts"), "viewPageLayouts", "", "");
				
		}
		
		
		if ($rbacsystem->checkAccess('edit_permission',$this->object->getRefId()) && !$this->peditor_active)
		{
			$tabs_gui->addTarget("perm_settings",
				$this->ctrl->getLinkTargetByClass(array(get_class($this),'ilpermissiongui'), "perm"), array("perm","info","owner"), 'ilpermissiongui');
		}
	}

	/**
	 * Create new style
	 */
	function createStyleObject()
	{
		global $ilCtrl;

		$ilCtrl->setParameter($this, "new_type", "sty");
		$ilCtrl->redirect($this, "create");
	}

	/**
	 * Save page layout types
	 */
	function savePageLayoutTypesObject()
	{
		global $lng, $ilCtrl;

		include_once("./Services/Style/classes/class.ilPageLayout.php");

		if (is_array($_POST["type"]))
		{
			foreach($_POST["type"] as $id => $t)
			{
				if ($id > 0)
				{
					$l = new ilPageLayout($id);
					$l->readObject();
					$l->setSpecialPage($t);		
					if(is_array($_POST["module"][$id]))
					{
						$l->setModules(array_keys($_POST["module"][$id]));
					}
					else
					{
						$l->setModules();
					}
					$l->update();
				}
			}						
			
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"));
		}

		$ilCtrl->redirect($this, "viewPageLayouts");
	}


	/**
	 * Export page layout template object
	 */
	function exportLayoutObject()
	{
		include_once("./Services/Export/classes/class.ilExport.php");
		$exp = new ilExport();
		
		$tmpdir = ilUtil::ilTempnam();
		ilUtil::makeDir($tmpdir);

		$succ = $exp->exportEntity("pgtp", (int) $_GET["layout_id"], "4.2.0",
			"Services/COPage", "Title", $tmpdir);
		
		if ($succ["success"])
		{
			ilUtil::deliverFile($succ["directory"]."/".$succ["file"], $succ["file"],
				"", false, false, false);
		}
		if (is_file($succ["directory"]."/".$succ["file"]))
		{
			unlink($succ["directory"]."/".$succ["file"]);
		}
		if (is_dir($succ["directory"]))
		{
			unlink($succ["directory"]);
		}
	}
	
	/**
	 * Import page layout
	 */
	function importPageLayoutFormObject()
	{
		global $tpl, $ilTabs;
		
		$ilTabs->setTabActive('page_layouts');
		$form = $this->initPageLayoutImportForm();
		$tpl->setContent($form->getHTML());
	}
	
	/**
	 * Init page layout import form.
	 */
	public function initPageLayoutImportForm()
	{
		global $lng, $ilCtrl;
	
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		
		// template file
		$fi = new ilFileInputGUI($lng->txt("file"), "file");
		$fi->setSuffixes(array("zip"));
		$fi->setRequired(true);
		$form->addItem($fi);
		
		$form->addCommandButton("importPageLayout", $lng->txt("import"));
		$form->addCommandButton("viewPageLayouts", $lng->txt("cancel"));
	                
		$form->setTitle($lng->txt("sty_import_page_layout"));
		$form->setFormAction($ilCtrl->getFormAction($this));
		
		return $form;
	 }
	 
	 /**
	  * Import page layout
	  */
	 public function importPageLayoutObject()
	 {
	 	global $tpl, $lng, $ilCtrl, $ilTabs;
	 
	 	$form = $this->initPageLayoutImportForm();
	 	if ($form->checkInput())
	 	{
	 		include_once("./Services/Style/classes/class.ilPageLayout.php");
	 		$pg = ilPageLayout::import($_FILES["file"]["name"], $_FILES["file"]["tmp_name"]);
	 		if ($pg > 0)
	 		{
	 			ilUtil::sendSuccess($lng->txt("sty_imported_layout"), true);
	 		}
	 		$ilCtrl->redirect($this, "viewPageLayouts");
	 	}
	 	else
	 	{
	 		$ilTabs->setTabActive('page_layouts');
	 		$form->setValuesByPost();
	 		$tpl->setContent($form->getHtml());
	 	}
	 }
	 
	 /**
	  * Assign styles to cats
	  *
	  * @param
	  * @return
	  */
	 function assignStylesToCatsObject()
	 {
	 	global $ilToolbar, $ilCtrl, $tpl, $lng, $rbacsystem;
	 	
	 	$ilCtrl->setParameter($this, "style_id", urlencode($_GET["style_id"]));
	 	
	 	if (!$rbacsystem->checkAccess("write", $this->object->getRefId()))
		{
			return;
		}
	 	
	 	$all_styles = ilStyleDefinition::getAllSkinStyles();
	 	$sel_style = $all_styles[$_GET["style_id"]];

	 	$options = array();
	 	if (is_array($sel_style["substyle"]))
	 	{
	 		foreach ($sel_style["substyle"] as $subst)
	 		{
	 			$options[$subst["id"]] = $subst["name"];
	 		}
	 	}
	 	
	 	// substyle
	 	include_once("./Services/Form/classes/class.ilSelectInputGUI.php");
	 	$si = new ilSelectInputGUI($this->lng->txt("sty_substyle"), "substyle");
	 	$si->setOptions($options);
	 	$ilToolbar->addInputItem($si, true);
	 	
	 	$ilToolbar->addFormButton($lng->txt("sty_add_assignment"), "addStyleCatAssignment");
	 	$ilToolbar->setFormAction($ilCtrl->getFormAction($this));
	 	
	 	include_once("./Services/Style/classes/class.ilSysStyleCatAssignmentTableGUI.php");
	 	$tab = new ilSysStyleCatAssignmentTableGUI($this, "assignStylesToCats");
	 	
	 	$tpl->setContent($tab->getHTML());
	 }
	 
	 
	/**
	 * Add style category assignment
	 *
	 * @param
	 * @return
	 */
	function addStyleCatAssignmentObject()
	{
		global $ilCtrl, $ilTabs, $lng, $tree, $tpl, $rbacsystem;
		
		if (!$rbacsystem->checkAccess("write", $this->object->getRefId()))
		{
			return;
		}

		$ilCtrl->setParameter($this, "style_id", urlencode($_GET["style_id"]));
		$ilCtrl->setParameter($this, "substyle", urlencode($_REQUEST["substyle"]));
		
		include_once 'Services/Search/classes/class.ilSearchRootSelector.php';
		$exp = new ilSearchRootSelector(
			$ilCtrl->getLinkTarget($this,'addStyleCatAssignment'));
		$exp->setExpand($_GET["search_root_expand"] ? $_GET["search_root_expand"] : $tree->readRootId());
		$exp->setExpandTarget($ilCtrl->getLinkTarget($this,'addStyleCatAssignment'));
		$exp->setTargetClass(get_class($this));
		$exp->setCmd('saveStyleCatAssignment');
		$exp->setClickableTypes(array("cat"));
		
		// build html-output
		$exp->setOutput(0);
		$tpl->setContent($exp->getOutput());
	}
	
	
	/**
	 * Save style category assignment
	 *
	 * @param
	 * @return
	 */
	function saveStyleCatAssignmentObject()
	{
		global $lng, $ilCtrl, $rbacsystem;
		
		if (!$rbacsystem->checkAccess("write", $this->object->getRefId()))
		{
			return;
		}

		$ilCtrl->setParameter($this, "style_id", urlencode($_GET["style_id"]));
		
		$style_arr = explode(":", $_GET["style_id"]);
		ilStyleDefinition::writeSystemStyleCategoryAssignment($style_arr[0], $style_arr[1],
			$_GET["substyle"], $_GET["root_id"]);
		ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);

		$ilCtrl->redirect($this, "assignStylesToCats");
	}
	
	/**
	 * Delete system style to category assignments
	 */
	function deleteSysStyleCatAssignmentsObject()
	{
		global $ilCtrl, $lng, $rbacsystem;
		
		if (!$rbacsystem->checkAccess("write", $this->object->getRefId()))
		{
			return;
		}

		$ilCtrl->setParameter($this, "style_id", urlencode($_GET["style_id"]));
		$style_arr = explode(":", $_GET["style_id"]);
		if (is_array($_POST["id"]))
		{
			foreach ($_POST["id"] as $id)
			{
				$id_arr = explode(":", $id);
				ilStyleDefinition::deleteSystemStyleCategoryAssignment($style_arr[0], $style_arr[1],
					$id_arr[0], $id_arr[1]);
			}
			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
		}
		
		$ilCtrl->redirect($this, "assignStylesToCats");
	}
	
}
?>
