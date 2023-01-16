<?php
userOnly();

if (!empty($_POST['album_id']) && !empty($_POST['album_name']) && !empty($_POST['album_descr']))
{
	$albumId = (int) $_POST['album_id'];
	$albumName = $escapeObj->stringEscape($_POST['album_name']);
	$albumDescr = $escapeObj->postEscape($_POST['album_descr']);

	$query = $conn->query("SELECT COUNT(id) AS count FROM " . DB_MEDIA . " WHERE id=$albumId AND timeline_id=" . $user['id'] . " AND type='album' AND temp=0 AND active=1");

	if ($query)
	{
		$fetch = $query->fetch_array(MYSQLI_ASSOC);

		if ($fetch['count'] == 1)
		{
			$conn->query("UPDATE " . DB_MEDIA . " SET name='$albumName',descr='$albumDescr' WHERE id=" . $albumId);
			
			$data = array(
	            'status' => 200,
	            'album_name' => $albumName,
	            'album_descr' => $albumDescr
	        );
		}
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();