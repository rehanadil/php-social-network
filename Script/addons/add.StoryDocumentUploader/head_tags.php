<?php
function doc_upload_css()
{
	return '<style>
	.doc-upload-container {
		display: block;
		color: #898f9c;
		padding: 5px 10px;
	}
	.storyDocViewer {
		display: block;
		margin: 5px;
		padding: 10px;
		background: #f1f2f3;
		border: 1px solid rgba(0, 0, 0, 0.07);
		color: #4e5665;
		font-size: 13px;
    	font-weight: bold;
	}
	.storyDocViewer:hover {
		text-decoration: underline;
	}
	#storyPublisherJar .more-wrapper .option.document {
		color: #883c5c;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'doc_upload_css');
