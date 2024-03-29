<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once "Modules/Group/classes/class.ilGroupParticipants.php";
include_once "Modules/Course/classes/class.ilCourseParticipants.php";
include_once "Services/PersonalWorkspace/classes/class.ilWorkspaceAccessGUI.php";

/**
 * Access handler for personal workspace
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @version $Id: class.ilPersonalDesktopGUI.php 26976 2010-12-16 13:24:38Z akill $
 * 
 * @ingroup ServicesPersonalWorkspace
 */
class ilWorkspaceAccessHandler
{
	protected $tree; // [ilTree]

	public function __construct(ilTree $a_tree = null)
	{
		global $ilUser, $lng;
		
		$lng->loadLanguageModule("wsp");
		
		if(!$a_tree)
		{
			include_once "Services/PersonalWorkspace/classes/class.ilWorkspaceTree.php";
			$a_tree = new ilWorkspaceTree($ilUser->getId());
		}
		$this->tree = $a_tree;
	}
	
	/**
	 * Get workspace tree
	 * 
	 * @return ilWorkspaceTree
	 */
	public function getTree()
	{
		return $this->tree;
	}

	/**
	 * check access for an object
	 *
	 * @param	string		$a_permission
	 * @param	string		$a_cmd
	 * @param	int			$a_node_id
	 * @param	string		$a_type (optional)
	 * @return	bool
	 */
	public function checkAccess($a_permission, $a_cmd, $a_node_id, $a_type = "")
	{
		global $ilUser;

		return $this->checkAccessOfUser($this->tree, $ilUser->getId(),$a_permission, $a_cmd, $a_node_id, $a_type);
	}

	/**
	 * check access for an object
	 *
	 * @param	ilTree		$a_tree
	 * @param	integer		$a_user_id
	 * @param	string		$a_permission
	 * @param	string		$a_cmd
	 * @param	int			$a_node_id
	 * @param	string		$a_type (optional)
	 * @return	bool
	 */
	public function checkAccessOfUser(ilTree $a_tree, $a_user_id, $a_permission, $a_cmd, $a_node_id, $a_type = "")
	{
		global $rbacreview, $ilUser;

		// :TODO: create permission for parent node with type ?!
		
		// tree root is read-only
		if($a_permission == "write")
		{
			if($a_tree->readRootId() == $a_node_id)
			{
				return false;
			}
		}
		
		// node owner has all rights
		if($a_tree->lookupOwner($a_node_id) == $a_user_id)
		{
			return true;
		}
		
		// other users can only read
		if($a_permission == "read" || $a_permission == "visible")
		{
			// get all objects with explicit permission
			$objects = $this->getPermissions($a_node_id);
			if($objects)
			{								
				// check if given user is member of object or has role
				foreach($objects as $obj_id)
				{
					switch($obj_id)
					{
						case ilWorkspaceAccessGUI::PERMISSION_ALL:				
							return true;
								
						case ilWorkspaceAccessGUI::PERMISSION_ALL_PASSWORD:
							// check against input kept in session
							if(self::getSharedNodePassword($a_node_id) == self::getSharedSessionPassword($a_node_id) || 
								$a_permission == "visible")
							{
								return true;
							}
							break;
					
						case ilWorkspaceAccessGUI::PERMISSION_REGISTERED:
							if($ilUser->getId() != ANONYMOUS_USER_ID)
							{
								return true;
							}
							break;
						
						default:
							switch(ilObject::_lookupType($obj_id))
							{
								case "grp":
									// member of group?
									if(ilGroupParticipants::_getInstanceByObjId($obj_id)->isAssigned($a_user_id))
									{
										return true;
									}
									break;

								case "crs":
									// member of course?
									if(ilCourseParticipants::_getInstanceByObjId($obj_id)->isAssigned($a_user_id))
									{
										return true;
									}
									break;

								case "role":
									// has role?
									if($rbacreview->isAssigned($a_user_id, $obj_id))
									{
										return true;
									}
									break;

								case "usr":
									// direct assignment
									if($a_user_id == $obj_id)
									{
										return true;
									}
									break;
							}
							break;
					}
				}
			}
		}
		
		return false;
	}

	/**
	 * Set permissions after creating node/object
	 * 
	 * @param int $a_parent_node_id
	 * @param int $a_node_id
	 */
	public function setPermissions($a_parent_node_id, $a_node_id)
	{
		// nothing to do as owner has irrefutable rights to any workspace object
	}

