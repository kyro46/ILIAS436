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

include_once("./Services/Object/classes/class.ilObjectAccess.php");

/**
* Class ilObjRootFolderAccess
*
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id: class.ilObjUserFolderAccess.php 33501 2012-03-03 11:11:05Z akill $
*
*/
class ilObjUserFolderAccess extends ilObjectAccess
{
	/**
	 * check whether goto script will succeed
	 */
	function _checkGoto($a_target)
	{
		global $ilAccess;

		$a_target = USER_FOLDER_ID;

		if ($ilAccess->checkAccess("read", "", $a_target))
		{
			return true;
		}
		return false;
	}

}

?>
