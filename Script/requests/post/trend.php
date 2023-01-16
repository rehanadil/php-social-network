<?php
if (isset($_GET['trend']))
{
	$afterId = (isset($_GET['after_id'])) ? (int) $_GET['after_id'] : 0;
	$posts = getTrendPosts($_GET['trend'], $afterId);
	$isEmpty = true;
	if ($posts) $isEmpty = false;
	$data = array(
		"status" => 200,
		"html" => $posts,
		"is_empty" => $isEmpty
	);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();