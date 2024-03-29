<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
* Class ilObjectGUI
* Basic methods of all Output classes
*
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id: class.ilObjectGUI.php 47139 2014-01-09 12:23:07Z smeyer $
*
*/
class ilObjectGUI
{
	const COPY_WIZARD_NEEDS_PAGE = 1;
	
	
	
	/**
	* ilias object
	* @var		object ilias
	* @access	private
	*/
	var $ilias;

	/**
	* object Definition Object
	* @var		object ilias
	* @access	private
	*/
	var $objDefinition;

	/**
	* template object
	* @var		object ilias
	* @access	private
	*/
	var $tpl;

	/**
	* tree object
	* @var		object ilias
	* @access	private
	*/
	var $tree;

	/**
	* language object
	* @var		object language (of ilObject)
	* @access	private
	*/
	var $lng;

	/**
	* output data
	* @var		data array
	* @access	private
	*/
	var $data;

	/**
	* object
	* @var          object
	* @access       private
	*/
	var $object;
	var $ref_id;
	var $obj_id;
	var $maxcount;			// contains number of child objects
	var $formaction;		// special formation (array "cmd" => "formaction")
	var $return_location;	// special return location (array "cmd" => "location")
	var $target_frame;	// special target frame (array "cmd" => "location")

	var $tab_target_script;
	var $actions;
	var $sub_objects;
	var $omit_locator = false;

	const CFORM_NEW = 1;
	const CFORM_IMPORT = 2;
	const CFORM_CLONE = 3;

	/**
	* Constructor
	* @access	public
	* @param	array	??
	* @param	integer	object id
	* @param	boolean	call be reference
	*/
	function ilObjectGUI($a_data, $a_id = 0, $a_call_by_reference = true, $a_prepare_output = true)
	{
		global $ilias, $objDefinition, $tpl, $tree, $ilCtrl, $ilErr, $lng, $ilTabs;

		$this->tabs_gui =& $ilTabs;

		if (!isset($ilErr))
		{
			$ilErr = new ilErrorHandling();
			$ilErr->setErrorHandling(PEAR_ERROR_CALLBACK,array($ilErr,'errorHandler'));
		}
		else
		{
			$this->ilErr =& $ilErr;
		}

		$this->ilias =& $ilias;
		$this->objDefinition =& $objDefinition;
		$this->tpl =& $tpl;
		$this->html = "";
		$this->ctrl =& $ilCtrl;

		$params = array("ref_id");

		if (!$a_call_by_reference)
		{
			$params = array("ref_id","obj_id");
		}

		$this->ctrl->saveParameter($this, $params);

		$this->lng =& $lng;
		$this->tree =& $tree;
		$this->formaction = array();
		$this->return_location = array();
		$this->target_frame = array();
		$this->actions = "";
		$this->sub_objects = "";

		$this->data = $a_data;
		$this->id = $a_id;
		$this->call_by_reference = $a_call_by_reference;
		$this->prepare_output = $a_prepare_output;
		$this->creation_mode = false;

		$this->ref_id = ($this->call_by_reference) ? $this->id : $_GET["ref_id"];
		$this->obj_id = ($this->call_by_reference) ? $_GET["obj_id"] : $this->id;

		if ($this->id != 0)
		{
			$this->link_params = "ref_id=".$this->ref_id;
		}

		// get the object
		$this->assignObject();
		
		// set context
		if (is_object($this->object))
		{
			if ($this->call_by_reference && $this->ref_id == $_GET["ref_id"])
			{
				$this->ctrl->setContext($this->object->getId(), 
					$this->object->getType());
			}
		}

		// use global $lng instead, when creating new objects object is not available
		//$this->lng =& $this->object->lng;

		//prepare output
		if ($a_prepare_output)
		{
			$this->prepareOutput();
		}
	}
	
	
	/**
	* execute command
	*/
	function &executeCommand()
	{
		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		switch($next_class)
		{
			default:
				$this->prepareOutput();
				if(!$cmd)
				{
					$cmd = "view";
				}
				$cmd .= "Object";
				$this->$cmd();
					
				break;
		}

		return true;
	}


	/**
	* determines wether objects are referenced or not (got ref ids or not)
	*/
	public function withReferences()
	{
		return $this->call_by_reference;
	}
	
	/**
	* if true, a creation screen is displayed
	* the current $_GET[ref_id] don't belong
	* to the current class!
	* the mode is determined in ilrepositorygui
	*/
	public function setCreationMode($a_mode = true)
	{
		$this->creation_mode = $a_mode;
	}
	
	/**
	* get creation mode
	*/
	public function getCreationMode()
	{
		return $this->creation_mode;
	}

	protected function assignObject()
	{
		// TODO: it seems that we always have to pass only the ref_id
//echo "<br>ilObjectGUIassign:".get_class($this).":".$this->id.":<br>";
		if ($this->id != 0)
		{
			if ($this->call_by_reference)
			{
				$this->object = ilObjectFactory::getInstanceByRefId($this->id);
			}
			else
			{
				$this->object = ilObjectFactory::getInstanceByObjId($this->id);
			}
		}
	}

	/**
	* prepare output
	*/
	protected function prepareOutput()
	{
		global $ilLocator, $tpl, $ilUser;

		$this->tpl->getStandardTemplate();
		// administration prepare output
		if (strtolower($_GET["baseClass"]) == "iladministrationgui")
		{
			$this->addAdminLocatorItems();
			$tpl->setLocator();

//			ilUtil::sendInfo();
			ilUtil::infoPanel();

			$this->setTitleAndDescription();

			if ($this->getCreationMode() != true)
			{
				$this->setAdminTabs();
			}
			
			return false;
		}
		// set locator
		$this->setLocator();
		// catch feedback message
//		ilUtil::sendInfo();
		ilUtil::infoPanel();

		// in creation mode (parent) object and gui object
		// do not fit
		if ($this->getCreationMode() == true)
		{
			// repository vs. workspace
			if($this->call_by_reference)
			{
				// get gui class of parent and call their title and description method
				$obj_type = ilObject::_lookupType($_GET["ref_id"],true);
				$class_name = $this->objDefinition->getClassName($obj_type);
				$class = strtolower("ilObj".$class_name."GUI");
				$class_path = $this->ctrl->lookupClassPath($class);
				include_once($class_path);
				$class_name = $this->ctrl->getClassForClasspath($class_path);
//echo "<br>instantiating parent for title and description";
				$this->parent_gui_obj = new $class_name("", $_GET["ref_id"], true, false);
				// the next line prevents the header action menu being shown
				$this->parent_gui_obj->setCreationMode(true);
				$this->parent_gui_obj->setTitleAndDescription();
			}
		}
		else
		{
			// set title and description and title icon
			$this->setTitleAndDescription();
	
			// set tabs
			$this->setTabs();

			// BEGIN WebDAV: Display Mount Webfolder icon.
			require_once 'Services/WebDAV/classes/class.ilDAVServer.php';
			if (ilDAVServer::_isActive() && 
				$ilUser->getId() != ANONYMOUS_USER_ID)
			{
				$this->showMountWebfolderIcon();
			}
			// END WebDAV: Display Mount Webfolder icon.
		}
		
		return true;
	}
	
