<?php
/* Groups */
function registerGroupMember($groupId=0, $memberId=0)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn, $lang;
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
    
    if ($memberObj->isFollowing($groupId))
    {
        return false;
    }
    
    if ($group['add_privacy'] == "admins")
    {
        if ($groupObj->isGroupAdmin())
        {
            $continue = true;
        }
    }
    elseif ($group['add_privacy'] == "members")
    {
        if ($groupObj->isFollowedBy())
        {
            $continue = true;
        }
    }
    
    if ($continue)
    {
        if ($groupObj->isFollowRequested($memberId))
        {
            $query = $conn->query("UPDATE " . DB_FOLLOWERS . " SET active=1 WHERE follower_id=" . $memberId . " AND following_id=" . $groupId . " AND active=0");
            
            if ($query)
            {
                registerNotification(array(
                    'recipient_id' => $member['id'],
                    'text' => str_replace('{group_name}', '[b weight=500]'. $group['name'] .'[/b]', $lang['accepted_group_join_request']),
                    'type' => 'accepted_group_member',
                    'url' => 'index.php?a=timeline&id=' . $group['username']
                ));
                
                return true;
            }
        }
        else
        {
            $query = $conn->query("INSERT INTO " . DB_FOLLOWERS . " (active,follower_id,following_id,time) VALUES (1," . $memberId . "," . $groupId . "," . time() . ")");
            
            if ($query)
            {
                registerNotification(array(
                    'recipient_id' => $member['id'],
                    'text' => str_replace('{group_name}', '[b weight=500]'. $group['name'] .'[/b]', $lang['added_to_group']),
                    'type' => 'made_group_member',
                    'url' => 'index.php?a=timeline&id=' . $group['username']
                ));
                
                return true;
            }
        }
    }
}
