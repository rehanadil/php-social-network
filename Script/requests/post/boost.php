<?php
userOnly();

if ($storyObj->putBoost())
{
	$data = array(
	    'status' => 200,
	    'html' => $storyObj->getBoostControlHtml()
	);
}
else
{
	$lang['you_can_boost_maximum_num_posts'] = str_replace('{num}', $user['subscription_plan']['boost_posts'], $lang['you_can_boost_maximum_num_posts']);
	$data = array(
	    'status' => 417,
	    'error' => array(
	    	'message' => \SocialKit\UI::view('popups/boost-limit')
	    )
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();