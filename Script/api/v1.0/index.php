<?php
/**
 * @package Social Kit - Social Networking Platform v2.5.0.2
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2017 Rehan Adil. All rights reserved.
**/

require_once('connect.php');

$api_data = array(
	'api_status' => 'failed',
	'api_version' => '1.0',
	'errors' => array(
		'error_type' => 'technical',
		'error_message' => 'There seems to be some technical issue. Please try again.'
	)
);

$type = $_GET['type'];


if ($type == "profile_data")
{
	if (! empty($_GET['username']) or ! empty($_GET['profileid']))
	{
		require_once('profile_data.php');
	}
}

if ($type == "post_data")
{
	require_once('post_data.php');
}

if ($type == "profile_posts")
{
	require_once('profile_posts.php');
}

if ($type == "search")
{
	if (! empty($_GET['query']))
	{
		$query = strescape($_GET['query']);
		$limit = 5;

		if (! empty($_GET['limit']))
		{
			$limit = (int) $_GET['limit'];

			if ($limit < 1)
			{
				$limit = 5;
			}

			$profileQuery = $conn->query("SELECT id,about,avatar_id,cover_id,name,username,verified,type FROM " . DB_ACCOUNTS . " WHERE name LIKE '%$query%' AND active=1 AND banned=0 AND type IN ('user','page') LIMIT 5");

			if ($profileQuery->num_rows == 0)
			{
				$api_data['errors']['error_type'] = 'no_result_found';
				$api_data['errors']['error_message'] = 'We could not find any results with that query.';
				header("Content-type: application/json; charset=utf-8");
				echo json_encode($api_data);
				$conn->close();
				exit();
			}

			$api_data['api_status'] = "success";
			$api_data['results'] = array();
			while ($profile = $profileQuery->fetch_array(MYSQLI_ASSOC))
			{
				if ($profile['type'] == "user")
				{
					require('search/user_data.php');
				}

				if ($profile['type'] == "page")
				{
					require('page_data.php');
				}
			}

			unset($api_data['errors']);
			header("Content-type: application/json; charset=utf-8");
			echo json_encode($api_data);
			$conn->close();
			exit();
		}
	}
}