<?php
function countOnlines($timelineId=0)
{
    if (!isLogged()) return false;
    
    global $conn;
    $data = array();
    $timelineId = (int) $timelineId;

    if ($timelineId < 1)
    {
        global $user;
        $timelineId = $user['id'];
    }
    
    $query = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
        WHERE id IN
            (SELECT following_id FROM " . DB_FOLLOWERS . "
                WHERE follower_id=$timelineId
                AND following_id<>$timelineId
                AND active=1
            )
        AND type='user'
        AND last_logged>" . (time()-15) . "
        AND active=1
        AND banned=0
        ORDER BY last_logged DESC");
    $fetch = $query->fetch_array(MYSQLI_ASSOC);
    
    return $fetch['count'];
}
