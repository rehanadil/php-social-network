<?php
function deletePageAdmin($pageId=0, $adminId=0)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn;
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
    
    if ($page['type'] != "page")
    {
        return false;
    }
    
    if ($admin['type'] != "user")
    {
        return false;
    }
    
    if ($pageObj->isPageAdmin() != "admin")
    {
        return false;
    }
    
    $query = $conn->query("DELETE FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $adminId . " AND page_id=" . $pageId);
    
    if ($query)
    {
        return true;
    }
}
