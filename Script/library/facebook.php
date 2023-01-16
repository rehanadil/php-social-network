<?php
require_once "Facebook/autoload.php";
$redirectUrl = ($config['smooth_links'] == 1) ? $config['site_url'] . "/social-login/facebook" : $config['site_url'] . "/social-login.php?site=facebook";

$fb = new Facebook\Facebook(array(
    'app_id' => $config['facebook_app_id'],
    'app_secret' => $config['facebook_app_secret'],
    'default_graph_version' => 'v2.2'
));
$helper = $fb->getRedirectLoginHelper();

if (isset($_SESSION['fb_access_url'])
    && $_SESSION['fb_access_url'] == 1)
{
    $_SESSION['fb_access_url'] = 0;
    try
    {
        $accessToken = $helper->getAccessToken();
    }
    catch(Facebook\Exceptions\FacebookResponseException $e)
    {
        error_log("Facebook Graph Error: " . $e->getMessage());
        header("Location: " . $config['site_url']);
    }
    catch(Facebook\Exceptions\FacebookSDKException $e)
    {
        error_log("Facebook SDK Error: " . $e->getMessage());
        header("Location: " . $config['site_url']);
    }

    if (!isset($accessToken))
    {
        if ($helper->getError())
        {
            $errorJson = array(
                "error" => $helper->getError(),
                "error_code" => $helper->getErrorCode(),
                "error_reason" => $helper->getErrorReason(),
                "error_description" => $helper->getErrorDescription()
            );
            error_log("Facebook Unknown Access Token: " . json_encode($errorJson));
            header("Location: " . $config['site_url']);
        }
        else
        {
            error_log("Facebook Unknown Access Token: Unknown Error");
            header("Location: " . $config['site_url']);
        }
        exit;
    }

    $oAuth2Client = $fb->getOAuth2Client();
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    $tokenMetadata->validateAppId($config['facebook_app_id']);
    $tokenMetadata->validateExpiration();

    if (! $accessToken->isLongLived())
    {
        try
        {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        }
        catch (Facebook\Exceptions\FacebookSDKException $e)
        {
            error_log("Facebook Long Lived Access Token: " . $helper->getMessage());
            header("Location: " . $config['site_url']);
        }
    }

    
    try
    {
        $response = $fb->get('/me?fields=id,email,birthday,gender,name,cover,picture.width(720).height(720)', $accessToken);
    }
    catch(Facebook\Exceptions\FacebookResponseException $e)
    {
        error_log("Facebook Graph Response Error: " . $e->getMessage());
        header("Location: " . $config['site_url']);
    }
    catch(Facebook\Exceptions\FacebookSDKException $e)
    {
        error_log("Facebook SDK Response Error: " . $e->getMessage());
        header("Location: " . $config['site_url']);
    }

    $getUser = $response->getGraphUser();
    
    if (!empty($getUser['name'])
        && !empty($getUser['id']))
    {
        $getUser['name'] = $escapeObj->stringEscape($getUser['name']);
        $getUser['id'] = $escapeObj->stringEscape($getUser['id']);
        $getUser['username'] = preg_replace('/([^a-z_])/i', '', strtolower($getUser['name'])) . time();

        if (!empty($getUser['birthday'])
            && is_array($getUser['birthday'])
            && count($getUser['birthday']) > 0)
        {
            $fbBdayArray = explode('/', $getUser['birthday']);

            $fbBdayArray[0] = (empty($fbBdayArray[0])) ? 1 : $fbBdayArray[0];
            $fbBdayArray[1] = (empty($fbBdayArray[1])) ? 1 : $fbBdayArray[1];
            $fbBdayArray[2] = (empty($fbBdayArray[2])) ? 1990 : $fbBdayArray[2];

            $getBday = array($fbBdayArray[0], $fbBdayArray[1], $fbBdayArray[2]);
            $getUser['birthday'] = implode('/', $getBday);
        }
        
        if (!empty($getUser['email']))
            $getUser['email'] = $escapeObj->stringEscape($getUser['email']);
        else
            $getUser['email'] = $getUser['username'] . '@facebook.com';
        
        if (! empty($getUser['gender']))
            $getUser['gender'] = $escapeObj->stringEscape($getUser['gender']);
        else
            $getUser['gender'] = 'female';
        
        $getUser['password'] = md5($getUser['email']);
        $query2 = $conn->query("SELECT id,password FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT id FROM " . DB_USERS . " WHERE social_login_facebook='" . $getUser['id'] . "') AND type='user' AND active=1 AND banned=0");
        $query3 = $conn->query("SELECT id,password FROM " . DB_ACCOUNTS . " WHERE email='" . $getUser['email'] . "' AND type='user' AND active=1 AND banned=0");
        
        if ($query2->num_rows == 1
            || $query3->num_rows == 1)
        {
            $selectedQuery = ($query2->num_rows == 1) ? $query2 : $query3;
            $fetch2 = $selectedQuery->fetch_array(MYSQLI_ASSOC);
            
            $_SESSION['user_id'] = $fetch2['id'];
            $_SESSION['user_pass'] = $fetch2['password'];
            
            setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
            setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));

            $conn->query("UPDATE " . DB_USERS . " SET social_login_facebook='" . $getUser['id'] . "' WHERE id=" . $fetch2['id']);
        }
        else
        {
            $registerObj = new \SocialKit\registerUser();
            $registerObj->setName($getUser['name']);
            $registerObj->setUsername($getUser['username']);
            $registerObj->setEmail($getUser['email']);
            $registerObj->setPassword($getUser['password']);
            $registerObj->setGender($getUser['gender']);
            $registerObj->setFacebookId($getUser['id']);

            if ($register = $registerObj->register())
            {
                $register['password'] = $getUser['password'];
                
                $_SESSION['user_id'] = $register['id'];
                $_SESSION['user_pass'] = md5($getUser['password']);
                
                setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
                setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));
                
                $to = $register['email'];
                $subject = $config['site_name'] . ' - Account Password';
                
                $themeData['mail_user_name'] = $register['name'];
                $themeData['mail_user_password'] = $register['password'];
                
                $message = \SocialKit\UI::view('emails/facebook-registration');
                @sendMail($to, $subject, $message);
                
                if (! empty($getUser['cover']) && is_array($getUser['cover']))
                {
                    $cover = import_photos($getUser['cover']['source']);
                    
                    if (is_array($cover))
                    {
                        $query3 = $conn->query("UPDATE " . DB_ACCOUNTS . " SET cover_id=" . $cover['id'] . " WHERE id=" . $register['id']);
                    }
                }
                
                if (is_array($getUser['picture']) && ! empty($getUser['picture']['data']['url']))
                {
                    $avatar = import_photos($getUser['picture']['data']['url']);
                    
                    if (is_array($avatar))
                    {
                        $query_two = $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=" . $register['id']);
                    }
                }
            }
        }
    }
    header('Location: ' . $config['site_url']);
}
else
{
    $permissions = array('email');
    $loginUrl = $helper->getLoginUrl($redirectUrl, $permissions);

    if ($loginUrl)
    {
        $_SESSION['fb_access_url'] = 1;
        header('Location: ' . $loginUrl);
    }
    else
    {
        error_log("Facebook Login Error: Bad Login URL");
        header('Location: ' . $config['site_url']);
    }
}
?>