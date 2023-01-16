<?php
$themeData['page_title'] = $lang['story'];

// Boosted Posts
if (isset($_GET['id']))
{
	$storyId = (int) $_GET['id'];
	$feedObj = new \SocialKit\Feed();
	$feedObj->setId($storyId);
	$feedObj->setHidden(true);
	$themeData['story'] = $feedObj->getTemplate();
}

$themeData['page_content'] = \SocialKit\UI::view('story-page/content');
