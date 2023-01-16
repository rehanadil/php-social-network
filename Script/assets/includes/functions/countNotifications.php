<?php
/* Notifications */
function countNotifications($timelineId=0, $unread=true)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn;
    $timelineId = (int) $timelineId;
    
    if ($timelineId < 1)
    {
        global $user;
        $timelineId = $user['id'];
        $timeline = $user;
    }
    else
    {
        global $user;
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
    }
    
    $queryText = "SELECT COUNT(id) AS count FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $timeline['id'] . " AND active=1";
    
    if ($unread == true)
    {
        $queryText .= " AND seen=0";
    }
    
    $queryText .= " ORDER BY id DESC";

    $query = $conn->query($queryText);
    $fetch = $query->fetch_array(MYSQLI_ASSOC);

    return $fetch['count'];
}
