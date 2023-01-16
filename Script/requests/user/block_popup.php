<?php
userOnly();

if (isset($_GET['id']))
{
	$blockedUserId = (int) $_GET['id'];
	$blockedUserObj = new \SocialKit\User();
	$blockedUserObj->setId($blockedUserId);

	if (!$blockedUserObj->isBlocked())
	{
		$blockedUser = $blockedUserObj->getRows();
		$themeData['block_id'] = $blockedUser['id'];
		$themeData['block_user'] = str_replace('{user}', $blockedUser['name'], $lang['block_user']);
		$themeData['block_user_confirmation'] = str_replace('{user}', $blockedUser['name'], $lang['block_user_confirmation']);
		$data = array(
			"status" => 200,
			"html" => \SocialKit\UI::view('popups/block-user')
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();