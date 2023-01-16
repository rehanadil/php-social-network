<?php

namespace SocialKit;

class User
{
	use \SocialTrait\AddOn;

	private $id;
	private $conn;
	public $data;
	private $escapeObj;
	private $banned;

	function __construct()
	{
		global $conn;
		$this->conn = $conn;
		$this->escapeObj = new \SocialKit\Escape();
		$this->banned = "0";
		return $this;
	}

	public function setConnection(\mysqli $conn)
	{
		$this->conn = $conn;
		return $this;
	}

	protected function getConnection()
	{
		return $this->conn;
	}

	public function getRows()
	{
		$cacheObj = new \SocialKit\Cache();
		$cacheObj->setType('user');
		$cacheObj->setId($this->id);
		$cacheObj->prepare();

		if ($cacheObj->exists())
		{
			$this->data = $cacheObj->get();

			$lastLoggedQuery = $this->getConnection()->query("SELECT last_logged FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id);
			$lastLoggedData = $lastLoggedQuery->fetch_array(MYSQLI_ASSOC);
			$this->data['last_logged'] = $lastLoggedData['last_logged'];
			$this->getOnline();
			$this->data['color'] = $this->getColor();

			return $this->data;
		}

		$query1 = $this->getConnection()->query("SELECT * FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id . " AND active=1 AND banned IN (" . $this->banned . ")");

		if ($query1->num_rows == 1)
		{
			$account_data = $query1->fetch_array(MYSQLI_ASSOC);

			if ($account_data['type'] === "user")
			{
				$user_data = $this->getData();
			}
			elseif ($account_data['type'] === "page")
			{
				$user_data = $this->getPageData();
			}
			elseif ($account_data['type'] === "group")
			{
				$user_data = $this->getGroupData();

			}
			elseif ($account_data['type'] === "event")
			{
				$user_data = $this->getEventData();

			}

			$this->data = array_merge($account_data, $user_data);

			if ($this->data['type'] == "event")
			{
				$this->data['url'] = smoothLink('index.php?a=timeline&type=event&id=' . $this->data['id']);
			} else {
				$this->data['url'] = smoothLink('index.php?a=timeline&id=' . $this->data['username']);
			}

			if (empty ($this->data['language']))
			{
				$this->data['language'] = "english";
			}

			// Get first and last names
			$this->getNames();

			// Get avatar
			$this->getAvatar();

			// Get cover
			$this->getCover();

			// Get verified result
			$this->getVerified();

			// Get online status
			$this->getOnline();

			$this->getSubscriptionPlan();

			$cacheObj->setData($this->data);
			$cacheObj->create();

			$this->data['color'] = $this->getColor();
			
			return $this->data;
		}
	}

	public function getColor()
	{
		if (!isLogged()) return "";
		global $conn, $user;

		if (isset($user))
		{
			$colorQuery = $conn->query("SELECT color
				FROM " . DB_USER_COLORS . "
				WHERE (
					user1=" . $user['id'] . "
					AND user2=" . $this->data['id'] . "
				)
				OR (
					user2=" . $user['id'] . "
					AND user1=" . $this->data['id'] . "
				)
			");

			if ($colorQuery->num_rows == 1)
			{
				$colorFetch = $colorQuery->fetch_array(MYSQLI_ASSOC);
		    	if (strlen($colorFetch['color']) > 0) return $colorFetch['color'];
			}
		}

	    return "";
	}

	public function getById($id) {
		 $this->setId($id);
		 return $this->getRows();
	}

	public function isReal($type='')
	{
		switch ($type)
		{
			case 'user':
				$sql = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id . " AND type='user' AND active=1 AND banned=0";
				break;
			
			case 'page':
				$sql = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id . " AND type='page' AND active=1 AND banned=0";
				break;
			
			case 'group':
				$sql = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id . " AND type='group' AND active=1 AND banned=0";
				break;
			
			case 'event':
				$sql = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id . " AND type='event' AND active=1 AND banned=0";
				break;
			
			default:
				$sql = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id=" . $this->id . " AND active=1 AND banned=0";
				break;
		}
		
		$query = $this->getConnection()->query($sql);
		if ($query->num_rows == 1) return true;
		return false;
	}

	public function isBlocked()
	{
		if (!isLogged()) return false;
		global $user;
		$sql = "SELECT id FROM " . DB_BLOCKED_USERS . " WHERE active=1 AND ((blocked_id=" . $this->id  . " AND blocker_id=" . $user['id'] . ") OR (blocker_id=" . $this->id  . " AND blocked_id=" . $user['id'] . "))";
		$query = $this->getConnection()->query($sql);
		if ($query->num_rows == 1) return true;
		return false;
	}

	public function isFollowing($fid=0)
	{
		if (!isLogged()) return false;

		$fid = (int) $fid;

		if ($fid < 1)
		{
			global $user;
			$fid = $user['id'];
		}

		$query = $this->getConnection()->query("SELECT id FROM ". DB_FOLLOWERS ." WHERE follower_id=" . $this->id . " AND following_id=$fid AND active=1");
		
		if ($query->num_rows > 0)
		{
			return true;
		}
	}

	public function isFollowedBy($fid=0)
	{
		if (! isLogged())
		{
			return false;
		}

		$fid = (int) $fid;

		if ($fid < 1)
		{
			global $user;
			$fid = $user['id'];
		}

		$query = $this->getConnection()->query("SELECT id FROM ". DB_FOLLOWERS ." WHERE follower_id=$fid AND following_id=" . $this->id . " AND active=1");
		
		if ($query->num_rows > 0)
		{
			return true;
		}

		return false;
	}

	public function isFollowRequested($fid=0)
	{
		if (! isLogged())
		{
			return false;
		}

		$fid = (int) $fid;

		if ($fid < 1)
		{
			global $user;
			$fid = $user['id'];
		}
		
		$query = $this->getConnection()->query("SELECT id FROM " . DB_FOLLOWERS . " WHERE follower_id=$fid AND following_id=" . $this->id . " AND active=0");
		
		if ($query->num_rows > 0) {
			return true;
		}
	}

	public function isEvent($eventId=0)
	{
		$query = $this->getConnection()->query("SELECT COUNT(id) AS cid FROM " . DB_EVENTS . " WHERE id=" . $this->id);
		$fetch = $query->fetch_array(MYSQLI_ASSOC);

		if ($fetch['cid'] == 1)
		{
			return true;
		}
	}

	public function isAdmin($adminId=0)
	{
		if (! isLogged())
		{
			return false;
		}
		
		$adminId = (int) $adminId;
		
		if ($this->data['type'] == "user")
		{
			if ($adminId < 1)
			{
				global $user;
				$adminId = $user['id'];
			}

			if ($adminId == $this->id)
			{
				return true;
			}
		}
		elseif ($this->data['type'] == "page")
		{
			return $this->isPageAdmin($adminId);
		}
		elseif ($this->data['type'] == "group")
		{
			return $this->isGroupAdmin($adminId);
		}
		elseif ($this->data['type'] == "event")
		{
			return $this->isEventAdmin($adminId);
		}
	}

	public function isPageAdmin($adminId=0)
	{
		if (! isLogged())
		{
			return false;
		}
		
		global $conn, $user;
		$adminId = (int) $adminId;

		if ($adminId < 1)
		{
			$adminId = $user['id'];
		}
		
		$query = $conn->query("SELECT id,role FROM " . DB_PAGE_ADMINS . " WHERE admin_id=$adminId AND page_id=" . $this->id . " AND active=1");
		
		if ($query->num_rows == 1)
		{
			$fetch = $query->fetch_array(MYSQLI_ASSOC);
			return $fetch['role'];
		}
	}

	public function isGroupAdmin($adminId=0) {
		if (! isLogged()) {
			return false;
		}
		
		global $conn, $user;
		$adminId = (int) $adminId;

		if ($adminId < 1) {
			$adminId = $user['id'];
		}
		
		$query = $conn->query("SELECT id FROM " . DB_GROUP_ADMINS . " WHERE admin_id=$adminId AND group_id=" . $this->id . " AND active=1");
		
		if ($query->num_rows == 1) {
			return true;
		}
	}

	public function isEventAdmin($adminId=0) {
		if (! isLogged()) {
			return false;
		}
		
		global $conn, $user;
		$adminId = (int) $adminId;

		if ($adminId < 1) {
			$adminId = $user['id'];
		}
		
		if ($this->data['timeline_id'] == $adminId) {
			return true;
		}
	}

	public function numFollowRequests()
	{
		$query = $this->getConnection()->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
			WHERE id IN (SELECT follower_id FROM " . DB_FOLLOWERS . " WHERE following_id=" . $this->id . " AND follower_id<>" . $this->id . " AND active=0) AND active=1 AND banned=0");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numFollowing() {
		$query = $this->getConnection()->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
			WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id<>" . $this->id . " AND active=1) AND type='user' AND active=1 AND banned=0");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numFollowers()
	{
		$query = $this->getConnection()->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
			WHERE id IN (SELECT follower_id FROM " . DB_FOLLOWERS . " WHERE following_id=" . $this->id . " AND follower_id<>" . $this->id . " AND active=1) AND active=1 AND banned=0");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numPageLikes()
	{
		$query = $this->getConnection()->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
			WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id<>" . $this->id . " AND active=1) AND type='page' AND active=1 AND banned=0");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numGroupsJoined()
	{
		$query = $this->getConnection()->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
			WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id<>" . $this->id . " AND active=1) AND type='group' AND active=1 AND banned=0");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numPageAdmins()
	{
		$query = $this->getConnection()->query("SELECT COUNT(DISTINCT admin_id) AS count FROM " . DB_PAGE_ADMINS . "
			WHERE page_id=" . $this->id . " AND active=1");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numGroupAdmins()
	{
		$query = $this->getConnection()->query("SELECT COUNT(DISTINCT admin_id) AS count FROM " . DB_GROUP_ADMINS . "
			WHERE group_id=" . $this->id . " AND active=1");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numStories()
	{
		$where1 = "timeline_id=" . $this->id . " AND recipient_id=0";
		
		if ($this->data['type'] == "group") {
			$where1 = "recipient_id=" . $this->id;
		}
		
		$query = $this->getConnection()->query("SELECT COUNT(id) AS count FROM " . DB_POSTS . " WHERE $where1 AND hidden=0 AND active=1");
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function numMessages($new=false)
	{
		$queryText = "SELECT COUNT(DISTINCT timeline_id) AS count FROM " . DB_MESSAGES . " WHERE active=1 AND recipient_id=" . $this->id;
		
		if ($new = true)
		{
			$queryText .= " AND seen=0";
		}
		
		$query = $this->getConnection()->query($queryText);
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		
		return $fetch['count'];
	}

	public function setId($id)
	{
		$this->id = (int) $id;
	}

	public function setBanned($boolean=false)
	{
		if ($boolean)
		{
			$this->banned = "0,1";
		}
	}

	private function getVerified() 
	{
		$this->data['verified'] = ($this->data['verified'] == 1) ? true : false;
	}

	private function getAvatar()
	{
		if ($this->data['avatar_id'] > 0)
		{
			$mediaObj = new \SocialKit\Media();
			$this->data['avatar'] = $mediaObj->getById($this->data['avatar_id']);
			$this->data['thumbnail_url'] =  SITE_URL . '/' . $this->data['avatar']['each'][0]['url'] . '_thumb.' . $this->data['avatar']['each'][0]['extension'];
			$this->data['avatar_url'] =  SITE_URL . '/' . $this->data['avatar']['each'][0]['url'] . '_100x100.' . $this->data['avatar']['each'][0]['extension'];
			$this->data['image_url'] =  SITE_URL . '/' . $this->data['avatar']['each'][0]['url'] . '.' . $this->data['avatar']['each'][0]['extension'];
		}
	}

	private function getCover()
	{
		global $config;
		if ($this->data['cover_id'] > 0)
		{
			$mediaObj = new \SocialKit\Media();
			$this->data['cover'] = $mediaObj->getById($this->data['cover_id']);
			$this->data['actual_cover_url'] =  SITE_URL . '/' . $this->data['cover']['each'][0]['url'] . '.' . $this->data['cover']['each'][0]['extension'];
			$this->data['cover_url'] =  SITE_URL . '/' . $this->data['cover']['each'][0]['url'] . '_cover.' . $this->data['cover']['each'][0]['extension'];
		}
		else
		{
			if ($this->data['type'] == "page")
			{
				$this->data['actual_cover_url'] = $this->data['cover_url'] = SITE_URL . '/' . $config['page_default_cover'];
			}
			elseif ($this->data['type'] == "group")
			{
				$this->data['actual_cover_url'] = $this->data['cover_url'] =  SITE_URL . '/' . $config['group_default_cover'];
			}
			elseif ($this->data['type'] == "event")
			{
				$this->data['actual_cover_url'] = $this->data['cover_url'] =  SITE_URL . '/' . $config['event_default_cover'];
			}
			else
			{
				$this->data['actual_cover_url'] = $this->data['cover_url'] =  SITE_URL . '/' . $config['user_default_cover'];
			}
		}
	}

	private function getData()
	{
		global $config;
		$query = $this->getConnection()->query("SELECT * FROM " . DB_USERS . " WHERE id=" . $this->id);
		
		if ($query->num_rows == 1)
		{
			$fetch = $query->fetch_array(MYSQLI_ASSOC);
			
			if (! empty($fetch['birthday']))
			{
				if (!preg_match('/(\-|\/)/', $fetch['birthday'])) $this->getConnection()->query("UPDATE " . DB_USERS . " SET birthday='01/01/1990' WHERE id=" . $this->id);

				$fetch['birth'] = explode('-', $fetch['birthday']);
				if (isset($fetch['birth'][1]))
				{
					$fetch['birth'] = array(
						'date' => (int) $fetch['birth'][0],
						'month' => (int) $fetch['birth'][1],
						'year' => (int) $fetch['birth'][2]
					);
				}
				else
				{
					$fetch['birth'] = explode('/', $fetch['birthday']);
					$fetch['birth'] = array(
						'date' => (int) $fetch['birth'][1],
						'month' => (int) $fetch['birth'][0],
						'year' => (int) $fetch['birth'][2]
					);
				}
			}
			
			if ($this->data['avatar_id'] == 0)
			{
				$fetch['thumbnail_url'] = $fetch['avatar_url'] = SITE_URL . '/' . $config['user_default_male_avatar'];
				
				if (! empty($fetch['gender']))
				{
					if ($fetch['gender'] == "female")
					{
						$fetch['thumbnail_url'] = $fetch['avatar_url'] = SITE_URL . '/' . $config['user_default_female_avatar'];
					}
				}
			}

			$fetch['location'] = $fetch['current_city'];

			return $fetch;
		}
	}

	private function getPageData()
	{
		global $config;
		$query = $this->getConnection()->query("SELECT * FROM " . DB_PAGES . " WHERE id=" . $this->id);
		
		if ($query->num_rows === 1)
		{
			$fetch = $query->fetch_array(MYSQLI_ASSOC);
			
			if ($this->data['avatar_id'] == 0)
			{
				$fetch['thumbnail_url'] = $fetch['avatar_url'] = SITE_URL . '/' . $config['page_default_avatar'];
			}
			
			return $fetch;
		}
	}

	private function getGroupData()
	{
		global $config;
		$query = $this->getConnection()->query("SELECT * FROM " . DB_GROUPS . " WHERE id=" . $this->id);
		
		if ($query->num_rows === 1)
		{
			$fetch = $query->fetch_array(MYSQLI_ASSOC);
			$fetch['thumbnail_url'] = $fetch['avatar_url'] = SITE_URL . '/' . $config['group_default_avatar'];
			
			return $fetch;
		}
	}

	private function getEventData()
	{
		global $config;
		$query = $this->getConnection()->query("SELECT * FROM " . DB_EVENTS . " WHERE id=" . $this->id);
		
		if ($query->num_rows === 1)
		{
			$fetch = $query->fetch_array(MYSQLI_ASSOC);
			$fetch['thumbnail_url'] = $fetch['avatar_url'] = SITE_URL . '/' . $config['event_default_avatar'];
			
			return $fetch;
		}
	}

	private function getNames() {
		$nameBreak = explode(' ', $this->data['name']);
		$this->data['first_name'] = $nameBreak[0];
		$this->data['last_name'] = $nameBreak[count($nameBreak)-1];
	}

	private function getOnline()
	{
		$this->data['online'] = false;
		if ($this->data['type'] !== "user") return false;
		if ($this->data['go_offline'] == 1)
		{
			$this->data['online'] = false;
			return false;
		}
		$this->data['online'] = (isLogged() && $this->data['last_logged'] > (time()-30)) ? true : false;
		$this->data['last_logged_time'] = date('c', $this->data['last_logged']);
	}

	public function getSubscriptionPlan()
	{
		if ($this->data['type'] !== "user") return false;

		global $config;
		$sqlQuery = $this->getConnection()->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=" . $this->data['subscription_plan']);
		$sqlData = $sqlQuery->fetch_array(MYSQLI_ASSOC);
		$sqlData['plan_icon'] = $config['site_url'] . '/' . $sqlData['plan_icon'];
		$this->data['subscription_plan'] = $sqlData;
		return true;
	}

	public function getFollowRequests($searchQ='', $beforeId=0, $all=false, $order="")
	{
		$beforeId = (int) $beforeId;
		$get = array();
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT follower_id FROM " . DB_FOLLOWERS . " WHERE follower_id<>" . $this->id . " AND following_id=" . $this->id . " AND active=0) AND type='user' AND active=1";

		if ($beforeId > 0)
		{
			$queryText .= " AND id<$beforeId";
		}

		if (! empty($searchQ))
		{
			$queryText .= " AND name LIKE '%$searchQ%'";
		}

		if (empty($order))
		{
			$order = "id DESC";
		}

		$queryText .= " ORDER BY $order";

		if (!$all)
		{
			$queryText .= " LIMIT 5";
		}

		$query = $this->getConnection()->query($queryText);
		
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
		{
			$get[] = $fetch['id'];
		}
		
		return $get;
	}

	public function getFollowings($searchQ='', $beforeId=0, $all=false, $order="", $limit=15)
	{
		$limit = (int) $limit;
		$beforeId = (int) $beforeId;
		$get = array();
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id<>" . $this->id . " AND active=1) AND type='user' AND active=1 AND banned=0";

		if ($beforeId > 0) $queryText .= " AND id<$beforeId";
		if (! empty($searchQ))
		{
			if (preg_match('/^\@/', $searchQ))
			{
				$searchQ = str_replace('@', '', $searchQ);
				$queryText .= " AND username='$searchQ'";
			}
			else
			{
				$queryText .= " AND (name LIKE '%$searchQ%' OR email='$searchQ')";
			}
		}
		if (empty($order)) $order = "id DESC";
		if ($limit < 1) $limit = 5;

		$queryText .= " ORDER BY $order";

		if (!$all) $queryText .= " LIMIT $limit";

		$query = $this->getConnection()->query($queryText);
		
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC)) $get[] = $fetch['id'];
		return $get;
	}

	public function getFollowers($searchQ='', $beforeId=0, $all=false, $order="", $limit=15)
	{
		$limit = (int) $limit;
		$beforeId = (int) $beforeId;
		$get = array();
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT follower_id FROM " . DB_FOLLOWERS . " WHERE follower_id<>" . $this->id . " AND following_id=" . $this->id . " AND active=1) AND type='user' AND active=1 AND banned=0";
		
		if ($beforeId > 0) $queryText .= " AND id<$beforeId";
		if (! empty($searchQ))
		{
			if (preg_match('/^\@/', $searchQ))
			{
				$searchQ = str_replace('@', '', $searchQ);
				$queryText .= " AND username='$searchQ'";
			}
			else
			{
				$queryText .= " AND (name LIKE '%$searchQ%' OR email='$searchQ')";
			}
		}
		if (empty($order)) $order = "id DESC";
		if ($limit < 1) $limit = 5;

		$queryText .= " ORDER BY $order";
		
		if (!$all) $queryText .= " LIMIT $limit";

		$query = $this->getConnection()->query($queryText);
		
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC)) $get[] = $fetch['id'];
		return $get;
	}

	public function getLikedPages($searchQ='', $beforeId=0, $all=false, $order="", $limit=15)
	{
		$limit = (int) $limit;
		$beforeId = (int) $beforeId;
		$get = array();
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id<>" . $this->id . " AND active=1) AND type='page' AND active=1 AND banned=0";
		
		if ($beforeId > 0)
		{
			$queryText .= " AND id<$beforeId";
		}

		if (! empty($searchQ))
		{
			$queryText .= " AND name LIKE '%$searchQ%'";
		}

		if (empty($order))
		{
			$order = "id DESC";
		}

		$queryText .= " ORDER BY $order";
		
		if (!$all)
		{
			$queryText .= " LIMIT $limit";
		}

		$query = $this->getConnection()->query($queryText);
		
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
		{
			$get[] = $fetch['id'];
		}
		
		return $get;
	}

	public function getGroupsJoined($searchQ='', $beforeId=0, $all=false, $order="", $limit=15)
	{
		$limit = (int) $limit;
		$beforeId = (int) $beforeId;
		$get = array();
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id<>" . $this->id . " AND active=1) AND type='group' AND active=1 AND banned=0";
		
		if ($beforeId > 0)
		{
			$queryText .= " AND id<$beforeId";
		}

		if (! empty($searchQ))
		{
			$queryText .= " AND name LIKE '%$searchQ%'";
		}

		if (empty($order))
		{
			$order = "id DESC";
		}

		$queryText .= " ORDER BY $order";
		
		if (!$all)
		{
			$queryText .= " LIMIT $limit";
		}

		$query = $this->getConnection()->query($queryText);
		
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
		{
			$get[] = $fetch['id'];
		}
		
		return $get;
	}

