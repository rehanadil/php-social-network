<?php
function youtube_search_css()
{
	return '<style>
	.youtube-search-wrapper {
	position: relative;
	width: 99.3%;
	display: none;
	background: white;
	color: #4e5665;
	border-top: 1px solid #e9eaeb;
	overflow: hidden;
	}
	.youtube-search-wrapper i {
	width: 27px;
	display: inline-block;
	text-align: center;
	}
	.youtube-search-wrapper i.icon {
	border-right: 1px solid #d3d4d5;
	}
	.youtube-search-wrapper input {
	width: 80%;
	display: inline-block;
	background: white;
	color: #4e5665;
	padding: 5px;
	border: 0;
	}
	.youtube-search-wrapper .input-result-wrapper {
	max-height: 300px;
	display: none;
	background: white;
	color: #4e5665;
	border-top: 1px solid #e9eaeb;
	overflow: auto;
	}
	.youtube-search-wrapper .input-result-wrapper .loading-wrapper {
	color: #838483;
	padding: 7px;
	}
	.youtube-search-wrapper .remove-btn {
	position: absolute;
	top: 5px;
	right: 4px;
	color: #898f9c;
	font-size: 12px;
	cursor: pointer;
	}
	#storyPublisherJar .more-wrapper .option.youtube {
		color: #cc181e;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'youtube_search_css');
