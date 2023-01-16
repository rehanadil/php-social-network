<?php
/* Get username status */
function getUsernameStatus($query='', $timelineId=0)
{
    $escapeObj = new \SocialKit\Escape();
    $query = $escapeObj->stringEscape($query);
    $timelineId = (int) $timelineId;
    
    if (! validateUsername($query))
    {
        return 406;
    }
    
    if (strlen($query) < 4)
    {
        return 410;
    }
    
    /*if ($timelineId < 1)
    {
        if (isLogged())
        {
            global $user;
            $timelineId = $user['id'];
        }
    }*/
    
    if (isLogged())
    {
        if ($timelineId < 1)
        {
            global $user, $userObj;
            $timelineId = $user['id'];
            $timelineObj = $userObj;
            $timeline = $user;

            if ($query == $user['username'])
            {
                return 201;
            }
        }
        else
        {
            $timelineId = (int) $timelineId;
            $timelineObj = new \SocialKit\User();
            $timelineObj->setId($timelineId);
            $timeline = $timelineObj->getRows();
        }

        if (empty($timeline['id']))
        {
            return false;
        }
        
        if (! $timelineObj->isAdmin())
        {
            return false;
        }
        
        if ($query == $timeline['username'])
        {
            return 201;
        }
    }
    
    global $conn;
    $query = $conn->query("SELECT id
        FROM " . DB_ACCOUNTS . "
        WHERE username='$query'");
    
    if ($query->num_rows == 0)
    {
        return 200;
    }
    else
    {
        return 410;
    }
}
