<?php
/* Get user Id */
function getUserId(\mysqli $conn, $u=0)
{
    if (is_numeric($u))
    {
        return (int) $u;
    }
    $escapeObj = new \SocialKit\Escape();
    $u = $escapeObj->stringEscape($u);

    if (filter_var($u, FILTER_VALIDATE_EMAIL))
    {
        $query = $conn->query("SELECT id FROM accounts WHERE email='$u'");
    }
    else
    {
        $query = $conn->query("SELECT id FROM accounts WHERE username='$u'");
    }

    if ($query->num_rows == 1)
    {
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        return $fetch['id'];
    }

    return false;
}
