<?php
userOnly();
$html = "";

if (isset($_GET['after_id']))
{
    $afterId = (int) $_GET['after_id'];

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

        $messagesInfo = array(
            'after_id' => $afterId,
            'recipient_id' => $receiver['id'],
            'timeline_id' => $timeline['id'],
            'receiver_only' => true
        );
        $html = getMessagesHtml($messagesInfo);
        $isEmpty = false;

        if (empty($html)) $isEmpty = true;

        $data = array(
            'status' => 200,
            'html' => $html,
            'is_empty' => $isEmpty,
            'is_seen' => false
        );

        if ($isEmpty
            && !empty($_GET['show_seen'])
            && $user['subscription_plan']['last_seen'] == 1)
        {
            $seenSql = "SELECT seen,timeline_id FROM " . DB_MESSAGES . " WHERE id=" . $afterId;
            $seenQuery = $conn->query($seenSql);

            if ($seenQuery->num_rows == 1)
            {
                $seenInfo = $seenQuery->fetch_array(MYSQLI_ASSOC);

                if ($seenInfo['timeline_id'] == $user['id']
                    && $seenInfo['seen'] > 0)
                {
                    $themeData['seen_time'] = $seenInfo['seen'];
                    $data['is_seen'] = true;
                    $data['seen'] = \SocialKit\UI::view('messages/seen');
                }
            }
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();