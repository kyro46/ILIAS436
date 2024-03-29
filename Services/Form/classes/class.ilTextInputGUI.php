<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Table/interfaces/interface.ilTableFilterItem.php");
include_once("./Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php");
include_once 'Services/UIComponent/Toolbar/interfaces/interface.ilToolbarItem.php';

/**
* This class represents a text property in a property form.
*
* @author Alex Killing <alex.killing@gmx.de> 
* @version $Id$
* @ingroup	ServicesForm
*/
class ilTextInputGUI extends ilSubEnabledFormPropertyGUI implements ilTableFilterItem, ilToolbarItem
{
	protected $value;
	protected $maxlength = 200;
	protected $size = 40;
	protected $validationRegexp;
	protected $validationFailureMessage = '';
	protected $suffix;
	protected $style_css;
	protected $css_class;
	protected $ajax_datasource;
	protected $submit_form_on_enter = false;

	/**
	* Constructor
	*
	* @param	string	$a_title	Title
	* @param	string	$a_postvar	Post Variable
	*/
	function __construct($a_title = "", $a_postvar = "")
	{
		parent::__construct($a_title, $a_postvar);
		$this->setInputType("text");
		$this->validationRegexp = "";
	}

	/**
	* Set Value.
	*
	* @param	string	$a_value	Value
	*/
	function setValue($a_value)
	{				
		if($this->getMulti() && is_array($a_value))
		{						
			$this->setMultiValues($a_value);	
			$a_value = array_shift($a_value);		
		}	
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
	
	public function setMulti($a_multi, $a_sortable = false)
	{
		$this->multi = (bool)$a_multi;
		$this->multi_sortable = (bool)$a_sortable;
	}

	/**
	 * Set message string for validation failure
	 * @return 
	 * @param string $a_msg
	 */
	public function setValidationFailureMessage($a_msg)
	{
		$this->validationFailureMessage = $a_msg;
	}
	
	public function getValidationFailureMessage()
	{
		return $this->validationFailureMessage;
	}

	/**
	* Set validation regexp.
	*
	* @param	string	$a_value	regexp
	*/
	public function setValidationRegexp($a_value)
	{
		$this->validationRegexp = $a_value;
	}

	/**
	* Get validation regexp.
	*
	* @return	string	regexp
	*/
	function getValidationRegexp()
	{
		return $this->validationRegexp;
	}

	/**
	* Set Max Length.
	*
	* @param	int	$a_maxlength	Max Length
	*/
	function setMaxLength($a_maxlength)
	{
		$this->maxlength = $a_maxlength;
	}

	/**
	* Get Max Length.
	*
	* @return	int	Max Length
	*/
	function getMaxLength()
	{
		return $this->maxlength;
	}

	/**
	* Set Size.
	*
	* @param	int	$a_size	Size
	*/
	function setSize($a_size)
	{
		$this->size = $a_size;
	}

	/**
	* Set inline style.
	*
	* @param	string	$a_style	style
	*/
	function setInlineStyle($a_style)
	{
		$this->style_css = $a_style;
	}
	
	/**
	* Get inline style.
	*
	* @return	string	style
	*/
	function getInlineStyle()
	{
		return $this->style_css;
	}
	
	public function setCssClass($a_class)
	{
		$this->css_class = $a_class;
	}
	
	public function getCssClass()
	{
		return $this->css_class;
	}
	
	
	/**
	* Set value by array
	*
	* @param	array	$a_values	value array
	*/
	function setValueByArray($a_values)
	{		
		$this->setValue($a_values[$this->getPostVar()]);
	}

	/**
	* Get Size.
	*
	* @return	int	Size
	*/
	function getSize()
	{
		return $this->size;
	}
	
	/**
	* Set suffix.
	*
	* @param	string	$a_value	suffix
	*/
	function setSuffix($a_value)
	{
		$this->suffix = $a_value;
	}

	/**
	* Get suffix.
	*
	* @return	string	suffix
	*/
	function getSuffix()
	{
		return $this->suffix;
	}

	/**
	 * set input type
	 *
	 * @access public
	 * @param string input type password | text
	 * 
	 */
	public function setInputType($a_type)
	{
	 	$this->input_type = $a_type;
	}
	
	/**
	 * get input type
	 *
	 * @access public
	 */
	public function getInputType()
	{
	 	return $this->input_type;
	}
	
	/**
	 * Set submit form on enter
	 *
	 * @param	boolean
	 */
	function setSubmitFormOnEnter($a_val)
	{
		$this->submit_form_on_enter = $a_val;
	}
	
	/**
	 * Get submit form on enter
	 *
	 * @return	boolean
	 */
	function getSubmitFormOnEnter()
	{
		return $this->submit_form_on_enter;
	}

	/**
	* Check input, strip slashes etc. set alert, if input is not ok.
	*
	* @return	boolean		Input ok, true/false
	*/	
	function checkInput()
	{
		global $lng;
		
		if(!$this->getMulti())
		{		
			$_POST[$this->getPostVar()] = ilUtil::stripSlashes($_POST[$this->getPostVar()]);
			if ($this->getRequired() && trim($_POST[$this->getPostVar()]) == "")
			{
				$this->setAlert($lng->txt("msg_input_is_required"));

				return false;
			}
			else if (strlen($this->getValidationRegexp()))
			{
				if (!preg_match($this->getValidationRegexp(), $_POST[$this->getPostVar()]))
				{
					$this->setAlert(
						$this->getValidationFailureMessage() ?
						$this->getValidationFailureMessage() :
						$lng->txt('msg_wrong_format')
					);
					return FALSE;
				}
			}			
		}
		else 
		{			
			foreach($_POST[$this->getPostVar()] as $idx => $value)
			{
				$_POST[$this->getPostVar()][$idx] = ilUtil::stripSlashes($value);
			}		
			$_POST[$this->getPostVar()] = array_unique($_POST[$this->getPostVar()]);
			
			if ($this->getRequired() && !trim(implode("", $_POST[$this->getPostVar()])))
			{
				$this->setAlert($lng->txt("msg_input_is_required"));

				return false;
			}
			else if (strlen($this->getValidationRegexp()))
			{
				$reg_valid = true;
				foreach($_POST[$this->getPostVar()] as $value)
				{
					if (!preg_match($this->getValidationRegexp(), $value))
					{
						$reg_valid = false;
						break;
					}
				}
				if(!$reg_valid)
				{
					$this->setAlert(
						$this->getValidationFailureMessage() ?
						$this->getValidationFailureMessage() :
						$lng->txt('msg_wrong_format')
					);
					return false;
				}
			}
		}		
		
		return $this->checkSubItemsInput();
	}

	/**
	 * get datasource link for js autocomplete
	 * @return	String	link to data generation script
	 */
	 function getDataSource()
	 {
	 	return $this->ajax_datasource;
	 }

	/**
	 * set datasource link for js autocomplete
	 * @param	String	link to data generation script
	 */
	function setDataSource($href)
	{
		$this->ajax_datasource = $href;
	}
	
	public function setMultiValues(array $a_values)
	{
		foreach($a_values as $idx => $value)
		{
			$a_values[$idx] = trim($value);
			if($a_values[$idx] == "")
			{
				unset($a_values[$idx]);
			}
		}
		parent::setMultiValues($a_values);
	}
	
	/**
	* Render item
	*/
	protected function render($a_mode = "")
	{
		$tpl = new ilTemplate("tpl.prop_textinput.html", true, true, "Services/Form");
		if (strlen($this->getValue()))
		{
			$tpl->setCurrentBlock("prop_text_propval");
			$tpl->setVariable("PROPERTY_VALUE", ilUtil::prepareFormOutput($this->getValue()));
			$tpl->parseCurrentBlock();
		}
		if (strlen($this->getInlineStyle()))
		{
			$tpl->setCurrentBlock("stylecss");
			$tpl->setVariable("CSS_STYLE", ilUtil::prepareFormOutput($this->getInlineStyle()));
			$tpl->parseCurrentBlock();
		}
		if(strlen($this->getCssClass()))
		{
			$tpl->setCurrentBlock("classcss");
			$tpl->setVariable('CLASS_CSS', ilUtil::prepareFormOutput($this->getCssClass()));
			$tpl->parseCurrentBlock();
		}
		if ($this->getSubmitFormOnEnter())
		{
			$tpl->touchBlock("submit_form_on_enter");
		}

		switch($this->getInputType())
		{
			case 'password':
				$tpl->setVariable('PROP_INPUT_TYPE','password');
				break;
			case 'hidden':
				$tpl->setVariable('PROP_INPUT_TYPE','hidden');
				break;
			case 'text':
			default:
				$tpl->setVariable('PROP_INPUT_TYPE','text');
		}
		$tpl->setVariable("ID", $this->getFieldId());
		$tpl->setVariable("SIZE", $this->getSize());
		if($this->getMaxLength() != null)
			$tpl->setVariable("MAXLENGTH", $this->getMaxLength());
		if (strlen($this->getSuffix())) $tpl->setVariable("INPUT_SUFFIX", $this->getSuffix());
		
		$postvar = $this->getPostVar();		
		if($this->getMulti() && substr($postvar, -2) != "[]")
		{
			$postvar .= "[]";
		}
		
		if ($this->getDisabled())
		{
			if($this->getMulti())
			{
				$value = $this->getMultiValues();
				$hidden = "";	
				if(is_array($value))
				{
					foreach($value as $item)
					{
						$hidden .= $this->getHiddenTag($postvar, $item);
					}
				}
			}
			else
			{			
				$hidden = $this->getHiddenTag($postvar, $this->getValue());
			}			
			if($hidden)
			{
				$tpl->setVariable("DISABLED", " disabled=\"disabled\"");
				$tpl->setVariable("HIDDEN_INPUT", $hidden);
			}			
		}
		else
		{
			$tpl->setVariable("POST_VAR", $postvar);
		}

		// use autocomplete feature?		
		if ($this->getDataSource())
		{
			include_once "Services/jQuery/classes/class.iljQueryUtil.php";
			iljQueryUtil::initjQuery();
			iljQueryUtil::initjQueryUI();
			
			if ($this->getMulti())
			{
				$tpl->setCurrentBlock("ac_multi");
				$tpl->setVariable('MURL_AUTOCOMPLETE', $this->getDataSource());
				$tpl->setVariable('ID_AUTOCOMPLETE', $this->getFieldId());
				$tpl->parseCurrentBlock();
				
				// set to fields that start with autocomplete selector
				$sel_auto = '[id^="'.$this->getFieldId().'"]';
			}
			else
			{
				// use id for autocomplete selector
				$sel_auto = "#".$this->getFieldId();
			}
			$tpl->setCurrentBlock("prop_text_autocomplete");
			$tpl->setVariable('SEL_AUTOCOMPLETE', $sel_auto);
			$tpl->setVariable('URL_AUTOCOMPLETE', $this->getDataSource());
			$tpl->parseCurrentBlock();
		}
		
		if ($a_mode == "toolbar")
		{
			// block-inline hack, see: http://blog.mozilla.com/webdev/2009/02/20/cross-browser-inline-block/
			// -moz-inline-stack for FF2
			// zoom 1; *display:inline for IE6 & 7
			$tpl->setVariable("STYLE_PAR", 'display: -moz-inline-stack; display:inline-block; zoom: 1; *display:inline;');
		}
		else
		{
			$tpl->setVariable("STYLE_PAR", '');
		}
		
		// multi icons
		if($this->getMulti() && !$a_mode && !$this->getDisabled())
		{
			$tpl->setVariable("MULTI_ICONS", $this->getMultiIconsHTML($this->multi_sortable));			
		}
		
		return $tpl->get();
	}
	
	/**
	* Insert property html
	*
	* @return	int	Size
	*/
	function insert(&$a_tpl)
	{
		$html = $this->render();

		$a_tpl->setCurrentBlock("prop_generic");
		$a_tpl->setVariable("PROP_GENERIC", $html);
		$a_tpl->parseCurrentBlock();
	}
	
	/**
	* Get HTML for table filter
	*/
	function getTableFilterHTML()
	{
		$html = $this->render();
		return $html;
	}

	/**
	* Get HTML for toolbar
	*/
	function getToolbarHTML()
	{
		$html = $this->render("toolbar");
		return $html;
	}
	
}
?>