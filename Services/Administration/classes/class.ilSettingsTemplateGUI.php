<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Administration/classes/class.ilSettingsTemplate.php");

/**
 * Settings template
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 * @ingroup ServicesAdministration
 */
class ilSettingsTemplateGUI
{
	private $config;

	/**
	 * Constructor
	 *
	 * @param
	 * @return
	 */
	function __construct($a_config)
	{
		global $ilCtrl;

		$ilCtrl->saveParameter($this, array("templ_id"));

		$this->setConfig($a_config);

		$this->readSettingsTemplate();
	}

	/**
	 * Execute command
	 */
	function executeCommand()
	{
		global $ilCtrl;

		$cmd = $ilCtrl->getCmd("listSettingsTemplates");
		$this->$cmd();
	}

	/**
	 * Set config object
	 *
	 * @param	object	$a_val	config object
	 */
	public function setConfig($a_val)
	{
		$this->config = $a_val;
	}

	/**
	 * Get config object
	 *
	 * @return	object	config object
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Read settings template
	 *
	 * @param
	 * @return
	 */
	function readSettingsTemplate()
	{
	    if ($this->getConfig()) {
		$this->settings_template = new ilSettingsTemplate((int) $_GET[templ_id], $this->getConfig());
	    }
	    else {
		$this->settings_template = new ilSettingsTemplate((int) $_GET[templ_id]);
	    }
	}

	/**
	 * List all settings template
	 *
	 * @param
	 * @return
	 */
	function listSettingsTemplates()
	{
		global $tpl, $ilToolbar, $ilCtrl, $lng;

		$ilToolbar->addButton($lng->txt("adm_add_settings_template"),
		$ilCtrl->getLinkTarget($this, "addSettingsTemplate"));

		include_once("./Services/Administration/classes/class.ilSettingsTemplateTableGUI.php");
		$table = new ilSettingsTemplateTableGUI($this, "listSettingsTemplates",
			$this->getConfig()->getType());

		$tpl->setContent($table->getHTML());
	}

