<?php
if (!empty($_POST['query'])
	&& $continue)
{
	$query = $escapeObj->stringEscape($_POST['query']);
	$data['status'] = 200;
	$searches = array();

	$sql = "SELECT id
	FROM " . DB_ACCOUNTS . "
	WHERE id<>" . $user['id'] . "
	AND avatar_id>0
	AND name LIKE '%$query%'
	AND type='user'
	AND active=1
	AND banned=0
	ORDER BY last_logged DESC
	LIMIT 30";
	$sqlQuery = $conn->query($sql);

	while ($sqlFetch = $sqlQuery->fetch_array(MYSQLI_ASSOC))
	{
	    $searchUserObj = new \SocialKit\User();
	    $searchUserObj->setId($sqlFetch['id']);
	    $searchUser = $searchUserObj->getRows();

	    $isFollowing = 0;
	    if ($searchUserObj->isFollowedBy())
	    {
	    	$isFollowing = 1;
	    }
	    elseif ($searchUserObj->isFollowRequested())
	    {
	    	$isFollowing = 2;
	    }

	    $searches[] = array(
	    	"id" => $searchUser['id'],
			"username" => $searchUser['username'],
			"name" => $searchUser['name'],
			"image" => $searchUser['avatar_url'],
			"cover" => $searchUser['cover_url'],
			"birthday" => $searchUser['birthday'],
			"gender" => $searchUser['gender'],
			"location" => $searchUser['location'],
			"hometown" => $searchUser['hometown'],
			"about" => $searchUser['about'],
			"time" => $searchUser['last_logged'],
			"color" => $searchUser['color'],
			"added" => $isFollowing
	    );
	}

	$data['results'] = $searches;
}