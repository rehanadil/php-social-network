<?php
function getEventBar()
{
    global $themeData;
    $events = getEvents();
    if ($events)
    {
    	$themeData['list_events'] = $events;
    	return \SocialKit\UI::view('event-bar/content');
    }
}