	/**
	 * Add settings template
	 */
	function addSettingsTemplate()
	{
		global $tpl;

		$this->initSettingsTemplateForm("create");
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Edit settings template
	 */
	function editSettingsTemplate()
	{
		global $tpl;

		$this->initSettingsTemplateForm("edit");
		$this->getSettingsTemplateValues();
		$tpl->setContent($this->form->getHTML());
	}

	/**
	 * Init settings template form.
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initSettingsTemplateForm($a_mode = "edit")
	{
		global $lng, $ilCtrl;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		// title
		$ti = new ilTextInputGUI($lng->txt("title"), "title");
		$ti->setMaxLength(200);
		$ti->setRequired(true);
		$this->form->addItem($ti);

		// description
		$ti = new ilTextAreaInputGUI($lng->txt("description"), "description");
		$this->form->addItem($ti);

		// hidable tabs
		$tabs = $this->getConfig()->getHidableTabs();
		if (is_array($tabs) && count($tabs) > 0)
		{
			$sec = new ilFormSectionHeaderGUI();
			$sec->setTitle($lng->txt("adm_hide_tabs"));
			$this->form->addItem($sec);

			foreach($tabs as $t)
			{
				// hide tab $t?
				$cb = new ilCheckboxInputGUI($t["text"], "tab_".$t["id"]);
				$this->form->addItem($cb);
			}
		}

		// settings
		$settings = $this->getConfig()->getSettings();
		if (is_array($settings) && count($settings) > 0)
		{
			$sec = new ilFormSectionHeaderGUI();
			$sec->setTitle($lng->txt("adm_predefined_settings"));
			$this->form->addItem($sec);

			foreach($settings as $s)
			{
				// setting
				$cb = new ilCheckboxInputGUI($s["text"], "set_".$s["id"]);
				$this->form->addItem($cb);

				switch ($s["type"])
				{
					case ilSettingsTemplateConfig::TEXT:

						$ti = new ilTextInputGUI($lng->txt("adm_value"), "value_".$s["id"]);
						//$ti->setMaxLength();
						//$ti->setSize();
						$cb->addSubItem($ti);
						break;

					case ilSettingsTemplateConfig::BOOL:
						$cb2 = new ilCheckboxInputGUI($lng->txt("adm_value"), "value_".$s["id"]);
						$cb->addSubItem($cb2);
						break;

					case ilSettingsTemplateConfig::SELECT:
						$si = new ilSelectInputGUI($lng->txt("adm_value"), "value_".$s["id"]);
						$si->setOptions($s["options"]);
						$cb->addSubItem($si);
						break;

                                        case ilSettingsTemplateConfig::CHECKBOX:
                                                $chbs = new ilCheckboxGroupInputGUI($lng->txt("adm_value"), "value_".$s["id"]);
                                                foreach($s['options'] as $key => $value) {
                                                    $chbs->addOption($c = new ilCheckboxInputGUI($value, $key));
                                                    $c->setValue($key);
                                                }
                                                $cb->addSubItem($chbs);
                                                break;
				}

                                if ($s['hidable']) {
                                    // hide setting
                                    $cb_hide = new ilCheckboxInputGUI($lng->txt("adm_hide"), "hide_".$s["id"]);
                                    $cb->addSubItem($cb_hide);
                                }
			}
		}

		// save and cancel commands
		if ($a_mode == "create")
		{
			$this->form->addCommandButton("saveSettingsTemplate", $lng->txt("save"));
			$this->form->addCommandButton("listSettingsTemplates", $lng->txt("cancel"));
			$this->form->setTitle($lng->txt("adm_add_settings_template"));
		}
		else
		{
			$this->form->addCommandButton("updateSettingsTemplate", $lng->txt("save"));
			$this->form->addCommandButton("listSettingsTemplates", $lng->txt("cancel"));
			$this->form->setTitle($lng->txt("adm_edit_settings_template"));
		}

		$this->form->setFormAction($ilCtrl->getFormAction($this));
	}

	/**
	 * Get current values for settings template from
	 */
	public function getSettingsTemplateValues()
	{
		$values = array();

		$values["title"] = $this->settings_template->getTitle();
		$values["description"] = $this->settings_template->getDescription();

		// save tabs to be hidden
		$tabs = $this->settings_template->getHiddenTabs();
		foreach ($tabs as $t)
		{
			$values["tab_".$t] = true;
		}

		// save settings values
		$set = $this->settings_template->getSettings();
		foreach($this->getConfig()->getSettings() as $s)
		{
			if (isset($set[$s["id"]]))
			{
				$values["set_".$s["id"]] = true;

                                if ($s['type'] == ilSettingsTemplateConfig::CHECKBOX) {
                                    if (!is_array($set[$s["id"]]["value"]))
					$ar = @unserialize($set[$s["id"]]["value"]);
				    else
					$ar = $set[$s["id"]]["value"];
                                    $values["value_".$s["id"]] = is_array($ar) ? $ar : array();
                                }
                                else {
                                    $values["value_".$s["id"]] = $set[$s["id"]]["value"];
                                }
                                
				$values["hide_".$s["id"]] = $set[$s["id"]]["hide"];
			}
		}
		$this->form->setValuesByArray($values);
	}

	/**
	 * Save settings template form
	 */
	public function saveSettingsTemplate()
	{
		global $tpl, $lng, $ilCtrl;

		$this->initSettingsTemplateForm("create");
		if ($this->form->checkInput())
		{
			$settings_template = new ilSettingsTemplate();
			$settings_template->setType($this->getConfig()->getType());

			$this->setValuesFromForm($settings_template);
			$settings_template->create();

			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "listSettingsTemplates");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}

	/**
	 * Update settings template
	 */
	function updateSettingsTemplate()
	{
		global $lng, $ilCtrl, $tpl;

		$this->initSettingsTemplateForm("edit");
		if ($this->form->checkInput())
		{
			$this->setValuesFromForm($this->settings_template);
			$this->settings_template->update();

			ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
			$ilCtrl->redirect($this, "listSettingsTemplates");
		}

		$this->form->setValuesByPost();
		$tpl->setContent($this->form->getHtml());
	}

	/**
	 * Set values from form
	 *
	 * @param
	 * @return
	 */
	function setValuesFromForm($a_set_templ)
	{
		// perform update
		$a_set_templ->setTitle($_POST["title"]);
		$a_set_templ->setDescription($_POST["description"]);

		// save tabs to be hidden
		$a_set_templ->removeAllHiddenTabs();
		foreach ($this->getConfig()->getHidableTabs() as $t)
		{
			if ($_POST["tab_".$t["id"]])
			{
				$a_set_templ->addHiddenTab($t["id"]);
			}
		}

		// save settings values
		$a_set_templ->removeAllSettings();
		foreach($this->getConfig()->getSettings() as $s)
		{
                        if ($_POST["set_".$s["id"]])
                        {
                           $a_set_templ->setSetting(
					$s["id"], $_POST["value_".$s["id"]],
                                    $_POST["hide_".$s["id"]]);
                        }
		}
	}

	/**
	 * Confirm settings template deletion
	 */
	function confirmSettingsTemplateDeletion()
	{
		global $ilCtrl, $tpl, $lng;

		if (!is_array($_POST["tid"]) || count($_POST["tid"]) == 0)
		{
			ilUtil::sendInfo($lng->txt("no_checkbox"), true);
			$ilCtrl->redirect($this, "listSettingsTemplates");
		}
		else
		{
			include_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");
			$cgui = new ilConfirmationGUI();
			$cgui->setFormAction($ilCtrl->getFormAction($this));
			$cgui->setHeaderText($lng->txt("adm_sure_delete_settings_template"));
			$cgui->setCancel($lng->txt("cancel"), "listSettingsTemplates");
			$cgui->setConfirm($lng->txt("delete"), "deleteSettingsTemplate");

			foreach ($_POST["tid"] as $i)
			{
				$cgui->addItem("tid[]", $i, ilSettingsTemplate::lookupTitle($i));
			}

			$tpl->setContent($cgui->getHTML());
		}
	}

	/**
	 * Delete settings template
	 *
	 * @param
	 * @return
	 */
	function deleteSettingsTemplate()
	{
		global $ilCtrl, $lng;

		if (is_array($_POST["tid"]))
		{
			foreach ($_POST["tid"] as $i)
			{
				$templ = new ilSettingsTemplate($i);
				$templ->delete();
			}
		}
		ilUtil::sendSuccess("msg_obj_modified");
		$ilCtrl->redirect($this, "listSettingsTemplates");
	}

}

?>
