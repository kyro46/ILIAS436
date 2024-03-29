<?php
 /*
   +----------------------------------------------------------------------------+
   | ILIAS open source                                                          |
   +----------------------------------------------------------------------------+
   | Copyright (c) 1998-2001 ILIAS open source, University of Cologne           |
   |                                                                            |
   | This program is free software; you can redistribute it and/or              |
   | modify it under the terms of the GNU General Public License                |
   | as published by the Free Software Foundation; either version 2             |
   | of the License, or (at your option) any later version.                     |
   |                                                                            |
   | This program is distributed in the hope that it will be useful,            |
   | but WITHOUT ANY WARRANTY; without even the implied warranty of             |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              |
   | GNU General Public License for more details.                               |
   |                                                                            |
   | You should have received a copy of the GNU General Public License          |
   | along with this program; if not, write to the Free Software                |
   | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA. |
   +----------------------------------------------------------------------------+
*/

include_once "./Modules/Survey/classes/inc.SurveyConstants.php";

/**
* Basic class for all survey question types
*
* The SurveyQuestion class defines and encapsulates basic methods and attributes
* for survey question types to be used for all parent classes.
*
* @author		Helmut Schottmüller <helmut.schottmueller@mac.com>
* @version	$Id: class.SurveyQuestion.php 47936 2014-02-14 10:59:08Z jluetzen $
* @ingroup ModulesSurveyQuestionPool
*/
class SurveyQuestion
{
/**
* A unique question id
*
* @var integer
*/
  var $id;

/**
* A title string to describe the question
*
* @var string
*/
  var $title;
/**
* A description string to describe the question more detailed as the title
*
* @var string
*/
  var $description;
/**
* A unique positive numerical ID which identifies the owner/creator of the question.
* This can be a primary key from a database table for example.
*
* @var integer
*/
  var $owner;

/**
* A text representation of the authors name. The name of the author must
* not necessary be the name of the owner.
*
* @var string
*/
  var $author;

/**
* Contains uris name and uris to additional materials
*
* @var array
*/
  var $materials;

/**
* The database id of a survey in which the question is contained
*
* @var integer
*/
  var $survey_id;

/**
* Object id of the container object
*
* @var double
*/
  var $obj_id;

/**
* Questiontext string
*
* @var string
*/
  var $questiontext;

/**
* Contains the obligatory state of the question
*
* @var boolean
*/
  var $obligatory;
	
/**
* The reference to the ILIAS class
*
* @var object
*/
  var $ilias;

/**
* The reference to the Template class
*
* @var object
*/
  var $tpl;

/**
* The reference to the Language class
*
* @var object
*/
  var $lng;

	/**
	* The orientation of the question output (0 = vertical, 1 = horizontal)
	*
	* @var integer
	*/
	var $orientation;
	
	var $material;
	var $complete;

	/**
	* An array containing the cumulated results of the question for a given survey
	*/
	protected $cumulated;

	/**
	* data array containing the question data
	*/
	private $arrData;

/**
* SurveyQuestion constructor
* The constructor takes possible arguments an creates an instance of the SurveyQuestion object.
*
* @param string $title A title string to describe the question
* @param string $description A description string to describe the question
* @param string $author A string containing the name of the questions author
* @param integer $owner A numerical ID to identify the owner/creator
* @access public
*/
	function SurveyQuestion
	(
		$title = "",
		$description = "",
		$author = "",
		$questiontext = "",
		$owner = -1
	)
	{
		global $ilias;
		global $lng;
		global $tpl;

		$this->ilias =& $ilias;
		$this->lng =& $lng;
		$this->tpl =& $tpl;
		$this->complete = 
		$this->title = $title;
		$this->description = $description;
		$this->questiontext = $questiontext;
		$this->author = $author;
		$this->cumulated = array();
		if (!$this->author) 
		{
			$this->author = $this->ilias->account->fullname;
		}
		$this->owner = $owner;
		if ($this->owner == -1) 
		{
			$this->owner = $this->ilias->account->id;
		}
		$this->id = -1;
		$this->survey_id = -1;
		$this->obligatory = 1;
		$this->orientation = 0;
		$this->materials = array();
		$this->material = array();
		$this->arrData = array();
		register_shutdown_function(array(&$this, '_SurveyQuestion'));
	}

	function _SurveyQuestion()
	{
	}

	/**
	* Sets the complete state of the question
	*
	* @param integer $a_complete 1 if complete, 0 otherwise
	* @access public
	*/
	function setComplete($a_complete)
	{
		$this->complete = ($a_complete) ? 1 : 0;
	}
	
/**
* Returns 1, if a question is complete for use
*
* @return integer 1, if the question is complete for use, otherwise 0
* @access public
*/
	function isComplete()
	{
		return 0;
	}

/**
* Returns TRUE if the question title exists in the database
*
* @param string $title The title of the question
* @param string $questionpool_reference The reference id of a container question pool
* @return boolean The result of the title check
* @access public
*/
	function questionTitleExists($title, $questionpool_object = "") 
	{
		global $ilDB;
		
		$refwhere = "";
		if (strcmp($questionpool_object, "") != 0)
		{
			$refwhere = sprintf(" AND obj_fi = %s",
				$ilDB->quote($questionpool_object, 'integer')
			);
		}
		$result = $ilDB->queryF("SELECT question_id FROM svy_question WHERE title = %s$refwhere",
			array('text'),
			array($title)
		);
		return ($result->numRows() > 0) ? true : false;
	}

/**
* Sets the title string of the SurveyQuestion object
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
* Sets the obligatory state of the question
*
* @param integer $obligatory 1, if the question is obligatory, otherwise 0
* @access public
* @see $obligatory
*/
	function setObligatory($obligatory = 1) 
	{
		$this->obligatory = ($obligatory) ? 1 : 0;
	}

/**
* Sets the orientation of the question output
*
* @param integer $orientation 0 = vertical, 1 = horizontal
* @access public
* @see $orientation
*/
	function setOrientation($orientation = 0) 
	{
		$this->orientation = ($orientation) ? $orientation : 0;
	}

/**
* Sets the id of the SurveyQuestion object
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
* Sets the survey id of the SurveyQuestion object
*
* @param integer $id A unique integer value
* @access public
* @see $survey_id
*/
	function setSurveyId($id = -1) 
	{
		$this->survey_id = $id;
	}

/**
* Sets the description string of the SurveyQuestion object
*
* @param string $description A description string to describe the question
* @access public
* @see $description
*/
	function setDescription($description = "") 
	{
		$this->description = $description;
	}


/**
* Sets the materials uri
*
* @param string $materials_file An uri to additional materials
* @param string $materials_name An uri name to additional materials
* @access public
* @see $materials
*/
	function addMaterials($materials_file, $materials_name="") 
	{
		if (empty($materials_name)) 
		{
			$materials_name = $materials_file;
		}
		if ((!empty($materials_name))&&(!array_key_exists($materials_name, $this->materials))) 
		{
			$this->materials[$materials_name] = $materials_file;
		}
	}

