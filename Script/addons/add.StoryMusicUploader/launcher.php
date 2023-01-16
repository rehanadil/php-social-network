<?php
function music_upload_launchericon()
{
	global $user, $config;
	$goto = '';
	$addon_data = json_decode(file_get_contents('addons/add.StoryMusicUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0)
	{
		return "";
	}

	if ($user['subscription_plan']['upload_audios'] == 0)
	{
		$goto = 'window.location.href = \'' . subscriptionUrl() . '\';';
	}

	return '<span class="option music" onclick="javascript:$(\'#storyPublisherJar\').find(\'input.audio-upload-input\').click();">
		<i class="fa fa-file-audio-o"></i>
	</span>

	<input class="audio-upload-input" type="file" name="audio[]" accept=".mp3" onchange="viewAudioUploadDisplayer(this);" style="height:1px;width:1px;overflow:hidden;margin-left:-999px;display:none;">

	<script>
	function viewAudioUploadDisplayer(input)
	{
		' . $goto . '
		audio = input.files[0];
		
		if (audio.size > ' . ($config['audio_filesize_limit'] * MB) . ')
		{
			alert("File size is too large. Maximum file upload limit is ' . ($config['audio_filesize_limit']) . ' MB");
			$(".audio-upload-input").val("");
		}

		if (audio.name.length > 4 && audio.size < ' . ($config['audio_filesize_limit'] * MB) . ')
		{
			parent_wrapper = $(\'#storyPublisherJar\');
	    	input_wrapper = parent_wrapper.find(\'.audio-upload-wrapper\');
	    	group_id = input_wrapper.attr(\'data-group\');

	    	parent_wrapper.find(\'.audio-upload-container\').html(\'<i class="fa fa-file-audio-o"></i> \' + audio.name);

	    	$(\'.input-wrapper[data-group=\' + group_id + \']\').slideUp();
	    	input_wrapper.slideDown();
	    	
	    	allowPost();
		}
	}
	</script>';
}
\SocialKit\Addons::register('new_story_feature_launchericon', 'music_upload_launchericon');