	/**
	* called by prepare output
	*/
	protected function setTitleAndDescription()
	{
		if (!is_object($this->object))
		{
			if ((int) $_REQUEST["crtptrefid"] > 0)
			{
				$cr_obj_id = ilObject::_lookupObjId((int) $_REQUEST["crtcb"]);
				$this->tpl->setTitle(ilObject::_lookupTitle($cr_obj_id));
				$this->tpl->setTitleIcon(ilObject::_getIcon($cr_obj_id));
			}
			return;
		}
		$this->tpl->setTitle($this->object->getPresentationTitle());
		$this->tpl->setDescription($this->object->getLongDescription());
			
		if (strtolower($_GET["baseClass"]) == "iladministrationgui")
		{
			// alt text would be same as heading -> empty alt text
			$this->tpl->setTitleIcon(ilUtil::getImagePath("icon_".$this->object->getType()."_b.png"),
				"");
		}
		else
		{
			$this->tpl->setTitleIcon(ilUtil::getImagePath("icon_".$this->object->getType()."_b.png"),
				$this->lng->txt("obj_" . $this->object->getType()));
		}

		include_once './Services/Object/classes/class.ilObjectListGUIFactory.php';
		$lgui = ilObjectListGUIFactory::_getListGUIByType($this->object->getType());
		$lgui->initItem($this->object->getRefId(), $this->object->getId());
		$this->tpl->setAlertProperties($lgui->getAlertProperties());		
	}
	
	/**
	 * Add header action menu
	 * 
	 * @param string $a_sub_type
	 * @param int $a_sub_id
	 * @return ilObjectListGUI
	 */
	protected function initHeaderAction($a_sub_type = null, $a_sub_id = null)
	{
		global $ilAccess; 
		
		if(!$this->creation_mode && $this->object)
		{
			include_once "Services/Object/classes/class.ilCommonActionDispatcherGUI.php";
			$dispatcher = new ilCommonActionDispatcherGUI(ilCommonActionDispatcherGUI::TYPE_REPOSITORY, 
				$ilAccess, $this->object->getType(), $this->ref_id, $this->object->getId());
			
			$dispatcher->setSubObject($a_sub_type, $a_sub_id);
			
			include_once "Services/Object/classes/class.ilObjectListGUI.php";
				ilObjectListGUI::prepareJSLinks($this->ctrl->getLinkTarget($this, "redrawHeaderAction", "", true), 
				$this->ctrl->getLinkTargetByClass(array("ilcommonactiondispatchergui", "ilnotegui"), "", "", true, false), 
				$this->ctrl->getLinkTargetByClass(array("ilcommonactiondispatchergui", "iltagginggui"), "", "", true, false));
			
			$lg = $dispatcher->initHeaderAction();
			
			if (is_object($lg))
			{				
				// to enable add to desktop / remove from desktop
				if($this instanceof ilDesktopItemHandling)
				{
					$lg->setContainerObject($this);
				}			
				
				// comments settings are always on (for the repository)
				// should only be shown if active or permission to toggle
				include_once "Services/Notes/classes/class.ilNote.php";
				if($ilAccess->checkAccess("write", "", $this->ref_id) ||
					$ilAccess->checkAccess("edit_permissions", "", $this->ref_id) ||
					ilNote::commentsActivated($this->object->getId(), 0, $this->object->getType()))
				{
					$lg->enableComments(true);
				}
				
				$lg->enableNotes(true);
				$lg->enableTags(true);
			}
			
			return $lg;
		}
	}
	
	/**
	 * Insert header action into main template
	 * 
	 * @param ilObjectListGUI $a_list_gui 
	 */
	protected function insertHeaderAction($a_list_gui)
	{
		if(is_object($a_list_gui))
		{
			$this->tpl->setHeaderActionMenu($a_list_gui->getHeaderAction());
		}
	}
	
	/**
	 * Add header action menu
	 */
	protected function addHeaderAction()
	{	
		$this->insertHeaderAction($this->initHeaderAction());
	}

	/**
	 * Ajax call: redraw action header only
	 */
	protected function redrawHeaderActionObject()
	{
		$lg = $this->initHeaderAction();
		echo $lg->getHeaderAction();
		exit;
	}
	
	// BEGIN WebDAV: Show Mount Webfolder Icon.
	final private function showMountWebfolderIcon()
	{
		global $tree, $tpl, $objDefinition;

		if ($this->object->getRefId() == "")
		{
			return;
		}

		$tpl->setMountWebfolderIcon($this->object->getRefId());
	}
	// END WebDAV: Show Mount Webfolder Icon.


	/**
	* set admin tabs
	* @access	public
	*/
	protected function setTabs()
	{
		$this->getTabs($this->tabs_gui);
	}

	/**
	* set admin tabs
	* @access	public
	*/
	protected final function setAdminTabs()
	{
		$this->getAdminTabs($this->tabs_gui);
	}

	/**
	* administration tabs show only permissions and trash folder
	*/
	function getAdminTabs(&$tabs_gui)
	{
		global $tree;

		if ($_GET["admin_mode"] == "repository")
		{
			$this->ctrl->setParameterByClass("iladministrationgui", "admin_mode", "settings");
			$tabs_gui->setBackTarget($this->lng->txt("administration"),
				$this->ctrl->getLinkTargetByClass("iladministrationgui", "frameset"),
				ilFrameTargetInfo::_getFrame("MainContent"));
			$this->ctrl->setParameterByClass("iladministrationgui", "admin_mode", "repository");
		}
		
		if ($this->checkPermissionBool("visible,read"))
		{
			$tabs_gui->addTarget("view",
				$this->ctrl->getLinkTarget($this, "view"), array("", "view"), get_class($this));

		}
		
		if ($this->checkPermissionBool("edit_permission"))
		{
			$tabs_gui->addTarget("perm_settings",
				$this->ctrl->getLinkTargetByClass(array(get_class($this),'ilpermissiongui'), "perm"), "", "ilpermissiongui");
		}
			
		if ($tree->getSavedNodeData($this->object->getRefId()))
		{
			$tabs_gui->addTarget("trash",
				$this->ctrl->getLinkTarget($this, "trash"), "trash", get_class($this));
		}
	}


	function getHTML()
	{
		return $this->html;
	}


	/**
	* set possible actions for objects in list. if actions are set
	* via this method, the values of objects.xml are ignored.
	*
	* @param	array		$a_actions		array with $command => $lang_var pairs
	*/
	final private function setActions($a_actions = "")
	{
		if (is_array($a_actions))
		{
			foreach ($a_actions as $name => $lng)
			{
				$this->actions[$name] = array("name" => $name, "lng" => $lng);
			}
		}
		else
		{
			$this->actions = "";
		}
	}

	/**
	* set possible subobjects for this object. if subobjects are set
	* via this method, the values of objects.xml are ignored.
	*
	* @param	array		$a_actions		array with $command => $lang_var pairs
	*/
	final private function setSubObjects($a_sub_objects = "")
	{
		if (is_array($a_sub_objects))
		{
			foreach ($a_sub_objects as $name => $options)
			{
				$this->sub_objects[$name] = array("name" => $name, "max" => $options["max"]);
			}
		}
		else
		{
			$this->sub_objects = "";
		}
	}

