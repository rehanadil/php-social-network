<?php
function document_upload_launchericon()
{
	global $user, $config;
	$goto = '';
	$addon_data = json_decode(file_get_contents('addons/add.StoryDocumentUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0) return "";

	if ($user['subscription_plan']['upload_documents'] == 0) $goto = 'window.location.href = \'' . subscriptionUrl() . '\';';
	
	return '<span class="option document" onclick="javascript:$(\'#storyPublisherJar\').find(\'input.docUploadInput\').click();">
		<i class="fa fa-file-text-o"></i>
	</span>

	<input class="docUploadInput" type="file" name="doc[]" accept=".pdf,.txt,.zip,.rar,.tar,.doc,.docx" onchange="docUploadInitializer(this);" style="height:1px;width:1px;overflow:hidden;margin-left:-999px;display:none;">

	<script>
	function docUploadInitializer(input)
	{
		' . $goto . '
		doc = input.files[0];

		if (doc.size > ' . ($config['document_filesize_limit'] * MB) . ')
		{
			alert("File size is too large. Maximum file upload limit is ' . $config['document_filesize_limit'] . ' MB");
			$(".docUploadInput").val("");
			return false;
		}
		else if (doc.name.length > 4 && doc.size < ' . ($config['document_filesize_limit'] * MB) . ')
		{
			parent_wrapper = $(\'#storyPublisherJar\');
	    	input_wrapper = parent_wrapper.find(\'.doc-upload-wrapper\');
	    	group_id = input_wrapper.attr(\'data-group\');

	    	parent_wrapper.find(\'.doc-upload-container\').html(\'<i class="fa fa-file-text-o"></i> \' + doc.name);

	    	$(\'.input-wrapper[data-group=\' + group_id + \']\').slideUp();
	    	input_wrapper.slideDown();
	    	
	    	allowPost();
		}
	}
	</script>';
}
\SocialKit\Addons::register('new_story_feature_launchericon', 'document_upload_launchericon');
