<?php
userOnly();

if (isset($_POST['id']))
{
	$blockedUserId = (int) $_POST['id'];
	$conn->query("DELETE FROM " . DB_BLOCKED_USERS . " WHERE blocker_id=" . $user['id'] . " AND blocked_id=" . $blockedUserId);
	$data = array(
		"status" => 200
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();