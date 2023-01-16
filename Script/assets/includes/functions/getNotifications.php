<?php
/* Get notifications */
function getNotifications($timelineId=0, $unread=false, $all=false)
{
    if (! isLogged())
    {
        return array();
    }
    
    $get = array();
    $timelineId = (int) $timelineId;
    
    if ($timelineId < 1)
    {
        global $user, $userObj;
        $timelineId = $user['id'];
        $timelineObj = $userObj;
        $timeline = $user;
    }
    else
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
    }

    if (! $timelineObj->isAdmin())
    {
        return array();
    }
    
    $new_notif = countNotifications();
    
    if ($new_notif > 0)
    {
        $queryText = "SELECT id,notifier_id,post_id,seen,text,time,timestamp,timeline_id,url FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $timelineId . " AND active=1 AND seen=0 ORDER BY id DESC";
    }
    else
    {
        $queryText = "SELECT id,notifier_id,post_id,seen,text,time,timestamp,timeline_id,url FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $timelineId . " AND active=1";
        
        if ($unread)
        {
            $queryText .= " AND seen=0";
        }
        
        $queryText .= " ORDER BY id DESC LIMIT 20";
    }
    
    if ($all)
    {
        $queryText = "SELECT id,notifier_id,post_id,seen,text,time,timestamp,timeline_id,url FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $timelineId . " AND active=1 AND seen=0 ORDER BY id DESC LIMIT 20";
    }
    
    global $conn;
    $query = $conn->query($queryText);
    
    if ($query->num_rows > 0)
    {
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $notifierObj = new \SocialKit\User();
            $notifierObj->setId($fetch['notifier_id']);
            $fetch['notifier'] = $notifierObj->getRows();

            $fetch['raw_url'] = $fetch['url'];
            $fetch['url'] = smoothLink($fetch['url']);

            $fetch['text'] = preg_replace(
                '/\[b(| weight\=)(|[0-9]+)\](.*?)\[\/b\]/i',
                '<strong style="font-weight: $2;">$3</strong>',
                $fetch['text']
            );
            $get[] = $fetch;
        }
    }
    
    $conn->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE time<" . (time() - (60 * 60 * 24 * 2)) . " AND seen>0");
    return $get;
}
