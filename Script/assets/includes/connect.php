<?php
$config = array();
$confQuery = $conn->query("SELECT * FROM " . DB_CONFIGURATIONS);
$config = $confQuery->fetch_array(MYSQLI_ASSOC);

$config['site_url'] = $site_url;
$config['theme_url'] = $site_url . '/themes/' . $config['theme'];
$config['script_path'] = str_replace('index.php', '', $_SERVER['PHP_SELF']);
$config['request_source'] = $config['script_path'] . 'request.php';
$config['page_path'] = $config['script_path'] . 'page.php';

$config['google_analytics'] = html_entity_decode($config['google_analytics']);
$config['ad_place_home'] = html_entity_decode($config['ad_place_home']);
$config['ad_place_messages'] = html_entity_decode($config['ad_place_messages']);
$config['ad_place_timeline'] = html_entity_decode($config['ad_place_timeline']);
$config['ad_place_hashtag'] = html_entity_decode($config['ad_place_hashtag']);
$config['ad_place_search'] = html_entity_decode($config['ad_place_search']);

if (!isset($_SESSION['language'])) $_SESSION['language'] = $config['language'];

foreach ($config as $cnm => $cfg)
{
    define(strtoupper($cnm), $cfg);
    $themeData['config_' . $cnm] = $cfg;
}

// Login verification and user stats update
$logged = false;
$user = null;

if (isLogged())
{
    $userObj = new \SocialKit\User();
    $userObj->setId($_SESSION['user_id']);
    $user = $userObj->getRows();

    if (isset($user['id']) && $user['type'] == "user")
    {
        $logged = true;
        
        if ($user['go_offline'] != 1) $conn->query("UPDATE " . DB_ACCOUNTS . " SET last_logged=" . time() . " WHERE id=" . $user['id']);
        if (!empty($user['language'])) $_SESSION['language'] = $user['language'];
        
        if (!isset($_SESSION['tempche_user_ownfollowing']))
        {
            $conn->query("DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . " AND following_id=" . $user['id']);
            $_SESSION['tempche_user_ownfollowing'] = true;
        }

        $user['location'] = $user['current_city'];
        $themeData['is_offline'] = $user['go_offline'];
        Sk_getArrayTheme($user, 'user');
    }
}

$sk['logged'] = $logged;

// Fetch preferred language
if (!empty($_GET['lang']))
{
    $cacheObj = new \SocialKit\Cache();
    $cacheObj->setType('user');
    $cacheObj->setId($user['id']);
    $cacheObj->prepare();

    if ($cacheObj->exists())
    {
        $cacheObj->clear();
    }
    
    $_GET['lang'] = strtolower(preg_replace('/[^A-Za-z0-9_]+/i', '', $_GET['lang']));
    if (file_exists('cache/languages/' . $_GET['lang'] . '.php'))
    {
        $config['language'] = $_GET['lang'];
        $_SESSION['language'] = $_GET['lang'];

        if ($logged) $conn->query("UPDATE " . DB_ACCOUNTS . " SET language='" . $_GET['lang'] . "' WHERE id=" . $user['id']);

        header("Location: " . $config['site_url']);
    }
    else
    {
        if (rebuildLanguage($_GET['lang']))
        {
            $config['language'] = $_GET['lang'];
            $_SESSION['language'] = $_GET['lang'];

            if ($logged) $conn->query("UPDATE " . DB_ACCOUNTS . " SET language='" . $_GET['lang'] . "' WHERE id=" . $user['id']);
            header("Location: " . $config['site_url']);
        }
    }
}

$lang = array();
if (file_exists('cache/languages/' . $_SESSION['language'] . '.php'))
{
    require_once('cache/languages/' . $_SESSION['language'] . '.php');
}
else
{
    require_once('cache/languages/english.php');
}

// Removes session and unnecessary variables if user verification fails
if (!$logged)
{
    unset($_SESSION['user_id']);
    unset($user);
}