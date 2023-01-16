<?php
userOnly();
if (isset($_GET['status']))
{
	$status = ($_GET['status'] == 0) ? 1 : 0;
	$sql = "UPDATE " . DB_USERS . " SET go_offline=$status WHERE id=" . $user['id'];
	$query = $conn->query($sql);

	if ($query)
	{
		$data = array(
			"status" => 200,
			"is_offline" => $status
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();