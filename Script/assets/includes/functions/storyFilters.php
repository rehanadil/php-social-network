<?php
function storyFilters($a=array())
{
	global $themeData;
	$timelineId = (isset($a['timeline_id'])) ? (int) $a['timeline_id'] : 0;
	$timelineObj = new \SocialKit\User();
	$timelineObj->setId($timelineId);
	$isReal = $timelineObj->isReal();

	$themeData['timeline_id'] = $timelineId;
	$themeData['filter_all'] = \SocialKit\UI::view('story/filters/all');
	$themeData['filter_texts'] = \SocialKit\UI::view('story/filters/texts');
	$themeData['filter_photos'] = \SocialKit\UI::view('story/filters/photos');
	$themeData['filter_videos'] = \SocialKit\UI::view('story/filters/videos');
	$themeData['filter_audios'] = \SocialKit\UI::view('story/filters/audios');
	$themeData['filter_places'] = \SocialKit\UI::view('story/filters/places');

	if ($isReal)
	{
		if ($timelineObj->isReal('user'))
		{
			$themeData['filter_likes'] = \SocialKit\UI::view('story/filters/likes');
			$themeData['filter_shares'] = \SocialKit\UI::view('story/filters/shares');
		}

		if (!$timelineObj->isReal('group'))
			$themeData['filter_others'] = \SocialKit\UI::view('story/filters/others');
	}

	return \SocialKit\UI::view('story/filters/content');
}