	/**
	* set Locator
	*
	* @param	object	tree object
	* @param	integer	reference id
	* @param	scriptanme that is used for linking;
	* @access	public
	*/
	protected function setLocator()
	{
		global $ilLocator, $tpl;
		
		if ($this->omit_locator)
		{
			return;
		}

		// repository vs. workspace
		if($this->call_by_reference)
		{
			// todo: admin workaround
			// in the future, objectgui classes should not be called in
			// admin section anymore (rbac/trash handling in own classes)
			$ref_id = ($_GET["ref_id"] != "")
				? $_GET["ref_id"]
				: $this->object->getRefId();
			$ilLocator->addRepositoryItems($ref_id);
		}
		
		if(!$this->creation_mode)
		{
			$this->addLocatorItems();
		}
		
		// not so nice workaround: todo: handle $ilLocator as tabs in ilTemplate
		if ($_GET["admin_mode"] == "" &&
			strtolower($this->ctrl->getCmdClass()) == "ilobjrolegui")
		{
			$this->ctrl->setParameterByClass("ilobjrolegui",
				"rolf_ref_id", $_GET["rolf_ref_id"]);
			$this->ctrl->setParameterByClass("ilobjrolegui",
				"obj_id", $_GET["obj_id"]);
			$ilLocator->addItem($this->lng->txt("role"),
				$this->ctrl->getLinkTargetByClass(array("ilpermissiongui",
					"ilobjrolegui"), "perm"));
		}

		$tpl->setLocator();
	}
	
	/**
	* should be overwritten to add object specific items
	* (repository items are preloaded)
	*/
	protected function addLocatorItems()
	{
	}
	
	protected function omitLocator($a_omit = true)
	{
		$this->omit_locator = $a_omit;
	}

	/**
	* should be overwritten to add object specific items
	* (repository items are preloaded)
	*/
	protected function addAdminLocatorItems()
	{
		global $ilLocator;
		
		if ($_GET["admin_mode"] == "settings")	// system settings
		{		
			$this->ctrl->setParameterByClass("iladministrationgui",
				"ref_id", SYSTEM_FOLDER_ID);
			$ilLocator->addItem($this->lng->txt("administration"),
				$this->ctrl->getLinkTargetByClass("iladministrationgui", "frameset"),
				ilFrameTargetInfo::_getFrame("MainContent"));
			if ($this->object->getRefId() != SYSTEM_FOLDER_ID)
			{
				$ilLocator->addItem($this->object->getTitle(),
					$this->ctrl->getLinkTarget($this, "view"));							
			}
			$this->ctrl->setParameterByClass("iladministrationgui",
				"ref_id", $this->object->getRefId());
		}
		else							// repository administration
		{
			$this->ctrl->setParameterByClass("iladministrationgui",
				"ref_id", "");
			$this->ctrl->setParameterByClass("iladministrationgui",
				"admin_mode", "settings");
			//$ilLocator->addItem($this->lng->txt("administration"),
			//	$this->ctrl->getLinkTargetByClass("iladministrationgui", "frameset"),
			//	ilFrameTargetInfo::_getFrame("MainContent"));
			$this->ctrl->clearParametersByClass("iladministrationgui");
			$ilLocator->addAdministrationItems();
		}

	}

	/**
	* Get objects back from trash
	*/
	public function undeleteObject()
	{
		include_once("./Services/Repository/classes/class.ilRepUtilGUI.php");
		$ru = new ilRepUtilGUI($this);
		$ru->restoreObjects($_GET["ref_id"], $_POST["trash_id"]);
		$this->ctrl->redirect($this, "trash");
	}

	/**
	* confirmed deletion of object -> objects are moved to trash or deleted
	* immediately, if trash is disabled
	*/
	public function confirmedDeleteObject()
	{
		global $ilSetting, $lng;
		
		if(isset($_POST["mref_id"]))
		{
			$_SESSION["saved_post"] = array_unique(array_merge($_SESSION["saved_post"], $_POST["mref_id"]));
		}
		
		include_once("./Services/Repository/classes/class.ilRepUtilGUI.php");
		$ru = new ilRepUtilGUI($this);
		$ru->deleteObjects($_GET["ref_id"], ilSession::get("saved_post"));
		ilSession::clear("saved_post");
		$this->ctrl->returnToParent($this);
	}

	/**
	* cancel deletion of object
	*
	* @access	public
	*/
	public function cancelDeleteObject()
	{
		ilSession::clear("saved_post");
		$this->ctrl->returnToParent($this);
	}

	/**
	* remove objects from trash bin and all entries therefore every object needs a specific deleteObject() method
	*
	* @access	public
	*/
	public function removeFromSystemObject()
	{
		global $log, $ilAppEventHandler, $lng;
		
		include_once("./Services/Repository/classes/class.ilRepUtilGUI.php");
		$ru = new ilRepUtilGUI($this);
		$ru->removeObjectsFromSystem($_POST["trash_id"]);
		$this->ctrl->redirect($this, "trash");
	}

	/**
	* cancel action and go back to previous page
	* @access	public
	*
	*/
	public function cancelObject($in_rep = false)
	{
		ilSession::clear("saved_post");
		$this->ctrl->returnToParent($this);
	}

	/**
	* create new object form
	*
	* @access	public
	*/
	public function createObject()
	{
		global $tpl, $ilErr;

		$new_type = $_REQUEST["new_type"];

		
		// add new object to custom parent container
		$this->ctrl->saveParameter($this, "crtptrefid");
		// use forced callback after object creation
		$this->ctrl->saveParameter($this, "crtcb");
		
		if (!$this->checkPermissionBool("create", "", $new_type))
		{
			$ilErr->raiseError($this->lng->txt("permission_denied"),$ilErr->MESSAGE);
		}
		else
		{
			$this->lng->loadLanguageModule($new_type);
			$this->ctrl->setParameter($this, "new_type", $new_type);
			
			$forms = $this->initCreationForms($new_type);
			
			// copy form validation error: do not show other creation forms
			if($_GET["cpfl"] && isset($forms[self::CFORM_CLONE]))
			{
				$forms = array(self::CFORM_CLONE => $forms[self::CFORM_CLONE]);
			}
			
			$tpl->setContent($this->getCreationFormsHTML($forms));
		}
	}

	/**
	 * Init creation froms
	 *
	 * this will create the default creation forms: new, import, clone
	 *
	 * @param	string	$a_new_type
	 * @return	array
	 */
	protected function initCreationForms($a_new_type)
	{
		$forms = array(
			self::CFORM_NEW => $this->initCreateForm($a_new_type),
			self::CFORM_IMPORT => $this->initImportForm($a_new_type),
			self::CFORM_CLONE => $this->fillCloneTemplate(null, $a_new_type)
			);
		
		return $forms;
	}

