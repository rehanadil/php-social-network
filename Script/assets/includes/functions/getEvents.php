<?php
function getEvents() {
	if (!isLogged()) return null;

	global $conn, $themeData, $user, $lang;
	$currentTime = time();
    $listEvents = '';

	$queryText = "SELECT id,location,start_time,end_time
        FROM " . DB_EVENTS . "
        WHERE end_time>$currentTime
        AND id IN
            (SELECT id
                FROM " . DB_ACCOUNTS . "
                WHERE active=1
                AND banned=0
            )
        ORDER BY start_time
        LIMIT 3";
	$query = $conn->query($queryText);

	while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
	{
		$eventObj = new \SocialKit\User();
        $eventObj->setId($fetch['id']);
        $event = $eventObj->getRows();

        $location = $fetch['location'];
        $startTime = $fetch['start_time'];
        $endTime = $fetch['end_time'];
        $eventTime = date('l h:i A', $startTime) . ' - ' . date('h:i A', $endTime);

        $themeData['list_event_id'] = $event['id'];
        $themeData['list_event_url'] = $event['url'];
        $themeData['list_event_username'] = $event['username'];
        $themeData['list_event_name'] = $event['name'];
        $themeData['list_event_thumbnail_url'] = $event['cover_url'];

        $themeData['list_event_location'] = $location;

        $inviteQuery = $conn->query("SELECT action
            FROM " . DB_EVENT_INVITES . "
            WHERE event_id=" . $fetch['id'] . "
            AND timeline_id=" . $user['id'] . "
            AND active=1");
        
        $inviteType = "";

        if ($inviteQuery->num_rows == 1)
        {
            $inviteFetch = $inviteQuery->fetch_array(MYSQLI_ASSOC);
            $inviteType = $inviteFetch['action'];
        }

        switch ($inviteType)
        {
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
        
        $listEvents .= \SocialKit\UI::view('event-bar/each');
	}

	return $listEvents;
}
