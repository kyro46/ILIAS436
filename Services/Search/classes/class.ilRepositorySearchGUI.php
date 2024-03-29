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
* Class ilRepositorySearchGUI
*
* GUI class for user, group, role search
*
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id: class.ilRepositorySearchGUI.php 42847 2013-06-20 09:51:35Z smeyer $
* 
* @package ilias-search
*
*/
include_once 'Services/Search/classes/class.ilSearchResult.php';
include_once 'Services/Search/classes/class.ilSearchSettings.php';
include_once './Services/User/classes/class.ilUserAccountSettings.php';
include_once 'Services/Search/classes/class.ilQueryParser.php';


class ilRepositorySearchGUI
{
	private $search_results = array();
	
	protected $add_options = array();
	protected $object_selection = false;

	protected $searchable_check = true;
	protected $search_title = '';
	
	var $search_type = 'usr';

	/**
	* Constructor
	* @access public
	*/
	function ilRepositorySearchGUI()
	{
		global $ilCtrl,$tpl,$lng;

		$this->ctrl =& $ilCtrl;
		$this->tpl =& $tpl;
		$this->lng =& $lng;
		$this->lng->loadLanguageModule('search');
		$this->lng->loadLanguageModule('crs');

		$this->setTitle($this->lng->txt('add_members_header'));

		$this->__setSearchType();
		$this->__loadQueries();

		$this->result_obj = new ilSearchResult();
		$this->result_obj->setMaxHits(1000000);
		$this->settings =& new ilSearchSettings();

	}

	/**
	 * Set form title
	 * @param string $a_title
	 */
	public function setTitle($a_title)
	{
		$this->search_title = $a_title;
	}

	/**
	 * Get search form title
	 * @return string
	 */
	public function getTitle()
	{
		return $this->search_title;
	}

	/**
	 * En/disable the validation of the searchable flag
	 * @param bool $a_status
	 */
	public function enableSearchableCheck($a_status)
	{
		$this->searchable_check = $a_status;
	}

	/**
	 *
	 * @return bool
	 */
	public function isSearchableCheckEnabled()
	{
		return $this->searchable_check;
	}


	/**
	 * fill toolbar with
	 * @param ilToolbarGUI $toolbar
	 * @param array options:  all are optional e.g.
	 * array(
	 *		auto_complete_name = $lng->txt('user'),
	 *		auto_complete_size = 15,
	 *		user_type = array(ilCourseParticipants::CRS_MEMBER,ilCourseParticpants::CRS_TUTOR),
	 *		submit_name = $lng->txt('add')
	 * )
	 *
	 * @return ilToolbarGUI
	 */
	public static function fillAutoCompleteToolbar($parent_object, ilToolbarGUI $toolbar = null, $a_options = array())
	{
		global $ilToolbar, $lng, $ilCtrl;

		if(!$toolbar instanceof ilToolbarGUI)
		{
			$toolbar = $ilToolbar;
		}

		// Fill default options
		if(!isset($a_options['auto_complete_name']))
		{
			$a_options['auto_complete_name'] = $lng->txt('obj_user');
		}
		if(!isset($a_options['auto_complete_size']))
		{
			$a_options['auto_complete_size'] = 15;
		}
		if(!isset($a_options['submit_name']))
		{
			$a_options['submit_name'] = $lng->txt('btn_add');
		}
		
		$ajax_url = $ilCtrl->getLinkTargetByClass(array(get_class($parent_object),'ilRepositorySearchGUI'), 
			'doUserAutoComplete', '', true);

		include_once("./Services/Form/classes/class.ilTextInputGUI.php");
		$ul = new ilTextInputGUI($a_options['auto_complete_name'], 'user_login');
		$ul->setDataSource($ajax_url);		
		$ul->setSize($a_options['auto_complete_size']);
		$toolbar->addInputItem($ul, true);

		if(count((array) $a_options['user_type']))
		{
			include_once './Services/Form/classes/class.ilSelectInputGUI.php';
			$si = new ilSelectInputGUI("", "user_type");
			$si->setOptions($a_options['user_type']);
			$toolbar->addInputItem($si);
		}

		$toolbar->addFormButton(
			$a_options['submit_name'],
			'addUserFromAutoComplete'
		);
		
		$toolbar->setFormAction(
			$ilCtrl->getFormActionByClass(
				array(
					get_class($parent_object),
					'ilRepositorySearchGUI')
			)
		);
		return $toolbar;
	}

