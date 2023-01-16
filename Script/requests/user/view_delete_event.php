<?php
$timelineId = (int) $_GET['timeline_id'];

$timelineObj = new \SocialKit\User();
$timelineObj->setId($timelineId);
$timelineObj->getRows();

if ($timelineObj->isEvent() && $timelineObj->isAdmin())
{
	$themeData['timeline_id'] = $timelineId;
	$data = array(
	    'status' => 200,
	    'html' => $timelineObj->getDeleteTemplate()
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();