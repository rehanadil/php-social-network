<?php
if (!empty($_GET['before_id']))
{
    $beforeId = (int) $_GET['before_id'];
    $listFollowRequests = '';
    
    foreach ($userObj->getFollowRequests('', $beforeId) as $requestId)
    {
        $requestObj = new \SocialKit\User();
        $requestObj->setId($requestId);
        $request = $requestObj->getRows();

        $themeData['list_request_id'] = $request['id'];
        $themeData['list_request_url'] = $request['url'];
        $themeData['list_request_username'] = $request['username'];
        $themeData['list_request_name'] = $request['name'];
        $themeData['list_request_thumbnail_url'] = $request['thumbnail_url'];

        $themeData['list_request_button'] = \SocialKit\UI::view('timeline/user/follow-request-button');

        $listFollowRequests .= \SocialKit\UI::view('timeline/user/list-followrequests-each');
    }

    $data = array(
        'status' => 200,
        'html' => $listFollowRequests
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();