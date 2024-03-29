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
* class ilTimingCache
*
* @author Stefan Meyer <meyer@leifos.com> 
* @version $Id: class.ilTimingCache.php 40078 2013-02-22 17:35:15Z jluetzen $
* 
*/
include_once 'Services/Object/classes/class.ilObjectActivation.php';
include_once 'Modules/Course/classes/Timings/class.ilTimingPlaned.php';

class ilTimingCache
{
	function &_getTimings($a_ref_id)
	{
		static $cache = array();

		if(isset($cache[$a_ref_id]))
		{
			return $cache[$a_ref_id];
		}
		$cache[$a_ref_id]['item'] = ilObjectActivation::getItem($a_ref_id);
		$cache[$a_ref_id]['user'] = ilTimingPlaned::_getPlanedTimingsByItem($a_ref_id);

		return $cache[$a_ref_id];
	}
		
	function _showWarning($a_ref_id,$a_usr_id)
	{
		global $objDefinition;
		
		include_once './Services/Tracking/classes/class.ilLPCollectionCache.php';
		include_once './Services/Tracking/classes/class.ilLPStatus.php';
		include_once './Services/Tracking/classes/class.ilLPObjSettings.php';

		global $ilObjDataCache;
		$obj_id = $ilObjDataCache->lookupObjId($a_ref_id);

		// if completed no warning
		if(ilLPStatus::_lookupStatus($obj_id, $a_usr_id) == LP_STATUS_COMPLETED_NUM)
		{
			return false;
		}
		// if editing time reached => show warning
		$timings =& ilTimingCache::_getTimings($a_ref_id);
		if($timings['item']['timing_type'] == ilObjectActivation::TIMINGS_PRESETTING)
		{
			if($timings['item']['changeable'] and $timings['user'][$a_usr_id]['end'])
			{
				$end = $timings['user'][$a_usr_id]['end'];
			}
			else
			{
				$end = $timings['item']['suggestion_end'];
			}
			if($end < time())
			{
				return true;
			}
		}

		// objective_ids would get confused with ref_ids !
        if(ilLPObjSettings::_lookupMode($obj_id) != LP_MODE_OBJECTIVES &&
            $objDefinition->isContainer(ilObject::_lookupType($obj_id)))
        {
			// No check subitems
			foreach(ilLPCollectionCache::_getItems($obj_id) as $item)
			{
				if(ilTimingCache::_showWarning($item,$a_usr_id))
				{
					return true;
				}
			}
		}
		
		// Really ???
		return false;
	}			
}
?>