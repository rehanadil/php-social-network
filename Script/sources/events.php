<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}

if (!isset($_GET['b'])) $_GET['b'] = "upcoming";

$themeData['page_title'] = $lang['events_label'] . ' - ' . $lang['event_' . $_GET['b'] . '_label'];

$currentTime = time();
$actionTypes = '';
$actionTypesArray = array(
    'upcoming' => array(
        'icon' => 'calendar',
        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE type='event' AND active=1 AND banned=0) AND start_time>$currentTime"
        ),
    'going' => array(
        'icon' => 'check-circle',
        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND action='going' AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0)"
        ),
    'interested' => array(
        'icon' => 'star',
        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND action='interested' AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0)"
        ),
    'invited' => array(
        'icon' => 'inbox',
        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND action='invited' AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0)"
        ),
    'past' => array(
        'icon' => 'calendar-check-o',
        'query' => "SELECT id FROM " . DB_EVENTS . " WHERE id IN (SELECT event_id FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $user['id'] . " AND active=1) AND id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE active=1 AND banned=0) AND end_time<$currentTime"
        )
    );

foreach ($actionTypesArray as $i => $v)
{
    $cls = "eventType";

    if ($i == $_GET['b']) $cls = "eventType active";

    $themeData['action_type'] = $i;
    $themeData['action_type_url'] = smoothLink('index.php?a=events&b=' . $i);
    $themeData['action_type_class'] = $cls;
    $themeData['action_type_icon'] = $v['icon'];
    $themeData['action_type_name'] = $lang['event_' . $i . '_label'];

    $actionTypes .= \SocialKit\UI::view('events/action-type');
}

$themeData['events_header'] = $actionTypes;

if (isset ($actionTypesArray[$_GET['b']]))
{
    $listEvents = '';
    $query = $conn->query($actionTypesArray[$_GET['b']]['query'] . " ORDER BY start_time ASC LIMIT 10");
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
        $themeData['list_event_name'] = $event['name'];
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
    $themeData['list_events'] = $listEvents;
}

$themeData['page_content'] = \SocialKit\UI::view('events/content');
