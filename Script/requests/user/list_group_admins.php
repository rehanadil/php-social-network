<?php
if (!empty($_GET['timeline_id']) && !empty($_GET['before_id']))
{
    $timelineId = (int) $_GET['timeline_id'];
    $beforeId = (int) $_GET['before_id'];

    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $listAdmins = '';
    
    foreach ($timelineObj->getGroupAdmins('', $beforeId) as $adminId)
    {
        $adminObj = new \SocialKit\User();
        $adminObj->setId($adminId);
        $admin = $adminObj->getRows();

        $themeData['list_admin_user_id'] = $admin['id'];
        $themeData['list_admin_user_url'] = $admin['url'];
        $themeData['list_admin_username'] = $admin['username'];
        $themeData['list_admin_user_thumbnail_url'] = $admin['thumbnail_url'];
        $themeData['list_admin_user_name'] = $admin['name'];

        if ($listAdminButtons)
        {
            $themeData['list_admins_btn'] = \SocialKit\UI::view('timeline/group/list-admins-btn');
        }

        $listAdmins .= \SocialKit\UI::view('timeline/group/list-admins-userlist-each');

        foreach($foreach_indexes as $fei => $fev) {
            $themeData[$fev] = null;
        }
    }

    $data = array(
        'status' => 200,
        'html' => $listAdmins
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();