	/**
	 * Do auto completion
	 * @return void
	 */
	protected function doUserAutoComplete()
	{

		if(!isset($_GET['autoCompleteField']))
		{
			$a_fields = array('login','firstname','lastname','email');
			$result_field = 'login';
		}
		else
		{
			$a_fields = array((string) $_GET['autoCompleteField']);
			$result_field = (string) $_GET['autoCompleteField'];
		}

		$GLOBALS['ilLog']->write(print_r($a_fields,true));
		include_once './Services/User/classes/class.ilUserAutoComplete.php';
		$auto = new ilUserAutoComplete();
		$auto->setSearchFields($a_fields);
		$auto->setResultField($result_field);
		$auto->enableFieldSearchableCheck(true);
		echo $auto->getList($_REQUEST['term']);
		exit();
	}


	/**
	* Set/get search string
	* @access public
	*/
	function setString($a_str)
	{
		$_SESSION['search']['string'] = $this->string = $a_str;
	}
	function getString()
	{
		return $this->string;
	}
		
	/**
	* Control
	* @access public
	*/
	function &executeCommand()
	{
		global $rbacsystem;

		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		$this->ctrl->setReturn($this,'');

		switch($next_class)
		{
			default:
				if(!$cmd)
				{
					$cmd = "showSearch";
				}
				$this->$cmd();
				break;
		}
		return true;
	}

	function __clearSession()
	{
		
		unset($_SESSION['rep_search']);
		unset($_SESSION['append_results']);
		unset($_SESSION['rep_query']);
		unset($_SESSION['rep_search_type']);
	}

	function cancel()
	{
		$this->ctrl->returnToParent($this);
	}

	function start()
	{
		// delete all session info
		$this->__clearSession();
		$this->showSearch();

		return true;
	}


	public function addUser()
	{
		$class = $this->callback['class'];
		$method = $this->callback['method'];

		// call callback if that function does give a return value => show error message
		// listener redirects if everything is ok.
		$class->$method($_POST['user']);

		$this->showSearchResults();
	}

	/**
	 * Add user from auto complete input
	 */
	protected function addUserFromAutoComplete()
	{
		$class = $this->callback['class'];
		$method = $this->callback['method'];

		$users = explode(',', $_POST['user_login']);
		$user_ids = array();
		foreach($users as $user)
		{
			$user_id = ilObjUser::_lookupId($user);
			if($user_id)
			{
				$user_ids[] = $user_id;
			}
		}

		$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 0;

		if(!$class->$method($user_ids,$user_type))
		{
			$GLOBALS['ilCtrl']->returnToParent($this);
		}
	}

	/**
	 * Handle multi command
	 */
	protected function handleMultiCommand()
	{
		$class = $this->callback['class'];
		$method = $this->callback['method'];

		// Redirects if everything is ok
		if(!$class->$method((array) $_POST['user'],$_POST['selectedCommand']))
		{
			$this->showSearchResults();
		}
	}

	public function setCallback(&$class,$method,$a_add_options = array())
	{
		$this->callback = array('class' => $class,'method' => $method);
		$this->add_options = $a_add_options ? $a_add_options : array();
	}
	
	public function showSearch()
	{
		$this->initFormSearch();
		$this->tpl->setContent($this->form->getHTML());
	}
	
