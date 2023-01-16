<?php
if (isset($_GET['conversation']))
{
    if (isset($_GET['id']))
    {
        $receiverId = (int) $_GET['id'];
        $receiverObj = new \SocialKit\User();
        $receiverObj->setId($receiverId);
        $receiver = $receiverObj->getRows();
        $continue = false;

        if (isset($_GET['timeline_id']))
        {
            $timelineId = (int) $_GET['timeline_id'];
            $timelineObj = new \SocialKit\User();
            $timelineObj->setId($timelineId);
            $timeline = $timelineObj->getRows();

            if ($timelineObj->isAdmin()) $continue = true;
        }
        else
        {
            $timelineId = $user['id'];
            $timelineObj = $userObj;
            $timeline = $user;
            $continue = true;
        }

        if ($continue)
        {
            $themeData['receiver_id'] = $receiverId;
            $data = array(
                "status" => 200,
                "html" => \SocialKit\UI::view('popups/delete-conversation')
            );
        }
    }
}
elseif (isset($_GET['id']))
{
    $messageId = (int) $_GET['id'];
    $selectSql = "SELECT id,timeline_id FROM " . DB_MESSAGES . " WHERE id=$messageId AND active=1";
    $selectQuery = $conn->query($selectSql);

    if ($selectQuery->num_rows === 1)
    {
        $message = $selectQuery->fetch_array(MYSQLI_ASSOC);

        $timelineObj = new \SocialKit\User($conn);
        $timelineObj->setId($message['timeline_id']);
        $timeline = $timelineObj->getRows();

        if ($timelineObj->isAdmin())
        {
            $themeData['message_id'] = $messageId;
            $data = array(
                "status" => 200,
                "html" => \SocialKit\UI::view('popups/delete-message')
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();