	/**
	 * Get HTML for creation forms (accordion)
	 *
	 * @param array $a_forms
	 */
	final protected function getCreationFormsHTML(array $a_forms)
	{
		global $tpl;
		
		// sanity check
		foreach($a_forms as $id => $form)
		{
			if(!$form instanceof ilPropertyFormGUI)
			{
				unset($a_forms[$id]);
			}
		}
		
		// no accordion if there is just one form
		if(sizeof($a_forms) == 1)
		{
			$a_forms = array_shift($a_forms);
			if (is_object($a_forms) && get_class($a_forms) == "ilPropertyFormGUI")
			{
				return $a_forms->getHTML();
			}
		}
		else
		{
			include_once("./Services/Accordion/classes/class.ilAccordionGUI.php");

			$acc = new ilAccordionGUI();
			$acc->setBehaviour(ilAccordionGUI::FIRST_OPEN);
			$cnt = 1;
			foreach ($a_forms as $form_type => $cf)
			{				
				$htpl = new ilTemplate("tpl.creation_acc_head.html", true, true, "Services/Object");
//					$htpl->setVariable("IMG_ARROW", ilUtil::getImagePath("accordion_arrow.png"));

				// using custom form titles (used for repository plugins)
				$form_title = "";
				if(method_exists($this, "getCreationFormTitle"))
				{
					$form_title = $this->getCreationFormTitle($form_type);
				}
				if(!$form_title)
				{
					$form_title = $cf->getTitle();
				}

				// move title from form to accordion
				$htpl->setVariable("TITLE", $this->lng->txt("option")." ".$cnt.": ".
					$form_title);
				$cf->setTitle(null);
				$cf->setTitleIcon(null);
				$cf->setTableWidth("100%");

				$acc->addItem($htpl->get(), $cf->getHTML());

				$cnt++;				
			}

			return "<div class='ilCreationFormSection'>".$acc->getHTML()."</div>";
		}
	}

	/**
	 * Init object creation form
	 *
	 * @param	string	$a_new_type
	 * @return	ilPropertyFormGUI
	 */
	protected function initCreateForm($a_new_type)
	{
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setTarget("_top");
		$form->setFormAction($this->ctrl->getFormAction($this, "save"));
		$form->setTitle($this->lng->txt($a_new_type."_new"));

		// title
		$ti = new ilTextInputGUI($this->lng->txt("title"), "title");
		$ti->setMaxLength(128);
		$ti->setSize(40);
		$ti->setRequired(true);
		$form->addItem($ti);

		// description
		$ta = new ilTextAreaInputGUI($this->lng->txt("description"), "desc");
		$ta->setCols(40);
		$ta->setRows(2);
		$form->addItem($ta);

		$form = $this->initDidacticTemplate($form);

		$form->addCommandButton("save", $this->lng->txt($a_new_type."_add"));
		$form->addCommandButton("cancel", $this->lng->txt("cancel"));

		return $form;
	}

	/**
	 * Show didactic template types
	 * @param ilPropertyFormGUI $form
	 * @return ilPropertyFormGUI $form
	 */
	protected function initDidacticTemplate(ilPropertyFormGUI $form)
	{
		global $lng;
		
		$lng->loadLanguageModule('didactic');
					
		$options = array();
		$options['dtpl_0'] = array($this->lng->txt('didactic_default_type'),
			sprintf(
				$this->lng->txt('didactic_default_type_info'),
				$this->lng->txt('objs_'.$this->type)
			));
		
		include_once './Services/DidacticTemplate/classes/class.ilDidacticTemplateSettings.php';
		$templates = ilDidacticTemplateSettings::getInstanceByObjectType($this->type)->getTemplates();
		if($templates)
		{
			foreach($templates as $template)
			{
				$options["dtpl_".$template->getId()] = array($template->getTitle(),
					$template->getDescription());			
			}
		}
		
		$this->addDidacticTemplateOptions($options);
		
		if(sizeof($options) > 1)
 		{
			$type = new ilRadioGroupInputGUI(
				$this->lng->txt('type'),
				'didactic_type'
			);
			// workaround for containers in edit mode
			if(!$this->getCreationMode())
			{
				include_once './Services/DidacticTemplate/classes/class.ilDidacticTemplateObjSettings.php';
				$type->setValue(
					'dtpl_'.ilDidacticTemplateObjSettings::lookupTemplateId($this->object->getRefId())
				);
			}
			else
			{
				$type->setValue('dtpl_0');
			}
			$form->addItem($type);		

			ilUtil::sortArray($options, 0);
			foreach($options as $id => $data)
			{
				$option = new ilRadioOption($data[0], $id, $data[1]);
				$type->addOption($option);
			}
		}
							
		return $form;
	}
	
	/**
	 * Add custom templates
	 *
	 * @param array $a_options 
	 */
	protected function addDidacticTemplateOptions(array &$a_options)
	{
		
	}

	/**
	 * cancel create action and go back to repository parent
	 */
	public function cancelCreation()
	{
		global $ilCtrl;
		
		$ilCtrl->redirectByClass("ilrepositorygui", "frameset");
	}

	/**
	* save object
	*
	* @access	public
	*/
	public function saveObject()
	{
		global $objDefinition, $tpl;

		$new_type = $_REQUEST["new_type"];

		// create permission is already checked in createObject. This check here is done to prevent hacking attempts
		if (!$this->checkPermissionBool("create", "", $new_type))
		{
			$this->ilias->raiseError($this->lng->txt("no_create_permission"), $this->ilias->error_obj->MESSAGE);
		}

		$this->lng->loadLanguageModule($new_type);
		$this->ctrl->setParameter($this, "new_type", $new_type);
		
		$form = $this->initCreateForm($new_type);
		if ($form->checkInput())
		{
			$this->ctrl->setParameter($this, "new_type", "");

			// create instance
			$class_name = "ilObj".$objDefinition->getClassName($new_type);
			$location = $objDefinition->getLocation($new_type);
			include_once($location."/class.".$class_name.".php");
			$newObj = new $class_name();
			$newObj->setType($new_type);
			$newObj->setTitle($form->getInput("title"));
			$newObj->setDescription($form->getInput("desc"));
			$newObj->create();
			
			$this->putObjectInTree($newObj);

			// apply didactic template?
			$dtpl = $this->getDidacticTemplateVar("dtpl");
			if($dtpl)
			{
				$newObj->applyDidacticTemplate($dtpl);
			}
			
			// additional paramters are added to afterSave()
			$args = func_get_args();
			if($args)
			{
				$this->afterSave($newObj, $args);
			}
			else
			{
				$this->afterSave($newObj);
			}
			return;
		}

		// display only this form to correct input
		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}
	
	/**
	 * Get didactic template setting from creation screen
	 * 
	 * @param string $a_type
	 * @return string
	 */
	protected function getDidacticTemplateVar($a_type)
	{
		$tpl = $_POST["didactic_type"];
		if($tpl && substr($tpl, 0, strlen($a_type)+1) == $a_type."_")
		{
			return (int)substr($tpl, strlen($a_type)+1);
		}
	}

