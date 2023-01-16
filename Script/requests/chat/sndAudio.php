<?php
userOnly();

if ($user['subscription_plan']['upload_audios'] == 1
	&& $user['subscription_plan']['send_messages'] == 1)
{
	if (isset($_POST['receiver_id']))
	{
	    $receiverId = (int) $_POST['receiver_id'];
	    $receiverObj = new \SocialKit\User();
	    $receiverObj->setId($receiverId);
	    $receiver = $receiverObj->getRows();

	    $file = $_FILES['audio'];
	    
	    $msgId = $receiverObj->sendMessage("", $file);

	    if ($msgId)
	    {
	        $msgInfo = array(
	            "id" => $msgId,
	            "receiver_id" => $receiver['id']
	        );
	        $data = array(
	            "status" => 200,
	            "html" => getChatHtml($msgInfo)
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