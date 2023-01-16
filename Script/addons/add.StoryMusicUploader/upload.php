<?php
function story_upload_music($Data)
{
	global $user;
	$conn = getConnection();
	$storyId = $Data['story_id'];
	$file = $Data['data'];

	if ($user['subscription_plan']['upload_audios'] == 0) return '';

	$addon_data = json_decode(file_get_contents('addons/add.StoryMusicUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0) return "";

	if (!isset($file['audio'])) return "";
	
	$audio = $file['audio'];

	$registerMediaObj = new \SocialKit\registerMedia();
    $registerMediaObj->setFile($audio);
    $registerMedia = $registerMediaObj->register();

    if (isset($registerMedia))
    {
    	$conn->query("UPDATE " . DB_STORIES . " SET media_id=" . $registerMedia[0]['id'] . " WHERE id=" . $storyId);
    	is_post_add_data(true);
        return $registerMedia[0]['id'];
    }
}
\SocialKit\Addons::register('new_story_insert_datafile', 'story_upload_music');