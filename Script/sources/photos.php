<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}

$footer = false;
$themeData['page_title'] = $lang['photos'];

$photos = $userObj->getPhotos();
$lis = "";
foreach ($photos as $photo)
{
	$themeData['li_story_id'] = $photo['post_id'];
	$themeData['li_photo'] = $photo['url'];
	$lis .= \SocialKit\UI::view('photos/li');
}

if (empty($lis))
{
	$lis = \SocialKit\UI::view('photos/none');
}

$themeData['li'] = $lis;
$themeData['page_content'] = \SocialKit\UI::view('photos/content');