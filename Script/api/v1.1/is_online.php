<?php
if (isset($_POST['friend_id'])
	&& $continue)
{
	$friendId = (int) $_POST['friend_id'];
	$friendObj = new \SocialKit\User();
	$friendObj->setId($friendId);
	$friend = $friendObj->getRows();

	if ($friendObj->isFollowing())
	{
		$data = array(
			"status" => 200,
			"time" => $friend['last_logged']
		);
	}
	else
	{
		$data = array(
			"status" => 200,
			"time" => 0
		);
	}
}