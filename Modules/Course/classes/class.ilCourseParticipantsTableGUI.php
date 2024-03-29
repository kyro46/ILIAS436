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
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 * @version $Id$
 *
 * @ingroup ModulesCourse
 */
class ilCourseParticipantsTableGUI extends ilTable2GUI
{
	protected $type = 'admin';
	protected $show_learning_progress = false;
	protected $show_timings = false;
	protected $show_edit_link = true;
	protected static $export_allowed = false;
	protected static $confirmation_required = true;
	protected static $accepted_ids = null;
	protected static $all_columns = null;

	protected $role_id = 0;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param
	 * @return
	 */
	public function __construct(
		$a_parent_obj,
		$a_type = 'admin',
		$show_content = true,
		$a_show_learning_progress = false,
		$a_show_timings = false,
		$a_show_edit_link= true,
		$a_role_id = 0,
		$a_show_lp_status_sync = false)
	{
		global $lng, $ilCtrl;

		$this->show_learning_progress = $a_show_learning_progress;		
		$this->show_timings = $a_show_timings;
		$this->show_edit_link = $a_show_edit_link;
		$this->show_lp_status_sync = $a_show_lp_status_sync;

		$this->lng = $lng;
		$this->lng->loadLanguageModule('crs');
		$this->lng->loadLanguageModule('trac');
		$this->ctrl = $ilCtrl;

		$this->type = $a_type;
		$this->setRoleId($a_role_id);

		include_once('./Services/PrivacySecurity/classes/class.ilPrivacySettings.php');
		$this->privacy = ilPrivacySettings::_getInstance();

		// required before constructor for columns
		$this->setId('crs_' . $a_type . '_' . $a_role_id.'_'. $a_parent_obj->object->getId());
		parent::__construct($a_parent_obj, 'members');

		$this->initAcceptedAgreements();

		$this->setFormName('participants');

		$this->addColumn('', 'f', "1");
		$this->addColumn($this->lng->txt('name'), 'lastname', '20%');

		$all_cols = $this->getSelectableColumns();
		foreach($this->getSelectedColumns() as $col)
		{
			$this->addColumn($all_cols[$col]['txt'], $col);
		}

		if($this->show_learning_progress)
		{
			$this->addColumn($this->lng->txt('learning_progress'), 'progress');
		}

		if($this->privacy->enabledCourseAccessTimes())
		{
			$this->addColumn($this->lng->txt('last_access'), 'access_ut', '16em');
		}
		
		$this->addColumn($this->lng->txt('crs_member_passed'), 'passed');
		if($this->show_lp_status_sync)
		{
			$this->addColumn($this->lng->txt('crs_member_passed_status_changed'), 'passed_info');
		}
		
		if($this->type == 'admin')
		{
			$this->setSelectAllCheckbox('admins');
			$this->addColumn($this->lng->txt('crs_notification_list_title'), 'notification');
			$this->addCommandButton('updateAdminStatus', $this->lng->txt('save'));
		}
		elseif($this->type == 'tutor')
		{
			$this->setSelectAllCheckbox('tutors');
			$this->addColumn($this->lng->txt('crs_notification_list_title'), 'notification');
			$this->addCommandButton('updateTutorStatus', $this->lng->txt('save'));
		}
		elseif($this->type == 'member')
		{
			$this->setSelectAllCheckbox('members');
			$this->addColumn($this->lng->txt('crs_blocked'), 'blocked');
			$this->addCommandButton('updateMemberStatus', $this->lng->txt('save'));
		}
		else
		{
			$this->setSelectAllCheckbox('roles');
			$this->addColumn($this->lng->txt('crs_blocked'), 'blocked');
			$this->addCommandButton('updateRoleStatus', $this->lng->txt('save'));

		}
		$this->addColumn($this->lng->txt(''), 'optional');

		$this->setRowTemplate("tpl.show_participants_row.html", "Modules/Course");

		if($show_content)
		{
			$this->setDefaultOrderField('lastname');
			$this->enable('sort');
			$this->enable('header');
			$this->enable('numinfo');
			$this->enable('select_all');
		}
		else
		{
			$this->disable('content');
			$this->disable('header');
			$this->disable('footer');
			$this->disable('numinfo');
			$this->disable('select_all');
		}

		$this->setEnableNumInfo(true);
		$this->setExternalSegmentation(true);
		
		$this->getItems();
		$this->setTopCommands(true);
		$this->setEnableHeader(true);
		$this->setEnableTitle(true);
		$this->initFilter();
			
		include_once "Services/Certificate/classes/class.ilCertificate.php";
		$this->enable_certificates = ilCertificate::isActive();		
		if($this->enable_certificates)
		{
			$this->enable_certificates = ilCertificate::isObjectActive($a_parent_obj->object->getId());
		}
		if($this->enable_certificates)
		{
			$lng->loadLanguageModule('certificate');
		}
	}

