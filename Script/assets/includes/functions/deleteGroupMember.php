<?php
function deleteGroupMember($groupId=0, $memberId=0)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn, $user;
    $groupId = (int) $groupId;
    $memberId = (int) $memberId;

    $groupObj = new \SocialKit\User();
    $groupObj->setId($groupId);
    $group = $groupObj->getRows();

    $memberObj = new \SocialKit\User();
    $memberObj->setId($memberId);
    $member = $memberObj->getRows();
    
    $continue = false;
    
    if (! isset($group['id']))
    {
        return false;
    }
    
    if (! isset($member['id']))
    {
        return false;
    }
    
    if ($group['type'] != "group")
    {
        return false;
    }
    
    if ($member['type'] != "user")
    {
        return false;
    }
    
    if ($memberId == $user['id'] or $groupObj->isGroupAdmin())
    {
        $query = $conn->query("DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $memberId . " AND following_id=" . $groupId);
        return true;
    }
}
