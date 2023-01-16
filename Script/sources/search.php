<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['search_header_label'];

// Suggestions
$themeData['user_suggestions'] = getUserSuggestions('', 5);
$themeData['page_suggestions'] = getPageSuggestions('', 5);
$themeData['group_suggestions'] = getGroupSuggestions('', 5);

// Events
$themeData['event_bar'] = getEventBar();

// Trending
$themeData['trendings'] = getTrendings('popular');
$listResults = false;

if (!empty($_GET['query']))
{
    $search_query = $escapeObj->stringEscape($_GET['query']);
    $i = 0;

    $themeData['page_title'] = $_GET['query'] . ' - ' . $lang['search_result_header_label'];

    foreach (getSearch($search_query, 0, 30) as $searchId)
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($searchId);
        $timeline = $timelineObj->getRows();

    	$themeData['list_search_id'] = $timeline['id'];
    	$themeData['list_search_url'] = $timeline['url'];
    	$themeData['list_search_username'] = $timeline['username'];
    	$themeData['list_search_name'] = $timeline['name'];
    	$themeData['list_search_thumbnail_url'] = $timeline['thumbnail_url'];

        if ($timeline['type'] == "user")
        {
            $themeData['list_search_info'] = '@'.$timeline['username'];
        }
        elseif ($timeline['type'] == "page")
        {
            $category = getPageCategoryData($timeline['category_id']);
            $themeData['list_search_info'] = $category['name'];
        }
        elseif ($timeline['type'] == "group")
        {
            $themeData['list_search_info'] = $lang[$timeline['group_privacy'] . '_group'];
        }

    	$themeData['list_search_button'] = $timelineObj->getFollowButton();

        $listResults .= \SocialKit\UI::view('search/list-each');
        $i++;
    }

    if (empty($listResults)) $listResults = \SocialKit\UI::view('search/none');
}

if (empty($listResults)) $listResults = \SocialKit\UI::view('search/greet');

$themeData['list_search_results'] = $listResults;
$themeData['page_content'] = \SocialKit\UI::view('search/content');
