<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
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

/*
* Solution Explorer for question pools
*
* @author Helmut Schottmüller <helmut.schottmueller@mac.com>
* @version $Id: class.ilSolutionExplorer.php 35765 2012-07-25 11:36:11Z mjansen $
*
* @ingroup ModulesTestQuestionPool
*/

include_once "./Services/UIComponent/Explorer/classes/class.ilExplorer.php";
include_once "./Modules/Test/classes/inc.AssessmentConstants.php";

class ilSolutionExplorer extends ilExplorer
{

/**
 * id of root folder
 * @var int root folder id
 * @access private
 */
	var $root_id;
	var $output;
	var $ctrl;

	var $selectable_type;
	var $ref_id;
	var $target_class;

/**
* Constructor
* @access	public
* @param	string	target
*/
	function ilSolutionExplorer($a_target, $a_target_class)
	{
		global $tree,$ilCtrl;

		$this->ctrl = $ilCtrl;
		$this->target_class = $a_target_class;
		parent::ilExplorer($a_target);
		$this->tree = $tree;
		$this->root_id = $this->tree->readRootId();
		$this->order_column = "title";

		$this->setSessionExpandVariable("expand_sol");

		// add here all container objects
		$this->addFilter("root");
		$this->addFilter("cat");
		$this->addFilter("grp");
		$this->addFilter("fold");
		$this->addFilter("crs");

		$this->setFilterMode(IL_FM_POSITIVE);
		$this->setFiltered(true);
	}

	/**
	 * @param int $ref_id
	 */
	public function expandPathByRefId($ref_id)
	{
		/**
		 * @var $tree ilTree
		 */
		global $tree;

		if(!$_SESSION[$this->expand_variable])
		{
			$_SESSION[$this->expand_variable] = array();
		}

		$path = $tree->getPathId($ref_id);
		foreach((array)$path as $node_id)
		{
			if(!in_array($node_id, $_SESSION[$this->expand_variable]))
				$_SESSION[$this->expand_variable][] = $node_id;
		}

		$this->expanded = $_SESSION[$this->expand_variable];
	}

	function setSelectableType($a_type)
	{
		$this->selectable_type  = $a_type;
	}
	function setRefId($a_ref_id)
	{
		$this->ref_id = $a_ref_id;
	}
	

	function buildLinkTarget($a_node_id, $a_type)
	{
		if($a_type == $this->selectable_type)
		{
			$this->ctrl->setParameterByClass($this->target_class,'source_id',$a_node_id);
			return $this->ctrl->getLinkTargetByClass($this->target_class,'linkChilds');
		}
		else
		{
			$this->ctrl->setParameterByClass($this->target_class,"ref_id",$this->ref_id);
			return $this->ctrl->getLinkTargetByClass($this->target_class,'addSolutionHint');
		}

	}

	function buildFrameTarget($a_type, $a_child = 0, $a_obj_id = 0)
	{
		return '';
	}

	function isClickable($a_type, $a_ref_id)
	{
		return $a_type == $this->selectable_type and $a_ref_id != $this->ref_id;
	}

	function showChilds($a_ref_id)
	{
		global $rbacsystem;

		if ($a_ref_id == 0)
		{
			return true;
		}

		if ($rbacsystem->checkAccess("read", $a_ref_id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	* overwritten method from base class
	* @access	public
	* @param	integer obj_id
	* @param	integer array options
	* @return	string
	*/
	function formatHeader($a_obj_id,$a_option)
	{
		global $lng, $ilias;

		include_once "./Services/UICore/classes/class.ilTemplate.php";
		$tpl = new ilTemplate("tpl.tree.html", true, true, "Services/UIComponent/Explorer");

		$tpl->setCurrentBlock("text");
		$tpl->setVariable("OBJ_TITLE", $lng->txt("repository"));
		$tpl->parseCurrentBlock();

		$tpl->setCurrentBlock("row");
		$tpl->parseCurrentBlock();

		$this->output[] = $tpl->get();
	}

} // END class ilSolutionExplorer
?>