	public function getPageAdmins($searchQ='', $beforeId=0, $all=false, $limit=15)
	{
		$limit = (int) $limit;
		$beforeId = (int) $beforeId;
		$get = false;
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT admin_id FROM " . DB_PAGE_ADMINS . " WHERE page_id=" . $this->id . " AND active=1) AND active=1 AND banned=0";
		
		if ($beforeId > 0)
		{
			$queryText .= " AND id<$beforeId";
		}

		if (! empty($searchQ))
		{
			$queryText .= " AND name LIKE '%$searchQ%'";
		}

		$queryText .= " ORDER BY id DESC";
		
		if (!$all)
		{
			$queryText .= " LIMIT $limit";
		}

		$query = $this->getConnection()->query($queryText);
		
		if ($query->num_rows > 0)
		{
			$get = array();

			while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
			{
				$get[] = $fetch['id'];
			}
		}
		
		return $get;
	}

	public function getGroupAdmins($searchQ='', $beforeId=0, $all=false, $limit=15)
	{
		$limit = (int) $limit;
		$beforeId = (int) $beforeId;
		$get = array();
		$queryText = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT admin_id FROM " . DB_GROUP_ADMINS . " WHERE group_id=" . $this->id . " AND active=1) AND active=1 AND banned=0";
		
		if ($beforeId > 0)
		{
			$queryText .= " AND id<$beforeId";
		}

		if (! empty($searchQ))
		{
			$queryText .= " AND name LIKE '%$searchQ%'";
		}

		$queryText .= " ORDER BY id DESC";
		
		if (!$all)
		{
			$queryText .= " LIMIT $limit";
		}

		$query = $this->getConnection()->query($queryText);
		
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
		{
			$get[] = $fetch['id'];
		}
		
		return $get;
	}

	public function getMessageRecipients($searchQuery='', $new=false)
	{
	    $searchQuery = $this->escapeObj->stringEscape($searchQuery);
	    $get = array();
	    $excludes = array();
	    $excludesNum = 0;
	    
	    if (! empty($searchQuery))
	    {
	        $queryText = "SELECT DISTINCT id FROM " . DB_ACCOUNTS . " WHERE (id IN (SELECT timeline_id FROM " . DB_MESSAGES . " WHERE recipient_id=" . $this->id . " AND active=1 AND banned=0";
	        
	        if ($new == true)
	        {
	            $queryText .= " AND seen=0";
	        }
	        
	        $queryText .= " ORDER BY id DESC)";
	        
	        if ($new == false)
	        {
	            $queryText .= " OR id IN (SELECT recipient_id FROM " . DB_MESSAGES . " WHERE timeline_id=" . $this->id . " AND active=1 ORDER BY id DESC)";
	        }
	        
	        $queryText .= ") AND id<>" . $this->id . " AND active=1 AND name LIKE '%$searchQuery%'";
	    }
	    else
	    {
	        $queryText = "SELECT DISTINCT id FROM " . DB_ACCOUNTS . " WHERE (id IN (SELECT timeline_id FROM " . DB_MESSAGES . " WHERE recipient_id=" . $this->id . " AND active=1 AND banned=0";
	        
	        if ($new == true)
	        {
	            $queryText .= " AND seen=0";
	        }
	        
	        $queryText .= " ORDER BY id DESC)";
	        
	        if ($new == false)
	        {
	            $queryText .= " OR id IN (SELECT recipient_id FROM " . DB_MESSAGES . " WHERE timeline_id=" . $this->id . " AND active=1 ORDER BY id DESC)";
	        }
	        
	        $queryText .= ") AND active=1";
	    }

	    $query = $this->getConnection()->query($queryText);
	    
	    if ($query->num_rows > 0)
	    {
	        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
	        {
	            $timelineObj = new \SocialKit\User();
	            $timelineObj->setId($fetch['id']);
	            $get[] = $timelineObj->getRows();
	            $excludes[] = $fetch['id'];
	            $excludesNum++;
	        }
	    }
	    
	    $excludeQueryString = 0;
	    $exclude_i = 0;
	    
	    if ($excludesNum > 0)
	    {
	        $excludeQueryString = '';
	        
	        foreach ($excludes as $exclude)
	        {
	            $exclude_i++;
	            $excludeQueryString .= $exclude;
	            
	            if ($exclude_i != $excludesNum)
	            {
	                $excludeQueryString .= ',';
	            }
	        }
	    }
	    
	    $query2Text = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id NOT IN (" . $this->id . ",$excludeQueryString) AND following_id IN (SELECT id FROM " . DB_USERS . ") AND active=1) AND active=1 AND banned=0";
	    
	    if (! empty($searchQuery))
	    {
	        $query2Text .= " AND name LIKE '%$searchQuery%'";
	    }
	    
	    $query2 = $this->getConnection()->query($query2Text);
	    
	    while ($fetch2 = $query2->fetch_array(MYSQLI_ASSOC))
	    {
	        $timelineObj = new \SocialKit\User();
	        $timelineObj->setId($fetch2['id']);
	        $get[] = $timelineObj->getRows();
	    }
	    
	    return $get;
	}

	public function getRawAbout()
	{
		$rawAbout = $this->data['about'];

		$rawAbout = $this->escapeObj->reversePostEscape($rawAbout);
	    $rawAbout = $this->escapeObj->reverseLinks($rawAbout);
	    $rawAbout = $this->escapeObj->reverseHashtags($rawAbout);
	    $rawAbout = $this->escapeObj->reverseMentions($rawAbout);

		return $rawAbout;
	}

	public function putFollow($followingId=0)
	{
		if (!isLogged()) return false;
		
		global $config, $lang;
		
		$followingId = (int) $followingId;
		
		if ($followingId == 0)
	    {
	    	global $user;
	        $followingId = $user['id'];
	        $following = $user;
	    }
	    else
	    {
	        $followingObj = new \SocialKit\User();
	        $followingObj->setId($followingId);
	        $following = $followingObj->getRows();
	    }
		
		if (!isset($following['id'])) return false;
		
		if ($this->isFollowedBy($followingId)) return false;

		$active = 1;
		$canFollow = true;
		
		if ($this->data['type'] == "user"
			&& $this->data['follow_privacy'] == "following"
			&& ! $this->isFollowing())
		{
			$canFollow = false;
		}
		
		if ($this->data['type'] == "user" && $this->data['confirm_followers'] == 1)
		{
			$active = 0;
		}

		if ($config['friends'] == true)
		{
			$active = 0;
		}

		if ($this->data['type'] == "page")
		{
			$active = 1;
		}

		if ($this->data['type'] == "group")
		{
			if ($this->data['group_privacy'] == "open")
			{
				$active = 1;
			}
			elseif ($this->data['group_privacy'] == "closed")
			{
				if ($this->isGroupAdmin())
				{
					$active = 1;
				}
				else
				{
					$active = 0;
				}
			}
			elseif ($this->data['group_privacy'] == "secret")
			{
				if ($this->isGroupAdmin())
				{
					$active = 1;
				}
				else
				{
					return false;
				}
			}
		}
		
		if ($canFollow == true)
		{
			$query = $this->getConnection()->query("INSERT INTO " . DB_FOLLOWERS . " (active,follower_id,following_id,time) VALUES ($active,$followingId," . $this->id . "," . time() . ")");
			
			if ($query)
			{
				if ($following['type'] == "user")
				{
					if ($this->data['type'] == "user")
					{
						if ($active == 1)
						{
							$this->putNotification('follow', $followingId);

							if ($this->data['mailnotif_follow'] == true)
							{
								global $themeData;
							    $themeData['followers_url'] = smoothLink('index.php?a=timeline&b=followers&id=' . $this->data['username']);
							    $themeData['mail_recipient_name'] = $this->data['name'];
							    $themeData['mail_generator_url'] = $following['url'];
							    $themeData['mail_generator_name'] = $following['name'];
							    $themeData['mail_generator_avatar'] = $following['thumbnail_url'];
							    
							    $subject = str_replace("{user}", $following['name'] . " (@" . $following['username'] . ")", $lang['new_follower_email_subject']);

							    $message = \SocialKit\UI::view('emails/notifications/new-follower');
							    sendMail($this->data['email'], $subject, $message);
							}
						}
						elseif ($config['friends'] == true && $this->data['mailnotif_friendrequests'] == true)
						{
							global $themeData;
						    $themeData['friend_requests_url'] = smoothLink('index.php?a=timeline&b=requests&id=' . $this->data['username']);
						    $themeData['mail_recipient_name'] = $this->data['name'];
						    $themeData['mail_generator_url'] = $following['url'];
						    $themeData['mail_generator_name'] = $following['name'];
						    $themeData['mail_generator_avatar'] = $following['thumbnail_url'];
						    
						    $subject = str_replace("{user}", $following['name'] . " (@" . $following['username'] . ")", $lang['new_friend_request_email_subject']);

						    $message = \SocialKit\UI::view('emails/notifications/new-friend-request');
						    
						    sendMail($this->data['email'], $subject, $message);
						}
					}
					elseif ($this->data['type'] == "page")
					{
						global $themeData;
						$themeData['page_url'] = $this->data['url'];
						$themeData['page_name'] = $this->data['name'];
						$pageAdmins = $this->getPageAdmins();

						if (is_array($pageAdmins))
						{
							foreach ($this->getPageAdmins() as $adminId)
							{
								if ($adminId != $followingId)
								{
									$pageAdminObj = new \SocialKit\User();
									$pageAdminObj->setId($adminId);
									$pageAdmin = $pageAdminObj->getRows();

									$this->putNotification('pagelike', $followingId, $adminId);

									if ($pageAdmin['mailnotif_pagelike'] == true)
									{
										$subject = str_replace("{user}", $following['name'] . " (@" . $following['username'] . ")", $lang['new_pagelike_email_subject']);

										$themeData['mail_recipient_name'] = $pageAdmin['name'];
							    		$themeData['mail_generator_url'] = $following['url'];
							    		$themeData['mail_generator_name'] = $following['name'];
							    		$themeData['mail_generator_avatar'] = $following['thumbnail_url'];

										$message = \SocialKit\UI::view('emails/notifications/new-page-like');
							    		sendMail($pageAdmin['email'], $subject, $message);
									}
								}
							}
						}
					}
					elseif ($this->data['type'] == "group")
					{
						global $themeData;
						$themeData['group_url'] = $this->data['url'];
						$themeData['group_name'] = $this->data['name'];

						foreach ($this->getGroupAdmins() as $adminId)
						{
							if ($adminId != $followingId)
							{
								$groupAdminObj = new \SocialKit\User();
								$groupAdminObj->setId($adminId);
								$groupAdmin = $groupAdminObj->getRows();

								if ($active == 1)
						    	{
									$this->putNotification('groupjoin', $followingId, $adminId);
								}
								else
								{
									$this->putNotification('grouprequest', $followingId, $adminId);
								}

								if ($groupAdmin['mailnotif_groupjoined'] == true)
								{
									$subject = str_replace("{user}", $following['name'] . " (@" . $following['username'] . ")", $lang['new_groupmember_email_subject']);

									$themeData['mail_recipient_name'] = $groupAdmin['name'];
						    		$themeData['mail_generator_url'] = $following['url'];
						    		$themeData['mail_generator_name'] = $following['name'];
						    		$themeData['mail_generator_avatar'] = $following['thumbnail_url'];

						    		if ($active == 1)
						    		{
						    			$message = \SocialKit\UI::view('emails/notifications/new-group-member');
						    		}
						    		else
						    		{
						    			$message = \SocialKit\UI::view('emails/notifications/new-group-request');
						    			
						    			$subject = $config['site_name'];
										$subject .= " | ";
										$subject .= str_replace("{user}", $following['name'] . " (@" . $following['username'] . ")", $lang['new_grouprequest_email_subject']);
						    		}
									
						    		sendMail($groupAdmin['email'], $subject, $message);
								}
							}
						}
					}
				}
				
				return true;
			}
		}
	}

	public function sendMessage($text="", $file=array(), $senderId=0)
	{
	    if (! isLogged()) // If user is not logged
	    {
	        return false; // Stop
	    }
	    
	    global $config, $lang, $user;
	    $continue = false;
	    $senderId = (int) $senderId;

	    if ($user['subscription_plan']['send_messages'] == 0) return false;

	    if ($senderId > 0) // If Sender ID is set to a positive integer more than 0, fetch information of Sender
	    {
	    	$senderObj = new \SocialKit\User();
	        $senderObj->setId($senderId);
	        $sender = $senderObj->getRows();
	    }
	    else // Else fetch information of logged in User as Sender
	    {
	    	global $userObj;
	        $senderId = $user['id'];
	        $senderObj = $userObj;
	        $sender = $user;
	    }

	    if ($sender['type'] === "group") // If Sender is Group 
	    {
	        return false; // Stop
	    }

	    if (! $senderObj->isAdmin()) // If Sender is neither User, nor Admin of Page or Group
	    {
	        return false; // Stop
	    }

	    if ($senderId == $this->id) // If Sender and Receiver are same
	    {
	        return false; // Stop
	    }

	    if ($this->isBlocked()) return false;
	    
	    // Check message privacy
	    if ($this->data['type'] == "user" && $sender['type'] == "user") // If Receiver and Sender are both Users
	    {
	        if ($this->data['message_privacy'] == "following" && !$recipientObj->isFollowing()) // If message privacy of Receiver is set to Followings/Friends & Sender is not following Receiver
	        {
	            return false; // Stop
	        }
	    }
	    elseif ($this->data['type'] == "page") // If Receiver is Page
	    {
	        if ($this->data['message_privacy'] == "none") // If message privacy of Receiver is set to No One
	        {
	            return false; // Stop
	        }
	    }
	    elseif ($this->data['type'] == "group" or $this->data['type'] == "event") // If Receiver is Group/Event
	    {
	        return false; // Stop
	    }
	    
	    $mediaId = 0;
	    
	    if (!empty($text)) // If text is empty
	    {
	        if ($config['message_character_limit'] > 0) // If maximum Message Word Limit is set to Unlimited
	        {
	            if (strlen($text) > $config['message_character_limit']) // If text length is more than Message Word Limit
	            {
	                return false; // Stop
	            }
	        }

	        // Create links
	        $text = $this->escapeObj->createLinks($text);

	        // Create hashtags
	        $text = $this->escapeObj->createHashtags($text);

	        // Create mentions
	        $mentions = $this->escapeObj->createMentions($text);
	        $text = $mentions['content'];
	        
	        // Encode every non-alphabetic and non-numeric characters
	        $text = $this->escapeObj->postEscape($text);

	        // Set continue to true
	        $continue = true;

	        if (empty($text)) $continue = false;
	    }
	    
	    if (isset($file['name']))
	    {
	    	$registerMediaObj = new \SocialKit\registerMedia();
	        $registerMediaObj->setFile($file);
	        $registerMediaObj->setTimeline($senderId);
	        $registerMedia = $registerMediaObj->register();

	        if (isset($registerMedia[0]))
	        {
	        	$mediaId = $registerMedia[0]['id'];
	            $continue = true;
	        }
	    }
	    
	    if ($continue)
	    {
	        $query = $this->getConnection()->query("INSERT INTO " . DB_MESSAGES . " (active,media_id,text,time,timeline_id,recipient_id) VALUES (1,$mediaId,'$text'," . time() . ",$senderId," . $this->id . ")");

	        if ($query)
	        {
	            $messageId = $this->getConnection()->insert_id;
	            
	            /* E-mail notification */
	            if ($this->data['type'] == "user")
	            {
	                if ($this->data['mailnotif_message'] == true)
	                {
	                    global $themeData;
	                    $themeData['conversation_url'] = smoothLink('index.php?a=messages&recipient_id=' . $sender['username']);
	                    $themeData['mail_recipient_name'] = $this->data['name'];
	                    $themeData['mail_generator_url'] = $sender['url'];
	                    $themeData['mail_generator_name'] = $sender['name'];
	                    $themeData['mail_generator_avatar'] = $sender['thumbnail_url'];
	                    
	                    $subject = str_replace("{user}", $sender['name'] . " (@" . $sender['username'] . ")", $lang['new_message_email_subject']);

	                    $message = \SocialKit\UI::view('emails/notifications/new-message');
	                    sendMail($this->data['email'], $subject, $message);
	                }
	            }
	            
	            return $messageId;
	        }
	    }

	    return false;
	}

	public function putNotification($a='')
    {
        if (! isLogged())
        {
            return false;
        }
        
        $lang = array();
		$langQuery = $this->getConnection()->query("SELECT keyword,text FROM " . DB_LANGUAGES . " WHERE type='" . $this->data['language'] . "'");
		
		while($langFetch = $langQuery->fetch_array(MYSQLI_ASSOC))
		{
			$lang[$langFetch['keyword']] = $langFetch['text'];
		}

        global $user;

        if ($a == "follow")
        {
        	$followingId = (int) func_get_arg(1);

	        if ($followingId < 1)
	        {
	        	return false;
	        }

	        $count = $this->numFollowers();
	        $text = '';
	        
	        if ($count > 1)
	        {
	            $text .= str_replace('{count}', ($count-1), $lang['following_you_plural']);
	        }
	        else
	        {
	            $text .= $lang['following_you_singular'];
	        }
	        
	        $query = $this->getConnection()->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $this->id . " AND notifier_id=$followingId AND post_id=0 AND type='following' AND active=1");
	        
	        if ($query->num_rows > 0)
	        {
	            $this->getConnection()->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $followingId . " AND notifier_id=$followingId AND post_id=0 AND type='following' AND active=1");
	        }

	        $this->getConnection()->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES (" . $this->id . ",1," . $followingId . ",0,'$text'," . time() . ",'following','index.php?a=timeline&b=followers&id=" . $this->id . "')");
	        return true;
        }
        elseif ($a == "pagelike")
        {
        	$followingId = (int) func_get_arg(1);
        	$adminId = (int) func_get_arg(2);

	        if ($followingId < 1)
	        {
	        	return false;
	        }

	        if ($adminId < 1)
	        {
	        	return false;
	        }

	        $followingObj = new \SocialKit\User();
	        $followingObj->setId($followingId);
	        $following = $followingObj->getRows();

	        $adminObj = new \SocialKit\User();
	        $adminObj->setId($adminId);
	        $admin = $adminObj->getRows();

	        $text = $lang['liked_your_page'];
	        $text = str_replace('{user}', '', $text);
	        $text = str_replace('{page}', $this->data['name'], $text);

	        $query = $this->getConnection()->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$adminId AND notifier_id=$followingId AND post_id=0 AND type='pagelike' AND active=1");
	        
	        if ($query->num_rows > 0)
	        {
	            $this->getConnection()->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$adminId AND notifier_id=$followingId AND post_id=0 AND type='pagelike' AND active=1");
	        }

	        $this->getConnection()->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES ($adminId,1,$followingId,0,'$text'," . time() . ",'pagelike','index.php?a=timeline&id=" . $this->id . "')");
	        return true;
        }
        elseif ($a == "groupjoin")
        {
        	$followingId = (int) func_get_arg(1);
        	$adminId = (int) func_get_arg(2);
        	
        	if ($followingId < 1)
	        {
	        	return false;
	        }

	        if ($adminId < 1)
	        {
	        	return false;
	        }

	        $followingObj = new \SocialKit\User();
	        $followingObj->setId($followingId);
	        $following = $followingObj->getRows();

	        $adminObj = new \SocialKit\User();
	        $adminObj->setId($adminId);
	        $admin = $adminObj->getRows();

	        $text = $lang['joined_your_group'];
	        $text = str_replace('{user}', "", $text);
	        $text = str_replace('{group}', $this->data['name'], $text);

	        $query = $this->getConnection()->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$adminId AND notifier_id=$followingId AND post_id=0 AND type='groupjoin' AND active=1");
	        
	        if ($query->num_rows > 0)
	        {
	            $this->getConnection()->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$adminId AND notifier_id=$followingId AND post_id=0 AND type='groupjoin' AND active=1");
	        }

	        $this->getConnection()->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES ($adminId,1,$followingId,0,'$text'," . time() . ",'groupjoin','index.php?a=timeline&id=" . $this->id . "')");
	        return true;
        }
        elseif ($a == "grouprequest")
        {
        	$followingId = (int) func_get_arg(1);
        	$adminId = (int) func_get_arg(2);

	        if ($followingId < 1)
	        {
	        	return false;
	        }

	        if ($adminId < 1)
	        {
	        	return false;
	        }

	        $followingObj = new \SocialKit\User();
	        $followingObj->setId($followingId);
	        $following = $followingObj->getRows();

	        $adminObj = new \SocialKit\User();
	        $adminObj->setId($adminObj);
	        $admin = $adminObj->getRows();

	        $text = $lang['requested_to_join_your_group'];
	        $text = str_replace('{user}', "", $text);
	        $text = str_replace('{group}', $this->data['name'], $text);

	        $query = $this->getConnection()->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$adminId AND notifier_id=$followingId AND post_id=0 AND type='grouprequest' AND active=1");
	        
	        if ($query->num_rows > 0)
	        {
	            $this->getConnection()->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$adminId AND notifier_id=$followingId AND post_id=0 AND type='grouprequest' AND active=1");
	        }

	        $this->getConnection()->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES ($adminId,1,$followingId,0,'$text'," . time() . ",'grouprequest','index.php?a=timeline&id=" . $this->id . "')");
	        return true;
        }
    }

    public function removeFollow($fid=0)
	{
	    if (! isLogged())
	    {
	        return false;
	    }
	    
	    global $config;
	    $fid = (int) $fid;

	    if ($fid < 1)
	    {
	        global $user, $userObj;
	        $fid = $user['id'];
	        $followingObj = $userObj;
	        $following = $user;
	    }
	    else
	    {
	        $followingObj = new \SocialKit\User();
	        $followingObj->setId($fid);
	        $following = $followingObj->getRows();
	    }

	    $active = 1;
	    
	    if (! isset($following['id']))
	    {
	        return false;
	    }
	    
	    if ($this->data['type'] == "user" && $this->data['confirm_followers'] == 1)
	    {
	        $active = 0;
	    }
	    elseif ($this->data['type'] == "group" && $this->data['group_privacy'] == "closed")
	    {
	        $active = 0;
	    }
	    
	    $query = $this->getConnection()->query("DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=$fid AND following_id=" . $this->id);

	    if ($config['friends'] == true)
	    {
	        $query2 = $this->getConnection()->query("DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $this->id . " AND following_id=$fid");
	    }
	    
	    if ($this->data['type'] == "group" && $this->isGroupAdmin())
	    {
	        if ($this->numGroupAdmins() > 1)
	        {
	            $query3 = $this->getConnection()->query("DELETE FROM " . DB_GROUP_ADMINS . " WHERE admin_id=$fid AND group_id=" . $this->id);
	        }
	    }
	    
	    return true;
	}

	/* Template methods */
	public function getFollowButton()
	{
		if (! isLogged())
		{
			return false;
		}
		
		global $user, $config, $themeData;
		$themeData['follow_id'] = $this->id;

		if ($this->id == $user['id'])
		{
			return false;
		}
		
		if ($config['friends'] == true)
		{
			switch ($this->data['type'])
			{
				case 'user':
					$follow_button = 'global/buttons/add_as_friend';
					$unfollow_button = 'global/buttons/unfriend';
					$request_button = 'global/buttons/request-sent';
				break;
				
				case 'page':
					$follow_button = 'global/buttons/like';
					$unfollow_button = 'global/buttons/unlike';
					$request_button = 'global/buttons/request-sent';
				break;
				
				case 'group':
					$follow_button = 'global/buttons/join';
					$unfollow_button = 'global/buttons/leave';
					$request_button = 'global/buttons/request-sent';
				break;
			}
		}
		else
		{
			switch ($this->data['type'])
			{
				case 'user':
					$follow_button = 'global/buttons/follow';
					$unfollow_button = 'global/buttons/unfollow';
					$request_button = 'global/buttons/request-sent';
				break;
				
				case 'page':
					$follow_button = 'global/buttons/like';
					$unfollow_button = 'global/buttons/unlike';
					$request_button = 'global/buttons/request-sent';
				break;
				
				case 'group':
					$follow_button = 'global/buttons/join';
					$unfollow_button = 'global/buttons/leave';
					$request_button = 'global/buttons/request-sent';
				break;
			}
		}
		
		if ($this->isFollowedBy())
		{
			return \SocialKit\UI::view($unfollow_button);
		}
		else
		{
			if ($this->isFollowRequested())
			{
				return \SocialKit\UI::view($request_button);
			}
			else
			{
				if ($this->data['type'] == "user")
				{
					if ($this->data['follow_privacy'] == "following")
					{
						if ($this->isFollowedBy())
						{
							return \SocialKit\UI::view($follow_button);
						}
					}
					elseif ($this->data['follow_privacy'] == "everyone")
					{
						return \SocialKit\UI::view($follow_button);
					}
				}
				elseif ($this->data['type'] == "page")
				{
					return \SocialKit\UI::view($follow_button);
				}
				elseif ($this->data['type'] == "group")
				{
					return \SocialKit\UI::view($follow_button);
				}
			}
		}
	}

	public function getAlbums($limit=0)
	{
		global $config;
		$limit = (int) $limit;
		$get = array();
		$queryText = "SELECT id,name,descr FROM " . DB_MEDIA . " WHERE timeline_id=" . $this->id . " AND type='album' AND temp=0 AND active=1";

		if ($limit > 0)
		{
			$queryText .= " LIMIT $limit";
		}

		$query = $this->getConnection()->query($queryText);

		while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
		{
			$previewQuery = $this->getConnection()->query("SELECT id FROM " . DB_MEDIA . " WHERE album_id=" . $fetch['id'] . " AND type='photo' AND active=1 ORDER BY RAND() LIMIT 1");
			if ($previewQuery->num_rows == 1)
			{
				$previewFetch = $previewQuery->fetch_array(MYSQLI_ASSOC);
				$previewId = $previewFetch['id'];
				$previewImageObj = new \SocialKit\Media();
				$previewImageObj->setId($previewId);
				$previewImage = $previewImageObj->getRows();
				$fetch['preview'] = $config['site_url'] . '/' . $previewImage['each'][0]['url'] . '_100x100.' . $previewImage['each'][0]['extension'];
			}
			$get[] = $fetch;
		}

		return $get;
	}

	public function getAlbumsTemplate()
	{
		global $themeData;
		$listAlbums = '';
		
		foreach ($this->getAlbums() as $album)
		{
			$themeData['list_album_id'] = $album['id'];
			$themeData['list_album_url'] = smoothLink('index.php?a=album&b=' . $album['id']);
			$themeData['list_album_preview'] = $album['preview'];
			$themeData['list_album_name'] = $album['name'];

			$listAlbums .= \SocialKit\UI::view('timeline/user/album-li');
		}

		if (empty($listAlbums)) return false;
		
		$themeData['list_albums'] = $listAlbums;
		return \SocialKit\UI::view('timeline/user/sidebar-albums');
	}

	public function getPhotos()
	{
		$privacy = array();
		$privacy[] = "'public'";

		if (isLogged())
		{
			global $user;

			if ($this->isFollowing()
				|| $this->id == $user['id'])
			{
				$privacy[] = "'friends'";
			}
		}

		$get = array();
		$query = $this->conn->query("SELECT id,media_id FROM " . DB_POSTS . " WHERE timeline_id=" . $this->id . " AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='photo' AND active=1) AND privacy IN (" . implode(',', $privacy) . ") AND active=1 ORDER BY id DESC LIMIT 9");
		while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
		{
			$mediaObj = new \SocialKit\Media();
			$media = $mediaObj->getById($fetch['media_id']);

			$get[] = array(
				'url' => SITE_URL . '/' . $media['each'][0]['url'] . '_100x100.' . $media['each'][0]['extension'],
				'post_id' => $fetch['id'],
				'post_url' => smoothLink('index.php?a=story&id=' . $fetch['id'])
			);
		}

		return $get;
	}

	public function getPhotosTemplate()
	{
		global $themeData;
		$photos = $this->getPhotos();
		$listPhotos = '';

		foreach ($photos as $value)
		{
			$themeData['photo_url'] = $value['url'];
			$themeData['photo_post_id'] = $value['post_id'];
			$listPhotos .= \SocialKit\UI::view('timeline/user/each-photo');
		}
		
		if (empty($listPhotos)) return false;
		
		$themeData['list_photos'] = $listPhotos;
		return \SocialKit\UI::view('timeline/user/sidebar-photos');
	}

	public function getInvitesTemplate($i='')
	{
		global $themeData;

		$listInvites = $this->getInviteListTemplate($i);

        $themeData['list_inviteactions'] = $listInvites;
        return \SocialKit\UI::view('timeline/event/view-inviteactions');
	}

	public function getInviteListTemplate($inv='')
	{
		global $themeData;
		
		$i = 0;
		$listInvites = '';

        foreach ($this->getEventInvites($inv) as $inviteUserId)
        {
        	$invitedObj = new \SocialKit\User();
        	$invitedObj->setId($inviteUserId);
        	$invitedUser = $invitedObj->getRows();

        	$themeData['list_user_id'] = $invitedUser['id'];
            $themeData['list_user_url'] = $invitedUser['url'];
            $themeData['list_user_username'] = $invitedUser['username'];
            $themeData['list_user_name'] = $invitedUser['name'];
            $themeData['list_user_thumbnail_url'] = $invitedUser['thumbnail_url'];

            $themeData['list_user_button'] = $invitedObj->getFollowButton();

            $listInvites .= \SocialKit\UI::view('timeline/event/list-inviteaction-each');
            $i++;
        }

        if ($i < 1)
        {
            $listInvites .= \SocialKit\UI::view('story/view-reactions-none');
        }

        return $listInvites;
	}

	public function getEventInvites($i='all')
	{
		$get = array();
		if (preg_match('/(going|interested|invited)/', $i))
		{
			$query = $this->getConnection()->query("SELECT id,timeline_id FROM " . DB_EVENT_INVITES . " WHERE event_id=" . $this->id . " AND action='$i' AND active=1");
		}
		else
		{
			$query = $this->getConnection()->query("SELECT id,timeline_id FROM " . DB_EVENT_INVITES . " WHERE event_id=" . $this->id . " AND active=1");
		}
	    
	    if ($query->num_rows > 0)
	    {
	        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
	        {
	        	$get[] = $fetch['timeline_id'];
	        }
	    }

	    return $get;
	}

	public function getDeleteTemplate()
	{
		global $themeData;
		return \SocialKit\UI::view('timeline/event/view-delete');
	}
}