	public function initFormSearch()
	{
		global $ilCtrl;

		include_once './Services/Form/classes/class.ilPropertyFormGUI.php';
		
		$this->form =  new ilPropertyFormGUI();
		$this->form->setFormAction($this->ctrl->getFormAction($this,'search'));
		$this->form->setTitle($this->getTitle());
		$this->form->addCommandButton('performSearch', $this->lng->txt('search'));
		$this->form->addCommandButton('cancel', $this->lng->txt('cancel'));
		
		
		$kind = new ilRadioGroupInputGUI($this->lng->txt('search_type'),'search_for');
		$kind->setValue($this->search_type);
		$this->form->addItem($kind);
		
		// Users
		$users = new ilRadioOption($this->lng->txt('search_for_users'),'usr');
			
		// UDF
		include_once 'Services/Search/classes/class.ilUserSearchOptions.php';
		foreach(ilUserSearchOptions::_getSearchableFieldsInfo(!$this->isSearchableCheckEnabled()) as $info)
		{
			switch($info['type'])
			{
				case FIELD_TYPE_UDF_SELECT:
				case FIELD_TYPE_SELECT:
						
					$sel = new ilSelectInputGUI($info['lang'],"rep_query[usr][".$info['db']."]");
					$sel->setOptions($info['values']);
					$users->addSubItem($sel);
					break;
	
				case FIELD_TYPE_UDF_TEXT:
				case FIELD_TYPE_TEXT:

					if(isset($info['autoComplete']) and $info['autoComplete'])
					{						
						$ilCtrl->setParameterByClass(get_class($this),'autoCompleteField',$info['db']);
						$ul = new ilTextInputGUI($info['lang'],	"rep_query[usr][".$info['db']."]");
						$ul->setDataSource($ilCtrl->getLinkTarget($this,
							"doUserAutoComplete", "", true));					
						$ul->setSize(30);
						$ul->setMaxLength(120);
						$users->addSubItem($ul);
					}
					else
					{
						$txt = new ilTextInputGUI($info['lang'],"rep_query[usr][".$info['db']."]");
						$txt->setSize(30);
						$txt->setMaxLength(120);
						$users->addSubItem($txt);
					}
					break;
			}
		}
		$kind->addOption($users);



		// Role
		$roles = new ilRadioOption($this->lng->txt('search_for_role_members'),'role');
		$role = new ilTextInputGUI($this->lng->txt('search_role_title'),'rep_query[role][title]');
		$role->setSize(30);
		$role->setMaxLength(120);
		$roles->addSubItem($role);
		$kind->addOption($roles);
			
		// Course
		$groups = new ilRadioOption($this->lng->txt('search_for_crs_members'),'crs');
		$group = new ilTextInputGUI($this->lng->txt('search_crs_title'),'rep_query[crs][title]');
		$group->setSize(30);
		$group->setMaxLength(120);
		$groups->addSubItem($group);
		$kind->addOption($groups);

		// Group
		$groups = new ilRadioOption($this->lng->txt('search_for_grp_members'),'grp');
		$group = new ilTextInputGUI($this->lng->txt('search_grp_title'),'rep_query[grp][title]');
		$group->setSize(30);
		$group->setMaxLength(120);
		$groups->addSubItem($group);
		$kind->addOption($groups);
	}
	

	function show()
	{
		$this->showSearchResults();
	}

	function appendSearch()
	{
		$_SESSION['search_append'] = true;
		$this->performSearch();
	}

	/**
	 * Perform a search
	 * @return 
	 */
	function performSearch()
	{
		$found_query = false;
		foreach((array) $_POST['rep_query'][$_POST['search_for']] as $field => $value)
		{
			if(trim(ilUtil::stripSlashes($value)))
			{
				$found_query = true;
				break;
			}
		}
		if(!$found_query)
		{
			ilUtil::sendFailure($this->lng->txt('msg_no_search_string'));
			$this->start();
			return false;
		}
	
		// unset search_append if called directly
		if($_POST['cmd']['performSearch'])
		{
			unset($_SESSION['search_append']);
		}

		switch($this->search_type)
		{
			case 'usr':
				$this->__performUserSearch();
				break;

			case 'grp':
				$this->__performGroupSearch();
				break;

			case 'crs':
				$this->__performCourseSearch();
				break;

			case 'role':
				$this->__performRoleSearch();
				break;

			default:
				echo 'not defined';
		}
		$this->result_obj->setRequiredPermission('read');
		$this->result_obj->addObserver($this, 'searchResultFilterListener');
		$this->result_obj->filter(ROOT_FOLDER_ID,QP_COMBINATION_OR);
		
		// User access filter
		if($this->search_type == 'usr')
		{
			include_once './Services/User/classes/class.ilUserFilter.php';
			$this->search_results = array_intersect(
				$this->result_obj->getResultIds(),
				ilUserFilter::getInstance()->filter($this->result_obj->getResultIds())
			);
		}
		else
		{
			$this->search_results = array();
			foreach((array) $this->result_obj->getResults() as $res)
			{
				$this->search_results[] = $res['obj_id'];
			}
		}

		if(!count($this->search_results))
		{
			ilUtil::sendFailure($this->lng->txt('search_no_match'));
			$this->showSearch();
			return true;
		}
		$this->__updateResults();
		if($this->result_obj->isLimitReached())
		{
			$message = sprintf($this->lng->txt('search_limit_reached'),$this->settings->getMaxHits());
			ilUtil::sendInfo($message);
			return true;
		}
		// show results
		$this->show();
		return true;
	}

