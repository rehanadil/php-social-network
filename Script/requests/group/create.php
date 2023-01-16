<?php
if ($config['group_allow_create'] == 1 && $user['subscription_plan']['create_groups'] == 1)
{
	$regObj = new \SocialKit\registerGroup();
	$regObj->setName($_POST['group_name']);
	$regObj->setUsername($_POST['group_username']);
	$regObj->setAbout($_POST['group_about']);
	$regObj->setPrivacy($_POST['group_privacy']);

	if ($register = $regObj->register())
	{
	    $groupObj = new \SocialKit\User($conn);
	    $groupObj->setId($register['id']);
	    $group = $groupObj->getRows();

	    $data = array(
	        'status' => 200,
	        'url' => $group['url']
	    );
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();