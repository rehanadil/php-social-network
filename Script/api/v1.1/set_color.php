<?php
if (isset($_POST['color'])
	&& isset($_POST['friend_id'])
	&& $continue)
{
	$color = preg_replace('/[^A-Za-z0-9]/i', '', $_POST['color']);
	$friendId = (int) $_POST['friend_id'];
	$friendObj = new \SocialKit\User();
    $friendObj->setId($friendId);
    $friend = $friendObj->getRows();
	
	if (preg_match('/^([A-Fa-f0-9]{6})$/', $color))
	{
		$color = strtolower($escapeObj->stringEscape($color));

		$isRowSql = "SELECT id FROM " . DB_USER_COLORS . " WHERE (user1=" . $user['id'] . " AND user2=$friendId) OR (user2=" . $user['id'] . " AND user1=$friendId)";
		$isRow = $conn->query($isRowSql);

		if ($isRow->num_rows > 0)
		{
			$conn->query("DELETE FROM " . DB_USER_COLORS . " WHERE (user1=" . $user['id'] . " AND user2=$friendId) OR (user2=" . $user['id'] . " AND user1=$friendId)");
		}

		$conn->query("INSERT INTO " . DB_USER_COLORS . " (color,user1,user2) VALUES ('$color'," . $user['id'] . ",$friendId)");

		$data = array(
			"status" => 200
		);
	}
}