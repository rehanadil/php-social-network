<?php
userOnly();

if (isset($_POST['descr']) && isset($_POST['event_id']))
{
	$descr = $_POST['descr'];
	$descr = $escapeObj->createLinks($descr);
	$descr = $escapeObj->postEscape($descr);
	
	$eventId = (int) $_POST['event_id'];
	$eventObj = new \SocialKit\User();
	$eventObj->setId($eventId);
	$event = $eventObj->getRows();

	if ($eventObj->isAdmin())
	{
		if ($conn->query("UPDATE " . DB_ACCOUNTS . " SET about='$descr' WHERE id=$eventId AND banned=0"))
		{
			$eventDescr = $descr;
			$eventDescr = $escapeObj->getEmoticons($eventDescr);
			$eventDescr = $escapeObj->getLinks($eventDescr);
			
			$data = array(
		        'status' => 200,
		        'html' => $eventDescr
		    );
		}
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();