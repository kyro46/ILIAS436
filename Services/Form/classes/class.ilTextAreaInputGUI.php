<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2007 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

include_once("./Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php");
include_once "./Services/RTE/classes/class.ilRTE.php";

/**
* This class represents a text area property in a property form.
*
* @author Alex Killing <alex.killing@gmx.de> 
* @version $Id$
* @ingroup	ServicesForm
*/
class ilTextAreaInputGUI extends ilSubEnabledFormPropertyGUI
{
	protected $value;
	protected $cols;
	protected $rows;
	protected $usert;
	protected $rtetags;
	protected $plugins;
	protected $removeplugins;
	protected $buttons;	
	protected $rtesupport;
	protected $use_tags_for_rte_only = true;
	
	/** 
	* Array of tinymce buttons which should be disabled
	* 
	* @var		Array
	* @type		Array
	* @access	protected
	* 
	*/
	protected $disabled_buttons = array();
	
	/** 
	* Use purifier or not
	* 
	* @var		boolean
	* @type		boolean
	* @access	protected
	* 
	*/
	protected $usePurifier = false;	
	
	/** 
	* Instance of ilHtmlPurifierInterface
	* 
	* @var		ilHtmlPurifierInterface
	* @type		ilHtmlPurifierInterface
	* @access	protected
	* 
	*/
	protected $Purifier = null;
	
	/**
	* TinyMCE root block element which surrounds the generated html
	*
	* @var		string
	* @type		string
	* @access	protected
	*/
	protected $root_block_element = null;
	
	protected $rte_tag_set = array(
		"standard" => array ("strong", "em", "u", "ol", "li", "ul", "p", "div",
			"i", "b", "code", "sup", "sub", "pre", "strike", "gap"),
		"extended" => array (
			"a","blockquote","br","cite","code","div","em","h1","h2","h3",
			"h4","h5","h6","hr","li","ol","p",
			"pre","span","strike","strong","sub","sup","u","ul",
			"i", "b", "gap"),
		"extended_img" => array (
			"a","blockquote","br","cite","code","div","em","h1","h2","h3",
			"h4","h5","h6","hr","img","li","ol","p",
			"pre","span","strike","strong","sub","sup","u","ul",
			"i", "b", "gap"),
		"extended_table" => array (
			"a","blockquote","br","cite","code","div","em","h1","h2","h3",
			"h4","h5","h6","hr","li","ol","p",
			"pre","span","strike","strong","sub","sup","table","td",
			"tr","u","ul", "i", "b", "gap"),
		"extended_table_img" => array (
			"a","blockquote","br","cite","code","div","em","h1","h2","h3",
			"h4","h5","h6","hr","img","li","ol","p",
			"pre","span","strike","strong","sub","sup","table","td",
			"tr","u","ul", "i", "b", "gap"),
		"full" => array (
			"a","blockquote","br","cite","code","div","em","h1","h2","h3",
			"h4","h5","h6","hr","img","li","ol","p",
			"pre","span","strike","strong","sub","sup","table","td",
			"tr","u","ul","ruby","rbc","rtc","rb","rt","rp", "i", "b", "gap"));
		
	
	/**
	* Constructor
	*
	* @param	string	$a_title	Title
	* @param	string	$a_postvar	Post Variable
	*/
	function __construct($a_title = "", $a_postvar = "")
	{
		parent::__construct($a_title, $a_postvar);
		$this->setType("textarea");
		$this->setRteTagSet("standard");
		$this->plugins = array();
		$this->removeplugins = array();
		$this->buttons = array();
		$this->rteSupport = array();
	}

	/**
	* Set Value.
	*
	* @param	string	$a_value	Value
	*/
	function setValue($a_value)
	{
		$this->value = $a_value;
	}

	/**
	* Get Value.
	*
	* @return	string	Value
	*/
	function getValue()
	{
		return $this->value;
	}

	/**
	* Set Cols.
	*
	* @param	int	$a_cols	Cols
	*/
	function setCols($a_cols)
	{
		$this->cols = $a_cols;
	}

