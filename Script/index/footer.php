<?php
$langArray = getLanguages();

$Languages = '';
foreach ($langArray as $language => $langUrl)
{
    $Languages .= '<a href="' . $langUrl . '">' . ucwords(str_replace('_', ' ', $language)) . '</a>';
}
$themeData['languages'] = $Languages;

$themeData['year'] = date('Y');

$themeData['about_url'] = smoothLink('index.php?a=terms&b=about');
$themeData['about_link'] = \SocialKit\UI::view('footer/links/about');

$themeData['contact_url'] = smoothLink('index.php?a=terms&b=contact');
$themeData['contact_link'] = \SocialKit\UI::view('footer/links/contact');

$themeData['privacy_url'] = smoothLink('index.php?a=terms&b=privacy');
$themeData['privacy_link'] = \SocialKit\UI::view('footer/links/privacy');

$themeData['tos_url'] = smoothLink('index.php?a=terms&b=tos');
$themeData['tos_link'] = \SocialKit\UI::view('footer/links/tos');

$themeData['disclaimer_url'] = smoothLink('index.php?a=terms&b=disclaimer');
$themeData['disclaimer_link'] = \SocialKit\UI::view('footer/links/disclaimer');

$themeData['developers_url'] = smoothLink('index.php?a=terms&b=developers');
$themeData['developers_link'] = \SocialKit\UI::view('footer/links/developers');

$themeData['admin_url'] = $config['site_url'] . '/admin/';
$themeData['admin_link'] = \SocialKit\UI::view('footer/links/admin');

if ($isLogged)
{
    if ($config['page_allow_create'] == 1)
    {
        $themeData['create_page_url'] = smoothLink('index.php?a=create-page');
        $themeData['create_page_link'] = \SocialKit\UI::view('footer/links/create-page');
    }

    if ($config['group_allow_create'] == 1)
    {
        $themeData['create_group_url'] = smoothLink('index.php?a=create-group');
        $themeData['create_group_link'] = \SocialKit\UI::view('footer/links/create-group');
    }
}

$themeData['bottom_footer'] = \SocialKit\UI::view('footer/content');
$themeData['sidebar_footer'] = \SocialKit\UI::view('footer/sidebar');
$themeData['welcome_footer'] = \SocialKit\UI::view('footer/welcome');