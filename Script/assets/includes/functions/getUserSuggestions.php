<?php
function getUserSuggestions($searchQuery='', $limit=5)
{
    global $lang;

    if (! isLogged()) return '';
    
    global $themeData;
    $suggestionsHtml = '';

    foreach (getUserSuggestionsInfo($searchQuery, $limit) as $k => $v)
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($v);
        $timeline = $timelineObj->getRows();
        
        $themeData['list_user_id'] = $timeline['id'];
        $themeData['list_user_url'] = $timeline['url'];
        $themeData['list_user_username'] = $timeline['username'];
        $themeData['list_user_name'] = $timeline['name'];
        $themeData['list_user_thumbnail_url'] = $timeline['thumbnail_url'];
        $themeData['list_user_info'] = '@'.$timeline['username'];

        $themeData['list_user_button'] = $timelineObj->getFollowButton();
        $suggestionsHtml .= \SocialKit\UI::view('suggestions/user-column');
    }

    $themeData['list_users'] = $suggestionsHtml;
    
    if (!empty($suggestionsHtml))
    {
        return \SocialKit\UI::view('suggestions/users', array(
            'key' => 'user_suggestions_ui_editor',
            'return' => 'string',
            'type' => 'APPEND',
            'content' => array()
        ));
    }
}
