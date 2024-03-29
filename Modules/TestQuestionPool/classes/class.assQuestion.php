<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once "./Modules/Test/classes/inc.AssessmentConstants.php";

/**
 * Abstract basic class which is to be extended by the concrete assessment question type classes
 *
 * The assQuestion class defines and encapsulates basic/common methods and attributes as well
 * as it provides abstract methods that are to be implemented by concrete question type classes.
 *
 * @abstract
 * 
 * @author		Helmut Schottmüller <helmut.schottmueller@mac.com>
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id: class.assQuestion.php 47445 2014-01-22 17:03:37Z bheyser $
 * 
 * @ingroup		ModulesTestQuestionPool
 */
abstract class assQuestion
{
	/**
	* Question id
	*
	* @var integer
	*/
	protected $id;

	/**
	* Question title
	*
	* @var string
	*/
	protected $title;

	/**
	* Question comment
	*
	* @var string
	*/
	protected $comment;

	/**
	* Question owner/creator
	*
	* @var integer
	*/
	protected $owner;

	/**
	* Contains the name of the author
	*
	* @var string
	*/
	protected $author;

	/**
	* The question text
	*
	* @var string
	*/
	protected $question;

	/**
	* The maximum available points for the question
	*
	* @var double
	*/
	protected $points;

	/**
	* Contains estimates working time on a question (HH MM SS)
	*
	* @var array
	*/
	protected $est_working_time;

	/**
	* Indicates whether the answers will be shuffled or not
	*
	* @var boolean
	*/
	protected $shuffle;

	/**
	* The database id of a test in which the question is contained
	*
	* @var integer
	*/
	protected $test_id;

	/**
	* Object id of the container object
	*
	* @var integer
	*/
	protected $obj_id;

	/**
	* The reference to the ILIAS class
	*
	* @var object
	*/
	protected $ilias;

	/**
	* The reference to the Template class
	*
	* @var object
	*/
	protected $tpl;

	/**
	* The reference to the Language class
	*
	* @var object
	*/
	protected $lng;

	/**
	* Contains the output type of a question
	*
	* @var integer
	*/
	protected $outputType;

	/**
	* Array of suggested solutions
	*
	* @var array
	*/
	protected $suggested_solutions;

	/**
	* ID of the "original" question
	*
	* @var integer
	*/
	protected $original_id;

	/**
	* Page object
	*
	* @var object
	*/
	protected $page;

	/**
	* Number of tries
	*/
	private $nr_of_tries;
	
	/**
	* Associative array to store properties
	*/
	private $arrData;

	/**
	 * (Web) Path to images
	 */
	private $export_image_path;
	
	/**
	 * this flag stores the fact wether obligations
	 * are to be considered or not
	 *
	 * @var bool
	 */
	private $obligationsToBeConsidered = false;
	
	/**
	* assQuestion constructor
	*
	* @param string $title A title string to describe the question
	* @param string $comment A comment string to describe the question
	* @param string $author A string containing the name of the questions author
	* @param integer $owner A numerical ID to identify the owner/creator
	* @param string $question Question text
	* @access public
	*/
	function __construct(
		$title = "",
		$comment = "",
		$author = "",
		$owner = -1,
		$question = ""
	)
	{
		global $ilias;
		global $lng;
		global $tpl;

		$this->ilias =& $ilias;
		$this->lng =& $lng;
		$this->tpl =& $tpl;

		$this->original_id = null;
		$this->title = $title;
		$this->comment = $comment;
		$this->page = null;
		$this->author = $author;
		$this->setQuestion($question);
		if (!$this->author)
		{
			$this->author = $this->ilias->account->fullname;
		}
		$this->owner = $owner;
		if ($this->owner <= 0)
		{
			$this->owner = $this->ilias->account->id;
		}
		$this->id = -1;
		$this->test_id = -1;
		$this->suggested_solutions = array();
		$this->shuffle = 1;
		$this->nr_of_tries = "";
		$this->setEstimatedWorkingTime(0,1,0);
		$this->outputType = OUTPUT_HTML;
		$this->arrData = array();
	}

	/**
	* Receives parameters from a QTI parser and creates a valid ILIAS question object
	*
	* @param object $item The QTI item object
	* @param integer $questionpool_id The id of the parent questionpool
	* @param integer $tst_id The id of the parent test if the question is part of a test
	* @param object $tst_object A reference to the parent test object
	* @param integer $question_counter A reference to a question counter to count the questions of an imported question pool
	* @param array $import_mapping An array containing references to included ILIAS objects
	* @access public
	*/
	function fromXML(&$item, &$questionpool_id, &$tst_id, &$tst_object, &$question_counter, &$import_mapping)
	{
		include_once "./Modules/TestQuestionPool/classes/import/qti12/class." . $this->getQuestionType() . "Import.php";
		$classname = $this->getQuestionType() . "Import";
		$import = new $classname($this);
		$import->fromXML($item, $questionpool_id, $tst_id, $tst_object, $question_counter, $import_mapping);
	}
	
	/**
	* Returns a QTI xml representation of the question
	*
	* @return string The QTI xml representation of the question
	* @access public
	*/
	function toXML($a_include_header = true, $a_include_binary = true, $a_shuffle = false, $test_output = false, $force_image_references = false)
	{
		include_once "./Modules/TestQuestionPool/classes/export/qti12/class." . $this->getQuestionType() . "Export.php";
		$classname = $this->getQuestionType() . "Export";
		$export = new $classname($this);
		return $export->toXML($a_include_header, $a_include_binary, $a_shuffle, $test_output, $force_image_references);
	}

	/**
	* Returns true, if a question is complete for use
	*
	* @return boolean True, if the question is complete for use, otherwise false
	* @access public
	*/
	function isComplete()
	{
		return false;
	}

	/**
	* Returns TRUE if the question title exists in the database
	*
	* @param string $title The title of the question
	* @return boolean The result of the title check
	* @access public
	*/
	function questionTitleExists($questionpool_id, $title)
	{
		global $ilDB;
		
		$result = $ilDB->queryF("SELECT * FROM qpl_questions WHERE obj_fi = %s AND title = %s",
			array('integer','text'),
			array($questionpool_id, $title)
		);
		return ($result->numRows() == 1) ? TRUE : FALSE;
	}

	/**
	* Sets the title string of the assQuestion object
	*
	* @param string $title A title string to describe the question
	* @access public
	* @see $title
	*/
	function setTitle($title = "")
	{
		$this->title = $title;
	}

	/**
	* Sets the id of the assQuestion object
	*
	* @param integer $id A unique integer value
	* @access public
	* @see $id
	*/
	function setId($id = -1)
	{
		$this->id = $id;
	}

	/**
	* Sets the test id of the assQuestion object
	*
	* @param integer $id A unique integer value
	* @access public
	* @see $test_id
	*/
	function setTestId($id = -1)
	{
		$this->test_id = $id;
	}

	/**
	* Sets the comment string of the assQuestion object
	*
	* @param string $comment A comment string to describe the question
	* @access public
	* @see $comment
	*/
	function setComment($comment = "")
	{
		$this->comment = $comment;
	}

	/**
	* Sets the output type
	*
	* @param integer $outputType The output type of the question
	* @access public
	* @see $outputType
	*/
	function setOutputType($outputType = OUTPUT_HTML)
	{
		$this->outputType = $outputType;
	}


	/**
	* Sets the shuffle flag
	*
	* @param boolean $shuffle A flag indicating whether the answers are shuffled or not
	* @access public
	* @see $shuffle
	*/
	function setShuffle($shuffle = true)
	{
		if ($shuffle)
		{
			$this->shuffle = 1;
		}
			else
		{
			$this->shuffle = 0;
		}
	}

	/**
	* Sets the estimated working time of a question
	*
	* @param integer $hour Hour
	* @param integer $min Minutes
	* @param integer $sec Seconds
	* @access public
	* @see $comment
	*/
	function setEstimatedWorkingTime($hour=0, $min=0, $sec=0)
	{
		$this->est_working_time = array("h" => (int)$hour, "m" => (int)$min, "s" => (int)$sec);
	}

