<?php
if (!empty($_GET['timeline_id']) && !empty($_GET['before_id']))
{
    $timelineId = (int) $_GET['timeline_id'];
    $beforeId = (int) $_GET['before_id'];

    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $listMembers = '';

    $themeData['timeline_id'] = $timelineId;
    
    foreach ($timelineObj->getFollowers('', $beforeId) as $memberId)
    {
        $memberObj = new \SocialKit\User();
        $memberObj->setId($memberId);
        $member = $memberObj->getRows();

        $themeData['list_member_user_id'] = $member['id'];
        $themeData['list_member_user_url'] = $member['url'];
        $themeData['list_member_username'] = $member['username'];
        $themeData['list_member_user_thumbnail_url'] = $member['thumbnail_url'];
        $themeData['list_member_user_name'] = $member['name'];

        if ($timelineObj->isGroupAdmin())
        {
            if (! $timelineObj->isGroupAdmin($member['id']))
            {
                $themeData['list_members_make_admin_btn'] = \SocialKit\UI::view('timeline/group/list-members-make-admin-btn');
            }

            $themeData['list_members_btn'] = \SocialKit\UI::view('timeline/group/list-members-btn');
        }

        $listMembers .= \SocialKit\UI::view('timeline/group/list-members-userlist-each');
    }

    $data = array(
        'status' => 200,
        'html' => $listMembers
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();