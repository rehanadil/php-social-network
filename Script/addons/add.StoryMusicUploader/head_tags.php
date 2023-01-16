<?php
function music_upload_css()
{
	return '<style>
	.audio-upload-container {
	display: block;
	color: #898f9c;
	padding: 5px 10px;
	}
	#storyPublisherJar .more-wrapper .option.music {
	    color: orange;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'music_upload_css');
