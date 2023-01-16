<?php
if (isset($_POST['group_id'])
	&& isset($_POST['name'])
	&& isset($_POST['value']))
{
	$groupId = (int) $_POST['group_id'];
	$name = $escapeObj->stringEscape($_POST['name']);
	$value = $escapeObj->stringEscape($_POST['value']);
	$table = DB_ACCOUNTS;

	if ($name === "password")
	{
		if (!preg_match('/^[a-f0-9]{32}$/', $value)) $value = md5($value);
	}

	$fieldCheckQuery = $conn->query("SELECT * 
	FROM information_schema.COLUMNS 
	WHERE 
	    TABLE_SCHEMA = '" . $sql_name . "' 
	AND TABLE_NAME = '" . DB_GROUPS . "' 
	AND COLUMN_NAME = '$name'");

	if ($fieldCheckQuery->num_rows)
	{
		$table = DB_GROUPS;
	}

	if ($conn->query("UPDATE " . $table . " SET $name='$value' WHERE id=$groupId"))
	{
		$cacheObj = new \SocialKit\Cache();
		$cacheObj->fromAdminArea(true);
	    $cacheObj->setType('user');
	    $cacheObj->setId($groupId);
	    $cacheObj->prepare();

	    if ($cacheObj->exists())
	    {
			$cacheObj->clear();
	    }
	}
}