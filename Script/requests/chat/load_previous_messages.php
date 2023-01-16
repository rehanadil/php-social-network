<?php
userOnly();
$html = "";

if (isset($_GET['before_id']))
{
    $beforeId = (int) $_GET['before_id'];
    $receiverId = (int) $_GET['receiver_id'];
    $receiverObj = new \SocialKit\User();
    $receiverObj->setId($receiverId);
    $receiver = $receiverObj->getRows();

    if (isset($receiver['id']))
    {
        $themeData['receiver_url'] = $receiver['url'];
        $themeData['receiver_message_url'] = smoothLink('index.php?a=messages&recipient_id=' . $receiver['username']);
        $themeData['receiver_id'] = $receiver['id'];
        $themeData['receiver_name'] = $receiver['name'];

        if (isset($receiver['id']))
        {
            $html = getChatHtml(array(
                "recipient_id" => $receiver['id'],
                "before_id" => $beforeId
            ));
            $isEmpty = false;

            if (empty($html)) $isEmpty = true;

            $data = array(
                'status' => 200,
                'html' => $html,
                'is_empty' => $isEmpty
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();