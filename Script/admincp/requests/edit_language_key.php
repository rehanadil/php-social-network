<?php
if (isset($_POST['language'])
	&& isset($_POST['key'])
	&& isset($_POST['value']))
{
	$continue = false;
	$language = $escapeObj->stringEscape($_POST['language']);
	$key = $escapeObj->stringEscape($_POST['key']);
	$value = str_replace('&amp;', '&', $escapeObj->postEscape($_POST['value']));

	$languageQuery = $conn->query("SELECT id FROM " . DB_LANGUAGES . " WHERE type='$language' LIMIT 1");
	if ($languageQuery->num_rows == 1)
	{
		$keyQuery = $conn->query("SELECT id FROM " . DB_LANGUAGES . " WHERE type='$language' AND keyword='$key' LIMIT 1");

		if ($keyQuery->num_rows == 0)
			$conn->query("INSERT INTO " . DB_LANGUAGES . " (type,keyword,text) VALUES ('$language','$key','$value')");
		else
			$conn->query("UPDATE " . DB_LANGUAGES . " SET text='$value' WHERE type='$language' AND keyword='$key'");
	}
}