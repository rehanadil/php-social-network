<?php
if ($isLogged)
{
    $themeData['body_rewrite_attr'] = ($config['smooth_links'] == 1) ? 1 : 0;
    $themeData['welcome_page_url'] = smoothLink('index.php?a=welcome');

    $themeData['notif_num'] = countNotifications(0, true);
    $themeData['messages_num'] = countMessages(array("new" => true));
    $themeData['follow_requests_num'] = countFollowRequests();

    $themeData['following_page_url'] = ($themeData['follow_requests_num'] == 0) ? 'index.php?a=timeline&b=following&id=' . $user['username'] : $themeData['following_page_url'];
    $themeData['following_label'] = ($config['friends'] == true) ? $lang['header_friends_label'] : $lang['header_following_label'];

    $themeData['following_page_url_smoothless'] = str_replace('index.php', '', $themeData['following_page_url']);
    $themeData['following_page_url'] = smoothLink($themeData['following_page_url']);
    $themeData['header_dropdowns'] = \SocialKit\UI::view('header/user-dropdowns');
    $themeData['header_navigation'] = \SocialKit\UI::view('header/user-navigation');

    $themeData['header_search'] = \SocialKit\UI::view('header/search');
    
    if ($config['chat'] == 1
        && $user['subscription_plan']['chat'] == 1)
    {
        $themeData['online_sidebar'] = \SocialKit\UI::view('online/content');
        if (!isset($_SESSION['chat_friends'])) $_SESSION['chat_friends'] = array();
        $themeData['chat_friends'] = json_encode($_SESSION['chat_friends']);
    }
}

if ($_GET['a'] !== "welcome") $themeData['header'] = \SocialKit\UI::view('header/content');