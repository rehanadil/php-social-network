<?php
function youtube_search_launchericon()
{
	return '<span class="option youtube" onclick="toggleMediaGroup(\'.youtube-search-wrapper\');">
        <i class="fa fa-youtube"></i>
    </span>';
}
\SocialKit\Addons::register('new_story_feature_launchericon', 'youtube_search_launchericon');
