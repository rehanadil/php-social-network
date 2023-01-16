<?php
$themeData['page_title'] = $lang['terms_tos_label'];

$themeData['terms_tos_url'] = smoothLink('index.php?a=terms&b=tos');
$themeData['terms_privacy_url'] = smoothLink('index.php?a=terms&b=privacy');
$themeData['terms_disclaimer_url'] = smoothLink('index.php?a=terms&b=disclaimer');
$themeData['terms_contact_url'] = smoothLink('index.php?a=terms&b=contact');
$themeData['terms_about_url'] = smoothLink('index.php?a=terms&b=about');
$themeData['terms_developers_url'] = smoothLink('index.php?a=terms&b=developers');

$termType = null;
$termsContent = '';

if (isset($_GET['b']))
{
    $termType = $_GET['b'];
}

switch($termType)
{
    case 'privacy':
        $termsContent = \SocialKit\UI::view('terms/privacy');
    break;
    
    case 'disclaimer':
        $termsContent = \SocialKit\UI::view('terms/disclaimer');
    break;
    
    case 'contact':
        $termsContent = \SocialKit\UI::view('terms/contact');
    break;
    
    case 'about':
        $termsContent = \SocialKit\UI::view('terms/about');
    break;

    case 'developers':
        $termsContent = \SocialKit\UI::view('terms/developers');
    break;
    
    default:
        $termsContent = \SocialKit\UI::view('terms/tos');
}

$themeData['terms_content'] = $termsContent;
$themeData['page_content'] = \SocialKit\UI::view('terms/content');
