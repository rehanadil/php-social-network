<?php
/* Get message recipients */
function countMessageRecipients($timelineId=0, $searchQuery='', $new=false)
{
    if (! isLogged())
    {
        return 0;
    }
    
    global $conn;
    $get = 0;
    $excludes = array();
    $timelineId = (int) $timelineId;
    $escapeObj = new \SocialKit\Escape();
    $searchQuery = $escapeObj->stringEscape($searchQuery);
    
    if ($timelineId < 1)
    {
        global $user;
        $timelineId = $user['id'];
    }

    if (! empty($searchQuery))
    {
        $query = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
            WHERE id IN
                (SELECT following_id FROM " . DB_FOLLOWERS . "
                    WHERE follower_id=$timelineId
                    AND following_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . ")
                    AND active=1
                )
            AND active=1
            AND banned=0
            AND name LIKE '%$searchQuery%'");
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        
        return $fetch['count'];
    }
    else
    {
        $get = 0;
        $excludes = array(0);

        $queryText = "SELECT id FROM " . DB_ACCOUNTS . "
            WHERE id IN
                (SELECT timeline_id FROM " . DB_MESSAGES . "
                    WHERE recipient_id=$timelineId
                    AND active=1
                    AND seen=0
                    ORDER BY seen ASC, id DESC
                )
            AND active=1
            AND banned=0";
        $query = $conn->query($queryText);
        $get = $query->num_rows;

        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $excludes[] = $fetch['id'];
        }

        $excludeQuery = implode(',', $excludes);

        if (! $new)
        {
            $queryText = "SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
            WHERE id NOT IN
                ($excludeQuery)
            AND (
                id IN
                    (SELECT timeline_id FROM " . DB_MESSAGES . "
                        WHERE recipient_id=$timelineId
                        AND active=1
                        AND seen>0
                    )
                OR id IN
                    (SELECT recipient_id FROM " . DB_MESSAGES . "
                        WHERE timeline_id=$timelineId
                        AND active=1
                    )
            )
            AND active=1
            AND banned=0
            ORDER BY id DESC";
            $query = $conn->query($queryText);
            $fetch = $query->fetch_array(MYSQLI_ASSOC);

            $get = $fetch['count'] + $get;
        }

        if ($get < 1)
        {
            $queryText = "SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . "
                WHERE id IN
                    (SELECT following_id FROM " . DB_FOLLOWERS . "
                        WHERE follower_id=$timelineId
                        AND active=1
                    )
                AND active=1
                AND banned=0
                ORDER BY id DESC";
            $query = $conn->query($queryText);
            $fetch = $query->fetch_array(MYSQLI_ASSOC);

            $get = $fetch['count'];
        }

        return $get;
    }
    
    return 0;
}
