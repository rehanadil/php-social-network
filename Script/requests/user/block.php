<?php
userOnly();

if (isset($_POST['id']))
{
	$blockedUserId = (int) $_POST['id'];
	$blockedUserObj = new \SocialKit\User();
	$blockedUserObj->setId($blockedUserId);

	if (!$blockedUserObj->isBlocked())
	{
		$blockQuery = $conn->query("INSERT INTO " . DB_BLOCKED_USERS . " (active,blocked_id,blocker_id) VALUES (1,$blockedUserId," . $user['id'] . ")");
		if ($blockQuery)
		{
			$conn->query("DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . " AND following_id=" . $blockedUserId);
			$conn->query("DELETE FROM " . DB_FOLLOWERS . " WHERE following_id=" . $user['id'] . " AND follower_id=" . $blockedUserId);
			$data = array(
				"status" => 200,
				"url" => smoothLink('index.php?a=settings&b=blocked-users')
			);
		}
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();