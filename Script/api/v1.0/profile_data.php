<?php
if (! empty($_GET['profileid']))
{
	$profileid = (int) $_GET['profileid'];
	$qry = "id=" . $profileid;
}
elseif (! empty($_GET['username']))
{
	if (is_numeric($_GET['username']))
	{
		header("Location: $site_url/api/v1.0/?type=profile_data&profileid=" . $_GET['username']);
	}

	$username = strescape($_GET['username']);
	$qry = "username='$username'";
}

$profileQuery = $conn->query("SELECT id,about,avatar_id,cover_id,name,username,verified,type FROM " . DB_ACCOUNTS . " WHERE $qry AND active=1 AND type IN ('user','page')");

if ($profileQuery->num_rows != 1)
{
	$api_data['errors']['error_type'] = 'username_not_found';
	$api_data['errors']['error_message'] = 'We could not find any user with that username :(';
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($api_data);
	$conn->close();
	exit();
}

$profile = $profileQuery->fetch_array(MYSQLI_ASSOC);

if ($profile['type'] == "user")
{
	require_once('user_data.php');
}

if ($profile['type'] == "page")
{
	require_once('page_data.php');
}