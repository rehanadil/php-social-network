<?php
if (!empty($_GET['timeline_id']) && !empty($_GET['before_id']))
{
    $timelineId = (int) $_GET['timeline_id'];
    $beforeId = (int) $_GET['before_id'];

    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $listFollowings = '';
    
    foreach ($timelineObj->getFollowings('', $beforeId) as $followingId)
    {
        $followingObj = new \SocialKit\User();
        $followingObj->setId($followingId);
        $following = $followingObj->getRows();

        $themeData['list_following_id'] = $following['id'];
        $themeData['list_following_url'] = $following['url'];
        $themeData['list_following_username'] = $following['username'];
        $themeData['list_following_name'] = $following['name'];
        $themeData['list_following_thumbnail_url'] = $following['thumbnail_url'];

        $themeData['list_following_button'] = $followingObj->getFollowButton();

        $listFollowings .= \SocialKit\UI::view('timeline/user/list-followings-each');
    }

    $data = array(
        'status' => 200,
        'html' => $listFollowings
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();