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
* Class for indexing hmtl ,pdf, txt files and htlm Learning modules.
* This indexer is called by cron.php
*
* @author Stefan Meyer <smeyer.ilias@gmx.de>
* @version $Id: class.ilLuceneIndexer.php 21874 2009-09-23 15:36:28Z smeyer $
*
* @package ilias
*/

class ilLuceneIndexer
{
	/**
	 * Constructor 
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * index 
	 * @return 
	 */
	public function index()
	{
		global $ilSetting,$ilLog;
		
		try
		{
			include_once './Services/WebServices/RPC/classes/class.ilRpcClientFactory.php';
			$res = ilRpcClientFactory::factory('RPCIndexHandler')->index(
				CLIENT_ID.'_'.$ilSetting->get('inst_id',0),
				true
			);
		}
		catch(XML_RPC2_FaultException $e)
		{
			// TODO: better error handling
			$ilLog->write(__METHOD__.': '.$e->getMessage());
			return;
		}
		catch(Exception $e)
		{
			$ilLog->write(__METHOD__.': '.$e->getMessage());
			return;
		}
	}
	

}
?>
