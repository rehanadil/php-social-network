<?php
function isEventInvited($eventId = 0, $timelineId = 0, $action = 'all')
{
    $eventId = (int) $eventId;
    $timelineId = (int) $timelineId;
    if ($eventId < 1)
    {
        return false;
    }

    $queryText = "SELECT id,action FROM " . DB_EVENT_INVITES . " WHERE event_id=$eventId";
    if ($timelineId > 0)
    {
        $queryText .= " AND timeline_id=$timelineId";
    }

    switch ($action) {
        case 'going':
            $queryText .= " AND action='going'";
            break;

        case 'interested':
            $queryText .= " AND action='interested'";
            break;

        case 'invited':
            $queryText .= " AND action='invited'";
            break;
        
        default:
            $queryText .= "";
            break;
    }

    $queryText .= " AND active=1";

    global $conn;
    $query = $conn->query($queryText);
    
    if (preg_match('/(going|interested|invited)/i', $action))
    {
        return $query->num_rows;
    }
    elseif ($query->num_rows == 1)
    {
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        return $fetch['action'];
    }
}
