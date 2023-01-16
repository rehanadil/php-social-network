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
                "after_id" => $afterId,
                'receiver_only' => true
            ));
            $isEmpty = false;

            if (empty($html)) $isEmpty = true;

            $data = array(
                'status' => 200,
                'html' => $html,
                'is_empty' => $isEmpty,
                'is_seen' => false,
                'online' => $receiver['online']
            );

            if ($isEmpty
            && !empty($_GET['show_seen'])
            && $user['subscription_plan']['last_seen'] == 1)
            {
                $seenSql = "SELECT seen,timeline_id FROM " . DB_MESSAGES . " WHERE id=" . $afterId;
                $seenQuery = $conn->query($seenSql);

                if ($seenQuery->num_rows === 1)
                {
                    $seenInfo = $seenQuery->fetch_array(MYSQLI_ASSOC);

                    if ($seenInfo['timeline_id'] === $user['id']
                        && $seenInfo['seen'] > 0)
                    {
                        $themeData['seen_time'] = $seenInfo['seen'];
                        $data['is_seen'] = true;
                        $data['seen'] = \SocialKit\UI::view('chat/seen');
                    }
                }
            }
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();