<?php
userOnly();

$eventId = (int) $_GET['event_id'];

if (! empty($_GET['action']))
{
	$action = $_GET['action'];

	if (updateEventAction($eventId, $user['id'], $action))
	{
		if ($action == "going")
		{
			$eventObj = new \SocialKit\User();
		    $eventObj->setId($eventId);
		    $event = $eventObj->getRows();

		    $query = $conn->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $event['timeline_id'] . " AND notifier_id=" . $user['id'] . " AND post_id=$eventId AND type='eventgoing' AND active=1");
		    
		    if ($query->num_rows > 0)
		    {
		        $conn->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $event['timeline_id'] . " AND notifier_id=" . $user['id'] . " AND post_id=$eventId AND type='eventgoing' AND active=1");
		    }

		    $text = str_replace("{eventName}", $event['name'], $lang['event_going_notif_text']);

		    $conn->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES (" . $event['timeline_id'] . ",1," . $user['id'] . ",$eventId,'$text'," . time() . ",'eventgoing','index.php?a=timeline&type=event&id=$eventId')");
		}

		$data = array(
	        'status' => 200,
	        'going_count' => isEventInvited($eventId, 0, 'going'),
	        'interested_count' => isEventInvited($eventId, 0, 'interested'),
	        'invited_count' => isEventInvited($eventId, 0, 'invited')
	    );
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();