	/**
	 * Add object to tree at given position
	 *
	 * @param ilObject $a_obj
	 * @param int $a_parent_node_id
	 */
	protected function putObjectInTree(ilObject $a_obj, $a_parent_node_id = null)
	{
		global $rbacreview, $ilUser, $objDefinition;

		if(!$a_parent_node_id)
		{
			$a_parent_node_id = $_GET["ref_id"];
		}
		
		// add new object to custom parent container
		if((int)$_REQUEST["crtptrefid"])
		{
			$a_parent_node_id = (int)$_REQUEST["crtptrefid"];
		}

		$a_obj->createReference();
		$a_obj->putInTree($a_parent_node_id);
		$a_obj->setPermissions($a_parent_node_id);

		$this->obj_id = $a_obj->getId();
		$this->ref_id = $a_obj->getRefId();

		// BEGIN ChangeEvent: Record save object.
		require_once('Services/Tracking/classes/class.ilChangeEvent.php');
		ilChangeEvent::_recordWriteEvent($this->obj_id, $ilUser->getId(), 'create');
		// END ChangeEvent: Record save object.

		// rbac log
		include_once "Services/AccessControl/classes/class.ilRbacLog.php";
		$rbac_log_roles = $rbacreview->getParentRoleIds($this->ref_id, false);
		$rbac_log = ilRbacLog::gatherFaPa($this->ref_id, array_keys($rbac_log_roles), true);
		ilRbacLog::add(ilRbacLog::CREATE_OBJECT, $this->ref_id, $rbac_log);
		
		// use forced callback after object creation
		if($_REQUEST["crtcb"])
		{
			$callback_type = ilObject::_lookupType((int)$_REQUEST["crtcb"], true);
			$class_name = "ilObj".$objDefinition->getClassName($callback_type)."GUI";
			$location = $objDefinition->getLocation($callback_type);
			include_once($location."/class.".$class_name.".php");
			if (in_array(strtolower($class_name), array("ilobjitemgroupgui")))
			{
				$callback_obj = new $class_name((int)$_REQUEST["crtcb"]);
			}
			else
			{
				// #10368
				$callback_obj = new $class_name(null, (int)$_REQUEST["crtcb"], true, false);
			}
			$callback_obj->afterSaveCallback($a_obj);
		}
	}

	/**
	 * Post (successful) object creation hook
	 *
	 * @param ilObject $a_new_object 
	 */
	protected function afterSave(ilObject $a_new_object)
	{
		ilUtil::sendSuccess($this->lng->txt("object_added"), true);
		$this->ctrl->returnToParent($this);
	}

	/**
	 * edit object
	 *
	 * @access	public
	 */
	public function editObject()
	{
		global $tpl, $ilTabs;

		if (!$this->checkPermissionBool("write"))
		{
			$this->ilias->raiseError($this->lng->txt("msg_no_perm_write"),$this->ilias->error_obj->MESSAGE);
		}

		$ilTabs->activateTab("settings");

		$form = $this->initEditForm();
		$values = $this->getEditFormValues();
		if($values)
		{
			$form->setValuesByArray($values);
		}
		$tpl->setContent($form->getHTML());
	}

