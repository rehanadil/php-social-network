<?php
/*
    *  Calling the Google Library
    */
require_once 'Google/Google_Client.php';
require_once 'Google/contrib/Google_Oauth2Service.php';
$redirectUrl = ($config['smooth_links'] == 1) ? $config['site_url'] . "/social-login/google" : $config['site_url'] . "/social-login.php?site=google";

/* 
    * Initializing Google Object
    */
$client = new Google_Client();
$client->setClientId($config['google_client_id']);
$client->setClientSecret($config['google_client_secret']);
$client->setRedirectUri($redirectUrl);
$oauthV2 = new Google_Oauth2Service($client);

if (isset($_GET['logout']))
{
    session_unset();
}

/* 
    * Authorizing the Code and receiving an Access Token in exchange
    * Then reloading the page
    */
if (isset ($_GET['code']))
{
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: ?');
}

/* 
    * Using the Access Token to retrieve User Information
    */
if (isset($_SESSION['access_token']) && $_SESSION['access_token'])
{
    $client->setAccessToken($_SESSION['access_token']);
    $me = $oauthV2->userinfo->get();
    $_SESSION['fml'] = $me;
    
    $googleId = $me['id'];
    $googleMail = $me['email'];
    $googleIdQuery = $conn->query("SELECT id FROM " . DB_USERS . " WHERE social_login_google=" . $googleId . " AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND banned=0)");
    $googleMailQuery = $conn->query("SELECT id,password FROM " . DB_ACCOUNTS . " WHERE email='" . $googleMail . "' AND type='user' AND active=1 AND banned=0");

    $googleIdNum = $googleIdQuery->num_rows;
    $googleMailNum = $googleMailQuery->num_rows;

    if ($googleIdNum == 1 or $googleMailNum == 1)
    {
        if ($googleMailNum == 1)
        {
            $profileData = $googleMailQuery->fetch_array(MYSQLI_ASSOC);
            
            if ($googleIdNum == 0)
            {
                $conn->query("UPDATE " . DB_USERS . " SET social_login_google='" . $googleId . "' WHERE id=" . $profileData['id']);
            }
        }
        else
        {
            $userData = $googleIdQuery->fetch_array(MYSQLI_ASSOC);
            $profile = $conn->query("SELECT id,password FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND id=" . $userData['id']);
            $profileData = $profile->fetch_array(MYSQLI_ASSOC);
        }

        $_SESSION['user_id'] = $profileData['id'];
        $_SESSION['user_pass'] = $profileData['password'];
        
        setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
        setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));
    }
    else
    {
        $profile_image_url = ""; //str_replace('sz=50', 'sz=500', $me['picture']);
        $cover_image_url = ""; //str_replace('s630', 's960', $me['cover']['coverPhoto']['url']);
        $password = md5(time());
        $username = str_replace(' ', '', strtolower($me['given_name']) . strtolower($me['family_name']));
        
        if (is_username_taken($username) == true)
        {
            $username = $username . time();
        }
        
        var_dump($username);
        
        $birthday = '';
        
        if (isset($me['birthday'])
            && !empty($me['birthday']))
        {
            $birth = explode('-', $me['birthday']);
            
            if (! isset($birth[2]))
            {
                $birth[2] = '1990';
            }
            
            $birthday = $birth[2] . '-' . $birth[1] . '-' . $birth[0];
        }

        $registerObj = new \SocialKit\registerUser();
        $registerObj->setName($me['given_name'] . $me['family_name']);
        $registerObj->setUsername($username);
        $registerObj->setEmail($me['email']);
        $registerObj->setPassword($password);
        $registerObj->setGender($me['gender']);
        $registerObj->setBirthday($birthday);
        $registerObj->setLocation('');
        $registerObj->setHometown('');
        $registerObj->setAbout('');
        
        $register = $registerObj->register();
        
        if ($register)
        {
            $conn->query("UPDATE " . DB_USERS . " SET social_login_google='" . $googleId . "' WHERE id=" . $register['id']);
            $register['password'] = $password;
            
            $_SESSION['user_id'] = $register['id'];
            $_SESSION['user_pass'] = md5($password);
            
            setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
            setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));

            if (! empty($profile_image_url))
            {
                $avatar = import_photos($profile_image_url);
                
                if (is_array($avatar))
                {
                    $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=" . $register['id']);
                }
            }

            if (! empty($cover_image_url))
            {
                $cover = import_photos($cover_image_url, true);
                
                if (is_array($cover))
                {
                    $conn->query("UPDATE " . DB_ACCOUNTS . " SET cover_id=" . $cover['id'] . " WHERE id=" . $register['id']);
                }
            }
        }
    }

    header('Location: ' . $config['site_url']);
}
else
{
/*
    * Creating the Authorization URL and redirecting User to it
    */
    $authUrl = $client->createAuthUrl();
    header("Location: $authUrl");
}
