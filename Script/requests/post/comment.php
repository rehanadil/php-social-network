<?php
$photo = 0;
if (isset($_FILES['photos']['name']))
{
	$photo = $_FILES['photos'];
}

$timelineId = (isset($_POST['timeline_id'])) ? (int) $_POST['timeline_id'] : 0;
$commentId = $storyObj->putComment($_POST['text'], $timelineId, $photo);

if ($commentId)
{
	$commentObj = new \SocialKit\Comment($conn);
	$commentObj->setId($commentId);

	$data = array(
	    'status' => 200,
	    'html' => $commentObj->getTemplate(),
	    'activity_html' => $storyObj->getCommentActivityTemplate()
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();