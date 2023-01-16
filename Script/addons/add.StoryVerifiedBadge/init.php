<?php
function story_verified_badge ($Data)
{
	$conn = getConnection();
	$story = $Data['story'];
	
	if ($story['timeline']['verified'] == 1
		|| (isset($story['timeline']['subscription_plan'])
			&& $story['timeline']['subscription_plan']['verified_badge'] == 1)
		)
	{
		return '<span class="story-verified-badge"><i class="fa fa-check"></i></span>';
	}
}
\SocialKit\Addons::register('story_timeline_link_ui_editor', 'story_verified_badge');

/* CSS */
function story_verified_badge_css()
{
	return '<style>
	.story-verified-badge {
		display: inline-block;
	    vertical-align: middle;
	    background: #6ba1d0;
	    color: white;
	    text-shadow: 0 0 0 white;
	    font-size: 8px;
	    margin: 0 5px 3px 0;
	    padding: 2px 3px 1px;
	    border: 0;
	    border-radius: 100%;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'story_verified_badge_css');