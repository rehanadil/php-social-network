<?php
if (isset($_POST['edit_text'])
	&& $user['subscription_plan']['edit_stories'] == 1)
{
	if ($storyObj->putEditText($_POST['edit_text']))
	{
		$storyObj = new \SocialKit\Story($conn);
	    $storyObj->setId($postId); 
	    $story = $storyObj->getRows();
	    
		$data = array(
		    'status' => 200,
		    'html' => $storyObj->getTextTemplate()
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();