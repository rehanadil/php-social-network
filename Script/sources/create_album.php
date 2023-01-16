<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}

if ($user['subscription_plan']['create_albums'] == 0)
{
	if ($ajax)
		$ajaxUrl = subscriptionUrl();
	else
		header("Location: " . subscriptionUrl());
}

$themeData['page_title'] = $lang['album_create_label'];

$themeData['user_suggestions'] = getUserSuggestions('', 5);
$themeData['page_suggestions'] = getPageSuggestions('', 5);
$themeData['group_suggestions'] = getGroupSuggestions('', 5);
$themeData['trendings'] = getTrendings('popular');


$themeData['page_content'] = \SocialKit\UI::view('album/create/content');
?>