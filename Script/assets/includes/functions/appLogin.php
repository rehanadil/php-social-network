<?php
function appLogin($Array=array())
{
	if (!isset($Array['username'], $Array['password'])
		&& !isset($Array['facebook_id'])) return false;

	global $conn, $config, $lang;
	$data = array();
	$data['status'] = 417;
	$data['error_message'] = $lang['error_bad_login'];
	$escapeObj = new \SocialKit\Escape();
	$proceed = false;

	if (isset($Array['facebook_id']))
	{
		$proceed = true;
		$facebookId = $escapeObj->stringEscape($Array['facebook_id']);
		$endSql = "id IN (SELECT id
			FROM " . DB_USERS . "
			WHERE social_login_facebook='$facebookId')";
		$conn->query("UPDATE " . DB_ACCOUNTS . "
			SET active=1
			WHERE banned=0
			AND id IN
				(SELECT id
				FROM " . DB_USERS . "
				WHERE social_login_facebook='$facebookId')
			");
	}
	elseif (isset($Array['username'], $Array['password']))
	{
		$id = $escapeObj->stringEscape($Array['username']);
		$password = trim($Array['password']);
		$hash = md5($password);
		$uid = getUserId($conn, $id);

		if ($uid)
		{
			$endSql = "id=$uid
		    	AND password='$hash'
		    	AND type='user'";
		    $proceed = true;
		    $conn->query("UPDATE " . DB_ACCOUNTS . " SET active=1 WHERE banned=0 AND id=" . $uid);
		}
	}

	if ($proceed)
	{
	    $query = $conn->query("SELECT id
	    	FROM " . DB_ACCOUNTS . "
	    	WHERE $endSql");
	    
	    if ($query->num_rows == 1)
	    {
	        $fetch = $query->fetch_array(MYSQLI_ASSOC);
	        $continue = true;

	        $userObj = new \SocialKit\User();
	        $userObj->setId($fetch['id']);
	        $user = $userObj->getRows();
	        
	        if ($config['email_verification'] == 1
	        	&& $user['email_verified'] == 0)
	        {
	            $continue = false;
	            $data['error_message'] = $lang['error_verify_email'];
	        }
	        
	        if ($continue)
	        {
	            $_SESSION['user_id'] = $user['id'];
	            $_SESSION['user_pass'] = $user['password'];
	            
	            if (isset($_POST['keep_logged_in'])
	            	&& $_POST['keep_logged_in'] == true)
	            {
	                setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 7));
	                setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 7));
	            }
	            
	            $data = array(
	            	"status" => 200,
	            	"userid" => $user['id'],
	            	"password" => $user['password'],
	            	"image" => $user['avatar_url'],
	            	"cover" => $user['cover_url'],
	            	"username" => $user['username'],
	            	"name" => $user['name'],
	            	"birthday" => $user['birth']['month'] . '/' . $user['birth']['date'] . '/' . $user['birth']['year'],
	            	"hometown" => $user['hometown'],
	            	"location" => $user['current_city'],
	            	"gender" => $user['gender'],
	            	"about" => $user['about'],
	            	"follow_privacy" => $user['follow_privacy'],
	            	"message_privacy" => $user['message_privacy'],
	            	"email_notifications" => ($user['mailnotif_message'] == 1) ? true : false
	            );
	        }
	    }
	    else
	    {
	        $data['error_message'] = $lang['incorrect_password'];
	    }
	}

	return $data;
}