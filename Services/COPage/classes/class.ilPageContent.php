<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
* Class ilPageContent
*
* Content object of ilPageObject (see ILIAS DTD). Every concrete object
* should be an instance of a class derived from ilPageContent (e.g. ilParagraph,
* ilMediaObject, ...)
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id: class.ilPageContent.php 29146 2011-05-19 15:26:31Z akill $
*
* @ingroup ServicesCOPage
*/
abstract class ilPageContent
{
	//var $type;		// type
	var $hier_id; 		// hierarchical editing id
	var $node;			// node in page xml
	var $dom;			// dom object

	/**
	* Constructor.
	*
	* All initialisation in derived classes should go to the
	* init() function
	*/
	final function __construct($a_dom)
	{
		$this->dom = $a_dom;
		$this->init();
		if ($this->getType() == "")
		{
			die ("Error: ilPageContent::init() did not set type");
		}
	}
	
	/**
	* Init object. This function must be overwritten and at least set
	* the content type.
	*/
	abstract function init();

	/**
	* Set Type. Must be called in constructor.
	*
	* @param	string	$a_type		type of page content component
	*/
	final protected function setType($a_type)
	{
		$this->type = $a_type;
	}

	/**
	* Get type of page content
	*
	* @return	string		Type as defined by the page content component
	*/
	function getType()
	{
		return $this->type;
	}

	/**
	* Set xml node of page content.
	*
	* @param	object	$a_node		node object
	*/
	function setNode(&$a_node)
	{
		$this->node =& $a_node;
	}
	

	/**
	* Get xml node of page content.
	*
	* @return	object				node object
	*/
	function &getNode()
	{
		return $this->node;
	}


	/**
	* Set hierarchical ID in xml structure
	*
	* @param	string		$a_hier_id		Hierarchical ID.
	*/
	function setHierId($a_hier_id)
	{
		$this->hier_id = $a_hier_id;
	}

	/**
	* Get hierarchical id
	*/
	function getHierId()
	{
		return $this->hier_id;
	}
	
	
	/**
	* Get hierarchical id from dom
	*/
	function lookupHierId()
	{
		return $this->node->get_attribute("HierId");
	}

	/**
	* Read PC Id.
	*
	* @return	string	PC Id
	*/
	function readHierId()
	{
		if (is_object($this->node))
		{
			return $this->node->get_attribute("HierId");
		}
	}

	/**
	* Set PC Id.
	*
	* @param	string	$a_pcid	PC Id
	*/
	function setPcId($a_pcid)
	{
		$this->pcid = $a_pcid;
	}

	/**
	* Get PC Id.
	*
	* @return	string	PC Id
	*/
	function getPCId()
	{
		return $this->pcid;
	}

	
	/**
	* Read PC Id.
	*
	* @return	string	PC Id
	*/
	function readPCId()
	{
		if (is_object($this->node))
		{
			return $this->node->get_attribute("PCID");
		}
	}

	/**
	 * Write pc id
	 */
	function writePCId($a_pc_id)
	{
		if (is_object($this->node))
		{
			$this->node->set_attribute("PCID", $a_pc_id);
		}
	}

	/**
	* Increases an hierarchical editing id at lowest level (last number)
	*
	* @param	string	$ed_id		hierarchical ID
	*
	* @return	string				hierarchical ID (increased)
	*/
	final static function incEdId($ed_id)
	{
		$id = explode("_", $ed_id);
		$id[count($id) - 1]++;
		
		return implode($id, "_");
	}

	/**
	* Decreases an hierarchical editing id at lowest level (last number)
	*
	* @param	string	$ed_id		hierarchical ID
	*
	* @return	string				hierarchical ID (decreased)
	*/
	final static function decEdId($ed_id)
	{
		$id = explode("_", $ed_id);
		$id[count($id) - 1]--;

		return implode($id, "_");
	}

	/**
	* Check, if two ids are in same container.
	*
	* @param	string	$ed_id1		hierachical ID 1
	* @param	string	$ed_id2		hierachical ID 2
	*
	* @return	boolean				true/false
	*/
	final static function haveSameContainer($ed_id1, $ed_id2)
	{
		$id1 = explode("_", $ed_id1);
		$id2 = explode("_", $ed_id1);
		if(count($id1) == count($id2))
		{
			array_pop($id1);
			array_pop($id2);
			foreach ($id1 as $key => $id)
			{
				if($id != $id2[$key])
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	* Sort an array of Hier IDS in ascending order
	*/
	static function sortHierIds($a_array)
	{
		uasort($a_array, array("ilPageContent", "isGreaterHierId"));
		
		return $a_array;
	}
	
	/**
	* Check whether Hier ID $a is greater than Hier ID $b
	*/
	function isGreaterHierId($a, $b)
	{
		$a_arr = explode("_", $a);
		$b_arr = explode("_", $b);
		for ($i = 0; $i < count($a_arr); $i++)
		{
			if ((int) $a_arr[$i] > (int) $b_arr[$i])
			{
				return true;
			}
			else if ((int) $a_arr[$i] < (int) $b_arr[$i])
			{
				return false;
			}
		}
		return false;
	}
	
	/**
	* Set Enabled value for page content component.
	*
	* @param	string	$value		"True" | "False"
	*
	*/
	function setEnabled($value) 
	{
		if (is_object($this->node))
		{
			$this->node->set_attribute("Enabled", $value);
		}
	}
	 
	/**
	* Enable page content.
	*/
	function enable() 
	{
		$this->setEnabled("True");
	}
	  
	/**
	* Disable page content.
	*/
	function disable() 	
	{
		$this->setEnabled("False");
	}

	/**
	* Check whether page content is enabled.
	*
	* @return	boolean			true/false
	*/
	final function isEnabled()
	{
		if (is_object($this->node) && $this->node->has_attribute("Enabled"))
		{
			$compare = $this->node->get_attribute("Enabled");	  			  		
		} 
		else
		{
			$compare = "True";
		}
		
		return strcasecmp($compare,"true") == 0;
	}
	
	/**
	* Create page content node (always use this method first when adding a new element)
	*/
	function createPageContentNode($a_set_this_node = true)
	{
		$node = $this->dom->create_element("PageContent");
		if ($a_set_this_node)
		{
			$this->node = $node;
		}
		return $node;
	}
	
	/**
	* Set anchor
	*/
/*
	function setAnchor($a_name)
	{
		$node = $this->getNode();
		
		ilDOMUtil::setFirstOptionalElement($this->dom, $node, "Anchor",
			array("BibItemIdentifier"), $a_name, array());
	}*/
	
	/**
	* Get Anchor
	*/
/*
	function getAnchor()
	{
		$node = $this->getNode();
		$childs = $node->child_nodes();

		for ($i=0; $i<count($childs); $i++)
		{
			if ($childs[$i]->node_name() == "Anchor")
			{
				return $childs[$i]->get_content();
			}
		}

		return "";
	}
*/
}
?>
