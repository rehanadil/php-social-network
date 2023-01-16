<?php
if (isset($_GET['query'], $_GET['event_id']))
{
	$eventId = (int) $_GET['event_id'];
	$query = $_GET['query'];

	if ($inviteData = getEventInviteList($query, $eventId))
	{
	    $data = array(
	        'status' => 200,
	        'html' => $inviteData
	    );
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();