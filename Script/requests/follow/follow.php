<?php
userOnly();

if ($user['subscription_plan']['add_friends'] == 1)
{
	$followingId = (int) $_POST['following_id'];
	$followingObj = new \SocialKit\User();
	$followingObj->setId($followingId);
	$following = $followingObj->getRows();

	if ($followingObj->isFollowedBy() or $followingObj->isFollowRequested())
	{
	    $followingObj->removeFollow();
	}
	else
	{
	    $followingObj->putFollow();
	}

	$data = array(
	    'status' => 200,
	    'html' => $followingObj->getFollowButton()
	);
}
else
{
	$data = array(
		"status" => 417,
		"url" => subscriptionUrl()
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();