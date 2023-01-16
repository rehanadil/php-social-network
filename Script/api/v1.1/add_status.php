<?php
if (!empty($_POST['friend_id'])
	&& $continue)
{
	$friendId = (int) $_POST['friend_id'];
	$friendObj = new \SocialKit\User();
	$friendObj->setId($friendId);
	$friend = $friendObj->getRows();

	$isAdded = 0;
	if ($friendObj->isFollowRequested())
	{
		$isAdded = 2;
	}
	elseif ($friendObj->isFollowedBy())
	{
		$isAdded = 1;
	}

	if (isset($friend['id']))
	{
		$data = array(
			"status" => 200,
			"added" => $isAdded
		);
	}
}