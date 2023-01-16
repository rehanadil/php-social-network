<?php
if (!empty($_GET['timeline_id']) && !empty($_GET['before_id']))
{
    $timelineId = (int) $_GET['timeline_id'];
    $beforeId = (int) $_GET['before_id'];

    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $listFollowers = '';
    
    foreach ($timelineObj->getFollowers('', $beforeId) as $followerId)
    {
        $followerObj = new \SocialKit\User();
        $followerObj->setId($followerId);
        $follower = $followerObj->getRows();

        $themeData['list_follower_id'] = $follower['id'];
        $themeData['list_follower_url'] = $follower['url'];
        $themeData['list_follower_username'] = $follower['username'];
        $themeData['list_follower_name'] = $follower['name'];
        $themeData['list_follower_thumbnail_url'] = $follower['thumbnail_url'];

        $themeData['list_follower_button'] = $followerObj->getFollowButton();

        $listFollowers .= \SocialKit\UI::view('timeline/user/list-followers-each');
    }

    $data = array(
        'status' => 200,
        'html' => $listFollowers
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();