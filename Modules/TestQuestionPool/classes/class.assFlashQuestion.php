<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
include_once "./Modules/Test/classes/inc.AssessmentConstants.php";

/**
 * Class for Mathematik Online based questions
 *
 * @extends assQuestion
 * 
 * @author		Helmut Schottmüller <helmut.schottmueller@mac.com> 
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id: class.assFlashQuestion.php 47445 2014-01-22 17:03:37Z bheyser $
 * 
 * @ingroup		ModulesTestQuestionPool
 */
class assFlashQuestion extends assQuestion
{
	private $width;
	private $height;
	private $parameters;
	private $applet;

	/**
	* assFlashQuestion constructor
	*
	* The constructor takes possible arguments an creates an instance of the assFlashQuestion object.
	*
	* @param string $title A title string to describe the question
	* @param string $comment A comment string to describe the question
	* @param string $author A string containing the name of the questions author
	* @param integer $owner A numerical ID to identify the owner/creator
	* @param string $question The question string of the single choice question
	* @access public
	* @see assQuestion:assQuestion()
	*/
	function __construct(
		$title = "",
		$comment = "",
		$author = "",
		$owner = -1,
		$question = ""
	)
	{
		parent::__construct($title, $comment, $author, $owner, $question);
		$this->parameters = array();
		$this->width = 540;
		$this->height = 400;
		$this->applet = "";
	}
	