	/**
	* returns TRUE if the key occurs in an array
	*
	* @param string $arraykey A key to an element in array
	* @param array $array An array to be searched
	* @access public
	*/
	function keyInArray($searchkey, $array)
	{
		if ($searchkey)
		{
			foreach ($array as $key => $value)
			{
				if (strcmp($key, $searchkey)==0)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	* Sets the authors name of the assQuestion object
	*
	* @param string $author A string containing the name of the questions author
	* @access public
	* @see $author
	*/
	function setAuthor($author = "")
	{
		if (!$author)
		{
			$author = $this->ilias->account->fullname;
		}
		$this->author = $author;
	}

	/**
	* Sets the creator/owner ID of the assQuestion object
	*
	* @param integer $owner A numerical ID to identify the owner/creator
	* @access public
	* @see $owner
	*/
	function setOwner($owner = "")
	{
		$this->owner = $owner;
	}

	/**
	* Gets the title string of the assQuestion object
	*
	* @return string The title string to describe the question
	* @access public
	* @see $title
	*/
	function getTitle()
	{
		return $this->title;
	}

	/**
	* Gets the id of the assQuestion object
	*
	* @return integer The id of the assQuestion object
	* @access public
	* @see $id
	*/
	function getId()
	{
		return $this->id;
	}

	/**
	* Gets the shuffle flag
	*
	* @return boolean The shuffle flag
	* @access public
	* @see $shuffle
	*/
	function getShuffle()
	{
		return $this->shuffle;
	}

	/**
	* Gets the test id of the assQuestion object
	*
	* @return integer The test id of the assQuestion object
	* @access public
	* @see $test_id
	*/
	function getTestId()
	{
		return $this->test_id;
	}

	/**
	* Gets the comment string of the assQuestion object
	*
	* @return string The comment string to describe the question
	* @access public
	* @see $comment
	*/
	function getComment()
	{
		return $this->comment;
	}

	/**
	* Gets the output type
	*
	* @return integer The output type of the question
	* @access public
	* @see $outputType
	*/
	function getOutputType()
	{
		return $this->outputType;
	}
	
	/**
	* Returns true if the question type supports JavaScript output
	*
	* @return boolean TRUE if the question type supports JavaScript output, FALSE otherwise
	* @access public
	*/
	function supportsJavascriptOutput()
	{
		return FALSE;
	}

	/**
	* Gets the estimated working time of a question
	*
	* @return array Estimated Working Time of a question
	* @access public
	* @see $est_working_time
	*/
	function getEstimatedWorkingTime()
	{
		if (!$this->est_working_time)
		{
			$this->est_working_time = array("h" => 0, "m" => 0, "s" => 0);
		}
		return $this->est_working_time;
	}

	/**
	* Gets the authors name of the assQuestion object
	*
	* @return string The string containing the name of the questions author
	* @access public
	* @see $author
	*/
	function getAuthor()
	{
		return $this->author;
	}

	/**
	* Gets the creator/owner ID of the assQuestion object
	*
	* @return integer The numerical ID to identify the owner/creator
	* @access public
	* @see $owner
	*/
	function getOwner()
	{
		return $this->owner;
	}

	/**
	* Get the object id of the container object
	*
	* @return integer The object id of the container object
	* @access public
	* @see $obj_id
	*/
	function getObjId()
	{
		return $this->obj_id;
	}

	/**
	* Set the object id of the container object
	*
	* @param integer $obj_id The object id of the container object
	* @access public
	* @see $obj_id
	*/
	function setObjId($obj_id = 0)
	{
		$this->obj_id = $obj_id;
	}

/**
* Returns the maximum points, a learner can reach answering the question
*
* @param integer $question_id The database Id of the question
* @see $points
*/
  function _getMaximumPoints($question_id) 
	{
		global $ilDB;

		$points = 0;
		$result = $ilDB->queryF("SELECT points FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			$points = $row["points"];
		}
		return $points;
  }

	/**
	* Returns question information from the database
	*
	* @param integer $question_id The database Id of the question
	* @return array The database row containing the question data
	*/
	function &_getQuestionInfo($question_id)
	{
		global $ilDB;

		$result = $ilDB->queryF("SELECT qpl_questions.*, qpl_qst_type.type_tag FROM qpl_qst_type, qpl_questions WHERE qpl_questions.question_id = %s AND qpl_questions.question_type_fi = qpl_qst_type.question_type_id",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows())
		{
			return $ilDB->fetchAssoc($result);
		}
		else return array();
	}
	
	/**
	* Returns the number of suggested solutions associated with a question
	*
	* @param integer $question_id The database Id of the question
	* @return integer The number of suggested solutions
	*/
	public static function _getSuggestedSolutionCount($question_id)
	{
		global $ilDB;

		$result = $ilDB->queryF("SELECT suggested_solution_id FROM qpl_sol_sug WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		return $result->numRows();
	}

	/**
	* Returns the output of the suggested solution
	*
	* @param integer $question_id The database Id of the question
	* @return string Suggested solution
	*/
	public static function _getSuggestedSolutionOutput($question_id)
	{
		$question =& assQuestion::_instanciateQuestion($question_id);
		if (!is_object($question)) return "";
		return $question->getSuggestedSolutionOutput();
	}

	public function getSuggestedSolutionOutput()
	{
		$output = array();
		foreach ($this->suggested_solutions as $solution)
		{
			switch ($solution["type"])
			{
				case "lm":
				case "st":
				case "pg":
				case "git":
					array_push($output, '<a href="' . assQuestion::_getInternalLinkHref($solution["internal_link"]) . '">' . $this->lng->txt("solution_hint") . '</a>');
					break;
				case "file":
					array_push($output, '<a href="' . $this->getSuggestedSolutionPathWeb() . $solution["value"]["name"] . '">' . ((strlen($solution["value"]["filenme"])) ? ilUtil::prepareFormOutput($solution["value"]["filenme"]) : $this->lng->txt("solution_hint")) . '</a>');
					break;
				case "text":
					array_push($output, $this->prepareTextareaOutput($solution["value"]));
					break;
			}
		}
		return join($output, "<br />");
	}

/**
* Returns a suggested solution for a given subquestion index
*
* @param integer $question_id The database Id of the question
* @param integer $subquestion_index The index of a subquestion (i.e. a close test gap). Usually 0
* @return array A suggested solution array containing the internal link
* @access public
*/
	function &_getSuggestedSolution($question_id, $subquestion_index = 0)
	{
		global $ilDB;

		$result = $ilDB->queryF("SELECT * FROM qpl_sol_sug WHERE question_fi = %s AND subquestion_index = %s",
			array('integer','integer'),
			array($question_id, $subquestion_index)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			return array(
				"internal_link" => $row["internal_link"],
				"import_id" => $row["import_id"]
			);
		}
		else
		{
			return array();
		}
	}
	
	/**
	* Return the suggested solutions
	*
	* @return array Suggested solutions
	*/
	public function getSuggestedSolutions()
	{
		return $this->suggested_solutions;
	}
	
	/**
	* Returns the points, a learner has reached answering the question
	*
	* @param integer $user_id The database ID of the learner
	* @param integer $test_id The database Id of the test containing the question
	* @param integer $question_id The database Id of the question
	*/
	function _getReachedPoints($active_id, $question_id, $pass = NULL)
	{
		global $ilDB;

		$points = 0;
		if (is_null($pass))
		{
			include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
			$pass = assQuestion::_getSolutionMaxPass($question_id, $active_id);
		}
		$result = $ilDB->queryF("SELECT * FROM tst_test_result WHERE active_fi = %s AND question_fi = %s AND pass = %s",
			array('integer','integer','integer'),
			array($active_id, $question_id, $pass)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			$points = $row["points"];
		}
		return $points;
	}

	/**
	* Returns the points, a learner has reached answering the question
	* This is the fast way to get the points directly from the database.
	*
	* @param integer $user_id The database ID of the learner
	* @param integer $test_id The database Id of the test containing the question
	* @access public
	*/
	function getReachedPoints($active_id, $pass = NULL)
	{
		return round($this->_getReachedPoints($active_id, $this->getId(), $pass), 2);
	}
	
	/**
	* Returns the maximum points, a learner can reach answering the question
	*
	* @access public
	* @see $points
	*/
	function getMaximumPoints()
	{
		return $this->points;
	}
        
        /**
         *  returns the reached points ...
         * - calculated by concrete question type class
         * - adjusted by hint point deduction
         * - adjusted by scoring options
         * ... for given testactive and testpass
         *
         * @param integer $active_id
         * @param integer $pass
         * @return integer $reached_points 
         */
        final public function getAdjustedReachedPoints($active_id, $pass = NULL)
        {
            if (is_null($pass))
            {
                    include_once "./Modules/Test/classes/class.ilObjTest.php";
                    $pass = ilObjTest::_getPass($active_id);
            }
            
            // determine reached points for submitted solution
            $reached_points = $this->calculateReachedPoints($active_id, $pass);

            // deduct points for requested hints from reached points
            require_once 'Modules/TestQuestionPool/classes/class.ilAssQuestionHintTracking.php';
            $requestsStatisticData = ilAssQuestionHintTracking::getRequestStatisticDataByQuestionAndTestpass($this->getId(), $active_id, $pass);
            $reached_points = $reached_points - $requestsStatisticData->getRequestsPoints();

            // adjust reached points regarding to tests scoring options
            $reached_points = $this->adjustReachedPointsByScoringOptions($reached_points, $active_id, $pass);
            
            return $reached_points;
        }
	
	/**
	 * Calculates the question results from a previously saved question solution
	 *
	 * @final
	 * @global ilDB $ilDB
	 * @global ilObjUser $ilUser
	 * @param integer $active_id Active id of the user
	 * @param integer $pass Test pass
	 */
	final public function calculateResultsFromSolution($active_id, $pass = NULL, $obligationsEnabled = false)
	{
		global $ilDB, $ilUser;
		
		if( is_null($pass) )
		{
			include_once "./Modules/Test/classes/class.ilObjTest.php";
			$pass = ilObjTest::_getPass($active_id);
		}
		
		// determine reached points for submitted solution
		$reached_points = $this->calculateReachedPoints($active_id, $pass);

		// deduct points for requested hints from reached points
		require_once 'Modules/TestQuestionPool/classes/class.ilAssQuestionHintTracking.php';
		$requestsStatisticData = ilAssQuestionHintTracking::getRequestStatisticDataByQuestionAndTestpass($this->getId(), $active_id, $pass);
		$reached_points = $reached_points - $requestsStatisticData->getRequestsPoints();

		// adjust reached points regarding to tests scoring options
		$reached_points = $this->adjustReachedPointsByScoringOptions($reached_points, $active_id, $pass);
		
		if( $obligationsEnabled && ilObjTest::isQuestionObligatory($this->getId()) )
		{
		    $isAnswered = $this->isAnswered($active_id, $pass);
		}
		else
		{
		    $isAnswered = true;
		}
		
		if( is_null($reached_points) ) $reached_points = 0;

		$query = "
			DELETE FROM		tst_test_result
			
			WHERE			active_fi = %s
			AND				question_fi = %s
			AND				pass = %s
		";
		
		$affectedRows = $ilDB->manipulateF(
			$query, array("integer", "integer", "integer"), array($active_id, $this->getId(), $pass)
		);

		$next_id = $ilDB->nextId("tst_test_result");
		
		$ilDB->insert('tst_test_result', array(
			'test_result_id'	=> array('integer', $next_id),
			'active_fi'			=> array('integer', $active_id),
			'question_fi'		=> array('integer', $this->getId()),
			'pass'				=> array('integer', $pass),
			'points'			=> array('float', $reached_points),
			'tstamp'			=> array('integer', time()),
			'hint_count'		=> array('integer', $requestsStatisticData->getRequestsCount()),
			'hint_points'		=> array('float', $requestsStatisticData->getRequestsPoints()),
			'answered'			=> array('integer', $isAnswered)
		));
		
		include_once ("./Modules/Test/classes/class.ilObjAssessmentFolder.php");
		
		if( ilObjAssessmentFolder::_enabledAssessmentLogging() )
		{
			$this->logAction(
				sprintf(
					$this->lng->txtlng(
						"assessment", "log_user_answered_question", ilObjAssessmentFolder::_getLogLanguage()
					),
					$reached_points
				),
				$active_id,
				$this->getId()
			);
		}

		// update test pass results
		$this->_updateTestPassResults($active_id, $pass, $obligationsEnabled);

		// Update objective status
		include_once 'Modules/Course/classes/class.ilCourseObjectiveResult.php';
		ilCourseObjectiveResult::_updateObjectiveResult($ilUser->getId(),$active_id,$this->getId());
	}

	/**
	 * persists the working state for current testactive and testpass
	 *
	 * @final
	 * @access public
	 * @param integer $active_id Active id of the user
	 * @param integer $pass Test pass
	 */
	final public function persistWorkingState($active_id, $pass = NULL, $obligationsEnabled = false)
	{
		if( $pass === null )
		{
			require_once 'Modules/Test/classes/class.ilObjTest.php';
			$pass = ilObjTest::_getPass($active_id);
		}
		
		$saveStatus = $this->saveWorkingData($active_id, $pass);
		
		$this->calculateResultsFromSolution($active_id, $pass, $obligationsEnabled);
		
		$this->reworkWorkingData($active_id, $pass, $obligationsEnabled);
		
		return $saveStatus;
	}
	
	/**
	 * Saves the learners input of the question to the database.
	 * 
	 * @abstract
	 * @access public
	 * @param integer $active_id Active id of the user
	 * @param integer $pass Test pass
	 * @return boolean $status
	 */
	abstract public function saveWorkingData($active_id, $pass = NULL);

	/**
	 * Reworks the allready saved working data if neccessary
	 *
	 * @abstract
	 * @access protected
	 * @param integer $active_id
	 * @param integer $pass
	 * @param boolean $obligationsAnswered
	 */
	abstract protected function reworkWorkingData($active_id, $pass, $obligationsAnswered);

	function _updateTestResultCache($active_id)
	{
		global $ilDB;

		include_once "./Modules/Test/classes/class.ilObjTest.php";
		include_once "./Modules/Test/classes/class.assMarkSchema.php";
		
		$pass = ilObjTest::_getResultPass($active_id);

		$query = "
			SELECT		tst_pass_result.*
			FROM		tst_pass_result
			WHERE		active_fi = %s
			AND			pass = %s
		";
		
		$result = $ilDB->queryF(
			$query, array('integer','integer'), array($active_id, $pass)
		);
		
		$row = $ilDB->fetchAssoc($result);
		
		$max = $row['maxpoints'];
		$reached = $row['points'];
		
		$obligationsAnswered = (int)$row['obligations_answered'];
		
		include_once "./Modules/Test/classes/class.assMarkSchema.php";
		
		$percentage = (!$max) ? 0 : ($reached / $max) * 100.0;
		
		$mark = ASS_MarkSchema::_getMatchingMarkFromActiveId($active_id, $percentage);
		
		$isPassed = (  $mark["passed"] ? 1 : 0 );
		$isFailed = ( !$mark["passed"] ? 1 : 0 );
		
		$query = "
			DELETE FROM		tst_result_cache
			WHERE			active_fi = %s
		";
		
		$affectedRows = $ilDB->manipulateF(
			$query, array('integer'), array($active_id)
		);
		
		$ilDB->insert('tst_result_cache', array(
			'active_fi'=> array('integer', $active_id),
			'pass'=> array('integer', strlen($pass) ? $pass : 0),
			'max_points'=> array('float', strlen($max) ? $max : 0),
			'reached_points'=> array('float', strlen($reached) ? $reached : 0),
			'mark_short'=> array('text', strlen($mark["short_name"]) ? $mark["short_name"] : " "),
			'mark_official'=> array('text', strlen($mark["official_name"]) ? $mark["official_name"] : " "),
			'passed'=> array('integer', $isPassed),
			'failed'=> array('integer', $isFailed),
			'tstamp'=> array('integer', time()),
			'hint_count'=> array('integer', $row['hint_count']),
			'hint_points'=> array('float', $row['hint_points']),
			'obligations_answered' => array('integer', $obligationsAnswered)
		));
	}

	function _updateTestPassResults($active_id, $pass, $obligationsEnabled = false)
	{
		global $ilDB;
		
		include_once "./Modules/Test/classes/class.ilObjTest.php";
		
		$data = ilObjTest::_getQuestionCountAndPointsForPassOfParticipant($active_id, $pass);
		$time = ilObjTest::_getWorkingTimeOfParticipantForPass($active_id, $pass);
		
		// update test pass results
		
		$result = $ilDB->queryF("
			SELECT		SUM(points) reachedpoints,
						SUM(hint_count) hint_count,
						SUM(hint_points) hint_points,
						COUNT(question_fi) answeredquestions
			FROM		tst_test_result
			WHERE		active_fi = %s
			AND			pass = %s
			",
			array('integer','integer'),
			array($active_id, $pass)
		);
		
		if ($result->numRows() > 0)
		{
			if( $obligationsEnabled )
			{
				$query = '
					SELECT		count(*) cnt,
								min( answered ) answ
					FROM		tst_test_question
					INNER JOIN	tst_active
					ON			active_id = %s
					AND			tst_test_question.test_fi = tst_active.test_fi
					LEFT JOIN	tst_test_result
					ON			tst_test_result.active_fi = %s
					AND			tst_test_result.pass = %s
					AND			tst_test_question.question_fi = tst_test_result.question_fi
					WHERE		obligatory = 1';

				$result_obligatory = $ilDB->queryF(
					$query, array('integer','integer','integer'), array($active_id, $active_id, $pass)
				);

				$row_obligatory = $ilDB->fetchAssoc($result_obligatory);

				if ($row_obligatory['cnt'] == 0)
				{
					$obligations_answered = 1;
				}
				else
				{
					$obligations_answered = (int) $row_obligatory['answ'];
				}
			}
			else
			{
				$obligations_answered = 1;
			}
			
			$row = $ilDB->fetchAssoc($result);
			
			if( $row['hint_count'] === null ) $row['hint_count'] = 0;
			if( $row['hint_points'] === null ) $row['hint_points'] = 0;
			
			$query = "
				DELETE FROM		tst_pass_result

				WHERE			active_fi = %s
				AND				pass = %s
			";

			$affectedRows = $ilDB->manipulateF(
				$query, array('integer','integer'), array($active_id, $pass)
			);
			
			$ilDB->insert('tst_pass_result', array(
				'active_fi'				=> array('integer', $active_id),
				'pass'					=> array('integer', strlen($pass) ? $pass : 0),
				'points'				=> array('float', $row['reachedpoints'] ? $row['reachedpoints'] : 0),
				'maxpoints'				=> array('float', $data['points']),
				'questioncount'			=> array('integer', $data['count']),
				'answeredquestions'		=> array('integer', $row['answeredquestions']),
				'workingtime'			=> array('integer', $time),
				'tstamp'				=> array('integer', time()),
				'hint_count'			=> array('integer', $row['hint_count']),
				'hint_points'			=> array('float', $row['hint_points']),
				'obligations_answered'	=> array('integer', $obligations_answered)
			));
		}
		
		assQuestion::_updateTestResultCache($active_id);
		
		return array(
			'active_fi' => $active_id,
			'pass' => $pass,
			'points' => ($row["reachedpoints"]) ? $row["reachedpoints"] : 0,
			'maxpoints' => $data["points"],
			'questioncount' => $data["count"],
			'answeredquestions' => $row["answeredquestions"],
			'workingtime' => $time,
			'tstamp' => time(),
			'hint_count' => $row['hint_count'],
			'hint_points' => $row['hint_points'],
			'obligations_answered' => $obligations_answered
		);
	}
	
/**
* Logs an action into the Test&Assessment log
*
* @param string $logtext The log text
* @param integer $question_id If given, saves the question id to the database
* @access public
*/
	function logAction($logtext = "", $active_id = "", $question_id = "")
	{
		global $ilUser;

		$original_id = "";
		if (strcmp($question_id, "") != 0)
		{
			include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
			$original_id = assQuestion::_getOriginalId($question_id);
		}
		include_once "./Modules/Test/classes/class.ilObjAssessmentFolder.php";
		include_once "./Modules/Test/classes/class.ilObjTest.php";
		ilObjAssessmentFolder::_addLog($ilUser->id, ilObjTest::_getObjectIDFromActiveID($active_id), $logtext, $question_id, $original_id);
	}
	
/**
* Logs an action into the Test&Assessment log
*
* @param string $logtext The log text
* @param integer $question_id If given, saves the question id to the database
* @access public
*/
	function _logAction($logtext = "", $active_id = "", $question_id = "")
	{
		global $ilUser;

		$original_id = "";
		if (strcmp($question_id, "") != 0)
		{
			include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
			$original_id = assQuestion::_getOriginalId($question_id);
		}
		include_once "./Modules/Test/classes/class.ilObjAssessmentFolder.php";
		include_once "./Modules/Test/classes/class.ilObjTest.php";
		ilObjAssessmentFolder::_addLog($ilUser->id, ilObjTest::_getObjectIDFromActiveID($active_id), $logtext, $question_id, $original_id);
	}
	
	/**
	* Move an uploaded media file to an public accessible temp dir to present it
	* 
	* @param string $file File path
	* @param string $name Name of the file
	* @access public
	*/
	function moveUploadedMediaFile($file, $name)
	{
		$mediatempdir = CLIENT_WEB_DIR . "/assessment/temp";
		if (!@is_dir($mediatempdir)) ilUtil::createDirectory($mediatempdir);
		$temp_name = tempnam($mediatempdir, $name . "_____");
		$temp_name = str_replace("\\", "/", $temp_name);
		@unlink($temp_name);
		if (!ilUtil::moveUploadedFile($file, $name, $temp_name))
		{
			return FALSE;
		}
		else
		{
			return $temp_name;
		}
	}
	
	/**
	* Returns the path for a suggested solution
	*
	* @access public
	*/
	function getSuggestedSolutionPath() {
		return CLIENT_WEB_DIR . "/assessment/$this->obj_id/$this->id/solution/";
	}

	/**
	* Returns the image path for web accessable images of a question.
	* The image path is under the CLIENT_WEB_DIR in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/images
	*
	* @access public
	*/
	function getJavaPath() {
		return CLIENT_WEB_DIR . "/assessment/$this->obj_id/$this->id/java/";
	}
	
	/**
	* Returns the image path for web accessable images of a question.
	* The image path is under the CLIENT_WEB_DIR in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/images
	*
	* @access public
	*/
	function getImagePath($question_id = null, $object_id = null)
	{
		if( $question_id === null)
		{
			$question_id = $this->id;
		}
		
		if( $object_id === null)
		{
			$object_id = $this->obj_id;
		}
		
		return CLIENT_WEB_DIR . "/assessment/$object_id/$question_id/images/";
	}

	/**
	* Returns the image path for web accessable flash files of a question.
	* The image path is under the CLIENT_WEB_DIR in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/flash
	*
	* @access public
	*/
	function getFlashPath() 
	{
		return CLIENT_WEB_DIR . "/assessment/$this->obj_id/$this->id/flash/";
	}

	/**
	* Returns the web image path for web accessable java applets of a question.
	* The image path is under the web accessable data dir in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/java
	*
	* @access public
	*/
	function getJavaPathWeb()
	{
		include_once "./Services/Utilities/classes/class.ilUtil.php";
		$webdir = ilUtil::removeTrailingPathSeparators(CLIENT_WEB_DIR) . "/assessment/$this->obj_id/$this->id/java/";
		return str_replace(ilUtil::removeTrailingPathSeparators(ILIAS_ABSOLUTE_PATH), ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH), $webdir);
	}

	/**
	* Returns the web path for a suggested solution
	*
	* @access public
	*/
	function getSuggestedSolutionPathWeb() 
	{
		include_once "./Services/Utilities/classes/class.ilUtil.php";
		$webdir = ilUtil::removeTrailingPathSeparators(CLIENT_WEB_DIR) . "/assessment/$this->obj_id/$this->id/solution/";
		return str_replace(ilUtil::removeTrailingPathSeparators(ILIAS_ABSOLUTE_PATH), ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH), $webdir);
	}

	/**
	* Returns the web image path for web accessable images of a question.
	* The image path is under the web accessable data dir in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/images
	*
	* @access public
	*/
	function getImagePathWeb()
	{
		if(!$this->export_image_path)
		{
			include_once "./Services/Utilities/classes/class.ilUtil.php";
			$webdir = ilUtil::removeTrailingPathSeparators(CLIENT_WEB_DIR) . "/assessment/$this->obj_id/$this->id/images/";
			return str_replace(ilUtil::removeTrailingPathSeparators(ILIAS_ABSOLUTE_PATH), ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH), $webdir);
		}
		else
		{
			return $this->export_image_path;
		}
	}

	/**
	* Returns the web image path for web accessable flash applications of a question.
	* The image path is under the web accessable data dir in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/flash
	*
	* @access public
	*/
	function getFlashPathWeb()
	{
		include_once "./Services/Utilities/classes/class.ilUtil.php";
		$webdir = ilUtil::removeTrailingPathSeparators(CLIENT_WEB_DIR) . "/assessment/$this->obj_id/$this->id/flash/";
		return str_replace(ilUtil::removeTrailingPathSeparators(ILIAS_ABSOLUTE_PATH), ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH), $webdir);
	}
	
