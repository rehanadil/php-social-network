<?php
if (isset($_POST['friend_id'])
	&& $continue)
{
	$friendId = (int) $_POST['friend_id'];
	$friendObj = new \SocialKit\User();
	$friendObj->setId($friendId);
	$friend = $friendObj->getRows();

	if ($friendObj->isFollowedBy() or $friendObj->isFollowRequested())
	{
	    $friendObj->removeFollow();
	}
	else
	{
	    $friendObj->putFollow();
	}

	$isFollowing = 0;
	if ($friendObj->isFollowRequested())
	{
		$isFollowing = 2;
	}
	elseif ($friendObj->isFollowedBy())
	{
		$isFollowing = 1;
	}

	$data = array(
		"status" => 200,
		"added" => $isFollowing
	);
}