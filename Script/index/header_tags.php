<?php
if ($config['smooth_links'] == 1)
{
	$themeData['request_source'] = $config['request_source'];
	$themeData['page_source'] = $config['page_path'];
}
else
{
	$themeData['request_source'] = 'request.php';
	$themeData['page_source'] = 'page.php';
}

$themeData['page_title'] = (isset($themeData['page_title'])) ? $themeData['page_title'] : $config['site_title'];

if (!isset($themeData['site_keywords'])) $themeData['header_keywords'] = $config['site_keywords'];

if (!isset($themeData['site_description'])) $themeData['header_description'] = $config['site_description'];

$themeData['header_metatags'] = \SocialKit\UI::view('global/header/metatags');

$themeData['header_title'] = \SocialKit\UI::view('global/header/title');

$themeData['header_favicon'] = \SocialKit\UI::view('global/header/favicon');

$themeData['header_stylesheets'] = \SocialKit\UI::view('global/header/stylesheets');

if (isLogged()) $themeData['pop_state'] = \SocialKit\UI::view('global/header/popstate');
$themeData['header_scripts'] = \SocialKit\UI::view('global/header/scripts');

$themeData['header_tags'] = \SocialKit\UI::view('global/header/all', array(
	'key' => 'head_tags_add_content',
	'return' => 'string',
	'type' => 'APPEND',
	'content' => array()
));

if ($_GET['a'] === "welcome") $themeData['header_tags'] .=\SocialKit\UI::view('welcome/header');
if ($_GET['a'] === "start") $themeData['header_tags'] .=\SocialKit\UI::view('start/header');
if (isLogged()
	&& $config['chat'] == 1
	&& $user['subscription_plan']['chat'] == 1) $themeData['header_tags'] .= '<link rel="stylesheet" href="' . $config['theme_url'] . '/css/chat.css">';