	/**
	 * Add permission to node for object
	 *
	 * @param int $a_node_id
	 * @param int $a_object_id
	 * @param string $a_extended_data
	 * @return bool
	 */
	public function addPermission($a_node_id, $a_object_id, $a_extended_data = null)
	{
		global $ilDB, $ilUser;

		// tree owner must not be added
		if($this->tree->getTreeId() == $ilUser->getId() &&
			$a_object_id == $ilUser->getId())
		{
			return false;
		}

		$ilDB->manipulate("INSERT INTO acl_ws (node_id, object_id, extended_data)".
			" VALUES (".$ilDB->quote($a_node_id, "integer").", ".
			$ilDB->quote($a_object_id, "integer").",".
			$ilDB->quote($a_extended_data, "text").")");
		return true;
	}

	/**
	 * Remove permission[s] (for object) to node
	 *
	 * @param int $a_node_id
	 * @param int $a_object_id 
	 */
	public function removePermission($a_node_id, $a_object_id = null)
	{
		global $ilDB;
		
		$query = "DELETE FROM acl_ws".
			" WHERE node_id = ".$ilDB->quote($a_node_id, "integer");

		if($a_object_id)
		{
			$query .= " AND object_id = ".$ilDB->quote($a_object_id, "integer");
		}

		return $ilDB->manipulate($query);
	}

	/**
	 * Get all permissions to node
	 *
	 * @param int $a_node_id
	 * @return array
	 */
	public static function getPermissions($a_node_id)
	{
		global $ilDB, $ilSetting;
		
		$publish_enabled = $ilSetting->get("enable_global_profiles");
		$publish_perm = array(ilWorkspaceAccessGUI::PERMISSION_ALL, 
			ilWorkspaceAccessGUI::PERMISSION_ALL_PASSWORD);

		$set = $ilDB->query("SELECT object_id FROM acl_ws".
			" WHERE node_id = ".$ilDB->quote($a_node_id, "integer"));
		$res = array();
		while($row = $ilDB->fetchAssoc($set))
		{
			if($publish_enabled || !in_array($row["object_id"], $publish_perm))
			{
				$res[] = $row["object_id"];
			}
		}
		return $res;
	}
	
	public function hasRegisteredPermission($a_node_id)
	{
		global $ilDB;

		$set = $ilDB->query("SELECT object_id FROM acl_ws".
			" WHERE node_id = ".$ilDB->quote($a_node_id, "integer").
			" AND object_id = ".$ilDB->quote(ilWorkspaceAccessGUI::PERMISSION_REGISTERED, "integer"));
		return (bool)$ilDB->numRows($set);
	}
	
	public function hasGlobalPermission($a_node_id)
	{
		global $ilDB, $ilSetting;
		
		if(!$ilSetting->get("enable_global_profiles"))
		{
			return false;
		}
		
		$set = $ilDB->query("SELECT object_id FROM acl_ws".
			" WHERE node_id = ".$ilDB->quote($a_node_id, "integer").
			" AND object_id = ".$ilDB->quote(ilWorkspaceAccessGUI::PERMISSION_ALL, "integer"));
		return (bool)$ilDB->numRows($set);
	}
	
	public function hasGlobalPasswordPermission($a_node_id)
	{
		global $ilDB, $ilSetting;
		
		if(!$ilSetting->get("enable_global_profiles"))
		{
			return false;
		}

		$set = $ilDB->query("SELECT object_id FROM acl_ws".
			" WHERE node_id = ".$ilDB->quote($a_node_id, "integer").
			" AND object_id = ".$ilDB->quote(ilWorkspaceAccessGUI::PERMISSION_ALL_PASSWORD, "integer"));
		return (bool)$ilDB->numRows($set);
	}
	
	public static function getPossibleSharedTargets()
	{
		global $ilUser, $ilSetting;
		
		include_once "Services/PersonalWorkspace/classes/class.ilWorkspaceAccessGUI.php";
		include_once "Services/Membership/classes/class.ilParticipants.php";
		$grp_ids = ilParticipants::_getMembershipByType($ilUser->getId(), "grp");
		$crs_ids = ilParticipants::_getMembershipByType($ilUser->getId(), "crs");
		
		$obj_ids = array_merge($grp_ids, $crs_ids);
		$obj_ids[] = $ilUser->getId();
		$obj_ids[] = ilWorkspaceAccessGUI::PERMISSION_REGISTERED;	
		
		if($ilSetting->get("enable_global_profiles"))
		{
			$obj_ids[] = ilWorkspaceAccessGUI::PERMISSION_ALL;
			$obj_ids[] = ilWorkspaceAccessGUI::PERMISSION_ALL_PASSWORD;
		}		

		return $obj_ids;
	}
	
