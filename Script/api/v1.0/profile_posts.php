<?php
$profileExists = false;
$limit = 5;

if (! empty($_GET['limit']))
{
	$limit = (int) $_GET['limit'];

	if ($limit < 1)
	{
		$limit = 5;
	}
}

if (! empty($_GET['profileid']))
{
	$profileid = (int) $_GET['profileid'];
	$getIdQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE id=$profileid AND type IN ('user','page') AND banned=0");

	if ($getIdQuery->num_rows == 1)
	{
		$profileExists = true;
	}
}
elseif (! empty($_GET['username']))
{
	$username = strescape($_GET['username']);
	$getIdQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE username='$username' AND type IN ('user','page') AND banned=0");

	if ($getIdQuery->num_rows == 1)
	{
		$getId = $getIdQuery->fetch_array(MYSQLI_ASSOC);
		$profileid = $getId['id'];
		$profileExists = true;
	}
}

if (! $profileExists)
{
	$api_data['errors']['error_type'] = 'username_not_found';
	$api_data['errors']['error_message'] = 'We could not find any user with that username :(';
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($api_data);
	$conn->close();
	exit();
}

$profile_posts = array();
$postsQuery = $conn->query("SELECT id FROM " . DB_POSTS . " WHERE timeline_id=" . $profileid . " AND activity_text='' AND hidden=0 AND privacy='public' AND shared=0 LIMIT $limit");

if ($postsQuery->num_rows == 0)
{
	$api_data['errors']['error_type'] = 'no_posts_found';
	$api_data['errors']['error_message'] = 'We could not find any post from this user.';
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($api_data);
	$conn->close();
	exit();
}

while ($post = $postsQuery->fetch_array(MYSQLI_ASSOC))
{
	$profile_posts[] = $post['id'];
}

$api_data['api_status'] = "success";
$api_data['profile_posts'] = $profile_posts;

unset($api_data['errors']);
header("Content-type: application/json; charset=utf-8");
echo json_encode($api_data);
$conn->close();
exit();