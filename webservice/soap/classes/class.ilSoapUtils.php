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
   * Soap utitliy functions
   *
   * @author Stefan Meyer <meyer@leifos.com>
   * @version $Id: class.ilSoapUtils.php 41608 2013-04-22 12:01:42Z jluetzen $
   *
   * @package ilias
   */
include_once './webservice/soap/classes/class.ilSoapAdministration.php';

class ilSoapUtils extends ilSoapAdministration
{
	function ilSoapUtils()
	{
		parent::ilSoapAdministration();
	}

	function ignoreUserAbort()
	{
		return ignore_user_abort(true);
	}

	function disableSOAPCheck()
	{
		$this->soap_check = false;		
	}
	
	function sendMail($sid,$to,$cc,$bcc,$sender,$subject,$message,$attach)
	{
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}			


		global $ilLog;

		include_once 'Services/Mail/classes/class.ilMimeMail.php';

		$mmail = new ilMimeMail();
		$mmail->autoCheck(false);
		$mmail->From($sender);
		$mmail->To(explode(',',$to));
		$mmail->Subject($subject);
		$mmail->Body($message);

		if($cc)
		{
			$mmail->Cc(explode(',',$cc));
		}

		if($bcc)
		{
			$mmail->Bcc(explode(',',$bcc));
		}
		if($attach)
		{
			// mjansen: switched separator from "," to "#:#" because of mantis bug #6039
			// for backward compatibility we have to check if the substring "#:#" exists as leading separator
			// otherwise we should use ";" 
			if(strpos($attach, '#:#') === 0)
			{
				$attach = substr($attach, strlen('#:#'));
				$attachments = explode('#:#', $attach);	
			}
			else
			{
				$attachments = explode(',', $attach);	
			}
			foreach ($attachments as $attachment)
			{
				$mmail->Attach($attachment);
			}
		}

		$mmail->Send();
		$ilLog->write('SOAP: sendMail(): '.$to.', '.$cc.', '.$bcc);