	public function getSharedOwners()
	{
		global $ilUser, $ilDB;
		
		$obj_ids = $this->getPossibleSharedTargets();
		
		$user_ids = array();
		$set = $ilDB->query("SELECT DISTINCT(obj.owner), u.lastname, u.firstname, u.title".
			" FROM object_data obj".
			" JOIN object_reference_ws ref ON (obj.obj_id = ref.obj_id)".
			" JOIN tree_workspace tree ON (tree.child = ref.wsp_id)".
			" JOIN acl_ws acl ON (acl.node_id = tree.child)".
			" JOIN usr_data u on (u.usr_id = obj.owner)".
			" WHERE ".$ilDB->in("acl.object_id", $obj_ids, "", "integer").
			" AND obj.owner <> ".$ilDB->quote($ilUser->getId(), "integer").
			" ORDER BY u.lastname, u.firstname, u.title");
		while ($row = $ilDB->fetchAssoc($set))
		{
			$user_ids[$row["owner"]] = $row["lastname"].", ".$row["firstname"];
			if($row["title"])
			{
				$user_ids[$row["owner"]] .= ", ".$row["title"];
			}
		}
		
		return $user_ids;
	}
	
	public function getSharedObjects($a_owner_id)
	{
		global $ilDB;
		
		$obj_ids = $this->getPossibleSharedTargets();
		
		$res = array();
		$set = $ilDB->query("SELECT ref.wsp_id,obj.obj_id".
			" FROM object_data obj".
			" JOIN object_reference_ws ref ON (obj.obj_id = ref.obj_id)".
			" JOIN tree_workspace tree ON (tree.child = ref.wsp_id)".
			" JOIN acl_ws acl ON (acl.node_id = tree.child)".
			" WHERE ".$ilDB->in("acl.object_id", $obj_ids, "", "integer").
			" AND obj.owner = ".$ilDB->quote($a_owner_id, "integer"));
		while ($row = $ilDB->fetchAssoc($set))
		{
			$res[$row["wsp_id"]] = $row["obj_id"];
		}
	
		return $res;
	}
	
	public static function getSharedNodePassword($a_node_id)
	{
		global $ilDB;
		
		include_once "Services/PersonalWorkspace/classes/class.ilWorkspaceAccessGUI.php";
		
		$set = $ilDB->query("SELECT * FROM acl_ws".
			" WHERE node_id = ".$ilDB->quote($a_node_id, "integer").
			" AND object_id = ".$ilDB->quote(ilWorkspaceAccessGUI::PERMISSION_ALL_PASSWORD, "integer"));
		$res = $ilDB->fetchAssoc($set);
		if($res)
		{
			return $res["extended_data"];
		}
	}
	
	public static function keepSharedSessionPassword($a_node_id, $a_password) 
	{
		$_SESSION["ilshpw_".$a_node_id] = $a_password;
	}
	
	public static function getSharedSessionPassword($a_node_id)
	{
		return $_SESSION["ilshpw_".$a_node_id];
	}
	
	public static function getGotoLink($a_node_id, $a_obj_id, $a_additional = null)
	{
		include_once('./Services/Link/classes/class.ilLink.php');
		return ilLink::_getStaticLink($a_node_id, ilObject::_lookupType($a_obj_id), true, $a_additional."_wsp");
	}		
	
	public function getObjectsIShare()
	{
		global $ilDB, $ilUser;
		
		$res = array();
		$set = $ilDB->query("SELECT ref.wsp_id,obj.obj_id".
			" FROM object_data obj".
			" JOIN object_reference_ws ref ON (obj.obj_id = ref.obj_id)".
			" JOIN tree_workspace tree ON (tree.child = ref.wsp_id)".
			" JOIN acl_ws acl ON (acl.node_id = tree.child)".
			" WHERE obj.owner = ".$ilDB->quote($ilUser->getId(), "integer"));
		while ($row = $ilDB->fetchAssoc($set))
		{
			$res[$row["wsp_id"]] = $row["obj_id"];
		}			
		
		return $res;
	}
	
	public static function getObjectDataFromNode($a_node_id)
	{
		global $ilDB;
		
		$set = $ilDB->query("SELECT obj.obj_id, obj.type, obj.title".
			" FROM object_reference_ws ref".
			" JOIN tree_workspace tree ON (tree.child = ref.wsp_id)".
			" JOIN object_data obj ON (ref.obj_id = obj.obj_id)".
			" WHERE ref.wsp_id = ".$ilDB->quote($a_node_id, "integer"));
		return $ilDB->fetchAssoc($set);
	}	
}

?>