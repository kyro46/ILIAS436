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
* Class ilFulltextMediaPoolSearch
*
* class for searching media pool folders and titles of mob's
*
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id: class.ilFulltextMediaPoolSearch.php 23143 2010-03-09 12:15:33Z smeyer $
* 
* @package ilias-search
*
*/
include_once 'Services/Search/classes/class.ilMediaPoolSearch.php';

class ilFulltextMediaPoolSearch extends ilMediaPoolSearch
{

	/**
	* Constructor
	* @access public
	*/
	function ilFulltextMediaPoolSearch(&$qp_obj)
	{
		parent::ilMediaPoolSearch($qp_obj);
	}

	function __createAndCondition()
	{
		// IN BOOLEAN MODE
		if($this->db->isMysql4_0OrHigher())
		{
			$query .= " AND MATCH(title) AGAINST('";
			foreach($this->query_parser->getQuotedWords(true) as $word)
			{
				$query .= $word;
				$query .= '* ';
			}
			$query .= "' IN BOOLEAN MODE) ";
		}
		else
		{
			// i do not see any reason, but MATCH AGAINST(...) OR MATCH AGAINST(...) does not use an index
			$query .= " AND MATCH (title) AGAINST(' ";
			foreach($this->query_parser->getQuotedWords(true) as $word)
			{
				$query .= $word;
				$query .= ' ';
			}
			$query .= "') ";
		}
		return $query;
	}
	
	/**
	 * mob keyword search 
	 * @return 
	 */
	public function __createKeywordAndCondition()
	{
		// IN BOOLEAN MODE
		if($this->db->isMysql4_0OrHigher())
		{
			$query .= " WHERE MATCH(keyword) AGAINST('";
			foreach($this->query_parser->getQuotedWords(true) as $word)
			{
				$query .= $word;
				$query .= '* ';
			}
			$query .= "' IN BOOLEAN MODE) ";
		}
		else
		{
			// i do not see any reason, but MATCH AGAINST(...) OR MATCH AGAINST(...) does not use an index
			$query .= " WHERE MATCH (keyword) AGAINST(' ";
			foreach($this->query_parser->getQuotedWords(true) as $word)
			{
				$query .= $word;
				$query .= ' ';
			}
			$query .= "') ";
		}
		return $query;
	}
}
?>