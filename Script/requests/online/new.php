<?php
$html = "";
$ids = array();
$offlineIds = array();
$lastSessionIds = $_SESSION['online_ids'];

unset($_SESSION['online_ids']);
$_SESSION['online_ids'] = array();

$sqlQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . ") AND type='user' AND last_logged>" . (time() - 30) . " AND active=1 AND banned=0 ORDER BY last_logged DESC");
while ($sqlFetch = $sqlQuery->fetch_array(MYSQLI_ASSOC))
{
    $oId = $sqlFetch['id'];
    $ids[] = $oId;

    if (isset($lastSessionIds[$oId]))
    {
        unset($lastSessionIds[$oId]);
    }

    $onlineUserObj = new \SocialKit\User();
    $onlineUserObj->setId($sqlFetch['id']);
    $onlineUser = $onlineUserObj->getRows();

    $themeData['list_online_user_id'] = $onlineUser['id'];
    $themeData['list_online_user_url'] = $onlineUser['url'];
    $themeData['list_online_username'] = $onlineUser['username'];
    $themeData['list_online_user_name'] = $onlineUser['name'];
    $themeData['list_online_user_thumbnail_url'] = $onlineUser['thumbnail_url'];
    $themeData['list_online_user_last_logged_time'] = $onlineUser['last_logged_time'];
    $themeData['list_online_user_unread'] = countMessages(array(
        "recipient_id" => $onlineUser['id'],
        "new" => true
    ));

    $themeData['online_status_html'] = \SocialKit\UI::view('online/online-indicator');

    $html .= \SocialKit\UI::view('online/li');
    $_SESSION['online_ids'][$oId] = true;
}

foreach ($lastSessionIds as $k => $v)
{
    $offlineIds[] = $k;
}

$data = array(
    "status" => 200,
    "html" => $html,
    "ids" => $ids,
    "offline_ids" => $offlineIds
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();