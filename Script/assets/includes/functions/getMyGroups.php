<?php
/* Get my groups */
function getMyGroups()
{
    if (!isLogged()) return array();
    
    global $conn, $user;
    $get = array();

    $query = $conn->query("SELECT id
        FROM " . DB_ACCOUNTS . "
        WHERE id IN
            (SELECT group_id
                FROM " . DB_GROUP_ADMINS . "
                WHERE admin_id=" . $user['id'] . "
                AND group_id IN
                    (SELECT id FROM " . DB_GROUPS .")
                AND active=1
            )
        AND type='group'
        AND active=1
        AND banned=0");

    while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($fetch['id']);
        $get[] = $timelineObj->getRows();
    }
    
    return $get;
}
