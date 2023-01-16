<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=home');
	else
		header('Location: ' . smoothLink('index.php?a=home'));
}
$themeData['page_title'] = $timeline['name'];

$isGroupAdmin = $timelineObj->isGroupAdmin();
$isFollowing = $timelineObj->isFollowing();
$isFollowedBy = $timelineObj->isFollowedBy();
$numFollowers = $timelineObj->numFollowers();
$numFollowRequests = $timelineObj->numFollowRequests();
$numGroupAdmins = $timelineObj->numGroupAdmins();
$numPosts = $timelineObj->numStories();
$joinButton = $timelineObj->getFollowButton();

if (!isset($_GET['b'])) $_GET['b'] = "stories";

$eventDescr = $timeline['about'];
$eventDescr = $escapeObj->getEmoticons($eventDescr);
$eventDescr = $escapeObj->getLinks($eventDescr);

$themeData['event_date'] = strtoupper(date('M', $timeline['start_time'])) . '<br>' . strtoupper(date('j', $timeline['start_time']));
$themeData['event_location'] = $timeline['location'];
$themeData['event_description'] = $eventDescr;
$themeData['event_raw_description'] = $timelineObj->getRawAbout();
$themeData['event_time'] = date('h:i A', $timeline['start_time']);

if (!empty($timeline['end_time'])) {
	if (date('Mj',$timeline['start_time']) == date('Mj',$timeline['end_time']))
	{
		$themeData['event_time'] .=  ' - ' . date('h:i A', $timeline['end_time']);
	}
	else {
    	$themeData['event_time'] .=  ' - ' . date('M j h:i A', $timeline['end_time']);
	}
}

$isInvited = $conn->query("SELECT id,action FROM " . DB_EVENT_INVITES . " WHERE event_id=" . $timeline['id'] . " AND timeline_id=" . $user['id'] . " AND active=1");
if ($isInvited->num_rows == 1)
{
	$inviteFetch = $isInvited->fetch_array(MYSQLI_ASSOC);
	$inviteType = $inviteFetch['action'];

	switch ($inviteType) {
		case 'going':
			$themeData['is_going'] = 1;
			break;

		case 'interested':
			$themeData['is_interested'] = 1;
			break;

		case 'invited':
			$themeData['is_invited'] = 1;
			break;
		
		default:
			break;
	}
}

$themeData['going_count'] = isEventInvited($timeline['id'], 0, 'going');
$themeData['interested_count'] = isEventInvited($timeline['id'], 0, 'interested');
$themeData['invited_count'] = isEventInvited($timeline['id'], 0, 'invited');

if ($timeline['timeline_id'] == $user['id'])
{
	$themeData['edit_description'] = \SocialKit\UI::view('timeline/event/edit-description');
	$themeData['edit_description_button'] = \SocialKit\UI::view('timeline/event/edit-description-btn');
	$themeData['timeline_buttons'] = \SocialKit\UI::view('timeline/event/timeline-buttons-admin');
}
else
{
	$themeData['timeline_buttons'] = \SocialKit\UI::view('timeline/event/timeline-buttons-default');
}

$themeData['list_invites'] = getEventInviteList('', $timeline['id']);

/* Event Info */
if ($timeline['private'] == 1)
{
	$themeData['event_privacy'] = $lang['private_label'];
	$themeData['event_privacy_icon'] = 'lock';
}
else
{
	$themeData['event_privacy'] = $lang['public_label'];
	$themeData['event_privacy_icon'] = 'globe';
}

$themeData['sidebar'] = \SocialKit\UI::view('timeline/event/sidebar');

/* Event Host */
$eventHostObj = new \SocialKit\User();
$eventHostObj->setId($timeline['timeline_id']);
$eventHost = $eventHostObj->getRows();

$themeData['event_host_url'] = $eventHost['url'];
$themeData['event_host_name'] = $eventHost['name'];

/* Stories */
$themeData['story_postbox'] = getStoryPostBox(0, $timeline['id']);

$feedObj = new \SocialKit\Feed($conn);
$feedObj->setTimelineId($timeline['id']);
$themeData['stories'] = $feedObj->getTemplate();

$themeData['tab_content'] = \SocialKit\UI::view('timeline/event/stories-tab');

if ($timeline['timeline_id'] == $user['id'])
{
    $themeData['end'] = \SocialKit\UI::view('timeline/event/admin-end');
}
else
{
    $themeData['end'] = \SocialKit\UI::view('timeline/event/default-end');
}

/* */

$themeData['page_content'] = \SocialKit\UI::view('timeline/event/content');
