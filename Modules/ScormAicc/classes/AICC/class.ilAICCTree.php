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

require_once("./Modules/ScormAicc/classes/SCORM/class.ilSCORMTree.php");

/**
* AICC Object Tree
*
* @version $Id: class.ilAICCTree.php 12711 2006-12-01 15:24:41Z akill $
*
* @ingroup ModulesScormAicc
*/
class ilAICCTree extends ilSCORMTree
{

	/**
	* Constructor
	*
	* @param	int		$a_id		tree id (= AICC Learning Module Object ID)
	* @access	public
	*/
	function ilAICCTree($a_id = 0)
	{
		parent::ilTree($a_id);
		$this->setTableNames('scorm_tree','aicc_object');
		$this->setTreeTablePK('slm_id');
	}
	

}
?>
