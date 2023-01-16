<?php
function story_display_video($Data)
{
	global $config;
	$conn = getConnection();
	$story = $Data['data'];
	$query = $conn->query("SELECT * FROM story_video_uploads WHERE story_id=" . $story['id']);
	
	if ($query->num_rows == 1)
	{
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		return '<div align="center">
			<video id="story-' . $story['id'] . '-video-upload" class="story-video-upload-html" src="' . $config['site_url'] . '/' . $fetch['url'] . '" controls="yes" preload="no">
				Your browser does not support video playing.
			</video>
		</div>
		<script>
		$("video#story-' . $story['id'] . '-video-upload").css("max-width", ($(".singleStoryJar").width() - 6));
		</script>';
	}
	else
	{
		if (!isset($story['media']['id'])) return false;

		$videoObj = new \SocialKit\Media();
		$videoObj->setId($story['media']['id']);
		$video = $videoObj->getRows();

		if ($video['type'] === "video")
		{
			return '<div align="center">
				<video id="story-' . $story['id'] . '-video-upload" class="story-video-upload-html" src="' . $config['site_url'] . '/' . $video['complete_url'] . '" controls="yes" preload="no">
					Your browser does not support video playing.
				</video>
			</div>
			<script>
			$("video#story-' . $story['id'] . '-video-upload").css("max-width", ($(".singleStoryJar").width() - 6));
			</script>';
		}
	}
}
\SocialKit\Addons::register('story_display_addon_data', 'story_display_video');