<?php
function deleteGroupAdmin($groupId=0, $adminId=0)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn;
    $groupId = (int) $groupId;
    $adminId = (int) $adminId;

    $groupObj = new \SocialKit\User();
    $groupObj->setId($groupId);
    $group = $groupObj->getRows();

    $adminObj = new \SocialKit\User();
    $adminObj->setId($adminId);
    $admin = $adminObj->getRows();
    
    if (! isset($group['id']))
    {
        return false;
    }
    
    if (! isset($admin['id']))
    {
        return false;
    }
    
    if ($group['type'] != "group")
    {
        return false;
    }
    
    if ($admin['type'] != "user")
    {
        return false;
    }
    
    if (! $groupObj->isAdmin())
    {
        return false;
    }

    if ($groupObj->numGroupAdmins() < 2)
    {
        return false;
    }
    
    $query = $conn->query("DELETE FROM " . DB_GROUP_ADMINS . " WHERE admin_id=" . $adminId . " AND group_id=" . $groupId);
    
    if ($query)
    {
        return true;
    }
}
