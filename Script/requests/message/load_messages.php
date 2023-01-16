<?php
userOnly();
$html = "";
$reply = true;

$receiverId = (int) $_GET['receiver_id'];
$receiverObj = new \SocialKit\User();
$receiverObj->setId($receiverId);
$receiver = $receiverObj->getRows();
$timelineId = (isset($_GET['timeline_id'])) ? (int) $_GET['timeline_id'] : $user['id'];

if ($timelineId === $user['id'])
{
    $timelineObj = $userObj;
    $timeline = $user;
}
else
{
    $timelineObj = new \SocialKit\User($conn);
    $timelineObj->setId($timelineId);
    $timeline = $timelineObj->getRows();
}

if (isset($receiver['id'])
    && $receiver['id'] != $timeline['id'])
{
    $themeData['receiver_url'] = $receiver['url'];
    $themeData['receiver_message_url'] = smoothLink('index.php?a=messages&recipient_id=' . $receiver['username']);
    $themeData['receiver_id'] = $receiver['id'];
    $themeData['receiver_name'] = $receiver['name'];

    $html = getMessagesHtml(array(
        'recipient_id' => $receiver['id'],
        'timeline_id' => $timeline['id']
    ));
    $isEmpty = false;

    if (empty($html))
    {
        $isEmpty = true;
        $html = \SocialKit\UI::view('messages/no-text');
    }

    if ($receiverObj->isBlocked())
    {
        $reply = false;
    }
    elseif ($receiver['type'] === "user"
        && $receiver['message_privacy'] === "following"
        && $timeline['type'] !== "page")
    {
        if (!$receiverObj->isFollowing($timelineId))
        {
            $reply = false;
        }
    }
    elseif ($receiver['type'] === "page" && $receiver['message_privacy'] !== "everyone")
    {
        $reply = false;
    }

    $data = array(
        'status' => 200,
        'html' => $html,
        'is_empty' => $isEmpty,
        'reply' => $reply,
        'href' => smoothLink('index.php?a=messages&recipient_id=' . $receiver['username'])
    );

    $colorQuery = $conn->query("SELECT color FROM " . DB_USER_COLORS . " WHERE (user1=" . $timeline['id'] . " AND user2=" . $receiver['id'] . ") OR (user2=" . $timeline['id'] . " AND user1=" . $receiver['id'] . ")");
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