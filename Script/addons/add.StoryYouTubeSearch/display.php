<?php
function youtube_search_display_video ($Data)
{
	$conn = getConnection();
	$story = $Data['data'];
	$query = $conn->query("SELECT youtube_video_id FROM " . DB_POSTS . " WHERE id=" . $story['id']);
	
	if ($query->num_rows == 1)
	{
		$fetch = $query->fetch_array(MYSQLI_ASSOC);

		if (! empty($fetch['youtube_video_id']))
		{
			return '<div class="youtube-wrapper" align="center">
			    <iframe src="https://www.youtube.com/embed/' . $fetch['youtube_video_id'] . '?ap=%2526fmt%3D18&disablekb=1&rel=0" width="100%" height="324px" frameborder="0" allowfullscreen></iframe>
			</div>';
		}
	}
}
\SocialKit\Addons::register('story_display_addon_data', 'youtube_search_display_video');