	/**
	 * Set current role id
	 */
	public function setRoleId($a_role_id)
	{
		$this->role_id = $a_role_id;
	}

	/**
	 * Get current role id
	 * @return int
	 */
	public function getRoleId()
	{
		return $this->role_id;
	}

	public function getItems()
	{
		
	}

	/**
	 * Get selectable columns
	 * @return 
	 */
	public function getSelectableColumns()
	{
		if(self::$all_columns)
		{
			return self::$all_columns;
		}

		include_once './Services/PrivacySecurity/classes/class.ilExportFieldsInfo.php';
		$ef = ilExportFieldsInfo::_getInstanceByType($this->getParentObject()->object->getType());
		self::$all_columns = $ef->getSelectableFieldsInfo($this->getParentObject()->object->getId());
		return self::$all_columns;
	}

	/**
	 * fill row 
	 *
	 * @access public
	 * @param
	 * @return
	 */
	public function fillRow($a_set)
	{
		global $ilAccess;

		$this->tpl->setVariable('VAL_ID', $a_set['usr_id']);
		$this->tpl->setVariable('VAL_NAME', $a_set['lastname'] . ', ' . $a_set['firstname']);

		if(!$ilAccess->checkAccessOfUser($a_set['usr_id'], 'read', '', $this->getParentObject()->object->getRefId()) and
			is_array($info = $ilAccess->getInfo()))
		{
			$this->tpl->setCurrentBlock('access_warning');
			$this->tpl->setVariable('PARENT_ACCESS', $info[0]['text']);
			$this->tpl->parseCurrentBlock();
		}

		if(!ilObjUser::_lookupActive($a_set['usr_id']))
		{
			$this->tpl->setCurrentBlock('access_warning');
			$this->tpl->setVariable('PARENT_ACCESS', $this->lng->txt('usr_account_inactive'));
			$this->tpl->parseCurrentBlock();
		}


		foreach($this->getSelectedColumns() as $field)
		{
			switch($field)
			{
				case 'gender':
					$a_set['gender'] = $a_set['gender'] ? $this->lng->txt('gender_' . $a_set['gender']) : '';
					$this->tpl->setCurrentBlock('custom_fields');
					$this->tpl->setVariable('VAL_CUST', $a_set[$field]);
					$this->tpl->parseCurrentBlock();
					break;

				case 'birthday':
					$a_set['birthday'] = $a_set['birthday'] ? ilDatePresentation::formatDate(new ilDate($a_set['birthday'], IL_CAL_DATE)) : $this->lng->txt('no_date');
					$this->tpl->setCurrentBlock('custom_fields');
					$this->tpl->setVariable('VAL_CUST', $a_set[$field]);
					$this->tpl->parseCurrentBlock();
					break;

				default:
					$this->tpl->setCurrentBlock('custom_fields');
					$this->tpl->setVariable('VAL_CUST', isset($a_set[$field]) ? (string) $a_set[$field] : '');
					$this->tpl->parseCurrentBlock();
					break;
			}
		}

		if($this->privacy->enabledCourseAccessTimes())
		{
			$this->tpl->setVariable('VAL_ACCESS', $a_set['access_time']);
		}
		if($this->show_learning_progress)
		{
			$this->tpl->setCurrentBlock('lp');
			switch($a_set['progress'])
			{
				case LP_STATUS_COMPLETED:
					$this->tpl->setVariable('LP_STATUS_ALT', $this->lng->txt($a_set['progress']));
					$this->tpl->setVariable('LP_STATUS_PATH', ilUtil::getImagePath('scorm/complete.png'));
					break;

				case LP_STATUS_IN_PROGRESS:
					$this->tpl->setVariable('LP_STATUS_ALT', $this->lng->txt($a_set['progress']));
					$this->tpl->setVariable('LP_STATUS_PATH', ilUtil::getImagePath('scorm/incomplete.png'));
					break;

				case LP_STATUS_NOT_ATTEMPTED:
					$this->tpl->setVariable('LP_STATUS_ALT', $this->lng->txt($a_set['progress']));
					$this->tpl->setVariable('LP_STATUS_PATH', ilUtil::getImagePath('scorm/not_attempted.png'));
					break;

				case LP_STATUS_FAILED:
					$this->tpl->setVariable('LP_STATUS_ALT', $this->lng->txt($a_set['progress']));
					$this->tpl->setVariable('LP_STATUS_PATH', ilUtil::getImagePath('scorm/failed.png'));
					break;
			}
			$this->tpl->parseCurrentBlock();
		}
		if($this->type == 'admin')
		{
			$this->tpl->setVariable('VAL_POSTNAME', 'admins');
			$this->tpl->setVariable('VAL_NOTIFICATION_ID', $a_set['usr_id']);
			$this->tpl->setVariable('VAL_NOTIFICATION_CHECKED', ($a_set['notification'] ? 'checked="checked"' : ''));
		}
		elseif($this->type == 'tutor')
		{
			$this->tpl->setVariable('VAL_POSTNAME', 'tutors');
			$this->tpl->setVariable('VAL_NOTIFICATION_ID', $a_set['usr_id']);
			$this->tpl->setVariable('VAL_NOTIFICATION_CHECKED', ($a_set['notification'] ? 'checked="checked"' : ''));
		}
		elseif($this->type == 'member')
		{
			$this->tpl->setCurrentBlock('blocked');
			$this->tpl->setVariable('VAL_POSTNAME','members');
			$this->tpl->setVariable('VAL_BLOCKED_ID',$a_set['usr_id']);
			$this->tpl->setVariable('VAL_BLOCKED_CHECKED',($a_set['blocked'] ? 'checked="checked"' : ''));
			$this->tpl->parseCurrentBlock();
		}
		else
		{
			$this->tpl->setCurrentBlock('blocked');
			$this->tpl->setVariable('VAL_BLOCKED_ID', $a_set['usr_id']);
			$this->tpl->setVariable('VAL_BLOCKED_CHECKED', ($a_set['blocked'] ? 'checked="checked"' : ''));
			$this->tpl->parseCurrentBlock();

			$this->tpl->setVariable('VAL_POSTNAME','roles');
		}
		
		$this->tpl->setVariable('VAL_PASSED_ID',$a_set['usr_id']);
		$this->tpl->setVariable('VAL_PASSED_CHECKED',($a_set['passed'] ? 'checked="checked"' : ''));
				
		if($this->show_lp_status_sync)
		{			
			$this->tpl->setVariable('PASSED_INFO', $a_set["passed_info"]);
		}		
		
		$this->ctrl->setParameter($this->parent_obj, 'member_id', $a_set['usr_id']);
		if($this->show_edit_link)
		{
			$this->tpl->setCurrentBlock('link');
			$this->tpl->setVariable('LINK_NAME', $this->ctrl->getLinkTarget($this->parent_obj, 'editMember'));
			$this->tpl->setVariable('LINK_TXT', $this->lng->txt('edit'));
			$this->tpl->parseCurrentBlock();
		}		
		if($a_set['passed'] && $this->enable_certificates)
		{
			$this->tpl->setCurrentBlock('link');
			$this->tpl->setVariable('LINK_NAME', $this->ctrl->getLinkTarget($this->parent_obj, 'deliverCertificate'));
			$this->tpl->setVariable('LINK_TXT', $this->lng->txt('download_certificate'));
			$this->tpl->parseCurrentBlock();
		}		
		$this->ctrl->clearParameters($this->parent_obj);

		if($this->show_timings)
		{
			$this->ctrl->setParameterByClass('ilcoursecontentgui', 'member_id', $a_set['usr_id']);
			$this->tpl->setCurrentBlock('link');
			$this->tpl->setVariable('LINK_NAME', $this->ctrl->getLinkTargetByClass('ilcoursecontentgui', 'showUserTimings'));
			$this->tpl->setVariable('LINK_TXT', $this->lng->txt('timings_timings'));
			$this->tpl->parseCurrentBlock();
		}
	}