	/**
	* Sets and uploads the materials uri
	*
	* @param string $materials_filename, string $materials_tempfilename, string $materials
	* @access public
	* @see $materials
	*/
	function setMaterialsfile($materials_filename, $materials_tempfilename="", $materials_name="")
	{
		if (!empty($materials_filename))
		{
			include_once "./Services/Utilities/classes/class.ilUtil.php";
			$materialspath = $this->getMaterialsPath();
			if (!file_exists($materialspath))
			{
				ilUtil::makeDirParents($materialspath);
			}
			//if (!move_uploaded_file($materials_tempfilename, $materialspath . $materials_filename))
			if (ilUtil::moveUploadedFile($materials_tempfilename, $materials_filename,
				$materialspath.$materials_filename))
			{
				print "image not uploaded!!!! ";
			}
			else
			{
				$this->addMaterials($materials_filename, $materials_name);
			}
		}
	}

/**
* Deletes a materials uri with a given name.
*
* @param string $index A materials_name of the materials uri
* @access public
* @see $materials
*/
  function deleteMaterial($materials_name = "") 
	{
		foreach ($this->materials as $key => $value) 
		{
			if (strcmp($key, $materials_name)==0) 
			{
				if (file_exists($this->getMaterialsPath().$value)) 
				{
					unlink($this->getMaterialsPath().$value);
				}
				unset($this->materials[$key]);
			}
		}
  }

/**
* Deletes all materials uris
*
* @access public
* @see $materials
*/
  function flushMaterials() 
	{
    $this->materials = array();
  }

/**
* Sets the authors name of the SurveyQuestion object
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
* Sets the questiontext of the SurveyQuestion object
*
* @param string $questiontext A string containing the questiontext
* @access public
* @see $questiontext
*/
	function setQuestiontext($questiontext = "") 
	{
		$this->questiontext = $questiontext;
	}

/**
* Sets the creator/owner ID of the SurveyQuestion object
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
* Gets the title string of the SurveyQuestion object
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
* Gets the id of the SurveyQuestion object
*
* @return integer The id of the SurveyQuestion object
* @access public
* @see $id
*/
	function getId() 
	{
		return $this->id;
	}

/**
* Gets the obligatory state of the question
*
* @return integer 1, if the question is obligatory, otherwise 0
* @see $obligatory
*/
	public function getObligatory($survey_id = "") 
	{
		if ($survey_id > 0)
		{
			global $ilDB;
			
			$result = $ilDB->queryF("SELECT * FROM svy_qst_oblig WHERE survey_fi = %s AND question_fi = %s",
				array('integer', 'integer'),
				array($survey_id, $this->getId())
			);
			if ($result->numRows())
			{
				$row = $ilDB->fetchAssoc($result);
				return ($row["obligatory"]) ? 1 : 0;
			}
			else
			{
				return ($this->obligatory) ? 1 : 0;
			}
		}
		else
		{
			return ($this->obligatory) ? 1 : 0;
		}
  }

/**
* Gets the survey id of the SurveyQuestion object
*
* @return integer The survey id of the SurveyQuestion object
* @access public
* @see $survey_id
*/
	function getSurveyId() 
	{
		return $this->survey_id;
	}

/**
* Gets the orientation of the question output
*
* @return integer 0 = vertical, 1 = horizontal
* @access public
* @see $orientation
*/
  function getOrientation() 
	{
		switch ($this->orientation)
		{
			case 0:
			case 1:
			case 2:
				break;
			default:
				$this->orientation = 0;
				break;
		}
    return $this->orientation;
  }


/**
* Gets the description string of the SurveyQuestion object
*
* @return string The description string to describe the question
* @access public
* @see $description
*/
	function getDescription() 
	{
		return (strlen($this->description)) ? $this->description : NULL;
	}

/**
* Gets the authors name of the SurveyQuestion object
*
* @return string The string containing the name of the questions author
* @access public
* @see $author
*/
	function getAuthor() 
	{
		return (strlen($this->author)) ? $this->author : NULL;
	}

/**
* Gets the creator/owner ID of the SurveyQuestion object
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
* Gets the questiontext of the SurveyQuestion object
*
* @return string The questiontext of the question object
* @access public
* @see $questiontext
*/
	function getQuestiontext() 
	{
		return (strlen($this->questiontext)) ? $this->questiontext : NULL;
	}

/**
* Get the reference id of the container object
*
* @return integer The reference id of the container object
* @access public
* @see $obj_id
*/
	function getObjId() {
		return $this->obj_id;
	}

/**
* Set the reference id of the container object
*
* @param integer $obj_id The reference id of the container object
* @access public
* @see $obj_id
*/
	function setObjId($obj_id = 0) 
	{
		$this->obj_id = $obj_id;
	}

/**
* Duplicates a survey question
*
* @access public
*/
	function duplicate($for_survey = true, $title = "", $author = "", $owner = "")
	{
		if ($this->getId() <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$clone = $this;
		$original_id = $this->getId();
		$clone->setId(-1);
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
		if ($for_survey)
		{
			$clone->saveToDb($original_id);
		}
		else
		{
			$clone->saveToDb();
		}
		// duplicate the materials
		$clone->duplicateMaterials($original_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($original_id);
		return $clone->getId();
	}

	/**
	* Copies an assOrderingQuestion object
	*
	* @access public
	*/
	function copyObject($target_questionpool, $title = "")
	{
		if ($this->getId() <= 0)
		{
			// The question has not been saved. It cannot be copied
			return;
		}
		$clone = $this;
		$original_id = SurveyQuestion::_getOriginalId($this->getId(), false);
		$clone->setId(-1);
		$source_questionpool = $this->getObjId();
		$clone->setObjId($target_questionpool);
		if ($title)
		{
			$clone->setTitle($title);
		}
		
		$clone->saveToDb();

		// duplicate the materials
		$clone->duplicateMaterials($original_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($original_id);
		return $clone->getId();
	}
	
/**
* Increases the media object usage counter when a question is duplicated
*
* @param integer $a_q_id The question id of the original question
* @access public
*/
	function copyXHTMLMediaObjectsOfQuestion($a_q_id)
	{
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mobs = ilObjMediaObject::_getMobsOfObject("spl:html", $a_q_id);
		foreach ($mobs as $mob)
		{
			ilObjMediaObject::_saveUsage($mob, "spl:html", $this->getId());
		}
	}
	
/**
* Loads a SurveyQuestion object from the database
*
* @param integer $question_id A unique key which defines the question in the database
* @access public
*/
	function loadFromDb($question_id)
	{
		global $ilDB;
		
		$result = $ilDB->queryF("SELECT * FROM svy_material WHERE question_fi = %s",
			array('integer'),
			array($this->getId())
		);
		$this->material = array();
		if ($result->numRows())
		{
			include_once "./Modules/SurveyQuestionPool/classes/class.ilSurveyMaterial.php";
			while ($row = $ilDB->fetchAssoc($result))
			{
				$mat = new ilSurveyMaterial();
				$mat->type = $row['material_type'];
				$mat->internal_link = $row['internal_link'];
				$mat->title = $row['material_title'];
				$mat->import_id = $row['import_id'];
				$mat->text_material = $row['text_material'];
				$mat->external_link = $row['external_link'];
				$mat->file_material = $row['file_material'];
				array_push($this->material, $mat);
			}
		}
	}

/**
* Checks whether the question is complete or not
*
* @return boolean TRUE if the question is complete, FALSE otherwise
* @access public
*/
	function _isComplete($question_id)
	{
		global $ilDB;

		$result = $ilDB->queryF("SELECT complete FROM svy_question WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows())
		{
			$row = $ilDB->fetchAssoc($result);
			if ($row["complete"] == 1)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
/**
* Saves the complete flag to the database
*
* @access public
*/
	function saveCompletionStatus($original_id = "")
	{
		global $ilDB;
		
		$question_id = $this->getId();
		if (strlen($original_id))
		{
			$question_id = $original_id;
		}

		if ($this->getId() > 0) 
		{
			// update existing dataset
			$affectedRows = $ilDB->manipulateF("UPDATE svy_question SET complete = %s, tstamp = %s WHERE question_id = %s",
				array('text', 'integer', 'integer'),
				array($this->isComplete(), time(), $question_id)
			);
		}
	}

	/**
	* Saves a SurveyQuestion object to a database
	*
	* @param integer $original_id
	* @access public
	*/
	function saveToDb($original_id = "")
	{
		global $ilDB;
		
		// cleanup RTE images which are not inserted into the question text
		include_once("./Services/RTE/classes/class.ilRTE.php");
		ilRTE::_cleanupMediaObjectUsage($this->getQuestiontext(), "spl:html", $this->getId());
		$affectedRows = 0;
		if ($this->getId() == -1) 
		{
			// Write new dataset
			$next_id = $ilDB->nextId('svy_question');
			$affectedRows = $ilDB->insert("svy_question", array(
				"question_id" => array("integer", $next_id),
				"questiontype_fi" => array("integer", $this->getQuestionTypeID()),
				"obj_fi" => array("integer", $this->getObjId()),
				"owner_fi" => array("integer", $this->getOwner()),
				"title" => array("text", $this->getTitle()),
				"label" => array("text", (strlen($this->label)) ? $this->label : null),
				"description" => array("text", $this->getDescription()),
				"author" => array("text", $this->getAuthor()),
				"questiontext" => array("clob", ilRTE::_replaceMediaObjectImageSrc($this->getQuestiontext(), 0)),
				"obligatory" => array("text", $this->getObligatory()),
				"complete" => array("text", $this->isComplete()),
				"created" => array("integer", time()),
				"original_id" => array("integer", ($original_id) ? $original_id : NULL),
				"tstamp" => array("integer", time())
			));
			$this->setId($next_id);
		} 
		else 
		{
			// update existing dataset
			$affectedRows = $ilDB->update("svy_question", array(
				"title" => array("text", $this->getTitle()),
				"label" => array("text", (strlen($this->label)) ? $this->label : null),
				"description" => array("text", $this->getDescription()),
				"author" => array("text", $this->getAuthor()),
				"questiontext" => array("clob", ilRTE::_replaceMediaObjectImageSrc($this->getQuestiontext(), 0)),
				"obligatory" => array("text", $this->getObligatory()),
				"complete" => array("text", $this->isComplete()),
				"tstamp" => array("integer", time())
			), array(
			"question_id" => array("integer", $this->getId())
			));
		}
		
		// #12420
		$set = $ilDB->query("SELECT survey_id FROM svy_svy".
			" WHERE obj_fi = ".$ilDB->quote($this->getObjId(), "integer"));
		$survey_fi = $ilDB->fetchAssoc($set);
		$survey_fi = $survey_fi["survey_id"];
		
		// pool?
		if($survey_fi)
		{	
			$set = $ilDB->query("SELECT obligatory FROM svy_qst_oblig".
				 " WHERE survey_fi = ".$ilDB->quote($survey_fi, "integer").
				" AND question_fi = ".$ilDB->quote($this->getId(), "integer"));
			$has_obligatory_states_entry = (bool)$ilDB->numRows($set);
			$is_obligatory = $ilDB->fetchAssoc($set);
			$is_obligatory = (bool)$is_obligatory["obligatory"];

			if(!$this->getObligatory())
			{
				if($has_obligatory_states_entry)
				{
					$ilDB->manipulate("DELETE FROM svy_qst_oblig".
						" WHERE survey_fi = ".$ilDB->quote($survey_fi, "integer").
						" AND question_fi = ".$ilDB->quote($this->getId(), "integer"));			
				}
			}
			else if($this->getObligatory())
			{
				if(!$has_obligatory_states_entry)
				{
					// ilObjSurvey::setObligatoryStates()
					$next_id = $ilDB->nextId('svy_qst_oblig');
					$affectedRows = $ilDB->manipulateF("INSERT INTO svy_qst_oblig (question_obligatory_id, survey_fi, question_fi, " .
						"obligatory, tstamp) VALUES (%s, %s, %s, %s, %s)",
						array('integer','integer','integer','text','integer'),
						array($next_id, $survey_fi, $this->getId(), 1, time())
					);
				}
				else if(!$is_obligatory)
				{
					 $ilDB->manipulate("UPDATE svy_qst_oblig".
						" SET obligatory = ".$ilDB->quote(1, "integer").
						" WHERE survey_fi = ".$ilDB->quote($survey_fi, "integer").
						" AND question_fi = ".$ilDB->quote($this->getId(), "integer"));	
				}
			}
		}
		
		return $affectedRows;
	}
	
	/**
	* save material to db
	*/
	public function saveMaterial()
	{
		global $ilDB;
		
		include_once "./Services/COPage/classes/class.ilInternalLink.php";
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_material WHERE question_fi = %s",
			array('integer'),
			array($this->getId())
		);
		ilInternalLink::_deleteAllLinksOfSource("sqst", $this->getId());

		foreach ($this->material as $material)
		{
			$next_id = $ilDB->nextId('svy_material');
			$affectedRows = $ilDB->manipulateF("INSERT INTO svy_material " .
				"(material_id, question_fi, internal_link, import_id, material_title, tstamp," .
				"text_material, external_link, file_material, material_type) ".
				"VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				array('integer','integer','text','text','text','integer','text','text','text','integer'),
				array(
					$next_id, $this->getId(), $material->internal_link, $material->import_id, 
					$material->title, time(), $material->text_material, $material->external_link,
					$material->file_material, $material->type)
			);
			if (preg_match("/il_(\d*?)_(\w+)_(\d+)/", $material->internal_link, $matches))
			{
				ilInternalLink::_saveLink("sqst", $this->getId(), $matches[2], $matches[3], $matches[1]);
			}
		}
	}
	
	/**
	* Creates a new question with a 0 timestamp when a new question is created
	* This assures that an ID is given to the question if a file upload or something else occurs
	*
	* @return integer ID of the new question
	*/
	public function createNewQuestion()
	{
		global $ilDB, $ilUser;
		
		$obj_id = ($this->getObjId() <= 0) ? (ilObject::_lookupObjId((strlen($_GET["ref_id"])) ? $_GET["ref_id"] : $_POST["sel_qpl"])) : $this->getObjId();
		if ($obj_id > 0)
		{
			$next_id = $ilDB->nextId('svy_question');
			$affectedRows = $ilDB->manipulateF("INSERT INTO svy_question (question_id, questiontype_fi, " .
				"obj_fi, owner_fi, title, description, author, questiontext, obligatory, complete, " .
				"created, original_id, tstamp) VALUES " .
				"(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				array('integer', 'integer', 'integer', 'integer', 'text', 'text', 'text', 'text', 
					'text', 'text', 'integer', 'integer', 'integer'),
				array(
					$next_id,
					$this->getQuestionTypeID(),
					$obj_id,
					$this->getOwner(),
					NULL,
					NULL,
					$this->getAuthor(),
					NULL,
					"1",
					"0",
					time(),
					NULL,
					0
				)
			);
			$this->setId($next_id);
		}
		return $this->getId();
	}

/**
* Saves the learners input of the question to the database
*
* @access public
* @see $answers
*/
  function saveWorkingData($limit_to = LIMIT_NO_LIMIT) 
	{
  }

/**
* Returns the image path for web accessable images of a question.
* The image path is under the CLIENT_WEB_DIR in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/images
*
* @access public
*/
	function getImagePath() 
	{
		return CLIENT_WEB_DIR . "/survey/$this->obj_id/$this->id/images/";
	}

/**
* Returns the materials path for web accessable materials of a question.
* The materials path is under the CLIENT_WEB_DIR in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/materials
*
* @access public
*/
	function getMaterialsPath() 
	{
		return CLIENT_WEB_DIR . "/survey/$this->obj_id/$this->id/materials/";
	}

/**
* Returns the web image path for web accessable images of a question.
* The image path is under the web accessable data dir in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/images
*
* @access public
*/
	function getImagePathWeb() 
	{
		include_once "./Services/Utilities/classes/class.ilUtil.php";
		$webdir = ilUtil::removeTrailingPathSeparators(CLIENT_WEB_DIR) . "/survey/$this->obj_id/$this->id/images/";
		return str_replace(ilUtil::removeTrailingPathSeparators(ILIAS_ABSOLUTE_PATH), ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH), $webdir);
	}

/**
* Returns the web image path for web accessable images of a question.
* The image path is under the web accessable data dir in assessment/REFERENCE_ID_OF_QUESTION_POOL/ID_OF_QUESTION/images
*
* @access public
*/
	function getMaterialsPathWeb() 
	{
		include_once "./Services/Utilities/classes/class.ilUtil.php";
		$webdir = ilUtil::removeTrailingPathSeparators(CLIENT_WEB_DIR) . "/survey/$this->obj_id/$this->id/materials/";
		return str_replace(ilUtil::removeTrailingPathSeparators(ILIAS_ABSOLUTE_PATH), ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH), $webdir);
	}

/**
* Saves a category to the database
*
* @param string $categorytext The description of the category
* @result integer The database id of the category
* @access public
* @see $categories
*/
	function saveCategoryToDb($categorytext, $neutral = 0)
	{
		global $ilUser, $ilDB;
		
		$result = $ilDB->queryF("SELECT title, category_id FROM svy_category WHERE title = %s AND neutral = %s AND owner_fi = %s",
			array('text','text','integer'),
			array($categorytext, $neutral, $ilUser->getId())
		);
		$insert = FALSE;
		$returnvalue = "";
		if ($result->numRows()) 
		{
			$insert = TRUE;
			while ($row = $ilDB->fetchAssoc($result))
			{
				if (strcmp($row["title"], $categorytext) == 0)
				{
					$returnvalue = $row["category_id"];
					$insert = FALSE;
				}
			}
		}
		else
		{
			$insert = TRUE;
		}
		if ($insert)
		{
			$next_id = $ilDB->nextId('svy_category');
			$affectedRows = $ilDB->manipulateF("INSERT INTO svy_category (category_id, title, neutral, owner_fi, tstamp) VALUES (%s, %s, %s, %s, %s)",
				array('integer','text','text','integer','integer'),
				array($next_id, $categorytext, $neutral, $ilUser->getId(), time())
			);
			$returnvalue = $next_id;
		}
		return $returnvalue;
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
		$affectedRows = $ilDB->manipulateF("DELETE FROM " . $this->getAdditionalTableName() . " WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
	}

/**
* Deletes a question and all materials from the database
*
* @param integer $question_id The database id of the question
* @access private
*/
	function delete($question_id) 
	{
		global $ilDB;
		
		if ($question_id < 1) return;
      
		$result = $ilDB->queryF("SELECT obj_fi FROM svy_question WHERE question_id = %s",
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
			return;
		}
		
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_answer WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);

		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_constraint WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);

		$result = $ilDB->queryF("SELECT constraint_fi FROM svy_qst_constraint WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		while ($row = $ilDB->fetchObject($result))
		{
			$affectedRows = $ilDB->manipulateF("DELETE FROM svy_constraint WHERE constraint_id = %s",
				array('integer'),
				array($row->constraint_fi)
			);
		}
	
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_qst_constraint WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);

		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_qblk_qst WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_qst_oblig WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_svy_qst WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_variable WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_question WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);

		$this->deleteAdditionalTableData($question_id);
		
		$affectedRows = $ilDB->manipulateF("DELETE FROM svy_material WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		include_once "./Services/COPage/classes/class.ilInternalLink.php";
		ilInternalLink::_deleteAllLinksOfSource("sqst", $question_id);

		$directory = CLIENT_WEB_DIR . "/survey/" . $obj_id . "/$question_id";
		if (preg_match("/\d+/", $obj_id) and preg_match("/\d+/", $question_id) and is_dir($directory))
		{
			include_once "./Services/Utilities/classes/class.ilUtil.php";
			ilUtil::delDir($directory);
		}

		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");
		$mobs = ilObjMediaObject::_getMobsOfObject("spl:html", $question_id);
		// remaining usages are not in text anymore -> delete them
		// and media objects (note: delete method of ilObjMediaObject
		// checks whether object is used in another context; if yes,
		// the object is not deleted!)
		foreach($mobs as $mob)
		{
			ilObjMediaObject::_removeUsage($mob, "spl:html", $question_id);
			$mob_obj =& new ilObjMediaObject($mob);
			$mob_obj->delete();
		}
		
		// #12772 - untie question copies from pool question 
		$ilDB->manipulate("UPDATE svy_question".
			" SET original_id = NULL".
			" WHERE original_id  = ".$ilDB->quote($question_id, "integer"));		
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

		$result = $ilDB->queryF("SELECT type_tag FROM svy_question, svy_qtype WHERE svy_question.question_id = %s AND svy_question.questiontype_fi = svy_qtype.questiontype_id",
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
	function _getTitle($question_id) 
	{
		global $ilDB;

		if ($question_id < 1) return "";

		$result = $ilDB->queryF("SELECT title FROM svy_question WHERE svy_question.question_id = %s",
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

/**
* Returns the original id of a question
*
* @param integer $question_id The database id of the question
* @return integer The database id of the original question
* @access public
*/
	function _getOriginalId($question_id, $a_return_question_id_if_no_original = true)
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT * FROM svy_question WHERE question_id = %s",
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
			else if((bool)$a_return_question_id_if_no_original) // #12419
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
		
		if ($this->getOriginalId())
		{
			$id = $this->getId();
			$original = $this->getOriginalId();

			$this->setId($this->getOriginalId());
			$this->setOriginalId(NULL);
			$this->saveToDb();

			$this->setId($id);
			$this->setOriginalId($original);

			include_once "./Services/COPage/classes/class.ilInternalLink.php";
			$affectedRows = $ilDB->manipulateF("DELETE FROM svy_material WHERE question_fi = %s",
				array('integer'),
				array($this->getOriginalId())
			);
			ilInternalLink::_deleteAllLinksOfSource("sqst", $this->original_id);
			if (strlen($this->material["internal_link"]))
			{
				$next_id = $ilDB->nextId('svy_material');
				$affectedRows = $ilDB->manipulateF("INSERT INTO svy_material (material_id, question_fi, internal_link, import_id, material_title, tstamp) VALUES (%s, %s, %s, %s, %s, %s)",
					array('integer', 'integer', 'text', 'text', 'text', 'integer'),
					array($next_id, $this->getOriginalId(), $this->material["internal_link"], $this->material["import_id"], $this->material["title"], time())
				);
				if (preg_match("/il_(\d*?)_(\w+)_(\d+)/", $this->material["internal_link"], $matches))
				{
					ilInternalLink::_saveLink("sqst", $this->getOriginalId(), $matches[2], $matches[3], $matches[1]);
				}
			}
		}
	}

/**
* Returns a phrase for a given database id
*
* @result String The title of the phrase
* @access public
*/
	function getPhrase($phrase_id)
	{
		global $ilDB;
		
		$result = $ilDB->queryF("SELECT title FROM svy_phrase WHERE phrase_id = %s",
			array('integer'),
			array($phrase_id)
		);
		if ($row = $ilDB->fetchAssoc($result))
		{
			return $row["title"];
		}
		return "";
	}

/**
* Returns true if the phrase title already exists for the current user
*
* @param string $title The title of the phrase
* @result boolean True, if the title exists, otherwise False
* @access public
*/
	function phraseExists($title)
	{
		global $ilUser, $ilDB;
		
		$result = $ilDB->queryF("SELECT phrase_id FROM svy_phrase WHERE title = %s AND owner_fi = %s",
			array('text', 'integer'),
			array($title, $ilUser->getId())
		);
		return ($result->numRows() == 0) ? false : true;
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
		
		$result = $ilDB->queryF("SELECT question_id FROM svy_question WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		return ($result->numRows() == 1) ? true : false;
	}

	function addInternalLink($material_id, $title = "")
	{
		if (strlen($material_id))
		{
			if (strcmp($material_title, "") == 0)
			{
				if (preg_match("/il__(\w+)_(\d+)/", $material_id, $matches))
				{
					$type = $matches[1];
					$target_id = $matches[2];
					$material_title = $this->lng->txt("obj_$type") . ": ";
					switch ($type)
					{
						case "lm":
							include_once("./Modules/LearningModule/classes/class.ilObjContentObjectGUI.php");
							$cont_obj_gui =& new ilObjContentObjectGUI("", $target_id, true);
							$cont_obj = $cont_obj_gui->object;
							$material_title .= $cont_obj->getTitle();
							break;
						case "pg":
							include_once("./Modules/LearningModule/classes/class.ilLMPageObject.php");
							include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
							$lm_id = ilLMObject::_lookupContObjID($target_id);
							include_once("./Modules/LearningModule/classes/class.ilObjContentObjectGUI.php");
							$cont_obj_gui =& new ilObjContentObjectGUI("", $lm_id, FALSE);
							$cont_obj = $cont_obj_gui->object;
							$pg_obj =& new ilLMPageObject($cont_obj, $target_id);
							$material_title .= $pg_obj->getTitle();
							break;
						case "st":
							include_once("./Modules/LearningModule/classes/class.ilStructureObject.php");
							include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
							$lm_id = ilLMObject::_lookupContObjID($target_id);
							include_once("./Modules/LearningModule/classes/class.ilObjContentObjectGUI.php");
							$cont_obj_gui =& new ilObjContentObjectGUI("", $lm_id, FALSE);
							$cont_obj = $cont_obj_gui->object;
							$st_obj =& new ilStructureObject($cont_obj, $target_id);
							$material_title .= $st_obj->getTitle();
							break;
						case "git":
							include_once "./Modules/Glossary/classes/class.ilGlossaryTerm.php";
							$material_title = $this->lng->txt("glossary_term") . ": " . ilGlossaryTerm::_lookGlossaryTerm($target_id);
							break;
						case "mob":
							break;
					}
				}
			}
			include_once "./Modules/SurveyQuestionPool/classes/class.ilSurveyMaterial.php";
			$mat = new ilSurveyMaterial();
			$mat->type = 0;
			$mat->internal_link = $material_id;
			$mat->title = $material_title;
			$this->addMaterial($mat);
			$this->saveMaterial();
		}
	}
	
	/**
	* Deletes materials
	*
	* @param array $a_array Array with indexes of the materials to delete
	*/
	public function deleteMaterials($a_array) 
	{
		foreach ($a_array as $idx)
		{
			unset($this->material[$idx]);
		}
		$this->material = array_values($this->material);
		$this->saveMaterial();
	}

	/**
	* Duplicates the materials of a question
	*
	* @param integer $question_id The database id of the original survey question
	* @access public
	*/
	function duplicateMaterials($question_id)
	{
		foreach ($this->materials as $filename)
		{
			$materialspath = $this->getMaterialsPath();
			$materialspath_original = preg_replace("/([^\d])$this->id([^\d])/", "\${1}$question_id\${2}", $materialspath);
			if (!file_exists($materialspath)) 
			{
				include_once "./Services/Utilities/classes/class.ilUtil.php";
				ilUtil::makeDirParents($materialspath);
			}
			if (!copy($materialspath_original . $filename, $materialspath . $filename)) 
			{
				print "material could not be duplicated!!!! ";
			}
		}
	}
	
	public function addMaterial($obj_material)
	{
		array_push($this->material, $obj_material);
	}
	
/**
* Sets a material link for the question
*
* @param string $material_id An internal link pointing to the material
* @param boolean $is_import A boolean indication that the internal link was imported from another ILIAS installation
* @access public
*/
	function setMaterial($material_id = "", $is_import = false, $material_title = "")
	{
		if (strcmp($material_id, "") != 0)
		{
			$import_id = "";
			if ($is_import)
			{
				$import_id = $material_id;
				$material_id = $this->_resolveInternalLink($import_id);
			}
			if (strcmp($material_title, "") == 0)
			{
				if (preg_match("/il__(\w+)_(\d+)/", $material_id, $matches))
				{
					$type = $matches[1];
					$target_id = $matches[2];
					$material_title = $this->lng->txt("obj_$type") . ": ";
					switch ($type)
					{
						case "lm":
							include_once("./Modules/LearningModule/classes/class.ilObjContentObjectGUI.php");
							$cont_obj_gui =& new ilObjContentObjectGUI("", $target_id, true);
							$cont_obj = $cont_obj_gui->object;
							$material_title .= $cont_obj->getTitle();
							break;
						case "pg":
							include_once("./Modules/LearningModule/classes/class.ilLMPageObject.php");
							include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
							$lm_id = ilLMObject::_lookupContObjID($target_id);
							include_once("./Modules/LearningModule/classes/class.ilObjContentObjectGUI.php");
							$cont_obj_gui =& new ilObjContentObjectGUI("", $lm_id, FALSE);
							$cont_obj = $cont_obj_gui->object;
							$pg_obj =& new ilLMPageObject($cont_obj, $target_id);
							$material_title .= $pg_obj->getTitle();
							break;
						case "st":
							include_once("./Modules/LearningModule/classes/class.ilStructureObject.php");
							include_once("./Modules/LearningModule/classes/class.ilLMObject.php");
							$lm_id = ilLMObject::_lookupContObjID($target_id);
							include_once("./Modules/LearningModule/classes/class.ilObjContentObjectGUI.php");
							$cont_obj_gui =& new ilObjContentObjectGUI("", $lm_id, FALSE);
							$cont_obj = $cont_obj_gui->object;
							$st_obj =& new ilStructureObject($cont_obj, $target_id);
							$material_title .= $st_obj->getTitle();
							break;
						case "git":
							include_once "./Modules/Glossary/classes/class.ilGlossaryTerm.php";
							$material_title = $this->lng->txt("glossary_term") . ": " . ilGlossaryTerm::_lookGlossaryTerm($target_id);
							break;
						case "mob":
							break;
					}
				}
			}
			$this->material = array(
				"internal_link" => $material_id,
				"import_id" => $import_id,
				"title" => $material_title
			);
		}
		$this->saveMaterial();
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
		$result = $ilDB->queryF("SELECT * FROM svy_material WHERE question_fi = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows())
		{
			while ($row = $ilDB->fetchAssoc($result))
			{
				$internal_link = $row["internal_link"];
				include_once "./Modules/SurveyQuestionPool/classes/class.SurveyQuestion.php";
				$resolved_link = SurveyQuestion::_resolveInternalLink($internal_link);
				if (strcmp($internal_link, $resolved_link) != 0)
				{
					// internal link was resolved successfully
					$affectedRows = $ilDB->manipulateF("UPDATE svy_material SET internal_link = %s, tstamp = %s WHERE material_id = %s",
						array('text', 'integer', 'integer'),
						array($resolved_link, time(), $row["material_id"])
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
			ilInternalLink::_deleteAllLinksOfSource("sqst", $question_id);

			$result = $ilDB->queryF("SELECT * FROM svy_material WHERE question_fi = %s",
				array('integer'),
				array($question_id)
			);
			if ($result->numRows())
			{
				while ($row = $ilDB->fetchAssoc($result))
				{
					if (preg_match("/il_(\d*?)_(\w+)_(\d+)/", $row["internal_link"], $matches))
					{
						ilInternalLink::_saveLink("sqst", $question_id, $matches[2], $matches[3], $matches[1]);
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
					$href = ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH) ."/goto.php?target=" . $type . "_" . $target_id;
					break;
				case "PageObject":
				case "StructureObject":
					$href = ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH) ."/goto.php?target=" . $type . "_" . $target_id;
					break;
				case "GlossaryItem":
					$href = ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH) ."/goto.php?target=" . $type . "_" . $target_id;
					break;
				case "MediaObject":
					$href = ilUtil::removeTrailingPathSeparators(ILIAS_HTTP_PATH) . "/ilias.php?baseClass=ilLMPresentationGUI&obj_type=" . $linktypes[$type] . "&cmd=media&ref_id=".$_GET["ref_id"]."&mob_id=".$target_id;
					break;
			}
		}
		return $href;
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
		
		$result = $ilDB->queryF("SELECT obj_fi FROM svy_question WHERE question_id = %s",
			array('integer'),
			array($question_id)
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			$qpl_object_id = $row["obj_fi"];
			include_once "./Modules/SurveyQuestionPool/classes/class.ilObjSurveyQuestionPool.php";
			return ilObjSurveyQuestionPool::_isWriteable($qpl_object_id, $user_id);
		}
		else
		{
			return false;
		}
	}

	/**
	* Returns the question type ID of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionTypeID()
	{
		global $ilDB;
		$result = $ilDB->queryF("SELECT questiontype_id FROM svy_qtype WHERE type_tag = %s",
			array('text'),
			array($this->getQuestionType())
		);
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			return $row["questiontype_id"];
		}
		else
		{
			return 0;
		}
	}

	/**
	* Returns the question type of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionType()
	{
		return "";
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
		if (file_exists("./Modules/SurveyQuestionPool/classes/class.".$type.".php"))
		{
			include_once "./Modules/SurveyQuestionPool/classes/class.".$type.".php";
			return true;
		}
		else
		{
			global $ilPluginAdmin;
			$pl_names = $ilPluginAdmin->getActivePluginsForSlot(IL_COMP_MODULE, "SurveyQuestionPool", "svyq");
			foreach ($pl_names as $pl_name)
			{
				$pl = ilPlugin::getPluginObject(IL_COMP_MODULE, "SurveyQuestionPool", "svyq", $pl_name);
				if (strcmp($pl->getQuestionType(), $question_type) == 0)
				{
					$pl->includeClass("class.".$type.".php");
					return true;
				}
			}
		}
		return false;
	}

	/**
	* Return the translation for a given question type tag
	*
	* @param string $type_tag The type tag of the question type
	* @access public
	*/
	static function _getQuestionTypeName($type_tag)
	{
		if (file_exists("./Modules/SurveyQuestionPool/classes/class.".$type_tag.".php"))
		{
			global $lng;
			return $lng->txt($type_tag);
		}
		else
		{
			global $ilPluginAdmin;
			$pl_names = $ilPluginAdmin->getActivePluginsForSlot(IL_COMP_MODULE, "SurveyQuestionPool", "svyq");
			foreach ($pl_names as $pl_name)
			{
				$pl = ilPlugin::getPluginObject(IL_COMP_MODULE, "SurveyQuestionPool", "svyq", $pl_name);
				if (strcmp($pl->getQuestionType(), $type_tag) == 0)
				{
					return $pl->getQuestionTypeTranslation();
				}
			}
		}
		return "";
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
		$question_type = SurveyQuestion::_getQuestionType($question_id);
		SurveyQuestion::_includeClass($question_type);
		$question = new $question_type();
		$question->loadFromDb($question_id);
		return $question;
  }
	
	/**
	* Creates an instance of a question GUI with a given question id
	*
	* @param integer $question_id The question id
	* @return object The question GUI instance
	* @access public
	*/
	  function &_instanciateQuestionGUI($question_id) 
		{
			$question_type = SurveyQuestion::_getQuestionType($question_id);
			SurveyQuestion::_includeClass($question_type, 1);
			$guitype = $question_type . "GUI";
			$question = new $guitype($question_id);
			return $question;
	  }

	/**
	* Checks if a given string contains HTML or not
	*
	* @param string $a_text Text which should be checked
	*
	* @return boolean 
	* @access public
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
	* Creates an XML material tag from a plain text or xhtml text
	*
	* @param object $a_xml_writer Reference to the ILIAS XML writer
	* @param string $a_material plain text or html text containing the material
	* @return string XML material tag
	* @access public
	*/
	function addMaterialTag(&$a_xml_writer, $a_material, $close_material_tag = TRUE, $add_mobs = TRUE, $a_attrs = null)
	{
		include_once "./Services/RTE/classes/class.ilRTE.php";
		include_once("./Services/MediaObjects/classes/class.ilObjMediaObject.php");

		$a_xml_writer->xmlStartTag("material");
		$attrs = array(
			"type" => "text/plain"
		);
		if ($this->isHTML($a_material))
		{
			$attrs["type"] = "text/xhtml";
		}
		if (is_array($a_attrs))
		{
			$attrs = array_merge($attrs, $a_attrs);
		}
		$a_xml_writer->xmlElement("mattext", $attrs, ilRTE::_replaceMediaObjectImageSrc($a_material, 0));

		if ($add_mobs)
		{
			$mobs = ilObjMediaObject::_getMobsOfObject("spl:html", $this->getId());
			foreach ($mobs as $mob)
			{
				$mob_obj =& new ilObjMediaObject($mob);
				$imgattrs = array(
					"label" => "il_" . IL_INST_ID . "_mob_" . $mob,
					"uri" => "objects/" . "il_" . IL_INST_ID . "_mob_" . $mob . "/" . $mob_obj->getTitle()
				);
				$a_xml_writer->xmlElement("matimage", $imgattrs, NULL);
			}
		}		
		if ($close_material_tag) $a_xml_writer->xmlEndTag("material");
	}

	/**
	* Prepares a string for a text area output in surveys
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
	* Returns the question data fields from the database
	*
	* @param integer $id The question ID from the database
	* @return array Array containing the question fields and data from the database
	* @access public
	*/
	function _getQuestionDataArray($id)
	{
		return array();
	}

	/**
	* Adds the entries for the title row of the user specific results
	*
	* @param array $a_array An array which is used to append the title row entries
	* @access public
	*/
	function addUserSpecificResultsExportTitles(&$a_array, $a_use_label = false, $a_substitute = true)
	{
		if(!$a_use_label)
		{
			$title = $this->title;			
		}
		else
		{
			if($a_substitute)
			{
				$title = $this->label ? $this->label : $this->title;
			}
			else
			{
				$title = $this->label;
			}
		}	
		
		array_push($a_array, $title);
		return $title;
	}

	/**
	* Adds the values for the user specific results export for a given user
	*
	* @param array $a_array An array which is used to append the values
	* @param array $resultset The evaluation data for a given user
	* @access public
	*/
	function addUserSpecificResultsData(&$a_array, &$resultset)
	{
		// overwrite in inherited classes
	}

	/**
	* Returns an array containing all answers to this question in a given survey
	*
	* @param integer $survey_id The database ID of the survey
	* @return array An array containing the answers to the question. The keys are either the user id or the anonymous id
	* @access public
	*/
	function &getUserAnswers($survey_id)
	{
		// overwrite in inherited classes
		return array();
	}

	/**
	* Creates the user data of the svy_answer table from the POST data
	*
	* @return array User data according to the svy_answer table
	* @access public
	*/
	function &getWorkingDataFromUserInput($post_data)
	{
		// overwrite in inherited classes
		$data = array();
		return $data;
	}
	
	/**
	* Import additional meta data from the question import file. Usually
	* the meta data section is used to store question elements which are not
	* part of the standard XML schema.
	*
	* @return array $a_meta Array containing the additional meta data
	* @access public
	*/
	function importAdditionalMetadata($a_meta)
	{
		// overwrite in inherited classes
	}
	
	/**
	* Import response data from the question import file
	*
	* @return array $a_data Array containing the response data
	* @access public
	*/
	function importResponses($a_data)
	{
		// overwrite in inherited classes
	}

	/**
	* Import bipolar adjectives from the question import file
	*
	* @return array $a_data Array containing the adjectives
	* @access public
	*/
	function importAdjectives($a_data)
	{
		// overwrite in inherited classes
	}

	/**
	* Import matrix rows from the question import file
	*
	* @return array $a_data Array containing the matrix rows
	* @access public
	*/
	function importMatrix($a_data)
	{
		// overwrite in inherited classes
	}

	/**
	* Creates the Excel output for the cumulated results of this question
	*
	* @param object $worksheet Reference to the excel worksheet
	* @param object $format_title Excel title format
	* @param object $format_bold Excel bold format
	* @param array $eval_data Cumulated evaluation data
	* @param integer $row Actual row in the worksheet
	* @return integer The next row which should be used for the export
	* @access public
	*/
	function setExportCumulatedXLS(&$worksheet, &$format_title, &$format_bold, &$eval_data, $row, $export_label)
	{
		include_once ("./Services/Excel/classes/class.ilExcelUtils.php");
		$column = 0;
		switch ($export_label)
		{
			case 'label_only':
				$worksheet->writeString($row, $column, ilExcelUtils::_convert_text($this->label));
				break;
			case 'title_only':
				$worksheet->writeString($row, $column, ilExcelUtils::_convert_text($this->getTitle()));
				break;
			default:
				$worksheet->writeString($row, $column, ilExcelUtils::_convert_text($this->getTitle()));
				$column++;
				$worksheet->writeString($row, $column, ilExcelUtils::_convert_text($this->label));
				break;
		}
		$column++;
		$worksheet->writeString($row, $column, ilExcelUtils::_convert_text($this->getQuestiontext()));
		$column++;
		$worksheet->writeString($row, $column, ilExcelUtils::_convert_text($this->lng->txt($eval_data["QUESTION_TYPE"])));
		$column++;
		$worksheet->write($row, $column, $eval_data["USERS_ANSWERED"]);
		$column++;
		$worksheet->write($row, $column, $eval_data["USERS_SKIPPED"]);
		$column++;
		$worksheet->write($row, $column, ilExcelUtils::_convert_text($eval_data["MODE_VALUE"]));
		$column++;
		$worksheet->write($row, $column, ilExcelUtils::_convert_text($eval_data["MODE"]));
		$column++;
		$worksheet->write($row, $column, $eval_data["MODE_NR_OF_SELECTIONS"]);
		$column++;
		$worksheet->write($row, $column, ilExcelUtils::_convert_text(str_replace("<br />", " ", $eval_data["MEDIAN"])));
		$column++;
		$worksheet->write($row, $column, $eval_data["ARITHMETIC_MEAN"]);
		return $row + 1;
	}
	
	/**
	* Creates the CSV output for the cumulated results of this question
	*
	* @param object $worksheet Reference to the excel worksheet
	* @param object $format_title Excel title format
	* @param object $format_bold Excel bold format
	* @param array $eval_data Cumulated evaluation data
	* @param integer $row Actual row in the worksheet
	* @return integer The next row which should be used for the export
	* @access public
	*/
	function &setExportCumulatedCVS(&$eval_data, $export_label)
	{
		$csvrow = array();
		switch ($export_label)
		{
			case 'label_only':
				array_push($csvrow, $this->label);
				break;
			case 'title_only':
				array_push($csvrow, $this->getTitle());
				break;
			default:
				array_push($csvrow, $this->getTitle());
				array_push($csvrow, $this->label);
				break;
		}
		array_push($csvrow, $this->getQuestiontext());
		array_push($csvrow, $this->lng->txt($eval_data["QUESTION_TYPE"]));
		array_push($csvrow, $eval_data["USERS_ANSWERED"]);
		array_push($csvrow, $eval_data["USERS_SKIPPED"]);
		array_push($csvrow, $eval_data["MODE"]);
		array_push($csvrow, $eval_data["MODE_NR_OF_SELECTIONS"]);
		array_push($csvrow, $eval_data["MEDIAN"]);
		array_push($csvrow, $eval_data["ARITHMETIC_MEAN"]);
		$result = array();
		array_push($result, $csvrow);
		return $result;
	}
	
	/**
	* Creates an Excel worksheet for the detailed cumulated results of this question
	*
	* @param object $workbook Reference to the parent excel workbook
	* @param object $format_title Excel title format
	* @param object $format_bold Excel bold format
	* @param array $eval_data Cumulated evaluation data
	* @access public
	*/
	function setExportDetailsXLS(&$workbook, &$format_title, &$format_bold, &$eval_data, $export_label)
	{
		// overwrite in inherited classes
	}
	
	/**
	* Returns if the question is usable for preconditions
	*
	* @return boolean TRUE if the question is usable for a precondition, FALSE otherwise
	* @access public
	*/
	function usableForPrecondition()
	{
		// overwrite in inherited classes
		return FALSE;
	}

	/**
	* Returns the available relations for the question
	*
	* @return array An array containing the available relations
	* @access public
	*/
	function getAvailableRelations()
	{
		// overwrite in inherited classes
		return array();
	}

	/**
	* Returns the options for preconditions
	*
	* @return array
	*/
	public function getPreconditionOptions()
	{
		// overwrite in inherited classes
	}
	
	/**
	* Returns the output for a precondition value
	*
	* @param string $value The precondition value
	* @return string The output of the precondition value
	* @access public
	*/
	function getPreconditionValueOutput($value)
	{
		// overwrite in inherited classes
		return $value;
	}

	/**
	* Creates a form property for the precondition value
	*
	* @return The ILIAS form element
	* @access public
	*/
	public function getPreconditionSelectValue($default = "", $title, $variable)
	{
		// overwrite in inherited classes
		return null;
	}

/**
* Creates an image visualising the results of the question
*
* @param integer $survey_id The database ID of the survey
* @param string $type An additional parameter to allow to draw more than one chart per question. Must be interpreted by the question. Default is an empty string
* @return binary Image with the visualisation
* @access private
*/
	function outChart($survey_id, $type = "")
	{
		// overwrite in inherited classes
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
	* Saves random answers for a given active user in the database
	*
	* @param integer $active_id The database ID of the active user
	*/
	public function saveRandomData($active_id)
	{
		// do nothing, overwrite in parent classes
	}
	
	public function getMaterial()
	{
		return $this->material;
	}
	
	public function setSubtype($a_subtype) 
	{
		// do nothing
	}

	public function getSubtype() 
	{
		// do nothing
		return null;
	}
	
	protected function &calculateCumulatedResults($survey_id)
	{
		if (count($this->cumulated) == 0)
		{
			include_once "./Modules/Survey/classes/class.ilObjSurvey.php";
			$nr_of_users = ilObjSurvey::_getNrOfParticipants($survey_id);
			$this->cumulated =& $this->getCumulatedResults($survey_id, $nr_of_users);
		}
		return $this->cumulated;
	}

	/**
	* Creates a the cumulated results data for the question
	*
	* @return array Data
	*/
	public function getCumulatedResultData($survey_id, $counter)
	{
		$cumulated =& $this->calculateCumulatedResults($survey_id);
		$questiontext = preg_replace("/\<[^>]+?>/ims", "", $this->getQuestiontext());
		
		$maxlen = 75;
		include_once "./Services/Utilities/classes/class.ilStr.php";
		if (ilStr::strlen($questiontext) > $maxlen + 3)
		{
			$questiontext = ilStr::substr($questiontext, 0, $maxlen) . "...";
		}
		
		$result = array(
			'counter' => $counter,
			'title' => $this->getTitle(),
			'question' => $questiontext,
			'users_answered' => $cumulated['USERS_ANSWERED'],
			'users_skipped' => $cumulated['USERS_SKIPPED'],
			'question_type' => $this->lng->txt($cumulated["QUESTION_TYPE"]),
			'mode' => $cumulated["MODE"],
			'mode_nr_of_selections' => $cumulated["MODE_NR_OF_SELECTIONS"],
			'median' => $cumulated["MEDIAN"],
			'arithmetic_mean' => $cumulated["ARITHMETIC_MEAN"]
		);
		return $result;
	}

	/**
	* Object getter
	*/
	public function __get($value)
	{
		switch ($value)
		{
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
			default:
				$this->arrData[$key] = $value;
				break;
		}
	}

	/**
	 * Change original id of existing question in db
	 *
	 * @param int $a_question_id
	 * @param int $a_original_id
	 * @param int $a_object_id
	 */
	public static function _changeOriginalId($a_question_id, $a_original_id, $a_object_id)
	{
		global $ilDB;

		$ilDB->manipulate("UPDATE svy_question".
			" SET original_id = ".$ilDB->quote($a_original_id, "integer").",".
			" obj_fi = ".$ilDB->quote($a_object_id, "integer").
			" WHERE question_id = ".$ilDB->quote($a_question_id, "integer"));
	}
	
	public function getCopyIds($a_group_by_survey = false)
	{
		global $ilDB;
		
		$set = $ilDB->query("SELECT q.question_id,s.obj_fi".
			" FROM svy_question q".
			" JOIN svy_svy_qst sq ON (sq.question_fi = q.question_id)".
			" JOIN svy_svy s ON (s.survey_id = sq.survey_fi)".
			" WHERE original_id = ".$ilDB->quote($this->getId(), "integer"));
		$res = array();
		while($row = $ilDB->fetchAssoc($set))
		{
			if(!$a_group_by_survey)
			{
				$res[] = $row["question_id"];
			}
			else
			{
				$res[$row["obj_fi"]][] = $row["question_id"];
			}
		}
		return $res;
	}
	
	public function hasCopies()
	{
		return (bool)sizeof($this->getCopyIds());						
	}
}
?>