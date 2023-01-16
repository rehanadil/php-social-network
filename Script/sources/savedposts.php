<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['saved_posts'];

// Boosted Posts
$feedObj = new \SocialKit\Feed();
$feedObj->setSaved(true);
$themeData['saved_posts'] = $feedObj->getTemplate();

// Suggestions
$themeData['user_suggestions'] = getUserSuggestions('', 5);
$themeData['page_suggestions'] = getPageSuggestions('', 5);
$themeData['group_suggestions'] = getGroupSuggestions('', 5);

// Events
$themeData['event_bar'] = getEventBar();

// Trending
$themeData['trendings'] = getTrendings('popular');


$themeData['page_content'] = \SocialKit\UI::view('saved-posts/content');
