<?php
function urlpreview_search_css()
{
	return '<style>
	.urlpreview-search-wrapper {
	position: relative;
	width: 99.3%;
	display: none;
	background: white;
	color: #4e5665;
	border-top: 1px solid #e9eaeb;
	overflow: hidden;
	}
	.urlpreview-search-wrapper i {
	width: 27px;
	display: inline-block;
	text-align: center;
	}
	.urlpreview-search-wrapper i.icon {
	border-right: 1px solid #d3d4d5;
	}
	.urlpreview-search-wrapper input {
	width: 80%;
	display: inline-block;
	background: white;
	color: #4e5665;
	padding: 5px;
	border: 0;
	}
	.urlpreview-search-wrapper .input-result-wrapper {
	max-height: 300px;
	display: none;
	background: white;
	color: #4e5665;
	border-top: 1px solid #e9eaeb;
	overflow: auto;
	}
	.urlpreview-search-wrapper .input-result-wrapper .loading-wrapper {
	color: #838483;
	padding: 7px;
	}
	.urlpreview-search-wrapper .remove-btn {
	position: absolute;
	top: 5px;
	right: 4px;
	color: #898f9c;
	font-size: 12px;
	cursor: pointer;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'urlpreview_search_css');
