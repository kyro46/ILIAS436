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
* QTI outcomes label class
*
* @author Helmut Schottmüller <hschottm@gmx.de>
* @version $Id: class.ilQTIOutcomes.php 12481 2006-11-09 00:04:34Z hschottm $
*
* @package assessment
*/
class ilQTIOutcomes
{
	var $comment;
	var $decvar;
	
	function ilQTIOutcomes()
	{
		$this->decvar = array();
	}
	
	function setComment($a_comment)
	{
		$this->comment = $a_comment;
	}
	
	function getComment()
	{
		return $this->comment;
	}
	
	function addDecvar($a_decvar)
	{
		array_push($this->decvar, $a_decvar);
	}
}
?>
