<?php
if (isset($_POST['name'])
	&& isset($_POST['value']))
{
	$continue = false;
	$name = $escapeObj->stringEscape($_POST['name']);
	$value = $escapeObj->stringEscape($_POST['value']);
	
	$fieldCheckQuery = $conn->query("SELECT * 
	FROM information_schema.COLUMNS 
	WHERE 
	    TABLE_SCHEMA = '" . $sql_name . "' 
	AND TABLE_NAME = '" . DB_PAGES . "' 
	AND COLUMN_NAME = '$name'");

	if ($fieldCheckQuery->num_rows)
	{
		$continue = true;
		$sql = "ALTER TABLE " . DB_PAGES . " ALTER $name SET DEFAULT '$value'";
	}
	else
	{
		$fieldCheckQuery2 = $conn->query("SELECT * 
		FROM information_schema.COLUMNS 
		WHERE 
		    TABLE_SCHEMA = '" . $sql_name . "' 
		AND TABLE_NAME = '" . DB_CONFIGURATIONS . "' 
		AND COLUMN_NAME = '$name'");
		if ($fieldCheckQuery2->num_rows)
		{
			$continue = true;
			$sql = "UPDATE " . DB_CONFIGURATIONS . " SET $name='$value'";
		}
	}

	if ($continue) $conn->query($sql);
}