<?php
$data = array(
    "status" => 417,
    "error_message" => $lang['error_empty_registration']
);
$proceed = true;

if (isset($_POST['username'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['gender']))
{
	$username = $escapeObj->stringEscape($_POST['username']);
	$name = $escapeObj->stringEscape($_POST['name']);
	$email = $escapeObj->stringEscape($_POST['email']);
	$password = trim($escapeObj->stringEscape($_POST['password']));
	$gender = $escapeObj->stringEscape($_POST['gender']);
	$facebookId = 0;

	if (isset($_POST['facebook'], $_POST['facebook_id']))
	{
		$facebookId = $escapeObj->stringEscape($_POST['facebook_id']);
		$facebookQuery = $conn->query("SELECT id FROM " . DB_USERS . " WHERE social_login_facebook='$facebookId'");

		if ($facebookQuery->num_rows > 0)
		{
			$proceed = false;
			$data = appLogin(array(
				"facebook_id" => $facebookId
			));
		}

		if (empty($username))
		{
			$username = preg_replace('/[^A-Za-z0-9_]/i', '', $name) . generateKey(5, 10);
		}

		if (empty($email))
		{
			$email = $facebookId . "@facebook.com";
		}

		if (empty($password))
		{
			$password = $name . time();
		}
	}

	$hash = md5($password);

	if ($proceed
		&& !validateUsername($username))
	{
		$proceed = false;
        $data = array(
            "status" => 417,
            "error_message" => $lang['username_requirements']
        );
	}

	if ($proceed)
    {
        $isUsernameExists = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE username='$username'");

        if ($isUsernameExists->num_rows > 0)
        {
        	$proceed = false;
            $data = array(
                "status" => 417,
                "error_message" => $lang['username_not_available']
            );
        }
    }

    if ($proceed
    	&& !preg_match('/^([-A-Za-z0-9_+.]+)\@([-A-Za-z0-9.]+)$/i', $email))
	{
		$proceed = false;
		$data = array(
	        "status" => 417,
	        "error_message" => $lang['invalid_email_address']
	    );
	}

	if ($proceed)
	{
		$mailQuery = $conn->query("SELECT id,password,email_verified FROM " . DB_ACCOUNTS . " WHERE email='$email'");

	    if ($mailQuery->num_rows > 0)
	    {
	    	$mailFetch = $mailQuery->fetch_array(MYSQLI_ASSOC);
	        $proceed = false;
	        $data = array(
	            "status" => 417,
	            "error_message" => $lang['error_email_exists']
	        );

	        if ($config['email_verification'] == 1
	        	&& $mailFetch['email_verified'] == 0)
	        {
	        	$data['error_message'] = $lang['error_email_exists'];
	        }

	        if ($mailFetch['password'] === $hash)
	        {
	        	$data = appLogin(array(
	        		"username" => $mailFetch['id'],
	        		"password" => $mailFetch['password']
	        	));
	        }
	    }
	}

	if ($proceed
		&& !in_array($gender, array('male','female')))
	{
		$proceed = false;
		$data = array(
	        "status" => 417,
	        "error_message" => "Unknown Gender"
	    );
	}

	if ($proceed
		&& strlen($password) < 4) $proceed = false;
}
else
{
	$proceed = false;
	$data = array(
        "status" => 417,
        "error_message" => "Please try again."
    );
}

if ($proceed)
{
    $registerObj = new \SocialKit\registerUser();
    $registerObj->setUsername($username);
    $registerObj->setName($name);
    $registerObj->setPassword($password);
    $registerObj->setEmail($email);
    $registerObj->setGender($gender);
    $registerObj->setFacebookId($facebookId);
    
    if ($register = $registerObj->register())
    {
    	if ($config['email_verification'] == 1)
    	{
    		$data = array(
		        "status" => 417,
		        "error_message" => $lang['verification_email_sent']
		    );
    	}
    	else
    	{
    		$data = array(
            	"status" => 200,
            	"userid" => $register['id'],
            	"password" => $hash,
            	"image" => $register['avatar_url'],
            	"cover" => $register['cover_url'],
            	"username" => $register['username'],
            	"name" => $register['name'],
            	"birthday" => $register['birth']['month'] . '/' . $register['birth']['date'] . '/' . $register['birth']['year'],
            	"hometown" => $register['hometown'],
            	"location" => $register['current_city'],
            	"gender" => $register['gender'],
            	"about" => $register['about'],
            	"follow_privacy" => $register['follow_privacy'],
            	"message_privacy" => $register['message_privacy'],
            	"email_notifications" => ($register['mailnotif_message'] == 1) ? true : false
            );

            $continue = true;

			$_SESSION['user_id'] = $register['id'];
		    $_SESSION['user_pass'] = $hash;

			$userObj = new \SocialKit\User();
			$userObj->setId($register['id']);
			$user = $userObj->getRows();
    	}
    }
}