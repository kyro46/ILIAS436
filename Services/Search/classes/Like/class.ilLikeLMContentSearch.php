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

/**
* Class ilLikeLMContentSearch
*
* class for searching media pool folders and titles of mob's
*
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id: class.ilLikeLMContentSearch.php 23143 2010-03-09 12:15:33Z smeyer $
* 
* @package ilias-search
*
*/
include_once 'Services/Search/classes/class.ilLMContentSearch.php';

class ilLikeLMContentSearch extends ilLMContentSearch
{

	/**
	* Constructor
	* @access public
	*/
	function ilLikeLMContentSearch(&$qp_obj)
	{
		parent::ilLMContentSearch($qp_obj);
	}

	function __createWhereCondition()
	{
		global $ilDB;

		$concat  = " content ";

		$and = "  WHERE ( ";
		$counter = 0;
		foreach($this->query_parser->getQuotedWords() as $word)
		{
			if($counter++)
			{
				$and .= " OR";
			}
			#$and .= $concat;
			#$and .= ("LIKE ('%".$word."%')");
			$and .= $ilDB->like($concat,'clob','%'.$word.'%');

		}
		return $and.") ";
	}
}
?>