		return true;
	}
	
	/**
	 * mail via soap
	 * @param object $sid
	 * @param object $a_mail_xml
	 * @return 
	 */
	public function distributeMails($sid, $a_mail_xml)
	{
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}
		
		include_once 'Services/Mail/classes/class.ilMail.php';
		include_once 'webservice/soap/classes/class.ilSoapMailXmlParser.php';
		
		$parser = new ilSoapMailXmlParser($a_mail_xml);
		try
		{
			// Check if wellformed
			libxml_use_internal_errors(true);
			$ok = simplexml_load_string($a_mail_xml);
			if(!$ok)
			{
				foreach(libxml_get_errors() as $err)
				{
					$error .= ($err->message.' ');
				}
				return $this->__raiseError($error, 'CLIENT');
			}
			$parser->start();		
		}
		catch(InvalidArgumentException $e)
		{
			$GLOBALS['ilLog']->write(__METHOD__.' '.$e->getMessage());
			return $this->__raiseError($e->getMessage(),'CLIENT');
		}
		catch(ilSaxParserException $e)
		{
			$GLOBALS['ilLog']->write(__METHOD__.' '.$e->getMessage());
			return $this->__raiseError($e->getMessage(), 'CLIENT');
		}
		
		$mails = $parser->getMails();
		
		global $ilUser;
		
		foreach($mails as $mail)
		{
			// Prepare attachments
			include_once './Services/Mail/classes/class.ilFileDataMail.php';
			$file = new ilFileDataMail($ilUser->getId());
			foreach((array) $mail['attachments'] as $attachment)
			{
				// TODO: Error handling
				$file->storeAsAttachment($attachment['name'], $attachment['content']);
				$attachments[] = ilUtil::_sanitizeFilemame($attachment['name']);
			}
			
			$mail_obj = new ilMail($ilUser->getId());
			$mail_obj->setSaveInSentbox(true);
			$mail_obj->saveAttachments((array) $attachments);
			$mail_obj->sendMail(
				implode(',',(array) $mail['to']),
				implode(',',(array) $mail['cc']),
				implode(',',(array) $mail['bcc']),
				$mail['subject'],
				implode("\n",$mail['body']),
				(array) $attachments,
				array($mail['type']),
				(bool) $mail['usePlaceholders']
			);
			
			// Finally unlink attachments
			foreach((array) $attachments as $att)
			{
				$file->unlinkFile($att);
			}
			$mail_obj->savePostData(
				$ilUser->getId(),
				array(),
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				''
			);
		}
		return true;
	}
	
	function saveTempFileAsMediaObject($sid, $name, $tmp_name)
	{
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}			

		include_once "./Services/MediaObjects/classes/class.ilObjMediaObject.php";
		return ilObjMediaObject::_saveTempFileAsMediaObject($name, $tmp_name);
	}
	
	function getMobsOfObject($sid, $a_type, $a_id)
	{
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}			

		include_once "./Services/MediaObjects/classes/class.ilObjMediaObject.php";
		return ilObjMediaObject::_getMobsOfObject($a_type, $a_id);
	}
	
	/**
	 * clone object dependencies (e.g. course start objects, preconditions ...)
	 *
	 * @access public
	 * @param
	 * 
	 */
	public function ilCloneDependencies($sid,$copy_identifier)
	{
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}			

		global $ilLog,$ilUser;

		include_once('Services/CopyWizard/classes/class.ilCopyWizardOptions.php');
		$cp_options = ilCopyWizardOptions::_getInstance($copy_identifier);
		
		// Check owner of copy procedure
		if(!$cp_options->checkOwner($ilUser->getId()))
		{
			$ilLog->write(__METHOD__.': Permission check failed for user id: '.$ilUser->getId().', copy id: '.$copy_identifier);
			return false;
		}
		
		// Fetch first node
		if(($node = $cp_options->fetchFirstDependenciesNode()) === false)
		{
			$cp_options->deleteAll();
			$ilLog->write(__METHOD__.': Finished copy step 2. Copy completed');
			
			return true;
		}

		// Check options of this node
		$options = $cp_options->getOptions($node['child']);
		$new_ref_id = 0;
		switch($options['type'])
		{
			case ilCopyWizardOptions::COPY_WIZARD_OMIT:
				$ilLog->write(__METHOD__.': Omitting node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$this->callNextDependency($sid,$cp_options);
				break;
			
			case ilCopyWizardOptions::COPY_WIZARD_LINK:
				$ilLog->write(__METHOD__.': Nothing to do for node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$this->callNextDependency($sid,$cp_options);
				break;
				
			case ilCopyWizardOptions::COPY_WIZARD_COPY:
				$ilLog->write(__METHOD__.': Start cloning dependencies: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$this->cloneDependencies($node,$cp_options);
				$this->callNextDependency($sid,$cp_options);
				break;
				
			default:
				$ilLog->write(__METHOD__.': No valid action type given for node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$this->callNextDependency($sid,$cp_options);
				break;
		}
	 	return true;
	}
	
	/**
	 * Clone object
	 *
	 * @access public
	 * @param string soap session id
	 * @param int copy identifier (ilCopyWizarardOptions)
	 * 
	 */
	public function ilClone($sid,$copy_identifier)
	{
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}			

		global $ilLog,$ilUser;
		
		include_once('Services/CopyWizard/classes/class.ilCopyWizardOptions.php');
		$cp_options = ilCopyWizardOptions::_getInstance($copy_identifier);
		
		// Check owner of copy procedure
		if(!$cp_options->checkOwner($ilUser->getId()))
		{
			$ilLog->write(__METHOD__.': Permission check failed for user id: '.$ilUser->getId().', copy id: '.$copy_identifier);
			return false;
		}
		
		
		// Fetch first node
		if(($node = $cp_options->fetchFirstNode()) === false)
		{
			$ilLog->write(__METHOD__.': Finished copy step 1. Starting copying of object dependencies...');
			return $this->ilCloneDependencies($sid,$copy_identifier);
		}
	
		// Check options of this node
		$options = $cp_options->getOptions($node['child']);
		
		$new_ref_id = 0;
		switch($options['type'])
		{
			case ilCopyWizardOptions::COPY_WIZARD_OMIT:
				$ilLog->write(__METHOD__.': Omitting node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				// set mapping to zero
				$cp_options->appendMapping($node['child'],0);
				$this->callNextNode($sid,$cp_options);
				break;
				
			case ilCopyWizardOptions::COPY_WIZARD_COPY:
				$ilLog->write(__METHOD__.': Start cloning node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$new_ref_id = $this->cloneNode($node,$cp_options);
				$this->callNextNode($sid,$cp_options);
				break;
			
			case ilCopyWizardOptions::COPY_WIZARD_LINK:
				$ilLog->write(__METHOD__.': Start linking node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$new_ref_id = $this->linkNode($node,$cp_options);
				$this->callNextNode($sid,$cp_options);
				break;

			default:
				$ilLog->write(__METHOD__.': No valid action type given for node: '.$node['obj_id'].', '.$node['title'].', '.$node['type']);
				$this->callNextNode($sid,$cp_options);
				break;
				
		}
	 	return $new_ref_id;
	}
	
	/**
	 * Call next node using soap
	 * @param object copx wizard options instance
	 * @access private
	 * 
	 */
	private function callNextNode($sid,$cp_options)
	{
		global $ilLog;

		$cp_options->dropFirstNode();

		if($cp_options->isSOAPEnabled())
		{
		 	// Start next soap call
		 	include_once 'Services/WebServices/SOAP/classes/class.ilSoapClient.php';
			$soap_client = new ilSoapClient();
			$soap_client->setResponseTimeout(1);
			$soap_client->enableWSDL(true);
			$soap_client->init();
			$soap_client->call('ilClone',array($sid,$cp_options->getCopyId()));
		}
		else
		{
			$ilLog->write(__METHOD__.': Cannot call SOAP server');
			$cp_options->read();			
			include_once('./webservice/soap/include/inc.soap_functions.php');
			$res = ilSoapFunctions::ilClone($sid,$cp_options->getCopyId());
		}
		return true;
	}
	
	private function callNextDependency($sid,$cp_options)
	{
		global $ilLog;

		$cp_options->dropFirstDependenciesNode();
		
		if($cp_options->isSOAPEnabled())
		{
		 	// Start next soap call
		 	include_once 'Services/WebServices/SOAP/classes/class.ilSoapClient.php';
			$soap_client = new ilSoapClient();
			$soap_client->setResponseTimeout(1);
			$soap_client->enableWSDL(true);
			$soap_client->init();
			$soap_client->call('ilCloneDependencies',array($sid,$cp_options->getCopyId()));
		}
		else
		{
			$ilLog->write(__METHOD__.': Cannot call SOAP server');
			$cp_options->read();
			include_once('./webservice/soap/include/inc.soap_functions.php');
			$res = ilSoapFunctions::ilCloneDependencies($sid,$cp_options->getCopyId());
		}
		return true;
	}
	
	/**
	 * Clone node
	 *
	 * @access private
	 * @param
	 * 
	 */
	private function cloneNode($node,$cp_options)
	{
		global $ilLog,$tree,$ilAccess,$rbacreview;
		
		#sleep(20);
		
		$source_id = $node['child'];
		$parent_id = $node['parent'];
		$options = $cp_options->getOptions($node['child']);
		$mappings = $cp_options->getMappings();
		
		if(!$ilAccess->checkAccess('copy','',$node['child']))
		{
			$ilLog->write(__METHOD__.': No copy permission granted: '.$source_id.', '.$node['title'].', '.$node['type']);
			return false;
			
		}
		if(!isset($mappings[$parent_id]))
		{
			$ilLog->write(__METHOD__.': Omitting node '.$source_id.', '.$node['title'].', '.$node['type']. '. No target found.');
			return true;
		}
		$target_id = $mappings[$parent_id];
		
		if(!$tree->isInTree($target_id))
		{
			$ilLog->write(__METHOD__.': Omitting node '.$source_id.', '.$node['title'].', '.$node['type']. '. Object has been deleted.');
			return false;
		}

		$orig = ilObjectFactory::getInstanceByRefId((int) $source_id);
		$new_obj = $orig->cloneObject((int) $target_id,$cp_options->getCopyId());
		
		if(!is_object($new_obj))
		{
			$ilLog->write(__METHOD__.': Error copying '.$source_id.', '.$node['title'].', '.$node['type'].'. No target found.');
			return false;
		}

		// rbac log
		include_once "Services/AccessControl/classes/class.ilRbacLog.php";
		$rbac_log_roles = $rbacreview->getParentRoleIds($new_obj->getRefId(), false);
		$rbac_log = ilRbacLog::gatherFaPa($new_obj->getRefId(), array_keys($rbac_log_roles), true);
		ilRbacLog::add(ilRbacLog::COPY_OBJECT, $new_obj->getRefId(), $rbac_log, (int)$source_id);
		
		// Finally add new mapping entry
		$cp_options->appendMapping($source_id,$new_obj->getRefId());
		return $new_obj->getRefId();
	}
	
	/**
	 * cloneDependencies
	 *
	 * @access private
	 * @param 
	 * 
	 */
	private function cloneDependencies($node,$cp_options)
	{
		global $ilLog;
		
		$source_id = $node['child'];
		$mappings = $cp_options->getMappings();
		
		if(!isset($mappings[$source_id]))
		{
			$ilLog->write(__METHOD__.': Omitting node '.$source_id.', '.$node['title'].', '.$node['type']. '. No mapping found.');
			return true;
		}
		$target_id = $mappings[$source_id];

		$orig = ilObjectFactory::getInstanceByRefId((int) $source_id);
		$orig->cloneDependencies($target_id,$cp_options->getCopyId());
		return true;
	}
	
	/**
	 * Link node
	 *
	 * @access private
	 * @param
	 * 
	 */
	private function linkNode($node,$cp_options)
	{
		global $ilLog,$ilAccess,$rbacreview;
		
		$source_id = $node['child'];
		$parent_id = $node['parent'];
		$options = $cp_options->getOptions($node['child']);
		$mappings = $cp_options->getMappings();
		
		if(!$ilAccess->checkAccess('delete','',$node['child']))
		{
			$ilLog->write(__METHOD__.': No delete permission granted: '.$source_id.', '.$node['title'].', '.$node['type']);
			return false;
			
		}
		if(!isset($mappings[$parent_id]))
		{
			$ilLog->write(__METHOD__.': Omitting node '.$source_id.', '.$node['title'].', '.$node['type']. '. No target found.');
			return true;
		}
		$target_id = $mappings[$parent_id];

		$orig = ilObjectFactory::getInstanceByRefId((int) $source_id);
		$new_ref_id = $orig->createReference();
		$orig->putInTree($target_id);
		$orig->setPermissions($target_id);
		
		if(!($new_ref_id))
		{
			$ilLog->write(__METHOD__.': Error linking '.$source_id.', '.$node['title'].', '.$node['type'].'. No target found.');
			return false;
		}

		// rbac log
		include_once "Services/AccessControl/classes/class.ilRbacLog.php";
		$rbac_log_roles = $rbacreview->getParentRoleIds($new_ref_id, false);
		$rbac_log = ilRbacLog::gatherFaPa($new_ref_id, array_keys($rbac_log_roles), true);
		ilRbacLog::add(ilRbacLog::LINK_OBJECT, $new_ref_id, $rbac_log, (int)$source_id);
		
		// Finally add new mapping entry
		$cp_options->appendMapping($source_id,$new_ref_id);
		return $new_ref_id;
	}

	/**
	 * validates an xml file, if dtd is attached
	 *
	 * @param $xml	current xml stream
	 * @return true, if correct, or String with error messages
	 */
	static function validateXML ($xml) {
		// validate to prevent wrong XMLs
		$dom = @domxml_open_mem($xml, DOMXML_LOAD_VALIDATING, $error);
   		if ($error)
   		{
		    $msg = array();
		    if (is_array($error))
		    {
	        	foreach ($error as $err) {
					$msg []= "(".$err["line"].",".$err["col"]."): ".$err["errormessage"];
		    	}
		    }
		    else
		    {
		   		$msg[] = $error;
		   	}
		   	$msg = join("\n",$msg);
		   	return $msg;
   		}
   		return true;
	}
	
	public function handleECSTasks($sid,$a_server_id)
	{		
		$this->initAuth($sid);
		$this->initIlias();

		if(!$this->__checkSession($sid))
		{
			return $this->__raiseError($this->__getMessage(),$this->__getMessageCode());
		}			
		
		include_once('./Services/WebServices/ECS/classes/class.ilECSTaskScheduler.php');

		global $ilLog;
		
		$ilLog->write(__METHOD__.': Starting task execution...');
		$scheduler = ilECSTaskScheduler::_getInstanceByServerId($a_server_id);
		$scheduler->startTaskExecution();
		
		return true;
	}

	/**
	 * 
	 * Method for soap webservice: deleteExpiredDualOptInUserObjects
	 * 
	 * This service will run in background. The client has not to wait for response.
	 * 
	 * @param	string	$sid	Session id + client id, separated by ::
	 * @param	integer	$usr_id	User id of the actuator
	 * @return	boolean	true or false
	 * @access	public
	 * 
	 */
	public function deleteExpiredDualOptInUserObjects($sid, $usr_id)
	{
		$this->initAuth($sid);
		$this->initIlias();
		
		// Session check not possible -> anonymous user is the trigger
		
		global $ilDB, $ilLog;
		
		$ilLog->write(__METHOD__.': Started deletion of inactive user objects with expired confirmation hash values (dual opt in) ...');

		require_once 'Services/Registration/classes/class.ilRegistrationSettings.php';
		$oRegSettigs = new ilRegistrationSettings();
		
		$query = '';

		/* 
		 * Fetch the current actuator user object first, because this user will try to perform very probably
		 * a new registration with the same login name in a few seconds ;-)
		 * 
		 */ 
		if((int)$usr_id > 0)
		{
			$query .= 'SELECT usr_id, create_date, reg_hash FROM usr_data '
				    . 'WHERE active = 0 '
				    . 'AND reg_hash IS NOT NULL '
				    . 'AND usr_id = '.$ilDB->quote($usr_id, 'integer').' ';
			$query .= 'UNION ';
		}
				
		$query .= 'SELECT usr_id, create_date, reg_hash FROM usr_data '
			    . 'WHERE active = 0 '
			    . 'AND reg_hash IS NOT NULL '
			    . 'AND usr_id != '.$ilDB->quote($usr_id, 'integer').' ';			    

		$res = $ilDB->query($query);
		
		$ilLog->write(__METHOD__.': '.$ilDB->numRows($res).' inactive user objects with confirmation hash values (dual opt in) found ...');

		/* 
		 * mjansen: 15.12.2010:
		 * I perform the expiration check in php because of multi database support (mysql, postgresql).
		 * I did not find an oracle equivalent for mysql: UNIX_TIMESTAMP()  
		 */
		
		$num_deleted_users = 0;
		while($row = $ilDB->fetchAssoc($res))
		{
			if($row['usr_id'] == ANONYMOUS_USER_ID || $row['usr_id'] == SYSTEM_USER_ID) continue;			
			if(!strlen($row['reg_hash'])) continue;
						
			if((int)$oRegSettigs->getRegistrationHashLifetime() > 0 &&
			   $row['create_date'] != '' &&
			   time() - $oRegSettigs->getRegistrationHashLifetime() > strtotime($row['create_date']))
			{
				$user = ilObjectFactory::getInstanceByObjId($row['usr_id'], false);
				if($user instanceof ilObjUser)
				{
					$ilLog->write(__METHOD__.': User '.$user->getLogin().' (obj_id: '.$user->getId().') will be deleted due to an expired registration hash ...');
					$user->delete();
					++$num_deleted_users;
				}
			}	
		}		
		
		$ilLog->write(__METHOD__.': '.$num_deleted_users.' inactive user objects with expired confirmation hash values (dual opt in) deleted ...');
		
		$ilLog->write(__METHOD__.': Finished deletion of inactive user objects with expired confirmation hash values (dual opt in) ...');
		
		return true;
	}
}
?>