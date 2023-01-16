<?php
userOnly();

if (isset($_GET['id']))
{
    $videoCallId = (int) $_GET['id'];
    $selectSql = "SELECT id FROM " . DB_VIDEO_CALLS . " WHERE id=$videoCallId AND receiver_id=" . $user['id'] . " AND accepted=0 AND declined=0";
    $selectQuery = $conn->query($selectSql);

    if ($selectQuery->num_rows == 1)
    {
        $selectFetch = $selectQuery->fetch_array(MYSQLI_ASSOC);
        $updateSql = "UPDATE " . DB_VIDEO_CALLS . " SET accepted=1 WHERE id=" . $videoCallId;
        $updateQuery = $conn->query($updateSql);

        if ($updateQuery)
        {
            $data = array(
                "status" => 200,
                "html" => getVideoCallInfo($videoCallId),
                "url" => smoothLink('index.php?a=video-call&id=' . $videoCallId)
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();