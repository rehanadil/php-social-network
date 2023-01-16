<?php
userOnly();

if (isset($_GET['id']))
{
    $videoCallId = (int) $_GET['id'];
    $selectSql = "SELECT * FROM " . DB_VIDEO_CALLS . " WHERE id=$videoCallId";
    $selectQuery = $conn->query($selectSql);

    if ($selectQuery->num_rows == 1)
    {
        $selectFetch = $selectQuery->fetch_array(MYSQLI_ASSOC);
        if ($selectFetch['accepted'] == 1
            && $selectFetch['declined'] == 0)
        {
            $data = array(
                "status" => 200,
                "html" => getVideoCallInfo($videoCallId),
                "url" => smoothLink('index.php?a=video-call&id=' . $videoCallId)
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