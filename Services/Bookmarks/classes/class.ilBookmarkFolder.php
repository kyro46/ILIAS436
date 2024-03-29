<?php

/* Copyright (c) 1998-2012 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
* bookmark folder
* (note: this class handles personal bookmarks folders only)
*
* @author Alex Killing <alex.killing@gmx.de>
* @author Manfred Thaler <manfred.thaler@endo7.com>
* @version $Id: class.ilBookmarkFolder.php 35228 2012-06-26 22:27:00Z akill $
* @ingroup ServicesBookmarks
*/
class ilBookmarkFolder
{
	/**
	* tree
	* @var object
	* @access private
	*/
	var $tree;

	/**
	* ilias object
	* @var object ilias
	* @access private
	*/
	var $ilias;

	var $id;
	var $title;
	var $parent;

	/**
	* Constructor
	* @access	public
	* @param	integer		user_id (optional)
	*/
	function ilBookmarkFolder($a_bmf_id = 0, $a_tree_id = 0)
	{
		global $ilias;

		// Initiate variables
		$this->ilias =& $ilias;
		if ($a_tree_id == 0)
		{
			$a_tree_id = $_SESSION["AccountId"];
		}

		$this->tree = new ilTree($a_tree_id);
		$this->tree->setTableNames('bookmark_tree','bookmark_data');
		$this->id = $a_bmf_id;

		if(!empty($this->id))
		{
			$this->read();
		}
	}

	/**
	* read bookmark folder data from db
	*/
	function read()
	{
		global $ilias, $ilDB;

		$q = "SELECT * FROM bookmark_data WHERE obj_id = ".
			$ilDB->quote($this->getId(), "integer");
		$bmf_set = $ilDB->query($q);
		if ($ilDB->numRows($bmf_set) == 0)
		{
			$message = "ilBookmarkFolder::read(): Bookmark Folder with id ".$this->getId()." not found!";
			$ilias->raiseError($message,$ilias->error_obj->WARNING);
		}
		else
		{
			$bmf = $ilDB->fetchAssoc($bmf_set);
			$this->setTitle($bmf["title"]);
			$this->setParent($this->tree->getParentId($this->getId()));
		}
	}

	/**
	* delete object data
	*/
	function delete()
	{
		global $ilDB;
		
		$q = "DELETE FROM bookmark_data WHERE obj_id = ".$ilDB->quote($this->getId(), "integer");
		$ilDB->query($q);
	}

	/**
	* create personal bookmark tree
	*/
	function createNewBookmarkTree()
	{
		global $ilDB;

		/*
		$q = "INSERT INTO bookmark_data (user_id, title, target, type) ".
			"VALUES ('".$this->tree->getTreeId()."','dummy_folder','','bmf')";
		$ilDB->query($q);*/
		//$this->tree->addTree($this->tree->getTreeId(), $ilDB->getLastInsertId());
		$this->tree->addTree($this->tree->getTreeId(), 1);
	}

	/**
	* creates new bookmark folder in db
	*
	* note: parent and title must be set
	*/
	function create()
	{
		global $ilDB;
		
		$this->setId($ilDB->nextId("bookmark_data"));
		$q = sprintf(
				"INSERT INTO bookmark_data (obj_id, user_id, title, type) ".
				"VALUES (%s,%s,%s,%s)",
				$ilDB->quote($this->getId(), "integer"),
				$ilDB->quote($_SESSION["AccountId"], "integer"),
				$ilDB->quote($this->getTitle(), "text"),
				$ilDB->quote('bmf', "text")
			);

		$ilDB->manipulate($q);
		$this->tree->insertNode($this->getId(), $this->getParent());
	}
	
	/**
	* Update bookmark folder item
	*/
	function update()
	{
		global $ilDB;
		
		$q = sprintf(
				"UPDATE bookmark_data SET title=%s ".
				"WHERE obj_id=%s",
				$ilDB->quote($this->getTitle(), "text"),
				$ilDB->quote($this->getId(), "integer")
			);
		$ilDB->manipulate($q);
	}


	function getId()
	{
		return $this->id;
	}

	function setId($a_id)
	{
		$this->id = $a_id;
	}

	function getTitle()
	{
		return $this->title;
	}

	function setTitle($a_title)
	{
		$this->title = $a_title;
	}

	function getParent()
	{
		return $this->parent;
	}

	function setParent($a_parent_id)
	{
		$this->parent = $a_parent_id;
	}

	/**
	* lookup bookmark folder title
	*/
	function _lookupTitle($a_bmf_id)
	{
		global $ilDB;

		$q = "SELECT * FROM bookmark_data WHERE obj_id = ".
			$ilDB->quote($a_bmf_id, "integer");
		$bmf_set = $ilDB->query($q);
		$bmf = $ilDB->fetchAssoc($bmf_set);

		return $bmf["title"];
	}

	/**
	* static
	*/
	function getObjects($a_id)
	{
		$a_tree_id = $_SESSION["AccountId"];
		$tree = new ilTree($a_tree_id);
		$tree->setTableNames('bookmark_tree','bookmark_data');

		if(empty($a_id))
		{
			$a_id = $tree->getRootId();
		}

		$childs = $tree->getChilds($a_id, "title");

		$objects = array();
		$bookmarks = array();

		foreach ($childs as $key => $child)
		{
			switch ($child["type"])
			{
				case "bmf":
					$objects[] = $child;
					break;

				case "bm":
					$bookmarks[] = $child;
					break;
			}
		}
		foreach ($bookmarks as $key => $bookmark)
		{
			$objects[] = $bookmark;
		}
		return $objects;
	}
	
	/**
	* Get number of folders and bookmarks for current user.
	*/
	function _getNumberOfObjects()
	{
		$a_tree_id = $_SESSION["AccountId"];
		$tree = new ilTree($a_tree_id);
		$tree->setTableNames('bookmark_tree','bookmark_data');

		$root_node = $tree->getNodeData($tree->getRootId());
		
		if ($root_node["lft"] != "")
		{
			$bmf = $tree->getSubTree($root_node, false, "bmf");
			$bm = $tree->getSubTree($root_node, false, "bm");
		}
		else
		{
			$bmf = array("dummy");
			$bm = array();
		}
		
		return array("folders" => (int) count($bmf) - 1, "bookmarks" => (int) count($bm));
	}

	
	/**
	* static
	*/
	function getObject($a_id)
	{
		$a_tree_id = $_SESSION["AccountId"];
		$tree = new ilTree($a_tree_id);
		$tree->setTableNames('bookmark_tree','bookmark_data');

		if(empty($a_id))
		{
			$a_id = $tree->getRootId();
		}

		$object = $tree->getNodeData($a_id);
		return $object;
	}

	function isRootFolder($a_id)
	{
		$a_tree_id = $_SESSION["AccountId"];
		$tree = new ilTree($a_tree_id);
		$tree->setTableNames('bookmark_tree','bookmark_data');

		if ($a_id == $tree->getRootId())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getRootFolder()
	{
		$a_tree_id = $_SESSION["AccountId"];
		$tree = new ilTree($a_tree_id);
		$tree->setTableNames('bookmark_tree','bookmark_data');

		return $tree->getRootId();
	}

	function _getParentId($a_id)
	{
		$a_tree_id = $_SESSION["AccountId"];
		$tree = new ilTree($a_tree_id);
		$tree->setTableNames('bookmark_tree','bookmark_data');
		return $tree->getParentId($a_id);
	}

}
?>
