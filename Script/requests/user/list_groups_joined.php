<?php
if (!empty($_GET['timeline_id']) && !empty($_GET['before_id']))
{
    $timelineId = (int) $_GET['timeline_id'];
    $beforeId = (int) $_GET['before_id'];

    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $listGroupsJoined = '';
    
    foreach ($timelineObj->getGroupsJoined('', $beforeId) as $groupId)
    {
        $groupObj = new \SocialKit\User();
        $groupObj->setId($groupId);
        $group = $groupObj->getRows();

        $themeData['list_group_id'] = $group['id'];
        $themeData['list_group_url'] = $group['url'];
        $themeData['list_group_username'] = $group['username'];
        $themeData['list_group_name'] = $group['name'];
        $themeData['list_group_thumbnail_url'] = $group['thumbnail_url'];

        $themeData['list_join_button'] = $groupObj->getFollowButton();

        $listGroupsJoined .= \SocialKit\UI::view('timeline/user/list-groups-joined-each');
    }

    $data = array(
        'status' => 200,
        'html' => $listGroupsJoined
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();