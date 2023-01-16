<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}
if ($user['subscription_plan']['create_events'] == 0)
{
	if ($ajax)
		$ajaxUrl = subscriptionUrl();
	else
		header('Location: ' . subscriptionUrl());
}

$themeData['page_title'] = $lang['event_create_label'];

$themeData['current_date'] = date('Y/m/d', time() + (60 * 60 * 1));
$themeData['current_time'] = date('h:i A', time() + (60 * 60 * 1));

$themeData['finish_date'] = date('Y/m/d', time() + (60 * 60 * 6));
$themeData['finish_time'] = date('h:i A', time() + (60 * 60 * 6));

$themeData['page_content'] = \SocialKit\UI::view('timeline/event/create/content');
