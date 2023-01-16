<?php
function story_display_music($Data)
{
	global $config;
	$conn = getConnection();
	$story = $Data['data'];
	$query = $conn->query("SELECT * FROM story_music_uploads WHERE story_id=" . $story['id']);
	
	if ($query->num_rows == 1)
	{
		$fetch = $query->fetch_array(MYSQLI_ASSOC);
		return '<audio id="story-' . $story['id'] . '-music-upload" class="story-music-upload-html" src="' . $config['site_url'] . '/' . $fetch['url'] . '" controls="yes" preload="no" style="width: 100%;">
			Your browser does not support the audio tag.
		</audio>
		<script>$(\'audio#story-' . $story['id'] . '-music-upload\').mediaelementplayer();</script>';
	}
	else
	{
		if (!isset($story['media']['id'])) return false;

		$audioObj = new \SocialKit\Media();
		$audioObj->setId($story['media']['id']);
		$audio = $audioObj->getRows();

		if ($audio['type'] === "audio")
		{
			return '<audio id="story-' . $story['id'] . '-music-upload" class="story-music-upload-html" src="' . $config['site_url'] . '/' . $audio['complete_url'] . '" controls="yes" preload="no" style="width: 100%;">
				Your browser does not support the audio tag.
			</audio>
			<script>$(\'audio#story-' . $story['id'] . '-music-upload\').mediaelementplayer();</script>';
		}
	}
}
\SocialKit\Addons::register('story_display_addon_data', 'story_display_music');