	function __performUserSearch()
	{
		include_once 'Services/Search/classes/class.ilUserSearchOptions.php';
		include_once 'Services/Search/classes/class.ilObjectSearchFactory.php';

		foreach(ilUserSearchOptions::_getSearchableFieldsInfo(!$this->isSearchableCheckEnabled()) as $info)
		{
			$name = $info['db'];
			$query_string = $_SESSION['rep_query']['usr'][$name];

			// continue if no query string is given
			if(!$query_string)
			{
				continue;
			}
		
			if(!is_object($query_parser = $this->__parseQueryString($query_string)))
			{
				ilUtil::sendInfo($query_parser);
				return false;
			}
			switch($info['type'])
			{
				case FIELD_TYPE_UDF_SELECT:
					// Do a phrase query for select fields
					$query_parser = $this->__parseQueryString('"'.$query_string.'"');
							
				case FIELD_TYPE_UDF_TEXT:
					$udf_search = ilObjectSearchFactory::_getUserDefinedFieldSearchInstance($query_parser);
					$udf_search->setFields(array($name));
					$result_obj = $udf_search->performSearch();

					// Store entries
					$this->__storeEntries($result_obj);
					break;

				case FIELD_TYPE_SELECT:
					// Do a phrase query for select fields
					$query_parser = $this->__parseQueryString('"'.$query_string.'"');

				case FIELD_TYPE_TEXT:
					$user_search =& ilObjectSearchFactory::_getUserSearchInstance($query_parser);
					$user_search->setFields(array($name));
					$result_obj = $user_search->performSearch();

					// store entries
					$this->__storeEntries($result_obj);
					break;
			}
		}
	}

	/**
	 * Search groups
	 * @return 
	 */
	function __performGroupSearch()
	{
		include_once 'Services/Search/classes/class.ilObjectSearchFactory.php';

		$query_string = $_SESSION['rep_query']['grp']['title'];
		if(!is_object($query_parser = $this->__parseQueryString($query_string)))
		{
			ilUtil::sendInfo($query_parser,true);
			return false;
		}

		include_once 'Services/Search/classes/Like/class.ilLikeObjectSearch.php';
		$object_search = new ilLikeObjectSearch($query_parser);
		$object_search->setFilter(array('grp'));
		$this->__storeEntries($object_search->performSearch());

		return true;
	}

	/**
	 * Search courses
	 * @return 
	 */
	protected function __performCourseSearch()
	{
		include_once 'Services/Search/classes/class.ilObjectSearchFactory.php';

		$query_string = $_SESSION['rep_query']['crs']['title'];
		if(!is_object($query_parser = $this->__parseQueryString($query_string)))
		{
			ilUtil::sendInfo($query_parser,true);
			return false;
		}

		include_once 'Services/Search/classes/Like/class.ilLikeObjectSearch.php';
		$object_search = new ilLikeObjectSearch($query_parser);
		$object_search->setFilter(array('crs'));
		$this->__storeEntries($object_search->performSearch());

		return true;
	}

	/**
	 * Search roles
	 * @return 
	 */
	function __performRoleSearch()
	{
		include_once 'Services/Search/classes/class.ilObjectSearchFactory.php';

		$query_string = $_SESSION['rep_query']['role']['title'];
		if(!is_object($query_parser = $this->__parseQueryString($query_string)))
		{
			ilUtil::sendInfo($query_parser,true);
			return false;
		}
		
		// Perform like search
		include_once 'Services/Search/classes/Like/class.ilLikeObjectSearch.php';
		$object_search = new ilLikeObjectSearch($query_parser);
		$object_search->setFilter(array('role'));
		$this->__storeEntries($object_search->performSearch());

		return true;
	}

	/**
	* parse query string, using query parser instance
	* @return object of query parser or error message if an error occured
	* @access public
	*/
	function &__parseQueryString($a_string,$a_combination_or = true)
	{
		$query_parser = new ilQueryParser(ilUtil::stripSlashes($a_string));
		$query_parser->setCombination($a_combination_or ? QP_COMBINATION_OR : QP_COMBINATION_AND);
		$query_parser->setMinWordLength(1,true);
		$query_parser->parse();

		if(!$query_parser->validate())
		{
			return $query_parser->getMessage();
		}
		return $query_parser;
	}

	// Private
	function __loadQueries()
	{
		if(is_array($_POST['rep_query']))
		{
			$_SESSION['rep_query'] = $_POST['rep_query'];
		}
	}


