<?php
if (!empty($_GET['timeline_id']) && !empty($_GET['before_id']))
{
    $timelineId = (int) $_GET['timeline_id'];
    $beforeId = (int) $_GET['before_id'];

    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $listPageLikes = '';
    
    foreach ($timelineObj->getLikedPages('', $beforeId) as $pageId)
    {
        $pageObj = new \SocialKit\User();
        $pageObj->setId($pageId);
        $page = $pageObj->getRows();

        $themeData['list_page_id'] = $page['id'];
        $themeData['list_page_url'] = $page['url'];
        $themeData['list_page_username'] = $page['username'];
        $themeData['list_page_name'] = $page['name'];
        $themeData['list_page_thumbnail_url'] = $page['thumbnail_url'];

        $themeData['list_like_button'] = $pageObj->getFollowButton();

        $listPageLikes .= \SocialKit\UI::view('timeline/user/list-page-likes-each');
    }

    $data = array(
        'status' => 200,
        'html' => $listPageLikes
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();