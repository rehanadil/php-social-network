<?php
if (isset($_POST['page_id'])
	&& isset($_POST['name'])
	&& isset($_POST['value']))
{
	$pageId = (int) $_POST['page_id'];
	$name = $escapeObj->stringEscape($_POST['name']);
	$value = $escapeObj->stringEscape($_POST['value']);
	$table = DB_ACCOUNTS;

	$fieldCheckQuery = $conn->query("SELECT * 
	FROM information_schema.COLUMNS 
	WHERE 
	    TABLE_SCHEMA = '" . $sql_name . "' 
	AND TABLE_NAME = '" . DB_PAGES . "' 
	AND COLUMN_NAME = '$name'");

	if ($fieldCheckQuery->num_rows)
	{
		$table = DB_PAGES;
	}

	
	if ($conn->query("UPDATE " . $table . " SET $name='$value' WHERE id=$pageId"))
	{
		$cacheObj = new \SocialKit\Cache();
		$cacheObj->fromAdminArea(true);
	    $cacheObj->setType('user');
	    $cacheObj->setId($pageId);
	    $cacheObj->prepare();

	    if ($cacheObj->exists())
	    {
			$cacheObj->clear();
	    }
	}
}