	/**
	* Loads solutions of a given user from the database an returns it
	*
	* @param integer $test_id The database id of the test containing this question
	* @access public
	* @see $answers
	*/
	function &getSolutionValues($active_id, $pass = NULL)
	{
		global $ilDB;

		$values = array();
		
		if (is_null($pass))
		{
			$pass = $this->getSolutionMaxPass($active_id);
		}		

		$result = $ilDB->queryF("SELECT * FROM tst_solutions WHERE active_fi = %s AND question_fi = %s AND pass = %s ORDER BY solution_id",
			array('integer','integer','integer'),
			array($active_id, $this->getId(), $pass)
		);
		while	($row = $ilDB->fetchAssoc($result))
		{
			array_push($values, $row);
		}

		return $values;
	}

	/**
	* Checks whether the question is in use or not
	*
	* @return boolean The number of datasets which are affected by the use of the query.
	* @access public
	*/
	function isInUse($question_id = "")
	{
		global $ilDB;
		
		if ($question_id < 1) $question_id = $this->getId();
		$result = $ilDB->queryF("SELECT COUNT(qpl_questions.question_id) question_count FROM qpl_questions, tst_test_question WHERE qpl_questions.original_id = %s AND qpl_questions.question_id = tst_test_question.question_fi",
			array('integer'),
			array($question_id)
		);
		$row = $ilDB->fetchAssoc($result);
		$count = $row["question_count"];

		$result = $ilDB->queryF("SELECT DISTINCT tst_active.test_fi, qpl_questions.question_id FROM qpl_questions, tst_test_rnd_qst, tst_active WHERE qpl_questions.original_id = %s AND qpl_questions.question_id = tst_test_rnd_qst.question_fi AND tst_test_rnd_qst.active_fi = tst_active.active_id",
			array('integer'),
			array($question_id)
		);
		$count += $result->numRows();

		return $count;
	}

