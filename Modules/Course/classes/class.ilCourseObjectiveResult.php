<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

define('IL_OBJECTIVE_STATUS_EMPTY','empty');
define('IL_OBJECTIVE_STATUS_PRETEST','pretest');
define('IL_OBJECTIVE_STATUS_FINAL','final');
define('IL_OBJECTIVE_STATUS_NONE','none');
define('IL_OBJECTIVE_STATUS_FINISHED','finished');
define('IL_OBJECTIVE_STATUS_PRETEST_NON_SUGGEST','pretest_non_suggest');

/**
* class ilcourseobjective
*
* @author Stefan Meyer <meyer@leifos.com> 
* @version $Id: class.ilCourseObjectiveResult.php 32374 2011-12-30 11:36:13Z smeyer $
* 
*/
class ilCourseObjectiveResult
{
	var $db = null;
	var $user_id = null;

	
	function ilCourseObjectiveResult($a_usr_id)
	{
		global $ilDB;

		$this->db =& $ilDB;

		$this->user_id = $a_usr_id;
	}
	function getUserId()
	{
		return $this->user_id;
	}

	function getAccomplished($a_crs_id)
	{
		return ilCourseObjectiveResult::_getAccomplished($this->getUserId(),$a_crs_id);
	}
	function _getAccomplished($a_user_id,$a_crs_id)
	{
		global $ilDB;

		include_once 'Modules/Course/classes/class.ilCourseObjective.php';
		$objectives = ilCourseObjective::_getObjectiveIds($a_crs_id);

		if(!is_array($objectives))
		{
			return array();
		}
		$query = "SELECT objective_id FROM crs_objective_status ".
			"WHERE ".$ilDB->in('objective_id',$objectives,false,'integer').' '.
			"AND user_id = ".$ilDB->quote($a_user_id ,'integer')." ";
		$res = $ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$accomplished[] = $row->objective_id;
		}
		return $accomplished ? $accomplished : array();
	}

	function getSuggested($a_crs_id,$a_status = IL_OBJECTIVE_STATUS_FINAL)
	{
		return ilCourseObjectiveResult::_getSuggested($this->getUserId(),$a_crs_id,$a_status);
	}
	
	function _getSuggested($a_user_id,$a_crs_id,$a_status = IL_OBJECTIVE_STATUS_FINAL)
	{
		global $ilDB;

		include_once './Modules/Course/classes/class.ilCourseObjective.php';
		$objectives = ilCourseObjective::_getObjectiveIds($a_crs_id);

		$finished = array();
		if($a_status == IL_OBJECTIVE_STATUS_FINAL or
		   $a_status == IL_OBJECTIVE_STATUS_FINISHED)
		{
			// check finished
			$query = "SELECT objective_id FROM crs_objective_status ".
				"WHERE ".$ilDB->in('objective_id',$objectives,false,'integer')." ".
				"AND user_id = ".$ilDB->quote($a_user_id ,'integer')." ";
			$res = $ilDB->query($query);
			while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
			{
				$finished[] = $row->objective_id;
			}
		}
		else
		{
			// Pretest 
			$query = "SELECT objective_id FROM crs_objective_status_p ".
				"WHERE ".$ilDB->in('objective_id',$objectives,false,'integer').' '.
				"AND user_id = ".$ilDB->quote($a_user_id ,'integer');
			$res = $ilDB->query($query);
			while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
			{
				$finished[] = $row->objective_id;
			}
		}
		foreach($objectives as $objective_id)
		{
			if(!in_array($objective_id,$finished))
			{
				$suggested[] = $objective_id;
			}
		}
		return $suggested ? $suggested : array();
	}
	
	/**
	 * get suggested questions ids
	 * @param object $a_usr_id
	 * @param object $a_crs_id
	 * @return 
	 */
	public static function getSuggestedQuestions($a_usr_id,$a_crs_id)
	{
		foreach(self::_getSuggested($a_usr_id,$a_crs_id) as $objective_id)
		{
			include_once './Modules/Course/classes/class.ilCourseObjectiveQuestion.php';
			$obj = new ilCourseObjectiveQuestion($objective_id);
			foreach($obj->getFinalTestQuestions() as $qst)
			{
				$qsts[] = $qst['question_id'];
			}
		}
		return $qsts ? $qsts : array();
	}

	function reset($a_course_id)
	{
		global $ilDB;
		
		include_once './Modules/Course/classes/class.ilCourseObjective.php';
		include_once './Modules/Course/classes/class.ilCourseObjectiveQuestion.php';


		foreach($objectives = ilCourseObjective::_getObjectiveIds($a_course_id) as $objective_id)
		{
			$tmp_obj_question =& new ilCourseObjectiveQuestion($objective_id);
		
			foreach($tmp_obj_question->getTests() as $test_data)
			{
				#$this->__deleteEntries($tmp_obj_question->getQuestionsByTest($test_data['ref_id']));
				
				if($tmp_test =& ilObjectFactory::getInstanceByRefId($test_data['ref_id']))
				{
					$tmp_test->removeTestResultsForUser($this->getUserId());
					unset($tmp_test);
				}
			}
		}

		if(count($objectives))
		{
			$query = "DELETE FROM crs_objective_status ".
				"WHERE ".$ilDB->in('objective_id',$objectives,false,'integer').' '.
				"AND user_id = ".$ilDB->quote($this->getUserId() ,'integer')." ";
			$res = $ilDB->manipulate($query);

			$query = "DELETE FROM crs_objective_status_p ".
				"WHERE ".$ilDB->in('objective_id',$objectives,false,'integer').' '.
				"AND user_id = ".$ilDB->quote($this->getUserId())."";
			$res = $ilDB->manipulate($query);
		}

		include_once("./Services/Tracking/classes/class.ilLPStatusWrapper.php");
		ilLPStatusWrapper::_updateStatus($a_course_id, $this->getUserId());
		
		return true;
	}

	function getStatus($a_course_id)
	{
		include_once './Modules/TestQuestionPool/classes/class.assQuestion.php';
		include_once 'Modules/Course/classes/class.ilCourseObjective.php';
		$objective_ids = ilCourseObjective::_getObjectiveIds($a_course_id);
		$objectives = ilCourseObjectiveResult::_readAssignedObjectives($objective_ids);
		$accomplished = $this->getAccomplished($a_course_id);
		$suggested = $this->getSuggested($a_course_id);

		if(!count($objective_ids))
		{
			return IL_OBJECTIVE_STATUS_EMPTY;
		}

		if(count($accomplished) == count($objective_ids))
		{
			return IL_OBJECTIVE_STATUS_FINISHED;
		}

		$all_pretest_answered = false;
		$all_final_answered = false;
		foreach($objectives as $data)
		{
			if(assQuestion::_areAnswered($this->getUserId(),$data['questions']))
			{
				if($data['tst_status'])
				{
					$all_final_answered = true;
				}
				else
				{
					$all_pretest_answered = true;
				}
			}
		}
		if($all_final_answered)
		{
			return IL_OBJECTIVE_STATUS_FINAL;
		}
		if($all_pretest_answered and 
		   !count($suggested))
		{
			return IL_OBJECTIVE_STATUS_PRETEST_NON_SUGGEST;
		}
		elseif($all_pretest_answered)
		{
			return IL_OBJECTIVE_STATUS_PRETEST;
		}
		return IL_OBJECTIVE_STATUS_NONE;
	}

	function hasAccomplishedObjective($a_objective_id)
	{
		global $ilDB;
		
		$query = "SELECT status FROM crs_objective_status ".
			"WHERE objective_id = ".$ilDB->quote($a_objective_id ,'integer')." ".
			"AND user_id = ".$ilDB->quote($this->getUserId() ,'integer')."";

		$res = $this->db->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			return true;
		}
		return false;
	}

	function readStatus($a_crs_id)
	{
		include_once './Modules/Course/classes/class.ilCourseObjective.php';

		$objective_ids = ilCourseObjective::_getObjectiveIds($a_crs_id);
		$objectives = ilCourseObjectiveResult::_readAssignedObjectives($objective_ids);
		ilCourseObjectiveResult::_updateObjectiveStatus($this->getUserId(),$objectives);
		return true;
	}
	



	// PRIVATE
	function __deleteEntries($a_objective_ids)
	{
		global $ilLog;
		
		$ilLog->logStack();
		#$ilLog(__METHOD__.': Call of deprecated method.');
		
		return true;
	}

	public static function _deleteUser($user_id)
	{
		global $ilDB;

		$query = "DELETE FROM crs_objective_status ".
			"WHERE user_id = ".$ilDB->quote($user_id ,'integer')." ";
		$res = $ilDB->manipulate($query);

		$query = "DELETE FROM crs_objective_status_p ".
			"WHERE user_id = ".$ilDB->quote($user_id ,'integer')." ";
		$res = $ilDB->manipulate($query);
		return true;
	}

	function _updateObjectiveResult($a_user_id,$a_active_id,$a_question_id)
	{
		// find all objectives this question is assigned to
		if(!$objectives = ilCourseObjectiveResult::_readAssignedObjectivesOfQuestion($a_question_id))
		{
			// no objectives found. TODO user has passed a test. After that questions of that test are assigned to an objective.
			// => User has not passed
			return true;
		}
		ilCourseObjectiveResult::_updateObjectiveStatus($a_user_id,$objectives);
		
		return true;
	}

	function _readAssignedObjectivesOfQuestion($a_question_id)
	{
		global $ilDB;

		// get all objtives and questions this current question is assigned to
		$query = "SELECT q2.question_id qid,q2.objective_id ob FROM crs_objective_qst q1, ".
			"crs_objective_qst q2 ".
			"WHERE q1.question_id = ".$ilDB->quote($a_question_id ,'integer')." ".
			"AND q1.objective_id = q2.objective_id ";

		$res = $ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$objectives['all_objectives'][$row->ob] = $row->ob;
			$objectives['all_questions'][$row->qid] = $row->qid;
		}
		if(!is_array($objectives))
		{
			return false;
		}
		$objectives['objectives'] = ilCourseObjectiveResult::_readAssignedObjectives($objectives['all_objectives']);
		return $objectives ? $objectives : array();
	}


	function _readAssignedObjectives($a_all_objectives)
	{
		global $ilDB;

		// Read necessary points
		$query = "SELECT t.objective_id obj,t.ref_id ref, question_id,tst_status,tst_limit ".
			"FROM crs_objective_tst t JOIN crs_objective_qst q ".
			"ON (t.objective_id = q.objective_id AND t.ref_id = q.ref_id) ".
			"WHERE ".$ilDB->in('t.objective_id',$a_all_objectives,false,'integer');

		$res = $ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			/*
			$objectives[$row->obj."_".$row->ref]['questions'][$row->question_id] = $row->question_id;
			$objectives[$row->obj."_".$row->ref]['tst_status'] = $row->tst_status;
			$objectives[$row->obj."_".$row->ref]['tst_limit'] = $row->tst_limit;
			$objectives[$row->obj."_".$row->ref]['objective_id'] = $row->obj;
			*/
			
			$objectives[$row->obj."_".$row->tst_status]['questions'][$row->question_id] = $row->question_id;
			$objectives[$row->obj."_".$row->tst_status]['tst_status'] = $row->tst_status;
			$objectives[$row->obj."_".$row->tst_status]['tst_limit'] = $row->tst_limit;
			$objectives[$row->obj."_".$row->tst_status]['objective_id'] = $row->obj;
			
			
		}
		return $objectives ? $objectives : array();
	}

	function _updateObjectiveStatus($a_user_id,$objectives)
	{
		global $ilDB,$ilUser;

		if(!count($objectives['all_questions']) or
		   !count($objectives['all_objectives']))
		{
			return false;
		}
		// Read reachable points
		$query = "SELECT question_id,points FROM qpl_questions ".
			"WHERE ".$ilDB->in('question_id',(array) $objectives['all_questions'],false,'integer');
		$res = $ilDB->query($query);
		while($row = $ilDB->fetchAssoc($res))
		{
			$objectives['all_question_points'][$row['question_id']]['max_points'] = $row['points'];
		}
		// Read reached points
		$query = "SELECT question_fi, MAX(points) as reached FROM tst_test_result ".
			"JOIN tst_active ON (active_id = active_fi) ".
			"WHERE user_fi = ".$ilDB->quote($a_user_id,'integer')." ".
			"AND ".$ilDB->in('question_fi',(array) $objectives['all_questions'],false,'integer')." ".
			#"AND question_fi IN (".implode(",",ilUtil::quoteArray($objectives['all_questions'])).") ".
			"GROUP BY question_fi,user_fi";
		$res = $ilDB->query($query);
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$objectives['all_question_points'][$row->question_fi]['reached_points'] = $row->reached;
		}
		
		// Check accomplished
		$fullfilled = array();
		$pretest = array();
		foreach($objectives['objectives'] as $kind => $data)
		{
			// objective does not allow to change status
			if(ilCourseObjectiveResult::__isFullfilled($objectives['all_question_points'],$data))
			{
				// Status 0 means pretest fullfilled, status 1 means final test fullfilled
				if($data['tst_status'])
				{
					$fullfilled[] = array($data['objective_id'],$ilUser->getId(),$data['tst_status']);
				}
				else
				{
					$pretest[] = array($data['objective_id'],$ilUser->getId());
				}
			}
		}
		if(count($fullfilled))
		{
			foreach($fullfilled as $fullfilled_arr)
			{
				$ilDB->replace(
					'crs_objective_status',
					array(
						'objective_id' => array('integer',$fullfilled_arr[0]),
						'user_id' => array('integer',$fullfilled_arr[1])
					),
					array(
						'status' => array('integer',$fullfilled_arr[2])
					)
				);
			}
			ilCourseObjectiveResult::__updatePassed($a_user_id,$objectives['all_objectives']);
		}
		if(count($pretest))
		{
			foreach($pretest as $pretest_arr)
			{
				$ilDB->replace(
					'crs_objective_status_p',
					array(
						'objective_id' => array('integer',$pretest_arr[0]),
						'user_id' => array('integer',$pretest_arr[1])
					),
					array()
				);
			}
		}
		return true;
	}

	function __isFullfilled($question_points,$objective_data)
	{
		if(!is_array($objective_data['questions']))
		{
			return false;
		}
		$max_points = 0;
		$reached_points = 0;
		foreach($objective_data['questions'] as $question_id)
		{
			$max_points += $question_points[$question_id]['max_points'];
			$reached_points += $question_points[$question_id]['reached_points'];
		}
		if(!$max_points)
		{
			return false;
		}
			
		return $reached_points >= $objective_data['tst_limit'] ? true : false;
		
		return (($reached_points / $max_points * 100) >= $objective_data['tst_limit']) ? true : false;
	}

	function __updatePassed($a_user_id,$objective_ids)
	{
		global $ilDB;

		$passed = array();
		
		$query = "SELECT COUNT(t1.crs_id) num,t1.crs_id FROM crs_objectives t1 ".
			"JOIN crs_objectives t2 WHERE t1.crs_id = t2.crs_id and  ".
			$ilDB->in('t1.objective_id',$objective_ids,false,'integer')." ".
			"GROUP BY t1.crs_id";
		$res = $ilDB->query($query);
		$crs_ids = array();
		while($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
		{
			$query = "SELECT COUNT(cs.objective_id) num_passed FROM crs_objective_status cs ".
				"JOIN crs_objectives co ON cs.objective_id = co.objective_id ".
				"WHERE crs_id = ".$ilDB->quote($row->crs_id ,'integer')." ".
				"AND user_id = ".$ilDB->quote($a_user_id ,'integer')." ";

			$user_res = $ilDB->query($query);
			while($user_row = $user_res->fetchRow(DB_FETCHMODE_OBJECT))
			{
				if($user_row->num_passed == $row->num)
				{
					$passed[] = $row->crs_id;
				}
			}
			$crs_ids[$row->crs_id] = $row->crs_id;
		}
		if(count($passed))
		{
			foreach($passed as $crs_id)
			{
				include_once('Modules/Course/classes/class.ilCourseParticipants.php');
				$members = ilCourseParticipants::_getInstanceByObjId($crs_id);
				$members->updatePassed($a_user_id,true);
			}
		}
		
		// update tracking status
		foreach ($crs_ids as $cid)
		{
			include_once("./Services/Tracking/classes/class.ilLPStatusWrapper.php");
			ilLPStatusWrapper::_updateStatus($cid, $a_user_id);
		}
	}
		
}
?>