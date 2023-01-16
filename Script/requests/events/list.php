<?php
userOnly();

if (isset ($_GET['after_id']) && isset ($_GET['type']))
{
	$after_id = (int) $_GET['after_id'];
	$type = $_GET['type'];

	if (preg_match('/(upcoming|going|interested|invited|past)/', $type))
	{
		$currentTime = time();
		$actionTypesArray = array(
		    'upcoming' => array(
		        'icon' => 'calendar',
		        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id>$after_id AND id NOT IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0) AND start_time>$currentTime"
		        ),
		    'going' => array(
		        'icon' => 'check-circle',
		        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id>$after_id AND id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND action='going' AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0)"
		        ),
		    'interested' => array(
		        'icon' => 'star',
		        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id>$after_id AND id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND action='interested' AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0)"
		        ),
		    'invited' => array(
		        'icon' => 'inbox',
		        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id>$after_id AND id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND action='invited' AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0)"
		        ),
		    'past' => array(
		        'icon' => 'calendar-check-o',
		        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id>$after_id AND id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0) AND end_time<$currentTime"
		        )
		    );

	    $listEvents = '';
	    $query = $conn->query($actionTypesArray[$type]['query'] . " ORDER BY id ASC LIMIT 10");
	    while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
	    {
	        $eventObj = new \SocialKit\User();
	        $eventObj->setId($fetch['id']);
	        $event = $eventObj->getRows();

	        $location = $event['location'];
	        $startTime = $event['start_time'];
	        $endTime = $event['end_time'];
	        $eventTime = date('l h:i A', $startTime) . ' - ' . date('h:i A', $endTime);

	        $themeData['list_event_id'] = $event['id'];
	        $themeData['list_event_url'] = $event['url'];
	        $themeData['list_event_username'] = $event['username'];
	        $themeData['list_event_name'] = substr($event['name'], 0, 13);
	        $themeData['list_event_thumbnail_url'] = $event['cover_url'];

	        $themeData['list_event_location'] = $location;

	        $inviteQuery = $conn->query("SELECT action FROM " . DB_EVENT_INVITES . " WHERE event_id=" . $fetch['id'] . " AND timeline_id=" . $user['id'] . " AND active=1");
	        $inviteType = '';
	        if ($inviteQuery->num_rows == 1)
	        {
	            $inviteFetch = $inviteQuery->fetch_array(MYSQLI_ASSOC);
	            $inviteType = $inviteFetch['action'];
	        }

	        switch ($inviteType) {
	            case 'going':
	                $themeData['event_status_icon'] = 'check-circle';
	                $themeData['event_status'] = $lang['event_going_label'];
	                break;

	            case 'interested':
	                $themeData['event_status_icon'] = 'star';
	                $themeData['event_status'] = $lang['event_interested_label'];
	                break;

	            case 'invited':
	                $themeData['event_status_icon'] = 'inbox';
	                $themeData['event_status'] = $lang['event_invited_label'];
	                break;
	            
	            default:
	                $themeData['event_status_icon'] = 'circle-o';
	                $themeData['event_status'] = $lang['event_notgoing_label'];
	                break;
	        }

	        $oneHour = 60 * 60;
	        $threeDays = 60 * 60 * 24 * 3;
	        $oneWeek = 60 * 60 * 24 * 7;

	        if ($startTime < $currentTime)
	        {
	            $eventTime = 'Happening Now';
	        }
	        elseif (($currentTime + $oneHour) > $startTime)
	        {
	            $minsRemaining = round(($startTime - $currentTime) / 60);
	            $eventTime = str_replace("{minCount}", $minsRemaining, $lang['event_in_minutes_label']);
	        }
	        elseif (($currentTime + $threeDays) > $startTime)
	        {
	            $hoursRemaining = ceil((($startTime - $currentTime) / 60) / 60);
	            $eventTime = str_replace("{hrsCount}", $hoursRemaining, $lang['event_in_hours_label']);
	        }
	        elseif (($currentTime + $oneWeek) > $startTime)
	        {
	            $eventTime = date('l h:i A', $startTime);
	        }
	        else
	        {
	            $eventTime = str_replace("{eventTime}", date('M j h:i A', $startTime), $lang['on_event_time_label']);
	        }

	        $themeData['list_event_time'] = $eventTime;
	        
	        $listEvents .= \SocialKit\UI::view('events/each');
	    }

	    $data = array(
		    'status' => 200,
		    'html' => $listEvents
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();