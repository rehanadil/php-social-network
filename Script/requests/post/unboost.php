<?php
if ($storyObj->deleteBoost())
{
	$data = array(
	    'status' => 200,
	    'html' => $storyObj->getBoostControlHtml()
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();