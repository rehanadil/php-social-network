<?php
function getMessageRecipients($timelineId=0, $searchQuery='', $new=false, $limit=10)
{
    if (! isLogged())
    {
        return array();
    }
    
    global $conn;
    $get = array();
    $excludes = array();
    $timelineId = (int) $timelineId;
    $limit = (int) $limit;
    $escapeObj = new \SocialKit\Escape();
    $searchQuery = $escapeObj->stringEscape($searchQuery);
    
    if ($timelineId < 1)
    {
        global $user;
        $timelineId = $user['id'];
    }

    if ($limit < 1)
    {
        $limit = 10;
    }

    if (! empty($searchQuery))
    {
        $query = $conn->query("SELECT id
            FROM " . DB_ACCOUNTS . "
            WHERE id IN
                (SELECT following_id
                    FROM " . DB_FOLLOWERS . "
                    WHERE follower_id=$timelineId
                    AND following_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . ")
                    AND active=1
                )
            AND active=1
            AND banned=0
            AND name LIKE '%$searchQuery%'
            LIMIT $limit");
        
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $timelineObj = new \SocialKit\User();
            $timelineObj->setId($fetch['id']);
            $get[] = $timelineObj->getRows();
        }

        return $get;
    }
    else
    {
        $excludes = array(0);

        $queryText = "SELECT id
            FROM " . DB_ACCOUNTS . "
            WHERE id IN
                (SELECT timeline_id
                    FROM " . DB_MESSAGES . "
                    WHERE recipient_id=$timelineId
                    AND active=1
                    AND seen=0
                    ORDER BY seen ASC, id DESC
                )
            AND active=1
            AND banned=0";
        $query = $conn->query($queryText);

        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $timelineObj = new \SocialKit\User();
            $timelineObj->setId($fetch['id']);
            $get[] = $timelineObj->getRows();
            $excludes[] = $fetch['id'];
        }

        $excludeQuery = implode(',', $excludes);

        if (! $new)
        {
            $queryText = "SELECT id
                FROM " . DB_ACCOUNTS . "
                WHERE id NOT IN ($excludeQuery)
                AND (
                    id IN
                        (SELECT timeline_id
                            FROM " . DB_MESSAGES . "
                            WHERE recipient_id=$timelineId
                            AND active=1
                            AND seen>0
                        )
                    OR id IN
                        (SELECT recipient_id
                            FROM " . DB_MESSAGES . "
                            WHERE timeline_id=$timelineId
                            AND active=1
                        )
                )
                AND active=1
                AND banned=0
                ORDER BY id DESC
                LIMIT $limit";
            $query = $conn->query($queryText);

            while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
            {
                $timelineObj = new \SocialKit\User();
                $timelineObj->setId($fetch['id']);
                $get[] = $timelineObj->getRows();
            }
        }

        if (! isset($get[0]))
        {
            $queryText = "SELECT id
                FROM " . DB_ACCOUNTS . "
                WHERE id IN
                    (SELECT following_id
                        FROM " . DB_FOLLOWERS . "
                        WHERE follower_id=$timelineId
                        AND active=1
                    )
                AND active=1
                ORDER BY id DESC
                LIMIT $limit";
            $query = $conn->query($queryText);

            while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
            {
                $timelineObj = new \SocialKit\User();
                $timelineObj->setId($fetch['id']);
                $get[] = $timelineObj->getRows();
            }
        }

        return $get;
    }
    
    return array();
}
