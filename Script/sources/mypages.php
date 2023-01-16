<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['my_pages'];

// Suggestions
$themeData['user_suggestions'] = getUserSuggestions('', 5);
$themeData['page_suggestions'] = getPageSuggestions('', 5);
$themeData['group_suggestions'] = getGroupSuggestions('', 5);

// Trending
$themeData['trendings'] = getTrendings('popular');

// My Pages
$i = 0;
$listManagedPages = '';
foreach (getMyPages() as $myPage)
{
	$themeData['page_id'] = $myPage['id'];
	$themeData['page_url'] = $myPage['url'];
	$themeData['page_username'] = $myPage['username'];
	$themeData['page_thumbnail_url'] = $myPage['thumbnail_url'];
	$themeData['page_name'] = $myPage['name'];

	$category = getPageCategoryData($myPage['category_id']);
    $themeData['page_info'] = $category['name'];
	
	$listManagedPages .= \SocialKit\UI::view('mypages/li');
	$i++;
}
if ($i < 1) $listManagedPages = \SocialKit\UI::view('mypages/none');
$themeData['my_pages'] = $listManagedPages;

// Page Content
$themeData['page_content'] = \SocialKit\UI::view('mypages/content');
