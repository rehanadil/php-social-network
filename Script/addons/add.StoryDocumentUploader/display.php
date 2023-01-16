<?php
function story_display_doc($Data)
{
	global $config;
	$conn = getConnection();
	$story = $Data['data'];
	if (!isset($story['media']['id'])) return false;

	$docObj = new \SocialKit\Media();
	$docObj->setId($story['media']['id']);
	$doc = $docObj->getRows();

	if ($doc['type'] === "document")
	{
		return '<a class="storyDocViewer" href="' . $doc['complete_url']  . '" target="_blank">
			<i class="fa fa-file-archive-o"></i>
			<span>' . $doc['name'] . '</span>
		</a>';
	}
}
\SocialKit\Addons::register('story_display_addon_data', 'story_display_doc');