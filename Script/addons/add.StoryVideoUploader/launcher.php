<?php
function video_upload_launchericon()
{
	global $user, $config;
	$goto = '';
	$addon_data = json_decode(file_get_contents('addons/add.StoryVideoUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0)
	{
		return "";
	}

	if ($user['subscription_plan']['upload_videos'] == 0)
	{
		$goto = 'window.location.href = \'' . subscriptionUrl() . '\';';
	}
	
	return '<span class="option video" onclick="javascript:$(\'#storyPublisherJar\').find(\'input.video-upload-input\').click();">
		<i class="fa fa-file-video-o"></i>
	</span>

	<input class="video-upload-input" type="file" name="video[]" accept=".mp4" onchange="viewVideoUploadDisplayer(this);" style="height:1px;width:1px;overflow:hidden;margin-left:-999px;display:none;">

	<script>
	function viewVideoUploadDisplayer(input)
	{
		' . $goto . '
		video = input.files[0];
		
		if (video.size > ' . ($config['video_filesize_limit'] * MB) . ')
		{
			alert("File size is too large. Maximum file upload limit is ' . ($config['video_filesize_limit']) . ' MB");
			$(".video-upload-input").val("");
		}

		if (video.name.length > 4 && video.size < ' . ($config['video_filesize_limit'] * MB) . ')
		{
			parent_wrapper = $(\'#storyPublisherJar\');
	    	input_wrapper = parent_wrapper.find(\'.video-upload-wrapper\');
	    	group_id = input_wrapper.attr(\'data-group\');

	    	parent_wrapper.find(\'.video-upload-container\').html(\'<i class="fa fa-file-video-o"></i> \' + video.name);

	    	$(\'.input-wrapper[data-group=\' + group_id + \']\').slideUp();
	    	input_wrapper.slideDown();
	    	
	    	allowPost();
		}
	}
	</script>';
}
\SocialKit\Addons::register('new_story_feature_launchericon', 'video_upload_launchericon');
