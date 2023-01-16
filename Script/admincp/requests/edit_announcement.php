<?php

if (isset($_POST['name'])
	&& isset($_POST['value'])
	&& isset($_POST['announcement_id']))
{
	$announcementId = (int) $_POST['announcement_id'];
	$announcementQuery = $conn->query("SELECT * FROM " . DB_ANNOUNCEMENTS . " WHERE id=$announcementId");
	if ($announcementQuery->num_rows == 1)
	{
		$name = $escapeObj->stringEscape($_POST['name']);
		if ($name === "text")
			$value = str_replace(array("'", '"'), array("\'", '\"'), htmlentities($_POST['value']));
		else
			$value = $escapeObj->stringEscape($_POST['value']);
		
		$sql = "UPDATE " . DB_ANNOUNCEMENTS . " SET $name='$value' WHERE id=$announcementId";
		$conn->query($sql);
	}
}