	/**
	 * Parse data
	 * @return
	 *
	 * @global ilRbacReview $rbacreview
	 */
	public function parse()
	{
		global $rbacreview;

		$this->determineOffsetAndOrder();

		include_once './Services/User/classes/class.ilUserQuery.php';

		$additional_fields = $this->getSelectedColumns();
		unset($additional_fields["firstname"]);
		unset($additional_fields["lastname"]);
		unset($additional_fields["last_login"]);
		unset($additional_fields["access_until"]);

		$udf_ids = $usr_data_fields = $odf_ids = array();
		foreach($additional_fields as $field)
		{
			if(substr($field, 0, 3) == 'udf')
			{
				$udf_ids[] = substr($field, 4);
				continue;
			}
			if(substr($field, 0, 3) == 'odf')
			{
				$odf_ids[] = substr($field, 4);
				continue;
			}

			$usr_data_fields[] = $field;
		}

		$usr_data = ilUserQuery::getUserListData(
			$this->getOrderField(),
			$this->getOrderDirection(),
			$this->getOffset(),
			$this->getLimit(),
			'',
			'',
			null,
			false,
			false,
			0,
			$this->getRoleId(),
			null,
			$usr_data_fields,
			$part
		);
		foreach((array) $usr_data['set'] as $user)
		{
			$usr_ids[] = $user['usr_id'];
		}

		// merge course data
		$course_user_data = $this->getParentObject()->readMemberData($usr_ids,$this->type == 'admin');
		$a_user_data = array();
		foreach((array) $usr_data['set'] as $ud)
		{			
			$a_user_data[$ud['usr_id']] = array_merge($ud,$course_user_data[$ud['usr_id']]);
			
			if($this->show_lp_status_sync)
			{								
				// #9912
				$passed_info = "";
				if($a_user_data[$ud['usr_id']]["passed_info"]["user_id"])
				{
					$pinfo = $a_user_data[$ud['usr_id']]["passed_info"];								
					if($pinfo["user_id"] < 0)
					{
						$passed_info = $this->lng->txt("crs_passed_status_system");
					}
					else if($pinfo["user_id"] > 0)
					{
						$name = ilObjUser::_lookupName($pinfo["user_id"]);
						$passed_info = $this->lng->txt("crs_passed_status_manual_by").": ".$name["login"];									
					}
				}
				if($pinfo["timestamp"])
				{
					$passed_info .= "<br />".ilDatePresentation::formatDate($pinfo["timestamp"]);	
				}
				$a_user_data[$ud['usr_id']]["passed_info"] = $passed_info;
			}
		}

		// Custom user data fields
		if($udf_ids)
		{
			include_once './Services/User/classes/class.ilUserDefinedData.php';
			$data = ilUserDefinedData::lookupData($usr_ids, $udf_ids);
			foreach($data as $usr_id => $fields)
			{
				if(!$this->checkAcceptance($usr_id))
				{
					continue;
				}

				foreach($fields as $field_id => $value)
				{
					$a_user_data[$usr_id]['udf_' . $field_id] = $value;
				}
			}
		}
		// Object specific user data fields
		if($odf_ids)
		{
			include_once './Modules/Course/classes/Export/class.ilCourseUserData.php';
			$data = ilCourseUserData::_getValuesByObjId($this->getParentObject()->object->getId());
			foreach($data as $usr_id => $fields)
			{
				// #7264: as we get data for all course members filter against user data
				if(!$this->checkAcceptance($usr_id) || !in_array($usr_id, $usr_ids))
				{
					continue;
				}

				foreach($fields as $field_id => $value)
				{
					$a_user_data[$usr_id]['odf_' . $field_id] = $value;
				}
			}
		}

		foreach($usr_data['set'] as $user)
		{
			// Check acceptance
			if(!$this->checkAcceptance($user['usr_id']))
			{
				continue;
			}
			// DONE: accepted
			foreach($usr_data_fields as $field)
			{
				$a_user_data[$user['usr_id']][$field] = $user[$field] ? $user[$field] : '';
			}
		}
		$this->setMaxCount($usr_data['cnt'] ? $usr_data['cnt'] : 0);
		return $this->setData($a_user_data);
	}

	public function checkAcceptance($a_usr_id)
	{
		if(!self::$confirmation_required)
		{
			return true;
		}
		if(!self::$export_allowed)
		{
			return false;
		}
		return in_array($a_usr_id, self::$accepted_ids);
	}

	/**
	 * Init acceptance
	 * @return 
	 */
	public function initAcceptedAgreements()
	{
		if(self::$accepted_ids !== NULL)
		{
			return true;
		}

		self::$export_allowed = ilPrivacySettings::_getInstance()->checkExportAccess($this->getParentObject()->object->getRefId());
		self::$confirmation_required = ilPrivacySettings::_getInstance()->courseConfirmationRequired();

		include_once 'Services/Membership/classes/class.ilMemberAgreement.php';
		self::$accepted_ids = ilMemberAgreement::lookupAcceptedAgreements($this->getParentObject()->object->getId());
	}
	
}
?>
