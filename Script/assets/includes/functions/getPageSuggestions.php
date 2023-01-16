<?php
function getPageSuggestions($searchQuery='', $limit=5)
{
    if (!isLogged()) return '';
    
    global $config, $lang, $user, $themeData;
    $currentThemeData = $themeData;

    if ($config['page_allow_create'] == 1
        && $user['subscription_plan']['create_pages'] == 1)
    {
        $themeData['new_page_link'] = \SocialKit\UI::view('suggestions/new-page');
    }

    $suggestionsHtml = '';
    foreach (getPageSuggestionsInfo($searchQuery, $limit) as $k => $v)
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($v);
        $timeline = $timelineObj->getRows();
        
        $themeData['list_page_id'] = $timeline['id'];
        $themeData['list_page_url'] = $timeline['url'];
        $themeData['list_page_username'] = $timeline['username'];
        $themeData['list_page_name'] = $timeline['name'];
        $themeData['list_page_thumbnail_url'] = $timeline['thumbnail_url'];
        
        $category = getPageCategoryData($timeline['category_id']);
        $themeData['list_page_info'] = $category['name'];
        $themeData['list_page_button'] = $timelineObj->getFollowButton();

        $suggestionsHtml .= \SocialKit\UI::view('suggestions/page-column');
    }
    $themeData['list_pages'] = $suggestionsHtml;
    
    if (empty($suggestionsHtml)) return '';

    $return = \SocialKit\UI::view('suggestions/pages', array(
        'key' => 'page_suggestions_ui_editor',
        'return' => 'string',
        'type' => 'APPEND',
        'content' => array()
    ));
    $themeData = $currentThemeData;
    return $return;
}