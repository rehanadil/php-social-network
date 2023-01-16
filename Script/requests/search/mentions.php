<?php
userOnly();

$data = array();
$searchQuery = $escapeObj->stringEscape($_GET['term']);
if (strlen($searchQuery) > 2)
{
    $sql = "SELECT id FROM " . DB_ACCOUNTS . "
    WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . ")
    AND (name LIKE '%$searchQuery%' OR username='$searchQuery')
    AND active=1
    AND banned=0
    AND type='user'
    AND (
        id NOT IN
            (SELECT blocked_id FROM " . DB_BLOCKED_USERS . "
            WHERE blocker_id=" . $user['id'] . ")
        AND id NOT IN
            (SELECT blocker_id FROM " . DB_BLOCKED_USERS . "
            WHERE blocked_id=" . $user['id'] . ")
    )";
    $query = $conn->query($sql);
    while($mentionInfo = $query->fetch_array(MYSQLI_ASSOC))
    {
        $mentionObj = new \SocialKit\User();
        $mentionObj->setId($mentionInfo['id']);
        $mention = $mentionObj->getRows();

        $data[] = array(
            "value" => $mention['name'],
            "image" => $mention['thumbnail_url'],
            "uid" => "@" . $mention['id'],
            "username" => $mention['username']
        );
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();