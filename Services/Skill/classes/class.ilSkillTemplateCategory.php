<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Skill/classes/class.ilSkillTreeNode.php");

/**
 * Skill Template Category
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 * @ingroup ServicesSkill
 */
class ilSkillTemplateCategory extends ilSkillTreeNode
{
	var $id;

	/**
	 * Constructor
	 * @access	public
	 */
	function __construct($a_id = 0)
	{
		parent::ilSkillTreeNode($a_id);
		$this->setType("sctp");
	}

	/**
	 * Copy skill category
	 */
	function copy()
	{
		$sctp = new ilSkillTemplateCategory();
		$sctp->setTitle($this->getTitle());
		$sctp->setType($this->getType());
		$sctp->setOrderNr($this->getOrderNr());
		$sctp->create();

		return $sctp;
	}

}
?>
