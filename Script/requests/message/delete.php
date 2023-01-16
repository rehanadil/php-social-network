<?php
userOnly();

if (isset($_POST['conversation']))
{
    if (isset($_POST['id']))
    {
        $receiverId = (int) $_POST['id'];
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
            $deleteSql = "DELETE FROM " . DB_MESSAGES . " WHERE (timeline_id=$timelineId AND recipient_id=$receiverId) OR (timeline_id=$receiverId AND recipient_id=$timelineId)";
            $deleteQuery = $conn->query($deleteSql);
            
            if ($deleteQuery)
            {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
}
elseif (isset($_POST['id']))
{
    $messageId = (int) $_POST['id'];
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
            $deleteSql = "DELETE FROM " . DB_MESSAGES . " WHERE id=$messageId AND active=1";
            $deleteQuery = $conn->query($deleteSql);
            
            if ($deleteQuery)
            {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();