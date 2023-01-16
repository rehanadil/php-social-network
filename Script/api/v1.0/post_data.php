<?php
if (! empty($_GET['postid']))
{
	$postid = (int) $_GET['postid'];
	$postQuery = $conn->query("SELECT google_map_name,link_url,media_id,soundcloud_uri,text,time,timeline_id,youtube_video_id FROM " . DB_POSTS . " WHERE id=" . $postid . " AND activity_text='' AND hidden=0 AND privacy='public' AND shared=0");

	if ($postQuery->num_rows != 1)
	{
		$api_data['errors']['error_type'] = 'post_not_found';
		$api_data['errors']['error_message'] = 'Post not found.';

		header("Content-type: application/json; charset=utf-8");
		echo json_encode($api_data);
		$conn->close();
		exit();
	}

	$post = $postQuery->fetch_array(MYSQLI_ASSOC);
	$post_data = array(
		'id' => $postid,
		'profile_id' => $post['timeline_id'],
		'time' => $post['time'],
		'text' => $post['text'],
		'location' => $post['google_map_name'],
		'link' => $post['link_url'],
		'soundcloud' => $post['soundcloud_uri'],
		'youtube' => $post['youtube_video_id']
	);

	/* Photos */
	if ($post['media_id'] > 0)
	{
		$mediaQuery = $conn->query("SELECT extension,type,url FROM " . DB_MEDIA . " WHERE id=" . $post['media_id'] . " AND active=1");

		if ($mediaQuery->num_rows == 1)
		{
			$media = $mediaQuery->fetch_array(MYSQLI_ASSOC);
			$post_data['photos'] = array();

			if ($media['type'] == "album")
			{
				$albumId = $post['media_id'];
				unset($media);

				$albumQuery = $conn->query("SELECT extension,type,url FROM " . DB_MEDIA . " WHERE album_id=" . $albumId . " AND active=1");

				if ($albumQuery->num_rows > 0)
				{
					while ($album = $albumQuery->fetch_array(MYSQLI_ASSOC))
					{
						$post_data['photos'][] = $site_url . '/' . $album['url'] . '_100x100.' . $album['extension'];
					}
				}
			}
			else
			{
				$post_data['photos'][] = $site_url . '/' . $media['url'] . '_100x100.' . $media['extension'];
			}
		}
	}

	/* Statistics */
	$post_data['reactions'] = array(
		'all' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1")->num_rows,
		'like' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1 AND reaction='like'")->num_rows,
		'love' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1 AND reaction='love'")->num_rows,
		'haha' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1 AND reaction='haha'")->num_rows,
		'wow' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1 AND reaction='wow'")->num_rows,
		'sad' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1 AND reaction='sad'")->num_rows,
		'angry' => $conn->query("SELECT id FROM " . DB_POSTLIKES . " WHERE post_id=" . $postid . " AND active=1 AND reaction='angry'")->num_rows
	);
	$post_data['comments'] = $conn->query("SELECT id FROM " . DB_COMMENTS . " WHERE post_id=" . $postid . " AND active=1")->num_rows;
	$post_data['shares'] = $conn->query("SELECT id FROM " . DB_POSTS . " WHERE post_id=" . $postid . " AND shared=1 AND active=1")->num_rows;

	$api_data['api_status'] = "success";
	$api_data['post_data'] = $post_data;

	unset($api_data['errors']);
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($api_data);
	$conn->close();
	exit();
}