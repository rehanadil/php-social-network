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
	AND TABLE_NAME = '" . DB_COMMENTS . "' 
	AND COLUMN_NAME = '$name'");

	if ($fieldCheckQuery->num_rows)
	{
		$continue = true;
		$sql = "ALTER TABLE " . DB_COMMENTS . " ALTER $name SET DEFAULT '$value'";
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
		else
		{
			$fieldCheckQuery3 = $conn->query("SELECT * 
			FROM information_schema.COLUMNS 
			WHERE 
			    TABLE_SCHEMA = '" . $sql_name . "' 
			AND TABLE_NAME = '" . DB_USERS . "' 
			AND COLUMN_NAME = '$name'");
			if ($fieldCheckQuery3->num_rows)
			{
				$continue = true;
				$sql = "ALTER TABLE " . DB_USERS . " ALTER $name SET DEFAULT '$value'";
			}
		}
	}

	if ($continue) $conn->query($sql);
}