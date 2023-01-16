<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}

$footer = false;
$themeData['page_title'] = $lang['albums'];

$albums = $userObj->getAlbums();
$lis = "";
foreach ($albums as $album)
{
	$themeData['li_preview'] = $album['preview'];
	$themeData['li_id'] = $album['id'];
	$themeData['li_name'] = $album['name'];
	$themeData['li_url'] = smoothLink('index.php?a=album&b=' . $album['id']);
	$lis .= \SocialKit\UI::view('albums/li');
}

if (empty($lis))
{
	$lis = \SocialKit\UI::view('albums/none');
}

$themeData['li'] = $lis;
$themeData['page_content'] = \SocialKit\UI::view('albums/content');