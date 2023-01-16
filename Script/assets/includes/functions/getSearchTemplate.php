<?php
function getSearchTemplate($a, $b, $c)
{
    global $themeData, $lang;
    $results = getSearch($a, $b, $c);
    $template = '';

    if (is_array($results))
    {
        foreach ($results as $k => $v)
        {
            $timelineObj = new \SocialKit\User();
            $timelineObj->setId($v);
            $timeline = $timelineObj->getRows();

            $themeData['search_user_id'] = $timeline['id'];
            $themeData['search_user_url'] = $timeline['url'];
            $themeData['search_user_username'] = $timeline['username'];
            $themeData['search_user_thumbnail_url'] = $timeline['thumbnail_url'];
            $themeData['search_user_name'] = $timeline['name'];

            if ($timeline['type'] == "user")
            {
                $themeData['search_user_info'] = '@'.$timeline['username'];
            }
            elseif ($timeline['type'] == "page")
            {
                $category = getPageCategoryData($timeline['category_id']);
                $themeData['search_user_info'] = $category['name'];
            }
            elseif ($timeline['type'] == "group")
            {
                $themeData['search_user_info'] = $lang[$timeline['group_privacy'] . '_group'];
            }

            $template .= \SocialKit\UI::view('header/search-result');
        }
    }
    
    return $template;
}
