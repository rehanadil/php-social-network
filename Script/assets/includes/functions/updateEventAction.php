<?php
function updateEventAction($eventId = 0, $timelineId = 0, $action = 'all')
{
    $eventId = (int) $eventId;
    $timelineId = (int) $timelineId;
    if ($eventId < 1)
    {
        return false;
    }
    if ($timelineId < 1)
    {
        return false;
    }

    if (! preg_match('/(going|interested|invited)/', $action))
    {
        return false;
    }

    $checkQuery = "SELECT id,action FROM " . DB_EVENT_INVITES . " WHERE event_id=$eventId AND timeline_id=$timelineId";
    
    global $conn;
    $check = $conn->query($checkQuery);
    
    if ($check->num_rows > 0)
    {
        $queryText = "UPDATE " . DB_EVENT_INVITES . " SET action='$action' WHERE event_id=$eventId AND timeline_id=$timelineId";
    }
    else
    {
        $queryText = "INSERT INTO " . DB_EVENT_INVITES . " (action,active,event_id,timeline_id) VALUES ('$action',1,'$eventId','$timelineId')";
    }

    $query = $conn->query($queryText);

    if ($query)
    {
        return true;
    }
}