	/**
	* Returns true, if a single choice question is complete for use
	*
	* @return boolean True, if the single choice question is complete for use, otherwise false
	* @access public
	*/
	function isComplete()
	{
		if (strlen($this->title) and ($this->author) and ($this->question) and ($this->getMaximumPoints() > 0) and (strlen($this->getApplet())))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* Saves a assFlashQuestion object to a database
	*
	* @access public
	*/
	function saveToDb($original_id = "")
	{
		global $ilDB, $ilLog;

		$this->saveQuestionDataToDb($original_id);

		// save additional data
		$affectedRows = $ilDB->manipulateF("DELETE FROM " . $this->getAdditionalTableName() . " WHERE question_fi = %s", 
			array("integer"),
			array($this->getId())
		);
		$affectedRows = $ilDB->manipulateF("INSERT INTO " . $this->getAdditionalTableName() . " (question_fi, width, height, applet, params) VALUES (%s, %s, %s, %s, %s)", 
			array("integer", "integer", "integer", "text", "text"),
			array(
				$this->getId(),
				(strlen($this->getWidth())) ? $this->getWidth() : 550,
				(strlen($this->getHeight())) ? $this->getHeight() : 400,
				$this->getApplet(),
				serialize($this->getParameters())
			)
		);
		if ($_SESSION["flash_upload_filename"])
		{
			$path = $this->getFlashPath();
			ilUtil::makeDirParents($path);
			@rename($_SESSION["flash_upload_filename"], $path . $this->getApplet());
			unset($_SESSION["flash_upload_filename"]);
		}

		parent::saveToDb();
	}

	/**
	* Loads a assFlashQuestion object from a database
	*
	* @param object $db A pear DB object
	* @param integer $question_id A unique key which defines the multiple choice test in the database
	* @access public
	*/
	function loadFromDb($question_id)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT qpl_questions.*, " . $this->getAdditionalTableName() . ".* FROM qpl_questions LEFT JOIN " . $this->getAdditionalTableName() . " ON " . $this->getAdditionalTableName() . ".question_fi = qpl_questions.question_id WHERE qpl_questions.question_id = %s",
			array("integer"),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			$data = $ilDB->fetchAssoc($result);
			$this->setId($question_id);
			$this->setNrOfTries($data['nr_of_tries']);
			$this->setTitle($data["title"]);
			$this->setComment($data["description"]);
			$this->setSuggestedSolution($data["solution_hint"]);
			$this->setOriginalId($data["original_id"]);
			$this->setObjId($data["obj_fi"]);
			$this->setAuthor($data["author"]);
			$this->setOwner($data["owner"]);
			$this->setPoints($data["points"]);

			include_once("./Services/RTE/classes/class.ilRTE.php");
			$this->setQuestion(ilRTE::_replaceMediaObjectImageSrc($data["question_text"], 1));
			$this->setEstimatedWorkingTime(substr($data["working_time"], 0, 2), substr($data["working_time"], 3, 2), substr($data["working_time"], 6, 2));
			// load additional data
			$result = $ilDB->queryF("SELECT * FROM " . $this->getAdditionalTableName() . " WHERE question_fi = %s",
				array("integer"),
				array($question_id)
			);
			if ($result->numRows() == 1)
			{
				$data = $ilDB->fetchAssoc($result);
				$this->setWidth($data["width"]);
				$this->setHeight($data["height"]);
				$this->setApplet($data["applet"]);
				$this->parameters = unserialize($data["params"]);
				if (!is_array($this->parameters)) $this->clearParameters();
				unset($_SESSION["flash_upload_filename"]);
			}
		}
		parent::loadFromDb($question_id);
	}

	/**
	* Duplicates an assFlashQuestion
	*
	* Duplicates an assFlashQuestion
	*
	* @access public
	*/
	function duplicate($for_test = true, $title = "", $author = "", $owner = "", $testObjId = null)
	{
		if ($this->id <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$this_id = $this->getId();
		
		if( (int)$testObjId > 0 )
		{
			$thisObjId = $this->getObjId();
		}
		
		$clone = $this;
		include_once ("./Modules/TestQuestionPool/classes/class.assQuestion.php");
		$original_id = assQuestion::_getOriginalId($this->id);
		$clone->id = -1;
		
		if( (int)$testObjId > 0 )
		{
			$clone->setObjId($testObjId);
		}
		
		if ($title)
		{
			$clone->setTitle($title);
		}

		if ($author)
		{
			$clone->setAuthor($author);
		}
		if ($owner)
		{
			$clone->setOwner($owner);
		}

		if ($for_test)
		{
			$clone->saveToDb($original_id);
		}
		else
		{
			$clone->saveToDb();
		}

		// copy question page content
		$clone->copyPageOfQuestion($this_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($this_id);
		// duplicate the generic feedback
		$clone->duplicateGenericFeedback($this_id);
		// duplicate the applet
		$clone->duplicateApplet($this_id, $thisObjId);

		$clone->onDuplicate($thisObjId, $this_id, $clone->getObjId(), $clone->getId());
		
		return $clone->id;
	}

	/**
	* Copies an assFlashQuestion object
	*
	* Copies an assFlashQuestion object
	*
	* @access public
	*/
	function copyObject($target_questionpool, $title = "")
	{
		if ($this->id <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$clone = $this;
		include_once ("./Modules/TestQuestionPool/classes/class.assQuestion.php");
		$original_id = assQuestion::_getOriginalId($this->id);
		$clone->id = -1;
		$source_questionpool = $this->getObjId();
		$clone->setObjId($target_questionpool);
		if ($title)
		{
			$clone->setTitle($title);
		}
		$clone->saveToDb();

		// copy question page content
		$clone->copyPageOfQuestion($original_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($original_id);
		// duplicate the generic feedback
		$clone->duplicateGenericFeedback($original_id);

		// duplicate the applet
		$clone->copyApplet($original_id, $source_questionpool);
		$clone->onCopy($this->getObjId(), $this->getId());

		return $clone->id;
	}

	/**
	* Duplicate the flash applet
	*
	* @access public
	* @see $points
	*/
	protected function duplicateApplet($question_id, $objectId = null)
	{
		$flashpath = $this->getFlashPath();
		$flashpath_original = preg_replace("/([^\d])$this->id([^\d])/", "\${1}$question_id\${2}", $flashpath);
		
		if( (int)$objectId > 0 )
		{
			$flashpath_original = str_replace("/$this->obj_id/", "/$objectId/", $flashpath_original);
		}
		
		if (!file_exists($flashpath))
		{
			ilUtil::makeDirParents($flashpath);
		}
		$filename = $this->getApplet();
		if (!copy($flashpath_original . $filename, $flashpath . $filename)) {
			print "flash applet could not be duplicated!!!! ";
		}
	}

	/**
	* Copy the flash applet
	*
	* @access public
	* @see $points
	*/
	protected function copyApplet($question_id, $source_questionpool)
	{
		$flashpath = $this->getFlashPath();
		$flashpath_original = preg_replace("/([^\d])$this->id([^\d])/", "\${1}$question_id\${2}", $flashpath);
		$flashpath_original = str_replace("/$this->obj_id/", "/$source_questionpool/", $flashpath_original);
		if (!file_exists($flashpath))
		{
			ilUtil::makeDirParents($flashpath);
		}
		$filename = $this->getApplet();
		if (!copy($flashpath_original . $filename, $flashpath . $filename)) 
		{
			print "flash applet could not be copied!!!! ";
		}
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
	 * Returns the points, a learner has reached answering the question.
	 * The points are calculated from the given answers.
	 * 
	 * @access public
	 * @param integer $active_id
	 * @param integer $pass
	 * @param boolean $returndetails (deprecated !!)
	 * @return integer/array $points/$details (array $details is deprecated !!)
	 */
	public function calculateReachedPoints($active_id, $pass = NULL, $returndetails = FALSE)
	{
		if( $returndetails )
		{
			throw new ilTestException('return details not implemented for '.__METHOD__);
		}
		
		global $ilDB;
		
		$found_values = array();
		if (is_null($pass))
		{
			$pass = $this->getSolutionMaxPass($active_id);
		}
		$result = $ilDB->queryF("SELECT * FROM tst_solutions WHERE active_fi = %s AND question_fi = %s AND pass = %s",
			array("integer", "integer", "integer"),
			array($active_id, $this->getId(), $pass)
		);

		$points = 0;
		while ($data = $ilDB->fetchAssoc($result))
		{
			$points += $data["points"];
		}

		return $points;
	}
	
	function sendToHost($url, $data, $optional_headers = null)
	{
		$params = array('http' => array(
			'method' => 'POST',
			'content' => $data
		));
		if ($optional_headers !== null) 
		{
			$params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) 
		{
			throw new Exception("Problem with $url, $php_errormsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) 
		{
			throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		return $response;
	}
	
	/**
	* Uploads a flash file
	*
	* @param string $flashfile Name of the original flash file
	* @param string $tmpfile Name of the temporary uploaded flash file
	* @return string Name of the file
	* @access public
	*/
	function moveUploadedFile($tmpfile, $flashfile)
	{
		$result = "";
		if (!empty($tmpfile))
		{
			$flashfile = str_replace(" ", "_", $flashfile);
			$flashpath = $this->getFlashPath();
			if (!file_exists($flashpath))
			{
				ilUtil::makeDirParents($flashpath);
			}
			if (ilUtil::moveUploadedFile($tmpfile, $flashfile, $flashpath.$flashfile))
			{
				$result = $flashfile;
			}
		}
		return $result;
	}

	function deleteApplet()
	{
		@unlink($this->getFlashPath() . $this->getApplet());
		$this->applet = "";
	}
	
	/**
	 * Saves the learners input of the question to the database.
	 * 
	 * @access public
	 * @param integer $active_id Active id of the user
	 * @param integer $pass Test pass
	 * @return boolean $status
	 */
	public function saveWorkingData($active_id, $pass = NULL)
	{
		// nothing to save!
		
		return true;
	}

	/**
	 * Reworks the allready saved working data if neccessary
	 *
	 * @access protected
	 * @param integer $active_id
	 * @param integer $pass
	 * @param boolean $obligationsAnswered
	 */
	protected function reworkWorkingData($active_id, $pass, $obligationsAnswered)
	{
		// nothing to rework!
	}

	/**
	* Returns the question type of the question
	*
	* Returns the question type of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionType()
	{
		return "assFlashQuestion";
	}
	
	/**
	* Returns the name of the additional question data table in the database
	*
	* Returns the name of the additional question data table in the database
	*
	* @return string The additional table name
	* @access public
	*/
	function getAdditionalTableName()
	{
		return "qpl_qst_flash";
	}
	
	/**
	* Returns the name of the answer table in the database
	*
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
	}

	/**
	* Collects all text in the question which could contain media objects
	* which were created with the Rich Text Editor
	*/
	function getRTETextWithMediaObjects()
	{
		$text = parent::getRTETextWithMediaObjects();
		return $text;
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
		include_once ("./Services/Excel/classes/class.ilExcelUtils.php");
		$worksheet->writeString($startrow, 0, ilExcelUtils::_convert_text($this->lng->txt($this->getQuestionType())), $format_title);
		$worksheet->writeString($startrow, 1, ilExcelUtils::_convert_text($this->getTitle()), $format_title);
		return $startrow + 1;
	}
	
	/**
	* Creates a question from a QTI file
	*
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
		include_once "./Modules/TestQuestionPool/classes/import/qti12/class.assFlashQuestionImport.php";
		$import = new assFlashQuestionImport($this);
		$import->fromXML($item, $questionpool_id, $tst_id, $tst_object, $question_counter, $import_mapping);
	}
	
	/**
	* Returns a QTI xml representation of the question and sets the internal
	* domxml variable with the DOM XML representation of the QTI xml representation
	*
	* @return string The QTI xml representation of the question
	* @access public
	*/
	function toXML($a_include_header = true, $a_include_binary = true, $a_shuffle = false, $test_output = false, $force_image_references = false)
	{
		include_once "./Modules/TestQuestionPool/classes/export/qti12/class.assFlashQuestionExport.php";
		$export = new assFlashQuestionExport($this);
		return $export->toXML($a_include_header, $a_include_binary, $a_shuffle, $test_output, $force_image_references);
	}

	/**
	* Returns the best solution for a given pass of a participant
	*
	* @return array An associated array containing the best solution
	* @access public
	*/
	public function getBestSolution($active_id, $pass)
	{
		$user_solution = array();
		return $user_solution;
	}
	
	public function setHeight($a_height)
	{
		if (!$a_height) $a_height = 400;
		$this->height = $a_height;
	}
	
	public function getHeight()
	{
		return $this->height;
	}

	public function setWidth($a_width)
	{
		if (!$a_width) $a_width = 550;
		$this->width = $a_width;
	}
	
	public function getWidth()
	{
		return $this->width;
	}
	
	public function setApplet($a_applet)
	{
		$this->applet = $a_applet;
	}
	
	public function getApplet()
	{
		return $this->applet;
	}
	
	public function addParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	
	public function setParameters($params)
	{
		if (is_array($params))
		{
			$this->parameters = $params;
		}
		else
		{
			$this->parameters = array();
		}
	}
	
	public function removeParameter($name)
	{
		unset($this->parameters[$name]);
	}
	
	public function clearParameters()
	{
		$this->parameters = array();
	}
	
	public function getParameters()
	{
		return $this->parameters;
	}
	
	public function isAutosaveable()
	{
		return FALSE;
	}
}

?>
