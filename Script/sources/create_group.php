<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}
if ($user['subscription_plan']['create_groups'] == 0)
{
	if ($ajax)
		$ajaxUrl = subscriptionUrl();
	else
		header('Location: ' . subscriptionUrl());
}

if ($config['group_allow_create'] == 1)
{
	$themeData['page_title'] = $lang['group_create_label'];

	if (! isLogged())
	{
	    header('Location: ' . smoothLink('index.php?a=logout'));
	}

	$themeData['page_content'] = \SocialKit\UI::view('timeline/group/create/content');
}