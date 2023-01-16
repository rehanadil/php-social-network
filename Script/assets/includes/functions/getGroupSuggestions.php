<?php
function getGroupSuggestions($searchQuery='', $limit=5)
{
    if (!isLogged()) return '';
    
    global $config, $lang, $user, $themeData;
    $currentThemeData = $themeData;

    if ($config['group_allow_create'] == 1
        && $user['subscription_plan']['create_groups'] == 1)
    {
        $themeData['new_group_link'] = \SocialKit\UI::view('suggestions/new-group');
    }

    $suggestionsHtml = '';
    foreach (getGroupSuggestionsInfo($searchQuery, $limit) as $k => $v)
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($v);
        $timeline = $timelineObj->getRows();
        
        $themeData['list_group_id'] = $timeline['id'];
        $themeData['list_group_url'] = $timeline['url'];
        $themeData['list_group_username'] = $timeline['username'];
        $themeData['list_group_name'] = $timeline['name'];
        $themeData['list_group_thumbnail_url'] = $timeline['cover_url'];
        $themeData['list_group_info'] = $lang[strtolower(($timeline['group_privacy'])) . '_group'];

        $themeData['list_group_button'] = $timelineObj->getFollowButton();

        $suggestionsHtml .= \SocialKit\UI::view('suggestions/group-column');
    }
    $themeData['list_groups'] = $suggestionsHtml;

    if (empty($suggestionsHtml)) return '';

    $return = \SocialKit\UI::view('suggestions/groups', array(
        'key' => 'suggestions_ui_editor',
        'return' => 'string',
        'type' => 'APPEND',
        'content' => array()
    ));
    $themeData = $currentThemeData;
    return $return;
}