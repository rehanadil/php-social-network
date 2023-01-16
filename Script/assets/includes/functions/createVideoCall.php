<?php
function createVideoCall($videoInfo)
{
    if (!isLogged()) return false;
    if (empty($videoInfo) || !is_array($videoInfo)) return false;
    if ($videoInfo['caller_id'] === $videoInfo['receiver_id']) return false;

    global $conn, $user;
    $userId = $user['id'];

    $videoInfo['time'] = time();
    $deleteSql = "DELETE FROM " . DB_VIDEO_CALLS . " WHERE caller_id=$userId OR receiver_id=$userId";
    $conn->query($deleteSql);

    $fields = implode(',', array_keys($videoInfo));
    $values = '\'' . implode('\',\'', $videoInfo) . '\'';
    $createSql = "INSERT INTO " . DB_VIDEO_CALLS . " ($fields) VALUES ($values)";
    $createQuery = $conn->query($createSql);

    if ($createQuery) return array('id' => $conn->insert_id);
    return false;
}