	/**
	* Get Cols.
	*
	* @return	int	Cols
	*/
	function getCols()
	{
		return $this->cols;
	}

	/**
	* Set Rows.
	*
	* @param	int	$a_rows	Rows
	*/
	function setRows($a_rows)
	{
		$this->rows = $a_rows;
	}

	/**
	* Get Rows.
	*
	* @return	int	Rows
	*/
	function getRows()
	{
		return $this->rows;
	}

	/**
	 * Set Use Rich Text Editing.
	 *
	 * @param	int	$a_usert	Use Rich Text Editing
	 * @param	string $version
	 */
	public function setUseRte($a_usert, $version = '')
	{
		$this->usert = $a_usert;

		if(strlen($version))
		{
			$this->rteSupport['version'] = $version;
		}
	}

	/**
	* Get Use Rich Text Editing.
	*
	* @return	int	Use Rich Text Editing
	*/
	function getUseRte()
	{
		return $this->usert;
	}
	
	/**
	* Add RTE plugin.
	*
	* @param string $a_plugin Plugin name
	*/
	function addPlugin($a_plugin)
	{
		$this->plugins[$a_plugin] = $a_plugin;
	}
	
	/**
	* Remove RTE plugin.
	*
	* @param string $a_plugin Plugin name
	*/
	function removePlugin($a_plugin)
	{
		$this->removeplugins[$a_plugin] = $a_plugin;
	}

	/**
	* Add RTE button.
	*
	* @param string $a_button Button name
	*/
	function addButton($a_button)
	{
		$this->buttons[$a_button] = $a_button;
	}
	
	/**
	* Remove RTE button.
	*
	* @param string $a_button Button name
	*/
	function removeButton($a_button)
	{
		unset($this->buttons[$a_button]);
	}

	/**
	* Set RTE support for a special module
	*
	* @param int $obj_id Object ID
	* @param string $obj_type Object Type
	* @param string $module ILIAS module
	*/
	function setRTESupport($obj_id, $obj_type, $module, $cfg_template = null, $hide_switch = false, $version = null)
	{
		$this->rteSupport = array("obj_id" => $obj_id, "obj_type" => $obj_type, "module" => $module, 'cfg_template' => $cfg_template, 'hide_switch' => $hide_switch, 'version' => $version);
	}
	
	/**
	* Remove RTE support for a special module
	*/
	function removeRTESupport()
	{
		$this->rteSupport = array();
	}

	/**
	* Set Valid RTE Tags.
	*
	* @param	array	$a_rtetags	Valid RTE Tags
	*/
	function setRteTags($a_rtetags)
	{
		$this->rtetags = $a_rtetags;
	}

	/**
	* Get Valid RTE Tags.
	*
	* @return	array	Valid RTE Tags
	*/
	function getRteTags()
	{
		return $this->rtetags;
	}
	
	/**
	* Set Set of Valid RTE Tags
	*
	* @return	array	Set name "standard", "extended", "extended_img",
	*					"extended_table", "extended_table_img", "full"
	*/
	function setRteTagSet($a_set_name)
	{
		$this->setRteTags($this->rte_tag_set[$a_set_name]);
	}

	/**
	* Get Set of Valid RTE Tags
	*
	* @return	array	Set name "standard", "extended", "extended_img",
	*					"extended_table", "extended_table_img", "full"
	*/
	function getRteTagSet($a_set_name)
	{
		return $this->rte_tag_set[$a_set_name];
	}

	
	/**
	* RTE Tag string
	*/
	function getRteTagString()
	{
		$result = "";
		foreach ($this->getRteTags() as $tag)
		{
			$result .= "<$tag>";
		}
		return $result;
	}

	/**
	 * Set use tags for RTE only (default is true)
	 *
	 * @param boolean $a_val use tags for RTE only	
	 */
	function setUseTagsForRteOnly($a_val)
	{
		$this->use_tags_for_rte_only = $a_val;
	}
	
