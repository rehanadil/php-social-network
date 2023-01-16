<?php
function comment_verified_badge($Data)
{
	$conn = getConnection();
	$comment = $Data['comment'];
	
	if ($comment['timeline']['verified'] == 1
		|| (isset($comment['timeline']['subscription_plan'])
			&& $comment['timeline']['subscription_plan']['verified_badge'] == 1)
		)
	{
		return '<span class="comment-verified-badge"><i class="fa fa-check"></i></span>';
	}
}
\SocialKit\Addons::register('comment_timeline_link_ui_editor', 'comment_verified_badge');

/* CSS */
function comment_verified_badge_css()
{
	return '<style>
	.comment-verified-badge {
		display: inline-block;
	    vertical-align: middle;
	    background: #6ba1d0;
	    color: white;
	    text-shadow: 0 0 0 white;
	    font-size: 7px;
	    margin: 0 3px 3px 0;
	    padding: 3px;
	    border-radius: 100%;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'comment_verified_badge_css');