	/**
	* Checks whether the question is a clone of another question or not
	*
	* @return boolean TRUE if the question is a clone, otherwise FALSE
	* @access public
	*/
	function isClone($question_id = "")
	{
		global $ilDB;
		
		if ($question_id < 1) $question_id = $this->id;
		$result = $ilDB->queryF("SELECT original_id FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		$row = $ilDB->fetchAssoc($result);
		return ($row["original_id"] > 0) ? TRUE : FALSE;
	}

	/**
	* Shuffles the values of a given array
	*
	* @param array $array An array which should be shuffled
	* @access public
	*/
	function pcArrayShuffle($array)
	{
		$keys = array_keys($array);
		shuffle($keys);
		$result = array();
		foreach ($keys as $key)
		{
			$result[$key] = $array[$key];
		}
		return $result;
	}

	/**
	* get question type for question id
	*
	* note: please don't use $this in this class to allow static calls
	*/
	function getQuestionTypeFromDb($question_id)
	{
		global $ilDB;

		$result = $ilDB->queryF("SELECT qpl_qst_type.type_tag FROM qpl_qst_type, qpl_questions WHERE qpl_questions.question_id = %s AND qpl_questions.question_type_fi = qpl_qst_type.question_type_id",
			array('integer'),
			array($question_id)
		);
		$data = $ilDB->fetchAssoc($result);
		return $data["type_tag"];
	}

	/**
	* Returns the name of the additional question data table in the database
	*
	* @return string The additional table name
	* @access public
	*/
	function getAdditionalTableName()
	{
		return "";
	}
	
	/**
	* Returns the name of the answer table in the database
	*
	* @return string The answer table name
	* @access public
	*/
	function getAnswerTableName()
	{
		return "";
	}

	/**
	* Deletes datasets from answers tables
	*
	* @param integer $question_id The question id which should be deleted in the answers table
	* @access public
	*/
	function deleteAnswers($question_id)
	{
		global $ilDB;
		$answer_table_name = $this->getAnswerTableName();
		if (is_array($answer_table_name))
		{
			foreach ($answer_table_name as $table)
			{
				if (strlen($table))
				{
					$affectedRows = $ilDB->manipulateF("DELETE FROM $table WHERE question_fi = %s",
						array('integer'),
						array($question_id)
					);
				}
			}
		}
		else
		{
			if (strlen($answer_table_name))
			{
				$affectedRows = $ilDB->manipulateF("DELETE FROM $answer_table_name WHERE question_fi = %s",
					array('integer'),
					array($question_id)
				);
			}
		}
	}

	/**
	* Deletes datasets from the additional question table in the database
	*
	* @param integer $question_id The question id which should be deleted in the additional question table
	* @access public
	*/
	function deleteAdditionalTableData($question_id)
	{
		global $ilDB;
		$additional_table_name = $this->getAdditionalTableName();
		if (is_array($additional_table_name))
		{
			foreach ($additional_table_name as $table)
			{
				if (strlen($table))
				{
					$affectedRows = $ilDB->manipulateF("DELETE FROM $table WHERE question_fi = %s",
						array('integer'),
						array($question_id)
					);
				}
			}
		}
		else
		{
			if (strlen($additional_table_name))
			{
				$affectedRows = $ilDB->manipulateF("DELETE FROM $additional_table_name WHERE question_fi = %s",
					array('integer'),
					array($question_id)
				);
			}
		}
	}

	/**
	* Deletes the page object of a question with a given ID
	*
	* @param integer $question_id The database id of the question
	* @access protected
	*/
	protected function deletePageOfQuestion($question_id)
	{
		include_once "./Services/COPage/classes/class.ilPageObject.php";
		$page = new ilPageObject("qpl", $question_id);
		$page->delete();
		return true;
	}

	/**
	* Deletes a question and all materials from the database
	*
	* @param integer $question_id The database id of the question
	* @access private
	*/
	public function delete($question_id)
	{
		global $ilDB, $ilLog;
		
		if ($question_id < 1) return true; // nothing to do

		$result = $ilDB->queryF("SELECT obj_fi FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			$obj_id = $row["obj_fi"];
		}
		else
		{
			return true; // nothing to do
		}
		try
		{
			$this->deletePageOfQuestion($question_id);
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Could not delete page of question $question_id: $e");
			return false;
		}
		
		$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($affectedRows == 0) return false;

		try
		{
			$this->deleteAdditionalTableData($question_id);
			$this->deleteAnswers($question_id);
			$this->deleteFeedbackGeneric($question_id);
			$this->deleteFeedbackSpecific($question_id);
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Could not delete additional table data of question $question_id: $e");
			return false;
		}

		try
		{
			// delete the question in the tst_test_question table (list of test questions)
			$affectedRows = $ilDB->manipulateF("DELETE FROM tst_test_question WHERE question_fi = %s", 
				array('integer'),
				array($question_id)
			);
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Could not delete delete question $question_id from a test: $e");
			return false;
		}

		try
		{
			// delete suggested solutions contained in the question
			$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_sol_sug WHERE question_fi = %s", 
				array('integer'),
				array($question_id)
			);
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Could not delete suggested solutions of question $question_id: $e");
			return false;
		}
				
		try
		{
			$directory = CLIENT_WEB_DIR . "/assessment/" . $obj_id . "/$question_id";
			if (preg_match("/\d+/", $obj_id) and preg_match("/\d+/", $question_id) and is_dir($directory))
			{
				include_once "./Services/Utilities/classes/class.ilUtil.php";
				ilUtil::delDir($directory);
			}
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Could not delete question file directory $directory of question $question_id: $e");
			return false;
		}

		try
		{
			include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
			$mobs = ilObjMediaObject::_getMobsOfObject("qpl:html", $question_id);
			// remaining usages are not in text anymore -> delete them
			// and media objects (note: delete method of ilObjMediaObject
			// checks whether object is used in another context; if yes,
			// the object is not deleted!)
			foreach($mobs as $mob)
			{
				ilObjMediaObject::_removeUsage($mob, "qpl:html", $question_id);
				if (ilObjMediaObject::_exists($mob))
				{
					$mob_obj =& new ilObjMediaObject($mob);
					$mob_obj->delete();
				}
			}
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Error deleting the media objects of question $question_id: $e");
			return false;
		}
		
		require_once 'Modules/TestQuestionPool/classes/class.ilAssQuestionHintTracking.php';
		ilAssQuestionHintTracking::deleteRequestsByQuestionIds(array($question_id));
		
		require_once 'Modules/TestQuestionPool/classes/class.ilAssQuestionHintList.php';
		ilAssQuestionHintList::deleteHintsByQuestionIds(array($question_id));

		try
		{
			// update question count of question pool
			include_once "./Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php";
			ilObjQuestionPool::_updateQuestionCount($this->getObjId());
		}
		catch (Exception $e)
		{
			$ilLog->write("EXCEPTION: Error updating the question pool question count of question pool " . $this->getObjId() . " when deleting question $question_id: $e");
			return false;
		}
		return true;
	}

	/**
	* get total number of answers
	*/
	function getTotalAnswers()
	{
		return $this->_getTotalAnswers($this->id);
	}

	/**
	* get number of answers for question id (static)
	* note: do not use $this inside this method
	*
	* @param	int		$a_q_id		question id
	*/
	function _getTotalAnswers($a_q_id)
	{
		global $ilDB;

		// get all question references to the question id
		$result = $ilDB->queryF("SELECT question_id FROM qpl_questions WHERE original_id = %s OR question_id = %s",
			array('integer','integer'),
			array($a_q_id, $a_q_id)
		);
		if ($result->numRows() == 0)
		{
			return 0;
		}
		$found_id = array();
		while ($row = $ilDB->fetchAssoc($result))
		{
			array_push($found_id, $row["question_id"]);
		}

		$result = $ilDB->query("SELECT * FROM tst_test_result WHERE " . $ilDB->in('question_fi', $found_id, false, 'integer'));

		return $result->numRows();
	}


	/**
	* get number of answers for question id (static)
	* note: do not use $this inside this method
	*
	* @param	int		$a_q_id		question id
	*/
	function _getTotalRightAnswers($a_q_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT question_id FROM qpl_questions WHERE original_id = %s OR question_id = %s",
			array('integer','integer'),
			array($a_q_id, $a_q_id)
		);
		if ($result->numRows() == 0)
		{
			return 0;
		}
		$found_id = array();
		while ($row = $ilDB->fetchAssoc($result))
		{
			array_push($found_id, $row["question_id"]);
		}
		$result = $ilDB->query("SELECT * FROM tst_test_result WHERE " . $ilDB->in('question_fi', $found_id, false, 'integer'));
		$answers = array();
		while ($row = $ilDB->fetchAssoc($result))
		{
			$reached = $row["points"]; 
			include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
			$max = assQuestion::_getMaximumPoints($row["question_fi"]);
			array_push($answers, array("reached" => $reached, "max" => $max));
		}
		$max = 0.0;
		$reached = 0.0;
		foreach ($answers as $key => $value)
		{
			$max += $value["max"];
			$reached += $value["reached"];
		}
		if ($max > 0)
		{
			return $reached / $max;
		}
		else
		{
			return 0;
		}
	}

	/**
	* Returns the title of a question
	*
	* @param	int		$a_q_id		question id
	*/
	function _getTitle($a_q_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT title FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($a_q_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			return $row["title"];
		}
		else
		{
			return "";
		}
	}
	
		/**
	* Returns question text
	*
	* @param	int		$a_q_id		question id
	*/
	function _getQuestionText($a_q_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT question_text FROM qpl_questions WHERE question_id = %s",
				array('integer'),
				array($a_q_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			return $row["question_text"];
		}
		else
		{
			return "";
		}
	}
	
	
	function copyXHTMLMediaObjectsOfQuestion($a_q_id)
	{
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mobs = ilObjMediaObject::_getMobsOfObject("qpl:html", $a_q_id);
		foreach ($mobs as $mob)
		{
			ilObjMediaObject::_saveUsage($mob, "qpl:html", $this->getId());
		}
	}
	
	function syncXHTMLMediaObjectsOfQuestion()
	{
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mobs = ilObjMediaObject::_getMobsOfObject("qpl:html", $this->getId());
		foreach ($mobs as $mob)
		{
			ilObjMediaObject::_saveUsage($mob, "qpl:html", $this->original_id);
		}
	}
	
	/**
	* create page object of question
	*/
	function createPageObject()
	{
		$qpl_id = $this->getObjId();

		include_once "./Services/COPage/classes/class.ilPageObject.php";
		$this->page = new ilPageObject("qpl", 0);
		$this->page->setId($this->getId());
		$this->page->setParentId($qpl_id);
		$this->page->setXMLContent("<PageObject><PageContent>".
			"<Question QRef=\"il__qst_".$this->getId()."\"/>".
			"</PageContent></PageObject>");
		$this->page->create();
	}

	function copyPageOfQuestion($a_q_id)
	{
		if ($a_q_id > 0)
		{
			include_once "./Services/COPage/classes/class.ilPageObject.php";
			$page = new ilPageObject("qpl", $a_q_id);

			$xml = str_replace("il__qst_".$a_q_id, "il__qst_".$this->id, $page->getXMLContent());
			$this->page->setXMLContent($xml);
			$this->page->saveMobUsage($xml);
			$this->page->updateFromXML();
		}
	}

	function getPageOfQuestion()
	{
		include_once "./Services/COPage/classes/class.ilPageObject.php";
		$page = new ilPageObject("qpl", $this->id);
		return $page->getXMLContent();
	}

/**
* Returns the question type of a question with a given id
*
* @param integer $question_id The database id of the question
* @result string The question type string
* @access private
*/
	function _getQuestionType($question_id) 
	{
		global $ilDB;

		if ($question_id < 1) return "";
		$result = $ilDB->queryF("SELECT type_tag FROM qpl_questions, qpl_qst_type WHERE qpl_questions.question_id = %s AND qpl_questions.question_type_fi = qpl_qst_type.question_type_id",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1) 
		{
			$data = $ilDB->fetchAssoc($result);
			return $data["type_tag"];
		}
		else 
		{
			return "";
		}
	}

