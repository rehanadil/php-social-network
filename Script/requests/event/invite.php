<?php
userOnly();

$eventId = (int) $_GET['event_id'];
$timelineId = (int) $_GET['timeline_id'];

if (updateEventAction($eventId, $timelineId, 'invited'))
{
	$query = $conn->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$timelineId AND post_id=$eventId AND type='eventinvite' AND active=1");
    
    if ($query->num_rows > 0)
    {
        $conn->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$timelineId AND post_id=$eventId AND type='eventinvite' AND active=1");
    }

    $eventObj = new \SocialKit\User();
    $eventObj->setId($eventId);
    $event = $eventObj->getRows();

    $text = str_replace("{eventName}", $event['name'], $lang['event_invite_notif_text']);

    $conn->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES ($timelineId,1," . $user['id'] . ",$eventId,'$text'," . time() . ",'eventinvite','index.php?a=timeline&type=event&id=$eventId')");

	$data = array(
        'status' => 200,
        'going_count' => isEventInvited($eventId, 0, 'going'),
        'interested_count' => isEventInvited($eventId, 0, 'interested'),
        'invited_count' => isEventInvited($eventId, 0, 'invited')
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();