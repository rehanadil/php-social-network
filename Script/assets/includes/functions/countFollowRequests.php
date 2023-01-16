<?php
/* Count follow requests */
function countFollowRequests($timelineId=0)
{
    if (! isLogged())
    {
        return false;
    }

    global $conn, $user;
    $timelineId = (int) $timelineId;
    
    if ($timelineId < 1)
    {
        $timelineId = $user['id'];
        $timeline = $user;
    }
    else
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
    }

    $query = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT follower_id FROM " . DB_FOLLOWERS . " WHERE following_id=$timelineId AND follower_id<>$timelineId AND active=0) AND active=1 AND banned=0");
    $fetch = $query->fetch_array(MYSQLI_ASSOC);
    
    return $fetch['count'];
}