	/**
	 * Get use tags for RTE only (default is true)
	 *
	 * @return boolean use tags for RTE only
	 */
	function getUseTagsForRteOnly()
	{
		return $this->use_tags_for_rte_only;
	}
	
	/**
	* Set value by array
	*
	* @param	array	$a_values	value array
	*/
	function setValueByArray($a_values)
	{
		$this->setValue($a_values[$this->getPostVar()]);
		
		foreach($this->getSubItems() as $item)
		{
			$item->setValueByArray($a_values);
		}
	}

	/**
	* Check input, strip slashes etc. set alert, if input is not ok.
	*
	* @return	boolean		Input ok, true/false
	*/	
	function checkInput()
	{
		global $lng;
		include_once("./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php");
		
		if($this->usePurifier() && $this->getPurifier())
		{
			$_POST[$this->getPostVar()] = ilUtil::stripOnlySlashes($_POST[$this->getPostVar()]);
   			$_POST[$this->getPostVar()] = $this->getPurifier()->purify($_POST[$this->getPostVar()]);
		}
		else
		{
			$allowed = $this->getRteTagString();
			if ($this->plugins["latex"] == "latex" && !is_int(strpos($allowed, "<span>")))
			{
				$allowed.= "<span>";
			}
			$_POST[$this->getPostVar()] = ($this->getUseRte() || !$this->getUseTagsForRteOnly())
				? ilUtil::stripSlashes($_POST[$this->getPostVar()], true, $allowed)
				: ilUtil::stripSlashes($_POST[$this->getPostVar()]);
		}

		if ($this->getRequired() && trim($_POST[$this->getPostVar()]) == "")
		{
			$this->setAlert($lng->txt("msg_input_is_required"));

			return false;
		}
		return $this->checkSubItemsInput();
	}

	/**
	* Insert property html
	*
	* @return	int	Size
	*/
	function insert(&$a_tpl)
	{
		$ttpl = new ilTemplate("tpl.prop_textarea.html", true, true, "Services/Form");
		
		// disabled rte
		if ($this->getUseRte() && $this->getDisabled())
		{
			$ttpl->setCurrentBlock("disabled_rte");
			$ttpl->setVariable("DR_VAL", $this->getValue());
			$ttpl->parseCurrentBlock();
		}
		else
		{
			if ($this->getUseRte())
			{
				$rtestring = ilRTE::_getRTEClassname();
				include_once "./Services/RTE/classes/class.$rtestring.php";
				$rte = new $rtestring($this->rteSupport['version']);
				
				// @todo: Check this.
				$rte->addPlugin("emotions");
				foreach ($this->plugins as $plugin)
				{
					if (strlen($plugin))
					{
						$rte->addPlugin($plugin);
					}
				}
				foreach ($this->removeplugins as $plugin)
				{
					if (strlen($plugin))
					{
						$rte->removePlugin($plugin);
					}
				}
	
				foreach ($this->buttons as $button)
				{
					if (strlen($button))
					{
						$rte->addButton($button);
					}
				}
				
				$rte->disableButtons($this->getDisabledButtons());
				
				if($this->getRTERootBlockElement() !== null)
				{
					$rte->setRTERootBlockElement($this->getRTERootBlockElement());
				}
				
				if (count($this->rteSupport) >= 3)
				{
					$rte->addRTESupport($this->rteSupport["obj_id"], $this->rteSupport["obj_type"], $this->rteSupport["module"], false, $this->rteSupport['cfg_template'], $this->rteSupport['hide_switch']);
				}
				else
				{
					$rte->addCustomRTESupport(0, "", $this->getRteTags());
				}			
				
				$ttpl->touchBlock("prop_ta_w");
				$ttpl->setCurrentBlock("prop_textarea");
				$ttpl->setVariable("ROWS", $this->getRows());
			}
			else
			{
				$ttpl->touchBlock("no_rteditor");
	
				if ($this->getCols() > 5)
				{
					$ttpl->setCurrentBlock("prop_ta_c");
					$ttpl->setVariable("COLS", $this->getCols());
					$ttpl->parseCurrentBlock();
				}
				else
				{
					$ttpl->touchBlock("prop_ta_w");
				}
				
				$ttpl->setCurrentBlock("prop_textarea");
				$ttpl->setVariable("ROWS", $this->getRows());
			}
			if (!$this->getDisabled())
			{
				$ttpl->setVariable("POST_VAR",
					$this->getPostVar());
			}
			$ttpl->setVariable("ID", $this->getFieldId());
			if ($this->getDisabled())
			{
				$ttpl->setVariable('DISABLED','disabled="disabled" ');
			}
			$ttpl->setVariable("PROPERTY_VALUE", ilUtil::prepareFormOutput($this->getValue()));
			$ttpl->parseCurrentBlock();
		}
		
		if ($this->getDisabled())
		{
			$ttpl->setVariable("HIDDEN_INPUT",
				$this->getHiddenTag($this->getPostVar(), $this->getValue()));
		}

		
		$a_tpl->setCurrentBlock("prop_generic");
		$a_tpl->setVariable("PROP_GENERIC", $ttpl->get());
		$a_tpl->parseCurrentBlock();

	}
	