	/**
	 * Init object edit form
	 *
	 * @return ilPropertyFormGUI
	 */
	protected function initEditForm()
	{
		global $lng, $ilCtrl;

		$lng->loadLanguageModule($this->object->getType());

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this, "update"));
		$form->setTitle($this->lng->txt($this->object->getType()."_edit"));

		// title
		$ti = new ilTextInputGUI($this->lng->txt("title"), "title");
		$ti->setMaxLength(128);
		$ti->setSize(40);
		$ti->setRequired(true);
		$form->addItem($ti);

		// description
		$ta = new ilTextAreaInputGUI($this->lng->txt("description"), "desc");
		$ta->setCols(40);
		$ta->setRows(2);
		$form->addItem($ta);

		$this->initEditCustomForm($form);

		$form->addCommandButton("update", $this->lng->txt("save"));
		//$this->form->addCommandButton("cancelUpdate", $lng->txt("cancel"));

		return $form;
	}

	/**
	 * Add custom fields to update form
	 *
	 * @param	ilPropertyFormGUI	$a_form
	 */
	protected function initEditCustomForm(ilPropertyFormGUI $a_form)
	{
		
	}

	/**
	 * Get values for edit form
	 *
	 * @return array
	 */
	protected function getEditFormValues()
	{
		$values["title"] = $this->object->getTitle();
		$values["desc"] = $this->object->getLongDescription();
		$this->getEditFormCustomValues($values);
		return $values;
	}

	/**
	 * Add values to custom edit fields
	 *
	 * @param	array	$a_values
	 */
	protected function getEditFormCustomValues(array &$a_values)
	{

	}

	/**
	 * updates object entry in object_data
	 */
	public function updateObject()
	{
		global $ilTabs, $tpl;
		
		if (!$this->checkPermissionBool("write"))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}

		$form = $this->initEditForm();
		if($form->checkInput())
		{
			$this->object->setTitle($form->getInput("title"));
			$this->object->setDescription($form->getInput("desc"));
			$this->updateCustom($form);
			$this->object->update();
			
			$this->afterUpdate();
			return;
		}

		// display form again to correct errors
		$ilTabs->activateTab("settings");
		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}

	/**
	 * Insert custom update form values into object
	 *
	 * @param	ilPropertyFormGUI	$a_form
	 */
	protected function updateCustom(ilPropertyFormGUI $a_form)
	{

	}

	/**
	 * Post (successful) object update hook
	 */
	protected function afterUpdate()
	{
		ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"),true);
		$this->ctrl->redirect($this, "edit");
	}

	/**
	 * Init object import form
	 *
	 * @param	string	new type
	 * @return	ilPropertyFormGUI
	 */
	protected function initImportForm($a_new_type)
	{
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setTarget("_top");
		$form->setFormAction($this->ctrl->getFormAction($this, "importFile"));
		$form->setTitle($this->lng->txt($a_new_type."_import"));

		include_once("./Services/Form/classes/class.ilFileInputGUI.php");
		$fi = new ilFileInputGUI($this->lng->txt("import_file"), "importfile");
		$fi->setSuffixes(array("zip"));
		$fi->setRequired(true);
		$form->addItem($fi);

		$form->addCommandButton("importFile", $this->lng->txt("import"));
		$form->addCommandButton("cancel", $this->lng->txt("cancel"));
	
		return $form;
	}

	/**
	 * Import
	 */
	protected function importFileObject($parent_id = null)
	{
		global $objDefinition, $tpl, $ilErr;

		if(!$parent_id)
		{
			$parent_id = $_GET["ref_id"];
		}
		$new_type = $_REQUEST["new_type"];

		// create permission is already checked in createObject. This check here is done to prevent hacking attempts
		if (!$this->checkPermissionBool("create", "", $new_type))
		{
			$ilErr->raiseError($this->lng->txt("no_create_permission"));
		}

		$this->lng->loadLanguageModule($new_type);
		$this->ctrl->setParameter($this, "new_type", $new_type);
		
		$form = $this->initImportForm($new_type);
		if ($form->checkInput())
		{
			// :todo: make some check on manifest file
			
			if($objDefinition->isContainer($new_type))
			{
				include_once './Services/Export/classes/class.ilImportContainer.php';
				$imp = new ilImportContainer((int)$parent_id);
			}
			else
			{
				include_once("./Services/Export/classes/class.ilImport.php");
				$imp = new ilImport((int)$parent_id);
			}

			$new_id = $imp->importObject(null, $_FILES["importfile"]["tmp_name"],
				$_FILES["importfile"]["name"], $new_type);

			if ($new_id > 0)
			{
				$this->ctrl->setParameter($this, "new_type", "");

				$newObj = ilObjectFactory::getInstanceByObjId($new_id);

				// put new object id into tree - already done in import for containers
				if(!$objDefinition->isContainer($new_type))
				{
					$this->putObjectInTree($newObj);
				}
				
				$this->afterImport($newObj);
			}
			// import failed
			else 
			{
				if($objDefinition->isContainer($new_type))
				{
					ilUtil::sendFailure($this->lng->txt("container_import_zip_file_invalid"));
				}
				else
				{
					// not enough information here...
					return;
				}
			}
		}

		// display form to correct errors
		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}

	/**
	 * Post (successful) object import hook
	 *
	 * @param ilObject $a_new_object
	 */
	protected function afterImport(ilObject $a_new_object)
	{
		ilUtil::sendSuccess($this->lng->txt("object_added"), true);
		$this->ctrl->returnToParent($this);
	}

	/**
	* get form action for command (command is method name without "Object", e.g. "perm")
	* @param	string		$a_cmd			command
	* @param	string		$a_formaction	default formaction (is returned, if no special
	*										formaction was set)
	* @access	public
	* @return	string
	*/
	public function getFormAction($a_cmd, $a_formaction = "")
	{
		if ($this->formaction[$a_cmd] != "")
		{
			return $this->formaction[$a_cmd];
		}
		else
		{
			return $a_formaction;
		}
	}

	/**
	* set specific form action for command
	*
	* @param	string		$a_cmd			command
	* @param	string		$a_formaction	default formaction (is returned, if no special
	*										formaction was set)
	* @access	public 
	*/
	protected function setFormAction($a_cmd, $a_formaction)
	{
		$this->formaction[$a_cmd] = $a_formaction;
	}

	/**
	* get return location for command (command is method name without "Object", e.g. "perm")
	* @param	string		$a_cmd		command
	* @param	string		$a_location	default return location (is returned, if no special
	*									return location was set)
	* @access	public
	*/
	protected function getReturnLocation($a_cmd, $a_location ="")
	{
		if ($this->return_location[$a_cmd] != "")
		{
			return $this->return_location[$a_cmd];
		}
		else
		{
			return $a_location;
		}
	}

	/**
	* set specific return location for command
	* @param	string		$a_cmd		command
	* @param	string		$a_location	default return location (is returned, if no special
	*									return location was set)
	* @access	public
	*/
	protected function setReturnLocation($a_cmd, $a_location)
	{
//echo "-".$a_cmd."-".$a_location."-";
		$this->return_location[$a_cmd] = $a_location;
	}

	/**
	* get target frame for command (command is method name without "Object", e.g. "perm")
	* @param	string		$a_cmd			command
	* @param	string		$a_target_frame	default target frame (is returned, if no special
	*										target frame was set)
	* @access	public
	*/
	protected function getTargetFrame($a_cmd, $a_target_frame = "")
	{
		if ($this->target_frame[$a_cmd] != "")
		{
			return $this->target_frame[$a_cmd];
		}
		elseif (!empty($a_target_frame))
		{
			return "target=\"".$a_target_frame."\"";
		}
		else
		{
			return;
		}
	}

	/**
	* set specific target frame for command
	* @param	string		$a_cmd			command
	* @param	string		$a_target_frame	default target frame (is returned, if no special
	*										target frame was set)
	* @access	public
	*/
	protected function setTargetFrame($a_cmd, $a_target_frame)
	{
		$this->target_frame[$a_cmd] = "target=\"".$a_target_frame."\"";
	}

	// BEGIN Security: Hide objects which aren't accessible by the user.
	public function isVisible($a_ref_id,$a_type)
	{
		global $ilBench;
		
		$ilBench->start("Explorer", "setOutput_isVisible");
		$visible = $this->checkPermissionBool("visible,read", "", "", $a_ref_id);
		
		if ($visible && $a_type == 'crs') {
			global $tree;
			if($crs_id = $tree->checkForParentType($a_ref_id,'crs'))
			{
				if(!$this->checkPermissionBool("write", "", "", $crs_id))
				{
					// Show only activated courses
					$tmp_obj =& ilObjectFactory::getInstanceByRefId($crs_id,false);
	
					if(!$tmp_obj->isActivated())
					{
						unset($tmp_obj);
						$visible = false;
					}
					if(($crs_id != $a_ref_id) and $tmp_obj->isArchived())
					{
						$visible = false;
					}					
				}
			}
		}
		
		$ilBench->stop("Explorer", "setOutput_isVisible");

		return $visible;
	}
	// END Security: Hide objects which aren't accessible by the user.

	/**
	* list childs of current object
	*
	* @access	public
	*/
	public function viewObject()
	{
		global$tpl;

		if (!$this->checkPermissionBool("visible,read"))
		{
			$this->ilias->raiseError($this->lng->txt("permission_denied"),$this->ilias->error_obj->MESSAGE);
		}
		
		// BEGIN ChangeEvent: record read event.
		require_once('Services/Tracking/classes/class.ilChangeEvent.php');
		global $ilUser;
		ilChangeEvent::_recordReadEvent(
			$this->object->getType(),
			$this->object->getRefId(),
			$this->object->getId(), $ilUser->getId());		
		// END ChangeEvent: record read event.

		include_once("./Services/Repository/classes/class.ilAdminSubItemsTableGUI.php");
		if (!$this->call_by_reference)
		{
			$this->ctrl->setParameter($this, "obj_id", $this->obj_id); 
		}
		$itab = new ilAdminSubItemsTableGUI($this, "view", $_GET["ref_id"]);
		
		$tpl->setContent($itab->getHTML());
	}

	/**
	* Display deletion confirmation screen.
	* Only for referenced objects. For user,role & rolt overwrite this function in the appropriate
	* Object folders classes (ilObjUserFolderGUI,ilObjRoleFolderGUI)
	*
	* @access	public
 	*/
	public function deleteObject($a_error = false)
	{
		global $tpl, $ilCtrl;
		
		if ($_GET["item_ref_id"] != "")
		{
			$_POST["id"] = array($_GET["item_ref_id"]);
		}

		// SAVE POST VALUES (get rid of this
		ilSession::set("saved_post", $_POST["id"]);

		include_once("./Services/Repository/classes/class.ilRepUtilGUI.php");
		$ru = new ilRepUtilGUI($this);
		if (!$ru->showDeleteConfirmation($_POST["id"], $a_error))
		{
			$ilCtrl->returnToParent($this);
		}
	}

	/**
	* Show trash content of object
	*
	* @access	public
 	*/
	public function trashObject()
	{
		global $tpl;

		include_once("./Services/Repository/classes/class.ilRepUtilGUI.php");
		$ru = new ilRepUtilGUI($this);
		$ru->showTrashTable($_GET["ref_id"]);
	}

	/**
	* show possible subobjects (pulldown menu)
	*
	* @access	public
 	*/
	protected function showPossibleSubObjects()
	{
		if ($this->sub_objects == "")
		{
			$d = $this->objDefinition->getCreatableSubObjects($this->object->getType());
		}
		else
		{
			$d = $this->sub_objects;
		}

		$import = false;

		if (count($d) > 0)
		{
			foreach ($d as $row)
			{
			    $count = 0;

				if ($row["max"] > 0)
				{
					//how many elements are present?
					for ($i=0; $i<count($this->data["ctrl"]); $i++)
					{
						if ($this->data["ctrl"][$i]["type"] == $row["name"])
						{
						    $count++;
						}
					}
				}

				if ($row["max"] == "" || $count < $row["max"])
				{
					$subobj[] = $row["name"];
				}
			}
		}

		if (is_array($subobj))
		{

			//build form
			$opts = ilUtil::formSelect(12,"new_type",$subobj);
			$this->tpl->setCurrentBlock("add_object");
			$this->tpl->setVariable("SELECT_OBJTYPE", $opts);
			$this->tpl->setVariable("BTN_NAME", "create");
			$this->tpl->setVariable("TXT_ADD", $this->lng->txt("add"));
			$this->tpl->parseCurrentBlock();
		}
	}

	/**
	* get a template blockfile
	* format: tpl.<objtype>_<command>.html
	*
	* @param	string	command
	* @param	string	object type definition
	* @access	public
 	*/
	final protected function getTemplateFile($a_cmd,$a_type = "")
	{
		mk(); die("ilObjectGUI::getTemplateFile() is deprecated.");
	}

	/**
	* get Titles of objects
	* this method is used for error messages in methods cut/copy/paste
	*
	* @param	array	Array of ref_ids (integer)
	* @return   array	Array of titles (string)
	* @access	private
 	*/
	protected function getTitlesByRefId($a_ref_ids)
	{
		foreach ($a_ref_ids as $id)
		{
			// GET OBJECT TITLE
			$tmp_obj =& $this->ilias->obj_factory->getInstanceByRefId($id);
			$title[] = $tmp_obj->getTitle();
			unset($tmp_obj);
		}

		return $title ? $title : array();
	}

	/**
	* get tabs
	* abstract method.
	* @abstract	overwrite in derived GUI class of your object type
	* @access	public
	* @param	object	instance of ilTabsGUI
	*/
	protected function getTabs(&$tabs_gui)
	{
		// please define your tabs here

	}

	// PROTECTED
	protected function __showButton($a_cmd,$a_text,$a_target = '')
	{
		global $ilToolbar;
		
		$ilToolbar->addButton($a_text, $this->ctrl->getLinkTarget($this, $a_cmd), $a_target);
	}

	protected function hitsperpageObject()
	{
        ilSession::set("tbl_limit", $_POST["hitsperpage"]);
        $_GET["limit"] = $_POST["hitsperpage"];
	}
	

	protected function &__initTableGUI()
	{
		include_once "./Services/Table/classes/class.ilTableGUI.php";

		return new ilTableGUI(0,false);
	}
	
	/**
	 * standard implementation for tables
	 * use 'from' variable use different initial setting of table 
	 * 
	 */
	protected function __setTableGUIBasicData(&$tbl,&$result_set,$a_from = "")
	{
		switch ($a_from)
		{
			case "clipboardObject":
				$offset = $_GET["offset"];
				$order = $_GET["sort_by"];
				$direction = $_GET["sort_order"];
				$tbl->disable("footer");
				break;

			default:
				$offset = $_GET["offset"];
				$order = $_GET["sort_by"];
				$direction = $_GET["sort_order"];
				break;
		}

		$tbl->setOrderColumn($order);
		$tbl->setOrderDirection($direction);
		$tbl->setOffset($offset);
		$tbl->setLimit($_GET["limit"]);
		$tbl->setFooter("tblfooter",$this->lng->txt("previous"),$this->lng->txt("next"));
		$tbl->setData($result_set);
	}
	
	protected function __showClipboardTable($a_result_set,$a_from = "")
	{
		global $ilCtrl;
		
    	$tbl =& $this->__initTableGUI();
		$tpl =& $tbl->getTemplateObject();

		$tpl->setCurrentBlock("tbl_form_header");
		$tpl->setVariable("FORMACTION", $ilCtrl->getFormAction($this));
		$tpl->parseCurrentBlock();

		$tpl->setCurrentBlock("tbl_action_btn");
		$tpl->setVariable("BTN_NAME","paste");
		$tpl->setVariable("BTN_VALUE",$this->lng->txt("insert_object_here"));
		$tpl->parseCurrentBlock();
		
		$tpl->setCurrentBlock("tbl_action_btn");
		$tpl->setVariable("BTN_NAME","clear");
		$tpl->setVariable("BTN_VALUE",$this->lng->txt("clear_clipboard"));
		$tpl->parseCurrentBlock();

		$tpl->setCurrentBlock("tbl_action_row");
		$tpl->setVariable("COLUMN_COUNTS",3);
		$tpl->setVariable("IMG_ARROW",ilUtil::getImagePath("spacer.png"));
		$tpl->parseCurrentBlock();
		
		$tbl->setTitle($this->lng->txt("clipboard"),"icon_typ_b.png",$this->lng->txt("clipboard"));
		$tbl->setHeaderNames(array($this->lng->txt('obj_type'),
								   $this->lng->txt('title'),
								   $this->lng->txt('action')));
		$tbl->setHeaderVars(array('type',
                                  'title',
								  'act'),
							array('ref_id' => $this->object->getRefId(),
								  'cmd' => 'clipboard',
								  'cmdClass' => $_GET['cmdClass'],
								  'cmdNode' => $_GET['cmdNode']));

		$tbl->setColumnWidth(array("","80%","19%"));


		$this->__setTableGUIBasicData($tbl,$a_result_set,$a_from);
		$tbl->render();
		
		$this->tpl->setVariable("RESULT_TABLE",$tbl->tpl->get());

		return true;
	}

	/**
	* redirects to (repository) view per ref id
	* usually to a container and usually used at
	* the end of a save/import method where the object gui
	* type (of the new object) doesn't match with the type
	* of the current $_GET["ref_id"] value
	*
	* @param	int		$a_ref_id		reference id
	*/
	protected function redirectToRefId($a_ref_id, $a_cmd = "")
	{
		$obj_type = ilObject::_lookupType($a_ref_id,true);
		$class_name = $this->objDefinition->getClassName($obj_type);
		$class = strtolower("ilObj".$class_name."GUI");
		$this->ctrl->setParameterByClass("ilrepositorygui", "ref_id", $a_ref_id);
		$this->ctrl->redirectByClass(array("ilrepositorygui", $class), $a_cmd);
	}
	
	// Object Cloning
	/**
	 * Fill object clone template
	 * This method can be called from any object GUI class that wants to offer object cloning. 
	 *
	 * @access public
	 * @param string template variable name that will be filled
	 * @param string type of new object
	 * 
	 */
	protected function fillCloneTemplate($a_tpl_varname,$a_type)
	{
		include_once './Services/Object/classes/class.ilObjectCopyGUI.php';
		$cp = new ilObjectCopyGUI($this);
		$cp->setType($a_type);
		$cp->setTarget($_GET['ref_id']);
		if($a_tpl_varname)
		{
			$cp->showSourceSearch($a_tpl_varname);
		}
		else
		{
			return $cp->showSourceSearch(null);
		}
	}
	
	/**
	 * Clone single (not container object)
	 * Method is overwritten in ilContainerGUI
	 *
	 * @access public
	 */
	public function cloneAllObject()
	{
		include_once('./Services/Link/classes/class.ilLink.php');
		include_once('Services/CopyWizard/classes/class.ilCopyWizardOptions.php');
		
		global $ilErr,$ilUser;
		
	 	$new_type = $_REQUEST['new_type'];
	 	if(!$this->checkPermissionBool("create", "", $new_type))
	 	{
	 		$ilErr->raiseError($this->lng->txt('permission_denied'));
	 	}
		if(!(int) $_REQUEST['clone_source'])
		{
			ilUtil::sendFailure($this->lng->txt('select_one'));
			$this->createObject();
			return false;
		}
		if(!$this->checkPermissionBool("write", "", $new_type, (int)$_REQUEST['clone_source']))
		{
	 		$ilErr->raiseError($this->lng->txt('permission_denied'));
		}
		
		// Save wizard options
		$copy_id = ilCopyWizardOptions::_allocateCopyId();
		$wizard_options = ilCopyWizardOptions::_getInstance($copy_id);
		$wizard_options->saveOwner($ilUser->getId());
		$wizard_options->saveRoot((int) $_REQUEST['clone_source']);
		
		$options = $_POST['cp_options'] ? $_POST['cp_options'] : array();
		foreach($options as $source_id => $option)
		{
			$wizard_options->addEntry($source_id,$option);
		}
		$wizard_options->read();
		
		$orig = ilObjectFactory::getInstanceByRefId((int) $_REQUEST['clone_source']);
		$new_obj = $orig->cloneObject((int) $_GET['ref_id'],$copy_id);
		
		// Delete wizard options
		$wizard_options->deleteAll();

		ilUtil::sendSuccess($this->lng->txt("object_duplicated"),true);
		ilUtil::redirect(ilLink::_getLink($new_obj->getRefId()));
	}
	
	
	/**
	* Get center column
	*/
	protected function getCenterColumnHTML()
	{
		global $ilCtrl;

		include_once("Services/Block/classes/class.ilColumnGUI.php");

		$obj_id = ilObject::_lookupObjId($this->object->getRefId());
		$obj_type = ilObject::_lookupType($obj_id);

		if ($ilCtrl->getNextClass() != "ilcolumngui")
		{
			// normal command processing	
			return $this->getContent();
		}
		else
		{
			if (!$ilCtrl->isAsynch())
			{
				//if ($column_gui->getScreenMode() != IL_SCREEN_SIDE)
				if (ilColumnGUI::getScreenMode() != IL_SCREEN_SIDE)
				{
					// right column wants center
					if (ilColumnGUI::getCmdSide() == IL_COL_RIGHT)
					{
						$column_gui = new ilColumnGUI($obj_type, IL_COL_RIGHT);
						$this->setColumnSettings($column_gui);
						$this->html = $ilCtrl->forwardCommand($column_gui);
					}
					// left column wants center
					if (ilColumnGUI::getCmdSide() == IL_COL_LEFT)
					{
						$column_gui = new ilColumnGUI($obj_type, IL_COL_LEFT);
						$this->setColumnSettings($column_gui);
						$this->html = $ilCtrl->forwardCommand($column_gui);
					}
				}
				else
				{
					// normal command processing	
					return $this->getContent();
				}
			}
		}
	}
	
	/**
	* Display right column
	*/
	protected function getRightColumnHTML()
	{
		global $ilUser, $lng, $ilCtrl;
		
		$obj_id = ilObject::_lookupObjId($this->object->getRefId());
		$obj_type = ilObject::_lookupType($obj_id);

		include_once("Services/Block/classes/class.ilColumnGUI.php");
		$column_gui = new ilColumnGUI($obj_type, IL_COL_RIGHT);
		
		if ($column_gui->getScreenMode() == IL_SCREEN_FULL)
		{
			return "";
		}
		
		$this->setColumnSettings($column_gui);
		
		if ($ilCtrl->getNextClass() == "ilcolumngui" &&
			$column_gui->getCmdSide() == IL_COL_RIGHT &&
			$column_gui->getScreenMode() == IL_SCREEN_SIDE)
		{
			$html = $ilCtrl->forwardCommand($column_gui);
		}
		else
		{
			if (!$ilCtrl->isAsynch())
			{
				$html = $ilCtrl->getHTML($column_gui);
			}
		}

		return $html;
	}

	/**
	* May be overwritten in subclasses.
	*/
	protected function setColumnSettings($column_gui)
	{
		$column_gui->setRepositoryMode(true);
		$column_gui->setEnableEdit(false);
		if ($this->checkPermissionBool("write"))
		{
			$column_gui->setEnableEdit(true);
		}
	}

	/**
	 * Check permission and redirect on error
	 * 
	 * @param string $a_perm
	 * @param string $a_cmd
	 * @param string $a_type
	 * @param int $a_ref_id
	 * @return bool
	 */
	protected function checkPermission($a_perm, $a_cmd = "", $a_type = "", $a_ref_id = null)
	{
		if (!$this->checkPermissionBool($a_perm, $a_cmd, $a_type, $a_ref_id))
		{
			if (!is_int(strpos($_SERVER["PHP_SELF"], "goto.php")))
			{
				// create: redirect to parent
				if($a_perm == "create")
				{
					if(!$a_ref_id)
					{
						$a_ref_id = $_GET["ref_id"];
					}
					$type = ilObject::_lookupType($a_ref_id, true);					
				}
				else
				{
					// does this make sense?
					if (!is_object($this->object))
					{
						return;
					}
					if (!$a_ref_id)
					{
						$a_ref_id = $this->object->getRefId();
					}
					$type = $this->object->getType();
				}

				ilSession::clear("il_rep_ref_id");
				ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
				ilUtil::redirect("goto.php?target=".$type."_".$a_ref_id);
			}
			// we should never be here
			else	
			{
				die("Permission Denied.");
			}
		}
	}

	/**
	 * Check permission
	 * 
	 * @param string $a_perm
	 * @param string $a_cmd
	 * @param string $a_type
	 * @param int $a_ref_id
	 * @return bool
	 */
	protected function checkPermissionBool($a_perm, $a_cmd = "", $a_type = "", $a_ref_id = null)
	{
		global $ilAccess;

		if($a_perm == "create")
		{
			if(!$a_ref_id)
			{
				$a_ref_id = $_GET["ref_id"];
			}
			return $ilAccess->checkAccess($a_perm."_".$a_type, $a_cmd, $a_ref_id);
		}
		else
		{
			// does this make sense?
			if (!is_object($this->object))
			{
				return false;
			}
			if (!$a_ref_id)
			{
				$a_ref_id = $this->object->getRefId();
			}
			return $ilAccess->checkAccess($a_perm, $a_cmd, $a_ref_id);
		}
	}
	
	/**
	 * Goto repository root
	 *
	 * @param
	 * @return
	 */
	static function _gotoRepositoryRoot($a_raise_error = false)
	{
		global $ilAccess, $ilErr;
		
		if ($ilAccess->checkAccess("read", "", ROOT_FOLDER_ID))
		{
			$_GET["cmd"] = "frameset";
			$_GET["target"] = "";
			$_GET["ref_id"] = ROOT_FOLDER_ID;
			$_GET["baseClass"] = "ilRepositoryGUI";
			include("ilias.php");
			exit;
		}

		if ($a_raise_error)
		{
			$ilErr->raiseError($lng->txt("msg_no_perm_read"), $ilErr->FATAL);
		}
	}
	
	/**
	 * Goto repository root
	 *
	 * @param
	 * @return
	 */
	static function _gotoRepositoryNode($a_ref_id, $a_cmd = "frameset")
	{
		global $ilAccess, $ilErr;

		$_GET["cmd"] = $a_cmd;
		$_GET["target"] = "";
		$_GET["ref_id"] = $a_ref_id;
		$_GET["baseClass"] = "ilRepositoryGUI";
		include("ilias.php");
		exit;
	}
	
	
	

} // END class.ilObjectGUI (3.10: 2896 loc)

?>