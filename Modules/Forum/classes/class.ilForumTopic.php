<?php
/* Copyright (c) 1998-2012 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once './Modules/Forum/classes/class.ilForumPost.php';

/**
* @author Michael Jansen <mjansen@databay.de>
* @version $Id:$
*
* @ingroup ModulesForum
*/
class ilForumTopic
{
	private $id = 0;
	
	private $forum_id = 0;	
	
	private $frm_obj_id = 0;
	
	private $user_id = 0;
	
	private $user_alias = '';
	
	private $subject = '';
	
	private $createdate = '0000-00-00 00:00:00';
	
	private $changedate = '0000-00-00 00:00:00';
	
	private $num_posts = 0;
	
	private $last_post_string = '';
	
	private $visits = 0;
	
	private $import_name = '';
	
	private $is_sticky = 0;
	
	private $is_closed = 0;
	
	private $orderField = '';
	
	private $posts = array();

	private $db = null;
	
	private $is_moderator = false;
	
	protected $orderDirection = 'DESC';
	
	protected static $possibleOrderDirections = array('ASC', 'DESC');
	
	/**
	* Constructor
	*
	* Returns an object of a forum topic. The constructor calls the private method read()
	* to load the topic data from database into the object.
	*
	* @param  	integer	$a_id			primary key of a forum topic (optional)
	* @param  	bool	$a_is_moderator	moderator-status of the current user (optional)
	* @param	bool	$preventImplicitRead	Prevents the implicit database query if an id was passed
	* 
	* @access	public
	*/
	public function __construct($a_id = 0, $a_is_moderator = false, $preventImplicitRead = false)
	{
		global $ilDB;

		$this->is_moderator = $a_is_moderator;
		$this->db = $ilDB;
		$this->id = $a_id;
		
		if(!$preventImplicitRead)
		{
			$this->read();
		}
	}

	/**
	 * @param $data
	 */
	public function assignData($data)
	{
		$this->setId((int) $data['thr_pk']);
		$this->setForumId((int) $data['thr_top_fk']);
		$this->setSubject($data['thr_subject']);
		$this->setUserId((int) $data['thr_usr_id']);
		$this->setUserAlias($data['thr_usr_alias']);
		$this->setLastPostString($data['last_post_string']);
		$this->setCreateDate($data['thr_date']);
		$this->setChangeDate($data['thr_update']);
		$this->setVisits((int) $data['visits']);
		$this->setImportName($data['import_name']);
		$this->setSticky((int) $data['is_sticky']);
		$this->setClosed((int) $data['is_closed']);

		// Aggregated values
		$this->setNumPosts((int) $data['num_posts']);
		$this->setNumUnreadPosts((int) $data['num_unread_posts']);
		$this->setNumNewPosts((int) $data['num_new_posts']);
		$this->setUserNotificationEnabled((bool) $data['usr_notification_is_enabled']);
	}
	