	/**
	* Setter/Getter for the html purifier usage
	*
	* @param	boolean	$a_flag	Use purifier or not
	* @return	mixed	Returns instance of ilTextAreaInputGUI or boolean
	* @access	public
	*/
	public function usePurifier($a_flag = null)
	{
		if(null === $a_flag)
		{
			return $this->usePurifier;
		}
		
		$this->usePurifier = $a_flag;
		return $this;
	}
	
	/**
	* Setter for the html purifier
	*
	* @param	ilHtmlPurifierInterface	Instance of ilHtmlPurifierInterface 
	* @return	ilTextAreaInputGUI		Instance of ilTextAreaInputGUI
	* @access	public
	*/
	public function setPurifier(ilHtmlPurifierInterface $Purifier)
	{
		$this->Purifier = $Purifier;
		return $this;
	}
	
	/**
	* Getter for the html purifier
	*
	* @return	ilHtmlPurifierInterface	Instance of ilHtmlPurifierInterface
	* @access	public
	*/
	public function getPurifier()
	{
		return $this->Purifier;
	}
	
	/**
	* Setter for the TinyMCE root block element
	*
	* @param	string				$a_root_block_element	root block element
	* @return	ilTextAreaInputGUI	Instance of ilTextAreaInputGUI
	* @access	public
	*/
	public function setRTERootBlockElement($a_root_block_element)
	{
		$this->root_block_element = $a_root_block_element;
		return $this;
	}
	
	/**
	* Getter for the TinyMCE root block element
	*
	* @return	string	Root block element of TinyMCE
	* @access	public
	*/
	public function getRTERootBlockElement()
	{
		return $this->root_block_element;
	}
	
	/** 
	* Sets buttons which should be disabled in TinyMCE
	* 
	* @param	mixed	$a_button	Either a button string or an array of button strings
	* @return	ilTextAreaInputGUI	Instance of ilTextAreaInputGUI
	* @access	public
	* 
	*/
	public function disableButtons($a_button)
	{
		if(is_array($a_button))
		{
			$this->disabled_buttons = array_unique(array_merge($this->disabled_buttons, $a_button));
		}
		else
		{
			$this->disabled_buttons = array_unique(array_merge($this->disabled_buttons, array($a_button)));
		}
		
		return $this;
	}
	
	/** 
	* Returns the disabled TinyMCE buttons
	* 
	* @param	boolean	$as_array	Should the disabled buttons be returned as a string or as an array
	* @return	Array	Array of disabled buttons
	* @access	public
	* 
	*/
	public function getDisabledButtons($as_array = true)
	{
		if(!$as_array)
		{
			return implode(',', $this->disabled_buttons);
		}
		else
		{
			return $this->disabled_buttons;
		}
	}
}
