<?php
userOnly();

if (isset($_SESSION['online_ids']))
{
    unset($_SESSION['online_ids']);
}

$_SESSION['online_ids'] = array();
$html = "";
$query = "";

if (isset($_GET['query']))
{
    $query = $escapeObj->stringEscape($_GET['query']);
}

$sqlQuery = (empty($query)) ? $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . ") AND type='user' AND active=1 AND banned=0 ORDER BY last_logged DESC LIMIT 30") : $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . ") AND type='user' AND (name LIKE '%$query%' OR username='$query') AND active=1 AND banned=0 ORDER BY last_logged DESC LIMIT 30");
while ($sqlFetch = $sqlQuery->fetch_array(MYSQLI_ASSOC))
{
    $oId = $sqlFetch['id'];
    
    $onlineUserObj = new \SocialKit\User();
    $onlineUserObj->setId($sqlFetch['id']);
    $onlineUser = $onlineUserObj->getRows();

    $themeData['list_online_user_id'] = $onlineUser['id'];
    $themeData['list_online_user_url'] = $onlineUser['url'];
    $themeData['list_online_user_username'] = $onlineUser['username'];
    $themeData['list_online_user_name'] = $onlineUser['name'];
    $themeData['list_online_user_thumbnail_url'] = $onlineUser['thumbnail_url'];
    $themeData['last_logged'] = $onlineUser['last_logged'];
    $themeData['list_online_user_unread'] = countMessages(array(
        "recipient_id" => $onlineUser['id'],
        "new" => true
    ));

    if ($onlineUser['online'] === true)
    {
        $themeData['online_status_html'] = \SocialKit\UI::view('online/online-indicator');
        $_SESSION['online_ids'][$oId] = true;
    }
    else
    {
        $themeData['online_status_html'] = \SocialKit\UI::view('online/offline-indicator');
    }

    $html .= \SocialKit\UI::view('online/li');
}

$data = array(
    "status" => 200,
    "html" => $html
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();