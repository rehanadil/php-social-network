<?php
if ($user['subscription_plan']['create_events'] == 1)
{
	$regObj = new \SocialKit\registerEvent();
	$regObj->setName($_POST['event_name']);
	$regObj->setLocation($_POST['event_location']);
	$regObj->setDescription($_POST['event_descr']);
	$regObj->setPrivate($_POST['event_privacy']);

	$startTime = $_POST['start_date'] . " " . $_POST['start_time'];
	$startTimeObj = new DateTime($startTime);
	$regObj->setStartTime($startTimeObj->getTimestamp());

	$endTime = $_POST['end_date'] . " " . $_POST['end_time'];
	$endTimeObj = new DateTime($endTime);
	$regObj->setEndTime($endTimeObj->getTimestamp());

	if ($register = $regObj->register())
	{
		updateEventAction($register['id'], $user['id'], 'going');

	    $eventObj = new \SocialKit\User($conn);
	    $eventObj->setId($register['id']);
	    $event = $eventObj->getRows();

	    $data = array(
	        'status' => 200,
	        'url' => $event['url']
	    );
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();