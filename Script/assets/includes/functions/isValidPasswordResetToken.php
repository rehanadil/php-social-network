<?php
/* Is valid password reset token */
function isValidPasswordResetToken($string)
{
    $stringExp = explode('_', $string);
    $id = (int) $stringExp[0];
    $escapeObj = new \SocialKit\Escape();
    $password = $escapeObj->stringEscape($stringExp[1]);
    
    if ($id < 1)
    {
        return false;
    }
    
    global $conn;
    $query = $conn->query("SELECT id
        FROM " . DB_ACCOUNTS . "
        WHERE id=$id
        AND password='$password'
        AND active=1
        AND banned=0");
    
    if ($query->num_rows == 1)
    {
        return array(
            'id' => $id,
            'password' => $password
        );
    }
    else
    {
        return false;
    }
}
