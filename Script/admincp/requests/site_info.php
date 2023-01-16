<?php
if (isset($_POST['name'])
	&& isset($_POST['value']))
{
	$name = $escapeObj->stringEscape($_POST['name']);
	$value = $_POST['value'];

	$unescapes = array(
		"google_analytics",
		"ad_place_home",
		"ad_place_messages",
		"ad_place_timeline",
		"ad_place_hashtag",
		"ad_place_search"
	);
	$value = trim($value);
	$value = $conn->real_escape_string($value);

	if (in_array($name, $unescapes))
	{
		$value = htmlentities($value);
	}
	else
	{
    	$value = htmlspecialchars($value, ENT_QUOTES);
    	$value = stripslashes($value);
	}

	$fieldCheckQuery = $conn->query("SELECT * 
	FROM information_schema.COLUMNS 
	WHERE 
	    TABLE_SCHEMA = '" . $sql_name . "' 
	AND TABLE_NAME = '" . DB_CONFIGURATIONS . "' 
	AND COLUMN_NAME = '$name'");

	if ($fieldCheckQuery->num_rows)
	{
		$conn->query("UPDATE " . DB_CONFIGURATIONS . " SET $name='$value'");
	}

	if ($name === "language")
	{
		$conn->query("ALTER TABLE " . DB_ACCOUNTS . " ALTER $name SET DEFAULT '$value'");
	}
}