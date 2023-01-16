<?php
function documentUploadOption()
{
	global $user;
	$addon_data = json_decode(file_get_contents('addons/add.StoryDocumentUploader/data.json'), true);
	
	if ($addon_data['verified_only'] == 1 && $user['verified'] == 0) return "";
	
	return '<div class="input-wrapper doc-upload-wrapper" data-group="D">
	    <div class="floatLeft">
			<div class="doc-upload-container">No document uploaded</div>
		</div>

		<div class="floatClear"></div>
	</div>';
}
\SocialKit\Addons::register('new_story_feature_option', 'documentUploadOption');
