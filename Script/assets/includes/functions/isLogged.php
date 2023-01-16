<?php
/* Log In Check */
function isLogged()
{
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_pass']))
    {
        $userId = (int) $_SESSION['user_id'];
        $userPass = $_SESSION['user_pass'];

        if (isset($_SESSION['_cache_']['user_data'][$userId]))
        {
            $cche_usrdata = $_SESSION['_cache_']['user_data'][$userId];

            if (is_array($cche_usrdata))
            {
                if ($cche_usrdata['password'] == $userPass)
                {
                    return true;
                }
            }
        }

        global $conn;
        $query = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE id=$userId AND password='$userPass' AND type='user' AND active=1 AND banned=0");
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        
        return $fetch['count'];
    }
}
