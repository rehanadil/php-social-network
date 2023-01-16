<?php
/* Get onlines */
function getOnlines($timelineId=0, $search_query='')
{
    if (! isLogged())
    {
        return array();
    }
    
    global $conn;
    $get = array();
    $excludes = array();
    $timelineId = (int) $timelineId;
    
    if ($timelineId < 1)
    {
        global $user;
        $timelineId = $user['id'];
    }
    
    $queryText = "SELECT id
        FROM " . DB_ACCOUNTS . "
        WHERE id IN
            (SELECT following_id
                FROM " . DB_FOLLOWERS . "
                WHERE follower_id=$timelineId
                AND active=1
            )
        AND type='user'
        AND last_logged>" . (time()-15) . "
        AND active=1
        AND banned=0";
    
    if (! empty($search_query))
    {
        $escapeObj = new \SocialKit\Escape();
        $search_query = $escapeObj->stringEscape($search_query);
        $queryText .= " AND name LIKE '%$search_query%'";
    }
    
    $queryText .= " ORDER BY last_logged DESC";
    $query = $conn->query($queryText);
    
    while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($fetch['id']);
        $get[] = $timelineObj->getRows();
        $excludes[] = $fetch['id'];
    }
    
    $exclude_query_string = 0;
    $exclude_i = 0;
    $excludes_num = count($excludes);
    
    if ($excludes_num > 0)
    {
        $exclude_query_string = '';
        
        foreach ($excludes as $exclude)
        {
            $exclude_i++;
            $exclude_query_string .= $exclude;
            
            if ($exclude_i != $excludes_num)
            {
                $exclude_query_string .= ',';
            }
        }
    }
    
    $query2Text = "SELECT DISTINCT id
        FROM " . DB_ACCOUNTS . "
        WHERE id NOT IN ($exclude_query_string)
        AND id IN
            (SELECT timeline_id
                FROM " . DB_MESSAGES . "
                WHERE recipient_id=$timelineId
                AND active=1
                AND seen=0
                ORDER BY id DESC
            )
        AND active=1
        AND banned=0";
    
    if (! empty($query2Text))
    {
        $query2Text .= " AND name LIKE '%$search_query%'";
    }
    
    $query2 = $conn->query($query2Text);
    
    while ($fetch2 = $query2->fetch_array(MYSQLI_ASSOC))
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($fetch2['id']);
        $get[] = $timelineObj->getRows();
    }
    
    return $get;
}
