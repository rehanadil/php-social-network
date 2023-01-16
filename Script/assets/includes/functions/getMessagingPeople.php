<?php
function getMessagingPeople($p=array())
{
    if (!isLogged()) return false;
    if (!is_array($p)) return false;

    global $conn, $user, $userObj;
    
    $timelineId = (int) $user['id'];
    if (isset($p['timeline_id'])
        && $p['timeline_id'] > 0) $timelineId = $p['timeline_id'];

    $limit = 10;
    if (isset($p['limit'])
        && $p['limit'] > 0) $limit = $p['limit'];

    $start = 0;
    if (isset($p['start'])
        && $p['start'] > 0) $start = $p['start'];

    $search = null;
    if (isset($p['search'])
        && !empty($p['search'])) $search = $p['search'];

    $newSql = "";
    if (isset($p['new'])) $newSql = " AND seen=0";

    $people = array();
    $msgs = array();
    $reorder = array();
    $return = array();

    $limit = ($limit == 0) ? 10 : $limit;
    $timelineId = ($timelineId == 0) ? $user['id'] : $timelineId;

    if ($timelineId === $user['id'])
    {
        $timelineObj = $userObj;
        $timeline = $user;
    }
    else
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();

        if (!$timelineObj->isAdmin()) return false;
    }

    if ($search)
    {
        $followings = $timelineObj->getFollowings($search, 0, false, "time DESC", $limit);
        foreach ($followings as $key => $fid)
        {
            $return[$fid] = "";
        }

        $followers = $timelineObj->getFollowers($search, 0, false, "time DESC", $limit);
        foreach ($followers as $key => $fid)
        {
            $return[$fid] = "";
        }

        return $return;
    }

    $isMessagesSql = "SELECT id FROM " . DB_MESSAGES . " WHERE (timeline_id=" . $timeline['id'] . " OR recipient_id=" . $timeline['id'] . ")" . $newSql;
    $isMessages = $conn->query($isMessagesSql);

    if ($isMessages->num_rows > 0)
    {
        $receiverIdSql = "SELECT DISTINCT recipient_id FROM " . DB_MESSAGES . " WHERE timeline_id=" . $timeline['id'] . $newSql . " ORDER BY time DESC";
        $receiverIdQuery = $conn->query($receiverIdSql);

        while ($receiverIdFetch = $receiverIdQuery->fetch_array(MYSQLI_ASSOC))
        {
            $receiverId = $receiverIdFetch['recipient_id'];

            $receiverSql = "SELECT time,text FROM " . DB_MESSAGES . " WHERE timeline_id=" . $timeline['id'] . " AND recipient_id=" . $receiverId . $newSql . " ORDER BY time DESC";
            $receiverQuery = $conn->query($receiverSql);
            $receiver = $receiverQuery->fetch_array(MYSQLI_ASSOC);

            $people[$receiverId] = $receiver['time'];
            $msgs[$receiverId] = $receiver['text'];
        }

        $senderIdSql = "SELECT DISTINCT timeline_id FROM " . DB_MESSAGES . " WHERE recipient_id=" . $timeline['id'] . $newSql . " ORDER BY time DESC";
        $senderIdQuery = $conn->query($senderIdSql);
        
        while ($senderIdFetch = $senderIdQuery->fetch_array(MYSQLI_ASSOC))
        {
            $senderId = $senderIdFetch['timeline_id'];

            $senderSql = "SELECT time,text FROM " . DB_MESSAGES . " WHERE recipient_id=" . $timeline['id'] . " AND timeline_id=" . $senderId . $newSql . " ORDER BY time DESC";
            $senderQuery = $conn->query($senderSql);
            $sender = $senderQuery->fetch_array(MYSQLI_ASSOC);
            
            if (!isset($people[$senderId])
                || $sender['time'] > $people[$senderId])
            {
                $people[$senderId] = $sender['time'];
                $msgs[$senderId] = $sender['text'];
            }
        }
    }
    else
    {
        $followings = $timelineObj->getFollowings('', 0, false, "time DESC", $limit);
        foreach ($followings as $key => $fid)
        {
            $return[$fid] = "";
        }

        return $return;
    }

    $peopleCount = count($people);
    $i = $start * $limit;
    if ($peopleCount == 0) return false;
    if ($limit > $peopleCount) $limit = $peopleCount;

    arsort($people);
    foreach ($people as $pid => $msg)
    {
        $reorder[] = array(
            'id' => $pid,
            'text' => $msgs[$pid]
        );
    }

    for ($i2 = $i; $i2 < ($i+$limit); $i2++)
    {
        if (isset($reorder[$i2]['id'])) $return[$reorder[$i2]['id']] = $reorder[$i2]['text'];
    }

    return $return;
}