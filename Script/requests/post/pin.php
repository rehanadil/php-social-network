<?php
if ($storyObj->putPin())
{
	$data = array(
	    'status' => 200,
	    'html' => $storyObj->getPinControlHtml()
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();