	function __setSearchType()
	{
		// Update search type. Default to user search
		if($_POST['search_for'])
		{
			#echo 1;
			$_SESSION['rep_search_type'] = $_POST['search_for'];
		}
		if(!$_POST['search_for'] and !$_SESSION['rep_search_type'])
		{
			#echo 2;
			$_SESSION['rep_search_type'] = 'usr';
		}
		
		$this->search_type = $_SESSION['rep_search_type'];
		#echo $this->search_type;

		return true;
	}


	function __updateResults()
	{
		if(!$_SESSION['search_append'])
		{
			$_SESSION['rep_search'] = array();
		}
		foreach($this->search_results as $result)
		{
			$_SESSION['rep_search'][$this->search_type][] = $result;
		}
		if(!$_SESSION['rep_search'][$this->search_type])
		{
			$_SESSION['rep_search'][$this->search_type] = array();
		}
		else
		{
			// remove duplicate entries
			$_SESSION['rep_search'][$this->search_type] = array_unique($_SESSION['rep_search'][$this->search_type]);
		}
		return true;
	}

	function __appendToStoredResults($a_usr_ids)
	{
		if(!$_SESSION['search_append'])
		{
			return $_SESSION['rep_search']['usr'] = $a_usr_ids;
		}
		$_SESSION['rep_search']['usr'] = array();
		foreach($a_usr_ids as $usr_id)
		{
			$_SESSION['rep_search']['usr'][] = $usr_id;
		}
		return $_SESSION['rep_search']['usr'] ? array_unique($_SESSION['rep_search']['usr']) : array();
	}

	function __storeEntries(&$new_res)
	{
		if($this->stored == false)
		{
			$this->result_obj->mergeEntries($new_res);
			$this->stored = true;
			return true;
		}
		else
		{
			$this->result_obj->intersectEntries($new_res);
			return true;
		}
	}

	/**
	 * Add new search button
	 * @return 
	 */
	protected function addNewSearchButton()
	{
		include_once './Services/UIComponent/Toolbar/classes/class.ilToolbarGUI.php';
		$toolbar = new ilToolbarGUI();
		$toolbar->addButton(
			$this->lng->txt('search_new'), 
			$this->ctrl->getLinkTarget($this,'showSearch')
		);
		$this->tpl->setVariable('ACTION_BUTTONS',$toolbar->getHTML());
	}
	
	public function showSearchResults()
	{
		$counter = 0;
		$f_result = array();
		
		$this->tpl->addBlockFile('ADM_CONTENT','adm_content','tpl.rep_search_result.html','Services/Search');
		$this->addNewSearchButton();
		
		switch($this->search_type)
		{
			case "usr":
				$this->showSearchUserTable($_SESSION['rep_search']['usr'],'showSearchResults');
				break;

			case 'grp':
				$this->showSearchGroupTable($_SESSION['rep_search']['grp']);
				break;

			case 'crs':
				$this->showSearchCourseTable($_SESSION['rep_search']['crs']);
				break;

			case 'role':
				$this->showSearchRoleTable($_SESSION['rep_search']['role']);
				break;
		}
	}

	/**
	 * Show usr table
	 * @return 
	 * @param object $a_usr_ids
	 */
	protected function showSearchUserTable($a_usr_ids,$a_parent_cmd)
	{
		$is_in_admin = ($_REQUEST['baseClass'] == 'ilAdministrationGUI');
		if($is_in_admin)
		{
			// remember link target to admin search gui (this)
			$_SESSION["usr_search_link"] = $this->ctrl->getLinkTarget($this,'show');
		}
		
		include_once './Services/Search/classes/class.ilRepositoryUserResultTableGUI.php';
		
		$table = new ilRepositoryUserResultTableGUI($this,$a_parent_cmd,$is_in_admin);
		if(count($this->add_options))
		{
			$table->addMultiItemSelectionButton(
				'selectedCommand',
				$this->add_options,
				'handleMultiCommand',
				$this->lng->txt('execute')
			);
		}
		else
		{
			$table->addMultiCommand('addUser', $this->lng->txt('btn_add'));
		}
		$table->parseUserIds($a_usr_ids);
		
		$this->tpl->setVariable('RES_TABLE',$table->getHTML());
	}
	
	/**
	 * Show usr table
	 * @return 
	 * @param object $a_usr_ids
	 **/
	protected function showSearchRoleTable($a_obj_ids)
	{
		include_once './Services/Search/classes/class.ilRepositoryObjectResultTableGUI.php';
		
		$table = new ilRepositoryObjectResultTableGUI($this,'showSearchResults',$this->object_selection);
		$table->parseObjectIds($a_obj_ids);
		
		$this->tpl->setVariable('RES_TABLE',$table->getHTML());
	}

