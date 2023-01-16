<?php
function story_subscription_badge($Data)
{
	global $config;
	$story = $Data['story'];
	if ($story['timeline']['type'] !== "user") return false;
	if ($story['timeline']['subscription_plan']['plan_icon'] === "") return false;
	if ($story['timeline']['subscription_plan']['is_default'] == 1) return false;
	
	return '<img class="story-subscription-badge" src="' . $story['timeline']['subscription_plan']['plan_icon'] . '">';
}
\SocialKit\Addons::register('story_timeline_link_ui_editor', 'story_subscription_badge');

/* CSS */
function story_subscription_badge_css()
{
	return '<style>
	.story-subscription-badge {
		display: inline-block;
	    vertical-align: middle;
	    margin: 0 0 3px 0;
	    border-radius: 100%;
	    width: 14px;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'story_subscription_badge_css');