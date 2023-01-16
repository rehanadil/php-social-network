<?php
userOnly();

if (isset($_POST['receiver_id'])
    && $user['subscription_plan']['send_messages'] == 1)
{
    $receiverId = (int) $_POST['receiver_id'];
    $receiverObj = new \SocialKit\User();
    $receiverObj->setId($receiverId);
    $receiver = $receiverObj->getRows();
    $text = "";
    $timelineId = 0;
    if (isset($_POST['timeline_id'])) $timelineId = (int) $_POST['timeline_id'];

    if (isset($_POST['text']))
    {
        $text = $_POST['text'];
    }

    $messageId = $receiverObj->sendMessage($text, "", $timelineId);

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

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();