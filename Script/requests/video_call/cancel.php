<?php
userOnly();

if (isset($_GET['id']))
{
    $videoCallId = (int) $_GET['id'];
    $selectSql = "SELECT id FROM " . DB_VIDEO_CALLS . " WHERE id=$videoCallId AND caller_id=" . $user['id'];
    $selectQuery = $conn->query($selectSql);

    if ($selectQuery->num_rows == 1)
    {
        $deleteSql = "DELETE FROM " . DB_VIDEO_CALLS . " WHERE id=$videoCallId AND caller_id=" . $user['id'];
        $deleteQuery = $conn->query($deleteSql);

        if ($deleteQuery)
        {
            $data = array(
                "status" => 200
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();