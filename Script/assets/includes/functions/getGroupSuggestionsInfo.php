<?php
function getGroupSuggestionsInfo($searchQuery='', $limit=5)
{
    if (! isLogged())
    {
        return array();
    }
    
    $limit = (int) $limit;
    $get = array();
    
    if ($limit < 1)
    {
        $limit = 5;
    }
    
    global $conn, $user;
    
    $queryText = "SELECT id
        FROM " . DB_ACCOUNTS . "
        WHERE id NOT IN
            (SELECT following_id
                FROM " . DB_FOLLOWERS . "
                WHERE follower_id=" . $user['id'] . "
            )
        AND id<>" . $user['id'] . "
        AND id IN
            (SELECT id
                FROM " . DB_GROUPS . "
                WHERE group_privacy IN ('open','closed')
            )
        AND active=1
        AND banned=0";

    if (! empty($searchQuery))
    {
        $escapeObj = new \SocialKit\Escape();
        $searchQuery = $escapeObj->stringEscape($searchQuery);
        $queryText .= " AND name LIKE '%$searchQuery%'";
    }
    
    $queryText .= " ORDER BY RAND() LIMIT $limit";
    $query = $conn->query($queryText);

    while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
    {
        $get[] = $fetch['id'];
    }

    return $get;
}