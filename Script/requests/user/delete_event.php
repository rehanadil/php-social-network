<?php
userOnly();

$timelineId = (int) $_GET['timeline_id'];

$timelineObj = new \SocialKit\User();
$timelineObj->setId($timelineId);
$timelineObj->getRows();

if ($timelineObj->isEvent() && $timelineObj->isAdmin())
{
	$deactivate = $conn->query("UPDATE " . DB_ACCOUNTS . " SET active=0 WHERE id=" . $timelineId);

	if ($deactivate)
	{
		$data = array(
		    'status' => 200,
		    'goto_url' => smoothLink('index.php?a=home')
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();