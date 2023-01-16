<?php
if (isset($_POST['user_id'])
	&& isset($_POST['name'])
	&& isset($_POST['value']))
{
	$usrId = (int) $_POST['user_id'];
	$name = $escapeObj->stringEscape($_POST['name']);
	$value = $escapeObj->stringEscape($_POST['value']);
	$table = DB_ACCOUNTS;
	$continue = true;

	if ($name === "password")
	{
		if (!preg_match('/^[a-f0-9]{32}$/', $value)) $value = md5($value);
	}

	$fieldCheckQuery = $conn->query("SELECT * 
	FROM information_schema.COLUMNS 
	WHERE 
	    TABLE_SCHEMA = '" . $sql_name . "' 
	AND TABLE_NAME = '" . DB_USERS . "' 
	AND COLUMN_NAME = '$name'");

	if ($fieldCheckQuery->num_rows)
	{
		$table = DB_USERS;
	}

	if ($name === "birthday")
	{
		$birth = explode('/', $value);
		if (count($birth) !== 3) $continue = false;
		if ($birth[0] < 1 || $birth[0] > 12) $continue = false;
		if ($birth[1] < 1 || $birth[0] > 31) $continue = false;
		if ($birth[2] < 1900 || $birth[0] > date('Y')) $continue = false;
		if ($birth[1] === 2 && $birth[0] > 29) $continue = false;
	}

	if ($continue)
	{
		if ($conn->query("UPDATE " . $table . " SET $name='$value' WHERE id=$usrId"))
		{
			$cacheObj = new \SocialKit\Cache();
			$cacheObj->fromAdminArea(true);
		    $cacheObj->setType('user');
		    $cacheObj->setId($usrId);
		    $cacheObj->prepare();

		    if ($cacheObj->exists())
		    {
		      $cacheObj->clear();
		    }
		}
	}
}