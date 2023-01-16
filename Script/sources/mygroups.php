<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['my_groups'];

/* Suggestions */
$themeData['user_suggestions'] = getUserSuggestions('', 5);
$themeData['page_suggestions'] = getPageSuggestions('', 5);
$themeData['group_suggestions'] = getGroupSuggestions('', 5);

/* Trending */
$themeData['trendings'] = getTrendings('popular');

/* My Pages */
$i = 0;
$listManagedPages = '';

foreach (getMyGroups() as $myGroup)
{
	$themeData['group_id'] = $myGroup['id'];
	$themeData['group_url'] = $myGroup['url'];
	$themeData['group_username'] = $myGroup['username'];
	$themeData['group_thumbnail_url'] = $myGroup['cover_url'];
	$themeData['group_name'] = $myGroup['name'];
	$themeData['group_info'] = $lang[strtolower(($myGroup['group_privacy'])) . '_group'];
	
	$listManagedPages .= \SocialKit\UI::view('mygroups/li');
	$i++;
}

if ($i < 1) $listManagedPages = \SocialKit\UI::view('mygroups/none');

$themeData['my_groups'] = $listManagedPages;

/* */

$themeData['page_content'] = \SocialKit\UI::view('mygroups/content');
