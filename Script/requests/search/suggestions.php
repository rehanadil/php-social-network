<?php
userOnly();

$type = "";
if (!empty($_GET['type'])) $type = $_GET['type'];
if (!isset($_GET['q'])) $_GET['q'] = "";

$search_query = $escapeObj->stringEscape($_GET['q']);
$html = '';

if (in_array($type, array('user','page','group')))
{
    switch ($type) {
        case 'group':
            $getSuggestionFunc = "getGroupSuggestionsInfo";
            break;
        
        case 'page':
            $getSuggestionFunc = "getPageSuggestionsInfo";
            break;

        default:
            $getSuggestionFunc = "getUserSuggestionsInfo";
            break;
    }
    foreach ($getSuggestionFunc($search_query, 5) as $k => $v)
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($v);
        $timeline = $timelineObj->getRows();
        
        if ($timeline['type'] === "user")
        {
            $themeData['list_user_id'] = $timeline['id'];
            $themeData['list_user_url'] = $timeline['url'];
            $themeData['list_user_username'] = $timeline['username'];
            $themeData['list_user_name'] = $timeline['name'];
            $themeData['list_user_thumbnail_url'] = $timeline['thumbnail_url'];
            $themeData['list_user_info'] = "@".$timeline['username'];
            $themeData['list_user_button'] = $timelineObj->getFollowButton();
        }
        elseif ($timeline['type'] === "page")
        {
            $themeData['list_page_id'] = $timeline['id'];
            $themeData['list_page_url'] = $timeline['url'];
            $themeData['list_page_username'] = $timeline['username'];
            $themeData['list_page_name'] = $timeline['name'];
            $themeData['list_page_thumbnail_url'] = $timeline['thumbnail_url'];
            $category = getPageCategoryData($timeline['category_id']);
            $themeData['list_page_info'] = $category['name'];
            $themeData['list_page_button'] = $timelineObj->getFollowButton();
        }
        elseif ($timeline['type'] === "group")
        {
            $themeData['list_group_id'] = $timeline['id'];
            $themeData['list_group_url'] = $timeline['url'];
            $themeData['list_group_username'] = $timeline['username'];
            $themeData['list_group_name'] = $timeline['name'];
            $themeData['list_group_thumbnail_url'] = $timeline['thumbnail_url'];
            $themeData['list_group_info'] = ucwords($timeline['group_privacy']) . ' Group';
            $themeData['list_group_button'] = $timelineObj->getFollowButton();
        }

        $html .= \SocialKit\UI::view('suggestions/' . $timeline['type'] . '-column');
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