/**
* Returns the question title of a question with a given id
*
* @param integer $question_id The database id of the question
* @result string The question title
* @access private
*/
	function _getQuestionTitle($question_id) 
	{
		global $ilDB;
		
		if ($question_id < 1) return "";

		$result = $ilDB->queryF("SELECT title FROM qpl_questions WHERE qpl_questions.question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1) 
		{
			$data = $ilDB->fetchAssoc($result);
			return $data["title"];
		}
		else 
		{
			return "";
		}
	}

	function setOriginalId($original_id)
	{
		$this->original_id = $original_id;
	}
	
	function getOriginalId()
	{
		return $this->original_id;
	}

/**
* Loads the question from the database
*
* @param integer $question_id A unique key which defines the question in the database
* @access public
*/
	function loadFromDb($question_id)
	{
		global $ilDB;
		
		$result = $ilDB->queryF("SELECT * FROM qpl_sol_sug WHERE question_fi = %s",
			array('integer'),
			array($this->getId())
		);
		$this->suggested_solutions = array();
		if ($result->numRows())
		{
			include_once("./Services/RTE/classes/class.ilRTE.php");
			while ($row = $ilDB->fetchAssoc($result))
			{
				$value = (is_array(unserialize($row["value"]))) ? unserialize($row["value"]) : ilRTE::_replaceMediaObjectImageSrc($row["value"], 1);
				$this->suggested_solutions[$row["subquestion_index"]] = array(
					"type" => $row["type"],
					"value" => $value,
					"internal_link" => $row["internal_link"],
					"import_id" => $row["import_id"]
				);
			}
		}
	}

	/**
	* Creates a new question without an owner when a new question is created
	* This assures that an ID is given to the question if a file upload or something else occurs
	*
	* @return integer ID of the new question
	*/
	public function createNewQuestion($a_create_page = true)
	{
		global $ilDB, $ilUser;
		
		$complete = "0";
		$estw_time = $this->getEstimatedWorkingTime();
		$estw_time = sprintf("%02d:%02d:%02d", $estw_time['h'], $estw_time['m'], $estw_time['s']);
		$obj_id = ($this->getObjId() <= 0) ? (ilObject::_lookupObjId((strlen($_GET["ref_id"])) ? $_GET["ref_id"] : $_POST["sel_qpl"])) : $this->getObjId();
		if ($obj_id > 0)
		{			
			if($a_create_page)
			{
				$tstamp = 0;
			}
			else
			{
				// question pool must not try to purge
				$tstamp = time();
			}
			
			$next_id = $ilDB->nextId('qpl_questions');
			$affectedRows = $ilDB->insert("qpl_questions", array(
				"question_id" => array("integer", $next_id),
				"question_type_fi" => array("integer", $this->getQuestionTypeID()),
				"obj_fi" => array("integer", $obj_id),
				"title" => array("text", NULL),
				"description" => array("text", NULL),
				"author" => array("text", $this->getAuthor()),
				"owner" => array("integer", $ilUser->getId()),
				"question_text" => array("clob", NULL),
				"points" => array("float", 0),
				"nr_of_tries" => array("integer", 1),
				"working_time" => array("text", $estw_time),
				"complete" => array("text", $complete),
				"created" => array("integer", time()),
				"original_id" => array("integer", NULL),
				"tstamp" => array("integer", $tstamp)
			));
			$this->setId($next_id);
			
			if($a_create_page)
			{
				// create page object of question
				$this->createPageObject();
			}
		}
		return $this->getId();
	}

	public function saveQuestionDataToDb($original_id = "")
	{
		global $ilDB;

		$estw_time = $this->getEstimatedWorkingTime();
		$estw_time = sprintf("%02d:%02d:%02d", $estw_time['h'], $estw_time['m'], $estw_time['s']);

		// cleanup RTE images which are not inserted into the question text
		include_once("./Services/RTE/classes/class.ilRTE.php");
		if ($this->getId() == -1)
		{
			// Neuen Datensatz schreiben
			$next_id = $ilDB->nextId('qpl_questions');
			$affectedRows = $ilDB->insert("qpl_questions", array(
				"question_id" => array("integer", $next_id),
				"question_type_fi" => array("integer", $this->getQuestionTypeID()),
				"obj_fi" => array("integer", $this->getObjId()),
				"title" => array("text", $this->getTitle()),
				"description" => array("text", $this->getComment()),
				"author" => array("text", $this->getAuthor()),
				"owner" => array("integer", $this->getOwner()),
				"question_text" => array("clob", ilRTE::_replaceMediaObjectImageSrc($this->getQuestion(), 0)),
				"points" => array("float", $this->getMaximumPoints()),
				"working_time" => array("text", $estw_time),
				"nr_of_tries" => array("integer", (strlen($this->getNrOfTries())) ? $this->getNrOfTries() : 1),
				"created" => array("integer", time()),
				"original_id" => array("integer", ($original_id) ? $original_id : NULL),
				"tstamp" => array("integer", time())
			));
			$this->setId($next_id);
			// create page object of question
			$this->createPageObject();
		}
		else
		{
			// Vorhandenen Datensatz aktualisieren
			$affectedRows = $ilDB->update("qpl_questions", array(
				"obj_fi" => array("integer", $this->getObjId()),
				"title" => array("text", $this->getTitle()),
				"description" => array("text", $this->getComment()),
				"author" => array("text", $this->getAuthor()),
				"question_text" => array("clob", ilRTE::_replaceMediaObjectImageSrc($this->getQuestion(), 0)),
				"points" => array("float", $this->getMaximumPoints()),
				"nr_of_tries" => array("integer", (strlen($this->getNrOfTries())) ? $this->getNrOfTries() : 1),
				"working_time" => array("text", $estw_time),
				"tstamp" => array("integer", time())
			), array(
			"question_id" => array("integer", $this->getId())
			));
		}
	}

	/**
	* Saves the question to the database
	*
	* @param integer $original_id
	* @access public
	*/
	function saveToDb($original_id = "")
	{
		global $ilDB;

		$this->updateSuggestedSolutions();
		
		// remove unused media objects from ILIAS
		$this->cleanupMediaObjectUsage();

		$complete = "0";
		if ($this->isComplete())
		{
			$complete = "1";
		}

			// update the question time stamp and completion status
		$affectedRows = $ilDB->manipulateF("UPDATE qpl_questions SET tstamp = %s, owner = %s, complete = %s WHERE question_id = %s",
			array('integer','integer', 'integer','text'),
			array(time(), ($this->getOwner() <= 0) ? $this->ilias->account->id : $this->getOwner(), $complete, $this->getId())
		);

		// update question count of question pool
		include_once "./Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php";
		ilObjQuestionPool::_updateQuestionCount($this->obj_id);
	}

        public function setNewOriginalId($newId) {
            global $ilDB;
            $ilDB->manipulateF("UPDATE qpl_questions SET tstamp = %s, original_id = %s WHERE question_id = %s",
                    array('integer','integer', 'text'),
                    array(time(), $newId, $this->getId())
            );
        }

	/**
	* Will be called when a question is duplicated (inside a question pool or for insertion in a test)
	*/
	protected function onDuplicate($originalParentId, $originalQuestionId, $duplicateParentId, $duplicateQuestionId)
	{
		$this->duplicateSuggestedSolutionFiles($originalParentId, $originalQuestionId);

		// duplicate question hints
		$this->duplicateQuestionHints($originalQuestionId, $this->getId());
	}
	
	/**
	* Will be called when a question is copied (into another question pool)
	*/
	protected function onCopy($source_questionpool_id, $source_question_id)
	{
		$this->copySuggestedSolutionFiles($source_questionpool_id, $source_question_id);
	}
	
/**
* Deletes all suggestes solutions in the database
*/
	public function deleteSuggestedSolutions()
	{
		global $ilDB;
		// delete the links in the qpl_sol_sug table
		$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_sol_sug WHERE question_fi = %s",
			array('integer'),
			array($this->getId())
		);
		// delete the links in the int_link table
		include_once "./Services/COPage/classes/class.ilInternalLink.php";
		ilInternalLink::_deleteAllLinksOfSource("qst", $this->getId());
		$this->suggested_solutions = array();
		ilUtil::delDir($this->getSuggestedSolutionPath());
	}
	
/**
* Returns a suggested solution for a given subquestion index
*
* @param integer $subquestion_index The index of a subquestion (i.e. a close test gap). Usually 0
* @return array A suggested solution array containing the internal link
* @access public
*/
	function getSuggestedSolution($subquestion_index = 0)
	{
		if (array_key_exists($subquestion_index, $this->suggested_solutions))
		{
			return $this->suggested_solutions[$subquestion_index];
		}
		else
		{
			return array();
		}
	}

/**
* Returns the title of a suggested solution at a given subquestion_index.
* This can be usable for displaying suggested solutions
*
* @param integer $subquestion_index The index of a subquestion (i.e. a close test gap). Usually 0
* @return string A string containing the type and title of the internal link
* @access public
*/
	function getSuggestedSolutionTitle($subquestion_index = 0)
	{
		if (array_key_exists($subquestion_index, $this->suggested_solutions))
		{
			$title = $this->suggested_solutions[$subquestion_index]["internal_link"];
			// TO DO: resolve internal link an get link type and title
		}
		else
		{
			$title = "";
		}
		return $title;
	}

/**
* Sets a suggested solution for the question.
* If there is more than one subquestion (i.e. close questions) may enter a subquestion index.
*
* @param string $solution_id An internal link pointing to the suggested solution
* @param integer $subquestion_index The index of a subquestion (i.e. a close test gap). Usually 0
* @param boolean $is_import A boolean indication that the internal link was imported from another ILIAS installation
* @access public
*/
	function setSuggestedSolution($solution_id = "", $subquestion_index = 0, $is_import = false)
	{
		if (strcmp($solution_id, "") != 0)
		{
			$import_id = "";
			if ($is_import)
			{
				$import_id = $solution_id;
				$solution_id = $this->_resolveInternalLink($import_id);
			}
			$this->suggested_solutions[$subquestion_index] = array(
				"internal_link" => $solution_id,
				"import_id" => $import_id
			);
		}
	}

	/**
	* Duplicates the files of a suggested solution if the question is duplicated
	*/
	protected function duplicateSuggestedSolutionFiles($parent_id, $question_id)
	{
		global $ilLog;

		foreach ($this->suggested_solutions as $index => $solution)
		{
			if (strcmp($solution["type"], "file") == 0)
			{
				$filepath = $this->getSuggestedSolutionPath();
				$filepath_original = str_replace(
					"/{$this->obj_id}/{$this->id}/solution",
					"/$parent_id/$question_id/solution",
					$filepath
				);
				if (!file_exists($filepath))
				{
					ilUtil::makeDirParents($filepath);
				}
				$filename = $solution["value"]["name"];
				if (strlen($filename))
				{
					if (!copy($filepath_original . $filename, $filepath . $filename))
					{
						$ilLog->write("File could not be duplicated!!!!", $ilLog->ERROR);
						$ilLog->write("object: " . print_r($this, TRUE), $ilLog->ERROR);
					}
				}
			}
		}
	}

	/**
	* Syncs the files of a suggested solution if the question is synced
	*/
	protected function syncSuggestedSolutionFiles($original_id)
	{
		global $ilLog;

		$filepath = $this->getSuggestedSolutionPath();
		$filepath_original = str_replace("/$this->id/solution", "/$original_id/solution", $filepath);
		ilUtil::delDir($filepath_original);
		foreach ($this->suggested_solutions as $index => $solution)
		{
			if (strcmp($solution["type"], "file") == 0)
			{
				if (!file_exists($filepath_original))
				{
					ilUtil::makeDirParents($filepath_original);
				}
				$filename = $solution["value"]["name"];
				if (strlen($filename))
				{
					if (!@copy($filepath . $filename, $filepath_original . $filename))
					{
						$ilLog->write("File could not be duplicated!!!!", $ilLog->ERROR);
						$ilLog->write("object: " . print_r($this, TRUE), $ilLog->ERROR);
					}
				}
			}
		}
	}

	protected function copySuggestedSolutionFiles($source_questionpool_id, $source_question_id)
	{
		global $ilLog;

		foreach ($this->suggested_solutions as $index => $solution)
		{
			if (strcmp($solution["type"], "file") == 0)
			{
				$filepath = $this->getSuggestedSolutionPath();
				$filepath_original = str_replace("/$this->obj_id/$this->id/solution", "/$source_questionpool_id/$source_question_id/solution", $filepath);
				if (!file_exists($filepath))
				{
					ilUtil::makeDirParents($filepath);
				}
				$filename = $solution["value"]["name"];
				if (strlen($filename))
				{
					if (!copy($filepath_original . $filename, $filepath . $filename))
					{
						$ilLog->write("File could not be copied!!!!", $ilLog->ERROR);
						$ilLog->write("object: " . print_r($this, TRUE), $ilLog->ERROR);
					}
				}
			}
		}
	}

	/**
	* Update the suggested solutions of a question based on the suggested solution array attribute
	*/
	public function updateSuggestedSolutions($original_id = "")
	{
		global $ilDB;

		$id = (strlen($original_id) && is_numeric($original_id)) ? $original_id : $this->getId();
		include_once "./Services/COPage/classes/class.ilInternalLink.php";
		$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_sol_sug WHERE question_fi = %s",
			array('integer'),
			array($id)
		);
		ilInternalLink::_deleteAllLinksOfSource("qst", $id);
		include_once("./Services/RTE/classes/class.ilRTE.php");
		foreach ($this->suggested_solutions as $index => $solution)
		{
			$next_id = $ilDB->nextId('qpl_sol_sug');

			/** @var ilDB $ilDB */
			$ilDB->insert('qpl_sol_sug', array(
										   'suggested_solution_id'	=> array( 'integer', 	$next_id ),
										   'question_fi'			=> array( 'integer', 	$id ),
										   'type'					=> array( 'text', 		$solution['type'] ),
										   'value'					=> array( 'clob', 		ilRTE::_replaceMediaObjectImageSrc( (is_array( $solution['value'] ) ) ? serialize( $solution[ 'value' ] ) : $solution['value'], 0 ) ),
										   'internal_link'			=> array( 'text', 		$solution['internal_link'] ),
										   'import_id'				=> array( 'text',		null ),
										   'subquestion_index'		=> array( 'integer', 	$index ),
										   'tstamp'				=> array( 'integer',	time() ),
									   )
			);

			if (preg_match("/il_(\d*?)_(\w+)_(\d+)/", $solution["internal_link"], $matches))
			{
				ilInternalLink::_saveLink("qst", $id, $matches[2], $matches[3], $matches[1]);
			}
		}
		if (strlen($original_id) && is_numeric($original_id)) $this->syncSuggestedSolutionFiles($id);
		$this->cleanupMediaObjectUsage();
	}
	
	/**
	* Saves a suggested solution for the question.
	* If there is more than one subquestion (i.e. close questions) may enter a subquestion index.
	*
	* @param string $solution_id An internal link pointing to the suggested solution
	* @param integer $subquestion_index The index of a subquestion (i.e. a close test gap). Usually 0
	* @param boolean $is_import A boolean indication that the internal link was imported from another ILIAS installation
	* @access public
	*/
	function saveSuggestedSolution($type, $solution_id = "", $subquestion_index = 0, $value = "")
	{
		global $ilDB;

		$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_sol_sug WHERE question_fi = %s AND subquestion_index = %s", 
			array("integer", "integer"),
			array(
				$this->getId(), 
				$subquestion_index
			)
		);
		
		$next_id = $ilDB->nextId('qpl_sol_sug');
		include_once("./Services/RTE/classes/class.ilRTE.php");
		/** @var ilDB $ilDB */
		$affectedRows = $ilDB->insert('qpl_sol_sug', array(
									   'suggested_solution_id'	=> array( 'integer', 	$next_id ),
									   'question_fi'			=> array( 'integer', 	$this->getId() ),
									   'type'					=> array( 'text', 		$type ),
									   'value'					=> array( 'clob', 		ilRTE::_replaceMediaObjectImageSrc( (is_array( $value ) ) ? serialize( $value ) : $value, 0 ) ),
									   'internal_link'			=> array( 'text', 		$solution_id ),
									   'import_id'				=> array( 'text',		null ),
									   'subquestion_index'		=> array( 'integer', 	$subquestion_index ),
									   'tstamp'					=> array( 'integer',	time() ),
								   )
		);

		if ($affectedRows == 1)
		{
			$this->suggested_solutions["subquestion_index"] = array(
				"type" => $type,
				"value" => $value,
				"internal_link" => $solution_id,
				"import_id" => ""
			);
		}
		$this->cleanupMediaObjectUsage();
	}

	function _resolveInternalLink($internal_link)
	{
		if (preg_match("/il_(\d+)_(\w+)_(\d+)/", $internal_link, $matches))
		{
			include_once "./Services/COPage/classes/class.ilInternalLink.php";
			include_once "./Modules/LearningModule/classes/class.ilLMObject.php";
			include_once "./Modules/Glossary/classes/class.ilGlossaryTerm.php";
			switch ($matches[2])
			{
				case "lm":
					$resolved_link = ilLMObject::_getIdForImportId($internal_link);
					break;
				case "pg":
					$resolved_link = ilInternalLink::_getIdForImportId("PageObject", $internal_link);
					break;
				case "st":
					$resolved_link = ilInternalLink::_getIdForImportId("StructureObject", $internal_link);
					break;
				case "git":
					$resolved_link = ilInternalLink::_getIdForImportId("GlossaryItem", $internal_link);
					break;
				case "mob":
					$resolved_link = ilInternalLink::_getIdForImportId("MediaObject", $internal_link);
					break;
			}
			if (strcmp($resolved_link, "") == 0)
			{
				$resolved_link = $internal_link;
			}
		}
		else
		{
			$resolved_link = $internal_link;
		}
		return $resolved_link;
	}
	
	function _resolveIntLinks($question_id)
	{
		global $ilDB;
		$resolvedlinks = 0;
		$result = $ilDB->queryF("SELECT * FROM qpl_sol_sug WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows())
		{
			while ($row = $ilDB->fetchAssoc($result))
			{
				$internal_link = $row["internal_link"];
				include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
				$resolved_link = assQuestion::_resolveInternalLink($internal_link);
				if (strcmp($internal_link, $resolved_link) != 0)
				{
					// internal link was resolved successfully
					$affectedRows = $ilDB->manipulateF("UPDATE qpl_sol_sug SET internal_link = %s WHERE suggested_solution_id = %s",
						array('text','integer'),
						array($resolved_link, $row["suggested_solution_id"])
					);
					$resolvedlinks++;
				}
			}
		}
		if ($resolvedlinks)
		{
			// there are resolved links -> reenter theses links to the database

			// delete all internal links from the database
			include_once "./Services/COPage/classes/class.ilInternalLink.php";
			ilInternalLink::_deleteAllLinksOfSource("qst", $question_id);

			$result = $ilDB->queryF("SELECT * FROM qpl_sol_sug WHERE question_fi = %s",
				array('integer'),
				array($question_id)
			);
			if ($result->numRows())
			{
				while ($row = $ilDB->fetchAssoc($result))
				{
					if (preg_match("/il_(\d*?)_(\w+)_(\d+)/", $row["internal_link"], $matches))
					{
						ilInternalLink::_saveLink("qst", $question_id, $matches[2], $matches[3], $matches[1]);
					}
				}
			}
		}
	}
	
	function _getInternalLinkHref($target = "")
	{
		global $ilDB;
		$linktypes = array(
			"lm" => "LearningModule",
			"pg" => "PageObject",
			"st" => "StructureObject",
			"git" => "GlossaryItem",
			"mob" => "MediaObject"
		);
		$href = "";
		if (preg_match("/il__(\w+)_(\d+)/", $target, $matches))
		{
			$type = $matches[1];
			$target_id = $matches[2];
			include_once "./Services/Utilities/classes/class.ilUtil.php";
			switch($linktypes[$matches[1]])
			{
				case "LearningModule":
					$href = "./goto.php?target=" . $type . "_" . $target_id;
					break;
				case "PageObject":
				case "StructureObject":
					$href = "./goto.php?target=" . $type . "_" . $target_id;
					break;
				case "GlossaryItem":
					$href = "./goto.php?target=" . $type . "_" . $target_id;
					break;
				case "MediaObject":
					$href = "./ilias.php?baseClass=ilLMPresentationGUI&obj_type=" . $linktypes[$type] . "&cmd=media&ref_id=".$_GET["ref_id"]."&mob_id=".$target_id;
					break;
			}
		}
		return $href;
	}
	
/**
* Returns the original id of a question
*
* @param integer $question_id The database id of the question
* @return integer The database id of the original question
* @access public
*/
	function _getOriginalId($question_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT * FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() > 0)
		{
			$row = $ilDB->fetchAssoc($result);
			if ($row["original_id"] > 0)
			{
				return $row["original_id"];
			}
			else
			{
				return $row["question_id"];
			}
		}
		else
		{
			return "";
		}
	}

	function syncWithOriginal()
	{
		global $ilDB;

		if( !$this->getOriginalId() )
		{
			return;
		}
		
		$originalObjId = self::lookupOriginalParentObjId($this->getOriginalId());
		
		if ( !$originalObjId )
		{
			return;
		}
		
		$id = $this->getId();
		$original = $this->getOriginalId();

		$this->setId($this->getOriginalId());
		$this->setOriginalId(NULL);
		$this->setObjId($originalObjId);
		$this->saveToDb();
		$this->deletePageOfQuestion($original);
		$this->createPageObject();
		$this->copyPageOfQuestion($id);

		$this->setId($id);
		$this->setOriginalId($original);
		$this->updateSuggestedSolutions($original);
		$this->syncFeedbackGeneric();
		$this->syncXHTMLMediaObjectsOfQuestion();
	}
	/**
	 * returns the parent object id for given original question id
	 * (should be a qpl id, but theoretically it can be a tst id, too)
	 * 
	 * @global ilDB $ilDB
	 * @param integer $originalQuestionId
	 * @return integer $originalQuestionParentObjectId
	 */
	public static function lookupOriginalParentObjId($originalQuestionId)
	{
		global $ilDB;
		
		$query = "SELECT obj_fi FROM qpl_questions WHERE question_id = %s";
		
		$res = $ilDB->queryF($query, array('integer'), array((int)$originalQuestionId));
		$row = $ilDB->fetchAssoc($res);
		
		return $row['obj_fi'];
	}

	function createRandomSolution($test_id, $user_id)
	{
	}

