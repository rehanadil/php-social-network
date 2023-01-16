<?php
userOnly();

if ($user['subscription_plan']['post_new_stories'] == 1)
{
	is_post_add_data(false);

	$regObj = new \SocialKit\registerStory();

	$regObj->setTimeline(__POST__('timeline_id'));
	$regObj->setRecipient(__POST__('recipient_id'));
	$regObj->setText(__POST__('text'));
	$regObj->setMapName(__POST__('google_map_name'));

	if ($config['story_allow_privacy'] == 1)
	{
		if ($user['subscription_plan']['story_privacy'] == 1)
		{
			$regObj->setPrivacy(__POST__('post_privacy'));
		}
	}

	if ($config['story_allow_photo_upload'] == 1)
	{
		if (isset($_FILES['photos']['name']))
		{
		    $regObj->setPhotos($_FILES['photos']);
		}
	}


	if ($config['story_allow_addons'] == 1)
	{
		if (isset($_POST))
		{
			$regObj->setAdditionalData($_POST);
		}

		if (isset($_FILES))
		{
			$regObj->setAdditionalFiles($_FILES);
		}
	}


	if ($storyId = $regObj->register())
	{
	    $storyObj = new \SocialKit\Story();
	    $storyObj->setId($storyId);
	    
	    $data = array(
	        'status' => 200,
	        'html' => $storyObj->getTemplate()
	    );
	}
}
else
{
	$data['url'] = subscriptionUrl();
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();