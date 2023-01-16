<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}

$footer = false;
$themeData['page_title'] = $lang['home_label'];

/* */
$themeData['announcements'] = getAnnouncements();
$themeData['story_postbox'] = getStoryPostBox();

/* Story Filters */
$themeData['story_filters'] = storyFilters();

/* Stories */
$feedObj = new \SocialKit\Feed();
$themeData['stories'] = $feedObj->getTemplate();

/* */

$themeData['page_content'] = \SocialKit\UI::view('home/content');