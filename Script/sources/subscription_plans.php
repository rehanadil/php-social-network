<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}

if (isset($_GET['b'])
	&& $_GET['b'] === "upgrade")
{
	include('upgrade.php');
}
else
{
	if ($user['subscription_plan']['is_default'] == 1)
	{
		if ($ajax)
			$ajaxUrl = smoothLink('index.php?a=home');
		else
			header('Location: ' . smoothLink('index.php?a=home'));
	}
	$planName = '<span>' . $user['subscription_plan']['name'] . '</span>';
	$themeData['currently_subscribed_to'] = str_replace('{name}', $planName, $lang['subscription_plan_currently_subscribed_to']);
	$themeData['page_content'] = \SocialKit\UI::view('subscription-plans/content');
}