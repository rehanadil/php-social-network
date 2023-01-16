<?php
userOnly();

if ($user['subscription_plan']['upload_videos'] == 1
	&& $user['subscription_plan']['send_messages'] == 1)
{
	if (isset($_POST['receiver_id']))
	{
	    $receiverId = (int) $_POST['receiver_id'];
	    $receiverObj = new \SocialKit\User();
	    $receiverObj->setId($receiverId);
	    $receiver = $receiverObj->getRows();
	    $timelineId = 0;
    	if (isset($_POST['timeline_id'])) $timelineId = (int) $_POST['timeline_id'];

	    $file = $_FILES['video'];
	    
	    $messageId = $receiverObj->sendMessage("", $file, $timelineId);

	    if ($messageId)
	    {
	    	$messageInfo = array(
	    		"id" => $messageId,
	    		"receiver_id" => $receiver['id'],
	    		"timeline_id" => $timelineId
	    	);
	    	$data = array(
	    		"status" => 200,
	    		"html" => getMessagesHtml($messageInfo),
	    		"id" => $messageId
	    	);
	    }
	}
}
else
{
	$data['url'] = subscriptionUrl();
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();