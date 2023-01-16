<?php
userOnly();

if (isset($_GET['color'])
	&& $_GET['receiver_id']
	&& $user['subscription_plan']['use_chat_colors'] == 1)
{
	$receiverId = (int) $_GET['receiver_id'];
	
	if (preg_match('/^([A-Fa-f0-9]{6})$/', $_GET['color']))
	{
		$color = strtolower($escapeObj->stringEscape($_GET['color']));

		$isRowSql = "SELECT id FROM " . DB_USER_COLORS . " WHERE (user1=" . $user['id'] . " AND user2=$receiverId) OR (user2=" . $user['id'] . " AND user1=$receiverId)";
		$isRow = $conn->query($isRowSql);

		if ($isRow->num_rows > 0)
		{
			$conn->query("DELETE FROM " . DB_USER_COLORS . " WHERE (user1=" . $user['id'] . " AND user2=$receiverId) OR (user2=" . $user['id'] . " AND user1=$receiverId)");
		}

		$conn->query("INSERT INTO " . DB_USER_COLORS . " (color,user1,user2) VALUES ('$color'," . $user['id'] . ",$receiverId)");

		$data = array(
			"status" => 200
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();