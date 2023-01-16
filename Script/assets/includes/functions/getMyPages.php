<?php
/* Get my pages */
function getMyPages()
{
    if (!isLogged()) return array();
    
    global $conn, $user;
    $get = array();

    $query = $conn->query("SELECT id
        FROM " . DB_ACCOUNTS . "
        WHERE id IN
            (SELECT page_id
                FROM " . DB_PAGE_ADMINS . "
                WHERE admin_id=" . $user['id'] . "
                AND page_id IN
                    (SELECT id FROM " . DB_PAGES .")
                AND active=1
            )
        AND type='page'
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
