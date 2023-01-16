<?php
userOnly();

$data['status'] = 200;
$data['notifications'] = countNotifications();

$data['messages'] = countMessages(array("new" => true));
$data['follow_requests'] = $userObj->numFollowRequests();
$data['chat'] = false;
$data['is_video_called'] = false;
$data['is_audio_called'] = false;

if (! empty($_GET['chat_recipient_id']) && is_numeric($_GET['chat_recipient_id']))
{
    $data['chat'] = true;
    $html = '';
    $recipientId = (int) $_GET['chat_recipient_id'];
    $recipientObj = new \SocialKit\User($conn);
    $recipientObj->setId($recipientId);
    $recipient = $recipientObj->getRows();
    
    if (isset($recipient['id']))
    {
        $data['chat_recipient_online'] = $recipient['online'];
        $countMessagesInfo = array(
            "recipient_id" => $recipient['id'],
            "new" => true
        );
        $messages_num = countMessages($countMessagesInfo);
        
        if ($messages_num > 0)
        {
            $messages = getMessages(array(
                'new' => true,
                'recipient_id' => $recipient['id']
            ));
            
            if (is_array($messages))
            {
                foreach ($messages as $k => $v)
                {
                    $themeData['message_text'] = $v['text'];
                    $themeData['message_time'] = getTimeAgo($v['time'], true);

                    $themeData['message_user_url'] = $v['timeline']['url'];
                    $themeData['message_user_avatar_url'] = $v['timeline']['avatar_url'];
                    $themeData['message_user_name'] = $v['timeline']['name'];

                    if (isset($v['media']['id']))
                    {
                        $themeData['message_media_url'] = $v['media']['complete_url'];
                        $themeData['message_media'] = \SocialKit\UI::view('chat/text-media');
                    }

                    $html .= \SocialKit\UI::view('chat/incoming-text');
                }
            }
            
            $data['chat_messages'] = $html;
        }
    }
}

$videoCallSql = "SELECT * FROM " . DB_VIDEO_CALLS . " WHERE receiver_id=" . $user['id'] . " AND accepted=0 AND declined=0 AND time>" . (time()-45) . " ORDER BY id DESC LIMIT 1";
$videoCallQuery = $conn->query($videoCallSql);
if ($videoCallQuery->num_rows === 1)
{
    $videoCall = $videoCallQuery->fetch_array(MYSQLI_ASSOC);

    $receiverId = $user['id'];
    $receiverObj = $userObj;
    $receiver = $user;

    $callerId = (int) $videoCall['caller_id'];
    $callerObj = new \SocialKit\User();
    $callerObj->setId($callerId);
    $caller = $callerObj->getRows();

    if ($receiverObj->isReal('user')
        && $callerObj->isReal('user'))
    {
        $themeData['call_id'] = $videoCall['id'];
        $themeData['caller_id'] = $caller['id'];
        $themeData['caller_thumbnail_url'] = $caller['thumbnail_url'];
        $themeData['caller_avatar_url'] = $caller['avatar_url'];
        $themeData['caller_image_url'] = $caller['avatar_url'];
        $themeData['caller_name'] = $caller['name'];
        $themeData['caller_username'] = $caller['username'];

        $data['is_video_called'] = true;
        $data['video_call'] = array(
            'id' => $videoCall['id'],
            'html' => \SocialKit\UI::view('popups/video-incoming-call')
        );
    }
}
else
{
    $audioCallSql = "SELECT * FROM " . DB_AUDIO_CALLS . " WHERE receiver_id=" . $user['id'] . " AND accepted=0 AND declined=0 AND time>" . (time()-45) . " ORDER BY id DESC LIMIT 1";
    $audioCallQuery = $conn->query($audioCallSql);
    if ($audioCallQuery->num_rows === 1)
    {
        $audioCall = $audioCallQuery->fetch_array(MYSQLI_ASSOC);

        $receiverId = $user['id'];
        $receiverObj = $userObj;
        $receiver = $user;

        $callerId = (int) $audioCall['caller_id'];
        $callerObj = new \SocialKit\User();
        $callerObj->setId($callerId);
        $caller = $callerObj->getRows();

        if ($receiverObj->isReal('user')
            && $callerObj->isReal('user'))
        {
            $themeData['call_id'] = $audioCall['id'];
            $themeData['caller_id'] = $caller['id'];
            $themeData['caller_thumbnail_url'] = $caller['thumbnail_url'];
            $themeData['caller_avatar_url'] = $caller['avatar_url'];
            $themeData['caller_image_url'] = $caller['avatar_url'];
            $themeData['caller_name'] = $caller['name'];
            $themeData['caller_username'] = $caller['username'];

            $data['is_audio_called'] = true;
            $data['audio_call'] = array(
                'id' => $audioCall['id'],
                'html' => \SocialKit\UI::view('popups/audio-incoming-call')
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();