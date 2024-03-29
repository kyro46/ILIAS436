<?php
/*
        +-----------------------------------------------------------------------------+
        | ILIAS open source                                                           |
        +-----------------------------------------------------------------------------+
        | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
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

include_once('./Services/Table/classes/class.ilTable2GUI.php');

/**
*
* @author Helmut Schottmüller <ilias@aurealis.de>
* @version $Id: class.ilSurveyResultsCumulatedTableGUI.php 41478 2013-04-17 12:42:26Z jluetzen $
*
* @ingroup ModulesSurvey
*/

class ilSurveyResultsCumulatedTableGUI extends ilTable2GUI
{
	private $totalcount;
	/**
	 * Constructor
	 *
	 * @access public
	 * @param
	 * @return
	 */
	public function __construct($a_parent_obj, $a_parent_cmd, $detail)
	{
		$this->setId("svy_cum");
		parent::__construct($a_parent_obj, $a_parent_cmd);

		global $lng, $ilCtrl;

		$this->lng = $lng;
		$this->ctrl = $ilCtrl;
		$this->counter = 1;
		$this->totalcount = 0;
		
		$this->setFormName('invitegroups');
		$this->setStyle('table', 'fullwidth');

		$this->addColumn($this->lng->txt("title"),'title', '');
		foreach ($this->getSelectedColumns() as $c)
		{
			if (strcmp($c, 'question') == 0) $this->addColumn($this->lng->txt("question"),'question', '');
			if (strcmp($c, 'question_type') == 0) $this->addColumn($this->lng->txt("question_type"),'question_type', '');
			if (strcmp($c, 'users_answered') == 0) $this->addColumn($this->lng->txt("users_answered"),'users_answered', '');
			if (strcmp($c, 'users_skipped') == 0) $this->addColumn($this->lng->txt("users_skipped"),'users_skipped', '');
			if (strcmp($c, 'mode') == 0) $this->addColumn($this->lng->txt("mode"),'mode', '');
			if (strcmp($c, 'mode_nr_of_selections') == 0) $this->addColumn($this->lng->txt("mode_nr_of_selections"),'mode_nr_of_selections', '');
			if (strcmp($c, 'median') == 0) $this->addColumn($this->lng->txt("median"),'median', '');
			if (strcmp($c, 'arithmetic_mean') == 0) $this->addColumn($this->lng->txt("arithmetic_mean"),'arithmetic_mean', '');
		}
	
		$this->setRowTemplate("tpl.il_svy_svy_results_cumulated_row.html", "Modules/Survey");

		$this->addCommandButton('printEvaluation', $this->lng->txt('print'), 'javascript:window.print(); return false;'); // #10944

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));

		$this->enable('header');
		$this->disable('sort');
		$this->disable('select_all');
	}

	function getSelectableColumns()
	{
		global $lng;
		$cols["question"] = array(
			"txt" => $lng->txt("question"),
			"default" => true
		);
		$cols["question_type"] = array(
			"txt" => $lng->txt("question_type"),
			"default" => true
		);
		$cols["users_answered"] = array(
			"txt" => $lng->txt("users_answered"),
			"default" => true
		);
		$cols["users_skipped"] = array(
			"txt" => $lng->txt("users_skipped"),
			"default" => true
		);
		$cols["mode"] = array(
			"txt" => $lng->txt("mode"),
			"default" => false
		);
		$cols["mode_nr_of_selections"] = array(
			"txt" => $lng->txt("mode_nr_of_selections"),
			"default" => false
		);
		$cols["median"] = array(
			"txt" => $lng->txt("median"),
			"default" => true
		);
		$cols["arithmetic_mean"] = array(
			"txt" => $lng->txt("arithmetic_mean"),
			"default" => true
		);
		return $cols;
	}

	/**
	 * fill row 
	 *
	 * @access public
	 * @param
	 * @return
	 */
	public function fillRow($data)
	{
		if (strlen($data['counter']))
		{
			$this->tpl->setCurrentBlock('counter');
			$this->tpl->setVariable("COUNTER", $data['counter']);
			$this->tpl->parseCurrentBlock();
			$this->totalcount++;
		}
		$this->tpl->setVariable("TITLE", $data['title']);
		$this->tpl->setVariable("CSS_ROW", ($this->totalcount % 2 == 1) ? 'tblrow1' : 'tblrow2');
		foreach ($this->getSelectedColumns() as $c)
		{
			if (strcmp($c, 'question') == 0)
			{
				$this->tpl->setCurrentBlock('question');
				$this->tpl->setVariable("QUESTION", $data['question']);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'question_type') == 0)
			{
				$this->tpl->setCurrentBlock('question_type');
				$this->tpl->setVariable("QUESTION_TYPE", $data['question_type']);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'users_answered') == 0)
			{
				$this->tpl->setCurrentBlock('users_answered');
				$this->tpl->setVariable("USERS_ANSWERED", $data['users_answered']);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'users_skipped') == 0)
			{
				$this->tpl->setCurrentBlock('users_skipped');
				$this->tpl->setVariable("USERS_SKIPPED", $data['users_skipped']);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'mode') == 0)
			{
				$this->tpl->setCurrentBlock('mode');
				$this->tpl->setVariable("MODE", $data['mode']);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'mode_nr_of_selections') == 0)
			{
				$this->tpl->setCurrentBlock('mode_nr_of_selections');
				$this->tpl->setVariable("MODE_NR_OF_SELECTIONS", strlen($data['mode_nr_of_selections']) ? $data['mode_nr_of_selections'] : 0);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'median') == 0)
			{
				$this->tpl->setCurrentBlock('median');
				$this->tpl->setVariable("MEDIAN", $data['median']);
				$this->tpl->parseCurrentBlock();
			}
			if (strcmp($c, 'arithmetic_mean') == 0)
			{
				$this->tpl->setCurrentBlock('arithmetic_mean');
				$this->tpl->setVariable("ARITHMETIC_MEAN", $data['arithmetic_mean']);
				$this->tpl->parseCurrentBlock();
			}
		}
	}
}
?>