<?php
function registerGroupAdmin($groupId=0, $adminId=0)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn, $lang;
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
    
    if (! $groupObj->isGroupAdmin())
    {
        return false;
    }
    
    if ($groupObj->isGroupAdmin($adminId))
    {
        return false;
    }
    
    $query = $conn->query("INSERT INTO " . DB_GROUP_ADMINS . " (active,admin_id,group_id) VALUES (1," . $adminId . "," . $groupId . ")");
    
    if ($query)
    {
        registerNotification(array(
            'recipient_id' => $adminId,
            'text' => str_replace('{group_name}', '[b weight=500]'. $group['name'] .'[/b]', $lang['made_group_admin']),
            'type' => 'made_group_admin',
            'url' => 'index.php?a=timeline&id=' . $group['username']
        ));
        
        return true;
    }
}
