<?php
userOnly();

$receiverId = (int) $_GET['receiver_id'];
$receiverObj = new \SocialKit\User();
$receiverObj->setId($receiverId);
if (!$receiverObj->isBlocked()) $receiver = $receiverObj->getRows();

if (isset($receiver['id']))
{
    $themeData['receiver_url'] = $receiver['url'];
    $themeData['receiver_message_url'] = smoothLink('index.php?a=messages&recipient_id=' . $receiver['username']);
    $themeData['receiver_id'] = $receiver['id'];
    $themeData['receiver_name'] = $receiver['name'];

    if (isset($receiver['id']))
    {
        $messagesHtml = getChatHtml(array(
            'recipient_id' => $receiver['id']
        ));

        $chatTextarea = false;

        if ($receiver['message_privacy'] == "following" && $receiverObj->isFollowing())
        {
            $chatTextarea = true;
        }
        elseif ($receiver['message_privacy'] == "everyone")
        {
            $chatTextarea = true;
        }

        if ($chatTextarea)
        {
            $themeData['chat_textarea'] = \SocialKit\UI::view('chat/chat-textarea');
        }

        $themeData['messages_html'] = $messagesHtml;
    }

    $data = array(
        'status' => 200,
        'html' => \SocialKit\UI::view('chat/content'),
        'href' => smoothLink('index.php?a=messages&recipient_id=' . $receiver['username']),
        'online' => $receiver['online']
    );
    if (!isset($_SESSION['chat_friends'][$receiver['id']]))
    {
        $_SESSION['chat_friends'][$receiver['id']] = array(
            "username" => $receiver['username'],
            "name" => $receiver['name']
        );
    }
        

    $colorQuery = $conn->query("SELECT color FROM " . DB_USER_COLORS . " WHERE (user1=" . $user['id'] . " AND user2=" . $receiver['id'] . ") OR (user2=" . $user['id'] . " AND user1=" . $receiver['id'] . ")");
    $colorFetch = $colorQuery->fetch_array(MYSQLI_ASSOC);

    if (strlen($colorFetch['color']) > 0)
    {
        $data['color'] = $colorFetch['color'];
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();