/**
* Returns true if the question already exists in the database
*
* @param integer $question_id The database id of the question
* @result boolean True, if the question exists, otherwise False
* @access public
*/
	function _questionExists($question_id)
	{
		global $ilDB;

		if ($question_id < 1)
		{
			return false;
		}
		
		$result = $ilDB->queryF("SELECT question_id FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

/**
* Returns true if the question already exists in the database and is assigned to a question pool
*
* @param integer $question_id The database id of the question
* @result boolean True, if the question exists, otherwise False
* @access public
*/
	function _questionExistsInPool($question_id)
	{
		global $ilDB;

		if ($question_id < 1)
		{
			return false;
		}

		$result = $ilDB->queryF("SELECT question_id FROM qpl_questions INNER JOIN object_data ON obj_fi = obj_id WHERE question_id = %s AND type = 'qpl'",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

/**
* Creates an instance of a question with a given question id
*
* @param integer $question_id The question id
* @return object The question instance
* @access public
*/
	function &_instanciateQuestion($question_id) 
	{
		if (strcmp($question_id, "") != 0)
		{
			$question_type = assQuestion::_getQuestionType($question_id);
			if (!strlen($question_type)) return null;
			assQuestion::_includeClass($question_type);
			$question = new $question_type();
			$question->loadFromDb($question_id);
			return $question;
		}
	}
	
/**
* Returns the maximum available points for the question
*
* @return integer The points
* @access public
*/
	function getPoints()
	{
		if (strcmp($this->points, "") == 0)
		{
			return 0;
		}
		else
		{
			return $this->points;
		}
	}

	
/**
* Sets the maximum available points for the question
*
* @param integer $a_points The points
* @access public
*/
	function setPoints($a_points)
	{
		$this->points = $a_points;
	}
	
/**
* Returns the maximum pass a users question solution
*
* @param return integer The maximum pass of the users solution
* @access public
*/
	function getSolutionMaxPass($active_id)
	{
		return $this->_getSolutionMaxPass($this->getId(), $active_id);
	}

/**
* Returns the maximum pass a users question solution
*
* @param return integer The maximum pass of the users solution
* @access public
*/
	function _getSolutionMaxPass($question_id, $active_id)
	{
/*		include_once "./Modules/Test/classes/class.ilObjTest.php";
		$pass = ilObjTest::_getPass($active_id);
		return $pass;*/

		// the following code was the old solution which added the non answered
		// questions of a pass from the answered questions of the previous pass
		// with the above solution, only the answered questions of the last pass are counted
		global $ilDB;

		$result = $ilDB->queryF("SELECT MAX(pass) maxpass FROM tst_test_result WHERE active_fi = %s AND question_fi = %s",
			array('integer','integer'),
			array($active_id, $question_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			return $row["maxpass"];
		}
		else
		{
			return 0;
		}
	}

/**
* Returns true if the question is writeable by a certain user
*
* @param integer $question_id The database id of the question
* @param integer $user_id The database id of the user
* @result boolean True, if the question exists, otherwise False
* @access public
*/
	function _isWriteable($question_id, $user_id)
	{
		global $ilDB;

		if (($question_id < 1) || ($user_id < 1))
		{
			return false;
		}
		
		$result = $ilDB->queryF("SELECT obj_fi FROM qpl_questions WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			$qpl_object_id = $row["obj_fi"];
			include_once "./Modules/TestQuestionPool/classes/class.ilObjQuestionPool.php";
			return ilObjQuestionPool::_isWriteable($qpl_object_id, $user_id);
		}
		else
		{
			return false;
		}
	}

	/**
	* Checks whether the question is used in a random test or not
	*
	* @return boolean The number how often the question is used in a random test
	* @access public
	*/
	function _isUsedInRandomTest($question_id = "")
	{
		global $ilDB;
		
		if ($question_id < 1) return 0;
		$result = $ilDB->queryF("SELECT test_random_question_id FROM tst_test_rnd_qst WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		return $result->numRows();
	}
	
	/**
	 * Returns the points, a learner has reached answering the question.
	 * The points are calculated from the given answers.
	 * 
	 * @abstract
	 * @access public
	 * @param integer $active_id
	 * @param integer $pass
	 * @param boolean $returndetails (deprecated !!)
	 * @return integer/array $points/$details (array $details is deprecated !!)
	 */
	abstract public function calculateReachedPoints($active_id, $pass = NULL, $returndetails = FALSE);

	/**
	 * Adjust the given reached points by checks for all
	 * special scoring options in the test container.
	 *
	 * @final
	 * @access public
	 * @param integer $points
	 * @param integer $active_id
	 * @param integer $pass
	 */
	final public function adjustReachedPointsByScoringOptions($points, $active_id, $pass = NULL)
	{
		include_once "./Modules/Test/classes/class.ilObjTest.php";
		$count_system = ilObjTest::_getCountSystem($active_id);
		if ($count_system == 1)
		{
			if ($points != $this->getMaximumPoints())
			{
				$points = 0;
			}
		}
		$score_cutting = ilObjTest::_getScoreCutting($active_id);
		if ($score_cutting == 0)
		{
			if ($points < 0)
			{
				$points = 0;
			}
		}
		return $points;
	}

	/**
	* Returns true if the question was worked through in the given pass
	* Worked through means that the user entered at least one value
	*
	* @param integer $user_id The database ID of the learner
	* @param integer $test_id The database Id of the test containing the question
	* @param integer $question_id The database Id of the question
	*/
	public static function _isWorkedThrough($active_id, $question_id, $pass = NULL)
	{
		global $ilDB;

		$points = 0;
		if (is_null($pass))
		{
			include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
			$pass = assQuestion::_getSolutionMaxPass($question_id, $active_id);
		}
		$result = $ilDB->queryF("SELECT solution_id FROM tst_solutions WHERE active_fi = %s AND question_fi = %s AND pass = %s",
			array('integer','integer','integer'),
			array($active_id, $question_id, $pass)
		);
		if ($result->numRows())
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* Checks if an array of question ids is answered by an user or not
	*
	* @param int user_id
	* @param array $question_ids user id array
	* @return boolean 
	*/
	public static function _areAnswered($a_user_id,$a_question_ids)
	{
		global $ilDB;

		$res = $ilDB->queryF("SELECT DISTINCT(question_fi) FROM tst_test_result JOIN tst_active ".
			"ON (active_id = active_fi) ".
			"WHERE " . $ilDB->in('question_fi', $a_question_ids, false, 'integer') .
			" AND user_fi = %s",
			array('integer'),
			array($a_user_id)
		);
		return ($res->numRows() == count($a_question_ids)) ? true : false;
	}
	
	/**
	* Checks if a given string contains HTML or not
	*
	* @param string $a_text Text which should be checked
	* @return boolean 
	* @access public
	* @deprecated use ilUtil::isHTML() instead
	*/
	function isHTML($a_text)
	{
		if (preg_match("/<[^>]*?>/", $a_text))
		{
			return TRUE;
		}
		else
		{
			return FALSE; 
		}
	}
	
	/**
	* Prepares a string for a text area output in tests
	*
	* @param string $txt_output String which should be prepared for output
	* @access public
	*/
	function prepareTextareaOutput($txt_output, $prepare_for_latex_output = FALSE)
	{
		include_once "./Services/Utilities/classes/class.ilUtil.php";
		return ilUtil::prepareTextareaOutput($txt_output, $prepare_for_latex_output);
	}

	/**
	* Reads an QTI material tag an creates a text string
	*
	* @param string $a_material QTI material tag
	* @return string text or xhtml string
	* @access public
	*/
	function QTIMaterialToString($a_material)
	{
		$result = "";
		for ($i = 0; $i < $a_material->getMaterialCount(); $i++)
		{
			$material = $a_material->getMaterial($i);
			if (strcmp($material["type"], "mattext") == 0)
			{
				$result .= $material["material"]->getContent();
			}
			if (strcmp($material["type"], "matimage") == 0)
			{
				$matimage = $material["material"];
				if (preg_match("/(il_([0-9]+)_mob_([0-9]+))/", $matimage->getLabel(), $matches))
				{
					// import an mediaobject which was inserted using tiny mce
					if (!is_array($_SESSION["import_mob_xhtml"])) $_SESSION["import_mob_xhtml"] = array();
					array_push($_SESSION["import_mob_xhtml"], array("mob" => $matimage->getLabel(), "uri" => $matimage->getUri()));
				}
			}
		}
		return $result;
	}
	
	/**
	* Creates a QTI material tag from a plain text or xhtml text
	*
	* @param object $a_xml_writer Reference to the ILIAS XML writer
	* @param string $a_material plain text or html text containing the material
	* @return string QTI material tag
	* @access public
	*/
	function addQTIMaterial(&$a_xml_writer, $a_material, $close_material_tag = TRUE, $add_mobs = TRUE)
	{
		include_once "./Services/RTE/classes/class.ilRTE.php";
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");

		$a_xml_writer->xmlStartTag("material");
		$attrs = array(
			"texttype" => "text/plain"
		);
		if ($this->isHTML($a_material))
		{
			$attrs["texttype"] = "text/xhtml";
		}
		$a_xml_writer->xmlElement("mattext", $attrs, ilRTE::_replaceMediaObjectImageSrc($a_material, 0));
		if ($add_mobs)
		{
			$mobs = ilObjMediaObject::_getMobsOfObject("qpl:html", $this->getId());
			foreach ($mobs as $mob)
			{
				$moblabel = "il_" . IL_INST_ID . "_mob_" . $mob;
				if (strpos($a_material, "mm_$mob") !== FALSE)
				{
					if (ilObjMediaObject::_exists($mob))
					{
						$mob_obj =& new ilObjMediaObject($mob);
						$imgattrs = array(
							"label" => $moblabel,
							"uri" => "objects/" . "il_" . IL_INST_ID . "_mob_" . $mob . "/" . $mob_obj->getTitle()
						);
					}
					$a_xml_writer->xmlElement("matimage", $imgattrs, NULL);
				}
			}
		}		
		if ($close_material_tag) $a_xml_writer->xmlEndTag("material");
	}
	
	function createNewImageFileName($image_filename)
	{
		$extension = "";
		if (preg_match("/.*\.(png|jpg|gif|jpeg)$/i", $image_filename, $matches))
		{
			$extension = "." . $matches[1];
		}
		$image_filename = md5($image_filename) . $extension;
		return $image_filename;
	}

	/**
	* Sets the points, a learner has reached answering the question
	* Additionally objective results are updated
	*
	* @param integer $user_id The database ID of the learner
	* @param integer $test_id The database Id of the test containing the question
	* @param integer $points The points the user has reached answering the question
	* @return boolean true on success, otherwise false
	* @access public
	*/
	function _setReachedPoints($active_id, $question_id, $points, $maxpoints, $pass, $manualscoring, $obligationsEnabled)
	{
		global $ilDB;
		
		if ($points <= $maxpoints)
		{
			if (is_null($pass))
			{
				$pass = assQuestion::_getSolutionMaxPass($question_id, $active_id);
			}

			// retrieve the already given points
			$old_points = 0;
			$result = $ilDB->queryF("SELECT points FROM tst_test_result WHERE active_fi = %s AND question_fi = %s AND pass = %s",
				array('integer','integer','integer'),
				array($active_id, $question_id, $pass)
			);
			$manual = ($manualscoring) ? 1 : 0;
			if ($result->numRows())
			{
				$row = $ilDB->fetchAssoc($result);
				$old_points = $row["points"];
				$affectedRows = $ilDB->manipulateF("UPDATE tst_test_result SET points = %s, manual = %s, tstamp = %s WHERE active_fi = %s AND question_fi = %s AND pass = %s",
					array('float', 'integer', 'integer', 'integer', 'integer', 'integer'),
					array($points, $manual, time(), $active_id, $question_id, $pass)
				);
			}
			else
			{
				$next_id = $ilDB->nextId('tst_test_result');
				$affectedRows = $ilDB->manipulateF("INSERT INTO tst_test_result (test_result_id, active_fi, question_fi, points, pass, manual, tstamp) VALUES (%s, %s, %s, %s, %s, %s, %s)",
					array('integer', 'integer','integer', 'float', 'integer', 'integer','integer'),
					array($next_id, $active_id, $question_id, $points, $pass, $manual, time())
				);
			}
			assQuestion::_updateTestPassResults($active_id, $pass, $obligationsEnabled);
			// finally update objective result
			include_once "./Modules/Test/classes/class.ilObjTest.php";
			include_once './Modules/Course/classes/class.ilCourseObjectiveResult.php';
			ilCourseObjectiveResult::_updateObjectiveResult(ilObjTest::_getUserIdFromActiveId($active_id),$question_id,$points);

			include_once ("./Modules/Test/classes/class.ilObjAssessmentFolder.php");
			if (ilObjAssessmentFolder::_enabledAssessmentLogging())
			{
				global $lng, $ilUser;
				include_once "./Modules/Test/classes/class.ilObjTestAccess.php";
				$username = ilObjTestAccess::_getParticipantData($active_id);
				assQuestion::_logAction(sprintf($lng->txtlng("assessment", "log_answer_changed_points", ilObjAssessmentFolder::_getLogLanguage()), $username, $old_points, $points, $ilUser->getFullname() . " (" . $ilUser->getLogin() . ")"), $active_id, $question_id);
			}
			
			return TRUE;
		}
			else
		{
			return FALSE;
		}
	}
	
/**
* Gets the question string of the question object
*
* @return string The question string of the question object
* @access public
* @see $question
*/
	function getQuestion() 
	{
		return $this->question;
	}

/**
* Sets the question string of the question object
*
* @param string $question A string containing the question text
* @access public
* @see $question
*/
	function setQuestion($question = "") 
	{
		$this->question = $question;
	}

	/**
	* Returns the question type of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionType()
	{
		// must be overwritten in every parent class
		return "";
	}
	
	/**
	* Returns the question type of the question
	*
	* Returns the question type of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionTypeID()
	{
		global $ilDB;
		
		$result = $ilDB->queryF("SELECT question_type_id FROM qpl_qst_type WHERE type_tag = %s",
			array('text'),
			array($this->getQuestionType())
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			return $row["question_type_id"];
		}
		return 0;
	}
	
	/**
	* Saves generic feedback to the database. Generic feedback is either
	* feedback for either the complete solution of the question or at least one
	* incorrect answer.
	*
	* @param integer $correctness 0 for at least one incorrect answer, 1 for the correct solution
	* @param string $feedback Feedback text
	* @access public
	*/
	function saveFeedbackGeneric($correctness, $feedback)
	{
		global $ilDB;
		
		switch ($correctness)
		{
			case 0:
				$correctness = 0;
				break;
			case 1:
			default:
				$correctness = 1;
				break;
		}
		$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_fb_generic WHERE question_fi = %s AND correctness = %s",
			array('integer', 'text'),
			array($this->getId(), $correctness)
		);
		if (strlen($feedback))
		{
			include_once("./Services/RTE/classes/class.ilRTE.php");
			$next_id = $ilDB->nextId('qpl_fb_generic');

			/** @var ilDB $ilDB */
			$ilDB->insert('qpl_fb_generic', array(
											  'feedback_id'	=> array( 'integer', 	$next_id ),
											  'question_fi'	=> array( 'integer', 	$this->getId() ),
											  'correctness'	=> array( 'text', 		$correctness ),
											  'feedback'	=> array( 'clob',		ilRTE::_replaceMediaObjectImageSrc( $feedback, 0 ) ),
											  'tstamp'		=> array( 'integer',	time() ),
										  )
			);

		}
	}
	
	/**
	* Returns the generic feedback for a given question state. The
	* state is either the complete solution of the question or at least one
	* incorrect answer
	*
	* @param integer $correctness 0 for at least one incorrect answer, 1 for the correct solution
	* @return string Feedback text
	* @access public
	*/
	function getFeedbackGeneric($correctness)
	{
		global $ilDB;
		
		$feedback = "";
		$result = $ilDB->queryF("SELECT * FROM qpl_fb_generic WHERE question_fi = %s AND correctness = %s",
			array('integer', 'text'),
			array($this->getId(), $correctness)
		);
		if ($result->numRows())
		{
			$row = $ilDB->fetchAssoc($result);
			include_once("./Services/RTE/classes/class.ilRTE.php");
			$feedback = ilRTE::_replaceMediaObjectImageSrc($row["feedback"], 1);
		}
		return $feedback;
	}

	/**
	* Duplicates the generic feedback of a question
	*
	* @param integer $original_id The database ID of the original question
	* @access public
	*/
	function duplicateGenericFeedback($original_id)
	{
		global $ilDB;
		
		$feedback = "";
		$result = $ilDB->queryF("SELECT * FROM qpl_fb_generic WHERE question_fi = %s",
			array('integer'),
			array($original_id)
		);
		if ($result->numRows())
		{
			while ($row = $ilDB->fetchAssoc($result))
			{
				$next_id = $ilDB->nextId('qpl_fb_generic');

				/** @var ilDB $ilDB */
				$ilDB->insert('qpl_fb_generic', array(
												  'feedback_id'	=> array( 'integer', 	$next_id ),
												  'question_fi'	=> array( 'integer', 	$this->getId() ),
												  'correctness'	=> array( 'text', 		$row["correctness"] ),
												  'feedback'	=> array( 'clob',		$row["feedback"] ),
												  'tstamp'		=> array( 'integer',	time() ),
											  )
				);
			}
		}
	}
	
	function syncFeedbackGeneric()
	{
		global $ilDB;

		$feedback = "";

		// delete generic feedback of the original
		$affectedRows = $ilDB->manipulateF("DELETE FROM qpl_fb_generic WHERE question_fi = %s",
			array('integer'),
			array($this->original_id)
		);
			
		// get generic feedback of the actual question
		$result = $ilDB->queryF("SELECT * FROM qpl_fb_generic WHERE question_fi = %s",
			array('integer'),
			array($this->getId())
		);

		// save generic feedback to the original
		if ($result->numRows())
		{
			while ($row = $ilDB->fetchAssoc($result))
			{
				$next_id = $ilDB->nextId('qpl_fb_generic');
				/** @var ilDB $ilDB */
				$ilDB->insert('qpl_fb_generic', array(
												  'feedback_id'	=> array( 'integer', 	$next_id ),
												  'question_fi'	=> array( 'integer', 	$this->original_id ),
												  'correctness'	=> array( 'text', 		$row["correctness"] ),
												  'feedback'	=> array( 'clob',		$row["feedback"] ),
												  'tstamp'		=> array( 'integer',	time() ),
											  )
				);
			}
		}
	}
	
	/**
	* Collects all text in the question which could contain media objects
	* which were created with the Rich Text Editor
	*/
	function getRTETextWithMediaObjects()
	{
		// must be called in parent classes. add additional RTE text in the parent
		// classes and call this method to add the standard RTE text
		$collected = $this->getQuestion();
		$collected .= $this->getFeedbackGeneric(0);
		$collected .= $this->getFeedbackGeneric(1);
		for( $i = 0; $i <= $this->getTotalAnswers(); $i++ )
		{
			$collected .= $this->getFeedbackSingleAnswer($i);
		}
		foreach ($this->suggested_solutions as $solution_array)
		{
			$collected .= $solution_array["value"];
		}
		return $collected;
	}
	
	function getFeedbackSingleAnswer($answer_index)
	{
		return '';
	}

	/**
	* synchronises appearances of media objects in the question with media
	* object usage table
	*/
	function cleanupMediaObjectUsage()
	{
		$combinedtext = $this->getRTETextWithMediaObjects();
		include_once("./Services/RTE/classes/class.ilRTE.php");
		ilRTE::_cleanupMediaObjectUsage($combinedtext, "qpl:html", $this->getId());
	}
	
	/**
	* Gets all instances of the question
	*
	* @result array All instances of question and its copies
	*/
	function &getInstances()
	{
		global $ilDB;

		$result = $ilDB->queryF("SELECT question_id FROM qpl_questions WHERE original_id = %s",
			array("integer"),
			array($this->getId())
		);
		$instances = array();
		$ids = array();
		while ($row = $ilDB->fetchAssoc($result))
		{
			array_push($ids, $row["question_id"]);
		}
		foreach ($ids as $question_id)
		{
			// check non random tests
			$result = $ilDB->queryF("SELECT tst_tests.obj_fi FROM tst_tests, tst_test_question WHERE tst_test_question.question_fi = %s AND tst_test_question.test_fi = tst_tests.test_id",
				array("integer"),
				array($question_id)
			);
			while ($row = $ilDB->fetchAssoc($result))
			{
				$instances[$row['obj_fi']] = ilObject::_lookupTitle($row['obj_fi']);
			}
			// check random tests
			$result = $ilDB->queryF("SELECT tst_tests.obj_fi FROM tst_tests, tst_test_rnd_qst, tst_active WHERE tst_test_rnd_qst.active_fi = tst_active.active_id AND tst_test_rnd_qst.question_fi = %s AND tst_tests.test_id = tst_active.test_fi",
				array("integer"),
				array($question_id)
			);
			while ($row = $ilDB->fetchAssoc($result))
			{
				$instances[$row['obj_fi']] = ilObject::_lookupTitle($row['obj_fi']);
			}
		}
		include_once "./Modules/Test/classes/class.ilObjTest.php";
		foreach ($instances as $key => $value)
		{
			$instances[$key] = array("obj_id" => $key, "title" => $value, "author" => ilObjTest::_lookupAuthor($key), "refs" => ilObject::_getAllReferences($key));
		}
		return $instances;
	}

	function _needsManualScoring($question_id)
	{
		include_once "./Modules/Test/classes/class.ilObjAssessmentFolder.php";
		$scoring = ilObjAssessmentFolder::_getManualScoringTypes();
		$questiontype = assQuestion::_getQuestionType($question_id);
		if (in_array($questiontype, $scoring))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* Returns the user id and the test id for a given active id
	*
	* @param integer $active_id Active id for a test/user
	* @return array Result array containing the user_id and test_id
	* @access public
	*/
	function getActiveUserData($active_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT * FROM tst_active WHERE active_id = %s",
			array('integer'),
			array($active_id)
		);
		if ($result->numRows())
		{
			$row = $ilDB->fetchAssoc($result);
			return array("user_id" => $row["user_fi"], "test_id" => $row["test_fi"]);
		}
		else
		{
			return array();
		}
	}

	/**
	* Include the php class file for a given question type
	*
	* @param string $question_type The type tag of the question type
	* @return integer 0 if the class should be included, 1 if the GUI class should be included
	* @access public
	*/
	static function _includeClass($question_type, $gui = 0)
	{
		$type = $question_type;
		if ($gui) $type .= "GUI";
		if (file_exists("./Modules/TestQuestionPool/classes/class.".$type.".php"))
		{
			include_once "./Modules/TestQuestionPool/classes/class.".$type.".php";
		}
		else
		{
			global $ilPluginAdmin;
			$pl_names = $ilPluginAdmin->getActivePluginsForSlot(IL_COMP_MODULE, "TestQuestionPool", "qst");
			foreach ($pl_names as $pl_name)
			{
				$pl = ilPlugin::getPluginObject(IL_COMP_MODULE, "TestQuestionPool", "qst", $pl_name);
				if (strcmp($pl->getQuestionType(), $question_type) == 0)
				{
					$pl->includeClass("class.".$type.".php");
				}
			}
		}
	}

	/**
	* Return the translation for a given question type tag
	*
	* @param string $type_tag The type tag of the question type
	* @access public
	*/
	static function _getQuestionTypeName($type_tag)
	{
		if (file_exists("./Modules/TestQuestionPool/classes/class.".$type_tag.".php"))
		{
			global $lng;
			return $lng->txt($type_tag);
		}
		else
		{
			global $ilPluginAdmin;
			$pl_names = $ilPluginAdmin->getActivePluginsForSlot(IL_COMP_MODULE, "TestQuestionPool", "qst");
			foreach ($pl_names as $pl_name)
			{
				$pl = ilPlugin::getPluginObject(IL_COMP_MODULE, "TestQuestionPool", "qst", $pl_name);
				if (strcmp($pl->getQuestionType(), $type_tag) == 0)
				{
					return $pl->getQuestionTypeTranslation();
				}
			}
		}
		return "";
	}

/**
* Creates an instance of a question gui with a given question id
*
* @param integer $question_id The question id
* @return object The question gui instance
* @access public
*/
	function &_instanciateQuestionGUI($question_id) 
	{
		if (strcmp($question_id, "") != 0)
		{
			$question_type = assQuestion::_getQuestionType($question_id);
			$question_type_gui = $question_type . "GUI";
			assQuestion::_includeClass($question_type, 1);
			$question_gui = new $question_type_gui();
			$question_gui->object->loadFromDb($question_id);
			return $question_gui;
		}
	}
	
	/**
	* Creates an Excel worksheet for the detailed cumulated results of this question
	*
	* @param object $worksheet Reference to the parent excel worksheet
	* @param object $startrow Startrow of the output in the excel worksheet
	* @param object $active_id Active id of the participant
	* @param object $pass Test pass
	* @param object $format_title Excel title format
	* @param object $format_bold Excel bold format
	* @param array $eval_data Cumulated evaluation data
	* @access public
	*/
	public function setExportDetailsXLS(&$worksheet, $startrow, $active_id, $pass, &$format_title, &$format_bold)
	{
		return $startrow;
	}

	/**
	* Object getter
	*/
	public function __get($value)
	{
		switch ($value)
		{
			case "id":
				return $this->getId();
				break;
			case "title":
				return $this->getTitle();
				break;
			case "comment":
				return $this->getComment();
				break;
			case "owner":
				return $this->getOwner();
				break;
			case "author":
				return $this->getAuthor();
				break;
			case "question":
				return $this->getQuestion();
				break;
			case "points":
				return $this->getPoints();
				break;
			case "est_working_time":
				return $this->getEstimatedWorkingTime();
				break;
			case "shuffle":
				return $this->getShuffle();
				break;
			case "test_id":
				return $this->getTestId();
				break;
			case "obj_id":
				return $this->getObjId();
				break;
			case "ilias":
				return $this->ilias;
				break;
			case "tpl":
				return $this->tpl;
				break;
			case "page":
				return $this->page;
				break;
			case "outputType":
				return $this->getOutputType();
				break;
			case "suggested_solutions":
				return $this->getSuggestedSolutions();
				break;
			case "original_id":
				return $this->getOriginalId();
				break;
			default:
				if (array_key_exists($value, $this->arrData))
				{
					return $this->arrData[$value];
				}
				else
				{
					return null;
				}
				break;
		}
	}

	/**
	* Object setter
	*/
	public function __set($key, $value)
	{
		switch ($key)
		{
			case "id":
				$this->setId($value);
				break;
			case "title":
				$this->setTitle($value);
				break;
			case "comment":
				$this->setComment($value);
				break;
			case "owner":
				$this->setOwner($value);
				break;
			case "author":
				$this->setAuthor($value);
				break;
			case "question":
				$this->setQuestion($value);
				break;
			case "points":
				$this->setPoints($value);
				break;
			case "est_working_time":
				if (is_array($value))
				{
					$this->setEstimatedWorkingTime($value["h"], $value["m"], $value["s"]);
				}
				break;
			case "shuffle":
				$this->setShuffle($value);
				break;
			case "test_id":
				$this->setTestId($value);
				break;
			case "obj_id":
				$this->setObjId($value);
				break;
			case "outputType":
				$this->setOutputType($value);
				break;
			case "original_id":
				$this->setOriginalId($value);
				break;
			case "page":
				$this->page =& $value;
				break;
			default:
				$this->arrData[$key] = $value;
				break;
		}
	}
	
	public function getNrOfTries()
	{
		return $this->nr_of_tries;
	}
	
	public function setNrOfTries($a_nr_of_tries)
	{
		$this->nr_of_tries = $a_nr_of_tries;
	}

	public function setExportImagePath($a_path)
	{
		$this->export_image_path = (string)$a_path;
	}

	function _questionExistsInTest($question_id, $test_id)
	{
		global $ilDB;

		if ($question_id < 1)
		{
			return false;
		}

		$result = $ilDB->queryF("SELECT question_fi FROM tst_test_question WHERE question_fi = %s AND test_fi = %s",
			array('integer', 'integer'),
			array($question_id, $test_id)
		);
		if ($result->numRows() == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Format self assessment question
	 *
	 * @param
	 * @return
	 */
	function formatSAQuestion($a_q)
	{
		include_once("./Services/RTE/classes/class.ilRTE.php");
		$a_q = nl2br((string) ilRTE::_replaceMediaObjectImageSrc($this->getQuestion(), 0));
		$a_q = str_replace("</li><br />", "</li>", $a_q);
		$a_q = str_replace("</li><br>", "</li>", $a_q);
		
		$a_q = ilUtil::insertLatexImages($a_q);
		
		return $a_q;
	}
	
	protected function duplicateQuestionHints($originalQuestionId, $duplicateQuestionId)
	{
		require_once 'Modules/TestQuestionPool/classes/class.ilAssQuestionHintList.php';
		ilAssQuestionHintList::duplicateListForQuestion($originalQuestionId, $duplicateQuestionId);
	}

	protected function deleteFeedbackSpecific($question_id)
	{
		return;
	}

	private function deleteFeedbackGeneric($question_id)
	{
		global $ilDB;
		$ilDB->manipulateF(
			'DELETE FROM qpl_fb_generic WHERE question_fi = %s',
			array('integer'),
			array($question_id)
		);
	}
	
	/**
	 * returns boolean wether the question
	 * is answered during test pass or not
	 * 
	 * method can be overwritten in derived classes,
	 * but be aware of also overwrite the method
	 * assQuestion::isObligationPossible()
	 * 
	 * @param integer $active_id
	 * @param integer $pass
	 * @return boolean $answered
	 */
	public function isAnswered($active_id, $pass = null)
	{
		return true;		
	}
	
	/**
	 * returns boolean wether it is possible to set
	 * this question type as obligatory or not
	 * considering the current question configuration
	 * 
	 * method can be overwritten in derived classes,
	 * but be aware of also overwrite the method
	 * assQuestion::isAnswered()
	 * 
	 * @param integer $questionId
	 * @return boolean $obligationPossible
	 */
	public static function isObligationPossible($questionId)
	{
		return false;
	}
	
	/**
	 * sets the flag wether obligations are to be considered or not
	 *
	 * @param bool $obligationsToBeConsidered
	 */
	public function setObligationsToBeConsidered($obligationsToBeConsidered = true)
	{
		$this->obligationsToBeConsidered = (bool)$obligationsToBeConsidered;
	}
	
	/**
	 * gets the flag wether obligations are to be considered or not
	 *
	 * @return bool $obligationsToBeConsidered
	 */
	public function areObligationsToBeConsidered()
	{
		return (bool)$this->obligationsToBeConsidered;
	}
	
	public function isAutosaveable()
	{
		return TRUE;
	}
	
	/**
	 * checks wether there are existing solution records
	 * for the given test active / pass and given question id
	 *
	 * @access protected
	 * @static
	 * @global ilDB $ilDB
	 * @param integer $activeId
	 * @param integer $pass
	 * @param integer $questionId
	 * @return boolean $solutionRecordsExist 
	 */
	protected static function doesSolutionRecordsExist($activeId, $pass, $questionId)
	{
		// check if a solution was stored in tst_solution

		global $ilDB;
		
		$query = "
			SELECT		count(active_fi) cnt
			
			FROM		tst_solutions
			
			WHERE		active_fi = %s
			AND			question_fi = %s
			AND			pass = %s
		";
		
		$res = $ilDB->queryF(
			$query, array('integer','integer','integer'),
			array($activeId, $questionId, $pass)
		);
		
		$row = $ilDB->fetchAssoc($res);
		
		$solutionRecordsExist = (
			0 < (int)$row['cnt'] ? true : false
		);

		return $solutionRecordsExist;
	}
}