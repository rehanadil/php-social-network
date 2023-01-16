<?php
userOnly();
$listResults = false;

if (!empty($_GET['query']))
{
    $search_query = $escapeObj->stringEscape($_GET['query']);
    $i = 0;
    $results = getSearch($search_query, 0, 50);

    if (is_array($results))
    {
	    foreach (getSearch($search_query, 0, 50) as $searchId)
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
	}

	if (empty($listResults)) $listResults = \SocialKit\UI::view('search/none');
}

if (empty($listResults)) $listResults = \SocialKit\UI::view('search/greet');

$data = array(
	"status" => 200,
	"html" => $listResults
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();