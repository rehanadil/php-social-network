<?php
require "Twitter/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$redirectUrl = ($config['smooth_links'] == 1) ? $config['site_url'] . "/social-login/twitter" : $config['site_url'] . "/social-login.php?site=twitter";

if (isset($_SESSION['oauth_token']))
{
    $oauth_token = $_SESSION['oauth_token'];
    unset($_SESSION['oauth_token']);

    $connection = new TwitterOAuth($config['twitter_consumer_key'], $config['twitter_consumer_secret']);

    $params = array("oauth_verifier" => $_GET['oauth_verifier'], "oauth_token" => $_GET['oauth_token']);
    $access_token = $connection->oauth("oauth/access_token", $params);

    $connection = new TwitterOAuth($config['twitter_consumer_key'], $config['twitter_consumer_secret'], $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $content = $connection->get("account/verify_credentials");

    $query = $conn->query("SELECT id FROM " . DB_USERS . " WHERE social_login_twitter=" . $content->id);
    
    if ($query->num_rows == 1)
    {
        $userData = $query->fetch_array(MYSQLI_ASSOC);
        $profile = $conn->query("SELECT id,password FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND banned=0 AND id=" . $userData['id']);
        $profileData = $profile->fetch_array(MYSQLI_ASSOC);

        $_SESSION['user_id'] = $profileData['id'];
        $_SESSION['user_pass'] = $profileData['password'];
        
        setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
        setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));
    }
    else
    {
        $twitterData['id'] = $content->id;
        $twitterData['name'] = $escapeObj->stringEscape($content->name);
        $twitterData['username'] = $escapeObj->stringEscape($content->screen_name);
        $twitterData['avatar_url'] = $escapeObj->stringEscape($content->profile_image_url);
        $twitterData['cover_url'] = $escapeObj->stringEscape($content->profile_banner_url);

        $twitterData['username'] = preg_replace('/([^a-z_])/i', '', strtolower($twitterData['username']));
        $twitterData['email'] = $twitterData['username'] . '@twitter.com';
        $twitterData['password'] = md5(time());
        $twitterData['birthday'] = '';
        $twitterData['gender'] = 'male';
        $twitterData['location'] = '';
        $twitterData['hometown'] = '';
        $twitterData['about'] = '';
        $twitterData['avatar_url'] = str_replace('_normal', '', $twitterData['avatar_url']);

        if (is_username_taken($twitterData['username']))
        {
            $twitterData['username'] = $twitterData['username'] . time();
        }

        $registerObj = new \SocialKit\registerUser();
        $registerObj->setName($twitterData['name']);
        $registerObj->setUsername($twitterData['username']);
        $registerObj->setEmail($twitterData['email']);
        $registerObj->setPassword($twitterData['password']);
        $registerObj->setGender($twitterData['gender']);
        $registerObj->setBirthday($twitterData['birthday']);
        $registerObj->setLocation($twitterData['location']);
        $registerObj->setHometown($twitterData['hometown']);
        $registerObj->setAbout($twitterData['about']);

        if ($register = $registerObj->register())
        {
            $conn->query("UPDATE " . DB_USERS . " SET social_login_twitter='" . $content->id . "' WHERE id=" . $register['id']);
            $register['password'] = $twitterData['password'];
            
            $_SESSION['user_id'] = $register['id'];
            $_SESSION['user_pass'] = md5($twitterData['password']);
            
            setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
            setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));

            if (! empty($twitterData['cover_url']))
            {
                $cover = import_photos($twitterData['cover_url']);
                
                if (is_array($cover))
                {
                    $conn->query("UPDATE " . DB_ACCOUNTS . " SET cover_id=" . $cover['id'] . " WHERE id=" . $register['id']);
                }
            }
            
            if (! empty($twitterData['avatar_url']))
            {
                $avatar = import_photos($twitterData['avatar_url']);
                
                if (is_array($avatar))
                {
                    $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=" . $register['id']);
                }
            }
        }
    }

    header('Location: ' . $config['site_url']);
}
else
{
    $connection = new TwitterOAuth($config['twitter_consumer_key'], $config['twitter_consumer_secret']);

    $temporary_credentials = $connection->oauth('oauth/request_token', array("oauth_callback" => $redirectUrl));

    $_SESSION['oauth_token'] = $temporary_credentials['oauth_token'];
    $_SESSION['oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
    $url = $connection->url("oauth/authorize", array("oauth_token" => $temporary_credentials['oauth_token']));

    // REDIRECTING TO THE URL
    header('Location: ' . $url);
}
