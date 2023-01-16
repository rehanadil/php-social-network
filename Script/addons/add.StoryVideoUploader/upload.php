<?php
function story_upload_video($Data)
{
	if (!isLogged()) return '';	
	global $user;
	$conn = getConnection();
	$storyId = $Data['story_id'];
	$file = $Data['data'];
	if ($user['subscription_plan']['upload_videos'] == 0) return '';

	$addon_data = json_decode(file_get_contents('addons/add.StoryVideoUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0) return "";
	
	ini_set('post_max_size', '64M');
	ini_set('upload_max_filesize', '64M');

	if (!isset($file['video'])) return "";
	
	$video = $file['video'];

	$registerMediaObj = new \SocialKit\registerMedia();
    $registerMediaObj->setFile($video);
    $registerMedia = $registerMediaObj->register();

    if (isset($registerMedia))
    {
    	$conn->query("UPDATE " . DB_STORIES . " SET media_id=" . $registerMedia[0]['id'] . " WHERE id=" . $storyId);
    	is_post_add_data(true);
        return $registerMedia[0]['id'];
    }
}
\SocialKit\Addons::register('new_story_insert_datafile', 'story_upload_video');