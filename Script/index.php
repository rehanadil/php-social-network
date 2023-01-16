<?php
/**
 * @package Social Kit - Social Networking Platform v2.5
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2016 Rehan Adil. All rights reserved.
**/

$ajax = false;
if (!isset($_GET['a'])) $_GET['a'] = 'welcome';

require_once('assets/includes/core.php');
$isLogged = isLogged();

foreach ($_GET as $key => $value)
{
    $themeData['get_' . $escapeObj->stringEscape(strtolower($key))] = $escapeObj->stringEscape($value);
}

if ($isLogged)
{
	$config['user_default_avatar'] = ($user['gender'] === "male") ? $config['user_default_male_avatar'] : $config['user_default_female_avatar'];
	$themeData['config_user_default_avatar'] = $config['user_default_avatar'];
}

require_once('index/nav.php');
require_once('index/header.php');
require_once('index/footer.php');
require_once('index/page.php');
require_once('index/header_tags.php');
require_once('index/footer_tags.php');

$container = \SocialKit\UI::view('container');
if ($isLogged && $config['chat'] == 1) $container .= \SocialKit\UI::view('chat/container');

echo $container;
$conn->close();
