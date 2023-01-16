<?php
/* Search */
function getSearch($searchQuery='', $fromRow=0, $limit=10)
{
    global $conn, $user;
    $get = array();
    $escapeObj = new \SocialKit\Escape();
    $searchQuery = $escapeObj->stringEscape($searchQuery);
    $fromRow = (int) $fromRow;
    $limit = (int) $limit;
    
    if (empty($searchQuery))
    {
        return false;
    }
    
    if ($fromRow < 0)
    {
        return false;
    }
    
    if ($limit < 1)
    {
        return false;
    }
    
    $query = $conn->query("SELECT id FROM " . DB_ACCOUNTS . "
        WHERE (
            name LIKE _utf8'%$searchQuery%' collate utf8_general_ci
            OR username='$searchQuery'
            OR email='$searchQuery'
            OR id IN
                (SELECT id FROM " . DB_PAGES . "
                    WHERE category_id IN
                        (SELECT id FROM " . DB_PAGE_CATEGORIES . "
                            WHERE name LIKE _utf8'%$searchQuery%' collate utf8_general_ci)
                )
            OR id IN
                (SELECT id FROM " . DB_USERS . "
                    WHERE country_id IN
                        (SELECT id FROM " . DB_COUNTRIES . "
                            WHERE name LIKE _utf8'%$searchQuery%' collate utf8_general_ci)
                )
            OR id IN
                (SELECT id FROM " . DB_USERS . "
                    WHERE current_city LIKE _utf8'%$searchQuery%' collate utf8_general_ci
                )
            OR id IN
                (SELECT id FROM " . DB_USERS . "
                    WHERE hometown LIKE _utf8'%$searchQuery%' collate utf8_general_ci
                )
        )
        AND (
            id IN
                (SELECT id FROM " . DB_USERS . "
                    WHERE (id NOT IN
                            (SELECT blocked_id FROM " . DB_BLOCKED_USERS . " 
                                WHERE blocker_id=" . $user['id'] . ")
                            AND id NOT IN
                                (SELECT blocker_id FROM " . DB_BLOCKED_USERS . "
                                    WHERE blocked_id=" . $user['id'] . ")
                    )
                )
                OR id IN
                    (SELECT id FROM " . DB_PAGES . ")
                OR id IN
                    (SELECT id FROM " . DB_GROUPS . "
                        WHERE group_privacy IN ('open','closed')
                    )
        )
        AND type IN ('user','page','group')
        AND active=1
        AND banned=0
        ORDER BY name ASC
        LIMIT $fromRow,$limit");
    
    if ($query->num_rows > 0)
    {
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $get[] = $fetch['id'];
        }

        return $get;
    }
}
