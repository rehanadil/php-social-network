<?php
userOnly();

if (isset($_GET['id']))
{
    $audioCallId = (int) $_GET['id'];
    $selectSql = "SELECT * FROM " . DB_AUDIO_CALLS . " WHERE id=$audioCallId";
    $selectQuery = $conn->query($selectSql);

    if ($selectQuery->num_rows == 1)
    {
        $selectFetch = $selectQuery->fetch_array(MYSQLI_ASSOC);
        $themeData['room'] = $selectFetch['room'];

        if ($selectFetch['accepted'] == 1
            && $selectFetch['declined'] == 0)
        {
            if ($selectFetch['caller_id'] === $user['id'])
            {
                $themeData['access_token'] = $selectFetch['caller_access_token'];
                $themeData['call_id'] = $selectFetch['receiver_call_id'];
                $themeData['is_invited'] = 0;

                $receiverObj = new \SocialKit\User();
                $receiverObj->setId($selectFetch['receiver_id']);
            }
            elseif ($selectFetch['receiver_id'] === $user['id'])
            {
                $themeData['access_token'] = $selectFetch['receiver_access_token'];
                $themeData['call_id'] = $selectFetch['caller_call_id'];
                $themeData['is_invited'] = 1;

                $receiverObj = new \SocialKit\User();
                $receiverObj->setId($selectFetch['caller_id']);
            }

            $receiver = $receiverObj->getRows();
            $themeData['receiver_url'] = $receiver['url'];
            $themeData['receiver_avatar_url'] = $receiver['avatar_url'];
            $themeData['receiver_name'] = $receiver['name'];
            $themeData['receiver_username'] = $receiver['username'];

            $data = array(
                "status" => 200,
                "html" => \SocialKit\UI::view('popups/audio-call')
            );
        }
        elseif ($selectFetch['accepted'] == 0
            && $selectFetch['declined'] == 1)
        {
            $data = array(
                "status" => 401
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();