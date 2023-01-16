<?php
if ($continue)
{
	$data['status'] = 200;
	$contacts = array();
	$followings = $userObj->getFollowings('', 0, true);

	foreach ($followings as $k => $fid)
	{
		$followingObj = new \SocialKit\User();
		$followingObj->setId($fid);
		$following = $followingObj->getRows();

		$contactData = array(
	    	"id" => $following['id'],
			"username" => $following['username'],
			"name" => $following['name'],
			"image" => $following['avatar_url'],
			"cover" => $following['cover_url'],
			"birthday" => $following['birthday'],
			"gender" => $following['gender'],
			"location" => $following['location'],
			"hometown" => $following['hometown'],
			"about" => $following['about'],
			"time" => $following['last_logged'],
			"color" => $following['color']
	    );

	    $contacts[] = $contactData;
	}

	$data['contacts'] = $contacts;
}