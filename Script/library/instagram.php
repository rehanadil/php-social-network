<?php
$redirectUrl = ($config['smooth_links'] == 1) ? $config['site_url'] . "/social-login/instagram" : $config['site_url'] . "/social-login.php?site=instagram";
/* 
    * Initializing
    */
if (! empty($_GET['code']))
{
	$code = $_GET['code'];

	$url = 'https://api.instagram.com/oauth/access_token';
	$data = array(
		'client_id' => $config['instagram_client_id'],
		'client_secret' => $config['instagram_client_secret'],
		'grant_type' => 'authorization_code',
		'redirect_uri' => $redirectUrl,
		'code' => $code
	);

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	if ($result != false)
	{
		$json = json_decode($result, true);
		$instagramData = $json['user'];
		$id = $instagramData['id'];
		$username = $instagramData['username'];
		$fullname = $instagramData['full_name'];
		$about = $instagramData['bio'];
		$avatar_url = $instagramData['profile_picture'];
		$email = $username . '@instagram.com';
		$password = md5(time());

		if (is_username_taken($username))
		{
			$username = $username . '_' . time();
		}

		$query = $conn->query("SELECT id FROM " . DB_USERS . " WHERE id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0) AND social_login_instagram='" . $id . "'");

		if ($query->num_rows == 1)
	    {
	        $userData = $query->fetch_array(MYSQLI_ASSOC);
	        $profile = $conn->query("SELECT id,password FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND id=" . $userData['id']);
	        $profileData = $profile->fetch_array(MYSQLI_ASSOC);

	        $_SESSION['user_id'] = $profileData['id'];
	        $_SESSION['user_pass'] = $profileData['password'];
	        
	        setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
	        setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));
	    }
	    else
	    {
	    	$registerObj = new \SocialKit\registerUser();
	        $registerObj->setName($fullname);
	        $registerObj->setUsername($username);
	        $registerObj->setEmail($email);
	        $registerObj->setPassword($password);
	        $registerObj->setGender('male');
	        $registerObj->setBirthday('');
	        $registerObj->setLocation('');
	        $registerObj->setHometown('');
	        $registerObj->setAbout($about);

	        if ($register = $registerObj->register())
	        {
	            $conn->query("UPDATE " . DB_USERS . " SET social_login_instagram='" . $id . "' WHERE id=" . $register['id']);
	            $register['password'] = $password;
	            
	            $_SESSION['user_id'] = $register['id'];
	            $_SESSION['user_pass'] = md5($password);
	            
	            setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
	            setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));

	            if (! empty($avatar_url))
	            {
	                $avatar = import_photos($avatar_url);
	                
	                if (is_array($avatar))
	                {
	                    $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=" . $register['id']);
	                }
	            }
	        }
	    }
	}

	header('Location: ' . $config['site_url']);
}
else
{
	$url = 'https://api.instagram.com/oauth/authorize/?client_id=' . $config['instagram_client_id'] . '&redirect_uri=' . $redirectUrl . '&response_type=code';
	header("Location: $url");
}