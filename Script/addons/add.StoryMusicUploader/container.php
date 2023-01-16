<?php
function music_upload_option()
{
	global $user;
	$addon_data = json_decode(file_get_contents('addons/add.StoryMusicUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0)
	{
		return "";
	}
	return '<div class="input-wrapper audio-upload-wrapper" data-group="D">
	    <div class="floatLeft">
			<div class="audio-upload-container">No audio uploaded</div>
		</div>

		<div class="floatClear"></div>
	</div>';
}
\SocialKit\Addons::register('new_story_feature_option', 'music_upload_option');
