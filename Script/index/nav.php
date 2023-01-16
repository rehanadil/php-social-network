<?php
if ($isLogged)
{
    $themeData['home_page_url'] = smoothLink('index.php?a=home');
    $themeData['messages_page_url'] = smoothLink('index.php?a=messages');
    $themeData['following_page_url'] = 'index.php?a=timeline&b=requests&id=' . $user['username'];
    $themeData['settings_page_url'] = smoothLink('index.php?a=settings');
    $themeData['search_page_url'] = smoothLink('index.php?a=search');
    $themeData['savedposts_page_url'] = smoothLink('index.php?a=saved-posts');
    $themeData['recommended_users_url'] = smoothLink('index.php?a=recommended&b=users');
    $themeData['boostedposts_page_url'] = smoothLink('index.php?a=boosted-posts');

    $themeData['new_page'] = smoothLink('index.php?a=create-page');
    $themeData['my_pages_url'] = smoothLink('index.php?a=my-pages');
    $themeData['recommended_pages_url'] = smoothLink('index.php?a=recommended&b=pages');
    if ($config['page_allow_create'] == 1
        && $user['subscription_plan']['create_pages'] == 1) $themeData['dropdown_create_page'] = \SocialKit\UI::view('header/dropdown/create-page');

    $themeData['new_group'] = smoothLink('index.php?a=create-group');
    $themeData['my_groups_url'] = smoothLink('index.php?a=my-groups');
    $themeData['recommended_groups_url'] = smoothLink('index.php?a=recommended&b=groups');
    if ($config['group_allow_create'] == 1
        && $user['subscription_plan']['create_groups'] == 1) $themeData['dropdown_create_group'] = \SocialKit\UI::view('header/dropdown/create-group');

    $themeData['new_event'] = smoothLink('index.php?a=create-event');
    $themeData['events_page_url'] = smoothLink('index.php?a=events&b=upcoming');
    if ($config['event_allow_create'] == 1
        && $user['subscription_plan']['create_events'] == 1) $themeData['dropdown_create_event'] = \SocialKit\UI::view('header/dropdown/create-event');

    $themeData['new_album'] = smoothLink('index.php?a=album&b=create');
    $themeData['photos_page_url'] = smoothLink('index.php?a=photos');
    $themeData['albums_page_url'] = smoothLink('index.php?a=albums');

    $themeData['subscription_upgrade_url'] =smoothLink('index.php?a=subscription-plans&b=upgrade');
    $themeData['logout_url'] = smoothLink('index.php?a=logout');

    if ($user['admin'] == 1)
    {
        $themeData['admin_area_url'] = $config['site_url'] . '/admincp/';
        $themeData['admin_area'] = \SocialKit\UI::view('header/admin-area');
    }
}