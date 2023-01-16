<?php
userOnly();

if (!isset($_GET['query'])) $_GET['query'] = "";

$search = $escapeObj->stringEscape($_GET['query']);
$limit = (isset($_GET['limit'])) ? (int) $_GET['limit'] : 10;
$start = (isset($_GET['start'])) ? (int) $_GET['start'] : 0;
$timelineId = (isset($_GET['timeline_id'])) ? (int) $_GET['timeline_id'] : $user['id'];
$html = "";
$i = 0;
$peopleInfo = array(
    'search' => $search,
    'limit' => $limit,
    'start' => $start,
    'timeline_id' => $timelineId
);
$people = getMessagingPeople($peopleInfo);

if ($people)
{
    foreach ($people as $pid => $text)
    {
        $personObj = new \SocialKit\User();
        $personObj->setId($pid);
        $person = $personObj->getRows();

        $themeData['receiver_id'] = $person['id'];
        $themeData['receiver_username'] = $person['username'];
        $themeData['receiver_name'] = $person['name'];
        $themeData['receiver_thumbnail_url'] = $person['thumbnail_url'];
        $themeData['receiver_online'] = 0;
        $themeData['receiver_unread'] = countMessages(array("recipient_id" => $person['id'], "new" => true));

        $text = $escapeObj->getEmoticons($text);
        $text = $escapeObj->getLinks($text);
        $text = $escapeObj->getHashtags($text);
        $text = $escapeObj->getMentions($text);

        $themeData['receiver_message'] = substr(strip_tags($text), 0, 125) . '...';
        $html .= (isset($_GET['header'])) ? \SocialKit\UI::view('header/message-column') : \SocialKit\UI::view('messages/people-column');
    }

    $data = array(
        'status' => 200,
        'html' => $html
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();