<?php
function comment_subscription_badge($Data)
{
	global $config;
	$comment = $Data['comment'];
	if ($comment['timeline']['type'] !== "user") return false;
	if ($comment['timeline']['subscription_plan']['plan_icon'] === "") return false;
	if ($comment['timeline']['subscription_plan']['is_default'] == 1) return false;
	
	return '<img class="comment-subscription-badge" src="' . $comment['timeline']['subscription_plan']['plan_icon'] . '">';
}
\SocialKit\Addons::register('comment_timeline_link_ui_editor', 'comment_subscription_badge');

/* CSS */
function comment_subscription_badge_css()
{
	return '<style>
	.comment-subscription-badge {
		display: inline-block;
	    vertical-align: middle;
	    margin: 0 0 3px 0;
	    border-radius: 100%;
	    width: 13px;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'comment_subscription_badge_css');