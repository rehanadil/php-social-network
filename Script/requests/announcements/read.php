<?php
userOnly();

if (isset($_GET['announcement_id']))
{
	$aid = (int) $_GET['announcement_id'];

	$query = $conn->query("SELECT id FROM " . DB_ANNOUNCEMENT_VIEWS . " WHERE announcement_id=$aid AND account_id=" . $user['id']);
	if ($query->num_rows == 0)
	{
		$conn->query("INSERT INTO " . DB_ANNOUNCEMENT_VIEWS . " (account_id,announcement_id) VALUES (" . $user['id'] . ",$aid)");
		$data = array(
			"status" => 200
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();