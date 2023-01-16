<?php
$data = \SocialKit\Addons::invoke(array(
	'key' => 'addon_ajax_request',
	'return' => 'array',
	'type' => 'MERGE',
	'content' => array(
		'data' => $data
	)
));

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();