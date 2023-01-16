<?php
/* Pages */
function registerPageAdmin($pageId=0, $adminId=0, $adminRole='')
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn, $lang;
    $pageId = (int) $pageId;
    $adminId = (int) $adminId;

    $pageObj = new \SocialKit\User();
    $pageObj->setId($pageId);
    $page = $pageObj->getRows();

    $adminObj = new \SocialKit\User();
    $adminObj->setId($adminId);
    $admin = $adminObj->getRows();
    
    if (! isset($page['id']))
    {
        return false;
    }
    
    if (! isset($admin['id']))
    {
        return false;
    }
    
    if (empty($adminRole))
    {
        return false;
    }
    
    $escapeObj = new \SocialKit\Escape();
    $adminRole = $escapeObj->stringEscape($adminRole);
    
    if (! preg_match('/(admin|editor)/', $adminRole))
    {
        return false;
    }
    
    if (! $pageObj->isAdmin())
    {
        return false;
    }
    
    if ($page['type'] != "page")
    {
        return false;
    }
    
    if ($admin['type'] != "user")
    {
        return false;
    }
    
    if ($pageObj->isAdmin($adminId))
    {
        $query = $conn->query("UPDATE " . DB_PAGE_ADMINS . " SET role='$adminRole' WHERE page_id=$pageId AND admin_id=$adminId AND active=1");
        
        if ($query)
        {
            return true;
        }
    }
    else
    {
        $query = $conn->query("INSERT INTO " . DB_PAGE_ADMINS . " (active,admin_id,page_id,role) VALUES (1,$adminId,$pageId,'$adminRole')");
        
        if ($query)
        {
            registerNotification(array(
                'recipient_id' => $adminId,
                'notifier_id' => $pageId,
                'type' => 'page_add_admin',
                'text' => $lang['made_page_admin'],
                'url' => 'index.php?a=timeline&id=' . $page['username']
            ));
            
            return true;
        }
    }
}
