<?php
if (preg_match('/(going|interested|invited)/', $_GET['action']) && isset($_GET['event_id']))
{
	$eventId = (int) $_GET['event_id'];
	$eventObj = new \SocialKit\User();
	$eventObj->setId($eventId);
	$event = $eventObj->getRows();

	$themeData['inviteactions_title'] = ucwords($_GET['action']);

	$data = array(
	    'status' => 200,
	    'html' => $eventObj->getInvitesTemplate($_GET['action'])
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();