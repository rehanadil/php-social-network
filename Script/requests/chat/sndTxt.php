<?php
userOnly();

if (isset($_POST['receiver_id'])
    && $user['subscription_plan']['send_messages'] == 1)
{
    $receiverId = (int) $_POST['receiver_id'];
    $receiverObj = new \SocialKit\User();
    $receiverObj->setId($receiverId);
    $receiver = $receiverObj->getRows();
    $txt = "";

    if (isset($_POST['txt']))
    {
        $txt = $_POST['txt'];
    }

    $msgId = $receiverObj->sendMessage($txt, "");

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

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();