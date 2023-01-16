<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['trending_header_label'];

// Suggestions
$themeData['user_suggestions'] = getUserSuggestions('', 5);
$themeData['page_suggestions'] = getPageSuggestions('', 5);
$themeData['group_suggestions'] = getGroupSuggestions('', 5);

// Events
$themeData['event_bar'] = getEventBar();

// Trending
$themeData['trendings'] = getTrendings('popular');


if (! empty($_GET['query']))
{
    $themeData['page_title'] = '#' . $_GET['query'];
    $themeData['stories'] = getTrendPosts($_GET['query']);
}

if ($config['smooth_links'] == 1)
    $themeData['end'] = \SocialKit\UI::view('hashtag/smooth-end');
else
    $themeData['end'] = \SocialKit\UI::view('hashtag/default-end');
/* */

$themeData['page_content'] = \SocialKit\UI::view('hashtag/content');
