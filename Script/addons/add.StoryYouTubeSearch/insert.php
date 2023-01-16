<?php
function story_insert_youtube_search_data ($Data)
{
	if (!isLogged()) return false;

	$conn = getConnection();
	$storyId = $Data['story_id'];
	$addonData = $Data['data'];
	$continue = false;

	if (!is_numeric($storyId)) return false;
	
	if (empty($addonData['youtube_video_id'])) return false;

	$escapeObj = new \SocialKit\Escape();
	$videoid = $escapeObj->stringEscape($addonData['youtube_video_id']);

	if (preg_match('/^(http\:\/\/|https\:\/\/|www\.|youtube\.com|youtu\.be)/', $videoid))
	{
		if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $videoid, $id))
		{
			$videoid = $id[1];
			$continue = true;
		}
		elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $videoid, $id))
		{
			$videoid = $id[1];
			$continue = true;
		}
		elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $videoid, $id))
		{
			$videoid = $id[1];
			$continue = true;
		}
		elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $videoid, $id))
		{
			$videoid = $id[1];
			$continue = true;
		}
		elseif (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $videoid, $id))
		{
		    $videoid = $id[1];
		    $continue = true;
		}
	}
	else
	{
		$continue = true;
	}

	$title = '';
	if (!empty($addonData['youtube_title']))
		$title = $escapeObj->stringEscape($addonData['youtube_title']);

	if ($continue)
	{
		$conn->query("UPDATE " . DB_POSTS . " SET youtube_video_id='$videoid',youtube_title='$title' WHERE id=" . $storyId);
		is_post_add_data(true);
	}
}
\SocialKit\Addons::register('new_story_insert_data', 'story_insert_youtube_search_data');