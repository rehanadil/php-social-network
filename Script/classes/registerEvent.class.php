<?php

namespace SocialKit;

class registerEvent
{
	private $conn;
	private $escapeObj;
	private $id;

	private $name;
	private $username;
	private $location;
	private $description;
	private $startTime;
	private $endTime;
	private $privacy;
	private $email;
	private $validPrivacies = array(0, 1);

	function __construct()
	{
		global $conn;
		$this->conn = $conn;
		$this->escapeObj = new \SocialKit\Escape();
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

	public function register()
	{
		if (!isLogged()) return false;
		global $user;
		if ($user['subscription_plan']['create_events'] == 0) return false;

		if (! empty($this->name) && ! empty($this->username) && ! empty($this->location) && ! empty($this->description) && ! empty($this->startTime) && ! empty($this->endTime) && isset($this->private))
		{
			$this->email = $this->username . '@' . $_SERVER['HTTP_HOST'];
			$query = $this->getConnection()->query("INSERT INTO ". DB_ACCOUNTS ." (about,active,cover_id,email,name,password,time,type,username) VALUES ('" . $this->description . "',1,0,'" . $this->email . "','" . $this->name . "','" . md5(generateKey()) . "'," . time() . ",'event','" . $this->username . "')");

			if ($query)
			{
				$this->id = $this->getConnection()->insert_id;
				
				$query2 = $this->getConnection()->query("INSERT INTO " . DB_EVENTS . " (id,end_time,location,private,start_time,timeline_id) VALUES (" . $this->id . "," . $this->endTime . ",'" . $this->location . "'," . $this->private . "," . $this->startTime . "," . $user['id'] . ")");

				if ($query2)
                {
                    $timelineObj = new \SocialKit\User();
                    $timelineObj->setId($this->id);
                    $get = $timelineObj->getRows();
                    return $get;
                }
			}
		}
	}

	private function validatePrivacy($p)
	{
		if (in_array($p, $this->validPrivacies))
		{
			return true;
		}
	}

	public function setName($n)
	{
		$this->name = $this->escapeObj->stringEscape($n);
		$this->username = "event_" . preg_replace('/[^A-Za-z0-9]+/i', '', ucwords($this->name)) . "_" . generateKey(5, 10);
	}

	public function setLocation($l)
	{
		$this->location = $this->escapeObj->stringEscape($l);
	}

	public function setDescription($a)
	{
		$this->description = $this->escapeObj->postEscape($a);
	}

	public function setPrivate($p)
	{
		if ($this->validatePrivacy($p))
		{
			$this->private = $p;
		}
	}

	public function setStartTime($t)
	{
		if (is_numeric($t) && $t > time())
		{
			$this->startTime = $t;
		}
	}

	public function setEndTime($t)
	{
		if (is_numeric($t) && $t > time() && $t > $this->startTime)
		{
			$this->endTime = $t;
		}
	}
}