	/**
	 * 
	 * @return 
	 * @param array $a_obj_ids
	 */
	protected function showSearchGroupTable($a_obj_ids)
	{
		include_once './Services/Search/classes/class.ilRepositoryObjectResultTableGUI.php';
		
		$table = new ilRepositoryObjectResultTableGUI($this,'showSearchResults',$this->object_selection);
		$table->parseObjectIds($a_obj_ids);
		
		$this->tpl->setVariable('RES_TABLE',$table->getHTML());
	}
	
	/**
	 * 
	 * @return 
	 * @param array $a_obj_ids
	 */
	protected function showSearchCourseTable($a_obj_ids)
	{
		include_once './Services/Search/classes/class.ilRepositoryObjectResultTableGUI.php';
		
		$table = new ilRepositoryObjectResultTableGUI($this,'showSearchResults',$this->object_selection);
		$table->parseObjectIds($a_obj_ids);
		
		$this->tpl->setVariable('RES_TABLE',$table->getHTML());
	}

	/**
	 * List users of course/group/roles
	 * @return 
	 */
	protected function listUsers()
	{
		// get parameter is used e.g. in exercises to provide
		// "add members of course" link
		if ($_GET["list_obj"] != "" && !is_array($_POST['obj']))
		{
			$_POST['obj'][0] = $_GET["list_obj"];
		}
		if(!is_array($_POST['obj']) or !$_POST['obj'])
		{
			ilUtil::sendFailure($this->lng->txt('select_one'));
			$this->showSearchResults();
			return false;
		}
		
		$_SESSION['rep_search']['objs'] = $_POST['obj'];
		
		// Get all members
		$members = array();
		foreach($_POST['obj'] as $obj_id)
		{
			$type = ilObject::_lookupType($obj_id);
			switch($type)
			{
				case 'crs':
				case 'grp':
					
					include_once './Services/Membership/classes/class.ilParticipants.php';
					if(ilParticipants::hasParticipantListAccess($obj_id))
					{
						$members = array_merge((array) $members, ilParticipants::getInstanceByObjId($obj_id)->getParticipants());
					}
					break;
					
				case 'role':
					global $rbacreview;
					
					include_once './Services/User/classes/class.ilUserFilter.php';
					$members = array_merge($members,  ilUserFilter::getInstance()->filter($rbacreview->assignedUsers($obj_id)));
					break;
			}
		}
		$members = array_unique((array) $members);
		$this->__appendToStoredResults($members);
		
		$this->tpl->addBlockFile('ADM_CONTENT','adm_content','tpl.rep_search_result.html','Services/Search');
		
		$this->addNewSearchButton();
		$this->showSearchUserTable($_SESSION['rep_search']['usr'],'storedUserList');
		return true;
	}
	
	/**
	 * Called from table sort
	 * @return 
	 */
	protected function storedUserList()
	{
		$_POST['obj'] = $_SESSION['rep_search']['objs'];
		$this->listUsers();
		return true;	
	}
	
	/**
	 * Listener called from ilSearchResult
	 * Id is obj_id for role, usr
	 * Id is ref_id for crs grp
	 * @param int $a_id 
	 * @param array $a_data
	 * @return 
	 */
	public function searchResultFilterListener($a_ref_id,$a_data)
	{
		if($a_data['type'] == 'usr')
		{
			if($a_data['obj_id'] == ANONYMOUS_USER_ID)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Toggle object selection status
	 *
	 * @param bool $a_value
	 */
	public function allowObjectSelection($a_value = false)
	{
		$this->object_selection = (bool)$a_value;
	}

	/**
	 * Return selection of course/group/roles to calling script
	 */
	protected function selectObject()
	{
		// get parameter is used e.g. in exercises to provide
		// "add members of course" link
		if ($_GET["list_obj"] != "" && !is_array($_POST['obj']))
		{
			$_POST['obj'][0] = $_GET["list_obj"];
		}
		if(!is_array($_POST['obj']) or !$_POST['obj'])
		{
			ilUtil::sendFailure($this->lng->txt('select_one'));
			$this->showSearchResults();
			return false;
		}

		$this->ctrl->setParameter($this->callback["class"], "obj", implode(";", $_POST["obj"]));
		$this->ctrl->redirect($this->callback["class"], $this->callback["method"]);
	}
}
?>