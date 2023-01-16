<?php
if (!empty($_COOKIE['sk_u_i']) && !empty($_COOKIE['sk_u_p']))
{
    $u_i = $escapeObj->stringEscape($_COOKIE['sk_u_i']);
    $u_p = $escapeObj->stringEscape($_COOKIE['sk_u_p']);
    
    $_SESSION['user_id'] = $u_i;
    $_SESSION['user_pass'] = $u_p;

    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=home');
    else
        header('Location: ' . smoothLink('index.php?a=home'));
}

require_once("assets/includes/core.php");

if (isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=home');
    else
        header('Location: ' . smoothLink('index.php?a=home'));
}

$themeData['page_title'] = $config['site_title'];

if ($config['smooth_links'] == 1)
{
    $themeData['facebook_login_url'] = $config['site_url'] . '/social-login/facebook';
    $themeData['google_login_url'] = $config['site_url'] . '/social-login/google';
    $themeData['twitter_login_url'] = $config['site_url'] . '/social-login/twitter';
    $themeData['instagram_login_url'] = $config['site_url'] . '/social-login/instagram';
}
else
{
    $themeData['facebook_login_url'] = $config['site_url'] . '/social-login.php?site=facebook';
    $themeData['google_login_url'] = $config['site_url'] . '/social-login.php?site=google';
    $themeData['twitter_login_url'] = $config['site_url'] . '/social-login.php?site=twitter';
    $themeData['instagram_login_url'] = $config['site_url'] . '/social-login.php?site=instagram';
}

$themeData['facebook_login'] = ($config['facebook_login'] == 1) ? \SocialKit\UI::view('welcome/facebook') : "";
$themeData['google_login'] = ($config['google_login'] == 1) ? \SocialKit\UI::view('welcome/google') : "";
$themeData['twitter_login'] = ($config['twitter_login'] == 1) ? \SocialKit\UI::view('welcome/twitter') : "";
$themeData['instagram_login'] = ($config['instagram_login'] == 1) ? \SocialKit\UI::view('welcome/instagram') : "";

/* User Profiles */
$userProfileImages = array();
$userProfileUrls = array();
$userProfileIds = array();
$upQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT id FROM " . DB_USERS . ") AND avatar_id>0 AND active=1 AND banned=0 AND type='user' ORDER BY RAND() LIMIT 50");
while ($upFetch = $upQuery->fetch_array(MYSQLI_ASSOC))
{
    $upObj = new \SocialKit\User();
    $upObj->setId($upFetch['id']);
    $up = $upObj->getRows();
    if (file_exists(str_replace($config['site_url'] . '/', '', $up['thumbnail_url'])))
    {
        $userProfileImages[] = $up['thumbnail_url'];
        $userProfileUrls[] = $up['url'];
        $userProfileIds[] = $up['id'];
    }
}
$themeData['user_profile_images'] = json_encode($userProfileImages);
$themeData['user_profile_urls'] = json_encode($userProfileUrls);
$themeData['user_profile_ids'] = json_encode($userProfileIds);

/* Tabs */
if ($config['disable_registration'] == 0)
{
    if ($config['reg_req_birthday'] == true) $themeData['signup_birthday_input'] = \SocialKit\UI::view('welcome/signup-birthday-input');
    if ($config['captcha'] == true) $themeData['signup_captcha_input'] = \SocialKit\UI::view('welcome/signup-captcha-input');
    
    $themeData['signup_form'] = \SocialKit\UI::view('welcome/signup-form');
}

if (isset($_GET['b']) && $_GET['b'] === "password_reset")
{
    if (isset($_GET['id']) && isValidPasswordResetToken($_GET['id']))
        $themeData['password_reset_form'] = \SocialKit\UI::view('welcome/password-reset-form');
}

/* Background Videos */
$videoDir = 'themes/' . $config['theme'] . '/wbg-videos';
$videoData = array();
if (file_exists($videoDir))
{
    $videos = glob($videoDir . '/*.mp4');
    foreach ($videos as $video)
    {
        $videoData[] = str_replace($videoDir . '/', '', $video);
    }

    if (!isset($_SESSION['wbg_video'])
        || $_SESSION['wbg_video'] >= count($videos)) $_SESSION['wbg_video'] = 0;

    $themeData['wbg_video_url'] = $config['theme_url'] . '/wbg-videos/' . $videoData[$_SESSION['wbg_video']];
    $_SESSION['wbg_video']++;
}
$themeData['wbg_videos'] = json_encode($videoData);

/* Background Images */
$imageDir = 'themes/' . $config['theme'] . '/wbg-images';
$imageData = array();
if (file_exists($imageDir))
{
    $images = glob($imageDir . '/*.{jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG}', GLOB_BRACE);
    foreach ($images as $image)
    {
        $imageData[] = str_replace($imageDir . '/', '', $image);
    }

    if (!isset($_SESSION['wbg_image'])
        || $_SESSION['wbg_image'] >= count($images)) $_SESSION['wbg_image'] = 0;

    $themeData['wbg_image_url'] = $config['theme_url'] . '/wbg-images/' . $imageData[$_SESSION['wbg_image']];
    $_SESSION['wbg_image']++;
}
$themeData['wbg_images'] = json_encode($imageData);


$themeData['page_content'] = \SocialKit\UI::view('welcome/content');