	/**
	* Inserts the object data into database
	*
	* @return 	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function insert()
	{			
		if ($this->forum_id)
		{	
			$nextId = $this->db->nextId('frm_threads');
			$statement = $this->db->manipulateF('
				INSERT INTO frm_threads
				(	thr_pk,
					thr_top_fk,
					thr_subject,
					thr_usr_id,
					thr_usr_alias,
					thr_num_posts,
					thr_last_post,
					thr_date, 			
					thr_update,
					import_name,
					is_sticky,
					is_closed
				)
				VALUES(%s,%s,%s,%s,%s,%s,%s,%s, %s, %s,%s,%s)',

				array(	'integer',
						'integer',
						'text',
						'integer',
						'text',
						'integer',
						'text',
						'timestamp',
						'timestamp',
						'text',
						'integer',
						'integer'),
				array(	$nextId,	
						$this->forum_id,
						$this->subject,
						$this->user_id,
						$this->user_alias,
						$this->num_posts,
						$this->last_post_string,
						$this->createdate,
						NULL,
						$this->import_name,
						$this->is_sticky,
						$this->is_closed
			));
			$this->id = $nextId;
							
			return true;
		}
		
		return false;	
	}
	
	/**
	* Updates an existing topic
	*
	* @return 	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function update()
	{
		if ($this->id)
		{		
			$statement = $this->db->manipulateF('
				UPDATE frm_threads
				SET thr_top_fk = %s,
					thr_subject = %s,
					thr_update = %s,
					thr_num_posts = %s,
					thr_last_post = %s
				WHERE thr_pk = %s',
				array('integer', 'text','timestamp', 'integer', 'text', 'integer'),
				array(	$this->forum_id, 
						$this->subject, 
			/*			$this->changedate, */
						date('Y-m-d H:i:s'),
						$this->num_posts, 
						$this->last_post_string, 
						$this->id
			));
			
			return true;
		}
		
		return false;
	}
	
	/**
	* Reads the data of the current object id from database and loads it into the object.
	*
	* @return  	bool	true in case of success, false in case of failure
	* 
	* @access 	private
	*/
	private function read()
	{

		if ($this->id)
		{
			$res = $this->db->queryf('
				SELECT frm_threads.*, top_frm_fk frm_obj_id
				FROM frm_threads
				INNER JOIN frm_data ON top_pk = thr_top_fk
				WHERE thr_pk = %s',
				array('integer'), array($this->id));

			$row = $res->fetchRow(DB_FETCHMODE_OBJECT);

			if (is_object($row))
			{
				
				$this->thr_pk = $row->pos_pk;   // thr_pk = pos_pk ??!??!
				$this->forum_id = $row->thr_top_fk;
				$this->user_id = $row->thr_usr_id;
				$this->user_alias = $row->thr_usr_alias;	
				$this->subject = html_entity_decode($row->thr_subject);
				$this->createdate = $row->thr_date;	
				$this->changedate = $row->thr_update;
				$this->import_name = $row->import_name;
				$this->num_posts = $row->thr_num_posts;
				$this->last_post_string = $row->thr_last_post;
				$this->visits = $row->visits;
				$this->is_sticky = $row->is_sticky;
				$this->is_closed = $row->is_closed;
				$this->frm_obj_id = $row->frm_obj_id;
				
				return true;
			}
			$this->id = 0;
			return false;
		}
		
		return false;
	}
	
	/**
	* Calls the private method read() to load the topic data from database into the object.
	*
	* @return  	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function reload()
	{
		return $this->read();
	}
	
	/**
	* Fetches the primary key of the first post node of the current topic from database and returns it.
	*
	* @return  	integer		primary key of the first post node
	* @access 	public
	*/
	public function getFirstPostId()
	{ 
		$res = $this->db->queryf('
			SELECT * FROM frm_posts_tree 
			WHERE thr_fk = %s
			AND parent_pos = %s',
			array('integer', 'integer'), array($this->id, '1'));
		
		$row = $res->fetchRow(DB_FETCHMODE_OBJECT);
		
		return $row->pos_fk ? $row->pos_fk : 0;
	}
	
	/**
	* Updates the visit counter of the current topic.
	* 
	* @access	public
	*/
	public function updateVisits()
	{
		$checkTime = time() - (60 * 60);
		
		if ($_SESSION['frm_visit_frm_threads_'.$this->id] < $checkTime)
		{
			$_SESSION['frm_visit_frm_threads_'.$this->id] = time();		
		
			$statement = $this->db->manipulateF('
				UPDATE frm_threads
				SET	visits = visits + 1
				WHERE thr_pk = %s',
				array('integer'), array($this->id));
		}
		
		return true;
	}
	
	/**
	* Fetches and returns the number of posts for the given user id.
	* 
	* @param  	integer		$a_user_id		user id
	* @return	integer		number of posts
	* @access	public
	*/
	public function countPosts()
	{
		$res = $this->db->queryf('
			SELECT COUNT(*) cnt
			FROM frm_posts
			WHERE pos_thr_fk = %s',
			array('integer'), array($this->id));
		
		$rec = $res->fetchRow(DB_FETCHMODE_ASSOC);
			
		return $rec['cnt'];
	}
	
	/**
	* Fetches and returns the number of active posts for the given user id.
	* 
	* @param  	integer		$a_user_id		user id
	* @return	integer		number of active posts
	* @access	public
	*/
	public function countActivePosts()
	{
		global $ilUser;

		$res = $this->db->queryf('
			SELECT COUNT(*) cnt
			FROM frm_posts
			WHERE (pos_status = %s
				 OR (pos_status = %s AND pos_usr_id = %s))
			AND pos_thr_fk = %s',
			array('integer', 'integer', 'integer', 'integer'), array('1', '0', $ilUser->getId(), $this->id));
			
		$rec = $res->fetchRow(DB_FETCHMODE_ASSOC);
			
		return $rec['cnt'];
	}
	
	/**
	* Fetches and returns an object of the first post in the current topic.
	* 
	* @return	ilForumPost		object of a post
	* @access	public
	*/
	public function getFirstPostNode()
	{		
		$res = $this->db->queryf('
			SELECT pos_pk
			FROM frm_posts 
			INNER JOIN frm_posts_tree ON pos_fk = pos_pk
			WHERE parent_pos = %s
			AND thr_fk = %s',
			array('integer', 'integer'),
			array('0', $this->id));
			
		$row = $res->fetchRow(DB_FETCHMODE_OBJECT);
		
		return new ilForumPost($row->pos_pk);
	}
	
	/**
	* Fetches and returns an object of the last post in the current topic.
	* 
	* @return	ilForumPost		object of the last post
	* @access	public
	*/
	public function getLastPost()
	{
		if ($this->id)
		{
			$this->db->setLimit(1);
			$res = $this->db->queryf('
				SELECT pos_pk
				FROM frm_posts 
				WHERE pos_thr_fk = %s				 
				ORDER BY pos_date DESC',
				array('integer'), array($this->id));
			
			$row = $res->fetchRow(DB_FETCHMODE_OBJECT);

			return new ilForumPost($row->pos_pk);
		}
		
		return false;
	}
	
	/**
	* Fetches and returns an object of the last active post in the current topic.
	* 
	* @return	ilForumPost		object of the last active post
	* @access	public
	*/
	public function getLastActivePost()
	{
		global $ilUser;
		
		if ($this->id)
		{
			$this->db->setLimit(1);
			$res = $this->db->queryf('
				SELECT pos_pk
				FROM frm_posts 
				WHERE pos_thr_fk = %s		
				AND (pos_status = %s OR 
					(pos_status = %s AND pos_usr_id = %s))							 
				ORDER BY pos_date DESC',
				array('integer', 'integer', 'integer', 'integer'),
				array($this->id, '1', '0', $ilUser->getId()));
			
			$row = $res->fetchRow(DB_FETCHMODE_OBJECT);
			
			return new ilForumPost($row->pos_pk);
		}
		
		return false;
	}
	
	public function getAllPosts()
	{	
	    $posts = array();
		
		if($this->id)
		{
			$res = $this->db->queryf('
				SELECT pos_pk
				FROM frm_posts 
				WHERE pos_thr_fk = %s',
				array('integer'),
				array($this->id));
			
			while ($row = $res->fetchRow(DB_FETCHMODE_OBJECT))
			{
				$posts[$row->pos_pk] = $row;
			}
		}
		
		return is_array($posts) ? $posts : array();
	}
 
 	/**
	* Fetches and returns an array of posts from the post tree, starting with the node object passed by
	* the first paramter.
	* 
	* @param    ilForumPost	$a_post_node	node-object of a post
	* @return	array		array of post objects
	* @access	public
	*/
	public function getPostTree(ilForumPost $a_post_node)
	{
		global $ilUser;

		$posts = array();
	    
		$data = array();
		$data_types = array();

		$query = '
			SELECT 			pos_pk, fpt_date, rgt, pos_top_fk, pos_thr_fk, 
							pos_usr_id, pos_usr_alias, pos_subject,
							pos_status, pos_message, pos_date, pos_update,
							update_user, pos_cens, pos_cens_com, notify,
							import_name, fpt_pk, parent_pos, lft, depth,
							(CASE
							WHEN fur.post_id IS NULL '.
								($ilUser->getId() == ANONYMOUS_USER_ID ? ' AND 1 = 2 ' : '').'
							THEN 0
							ELSE 1
							END) post_read,
							firstname, lastname, title, login
							 
			FROM 			frm_posts_tree
			 
			INNER JOIN 		frm_posts 
				ON 			pos_fk = pos_pk
				
			LEFT JOIN		usr_data
				ON			pos_usr_id  = usr_id
				
			LEFT JOIN		frm_user_read fur
				ON			fur.thread_id = pos_thr_fk
				AND			fur.post_id = pos_pk
				AND			fur.usr_id = %s
				 
			WHERE 			lft BETWEEN %s AND %s 
				AND 		thr_fk = %s';
		
		array_push($data_types, 'integer', 'integer', 'integer', 'integer');
		array_push($data, $ilUser->getId(), $a_post_node->getLft(), $a_post_node->getRgt(), $a_post_node->getThreadId());

		if($this->orderField != "")
		{
			$query .= " ORDER BY ".$this->orderField." ".$this->getOrderDirection();
		}

		$res = $this->db->queryf($query, $data_types, $data);
		
		$usr_ids = array();

		$deactivated = array();
		while( $row = $this->db->fetchAssoc($res) )
		{
			$tmp_object = new ilForumPost($row['pos_pk'], false, true);
			$tmp_object->assignData($row);	

		 	if (!$this->is_moderator)
		 	{
				if (!$tmp_object->isActivated() && $tmp_object->getUserId() != $ilUser->getId())
			 	{
			 		$deactivated[] = $tmp_object;
			 		unset($tmp_object);
			 		continue;
			 	}
			 
				foreach ($deactivated as $deactivated_node)
				{
					if ($deactivated_node->getLft() < $tmp_object->getLft() && $deactivated_node->getRgt() > $tmp_object->getLft())
					{
				 		$deactivated[] = $tmp_object;
				 		unset($tmp_object);
						continue 2;
					}
				}
		 	}

			if((int)$row['pos_usr_id'])
			{
				$usr_ids[] = (int)$row['pos_usr_id'];
			}
			if((int)$row['update_user'])
			{
				$usr_ids[] = (int)$row['update_user'];
			}
			 
			$posts[] = $tmp_object;
			 
			unset($tmp_object);
		}

		require_once 'Modules/Forum/classes/class.ilForumAuthorInformationCache.php';
		ilForumAuthorInformationCache::preloadUserObjects(array_unique($usr_ids));

		return $posts;
	}
	
	/**
	* Moves all posts within the current thread to a new forum
	* 
	* @param    integer 	$old_obj_id object id of the current forum
	* @param    integer 	$old_pk		primary key of old forum
	* @param    integer 	$new_obj_id	object id of the new forum
	* @param    integer 	$new_pk		primary key of new forum
	* @return	integer 	number of afffected rows by updating posts
	* @access	public
	*/
	public function movePosts($old_obj_id, $old_pk, $new_obj_id, $new_pk)
	{
		global $ilDB;
		
		if ($this->id)
		{
			$nodes = $this->getAllPosts();
			if(is_array($nodes))
			{
				// Move attachments
				foreach($nodes as $node)
				{
					$file_obj = new ilFileDataForum((int)$old_obj_id, (int)$node->pos_pk);
					$file_obj->moveFilesOfPost((int)$new_obj_id);
					unset($file_obj);
				}
			}

			$this->db->manipulateF('
				DELETE FROM frm_user_read
				WHERE obj_id = %s AND thread_id =%s',
				array('integer', 'integer'),
				array($new_obj_id, $this->id));
			
			$this->db->manipulateF('
				UPDATE frm_user_read
				SET obj_id = %s
				WHERE thread_id = %s',
				array('integer', 'integer'),
				array($new_obj_id, $this->id));

			$this->db->manipulateF('
				DELETE FROM frm_thread_access
				WHERE obj_id = %s AND thread_id =%s',
				array('integer', 'integer'),
				array($new_obj_id, $this->id));
			
			$this->db->manipulateF('
				UPDATE frm_thread_access
				SET obj_id = %s
				WHERE thread_id =%s',
				array('integer', 'integer'),  
				array($new_obj_id, $this->id));
			
			$this->db->manipulateF('
				UPDATE frm_posts
				SET pos_top_fk = %s
				WHERE pos_thr_fk = %s',
				array('integer', 'integer'), 
				array($new_pk, $this->id));
			
			// update all related news
			$posts = $ilDB->queryf('
				SELECT * FROM frm_posts WHERE pos_thr_fk = %s',
				array('integer'), array($this->id));
			
			$old_obj_id = ilForum::_lookupObjIdForForumId($old_pk);

			$new_obj_id = ilForum::_lookupObjIdForForumId($new_pk);

			while($post = $posts->fetchRow(DB_FETCHMODE_ASSOC))
			{ 
				include_once("./Services/News/classes/class.ilNewsItem.php");
				$news_id = ilNewsItem::getFirstNewsIdForContext($old_obj_id,
					"frm", $post["pos_pk"], "pos");
				$news_item = new ilNewsItem($news_id);
				$news_item->setContextObjId($new_obj_id);
				$news_item->update();
				//echo "<br>-".$post["pos_pk"]."-".$old_obj_id."-".$new_obj_id."-";
			}
			
			return count($nodes);
		}
		
		return 0;
	}
	
	public function getNestedSetPostChildren($pos_id = null, $expandedNodes = array())
	{
		global $ilUser;

		$data = null;

		if( $pos_id !== null )
		{
			$res = $this->db->queryF("
				SELECT		lft, rgt
				FROM		frm_posts_tree
				WHERE		pos_fk = %s
				AND			thr_fk = %s",
				array('integer', 'integer'),
				array($pos_id, $this->id)
			);

			$data = $this->db->fetchAssoc($res);
		}

		$query = '
			SELECT			fpt.depth,
							fpt.parent_pos,
							fp.pos_pk,
							fp.pos_subject,
							fp.pos_usr_alias,
							fp.pos_date,
							fp.pos_update,
							fp.pos_status,
							fp.pos_usr_id,
							fp.pos_usr_alias,
							fp.import_name,
							fur.post_id,
							(CASE
							WHEN fur.post_id IS NULL '.
								($ilUser->getId() == ANONYMOUS_USER_ID ? ' AND 1 = 2 ' : '').'
							THEN 0
							ELSE 1
							END) post_read,
							COUNT(fpt2.pos_fk) children	

			FROM			frm_posts_tree fpt

			INNER JOIN		frm_posts fp
				ON			fp.pos_pk = fpt.pos_fk
				
			LEFT JOIN		frm_posts_tree fpt2
				 ON         fpt2.lft BETWEEN fpt.lft AND fpt.rgt
				 AND		fpt.thr_fk = fpt2.thr_fk
				 AND		fpt.pos_fk != fpt2.pos_fk ';
								
	
		$query .= '
			LEFT JOIN		frm_user_read fur
				ON			fur.thread_id = fp.pos_thr_fk
				AND			fur.post_id = fp.pos_pk
				AND			fur.usr_id = '.$this->db->quote($ilUser->getId(), 'integer').'

			LEFT JOIN		usr_data ud
				ON			ud.usr_id = fp.pos_usr_id
		
			WHERE			fpt.thr_fk = '.$this->db->quote($this->id, 'integer');

		if( $data )
		{
			$query .= '		AND fpt.lft > '.$this->db->quote($data['lft'], 'integer').
					'		AND fpt.lft < '.$this->db->quote($data['rgt'], 'integer').' ';
		}

		if( !$this->is_moderator )
		{
			$query .= ' AND (fp.pos_status = 1 OR fp.pos_status = 0 AND fp.pos_usr_id = '.
						$this->db->quote($ilUser->getId(), 'integer').') ';
		}
		
		if( $expandedNodes )			
		{
			$query .= ' AND '.$this->db->in('fpt.parent_pos', $expandedNodes, false, 'integer').' ';	
		}
			

		$query .= ' GROUP BY fpt.depth,
							fpt.parent_pos,
							fp.pos_pk,
							fp.pos_subject,
							fp.pos_usr_alias,
							fp.pos_date,
							fp.pos_update,
							fp.pos_status,
							fp.pos_usr_id,
							fp.pos_usr_alias,
							fp.import_name,
							fur.post_id
					ORDER BY fpt.rgt DESC
		';		
		
		$queryCounter = '
			SELECT			pos_fk
			FROM			frm_posts_tree fpt
			INNER JOIN		frm_posts fp
				ON			fp.pos_pk = fpt.pos_fk
			WHERE			fpt.thr_fk = '.$this->db->quote($this->id, 'integer');
		if( !$this->is_moderator )
		{
			$queryCounter .= ' AND (fp.pos_status = 1 OR fp.pos_status = 0 AND fp.pos_usr_id = '.
						$this->db->quote($ilUser->getId(), 'integer').') ';
		}
		$queryCounter .= ' ORDER BY fpt.rgt DESC';

		$resCounter = $this->db->query($queryCounter);
		$counter = array();
		$i = 0;
		while( $row = $this->db->fetchAssoc($resCounter) )
		{
			$counter[$row['pos_fk']] = $i++;
		}		

		$res = $this->db->query($query);		
		$children = array();
		$usr_ids = array();
		while( $row = $this->db->fetchAssoc($res) )
		{
			if((int)$row['pos_usr_id'])
			{
				$usr_ids[] = (int)$row['pos_usr_id'];
			}
			
			$row['counter'] = $counter[$row['pos_pk']];
			$children[] = $row;
		}

		require_once 'Modules/Forum/classes/class.ilForumAuthorInformationCache.php';
		ilForumAuthorInformationCache::preloadUserObjects(array_unique($usr_ids));

		return $children;
	}
	
	/**
	* Check whether a user's notification about new posts in a thread is enabled (result > 0) or not (result == 0).
	* @param    integer		$a_user_id		id of an user
	* @return	bool		true in case of success, false in case of failure
	* @access	public
	*/
	public function isNotificationEnabled($a_user_id)
	{
		if ($this->id && $a_user_id)
		{					
			$result = $this->db->queryf('
				SELECT COUNT(notification_id) cnt FROM frm_notification 
				WHERE user_id = %s AND thread_id = %s',
				array('integer', 'integer'),
				array($a_user_id, $this->id));

			while($record = $this->db->fetchAssoc($result))
			{				
				return (bool)$record['cnt'];
			}
			
			return false;			
		}
		
		return false;		
	}
	
	/**
	* Enable a user's notification about new posts in a thread.
	* @param    integer	$a_user_id		id of an user
	* @return	bool	true in case of success, false in case of failure
	* @access	public
	*/
	public function enableNotification($a_user_id)
	{
		if ($this->id && $a_user_id)
		{
			if (!$this->isNotificationEnabled($a_user_id))
			{
				$nextId = $this->db->nextId('frm_notification');
				$statement = $this->db->manipulateF('
					INSERT INTO frm_notification
					(	notification_id,
						user_id,
						thread_id
					)
					VALUES(%s, %s, %s)',
					array('integer', 'integer', 'integer'),
					array($nextId, $a_user_id, $this->id));

				return true;
			}
			return false;
		}
		
		return false;
	}
	
	/**
	* Disable a user's notification about new posts in a thread.
	* @param    integer	$a_user_id		id of an user
	* @return	bool	true in case of success, false in case of failure
	* @access	public
	*/
	public function disableNotification($a_user_id)
	{
		if ($this->id && $a_user_id)
		{
			$statement = $this->db->manipulateF('
				DELETE FROM frm_notification
				WHERE user_id = %s
				AND thread_id = %s',
				array('integer', 'integer'),
				array($a_user_id, $this->id));
				
			return false;
		}
		
		return false;
	}
	
	/**
	* Sets the current topic sticky. 
	*
	* @return  	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function makeSticky()
	{
		if ($this->id && !$this->is_sticky)
		{
			$statement = $this->db->manipulateF('
				UPDATE frm_threads 
				SET is_sticky = %s
				WHERE thr_pk = %s',
				array('integer', 'integer'),
				array('1', $this->id));
			
			$this->is_sticky = 1;
			
			return true;
		}
		
		return false;
	}
	
	/**
	* Sets the current topic non-sticky. 
	*
	* @return  	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function unmakeSticky()
	{
		if ($this->id && $this->is_sticky)
		{
			$statement = $this->db->manipulateF('
				UPDATE frm_threads 
				SET is_sticky = %s
				WHERE thr_pk = %s',
				array('integer', 'integer'),
				array('0', $this->id));
			
			$this->is_sticky = 0;
			
			return true;
		}
		
		return false;
	}
	
	/**
	* Closes the current topic.
	*
	* @return  	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function close()
	{ 
		if ($this->id && !$this->is_closed)
		{
			$statement = $this->db->manipulateF('
				UPDATE frm_threads 
				SET is_closed = %s
				WHERE thr_pk = %s',
				array('integer', 'integer'),
				array('1', $this->id));
				
			$this->is_closed = 1;
			
			return true;
		}
		
		return false;
	}
	
	/**
	* Reopens the current topic.
	*
	* @return  	bool	true in case of success, false in case of failure
	* @access 	public
	*/
	public function reopen()
	{
		if ($this->id && $this->is_closed)
		{
			$statement = $this->db->manipulateF('
				UPDATE frm_threads 
				SET is_closed = %s
				WHERE thr_pk = %s',
				array('integer', 'integer'),
				array('0', $this->id));
								
			$this->is_closed = 0;
			
			return true;
		}
		
		return false;
	}

	public function setId($a_id)
	{
		$this->id = $a_id;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setForumId($a_forum_id)
	{
		$this->forum_id = $a_forum_id;
	}
	public function getForumId()
	{
		return $this->forum_id;
	}	
	public function setUserId($a_user_id)
	{
		$this->user_id = $a_user_id;		
	}
	public function getUserId()
	{
		return $this->user_id;
	}
	public function setUserAlias($a_user_alias)
	{  
		$this->user_alias = $a_user_alias;
	}
	public function getUserAlias()
	{
		return $this->user_alias;
	}
	public function setSubject($a_subject)
	{
		$this->subject = $a_subject;
	}
	public function getSubject()
	{
		return $this->subject;
	}	
	public function setCreateDate($a_createdate)
	{
		$this->createdate = $a_createdate;
	}
	public function getCreateDate()
	{
		return $this->createdate;
	}
	public function setChangeDate($a_changedate)
	{
		if($a_changedate == '0000-00-00 00:00:00')
			$this->changedate = NULL;
		else
			$this->changedate = $a_changedate;
	}
	public function getChangeDate()
	{
		return $this->changedate;
	}	
	public function setImportName($a_import_name)
	{
		$this->import_name = $a_import_name;
	}
	public function getImportName()
	{
		return $this->import_name;
	}	
	public function setLastPostString($a_last_post)
	{
		if($a_last_post == '') $a_last_post = NULL;
		
		$this->last_post_string = $a_last_post;
	}
	public function getLastPostString()
	{
		return $this->last_post_string;
	}
	public function setVisits($a_visits)
	{
		$this->visits = $a_visits;
	}
	public function getVisits()
	{
		return $this->visits;
	}
	public function setSticky($a_sticky)
	{
		$this->is_sticky = $a_sticky;
	}
	public function isSticky()
	{
		return $this->is_sticky == 1 ? true : false;
	}
	public function setClosed($a_closed)
	{
		$this->is_closed = $a_closed;
	}
	public function isClosed()
	{
		return $this->is_closed == 1 ? true : false;
	}
	function setOrderField($a_order_field)
	{
		$this->orderField = $a_order_field;
	}
	function getOrderField()
	{
		return $this->orderField;
	}
	function setModeratorRight($bool)
	{
		$this->is_moderator = $bool;
	}
	function getModeratorRight()
	{
		return $this->is_moderator;
	}
	function getFrmObjId()
	{
		return $this->frm_obj_id;
	}
	
	/**
	* Looks up the title/subject of a topic/thread
	*
	* @param	integer id of the topic/thread
	* @return  	string	title/subject of the topic/thread
	* @access 	public
	* @static
	*/
	public static function _lookupTitle($a_topic_id)
	{
		global $ilDB;
		
		$res = $ilDB->queryf('
			SELECT thr_subject
			FROM frm_threads
			WHERE thr_pk = %s',
			array('integer'), array($a_topic_id));
		$row = $ilDB->fetchObject($res);

		if(is_object($row))
		{
			return $row->thr_subject;
		}
		
		return '';
	}

	public function updateThreadTitle()
	{
		global $ilDB;

		$ilDB->update('frm_threads',
			array('thr_subject' => array('text',$this->getSubject())),
			array('thr_pk'=> array('integer', $this->getId()))
		);
	}

	/**
	 * @param $a_num_posts
	 * @return ilForumTopic
	 */
	public function setNumPosts($a_num_posts)
	{
		$this->num_posts = $a_num_posts;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNumPosts()
	{
		return $this->num_posts;
	}

	/**
	 * @param int $num_new_posts
	 * @return ilForumTopic
	 */
	public function setNumNewPosts($num_new_posts)
	{
		$this->num_new_posts = $num_new_posts;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNumNewPosts()
	{
		return $this->num_new_posts;
	}

	/**
	 * @param int $num_unread_posts
	 * @return ilForumTopic
	 */
	public function setNumUnreadPosts($num_unread_posts)
	{
		$this->num_unread_posts = $num_unread_posts;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getNumUnreadPosts()
	{
		return $this->num_unread_posts;
	}

	/**
	 * @param boolean $user_notification_enabled
	 * @return ilForumTopic
	 */
	public function setUserNotificationEnabled($user_notification_enabled)
	{
		$this->user_notification_enabled = $user_notification_enabled;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getUserNotificationEnabled()
	{
		return $this->user_notification_enabled;
	}

	public function setOrderDirection($direction)
	{
		if(!in_array(strtoupper($direction), self::$possibleOrderDirections))
		{
			$direction = current(self::$possibleOrderDirections);
		}
		
		$this->orderDirection = $direction;
		return $this;
	}

	public function getOrderDirection()
	{
		return $this->orderDirection;
	}	
	
}