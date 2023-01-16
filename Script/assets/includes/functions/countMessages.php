<?php
/* Count messages */
function countMessages($p=array())
{
    if (!isLogged()) return false;
    
    global $conn, $user, $userObj;
    $timelineId = (isset($p['timeline_id'])) ? (int) $p['timeline_id'] : $user['id'];
    $recipientId = (isset($p['recipient_id'])) ? (int) $p['recipient_id'] : 0;
    $new = (isset($p['new'])) ? true : false;
    
    if ($timelineId === $user['id'])
    {
        $timelineObj = $userObj;
        $timeline = $user;
    }
    else
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
        if (!$timelineObj->isAdmin()) return false;
    }
    
    if ($recipientId > 0)
        if ($new)
            $sql = "SELECT COUNT(id) AS count FROM " . DB_MESSAGES . " WHERE active=1 AND timeline_id=$recipientId AND recipient_id=$timelineId";
        else
            $sql = "SELECT COUNT(id) AS count FROM " . DB_MESSAGES . " WHERE active=1 AND ((timeline_id=$recipientId AND recipient_id=$timelineId) OR (timeline_id=$timelineId AND recipient_id=$recipientId))";
    else
        $sql = "SELECT COUNT(DISTINCT timeline_id) AS count FROM " . DB_MESSAGES . " WHERE active=1 AND recipient_id=$timelineId";
    
    if ($new) $sql .= " AND seen=0";

    $query = $conn->query($sql);
    $fetch = $query->fetch_array(MYSQLI_ASSOC);
    if ($fetch['count'] > 0) return $fetch['count'];
    return false;
}
