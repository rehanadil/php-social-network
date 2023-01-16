<?php
function getMessages($p=array())
{
    if (!isLogged()) return false;
    $limit = 10;

    if (isset($p['limit'])) $limit = (int) $p['limit'];
    if (isset($p['id'])) $p['message_id'] = $p['id'];
    if (isset($p['receiver_id'])) $p['recipient_id'] = $p['receiver_id'];
    
    global $conn;
    $get = array();
    
    if (!empty($p['recipient_id'])
        && is_numeric($p['recipient_id']))
    {
        $receiverId = (int) $p['recipient_id'];
        $receiverObj = new \SocialKit\User();
        $receiverObj->setId($receiverId);
        $receiver = $receiverObj->getRows();
    }
    
    $queryText = "SELECT id,active,media_id,recipient_id,seen,text,time,timeline_id FROM " . DB_MESSAGES . " WHERE active=1";
    
    if (!empty($p['id'])
        && is_numeric($p['id']))
    {
        $messageId = (int) $p['id'];
        $queryText .= " AND id=$messageId";
    }
    elseif (isset($p['ids'])
        && is_array($p['ids']))
    {
        $mIds = $p['ids'];
        $mIds[] = "0";
        $queryText .= " AND id IN (" . implode(',', $mIds) . ")";
    }
    elseif (!empty($p['before_id'])
        && is_numeric($p['before_id']))
    {
        $beforeMessageId = (int) $p['before_id'];
        $queryText .= " AND id<$beforeMessageId";
    }
    elseif (!empty($p['after_id'])
        && is_numeric($p['after_id']))
    {
        $afterMessageId = (int) $p['after_id'];
        $queryText .= " AND id>$afterMessageId";
    }
    
    if (empty($p['timeline_id'])
        || $p['timeline_id'] < 1)
    {
        global $user, $userObj;
        $timelineId = $user['id'];
        $timelineObj = $userObj;
        $timeline = $user;
    }
    else
    {
        $timelineId = (int) $p['timeline_id'];
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
    }
    
    if (!isset($timeline['id'])
        || !$timelineObj->isAdmin()
        || (isset($receiverId)
            && $timelineId == $receiverId)
        ) return false;
    
    if (isset($p['new'])
        && $p['new'] == true)
    {
        $queryText .= (isset($receiverId)) ? " AND seen=0 AND timeline_id=" . $receiverId . " AND recipient_id=" . $timelineId : " AND seen=0 AND (timeline_id=" . $timelineId . " OR recipient_id=" . $timelineId . ")";
    }
    elseif (isset($p['receiver_only'], $receiverId)
        && $p['receiver_only'] == true)
    {
        $queryText .= " AND timeline_id=" . $receiverId . " AND recipient_id=" . $timelineId;
    }
    else
    {
        $queryText .= (isset($receiverId)) ? " AND ((timeline_id=" . $timelineId . " AND recipient_id=" . $receiverId . ") OR (timeline_id=" . $receiverId . " AND recipient_id=" . $timelineId . "))" : " AND (timeline_id=" . $timelineId . " OR recipient_id=" . $timelineId . ")";
    }
    
    $query = $conn->query($queryText);
    $queryLimitFrom = $query->num_rows - $limit;
    
    if ($queryLimitFrom < 0)
    {
        $queryLimitFrom = 0;
    }
    
    $queryText .= (isset($p['order'])) ? " ORDER BY " . $p['order'] : " ORDER BY id ASC";
    $queryText .= ($limit > 0) ? " LIMIT $queryLimitFrom,$limit" : "";
    $query2 = $conn->query($queryText);
    
    if ($query2->num_rows == 0)
    {
        return false;
    }

    $escapeObj = new \SocialKit\Escape();
    
    while ($fetch2 = $query2->fetch_array(MYSQLI_ASSOC))
    {
        $msgTimelineObj = new \SocialKit\User();
        $msgTimelineObj->setId($fetch2['timeline_id']);
        $fetch2['timeline'] = $fetch2['account'] = $msgTimelineObj->getRows();

        if ($fetch2['recipient_id'] == $timelineId)
        {
            $fetch2['receiver'] = $fetch2['timeline'];
        }
        else
        {
            $msgReceiverObj = new \SocialKit\User();
            $msgReceiverObj->setId($fetch2['recipient_id']);
            $fetch2['receiver'] = $msgReceiverObj->getRows();
        }

        $fetch2['admin'] = false;
        if ($msgTimelineObj->isAdmin()) $fetch2['admin'] = true;
        
        $fetch2['text'] = $escapeObj->getEmoticons($fetch2['text']);
        $fetch2['text'] = $escapeObj->getLinks($fetch2['text']);
        $fetch2['text'] = $escapeObj->getHashtags($fetch2['text']);
        $fetch2['text'] = $escapeObj->getMentions($fetch2['text']);
        $fetch2['media'] = array();

        if (!empty($fetch2['media_id']))
        {
            $mediaObj = new \SocialKit\Media();
            $mediaObj->setId($fetch2['media_id']);
            $fetch2['media'] = $mediaObj->getRows();
        }
        
        if ($fetch2['recipient_id'] == $timelineId && $fetch2['seen'] == 0)
        {
            $conn->query("UPDATE " . DB_MESSAGES . " SET seen=" . time() . " WHERE id=" . $fetch2['id']);
        }
        
        $get[] = $fetch2;
    }

    return $get;
}
